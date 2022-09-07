<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "room".
 *
 * @property integer $id
 * @property string $name
 * @property integer $podkowa
 * @property integer $bankiet
 * @property integer $teatr
 * @property string $create_time
 * @property string $update_time
 * @property integer $location_id
 *
 * @property \common\models\Location $location
 * @property \common\models\RoomPhoto[] $roomPhotos
 */
//extends \yii\db\ActiveRecord
class Room extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;

    /**
     * @inheritdoc
     */
    public static function getDb() {
        return Yii::$app->db2;
    }
    public function rules()
    {
        return [
            [['podkowa', 'bankiet', 'teatr', 'location_id', 'area'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['name'], 'string', 'max' => 255]
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'room';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' =>  Yii::t('app', 'ID'),
            'name' =>  Yii::t('app', 'Nazwa'),
            'podkowa' =>  Yii::t('app', 'Podkowa'),
            'bankiet' =>  Yii::t('app', 'Bankiet'),
            'teatr' =>  Yii::t('app', 'Teatr'),
            'create_time' =>  Yii::t('app', 'Stworzono'),
            'update_time' =>  Yii::t('app', 'Zaktualizowano'),
            'location_id' =>  Yii::t('app', 'Miejsce'),
            'area' =>  Yii::t('app', 'Powierzchnia [m2]')
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLocation()
    {
        return $this->hasOne(\common\models\Location::className(), ['id' => 'location_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoomPhotos()
    {
        return $this->hasMany(\common\models\RoomPhoto::className(), ['room_id' => 'id']);
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
