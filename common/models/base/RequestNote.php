<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "request_note".
 *
 * @property integer $id
 * @property string $text
 * @property integer $request_id
 * @property string $datetime
 * @property string $user_name
 * @property integer $user_id
 * @property integer $type
 */
class RequestNote extends \yii\db\ActiveRecord
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
            [['request_id', 'user_id', 'type'], 'integer'],
            [['text'], 'string'],
            [['datetime'], 'safe'],
            [['user_name'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'request_note';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db2');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text' => 'Text',
            'request_id' => 'Request ID',
            'datetime' => 'Datetime',
            'user_name' => 'User Name',
            'user_id' => 'User ID',
            'type' => 'Type',
        ];
    }

    public function getRequest()
    {
        return $this->hasOne(\common\models\Request::className(), ['id' => 'request_id']);
    }
}
