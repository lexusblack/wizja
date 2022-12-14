<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "planboard_user_general_event_order".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $event_id
 * @property integer $user_event
 * @property integer $order_key
 *
 * @property \common\models\User $user
 * @property \common\models\User $userEvent
 * @property \common\models\Event $event
 * @property string $aliasModel
 */
abstract class PlanboardUserGeneralEventOrder extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'planboard_user_general_event_order';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'event_id', 'user_event', 'order_key'], 'required'],
            [['user_id', 'event_id', 'user_event', 'order_key'], 'integer'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['user_event'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_event' => 'id']],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::className(), 'targetAttribute' => ['event_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'user_id' => Yii::t('app', 'ID użytkownika'),
            'event_id' => Yii::t('app', 'ID wydarzenia'),
            'user_event' => Yii::t('app', 'Użytkownik wydarzenia'),
            'order_key' => Yii::t('app', 'Kolejność'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserEvent()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_event']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(\common\models\Event::className(), ['id' => 'event_id']);
    }




}
