<?php

namespace backend\modules\finances\controllers;

use backend\components\Controller;
use backend\modules\finances\models\SendForm;
use common\components\filters\AccessControl;
use common\components\Model;
use common\helpers\ArrayHelper;
use common\models\EventExpense;
use common\models\Expense;
use common\models\EventLog;
use common\models\ExpenseAttachment;
use common\models\ExpenseContent;
use common\models\ExpenseContentRate;
use common\models\ExpensePaymentHistory;
use common\models\ExpenseSearch;
use kartik\mpdf\Pdf;
use Yii;
use yii\filters\VerbFilter;
use yii\web\BadRequestHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;

/**
 * ExpenseController implements the CRUD actions for Expense model.
 */
class ExpenseController extends Controller
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
                        'actions' => ['index', 'event-expenses'],
                        'roles' => ['menuInvoicesExpense'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['create', 'upload', 'delete-file'],
                        'roles' => ['menuInvoicesExpenseCreate'],

                    ],
                    [
                        'allow' => true,
                        'actions' => ['view'],
                        'roles' => ['menuInvoicesExpenseView'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'roles' => ['menuInvoicesExpenseDelete'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['update', 'edit', 'add-payment', 'history-remove', 'history-edit'],
                        'roles' => ['menuInvoicesExpenseEdit'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['send'],
                        'roles' => ['menuInvoicesExpenseSend'],
                    ],
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

    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/'.ExpenseAttachment::UPLOAD_DIR,
                'afterUploadHandler' => [$this, 'batchCreate'],
            ]
        ];

        return array_merge(parent::actions(), $actions);
    }

        public function actionHistoryEdit($id)
    {
        $model = ExpensePaymentHistory::findOne($id);
        if ($model->load(Yii::$app->request->post())  && $model->save()) {
            $invoice = $model->expense;
            $invoice->countPayments();
            $invoice->attributesUpdate();
            $invoice->save();
            $invoice->storeData();

            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['sum'=>Yii::$app->formatter->asCurrency($invoice->alreadypaid), 'id'=>$id];
        }else{
            $payments = \common\models\ExpensePaymentHistory::find()->where(['expense_id' => $model->expense_id])->all();
            return $this->renderAjax('add-payment', [
                'model' => $model,
                'payments' => $payments
            ]);
        }
    }

    /**
     * Lists all Expense models.
     * @return mixed
     */
    public function actionIndex($y=null, $m=null)
    {
        $searchModel = new ExpenseSearch();
        $params = Yii::$app->request->queryParams;
        if (count($params)<1) {
          $params = Yii::$app->session['expenseparams'];
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
        Yii::$app->session['expenseparams'] = $params;
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

    /**
     * Displays a single Expense model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $data = $model->loadData();
        $tmpContent = new ExpenseContent();
        return $this->render('view2', [
            'model' => $data['model'],
            'data'=>$data,
	        'tmpContent'=>$tmpContent,
        ]);
    }

    /**
     * Creates a new Expense model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($type=1, $id=null, $user_id=null, $year=null, $month=null)
    {
        $model = new Expense([
            'currency' => Yii::$app->settings->get('defaultCurrency', 'main'),
            'date' => date('Y-m-d'),
        ]);
        $model->eventIds = [$id];
        $model->needToLink = true;
        $model->type = $type;
        $items = [new ExpenseContent()];
        $rates = [new ExpenseContentRate()];

        $payment = new ExpensePaymentHistory();
        $payment->creator_id = Yii::$app->user->id;
        $payment->payment_method = Yii::t('app', 'przelew');

        $post = Yii::$app->request->post();
        if ($model->load($post) && $payment->load($post))
        {
            if (!$model->alreadypaid)
                $model->alreadypaid = 0;
            $items = Model::createMultiple(ExpenseContent::className());
            if ($model->expense_type < Expense::NO_ITEMS_TYPE)
            {
                Model::loadMultiple($items, $post);
            }
            else
            {
                $items = [];
            }

            $rates = Model::createMultiple(ExpenseContentRate::className());
            Model::loadMultiple($rates, $post);

            if (Yii::$app->request->isAjax)
            {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($items),
                    ActiveForm::validateMultiple($rates),
                    ActiveForm::validate($model)
                );
            }
            if (is_numeric($payment->amount))
                $model->alreadypaid += $payment->amount;

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($items) && $valid;
            $valid = Model::validateMultiple($rates) && $valid;

            $customerId = $model->customer_id;
            foreach ($items as $item)
            {
                if ($item->eventExpense !== null)
                    if (!$item->eventExpense->customer_id)
                    {
                        $item->eventExpense->customer_id = $customerId;
                        $item->eventExpense->save();
                    }
                if ($item->eventExpense !== null && $item->eventExpense->customer_id != $customerId)
                {
                    $model->addError('customer_id', Yii::t('app', 'Dostawcy kosztów nie zgadzają się').$item->eventExpense->customer_id." ".$model->customer_id);
                    $valid = false;
                }
            }


            if ($valid) {
                $transaction = \Yii::$app->db->beginTransaction();

                try {
                    if ($flag = $model->save(false)) {
                        
                        $payment->expense_id = $model->id;
                        $payment->payment_method = $model->paymentmethod;
                        $payment->creator_id = Yii::$app->user->id;
                        if ($payment->amount>0)
                            $payment->save(true);

                        foreach ($items as $item) {
                            $item->expense_id = $model->id;
                            if (! ($flag = $item->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                        foreach ($rates as $rate) {
                            $rate->expense_id = $model->id;
                            if (! ($flag = $rate->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                    }
                    if ($flag) {

                        if ( Yii::$app->session->get(self::SESSION_ATTACHMENTS_KEY) != null)
                        {
                            ExpenseAttachment::updateAll([
                                'expense_id'=>$model->id,
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
                        if ($user_id)
                        {
                            $eup = new \common\models\ExpenseUserPayment();
                            $eup->expense_id = $model->id;
                            $eup->user_id = $user_id;
                            $eup->year = $year;
                            $eup->month = $month;
                            $eup->save();
                        }
//                        $model->linkObjects();

                        $transaction->commit();
                                        $eventlog = new EventLog;
                                        $eventlog->event_id = $id;
                                        $eventlog->user_id = Yii::$app->user->identity->id;
                                        $eventlog->content = Yii::t('app', "Wystawiono fakturę kosztową")." ".$model->name;
                                        $eventlog->save();
                        return $this->redirect(['view', 'id' => $model->id]);
                    }
                } catch (Exception $e) {
                    $transaction->rollBack();
                }

                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                return $this->redirect(['view', 'id' => $model->id]);
            }



        }



        if (empty($items) == true)
        {
            $items = [new ExpenseContent()];
        }
        if (empty($rates) == true)
        {
            $rates = [new ExpenseContentRate()];
        }

        return $this->render('create', [
            'model' => $model,
            'items' => $items,
            'rates' => $rates,
            'payment'=>$payment,
        ]);
    }

    /**
     * Updates an existing Expense model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->loadLinkedObjects();
        $model->needToLink = true;
        $items = $model->expenseContents;
        $rates = $model->expenseContentRates;
        $alreadypaid = $model->alreadypaid;
        $payment = new ExpensePaymentHistory([
            'expense_id' => $model->id,
            'creator_id'=>Yii::$app->user->id,
            'payment_method'=>Yii::t('app', ' przelew')
        ]);

        $post = Yii::$app->request->post();
        if ($model->load($post) && $payment->load($post))
        {

            $oldItems = ArrayHelper::map($items, 'id', 'id');
            if ($model->expense_type<Expense::NO_ITEMS_TYPE)
            {
                $items = Model::createMultiple(ExpenseContent::className(), $items);
                Model::loadMultiple($items, $post);
            }
            else
            {
                $items = [];
            }

            $deletedItems= array_diff($oldItems, array_filter(ArrayHelper::map($items, 'id', 'id')));



            $oldRates = ArrayHelper::map($rates, 'id', 'id');
            $rates = Model::createMultiple(ExpenseContentRate::className(), $rates);
            Model::loadMultiple($rates, $post);
            $deletedRates= array_diff($oldRates, array_filter(ArrayHelper::map($rates, 'id', 'id')));

            if (Yii::$app->request->isAjax)
            {
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ArrayHelper::merge(
                    ActiveForm::validateMultiple($items),
                    ActiveForm::validateMultiple($rates),
                    ActiveForm::validate($model)
                );
            }

            if ($payment->amount && $model->paid ==0)
            {
                $model->alreadypaid += $payment->amount;
            }

            // validate all models
            $valid = $model->validate();
            $valid = Model::validateMultiple($items) && $valid;
            $valid = Model::validateMultiple($rates) && $valid;

            $customerId = $model->customer_id;
            foreach ($items as $item)
            {
                if ($item->eventExpense !== null && $item->eventExpense->customer_id != $customerId)
                {
                    $model->addError('customer_id', Yii::t('app', 'Dostawcy kosztów nie zgadzają się'));
                    $valid = false;
                }
            }

            if ($valid)
            {
                $transaction = \Yii::$app->db->beginTransaction();
                try {
                    if ($flag = $model->save(false))
                    {
                        /*
                        EventExpense::updateAll([
                            'expense_id'=>null,
                            'invoice_nr'=>null,
                        ], [
                            'expense_id'=>$model->id,
                        ]);
                        */
                        if (empty($deletedItems) == false)
                        {
                            ExpenseContent::deleteAll(['id' => $deletedItems]);
                        }
                        foreach ($items as $item) {
                            $item->expense_id = $model->id;
                            if (! ($flag = $item->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }

                        if (empty($deletedRates) == false) {
                            ExpenseContentRate::deleteAll(['id' => $deletedRates]);
                        }
                        foreach ($rates as $rate) {
                            $rate->expense_id = $model->id;
                            if (! ($flag = $rate->save(false))) {
                                $transaction->rollBack();
                                break;
                            }
                        }
                    }
                    if ($flag) {

                        if ( Yii::$app->session->get(self::SESSION_ATTACHMENTS_KEY) != null)
                        {
                            ExpenseAttachment::updateAll([
                                'expense_id'=>$model->id,
                            ],
                                [
                                    'id'=>Yii::$app->session->get(self::SESSION_ATTACHMENTS_KEY)
                                ]);
                            Yii::$app->session->remove(self::SESSION_ATTACHMENTS_KEY);
                        }
                        $model->attributesUpdate();
                        $model->save(false);
                        $payment->payment_method = $model->paymentmethod;
                        $payment->creator_id = Yii::$app->user->id;
                        if ($model->paid==1)
                        {
                            $payment->amount = $model->total - $alreadypaid;

                        }
                            //echo var_dump($payment);
                        if ($payment->amount != 0)
                        {
                            $payment->save();
                        }
                        $model->storeData();
//                        $model->linkObjects();

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
        if (empty($items) == true)
        {
            $items = [new ExpenseContent()];
        }
        if (empty($rates) == true)
        {
            $rates = [new ExpenseContentRate()];
        }

        return $this->render('create', [
            'model' => $model,
            'items' => $items,
            'rates' => $rates,
            'payment' => $payment,
        ]);
    }

    /**
     * Deletes an existing Expense model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

        public function actionDeleteFile($id)
    {
        $model = ExpenseAttachment::findOne($id);
        $model->delete();
        $model->expense->attributesUpdate();
        $model->expense->save(false);
        $model->expense->storeData();
        return $this->redirect(['view', 'id'=>$model->expense_id]);
    }

    /**
     * Finds the Expense model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Expense the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Expense::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionAddPayment($id)
    {
        $invoice = $this->findModel($id);
        $model = new ExpensePaymentHistory([
            'expense_id' => $id, 'date'=>date('Y-m-d'), 'creator_id'=>Yii::$app->user->id, 'amount'=>$invoice->remaining
        ]);
        if ($model->load(Yii::$app->request->post())  && $model->save()) {
            
            $invoice->alreadypaid += $model->amount;
            if ($invoice->alreadypaid>=$invoice->total)
                $invoice->paid = 1;
            $invoice->attributesUpdate();
            $invoice->save();
            $invoice->storeData();

            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['sum'=>Yii::$app->formatter->asCurrency($invoice->alreadypaid), 'id'=>$id];
        }else{
            $payments = \common\models\ExpensePaymentHistory::find()->where(['expense_id' => $id])->all();
            return $this->renderAjax('add-payment', [
                'model' => $model,
                'payments' => $payments
            ]);
        }
    }
      

    public function actionEventExpenses($customer_id=null)
    {
        $id = Yii::$app->request->post('id', false);

        if (Yii::$app->request->isAjax == false)
        {
            throw new BadRequestHttpException();
        }

        $models = [];
        if ($id !== false)
        {
            if ($customer_id)
            {
                $models = EventExpense::find()
                ->joinWith('customer')
                ->where([
                    'event_id'=>$id,
                    'group_id'=>null,
                    'customer_id'=>$customer_id
                ])
                ->asArray()
                ->all();
            }else{
                $models = EventExpense::find()
                ->joinWith('customer')
                ->where([
                    'event_id'=>$id,
                    'group_id'=>null,
                ])
                ->asArray()
                ->all();
            }
            

        }




        Yii::$app->response->format = Response::FORMAT_JSON;
        return $models;
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
        $model->loadEmails();
        $post = Yii::$app->request->post();
        if ($model->load($post))
        {
            $model->arrayTo();
            if ($model->validate())
            {
                if ($model->send())
                {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Wysłano'));
                    return $this->refresh();
                }
                else
                {
                    Yii::$app->session->setFlash('error', Yii::t('app', 'Nie wysłano'));

                }
            }

            $model->stringTo();
        }

        return $this->render('send', ['model'=>$model]);


    }

    public function batchCreate($data)
    {
        /* @var $file \yii\web\UploadedFile */
        $file = $data['file'];
        $model = new ExpenseAttachment();
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
        }
    }

    public function actionHistoryRemove($id)
    {
        if (Yii::$app->request->isAjax)
        {
            $model = ExpensePaymentHistory::findOne($id);
            if ($model!==null)
            {
                $transaction = Yii::$app->db->beginTransaction();
                try
                {
                    $invoice = $model->expense;
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
                    echo "dupa";
                    $transaction->rollBack();
                }
            }
        }
        else {
            throw new BadRequestHttpException();
        }
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
        $payment = new ExpensePaymentHistory([
            'expense_id' => $model->id,
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
            $model->alreadypaid += $payment->amount;

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
}
