<?php
/**
 * 公共数据缓存类
 */
class CacheFile extends CController {
	/**
	 *
	 * @name 查询手机品牌、型号和对应的中文名并写入缓存
	 * @return 返回一个含品牌、型号和对应的中文名的多维数组
	 */
	public static function phoneBrand() {
		$name = 'phoneBrand';
		$value = Yii::app ()->cache->get ( $name );
		if ($value) {
			return $value;
		}
		
		$connection = Yii::app ()->db;
		$sql = "SELECT DISTINCT t1.phoneBrand,t1.phoneModel,t2.phoneBrandCN FROM `static_information` t1, `phone_brand` t2 WHERE t1.phoneBrand=t2.phoneBrand";
		$command = $connection->createCommand ( $sql );
		$row = $command->queryAll ();
		
		foreach ( $row as $val ) {
			$pb [] = $val ['phoneBrand'];
		}
		$pb = array_unique ( $pb );
		foreach ( $row as $val ) {
			if (in_array ( $val ['phoneBrand'], $pb )) {
				$rs [$val ['phoneBrand']] ['phoneBrandCN'] = $val ['phoneBrandCN'];
				$rs [$val ['phoneBrand']] ['phoneModel'] [] = $val ['phoneModel'];
			}
		}
		Yii::app ()->cache->set ( $name, $rs, 86400 );
		return $rs;
	}
	
	/**
	 *
	 * @name 终端品牌中文名
	 * @return 返回键为手机英文品牌，值为中文的数组
	 */
	public static function getPhoneBrandCN() {
		$name = 'cache_phoneBrandCN';
		$value = Yii::app ()->cache->get ( $name );
		if ($value) {
			return $value;
		} else {
			$connection = Yii::app ()->db;
			$sqlGroupData = "select phoneBrand,phoneBrandCN from phone_Brand";
			$command = $connection->createCommand ( $sqlGroupData );
			$groupDataRow = $command->queryAll ();
			
			foreach ( $groupDataRow as $phoneBrandKey => $phoneBrandVal ) {
				$groupData [strtolower ( $phoneBrandVal ['phoneBrand'] )] = $phoneBrandVal ['phoneBrandCN'];
			}
			
			Yii::app ()->cache->set ( $name, $groupData, 86400 );
			
			return $groupData;
		}
	}
	
	/**
	 *
	 * @name 运营商列表
	 * @return 返回运营商列表数组
	 */
	public static function mobileType() {
		return array (
				'中国移动',
				'中国联通',
				'中国电信' 
		);
	}
	
	/**
	 *
	 * @name 数据接收方列表
	 * @return 返回数据接收方列表数组
	 */
	public static function operatorList() {
		$name = 'operatorList';
		$value = Yii::app ()->cache->get ( $name );
		if ($value) {
			return $value;
		}
		
		$connection = Yii::app ()->db;
		$sql = "SELECT title,id FROM `operator_list`";
		$command = $connection->createCommand ( $sql );
		$row = $command->queryAll ();
		
		foreach ( $row as $v ) {
			$rs [$v ['id']] = $v ['title'];
		}
		Yii::app ()->cache->set ( $name, $rs, 86400 );
		return $rs;
	}
	
	/**
	 *
	 * @name 查询缓存类型并写入缓存
	 * @return 返回一个键为英文，值为中文的日志搜索二维数组
	 * @delete delete Yii::app()->cache->delete('cache_logsSearch'.$thisUserRole);
	 */
	public static function logsSearch($delete=false) {
		$thisUserRole = isset( $_SESSION['userRole'] ) ? $_SESSION['userRole'] : HelpTool::getThisIdRole(Yii::app()->user->getId()) ; //用户角色
		$name = 'cache_logsSearch'.$thisUserRole;
		$value = Yii::app ()->cache->get ( $name );
		$isAdmin = HelpTool::isAdmin(); //是否为Admin
		if ($value && $delete==false) {
			return $value;
		} else {
			$logsSearch=array();
			$getAllRoles = HelpTool::getGroupData ( 'view_action_logs', 'userrole' ); // 所有用户组
			$getAllCategory = HelpTool::getGroupData ( 'view_action_logs', 'actiontype' ); // 所有类型
			                                                                                  
			// 获取用户组
			foreach ( $getAllRoles as $rolesKey => $rolesVal ) 
			{
				if(  $rolesVal  != $isAdmin['Admin'] || $isAdmin['flag'] == true)
				{
					$logsSearch ['userrole'] [$rolesVal] = HelpTool::getAllRolesTrans ( $rolesVal );
				}
			}
			
			// 获取日志类型
			$connection = Yii::app ()->db;
			$sqlGroupData = "select  actiontype,userrole  from  view_action_logs group by actiontype,userrole";
			$command = $connection->createCommand ( $sqlGroupData );
			$groupDataRow = $command->queryAll ();
			
			foreach( $groupDataRow as $val )
			{
				$isAdmin = HelpTool::isAdmin();
				if( (  $val['userrole']  != $isAdmin['Admin'] ) || ( $isAdmin['flag'] == true ))
				{
					$logsSearch ['actiontype'] [$val['actiontype']] = HelpTool::getLogTypeTrans( $val['actiontype'] );
				}
			}
			
			Yii::app ()->cache->set ( $name, $logsSearch, 86400 );
			
			return $logsSearch;
		}
	}
	
