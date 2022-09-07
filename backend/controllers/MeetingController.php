<?php

namespace backend\controllers;

use backend\modules\permission\models\BasePermission;
use Yii;
use common\models\Meeting;
use backend\models\Ics;
use common\models\MeetingSearch;
use backend\components\Controller;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\helpers\ArrayHelper;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;

/**
 * MeetingController implements the CRUD actions for Meeting model.
 */
class MeetingController extends Controller
{

    public $enableCsrfValidation = false;
    public function behaviors() {
        $behaviors = parent::behaviors();
        $behaviors['access'] = [
            'class'=>AccessControl::className(),
            'rules' => [
                [
                        'actions' => ['ics'],
                        'allow' => true,
                ],
                [
                    'allow'=>true,
                    'actions' => ['index', 'send-mail'],
                    'roles' => ['eventsMeetings'],
                ],
                [
                    'allow' => true,
                    'actions' => ['create'],
                    'roles' => ['eventMeetingAdd'],

                ],
                [
                    'allow' => true,
                    'actions' => ['view'],
                    'roles' => ['@'],
                    'matchCallback' => function() {
                        if (Yii::$app->user->can('eventMeetingView'.BasePermission::SUFFIX[BasePermission::ALL])) {
                            return true;
                        }
                        return $this->isMeetingUser('eventMeetingView');
                    }

                ],
                [
                    'allow' => true,
                    'actions' => ['delete'],
                    'roles' => ['@'],
                    'matchCallback' => function() {
                        if (Yii::$app->user->can('eventMeetingDelete'.BasePermission::SUFFIX[BasePermission::ALL])) {
                            return true;
                        }
                        return $this->isMeetingUser('eventMeetingDelete');
                    }
                ],
                [
                    'allow' => true,
                    'actions' => ['update'],
                    'roles' => ['@'],
                    'matchCallback' => function() {
                        if (Yii::$app->user->can('eventMeetingEdit'.BasePermission::SUFFIX[BasePermission::ALL])) {
                            return true;
                        }
                        return $this->isMeetingUser('eventMeetingEdit');
                    }
                ],
            ]
        ];

        return $behaviors;
    }

    private function isMeetingUser($text) {
        $meeting = $this->findModel(Yii::$app->request->get('id'));
        if (Yii::$app->user->can($text.BasePermission::SUFFIX[BasePermission::MINE])) {
            if ($text == "eventMeetingView"){
            foreach ($meeting->users as $user) {
                if ($user->id == Yii::$app->user->id) {
                    return true;
                }
            }
            }
            if ($meeting->created_by == Yii::$app->user->id)
                return true;
        }
        return false;
    }

    public function actionIcs($id)
    {
        $model = $this->findModel($id);
        $ics = new Ics();
        $ics->ICS($model->start_time,$model->end_time,$model->name,$model->description,$model->location);

        $ics->show();
        exit;
    }

    /**
     * Lists all Meeting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new MeetingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $user = Yii::$app->user;
        if (!$user->can('SiteAdministrator') && $user->can('eventsMeetings'.BasePermission::SUFFIX[BasePermission::MINE])) {
            $query = Meeting::find()->joinWith('users')->where(['user.id' => Yii::$app->user->id]);
            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);
        }
        $dataProvider->query->andWhere(['meeting.active'=>1]);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Meeting model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id)
        ]);
    }

    /**
     * Creates a new Meeting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($start=null)
    {
        $model = new Meeting();
        if ($start == null)
        {
            $start = date('Y-m-d');
        }
        $model->start_time = date($start.' 00:00');
        $model->end_time = date($start.' 23:59');
        $model->userIds = [Yii::$app->user->id];
        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $model->start_time = substr($model->dateRange, 0, 16);
            $model->end_time = substr($model->dateRange, 20, 16);
            if ($model->save()) {
                $model->linkObjects();
                \common\models\Note::createNote(4, 'meetingAdded', $model, $model->customer_id);
                return $this->redirect(['send-mail', 'id' => $model->id]);
            }
        }
        $model->prepareDateAttributes();
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Meeting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->loadLinkedObjects();


        if ($model->load(Yii::$app->request->post())) {
            foreach ($model->notificationSmses as $sms) {
                $sms->delete();
            }
            foreach ($model->notificationMails as $mail) {
                $mail->delete();
            }
            $model->linkObjects();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Zapisano!'));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }
        $model->prepareDateAttributes();
        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Meeting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->loadLinkedObjects();
        foreach ($model->notificationSmses as $sms) {
            if (new \DateTime($sms->sending_time) > new \DateTime()) {
                $sms->delete();
            }
        }
        foreach ($model->notificationMails as $mail) {
            $mail->delete();
        }
        $model->active = 0;
        $model->linkObjects();
        $model->prepareDateAttributes();
        $model->save();
        \common\models\Note::createNote(4, 'meetingDeleted', $model, $model->customer_id);
        return $this->redirect(['index']);
    }

    /**
     * Finds the Meeting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Meeting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Meeting::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }

    public function actionDeleteSms($id) {
        if (Yii::$app->request->isPost) {
            foreach ($this->findModel($id)->notificationSmses as $sms) {
                $sms->delete();
            }
        }
        else {
            throw new MethodNotAllowedHttpException();
        }
    }

    public function actionDeleteMail($id) {
        if (Yii::$app->request->isPost) {
            $model = $this->findModel($id);
            foreach ($model->notificationMails as $mail) {
                $mail->delete();
            }
            $model->remind_email = 0;
            $model->save();
        }
        else {
            throw new MethodNotAllowedHttpException();
        }
    }

    public function actionSendMail($id)
    {
        $model = new \backend\models\SendMeetingMail();
        $meeting = $this->findModel($id);
        $model->meeting = $meeting;
        
        if ($model->load(Yii::$app->request->post()) && $model->validate()){


            $mail = \Yii::$app->mailer->compose('@app/modules/offers/views/default/mail', [
                'model' =>  $model,
            ])
            ->setFrom([Yii::$app->params['mailingEmail']=>Yii::$app->user->identity->email])
            ->setTo($model->recipients)
            ->setSubject($model->subject)
            ->setReplyTo(Yii::$app->user->identity->email);  
            $ics = new Ics();
            $ics->ICS($meeting->start_time,$meeting->end_time,$meeting->name,$meeting->description,$meeting->location);
            $ics->save(Yii::getAlias('@uploadroot/'.$meeting->name.'.ics'));
            $mail->attach(Yii::getAlias('@uploadroot/'.$meeting->name.'.ics'));          
            if ($mail->send())
            {
                Yii::$app->session->setFlash('success',  Yii::t('app', 'Email wysłany!'));
            } else {
                Yii::$app->session->setFlash('danger',  Yii::t('app', 'Błąd!'));
            }

            return $this->redirect(['view', 'id'=>$id]);
        } 
        return $this->render('send-mail', [
            'model' => $model,
        ]);
    }
}
