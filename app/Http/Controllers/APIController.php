<?php

namespace Looden\Framework\Controller;
use App\Http\Controllers\Controller;
use http\Env\Response;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Looden\Framework\App\LoodenModel;
use Looden\Framework\Helpers\LoodenHelper;
use Looden\Framework\Traits\ApiDescribe;

class APIController extends Controller
{
    protected $query, $model;

    public function describe($model = null){
        if ($model){
            $loodenModelColl = LoodenModel::whereSlug($model)->get();
        }else{
            $loodenModelColl = LoodenModel::all();
        }
        $loodenModelMap = $loodenModelColl->mapWithKeys(function ($loodenModel){
            $rest = new \stdClass();

            $rest->show = ApiDescribe::getDescriptionForShow($loodenModel);
            $rest->index = ApiDescribe::getDescriptionForIndex($loodenModel);
            $rest->destroy = ApiDescribe::getDescriptionForDestroy($loodenModel);
            $rest->destroyCollection = ApiDescribe::getDescriptionForDestroyCollection($loodenModel);
            $rest->store = ApiDescribe::getDescriptionForStore($loodenModel);
            $rest->storeCollection = ApiDescribe::getDescriptionForStoreCollection($loodenModel);
            $rest->storeRaw = ApiDescribe::getDescriptionForStoreRaw($loodenModel);
            $rest->storeRawCollection = ApiDescribe::getDescriptionForStoreRawCollection($loodenModel);
            $rest->store = ApiDescribe::getDescriptionForUpdate($loodenModel);
            $rest->storeCollection = ApiDescribe::getDescriptionForUpdateCollection($loodenModel);
            $rest->storeRaw = ApiDescribe::getDescriptionForUpdateRaw($loodenModel);
            $rest->storeRawCollection = ApiDescribe::getDescriptionForUpdateRawCollection($loodenModel);
            $rest->validate = ApiDescribe::getDescriptionForValidate($loodenModel);
            return [$loodenModel['slug'] => $rest];
        });
        return response()->json($loodenModelMap);
    }

    public function show($slug, $value, $field = 'id'){
        try{
            $this->model = LoodenHelper::getModelBySlug($slug);
            $this->handleFieldsToSelect();
            $this->query->where($field, $value)->take(1);
            $this->handleWith();
            $modelColl = $this->query->get();
            if(count($modelColl) > 0){
                return response()->json($modelColl[0]);
            }
            return response()->json([], 404);
        }catch (\Exception $e){
            return $this->handleException($e);
        }
    }

    public function destroy($slug, $id, $field = 'id'){
        try {
            $model = LoodenHelper::getModelBySlug($slug);
            $modelFound = $model::where($field, $id);
            if ($modelFound) {
                $modelFound->delete();
                return response()->json($modelFound->toArray());
            }
            return response()->json([], 404);
        }catch (\Exception $e){
            return $this->handleException($e);
        }
    }

    public function destroyCollection($slug, $field = 'id'){
        try {

            $model = LoodenHelper::getModelBySlug($slug);
            $keys = collect(request()->all())->map(function ($request) use ($field){
                return $request[$field];
            });
            $modelCollection = $model::whereIn($field, $keys)->get();
            foreach ($modelCollection as $model){
                $model->delete();
            }
            return response()->json($modelCollection);
        }catch (\Exception $e){
            return $this->handleException($e);
        }
    }

    public function index($slug, Request $request){
        try {
            $this->model = LoodenHelper::getModelBySlug($slug);
            $this->handleFieldsToSelect();
            $this->handleWith()->handleFilters()->handleOrderBy();
            $modelColl = $this->query->paginate(request()['page-size'] ?: 15);
            return response()->json($modelColl);
        }catch (\Exception $e){
            return $this->handleException($e);
        }
    }
    
