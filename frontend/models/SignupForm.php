<?php
namespace frontend\models;

use common\models\User;
use yii\base\Model;
use Yii;

/**
 * Signup form
 */
class SignupForm extends Model
{
    public $username;
    public $email;
    public $password;
	public $first_name;
	public $last_name;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'first_name', 'last_name'], 'filter', 'filter' => 'trim'],
            [['username', 'first_name', 'last_name'], 'required'],
            ['username', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('app', 'Nazwa użytkownika jest już zajęta.')],
            [['username', 'first_name', 'last_name'], 'string', 'min' => 2, 'max' => 255],

            ['email', 'filter', 'filter' => 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => Yii::t('app', 'Email jest już przypisany do użytkownika.')],

            ['password', 'required'],
            ['password', 'string', 'min' => 6],
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if ($this->validate()) {
            $user = new User();
            $user->username = $this->username;
            $user->email = $this->email;
			$user->first_name = $this->first_name;
			$user->last_name = $this->last_name;
            $user->setPassword($this->password);
            $user->generateAuthKey();
            $user->save();
            return $user;
        }

        return null;
    }
	
	public function attributeLabels()
	{
		return [
			'username'=>Yii::t('app', 'Nazwa użytkownika'),
			'password'=>Yii::t('app', 'Hasło'),
			'email'=>Yii::t('app', 'Adres email'),
			'first_name'=>Yii::t('app', 'Imię'),
			'last_name'=>Yii::t('app', 'Nazwisko'),
		];
	}
}
