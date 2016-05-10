<?php

/**
 * LoginForm 类.
 * LoginForm 是一直使用的数据结构（就是会在多个地方使用）
 * 用户登录的表单数据.它被用作'SiteController'控制器的'login' 动作 
 */
class ApiForm extends CFormModel
{
	private $tid;      //terminal id
	private $time;     //timestamp

	private $_identity;

	/**
	 * 声明验证规则
	 * 此规则声明用户名和密码是必须的,
	 * 并且密码需要被认证
	 */
	public function rules()//表单提交时执行此规则（非Ajax验证）
	{
		return array(
			// username 和 password 是必须的
			array('tid, time', 'required'),
			// rememberMe 是 boolean类型
			array('tid,time', 'boolean'),
			// password 需要认证
			//array('password', 'authenticate'),
		);
	}

	/**
	 * 定义属性标签
	 */
	public function attributeLabels()
	{
		return array(
			'rememberMe'=>'下次记住我',
		);
	}

	/**
	 * 认证密码 password.
	 * 这是在rules()中定义的验证程序authenticate，这里的authenticate调用UserIdentity里面的authenticate函数。
	 */
	public function authenticate($attribute,$params)//这两个参数在这里没用
	{
		$this->_identity=new UserIdentity($this->username,$this->password);
		if(!$this->_identity->authenticate())
			$this->addError('password','不正确的用户名或密码！');
	}

	/**
	 * 用户使用模型中给定的用户名和密码登录
	 * @返回是否登录成功的 boolean 值。
	 */
	public function login()
	{	
		if($this->_identity===null)//如果没有启用验证规则（也就是SiteController.php下面的actionlogin()动作下面的$model->validate()），则在这里要进行验证。
		{
			$this->_identity=new UserIdentity($this->username,$this->password);
			$this->_identity->authenticate();
		}
		if($this->_identity->errorCode===UserIdentity::ERROR_NONE)
		{
			$duration=$this->rememberMe ? 3600*24*30 : 0; // 30 天
			Yii::app()->user->login($this->_identity,$duration);//此处是登录一个用户，具体可参考：sysytem.web.auth->CWebUser->login()方法。
			return true;
		}
		else
			return false;
	}
}
