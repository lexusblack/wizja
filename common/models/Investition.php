<?php

namespace common\models;

use Yii;
use \common\models\base\Investition as BaseInvestition;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "investition".
 */
class Investition extends BaseInvestition
{
    public $sections;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['quantity',  'expense_id', 'creator_id', 'type', 'group_id'], 'integer'],
            [['price', 'total_price', 'vat'], 'number'],
            [['create_time', 'datetime', 'sections'], 'safe'],
            [['name', 'section'], 'string', 'max' => 255]
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
                $model->total_price = round(($this->total_price/$sectionCount), 2);
                $model->group_id = $this->id;

                if ($model->save() == false)
                {
                    var_dump($model->errors); die;
                }
            }
        }
    }
    
	
}
