<?php
namespace common\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

class LinkBehavior extends Behavior
{
    public $attributes = [];
    public $relations = [];
    public $modelClasses = [];
    public $connectionClasses = [];


    public function linkObjects($extraColumns= [])
    {
        $owner = $this->owner;

        foreach ($this->attributes as $index => $attribute)
        {
            $relationName = $this->relations[$index];
            $owner->unlinkAll($relationName, true);
            $className = $this->modelClasses[$index];

            $models = $className::findAll($owner->{$attribute});
            foreach ($models as $model)
            {
                $owner->link($relationName, $model, $extraColumns);
            }
        }
    }

    public function linkNewObjects()
    {
        $owner = $this->owner;
        foreach ($this->attributes as $index => $attribute)
        {
            $oldValues = $this->getLinkedIdsForAttribute($attribute);

            $toAdd = [];
            $toDelete = [];
            if (is_array($owner->{$attribute}))
            {
                $toAdd = array_diff($owner->{$attribute},$oldValues);
                $toDelete = array_diff($oldValues, $owner->{$attribute});
            }

            $relationName = $this->relations[$index];
            $relation = $owner->getRelation($relationName);
            $connectionClass = $this->connectionClasses[$index];

            $ownerAttribute = current(array_flip($relation->via->link));
            $relatedAttribute = current($relation->link);

            $connectionClass::deleteAll([
                $ownerAttribute=>$owner->id,
                $relatedAttribute=>$toDelete,
            ]);
//
            foreach ($toAdd as $id)
            {
                $obj = new $connectionClass([
                    $ownerAttribute=>$owner->id,
                    $relatedAttribute=>$id
                ]);
                $obj->save();
            }

        }

    }

    public function getLinkedIdsForAttribute($attribute)
    {
        $owner = $this->owner;

        $index = array_search($attribute, $this->attributes);
        $relationName = $this->relations[$index];

        return ArrayHelper::map($owner->{$relationName}, 'id', 'id');
    }

    public function loadLinkedObjects()
    {
        $owner = $this->owner;

        foreach ($this->attributes as $index => $attribute)
        {
            $relationName = $this->relations[$index];

            $owner->{$attribute} = ArrayHelper::map($owner->{$relationName}, 'id', 'id');

        }
    }

    public function saveAndLink($newOnly = false)
    {
        $owner = $this->owner;
        $transaction = \Yii::$app->getDb()->beginTransaction();
        try
        {
            if ($owner->save())
            {
                if ($newOnly == true)
                {
                    $this->linkNewObjects();
                }
                else
                {
                    $this->linkObjects();
                }
            }
            else
            {
                throw new \Exception();
            }
            $transaction->commit();
            return true;
        }
        catch (\Throwable $e)
        {
            $transaction->rollBack();
//            throw $e;
            return false;
        }
    }
}
