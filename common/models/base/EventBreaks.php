<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "event_breaks".
 *
 * @property integer $id
 * @property integer $event_id
 * @property string $name
 * @property string $start_time
 * @property string $end_time
 * @property integer $icon
 *
 * @property \common\models\Event $event
 * @property \common\models\EventBreaksUser[] $eventBreaksUsers
 * @property \common\models\User[] $users
 * @property string $aliasModel
 */
abstract class EventBreaks extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_breaks';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_id', 'name', 'start_time', 'end_time', 'icon'], 'required'],
            [['event_id', 'icon'], 'integer'],
            [['start_time', 'end_time'], 'safe'],
            [['name'], 'string', 'max' => 255],
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
            'event_id' => Yii::t('app', 'ID wydarzenia'),
            'name' => Yii::t('app', 'Nazwa'),
            'start_time' => Yii::t('app', 'Początek'),
            'end_time' => Yii::t('app', 'Koniec'),
            'icon' => Yii::t('app', 'Ikona'),
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
     * @return \yii\db\ActiveQuery
     */
    public function getEventBreaksUsers()
    {
        return $this->hasMany(\common\models\EventBreaksUser::className(), ['event_break_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(\common\models\User::className(), ['id' => 'user_id'])->viaTable('event_breaks_user', ['event_break_id' => 'id']);
    }




}
