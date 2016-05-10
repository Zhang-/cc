<?php

// $getData=HelpTool::getGroupData($dataList,$column);
class HelpTool
{
    public static function pgsql_synchronous($synchronousType,$dataArray,$tableName,$keyArray)
    {
        switch ($synchronousType) {
            case 'value':
                # code...
            break;
            case 'value':
                # code...
            break;
        }
        @pg_query("");
    }


    /*获取findAll取的的Data*/
    public static function getFindAllData($data)
    {
        $return = array();
        foreach ($data as $dVal)
        {
            $return[] = $dVal->attributes;
        }
        return $return;
    }


   /** 
     * @name 转换百分率，默认传入小数
     * @param integer $value 待转换数值
     * @param boolean $addTag 转换完毕是否添加%标识
     * @param boolean $isRate 传入的是否已经是百分率
     * @return int $result $addTag===false 
     * @return string $result $addTag===false  
     * 
     * @author zhanghy
     * @date 2014-08-27 10:47:13
     */
    public static function convertPercent($value, $addTag = false, $isRate = false)
    {
        $return = false;
        if(!!$value){
            if($isRate === false){
                $return = ($addTag === false) ? ($value*100) : ($value*100).'%';
            }else
                $return = $value/100;
        }
        return $return;
    }

   // HelpTool::logTrace('模块数据是否达标 数组判断', array($modelName, $data, $reverse)); //日志记录

    public static function logTrace($key, $contents = false, $cover = true)
    {
        $time = self::microtime_format(self::microtime_float());
        $date = date('Y-m-d');
        $path = Yii::app()->basePath . '/runtime/logTrace-'.$date.'.log'; // 日志路径
        $key = "\r\n[$time]>>>>>>>>>>>>>>> $key Start! \r\n";
        $key .= "Data Begin>>>\r\n------------------\r\n".var_export($contents, true)."\r\n-------------\r\n<<<Data End.\r\n"; 
        file_put_contents($path, $key, FILE_APPEND);
    }

    /** 获取当前时间戳，精确到毫秒 */
    public static function microtime_float()
    {
       list($usec, $sec) = explode(" ", microtime());
       return ((float)$usec + (float)$sec);
    }

    /** 格式化时间戳，精确到毫秒，x代表毫秒 */
    public static function microtime_format($time = null, $tag = "Y-m-d H:i:s xms")
    {
        if(is_null($time))
            $time = self::microtime_float();
       list($usec, $sec) = explode(".", $time);
       $date = date($tag,$usec);
       return str_replace('x', $sec, $date);
    }

    /** 
     * @name  公用分页
     * @param array   $data        待分页数据
     * @param int     $currentPage 当前页
     * @param int     $pageSize    每页数据数量
     * @return array  $arrays      返回一组已排序的数组
     * 
     * @author zhanghy
     * @date 2015-12-04 10:20:33
     */
    public static function getPaging(array $data, $currentPage, $pageSize = 20)
    {
        $dataCount      =   count($data);
        $pageCount      =   ceil($dataCount / $pageSize);
        $currentPage    >   $pageCount && $currentPage = $pageCount;
        $currentData    =   array_slice($data, ($currentPage - 1) * $pageSize, $pageSize);
        $restNum        =   ($pageCount - $currentPage) ? ($dataCount - $currentPage * $pageSize) : 0; //剩余条数

        $return           =    
        [
            'pageCount'   => $pageCount,   //总页数
            'currentData' => $currentData, //已分页数据
            'dataCount'   => $dataCount,   //数据总数
            'restNum'     => $restNum,     //剩余条数
        ];

        return $return;
    }

    /** 
     * @name 二维数组排序
     * @param Array   $arrays   待排序数组
     * @param string  $sort_key 排序参照key
     * @param sorting $sort_order 规定排列顺序。可能的值是 SORT_ASC 和 SORT_DESC
     * @param sorting $sort_type  规定排序类型。可能的值是SORT_REGULAR、SORT_NUMERIC和SORT_STRING
     * @return array  $arrays     返回一组已排序的数组
     * 
     * @author zhanghy
     * @date 2014-06-12 11:16:08
     */
    public static function my_array_multisort($arrays, $sort_key, $sort_order = SORT_ASC, $sort_type = SORT_NUMERIC)
    {   
        if(is_array($arrays))
        {  
            if (!$arrays) return $arrays;

            $key_arrays = []; 

            foreach ($arrays as $array)
            {   
                if(is_array($array) && isset($array[$sort_key]))
                {   
                    $key_arrays[] = $array[$sort_key];   
                }
                else
                {   
                    return false;   
                }   
            }   
        }
        else
        {   
            return false;   
        }  

        array_multisort($key_arrays,$sort_order,$sort_type,$arrays); 
          
        return $arrays;   
    } 