	/**
	 *
	 * @name 查询缓存类型并写入缓存
	 * @return 返回一个用户搜索多维数组
	 * @delete Yii::app()->cache->delete('cache_userSearch'.$thisUserRole);  
	 */
	public static function userSearch($delete=false) {
		$thisUserRole = isset( $_SESSION['userRole'] ) ? $_SESSION['userRole'] : HelpTool::getThisIdRole(Yii::app()->user->getId()) ; //用户角色
		$name = 'cache_userSearch'.$thisUserRole;
		$value = Yii::app ()->cache->get ( $name );
		$isAdmin = HelpTool::isAdmin(); //是否为Admin
		if ($value && $delete==false) {
			return $value;
		} else {
			$groupData = array();
			$getAllRoles = HelpTool::getGroupData ( 'manage_list', 'itemname' ); // 获取用户组
			
			// 查询用户类型分组
			foreach ( $getAllRoles as $rolesKey => $rolesVal ) 
			{
				if( (  $rolesVal  != $isAdmin['Admin'] ) || ( $isAdmin['flag'] == true ))
				{ 
					$groupData ['userrole'] [$rolesVal] = HelpTool::getAllRolesTrans ( $rolesVal );
				}
			}
			
			// 查询注册地址分组
			$connection = Yii::app ()->db;
			$sqlGroupData = "select  province,city,itemname  from  manage_list group by province,city,itemname";
			$command = $connection->createCommand ( $sqlGroupData );
			$groupDataRow = $command->queryAll ();

			foreach( $groupDataRow as $val )
			{
				$isAdmin = HelpTool::isAdmin();
				if( (  $val['itemname']  != $isAdmin['Admin'] ) || ( $isAdmin['flag'] == true ))
				{
					$groupData ['address'] [$val['province']] [$val['city']] = $val['city'];
				}
			}
			
			Yii::app ()->cache->set ( $name, $groupData, 86400 );
			
			return $groupData;
		}
	}
	
	/**
	 *
	 * @name 获取地市数据
	 * @return 返回地市多维数组
	 */
	public static function getCity($province) 
	{
		$rs = array ();
		$name = 't_city_' . $province;
		$value = Yii::app ()->cache->get ( $name );
		if ($value) {
			return $value;
		}
		$connection = Yii::app ()->db;
		if ($province != "所有城市") {
			$sql = "select DISTINCT t1.id,t1.region_name from province_city t1, province_city t2,static_information t3 where t2.region_name = '" . $province . "' and t1.id=t3.province_city_id and t1.parent_id=t2.id";
			$command = $connection->createCommand ( $sql );
			$row = $command->queryAll ();
			foreach ( $row as $k => $v ) {
				$rs [$v ['id']] = $v ['region_name'];
			}
		} else {
			$sql = "SELECT DISTINCT t1.region_name,t1.id FROM `province_city` t1, `static_information` t2 WHERE t1.id = t2.province_city_id";
			$command = $connection->createCommand ( $sql );
			$row = $command->queryAll ();
			foreach ( $row as $k => $v ) {
				$rs [$v ['id']] = $v ['region_name'];
			}
		}
		Yii::app ()->cache->set ( $name, $rs, 86400 );
		return $rs;
	}
	
	/**
	 *
	 * @name 数据接收方已分配的终端品牌、型号列表
	 * @return 返回键为包含手机品牌、型号的数组
	 * @delete delete Yii::app()->cache->delete('cache_operatorTerminal_' . $operatorId);
	 */
	public static function getOperatorTerminalCache($operatorId) {
		$name = 'cache_operatorTerminal_' . $operatorId;
		$value = Yii::app ()->cache->get ( $name );
		if ($value) {
			return $value;
		} else {
			$connection = Yii::app ()->db;
			$sql = " SELECT phoneBrand,phoneModel FROM `static_information` where operator_list_id='" . $operatorId . "' ";
			$command = $connection->createCommand ( $sql );
			$row = $command->queryAll ();
			
			$groupData = array ();

			foreach($row as $phoneBrandKey=> $phoneBrandVal )
			{
				$groupData[  strtolower( $phoneBrandVal['phoneBrand'] ) ] [  strtolower( $phoneBrandVal['phoneModel'] ) ] = $phoneBrandVal['phoneModel'] ;
			}
			Yii::app ()->cache->set ( $name, $groupData, 86400 );
			return $groupData;
		}
	}
	
