<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "event_breaks_user".
 *
 * @property integer $event_break_id
 * @property integer $user_id
 *
 * @property \common\models\EventBreaks $eventBreak
 * @property \common\models\User $user
 * @property string $aliasModel
 */
abstract class EventBreaksUser extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_breaks_user';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_break_id', 'user_id'], 'required'],
            [['event_break_id', 'user_id'], 'integer'],
            [['event_break_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventBreaks::className(), 'targetAttribute' => ['event_break_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'event_break_id' => Yii::t('app', 'ID przerwy wydarzenia'),
            'user_id' => Yii::t('app', 'ID użytkownika'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventBreak()
    {
        return $this->hasOne(\common\models\EventBreaks::className(), ['id' => 'event_break_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_id']);
    }




}
