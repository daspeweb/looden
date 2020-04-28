<?php
namespace Looden\Framework\Traits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Looden\Framework\Helpers\LoodenHelper;

trait UpsertApi
{
    public function upsert($slug, $key = null){
        try{
            $this->model = LoodenHelper::getModelBySlug($slug);
            $existingModel = null;
            /*If it is a update call, $existingModel must be retrieved from DB and sent to fillModel method*/
            if($key){
                $field = \request()->input('key') ?? $this->model->getKeyName();
                $existingModel = $this->model->where($field, $key)->first();
            }
            [$modelInstance, $validationErrors, $hasError] = $this->fillModel($slug, \request()->all(), $existingModel);
            if ($hasError){
                return response()->json($validationErrors, 400);
            }
            $modelInstance->save();
            return response()->json($modelInstance);
        }catch (\Exception $e){
            return $this->handleException($e);
        }
    }

    /*
     * It's required to send request because there are some operations that runs in batch mode
     * So that the full request variable can acctually contains information about more than one model
     */
    public function fillModel($slug, $request, $modelInstance =null){
        $fields = collect($request)->except('ignore-validation-rules')->toArray();
        $model = LoodenHelper::getModelBySlug($slug);
        /*The $modelInstance variable represents a model to be created or, if sent as parameter, a existing model just retrieved from DB
        In case it was sent, this is a update operation*/
        $modelInstance = $modelInstance ?? new $model();
        $modelInstance->fill($fields);
        $validationErrors = [];
        $hasError = false;
        if(!\request()->has('ignore-validation-rules')){
            $validator = Validator::make($modelInstance->toArray(), LoodenHelper::getRulesForModel($model));
            $hasError = $validator->fails() ? true : $hasError;
            /*Every model must has a error array, even if any error was thrown (empty array in this case)*/
            $validationErrors = $validator->fails() ? $validator->errors()->toArray() : new \stdClass();
        }
        return [
            $modelInstance,
            $validationErrors,
            $hasError
        ];
    }
}