<?php
namespace backend\modules\permission\controllers;

use common\models\AuthItem;
use common\components\filters\AccessControl;
use dektrium\rbac\controllers\RoleController as BaseController;
use yii\rbac\Item;
use dektrium\rbac\models\Search;
use Yii;

class RoleController extends BaseController
{
    public $layout = '@backend/themes/e4e/layouts/main-panel';
    public $modelClass = 'backend\modules\permission\models\Role';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'rules' => [
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['settingsAccessControlAdd'],
                ],
                [
                    'allow' => true,
                    'actions' => ['index'],
                    'roles' => ['settingsAccessControlManage'],
                ],
                [
                    'allow' => true,
                    'actions' => ['update'],
                    'roles' => ['settingsAccessControlManageEdit'],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['settingsAccessControlManageDelete'],
                ],
            ]

        ];

        return $behaviors;
    }


}