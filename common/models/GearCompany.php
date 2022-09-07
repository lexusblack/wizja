<?php

namespace common\models;
use Yii;
use yii\behaviors\TimestampBehavior;
use \common\models\base\GearCompany as BaseGearCompany;

/**
 * This is the model class for table "gear_company".
 */
class GearCompany extends BaseGearCompany
{
    /**
     * @inheritdoc
     */
    public $existingModels = null;

    /**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => 'update_time',
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }
    /**
     * @inheritdoc
     * @return \common\models\query\GearCompanyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\GearCompanyQuery(get_called_class());
    }
    
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['create_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ]);
    }

    public static function getList($term=null)
    {
        $models = static::find()
            ->andFilterWhere([ 'or',
                ['like', 'name', $term]

            ])
            ->all();
        $list = [];

        foreach ($models as $model)
        {
            $list[$model->id] = $model->getDisplayLabel();
        }

        return $list;
    }

    public function getDisplayLabel()
    {
        $attributes = [
            $this->name
        ];
        $attributes = array_filter($attributes);
        return implode(', ', $attributes);
    }
	
}
