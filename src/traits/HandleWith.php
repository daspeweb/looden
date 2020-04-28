<?php
namespace Looden\Framework;
use Illuminate\Validation\ValidationException;
use Looden\Framework\Helpers\LoodenHelper;

trait HandleWith
{
    public function handleWith(){
        if(!\request()->has('with')) return $this;
        foreach (request()->get('with') as $with){
            $relationshipTree = explode('.', $with);
            $this->applyRelationshipToQuery($this->query, $this->model, $relationshipTree);
        }
        return $this;
    }
    private function applyRelationshipToQuery($query, $model, $relationshipTree){
        if(!$relationshipTree) return;
        //realationship method on parent class
        $relationship = $relationshipTree[0];
        [$fieldsToSelectStr, $relationshipName ]= $this->splitRelationShipAndFieldsToSelect($relationship);

        $relationshipModel = $model->$relationshipName()->getRelated();

        //the primary key is always selected
        $fieldsToSelectStr = $fieldsToSelectStr != '*'
            ? $fieldsToSelectStr  . ',' .  $relationshipModel->getKeyName()
            : $fieldsToSelectStr;

        //if any field is required to get models related, its going to be selected, even if was not quoted in query sent by client
        $fieldsToSelectStr = $fieldsToSelectStr != '*' && strpos(get_class($model->$relationshipName()), 'HasMany')
                    ? $fieldsToSelectStr .  ',' . $model->$relationshipName()->getForeignKeyName()
                    : $fieldsToSelectStr;

        $query->with(array($relationshipName=>function($query) use ($relationshipTree, $fieldsToSelectStr, $relationshipModel){
            array_shift($relationshipTree);
            //the following code try to identify if any field is required for relate the parent model
            if(count($relationshipTree)> 0 && $fieldsToSelectStr != '*'){
                [$fieldsToSelectForNextRelationshipStr, $nextRelationshipName ]= $this->splitRelationShipAndFieldsToSelect($relationshipTree[0]);
                if(strpos(get_class($relationshipModel->$nextRelationshipName()), 'Belongs')){
                    $fieldsToSelectStr = $fieldsToSelectStr . ',' .$relationshipModel->$nextRelationshipName()->getForeignKeyName();
                }
            }
            $query->select(explode(',', $fieldsToSelectStr));
            $this->applyRelationshipToQuery($query, $relationshipModel, $relationshipTree);
        }));
    }
    //entry example => orderProducts:quantity,sales_price,total
    private function splitRelationShipAndFieldsToSelect($fullRelationshipDescription){
        preg_match('/(.*)(:)(.*)/', $fullRelationshipDescription, $relationshipAndPossibleFieldsMatch);
        // returns 0 => relationshipName 1 => fields to select on relationship
        return [
                $relationshipAndPossibleFieldsMatch ? $relationshipAndPossibleFieldsMatch[3] : '*',
                $relationshipAndPossibleFieldsMatch ? $relationshipAndPossibleFieldsMatch[1] :  $fullRelationshipDescription
        ];
    }
}