    /** 
     * @name 获取二维数组中指定键的一组数据
     * @param Array $array 待转换数组
     * @return array $result 返回$input数组中键值为$columnKey的列， 
     *         如果指定了可选参数$indexKey，那么$input数组中的这一列的值将作为返回数组中对应值的键
     * 
     * @author zhanghy
     * @date 2014-06-12 11:16:08
     */
    public static function my_array_column($input, $columnKey, $indexKey=null)
    {
        if(!function_exists('array_column')){ 
            $columnKeyIsNumber = (is_numeric($columnKey))?true:false; 
            $indexKeyIsNull = (is_null($indexKey))?true :false; 
            $indexKeyIsNumber = (is_numeric($indexKey))?true:false; 
            $result = array(); 
            foreach((array)$input as $key=>$row){ 
                if($columnKeyIsNumber){ 
                    $tmp= array_slice($row, $columnKey, 1); 
                    $tmp= (is_array($tmp) && !empty($tmp))?current($tmp):null; 
                    
                }else{ 
                    $tmp= isset($row[$columnKey])?$row[$columnKey]:null; 
                } 
                if(!$indexKeyIsNull){ 
                    if($indexKeyIsNumber){ 
                      $key = array_slice($row, $indexKey, 1); 
                      $key = (is_array($key) && !empty($key))?current($key):null; 
                      $key = is_null($key)?0:$key; 
                    }else{ 
                      $key = isset($row[$indexKey])?$row[$indexKey]:0; 
                    } 
                } 
                $result[$key] = $tmp; 
            } 
            return $result; 
        }else{
            return array_column($input, $columnKey, $indexKey);
        }
    }


    /** 
     * @name 数组转换为对象
     * @param Array $array 待转换数组
     * @return stdClass $object 日期差
     * 
     * @author zhanghy
     * @date 2014-06-11 13:35:57
     */
    public static function arrayToObject($array)
    {
        if(!is_array($array)) return $array;

        $object = new stdClass();
        if(is_array($array) && count($array) > 0)
        {
            foreach($array as $name=>$value)
            {
                $name = trim($name);
                if($name) $object->$name = self::arrayToObject($value);
            }

            return $object;
        }
        else return FALSE;
    }

    /** 
     * @name 时间戳之间求年月日差
     * @param String $part 返回时间差的类型
     * @param integer $begin 开始时间
     * @param integer $end 结束时间
     * @return integer $retval 日期差
     * 
     * @author zhanghy
     * @date 2014-06-09 15:13:01
     */
    public static function dateDiff($part, $begin, $end)
    {
        $diff = abs($end - $begin);
        switch($part)
        {
        case "y": $retval = bcdiv($diff, (60 * 60 * 24 * 365)); break;
        case "m": $retval = bcdiv($diff, (60 * 60 * 24 * 30)); break;
        case "w": $retval = bcdiv($diff, (60 * 60 * 24 * 7)); break;
        case "d": $retval = bcdiv($diff, (60 * 60 * 24)); break;
        case "h": $retval = bcdiv($diff, (60 * 60)); break;
        case "n": $retval = bcdiv($diff, 60); break;
        case "s": $retval = $diff; break;
        }
        return $retval;
    }

