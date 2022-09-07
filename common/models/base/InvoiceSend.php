<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "invoice_send".
 *
 * @property integer $id
 * @property integer $invoice_id
 * @property string $recipient
 * @property string $datetime
 * @property string $filename
 * @property integer $user_id
 */
class InvoiceSend extends \yii\db\ActiveRecord
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
            [['invoice_id', 'user_id'], 'integer'],
            [['datetime'], 'safe'],
            [['recipient', 'filename'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice_send';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_id' => 'Invoice ID',
            'recipient' => 'Recipient',
            'datetime' => 'Datetime',
            'filename' => 'Filename',
            'user_id' => 'User ID',
        ];
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }
}
