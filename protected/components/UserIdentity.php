<?php

/**
 * UserIdentity represents the data needed to identity a user.
 * It contains the authentication method that checks if the provided
 * data can identity the user.
 */
class UserIdentity extends CUserIdentity {

	private $_id;
	
	/**
	 * Authenticates a user.
	 * The example implementation makes sure if the username and password
	 * are both 'demo'.
	 * In practical applications, this should be changed to authenticate
	 * against some persistent user identity storage (e.g. database).
	 * 
	 * @return boolean whether authentication succeeds.
	 */
	public function authenticate()
	{
		$this->username = trim( strtolower ( $this->username )); //用户名首尾去空格
		$this->password = trim( $this->password ); //密码首尾去空格
		$users = ManageList::model()->findByAttributes( array( 'username'=>$this->username ) ); //查询数据库中符合此用户名的密码
		$this->password = md5( md5( $this->password ).$users->salt );
		if(!isset($users->username))
		{
			$this->errorCode=self::ERROR_USERNAME_INVALID;
		}
		else if($users->password!==$this->password)
		{
			$this->errorCode=self::ERROR_PASSWORD_INVALID;
		}else{
			$this->_id = $users->userid;
			$this->setState('userRole',$users->itemname);
			$this->errorCode=self::ERROR_NONE;
		}
		return !$this->errorCode;
	}
	
	/*重写getId()方法*/
	public function getId() 
	{
		return $this->_id;
	}
}