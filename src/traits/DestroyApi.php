<?php
namespace Looden\Framework\Traits;
use Illuminate\Http\Request;
use Looden\Framework\Helpers\LoodenHelper;

trait DestroyApi
{
    public function destroy($slug, $key){
        try {
            $this->model = LoodenHelper::getModelBySlug($slug);
            $field = \request()->input('key') ?? $this->model->getKeyName();
            $model = $this
                ->handleFieldsToSelect()
                ->query()
                ->where($field, $key)
                ->first();

            if($model){
                $model->delete();
            }
            return $model
                ? response()->json($model->toArray())
                : response()->json(null, 200);
        }catch (\Exception $e){
            return $this->handleException($e);
        }
    }
}