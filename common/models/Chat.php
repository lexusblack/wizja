<?php

namespace common\models;

use \common\models\base\Chat as BaseChat;
use common\helpers\ArrayHelper;
use Yii;
/**
 * This is the model class for table "chat".
 */
class Chat extends BaseChat
{
    /**
     * @inheritdoc
     */

public $userIds;

    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['last_message', 'create_time', 'update_time'], 'safe'],
            [['create_by', 'event_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['userIds'], 'each', 'rule' => ['integer']],
        ]);
    }

    public function addToChat($eventUser)
    {
        $chat = Chat::find()->where(['event_id'=>$eventUser->event_id])->one();
        if (!$chat)
        {
            return false;
        }else{
            $chatUser = ChatUser::find()->where(['chat_id'=>$chat->id])->andWhere(['user_id'=>$eventUser->user_id])->one();
            if (!$chatUser)
            {
                $chatUser = new ChatUser(['chat_id'=>$chat->id, 'user_id'=>$eventUser->user_id]);
                $chatUser->save();
                $m = new ChatMessage();
                $m->chat_id = $chat->id;
                $m->user_to = $eventUser->user_id;
                $m->text = Yii::t('app', 'Witaj na chacie!');
                $m->user_from = Yii::$app->user->id;
                $m->read = 0;
                $m->create_time = date("Y-m-d H:i:s");
                $m->save();
                return true;

            }else{
                return true;
            }
        }
    }

    public function removeFromChat($eventUser)
    {
         $chat = Chat::find()->where(['event_id'=>$eventUser->event_id])->one();
        if (!$chat)
        {
            return false;
        }else{
            $chatUser = ChatUser::find()->where(['chat_id'=>$chat->id])->andWhere(['user_id'=>$eventUser->user_id])->one();
            if (!$chatUser)
            {
                return true;
            }else{
                $chatUser->delete();
                return true;
            }
        }       
    }

    public function isHistory()
    {
        $chatUser = ChatUser::find()->where(['chat_id'=>$this->id])->andWhere(['user_id'=>Yii::$app->user->id])->one();
        if ($chatUser)
            return 0;
        else
            return 1;
    }

    public function getUserList($asString=false, $separator = ', ')
    {
        $list = ArrayHelper::map($this->users, 'id', 'name');

        if ($asString == true)
        {
            $value = implode($separator, $list);
        }
        else
        {
            $value = $list;
        }

        return $value;
    }
     public function getLastMessage($user_id=null)
    {
        if ($user_id)
            return \common\models\ChatMessage::find()->where(['and', 'chat_id='.$this->id, ['or', 'user_from='.$user_id, 'user_to='.$user_id]])->orderBy(['create_time'=>SORT_DESC])->one();
        else
            return null;

    }

    public function getChatList($user_id)
    {
        $chat_ids = \common\models\ChatMessage::find()->select('chat_id')->distinct()->where(['user_from'=>$user_id])->orWhere(['user_to'=>$user_id])->asArray()->all();
        $ids = array();
        foreach ($chat_ids as $id){
            array_push ($ids , $id['chat_id']);
        }
        $models = Chat::find()->where(['in', 'id', $ids])->orderBy(['last_message'=>SORT_DESC])->all();
        $list = [];
        foreach ($models as $model)
        {
            $obj =[];
            $obj['id']=$model->id;
            $obj['name']=$model->name;
            $m = $model->getLastMessage($user_id);
            $obj['time'] = $m->getTime();
            $obj['last_messages'] = $m->text;
            $obj['last_messages_from'] = $m->user_from;
            $obj['last_messages_read'] = $m->read;
            $obj['last_messages_from_name'] = $m->getUserName();
            $list[] = $obj;
        }
        return $list;
    }

    public function getChatApiMessages($user_id, $chat_id, $time)
    {
        if ($time==null){
            $time = '2010-01-01';
        }
        $notread = ChatMessage::find()->where(['user_to'=>$user_id])->andWhere(['<', 'read', 1])->andWhere(['chat_id'=>$chat_id])->andWhere(['>', 'create_time', $time])->all();
        $messages = ChatMessage::find()->where(['user_from'=>$user_id])->orWhere(['user_to'=>$user_id])->andWhere(['chat_id'=>$chat_id])->andWhere(['>', 'create_time', $time])->groupBy(['user_from', 'create_time'])->orderBy(['create_time'=>SORT_ASC])->all();
        $list = [];
        if( isset($_SERVER['HTTPS']) ) {
            $protocol = "https://";
        }else{
            $protocol = "http://";
        }
        foreach ($messages as $model)
        {
            $user_from = $model->getUserFrom();
            $obj =[];
            $obj['id']=$model->id;
            $obj['text']=$model->text;
            $obj['time'] = $model->create_time;
            $obj['user_from'] = $model->user_from;
            $obj['read'] = $model->read;
            $obj['user_from_name'] = $user_from->first_name." ".$user_from->last_name;
            $obj['user_photo'] = $protocol .  $_SERVER['SERVER_NAME'] . $user_from->getUserPhotoUrl();
            $list[] = $obj;
        }
        foreach ($notread as $nt)
        {
            $nt->read=1;
            $nt->save();
        }
        return $list;
    } 

    public function sendMessage($user_id, $chat_id, $text)
    {
        $model = Chat::find()->where(['id'=>$chat_id])->one();
        date_default_timezone_set(Yii::$app->params['timeZone']);
        $data = date("Y-m-d H:i:s");
        $model->last_message = $data;
        $model->save();
        foreach ($model->chatUsers as $user)
        {
            if ($user->user_id!=$user_id)
            {
                $m = new ChatMessage();
                $m->chat_id = $chat_id;
                $m->user_to = $user->user_id;
                $m->text = $text;
                $m->user_from = $user_id;
                $m->read = 0;
                $m->create_time = $data;
                $m->save();
            }
        }
        return true;
    }

    public function sendDeleteTaskNote($task)
    {
        $users = $task->getAllUsers();
        $nusers = $task->getAllNotificationUsers();
        $user_ids = [];
        if (isset($task->creator_id))
            $user_ids[$task->creator_id] = $task->creator;
        foreach ($users as $user)
        {
            $user_ids[$user->id] = $user;

        }
        foreach ($nusers as $user)
        {
            $user_ids[$user->id] = $user;
            
        }

        foreach ($user_ids as $user)
        {
            $chat = Chat::find()->where(['create_by'=>$user->id])->andWhere(['name'=>Yii::t('app', 'Powiadomienia NEW')])->one();
            if (!$chat)
            {
                $chat = new Chat();
                $chat->create_by = $user->id;
                $chat->name = Yii::t('app', 'Powiadomienia NEW');
                $chat->type = 1;
                $chat->last_message = date('Y-m-d H:i:s');
                $chat->save();
                $cu = new ChatUser();
                $cu->chat_id = $chat->id;
                $cu->user_id = $user->id;
                $cu->save();
            }
            if ($user->id!=Yii::$app->user->id)
            {
                $message = new ChatMessage();
                $message->user_from = $user->id;
                $message->user_to = $user->id;
                $message->chat_id = $chat->id;
                $message->read = 0;
                $message->create_time = date('Y-m-d H:i:s');
                $message->text = Yii::t('app', 'Usunięto zadanie ').$task->title;
                if ($task->event_id)
                    $message->text .= Yii::t('app', " w wydarzeniu ").$task->event->name;
                $message->save();
            }
        }        
    }

    public function sendChangeTask($task, $old_title)
    {
        $users = $task->getAllUsers();
        $nusers = $task->getAllNotificationUsers();
        $user_ids = [];
        if (isset($task->creator_id))
            $user_ids[$task->creator_id] = $task->creator;
        foreach ($users as $user)
        {
            $user_ids[$user->id] = $user;

        }
        foreach ($nusers as $user)
        {
            $user_ids[$user->id] = $user;
            
        }

        foreach ($user_ids as $user)
        {
            $chat = Chat::find()->where(['create_by'=>$user->id])->andWhere(['name'=>Yii::t('app', 'Powiadomienia NEW')])->one();
            if (!$chat)
            {
                $chat = new Chat();
                $chat->create_by = $user->id;
                $chat->name = Yii::t('app', 'Powiadomienia NEW');
                $chat->type = 1;
                $chat->last_message = date('Y-m-d H:i:s');
                $chat->save();
                $cu = new ChatUser();
                $cu->chat_id = $chat->id;
                $cu->user_id = $user->id;
                $cu->save();
            }
            if ($user->id!=Yii::$app->user->id)
            {
                $message = new ChatMessage();
                $message->user_from = $user->id;
                $message->user_to = $user->id;
                $message->chat_id = $chat->id;
                $message->read = 0;
                $message->create_time = date('Y-m-d H:i:s');
                $message->text = Yii::t('app', 'Zmieniono zadanie ').$old_title.Yii::t('app', ' na ').$task->title;
                if ($task->event_id)
                    $message->text .= Yii::t('app', " w wydarzeniu ").$task->event->name;
                $message->save();
            }
        } 
    }

    public function sendTaskNote($note)
    {
        $users = $note->task->getAllUsers();
        $nusers = $note->task->getAllNotificationUsers();
        $user_ids = [];
        if (isset($note->task->creator_id))
            $user_ids[$note->task->creator_id] = $note->task->creator;
        foreach ($users as $user)
        {
            $user_ids[$user->id] = $user;

        }
        foreach ($nusers as $user)
        {
            $user_ids[$user->id] = $user;
            
        }

        foreach ($user_ids as $user)
        {
            $chat = Chat::find()->where(['create_by'=>$user->id])->andWhere(['name'=>Yii::t('app', 'Powiadomienia NEW')])->one();
            if (!$chat)
            {
                $chat = new Chat();
                $chat->create_by = $user->id;
                $chat->name = Yii::t('app', 'Powiadomienia NEW');
                $chat->type = 1;
                $chat->last_message = date('Y-m-d H:i:s');
                $chat->save();
                $cu = new ChatUser();
                $cu->chat_id = $chat->id;
                $cu->user_id = $user->id;
                $cu->save();
            }
            if ($user->id!=$note->user_id)
            {
                $message = new ChatMessage();
                $message->user_from = $user->id;
                $message->user_to = $user->id;
                $message->chat_id = $chat->id;
                $message->read = 0;
                $message->create_time = date('Y-m-d H:i:s');
                $message->text = Yii::t('app', 'Komentarz do zadania ').$note->task->title.Yii::t('app', ' od')." ".$note->user->displayLabel.": ".$note->text;
                $message->text .= " <br/><a href='/admin/task/index?id=".$note->task->id."'>".Yii::t('app', 'Zobacz zadanie')."</a>";
                $message->save();
            }
        }
    }
	
}

