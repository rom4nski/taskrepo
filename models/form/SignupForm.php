<?php

namespace app\models\form;

use Yii;
use yii\base\Model;
use app\models\User;

/**
 * SignupForm is the model behind the signup form.
 *
 * @property-read User|null $user
 *
 */
class SignupForm extends Model
{
    public $username;
    public $password;
	
	private $_user = false;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required'],
			['username', 'unique', 'targetClass' => User::class, 'message' => 'Username has already been taken.'],
            ['password', 'string', 'min' => 6],
        ];
    }
	
	public function signup()
    {
        if (!$this->validate()) {
			return false;
        }
		
		$user = new User();
		$user->username = $this->username;
		$user->setPassword($this->password);
		$user->generateAuthKey();
		return $user->save();
    }
	
	public function getUser()
    {
        if ($this->_user === false) {
            $this->_user = User::findByUsername($this->username);
        }

        return $this->_user;
    }
}
