<?php
namespace backend\models\base;


use yii\base\Model;

abstract class AssignSortableForm extends Model
{
    public $items;
    public $assignedItems = [];



    public function rules()
    {
        $rules = [
            [['items', 'assignedItems'], 'safe'],
        ];
        return array_merge(parent::rules(), $rules);
    }

    abstract public function getItems();

    abstract public function getAssignedItems();

    abstract public function save();
}