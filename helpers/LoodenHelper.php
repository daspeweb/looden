<?php
namespace Looden\Framework\Helpers;

use App\DwModel;
use App\DwPermissionDetailRole;
use Daspeweb\Framework\Model\ModelNew;
use Illuminate\Support\Facades\Cache;
use Looden\Framework\App\LoodenModel;

class LoodenHelper
{
    public static function getModelBySlug($slug, $avoidException = false){
        $key = 'slug-'.$slug;
        $model = LoodenModel::whereSlug($slug)->first();
        if (!$model && !$avoidException){
            throw new \Exception('Model not found. Check the slug.');
        }else if(!$model){
            return null;
        }
        return Cache::get($key, app($model->namespace), 60*24);
    }

    public static function getRulesForModel($model){
        return method_exists($model, 'validations')
            ? $model->validations()
            : [];
    }
}
