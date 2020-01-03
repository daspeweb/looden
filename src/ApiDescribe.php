<?php


namespace Looden\Framework\Traits;


trait ApiDescribe
{
    static function getDescriptionForShow($loodenModel){
        return self::fillDescription(
            'GET',
            $loodenModel['slug'].'/{key}',
            [
                $loodenModel['slug'].'/{key}/{field?}'
            ]
        );
    }

    static function getDescriptionForDestroy($loodenModel){
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

    static function getDescriptionForIndex($loodenModel){
        return self::fillDescription(
            'GET',
            $loodenModel['slug'],
            [
                $loodenModel['slug'].'?fields={field1},{field2},{fieldn}',
                $loodenModel['slug'].'?with[]={relationship},{relationship.relationship2},{relationshipN}',
                $loodenModel['slug'].'?filter[]={field,criteria,value}',
                $loodenModel['slug']."?filter[]={field,in,['n','n2']}",
                $loodenModel['slug'].'?orderBy[]={field}',
                $loodenModel['slug'].'?orderBy[]={field.ASC}',
                $loodenModel['slug'].'?orderBy[]={field.DESC}',
                $loodenModel['slug'].'?orderBy[]={field}&orderBy[]={field2}&orderBy[]={fieldN}',
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
    }

    private static function fillDescription($protocol, $route, $variants= []){
        $rest = new \stdClass();
        $rest->protocol = $protocol;
        $rest->method = '/'.$route;
        $rest->variants = $variants;
        return $rest;
    }
}