<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "vehicle_price".
 *
 * @property integer $id
 * @property integer $vehicle_model_id
 * @property string $name
 * @property string $price
 * @property string $cost
 * @property string $unit
 * @property integer $default
 *
 * @property \common\models\VehicleModel $vehicleModel
 */
class VehiclePrice extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'vehicleModel'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'price'], 'required'],
            [['vehicle_model_id', 'default'], 'integer'],
            [['price', 'cost'], 'number'],
            [['name', 'unit', 'currency'], 'string'],
            
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vehicle_price';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'vehicle_model_id' => 'Vehicle Model ID',
            'name' => Yii::t('app', 'Nazwa'),
            'price' => Yii::t('app', 'Cena'),
            'cost' => Yii::t('app', 'Koszt'),
            'unit' => Yii::t('app', 'Jednostka'),
            'default' => Yii::t('app', 'Domyślna'),
            'currency' => Yii::t('app', 'Waluta'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVehicleModel()
    {
        return $this->hasOne(\common\models\VehicleModel::className(), ['id' => 'vehicle_model_id']);
    }

    public function getList($vehicle_id, $currency)
    {
        
        return \common\helpers\ArrayHelper::map(VehiclePrice::find()->where(['vehicle_model_id'=>$vehicle_id, 'currency'=>$currency])->asArray()->all(), 'id', 'name');
    }
    }