	/**
	 * @name 口径类型
	 * @return 返回口径类型数组
	 * @create date 2013-06-16 11:26:55
	 */
	public static function getStatementType() 
	{
		return array (
				'基站迫迁'=>'基站迫迁',
				'弱覆盖'=>'弱覆盖',
				'故障抢修'=>'故障抢修',
				'网络调整'=>'网络调整'
		);
	}
	
	/**
	 * @name 项目状态
	 * @return 返回项目状态数组
	 * @create date 2013-06-16 11:26:32
	 */
	public static function getProjectStatus() 
	{
		return array (
				'已完成'=>'已完成',
				'已解决'=>'已解决',
				'堪站'=>'堪站',
				'宏站'=>'宏站',
				'室分'=>'室分',
				'规划建设中'=>'规划建设中',
				'暂不解决'=>'暂不解决',
				'未完成'=>'未完成',
				'未选址，主城区选址困难'=>'未选址，主城区选址困难',
				'设备更换完成，待复测'=>'设备更换完成，待复测',
		);
	}
	
	/**
	*
	* @name 口径管理搜索缓存
	* @return 返回一个包含口径搜索信息的多维数组
	* @author 张洪源
	* @create time 2013-05-29 10:09
	* @rewrite time 2013-06-16 11:26:13
	*/
	 
	public static function statementSearch($delete=false)
	{
		$thisUserRole = isset( $_SESSION['userRole'] ) ? $_SESSION['userRole'] : HelpTool::getThisIdRole(Yii::app()->user->getId()) ; //用户角色
		$name = 'cache_statementSearch'.$thisUserRole;
		$value = Yii::app ()->cache->get ( $name );
		if ($value && $delete==false) {
			return $value;
		} else {
			$statementSearch=array();
			$statementSearch['state_type'] = HelpTool::getGroupData ( 'complain_statement_list', 'state_type' ); // 所有口径类型
			$statementSearch['project_status'] = HelpTool::getGroupData ( 'complain_statement_list', 'project_status' ); // 所有项目状态
			Yii::app ()->cache->set ( $name, $statementSearch, 86400 );
			return $statementSearch;
		}
	}
	
	/**
	 *
	 * @name 账户在线控制类缓存
	 * @return 返回值为布尔值 true为可以登录  false为不可登录
	 */
	 
	 public static function getUserState( $logout = false )
	 {
		$loginControl = Yii::app()->params->loginControl; //在线人数控制配置数组
		
		if($loginControl['debug'] == false) //判断debug是否开启
		{
			$userLogoutTime = Yii::app()->params->loginControl['cacheDestroyTime']; //用户控制缓存销毁时间
			$userInfo = array(); // 用户信息数组
			$userName = Yii::app()->user->name; //用户名
			$name = 'loginLimit_'.$userName; //缓存名称
			$thisUserRole = isset( $_SESSION['userRole'] ) ? $_SESSION['userRole'] : HelpTool::getThisIdRole(Yii::app()->user->getId()) ; //用户角色
			$sessionId = Yii::app()->session->sessionID ; //此次会话的session ID
			$thisTime = time(); //最后活动的时间

			if( !empty( $loginControl['loginLimit'][$thisUserRole] ) )
			{
				$value = Yii::app ()->cache->get ( $name ); //查询该用户name的缓存文件
				
				if(!empty($value)) //如果该用户name缓存存在且等于在线人数控制
				{ 
					if( count( $value ) < $loginControl['loginLimit'][$thisUserRole] || array_key_exists($sessionId,$value) ) //如果在线人数小于人数控制或为同一次会话
					{
						if( count( $value ) > 1 )
						{
							foreach( $value as $sessionKey=>$sessionVal)
							{
								if( $thisTime - $sessionVal['time'] > $userLogoutTime )
								{
									unset($value[$sessionKey]); //清除过期session ID
								}
							}
						}
						if( $logout ) //如果注销
						{
							unset($value[$sessionId]); //清除本次session ID
						}else{
							$value[$sessionId] = array('sessionID'=>$sessionId,'time'=>$thisTime); //用户sission ID 存入	
						}
						
						Yii::app ()->cache->set ( $name, $value, $userLogoutTime ); //存入缓存
						return true;
					}else{ //非同一次会话
						Yii::app()->session->clear();
						Yii::app()->session->destroy();
						return false;
					}
				}else{ //缓存已经销毁  重新存入
					$userInfo[$sessionId] = array('sessionID'=>$sessionId,'time'=>$thisTime);
					Yii::app ()->cache->set ( $name, $userInfo, $userLogoutTime );
					return true;
				}
			}else{
				return true;
			}
		}else{
			return true;
		}
	 }
}