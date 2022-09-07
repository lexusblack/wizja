<?php

namespace backend\modules\finances\controllers;

use common\components\filters\AccessControl;
use common\models\Invoice;
use common\models\InvoiceTypeDefaultSeries;
use Yii;
use common\models\InvoiceSerie;
use common\models\InvoiceSerieSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InvoiceSerieController implements the CRUD actions for InvoiceSerie model.
 */
class InvoiceSerieController extends Controller
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
                        'actions' => ['index', 'default-series'],
                        'roles' => ['financesInvoiceSeries'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create'],
                        'roles' => ['financesInvoiceSeries2Create'],

                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['financesInvoiceSeries2View'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['financesInvoiceSeries2Delete'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update'],
                        'roles' => ['financesInvoiceSeries2Edit'],
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
     * @inheritdoc
     */
    public function init()
    {
        $this->layout = static::LAYOUT_PANEL;
        Yii::$app->view->params['active_tab'] = 4;
        parent::init();
    }

    /**
     * Lists all InvoiceSerie models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InvoiceSerieSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single InvoiceSerie model.
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
     * Creates a new InvoiceSerie model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($type=null)
    {
        $model = new InvoiceSerie([
            'type'=>$type,
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['index']);
        }
        else
        {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing InvoiceSerie model.
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
     * Deletes an existing InvoiceSerie model.
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
     * Finds the InvoiceSerie model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InvoiceSerie the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InvoiceSerie::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionDefaultSeries() {

        if (Yii::$app->request->isPost) {
            $defaultSeries = Yii::$app->request->post('defaultSeries');
            foreach ($defaultSeries as $typeId => $series) {
                InvoiceTypeDefaultSeries::deleteAll(['invoice_type' => $typeId]);
                if ($series != 0) {
                    $defaultType = new InvoiceTypeDefaultSeries();
                    $defaultType->invoice_type = $typeId;
                    $defaultType->series_id = $series;
                    $defaultType->save();
                }
            }
        }

        $invoiceTypes = Invoice::getTypeList();
        $models = [];
        $seriesList = [];
        foreach ($invoiceTypes as $id => $label) {
            $default = null;
            if ($inv = InvoiceTypeDefaultSeries::find()->where(['invoice_type' => $id])->one()) {
                $default = $inv->series_id;
            }
            $models[$id] = [$label, $default];
            $seriesList[$id][0] = 'Brak';
            foreach (InvoiceSerie::find()->where(['type' => $id])->all() as $series) {
                $seriesList[$id][$series->id] = $series->name;
            }
        }

        return $this->render('default', [
            'models' => $models,
            'seriesList' => $seriesList,
        ]);
    }
}
