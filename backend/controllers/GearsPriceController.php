<?php

namespace backend\controllers;

use Yii;
use common\models\GearsPrice;
use yii\data\ActiveDataProvider;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * GearsPriceController implements the CRUD actions for GearsPrice model.
 */
class GearsPriceController extends Controller
{
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'add-gears-price-percent', 'export'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false
                    ]
                ]
            ]
        ];
    }
    /**
     * Lists all GearsPrice models.
     * @return mixed
     */
    public function actionExport()
    {
        $prices = \common\models\PriceGroup::find()->all();
        $sheets = [];
        foreach ($prices as $p)
        {
            $sheets[$p->name] = [   // Name of the excel sheet
                    'data' => $p->getExcelData(),

                    // Set to `false` to suppress the title row
                    'titles' => false
                ];
        }

        $file = \Yii::createObject([
            'class' => 'codemix\excelexport\ExcelFile',
            'sheets' => $sheets
        ]);

        // Save on disk
        $file->send('cennik.xlsx');
    }

    /**
     * Lists all GearsPrice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = new ActiveDataProvider([
            'query' => GearsPrice::find(),
        ]);
        Url::remember();
        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single GearsPrice model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $providerGearPrice = new \yii\data\ArrayDataProvider([
            'allModels' => $model->gearPrices,
        ]);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'providerGearPrice' => $providerGearPrice,
        ]);
    }

    /**
     * Creates a new GearsPrice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($gear_category_id=null, $gear_id=null)
    {
        $model = new GearsPrice();
        $model->gear_category_id = $gear_category_id;
        $model->gear_id = $gear_id;
        if ($model->gear_id)
        {
            $model->type = 3;
        }else{
            if ($model->gear_category_id)
            {
                $model->type = 2;
            }else{
                $model->type = 1;
            }
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
             $model->linkObjects();
             $model->copyPrices();
             $model->savePercents(Yii::$app->request->post('GearsPricePercent'));
            return $this->goBack();
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing GearsPrice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->loadLinkedObjects();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
             $model->linkObjects();
             $model->copyPrices();
             $model->savePercents(Yii::$app->request->post('GearsPricePercent'));

            return $this->redirect(['index']);
        } else {
            return $this->render('update', ['model' => $model]);
        }
    }

    /**
     * Deletes an existing GearsPrice model.
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
     * Finds the GearsPrice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return GearsPrice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = GearsPrice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
    * Action to load a tabular form grid
    * for GearSetItem
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddGearsPricePercent()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('GearsPricePercent');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formPercent', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    

}
