<?php


namespace Looden\Framework;


trait HandleFieldToSelect
{
    public function handleFieldsToSelect(){
        $fieldsToSelect = explode(',', request()->input('fields-to-select') ?? '*');
        array_push($fieldsToSelect, $this->model->getKeyName());
        $this->query = $this->model::select($fieldsToSelect);
        return $this;
    }
}