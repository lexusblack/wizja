<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "vehicle_model".
 *
 * @property integer $id
 * @property string $name
 * @property string $capacity
 * @property string $volume
 * @property integer $capacity_people
 */
class VehicleModel extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'vehiclePrices'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['capacity', 'volume'], 'number'],
            [['capacity_people', 'position', 'active'], 'integer'],
            [['name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vehicle_model';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => Yii::t('app', 'Nazwa'),
            'capacity' => Yii::t('app', 'Ładowność [kg]'),
            'volume' => Yii::t('app', 'Objętość [m3]'),
            'capacity_people' => Yii::t('app', 'Liczba pasażerów'),
            'position' => Yii::t('app', 'Kolejność na liście')
        ];
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVehiclePrices()
    {
        return $this->hasMany(\common\models\VehiclePrice::className(), ['vehicle_model_id' => 'id']);
    }

    public static function getList($id=false)
    {
        if ($id)
            return \common\helpers\ArrayHelper::map(VehicleModel::find()->where(['active'=>1])->orWhere(['id'=>$id])->orderBy(['position'=>SORT_ASC])->asArray()->all(), 'id', 'name');
        else 
            return \common\helpers\ArrayHelper::map(VehicleModel::find()->where(['active'=>1])->orderBy(['position'=>SORT_ASC])->asArray()->all(), 'id', 'name');
    }

    public function getVehicleTranslates()
    {
        return $this->hasMany(\common\models\VehicleTranslate::className(), ['vehicle_id' => 'id']);
    }


}
