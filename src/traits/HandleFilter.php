<?php
namespace Looden\Framework;
use Illuminate\Validation\ValidationException;

trait HandleFilter
{
    public function handleFilters(){
        if(!\request()->has('where')) return $this;
        foreach (request()['where'] as $filter){
            $filterArr  = explode(',', $filter);
            $valueToFilter = $filterArr[2];
            $field = $filterArr[0];
            if(preg_match('/(.+)([.])(.+)/', $field, $relationshipArr)){
                $relationship = $relationshipArr[1];
                $relationshipFieldToFilter = $relationshipArr[3];
//                dd($relationship);
                $this->query->whereHas($relationship, function($q) use($relationshipFieldToFilter, $valueToFilter) {
                    $q->where($relationshipFieldToFilter, $valueToFilter);
                })->get();
            }
            return $this;

            if($filterArr[1] == 'not-null'){
                $this->query->whereNotNull($filterArr[0]);
            }else if($filterArr[1] == 'in' || $filterArr[1] == 'not-in'){
                $valueToFilter = explode(':',$valueToFilter);
                if($filterArr[1] == 'in'){
                    $this->query->whereIn($filterArr[0], $valueToFilter);
                }else if($filterArr[1] == 'not-in'){
                    $this->query->whereNotIn($filterArr[0], $valueToFilter);
                }
            }else{
                $this->query->where($filterArr[0], $filterArr[1], $valueToFilter);
            }

        }
        return $this;
    }
}