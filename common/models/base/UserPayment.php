<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "user_payment".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $month
 * @property integer $year
 * @property string $amount
 * @property integer $creator_id
 * @property string $datetime
 * @property string $description
 */
class UserPayment extends \yii\db\ActiveRecord
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
            [['user_id', 'month', 'year', 'creator_id'], 'integer'],
            [['amount'], 'number'],
            [['datetime', 'payment_method'], 'safe'],
            [['description'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user_payment';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'month' => 'Month',
            'year' => 'Year',
            'amount' => 'Amount',
            'creator_id' => 'Creator ID',
            'datetime' => 'Datetime',
            'description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'creator_id']);
    }
}
