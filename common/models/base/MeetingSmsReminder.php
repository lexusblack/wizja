<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "meeting_sms_reminder".
 *
 * @property integer $id
 * @property integer $meeting_id
 * @property integer $sms_id
 *
 * @property \common\models\Meeting $meeting
 * @property \common\models\NotificationSms $sms
 * @property string $aliasModel
 */
abstract class MeetingSmsReminder extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'meeting_sms_reminder';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['meeting_id', 'sms_id'], 'required'],
            [['meeting_id', 'sms_id'], 'integer'],
            [['meeting_id'], 'exist', 'skipOnError' => true, 'targetClass' => Meeting::className(), 'targetAttribute' => ['meeting_id' => 'id']],
            [['sms_id'], 'exist', 'skipOnError' => true, 'targetClass' => NotificationSms::className(), 'targetAttribute' => ['sms_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' =>  Yii::t('app', 'ID'),
            'meeting_id' =>  Yii::t('app', 'ID spotkania'),
            'sms_id' =>  Yii::t('app', 'ID smsa'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMeeting()
    {
        return $this->hasOne(\common\models\Meeting::className(), ['id' => 'meeting_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSms()
    {
        return $this->hasOne(\common\models\NotificationSms::className(), ['id' => 'sms_id']);
    }




}