<?php


namespace backend\modules\api\controllers;

use Yii;
use common\models\Chat;
use common\models\ChatUser;
use common\models\ChatMessage;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UnprocessableEntityHttpException;

class ChatController extends BaseController {

    public $modelClass = '\common\models\Chat';


    public function actionList()
    {
        return Chat::getChatList(Yii::$app->user->id);
    }

    public function actionMessages($id=null, $time = null)
    {
        if ($id == null) {
            throw new BadRequestHttpException(Yii::t('app', "Brak wymaganych parametrów: id"));
        }else{
            return Chat::getChatApiMessages(Yii::$app->user->id,$id, $time);
        }
    }
    public function actionSend($id=null)
    {
        if ($id == null) {
            throw new BadRequestHttpException(Yii::t('app', "Brak wymaganych parametrów: id"));
        }else{
            if (Yii::$app->request->post("text")!="")
            {
                return Chat::sendMessage(Yii::$app->user->id,$id, Yii::$app->request->post("text"));
            }else{
                return false;
            }
        }
    }

    public function actionCreateForEvent()
    {
        if (Yii::$app->request->isPost) {
            date_default_timezone_set(Yii::$app->params['timeZone']);
            $event_id = Yii::$app->request->post('event_id');
            $type = 1;
            $model = Chat::find()->where(['event_id'=>$event_id])->one();
            if ($model)
                return ['chat_id'=>$model->id, 
                                'name' => $model->name,
                                'message' => '',
                                'code' => 0,
                                'status' => 200];
            $event = \common\models\Event::findOne($event_id);
            $model = new Chat();
            $model->type = $type;
            $model->last_message = date("Y-m-d H:i:s");
            $model->name = $event->name;
            $model->event_id = $event_id;
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
                        $cm->create_time = date("Y-m-d H:i:s");
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
                        $cm->create_time = date("Y-m-d H:i:s");
                        $cm->save();    

                    }            
            }
            return ['chat_id'=>$model->id, 
                                'name' => $model->name,
                                'message' => '',
                                'code' => 0,
                                'status' => 200];

        }else{
             throw new NotFoundHttpException(Yii::t('app', 'Błąd'));
        }
        throw new MethodNotAllowedHttpException();        
    }

    public function actionCreateChat()
    {
        if (Yii::$app->request->isPost) {
            date_default_timezone_set(Yii::$app->params['timeZone']);
            $name = Yii::$app->request->post('name');
            $user_ids = json_decode(Yii::$app->request->post('users'));
            $user_ids[] = Yii::$app->user->id;
            $type = 1;
            if (count($user_ids)<3)
            {
                //wiadomość tylko do jednej osoby, sprawdzamy czy nie została już utworzona
                if (count($user_ids)<2)
                {
                    throw new NotFoundHttpException(Yii::t('app', 'Zbyt małą liczba uczestników chatu'));
                }else{
                    $chat_ids = ChatUser::find()->select('chat_id')->distinct()->where(['user_id'=>$user_ids[0]])->asArray()->all();
                    $ids1 = array();
                    foreach ($chat_ids as $id){
                        array_push ($ids1 , $id['chat_id']);
                    }
                    $chat_ids = ChatUser::find()->select('chat_id')->distinct()->where(['user_id'=>$user_ids[1]])->asArray()->all();
                    $ids2 = array();
                    foreach ($chat_ids as $id){
                        array_push ($ids2 , $id['chat_id']);
                    }
                     $model = Chat::find()->where(['in', 'id', $ids2])->andWhere(['in', 'id', $ids2])->andWhere(['type'=>2])->one();
                     if ($model)
                     {
                        return ['chat_id'=>$model->id, 
                                'name' => $model->name,
                                'message' => '',
                                'code' => 0,
                                'status' => 200];
                     }else{
                        $type = 2;
                     }
            
                }
            }
            $model = new Chat();
            $model->type = $type;
            $model->last_message = date("Y-m-d H:i:s");
            $model->name = $name;
            $model->save();
            foreach ($user_ids as $user_id)
            {
                $cu = new ChatUser;
                $cu->user_id = $user_id;
                $cu->chat_id = $model->id;
                $cu->save();
            }
            
            foreach ($model->users as $user){
                if (Yii::$app->user->identity->id!=$user->id)
                {
                    $cm = new ChatMessage;
                    $cm->chat_id = $model->id;
                    $cm->user_from = Yii::$app->user->identity->id;
                    $cm->user_to = $user->id;
                    $cm->text = Yii::t('app', "Utworzono nową rozmowę");
                    $cm->create_time = date("Y-m-d H:i:s");
                    $cm->read = 0;
                    $cm->save();                   
                }

            }
                return ['chat_id'=>$model->id, 
                                'name' => $model->name,
                                'message' => '',
                                'code' => 0,
                                'status' => 200];

        }else{
             throw new NotFoundHttpException(Yii::t('app', 'Błąd'));
        }
        throw new MethodNotAllowedHttpException(); 
    }
}


/**
 *
 * Dzisiejsze wydarzenia:
 * get: /admin/api/dashboard/events?type=today // return array/error
 *
 * Najbliższe wydarzenia:
 * get: /admin/api/dashboard/events?type=upcoming // return array/error
 *
 * Wydarzenia działu:
 * get: /admin/api/dashboard/events?type=department // return array/error
 *
 * Powiadomienia:
 * get: /admin/api/dashboard/notifications // return array/error
 *
 * Zadania:
 * get: /admin/api/dashboard/tasks // return array/error
 * put: /admin/api/dashboard/tasks/{id}/status // && $_POST['status'] - zmienia status na: status = 0 => nowy, status = 10 => zrobiony // return status 200 / error
 * post: /admin/api/dashboard/tasks/{id}/comment //  && $_POST['comment'] - dodaje komentarz // return status 200 / error
 *
 * Checklista:
 * get: /admin/api/dashboard/checklist return array/error
 * put: /admin/api/dashboard/checklist/{id}/status // zmienia status // return status 200 / error
 *
 */