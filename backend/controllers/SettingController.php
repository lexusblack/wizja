<?php

namespace backend\controllers;

use backend\models\SettingsForm;
use backend\models\SettingsOfferForm;
use common\components\filters\AccessControl;
use common\models\Department;
use common\models\GearCategory;
use common\models\Event;
use common\models\Notification;
use common\models\SettingAttachment;
use common\models\SettingAttachmentSearch;
use Yii;
use backend\components\Controller;
use yii\base\Model;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;

class SettingController extends Controller
{
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'rules'=>[
                [
                    'allow' => true,
                    'actions' => ['menu-settings'],
                    'roles' => ['menuSettings'],
                ],
                [
                    'allow' => true,
                    'actions' => ['index'],
                    'roles' => ['settingsCompany'],
                ],
                [
                    'allow' => true,
                    'actions' => ['upload'],
                    'roles' => ['settingsCompanySave'],
                ],
                [
                    'allow' => true,
                    'actions' => ['personalize'],
                    'roles' => ['settingsPersonalization'],
                ],
                [
                    'allow' => true,
                    'actions' => ['offer'],
                    'roles' => ['settingsOffers'],
                ],
                [
                    'allow' => true,
                    'actions' => ['upload-offer'],
                    'roles' => ['settingsOffersAddFile']
                ],
                [
                    'allow' => true,
                    'actions' => ['notification', 'change-event-notifications'],
                    'roles' => ['settingsNotifications'],
                ]
            ]
        ];

        return $behaviors;
    }

    public function actions()
    {
        return [
            /*'index' => [
                'class' => 'backend\actions\SettingAction',
                'modelClass' => 'backend\models\SettingsForm',
                'viewName' => 'index',   // The form we need to render
            ],*/
            'upload-offer'=> [
                'class'=>\backend\actions\UploadMultipleAction::className(),
                'upload'=>'/settings-attachment',
                'targetClassName' => SettingAttachment::className(),

            ],
            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/settings',
                'afterUploadHandler' => [$this, 'savePhotoUrl'],

            ]
        ];
    }

    public function savePhotoUrl($data)
    {
            $settings = new SettingsForm();
            $settings->loadValues();
            $settings->companyLogo = $data['filename'];
            $settings->saveValues();
            return $settings->attributes;
    }

    public function actionOffer()
    {
        $this->layout = 'panel';
        Yii::$app->view->params['active_tab'] = 3;

        Url::remember();
        $model = new SettingAttachment([
            'type'=>SettingAttachment::TYPE_OFFER,
        ]);

        $searchModel = new SettingAttachmentSearch();
        $params = [
            $searchModel->formName()=>[
                'type' => SettingAttachment::TYPE_OFFER,
            ]
        ];
//        $searchModel->type = SettingAttachment::TYPE_OFFER;
        $dataProvider = $searchModel->search($params);
        $dataProvider->pagination = false;
        $categories = GearCategory::find()->where(['lvl'=>1])->andWhere(['active'=>1])->indexBy('id')->all();
        $settings = new SettingsOfferForm();
        $settings->loadValues();

        $params = Yii::$app->request->post();
        if ($settings->load($params) && $settings->validate() && Yii::$app->user->can('settingsOffersSave')&& Model::loadMultiple($categories, $params) && Model::validateMultiple($categories))
        {
            
            foreach ($categories as $cat)
            {
                $cat->color = $params['GearCategory'][$cat->id]['color'];
                $cat->font_color = $params['GearCategory'][$cat->id]['font_color'];
                $cat->save(false);
            }
            $settings->saveValues();
            Yii::$app->getSession()->addFlash('success', Yii::t('app', 'Zapisano'));
            //return $this->refresh();
        }

        return $this->render('offer', [
            'model'=>$model,
            'dataProvider' => $dataProvider,
            'settings' => $settings,
            'categories'=>$categories
        ]);
    }

    public function actionPersonalize()
    {
        $this->layout = 'panel';
        Yii::$app->view->params['active_tab'] = 8;
        Yii::$app->settings->clearCache();
        $params = Yii::$app->request->post();

        $departments = Department::find()->indexBy('id')->all();

        $model = new SettingsForm();
        $model->loadValues();
        $model->blackFieldArray = explode(";", $model->blackField);
        if ($model->load($params) && $model->validate() && Model::loadMultiple($departments, $params) && Model::validateMultiple($departments) && Yii::$app->user->can('settingsPersonalizationSave')) {
            foreach ($departments as $department)
            {
                $department->save(false);
            }
            if ($model->blackFieldArray)
            {
                            $model->blackField = implode(";", $model->blackFieldArray);
            $model->blackFieldArray = implode(";", $model->blackFieldArray);
            }

            $model->saveValues();
            Yii::$app->getSession()->addFlash('success', Yii::t('app', 'Zapisano'));
            return $this->refresh();
        }
        
        return $this->render('personalize', [
            'model' => $model,
            'departments' => $departments,
        ]);
    }

    public function actionIndex()
    {
        $this->layout = 'panel';
        Yii::$app->view->params['active_tab'] = 0;
        Yii::$app->settings->clearCache();
        $params = Yii::$app->request->post();

        $model = new SettingsForm();
        $model->loadValues();
        $model->crossRentalUsersArray = explode(";", $model->crossRentalUsers);
        if ($model->load($params) && $model->validate()) {
            if ($model->crossRentalUsersArray)
                $model->crossRentalUsers = implode(";", $model->crossRentalUsersArray);
            $model->crossRentalUsersArray = "";
            $model->saveValues();
            Yii::$app->getSession()->addFlash('success', Yii::t('app', 'Zapisano'));
            return $this->refresh();
        }
        
        return $this->render('index', [
            'model' => $model
                    ]);
    }

    public function actionNotification()
    {
        $this->layout = 'panel';
        Yii::$app->view->params['active_tab'] = 6;

        $params = Yii::$app->request->post();

        $models = Notification::find()->where(['NOT IN', 'name', ["customerCreate", "eventTempAccess", "eventGearChange"]])->indexBy('id')->all();
        foreach ($models as $model)
        {
            $model->loadLinkedObjects();
        }

        $settingForm = $this->changeEventNotifications();

        if (Model::loadMultiple($models, $params) && Model::validateMultiple($models) && Yii::$app->user->can('settingsNotificationsSave'))
        {
            foreach ($models as $model)
            {
                $model->save();
                $model->linkObjects();
            }
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->refresh();
        }


        return $this->render('notification', [
            'models' => $models,
            'settingForm' => $settingForm
        ]);
    }

    public function actionMenuSettings() {
        $user = Yii::$app->user;
        if ($user->can('settingsCompany')) {
            return $this->redirect(['/setting/index']);
        }
        if ($user->can('settingsCompanyDepartments')) {
            return $this->redirect(['/department/index']);
        }
        if ($user->can('settingsRole')) {
            return $this->redirect(['/user-event-role/index']);
        }
        if ($user->can('settingsAddons')) {
            return $this->redirect(['/addon-rate/users']);
        }
        if ($user->can('settingsPersonalization')) {
            return $this->redirect(['/setting/personalize']);
        }
        if ($user->can('settingsOffers')) {
            return $this->redirect(['/setting/offer']);
        }
        if ($user->can('settingsNotifications')) {
            return $this->redirect(['/setting/notification']);
        }
        if ($user->can('settingsAccessControl')) {
            return $this->redirect(['/permission/default/manage-roles2']);
        }
        if ($user->can('settingsFinances')) {
            return $this->redirect(['/finances/settings/index']);
        }
        if ($user->can('settingsLanguage')) {
            return $this->redirect(['/i18n/default/index']);
        }
        throw new ForbiddenHttpException(Yii::t('yii', 'Nie jesteś uprawniony do wykonania tej akcji.'));
    }

    public function actionChangeEventNotifications() {
         $this->changeEventNotifications();
    }

    private function changeEventNotifications() {
        Yii::$app->settings->clearCache();
        $settingForm = new SettingsForm();
        $settingForm->loadValues();
        $start = Yii::$app->settings->get('eventNotifications', 'main');
        if ($settingForm->load(Yii::$app->request->post()) && $settingForm->validate()) {
            $settingForm->saveValues();
        }
        $end = Yii::$app->settings->get('eventNotifications', 'main');
        if ($start == 0 && $end == 1) {
            Event::sendAllNotifications();
        }
        return $settingForm;
    }
}
