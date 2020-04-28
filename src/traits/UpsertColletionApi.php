<?php
namespace Looden\Framework\Traits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Looden\Framework\Helpers\LoodenHelper;

trait UpsertColletionApi
{
    public function upsertCollection($slug){
        try{
            DB::beginTransaction();
            $this->model = LoodenHelper::getModelBySlug($slug);
            $errors = [];
            $hasError = false;
            $modelColl = [];
            $primaryKey = \request()->input('key') ?? $this->model->getKeyName();
            foreach (\request()->all() as $request){
                $existingModel = null;
                if($request[$primaryKey]){
                    $existingModel = $this->model->where($primaryKey, $request[$primaryKey])->first();
                }
                [$modelInstance, $validationErrorsForThisModel, $hasErrorForThisModel] = $this->fillModel($slug, $request, $existingModel);
                $errors[] = $validationErrorsForThisModel;
                $hasError = $hasErrorForThisModel ? true : $hasError;
                $modelColl[] = $modelInstance;
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
}