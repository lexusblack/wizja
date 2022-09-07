<?php

namespace backend\controllers;

use Yii;
use common\models\Chat;
use common\models\User;
use common\models\ChatMessage;
use common\models\ChatUser;
use common\models\ChatSearch;
use common\models\Event;
use backend\components\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;
/**
 * ChatController implements the CRUD actions for Chat model.
 */
class ChatController extends Controller
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
                        'actions' => ['create', 'update', 'delete', 'add-chat-message', 'add-chat-user', 'load', 'loaddialog', 'send', 'sendcrn', 'ajaxload', 'ajaxloadcrn', 'createevent', 'all', 'loadchat', 'ajaxchatload', 'createuser', 'loadcrndialog', 'createcrn'],
                        'roles' => ['@']
                    ],
                    [
                        'allow' => false
                    ]
                ]
            ]
        ];
    }
    public function beforeAction($action) {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }


    public function actionAll()
    {
        $chat_ids = ChatMessage::find()->select('chat_id')->distinct()->where(['user_from'=>Yii::$app->user->identity->id])->orWhere(['user_to'=>Yii::$app->user->identity->id])->asArray()->all();
        $ids = array();
        foreach ($chat_ids as $id){
            array_push ($ids , $id['chat_id']);
        }
        $model = Chat::find()->where(['in', 'id', $ids])->orderBy(['last_message'=>SORT_DESC])->all();
        return $this->render('all', [
                'model' => $model
            ]);        
    }

    public function actionCreatecrn($id)
    {
        $rental = \common\models\CrossRental::findOne($id);
        $model = new \common\models\CrnChat();
        date_default_timezone_set(Yii::$app->params['timeZone']);
        $date = date("Y-m-d H:i:s");
        $model->last_message = $date;
        $model->create_by = Yii::$app->user->id;
        $model->company_asking = Yii::$app->params['companyID'];
        $model->company_recieving = $rental->owner;
        $model->name = Yii::t('app', '[Zapytanie CRN]')." ".$rental->gearModel->name;
        $model->save();
        $m = new \common\models\CrnChatMessage();
        $m->crn_chat_id = $model->id;
        $m->user = Yii::$app->user->identity->displayLabel;
        $m->user_id = Yii::$app->user->id;
        $m->company = Yii::$app->params['companyID'];
        $m->read = 0;
        $m->text = Yii::t('app', 'Utworzono rozmowę');
        $m->datetime = $date;
        $m->save();
        Yii::$app->response->format = Response::FORMAT_JSON;
        return ['id'=>$model->id];
        exit;


    }
    /**
     * Creates a new Chat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $type = 1;
        if (Yii::$app->request->post())
        {
            $chat = Yii::$app->request->post('Chat');
            if (count($chat['userIds'])<2)
            {
                //wiadomość tylko do jednej osoby, sprawdzamy czy nie została już utworzona
                if (count($chat['userIds'])<1)
                {
                    return $this->redirect(['site/index']);
                }else{
                    $user_id = $chat['userIds'][0];
                    $chat_ids = ChatUser::find()->select('chat_id')->distinct()->where(['user_id'=>Yii::$app->user->identity->id])->asArray()->all();
                    $ids1 = array();
                    foreach ($chat_ids as $id){
                        array_push ($ids1 , $id['chat_id']);
                    }
                    $chat_ids = ChatUser::find()->select('chat_id')->distinct()->where(['user_id'=>$user_id])->asArray()->all();
                    $ids2 = array();
                    foreach ($chat_ids as $id){
                        array_push ($ids2 , $id['chat_id']);
                    }
                     $model = Chat::find()->where(['in', 'id', $ids1])->andWhere(['in', 'id', $ids2])->andWhere(['type'=>2])->one();
                     if ($model)
                     {
                        return $this->redirect(['site/index', 'dialog'=>$model->id]);
                     }else{
                        $type = 2;
                     }
                }
            }
        }
        $model = new Chat();
        $model->type = $type;
        $model->last_message = date("Y-m-d H:i:s");
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $model->linkObjects();
            $cu = new ChatUser;
            $cu->user_id = Yii::$app->user->identity->id;
            $cu->chat_id = $model->id;
            $cu->save();
            foreach ($model->users as $user){
                if (Yii::$app->user->identity->id!=$user->id)
                {
                    $cm = new ChatMessage;
                    $cm->chat_id = $model->id;
                    $cm->user_from = Yii::$app->user->identity->id;
                    $cm->user_to = $user->id;
                    $cm->text = Yii::t('app', "Utworzono nową rozmowę");
                    $cm->read = 0;
                    $cm->save();                   
                }

            }
            return $this->redirect(['site/index', 'dialog'=>$model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }


    public function actionCreateevent($id)
    {
        $chat = Chat::find()->where(['event_id'=>$id])->one();
        if ($chat)
        {
            echo $chat->id;
            exit;
        }
        $event = Event::find()->where(['id'=>$id])->one();
        $model = new Chat;
        $model->name = $event->name;
        $model->event_id = $id;
        $data = date("Y-m-d H:i:s");
        $model->last_message = $data;
        $model->type = 1;
        $model->save();
        foreach ($event->getAssignedUsers()->getModels() as $user)
        {
            $cu = new ChatUser;
            $cu->user_id = $user->id;
            $cu->chat_id = $model->id;
            $cu->save();
                if (Yii::$app->user->identity->id!=$user->id)
                {
                    $cm = new ChatMessage;
                    $cm->chat_id = $model->id;
                    $cm->user_from = Yii::$app->user->identity->id;
                    $cm->user_to = $user->id;
                    $cm->text = Yii::t('app', "Utworzono nową rozmowę");
                    $cm->read = 0;
                    $cm->create_time = $data;
                    $cm->save();    

                }         
        }
        if (isset($event->manager))
        {
            $user = $event->manager;
            $cu = new ChatUser;
            $cu->user_id = $user->id;
            $cu->chat_id = $model->id;
            $cu->save();
                if (Yii::$app->user->identity->id!=$user->id)
                {
                    $cm = new ChatMessage;
                    $cm->chat_id = $model->id;
                    $cm->user_from = Yii::$app->user->identity->id;
                    $cm->user_to = $user->id;
                    $cm->text = "Utworzono nową rozmowę";
                    $cm->read = 0;
                    $cm->create_time = $data;
                    $cm->save();    

                }            
        }
        echo $model->id;
        exit;
    }

    public function actionCreateuser($id)
    {
        $user = User::findOne($id);
        $ids1 = ChatUser::find()->where(['user_id'=>$id])->all();
        $ids1 = array_column($ids1 , 'chat_id');
        $ids2 = ChatUser::find()->where(['user_id'=>Yii::$app->user->identity->id])->all();
        $ids2 = array_column($ids2 , 'chat_id');
        $ids = array_intersect($ids1, $ids2);
        $chat = Chat::find()->where(['type'=>2])->andWhere(['IN', 'id', $ids])->one();
        if ($chat)
        {
            echo $chat->id;
            exit;
        }
        $model = new Chat;
        $model->name = Yii::$app->user->identity->first_name."/".$user->first_name;
        $data = date("Y-m-d H:i:s");
        $model->last_message = $data;
        $model->type = 2;
        $model->save();
            $cu = new ChatUser;
            $cu->user_id = $id;
            $cu->chat_id = $model->id;
            $cu->save();
            $cu = new ChatUser;
            $cu->user_id = Yii::$app->user->identity->id;
            $cu->chat_id = $model->id;
            $cu->save();
                    $cm = new ChatMessage;
                    $cm->chat_id = $model->id;
                    $cm->user_from = Yii::$app->user->identity->id;
                    $cm->user_to = $id;
                    $cm->text = Yii::t('app', "Utworzono nową rozmowę");
                    $cm->read = 0;
                    $cm->create_time = $data;
                    $cm->save();           
        echo $model->id;
        exit;
    }

    public function actionSend($id)
    {
        $model = $this->findModel($id);
        date_default_timezone_set(Yii::$app->params['timeZone']);
        $data = date("Y-m-d H:i:s");
        $model->last_message = $data;
        $model->save();
        foreach ($model->chatUsers as $user)
        {
            if ($user->user_id!=Yii::$app->user->identity->id)
            {
                $m = new ChatMessage();
                $m->chat_id = $id;
                $m->user_to = $user->user_id;
                $m->text = Yii::$app->request->post("text");
                $m->user_from = Yii::$app->user->identity->id;
                $m->read = 0;
                $m->create_time = $data;
                $m->save();
            }
        }
        exit;
    }

    public function actionSendcrn($id)
    {
        $model = \common\models\CrnChat::findOne($id);
        date_default_timezone_set(Yii::$app->params['timeZone']);
        $data = date("Y-m-d H:i:s");
        $model->last_message = $data;
        $model->save();
                $m = new \common\models\CrnChatMessage();
                $m->crn_chat_id = $id;
                $m->user_id = Yii::$app->user->identity->id;
                $m->text = Yii::$app->request->post("text");
                $m->company = Yii::$app->params['companyID'];
                $m->user = Yii::$app->user->identity->displayLabel;
                $m->read = 0;
                $m->datetime = $data;
                $m->save();
        exit;
    }
    /**
     * Updates an existing Chat model.
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
            return $this->redirect(['all']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }
    public function actionLoad()
    {
        $chat_ids = ChatMessage::find()->select('chat_id')->distinct()->where(['user_from'=>Yii::$app->user->identity->id])->orWhere(['user_to'=>Yii::$app->user->identity->id])->asArray()->all();
        $ids = array();
        foreach ($chat_ids as $id){
            array_push ($ids , $id['chat_id']);
        }
        $model = Chat::find()->where(['in', 'id', $ids])->orderBy(['last_message'=>SORT_DESC])->all();
        $notread = ChatMessage::find()->where(['user_to'=>Yii::$app->user->identity->id])->andWhere(['<', 'read', 1])->groupBy(['chat_id'])->count();
        $settings = \common\models\Settings::find()->indexBy('key')->where(['section'=>'main'])->all();
        if (isset($settings['crossRentalUsers']))
        {
            $cr_users = explode(";", $settings['crossRentalUsers']->value);
        }else{
            $cr_users = [];
        }
        if (in_array(Yii::$app->user->id, $cr_users))
        {
            //użytkownik jest na liście użytkowników do kontaktu z CRN
            $crn_chats_recieving = \common\models\CrnChat::find()->where(['company_recieving'=>Yii::$app->params['companyID']])->orderBy(['last_message'=>SORT_DESC])->all();
            $ids = array();
            foreach ($crn_chats_recieving  as $id){
                array_push ($ids , $id->id);
            }
            $notread +=\common\models\CrnChatMessage::find()->where(['crn_chat_id'=>$ids])->andWhere(['read'=>0])->andWhere(['<>', 'company', Yii::$app->params['companyID']])->groupBy(['crn_chat_id'])->count();
        }else{
            $crn_chats_recieving = [];
        }
        $crn_chats_asking = \common\models\CrnChat::find()->where(['company_asking'=>Yii::$app->params['companyID']])->andWhere(['create_by'=>Yii::$app->user->id])->orderBy(['last_message'=>SORT_DESC])->all();
        $ids = array();
            foreach ($crn_chats_asking as $id){
                array_push ($ids , $id->id);
            }
            $notread +=\common\models\CrnChatMessage::find()->where(['crn_chat_id'=>$ids])->andWhere(['read'=>0])->andWhere(['<>', 'company', Yii::$app->params['companyID']])->groupBy(['crn_chat_id'])->count();
        $this->layout = false;
        return $this->render('load', [
                'model' => $model,
                'notread' => $notread,
                'crn_chats_recieving' => $crn_chats_recieving,
                'crn_chats_asking' => $crn_chats_asking
            ]);        
    }

    public function actionLoadchat($id)
    {
        $model = $this->findModel($id);
        if ($model->type==2)
        {
            foreach ($model->users as $user)
            {
                if ($user->id!=Yii::$app->user->identity->id)
                {
                    $model->name = $user->name;
                }
            }
        }
        $notread = ChatMessage::find()->where(['user_to'=>Yii::$app->user->identity->id])->andWhere(['<', 'read', 1])->andWhere(['chat_id'=>$id])->all();
        foreach ($notread as $nt)
        {
            $nt->read=1;
            $nt->save();
        }   
        $messages = ChatMessage::find()->where(['user_from'=>Yii::$app->user->identity->id])->orWhere(['user_to'=>Yii::$app->user->identity->id])->andWhere(['chat_id'=>$id])->groupBy(['user_from', 'create_time'])->orderBy(['create_time'=>SORT_ASC])->all();     
        $this->layout = false;
        return $this->render('loadchat', [
                'model' => $model,
                'messages'=>$messages
            ]); 
    }

    public function actionLoaddialog($id)
    {

        $model = $this->findModel($id);
        if ($model->type==2)
        {
            foreach ($model->users as $user)
            {
                if ($user->id!=Yii::$app->user->identity->id)
                {
                    $model->name = $user->name;
                }
            }
        }
        $notread = ChatMessage::find()->where(['user_to'=>Yii::$app->user->identity->id])->andWhere(['<', 'read', 1])->andWhere(['chat_id'=>$id])->all();
        foreach ($notread as $nt)
        {
            $nt->read=1;
            $nt->save();
        }   
        $messages = ChatMessage::find()->where(['user_from'=>Yii::$app->user->identity->id])->orWhere(['user_to'=>Yii::$app->user->identity->id])->andWhere(['chat_id'=>$id])->groupBy(['user_from', 'create_time'])->orderBy(['create_time'=>SORT_ASC])->all();     
        $this->layout = false;
        return $this->render('load_dialog', [
                'model' => $model,
                'messages'=>$messages
            ]);        
    }

    public function actionLoadcrndialog($id)
    {

        $model = \common\models\CrnChat::findOne($id);
        $notread = \common\models\CrnChatMessage::find()->where(['<>', 'company', Yii::$app->params['companyID']])->andWhere(['crn_chat_id'=>$id])->all();
        foreach ($notread as $nt)
        {
            $nt->read=1;
            $nt->save();
        }   
        $messages = \common\models\CrnChatMessage::find()->where(['crn_chat_id'=>$id])->orderBy(['datetime'=>SORT_ASC])->all();     
        $this->layout = false;
        return $this->render('load_dialog_crn', [
                'model' => $model,
                'messages'=>$messages
            ]);        
    }

    public function actionAjaxload($id)
    {

        $model = $this->findModel($id);
        $notread = ChatMessage::find()->where(['user_to'=>Yii::$app->user->identity->id])->andWhere(['<', 'read', 1])->andWhere(['chat_id'=>$id])->all();
        if (!$notread)
            exit;
        foreach ($notread as $nt)
        {
            $nt->read=1;
            $nt->save();
        }       
        $this->layout = false;

        return $this->render('ajax_load', [
                'notread' => $notread,
                'model'=>$model
            ]);        
    }

    public function actionAjaxloadcrn($id)
    {

        $model = \common\models\CrnChat::findOne($id);
        $notread = \common\models\CrnChatMessage::find()->where(['<>', 'company', Yii::$app->params['companyID']])->andWhere(['crn_chat_id'=>$id])->andWhere(['<', 'read', 1])->all();
        if (!$notread)
            exit;
        foreach ($notread as $nt)
        {
            $nt->read=1;
            $nt->save();
        }       
        $this->layout = false;

        return $this->render('ajax_load_crn', [
                'notread' => $notread,
                'model'=>$model
            ]);        
    }

    public function actionAjaxchatload($id)
    {

        $model = $this->findModel($id);
        $notread = ChatMessage::find()->where(['user_to'=>Yii::$app->user->identity->id])->andWhere(['<', 'read', 1])->andWhere(['chat_id'=>$id])->all();
        if (!$notread)
            exit;
        foreach ($notread as $nt)
        {
            $nt->read=1;
            $nt->save();
        }       
        $this->layout = false;

        return $this->render('ajax_chat_load', [
                'notread' => $notread
            ]);        
    }
    /**
     * Deletes an existing Chat model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->deleteWithRelated();

        return $this->redirect(['index']);
    }

    
    /**
     * Finds the Chat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Chat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Chat::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for ChatMessage
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddChatMessage()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('ChatMessage');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formChatMessage', ['row' => $row]);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
    
    /**
    * Action to load a tabular form grid
    * for ChatUser
    * @author Yohanes Candrajaya <moo.tensai@gmail.com>
    * @author Jiwantoro Ndaru <jiwanndaru@gmail.com>
    *
    * @return mixed
    */
    public function actionAddChatUser()
    {
        if (Yii::$app->request->isAjax) {
            $row = Yii::$app->request->post('ChatUser');
            if((Yii::$app->request->post('isNewRecord') && Yii::$app->request->post('_action') == 'load' && empty($row)) || Yii::$app->request->post('_action') == 'add')
                $row[] = [];
            return $this->renderAjax('_formChatUser', ['row' => $row]);
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'Strona nie istnieje.'));
        }
    }
}
