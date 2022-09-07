<?php

namespace common\models;

use Yii;
use \common\models\base\EventAdditionalStatut as BaseEventAdditionalStatut;

/**
 * This is the model class for table "event_additional_statut".
 */
class EventAdditionalStatut extends BaseEventAdditionalStatut
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
            [['active'], 'integer'],
            [['name', 'permission_users', 'permission_teams'], 'string', 'max' => 255],
            [['users', 'teams'], 'safe'],
        ]);
    }

    public function showToUser()
    {
        if (($this->permission_users=="")&&($this->permission_teams==""))
        {
            return true;
        }else{
            $users = explode(';', $this->permission_users);
            if (in_array(Yii::$app->user->id, $users))
            {
                return true;
            }
            $teams = explode(';', $this->permission_teams);
            if (TeamUser::find()->where(['user_id'=>Yii::$app->user->id, 'team_id'=>$teams])->one())
                return true;
        }
        return false;
    }
    

    public function beforeSave($insert)
    {
            if ($this->users)
                $this->permission_users = implode(';', $this->users);
            else
                $this->permission_users = "";
            if ($this->teams)
                $this->permission_teams = implode(';', $this->teams);
            else
                $this->permission_teams = "";

        return parent::beforeSave($insert);
    }

    public function getStatusList($s=false)
    {
        $list = \common\helpers\ArrayHelper::map(EventAdditionalStatutName::find()->where(['active'=>1])->andWhere(['event_additional_statut_id'=>$this->id])->orderBy(['position'=>SORT_ASC])->asArray()->all(), 'id', 'name');
        if (!$s)
        $list[""] = "";
        return $list;
    }

}
