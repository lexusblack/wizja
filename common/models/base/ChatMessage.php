<?php

namespace common\models\base;

use Yii;
use yii\behaviors\TimestampBehavior;

/**
 * This is the base model class for table "chat_message".
 *
 * @property integer $id
 * @property integer $user_from
 * @property integer $user_to
 * @property integer $chat_id
 * @property string $create_time
 * @property string $text
 * @property integer $read
 *
 * @property \common\models\Chat $chat
 * @property \common\models\User $userFrom
 * @property \common\models\EventUser $userTo
 */
class ChatMessage extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_from', 'user_to', 'chat_id', 'read'], 'integer'],
            [['create_time'], 'safe'],
            [['text'], 'string']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'chat_message';
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app','ID'),
            'user_from' => Yii::t('app','Od użytkownika'),
            'user_to' => Yii::t('app','Do użytkownika'),
            'chat_id' => Yii::t('app','ID chatu'),
            'create_time' => Yii::t('app','Utworzony'),
            'text' => Yii::t('app','Tekst'),
            'read' => Yii::t('app','Przeczytany'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */

    public function getTime()
    {
        date_default_timezone_set(Yii::$app->params['timeZone']);

        $originalDate = $this->create_time;
        $week = [0=>'nd', 1=>'pon.', 2=>'wt.', 3=>'śr.', 4=>'czw.', 5=>'pt.', 6=>'sb.'];
        if (date('Y-m-d') == date("Y-m-d", strtotime($originalDate)))
             $newDate = date("H:i", strtotime($originalDate));
         else
            $newDate = $week[date("w", strtotime($originalDate))];
       
        return $newDate;
    }

    public function getFullTime()
    {
        date_default_timezone_set(Yii::$app->params['timeZone']);
        $originalDate = $this->create_time;
        $week = [0=>'nd', 1=>'pon.', 2=>'wt.', 3=>'śr.', 4=>'czw.', 5=>'pt.', 6=>'sb.'];
        $newDate = $week[date("w", strtotime($originalDate))]." ".date("d.m.Y H:i:s", strtotime($originalDate));
        return $newDate;
    }    

    public function notMe($id)
    {
        if ($this->user_to==$id){
            return $this->userFrom;
        }else{
            return $this->userTo;
        }
    }

    public function getChat()
    {
        return $this->hasOne(\common\models\Chat::className(), ['id' => 'chat_id']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserFrom()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_from']);
    }
        
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserTo()
    {
        return $this->hasOne(\common\models\User::className(), ['id' => 'user_to']);
    }
    
/**
     * @inheritdoc
     * @return array mixed
     */ 
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => false,
                'updatedAtAttribute' => false,
                'value' => new \yii\db\Expression('NOW()'),
            ],
        ];
    }
}
