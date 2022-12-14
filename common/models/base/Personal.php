<?php
// This class was automatically generated by a giiant build task
// You should not change it manually as it will be overwritten on next build

namespace common\models\base;

use Yii;

/**
 * This is the base-model class for table "personal".
 *
 * @property integer $id
 * @property string $name
 * @property string $location
 * @property string $start_time
 * @property string $end_time
 * @property integer $repeat
 * @property string $repeat_since
 * @property integer $reminder
 * @property string $description
 * @property integer $user_id
 * @property string $create_time
 * @property string $update_time
 * @property integer $status
 * @property integer $typ
 * @property integer $parent_id
 * @property integer $remind_sms
 * @property integer $notification_sms_id
 * @property integer $notification_mail_id
 * @property integer $remind_email
 * @property integer $remind_push
 *
 * @property \common\models\User $user
 * @property \common\models\NotificationSms $notificationSms
 * @property \common\models\NotificationMail $notificationMail
 * @property string $aliasModel
 */
abstract class Personal extends \common\components\BaseActiveRecord
{



    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'personal';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['start_time', 'end_time', 'repeat_since', 'create_time', 'update_time'], 'safe'],
            [['repeat', 'reminder', 'user_id', 'status', 'typ', 'parent_id', 'remind_sms', 'notification_sms_id', 'remind_email', 'remind_push'], 'integer'],
            [['description'], 'string'],
            [['name', 'location'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['notification_sms_id'], 'exist', 'skipOnError' => true, 'targetClass' => NotificationSms::className(), 'targetAttribute' => ['notification_sms_id' => 'id']]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Nazwa'),
            'location' => Yii::t('app', 'Miejsce'),
            'start_time' => Yii::t('app', 'Od'),
            'end_time' => Yii::t('app', 'Do'),
            'repeat' => Yii::t('app', 'Powtarzaj'),
            'repeat_since' => Yii::t('app', 'A?? do'),
            'reminder' => Yii::t('app', 'Przypomnij'),
            'description' => Yii::t('app', 'Opis'),
            'user_id' => Yii::t('app', 'U??ytkownik'),
            'create_time' => Yii::t('app', 'Stworzono'),
            'update_time' => Yii::t('app', 'Zaktualizowano'),
            'status' => Yii::t('app', 'Status'),
            'typ' => Yii::t('app', 'Typ'),
            'parent_id' => Yii::t('app', 'ID rodzica'),
            'remind_sms' => Yii::t('app', 'Powiadomienia SMS'),
            'remind_email' => Yii::t('app', 'Powiadomienia EMAIL'),
            'remind_push' => Yii::t('app', 'Powiadomienia PUSH'),
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
    public function getNotificationSms()
    {
        return $this->hasOne(\common\models\NotificationSms::className(), ['id' => 'notification_sms_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotificationMail()
    {
        return $this->hasOne(\common\models\NotificationMail::className(), ['id' => 'notification_mail_id']);
    }

}
