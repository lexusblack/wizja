<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use Yii;
use common\models\GearCompany;
use common\models\GearCompanySearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GearCompanyController implements the CRUD actions for GearCompany model.
 */
class GearCompanyController extends Controller
{
    public $enableCsrfValidation = false;
    public function behaviors()
    {
        return [
            'access' => [
                'class'=>AccessControl::className(),
                'rules' => [
                    [
                        'allow'=>true,
                        'actions' => ['index', 'list'],
                        'roles' => ['gearProducer'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['gearProducerCreate'],

                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['gearProducerView'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['gearProducerDelete'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['gearProducerEdit'],
                    ],
                ]
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all GearCompany models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new GearCompanySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GearCompany model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $providerGearModel = new \yii\data\ArrayDataProvider([
            'allModels' => $model->gearModels,
        ]);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'providerGearModel' => $providerGearModel,
        ]);
    }

    /**
     * Creates a new GearCompany model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new GearCompany();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing GearCompany model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing GearCompany model.
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
     * Finds the GearCompany model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GearCompany the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GearCompany::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for GearModel
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddGearModel()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('GearModel');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formGearModel', ['row' => $row]);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

   public function actionList($q=null)
    {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $out = ['results' => ['id' => '', 'text' => '']];
        $data = GearCompany::getList($q);
        $out['results'] = [];
        foreach ($data as $key=>$value)
        {
            $out['results'][] = [
                'id' => $key,
                'text' => $value,
            ];
        }
        return $out;


    }
}
