<?php

namespace backend\controllers;

use backend\modules\permission\models\BasePermission;
use common\components\filters\AccessControl;
use common\models\GearItem;
use common\models\RentGearItem;
use common\models\Notification;
use Yii;
use common\models\Rent;
use common\models\RentGear;
use common\models\RentSearch;
use backend\components\Controller;
use yii\web\Response;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use kartik\mpdf\Pdf;
use common\models\Settings;
use yii\helpers\Inflector;
/**
 * RentController implements the CRUD actions for Rent model.
 */
class RentController extends Controller
{
    public $enableCsrfValidation = false;
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'rules' => [
                [
                    'allow'=>true,
                    'actions' => ['index','change-status'],
                    'roles' => ['eventRents'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['eventRentsAdd'],

                ],
                [
                    'allow' => true,
                    'actions' => ['view', 'packing-list'],
                    'roles' => ['@'],
                    'matchCallback' => function() {
                        if (Yii::$app->user->can('eventRentsView'.BasePermission::SUFFIX[BasePermission::ALL])) {
                            return true;
                        }
                        return $this->isAuthor('eventRentsView');
                    }

                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['@'],
                    'matchCallback' => function() {
                        if (Yii::$app->user->can('eventRentsDelete'.BasePermission::SUFFIX[BasePermission::ALL])) {
                            return true;
                        }
                        return $this->isAuthor('eventRentsDelete');
                    }
                ],
                [
                    'allow' => true,
                    'actions' => ['update', 'invoice-ready', 'status'],
                    'roles' => ['@'],
                    'matchCallback' => function() {
                        if (Yii::$app->user->can('eventRentsEdit'.BasePermission::SUFFIX[BasePermission::ALL])) {
                            return true;
                        }
                        return $this->isAuthor('eventRentsEdit');
                    }
                ],
            ]
        ];