	/* 返回菜单列表  $selected 需要选中的选项 */
	public static function getMenuList($selected)
	{
		$menuData = Yii::app()->params->powermenu;
		$baseUrl = Yii::app()->request->baseUrl.'/index.php?r=';
		$menuList = '';
		foreach($menuData['items'] as $key=>$val){
			$menuUrl=strtolower(str_replace('/','',$val['url']));
			if(self::checkActionAccess($menuUrl)){
				$class = ($selected == $menuUrl) ? 'li_menu' : '';
				$url = $baseUrl.$val['url'];
				$menuList .= "<li class='{$class}'><a href='{$url}'><p class='p1'><span>{$val['label']}</span></p></a></li>";
			}
		}
		echo $menuList;
	}
	
	
    /* $type  需要返回的单双引号类型 */
	public static function getStrValue($type,$value)
	{
		if( $type == 'double' )
			return '"'.$value.'"';
		else if( $type == 'single' )
			return "'".$value."'";
		else
			return null;
	}
	
	
    /*
     * 日志获取信息类 $affectIds:受到影响的数据的id,默认为:1,2,3,4; 访问操作为0
     * $type:操作类型->Delete,Update,Create,Output.分别对应数字:0,1,2,3. 默认方法：
     * HelpTool::getActionInfo($affectIds,0);//删除操作
     * HelpTool::getActionInfo($affectIds,1);//修改操作
     * HelpTool::getActionInfo($affectIds,2);//新建操作
     * HelpTool::getActionInfo($affectIds,3);//导出表格操作
     * HelpTool::getActionInfo($affectIds,4);//访问操作
     * HelpTool::getActionInfo($affectIds,5);//备份操作
     * HelpTool::getActionInfo($affectIds,6);//下载备份操作
     * HelpTool::getActionInfo($affectIds,7);//删除备份操作
     * HelpTool::getActionInfo($affectIds,8);//用户登录
     * HelpTool::getActionInfo($affectIds,9);//注销登陆
     * HelpTool::getActionInfo(0,10);//导入终端信息
     * HelpTool::getActionInfo(0,11);//清除日志 
	 * HelpTool::getActionInfo(0,12);//清除缓存
     * HelpTool::getActionInfo(0,13);//修改密码
     */
    public static function getActionInfo ($affectIds, $type)
    {
        
        // type对应的级别
        $typeArray = array(
                0 => 'Delete',
                1 => 'Update',
                2 => 'Create',
                3 => 'Output',
                4 => 'View',
                5 => 'Backup',
                6 => 'DownloadBackup',
                7 => 'DeleteBackup',
                8 => 'Login',
                9 => 'Logout',
                10 => 'InputTerminal',
                11 => 'ClearLogs',
                12 => 'ClearCache',
                13 => 'UpdatePassword'
        );
        
        $thisTime = date('Y-m-d H:i:s'); // date
        
        if(Yii::app()->user->getId()) // userid
        {
			$userId = Yii::app()->user->getId();
			$ThisUserRole = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : HelpTool::getThisIdRole($userId); // user
		}else{
			$userId = 0 ;
			$ThisUserRole = 'Guest';
		}

        $userName = Yii::app()->user->name; // username
	 
        $thisActionType = $typeArray[$type]; // log type
        
        $thisActionUrl = $_SERVER["REQUEST_URI"]; // url
        
        $userIP = $_SERVER['REMOTE_ADDR']; // user的IP地址
        
        if (is_array($affectIds))
            $affectIds = implode(',', $affectIds);
        elseif ($affectIds == 0)
            $affectIds = $userId;
       

        
        $actionInfoArray = array(
                'actiontime' => $thisTime,
                'userip' => $userIP,
                'userid' => $userId,
                'username' => $userName,
                'userrole' => $ThisUserRole,
                'actiontype' => $thisActionType,
                'affectid' => $affectIds,
                'url' => $thisActionUrl
        );
        
		//return $actionInfoArray;
		
        HelpTool::saveLogs($actionInfoArray);
        HelpTool::saveDbLogs($actionInfoArray);
    }
    
    // 写入文件log
    public static function saveLogs ($actionInfoArray)
    {
        $today = date('Y-m-d');
        $tempString = implode("] [", $actionInfoArray);
        $actionInfo = "[" . $tempString . "]\r\n";
        
        $path = Yii::app()->basePath . '/runtime/actionlog.' . $today . '.log'; // 日志路径
        $fp = fopen($path, "a");
        fwrite($fp, $actionInfo);
        fclose($fp);
    }
    
