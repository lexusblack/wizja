<?php

namespace backend\modules\permission\controllers;

use backend\modules\permission\models\PermissionForm;
use backend\modules\permission\models\PermissionTree;
use common\components\filters\AccessControl;
use yii\web\Controller;
use Yii;
/**
 * Default controller for the `permission` module
 */
class DefaultController extends Controller
{

    public $layout = '@backend/themes/e4e/layouts/main-panel';
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['manage-roles'],
                    'roles' => ['settingsAccessControl'],
                ],
                [
                    'allow' => true,
                    'actions' => ['manage-roles2'],
                    'roles' => ['settingsAccessControl'],
                ],
            ]
        ];

        return $behaviors;
    }

    public function actionManageRoles($role=null)
    {
        $this->layout = '@backend/themes/e4e/layouts/panel';
        Yii::$app->view->params['active_tab'] = 7;

        if ($role == null)
        {
            return $this->redirect(['manage-roles', 'role'=>'projectManager']);
        }

        $model = new PermissionForm();
        $model->setRole($role);

        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $model->save();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->refresh();
        }


        $roleMenuItems = [];
        $roles = $model->manager->getRoles();
        foreach ($roles as $role) {
            if ($role->name == 'SiteAdministrator') {
                continue;
            }
            $roleMenuItems[] = [
               'label'=>$role->description,
                'url' => ['default/manage-roles', 'role'=>$role->name],
            ];
        }

        return $this->render('manageRoles', [
            'model'=>$model,
            'roleMenuItems' => $roleMenuItems,
        ]);

    }


    public function actionManageRoles2($role = null) {
        $this->layout = '@backend/themes/e4e/layouts/panel';
        Yii::$app->view->params['active_tab'] = 7;

        if ($role == null) {
            $role = \common\models\AuthItem::find()->where(['type'=>1])->one();
            return $this->redirect(['manage-roles2', 'role'=>$role->name]);
        }

        $model = new PermissionTree();
        $model->setRole($role);

        if ($model->load(Yii::$app->request->post()) && $model->validate() && Yii::$app->user->can('settingsAccessControlSave'))
        {

            $model->save();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            //return $this->refresh();
        }



        $roleMenuItems = [];
        $roles = \Yii::$app->authManager->getRoles();

        foreach ($roles as $role)
        {
            if ($role->name == 'SiteAdministrator')
            {
                continue;
            }
            if ($role->superuser){
                if ($role->superuser==1)
                $sup = " (superuser)";
            else
                $sup = " (user+)";
            }else{
                $sup = "";
            }
            $roleMenuItems[] = [
                'label'=>$role->description.$sup,
                'url' => ['default/manage-roles2', 'role'=>$role->name],
            ];
        }

        return $this->render('manageNewRoles', [
            'model'=>$model,
            'roleMenuItems' => $roleMenuItems,
        ]);
    }
}
