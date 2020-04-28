<?php

namespace Looden\Framework\Console\Commands;

use App\Role;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Looden\Framework\App\LoodenModel;

class ModelReader extends Command
{
    protected $signature = 'looden:readmodels';
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        $out = [];
        foreach (scandir(app_path()) as $result) {
            if ($result === '.' or $result === '..') continue;
            $filename = app_path() . '/' . $result;
            $namespace = 'App\\'.preg_replace('/(.*)app\/(.*)(.php)/', '${2}', $filename);
            $modelName = preg_replace('/(.*)app\/(.*)(.php)/', '${2}', $filename);
            if (!is_dir($filename)) {
                $out[] = [
                    'namespace' => $namespace,
                    'model_name' => $modelName,
                    'snake' => snake_case($modelName),
                    'slug' => str_plural(snake_case($modelName)),
                    'singular_name' => $modelName,
                    'plural_name' => $modelName,
                ];
            }
        }
        $models = [];
        foreach($out as $key => $value){
            $models[] = LoodenModel::updateOrCreate(
                ['name' => $value['model_name']],
                [
                    'name' => $value['model_name'],
                    'namespace' => $value['namespace'],
                    'slug' => str_plural(snake_case($value['model_name'])),
                    'singular_name' => $this->getInfoAboutModel('singular_name', $value['model_name']),
                    'plural_name' => $this->getInfoAboutModel('plural_name', $value['model_name']),
                    'icon' => $this->getInfoAboutModel('icon', $value['model_name'])
                ]
            );
        }

        $models[] = LoodenModel::updateOrCreate(
            ['name' => 'LoodenModel'],
            [
                'name' => 'LoodenModel',
                'namespace' => LoodenModel::class,
                'slug' => str_plural(snake_case('LoodenModel')),
                'singular_name' => 'LoodenModel',
                'plural_name' => 'LoodenModels',
                'icon' => 'fas fa-atom'
            ]
        );

        Artisan::call('cache:clear');
        Artisan::call('config:clear');
    }

    private function getInfoAboutModel($attr, $modelName){

        $arrDefault = [
            'singular_name' => $modelName,
            'plural_name' => str_plural($modelName),
            'icon' => 'fas fa-atom',
        ];
        if(config('looden.slug-info.'.$modelName.'.'.$attr) !== null){
            return config('looden.slug-info.'.$modelName.'.'.$attr);
        }
        if (isset($arrDefault[$modelName][$attr])){
            return $arrDefault[$modelName][$attr];
        }
        return $arrDefault[$attr];
    }
}
