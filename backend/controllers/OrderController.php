<?php

namespace backend\controllers;

use Yii;
use common\helpers\ArrayHelper;

use common\models\Order;
use common\models\Note;
use common\models\Settings;
use common\models\OrderForm;
use common\models\Customer;
use common\models\OrderSearch;
use common\models\EventOuterGearSearch;
use common\models\Event;
use common\models\EventConflict;
use common\models\EventConflictSearch;
use common\models\EventOuterGear;
use common\models\OuterGear;
use common\models\EventOuterGearModel;
use common\models\EventOuterGearModelSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;
use yii\helpers\Inflector;
use yii\helpers\Url;

/**
 * OrderController implements the CRUD actions for Order model.
 */
class OrderController extends Controller
{
    
    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }

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
                        'actions' => ['list', 'view', 'create', 'update', 'delete', 'index', 'pdf', 'send-mail', 'mark-resolved', 'add-event-outer-gear', 'add', 'purchase-no-company', 'purchase'],
                        'roles' => ['menuOrders']
                    ],
                    [
                        'allow' => false
                    ]
                ]
            ]
            
        ];
    }

    public function actionPurchase($page = 'company')
    {
        Url::remember();
        $myDate = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-3 days" ) );
        $myDate2 = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "+ 10 days" ) );
        $eventIds = ArrayHelper::map(Event::find()->where(['>', 'event_end', $myDate])->andWhere(['<', 'event_start', $myDate2])->asArray()->all(), 'id', 'id');
        if ($page != 'company')
        {
                    $dataProvider = new ActiveDataProvider([
            'query' => EventOuterGearModel::find()->where(['resolved'=>0])->andWhere(['in', 'event_id', $eventIds])->andWhere(['prod'=>1]),
                ]);
        }else{
            $dataProvider = new ActiveDataProvider([
            'query' => EventOuterGear::find()->where(['order_id'=>null])->andWhere(['in', 'event_id', $eventIds])->andWhere(['prod'=>1]),
        ]);
        }



            return $this->render('purchase', [
            'dataProvider2' => $dataProvider,
            'page'=>$page
            ]);
    }


    public function actionList($page=false)
    {
        Url::remember();
        $params = Yii::$app->request->queryParams;
        $myDate = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "-3 days" ) );
        $myDate2 = date("Y-m-d", strtotime( date( "Y-m-d", strtotime( date("Y-m-d") ) ) . "+ 10 days" ) );
        $eventIds = ArrayHelper::map(Event::find()->where(['>', 'event_end', $myDate])->andWhere(['<', 'event_start', $myDate2])->asArray()->all(), 'id', 'id');
        $conflictsCount = EventConflict::find()->where(['resolved'=>0])->andWhere(['in', 'event_id', $eventIds])->count();
        $noCompanyCount = EventOuterGearModel::find()->where(['resolved'=>0])->andWhere(['in', 'event_id', $eventIds])->count();;
        if (!$page)
        {
            $searchModel = new EventConflictSearch();
            $params['EventConflictSearch']['resolved']=0;
            $dataProvider = $searchModel->search($params);
            return $this->render('list', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'conflictsCount' => $conflictsCount,
            'noCompanyCount'=>$noCompanyCount
            ]);
        }
        if ($page == 'noCompany')
        {
            $searchModel = new EventOuterGearModelSearch();
            $params['EventOuterGearModelSearch']['a']=null;
            $params['EventOuterGearModelSearch']['resolved']=0;
            $dataProvider = $searchModel->search($params);
            return $this->render('listNoCompany', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'conflictsCount' => $conflictsCount,
            'noCompanyCount'=>$noCompanyCount
            ]);            
        }
        if ($page == 'withCompany')
        {
            $searchModel = new EventOuterGearSearch();
            $params['EventOuterGearSearch']['order_id']=null;
            $dataProvider = $searchModel->search($params);
            return $this->render('listWithCompany', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'conflictsCount' => $conflictsCount,
            'noCompanyCount'=>$noCompanyCount
            ]);            
        }
        if ($page == 'orders')
        {
            $searchModel = new OrderSearch();
            $dataProvider = $searchModel->search($params);
            return $this->render('listOrder', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'conflictsCount' => $conflictsCount,
            'noCompanyCount'=>$noCompanyCount
            ]);            
        }
    }
    /**
     * Lists all Order models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new OrderSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Order model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $providerEventOuterGear = new \yii\data\ArrayDataProvider([
            'allModels' => $model->eventOuterGears,
        ]);
        return $this->render('view', [
            'model' => $model,
            'providerEventOuterGear' => $providerEventOuterGear,
        ]);
    }

    public function actionConfirm($id)
    {
        $model = $this->findModel($id);
        $model->confirm = 1;
        foreach ($model->eventOuterGears as $gear)
        {
            $gear->confirm = 1;
            $gear->save();
        }
        $model->save();
        return $this->redirect(['view', 'id' => $model->id]);       
    }

    public function actionConfirmone($event_id, $outer_gear_id)
    {
        $model = EventOuterGear::find()->where(['event_id'=>$event_id, 'outer_gear_id'=>$outer_gear_id])->one();
        $model->confirm = 1;
        $model->save();
        return $this->redirect(['view', 'id' => $model->order_id]);       
    }
    /**
     * Creates a new Order model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Order();

        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Order model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->loadAll(Yii::$app->request->post()) && $model->saveAll()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    public function actionPdf($id)
    {
        $order = $this->findModel($id);
        $pdf = $this->preparePDF($order);
        return $pdf->render();
    }

    public function actionAdd($ids)
    {
        if (Yii::$app->request->post())
        {
            $data = Yii::$app->request->post();
            $model = new Order;
            $model->company_id = $data['OrderForm']['company_id'];
            $model->contact_id = $data['OrderForm']['contact_id'];
            $model->hash = md5(time());
            $model->save();
            $id = $model->id;
            foreach($data['EventOuterGear'] as $gear)
            {
                $model = EventOuterGear::find()->where(['event_id'=>$gear['event_id'], 'outer_gear_id'=>$gear['outer_gear_id']])->one();
                $model->order_id = $id;
                $model->price = $gear['price'];
                if ($gear['reception_time'])
                {
                    $model->reception_time = $gear['reception_time'];
                }else{
                    $model->reception_time = $data['OrderForm']['reception'];
                }
                if ($gear['return_time'])
                {
                    $model->return_time = $gear['return_time'];
                }else{
                    $model->return_time = $data['OrderForm']['return'];
                }                
                $model->save();
                $model->updateExpense();
            }
            return $this->redirect(['view', 'id' => $id]);
            
        }
        $ids = json_decode($ids);
        $gears = array();
        $i=0;
        $error = false;
        if (!$ids)
            $error = true;
        foreach ($ids as $id)
        {
            $model = EventOuterGear::find()->where(['id'=>$id, 'order_id'=>null])->one();
            if (!$model)
            {
                $error = true;
            }else{
                 $model->price = $model->getRentPrice();
                $gears[$i] = $model;
                $i++;
                if ($i==1)
                {
                    $company_id = $model->outerGear->company_id;
                }else{
                    if ($model->outerGear->company_id!=$company_id)
                    {
                        $error = true;
                    }
                }               
            }

        }
        if ($error)
        {
            exit;
            //return $this->redirect('list');
        }else{
            $model = new OrderForm();
            $model->eventOuterGear = $gears;
            $model->company_id=$company_id;
            $company = Customer::findOne($company_id);
            return $this->render('add', [
                'gears' => $gears,
                'model' => $model,
                'company'=>$company
            ]);
        }
        
        exit;
    }

    /**
     * Deletes an existing Order model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->deleteWithRelated();

        return $this->redirect(['list#tab-orders']);
    }

    
    /**
     * Finds the Order model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Order the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Order::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for EventOuterGear
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddEventOuterGear()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('EventOuterGear');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formEventOuterGear', ['row' => $row]);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    protected function preparePDF($order, $dest=null){
        if ($dest)
            $dist = $dest;
        else
            $dist = Pdf::DEST_BROWSER;
        $settings = Settings::find()->indexBy('key')->where(['section'=>'main'])->all(); 
        $content = $this->renderPartial('pdf', [
            'model' => $order,
            'settings' => $settings
        ]);

        $header = $this->renderPartial('pdf-header', [
            'model' =>  $order,
            'settings' => $settings
        ]);

        $footer = $this->renderPartial('pdf-footer', [
            'model' =>  $order,
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
                'options' => ['title' => Yii::t('app', 'Zamówienie')."-".$order->id],
                'filename' => Yii::getAlias('@uploadroot').'/order/zam-'.Inflector::slug($order->id).'.pdf',
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

        return $pdf;
    }

    public function actionSendMail($id)
    {
        $model = new \backend\models\SendOrderMail();
        $model->orderId = $id;
        $order = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()){
        $mail = \Yii::$app->mailer->compose('@app/views/order/mail', [
                'model' =>  $model,
                'order' => $order
            ])
            ->setFrom([Yii::$app->params['mailingEmail']=>$order->user->email])
            ->setTo($model->recipients)
            ->setSubject($model->subject)
            ->setReplyTo($order->user->email);
            $pdf = $this->preparePDF($order,Pdf::DEST_FILE);
            $pdf->render();
            $filename = Inflector::slug("zamowienie_".$order->id);
            $mail->attach($pdf->filename);           
            if ($mail->send())
            {
                Note::createNote(2, 'orderSend', $order, $order->company_id);
                Yii::$app->session->setFlash('success', Yii::t('app', 'Email wysłany!'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('app', 'Wystąpił błąd!'));
            }

            return $this->redirect(['view', 'id'=>$id]);
        }
                return $this->render('send-mail', [
            'model' => $model,
        ]);
    }

    public function actionMarkResolved()
    {
        $eogms = EventOuterGearModel::find()->where(['resolved'=>0])->all();
        foreach ($eogms as $eogm)
        {
            $ids = ArrayHelper::map(OuterGear::find()->where(['outer_gear_model_id'=>$eogm->outer_gear_model_id])->asArray()->all(), 'id', 'id');
                    $eogs = EventouterGear::find()->where(['event_id'=>$eogm->event_id, 'outer_gear_id'=>$ids])->all();
                    $sum = 0;
                    foreach ($eogs as $eog)
                    {
                        $sum+=$eog->quantity;
                    }
                    if ($sum >= $eogm->quantity)
                    {
                        $eogm->resolved = 1;
                    }else{
                        $eogm->resolved = 0;
                    }
                    $eogm->save();
        }
        echo "Done!";
        //return $this->redirect(['index']);
        exit;
    }
}
