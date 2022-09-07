<?php

namespace common\models;

use Yii;
use \common\models\base\OfferStatut as BaseOfferStatut;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "offer_statut".
 */
class OfferStatut extends BaseOfferStatut
{
    /**
     * @inheritdoc
     */

    public $users;
    public $groups;
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['is_send', 'is_accepted', 'visible_in_planning', 'visible_in_finances', 'reminder_sms', 'reminder_mail', 'reminder_pm', 'blocked'], 'integer'],
            [['name', 'color', 'reminder_users', 'reminder_groups', 'reminder_text'], 'string', 'max' => 255],
            [['users', 'groups'], 'safe']
        ]);
    }

    public function getList()
    {
        return ArrayHelper::map(OfferStatut::find()->where(['active'=>1])->orderBy(['position'=>SORT_ASC])->asArray()->all(), 'id', 'name');
    }

        public function beforeSave($insert)
    {
            if ($this->users)
                $this->reminder_users = implode(';', $this->users);
            else
                $this->reminder_users = "";
            if ($this->groups)
                $this->reminder_groups = implode(';', $this->groups);
            else
                $this->reminder_groups = "";

        return parent::beforeSave($insert);
    }
	
}
