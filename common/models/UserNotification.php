<?php

namespace common\models;

use Yii;
use \common\models\base\UserNotification as BaseUserNotification;
use yii\helpers\ArrayHelper;
use common\helpers\StringHelper;
/**
 * This is the model class for table "user_notification".
 */
class UserNotification extends BaseUserNotification
{

public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                # custom behaviors
            ]
        );
    }

    public function rules()
    {
        return ArrayHelper::merge(
             parent::rules(),
             [
                  # custom validation rules
             ]
        );
    }

    /**
     * @param int $limit
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getListForUser($limit=10)
    {
        //można zrobić, od ostatniego logowania
        $query = static::find()
            ->limit($limit)
            ->where([
                'user_id'=>Yii::$app->user->id,
            ])
            ->orderBy(['create_time'=>SORT_DESC]);
        $models = $query->all();
        return $models;
    }

    public function getTargetObject()
    {
        $className = $this->target_class;
        $obj = $className::findOne($this->target_id);
        return $obj;
    }

    public function getParsedContent()
    {
        $content = '';
        if ($this->data == null)
        {
            $content = StringHelper::parseText($this->content, $this->getTargetObject());
        }
        else
        {
            $data = unserialize($this->data);
            $content = StringHelper::parseText($this->content, $data);
        }
        return $content;
    }

    public function afterSave( $insert, $changedAttributes ) {
	    parent::afterSave( $insert, $changedAttributes );
	    if ($insert) {
	    	Notification::sendUserPushNotification($this->user, 'Dodano powiadomienie', $this->title, Notification::PUSH_TYPE_NOTIFICATION);
	    }
    }

}
