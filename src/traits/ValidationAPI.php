<?php
namespace Looden\Framework\Traits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Looden\Framework\Helpers\LoodenHelper;

trait ValidationAPI
{
    public function runValidation($slug, $value = null){
        try{
            $this->model = LoodenHelper::getModelBySlug($slug);
            $modelInstance = null;
            if ($value != null){
                $primaryKey = \request()->input('key') ?? $this->model->getKeyName();
                $modelInstance = $this->model::where($primaryKey, $value)->first();
                if(!$modelInstance) return response()->json([], 404);
            }
            $modelInstance->fill(request()->all());
            $validator = Validator::make($modelInstance->toArray(), LoodenHelper::getRulesForModel($this->model));
            if($validator->fails()){
                throw new ValidationException($validator);
            }
            return response()->json([]);
        }catch (\Exception $e){
            return $this->handleException($e);
        }
    }
}