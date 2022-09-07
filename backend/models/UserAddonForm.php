<?php
namespace backend\models;

use backend\models\base\AssignSortableForm;
use common\helpers\ArrayHelper;
use common\helpers\Url;
use common\models\AddonRate;
use common\models\Event;
use common\models\EventUserRole;
use common\models\User;
use common\models\UserAddonRate;
use common\models\UserEventRole;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class UserAddonForm extends AssignSortableForm
{
    public $roleId;
    public $level = 1;
    public $period = 0;

    public $currentRole;

    public function rules()
    {
        $rules = [

        ];

        return array_merge(parent::rules(), $rules);
    }

    public function getItems()
    {
        $items = [];

        $users = User::getList([User::ROLE_ADMIN, User::ROLE_USER, User::ROLE_PROJECT_MANAGER, User::ROLE_SUPERADMIN], true);
        $assigned = $this->getAssignedItems();

        $users = array_diff_key($users, $assigned);

        foreach ($users as $id=>$name)
        {
            $items[$id] = [
                'content' => $name,
            ];
        }
        return $items;
    }

    public function getAssignedItems($rateId=null)
    {
        // Ludziki, którzy zostali przypisani dla tej roli i na tym poziomie eventu
        // wszystkie okresy rozliczeniowe
        $items = [];
        $users = User::find()
            ->innerJoinWith(['userAddonRates' => function ($q)
            {
                $q->innerJoinWith('rate');
            }
            ])
            ->where([
                'user_addon_rate.role_id'=>$this->roleId,
                'addon_rate.level' => $this->level,
            ])
            ->andFilterWhere([
                'user_addon_rate.rate_id'=>$rateId
            ])
            ->orderBy(['last_name'=>SORT_ASC, 'first_name'=>SORT_ASC])
            ->all();
        foreach ($users as $user)
        {
            $items[$user->id] = [
                'content' => $user->getDisplayLabel(),
            ];
        }

        return $items;
    }

    public function save()
    {
        $assignedIds = [];
        UserAddonRate::deleteAll([
            'role_id'=>$this->roleId,
            'rate_id'=>array_keys($this->getRateList()),

        ]);
        foreach ($this->assignedItems as $rateId=>$users)
        {
            if(empty($users)==true)
            {
                continue;
            }
            $userIds = explode(',', $users);
            foreach ($userIds as $uId)
            {
                $model = new UserAddonRate();
                $model->user_id = $uId;
                $model->rate_id = $rateId;
                $model->role_id = $this->roleId;
                if ($model->save() == false)
                {
                    $message = VarDumper::dumpAsString($model->errors,10, true);
                    throw new HttpException(666, $message);
                }
            }

        }

        return true;
    }

    public function getRateList()
    {
        $models = AddonRate::find()
            ->where([
                'level'=>$this->level,
                'period' => $this->period,
            ])
            ->orderBy(['amount'=>SORT_ASC])
            ->all();

        $formatter = \Yii::$app->formatter;

        $list = [];
        foreach ($models as $model)
        {
        	if (key_exists($this->roleId, $model->getRoles()->indexBy('id')->asArray()->all())){
        		$list[ $model->id ] = $formatter->asCurrency( $model->amount );
		        if ( empty( $model->name ) == false ) {
			        $list[ $model->id ] .= ' (' . $model->name . ')';
		        }
	        }
        }
        return $list;

    }

    public function getMenuItems()
    {
        $route = \Yii::$app->controller->route;
        $items = [];
        $models = UserEventRole::find()->orderBy(['name'=>SORT_ASC])->all();
        foreach ($models as $model)
        {
            $url = Url::to([$route, 'roleId'=>$model->id, 'level'=>$this->level, 'period'=>$this->period]);
//            var_dump(Url::current(), $url);
            $active = ($url==Url::current());

            $items[] = [
                'label'=>$model->name,
                'url'=>$url,
                'linkOptions'=>[
                    'class'=>$active ? 'btn-primary' : '',
                ]
            ];
        }
        return $items;
    }

    public function getCurrentRoleName()
    {
        $model = UserEventRole::findOne($this->roleId);
        return $model->name;
    }

    public function getMenu2Items()
    {
        $route = \Yii::$app->controller->route;
        $items = [];
        foreach (Event::getLevelList() as $level)
        {
            $url = Url::to([$route, 'roleId'=>$this->roleId, 'level'=>$level, 'period'=>$this->period]);
            $active = ($url==Url::current());
            $items[] = [
                'label'=>$level,
                'url'=>$url,
                'linkOptions'=>[
                    'class'=>$active ? 'btn-primary' : '',
                ]
            ];
        }
        return $items;
    }

    public function getMenu3Items()
    {
        $route = \Yii::$app->controller->route;
        $items = [];
        foreach (AddonRate::getPeriodList() as $period=>$label)
        {
            $url = Url::to([$route, 'roleId'=>$this->roleId, 'level'=>$this->level, 'period'=>$period]);
            $active = ($url==Url::current());
            $items[] = [
                'label'=>$label,
                'url'=>$url,
                'linkOptions'=>[
                    'class'=>$active ? 'btn-primary' : '',
                ]
            ];
        }
        return $items;
    }

    public function getPeriodLabel()
    {
        return ArrayHelper::getValue(AddonRate::getPeriodList(), $this->period, UNDEFINDED_STRING);
    }
}