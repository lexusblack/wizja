<?php

namespace backend\controllers;

use backend\modules\permission\models\BasePermission;
use common\models\User;
use Yii;
use common\models\Vacation;
use common\models\VacationSearch;
use backend\components\Controller;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * VacationController implements the CRUD actions for Vacation model.
 */
class VacationController extends Controller
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
                        'roles' => ['eventVacations'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['eventVacationsAdd'],

                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['@'],
                        'matchCallback' => function() {
                            if (Yii::$app->user->can('eventVacationsView'.BasePermission::SUFFIX[BasePermission::ALL])) {
                                return true;
                            }
                            return $this->isAuthor('eventVacationsView');
                        }

                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['@'],
                        'matchCallback' => function() {
                            if (Yii::$app->user->can('eventVacationsDelete'.BasePermission::SUFFIX[BasePermission::ALL])) {
                                return true;
                            }
                            return $this->isAuthor('eventVacationsDelete');
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['@'],
                        'matchCallback' => function() {
                            if (Yii::$app->user->can('eventVacationsEdit'.BasePermission::SUFFIX[BasePermission::ALL])) {
                                return true;
                            }
                            return $this->isAuthor('eventVacationsEdit');
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    private function isAuthor($text) {
        $vacation = $this->findModel(Yii::$app->request->get('id'));

        $user = Yii::$app->user;
        if (!$user->can('SiteAdministrator') && $user->can($text.BasePermission::SUFFIX[BasePermission::MINE])) {
            if ($vacation->user_id == Yii::$app->user->id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Lists all Vacation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new VacationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $user = Yii::$app->user;
        if (!$user->can('SiteAdministrator') && $user->can('eventVacations'.BasePermission::SUFFIX[BasePermission::MINE])) {
            $query = Vacation::find()->where(['user_id' => Yii::$app->user->id]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Vacation model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        Url::remember();
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Vacation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($start=null)
    {
        $model = new Vacation();
        $model->user_id = Yii::$app->user->id;
        if ($start == null)
        {
            $start = date('Y-m-d');
        }
        $model->start_date = $start;
        $model->end_date = $start;

        $model->loadDefaultValues();
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['/site/calendar']);
        }
        else
        {
            $model->prepareDateAttributes();
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Vacation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['/site/calendar']);
        }
        else
        {
            $model->prepareDateAttributes();
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Vacation model.
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
     * Finds the Vacation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Vacation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Vacation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
