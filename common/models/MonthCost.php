<?php

namespace common\models;
use yii\helpers\ArrayHelper;

use Yii;
use \common\models\base\MonthCost as BaseMonthCost;

/**
 * This is the model class for table "month_cost".
 */
class MonthCost extends BaseMonthCost
{
    /**
     * @inheritdoc
     */
    public $sections;
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['department_id', 'creator_id', 'type', 'group_id'], 'integer'],
            [['create_time', 'update_time', 'sections'], 'safe'],
            [['amount'], 'number'],
        ]);
    }

    public function beforeSave($insert)
    {

        if (sizeof($this->sections) > 1)
        {
            $this->type = 1;
            $this->section = implode(';', $this->sections);
        }
        else
        {
            $this->type = 2;
            $this->section = ArrayHelper::getValue($this->sections, 0, null);
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        //kasujemy grupowe
        static::deleteAll([
            'group_id'=>$this->id,
        ]);
        if ($this->type == 1 && $this->group_id==null)
        {
            $sectionCount = sizeof($this->sections);
            foreach ($this->sections as $section)
            {
                $model = new static($this->attributes);
                $model->sections = [$section];
                $model->id = null;
                $model->type = 2;
                $model->amount = round(($this->amount/$sectionCount), 2);
                $model->group_id = $this->id;

                if ($model->save() == false)
                {
                    var_dump($model->errors); die;
                }
            }
        }
    }


    public function loadSections()
    {
        if ($this->type == 2)
        {
            $this->sections = [$this->section];
        }
        else if ($this->isNewRecord == false)
        {
            $sections = static::find()
                ->select(['section'])
                ->where([
                    'group_id'=>$this->id,
                ])
                ->column();
            $this->sections = array_unique(array_filter($sections));
        }
    }
	
}
