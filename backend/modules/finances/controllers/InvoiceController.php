<?php

namespace backend\modules\finances\controllers;

use backend\modules\finances\models\SendForm;
use common\actions\EditableColumnAction;
use common\components\filters\AccessControl;
use common\helpers\ArrayHelper;
use common\models\Event;
use common\models\InvoiceTypeDefaultSeries;
use common\models\Rent;
use common\models\EventLog;
use common\models\GearItem;
use common\models\InvoiceAttachment;
use common\models\InvoiceContent;
use common\models\InvoicePaymentHistory;
use common\models\Settings;
use function igorw\retry;
use kartik\form\ActiveForm;
use kartik\mpdf\Pdf;
use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use Yii;
use common\models\Invoice;
use common\models\InvoiceSearch;
use backend\components\Controller;
use common\components\Model;
use yii\bootstrap\BootstrapAsset;
use yii\helpers\Inflector;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * InvoiceController implements the CRUD actions for Invoice model.
 */
class InvoiceController extends Controller
{
    public $layout = '@backend/themes/e4e/layouts/main-panel';
    public $enableCsrfValidation = false;
    const SESSION_ATTACHMENTS_KEY = 'attachmentsId';

    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow'=>true,
                        'actions' => ['index'],
                        'roles' => ['menuInvoicesInvoice'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'upload', 'delete-file'],
                        'roles' => ['menuInvoicesInvoiceCreate'],

                    ],
                    [
                        'allow' => true,
                        'actions' => ['view', 'pdf'],
                        'roles' => ['menuInvoicesInvoiceView'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete', 'history-remove'],
                        'roles' => ['menuInvoicesInvoiceDelete'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'edit', 'pay', 'add-payment', 'history-edit'],
                        'roles' => ['menuInvoicesInvoiceEdit'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['send'],
                        'roles' => ['menuInvoicesInvoiceSend'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['error', 'update-payments'],
                        'roles' => ['@']
                    ]
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                    'delete-file' => ['POST'],
                ],
            ],
        ];
    }

    public function actionUpdatePayments()
    {
        $invoices = Invoice::find()->where(['>', 'alreadypaid', 0])->all();
        foreach ($invoices as $invoice)
        {
            $payments = \common\models\InvoicePaymentHistory::find()->where(['invoice_id' => $invoice->id])->all();
            $sum = 0;
            foreach ($payments as $payment)
            {
                $sum +=$payment->amount;
            }
            if ($sum<$invoice->alreadypaid)
            {
                $amount = $invoice->alreadypaid-$sum;
                $p = new \common\models\InvoicePaymentHistory();
                $p->amount = $amount;
                $p->invoice_id = $invoice->id;
                $p->date = date("Y-m-d");
                $p->save();
            }
        }
        exit;
    }

    public function actionAddPayment($id)
    {
        $invoice = $this->findModel($id);
        $model = new InvoicePaymentHistory([
            'invoice_id' => $id, 'date'=>date('Y-m-d'), 'creator_id'=>Yii::$app->user->id, 'amount'=>$invoice->remaining
        ]);
        if ($model->load(Yii::$app->request->post())  && $model->save()) {
            
            if ($invoice->alreadypaid)
                $invoice->alreadypaid += $model->amount;
            else
                $invoice->alreadypaid = $model->amount;
            $invoice->attributesUpdate();
            
            $invoice->save();
            $invoice->storeData();

            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['sum'=>Yii::$app->formatter->asCurrency($invoice->alreadypaid), 'id'=>$id];
        }else{
            $payments = \common\models\InvoicePaymentHistory::find()->where(['invoice_id' => $id])->all();
            return $this->renderAjax('add-payment', [
                'model' => $model,
                'payments' => $payments
            ]);
        }
        
    }

    public function actionHistoryEdit($id)
    {
        $model = InvoicePaymentHistory::findOne($id);
        $amount= $model->amount;
        if ($model->load(Yii::$app->request->post())  && $model->save()) {
            $invoice = $model->invoice;
            $invoice->countPayments();
            $invoice->attributesUpdate();
            $invoice->save();
            $invoice->storeData();

            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['sum'=>Yii::$app->formatter->asCurrency($invoice->alreadypaid), 'id'=>$id];
        }else{
            $payments = \common\models\InvoicePaymentHistory::find()->where(['invoice_id' => $model->invoice_id])->all();
            return $this->renderAjax('add-payment', [
                'model' => $model,
                'payments' => $payments
            ]);
        }
    }

    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/'.InvoiceAttachment::UPLOAD_DIR,
                'afterUploadHandler' => [$this, 'batchCreate'],
            ],
            'edit' => [                                       // identifier for your editable column action
                'class' => EditableColumnAction::className(),     // action class name
                'modelClass' => Invoice::className(),                // the model for the record being edited
                'outputValue' => function ($model, $attribute, $key, $index) {
                    return $model->$attribute;      // return any custom output value if desired
                },
                'outputMessage' => function($model, $attribute, $key, $index) {
                    return '';                                  // any custom error to return after model save
                },
                'showModelErrors' => true,                        // show model validation errors after save
                'errorOptions' => ['header' => '']                // error summary HTML options
                // 'postOnly' => true,
                // 'ajaxOnly' => true,
                // 'findModel' => function($id, $action) {},
                // 'checkAccess' => function($action, $model) {}
            ]
        ];

        return array_merge(parent::actions(), $actions);
    }

    /**
     * Lists all Invoice models.
     * @return mixed
     */
    public function actionIndex($y=null, $m=null)
    {
        $searchModel = new InvoiceSearch();
        $params = Yii::$app->request->queryParams;
        if (count($params)<1) {
          $params = Yii::$app->session['invoiceparams'];
          } 
        $date = new \DateTime();
        if($y==null)
        {
            if (isset($params['y']))
            {
                    $y = $params['y'];
            }else{
                    $y = $date->format('Y');
            }
            
        }
        if ($m==null)
        {
            if (isset($params['m']))
            {
                    $m = $params['m'];
            }else{
                    $m = 0;
            }
        }
        $searchModel->year = $y;
        $searchModel->month = $m; 
        if ($m)
            $date = \DateTime::createFromFormat('Yn', $y.$m);
        else
            $date =  \DateTime::createFromFormat('Y', $y);
        //$params = Yii::$app->request->queryParams;

        if (empty($params[$searchModel->formName()]['dateRange'])==true)
        {
            
            if ($m)
            {
                $searchModel->dateStart = $date->format('Y-m-01');
                $searchModel->dateEnd = $date->format('Y-m-t');
            }else{
                $searchModel->dateStart = $date->format('Y-01-01');
                $searchModel->dateEnd = $date->format('Y-12-31');                
            }

            $searchModel->year = $y;
            $searchModel->month = $m;
        }

        Yii::$app->session['invoiceparams'] = $params;
        $dataProvider = $searchModel->search($params);
        $date = \DateTime::createFromFormat('Yn', $y.$m);
        $dateInterval = new \DateInterval('P1M');
        $date1 = clone $date;
        $date2 = clone $date;
        $date1->sub($dateInterval);
        $date2->add($dateInterval);
        $prev = [
            'y'=>$date1->format('Y'),
            'm'=>$date1->format('n'),
        ];
        $next = [
            'y'=>$date2->format('Y'),
            'm'=>$date2->format('n'),
        ];
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'next' => $next,
            'prev' => $prev,
            'date'=>$date,
            'y'=>$y,
            'm'=>$m
        ]);
    }

    public function actionView($id)
    {
        $settings = Yii::$app->settings;
        $model = $this->findModel($id);
        $data = $model->loadData();
        $tmpModel = new Invoice();
        $tmpContent = new InvoiceContent();
        $parentInvoice = null;
        $data2 = null;
        if (in_array($model->type, [Invoice::TYPE_CORRECTION_DATA, Invoice::TYPE_CORRECTION_ITEMS]) && $model->parent_id) {
            $parentInvoice = \common\models\Invoice::findOne($model->parent_id);
            $data2 = $parentInvoice->loadData();
            //$data['model']['total'] = $parentInvoice->total - $data['model']['total'];
            //$data['model']['netto'] = $parentInvoice->netto - $data['model']['netto'];
            //$data['model']['tax'] = $parentInvoice->tax - $data['model']['tax'];
        }

        return $this->render('view2', [
            'model' => $data['model'],
            'data'=>$data,
            'tmpModel'=>$tmpModel,
	        'tmpContent'=>$tmpContent,
            'modelObject' => $model,
            'parentInvoice' => $parentInvoice,
            'data2'=>$data2
        ]);
    }

    /**
     * Creates a new Invoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($type=0, $id=null, $invoiceId=null, $owner=null)
    {
        $settings = Yii::$app->settings;
        $items = [];
        $model = new Invoice([
            'parent_id' => $invoiceId,
            'currency' => Yii::$app->settings->get('defaultInvoiceCurrency', 'main'),
            'disposaldate' => date('Y-m-d'),
            'date' => date('Y-m-d'),
            'paymentdate' => date('Y-m-d'),
            'bank_account' => $settings->get('companyBankNumber', 'main'),
            'bank_name' => $settings->get('companyBankName', 'main'),
	        'owner_type' => $owner,
	        'owner_id' => $id,
        ]);
        if ($deafulSeries = InvoiceTypeDefaultSeries::find()->where(['invoice_type' => $type])->one()) {
            $model->series_id = $deafulSeries->series_id;
        }

        if (in_array($type, [Invoice::TYPE_CORRECTION_DATA, Invoice::TYPE_CORRECTION_ITEMS]) && !$invoiceId) {
            throw new BadRequestHttpException(Yii::t('app', 'Nie wybrano faktury do skorygowania'));
        }

        if ($invoiceId) {
            $model2 = $this->findModel($invoiceId);
            if (in_array($type, [Invoice::TYPE_CORRECTION_DATA, Invoice::TYPE_CORRECTION_ITEMS]) && in_array($model2->type, [Invoice::TYPE_CORRECTION_DATA, Invoice::TYPE_CORRECTION_ITEMS])) {
                // nie można korygować faktury korygującej
                throw new BadRequestHttpException(Yii::t('app', 'Nie można korygować faktury korygującej'));
            }

            $model->loadPaymentDatePeriod();
            $model->date = date('Y-m-d');
            $model->parent_id = $invoiceId;
            $items = $model->invoiceContents;
            if (empty($items))
            {
                $items = [
                    new InvoiceContent([
                        'invoice_id' => $model->id,
                    ]),
                ];
            }
            $payment = new InvoicePaymentHistory([
                'invoice_id' => $model->id,
            ]);
        }

        if ($id !== null)
        {
            $event = $model->owner;
            if ($model===null)
            {
                throw new NotFoundHttpException(Yii::t('app', 'Nie znaleziono wydarzenia'));
            }
            $model->description = $event->displayLabel;
            $model->owner_id = $event->id;
            $model->owner_class = $event::className();
            $model->customer_id = $event->customer_id;

            $nettoStored = Invoice::find()
                ->where([
                    'owner_id'=>$model->owner_id,
	                'owner_type'=>$model->owner_type
                ])
                ->sum('netto');


                /* @var $gearItem GearItem */
                $netto = $event->getEventValueSum();
                $netto -= $nettoStored;
                $item = new InvoiceContent([
                   'name' => $event->name.' ['.$event->code.']',
                    'count' => 1,
                    'price' => $netto,
                    'vat'=> 23,
                    'discount_percent' => 0,
                ]);
                $items[] = $item;

        }
        $payment = new InvoicePaymentHistory();

        $model->type = $type;
        if ($model->parent !== null)
        {
            $parent = $model->parent;
            $items = $parent->invoiceContents;
            $model->attributes = $parent->attributes;
            $model->number = null;
            if ($deafulSeries = InvoiceTypeDefaultSeries::find()->where(['invoice_type' => $type])->one()) {
                $model->series_id = $deafulSeries->series_id;
            }
            $model->parent_id = $invoiceId;
            $model->type = $type;
            $model->id = null;
        }

        if (empty($items) == true)
        {
            $items = [new InvoiceContent()];
        }

        $post = Yii::$app->request->post();
        if ($model->load($post) && $payment->load($post))
        {

            $items = Model::createMultiple(InvoiceContent::className());
            Model::loadMultiple($items, $post);

            if (Yii::$app->request->isAjax)
            {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($items),
                    ActiveForm::validate($model)
                );
            }

            if (!$model->alreadypaid)
                $model->alreadypaid = 0;
            if (!$payment->amount)
                $payment->amount = 0;
            $model->alreadypaid += $payment->amount;
            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($items) && $valid;
            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false)) {

                    	if (empty($payment->amount) == false)
	                    {
		                    $payment->invoice_id=$model->id;
		                    $payment->save(true);
	                    }

                        foreach ($items as $item) {
                            $item->invoice_id = $model->id;
                            if (! ($flag = $item->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                    }
                    if ($flag) {

                        if ( Yii::$app->session->get(self::SESSION_ATTACHMENTS_KEY) != null)
                        {
                            InvoiceAttachment::updateAll([
                                'invoice_id'=>$model->id,
                            ],
                                [
                                    'id'=>Yii::$app->session->get(self::SESSION_ATTACHMENTS_KEY)
                                ]);
                            Yii::$app->session->remove(self::SESSION_ATTACHMENTS_KEY);
                        }
                        $model->attributesUpdate();
                        $model->save(false);
                        if ($model->paid==1)
                        {
                            $payment->amount = $model->total;
                            $payment->save();
                        }
                        $model->storeData();

                        $transaction->commit();
                        if ($owner ==1)
                        {
                                        $eventlog = new EventLog;
                                        $eventlog->event_id = $model->owner_id;
                                        $eventlog->user_id = Yii::$app->user->identity->id;
                                        $eventlog->content = Yii::t('app', "Wystawiono fakturę")." ".$model->fullnumber;
                                        $eventlog->save();                            
                        }
                        if ($owner==2)
                        {
                            $rent = Rent::findOne($model->owner_id);
                            //$rent->status = Rent::INVOICE;
                            $rent->invoice_status = 10;
                            $rent->invoice_number = $model->fullnumber;
                            $rent->addLog(Yii::t('app', "Wystawiono fakturę: ").$rent->invoice_number);
                            $rent->save();
                        }
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }

                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                return $this->redirect(['view', 'id' => $model->id]);
            }



        }else{


        }

        return $this->render('create', [
            'model' => $model,
            'items' => $items,
            'payment'=>$payment,
        ]);
    }

    /**
     * Updates an existing Invoice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->loadPaymentDatePeriod();
        $items = $model->invoiceContents;
        if (empty($items))
        {
            $items = [
                new InvoiceContent([
                    'invoice_id' => $model->id,
                ]),
            ];
        }
        $payment = new InvoicePaymentHistory([
            'invoice_id' => $model->id,
        ]);

        $post = Yii::$app->request->post();
        if ($model->load($post) )
        {
	        $payment->load($post);
            $oldItems = ArrayHelper::map($items, 'id', 'id');
            $items = Model::createMultiple(InvoiceContent::className(), $items);
            Model::loadMultiple($items, $post);
            $deletedItems= array_diff($oldItems, array_filter(ArrayHelper::map($items, 'id', 'id')));

            if (Yii::$app->request->isAjax)
            {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($items),
                    ActiveForm::validate($model)
                );
            }

            if ($payment->amount && $model->paid ==0)
            {

                if ($model->alreadypaid)
                    $model->alreadypaid += $payment->amount;
                else
                    $model->alreadypaid = $payment->amount;
            }



            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($items) && $valid;

            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();


                try {
                    if ($flag = $model->save(false)) {
                        if (empty($deletedItems) == false) {
                            InvoiceContent::deleteAll(['id' => $deletedItems]);
                        }
                        foreach ($items as $item) {
                            $item->invoice_id = $model->id;
                            if (! ($flag = $item->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {
                        $model->attributesUpdate();
                        $model->save(false);
                        if ($model->paid==1)
                        {
                            //$payment->amount = $model->total - $model->alreadypaid;

                        }

                        if ($payment->amount != 0)
                        {
                            $payment->save();
                        }
                        $model->storeData();




                        $transaction->commit();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }

                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
            'items' => $items,
            'payment'=>$payment,
        ]);
    }

    /**
     * Deletes an existing Invoice model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (count($model->invoicePaymentHistories) > 0) {
            return $this->redirect(['error', 'id'=>$model->id]);
        }
        $model->delete();
        return $this->redirect(['index']);
    }

    public function actionDeleteFile($id)
    {
        $model = InvoiceAttachment::findOne($id);
        $model->delete();
        return $this->redirect(['view', 'id'=>$model->invoice_id]);
    }

    public function actionError($id) {
        $model = $this->findModel($id);
        $data = $model->loadData();
        return $this->render('delete-error', ['model'=>$model, 'data'=>$data]);
    }

    /**
     * Finds the Invoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Invoice::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionPdf($id, $action='print')
    {
        $dest = Pdf::DEST_BROWSER;
        switch ($action)
        {
            case 'print':
                $dest  =Pdf::DEST_BROWSER;
                break;
            case 'download':
                $dest = Pdf::DEST_DOWNLOAD;
                break;
            default:
                //błąd?
                break;
        }

        $model = $this->findModel($id);
        $pdf = $model->loadPdf();


        $pdf->destination = $dest;
        return $pdf->render();

    }

    public function actionSend()
    {
        $id = Yii::$app->request->get('id', false);
        if($id==false)
        {
            throw new BadRequestHttpException(Yii::t('app', 'Błędne żądanie'));
        }
        $invoices = [];
        if (is_array($id)==false)
        {
            $id = [$id];
        }

        foreach ($id as $i)
        {
            $invoices[] = $this->findModel($i);
        }

        $model = new SendForm([
            'invoices'=>$invoices,
        ]);

        $post = Yii::$app->request->post();
        if ($model->load($post))
        {
            if ($model->validate())
            {
                if ($model->send())
                {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Wysłano'));
                    return $this->redirect(['index']);
                }
                else
                {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Nie wysłano'));

                }
            }

//            $model->stringTo();
        }

        return $this->render('send', ['model'=>$model]);


    }

    public function batchCreate($data)
    {
        /* @var $file \yii\web\UploadedFile */
        $file = $data['file'];
        $model = new InvoiceAttachment();
        $model->attributes = $data['params'];
        $model->mime_type = $file->type;
        $model->base_name = $file->baseName;
        $model->extension = $file->extension;
        $model->filename = $data['filename'];
        if ($model->save())
        {
            $varKey = self::SESSION_ATTACHMENTS_KEY;
            $ids = Yii::$app->session->get($varKey, []);
            $ids[] = $model->id;
            Yii::$app->session->set($varKey, $ids);
            return $model->attributes;
        }
        else
        {
            return $model->errors;
        };
    }

    public function actionPay()
    {
        $request = Yii::$app->request;
        $post = $request->post();
        if (!isset($post['hasEditable'])) {
            return ['output' => '', 'message' => Yii::t('app', 'Błąd!')];
        }


        $key = ArrayHelper::getValue($post, 'editableKey');
        $model = $this->findModel($key);
        $payment = new InvoicePaymentHistory([
            'invoice_id' => $model->id,
        ]);
        $index = ArrayHelper::getValue($post, 'editableIndex');
        $attribute = ArrayHelper::getValue($post, 'editableAttribute');
        $formName = $model->formName();

        $val = $post[$formName][$index][$attribute];

        $date = ArrayHelper::getValue($post[$formName], 'paymentdate', date('Y-m-d'));
        $payment->date = $date;

        $message = '';
        if ($attribute=='alreadypaid')
        {
            $payment->amount = $val;
            if ($model->alreadypaid)
                $model->alreadypaid += $payment->amount;
            else
                $model->alreadypaid = $payment->amount;

        }
        else if ($attribute=='paid') {
            $model->paid = $val;
            if ($model->paid==1)
            {
                $payment->amount = $model->total - $model->alreadypaid;
            }
        }

        if ($payment->amount != 0)
        {
            $payment->save();
        }
        $model->attributesUpdate();
        $model->save();
        $model->storeData();

        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['output' => '', 'message' => $message];
    }

    public function actionHistoryRemove($id)
    {
        if (Yii::$app->request->isAjax)
        {
            $model = InvoicePaymentHistory::findOne($id);
            if ($model!==null)
            {
                $transaction = Yii::$app->db->beginTransaction();
                try
                {
                    $invoice = $model->invoice;
                    $invoice->paid = 0;
                    $invoice->alreadypaid -= $model->amount;
                    $invoice->attributesUpdate();
                    $invoice->save();
	                $model->delete();
	                $invoice->storeData();


                    $transaction->commit();
                }
                catch (\Exception $e)
                {
                    $transaction->rollBack();
                }
            }
        }
        else {
            throw new BadRequestHttpException();
        }
    }

}