    // 写入数据库表
    public static function saveDbLogs ($actionInfoArray)
    {
        /*$connection = Yii::app()->db;
        $sql = "insert into view_action_logs (actiontime,userip,userid,username,userrole,actiontype,affectid,url) values('" .
                 $actionInfoArray['actiontime'] . "','" .
                 $actionInfoArray['userip'] . "','" . $actionInfoArray['userid'] .
                 "','" . $actionInfoArray['username'] . "','" .
                 $actionInfoArray['userrole'] . "','" .
                 $actionInfoArray['actiontype'] . "','" .
                 $actionInfoArray['affectid'] . "','" . $actionInfoArray['url'] .
                 "')";
        $command = $connection->createCommand($sql);
        $command->execute();*/
    }
    
    // 操作类型array
    public static function getLogTypeTrans ($type)
    {
        $logTypeTrans = array(
                'Delete' => '删除',
                'Update' => '修改',
                'Create' => '新建',
                'Output' => '导出表格',
                'View' => '查看',
                'Backup' => '数据备份',
                'DownloadBackup' => '下载数据备份',
                'DeleteBackup' => '删除数据备份',
                'Login' => '登录',
                'Logout' => '注销登录',
                'InputTerminal' => '导入终端信息',
                'ClearLogs' => '清除日志',
                'ClearCache' => '清除缓存',
                'UpdatePassword' => '修改密码'
        );
        if ($type == '') {
            return $logTypeTrans;
        } else {
            return $logTypeTrans[$type];
        }
    }
	
	//日志数据库清理

	public static function clearDbLogs()
	{
		$limitNum = Yii::app()->params->logsLimitNum; //日志存储限制数
		$count = ViewActionLogs::model()->count();  //根据一个条件查询一个集合有多少条记录，返回一个int型数字
		
		//if($count > $limitNum)
		//{
			/*$connection = Yii::app()->db;
			$sql = "select max(id) from view_action_logs";
			$row = $connection->createCommand($sql)->queryRow();*/

            $maxId = ViewActionLogs::model()->find(array('select'=>'max(id) as id'))->getAttributes();
		
			$maxId = $maxId['id']; //日志表最大ID值
			
			$deleteMaxID = $maxId - $limitNum; //需要删除的最大ID值
			ViewActionLogs::model()->deleteAll("id < '".$deleteMaxID."'"); //删除最早日志
		//}
	}
    
    // 用户登录时间操作
    public static function loginTime ()
    {
        $model = new ManageList();
        $thisTime = date("Y-m-d H:i:s");
        $thisUserID = Yii::app()->user->getId();
        $thisUser = $model->findByPk($thisUserID);
        $_SESSION['lastLoginTime'] = $thisUser->lastLoginTime;
        $_SESSION['userRole'] = $thisUser->itemname;
        $user['lastLoginTime'] = $thisTime;
        $user['regDateTime'] = $thisUser->regDateTime;
        $model->updateAll($user, " userid='" . $thisUserID . "'");
    }
    
    // 查询用户最近一次登录时间
    public static function getUserLastLoginTime ()
    {
        $id = Yii::app()->user->getId();
		$row = ManageList::model()->findByPk($id);
		return $row['lastLoginTime'];
    }
    
    // 翻译表
    public static function translate ($text = '')
    {
        $connection = Yii::app()->db;
        $sqlTranslate = 'select description from translate_list where columnname="' .
                 $text . '" ';
        $commandTrans = $connection->createCommand($sqlTranslate);
        $urTrans = $commandTrans->queryRow();
      
        return implode('',$urTrans);
        
    }
    
    // 获取数据库数据的分组
    public static function getGroupData ($dataList = '', $column = '')
    {
        $groupData = array();
        $connection = Yii::app()->db;
        $sqlGroupData = 'select ' . $column . '  from ' . $dataList .
                 ' group by ' . $column;
        $command = $connection->createCommand($sqlGroupData);
        $groupDataRow = $command->queryAll();
        foreach ($groupDataRow as $groupDataKey => $groupDataVal) {
            if ($groupDataVal[$column] != '') {
                $groupData[$groupDataVal[$column]] = $groupDataVal[$column];
            }
        }
        return $groupData;
    }
    
    // 获取省份下属城市
    public static function getProvinceCity ($dataList = '', $province = '')
    {
        $provinceCity = array();
        $connection = Yii::app()->db;
        $sqlProvinceCity = 'select city  from  ' . $dataList . ' where province=' .
                 $province . ' group by city';
        $command = $connection->createCommand($sqlProvinceCity);
        $provinceCityRow = $command->queryAll();
        foreach ($provinceCityRow as $ProvinceCityKey => $ProvinceCityVal) {
            $provinceCity[$ProvinceCityVal['city']] = $ProvinceCityVal['city'];
        }
        return $provinceCity;
    }
    
