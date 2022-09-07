<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "rent_gear".
 *
 * @property integer $id
 * @property integer $rent_id
 * @property integer $gear_id
 * @property string $start_time
 * @property string $end_time
 * @property string $rent_gearcol
 * @property integer $type
 * @property string $create_time
 * @property string $update_time
 *
 * @property \common\models\Gear $gear
 * @property \common\models\Rent $rent
 */
class RentGear extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'gear',
            'rent'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['rent_id', 'gear_id', 'type'], 'integer'],
            [['start_time', 'end_time', 'create_time', 'update_time'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rent_gear';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'rent_id' => 'Rent ID',
            'gear_id' => 'Gear ID',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
            'type' => 'Type',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGear()
    {
        return $this->hasOne(\common\models\Gear::className(), ['id' => 'gear_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRent()
    {
        return $this->hasOne(\common\models\Rent::className(), ['id' => 'rent_id']);
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
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }
}
