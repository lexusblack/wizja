<?php
namespace backend\models;

use yii\base\Model;
use common\helpers\ArrayHelper;
use Yii;

class SendMail extends Model
{
    public $link;
    public $subject;
    public $text;
    public $type;
    public $priority;
    public $username;
    public $usermail;
    public $company;


    public function rules()
    {
        return [
            [['subject', 'text', 'link', 'username', 'usermail', 'company'], 'string'],
            [[ 'type', 'priority'], 'number'],
            [['subject',], 'required']
        ];
    }

        public function attributeLabels()
    {
        $labels = [
            'subject' => Yii::t('app', 'Temat'),
            'text' => Yii::t('app', 'Treść'),
            'link' =>Yii::t('app', 'Link'),
            'priority' =>Yii::t('app', 'Priorytet'),
            'type' =>Yii::t('app', 'Type'),
            'username'=>Yii::t('app', 'Imię i nazwisko zgłaszającego'),
            'usermail'=>Yii::t('app', 'E-mail zgłaszającego'),
            'company'=>Yii::t('app', 'Firma zgłaszającego')
        ];

        return array_merge(parent::attributeLabels(), $labels);
    }

}