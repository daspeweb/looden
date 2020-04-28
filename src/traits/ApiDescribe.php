<?php
namespace Looden\Framework\Traits;
use Looden\Framework\App\LoodenModel;
use Looden\Framework\Helpers\LoodenHelper;

trait ApiDescribe
{
    public function describe($model = null){
        if ($model){
            $loodenModelColl = LoodenModel::whereSlug($model)->get();
        }else{
            $loodenModelColl = LoodenModel::all();
        }
        $loodenModelMap = $loodenModelColl->mapWithKeys(function ($loodenModel){

            return [$loodenModel['slug'] => ApiDescribe::getAll($loodenModel)];
        });
        if($model && \request()->has('action')){
            return response()->json($loodenModelMap[$model]->{(\request()['action'])});
        }else if ($model){
            return response()->json($loodenModelMap[$model]);
        }
        return response()->json($loodenModelMap);
    }

    static function getAll($loodenModel){
        $rest = new \stdClass();
        $rest->show = ApiDescribe::getDescriptionForShow($loodenModel);
        $rest->index = ApiDescribe::getDescriptionForIndex($loodenModel);
//        $rest->destroy = ApiDescribe::getDescriptionForDestroy($loodenModel);
//        $rest->destroyCollection = ApiDescribe::getDescriptionForDestroyCollection($loodenModel);
//        $rest->store = ApiDescribe::getDescriptionForStore($loodenModel);
//        $rest->storeCollection = ApiDescribe::getDescriptionForStoreCollection($loodenModel);
//        $rest->storeRaw = ApiDescribe::getDescriptionForStoreRaw($loodenModel);
//        $rest->storeRawCollection = ApiDescribe::getDescriptionForStoreRawCollection($loodenModel);
//        $rest->update = ApiDescribe::getDescriptionForUpdate($loodenModel);
//        $rest->updateCollection = ApiDescribe::getDescriptionForUpdateCollection($loodenModel);
//        $rest->updateRaw = ApiDescribe::getDescriptionForUpdateRaw($loodenModel);
//        $rest->updateRawCollection = ApiDescribe::getDescriptionForUpdateRawCollection($loodenModel);
//        $rest->validate = ApiDescribe::getDescriptionForValidate($loodenModel);
        return $rest;
    }
    static function getDescriptionForShow($loodenModel){
        return self::fillDescription(
            'GET',
            $loodenModel['slug'].'/{key}',
            [
                'fields-to-select={field1},{field2},{fieldN]',
                'with[]={relationship1}',
                'with[]={relationship1}.{relationship2}.{relationshipN}',
                'with[]={relationship1}:{field1},{field2}.{relationship2}:{field1},{field2}.{relationshipN}:{field1},{field2},{fieldN}',
            ]
        );
    }

    static function getDescriptionForIndex($loodenModel){
        return self::fillDescription(
            'GET',
            $loodenModel['slug'],
            [
                'page-size={n}',
                'fields-to-select={field1},{field2},{fieldN]',
                'with[]={relationship1}',
                'with[]={relationship1}.{relationship2}.{relationshipN}',
                'with[]={relationship1}:{field1},{field2}.{relationship2}:{field1},{field2}.{relationshipN}:{field1},{field2},{fieldN}',
                'where[]={field},{criteria},{value}',
                'where[]={field},not-null',
                "where[]={field},in,{value1}:{value2}:{valueN}",
                "where[]={field},not-in,{value1}:{value2}:{valueN}",
                'orderBy[]={field}',
                'orderBy[]={field.ASC}',
                'orderBy[]={field.DESC}'
            ]
        );
    }

 /*   static function getDescriptionForDestroy($loodenModel){
        return self::fillDescription(
            'DELETE',
            $loodenModel['slug'].'/{key}',
            [
                $loodenModel['slug'].'/{key}/{field?}'
            ]
        );
    }

    static function getDescriptionForDestroyCollection($loodenModel){
        return self::fillDescription(
            'DELETE',
            $loodenModel['slug'],
            [
                $loodenModel['slug'].'/{field?}'
            ]
        );
    }



    static function getDescriptionForStore($loodenModel){
        return self::fillDescription(
            'POST',
            $loodenModel['slug']
        );
    }
    static function getDescriptionForStoreRaw($loodenModel){
        return self::fillDescription(
            'POST',
            $loodenModel['slug'].'/raw'
        );
    }

    static function getDescriptionForStoreCollection($loodenModel){
        return self::fillDescription(
            'POST',
            $loodenModel['slug'].'/collection'
        );
    }
    static function getDescriptionForStoreRawCollection($loodenModel){
        return self::fillDescription(
            'POST',
            $loodenModel['slug'].'/collection/raw'
        );
    }

    static function getDescriptionForUpdate($loodenModel){
        return self::fillDescription(
            'PUT',
            $loodenModel['slug'].'/{key}',
            [
                $loodenModel['slug'].'/{key}/{field?}',
            ]
        );
    }
    static function getDescriptionForUpdateRaw($loodenModel){
        return self::fillDescription(
            'PUT',
            $loodenModel['slug'].'/raw/{key}',
            [
                $loodenModel['slug'].'/raw/{key}/{field?}',
            ]
        );
    }

    static function getDescriptionForUpdateCollection($loodenModel){
        return self::fillDescription(
            'PUT',
            $loodenModel['slug'].'/collection',
            [
                $loodenModel['slug'].'/collection/{field?}',
            ]
        );
    }
    static function getDescriptionForUpdateRawCollection($loodenModel){
        return self::fillDescription(
            'PUT',
            $loodenModel['slug'].'/collection/raw',
            [
                $loodenModel['slug'].'/collection/{field?}',
            ]
        );
    }
    static function getDescriptionForValidate($loodenModel){
        return self::fillDescription(
            'POST',
            $loodenModel['slug'].'/validate',
            [
                $loodenModel['slug'].'/validate/{key?}',
                $loodenModel['slug'].'/validate/{key?}/{field?}',
            ]
        );
    }*/

    private static function fillDescription($protocol, $route, $parameters = []){
        $rest = new \stdClass();
        $rest->protocol = $protocol;
        $rest->method = '/'.$route;
        $rest->parameters = $parameters;
        return $rest;
    }
}