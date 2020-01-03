<?php


namespace Looden\Framework;


use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Route;

class Looden extends Facade
{
    protected static function getFacadeAccessor() { return 'looden'; }

    public static function routes(){

    }
}