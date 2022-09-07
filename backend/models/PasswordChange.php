<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use kartik\password\StrengthValidator;
use yii\db\Expression;

class PasswordChange extends Model
{
    public $old_password;
    public $password;
    public $password_repeat;

    public $email;
    public $username;

    public function init()
    {
        parent::init();
        $user = \Yii::$app->user->identity;
        $this->email = $user->email;
        $this->username = $user->username;
        if (empty($user->last_vist) == true)
        {
            \Yii::$app->session->setFlash('warning', Yii::t('app', 'Nie ustawiłeś jeszcze nowego hasła!'));
        }
    }

    public function rules()
    {
        $rules = [
            [['old_password', 'password', 'password_repeat'], 'required'],
            [['old_password'], 'validatePassword'],
            [['password'], 'compare', 'compareAttribute'=>'old_password', 'operator'=>'!='],
            [['password'], 'compare'],
//            [['password'], 'string', 'min'=>8],
            [['password'], StrengthValidator::className(), 'preset'=>StrengthValidator::NORMAL]
        ];
        return array_merge(parent::rules(), $rules);
    }

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = \Yii::$app->user->identity;
            if (!$user || !$user->validatePassword($this->old_password)) {
                $this->addError($attribute, Yii::t('app', 'Stare hasło niepoprawne'));
            }
        }
    }

    public function attributeLabels()
    {
        $labels = [
            'old_password'=>Yii::t('app', 'Obecne hasło'),
            'password'=>Yii::t('app', 'Nowe hasło'),
            'password_repeat'=>Yii::t('app', 'Powtórz nowe hasło'),
        ];
        return array_merge(parent::attributeLabels(), $labels);
    }

    public function save()
    {
        if ($this->validate()) {
            $user = \Yii::$app->user->identity;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            if (empty($user->last_vist) == true)
            {
                $user->last_visit = new Expression('NOW()');
            }

            $user->save();
            return $user;
        }
    }
}