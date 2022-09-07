<?php

namespace common\models;

use Yii;
use \common\models\base\Purchase as BasePurchase;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "purchase".
 */
class Purchase extends BasePurchase
{
    /**
     * @inheritdoc
     */

    public $eventIds;
    public $sections;

    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['user_id', 'purchase_type_id', 'group_id'],  'integer'],
            [['datetime', 'sections'], 'safe'],
            [['description'], 'string'],
            [['price'], 'number'],
            [['section'], 'string'],
            [['code'], 'string'],
            [['eventIds'], 'each', 'rule'=>['integer']],
        ]);
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['link'] = [
            'class' => \common\behaviors\LinkBehavior::className(),
            'attributes' => [
                'eventIds',
            ],
            'relations' => [
                'events',
            ],
            'modelClasses'=>[
                'common\models\Event',
            ],
        ];

        $behaviors['codeBehavior'] = [
            'class'=>\common\behaviors\CodeBehavior::className(),
            'prefix' => 'Z',
        ];

        return $behaviors;
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
                $model->price = round(($this->price/$sectionCount), 2);
                $model->group_id = $this->id;

                if ($model->save() == false)
                {
                    var_dump($model->errors); die;
                }
            }
        }
    }
	
}
