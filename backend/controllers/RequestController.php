<?php

namespace backend\controllers;

use common\components\filters\AccessControl;
use Yii;
use common\models\Request;
use common\models\Event;
use common\models\RequestSearch;
use common\models\RequestNote;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\ArrayHelper;

/**
 * AddonRateController implements the CRUD actions for AddonRate model.
 */
class RequestController extends Controller
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
                        'actions' => ['index', 'show-notes', 'add-note', 'delete-note', 'status', 'create-from-request', 'add-to-event'],
                        'roles' => ['@']
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

    public function actionIndex()
    {
        $searchModel = new RequestSearch();
        if (Yii::$app->params['companyID']=="admin")
        {
            $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        }else{
            $dataProvider = $searchModel->searchByCompany(Yii::$app->request->queryParams);
        }
        

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionShowNotes($id)
    {
        //$model = Request::findOne($id);
        if (Yii::$app->params['companyID']=="admin")
        {
            $model = Request::findOne($id);
        }else{
             $model = Request::find()->where(['company_id'=>Yii::$app->params['companyID'], 'id'=>$id])->one();
        }
       
        if (!$model)
            exit;
        $model->addRead();
        return $this->renderAjax('show-notes', [
            'model'=>$model
        ]);
    }

    public function actionAddToEvent($id)
    {
        $model = new \common\models\EventRequest();
        $model->request_id = $id;
        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            $this->redirect(['/event/view', 'id'=>$model->event_id]);
        }else{
            $ids = ArrayHelper::map(\common\models\EventRequest::find()->where(['request_id'=>$id])->asArray()->all(), 'event_id', 'event_id');
            $events = ArrayHelper::map(Event::find()->where(['NOT IN', 'id', $ids])->orderBy(['id'=>SORT_DESC])->asArray()->all(), 'id', 'name');
            return $this->renderAjax('add-to-event', [
            'model'=>$model, 'events'=>$events
        ]);
        }
    }

    public function actionStatus($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $model = Request::findOne($id);
        $post = Yii::$app->request->post();
        $status = $post['Request'][$post['editableIndex']]['status'];
        $model->status = $status;
        $model->save();
        $list = \common\models\Request::getStatusList();
        //$model->addLog(Yii::t('app', 'Zmieniono status wydarzenia na:').$list[$model->status]);
        
        $output = ['output'=>$list[$model->status], 'message'=>''];
        return $output;
        exit;
    }

    public function actionCreateFromRequest($id)
    {
        $model = Request::findOne($id);
        $event = new \common\models\Event();
        $event->name = $model->name;
        $event->customer_id = 1;
        $event->type = 2;
        if ($event->save())
        {
            $er = new \common\models\EventRequest(['request_id'=>$id, 'event_id'=>$event->id]);
            $er->save();
            $this->redirect(['/event/update', 'id'=>$event->id]);
        }

    }

    public function actionAddNote($id)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $note = new RequestNote();
        $note->request_id = $id;
        $note->datetime = date('Y-m-d H:i:s');
        if (Yii::$app->params['companyID']=="admin")
        {
            $note->type = 2;
            //$note->user_id = Yii::$app->user->id;
            $note->user_name = 'NEWSYSTEMS SUPPORT';
        }else{
            $note->type = 1;
            $note->user_id = Yii::$app->user->id;
            $note->user_name = Yii::$app->user->identity->displayLabel;
            //wysyłamy maila na support
            $request = Request::findOne($id);
            $model = new \backend\models\SendMail();
            $model->type = $request->type;
            $model->priority = 1;
            $model->text = Yii::$app->request->post('RequestNote')['text'];
            $model->link = "https://admin.newsystems.pl/admin/request/index?RequestSearch%5Bid%5D=".$request->id;
            $mail = \Yii::$app->mailer->compose('@app/views/site/mail', [
                'model' =>  $model            ])
            ->setFrom([Yii::$app->params['mailingEmail']=>$request->mail])
            ->setTo([Yii::$app->params['errorEmail']])
            ->setSubject(Yii::t('app', 'Nowy komentarz do zgłoszenia nr ').$request->id);            
            if ($mail->send())
            {
            }
        }

        if ($note->load(Yii::$app->request->post()) && $note->save())
        {
            $note->request->update_time = date('Y-m-d H:i:s');
            $note->request->save();
            $note->request->removeRead();
            return ['success'=>true];
        } else{
            return ['success'=>false];
        }
    }

    public function actionDeleteNote($id)
    {
        $note = RequestNote::findOne($id);
        $note->delete();
        return $this->redirect(['index']);
    }


}
