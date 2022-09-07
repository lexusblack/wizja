<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "crn_chat_message".
 *
 * @property integer $id
 * @property integer $crn_chat_id
 * @property integer $user_id
 * @property string $text
 * @property string $datetime
 * @property string $company
 * @property string $user
 * @property integer $read
 */
class CrnChatMessage extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


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
            [['crn_chat_id', 'user_id', 'read'], 'integer'],
            [['text'], 'string'],
            [['datetime'], 'safe'],
            [['company'], 'string', 'max' => 45],
            [['user'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'crn_chat_message';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db2');
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getCcompany()
    {
        return $this->hasOne(\common\models\Company::className(), ['code' => 'company']);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'crn_chat_id' => 'Crn Chat ID',
            'user_id' => 'User ID',
            'text' => 'Text',
            'datetime' => 'Datetime',
            'company' => 'Company',
            'user' => 'User',
            'read' => 'Read',
        ];
    }
}
