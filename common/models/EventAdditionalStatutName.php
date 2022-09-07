<?php

namespace common\models;

use Yii;
use \common\models\base\EventAdditionalStatutName as BaseEventAdditionalStatutName;
use kartik\helpers\Html;

/**
 * This is the model class for table "event_additional_statut_name".
 */
class EventAdditionalStatutName extends BaseEventAdditionalStatutName
{
    /**
     * @inheritdoc
     */
        public $users;
    public $teams;

    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['event_additional_statut_id', 'reminder_mail', 'reminder_sms', 'reminder_pm', 'active', 'position'], 'integer'],
            [['name', 'reminder_users', 'reminder_teams'], 'string', 'max' => 255],
            [['icon'], 'string', 'max' => 45],
            [['users', 'teams'], 'safe'],
        ]);
    }
	
        public function beforeSave($insert)
    {
            if ($this->users)
                $this->reminder_users = implode(';', $this->users);
            else
                $this->reminder_users = "";
            if ($this->teams)
                $this->reminder_teams = implode(';', $this->teams);
            else
                $this->reminder_teams = "";
            if ($insert)
            {
                $this->position = EventAdditionalStatutName::find()->where(['active'=>1, 'event_additional_statut_id'=>$this->event_additional_statut_id])->count();
            }

        return parent::beforeSave($insert);
    }

    public function sendReminders($id)
    {
        if (($this->reminder_sms)||($this->reminder_mail))
        {
            $users = explode(";", $this->reminder_users);
            $event = Event::findOne($id);
            $users2 = \common\helpers\ArrayHelper::map(TeamUser::find()->where(['team_id'=>explode(";", $this->reminder_teams)])->asArray()->all(), 'user_id', 'user_id');
            if ($this->reminder_pm)
            {
                $users3 = $event->manager_id;
            }else{
                $users3 = null;
            }
            $u = User::find()->where(['id'=>$users])->orWhere(['id'=>$users2])->orWhere(['id'=>$users3])->all();
             $text = Yii::t('app', 'Zmieniono status ').$this->eventAdditionalStatut->name." ".$event->name.Yii::t('app', ' na ').$this->name;
            foreach ($u as $user)
            {
                            if ($this->reminder_sms)
                            {
                                Notification::sendUserSmsNotification($user, $text, false);
                            }
                            if ($this->reminder_mail)
                            {
                                Notification::sendUserMailNotification($user, Yii::t('app', 'Wiadomość automatyczna'), $text." ".Html::a(Yii::t('app', 'Zobacz'), "http://".Yii::$app->getRequest()->serverName.'/admin/event/view?id='.$event->id ));
                            } 
            }
        }
    }
}
