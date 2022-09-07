<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "ride".
 *
 * @property integer $id
 * @property integer $vehicle_id
 * @property integer $user_id
 * @property integer $event_id
 * @property string $start
 * @property string $end
 * @property integer $km_start
 * @property integer $km_end
 * @property string $start_place
 * @property string $end_place
 * @property string $description
 *
 * @property \common\models\Event $event
 * @property \common\models\Vehicle $vehicle
 * @property \common\models\User $user
 */
class Ride extends \yii\db\ActiveRecord
{


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'event',
            'vehicle',
            'user'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['vehicle_id', 'user_id', 'event_id', 'km_start', 'km_end'], 'integer'],
            [['vehicle_id', 'user_id', 'km_start', 'start'], 'required'],
            [['start', 'end'], 'safe'],
            [['description'], 'string'],
            [['start_place', 'end_place'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ride';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'vehicle_id' => Yii::t('app', 'Pojazd'),
            'user_id' => Yii::t('app', 'Użytkownik'),
            'event_id' => Yii::t('app', 'Wydarzenie'),
            'start' => Yii::t('app', 'Data początku podróży'),
            'end' => Yii::t('app', 'Data końca podróży'),
            'km_start' => Yii::t('app', 'Początkowy stan licznika'),
            'km_end' => Yii::t('app', 'Końcowy stan licznika'),
            'start_place' => Yii::t('app', 'Miejsce startu'),
            'end_place' => Yii::t('app', 'Miejsce docelowe'),
            'description' => Yii::t('app', 'Opis'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(\common\models\Event::className(), ['id' => 'event_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getVehicle()
    {
        return $this->hasOne(\common\models\Vehicle::className(), ['id' => 'vehicle_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }
    }
