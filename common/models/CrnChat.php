<?php

namespace common\models;

use Yii;
use \common\models\base\CrnChat as BaseCrnChat;

/**
 * This is the model class for table "crn_chat".
 */
class CrnChat extends BaseCrnChat
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_replace_recursive(parent::rules(),
	    [
            [['cross_rental_id', 'create_by'], 'integer'],
            [['last_message'], 'safe'],
            [['company_asking', 'company_recieving'], 'string', 'max' => 45],
            [['name'], 'string', 'max' => 255]
        ]);
    }
	
    public function getLastMessage()
    {
            return \common\models\CrnChatMessage::find()->where(['crn_chat_id'=>$this->id])->orderBy(['datetime'=>SORT_DESC])->one();

    }
    public function getLastMessageMine()
    {
            return \common\models\CrnChatMessage::find()->where(['crn_chat_id'=>$this->id])->andWhere(['<>', 'company', Yii::$app->params['companyID']])->orderBy(['datetime'=>SORT_DESC])->one();

    }    
}