    // 获取运营商列表（运营商的名称）
    public static function getOperatorList ()
    {
        $connection = Yii::app()->db;
        $sql = 'select id,title  from  operator_list ';
        $command = $connection->createCommand($sql);
        $row = $command->queryAll();
        foreach ($row as $key => $operatroVals) {
            $operatros[$operatroVals['title']] = $operatroVals['id'];
        }
        return $operatros;
    }
	
	//获取时间戳格式化时间
    public static function getDateTime($strtotime)
	{
		$strtotime = intval($strtotime);
		return date('Y-m-d H:i:s',$strtotime);
	}
    
    /*
     *
     * ↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓权限相关方法↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
     */
    
    // 返回当前ID的角色
    public static function getThisIdRole ($id)
    {
        $connection = Yii::app()->db;
        $sql = "select itemname from manage_list where userid='" . $id . "'";
        $command = $connection->createCommand($sql);
        $row = $command->queryRow();
        if ($row != null) 
		{
            return implode('',$row);
        } else {
            return "";
        }
    }
    
    // 返回该ID可访问的Action的集合,如果为guest用户，$id=0;
    public static function getActionByID ($id)
    {
        $r = '';
        if ($id != 0) {
            $connection = Yii::app()->db;
            $sql = 'select child from manage_view_itemchild where parent=any(select child from manage_view_itemchild where 
					parent=any(select itemname from manage_list where userid=' . $id .'))';
            $command = $connection->createCommand($sql);
            $row = $command->queryAll();
            foreach ($row as $v) 
			{
                $r[] = strtolower($v['child']);
            }
            if ($r == '') {
                return '';
            } else {
                return $r;
            }
        } else {
            return array(
                    'sitelogin',
                    'sitelogout',
                    'siteindex',
                    'siteerror',
                    'sitecontact'
            );
        }
    }
    
    // 获取所有角色
    public static function getAllRoles ()
    {
        $allRoles = array();
        $connection = Yii::app()->db;
        $sql = 'select name from manage_view_item where type=2';
        $command = $connection->createCommand($sql);
        $row = $command->queryAll();
        foreach ($row as $rolesKey => $rolesVal) {
            $allRoles[$rolesVal['name']] = $rolesVal['name'];
        }
        return $allRoles;
    }
    
    // 角色汉化,如有变动，需手动配置
    public static function getRolesTrans ()
    {
        return array(
                'Guest' => '访客',
                'Admin' => '超级管理员',
                'Manager' => '管理员',
                'ViewUser' => '只读用户',
                'ChartUser' => '报表用户'
        );
    }
    
    // 获取角色的汉化,如果参数是''，则返回所有角色汉化数组
    public static function getAllRolesTrans ($thisRole)
    {
        $allRoles = HelpTool::getAllRoles();
        $rolesTrans = HelpTool::getRolesTrans();
        $allRoleTrans = array();
        if ($thisRole != '') {
            if (array_key_exists($thisRole, $rolesTrans)) {
                return $rolesTrans[$thisRole];
            } else {
                return $thisRole;
            }
        } else { // 获取所有角色
            foreach ($allRoles as $roleKey => $roleVal) {
                if (array_key_exists($roleKey, $rolesTrans)) {
                    $allRoleTrans[$roleKey] = $rolesTrans[$roleKey];
                } else {
                    $allRoleTrans[$roleKey] = $roleVal;
                }
            }
            return $allRoleTrans;
        }
    }
    
    // 获取所有角色的汉化
    public static function allRolesTrans ()
    {
        $allRoles = HelpTool::getAllRoles();
        $rolesTrans = HelpTool::getRolesTrans();
        
        foreach ($allRoles as $roleKey => $roleVal) {
            if (array_key_exists($roleKey, $rolesTrans)) {
                $allRoleTrans[$roleKey] = $rolesTrans[$roleKey];
            } else {
                $allRoleTrans[$roleKey] = $roleVal;
            }
        }
        return $allRoleTrans;
    }
    
    // 是否为超级管理员
    public static function isAdmin ()
    {
        $adminRole = Helper::findModule('srbac')->superUser; // 获取超级用户组
        $thisUserId = Yii::app()->user->getId(); // 获取当前用户ID
        $thisUser = ManageList::model()->findByAttributes(
                array(
                        'userid' => $thisUserId
                )); // 获取当前用户信息
        
        if ($thisUser['itemname'] != $adminRole) {
            $flag = false;
        } else {
            $flag = true;
        }
        return array(
                'flag' => $flag,
                'Admin' => $adminRole
        );
    }
    
    // 手动创建角色下拉列表
    public static function roleSelect ($model)
    {
        $allRoles = HelpTool::getAllRoles(); // 获取所有角色
        $adminRole = Helper::findModule('srbac')->superUser; // 获取超级用户组
        $thisUserId = Yii::app()->user->getId(); // 获取当前用户ID
        $thisUser = ManageList::model()->findByAttributes(
                array(
                        'userid' => $thisUserId
                )); // 获取当前用户信息
        $transArr = HelpTool::getRolesTrans(); // 汉化数组
        $getThisIdRole = HelpTool::getThisIdRole($model->id); // 获取当前显示用户的角色
        
        if ($thisUser['itemname'] != $adminRole) {
            unset($allRoles[$adminRole]);
        }
        
        echo '<label for="giveRole" class="required">用户权限 </label>
		<select name="giveRole" id="giveRole" >';
        // 角色下拉列表
        
        foreach ($allRoles as $vals) {
            if ($vals == $getThisIdRole) {
                $option = 'selected="selected"';
            } else {
                $option = '';
            }
            if ($getThisIdRole == "") {
                if (array_key_exists($vals, $transArr)) {
                    echo '<option value="' . $vals . '">' . $transArr[$vals] .
                             '</option>';
                } else {
                    echo '<option value="' . $vals . '">' . $vals . '</option>';
                }
            } else {
                if (array_key_exists($vals, $transArr)) {
                    echo '<option value="' . $vals . '" ' . $option . '>' .
                             $transArr[$vals] . '</option>';
                } else {
                    echo '<option value="' . $vals . '" ' . $option . '>' . $vals .
                             '</option>';
                }
            }
        }
        echo '</select>';
    }
	
	//登录时存入可访问action的Cookie
	public static function setActionsCookie($cookieName,$cookieValue,$durationTime)
	{
		$cookie = new CHttpCookie($cookieName, $cookieValue); //新建cookie
		$cookie->expire = time()+$durationTime;  //定义cookie的有效期
		Yii::app()->request->cookies[$cookieName]=$cookie; //把cookie写入cookies使其生效
	}
	
    
    // 判断action访问权限
    public static function checkActionAccess ($thisActionName)
    {
		$userSessionName = Yii::app()->params->userSessionName;
        $myActions = !empty($_SESSION[$userSessionName]) ? json_decode($_SESSION[$userSessionName], true) : [];
        if ($myActions && in_array($thisActionName, $myActions)) 
		{
            return true;
        } else {
            return false;
        }
    }
    
    // 查找本角色Bizrule,其他角色，请传参
    public static function getThisRoleBizrule ($thisRole = '')
    {
        if ($thisRole == '') {
            $thisRole = isset($_SESSION['userRole']) ? $_SESSION['userRole'] : HelpTool::getThisIdRole(Yii::app()->user->getId());
        }
        
        $thisRoleBizrule = '';
        $itemTableName = Yii::app()->authManager->itemTable;
        
        $connection = Yii::app()->db;
        $sql = "select bizrule from $itemTableName where name = " . "'" .
                 $thisRole . "'";
        $command = $connection->createCommand($sql);
        $row = $command->queryAll();
        
        foreach ($row as $val) {
            $thisRoleBizrule = implode($val);
        }
        
        if ($thisRoleBizrule !== '') {
            $thisRoleBizrule = json_decode($thisRoleBizrule);
            $thisRoleBizrule = (array) $thisRoleBizrule;
            
            return $thisRoleBizrule;
        } else {
            return $thisRoleBizrule;
        }
    }
    
    // 需要配置数据层访问控制的action（手动修改）
    public static function dbConnectControlAction ()
    {
        return array(
                'SysManageUserAdmin' => '',
                'UnderlingRole' => ''
        );
    }

    public static function getDBConnectLevel ()
    {
        return array(
                '4' => '不能查看数据',
                '3' => '查看自身数据',
                '2' => '查看自身及下属成员数据',
                '1' => '查看全部数据'
        );
    }
    
    // 配置用户的action数据访问权限
    public static function selectActionDBConnect ($thisRole = '')
    {
        $allActionsArray = HelpTool::dbConnectControlAction();
        $allDBConnectLevel = HelpTool::getDBConnectLevel();
        $thisRoleConnectActions = array();
        $allRoles = array();
        
        if ($thisRole !== '') {
            $thisRoleConnectActions = HelpTool::getThisRoleBizrule($thisRole);
        }
        
        foreach ($allActionsArray as $actionKey => $actionVal) {
            if ($actionKey !== 'UnderlingRole') {
                echo '<label for="' . $actionKey . '" class="required">' .
                         $actionKey . '</label>
					<select name="bizrule[' .
                         $actionKey . ']" id="' . $actionKey . '" >';
                
                foreach ($allDBConnectLevel as $levelKey => $levelVal) {
                    $selected = '';
                    if (isset($thisRoleConnectActions[$actionKey]) &&
                             ($levelKey == $thisRoleConnectActions[$actionKey])) {
                        $selected = 'selected="selected"';
                    }
                    echo '<option ' . $selected . ' value="' . $levelKey . '" >' .
                             $levelVal . '</option>';
                }
                
                echo '</select></br></br>';
            } else {
                $allRoles = HelpTool::getAllRoles();
                echo '<label for="' . $actionKey . '" class="required">' .
                         $actionKey . '</label>
					<select name="bizrule[' .
                         $actionKey . ']" id="' . $actionKey . '" >';
                
                foreach ($allRoles as $roleKey => $roleVal) {
                    $selected = '';
                    if (isset($thisRoleConnectActions[$actionKey]) &&
                             ($roleKey == $thisRoleConnectActions[$actionKey])) {
                        $selected = 'selected="selected"';
                    }
                    echo '<option ' . $selected . ' value="' . $roleKey . '" >' .
                             $roleVal . '</option>';
                }
                
                echo '</select>';
            }
        }
    }
    
    // 根据用户配置，控制数据访问量(简单的单表单字段控制,ID为判断关键字段)
    public static function getAllDBControlInfo ($dbList)
    {}

    public static function saveCellCoveringGISCache ($jsonCache,$md)
    {
        $path = dirname(Yii::app()->BasePath) .
                 '/flexgis/cellCovering/cache/cellCoveringGISCache_'.$md.'.txt';
        // $path=Yii::app()->basePath.'/runtime/cellCoveringGISCache.txt';//日志路径
        $fp = fopen($path, "w");
        fwrite($fp, $jsonCache);
        fclose($fp);
    }

    public static function saveCellDataAnalysisGISCache1 ($jsonCache,$md)
    {
        $path = dirname(Yii::app()->BasePath) .
                 '/flexgis/celldatagis/celldataGIS_'.$md.'1.txt';
        // $path=Yii::app()->basePath.'/runtime/cellCoveringGISCache.txt';//日志路径
        $fp = fopen($path, "w");
        fwrite($fp, $jsonCache);
        fclose($fp);
    }

    public static function saveCellDataAnalysisGISCache2 ($jsonCache,$md)
    {
        $path = dirname(Yii::app()->BasePath) .
                 '/flexgis/celldatagis/celldataGIS_'.$md.'2.txt';
        // $path=Yii::app()->basePath.'/runtime/cellCoveringGISCache.txt';//日志路径
        $fp = fopen($path, "w");
        fwrite($fp, $jsonCache);
        fclose($fp);
    }

    public static function saveCellDataAnalysisGISCache3 ($jsonCache,$md)
    {
        $path = dirname(Yii::app()->BasePath) .
                 '/flexgis/celldatagis/celldataGIS_'.$md.'3.txt';
        // $path=Yii::app()->basePath.'/runtime/cellCoveringGISCache.txt';//日志路径
        $fp = fopen($path, "w");
        fwrite($fp, $jsonCache);
        fclose($fp);
    }
}

/*
 *
 * ↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑权限相关方法↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
 */