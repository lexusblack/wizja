<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "rfid_command".
 *
 * @property integer $id
 * @property string $reader
 * @property string $command
 * @property string $content
 * @property integer $status
 * @property string $create_time
 * @property string $done_time
 */
class RfidCommand extends \yii\db\ActiveRecord
{


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            ''
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['reader', 'command'], 'required'],
            [['status'], 'integer'],
            [['create_time', 'done_time'], 'safe'],
            [['reader', 'command', 'content', 'info'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rfid_command';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'reader' => 'Reader',
            'command' => 'Command',
            'content' => 'Content',
            'status' => 'Status',
            'create_time' => 'Create Time',
            'done_time' => 'Done Time',
        ];
    }

    /**
     * @inheritdoc
     * @return array mixed
     */
    public function behaviors()
    {
        date_default_timezone_set(Yii::$app->params['timeZone']);

        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'create_time',
                'updatedAtAttribute' => false,
                'value' => date('Y-m-d H:i:s'),
            ],
        ];
    }

}
