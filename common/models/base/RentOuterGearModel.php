<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "rent_outer_gear_model".
 *
 * @property integer $id
 * @property integer $rent_id
 * @property integer $outer_gear_model_id
 * @property integer $quantity
 * @property string $start_time
 * @property string $end_time
 * @property string $create_time
 * @property string $update_time
 * @property integer $type
 * @property integer $resolved
 *
 * @property \common\models\Rent $rent
 * @property \common\models\OuterGearModel $outerGearModel
 */
class RentOuterGearModel extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'rent',
            'outerGearModel'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rent_id', 'outer_gear_model_id', 'quantity', 'type', 'resolved'], 'integer'],
            [['start_time', 'end_time', 'create_time', 'update_time'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rent_outer_gear_model';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rent_id' => 'Rent ID',
            'outer_gear_model_id' => 'Outer Gear Model ID',
            'quantity' => 'Quantity',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'type' => 'Type',
            'resolved' => 'Resolved',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRent()
    {
        return $this->hasOne(\common\models\Rent::className(), ['id' => 'rent_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOuterGearModel()
    {
        return $this->hasOne(\common\models\OuterGearModel::className(), ['id' => 'outer_gear_model_id']);
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
            ],
        ];
    }
}
