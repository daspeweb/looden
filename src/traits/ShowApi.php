<?php


namespace Looden\Framework\Traits;


use Looden\Framework\Helpers\LoodenHelper;

trait ShowApi
{
    public function show($slug, $value){
        try{
            $this->model = LoodenHelper::getModelBySlug($slug);
            $model = $this->handleFieldsToSelect()
                ->handleWith()
                ->query()->where(request()->input('key-field') ?? $this->model->getKeyName(), $value)
                ->first();
            return $model ?   response()->json($model) :  response()->json([], 404);
        }catch (\Exception $e){
            return $this->handleException($e);
        }
    }
}