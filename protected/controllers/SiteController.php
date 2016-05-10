<?php

class SiteController extends Controller
{
	/**
	 * Declares class-based actions.
	 */
	public function actions()
	{
		return array(
			// captcha action renders the CAPTCHA image displayed on the contact page
			'captcha'=>array(
				'class'=>'CCaptchaAction',
				'backColor'=>0xFFFFFF,
			),
			// page action renders "static" pages stored under 'protected/views/site/pages'
			// They can be accessed via: index.php?r=site/page&view=FileName
			'page'=>array(
				'class'=>'CViewAction',
			),
		);
	}

	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		if(!Yii::app()->user->isGuest){
			$this->render('index',array(
				//'oftenUse' => CShort::getOftenUseUrl(),// 最常访问
			));
		}else{
			$this->redirect(array('site/login'));
//			$thisRoleActions=HelpTool::getActionByID(0);
//			Yii::app()->user->setState(Yii::app()->params->userSessionName,$thisRoleActions);
		}
	}


	/**
	 * 系统帮助信息
	 */
	public function actionHelp(){
		$this->render('help');
	}

	/**
	 * 版本更新记录
	 */
	public function actionChangelog(){
		$this->render('changelog');
	}

	/**
	 * This is the action to handle external exceptions.
	 */
	public function actionError()
	{
	    if($error=Yii::app()->errorHandler->error)
	    {
	    	if(Yii::app()->request->isAjaxRequest)
	    		echo $error['message'];
	    	else
	        	$this->render('error', $error);
	    }
	}

	/**
	 * Displays the contact page
	 */
	public function actionContact()
	{
		$model=new ContactForm;
		if(isset($_POST['ContactForm']))
		{
			$model->attributes=$_POST['ContactForm'];
			if($model->validate())
			{
				$headers="From: {$model->email}\r\nReply-To: {$model->email}";
				mail(Yii::app()->params['adminEmail'],$model->subject,$model->body,$headers);
				Yii::app()->user->setFlash('contact','Thank you for contacting us. We will respond to you as soon as possible.');
				$this->refresh();
			}
		}
		$this->render('contact',array('model'=>$model));
	}

	/**
	 * Displays the login page
	 */
	public function actionLogin()
	{
		$thisRoleActions=HelpTool::getActionByID(0);
		Yii::app()->user->setState(Yii::app()->params->userSessionName,$thisRoleActions);//给予Guest权限
		$model=new LoginForm;
		$limitUser = true ; //账号在线用户控制状态参数

		// if it is ajax validation request
		if(isset($_POST['ajax']) && $_POST['ajax']==='login-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

		// collect user input data
		if(isset($_POST['LoginForm']))
		{
			$model->attributes=$_POST['LoginForm'];
			// validate user input and redirect to the previous page if valid
			if($model->validate() && $model->login())
				{

					HelpTool::loginTime (); // 上次登陆信息
					$getUserState = CacheFile::getUserState(false); //判断当前账户在线人数是否超出限制
					if($getUserState)
					{
						//获取本ID的可访问的action信息
						$jason=json_encode(HelpTool::getActionByID(Yii::app()->user->getId()));
						$_SESSION[Yii::app()->params->userSessionName] = $jason; //存储可访问action的SESSION
						HelpTool::clearDbLogs(); //清除超出限制的日志
						HelpTool::getActionInfo(Yii::app()->user->getId(),8);//用户登录
						sleep(1);
						$this->redirect(Yii::app()->user->returnUrl);
					}else{
						$limitUser = false ;
					}

				}
		}
		// display the login form
		$this->renderPartial('login',array('model'=>$model,'limitUser'=>$limitUser));
	}

	/**
	 * Logs out the current user and redirect to homepage.
	 */
	public function actionLogout()
	{
		HelpTool::getActionInfo(Yii::app()->user->getId(),9);//用户注销
		CacheFile::getUserState(true); //清除 帐号在线用户控制缓存 中本次会话的sessionID
		Yii::app()->user->logout();
		//Yii::app ()->cache->flush (); //清除缓存
		$this->redirect(Yii::app()->homeUrl);
	}

	/**
	 * 管理员修改密码
	 */
	public function actionChangpwd() {
		$msg = '';
		if (isset ( $_POST ['Changpwd'] )) {
			$pl = $_POST ['Changpwd'];
			$oldpwd = $pl ['oldpwd'];
			$newpwd = trim ( $pl ['newpwd'] );
			$newpwds = trim ( $pl ['newpwds'] );

			$uname = Yii::app ()->user->name;

			$rs = ManageList::model ()->findByAttributes ( array (
					'username' => $uname
			) );
			$salt = $rs ['salt'];
			$md5Oldpwd = MD5 ( MD5 ( $oldpwd ) . $rs ['salt'] );

			$ca = ManageList::model ()->findByAttributes ( array (
					'password' => $md5Oldpwd,
					'username' => $uname
			) );

			if (empty ( $oldpwd ))
				$msg = '请输入原密码！';
			elseif (empty ( $newpwd ))
				$msg = '请输入新密码！';
			elseif ($newpwd != $newpwds)
				$msg = '两次输入的新密码不一致！';
			elseif (! $ca)
				$msg = '您输入的原始密码有误！';
			elseif($oldpwd==$newpwd)
			$msg = '新密码与旧密码不能一致！';
			elseif(trim($newpwd) == '')
			$msg = '密码不能为空！';
			else {
				$list ['password'] = MD5 ( MD5 ( $newpwd ) . $salt );
				if (ManageList::model ()->updateAll ( $list, " username='" . $uname . "'" ))
				{
					$msg = '修改成功！';
					HelpTool::getActionInfo(0,13);//修改密码
				}
			}
			echo $msg;
			exit ();
		} else {
			$this->renderPartial ( 'changpwd', array (
					'msg' => $msg
			) );
		}
	}


	/**
	 * 文件下载
	 */
	public function actionDownload()
	{
		$filedir = 'cache/';
		$fn = json_decode($_GET['fn']);
		$filename = iconv('utf-8','gbk',$fn);
		$filename = preg_replace('/^.+[\\\\\\/]/','',$filename);
		if (!empty($filename) && file_exists($filedir.$filename)){
		    $file = fopen($filedir.$filename,"r");
		    ob_end_clean();//清空缓存区 修复 BUG #1137
		    header("Content-type: application/force-download");
		    header("Accept-Ranges: bytes");
		    header("Accept-Length: ".filesize($filedir.$filename));
		    header("Content-Disposition: attachment; filename=".$filename);
		    echo fread($file, filesize($filedir.$filename));
		    fclose($file);
		} else {
		    header("Content-type: text/html; charset=utf-8");
		    echo "File not found!";
		    exit;
		}
	}

	/**
	 * 清空缓存
	 */
	public function actionClearCache($url)
	{
		// 清空缓存
		Yii::app ()->cache->flush ();
		HelpTool::getActionInfo(0,12);//清除缓存
		$this->redirect ( $url );
	}
}