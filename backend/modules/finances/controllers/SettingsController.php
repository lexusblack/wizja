<?php
namespace backend\modules\finances\controllers;

use backend\components\Controller;
use backend\models\SettingsForm;
use common\components\filters\AccessControl;
use Yii;

class SettingsController extends Controller
{
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'=>true,
                        'actions' => ['index'],
                        'roles' => ['settingsFinances'],
                    ],
                ],
            ],
        ];
    }

    public function init()
    {
        $this->layout = static::LAYOUT_PANEL;
        Yii::$app->view->params['active_tab'] = 4;
        parent::init();
    }

    public function actionIndex()
    {
        $model = new SettingsForm();
        $params = Yii::$app->request->post();
        if ($model->load($params) && $model->validate() && Yii::$app->user->can('settingsFinancesSave'))
        {
            $model->saveValues($params[$model->formName()]);
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano'));
            return $this->refresh();
        }
        $model->loadValues();
        return $this->render('index', [
            'model'=>$model,
        ]);
    }
}