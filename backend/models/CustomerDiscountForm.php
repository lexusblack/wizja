<?php
namespace backend\models;

use backend\models\base\AssignSortableForm;
use common\helpers\ArrayHelper;
use common\models\AddonRate;
use common\models\Customer;
use common\models\User;
use common\models\UserAddonRate;
use Yii;
use yii\helpers\VarDumper;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

class UserAddonForm extends AssignSortableForm
{
    public function getItems()
    {
        $items = [];

        $users = Customer::getList();
        $assigned = CustomerDi::find()->select('id')
            ->where(['not', ['addon_rate_id'=>null]])
            ->column();


//
        $users = array_diff_key($users, array_flip($assigned));

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
        if ($rateId === null)
        {
            throw new HttpException(400, Yii::t('app', 'Błędne wywołanie'));
        }
        $items = [];
        $users = User::find()
            ->where(['addon_rate_id'=>$rateId])
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
        User::updateAll([
            'addon_rate_id'=>null,
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
                $model = User::findOne($uId);
                $model->addon_rate_id = $rateId;
                $model->save();
            }

        }

        return true;
    }




    public function getRateList()
    {
        return AddonRate::getModelList(false, 'amount', 'id');
    }

}