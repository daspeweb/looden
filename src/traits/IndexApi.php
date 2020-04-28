<?php


namespace Looden\Framework\Traits;


use Illuminate\Http\Request;
use Looden\Framework\Helpers\LoodenHelper;

trait IndexApi
{
    public function index($slug, Request $request){
        try {
            $this->model = LoodenHelper::getModelBySlug($slug);
            $this->handleFieldsToSelect()->handleWith()->handleFilters()->handleOrderBy();
            $modelColl = $this->query->paginate(request()['page-size'] ?: 15);
            return response()->json($modelColl);
        }catch (\Exception $e){
            return $this->handleException($e);
        }
    }
}