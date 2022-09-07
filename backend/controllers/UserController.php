<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use common\models\EventSearch;
use common\models\Notification;
use Yii;
use common\models\User;
use common\models\UserProvision;
use common\models\UserNotification;
use common\models\UserSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use common\helpers\ArrayHelper;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
{
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'rules' => [
                [
                    'allow'=>true,
                    'actions' => ['index', 'upload'],
                    'roles' => ['usersUsers'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['usersUsersCreate'],

                ],
                [
                    'allow' => true,
                    'actions' => ['view'],
                    'roles' => ['usersUsersView'],
                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['usersUsersDelete'],
                ],
                [
                    'allow' => true,
                    'actions' => ['update', 'save-finance', 'save-section'],
                    'roles' => ['usersUsersEdit'],
                ],
                [
                    'allow' => true,
                    'actions' => ['inactive'],
                    'roles' => ['usersUsersInactive'],
                ],
                [
                    'allow' => true,
                    'actions' => ['history'],
                    'roles' => ['usersUsersHistory'],
                ],
                [
                    'allow' => true,
                    'actions' => ['load-notification', 'read-notifications']
                ]
            ]
        ];

        return $behaviors;
    }

    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/user'

            ]
        ];

        return array_merge(parent::actions(), $actions);
    }

    public function actionLoadNotification()
    {
        $models = UserNotification::find()->where(['shown'=>0, 'user_id'=>Yii::$app->user->id])->all();
        $this->layout = false;
        return $this->render('load-notifications', [
            'models'=>$models
        ]);
    } 
    public function actionReadNotifications()
    {
        $models = UserNotification::find()->where(['shown'=>0, 'user_id'=>Yii::$app->user->id])->all();
        foreach ($models as $model)
        {
            $model->shown = 1;
            $model->save();
        }
        exit;
    }

    /**
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dataProvider->query->andWhere(['active' => 1]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'active' => 1,
        ]);
    }

    public function actionInactive() {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;
        $dataProvider->query->andWhere(['active' => 0]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'active' => 0,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $searchModel = new EventSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search2($params);

        $model = $this->findModel($id);
        $ids = $model->getEventUsers()->select('event_id')->column();

        $dataProvider->query->andWhere(['event.id'=>$ids]);
        $dataProvider->sort->defaultOrder = ['event_start'=>SORT_DESC];

        $model->createProvisions();     
        $sections = UserProvision::find()->where(['user_id'=>$id])->all(); 
        return $this->render('view', [
            'model' => $model,
            'dataProvider'=>$dataProvider,
            'searchModel'=>$searchModel,
            'sections'=>$sections
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new User();
        $model->username = "xxx";
        $model->generateAuthKey();
        $groups_super_user = ArrayHelper::map(\common\models\AuthItem::find()->where(['superuser'=>1])->asArray()->all(), 'name', 'name');
        $superusers = User::find()->where(['active'=>1])->andWhere(['NOT LIKE', 'username', '@newsystems.pl'])->andWhere(['id'=>ArrayHelper::map(\common\models\base\AuthAssignment::find()->where(['item_name'=>$groups_super_user])->asArray()->all(), 'user_id', 'user_id')])->count();
        $superuser = \common\models\Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
        $superusers_paid = $superuser->superusers_paid;
        if ($model->load(Yii::$app->request->post())) {
            $send_password = Yii::$app->request->post()['User']['send_password'];
            if ($model->newPassword) {
                $model->setPassword($model->newPassword);
            }
            if ($model->validate()) {
                $model->username = $model->email;
                $model->save(false);
                $model->linkObjects();
                $relationName = 'auths';
                $model->unlinkAll($relationName, true);
                $className = 'common\models\AuthItem';

                $models = $className::findAll(Yii::$app->request->post()['User']['authAssigmentIds']);
                foreach ($models as $m)
                {
                    $model->link($relationName, $m, []);
                }
                if ($send_password)
                    Notification::sendUserNotifications($model, Notification::CREATE_NEW_USER, [$model]);
                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
            $model->send_password = 1;

        return $this->render('create', [
            'model' => $model,
            'superusers'=>$superusers,
            'superusers_paid'=>$superusers_paid
        ]);
    }


    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->loadLinkedObjects();
        $model->username = $model->email;
        $model->authAssigmentIds = ArrayHelper::map($model->auths, 'name', 'name');
        $groups_super_user = ArrayHelper::map(\common\models\AuthItem::find()->where(['superuser'=>1])->asArray()->all(), 'name', 'name');
        $superusers = User::find()->where(['active'=>1])->andWhere(['NOT LIKE', 'username', '@newsystems.pl'])->andWhere(['id'=>ArrayHelper::map(\common\models\base\AuthAssignment::find()->where(['item_name'=>$groups_super_user])->asArray()->all(), 'user_id', 'user_id')])->count();
        $superuser = \common\models\Company::find()->where(['code'=>\Yii::$app->params['companyID']])->one();
        $superusers_paid = $superuser->superusers_paid;
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $send_password = Yii::$app->request->post()['User']['send_password'];
            if ($model->newPassword)
            {
                $model->setPassword($model->newPassword);
            }
            $model->username = $model->email;
            $model->save(false);
            $model->linkObjects();
            $relationName = 'auths';
            $model->unlinkAll($relationName, true);
            $className = 'common\models\AuthItem';

            $models = $className::findAll(Yii::$app->request->post()['User']['authAssigmentIds']);
            foreach ($models as $m)
            {
                $model->link($relationName, $m, []);
            }
            if ($model->newPassword)
            {
                if ($send_password)
                    Notification::sendUserNotifications($model, Notification::CREATE_NEW_USER, [$model]);
            }
                            

            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $model->send_password = 1;
            return $this->render('update', [
                'model' => $model,
            'superusers'=>$superusers,
            'superusers_paid'=>$superusers_paid
            ]);
        }
    }

    public function actionSaveFinance($id)
    {
        $model = $this->findModel($id);
        $model->loadLinkedObjects();
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $model->save(false);
            $model->linkObjects();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['view', 'id' => $model->id, "#"=>'tab-finance']);
        } else {
            $model->send_password = 1;
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionSaveSection($id, $section)
    {
        $model = UserProvision::find()->where(['user_id'=>$id])->andWhere(['section'=>$section])->one();
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $model->save(false);
            return $this->redirect(['view', 'id' => $id, "#"=>'tab-finance']);
        } else {
            return $this->redirect(['view', 'id' => $id, "#"=>'tab-finance']);
        }
    }
    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionList($user_id=null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        $data = User::find()->all();
        if($data){
            $out['results'] = [];
            foreach ($data as $key=>$user)
            {
                $out['results'][] = [
                    'id' => $user->id,
                    'text' => $user->first_name.' '.$user->last_name,
                ];
            }
        }
        
        return $out;
    }

    public function actionHistory($id)
    {
        /* @var $dataProvider ActiveDataProvider */
        $searchModel = new EventSearch();
        $params = Yii::$app->request->queryParams;
        $dataProvider = $searchModel->search($params);

        $model = $this->findModel($id);
        $ids = $model->getEventUsers()->select('event_id')->column();

        $dataProvider->query->andWhere(['event.id'=>$ids]);
        $dataProvider->sort->defaultOrder = ['event_start'=>SORT_DESC];

        return $this->render('history', [
            'model' => $model,
            'dataProvider'=>$dataProvider,
            'searchModel' => $searchModel,
        ]);
    }
}
