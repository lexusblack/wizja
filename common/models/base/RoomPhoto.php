<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "room_photo".
 *
 * @property integer $id
 * @property string $filename
 * @property string $extension
 * @property integer $status
 * @property string $create_time
 * @property string $update_time
 * @property integer $room_id
 * @property string $mime_type
 * @property string $base_name
 *
 * @property \common\models\Room $room
 */
class RoomPhoto extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;
    public static function getDb() {
        return Yii::$app->db2;
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status', 'room_id'], 'integer'],
            [['create_time', 'update_time'], 'safe'],
            [['filename', 'base_name'], 'string', 'max' => 255],
            [['extension', 'mime_type'], 'string', 'max' => 45]
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'room_photo';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' =>  Yii::t('app', 'ID'),
            'filename' =>  Yii::t('app', 'Nazwa pliku'),
            'extension' =>  Yii::t('app', 'Rozszerzenie'),
            'status' =>  Yii::t('app', 'Status'),
            'create_time' =>  Yii::t('app', 'Stworzono'),
            'update_time' =>  Yii::t('app', 'Zaktualizowano'),
            'room_id' =>  Yii::t('app', 'ID pokoju'),
            'mime_type' =>  Yii::t('app', 'Typ Mime'),
            'base_name' =>  Yii::t('app', 'Nazwa podstawowa'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoom()
    {
        return $this->hasOne(\common\models\Room::className(), ['id' => 'room_id']);
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
