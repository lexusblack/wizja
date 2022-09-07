<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_offer_role".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $user_role_id
 * @property integer $quantity
 * @property string $schedule
 */
class EventOfferRole extends \yii\db\ActiveRecord
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
            [['event_id', 'user_role_id', 'quantity'], 'integer'],
            [['schedule'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_offer_role';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'user_role_id' => 'User Role ID',
            'quantity' => 'Quantity',
            'schedule' => 'Schedule',
        ];
    }

        /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(\common\models\UserEventRole::className(), ['id' => 'user_role_id']);
    } 
}
