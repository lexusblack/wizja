<?php

namespace common\models\base;

use Yii;

/**
 * This is the base model class for table "event_additional_statut_name".
 *
 * @property integer $id
 * @property integer $event_additional_statut_id
 * @property string $name
 * @property string $icon
 * @property integer $reminder_mail
 * @property integer $reminder_sms
 * @property integer $reminder_pm
 * @property string $reminder_users
 * @property string $reminder_teams
 * @property integer $active
 * @property integer $position
 *
 * @property \common\models\EventAdditionalStatut $eventAdditionalStatut
 */
class EventAdditionalStatutName extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;


    /**
    * This function helps \mootensai\relation\RelationTrait runs faster
    * @return array relation names of this model
    */
    public function relationNames()
    {
        return [
            'eventAdditionalStatut'
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['event_additional_statut_id', 'reminder_mail', 'reminder_sms', 'reminder_pm', 'active', 'position'], 'integer'],
            [['name', 'reminder_users', 'reminder_teams'], 'string', 'max' => 255],
            [['icon'], 'string', 'max' => 45]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event_additional_statut_name';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_additional_statut_id' => 'Event Additional Statut ID',
            'name' => Yii::t('app', 'Nazwa'),
            'icon' => Yii::t('app', 'Ikona'),
            'reminder_mail' => Yii::t('app', 'Powiadomienie mailowe'),
            'reminder_sms' => Yii::t('app', 'Powiadomienie SMS'),
            'reminder_pm' => Yii::t('app', 'Powiadomienie dla PM'),
            'reminder_users' => Yii::t('app', 'Powiadom użytkowników'),
            'reminder_teams' => Yii::t('app', 'Powiadom grupy użytkowników'),
            'active' => 'Active',
            'position' => 'Position',
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEventAdditionalStatut()
    {
        return $this->hasOne(\common\models\EventAdditionalStatut::className(), ['id' => 'event_additional_statut_id']);
    }
    }
