<?php
namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\User;
/**
 * Login form
 */
class PasswordForget extends Model
{
    public $username;

    private $_user = false;


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username'], 'required'],
        ];
    }


    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::backendFindByUsername($this->username);
        }

        return $this->_user;
    }
	
	public function attributeLabels()
	{
		return [
			'username'=>Yii::t('app', 'Nazwa użytkownika'),
			'password'=>Yii::t('app', 'Hasło'),
			'rememberMe'=>Yii::t('app', 'Zapamiętaj mnie'),
		];
	}
}
