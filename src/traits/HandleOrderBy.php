<?php
namespace Looden\Framework;
use Illuminate\Validation\ValidationException;

trait HandleOrderBy
{
    public function handleOrderBy(){
        if(!\request()->has('orderBy')) return $this;
        foreach (request()->get('orderBy') as $order){
            $split = explode('.', $order);
            if(count($split) == 1){
                $split[1] = 'ASC';
            }
            $this->query->orderBy($split[0], $split[1]);
        }
        return $this;
    }
}