<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "deal".
 *
 * @property integer $id
 * @property string $filename
 * @property string $extension
 * @property string $create_time
 * @property string $update_time
 * @property integer $event_id
 * @property string $mime_type
 * @property string $base_name
 *
 * @property \common\models\Event $event
 */
class Deal extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'event'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['create_time', 'update_time'], 'safe'],
            [['event_id'], 'required'],
            [['event_id'], 'integer'],
            [['filename', 'extension', 'mime_type', 'base_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'deal';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'filename' => 'Filename',
            'extension' => 'Extension',
            'create_time' => 'Create Time',
            'update_time' => 'Update Time',
            'event_id' => 'Event ID',
            'mime_type' => 'Mime Type',
            'base_name' => 'Base Name',
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
