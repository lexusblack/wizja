<?php

namespace backend\controllers;

use Yii;
use common\models\PurchaseList;
use common\models\PurchaseListSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use kartik\mpdf\Pdf;
use common\models\Settings;
use yii\helpers\Inflector;
/**
 * PurchaseListController implements the CRUD actions for PurchaseList model.
 */
class PurchaseListController extends Controller
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
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'add-purchase-list-item', 'add', 'status', 'pdf', 'order'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false
                    ]
                ]
            ]
        ];
    }

    public function actionOrder()
    {
        $i=1;
        $items = Yii::$app->request->post('data');
        foreach ($items as $item)
        {
            $pli = \common\models\PurchaseListItem::findOne($item);
            if ($pli)
            {
                $pli->position = $i;
                $pli->save();
                $i++;
            }
        }
        exit;
    }

    public function actionPdf($id)
    {
        $model = $this->findModel($id);
        $dist = Pdf::DEST_BROWSER;
        $settings = Settings::find()->indexBy('key')->where(['section'=>'main'])->all(); 
        $content = $this->renderPartial('pdf', [
            'model' => $model,
        ]);

        $header = $this->renderPartial('pdf-header', [
            'model' =>  $model,
            'settings' => $settings
        ]);

        $footer = $this->renderPartial('pdf-footer', [
            'model' =>  $model,
            'settings' => $settings
        ]);

        // setup kartik\mpdf\Pdf component
        $pdf = new Pdf([
            // set to use core fonts only
                'mode' => Pdf::MODE_UTF8,
            // A4 paper format
                'format' => Pdf::FORMAT_A4,
            // portrait orientation
                'orientation' => Pdf::ORIENT_PORTRAIT,
            // stream to browser inline
                'destination' => $dist,
            // your html content input
                'content' => $content,
            // format content from your own css file if needed or use the
            // enhanced bootstrap css built by Krajee for mPDF formatting
                'marginTop' => 45,
                'marginBottom' => 30,
                'cssFile' => '@webroot/themes/e4e/css/pdf_offer.css',
            // any css to be embedded if required
                'cssInline' => '.pdf_box .offer_table {
                    border: 2px solid #000;
                    width: 100%;
                }

                .pdf_box .logo img {
                    max-width: 100%;
                    max-height: 200px;
                    height: 200px;
                    width: auto;
                }',
            // set mPDF properties on the fly
                'options' => ['title' => $model->name],
                'filename' => $model->name.'.pdf',
            // call mPDF methods on the fly
                'methods' => [
                        'SetHeader'=>$header,
                        'SetFooter'=>$footer,
                ]
        ]);
        
        $pdf->options = [
            'defaultheaderline' => 0,
            'defaultfooterline' => 0,
        ];

        $pdf->render();
    }

    public function actionStatus($id)
    {
            $model = $this->findModel($id);
            $key = Yii::$app->request->post("editableIndex");
            $model->status = Yii::$app->request->post("PurchaseList")[$key]['status'];
            $model->save();
            Yii::$app->response->format = Response::FORMAT_JSON;
                $output = ['output'=>\common\models\PurchaseList::getStatusList()[$model->status], 'message'=>''];
                return $output;
                exit;
    }

    /**
     * Lists all PurchaseList models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PurchaseListSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index2', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PurchaseList model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $providerPurchaseListItem = new \yii\data\ArrayDataProvider([
            'allModels' => $model->purchaseListItems,
        ]);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'providerPurchaseListItem' => $providerPurchaseListItem,
        ]);
    }

    /**
     * Creates a new PurchaseList model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($ids = false)
    {
        $model = new PurchaseList();
        $model->datetime = date("Y-m-d");
        $model->status = 0;
        $model->user_id = Yii::$app->user->id;
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if ($ids)
            {
                $ids = json_decode($ids);
                $i=0;
                foreach ($ids as $id)
                {
                    //dodajemy pozycje
                    $eog = \common\models\EventOuterGear::findOne(['outer_gear_id'=>$id->outer_gear_id, 'event_id'=>$id->event_id]);

                    if ($eog)
                    {
                        $i++;
                        $pli = new \common\models\PurchaseListItem();
                        $pli->name = $eog->outerGear->name;
                        $pli->event_id = $id->event_id;
                        $pli->outer_gear_id = $id->outer_gear_id;
                        $pli->purchase_list_id = $model->id;
                        $pli->status = 0;
                        $pli->quantity = $eog->quantity;
                        $pli->price = $eog->price;
                        $pli->company_name = $eog->outerGear->company->name;
                        $pli->company_address = $eog->outerGear->company->address.", ".$eog->outerGear->company->zip." ".$eog->outerGear->company->city;
                        $pli->position = $i;
                        $pli->save();
                    }
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    public function actionAdd($ids = false)
    {
        $model = new \common\models\PurchaseListItem();
        if ($model->load(Yii::$app->request->post()))
        {
            if ($ids)
            {
                $ids = json_decode($ids);
                $i=\common\models\PurchaseListItem::find()->where(['purchase_list_id'=>$model->purchase_list_id])->count();
                foreach ($ids as $id)
                {
                    //dodajemy pozycje
                    $eog = \common\models\EventOuterGear::findOne(['outer_gear_id'=>$id->outer_gear_id, 'event_id'=>$id->event_id]);

                    if ($eog)
                    {
                        $i++;
                        $pli = new \common\models\PurchaseListItem();
                        $pli->name = $eog->outerGear->name;
                        $pli->event_id = $id->event_id;
                        $pli->outer_gear_id = $id->outer_gear_id;
                        $pli->purchase_list_id = $model->purchase_list_id;
                        $pli->status = 0;
                        $pli->price = $eog->price;
                        $pli->quantity = $eog->quantity;
                        $pli->company_name = $eog->outerGear->company->name;
                        $pli->company_address = $eog->outerGear->company->address.", ".$eog->outerGear->company->zip." ".$eog->outerGear->company->city;
                        $pli->position = $i;
                        $pli->save();
                    }
                }
            }
            return $this->redirect(['view', 'id' => $model->purchase_list_id]);
        }else{
            return $this->render('add', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing PurchaseList model.
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
     * Deletes an existing PurchaseList model.
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
     * Finds the PurchaseList model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PurchaseList the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PurchaseList::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for PurchaseListItem
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddPurchaseListItem()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('PurchaseListItem');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formPurchaseListItem', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
