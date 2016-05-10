<?php
 
class SysManageController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/column2';
	
	
	 /* 
	 * Test Action
	 */
	 
	 
	 public function actionTest()
	 {
		$pdata = Yii::app()->params->powermenu; //菜单数组
		
		$itmesArray = array();
		
		 /* foreach($pdata['items'] as $firstVal)
		{
			foreach($firstVal['items'] as $secondVal)
			{
				foreach($secondVal['items'] as $thirdVal)
				{
					$thisUrlAdmin = str_replace('/','',$thirdVal['url']).'Admin';
					$thisUrlUser = str_replace('/','',$thirdVal['url']).'User';
					$itmesArray[] = $thisUrlAdmin;
					$itmesArray[] = $thisUrlUser;
				}
			}
		} 
		
		foreach($itmesArray as $taskVal)
		{
			$rs = AuthItem::model()->findByAttributes(array('name'=>$taskVal));
			if(!isset($rs)){
				$admin=new AuthItem;     
				$admin->name=$taskVal; 
				$admin->type=1; 
				$admin->save();
			}
		} */
		
		
		$this->render('test',array(
			'itmesArray'=>$itmesArray,
			'pdata'=>$pdata,
		));
	 
	 }
	 
	 /* 
	 *初始化GIS栅格数据
	  @author 曹芳
	 */
	 public function actioninitgisgriddb()
	 {
		$connect=Yii::app()->db;  //建立MySQL数据库的连接
		//得到存储在MySQL中的数据库名字
		$connectString = Yii::app()->db->connectionString;//如：'mysql:host=localhost;dbname=mqs_gis'
		$connectString1= substr(strpbrk($connectString,"="),1);//如：'localhost;dbname=mqs_gis'
		$dbname=substr(strpbrk($connectString1,"="),1);
		
		//判断MySQL数据库中表grids_information是否存在
		$m_grid_tbname = Yii::app()->params->gis_grid['m_grid_tbname'];
		$mysql_IsTableExists = "select TABLE_NAME from INFORMATION_SCHEMA.TABLES where TABLE_SCHEMA='".$dbname."' and TABLE_NAME='".$m_grid_tbname."'" ;
		$m_gridIsExists=$connect->createCommand($mysql_IsTableExists)->queryAll();
		
		$conn_string = GISConfig::get()->pgConnectStr;//pgsql链接
		$pgsql_con = pg_connect($conn_string) or die ('connection failed'); //建立postgreSQL数据库的连接
		
		//判断postgreSQL数据库中表grids_tz是否存在
		$p_grid_tbname = Yii::app()->params->gis_grid['p_grid_tbname'];
		$pg_gridIsExists = pg_fetch_array( pg_query("select tablename from pg_tables where schemaname='public' and tablename='".$p_grid_tbname."'" ) );
		
		$this->render('initgisgriddb',array(
			'mysql_GridTbIsExist' => $m_gridIsExists,
			'pg_GridTbIsExist' => $pg_gridIsExists)
		);
	 }
	 /* 
	 *创建新的栅格数据库 
	  @author 曹芳
	 */
	  public function actionGis_NewGriddb()
	 {
		set_time_limit(0);
		require_once(Yii::app()->basePath.'/extensions/functions.php');
		require_once(Yii::app()->basePath.'/extensions/gisFunctions.php');
		$city = $_POST['city'];
		$province = $_POST['province'];
		$minLat = $_POST['minLat'];//31°56′ N
		$maxLat = $_POST['maxLat'];//33°12′N
		$minLon = $_POST['minLon'];//119°38′ E
		$maxLon = $_POST['maxLon'];//120°33′ E
		$deltaLon = $_POST['deltaLon'];//30″
		$deltaLat = $_POST['deltaLat']; //30″
		$mysql_GridTbIsExist=$_POST['mysql_GridTbIsExist'];//判断表grids_information是否存在
		//建立MySQL数据库的连接
		$connect=Yii::app()->db;
		
		$m_grid_tbname = Yii::app()->params->gis_grid['m_grid_tbname'];
		$mysql_creategriddb="create table ". $m_grid_tbname ."(
			id int(11) not null primary key,
			centerLon double(30,12) not null,
			centerLat double(30,12) not null,
			minLon double(30,12) not null,
			minLat double(30,12) not null,
			maxLon double(30,12) not null,
			maxLat double(30,12) not null,
			province varchar(64),
			city varchar(64),
			district varchar(64),
			street varchar(256),
			g_grid_extentData longtext,
			g_grid_centerLonLat longtext)";
		if($mysql_GridTbIsExist=='true'){
			$connect->createCommand("TRUNCATE TABLE ".$m_grid_tbname)->execute();
		}else{
			$connect->createCommand($mysql_creategriddb)->execute();
		}
		
		//建立postgreSQL数据库的连接
		$conn_string = GISConfig::get()->pgConnectStr; //pgsql链接
		$pgsql_con = pg_connect($conn_string) or die ('connection failed');
		$p_grid_tbname = Yii::app()->params->gis_grid['p_grid_tbname'];
		pg_query("TRUNCATE TABLE ".$p_grid_tbname);
		
		$id=0;
		$numLat=floor(($maxLat-$minLat)/$deltaLat);
		$numLon=floor(($maxLon-$minLon)/$deltaLon);
		// print_r($numLat);
		// print_r($numLon);
		$grid_noAddress=array(); //用于存储第一次请求百度地图未请求到具体地址的格网单元的id和中心点的经纬度
		$address='';
		for($i=1;$i<=$numLat;$i++){
			for($j=1;$j<=$numLon;$j++){
				$cell_minLon= round($minLon + ($j-1)*$deltaLon,16);
				$cell_maxLon= round($minLon + $j*$deltaLon,16);
				$cell_minLat= round($minLat + ($i-1)*$deltaLat,16);
				$cell_maxLat= round($minLat + $i*$deltaLat,16);
				$cell_centerLon= round($cell_maxLon-$deltaLon/2,16);
				$cell_centerLat= round($cell_maxLat-$deltaLon/2,16);
				$cell_address = getPointAddress($cell_centerLon,$cell_centerLat,0);
				// print_r($cell_address);
				if($cell_address==null){
					for($k=1;$k<=5;$k++){
						$cell_address = getPointAddress($cell_centerLon,$cell_centerLat,0);
						if($cell_address!=null){
							break;
						}
					}
				}
				
				if($cell_address==null){
					$id++;
					$cell_address->province='';
					$cell_address->city='';
					$cell_address->district='';
					$cell_address->street='';
					$cell_address->street_number='';
					$uncellgrid=array(); 
					$uncellgrid['id']=$id;
					$uncellgrid['centerLon']=$cell_centerLon;
					$uncellgrid['centerLat']=$cell_centerLat;
					array_push($grid_noAddress,$uncellgrid);
				}else if($cell_address->province== $province && $cell_address->city == $city){
					$id++;
					$address=$cell_address->province.$cell_address->city.$cell_address->district.$cell_address->street.
					$cell_address->street_number;
				}else{
					continue;
				}
				
				//往postgreSQL中的表grids_tz插入数据
				$re = drawGrid($p_grid_tbname,$id,$address,$cell_centerLon,$cell_centerLat,$cell_minLon,$cell_minLat,$cell_maxLon,$cell_maxLat);
				pg_query( $re[0] ); 
				// print_r($re);exit;
				//往mysql中的表grid_information插入数据
				$g_grid_extentData = $re[1];
				$g_grid_centerLonLat = $re[2];
				$sql_insertgridcell="insert into ".$m_grid_tbname."(id, centerLon, centerLat, minLon, minLat, maxLon, maxLat, province, city, district, street, g_grid_extentData,g_grid_centerLonLat) values (".$id.",".$cell_centerLon.",".$cell_centerLat.",".$cell_minLon.",".$cell_minLat.",".$cell_maxLon.",".$cell_maxLat.",'".$cell_address->province."','".$cell_address->city."','".$cell_address->district."','".$cell_address->street.$cell_address->street_number."','".$g_grid_extentData."','".$g_grid_centerLonLat."') ";
				$connect->createCommand($sql_insertgridcell)->execute();
			}
		}
		pg_close($pgsql_con);
		echo $grid_noAddress;
	}
	 
	 /* 
	 *创建新角色 
	 */
	 public function actionCreateRole()
	 {
		$postValue = array();
		$roleNameStr = 'myRole'; //角色称前缀
		
		//接收角色Task分配表单
		 if(isset($_POST['formArray']))
		{
			$postValue = $_POST['formArray'];
			$postnameValue = $_POST['roleName'];

			//插入翻译表
			$roleTrans=new UserRoleTrans;     
			$roleTrans->roletrans=$postnameValue; 
			$roleTrans->save();
			
			//创建角色名称
			$thisrolers = UserRoleTrans::model()->findByAttributes(array('roletrans'=>$postnameValue));
			$thisroleId = $thisrolers['id'];
			$list['rolename'] = $roleNameStr.$thisroleId; //要修改的角色名称
			UserRoleTrans::model()->updateAll($list," roletrans='".$postnameValue."'"); //添加角色名称
			
			//插入数据库角色名
			$role=new AuthItem;     
				$role->name=$list['rolename']; 
				$role->type=2; 
				$role->save();
			
			//插入数据库角色所属权限
			foreach( $postValue as $taskKey=>$taskVal )
			{
				if( $taskVal =='manage' )
				{
					$thisTask = str_replace('/','',$taskKey).'Admin';
				}else if( $taskVal =='readOnly' ){
					$thisTask = str_replace('/','',$taskKey).'User';
				}
				
				//保存角色分配的Task
				$task=new ItemChildren;     
				$task->parent=$list['rolename']; 
				$task->child=$thisTask; 
				$task->save();
			}
		}
		 
		$this->renderPartial('createrole',array(
		'thisForm'=>$postValue,
		'post'=>$_POST,
		));
	 }
	 
	 
	  /* 
	 *角色修改
	 */
	 public function actionRoleUpdate($roleName)
	{	
		//检测需要修改的角色已分配的Task
		$rs = ItemChildren::model()->findByAttributes(array('parent'=>$roleName));
		
		//如果获取到新的修改的表单，执行修改
		if(isset($_POST['newTaskArray']))
		{
			//删除原有已分配的Task
			ItemChildren::model()->deleteAll(array('parent'=>$roleName)); 
			//插入数据库  同上
		}
		
		$this->renderPartial('roleupdate',array(
			'oldTaskArray'=>$rs,
		));
		
	}
	 
	 
	 /* 
	 *清除日志
	 */
	public function actionClearLogs()
	{	
		//HelpTool::getActionInfo($id,4);//访问操作
		$connection=Yii::app()->db;
		$sql="delete from view_action_logs";
		$command = $connection->createCommand($sql);
		$command->execute();  
		CacheFile::logsSearch(true); //重新生成日志缓存
		$this->redirect('index.php?r=sysmanage/userlogadmin'); 
	}
	
	
	/* 
	 *查看用户信息
	*/
	public function actionUserView($id)
	{
	HelpTool::getActionInfo($id,4);//访问操作
		$this->render('userview',array(
			'model'=>$this->loadUserModel($id),
		));
	}
	
	/* 
	 *修改完成视图
	*/
	public function actionUserUpdateView($id)
	{
	HelpTool::getActionInfo($id,4);//访问操作
		$this->renderPartial('userupdateview',array(
			'model'=>$this->loadUserModel($id),
		));
	}
	
	
	/* 
	 *用户日志详情视图
	*/
	public function actionUserLogView($id)
	{
		HelpTool::getActionInfo($id,4);//访问操作
		$this->renderPartial('userlogview',array(
			'model'=>$this->loadUserLogModel($id),
		));
	}

	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */

	 /* 
	 *创建新用户
	 */
	public function actionUserCreate()
	{
		if(isset($_REQUEST['id']))
		{
			$this->render('usercreate',array(
				'model'=>$this->loadUserModel($_REQUEST['id']),
				'select'=>'usercreateview',
			));

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);
		
		}else{
		
			$model=new ManageList;

			if(isset($_POST['ManageList']))
				{
					$randomStr='';//初始化随机字段
					$length=rand(5,20);//定义随机字段长度
					//随机字段字符数组
					$conso=array("!","@","#","$","%","&","*","1","2","3","4","5","6","7","8","9","b","c","d","f","g","h","j","k","l","m","n","p","r","s","t","v","w","x","y","z");
					//生成随机字段
					for($i=1; $i<=$length; $i++)
					{
						$tempNum = rand(0,35);
						$randomStr = $conso[$tempNum].$randomStr;
					}
					$manageList = $_POST['ManageList'];//获取页面表单
					$manageList['username'] = trim( strtolower( $_POST['ManageList']['username'] ) ); // 用户名去除空格
					$salt = $randomStr;//定义salt
					$md5Salt = MD5($salt);//salt MD5散列
					$manageList['salt'] = $md5Salt;
					//判断密码空格
					$manageList['password'] = trim($manageList['password']);
					if($manageList['password'] != '')
					{
						$md5Password = MD5(MD5($manageList['password']).$md5Salt);//用户密码加盐散列
						$manageList['password'] = $md5Password;
					}
					else
					{
						$manageList['password'] == '';
					}

					$manageList['regDateTime'] = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']); //reg time
					
					$model->attributes = $manageList;
					
					if($model->save())
					{
						HelpTool::getActionInfo($model->userid,2);//新建操作
						CacheFile::userSearch(true); //重新载入用户缓存
						$this->redirect(array('usercreate','id'=>$model->userid));
					}
				}
			$this->render('usercreate',array(
				'model'=>$model,
				'select'=>'usercreate',
			));
		}
	}
	
	
	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	
	/* 
	 *用户信息修改
	 */
	public function actionUserUpdate($id)
	{
		$model=$this->loadUserModel($id);

		if(isset($_POST['ManageList']))
		{
			if($_POST['ManageList'])
			{
				$_POST['ManageList']['username'] = trim(strtolower($_POST['ManageList']['username']));
				$oldPassword = $model->password; //旧密码
				$pagePassword = trim( $_POST['ManageList']['password'] ); //页面输入的新密码
				$salt=$model->salt; //salt
				//判断密码空格
				if($pagePassword!='')
				{
					if($pagePassword!==$oldPassword)
					{
						$newPassword=MD5(MD5($pagePassword).$salt); //新密码加盐MD5散列
						$_POST['ManageList']['password']=$newPassword;
					}
				}else{
					$_POST['ManageList']['password'] = '';
				}
			}
			
			$model->attributes=$_POST['ManageList'];
			
			if($model->save())
			{
				HelpTool::getActionInfo($model->userid,1);//修改操作
				CacheFile::userSearch(true); //重新载入用户缓存
				$this->redirect(array('userupdateview','id'=>$model->userid));
			}
		}
		$this->renderPartial('userupdate',array(
			'model'=>$model,
		));
	}
	

	/**
	 * Deletes a particular model.
	 * If deletion is successful, the browser will be redirected to the 'admin' page.
	 * @param integer $id the ID of the model to be deleted
	 */

	
	public function actionUserDelete($id)
	{
	
		$thisUserId = Yii::app()->user->getId();
		if( $id != $thisUserId)
		{
			$this->loadUserModel($id)->delete();
			HelpTool::getActionInfo($id,0);//删除操作
			CacheFile::userSearch(true); //重新载入用户缓存
		}

			if(!isset($_GET['ajax']))
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('useradmin'));
		else
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
	}
	
	public function actionUserDeleteAll($ids)
	{
		if(!!$ids)
		{
			$idArray = array();
			$idArray = explode(',',$ids);
			ManageList::model()->deleteByPk($idArray);
			
			HelpTool::getActionInfo($ids,0);//删除操作
			CacheFile::userSearch(true); //重新载入用户缓存
		}
		$this->render('useradmin');
	}

	public function actionUserLogDelete($id)
	{

			$this->loadUserLogModel($id)->delete();
			HelpTool::getActionInfo($id,0);//删除操作
			CacheFile::logsSearch(true); //重新载入日志缓存
			if(!isset($_GET['ajax'])){
				$this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('userlogadmin'));
		}else{
			throw new CHttpException(400,'Invalid request. Please do not repeat this request again.');
		}
	}
	
	public function actionUserLogDeleteAll($ids){
	
		$idArray = array();
		$idArray = explode(',',$ids);
		ViewActionLogs::model()->deleteByPk($idArray);
		HelpTool::getActionInfo($ids,0);//删除操作
		CacheFile::logsSearch(true); //重新载入日志缓存
	}


	/**
	 * Manages all models.
	 */
	public function actionAdmin()
	{

		//$model=new DbbackupList('search');
		//$model->unsetAttributes();  // clear any default values		
			$FilePath = Yii::app()->basePath . '/../protected/data/backup/';
			$Order=0;
			$listSearch = array();

			$FilePath = opendir($FilePath);
			 while($filename = readdir($FilePath)) {
					$fileArr[] = $filename;
			 }
			$Order == 0 ? sort($fileArr) : rsort($fileArr);

			$list = array();	  
				foreach ($fileArr as $key => $value){			
					if($key > 1){	 
				 //获取文件创建时间 
				$FilePath = Yii::app()->basePath . '/../protected/data/backup/';				 
				 $fileTime = date('Y-m-d H:i',filemtime($FilePath.$value));
				 $fileSize = filesize($FilePath.$value)/1024;
				 //获取文件大小
				 $fileSize = $fileSize < 1024 ? number_format($fileSize,2).' KB':
				 number_format($fileSize/1024,2).' MB';
				 //构建列表数组
				$list[]=array(
				   'name' => $value,
				   'time' => $fileTime,
				   'size' => $fileSize
				);
			}
		}
		
			if(isset($_GET['DbbackupList'])){
			$backupTime=$_GET['DbbackupList']['backupTime'];
			foreach($list as $listVals){
			$cutTime=substr_replace($listVals['time'],"",-6,6);
				if($cutTime==$backupTime){
				$listSearch[]=$listVals;
					}else{
					$listSearch[]=null;
					}
				}
					$this->render('admin',array(
					//'model'=>$model,
					'data'=>$listSearch,
				));			
			}else{
		$this->render('admin',array(
			//'model'=>$model,
			'data'=>$list,
		));
		
		}
	}
	
	/* 
	 * @name 数据库自动备份
	 * @author 张洪源
	 * @create date 2013-07-18 14:26
	 */
	public function actionAutoBackup()
	{
		$os = PATH_SEPARATOR==':' ? 'Linux' : 'Windows'; //判断操作系统,在linux上是一个":"号,WIN上是一个";"号
		$week = array('M'=>'周一','T'=>'周二','W'=>'周三','TH'=>'周四','F'=>'周五','S'=>'周六','SU'=>'周日'); //定义备份日期
		//$path = Yii::app()->basePath . '\\auto\\'; //自动备份文件绝对路径
		$path = Yii::app()->basePath . '/auto/'; //自动备份文件绝对路径
		$main = include_once($path.'main.php'); //引入上次保存的配置文件
		$error = '';
		//如果提交表单
		if(isset($_GET['AutoBackup']))
		{
			$openDBAutoBackup = isset($_GET['AutoBackup']['openDBAutoBackup']) ? $_GET['AutoBackup']['openDBAutoBackup'] : '';
			$backupDay = isset($_GET['AutoBackup']['backupDay']) ? array_keys($_GET['AutoBackup']['backupDay']) : array();
			$serverName = $_SERVER['SERVER_NAME'] == 'localhost' ? '127.0.0.1' : $_SERVER['SERVER_NAME']; //判断localhost
			$fileInfo = ''; //待写入文件的字符
			$osPath = $os.'\\'; //可执行文件路径
			//如果操作系统为Windows
			
			
			
			if( $openDBAutoBackup == 'on' )
			{
				if( !empty( $backupDay ) )
				{
					if($os == 'Windows')
					{
						$fileName = "autoBackup.bat"; //自动运行程序文件名
						//以下为可执行文件文件内容
						$backupTime = isset($_GET['AutoBackup']['backupTime']) ? $_GET['AutoBackup']['backupTime'] : '00:00:00';
						$backupDayStr = implode(',',$backupDay);
						$at = "at $backupTime ";
						$every = "/every:$backupDayStr ";
						$curlPath = '"' . $path . 'windows\\curl.exe" -s http://'.$serverName.Yii::app()->createUrl("databack/backup")."\r\n";
						$fileInfo = $at.$every.$curlPath;
					}else{ //操作系统为Linux
						$fileName = "autobackup.sh";
					}
					
					//执行文件
					file_put_contents($path.$osPath.$fileName, mb_convert_encoding($fileInfo,'GBK')); //将执行内容写入可执行文件
					//配置文件
					$fileInfo = '<?php return '.var_export($_GET['AutoBackup'], true).'; ?>'; 
					file_put_contents($path.'main.php', mb_convert_encoding($fileInfo,'GBK')); //将本次配置内容写入配置文件
					
					if($os == 'Windows') 
					{
						$atRes = include_once($path.$osPath.'ATID.php');
						$atId = substr($atRes[2],-1,1);
						$deleteAtInfo = "at $atId /Delete";
						file_put_contents($path.$osPath.'deleteAt.bat', $deleteAtInfo); //将取消上次任务的执行内容存入可执行文件
						exec($path.$osPath.'deleteAt.bat',$deleteRes); //Windows系统取消上次计划任务
						file_put_contents($path.$osPath.'deleteAt.php', $deleteRes); //将本次执行的delete at的操作返回值存入deleteAt文件
						exec($path.$osPath.$fileName,$commitRes); //Windows系统执行新的计划任务
						$fileInfo = '<?php return '.var_export($commitRes, true).'; ?>'; //构造
						file_put_contents($path.$osPath.'ATID.php', $fileInfo); //将本次执行的AT id保存至ATID文件
					}
					echo "<script>alert('自动备份设置成功！');parent.location.reload(true);</script>";
				}else{
					$error = "请至少选择一个备份日期";
				}
			}else{
				if($os == 'Windows') 
				{
					exec($path.$osPath.'deleteAt.bat',$deleteRes); //Windows系统取消上次计划任务
					file_put_contents($path.$osPath.'deleteAt.php', $deleteRes); //将本次执行的delete at的操作返回值存入deleteAt文件
					file_put_contents($path.'main.php', mb_convert_encoding('','GBK')); //将本次配置内容写入配置文件
				}
				echo "<script>alert('自动备份已成功取消！');parent.location.reload(true);</script>";
			}
				
		}
		$this->renderPartial('autobackup',array(
			'os'=>$os,
			'week'=>$week,
			'main'=>$main,
			'error'=>$error
		));
	}

	
	public function actionUserAdmin()
	{
		$model=new ManageList('search');
		$model->unsetAttributes();  // clear any default values
		$thisUserID = Yii::app()->user->getId();
		
			if(isset($_GET['ManageList'])){
			$manageSearch=$_GET['ManageList'];
			
			if(isset($_GET['ManageList']['username'])){
				if($manageSearch['username']=='请输入用户名进行搜索'){
					$manageSearch['username']='';
				}else{
					$manageSearch['username'] = trim($_GET['ManageList']['username']);
				}
			}

			if(isset($_GET['ManageList']['itemname']))
			{
				if($manageSearch['itemname']=='所有用户组')
				{
					$manageSearch['itemname']='';
				}else{
					$allRolesTrans=HelpTool::allRolesTrans();
					foreach($allRolesTrans as $allRolesTransKey=>$allRolesTransVal)
					{
						if($allRolesTransVal==$manageSearch['itemname'])
						{
							$manageSearch['itemname']=$allRolesTransKey;
						}else{
							continue;
						}
					}
				}
			}
			
			if(isset($_GET['ManageList']['province']))
			{
				$manageSearch['city'] = '';
				if($manageSearch['province']=='所有省份')
				{
					$manageSearch['province']='';
				}
			}
			
			if(isset($_GET['ManageList']['city']))
			{
				
				if($_GET['ManageList']['city']!='所有城市')
				{
					$thisProviceCity = CacheFile::userSearch();
					if(!empty($manageSearch['province'])&&in_array($_GET['ManageList']['city'],$thisProviceCity['address'][$manageSearch['province']]))
					{
						$manageSearch['city'] = $_GET['ManageList']['city'];
					}else{
						$manageSearch['city'] = '';
						$_GET['ManageList']['city']="所有城市";
					}
				}
			}
			$model->attributes=$manageSearch;
			}

		$this->render('useradmin',array(
			'model'=>$model,
			'thisUserID'=>$thisUserID,
		));
	}
	
	public function actionUserLogAdmin()
	{
		$model=new ViewActionLogs('search');
		$model->unsetAttributes();  // clear any default values
		if(isset($_GET['ViewActionLogs'])){
		
		
		$logsSearch=$_GET['ViewActionLogs'];
			
			if(isset($_GET['ViewActionLogs']['userrole'])){
				if($logsSearch['userrole']=='所有用户组'){
					$logsSearch['userrole']='';
				}else{
					$allRolesTrans=HelpTool::getRolesTrans();
					foreach($allRolesTrans as $allRolesTransKey=>$allRolesTransVal)
					{
						if($logsSearch['userrole']==$allRolesTransVal)
						{
							$logsSearch['userrole']=$allRolesTransKey;
						}
					}
				}
			}
			
			if(isset($_GET['ViewActionLogs']['actiontype'])){
			
				if($logsSearch['actiontype']=='所有类型')
				{
					$logsSearch['actiontype']='';
				}else{
					$getLogTypeTrans=HelpTool::getLogTypeTrans('');
					foreach($getLogTypeTrans as $getLogTypeTransKey=>$getLogTypeTransVal)
					{
						if($getLogTypeTransVal==$logsSearch['actiontype'])
						{
							$logsSearch['actiontype']=$getLogTypeTransKey;
						}else{
							continue;
						}
					}	
				}
			}
			
			
			if(isset($_GET['ControlActionLogs']['actiontype'])){
				if($logsSearch['actiontype']=='所有类型'){
					$logsSearch['actiontype']='';
				}else{
				$getLogTypeTrans=HelpTool::getLogTypeTrans('');
				foreach($getLogTypeTrans as $getLogTypeTransKey=>$getLogTypeTransVal){
				if($getLogTypeTransVal==$logsSearch['actiontype']){
				$logsSearch['actiontype']=$getLogTypeTransKey;
						}else{
						continue;
						}
					}	
				}
			}
			
			if(isset($_GET['ViewActionLogs']['username'])){
				if($logsSearch['username']=='请输入用户名进行搜索')
				{
					$logsSearch['username']='';
				}else{
					$logsSearch['username']=trim($_GET['ViewActionLogs']['username']);
				}
			}

			$model->attributes=$logsSearch;
			}

		$this->render('userlogadmin',array(
			'model'=>$model,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	
	public function loadUserModel($id)
	{
		$model=ManageList::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function loadUserLogModel($id)
	{
		$model=ViewActionLogs::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function loadphoneModel($id)
	{
		$model=ModelNettype::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	
	public function loadStatementModel($id)
	{
		$model=ComplainStatementList::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}

	public function loadAlertsModel($id)
	{
		$model=SystemAlerts::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404,'The requested page does not exist.');
		return $model;
	}
	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model)
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='dbbackup-list-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		if(isset($_POST['ajax']) && $_POST['ajax']==='manage-list-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		if(isset($_POST['ajax']) && $_POST['ajax']==='sysbackup-list-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		if(isset($_POST['ajax']) && $_POST['ajax']==='view-action-logs-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}

			if(isset($_POST['ajax']) && $_POST['ajax']==='system-alerts-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
	}
	
	function actionshowerror()
	{
		$this->renderPartial('showerror');
	}
	function actionexcelhelp()
	{
		$this->renderPartial('excelhelp');
	}
	
	/**
	 * 终端配置管理
	 */ 
	public function actionPhoneConfig(){
		$model = new TerminalConfig();
		$list = array();
		if(isset($_GET['TerminalConfig']['tagname']) && $_GET['TerminalConfig']['tagname'] != '输入配置名进行搜索'){
			$list['tagname'] = trim($_GET['TerminalConfig']['tagname']);
		}
		$model->attributes = $list;
		$this->render('phoneConfig',array(
			'model'=>$model,
		));
	}
	public function actionModifyConfig(){
		$model = new TerminalConfig();
		$error = "";
		if(isset($_POST['terminalConfig'])){
			$val = trim($_POST['terminalConfig']['tagvalue']);
			if(empty($val)){
				$error = "配置项数值不能为空";
				$model = TerminalConfig::model()->findByPk($_POST['terminalConfig']['id']);
				$this->renderPartial('modifyConfig',array(
					'model'=>$model,
					'error'=>$error,
				));
			}else{
				$condition['tagvalue'] = $val;
				$model = TerminalConfig::model()->findByPk($_POST['terminalConfig']['id']);
				$data_old = $model->attributes;
				if($condition['tagvalue'] != $data_old['tagvalue']){
					$post_data = array();
					$model->updateByPk($_POST['terminalConfig']['id'],$condition);							
					$model = TerminalConfig::model()->findByPk($_POST['terminalConfig']['id']);
					$data = $model->attributes;
					//同步控制端
					$row1['tagname'] = $data['tagname'];
					$row1['tagvalue'] = $data['tagvalue'];
					$row1['city'] = Yii::app()->params->city;
					$post_data[] = $row1;
					
					//查找未同步成功的数据
					$conn = Yii::app()->db;
					$comm= $conn->createCommand("select data from cache_config;");
					$result = $comm->queryAll();
					if(!empty($result)){
						foreach ($result as $row){
							$rowData = json_decode($row['data'],true);
							if($rowData['tagname'] != $data['tagname'])
								$post_data[] = $rowData;
						}
					}
	
					$post_str['data'] = json_encode($post_data);
					
					$url = Yii::app()->params->url_control."update/synchrodbConfig.php";
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_URL, $url);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);  // 返回结果，而不是输出它
					curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);//最大连接时间10s
					curl_setopt($ch, CURLOPT_POSTFIELDS, $post_str);//将要修改的数据项传递给控制端
				
					$output = curl_exec($ch);
					$code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
					curl_close($ch);
					if($code == 200){
						if($output == 'false'){					
							$str = json_encode($row1);
							$res = $conn->createCommand("select id from cache_config where tagname='{$data['tagname']}' limit 0,1;")->queryRow();
							if(!isset($res['id']) || empty($res['id'])){
								$sql = "update cache_config set data='{$str}' where tagname='{$data['tagname']}'";						
							}else{
								$sql = "insert into cache_config (tagname,data) values ('{$data['tagname']}','{$str}')";						
							}
							$conn->createCommand($sql)->execute();			
						}else{
							$conn->createCommand("truncate table cache_config;")->execute();
						}
					}
				}
				HelpTool::getActionInfo(0,1);//访问操作
				$this->renderPartial('configView',array(
					'model'=>$model,
					'error'=>$error,
				));
			}
		}elseif(isset($_GET['id'])){
			HelpTool::getActionInfo(0,4);//访问操作
			$id = $_GET['id'];
			$model = TerminalConfig::model()->findByPk($id);
			$this->renderPartial('modifyConfig',array(
				'model'=>$model,
			));
		}else{
			$this->redirect('index.php?r=sysmanage/modifyConfig');
		}	
	}
	/**
	*author pang
	*update fang
	*lastupdate 2013-3-12
	*关联表操作
	*/
	function actionreferdata(){
		
		set_time_limit(0);
		$connection=Yii::app()->db;	

/*		//虽然节省了数据库访问次数，但是耗时太长4m10s
 * 		$update = "update site s set gridId=(select id from grids_information  where s.lng >= minLon and s.lng < maxLon and s.lat >= minLat and s.lat < maxLat LIMIT 1) where EXISTS 
			(SELECT id from grids_information  where s.lng >= minLon and s.lng < maxLon and s.lat >= minLat and s.lat < maxLat)";
		*/
		$data = Yii::app()->db->createCommand("select s.id,g.id as gid from site s join grids_information g on s.lng >= g.minLon and s.lng < g.maxLon and s.lat >= g.minLat and s.lat < g.maxLat")->queryAll();		
		$update = "";
		Yii::app()->db->createCommand("update site set gridId=NULL")->execute();
		if(!empty($data)){
			$i=0;
			foreach ($data as $v){
				if($i >= 300){
					Yii::app()->db->createCommand($update)->execute();
					$update ="";
					$i = 0;
				}
				$update .= "update site set gridId='{$v['gid']}' where id='{$v['id']}';";
				$i++;
			}
		}		
		if(!empty($update)){
			Yii::app()->db->createCommand($update)->execute();
			$success = "操作成功";
			//messages::show_msg($this->createUrl('sysmanage/dataInput'), '基站表与栅格信息表关联更新成功！!');
			$this->redirect('index.php?r=sysmanage/dataInput&md=ref&success='.json_encode($success));
		}else{
			$error = "未匹配到需更新的数据";
			$this->redirect('index.php?r=sysmanage/dataInput&md=ref&error='.json_encode($error));
		}	
	}
	
	//系统配置
	public function actionSysconfig($md='bh'){
		if(isset($_POST['sysconfig'])){
			$sca = $_POST['sysconfig'];
			if(trim($sca['sendByte'])!='' && trim($sca['callDuration'])!='')
				$this->saveInit($sca);
			else 
			    $this->redirect(Yii::app()->createUrl('sysmanage/sysconfig',array('md'=>$md)));
		}
		
		elseif(isset($_POST['gisconfig'])){
		    $ca = $_POST['gisconfig'];
		    $gismodel=GisConfig::model()->findByPk($ca['id']);
		    $gismodel->attributes=$ca;
		    if($gismodel->save()){
		        $file = Yii::app()->basePath.'/../flexgis/'.$ca['tag'].'/config.xml';
		        $xml = '<?xml version="1.0" encoding="utf-8"?>
<config>
    <level>'.$ca['level'].'</level>
    <clon>'.$ca['clon'].'</clon>
    <clat>'.$ca['clat'].'</clat>
    <dataurl>'.$ca['dataurl'].'</dataurl>
    <mapurl>'.$ca['mapurl'].'</mapurl>
</config>';
		        $fp = fopen($file,"w");
		        fwrite($fp,$xml);
		        fclose($fp);
		    }
		}
		
		else{
		    /* if($md=='gis'){
    		    $gismodel=new GisConfig('search');
    		    $gismodel->unsetAttributes();  // clear any default values
    		    if(isset($_GET['GisConfig']))
    		        $gismodel->attributes=$_GET['GisConfig'];
    		    
    		    $this->render('gisconfig',array(
    		            'md'=>$md,
    		            'gismodel'=>$gismodel,
    		    ));
		    }
		    else { */
		        $this->render('sysconfig',array('md'=>$md));
		    //}
		    exit();
		}
		$this->redirect(Yii::app()->createUrl('sysmanage/sysconfig',array('md'=>$md)));
	}
	
	/*
	*GIS地图数据库与最近修改的数据库同步
	*/
	public function actionPgsql_Synchronous()
	{
		set_time_limit(0);
		ini_set('memory_limit', '2048M');
		require_once(Yii::app()->basePath.'/extensions/gisFunctions.php');
		$connect=Yii::app()->db;//mysql连接
		$gisConfig = GISConfig::get();
		$conn_string = $gisConfig->pgConnectStr; //pgsql链接
		$dbconn4 = pg_connect($conn_string) or die ('connection failed');
		$mqsQueryTableName = 'site'; //基站数据表
		$gisTableName = $gisConfig->layer->sitesLayer; //更新地理数据库表名称
		$updateSitePoints = isset($_GET['updateSitePoints']) ? $_GET['updateSitePoints'] : 'false'; //是否更新site_points
		@pg_query("TRUNCATE TABLE $gisTableName;");
		
		if($updateSitePoints == 'true')
		{
			$truncate = "TRUNCATE TABLE site_points";
			$connect->createCommand($truncate)->execute();
		}
		//查询出基站信息

		$allSites = $connect->createcommand("select cell_name,lac,cellId,angle,lng,lat,type from $mqsQueryTableName")->queryAll(); //查询site表信息
		if($allSites) //如果表中有数据
		{
			foreach ($allSites as $siteKey => $siteValue)
				$siteLacCellIds[$siteValue['lac'].'_'.$siteValue['cellId']][] = $siteValue; //将基站以lac+cellid加以区分
			foreach ($siteLacCellIds as $key => $value) //遍历所有基站信息
			{
				$GRRUData = null; //高铁信息
				$insertSql = array(); //pgsql语句
				if (count($value)>1) { //如果为高铁小区
					foreach ($value as $GRRUKey => $GRRUValue){ //遍历多个lac+cellid相同的基站
						$oneGRRUData = getPOLYGONPoints($GRRUValue['lng'],$GRRUValue['lat'],$GRRUValue['angle']); //获取各个基站的绘制点集
						$GRRUData['points'][] = $oneGRRUData['points'];
						$GRRUData['lineX'] = $oneGRRUData['lineX'];
						$GRRUData['lineY'] = $oneGRRUData['lineY'];
					}
					$GRRUData['points'] = implode(')),((', $GRRUData['points']); //多个扇形
					//print_r($GRRUData);exit;
					$value = $GRRUValue; //随机基站作为主信息
					$value['cell_name'] = 'GRRU(高铁基站)'; //统一命名为高铁基站

					
				}else
					$value = $value[0];
				//print_r($value);exit;
				$insertSql = CommonCell($gisTableName, $value['cell_name'], $value['lac'], $value['cellId'], $value['angle'], $value['lng'], $value['lat'], $value['type'],$GRRUData); //生成pgsql插入语句

				//var_dump($insertSql);exit;
				if(isset($insertSql[1])) //如果生成的数据有效
				{
					//print_r($insertSql[0]);exit;
					@pg_query($insertSql[0]); //插入pgsql数据库
					if($updateSitePoints == 'true') //如果更新site_points表
					{
						$thisPoint = $value['lac'].','.$value['cellId'];
						$sitePointsModel = new SitePoints;
						$sitePointsModel->attributes = array('laccellid'=>$thisPoint,'pointdata'=>$insertSql[1],'centerlng'=>$insertSql[2],'centerlat'=>$insertSql[3],'type'=>$value['type']);
						$sitePointsModel->save();
					}
				}
			}
		}

		pg_close ($dbconn4);
		
		$this->redirect('index.php?r=sysmanage/dataInput&md=gis',array('success'=>'操作成功')); 
	}
	 

	/**
	 * @name 系统提醒子项主页及添加系统子项页面
	 * @param String $md ('ac','aa','av')=>(alertsCreate,alertsAdmin,alertsView)
	 *
	 * @author zhanghy
	 * @date 2014-02-22 17:29:09
	**/
	public function actionAlertsAdmin()
	{
		if (isset($_GET['md']) && $_GET['md'] === 'ac') //alertsCreate page
		{ 
			$model=new SystemAlerts;
		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model); //ajax提交

			if(isset($_POST['SystemAlerts'])) //from提交后
			{
				$model->attributes=$_POST['SystemAlerts'];
				if($model->save()) //如果保存成功
				{ 
					$_GET['md'] = 'av'; //跳转至alertsView
					$alerts = new Alerts('reset'); //重载系统提醒缓存
				}
			}
		}else{ 
			$_GET['md'] = 'aa'; //alertsAdmin page
			$model=new SystemAlerts('search'); //SystemAlerts()->search()
			$model->unsetAttributes();  // clear any default values
			if(isset($_GET['SystemAlerts']))
			{
				if(isset($_GET['SystemAlerts']['alerts_tag']) && $_GET['SystemAlerts']['alerts_tag'] !== '请输入关键字进行搜索' ) //不为初始值
					$_GET['SystemAlerts']['alerts_tag'] = trim($_GET['SystemAlerts']['alerts_tag']); //前后去空格

				$model->attributes=$_GET['SystemAlerts']; //表单值
			}
		}

		$this->render('alertsAdmin',array(
			'model'=>$model,
			'md'=>$_GET['md']
		));
	}

	/**
	 * @name 删除一个或多个系统管理子项
	 * @param String $_GET['id'] 删除的id
	 *
	 * @author zhanghy
	 * @date 2014-02-22 17:32:05
	**/
	public function actionAlertsDelete()
	{
		$model = new SystemAlerts('search'); //SystemAlerts()->search()
		$model->unsetAttributes(); // clear any default values
		$idArray = null; //初始$idArray
		if(isset($_GET['id'])) //如果有id值
			$idArray = explode(',',$_GET['id']); //将$Stirng $_GET['id'] 拆分为数组
		if ($model->deleteByPk($idArray)) //主键删除
		{
			$alerts = new Alerts('reset'); //更新系统提醒缓存
			HelpTool::getActionInfo($idArray,0);//删除操作
		}
		
		if(count($idArray) == 1 ) //如果删除为一条，跳转至系统提醒主页
			$this->redirect(Yii::app()->createUrl('sysmanage/alertsAdmin'),array('model'=>$model));
	}



	/**
	 * @name 系统管理子项更新
	 * @param int $id 需要更新的系统子项id
	 *
	 * @author zhanghy
	 * @date 2014-02-22 17:37:22
	**/
	public function actionAlertsUpdate($id)
	{
		$model=$this->loadAlertsModel($id);

		// Uncomment the following line if AJAX validation is needed
		// $this->performAjaxValidation($model);

		if(isset($_POST['SystemAlerts']))
		{
			$model->attributes=$_POST['SystemAlerts'];
			if($model->save()) //如果保存成功
			{
				$alerts = new Alerts('reset'); //更新系统提醒缓存
				$this->redirect(array('alertsView','id'=>$model->id)); //跳转至alertsView
			}
		}

		$this->renderPartial('alertsUpdate',array(
			'model'=>$model,
		));
	}

	/**
	 * @name 系统管理子项详情.
	 * @author zhanghy
	 */
	public function actionAlertsView($id)
	{
		$this->renderPartial('alertsView',array(
			'model'=>$this->loadAlertsModel($id),
		));
	}

	/**
	 * @name 系统提醒修改及配置中转action
	 * @param String $_GET['alertkey'] 系统提醒子项关键字
	 * @param String $_GET['alertdisplay'] 系统提醒子项显示状态
	 * @param String $_GET['alerts_link'] 系统提醒子项预设跳转链接
	 * @param String $_GET['setalert'] 系统提醒子项显示设置
	 * @param String $_GET['alertreturn'] 系统提醒子项修改完毕点击返回
	 *
	 * @author zhanghy
	 * @date 2014-02-20 12:24:20
	**/
	public function actionAlertsManage()
	{
		$alerts = new Alerts(); //获取所有系统提醒子项信息
		if (isset($_GET['alertkey']) && !!$_GET['alertkey']) { //如果提交alertkey并且为有效值
			if (isset($_GET['alertdisplay'])) //如果修改系统提醒子项状态值
			{
				$alertDisplay = $_GET['alertdisplay'] === 'checked' ? true : false; //判断修改后的值
				if( $alerts->updateAlert($_GET['alertkey'],null,null,$alertDisplay) ) //更新系统提醒数据库与缓存
					echo "保存成功!";
				else
					echo "保存失败，请刷新页面后重试！";
			}elseif (isset($_GET['link'])) { //如果是点击系统提醒栏系统提醒子项事件

				$link = $alerts->alerts[$_GET['alertkey']]['alerts_link']; //获取该提醒子项地址
				if ( $alerts->updateAlert($_GET['alertkey'],null,null,false,true) )  //点击系统提醒子项后更新缓存，但不更新数据库
					$this->redirect(Yii::app()->createUrl($link)); //跳转至系统系统提醒子项预设页面
			}
		}elseif(isset($_POST['newAlert'])||isset($_GET['newAlert'])){
			$data=json_decode(base64_decode($_GET['newAlert']),true);
			$return=$alerts->updateAlert($data['alerts_key'],intval($data['status_change']),$data['isCover'],$data['alerts_display']);
			if($return===true) echo 'ok';
			else echo 'error';
			return '';
		}else{
			$allAlerts = array();
			if(isset($_POST['setalert'])) //如果是系统提醒子项显示设置请求
				$allAlerts = $alerts->queryAlerts(true,true); //请求数据库中所有系统提醒子项状态
			elseif(isset($_POST['alertreturn'])) //如果系统提醒子项修改完毕点击返回按钮
				$allAlerts = $alerts->queryAlerts(false,true); //获取当前系统提醒缓
			
				$alertsDisplay = array(); //系统提醒子项显示状态数组
				foreach($allAlerts as $alertkey=>$alertVal)
					$alertsDisplay[$alertkey] = $alertVal['alerts_display']; //系统提醒子项关键字=>该系统子项显示状态
				echo json_encode($alertsDisplay); //返回json编码的该数组
			
		}
	}
	
	/**
	 * @name 片区信息管理
	 * @author 曹芳
	 * @date 2014-04-18
	**/
	public function actionregionManage()
	{		
		$model = new RegionInfo();
		$this->render('regionManage',
			Array(
				'model'=>$model,
			)
		);
	}
	/**
	 * @name 片区信息导入
	 * @author 曹芳
	 * @date 2014-04-22
	**/
	public function actionregionInfoUpdate(){
		set_time_limit(0);
		//error_reporting(0);
		require_once(Yii::app()->basePath.'/output/common/PHPExcel.php'); 
		require_once(Yii::app()->basePath.'/output/common/PHPExcel/IOFactory.php');
		require_once(Yii::app()->basePath.'/output/common/MysqlClass.php');
		require_once(Yii::app()->basePath.'/output/common/PHPExcel/Reader/Excel2007.php');//读取excel07类
		require_once(Yii::app()->basePath.'/output/common/PHPExcel/Reader/Excel5.php');//读取excel03类
		$colname=array('id','city','district','contacts','isreply');
		$col_str = "city,district,contacts,isreply";
		$col_count= count($colname)-1;
		$error="";
		if(isset($_FILES['regionexcel'])){
			$excel_keyWord='regionexcel';
			$table_name="region_info";
			if(!empty($_FILES[$excel_keyWord]['name'])){
				if(($_FILES[$excel_keyWord]['size']/1024/1024)<5){
					$file_name= Yii::app()->basePath.'/input/'.$_FILES[$excel_keyWord]['name'];
					move_uploaded_file($_FILES[$excel_keyWord]['tmp_name'],$file_name);
					if($_FILES[$excel_keyWord]['type']=='application/vnd.ms-excel'||$_FILES[$excel_keyWord]['type']=='application/force-download'){
						$objReader = PHPExcel_IOFactory::createReader('Excel5');//使用软件2003格式  
					}elseif($_FILES[$excel_keyWord]['type']=='application/octet-stream'){
						$error=json_encode('错误：您所操作的表格正被其他程序占用，请关闭该程序后再试');
						$this->redirect('index.php?r=sysmanage/regionManage&error='.$error);
					}else{
						$error=json_encode('错误：导入文件格式不正确，请选择03版EXCEL文件为导入文件');
						$this->redirect('index.php?r=sysmanage/regionManage&error='.$error);
					}
					try{
						if(count($objReader->listWorksheetNames($file_name))=='1'){//excel里面是单张表
							$objPHPExcel = $objReader->load($file_name);        //open excel
							$objWorksheet = $objPHPExcel->getActiveSheet();  
							$highestRow = $objWorksheet->getHighestRow();//row
							$highestColumn = $objWorksheet->getHighestColumn();//col       
							$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);//总列数 
							// var_dump($highestColumnIndex);
							// var_dump('111111');
							// var_dump($col_count);
							if($highestColumnIndex == $col_count ){
								//提取表格数据查重插入数据
								$_data=array();
								$sql_string=array();
								for ($row=2;$row <= $highestRow;$row++){
									
									$strs=array();
									for ($col = 0;$col < $col_count;$col++){
										$getVal=NULL;
										$getVal=$objWorksheet->getCellByColumnAndRow($col,$row)->getValue();
										if($getVal instanceof PHPExcel_RichText)     //富文本转换字符串  
											$strs[$colname[$col+1]] = $getVal->__toString();
										 else
											$strs[$colname[$col+1]]=$getVal;		
									}
									if($strs['city']!==null && $strs['district']!==null && $strs['contacts']!==null && $strs['isreply']!==null){
										array_push($_data,$strs);	
										array_push($sql_string,"('".implode("','",$strs)."')");	
									}else{
										$strs1 = array();
										if($strs['city'] === null){
											$strs1['error'] = '"市"单元格内容为空';
										}
										if($strs['district'] === null){
											if(isset($strs1['error']) && !empty($strs1['error']))
												$strs1['error'] .= ' & "区、县"单元格内容为空';
											else 
												$strs1['error'] = '"区、县"单元格内容为空';
										}
										if($strs['contacts'] === null){
											if(isset($strs1['error']) && !empty($strs1['error']))
												$strs1['error'] .= ' & "投诉联系人"单元格内容为空';
											else 
												$strs1['error'] = '"投诉联系人"单元格内容为空';
										}
										if($strs['isreply'] === null){
											if(isset($strs1['error']) && !empty($strs1['error']))
												$strs1['error'] .= ' & "是否回复"单元格内容为空';
											else 
												$strs1['error'] = '"是否回复"单元格内容为空';
										}
										if(!empty($strs1)){
											$strs1['id']=$row;
											$cell_error[]=$strs1;
										}
									}
								}
								
								/**
								*将合法的数据导入到region_info表中（不插入重复的）
								*/
								$all=count($sql_string);
								$delRepeatCount = 0;//表示重复的记录数
								$temp_all = $all;	
								if($temp_all>0){
									$_col_str = explode(",",$col_str);
									$where = "";
									foreach($_col_str as $k4=>$val4){
										$where .= "t1.".$val4."= t2.".$val4." AND ";
									}
									$delete=Yii::app()->db->createCommand("TRUNCATE TABLE ".$table_name)->execute();//先清空之前的信息				
									$rs=Yii::app()->db->createCommand("INSERT INTO `".$table_name."`(".$col_str." )VALUES ".implode(',',$sql_string).';')->execute();  
									$Repeat=Yii::app()->db->createCommand("SELECT t1.id as repeatId FROM `".$table_name."` AS `t1` , (SELECT DISTINCT MIN( `id` ) AS `id` , ".$col_str."
									FROM `".$table_name."` GROUP BY ". $col_str ."
									HAVING COUNT( * ) >1 ) AS `t2`
									WHERE ".$where." `t1`.`id` <> `t2`.`id`")->queryAll();
									$deleteRepeatId=array();
									foreach($Repeat as $k=>$v){
										array_push($deleteRepeatId,$v['repeatId']);
									}
									$delRepeatCount = count($deleteRepeatId);
									$all = $all- $delRepeatCount;
									$deleteRepeat=Yii::app()->db->createCommand("delete from `".$table_name."` where id in ('".implode("','",$deleteRepeatId)."')")->execute();
								}
								if(isset($cell_error)&&!empty($cell_error)){
									foreach($cell_error as $key_o=>$val_o)
										$val_o['id']=$key_o; 
									$cell_error_jason=json_encode($cell_error);
									$excel_error_model=new ExcelErrorInfo;
									$excel_error_model->unsetAttributes(); 
									$update['id']='';
									$update['error_jason']=$cell_error_jason;
									$update['time']=date('Y-m-d H:i:s');
									$update['operate']='覆盖';
									$error='导入：'.($all).'条数据,但有：'.count($cell_error).'条数据解析错误';
									$update['state']=$error;
									$excel_error_model->attributes=$update; 
									$excel_error_model->save();
									$test_tag=$update['time'];
									$tests=isset($test_tag)?'&tabla_text='.$test_tag:'';
									$this->redirect('index.php?r=sysmanage/regionManage&error='.json_encode($error).$tests);  	
								}else{
									$excel_error_model=new ExcelErrorInfo;
									$excel_error_model->unsetAttributes(); 
									$update['id']='';
									$update['error_jason']='';
									$update['time']=date('Y-m-d H:i:s');
									$update['operate']='覆盖';
									if($temp_all == 0){
										$success='您所选择的excel表格为空，操作不成功！';
										$url = 'index.php?r=sysmanage/regionManage&error='.json_encode($success);
									}elseif($delRepeatCount!==0){
										$success='成功为您导入：'.($all).'条数据，有'.($delRepeatCount).'条数据重复未导入';
										$url = 'index.php?r=sysmanage/regionManage&success='.json_encode($success);
									}else{
										$success='成功为您导入：'.($all).'条数据';
										$url = 'index.php?r=sysmanage/regionManage&success='.json_encode($success);
									}
									$update['state']=$success;
									$excel_error_model->attributes=$update; 
									$excel_error_model->save();
									$this->redirect($url);
								}
							}else{
								$error='错误：导入表格格式不正确，请参考表格范本';
							}
						}else {
							if(count($objReader->listWorksheetNames($file_name))>1){
								foreach($objReader->listWorksheetNames($file_name) as $tabval)
									$error	.='《'.$tabval.'》';
								$error='错误：您上传的表格中有'.$error.count($objReader->listWorksheetNames($file_name)).'张表格，为避免错误请将他们分开操作';
							}else if(count($objReader->listWorksheetNames($file_name))=='0'){
								$error='错误：您上传的excel中没有表格';
							}
						}
					}catch(Exception $e){
						$json_text=json_encode('错误：表格有误，请检查您表格的内容和文件格式');
						$this->redirect('index.php?r=sysmanage/regionManage&error='.$json_text);
					}
				}else{
					$error='上传文件不得大于2M';
				}
			}else {
				$error = '错误：未选择要上传的表格';
			}
			if (empty ( $error ) && ! isset ( $success )) {
				$this->render('regionManage');
			} else {
				$error = isset ( $error ) ? '&error=' . json_encode ( $error ) : '';
				$this->redirect ( 'index.php?r=sysmanage/regionManage' . $error );
			}
		}	
	}
	/**
	 * @name 修改、添加、删除片区信息
	 * @author 曹芳
	 * @date 2014-04-21
	**/
	public function actionchangeRegionInfo()
	{
		$model = new RegionInfo();
		$error = "";
		if(isset($_POST['addRegionInfo'])){
			HelpTool::getActionInfo(0,2);//新建操作
			
			$city = trim($_POST['addRegionInfo']['city']);
			$district = trim($_POST['addRegionInfo']['district']);
			if( $city == "未选择城市" ){
				$error = "未选择城市<br>";
			}
			$rec_region = RegionInfo::model()->findAll();
			foreach( $rec_region as $k=>$val ){
				$val = $val->attributes;
				$region = $val['city'].$val['district'];
				$currentRegion = $city.$district;
				if( $currentRegion == $region ){
					$error = $error."输入的区域已经存在<br>";
					break;
				}
			}
			
			$contacts = trim($_POST['addRegionInfo']['contacts']);
			$contacts_length = strlen($contacts);
			if( empty($contacts) ){
				$error = $error.'"投诉联系人"不能为空<br>';
			}elseif ( $contacts_length > 200 ){
				$error = $error . '"投诉联系人"长度不能超过200<br>';
			}
			if( $error == ""){
				$condition['city'] = $city;
				$condition['district'] = $district;
				$condition['contacts'] = $contacts;
				if($_POST['addRegionInfo']['isreply'] ==1 )
					$condition['isreply'] = "是";
				elseif( $_POST['addRegionInfo']['isreply'] ==2)
					$condition['isreply'] = "否";
				$model->attributes = $condition;
				if($model->save());
				$model = RegionInfo::model()->findByAttributes($condition);
				$this->renderPartial('changeRegionInfoView',array(
					'model'=>$model,
				));
			}else{
				$this->renderPartial('changeRegionInfo',array(
					'error'=>$error,
					'type'=>0,
				));
			}
		}elseif(isset($_POST['modifyRegionInfo'])){
			HelpTool::getActionInfo(0,1);//更新操作
			
			$contacts = trim($_POST['modifyRegionInfo']['contacts']);
			$contacts_length = strlen($contacts);
			if( empty($contacts) ){
				$error = '"投诉联系人"不能为空<br>';
			}elseif ( $contacts_length > 200 ){
				$error = '"投诉联系人"长度不能超过200';
			}
			if( $error == ""){
				$condition['contacts'] = $_POST['modifyRegionInfo']['contacts'];
				if($_POST['modifyRegionInfo']['isreply'] ==1 )
					$condition['isreply'] = "是";
				elseif( $_POST['modifyRegionInfo']['isreply'] ==2)
					$condition['isreply'] = "否";
				if($model->updateByPk($_POST['modifyRegionInfo']['id'],$condition));
				$model = RegionInfo::model()->findByPk($_POST['modifyRegionInfo']['id']);
				$this->renderPartial('changeRegionInfoView',array(
					'model'=>$model,
				));
			}else{
				$model = RegionInfo::model()->findByPk($_POST['modifyRegionInfo']['id']);
				$this->renderPartial('changeRegionInfo',array(
					'model'=>$model,
					'error'=>$error,
					'type'=>1,
				));
			}
		}elseif(isset($_GET['id'])){
			$id = $_GET['id'];
			$type = $_GET['type'];
			if( $type == '0' ){
				HelpTool::getActionInfo(0,2);//新建操作
				$this->renderPartial('changeRegionInfo',array(
					'type'=>0, //表示添加片区信息
				));
			}elseif( $type == '1' ){
				HelpTool::getActionInfo(0,4);//访问操作
				$model = RegionInfo::model()->findByPk($id);
				$this->renderPartial('changeRegionInfo',array(
					'model'=>$model,
					'type'=>1, //表示修改片区信息
				));
			}elseif( $type == '2' ){
				HelpTool::getActionInfo(0,0);//删除操作
				if(RegionInfo::model()->deleteByPk($id));
			}
		}else{
			$this->redirect('index.php?r=sysmanage/regionManage');
		}
	}
	
	
	/**
	 * @name 更新表grid_bussiness_info
	 * @author 曹芳
	 * @date 2014-07-08
	**/
	public function actiongridBussinessUpdate(){
		set_time_limit(0);
		$thisMonth = date('Y-m');
		$month1 = date('Y-m',strtotime($thisMonth."- 1 months")); //3个月前
		$month2 = date('Y-m',strtotime($thisMonth."- 2 months")); //3个月前
		$month3 = date('Y-m',strtotime($thisMonth."- 3 months")); //3个月前
		$time =  isset($_GET['time']) ? $_GET['time'] : '';
		if( $time == 1 ){
			$time = $month1;
		}elseif( $time == 2 ){
			$time = $month2;
		}elseif( $time == 3 ){
			$time = $month3;
		}
		
		Yii::app()->db->createCommand("delete from grid_bussiness_info where time < '{$month3}'")->execute();
		Yii::app()->db->createCommand("delete from grid_bussiness_info where time = '{$time}'")->execute();
		
		$siteInfo = Yii::app()->db->createCommand("select A.id,A.gridId,A.lac,A.cellId,A.type,B.speechTraffic,B.dataTraffic,B.wirelessRate,B.time from site A left join site_bussiness_info B on A.lac = B.lac and A.cellId =B.cellId and B.time = '{$time}' ")->queryAll();
		
		$grid_site_bussiness = array();
		foreach( $siteInfo as $k=>$val ){
			if( !empty( $val['gridId'] ) ){
				// if( !isset( $grid_site_bussiness[$val['gridId']] ) ){
					// $grid_site_bussiness[$val['gridId']] = array();
				// }
				if( $val['type'] == 0 || $val['type'] == 2 ){//2g小区
					if( !isset($grid_site_bussiness[$val['gridId']]['gsm']) ){
						$grid_site_bussiness[$val['gridId']]['gsm'] = array();
					}
					if( !isset($grid_site_bussiness[$val['gridId']]['gsm'][$val['lac'].'_'.$val['cellId']]) ){
						$grid_site_bussiness[$val['gridId']]['gsm'][$val['lac'].'_'.$val['cellId']] = $val;
					}
				}elseif( $val['type'] == 1 ){//3g小区
					if( !isset($grid_site_bussiness[$val['gridId']]['td']) ){
						$grid_site_bussiness[$val['gridId']]['td'] = array();
					}
					if( !isset($grid_site_bussiness[$val['gridId']]['td'][$val['lac'].'_'.$val['cellId']]) ){
						$grid_site_bussiness[$val['gridId']]['td'][$val['lac'].'_'.$val['cellId']] = $val;
					}
				}elseif( $val['type'] == 4 ){//4g小区
					if( !isset($grid_site_bussiness[$val['gridId']]['four']) ){
						$grid_site_bussiness[$val['gridId']]['four'] = array();
					}
					if( !isset($grid_site_bussiness[$val['gridId']]['four'][$val['lac'].'_'.$val['cellId']]) ){
						$grid_site_bussiness[$val['gridId']]['four'][$val['lac'].'_'.$val['cellId']] = $val;
					}
				}
			}else{
				continue;
			}
		}
		// foreach( $grid_site_bussiness as $k=>$v ){
			// var_dump( $k . '_____________________________________');
			// var_dump($v);
		// }
		// exit;
		// var_dump($grid_site_bussiness);
		$grid_site_bussinessInfo = array();
		foreach( $grid_site_bussiness as $key=>$val ){
			$num_2g=0;$num_3g=0;$num_4g=0;
			$speechTraffic_2g = 'null';$speechTraffic_3g = 'null';$dataTraffic_2g = 'null';$dataTraffic_3g = 'null';$dataTraffic_4g = 'null';$wirelessRate_2g = 'null';$wirelessRate_3g = 'null';$wirelessRate_4g = 'null';
			$speechTraffic_3_2g = 'null';$dataTraffic_3_2g = 'null';$dataTraffic_4_3g = 'null';$dataTraffic_4_2g = 'null';
			foreach( $val as $k => $v ){
				if( $k == 'gsm' ){
					$site_2g = Yii::app()->db->createCommand("select * from site where gridId = {$key} and (type = 0 or type = 2)")->queryAll();
					$num_2g = count($site_2g);
					foreach( $v as $value){
						// if(!isset($speechTraffic_2g)){
							// $speechTraffic_2g = $value['speechTraffic'];
						// }
						// if(!isset($dataTraffic_2g)){
							// $dataTraffic_2g = $value['dataTraffic'];
						// }
						// if(!isset($wirelessRate_2g)){
							// $wirelessRate_2g = $value['wirelessRate'];
						// }
						
						if( !empty($value['speechTraffic']) ){
							$speechTraffic_2g = $speechTraffic_2g + $value['speechTraffic'];
						}
						if( !empty($value['dataTraffic']) ){
							$dataTraffic_2g = $dataTraffic_2g + $value['dataTraffic'];
						}
						if( !empty($value['wirelessRate'])  ){
							$wirelessRate_2g = $wirelessRate_2g + $value['wirelessRate'];
						}
					}
					
					if( $wirelessRate_2g !== 'null' && !empty($num_2g) ){
						$wirelessRate_2g = $wirelessRate_2g/$num_2g;
					}
				}
				if( $k == 'td' ){
					$site_3g = Yii::app()->db->createCommand("select * from site where gridId = {$key} and type = 1")->queryAll();
					$num_3g = count($site_3g);
					foreach( $v as $value){
						// if(!isset($speechTraffic_3g)){
							// $speechTraffic_3g = $value['speechTraffic'];
						// }
						// if(!isset($dataTraffic_3g)){
							// $dataTraffic_3g = $value['dataTraffic'];
						// }
						// if(!isset($wirelessRate_3g)){
							// $wirelessRate_3g = $value['wirelessRate'];
						// }
						if( !empty($value['speechTraffic']) ){
							$speechTraffic_3g = $speechTraffic_3g + $value['speechTraffic'];
						}
						if( !empty($value['dataTraffic']) ){
							$dataTraffic_3g = $dataTraffic_3g + $value['dataTraffic'];
						}
						if( !empty($value['wirelessRate']) ){
							$wirelessRate_3g = $wirelessRate_3g + $value['wirelessRate'];
						}
					}
					if( $wirelessRate_3g!=='null' && !empty($num_3g) ){
						$wirelessRate_3g = $wirelessRate_3g/$num_3g;
					}
				}
				if( $k == 'four' ){
					$site_4g = Yii::app()->db->createCommand("select * from site where gridId = {$key} and type = 4")->queryAll();
					$num_4g = count($site_4g);
					foreach( $v as $value){
						// if(!isset($dataTraffic_4g)){
							// $dataTraffic_4g = $value['dataTraffic'];
						// }
						// if(!isset($wirelessRate_4g)){
							// $wirelessRate_4g = $value['wirelessRate'];
						// }
						if( !empty($value['dataTraffic']) ){
							$dataTraffic_4g = $dataTraffic_4g + $value['dataTraffic'];
						}
						if( !empty($value['wirelessRate']) ){
							$wirelessRate_4g = $wirelessRate_4g + $value['wirelessRate'];
						}
					}
					if( $wirelessRate_4g !== 'null' && !empty($num_4g) ){
						$wirelessRate_4g = $wirelessRate_4g/$num_4g;
					}
				}
			}
			
			if( $num_2g === 0 ){
				$speechTraffic_2g = 0;
				$dataTraffic_2g = 0;
				$wirelessRate_2g = 0;
			}
			if( $num_3g === 0 ){
				$speechTraffic_3g = 0;
				$dataTraffic_3g = 0;
				$wirelessRate_3g = 0;
			}
			if( $num_4g === 0 ){
				$dataTraffic_4g = 0;
				$wirelessRate_4g = 0;
			}
			// var_dump($speechTraffic_2g);
			// var_dump($speechTraffic_3g);
			// var_dump(empty($speechTraffic_2g));
			// var_dump(empty($speechTraffic_3g));exit;
			if( $speechTraffic_3g !=='null' && !empty($speechTraffic_2g) && $speechTraffic_2g !== 'null' ){
				$speechTraffic_3_2g = $speechTraffic_3g/$speechTraffic_2g;
			}
			if( $dataTraffic_3g !== 'null' && !empty($dataTraffic_2g) && $dataTraffic_2g !== 'null' ){
				$dataTraffic_3_2g = $dataTraffic_3g/$dataTraffic_2g;
			}
			if( $dataTraffic_4g !== 'null' && !empty($dataTraffic_3g) && $dataTraffic_3g !== 'null' ){
				$dataTraffic_4_3g = $dataTraffic_4g/$dataTraffic_3g;
			}
			if( $dataTraffic_4g !== 'null' && !empty($dataTraffic_2g) && $dataTraffic_2g !== 'null' ){
				$dataTraffic_4_2g = $dataTraffic_4g/$dataTraffic_2g;
			}
			
			$siteInfo_exist = Yii::app()->db->createCommand("select * from grid_bussiness_info where gridId = {$key} and time = '{$time}' ")->queryAll();
			
			if( empty($siteInfo_exist) ){
				$insertSql = "insert into grid_bussiness_info(gridId,num_2g,num_3g,num_4g,time,speechTraffic_2g,speechTraffic_3g,speechTraffic_3_2g,dataTraffic_2g,dataTraffic_3g,dataTraffic_4g,dataTraffic_3_2g,dataTraffic_4_3g,dataTraffic_4_2g,wirelessRate_2g,wirelessRate_3g,wirelessRate_4g,updateTime) values ({$key},{$num_2g},{$num_3g},{$num_4g},'{$time}',{$speechTraffic_2g},{$speechTraffic_3g},{$speechTraffic_3_2g},{$dataTraffic_2g},{$dataTraffic_3g},{$dataTraffic_4g},{$dataTraffic_3_2g},{$dataTraffic_4_3g},{$dataTraffic_4_2g},{$wirelessRate_2g},{$wirelessRate_3g},{$wirelessRate_4g},now()) ";
				 Yii::app()->db->createCommand($insertSql)->execute();
			}else{
				$updateSql = "update grid_bussiness_info set num_2g = {$num_2g},num_3g={$num_3g},num_4g={$num_4g},speechTraffic_2g={$speechTraffic_2g},speechTraffic_3_2g={$speechTraffic_3_2g},dataTraffic_2g={$dataTraffic_2g},dataTraffic_3g={$dataTraffic_3g},dataTraffic_4g={$dataTraffic_4g},dataTraffic_3_2g={$dataTraffic_3_2g},dataTraffic_4_3g={$dataTraffic_4_3g},dataTraffic_4_2g={$dataTraffic_4_2g},wirelessRate_2g={$wirelessRate_2g},wirelessRate_3g={$wirelessRate_3g},wirelessRate_4g={$wirelessRate_4g},updateTime=now() where gridId = {$key} and time = '{$time}' ";
				 Yii::app()->db->createCommand($updateSql)->execute();
			}	
		}
		$this->redirect('index.php?r=sysmanage/speechTrafficInput&md=update&success="操作成功"'); 
	}
	
	
}