        return $behaviors;
    }

    private function isAuthor($text) {
        $rent = $this->findModel(Yii::$app->request->get('id'));
        if (Yii::$app->user->can($text.BasePermission::SUFFIX[BasePermission::MINE])) {
            if ($rent->created_by == Yii::$app->user->id) {
                return true;
            }
            if ($rent->manager_id == Yii::$app->user->id) {
                return true;
            }
        }
        return false;
    }

    /**
     * Lists all Rent models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new RentSearch();
        $params = Yii::$app->request->queryParams;
        if (count($params)<1) {
          $params = Yii::$app->session['rentparams'];
          } else {
            Yii::$app->session['rentparams'] = $params;
        }
        $dataProvider = $searchModel->search($params);
        /*
        $user = Yii::$app->user;
        if (!$user->can('SiteAdministrator') && $user->can('eventRents'.BasePermission::SUFFIX[BasePermission::MINE])) {
            $query = Rent::find()->where(['created_by' => Yii::$app->user->id])->orWhere(['manager_id'=>Yii::$app->user->id]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
        }
    */
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Rent model.
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
     * Creates a new Rent model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($start=null)
    {
        $model = new Rent();
        $schema = \common\models\TasksSchema::find()->where(['type'=>3])->andWhere(['default'=>1])->one();
        if ($schema)
            $model->tasks_schema_id = $schema->id;
        if ($start == null)
        {
            $start = date('Y-m-d');
        }
        $model->start_time = date($start.' 00:00:00');
        $model->end_time = date($start.' 23:59:59');

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            if ($model->tasks_schema_id)
            {
                $model->copyTasks();
            }
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else
        {
            $model->prepareDateAttributes();
            return $this->render('create', [
                'model' => $model,
                'schema_change_possible' => true
            ]);
        }
    }

    /**
     * Updates an existing Rent model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $oldModel = clone $model;
        $dateError = [];
        $schema_change_possible = true;
        $tasks = \common\models\Task::find()->where(['rent_id'=>$model->id])->andWhere(['status'=>10])->count();
        if ($tasks)
            $schema_change_possible = false;
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
            if (($model->tasks_schema_id)&&($schema_change_possible)&&($oldModel->tasks_schema_id!=$model->tasks_schema_id))
            {
                $model->deleteAllTasks();
                $model->copyTasks();
            }
            if ($model->status!=$oldModel->status)
            {
                $model->addLog(Yii::t('app', 'Zmieniono status wypożyczenia na:').$model->statusLabel);
            }else{
                $logAdded = false;
                $dateChanged = false;
                if ($model->name!=$oldModel->name){
                    $model->addLog(Yii::t('app', 'Zmieniono nazwę z ').$oldModel->name.Yii::t('app', ' na ').$model->name);
                    $logAdded = true;
                }
                if ($model->days!=$oldModel->days){
                    $model->addLog(Yii::t('app', 'Zmieniono liczbę dni pracy z ').$oldModel->days.Yii::t('app', ' na ').$model->days);
                    $logAdded = true;
                }
                if ($model->manager_id!=$oldModel->manager_id)
                {
                    $m1 = Yii::t('app','Brak');
                    $m2 = $m1;
                    if ($model->manager_id)
                    {
                        $m1 = $model->manager->displayLabel;
                    }
                    if ($oldModel->manager_id)
                    {
                        $m2 = $oldModel->manager->displayLabel;
                    }
                    $model->addLog(Yii::t('app', 'Zmieniono osobę odpowiedzialną z ').$m2.Yii::t('app', ' na ').$m1);
                    $logAdded = true;
                }
                if ($model->start_time.":00"!=$oldModel->start_time){
                    $model->addLog(Yii::t('app', 'Zmieniono początek z ').$oldModel->start_time.Yii::t('app', ' na ').$model->start_time);
                    $logAdded = true;
                    $dateChanged = true;
                }
                if ($model->end_time.":00"!=$oldModel->end_time){
                    $model->addLog(Yii::t('app', 'Zmieniono koniec z ').$oldModel->end_time.Yii::t('app', ' na ').$model->end_time);
                    $logAdded = true;
                    $dateChanged = true;
                }
                if ($dateChanged)
                {
                    $rentGears = RentGear::find()->where(['rent_id'=>$model->id])->all();
                    if (($model->start_time.":00">=$oldModel->start_time)&&($model->end_time.":00"<=$oldModel->end_time))
                    {
                        //nowe daty zawierają się w poprzednim przedziale, możemy bezpiecznie zmienić rezerwację urządzeń
                        
                        foreach ($rentGears as $rg)
                        {
                            $rg->start_time = $model->start_time;
                            $rg->end_time = $model->end_time;
                            $rg->save();
                        }
                        foreach ($model->rentGearItems as $rg)
                        {
                            $rg->start_time = $model->start_time;
                            $rg->end_time = $model->end_time;
                            $rg->save();
                        }
                    }else{
                        $reverse = false;
                        foreach ($rentGears as $rg)
                        {
                            $count = $rg->gear->getAvailableDateChanged($model->start_time, $model->end_time, $model->id, 'rent');
                            if ($count<$rg->quantity)
                            {
                                $reverse = true;
                                $dateError[] = ['gear'=>$rg->gear, 'missing'=>$rg->quantity-$count];
                            }
                        }
                        if ($reverse)
                        {
                            $model->start_time = $oldModel->start_time;
                            $model->end_time = $oldModel->end_time;
                            $model->save();
                            $model->prepareDateAttributes();
                            return $this->render('update', [
                                    'model' => $model,
                                    'dateError' => $dateError
                                ]);
                        }  else{
                            foreach ($rentGears as $rg)
                            {
                                $rg->start_time = $model->start_time;
                                $rg->end_time = $model->end_time;
                                $rg->save();
                            }
                            foreach ($model->rentGearItems as $rg)
                            {
                                $rg->start_time = $model->start_time;
                                $rg->end_time = $model->end_time;
                                $rg->save();
                            }                           
                        }                    
                    }                    
                }

                if ($model->customer_id!=$oldModel->customer_id)
                {

                    if ($oldModel->customer_id)
                        $model->addLog(Yii::t('app', 'Zmieniono klienta z ').$oldModel->customer->name.Yii::t('app', ' na ').$model->customer->name);
                    else
                        $model->addLog(Yii::t('app', 'Zmieniono klienta ').Yii::t('app', ' na ').$model->customer->name);
                    $logAdded = true;
                }
                if ($model->description!=$oldModel->description)
                {

                    $model->addLog(Yii::t('app', 'Zmieniono opis'));
                    $logAdded = true;
                }
                if (!$logAdded)
                    $model->addLog(Yii::t('app', 'Zmieniono dane podstawowe wypożyczenia.'));

            }
            return $this->redirect(['view', 'id' => $model->id]);
        }
        else
        {
            $model->prepareDateAttributes();
            return $this->render('update', [
                'model' => $model,
                'dateError' => $dateError,
                'schema_change_possible' => $schema_change_possible
            ]);
        }
    }

    public function actionChangeStatus($rent_id, $status)
    {
        $model = $this->findModel($rent_id);
        $model->status = $status;
        $success = false;
        if ($model->save()){
            $model->addLog(Yii::t('app', 'Zmieniono status wypożyczenia na: ').$model->statusLabel);
            $success = true;
        }else{
            var_dump($model->errors);
        }
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $success;
        exit;
    }


    public function actionStatus($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = $this->findModel($id);
        $old = $model->status;
        $post = Yii::$app->request->post();
        $status = $post['Rent'][$post['editableIndex']]['status'];
        $model->status = $status;
        $model->save();
        $model->addLog(Yii::t('app', 'Zmieniono status wypożyczenia na:').$model->statusLabel);
        $list = \common\models\Rent::getStatusList();
        $output = ['output'=>$list[$model->status], 'message'=>''];
        return $output;
        exit;
    }

    public function actionInvoiceReady($id)
    {
        $model = $this->findModel($id);
        $model->status = Rent::READY_TO_INVOICE;
        $model->save(); 
                $notification = Notification::getByName(Notification::READY_TO_INVOICE);
                $users = $notification->getRecipients()->getModels();
                $notification->addUserNotification($users, ['event'=>$model, 'creator'=>Yii::$app->user->identity], $model);
                $notification->added = true;
                foreach ($users as $user)
                {
                    $notification->sendUserNotifications($user, Notification::READY_TO_INVOICE, [$model]);  
                }     
        return $this->redirect(['view', 'id' => $model->id]);
    }

    /**
     * Deletes an existing Rent model.
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
     * Finds the Rent model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Rent the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Rent::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionUpdateWorkingTimeEventGearItem($eventId, $itemId = null, $gearId, $gearGroup = null, $otherEvent = false) {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $event = Rent::find()->where(['id'=>$eventId])->one();
        $start = $_POST['RentGearItem']['start_time'];
        $end = $_POST['RentGearItem']['end_time'];

        if ($itemId != null) {
            $gears = RentGearItem::find()->where(['rent_id' => $eventId])->andWhere(['gear_item_id' => $itemId])->all();
            foreach ($gears as $gear) {
                $gear->start_time = $start;
                $gear->end_time = $end;
                $gear->save();
            }
        }
        else {
            foreach (GearItem::find()->where(['gear_id' => $gearId])->andWhere(['group_id' => $gearGroup])->all() as $gearItem) {
                $gears = RentGearItem::find()->where(['rent_id' => $eventId])->andWhere(['gear_item_id' => $gearItem->id])->all();
                foreach ($gears as $gear) {
                    $gear->start_time = $start;
                    $gear->end_time = $end;
                    $gear->save();
                }
            }
        }
        $output = $start.' - '.$end;
        if ($start == $event->getTimeStart() && $end == $event->getTimeEnd() && !$otherEvent) {
            $output = 'Cały event';
        }
        return ['output' => $output, 'message' => null, 'gear_id' => $gearId];
    }

    public function actionPackingList($id)
    {
        $model = $this->findModel($id);
        $pdf = $this->preparePDF($model);
        return $pdf->render();
    }

    protected function preparePDF($event){
        $dist = Pdf::DEST_BROWSER;
        $settings = Settings::find()->indexBy('key')->where(['section'=>'main'])->all(); 
        $content = $this->renderPartial('pdf', [
            'model' => $event,
            'settings' => $settings
        ]);

        $header = $this->renderPartial('pdf-header', [
            'model' =>  $event,
            'settings' => $settings
        ]);

        $footer = $this->renderPartial('pdf-footer', [
            'model' =>  $event,
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
                'options' => ['title' => 'PackingLista-'.$event->name],
                'filename' => Yii::getAlias('@uploadroot').'/rent/packing-list-'.Inflector::slug($event->id).'.pdf',
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
}
