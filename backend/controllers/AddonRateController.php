<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use common\models\UserEventRole;
use Yii;
use common\models\AddonRate;
use common\models\AddonRateSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\UserAddonForm;

/**
 * AddonRateController implements the CRUD actions for AddonRate model.
 */
class AddonRateController extends Controller
{
    public $enableCsrfValidation = false;
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'=>true,
                        'actions' => ['users'],
                        'roles' => ['settingsAddons'],
                    ],
                    [
                        'allow'=>true,
                        'actions' => ['index'],
                        'roles' => ['settingsAddonsRateManage'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['settingsAddons'],

                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['settingsAddonsRateManageView'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['settingsAddonsRateManageDelete'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['settingsAddonsRateManageUpdate'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all AddonRate models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AddonRateSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AddonRate model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AddonRate model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($level=1)
    {
        $model = new AddonRate();
        $model->level = $level;

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
	        $model->linkObjects();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['users']);
        }
        else
        {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AddonRate model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
	    $model->loadLinkedObjects();

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
	        $model->linkObjects();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['index']);
        }
        else
        {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AddonRate model.
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
     * Finds the AddonRate model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AddonRate the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AddonRate::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionUsers($roleId=null, $level=1, $period=0)
    {
        $this->layout = 'panel';
        Yii::$app->view->params['active_tab'] = 5;

        if ($roleId===null) {
            $roleId = UserEventRole::find()->scalar();
            return $this->redirect(['users', 'roleId'=>$roleId, 'level'=>$level, 'period'=>$period]);
        }
        if (!$roleId) {
	        return $this->redirect( '/admin/user-event-role/index' );
        }

        $model = new UserAddonForm([
            'roleId'=>$roleId,
            'level'=>$level,
            'period'=>$period,
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $model->save();
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->refresh();
        }


        return $this->render('users', [
            'model'=>$model,
        ]);

    }
}
