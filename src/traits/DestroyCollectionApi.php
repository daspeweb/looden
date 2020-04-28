<?php
namespace Looden\Framework\Traits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Looden\Framework\Helpers\LoodenHelper;

trait DestroyCollectionApi
{
    public function destroyCollection($slug){
        try {
            DB::beginTransaction();
            $this->model = LoodenHelper::getModelBySlug($slug);
            $field = \request()->input('key') ?? $this->model->getKeyName();
            $keys = collect(request()->all())
                ->map(function ($request){
                    return $request;
                });

            $modelCollection = $this
                ->handleFieldsToSelect()
                ->query
                ->whereIn($field, $keys)
                ->get();

            foreach ($modelCollection as $model){
                $model->delete();
            }
            DB::commit();
            return response()->json($modelCollection);
        }catch (\Exception $e){
            DB::rollBack();
            return $this->handleException($e);
        }
    }
}