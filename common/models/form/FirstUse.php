<?php
namespace common\models\form;

use Yii;
use common\models\Department;
use common\models\Skill;
use common\models\UserEventRole;
use common\models\User;
use yii\base\Model;

class FirstUse extends Model
{
    public $show;
    public $companyData;
    public $departments;
    public $roles;
    public $skills;
    public $users;

    public function init()
    {
        $this->show = false;
        if ((Yii::$app->settings->get('companyAddress', 'main')=="")||(Yii::$app->settings->get('companyCity', 'main')=="")||(Yii::$app->settings->get('companyName', 'main')==""))
        {
            $this->show = true;
            $this->companyData = true;
        }else{
            $this->companyData = false;
        }
        $departments = Department::find()->count();
        if (!$departments)
        {
            $this->departments = true;
            $this->show = true;
        }else{
            $this->departments = false;
        }
        $skills = Skill::find()->count();
        if (!$skills)
        {
            $this->skills = true;
            $this->show = true;
        }else{
            $this->skills = false;
        }
        $roles = UserEventRole::find()->count();
        if (!$roles)
        {
            $this->roles = true;
            $this->show = true;
        }else{
            $this->roles = false;
        }
        $users = User::find()->count();
         if ($users<2)
        {
            $this->users = true;
            $this->show = true;
        }else{
            $this->users = false;
        }       
    }

}