    public function runValidation($slug, $value = null, $field = 'id'){
        try{
            $this->model = LoodenHelper::getModelBySlug($slug);
            
            if ($value != null){
                $modelColl = $this->model::where($field, $value)->take(1)->get();
                if(count($modelColl) == 0) return response()->json([], 404);
                $this->model->fill($modelColl[0]->toArray());
            }
    
            $this->model->fill(request()->all());

            $validator = Validator::make($this->model->toArray(), LoodenHelper::getRulesForModel($this->model));
            if($validator->fails()){
                throw new ValidationException($validator);
            }

            return response()->json([]);

        }catch (\Exception $e){
            return $this->handleException($e);
        }
    }


    public function store($slug){
        try{
            $this->model = LoodenHelper::getModelBySlug($slug);
            $this->model->fill(request()->all());
            $validator = Validator::make($this->model->toArray(), LoodenHelper::getRulesForModel($this->model));
            if($validator->fails()){
                throw new ValidationException($validator);
            }
            return response()->json(LoodenHelper::getModelBySlug($slug)::create(request()->all()));
        }catch (\Exception $e){
            return $this->handleException($e);
        }
    }

    public function storeRaw($slug){
        try{
            return response()->json(LoodenHelper::getModelBySlug($slug)::create(request()->all()));
        }catch (\Exception $e){
            return $this->handleException($e);
        }
    }

    public function storeColl($slug){
        try{
            DB::beginTransaction();
            $errors = [];
            $hasError = false;
            $modelColl = [];
            foreach (\request()->all() as $request){
                $this->model = LoodenHelper::getModelBySlug($slug);
                $this->model->fill($request);
                $validator = Validator::make($this->model->toArray(), LoodenHelper::getRulesForModel($this->model));
                $errors[] = $validator->fails()
                    ? $validator->errors()->toArray()
                    : new \stdClass();
                if ($validator->fails()){
                    $hasError = true;
                }
                if (!$hasError){
                    $modelColl[] = $this->model;
                }
            }
            if ($hasError){
                DB::rollBack();
                return response()->json($errors);
            }
            foreach ($modelColl as $model){
                $model->save();
            }
            DB::commit();
            return response()->json($modelColl);
        }catch (\Exception $e){
            DB::rollBack();
            return $this->handleException($e);
        }
    }

    public function storeCollRaw($slug){
        try{
            DB::beginTransaction();
            $this->model = LoodenHelper::getModelBySlug($slug);
            $modelCollection = Collection::make([]);
            foreach (request()->all() as $request){
                $modelCollection->add($this->model::create($request));
            }
            DB::commit();
            return response()->json($modelCollection);
        }catch (\Exception $e){
            DB::rollBack();
            return $this->handleException($e);
        }
    }

    public function update($slug, $value, $field = 'id'){
        try{
            $this->model = LoodenHelper::getModelBySlug($slug);
            $modelColl = $this->model::where($field, $value)->take(1)->get();
            if(count($modelColl) == 0) return response()->json([], 404);
            $modelPersisted = $modelColl[0];
            $modelPersisted->fill(request()->all());
            $validator = Validator::make($modelPersisted->toArray(), LoodenHelper::getRulesForModel($this->model));
            if($validator->fails()){
                throw new ValidationException($validator);
            }
            $modelPersisted->save();
            return response()->json($modelPersisted);
        }catch (\Exception $e){
            return $this->handleException($e);
        }
    }

    public function updateRaw($slug, $value, $field = 'id'){
        try{
            $this->model = LoodenHelper::getModelBySlug($slug);
            $modelColl = $this->model::where($field, $value)->take(1)->get();
            if(count($modelColl) == 0) return response()->json([], 404);
            $modelPersisted = $modelColl[0];
            $modelPersisted->fill(request()->all());
            $modelPersisted->save();
            return response()->json($modelPersisted);
        }catch (\Exception $e){
            return $this->handleException($e);
        }
    }

