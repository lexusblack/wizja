<?php

namespace backend\controllers;

use Yii;
use common\models\CustomerNote;
use common\models\Customer;
use common\models\ClientNoteAttachment;
use common\models\CustomerNoteSearch;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CustomerNoteController implements the CRUD actions for CustomerNote model.
 */
class CustomerNoteController extends Controller
{

    public function actions()
    {
        $actions = [

            'upload'=> [
                'class'=>\backend\actions\UploadAction::className(),
                'upload'=>'/note-attachment',
                'afterUploadHandler' => [$this, 'batchCreate'],

            ]
        ];
        return array_merge(parent::actions(), $actions);
    }
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                    'delete-file' => ['post'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['index', 'view', 'create', 'update', 'delete', 'add-client-note-attachment', 'add-file', 'upload', 'delete-file'],
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
     * Lists all CustomerNote models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CustomerNoteSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CustomerNote model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $providerClientNoteAttachment = new \yii\data\ArrayDataProvider([
            'allModels' => $model->clientNoteAttachments,
        ]);
        return $this->render('view', [
            'model' => $this->findModel($id),
            'providerClientNoteAttachment' => $providerClientNoteAttachment,
        ]);
    }

    /**
     * Creates a new CustomerNote model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($customer_id, $contact_id=null, $offer_id=null, $event_id=null)
    {
        $customer = Customer::findOne($customer_id);
        $model = new CustomerNote();
        $model->customer_id = $customer_id;
        $model->contact_id = $contact_id;
        $model->offer_id = $offer_id;
        $model->event_id = $event_id;
        $model->user_id = Yii::$app->user->identity->id;
        $model->datetime = date("Y-m-d H:i:s");

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            if (Yii::$app->request->isAjax) {
                return true;
            }
            if ($offer_id)
                return $this->redirect(['/offer/default/view', 'id' => $offer_id, '#'=>'tab-notes']);
            else 
                if ($event_id){
                    $model->linkPermissions(Yii::$app->request->post('CustomerNote')['permissions']);
                    return $this->redirect(['/event/view', 'id' => $event_id, '#'=>'tab-calendar']);
                }else   
                    return $this->redirect(['/customer/view', 'id' => $model->customer_id, '#'=>'tab-notes']);
        } else {
            return $this->render('create', [
                'model' => $model,
                'customer'=>$customer,
                'event_id'=>$event_id,
                'ajax'=>false
            ]);
        }
    }

    /**
     * Updates an existing CustomerNote model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->permissions = $model->getPermissionIds();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['/customer/view', 'id' => $model->customer_id, '#'=>'tab-notes']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CustomerNote model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $customer_id = $model->customer_id;
        $model->delete();

            return $this->redirect(['/customer/view', 'id' => $customer_id, '#'=>'tab-notes']);
    }

    /**
     * Deletes an existing CustomerNote model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDeleteFile($id)
    {
        $model = ClientNoteAttachment::findOne($id);
        $customer_id = $model->clientNote->customer_id;
        $model->delete();

            return $this->redirect(['/customer/view', 'id' => $customer_id, '#'=>'tab-notes']);
    }

    /**
     * Deletes an existing CustomerNote model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionAddFile($id)
    {
        $model = new ClientNoteAttachment();
        $model->client_note_id = $id;
        $note = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            if ($model->clientNote->offer_id)
            {
                return $this->redirect(['/offer/default/view', 'id' => $model->clientNote->offer_id, '#'=>'tab-notes']);
            }else{
                return $this->redirect(['/customer/view', 'id' => $model->clientNote->customer_id, '#'=>'tab-notes']);
            }
            
        }
        else
        {
            return $this->render('batchCreate', [
                'model' => $model,
                'note'=> $note
            ]);
        }
    }

    public function batchCreate($data)
    {
        /* @var $file \yii\web\UploadedFile */
        $file = $data['file'];
        $model = new ClientNoteAttachment();
        $model->attributes = $data['params'];
        $model->mime_type = $file->type;
        $model->base_name = $file->baseName;
        $model->extension = $file->extension;
        $model->filename = $data['filename'];
        if ($model->save())
        {           
            return $model->attributes;
        }
        else
        {
            return $model->errors;
        };
    }
    
    /**
     * Finds the CustomerNote model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CustomerNote the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CustomerNote::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for ClientNoteAttachment
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddClientNoteAttachment()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('ClientNoteAttachment');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formClientNoteAttachment', ['row' => $row]);
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
