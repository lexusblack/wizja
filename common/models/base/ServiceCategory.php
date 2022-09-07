<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "service_category".
 *
 * @property integer $id
 * @property string $name
 * @property integer $in_offer
 * @property integer $position
 * @property string $create_time
 * @property string $update_time
 *
 * @property \common\models\Service[] $services
 */
class ServiceCategory extends \yii\db\ActiveRecord
{

    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'services'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['in_offer', 'position'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'service_category';
    }



    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'in_offer' => Yii::t('app', 'W ofercie'),
            'position' => Yii::t('app', 'Pozycja'),
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getServices()
    {
        return $this->hasMany(\common\models\Service::className(), ['service_category_id' => 'id'])->
      orderBy(['position' => SORT_ASC]);
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