    public function updateColl($slug, $field = 'id'){
        try{
            DB::beginTransaction();
            $this->model = LoodenHelper::getModelBySlug($slug);
            $errors = [];
            $hasError = false;
            $modelColl = [];
            $keys = collect(request()->all())->map(function ($request) use ($field){
                return $request[$field];
            });

            $modelCollMap = $this->model::whereIn($field, $keys)->get()->mapWithKeys(function ($record) use ($field){
                return [$record[$field] => $record];
            });

            foreach (\request()->all() as $request){
                $modelPersisted = $modelCollMap->get($request[$field]);
                if ($modelPersisted == null) {
                    throw new \Exception('Record not found for key ' . $field . ' ' .$request[$field]);
                }
                $modelPersisted->fill($request);
                $validator = Validator::make($modelPersisted->toArray(), LoodenHelper::getRulesForModel($modelPersisted));
                $errors[] = $validator->fails() ? $validator->errors()->toArray() : new \stdClass();

                if ($validator->fails()){
                    $hasError = true;
                }
                if (!$hasError){
                    $modelColl[] = $modelPersisted;
                }
            }
            if ($hasError){
                DB::rollBack();
                return response()->json($errors);
            }
            foreach ($modelColl as $model){
                $model->save();
            }
            DB::commit();
            return response()->json($modelColl);
        }catch (\Exception $e){
            DB::rollBack();
            return $this->handleException($e);
        }
    }

    public function updateCollRaw($slug, $field = 'id'){
        try{
            DB::beginTransaction();
            $this->model = LoodenHelper::getModelBySlug($slug);
            $modelCollection = Collection::make([]);
            foreach (request()->all() as $request){
                $value = $request['__key_for_update'];
                $modelColl = $this->model::where($field, $value)->take(1)->get();
                if(count($modelColl) == 0){
                    throw new \Exception('Record not found for key ' . $field . ' ' .$value);
                }
                $modelPersisted = $modelColl[0];
                $modelPersisted->fill($request);
                $modelPersisted->save();
                $modelCollection->add($modelPersisted);
            }
            DB::commit();
            return response()->json($modelCollection);
        }catch (\Exception $e){
            DB::rollBack();
            return $this->handleException($e);
        }
    }

    //todo updateRaw

    //todo file , create, update, delete

    private function handleWith(){
        if(!\request()->has('with')) return $this;
        foreach (request()->get('with') as $with){
            $this->query->with($with);
        }
        return $this;
    }
    private function handleFilters(){
        if(!\request()->has('filter')) return $this;
        foreach (request()['filter'] as $filter){
            $filterArr  = explode(',', $filter);
            $value = $filterArr;
            unset($value[0]);
            unset($value[1]);
            $value = implode(',', $value);

            if($filterArr[1] == 'in'){
                $value = json_decode($value);
                if($value){
                    $this->query->whereIn($filterArr[0], $value);
                }else{
                    throw new \Exception('Invalid array given for in filter.');
                }
            }else{
                $this->query->where($filterArr[0], $filterArr[1], $value);
            }

        }
        return $this;
    }
    private function handleOrderBy(){
        if(!\request()->has('orderBy')) return $this;
        foreach (request()->get('orderBy') as $order){
            $split = explode('.', $order);
            if(count($split) == 1){
                $split[1] = 'ASC';
            }
            $this->query->orderBy($split[0], $split[1]);
        }
        return $this;
    }
    private function handleFieldsToSelect(){
        if(!\request()->has('fields')){
            $this->query = $this->model::select('*');
        }else{
            $this->query = $this->model::select(explode(',', request()->input('fields')));
        }
        return $this;
    }

    private function handleException($e){
        if ($e instanceof ValidationException){
            return response()->json(['validations' => $e->errors()]);
        }
        return response()->json([
            'error_message' => $e ->getMessage(),
            'trace' => config('looden.get_trace_error_on_api_calls')
                ?   $e->getTraceAsString()
                :   'disabled'
        ], 500);
    }
}
