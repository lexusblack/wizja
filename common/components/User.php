<?php
namespace common\components;

use common\models\User as UserModel;
use yii\db\Expression;

class User extends \yii\web\User
{
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        $manager = \Yii::$app->authManager;
        $administrators = $manager->getUserIdsByRole('SiteAdministrator');

        if (in_array($this->id, $administrators))
        {
            return true;
        }
        else
        {
            return parent::can($permissionName, $params, $allowCaching);
        }
    }

    public function beforeLogin($identity, $cookieBased, $duration)
    {
        if (empty($identity->last_visit) == true)
        {
            $this->returnUrl = ['/site/update-password'];
        }
        return parent::beforeLogin($identity, $cookieBased, $duration);
    }

    public function afterLogin($identity, $cookieBased, $duration)
    {
        parent::afterLogin($identity, $cookieBased, $duration);
        $model = $this->identity;
        if (empty($model->last_visit) == false)
        {
            $model->updateAttributes([
                'last_visit'=>new Expression('NOW()'),
            ]);
        }
    }

    public function getIsAdministrator()
    {
        return $this->can(UserModel::RBAC_ADMINISTARTOR);
    }

    public function getIsSuperAdmin()
    {
        return $this->can(UserModel::RBAC_SUPERADMIN);
    }
}