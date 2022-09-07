<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "service".
 *
 * @property integer $id
 * @property string $name
 * @property integer $service_category_id
 * @property integer $in_offer
 * @property integer $position
 * @property string $create_time
 * @property string $update_time
 *
 * @property \common\models\ServiceCategory $serviceCategory
 */
class Service extends \yii\db\ActiveRecord
{


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'serviceCategory'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['service_category_id', 'in_offer', 'position'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'service_category_id' => Yii::t('app', 'Kategoria usługi'),
            'in_offer' => Yii::t('app', 'W ofercie'),
            'position' => Yii::t('app', 'Pozycja'),
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServiceCategory()
    {
        return $this->hasOne(\common\models\ServiceCategory::className(), ['id' => 'service_category_id']);
    }
    
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
}
