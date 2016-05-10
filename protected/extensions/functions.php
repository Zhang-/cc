<?php 

function convertTime($time){
	if($time)
		return date("Y-m-d H:i:s", $time);
	return "-";
}

//手机号有效性验证
function getPhone($phone){
	if(!empty($phone) && $phone != '11111111111')
		return $phone;
	else 
		return '-';
}
//判断显示经纬度
function showLngorLat($val){
	if(empty($val) || round($val,0) == 0 || round($val,0) == 720)
		return "-";
	else
		return $val;
}
//时间字符串转换1,包括小时
function formatTime($time){
	$ftime = explode(":", $time);
	$t = str_ireplace('-', '', str_ireplace(' ', '', $ftime[0]));
	return $t;
}
//时间字符串转换2,不包括小时
function formatTime2($time){
	$ftime = explode(" ", $time);
	$t = str_ireplace('-', '',$ftime[0]);
	return $t;
}

//页面查看详情跳转方法
function createUrl($url = null,$gis=''){
$funName = ( $gis == '' ) ? 'showMore' : 'showMoreGIS';
echo <<<eot
	<a  class='aview under' onclick='{$funName}("{$url}")' title='查看详情'>查看详情</a>
eot;
}


	//根据lac和cid查询小区的信息
	function getinformationforcell($lac,$cid,$net)
	{
		$connection=Yii::app()->db;
		if($net=='HSDPA' || $net=='TD-SCDMA'){
			$sql="select * from site where lac='".$lac."' and cellId='".$cid."' and type=1 ";	
			$command = $connection->createCommand($sql);
			$row=$command->queryRow();
			if(!empty($row))
			{
				return $row;	
			}
		}
		elseif($net=='GSM' || $net=='GPRS' || $net=='EDGE'){
			$sql="select * from site where lac='".$lac."' and cellId='".$cid."' and (type=0 or type=2) ";	
			$command = $connection->createCommand($sql);
			$row=$command->queryRow();
			if(!empty($row))
			{
				return $row;	
			}
		}elseif ($net=='LTE'){
			$sql="select * from site where lac='".$lac."' and cellId='".$cid."' and type=4 ";	
			$command = $connection->createCommand($sql);
			$row=$command->queryRow();
			if(!empty($row))
			{
				return $row;	
			}
		}
				
	}
	 
	
	//2维数组去重
	 function array_unique_fb($array2D){
				 $temp=array();
                 foreach ($array2D as $v){
                     $v = join(",",$v); //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
                     $temp[] = $v;
                 }
                 $temp = array_unique($temp);    //去掉重复的字符串,也就是重复的一维数组
                foreach ($temp as $k => $v){
                    $temp[$k] = explode(",",$v);   //再将拆开的数组重新组装
                }
                return $temp;
            }
	/**
	 * 查询数据库函数
	 * 直接传入sql语语，返回数组格式的结果
	 * @param string $sql
	 */
	function getDataBaseData($sql){
		if(empty($sql))
			return false;
		//echo $sql."\r\n";
		$connection=Yii::app()->db;
		$command = $connection->createCommand($sql);
		$rs = $command->queryAll();
		unset($command);
		return $rs;
	}
	/**
	 * 
	 * 返回网络类型对应的网络编码值数组
	 * @param string $md 网络类型
	 */
	function gettypeAry($md){
		global $ary_gsm;
		global $ary_td;
		global $ary_lte;
		$connection=Yii::app()->db;
		if($md=='GSM'){
			if(!empty($ary_gsm)){
				return $ary_gsm;
			}else{
				$s1="select id from `network_type` where nettype in('GPRS','EDGE','GSM')";
				$com1=$connection->createCommand($s1);
				$res1=$com1->queryAll();
				foreach($res1 as $a1){
					$ary_gsm[]=$a1['id'];	
				}
				return $ary_gsm;
			}
		}elseif($md=='TD'){
			if(!empty($ary_td)){
				return $ary_td;
			}else{
				$s2="select id from `network_type` where nettype in('TD-SCDMA','HSDPA','HSPA','HSUPA')";
				$com2=$connection->createCommand($s2);
				$res2=$com2->queryAll();
				foreach($res2 as $a2){
				$ary_td[]=$a2['id'];	
				}
				return $ary_td;
			}	
		}elseif ($md == 'LTE'){
			if(!empty($ary_lte)){
				return $ary_lte;
			}else{
				$s3="select id from `network_type` where nettype in('LTE')";
				$com3=$connection->createCommand($s3);
				$res3=$com3->queryAll();
				foreach($res3 as $a3){
				$ary_lte[]=$a3['id'];	
				}
				return $ary_lte;
			}
		}
	}
	function netTypeStr($md){
		global $str_gsm;
		global $str_td;
		global $str_lte;
		global $str_all;
		global $type_id;
		$connection=Yii::app()->db;
		if($md=='GSM'){
			if(!empty($str_gsm)){
				return $str_gsm;
			}else{
				$s1="select id from `network_type` where nettype in('GPRS','EDGE','GSM')";
				$com1=$connection->createCommand($s1);
				$res1=$com1->queryAll();
				$str_gsm="-1";
				foreach($res1 as $a1){
					$str_gsm.=",".$a1['id'];	
				}
				return $str_gsm;
			}
		}elseif($md=='TD'){
			if(!empty($str_td)){
				return $str_td;
			}else{
				$s2="select id from `network_type` where nettype in('TD-SCDMA','HSDPA','HSPA','HSUPA')";
				$com2=$connection->createCommand($s2);
				$res2=$com2->queryAll();
				$str_td="-1";
				foreach($res2 as $a2){
					$str_td.=",".$a2['id'];	
			
				}
				return $str_td;
			}
		}elseif($md=='LTE'){
			if(!empty($str_lte)){
				return $str_lte;
			}else{
				$s3="select id from `network_type` where nettype in('LTE')";
				$com3=$connection->createCommand($s3);
				$res3=$com3->queryAll();
				$str_lte="-1";
				foreach($res3 as $a3){
					$str_lte.=",".$a3['id'];	
			
				}
				return $str_td;
			}	
		}elseif($md=='all'){
			if(!empty($str_all)){
				return $str_all;
			}else{
				$s1="select id from `network_type` where nettype in('GPRS','EDGE','GSM','TD-SCDMA','HSDPA','HSPA','HSUPA')";
				$com1=$connection->createCommand($s1);
				$res1=$com1->queryAll();
				$str_all="-1";
				foreach($res1 as $a1){
					$str_all.=",".$a1['id'];	
				}
				return $str_all;
			}
		}else{
			if(!empty($type_id)){
				$temp_id=array_search($md,$type_id);
				if(!$temp_id)
					return $temp_id;
			}
			$s1="select id from `network_type` where nettype ='".$md."'";
			$com1=$connection->createCommand($s1);
			$res1=$com1->queryRow();
			$type_id[$res1['id']]=$md;
			return $res1['id'];
		}
	}
	/**
	 * 判断是gsm网络
	 * @param int $id
	 * @return true | false
	 */
	function isNetTypeGSM($id){
		global $global_net_type_gsm;
		if(empty($global_net_type_gsm)){
			$global_net_type_gsm = getDataBaseData("select id from `network_type` where nettype in('GPRS','EDGE','GSM');");
			$temp = array();
			foreach ($global_net_type_gsm as $value) {
				$temp[] = $value["id"];
			}
			
			$global_net_type_gsm = (empty($temp))?array("空"):$temp;
		}
		return in_array($id, $global_net_type_gsm);
	}
	
	/**
	 * 判断是td网络
	 * @param int $id
	 * @return true | false
	 */
	function isNetTypeTD($id){
		global $global_net_type_td;
		if(empty($global_net_type_td)){
			$global_net_type_td = getDataBaseData("select id from `network_type` where nettype in('TD-SCDMA','HSDPA','HSPA','HSUPA');");
			$temp = array();
			foreach ($global_net_type_td as $value) {
				$temp[] = $value["id"];
			}
			$global_net_type_td = (empty($temp))?array("空"):$temp;
		}
		return in_array($id, $global_net_type_td);
	}
	/**
	 * 判断是lte网络
	 * @param int $id
	 * @return true | false
	 */
	function isNetTypeLTE($id){
		global $global_net_type_lte;
		if(empty($global_net_type_lte)){
			$global_net_type_lte = getDataBaseData("select id from `network_type` where nettype in('LTE');");
			$temp = array();
			foreach ($global_net_type_lte as $value) {
				$temp[] = $value["id"];
			}
			$global_net_type_lte = (empty($temp))?array("空"):$temp;
		}
		return in_array($id, $global_net_type_lte);
	}
	
	/**
	 * 判断传入的信息是否在site_td表中
	 * @param int $lac
	 * @param int $cellId
	 * @return true | false
	 */
	function isSiteTD($lac,$cellId){
		global $global_site_td_simplify;
		if(isset($global_site_td_simplify[($lac."_".$cellId)])){
			$temp1 = $global_site_td_simplify[($lac."_".$cellId)];
			return $temp1;
		}
		
		global $global_site_td;
		if(empty($global_site_td)){
			
			$global_site_td = getDataBaseData("select lac,cellId from site where type=1;");
			if(!empty($global_site_td)){
				$temp = array();
				foreach ($global_site_td as $value)
					$temp[] = $value["lac"]."_".$value["cellId"];
			}else{
				$temp = array("空");
			}
			$global_site_td = $temp;
		}
		$temp2 = in_array(($lac."_".$cellId), $global_site_td);
		if($temp2){
			$global_site_td_simplify[($lac."_".$cellId)] = true;
			return true;
		}else{
			$global_site_td_simplify[($lac."_".$cellId)] = false;
			return false;
		}
	}
	
	/**
	 * 判断传入的信息是否在site_gsm表中
	 * @param int $lac
	 * @param int $cellId
	 * @return true | false
	 */
	function isSiteGSM($lac,$cellId){
			global $global_site_gsm_simplify;
		if(isset($global_site_gsm_simplify[($lac."_".$cellId)])){
			$temp1 = $global_site_gsm_simplify[($lac."_".$cellId)];
			return $temp1;
		}
		
		global $global_site_gsm;
		if(empty($global_site_gsm)){
			
			$global_site_gsm = getDataBaseData("select lac,cellId from site where type=0 or type=2;");
			if(!empty($global_site_gsm)){
				$temp = array();
				foreach ($global_site_gsm as $value)
					$temp[] = $value["lac"]."_".$value["cellId"];
			}else{
				$temp = array("空");
			}
			$global_site_gsm = $temp;
		}
		$temp2 = in_array(($lac."_".$cellId), $global_site_gsm);
		if($temp2){
			$global_site_gsm_simplify[($lac."_".$cellId)] = true;
			return true;
		}else{
			$global_site_gsm_simplify[($lac."_".$cellId)] = false;
			return false;
		}
	}
	
	/**
	 * 判断传入的信息是否是4g小区
	 * @param int $lac
	 * @param int $cellId
	 * @return true | false
	 */
	function isSiteLTE($lac,$cellId){
			global $global_site_lte_simplify;
		if(isset($global_site_lte_simplify[($lac."_".$cellId)])){
			$temp1 = $global_site_lte_simplify[($lac."_".$cellId)];
			return $temp1;
		}
		
		global $global_site_lte;
		if(empty($global_site_lte)){
			
			$global_site_lte = getDataBaseData("select lac,cellId from site where type=4;");
			if(!empty($global_site_lte)){
				$temp = array();
				foreach ($global_site_lte as $value)
					$temp[] = $value["lac"]."_".$value["cellId"];
			}else{
				$temp = array("空");
			}
			$global_site_lte = $temp;
		}
		$temp2 = in_array(($lac."_".$cellId), $global_site_lte);
		if($temp2){
			$global_site_lte_simplify[($lac."_".$cellId)] = true;
			return true;
		}else{
			$global_site_lte_simplify[($lac."_".$cellId)] = false;
			return false;
		}
	}
	
//获取用户所有业务的开始时间和结束时间
function getDateTime($id){
	//取得指定的开始时间和结束时间	
	$str=getTTime(); 
	$stp=getPTime();
	$times=array();
	$connection=Yii::app()->db;	
	//语音
	$sql ="SELECT startDateTime,stopDateTime FROM `voice_service` where staticId=".$id." and startDateTime  < '".$stp."' and stopDateTime > '".$str."'";
	$command = $connection->createCommand($sql);
	$rs = $command->queryAll();
	foreach($rs as $row){
		if($row['startDateTime']<$str){
			$time['st']=$str;
		}else{
			$time['st']=$row['startDateTime'];
		}
		if($row['stopDateTime']>$stp){
			$time['sp']=$stp;
		}else{
			$time['sp']=$row['stopDateTime'];
		}
		$times[]=$time;
	}
	//数据
	$sql ="SELECT startDateTime,stopDateTime FROM `data_service` where staticId=".$id." and startDateTime  < '".$stp."' and stopDateTime > '".$str."'";
	$command = $connection->createCommand($sql);
	$rs = $command->queryAll();
	foreach($rs as $row){
		$time['st']=$row['startDateTime'];
		$time['sp']=$row['stopDateTime'];
		$times[]=$time;
	}
	return $times;
}

//取得限定开始时间
function getTTime(){
	if(isset($_GET['StaticInformation']['startDateTime'])){
		$startdateTime=$_GET['StaticInformation']['startDateTime']; 
	}else{
		$startdateTime=date('Y-m-d H',strtotime("-1 hours")).':00';
	}

	return $startdateTime;
}
//取得限定结束时间
function getPTime(){
	if(isset($_GET['StaticInformation']['stopDateTime'])){
		$stopdateTime=$_GET['StaticInformation']['stopDateTime'];
	}else{
		$stopdateTime=date('Y-m-d H',strtotime("-1 hours")).':59';
	}
	return $stopdateTime;
}


/*------------------------------------------------------------------用户网络占用分析---------------------------------------------------------------------------------------*/
//执行生成缓存
function getOcpyCache($str,$stp,$temp_cach,$temp_cach_user){
	ini_set('memory_limit', '512M');
	set_time_limit(0);
	//将时间转化为时间戳
	$user_start_time_time = strtotime($str);
	$user_end_time_time = strtotime($stp);
	//--------------以上是对前台传入的数据进行验证-----------------
	$dynamic_information = getDataBaseData("select voiceID,dataID,startDateTime,cellId,lac,netType from dynamic_information where (startDateTime  between '{$str}' and '{$stp}') ORDER BY startDateTime asc;");

	//得到所有符合的语音数据
	$voice_service = getDataBaseData("SELECT id,staticID,startDateTime,stopDateTime FROM `voice_service` where startDateTime  < '{$stp}' and stopDateTime > '{$str}';");

	//得到所有符合的数据业务数据
	$data_service = getDataBaseData("SELECT id,staticID,startDateTime,stopDateTime FROM `data_service` where startDateTime  < '{$stp}' and stopDateTime > '{$str}';");

		/**
		 * 处理动态数据的格式,方便取数据
		 * 以下是还入数据的格式
		 * Array
(
	[v683] => Array
		(
			[0] => Array
				(
					[voiceID] => 683
					[dataID] => 0
					[startDateTime] => 2013-01-05 09:17:49
					[cellId] => 0
					[lac] => 0
					[netType] => 16
				)
			[1] => Array.....
			[2] => Array...

		)
	 [d2288] => Array
		(
			[0] => Array...
			[1] => Array...

		)
		 */
	$result = array();
	$user_lc = array();
	if(!empty($dynamic_information)){
		$dynamic_data = array();
		foreach ($dynamic_information as $value) {
			if(!empty($value["voiceID"])){
				$dynamic_data[("v".$value["voiceID"])][] = $value;
			}elseif(!empty($value["dataID"])){
				$dynamic_data[("d".$value["dataID"])][] = $value;
			}
		}
		unset($dynamic_information);
		/**
		 * 计算所有语音的结果
		 * 
		 * 语音数据，语音开始时间，语音结束时间
		 * 		1.动态数据, 本次时间-语音开始时间
		 * 		2.动态数据, 本次时间-上次动态时间
		 * 		3.动态数据, 本次时间-上次动态时间
		 * 			.....
		 * 		4.动态数据, (用户选的结束时间<语音结束时间)?(用户选的结束时间-本次时间):语音结束时间-本次时间
		 * 
		 * 注：以上时间要用绝对值
		 */
		foreach ($voice_service as $voice) {
			//如果语音数据没有对应的动态数据,则不计算
			if(isset($dynamic_data[("v".$voice["id"])]))
				$temp_dynamic_array = $dynamic_data[("v".$voice["id"])];
			else 
				continue;
				
			//上一次的动态数据时间-----gsm占用时长-------td占用时长----
			$previous_time = $gsm_length = $td_length = $lte_length = 0;
			$temp_dynamic_count = count($temp_dynamic_array);//动态数据的数量
			$temp_prv_lc = '*';
			foreach ($temp_dynamic_array as $key => $dynamic_value) {
				if(($dynamic_value["startDateTime"] >= $voice["startDateTime"]) && ($dynamic_value["startDateTime"] <= $voice["stopDateTime"])){
					//如果cellId和lac都等于0,则记录本次的时间，计算一下次的动态数据
					if($dynamic_value["cellId"] == "0" || $dynamic_value["lac"] == "0"){
						$previous_time = strtotime($dynamic_value["startDateTime"]);
						continue;
					}
					$temp_lc = $dynamic_value['lac']."-".$dynamic_value['cellId'];//将lac和cellId连接起来
					$temp_value = 0;//临时结果存放
					if($key == 0 || empty($previous_time)){//如果是第一条动态数据,减语音开始时间
						$temp_value = $user_start_time_time < strtotime($voice["startDateTime"])?(strtotime($dynamic_value["startDateTime"])-strtotime($voice["startDateTime"])):(strtotime($dynamic_value["startDateTime"])-$user_start_time_time);
					}else{ //中间的动态数据，减去上一次的动态时间
						$temp_value = strtotime($dynamic_value["startDateTime"])-$previous_time;		
					}
					if($temp_dynamic_count == ($key+1)){//是最后一条动态数据,用本次的时间减去语音表的结束时间[数据可能是,语音中的结束时间大于最后一条动态时间],需要加上最后的时间
						$temp_value += ($user_end_time_time<strtotime($voice["stopDateTime"]))?($user_end_time_time-strtotime($dynamic_value["startDateTime"])):(strtotime($voice["stopDateTime"])-strtotime($dynamic_value["startDateTime"]));			
					}
					if($temp_value > 0 || ($key == 0 && $temp_value == 0)){
						//计算GSM占用时长,要是gms网络,并且在gsm表中能找到对应的lac,cellId
						if(isNetTypeGSM($dynamic_value["netType"]) && isSiteGSM($dynamic_value["lac"], $dynamic_value["cellId"])){
							$gsm_length += $temp_value;
							//把用户占用的小区号存入数组
							if(!isset($user_lc[$voice["staticID"]])){
								$user_lc[$voice["staticID"]][$temp_lc] = 1;
							}elseif(!array_key_exists($temp_lc,$user_lc[$voice["staticID"]])){
								$user_lc[$voice["staticID"]][$temp_lc] = 1;
							}else{
								if($temp_lc != $temp_prv_lc)
									$user_lc[$voice["staticID"]][$temp_lc] += 1;
							}
						//计算TD占用时长,要是TD网络,并且在gsm表中能找到对应的lac,cellId
						}elseif(isNetTypeTD($dynamic_value["netType"]) && isSiteTD($dynamic_value["lac"], $dynamic_value["cellId"])){
							$td_length += $temp_value;
							//把用户占用的小区号存入数组
							if(!isset($user_lc[$voice["staticID"]])){
								$user_lc[$voice["staticID"]][$temp_lc] = 1;
							}elseif(!array_key_exists($temp_lc,$user_lc[$voice["staticID"]])){
								$user_lc[$voice["staticID"]][$temp_lc] = 1;
							}else{
								if($temp_lc != $temp_prv_lc)
									$user_lc[$voice["staticID"]][$temp_lc] += 1;
							}
						}
						/*elseif (isNetTypeLTE($dynamic_value["netType"]) && isSiteLTE($dynamic_value["lac"], $dynamic_value["cellId"])){
							$lte_length += $temp_value;
							//把用户占用的小区号存入数组
							if(!isset($user_lc[$voice["staticID"]])){
								$user_lc[$voice["staticID"]][$temp_lc] = 1;
							}elseif(!array_key_exists($temp_lc,$user_lc[$voice["staticID"]])){
								$user_lc[$voice["staticID"]][$temp_lc] = 1;
							}else{
								if($temp_lc != $temp_prv_lc)
									$user_lc[$voice["staticID"]][$temp_lc] += 1;
							}
						}*/
					}
					$previous_time = strtotime($dynamic_value["startDateTime"]);
					$temp_prv_lc = $temp_lc;
				}
			}
			if($td_length == 0 && $gsm_length == 0 && $lte_length == 0)
				continue;
			//将本次的语音时长结果放入数组
			if(isset($result[$voice["staticID"]])){
				$result[$voice["staticID"]]["td"] += $td_length;
				$result[$voice["staticID"]]["gsm"] += $gsm_length;
				$result[$voice["staticID"]]["lte"] += $lte_length;
			}else{
				$result[$voice["staticID"]]["td"] = $td_length;
				$result[$voice["staticID"]]["gsm"] = $gsm_length;
				$result[$voice["staticID"]]["lte"] = $lte_length;
			}
		}
		unset($voice_service);
		//计算所有数据业务的结果
		foreach ($data_service as $data) {
			if(isset($dynamic_data[("d".$data["id"])]))
				$temp_dynamic_array = $dynamic_data[("d".$data["id"])];
			else 
				continue;					
			//上一次的动态数据时间-----gsm占用时长-------td占用时长----lte占用时长
			$previous_time = $gsm_length = $td_length = $lte_length = 0;
			$temp_dynamic_count = count($temp_dynamic_array);//动态数据的数量
			$temp_prv_lc = '*';
			foreach ($temp_dynamic_array as $key => $dynamic_value) {
				if(($dynamic_value["startDateTime"] >= $data["startDateTime"]) && ($dynamic_value["startDateTime"] <= $data["stopDateTime"])){
					if($dynamic_value["cellId"] == "0" || $dynamic_value["lac"] == "0"){
						$previous_time = strtotime($dynamic_value["startDateTime"]);
						continue;
					}
					$temp_lc = $dynamic_value['lac']."-".$dynamic_value['cellId'];
					$temp_value = 0;
					if($key == 0 || empty($previous_time)){
						$temp_value = $user_start_time_time < strtotime($data["startDateTime"])?(strtotime($dynamic_value["startDateTime"])-strtotime($data["startDateTime"])):(strtotime($data["startDateTime"])-strtotime($dynamic_value["startDateTime"]));
					}else{ 
						$temp_value = strtotime($dynamic_value["startDateTime"])-$previous_time;	
					}
					if($temp_dynamic_count == ($key+1)){
						$temp_value += ($user_end_time_time<strtotime($data["stopDateTime"]))?($user_end_time_time-strtotime($dynamic_value["startDateTime"])):(strtotime($data["stopDateTime"])-strtotime($dynamic_value["startDateTime"]));				
					}
					if($temp_value > 0 || ($key == 0 && $temp_value == 0)){
						if(isNetTypeGSM($dynamic_value["netType"]) && isSiteGSM($dynamic_value["lac"], $dynamic_value["cellId"])){
							$gsm_length += $temp_value;
							//把用户占用的小区号存入数组
							if(!isset($user_lc[$data["staticID"]])){
								$user_lc[$data["staticID"]][$temp_lc] = 1;
							}elseif(!array_key_exists($temp_lc,$user_lc[$data["staticID"]])){
								$user_lc[$data["staticID"]][$temp_lc] = 1;
							}else{
								if($temp_lc != $temp_prv_lc)
									$user_lc[$data["staticID"]][$temp_lc] += 1;
							}
						}elseif(isNetTypeTD($dynamic_value["netType"]) && isSiteTD($dynamic_value["lac"], $dynamic_value["cellId"])){
							$td_length += $temp_value;
							//把用户占用的小区号存入数组
							if(!isset($user_lc[$data["staticID"]])){
								$user_lc[$data["staticID"]][$temp_lc] = 1;
							}elseif(!array_key_exists($temp_lc,$user_lc[$data["staticID"]])){
								$user_lc[$data["staticID"]][$temp_lc] = 1;
							}else{
								if($temp_lc != $temp_prv_lc)
									$user_lc[$data["staticID"]][$temp_lc] += 1;
							}
						}elseif (isNetTypeLTE($dynamic_value["netType"]) && isSiteLTE($dynamic_value["lac"], $dynamic_value["cellId"])){
							$lte_length += $temp_value;
							//把用户占用的小区号存入数组
							if(!isset($user_lc[$data["staticID"]])){
								$user_lc[$data["staticID"]][$temp_lc] = 1;
							}elseif(!array_key_exists($temp_lc,$user_lc[$data["staticID"]])){
								$user_lc[$data["staticID"]][$temp_lc] = 1;
							}else{
								if($temp_lc != $temp_prv_lc)
									$user_lc[$data["staticID"]][$temp_lc] += 1;
							}
						}
					}
					$previous_time = strtotime($dynamic_value["startDateTime"]);
					$temp_prv_lc = $temp_lc;
				}
			}
			if($td_length == 0 && $gsm_length == 0 && $lte_length == 0)
				continue;
			//将本次的数据业务时长结果放入数组
			if(isset($result[$data["staticID"]])){
				$result[$data["staticID"]]["td"] += $td_length;
				$result[$data["staticID"]]["gsm"] += $gsm_length;
				$result[$data["staticID"]]["lte"] += $lte_length;
			}else{
				$result[$data["staticID"]]["td"] = $td_length;
				$result[$data["staticID"]]["gsm"] = $gsm_length;
				$result[$data["staticID"]]["lte"] = $lte_length;
			}
		}
		unset($data_service);
		if(!empty($result))
			Yii::app()->cache ->set($temp_cach, $result, 3600*24);
		if(!empty($user_lc))
			Yii::app()->cache ->set($temp_cach_user, $user_lc, 3600*24);
	}
	return $result;
}
function getOcpyTime($id,$tt,$opTime){
	
	$temp_time = json_decode($opTime,true);
	//print_r($temp_time);
	return round($temp_time[$id][$tt]/60,2);			
}

//获取用户在某一时间段内占用的小区
function getThisOcpData($id,$sor){
	$temp_data = json_decode($sor,true);
	//print_r($temp_data[$id]);
	$re_data = json_encode($temp_data[$id]);
	
  	return $re_data;
}

//获得小区的网络类型
function getNType($lac,$cid){
	if(isSiteTD($lac,$cid)){
		return "TD-SCDMA";
	}elseif(isSiteGSM($lac,$cid)){
		return "GSM";	
	}elseif (isSiteLTE($lac,$cid)){
		return "LTE";
	}else{
		return "未知网络";
	}
}

function getOcpyData($id,$lal,$str,$stp){
	$result = array();
	$result_temp = array();
	$voice_service = getDataBaseData("SELECT id,staticID,startDateTime,stopDateTime FROM `voice_service` where staticID=".$id." and startDateTime  < '".$stp."' and stopDateTime > '".$str."';");
	$vid="-1";	
	foreach($voice_service as $v){
		$vid.=",".$v['id'];
	}
	//得到所有符合的数据业务数据
	$data_service = getDataBaseData("SELECT id,staticID,startDateTime,stopDateTime FROM `data_service` where staticID=".$id." and startDateTime  < '".$stp."' and stopDateTime > '".$str."';");
	$did="-1";
	foreach($data_service as $v){
		$did.=",".$v['id'];
	}	
	$dynamic_information = getDataBaseData("select lac,cellId,voiceID,dataID,startDateTime,max(rssi) as maxr,max(RSRP) as maxrp from dynamic_information where (rssi<-47 or RSRP>-128 and RSRP<-40) and (voiceID in (".$vid.") or dataID in (".$did.")) and (startDateTime  between '".$str."' and '".$stp."') group by voiceID,dataID,lac,cellId;");	
//	if(empty($dynamic_information)){
//		$dynamic_information = getDataBaseData("select lac,cellId,voiceID,dataID,startDateTime,max(rssi) as maxr,max(RSRP) as maxrp from dynamic_information where (voiceID in (".$vid.") or dataID in (".$did.")) and (startDateTime  between '".$str."' and '".$stp."') group by voiceID,dataID,lac,cellId;");
//	}
	if(!empty($dynamic_information)){
		foreach ($dynamic_information as $key=>$val){	
			$lac_cid = $val['lac']."-".$val['cellId'];		
			if(array_key_exists($lac_cid, $lal)){
				if(isset($result_temp[$lac_cid])){
					$result_temp[$lac_cid]['num'] += 1;
					$result_temp[$lac_cid]['r'] += $val['maxr'];
					$result_temp[$lac_cid]['rp'] += $val['maxrp'];
				}else{
					$result_temp[$lac_cid]['num'] = 1;
					$result_temp[$lac_cid]['r'] = $val['maxr'];
					$result_temp[$lac_cid]['rp'] = $val['maxrp'];
				}
			}
		}
	}
	foreach ($result_temp as $k=>$v){
		$result[$k]['num'] = $lal[$k];
		if($v['num'] > 0){
			if($v['r'] == 0)
				$result[$k]['rssi'] = "无效数据";
			else
				$result[$k]['rssi'] = round($v['r']/$v['num'],0);
			if($v['rp'] == 0)
				$result[$k]['rsrp'] = "无效数据";
			else
				$result[$k]['rsrp'] = round($v['rp']/$v['num'],0);
		}else{
			$result[$k]['rssi'] = "无效数据";
			$result[$k]['rsrp'] = "无效数据";
		}
	}
	return $result;
}

/*--------------------------------------------------------------用户终端系统间互操作分析----------------------------------------------------------------------*/

function getUserSysCache($str,$stp,$sys_cache){
	ini_set('memory_limit', '512M');
	set_time_limit(0);
	$result = array();
	//得到所有符合条件的语音数据
	$voice_information=getDataBaseData("SELECT v.id,v.staticID,v.startDateTime,v.stopDateTime FROM `voice_service` v JOIN `static_information` s ON (v.staticID=s.id) where v.startDateTime  < '{$stp}' and v.stopDateTime > '{$str}';");
	$temp_v="-1";
	if(!empty($voice_information)){
		foreach($voice_information as $k=>$v){
			$temp_voice[$v['id']]['str']=$v['startDateTime'];
			$temp_voice[$v['id']]['stp']=$v['stopDateTime'];
			$temp_v.=",".$v['id'];
		}
	}
	//得到所有符合条件的数据业务数据
	$data_information=getDataBaseData("SELECT d.id,d.staticID,d.startDateTime,d.stopDateTime FROM `data_service` d JOIN `static_information` s ON (d.staticID=s.id) where d.startDateTime  < '{$stp}' and d.stopDateTime > '{$str}';");
	$temp_d="-1";
	if(!empty($data_information)){
		foreach($data_information as $k=>$v){
			$temp_data[$v['id']]['str']=$v['startDateTime'];
			$temp_data[$v['id']]['stp']=$v['stopDateTime'];
			$temp_d.=",".$v['id'];
		}
	}
	//得到所有符合条件的动态数据
	if(!empty($voice_information) || !empty($data_information)){
		$dynamic_information=getDataBaseData("select id,voiceID,dataID,startDateTime,cellId,lac,lng,lat,netType from dynamic_information where (voiceID in (".$temp_v.") or dataID in (".$temp_d.")) and (startDateTime  between '{$str}' and '{$stp}')  ORDER BY startDateTime  asc; ");			
	
		//循环动态数据，分语音和数据业务数据
		$dynamic_data = array();
		if(!empty($dynamic_information)){
			foreach ($dynamic_information as $value){
				if(!empty($value["voiceID"])&&isset($temp_voice[$value["voiceID"]])&&$value['startDateTime']>=$temp_voice[$value["voiceID"]]['str']&&$value['startDateTime']<=$temp_voice[$value["voiceID"]]['stp'])
				{
					$dynamic_data[("v".$value["voiceID"])][] = $value;
				}elseif(!empty($value["dataID"])&&isset($temp_data[$value["dataID"]])&&$value['startDateTime']>=$temp_data[$value["dataID"]]['str']&&$value['startDateTime']<=$temp_data[$value["dataID"]]['stp'])
				{
					$dynamic_data[("d".$value["dataID"])][] = $value;
				}
			}
		}
		unset($dynamic_information);
		//计算语音数据中系统间互操作数
		$result1=array();
		if(!empty($voice_information)){
			foreach($voice_information as $voice){
				if(isset($dynamic_data[("v".$voice['id'])])){										
					$temp_dynamic_array=$dynamic_data["v".$voice['id']];	
					foreach ($temp_dynamic_array as $k=>$v){
						if($v['cellId']!=0&&$v['lac']!=0){
							if(isNetTypeGSM($v['netType']) && isSiteGSM($v["lac"], $v["cellId"])){
								$result1[$voice["staticID"]][$v['voiceID']][$v['id']]['type']="G";
								$result1[$voice["staticID"]][$v['voiceID']][$v['id']]['time']=$v["startDateTime"];
								$result1[$voice["staticID"]][$v['voiceID']][$v['id']]['lac']=$v["lac"];
								$result1[$voice["staticID"]][$v['voiceID']][$v['id']]['cid']=$v["cellId"];
								$result1[$voice["staticID"]][$v['voiceID']][$v['id']]['lng']=$v["lng"];
								$result1[$voice["staticID"]][$v['voiceID']][$v['id']]['lat']=$v["lat"];
							}
							elseif(isNetTypeTD($v['netType']) && isSiteTD($v["lac"], $v["cellId"])){
								$result1[$voice["staticID"]][$v['voiceID']][$v['id']]['type']="T";
								$result1[$voice["staticID"]][$v['voiceID']][$v['id']]['time']=$v["startDateTime"];
								$result1[$voice["staticID"]][$v['voiceID']][$v['id']]['lac']=$v["lac"];
								$result1[$voice["staticID"]][$v['voiceID']][$v['id']]['cid']=$v["cellId"];
								$result1[$voice["staticID"]][$v['voiceID']][$v['id']]['lng']=$v["lng"];
								$result1[$voice["staticID"]][$v['voiceID']][$v['id']]['lat']=$v["lat"];
							}
						}
					} 
				}
			}
		}
		unset($voice_information);
		if(!empty($result1)){
			foreach($result1 as $k=>$v){				
				foreach($v as $k1=>$v1){
					$index="-1";
					$t_id = 0;
					$p_lac = 0;
					$p_cid = 0;
					foreach($v1 as $k2=>$v2){
						if($v2['type']!=$index && $index=='T'){	
							$temp_key = $t_id."-".$k2;
							$temp_val = "v,".$index."-".$v2['type'];
							$result[$k][$temp_key]['type']=$temp_val;	
							$result[$k][$temp_key]['p_lac']=$p_lac;
							$result[$k][$temp_key]['p_cid']=$p_cid;
							$result[$k][$temp_key]['n_lac']=$v2['lac'];
							$result[$k][$temp_key]['n_cid']=$v2['cid'];
							$result[$k][$temp_key]['lng']=$v2['lng'];
							$result[$k][$temp_key]['lat']=$v2['lat'];
							$result[$k][$temp_key]['time']=$v2['time'];
						}
						$index = $v2['type'];
						$t_id = $k2;
						$p_lac = $v2['lac'];
						$p_cid = $v2['cid'];
					}
				}	
			}
		}
		//计算数据业务
		$result2=array();
		if(!empty($data_information)){
			foreach($data_information as $data){
				if(isset($dynamic_data[("d".$data['id'])])){
					$temp_dynamic_array=$dynamic_data["d".$data['id']];							
					foreach ($temp_dynamic_array as $k=>$v){
						if($v['cellId']!=0&&$v['lac']!=0){
							if(isNetTypeGSM($v['netType']) && isSiteGSM($v["lac"], $v["cellId"])){
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['type']='G';
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['time']=$v["startDateTime"];
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['lac']=$v["lac"];
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['cid']=$v["cellId"];
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['lng']=$v["lng"];
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['lat']=$v["lat"];
							}
							elseif(isNetTypeTD($v['netType']) && isSiteTD($v["lac"], $v["cellId"])){
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['type']='T';
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['time']=$v["startDateTime"];
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['lac']=$v["lac"];
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['cid']=$v["cellId"];
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['lng']=$v["lng"];
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['lat']=$v["lat"];
							}
							elseif (isNetTypeLTE($v['netType']) && isSiteLTE($v["lac"], $v["cellId"]))	{
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['type']='L';
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['time']=$v["startDateTime"];
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['lac']=$v["lac"];
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['cid']=$v["cellId"];
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['lng']=$v["lng"];
								$result2[$data["staticID"]][$v['dataID']][$v['id']]['lat']=$v["lat"];
							}
						}
					} 
				}
			}
		}
		unset($data_information);
		if(!empty($result2)){//print_r($result2);exit;
			foreach($result2 as $k=>$v){				
				foreach($v as $k1=>$v1){
					$index="-1";
					$t_id = 0;
					$p_lac = 0;
					$p_cid = 0;
					foreach($v1 as $k2=>$v2){
						if($index!="-1" && $v2['type']!=$index){	
							$temp_key = $t_id."-".$k2;
							$temp_val = "d,".$index."-".$v2['type'];
							$result[$k][$temp_key]['type']=$temp_val;	
							$result[$k][$temp_key]['p_lac']=$p_lac;
							$result[$k][$temp_key]['p_cid']=$p_cid;
							$result[$k][$temp_key]['n_lac']=$v2['lac'];
							$result[$k][$temp_key]['n_cid']=$v2['cid'];
							$result[$k][$temp_key]['lng']=$v2['lng'];
							$result[$k][$temp_key]['lat']=$v2['lat'];
							$result[$k][$temp_key]['time']=$v2['time'];
						}
						$index = $v2['type'];
						$t_id = $k2;
						$p_lac = $v2['lac'];
						$p_cid = $v2['cid'];
					}
				}	
			}
		}
		if(!empty($result)){
			Yii::app()->cache ->set($sys_cache, $result, 3600*24);
		}
	}
	return $result;
}
//根据controller传过来的数组数据统计用户互操作次数
function getCishubysj($id,$sor){ 
		  
  $temp_data = json_decode($sor,true);
  return count($temp_data[$id]);	  
}

//为查看详情传递数据
function getThisUData($id,$sor){ 
	$temp_data = json_decode($sor,true);
	$re_data = json_encode($temp_data[$id]);
  	return $re_data;	
}

//根据动态id获取当前用户所在的小区的时间
function getThisTime($id){
	$connection=Yii::app()->db;
	//语音
	$sql="select startDateTime from `dynamic_information` where id=".$id;
	$command=$connection->createCommand($sql);
	$rs=$command->queryRow();
	return $rs['startDateTime'];
}

//根据动态id获取当前用户所在的小区的lac
function getThisLac($id){
	$connection=Yii::app()->db;
	//语音
	$sql="select lac from `dynamic_information` where id=".$id;
	$command=$connection->createCommand($sql);
	$rs=$command->queryRow();
	return $rs['lac'];
}

//根据动态id获取当前用户所在的小区的cellId
function getThisCi($id){
	$connection=Yii::app()->db;
	//语音
	$sql="select cellId from `dynamic_information` where id=".$id;
	$command=$connection->createCommand($sql);
	$rs=$command->queryRow();
	return $rs['cellId'];
}

//根据动态id获取当前用户所在的小区的rssi
function getThisR($id,$t){
	$connection=Yii::app()->db;
	//语音
	if($t == 'L'){
		$sql="select RSRP from `dynamic_information` where id=".$id;
		$command=$connection->createCommand($sql);
		$rs=$command->queryRow();
		if($rs['RSRP']<-40 && $rs['RSRP']>-128)
			return $rs['RSRP'];
	}else{
		$sql="select rssi from `dynamic_information` where id=".$id;
		$command=$connection->createCommand($sql);
		$rs=$command->queryRow();
		if($rs['rssi']<-47 && $rs['rssi']>-117)
			return $rs['rssi'];
	}
	return "无效数据";
}

/*----------------------------------------------------------小区终端系统间互操作分析------------------------------------------------------------------------------*/

function getCellSysCache($str,$stp,$temp_cach){
	ini_set('memory_limit', '512M');
	set_time_limit(0);
	$result = array();
	//得到所有符合的语音数据
	$voice_service = getDataBaseData("SELECT v.id,v.staticID,v.startDateTime,v.stopDateTime FROM `voice_service` v JOIN `static_information` s ON (v.staticID=s.id) where v.startDateTime  < '{$stp}' and v.stopDateTime > '{$str}';");
//	getDataBaseData("SELECT id,startDateTime,stopDateTime FROM `voice_service` where startDateTime  < '".$stp."' and stopDateTime > '".$str."';");
	$vid="-1";			
	foreach($voice_service as $v){
		$temp_voice[$v['id']]['str']=$v['startDateTime'];
		$temp_voice[$v['id']]['stp']=$v['stopDateTime'];
		$vid.=",".$v['id'];
	}
	//得到所有符合的数据业务数据
	$data_service = getDataBaseData("SELECT d.id,d.staticID,d.startDateTime,d.stopDateTime FROM `data_service` d JOIN `static_information` s ON (d.staticID=s.id) where d.startDateTime  < '{$stp}' and d.stopDateTime > '{$str}';");
//	getDataBaseData("SELECT id,startDateTime,stopDateTime FROM `data_service` where startDateTime  < '".$stp."' and stopDateTime > '".$str."';");
	$did="-1";
	foreach($data_service as $v){
		$temp_data[$v['id']]['str']=$v['startDateTime'];
		$temp_data[$v['id']]['stp']=$v['stopDateTime'];
		$did.=",".$v['id'];
	}
	if(!empty($voice_service) || !empty($data_service)){
		$dynamic_information = getDataBaseData("select id,voiceID,dataID,lac,cellId,lng,lat,startDateTime,netType from dynamic_information where (voiceID in (".$vid.") or dataID in (".$did.")) and startDateTime  between '".$str."' and '".$stp."' order by startDateTime asc;");				
		//循环动态数据，分语音和数据业务数据
		if(!empty($dynamic_information)){
			$dynamic_data = array();			
			foreach ($dynamic_information as $value){
				if(isNetTypeGSM($value['netType']) && isSiteGSM($value['lac'],$value['cellId'])){
					$value['type'] = 'G';
				}elseif(isNetTypeTD($value['netType']) && isSiteTD($value['lac'],$value['cellId'])){
					$value['type'] = 'T';
				}elseif  (isNetTypeLTE($value['netType']) && isSiteLTE($value["lac"], $value["cellId"])){
					$value['type'] = 'L';
				}else{
					continue;
				}
				if(!empty($value["voiceID"]) && $value['startDateTime']>=$temp_voice[$value["voiceID"]]['str'] && $value['startDateTime']<=$temp_voice[$value["voiceID"]]['stp']){
					
					$dynamic_data["v".$value["voiceID"]][] = $value;
					
				}elseif(!empty($value["dataID"]) && $value['startDateTime']>=$temp_data[$value["dataID"]]['str'] && $value['startDateTime']<=$temp_data[$value["dataID"]]['stp']){
					
					$dynamic_data["d".$value["dataID"]][] = $value;
				}
			}
			unset($dynamic_information);
			if(!empty($dynamic_data)){
				$i=1;
				foreach($dynamic_data as $key=>$val){					
					$prv_id = 0;
					$prv_lc = '-1';
					$prv_type = '-1';
					foreach($val as $k=>$v){
						if($v['lac'] != 0 && $v['cellId'] != 0){
							$nxt_id = $v['id'];
							$nxt_lc = $v['lac']."-".$v['cellId'];
							$nxt_type = $v['type'];
							$j = $i."-";			
							if($prv_lc != '-1' && $prv_type != '-1'){
								if($prv_type != $nxt_type){	
									if(!empty($v['voiceID']) && $prv_type != 'G' || empty($v['voiceID'])){
										if(!empty($v['voiceID']) && $prv_type != 'G'){
											$result[$prv_lc][$j.$nxt_lc]['type'] = '语音业务';
											$result[$nxt_lc][$j.$prv_lc]['type'] = '语音业务';
										}else{
											$result[$prv_lc][$j.$nxt_lc]['type'] = '数据业务';
											$result[$nxt_lc][$j.$prv_lc]['type'] = '数据业务';
										}
										$result[$prv_lc][$j.$nxt_lc]['act'] = $prv_type."-".$nxt_type;
										$result[$prv_lc][$j.$nxt_lc]['pid'] = $prv_id;
										$result[$prv_lc][$j.$nxt_lc]['nid'] = $v['id'];
										$result[$prv_lc][$j.$nxt_lc]['lng'] = $v['lng'];
										$result[$prv_lc][$j.$nxt_lc]['lat'] = $v['lat'];
										$result[$prv_lc][$j.$nxt_lc]['time'] = $v['startDateTime'];
										$result[$prv_lc][$j.$nxt_lc]['side'] = '1';// 1表示从上一个小区切换到下一个小区
										
										$result[$nxt_lc][$j.$prv_lc]['act'] = $prv_type."-".$nxt_type;
										$result[$nxt_lc][$j.$prv_lc]['pid'] = $prv_id;
										$result[$nxt_lc][$j.$prv_lc]['nid'] = $v['id'];
										$result[$nxt_lc][$j.$prv_lc]['lng'] = $v['lng'];
										$result[$nxt_lc][$j.$prv_lc]['lat'] = $v['lat'];
										$result[$nxt_lc][$j.$prv_lc]['time'] = $v['startDateTime'];
										$result[$nxt_lc][$j.$prv_lc]['side'] = '0';// 0表示从下一个小区切换到上一个小区
									}
								}
							}		
							$prv_id = $nxt_id;				
							$prv_lc = $nxt_lc;
							$prv_type = $nxt_type;
							$i++;
						}
					}
				}
			}
			if(!empty($result))
				Yii::app()->cache ->set($temp_cach, $result, 3600*24);
		}
	}
	return $result;
}
//取得页面操作次数数据
function getcSysData($lac,$cid,$sor){
	$r=$lac."-".$cid;
	$temp_data = json_decode($sor,true);
	$time = count($temp_data[$r]);
	return $time;
}

//取得当前小区的数据
function getThisSData($lac,$cid,$sor){ 
	$r=$lac."-".$cid;
	$temp_data = json_decode($sor,true);
	$re_data = json_encode($temp_data[$r]);
	return $re_data;
}
 

/* ----------------------------------------------------小区数据业务分析--------------------------------------------------------------------------------------- */

function getCellDataCache($str,$stp,$md,$sys_tab_cache){
	ini_set('memory_limit', '512M');
	set_time_limit(0);	
	$md_type=netTypeStr($md);
	$dids = array();
	$lac_cid = array();	
	$temp_lcl = array();		
	$dynamic_inf=getDataBaseData("select id,lac,cellId,netType,upload_traffic,upload_time,download_traffic,download_time,avgTime,packetLoss from dynamic_information where dataID<>0 and  netType=(".$md_type.") and (uploadRate <>0 or downloadRate <>0 or avgTime <>0 or throughputRate <>0 or packetLoss <>0) and startDateTime >='".$str."' and startDateTime <='".$stp."';");			
	if(!empty($dynamic_inf)){
		foreach($dynamic_inf as $k=>$v){	
			if(($md == 'GPRS' || $md == 'EDGE') && isNetTypeGSM($v['netType']) && isSiteGSM($v['lac'],$v['cellId'])){
				$lcl = $v['lac']."-".$v['cellId'];
				$dids[] = $v['id'];
				if(!array_key_exists($lcl,$temp_lcl)){
					$temp_lcl[$lcl]['lac']=$v['lac'];	
					$temp_lcl[$lcl]['cellId']=$v['cellId'];
					$temp_lcl[$lcl]['Up'] = ($v['upload_traffic']==null? 0:$v['upload_traffic']);
					$temp_lcl[$lcl]['Uptime'] = ($v['upload_time']==null? 0:$v['upload_time']);
					$temp_lcl[$lcl]['Down'] = ($v['download_traffic']==null? 0:$v['download_traffic']);
					$temp_lcl[$lcl]['Downtime'] = ($v['download_time']==null? 0:$v['download_time']);
					$temp_lcl[$lcl]['Time'] = $v['avgTime'];
					$temp_lcl[$lcl]['Loss'] = $v['packetLoss'];
					$temp_lcl[$lcl]['counts'] = 1;
				}else{
					if($v['upload_traffic'] != null)
						$temp_lcl[$lcl]['Up'] += $v['upload_traffic'];
					if($v['upload_time'] != null)
						$temp_lcl[$lcl]['Uptime'] += $v['upload_time'];
					if($v['download_traffic'] != null)
						$temp_lcl[$lcl]['Down'] += $v['download_traffic'];
					if($v['download_time'] != null)
						$temp_lcl[$lcl]['Downtime'] += $v['download_time'];
					$temp_lcl[$lcl]['Time'] += $v['avgTime'];
					$temp_lcl[$lcl]['Loss'] += $v['packetLoss'];
					$temp_lcl[$lcl]['counts'] += 1;
				}
			}elseif($md == 'HSDPA' && isNetTypeTD($v['netType']) && isSiteTD($v['lac'],$v['cellId'])){
				$lcl = $v['lac']."-".$v['cellId'];
				$dids[] = $v['id'];
				if(!array_key_exists($lcl,$temp_lcl)){
					$temp_lcl[$lcl]['lac']=$v['lac'];	
					$temp_lcl[$lcl]['cellId']=$v['cellId'];	
					$temp_lcl[$lcl]['Up'] = ($v['upload_traffic']==null? 0:$v['upload_traffic']);
					$temp_lcl[$lcl]['Uptime'] = ($v['upload_time']==null? 0:$v['upload_time']);
					$temp_lcl[$lcl]['Down'] = ($v['download_traffic']==null? 0:$v['download_traffic']);
					$temp_lcl[$lcl]['Downtime'] = ($v['download_time']==null? 0:$v['download_time']);
					$temp_lcl[$lcl]['Time'] = $v['avgTime'];
					$temp_lcl[$lcl]['Loss'] = $v['packetLoss'];
					$temp_lcl[$lcl]['counts'] = 1;
				}else{
					if($v['upload_traffic'] != null)
						$temp_lcl[$lcl]['Up'] += $v['upload_traffic'];
					if($v['upload_time'] != null)
						$temp_lcl[$lcl]['Uptime'] += $v['upload_time'];
					if($v['download_traffic'] != null)
						$temp_lcl[$lcl]['Down'] += $v['download_traffic'];
					if($v['download_time'] != null)
						$temp_lcl[$lcl]['Downtime'] += $v['download_time'];
					$temp_lcl[$lcl]['Time'] += $v['avgTime'];
					$temp_lcl[$lcl]['Loss'] += $v['packetLoss'];
					$temp_lcl[$lcl]['counts'] += 1;
				}
			}
		}
		unset($dynamic_inf);
		$lac_cid['id'] = $dids;
		foreach($temp_lcl as $k=>$v){
			if($v['Uptime'] != 0){
				$lac_cid[$k]['avgUp'] = round($v['Up']/$v['Uptime'],4);
			}else{
				$lac_cid[$k]['avgUp'] = ""; 
			}
			if($v['Downtime'] != 0){
				$lac_cid[$k]['avgDown'] = round($v['Down']/$v['Downtime'],4);
				$lac_cid[$k]['avgTp'] = round($v['Down']/$v['Downtime'],4);
			}else{
				$lac_cid[$k]['avgDown'] = "";
				$lac_cid[$k]['avgTp'] = "";
			}
			if($v['counts'] != 0){
				$lac_cid[$k]['avgTime'] = round($v['Time']/$v['counts']/1000,2);
				$lac_cid[$k]['avgLsp'] = round($v['Loss']/$v['counts'],4);
			}else{
				$lac_cid[$k]['avgTime'] = "";
				$lac_cid[$k]['avgLsp'] = "";
			}
		}
		unset($temp_lcl); 
		if(!empty($lac_cid))
			Yii::app()->cache ->set($sys_tab_cache, $lac_cid, 3600*24);//表格数据缓存
	}	
	return $lac_cid;
}
//取得页面操作次数数据
function getDateD($lac,$cid,$field,$sor){
	$r=$lac."-".$cid;
	$temp_data = json_decode($sor,true);
	//print_r($temp_data);exit;
	$data = $temp_data[$r][$field];
	unset($temp_data);
	return $data;
}
//计算平均上传速率
/*
function getAvgUp($lac,$cid,$type,$str,$stp){
	$uprate=0;
	$count = 0;
	$connection=Yii::app()->db;	
	$sq="select id from network_type where nettype='".$type."'";
	$command = $connection->createCommand($sq);
	$rs = $command->queryRow();	
	$sql ="SELECT sum(uploadRate) as sum,count(id) as counts FROM `dynamic_information` where uploadRate is not null and uploadRate<>0 and netType='".$rs['id']."' and lac=".$lac." and cellId=".$cid." and startDateTime>='".$str."' and startDateTime<='".$stp."'";
	$command = $connection->createCommand($sql);
	$rs = $command->queryRow();		
	if($rs['counts']!=0){
		$uprate=$rs['sum']/$rs['counts'];
	}
	return round($uprate,2);
}
*/
function getAvgUp($lac,$cid,$type,$str,$stp){
	$uprate=null;
	//$count = 0;
	$connection=Yii::app()->db;	
	$type_id=netTypeStr($type);

	$sql="select SUM(upload_traffic) su,SUM(upload_time) st from dynamic_information 
	where dataID<>0 and lac=".$lac." and cellId=".$cid."  and netType=".$type_id." and upload_traffic!=0 and upload_time!=0 and startDateTime>='".$str."' and startDateTime<='".$stp."'";
	$command = $connection->createCommand($sql);
	$rs = $command->queryRow();		
	//print_r($rs);
	if(!empty($rs)){
		if($rs['su']!=null&&$rs['st']!=null && $rs['st']!=0){
			$uprate=round(($rs['su']/$rs['st']),4);
		}
	}
	return $uprate;
	//return round($uprate,2);
}
/*
function getAvgUp2($lac,$cid,$type,$str,$stp){
	$connection=Yii::app()->db;	
	$sq="select id from network_type where nettype='".$type."'";
	$command = $connection->createCommand($sq);
	$rs = $command->queryRow();
	
	$sql="select dataID from dynamic_information where lac=".$lac." and cellId=".$cid."  and netType=".$rs['id']." and upload_traffic!=0 and upload_time!=0 and startDateTime>='".$str."' and startDateTime<='".$stp."' group by dataID";
	$command = $connection->createCommand($sql);
	$row = $command->queryALL();
	$str="null";
	if(!empty($row)){
		foreach($row as $v){
			if($str=="null"){
				$str='';
				$str.=$v['dataID'];
			}else{
				$str.=",".$v['dataID'];
			}
		}
	}
	$sql="select staticID from data_service where id in (".$str.") group by staticID";
	$command = $connection->createCommand($sql);
	$row = $command->queryALL();
	
	if(!empty($row)){
		foreach($row as $v){
			$sql="select id from data_service where staticID=".$row['id']." and startDateTime>='".$str."' and startDateTime<='".$stp."'";
			$command = $connection->createCommand($sql);
			$row = $command->queryALL();
			$str1="null;"
			foreach($row as $v){
				if($str=="null"){
					$str='';
					$str.=$v['id'];
				}else{
					$str.=",".$v['id'];
				}			
			}
			
			$sql="select SUM(upload_traffic) su,SUM(upload_time) st from dynamic_information where lac=".$lac." and cellId=".$cid."  
	and netType=".$rs['id']." and upload_traffic!=0 and upload_time!=0 and startDateTime>='".$str."' and startDateTime<='".$stp."' "
			
			$command = $connection->createCommand($sql);
			$row = $command->queryRow();
			
			
		}
	}
	
	
}
*/
//计算平均下载速率
/*
function getAvgDown($lac,$cid,$type,$str,$stp){
	$downrate=0;
	//$count = 0;
	$connection=Yii::app()->db;	
	$sq="select id from network_type where nettype='".$type."'";
	$command = $connection->createCommand($sq);
	$rs = $command->queryRow();
	$sql ="SELECT sum(downloadRate) as sum,count(id) as counts FROM `dynamic_information` where dataID<>0 and downloadRate<>0 and netType='".$rs['id']."' and lac=".$lac." and cellId=".$cid." and startDateTime>='".$str."' and startDateTime<='".$stp."'";
	$command = $connection->createCommand($sql);
	$rs = $command->queryRow();		
	if($rs['counts']!=0){
		$downrate=$rs['sum']/$rs['counts'];
	}
	return round($downrate,2);
}*/

function getAvgDown($lac,$cid,$type,$str,$stp){
	$downrate=null;
	//$count = 0;
	$connection=Yii::app()->db;	
	$type_id=netTypeStr($type);	

	$sql="select SUM(download_traffic) sd,SUM(download_time) st from dynamic_information 
	where dataID<>0 and lac=".$lac." and cellId=".$cid."  and netType=".$type_id." and upload_traffic!=0 and upload_time!=0 and startDateTime>='".$str."' and startDateTime<='".$stp."'";
	$command = $connection->createCommand($sql);
	$rs = $command->queryRow();		
	//print_r($rs);
	if(!empty($rs)){
		if($rs['sd']!=null&&$rs['st']!=null&&$rs['st']!=0){
			$downrate=round(($rs['sd']/$rs['st']),4);
		}
	}
	return $downrate;
	//return round($uprate,2);
}
//计算平均时延
function getAvgTime($lac,$cid,$type,$str,$stp){
	$avgtime=0;
	$count = 0;
	$connection=Yii::app()->db;	
	$type_id=netTypeStr($type);
	$sql ="SELECT sum(avgTime) as sum,count(id) as counts FROM `dynamic_information` where avgTime is not null and dataID<>0 and netType='".$type_id."' and lac=".$lac." and cellId=".$cid." and startDateTime>='".$str."' and startDateTime<='".$stp."'";
	$command = $connection->createCommand($sql);
	$rs = $command->queryRow();
	if($rs['counts']!=0){
		$avgtime=$rs['sum']/$rs['counts'];
	}
	$time=round($avgtime/1000,2);
	return $time;
}

//计算平均吞吐率(同平均下载速率)
/*
function getAvgTP($lac,$cid,$type,$str,$stp){
	$tprate=0;
	//$count = 0;
	$connection=Yii::app()->db;	
	$sq="select id from network_type where nettype='".$type."'";
	$command = $connection->createCommand($sq);
	$rs = $command->queryRow();
	$sql ="SELECT sum(downloadRate) as sum,count(id) as counts FROM `dynamic_information` where dataID<>0 and downloadRate<>0 and netType='".$rs['id']."' and lac=".$lac." and cellId=".$cid." and startDateTime>='".$str."' and startDateTime<='".$stp."'";
	$command = $connection->createCommand($sql);
	$rs = $command->queryRow();		
	if($rs['counts']!=0){
		$tprate=$rs['sum']/$rs['counts'];
	}
	return round($tprate,2);
}*/

function getAvgTP($lac,$cid,$type,$str,$stp){
	$tprate=null;
	//$count = 0;

	$connection=Yii::app()->db;	
	$type_id=netTypeStr($type);	

	$sql="select SUM(download_traffic) sd,SUM(download_time) st from dynamic_information 
	where dataID<>0 and lac=".$lac." and cellId=".$cid."  and netType=".$type_id." and upload_traffic!=0 and upload_time!=0 and startDateTime>='".$str."' and startDateTime<='".$stp."'";
	$command = $connection->createCommand($sql);
	$rs = $command->queryRow();		
	//print_r($rs);
	if(!empty($rs)){
		if($rs['sd']!=null&&$rs['st']!=null&&$rs['st']!=0){
			$tprate=round(($rs['sd']/$rs['st']),4);
		}
	}
	return $tprate;
	//return round($uprate,2);
}
//计算平均上丢包率
function getAvgLsp($lac,$cid,$type,$str,$stp){
	$lsrate=0;
	$count = 0;
	$connection=Yii::app()->db;	
	$type_id=netTypeStr($type);
	$sql ="SELECT sum(packetLoss) as sum,count(id) as counts FROM `dynamic_information` where dataID<>0 and packetLoss is not null and netType='".$type_id."' and lac=".$lac." and cellId=".$cid." and startDateTime>='".$str."' and startDateTime<='".$stp."'";
	$command = $connection->createCommand($sql);
	$rs = $command->queryRow();
	if($rs['counts']!=0){
		$lsrate=$rs['sum']/$rs['counts'];
	}
	return round($lsrate,2);
}


/*--------------------------------------------------------小区覆盖能力分析-----------------------------------------------------------------------------*/
//执行生成缓存
function getCoverTableDate($temp_cach,$str,$stp,$md,$md_type){
	ini_set('memory_limit', '512M');
	set_time_limit(0);
	$sor = array();
	$voice_service = getDataBaseData("SELECT id,staticID,startDateTime,stopDateTime FROM `voice_service` where startDateTime  < '{$stp}' and stopDateTime > '{$str}';");
	$vid="-1";		
	foreach($voice_service as $v){
		$vid.=",".$v['id'];
	}
	//得到所有符合的数据业务数据
	$data_service = getDataBaseData("SELECT id,staticID,startDateTime,stopDateTime FROM `data_service` where startDateTime  < '{$stp}' and stopDateTime > '{$str}';");
	$did="-1";
	foreach($data_service as $v){
		$did.=",".$v['id'];
	}			
	$dynamic_information = getDataBaseData("select voiceID,dataID,lac,cellId,startDateTime,max(rssi) as maxr from dynamic_information where lac<>0 and cellId<>0 and rssi<-47 and (voiceID in (".$vid.") or dataID in (".$did.")) and netType in (".$md_type.") and (startDateTime  between '".$str."' and '".$stp."') group by lac,cellId,voiceID,dataID;");					
	if(!empty($dynamic_information)){
		//将静态Id和语音业务id对应
		foreach($voice_service as $k=>$v){
			$v_sid[$v['id']]=$v['staticID'];
		}
		//将静态Id和数据业务id对应
		foreach($data_service as $k=>$v){
			$d_sid[$v['id']]=$v['staticID'];
		}
		$lac_cellId = array();
		foreach($dynamic_information as $key=>$val){
			$_temp=$val['lac']."-".$val['cellId'];
			$flag=false;
			//判断小区在不在基站表中
			if($md=="TD-SCDMA"){
				$flag=isSiteTD($val['lac'],$val['cellId']);
			}elseif($md=="GSM"){
				$flag=isSiteGSM($val['lac'],$val['cellId']);
			}
			if(!$flag)
				continue;
				
			if(!empty($val['voiceID'])){
				//语音业务
				if(!isset($lac_cellId[$_temp]['sid'])||!in_array($v_sid[$val['voiceID']],$lac_cellId[$_temp]['sid'])){
					$lac_cellId[$_temp]['sid'][]=$v_sid[$val['voiceID']];
					$lac_cellId[$_temp][$v_sid[$val['voiceID']]] = $val['maxr'];	
					$lac_cellId[$_temp]["c".$v_sid[$val['voiceID']]] =1;			
				}else{
					$lac_cellId[$_temp][$v_sid[$val['voiceID']]] += $val['maxr'];//同一用户的每次语音业务的最大rssi累加
					$lac_cellId[$_temp]["c".$v_sid[$val['voiceID']]] ++;		
				}
			}elseif(!empty($val['dataID'])){
				//数据业务					
				if(!isset($lac_cellId[$_temp]['sid'])||!in_array($d_sid[$val['dataID']],$lac_cellId[$_temp]['sid'])){
					$lac_cellId[$_temp]['sid'][]=$d_sid[$val['dataID']];
					$lac_cellId[$_temp][$d_sid[$val['dataID']]] = $val['maxr'];	
					$lac_cellId[$_temp]["c".$d_sid[$val['dataID']]] =1;			
				}else{
					$lac_cellId[$_temp][$d_sid[$val['dataID']]] += $val['maxr'];//同一用户的每次语音业务的最大rssi累加
					$lac_cellId[$_temp]["c".$d_sid[$val['dataID']]] ++;		
				}

			}
		}
		unset($dynamic_information);
		foreach($lac_cellId as $k=>$v){
			if(isset($v['sid'])){
				$sor[$k]['avgR'] =0;
				$sor[$k]['all'] = count($v['sid']);
				$sor[$k]['low'] = 0;
				$sor[$k]['lowRate']=0;
				$temp_r=0;
				foreach($v['sid'] as $i=>$r){
					$temp_rssi = $v[$r]/$v["c".$r];
					$temp_r += $temp_rssi;
					$sor[$k]['user'][$r] = round($temp_rssi,0);
				}
				if($sor[$k]['all']>0){
					$sor[$k]['avgR'] = round($temp_r/$sor[$k]['all'],0);
				}
				foreach($sor[$k]['user'] as $row){
					if($row<$sor[$k]['avgR']){
						$sor[$k]['low']++;
					}
				}
				if($sor[$k]['all']>0){
					$rate=$sor[$k]['low']/$sor[$k]['all']*100;
					$sor[$k]['lowRate'] = round($rate,2);
				}
			}				
		}
		if(!empty($sor))
			Yii::app()->cache ->set($temp_cach, $sor, 3600*24);
	}
	//print_r($sor);
	return $sor;
}
//获取显示数据
function getCoverData($lac,$cellid,$elm,$sor){
	
	$r=$lac."-".$cellid;
	$data_cover = json_decode($sor,true);
	//print_r($data_cover);exit;
	if($elm=='avgR'){
		return $data_cover[$r]['avgR'];
	}elseif($elm=='allC'){
		return $data_cover[$r]['all'];
	}elseif($elm=='lowC'){
		return $data_cover[$r]['low'];
	}elseif($elm=='lowR'){
		return $data_cover[$r]['lowRate'];
	}
}

//获取本小区下的用户id和平均rssi
function getThisData($lac,$cid,$sor){
	$r=$lac."-".$cid;
	$data_cover = json_decode($sor,true);
	$data_user = $data_cover[$r]['user'];
	return json_encode($data_user);
}

//查看详情   计算用户在某一小区某一时间段内的平均RSSI
function getavgR($lac,$cid,$id,$md,$str,$stp){
	$rssi=0;
	$count=0;
	$connection=Yii::app()->db;
	if($md=='TD-SCDMA')
		$md='TD';
	$m=netTypeStr($md);
	//语音业务
	$sql1 = "select id,startDateTime,stopDateTime from `voice_service` where staticID=".$id." and startDateTime  < '".$stp."' and stopDateTime > '".$str."'";
	$command1 = $connection->createCommand($sql1);
	$rs1 = $command1->queryAll();
	foreach($rs1 as $row1){
		$sql = "select max(rssi) as avgrssi from `dynamic_information` where lac=".$lac." and cellId=".$cid." and voiceID=".$row1['id']." and rssi<-47 and netType in (".$m.") and (startDateTime between '".$str."' and '".$stp."') and (startDateTime between '".$row1['startDateTime']."' and '".$row1['stopDateTime']."')";
		$command = $connection->createCommand($sql);
		$rs = $command->queryRow();
		if($rs['avgrssi']!=null){
			$rssi+=$rs['avgrssi'];
			$count++;
		}
	}
	//数据业务
	$sql2 = "select id,startDateTime,stopDateTime from `data_service` where staticID=".$id." and startDateTime  < '".$stp."' and stopDateTime > '".$str."'";
	$command2 = $connection->createCommand($sql2);
	$rs2 = $command2->queryAll();
	foreach($rs2 as $row2){
		$sql = "select max(rssi) as avgrssi from `dynamic_information` where lac=".$lac." and cellId=".$cid." and dataID=".$row2['id']." and rssi<-47 and netType in (".$m.") and (startDateTime between '".$str."' and '".$stp."') and (startDateTime between '".$row1['startDateTime']."' and '".$row1['stopDateTime']."')";
		$command = $connection->createCommand($sql);
		$rs = $command->queryRow();
		if($rs['avgrssi']!=null){
			$rssi+=$rs['avgrssi'];
			$count++;
		}
	}
	if($count!=0){
		$rssi=$rssi/$count;
	}
	return round($rssi,0);
}

//计算一段时间内用户的平均RSSI
function getURssi($lac,$cid,$id,$md,$str,$stp){
	$rssi=0;
	$count=0;
	if($md=='TD-SCDMA')
		$md='TD';
	$m=netTypeStr($md);
	$connection=Yii::app()->db;
	//语音业务
	$sql1 = "select id,startDateTime,stopDateTime from `voice_service` where staticID=".$id." and startDateTime  < '".$stp."' and stopDateTime > '".$str."'";
	$command1 = $connection->createCommand($sql1);
	$rs1 = $command1->queryAll();
	foreach($rs1 as $row1){
		$sql = "select max(rssi) as rs from `dynamic_information` where lac=".$lac." and cellId=".$cid." and voiceID=".$row1['id']." and rssi<-47 and netType in (".$m.") and (startDateTime between '".$str."' and '".$stp."') and (startDateTime between '".$row1['startDateTime']."' and '".$row1['stopDateTime']."')";
		$command = $connection->createCommand($sql);
		$rs = $command->queryRow();
		if($rs['rs']!=null){
			$rssi+=$rs['rs'];
			$count++;
		}
	}
	//echo $count;
	//数据业务
	$sql2 = "select id,startDateTime,stopDateTime from `data_service` where staticID=".$id." and startDateTime  < '".$stp."' and stopDateTime > '".$str."'";
	$command2 = $connection->createCommand($sql2);
	$rs2 = $command2->queryAll();
	foreach($rs2 as $row2){
		$sql = "select max(rssi) as rs from `dynamic_information` where lac=".$lac." and cellId=".$cid." and dataID=".$row2['id']." and rssi<-47 and netType in (".$m.") and (startDateTime between '".$str."' and '".$stp."') and (startDateTime between '".$row2['startDateTime']."' and '".$row2['stopDateTime']."')";
		$command = $connection->createCommand($sql);
		$rs = $command->queryRow();
		if($rs['rs']!=null){
			$rssi+=$rs['rs'];
			$count++;
		}
	}
	if($count!=0){
		$rssi=$rssi/$count;
		return round($rssi,0);
	}else{
		return "无效数据";	
	}
}

//计算某一时间段内小区平均RSSI
function getAvgRssi($lac,$cid,$md,$str,$stp){
	$ids=array();
	$rssi=0;
	if($md=='TD-SCDMA')
		$md='TD';
	$m=netTypeStr($md);
	$connection=Yii::app()->db;
	$sql="select voiceID from `dynamic_information` where lac=".$lac." and cellId=".$cid." and voiceID<>0 and rssi<-47 and netType in (".$m.") and startDateTime >'".$str."' and startDateTime <='".$stp."'";
	$command=$connection->createCommand($sql);
	$rs=$command->queryAll();
	$str1="-1";
	foreach($rs as $v){
		if($v!=null){
			$str1.=",".$v['voiceID'];	
		}
	}
	//echo $str1;
	//语音
	$sql1 ="select staticID from `voice_service` where id in (".$str1.") group by staticID";
	$command1 = $connection->createCommand($sql1);
	$rs1 = $command1->queryAll();
	foreach($rs1 as $row1){
		if(!in_array($row1['staticID'],$ids)){
			$ids[]=$row1['staticID'];
			$rssi+=getURssi($lac,$cid,$row1['staticID'],$md,$str,$stp);
		}
	}
	$sql="SELECT dataID FROM `dynamic_information` where lac=".$lac." and cellId=".$cid." and dataID<>0 and rssi<-47 and netType in (".$m.") and startDateTime >'".$str."' and startDateTime <='".$stp."'";
	$command=$connection->createCommand($sql);
	$rs=$command->queryAll();
	$str2="-1";
	foreach($rs as $v){
		if($v!=null){
			$str2.=",".$v['dataID'];	
		}
	}
	//数据
	$sql2 ="SELECT staticID FROM `data_service` where id in (".$str2.") group by staticID";
	
	$command2 = $connection->createCommand($sql2);
	$rs2 = $command2->queryAll();
	foreach($rs2 as $row2){
		if(!in_array($row2['staticID'],$ids)){
			$ids[]=$row2['staticID'];
			$rssi+=getURssi($lac,$cid,$row2['staticID'],$md,$str,$stp);
		}		
	}
	//$count=getCount($lac,$cid,$md,$str,$stp);
	$count=count($ids);
	//echo $count;
	if($count!=0){
		$rssi=$rssi/$count;
		return round($rssi,0);
	}else{
		return "无效数据";
	}
}

/*-------------------------------------------------------小区间乒乓切换分析-------------------------------------------------------------------*/
//获取LAC和cellId
function  getLacCI(){
	$lc=array();	
	$connection=Yii::app()->db;	
	$sql ="SELECT lac,cellId FROM `dynamic_information`where lac<>0 and cellId<>0 group by lac,cellId";
	$command = $connection->createCommand($sql);
	$rs = $command->queryAll();
	foreach($rs as $row){
		$lci=array();
		$lci['lac']=$row['lac'];
		$lci['ci']=$row['cellId'];
		$lc[]=$lci;
	}
	//print_r($lc);exit;
	return $lc;
}

//查询出所有发生了切换的源小区和目的小区并存入数组
function getLCarray(){
	$lcl=array();
	//取得指定的开始时间和结束时间
	$startdateTime=getTTime(); 
	$stopdateTime=getPTime();
	$alllc=getLacCI();
	//print_r($alllc);exit;
	$connection=Yii::app()->db;
	foreach($alllc as $v){
		//语音
		$sql1 ="SELECT voiceID FROM `dynamic_information` where lac=".$v['lac']." and cellId=".$v['ci']." and startDateTime>='".$startdateTime."' and startDateTime<='".$stopdateTime."' and dataID=0 group by voiceID";
		$command1 = $connection->createCommand($sql1);
		$rs1 = $command1->queryAll();
		//print_r($rs1);exit;
		foreach($rs1 as $row1){			
			if($v['voiceID']!=0){
				$sql="select startDateTime,stopDateTime from voice_service where id=".$v['voiceID'];
				$com=$connection->createCommand($sql);
				$tm=$com->queryRow();
			}else{
				$sql="select startDateTime,stopDateTime from data_service where id=".$v['dataID'];
				$com=$connection->createCommand($sql);
				$tm=$com->queryRow();
			}
			$sql2 ="SELECT lac,cellId FROM `dynamic_information` where voiceID=".$row1['voiceID']." and lac<>0 and cellId<>0 and ((startDateTime between '".$startdateTime."' and '".$stopdateTime."') and (startDateTime between '".$tm['startDateTime']."' and '".$tm['stopDateTime']."')) order by startDateTime asc";
			$command2 = $connection->createCommand($sql2);
			$rs2 = $command2->queryAll();
			foreach($rs2 as $v1){
				if($v1!=null){
					$str="";
					if($v['lac']!=$v1['lac']||$v['ci']!=$v1['cellId']){
						$str.=$v['lac'].",".$v['ci'].",".$v1['lac'].",".$v1['cellId'];
						//echo $str;echo "<hr />";
						if(in_array($str,$lcl)){
							continue;	
						}else{
							$lcl[]=$str;
						}
					}
				}
			}
		}
		//数据
		$sql1 ="SELECT dataID FROM `dynamic_information` where lac=".$v['lac']." and cellId=".$v['ci']." and startDateTime>='".$startdateTime."' and startDateTime<='".$stopdateTime."' and voiceID=0 group by dataID";
		$command1 = $connection->createCommand($sql1);
		$rs1 = $command1->queryAll();
		foreach($rs1 as $row1){
			if($v['voiceID']!=0){
				$sq="select startDateTime,stopDateTime from voice_service where id=".$v['voiceID'];
				$com=$connection->createCommand($sql);
				$tm=$com->queryRow();
			}else{
				$sq="select startDateTime,stopDateTime from data_service where id=".$v['dataID'];
				$com=$connection->createCommand($sql);
				$tm=$com->queryRow();
			}
			$sql2 ="SELECT lac,cellId FROM `dynamic_information` where dataID=".$row1['dataID']."and lac<>0 and cellId<>0 and ((startDateTime between '".$startdateTime."' and '".$stopdateTime."') and (startDateTime between '".$tm['startDateTime']."' and '".$tm['stopDateTime']."')) order by startDateTime limit 0,1";
			$command2 = $connection->createCommand($sql2);
			$rs2 = $command2->queryRow();
			foreach($rs2 as $v1){
				if($v1!=null){
					$str="";
					if($v['lac']!=$v1['lac']||$v['ci']!=$v1['cellId']){
						$str.=$v['lac'].",".$v['ci'].",".$v1['lac'].",".$v1['cellId'];
						if(in_array($str,$lcl)){
							continue;	
						}else{
							$lcl[]=$str;
						}
					}
				}
			}
		}
	}
	//print_r($lcl);
	return $lcl;
}

//计算本小区用户平均RSSI
function getRssi($lac,$cid,$str,$stp){
	$rssi=0;
	$count=0;
	$connection=Yii::app()->db;	
	$sql = "select max(rssi) as avgrssi from `dynamic_information` where lac=".$lac." and cellId=".$cid." and rssi<-47 and (startDateTime between '".$str."' and '".$stp."') group by voiceID,dataID";
	$command = $connection->createCommand($sql);
	$rs = $command->queryAll();
	foreach ($rs as $v){
		if($v['avgrssi']!=null){
			$rssi+=$v['avgrssi'];
			$count++;
		}
	}
	if($count!=0){
		$rssi=$rssi/$count;
		return round($rssi,0);
	}else{
		return "无效数据";	
	}
	
}

//计算乒乓切换次数
function getNmu($lc1,$c1,$lc2,$c2,$str,$stp){
	$count=0;
	$connection=Yii::app()->db;	
	$sql ="SELECT count(id) as counts FROM `cell_identity_change_record` where before_lac=".$lc1." and before_cellId=".$c1." and later_lac=".$lc2." and later_cellId=".$c2." and (change_time1 between '".$str."' and '".$stp."')";
	$command = $connection->createCommand($sql);
	$rs = $command->queryRow();
	$count=$rs['counts'];
	return $count;
}

/*---------------------------------------------------------网络流量精确分析--------------------------------------------------------------------------*/

function get_UserappNetFlow($str,$stp,$get_imsi,$get_imei){
	//每个应用的流量统计（屏蔽"全部应用","unknowapp"加入"其它"选项中）
	$sql="SELECT SUM(Tx_2g+Rx_2g)/1024/1024 AS 2g,
	SUM(Tx_3g+Rx_3g)/1024/1024 AS 3g,
	SUM(Tx_4g+Rx_4g)/1024/1024 AS 4g,
	SUM(Tx_3g+Rx_3g+Tx_2g+Rx_2g)/1024/1024 AS All_count,Name
	FROM `traffic_data` WHERE static_information_id IN (select id from static_information where imsi=".$get_imsi." and imei=".$get_imei.") and Name<>'全部应用'
	and Name<>'unknowapp' and collection_time>='".$str."' and collection_time<='".$stp."' GROUP BY Name ORDER BY SUM(Tx_3g+Rx_3g+Tx_2g+Rx_2g)/1024/1024 DESC";
	$flow_data=Yii::app()->db->createCommand($sql)->queryall();
	//"unknowapp"项流量统计
	$sql1="SELECT SUM(Tx_2g+Rx_2g)/1024/1024 AS 2g,
	SUM(Tx_3g+Rx_3g)/1024/1024 AS 3g,
	SUM(Tx_4g+Rx_4g)/1024/1024 AS 4g,
	SUM(Tx_3g+Rx_3g+Tx_2g+Rx_2g)/1024/1024 AS All_count,Name
	FROM `traffic_data` WHERE static_information_id IN (select id from static_information where imsi=".$get_imsi." and imei=".$get_imei.") 
	and Name='unknowapp' and collection_time>='".$str."' and collection_time<='".$stp."'";
	$unkownapp=Yii::app()->db->createCommand($sql1)->queryrow();
	//所有流量求和
	$sum_all=0;
	foreach($flow_data as $v)
		$sum_all +=$v['All_count'];
	$sum_all +=	$unkownapp['All_count'];
	/**
	*流量统计数据算法
	*算法细节:1.前九显示,后面的并入'其它项'；
	*		  2.数据显示小数点后两位，<0.01并且>0的显示为'小于0.01MB'；
	*		  3.所有总流量小于0.01的全部并入'其它项'。
	*/
	$mark=1;
	//$others=array('Name'=>'其他','2g'=>0,'3g'=>0,'per'=>0,'All_count'=>0);
	$others=array('Name'=>'其他','2g'=>0,'3g'=>0,'4g'=>0,'per'=>0,'All_count'=>0);
	$top_ten=array();
	$pie_show_data=array();
	foreach($flow_data as $k=>$v){
		if($k>8){
			$others['2g'] +=$v['2g'];
			$others['3g'] +=$v['3g'];
			$others['4g'] +=$v['4g'];
			$others['All_count'] +=$v['All_count'];
			unset($flow_data[$k]);
		}
		else{
			if($v['All_count']<0.01&&$v['All_count']>0){
				$others['2g'] +=$v['2g'];
				$others['3g'] +=$v['3g'];
				$others['4g'] +=$v['4g'];
				$others['All_count'] +=$v['All_count'];
				unset($flow_data[$k]);
			}
			else{
				$flow_data[$k]['id']=$mark;
				$flow_data[$k]['2g']=($v['2g']<0.01&&$v['2g']>0)?'小于0.01MB':round($v['2g'],4);
				$flow_data[$k]['3g']=($v['3g']<0.01&&$v['3g']>0)?'小于0.01MB':round($v['3g'],4);
				$flow_data[$k]['4g']=($v['4g']<0.01&&$v['4g']>0)?'小于0.01MB':round($v['4g'],4);
				$flow_data[$k]['All_count']=($v['All_count']<0.01&&$v['All_count']>0)?'小于0.01MB':round($v['All_count'],4);
				$flow_data[$k]['per']=round(($v['All_count']/$sum_all)*100,2);
				array_push($pie_show_data,array('0'=>$flow_data[$k]['Name'],'1'=>round($v['All_count'],8)));
				$mark++;
			}
		}
	}
	if($sum_all > 0 || !empty($others)){
		$others['id']=$mark;
		$others['2g'] =(($others['2g']+$unkownapp['2g'])<0.01&&($others['2g']+$unkownapp['2g'])>0)?'小于0.01MB':round(($others['2g']+$unkownapp['2g']),4);
		$others['3g'] =(($others['3g']+$unkownapp['3g'])<0.01&&($others['3g']+$unkownapp['3g'])>0)?'小于0.01MB':round(($others['3g']+$unkownapp['3g']),4);
		$others['4g'] =(($others['4g']+$unkownapp['4g'])<0.01&&($others['4g']+$unkownapp['4g'])>0)?'小于0.01MB':round(($others['4g']+$unkownapp['4g']),4);
		$other_pie =$others['All_count']+$unkownapp['All_count'];
		$others['All_count'] =(($others['All_count']+$unkownapp['All_count'])<0.01&&($others['All_count']+$unkownapp['All_count'])>0)?'小于0.01MB':round(($others['All_count']+$unkownapp['All_count']),4);
		if($sum_all > 0 ){
			$others['per']=round(($other_pie/$sum_all)*100,2);
		}else{
			$others['per'] = 100;
			$other_pie = 100;
		}
		if($other_pie>0){
			array_push($pie_show_data,array('0'=>$others['Name'],'1'=>round($other_pie,8)));//$pie_show_data饼图数据
			array_push($flow_data,$others);//$flow_data列表数据
		}
	}
	return $data=array('pie'=>$pie_show_data,'list'=>$flow_data);
}
//用户各种流量数据提供2G,2G,总流量
function getKidsFlow($id,$keyWord,$s){
	$temp=explode(';',$s);
	foreach($temp as $k=>$v){
		$temp1=explode(',',$v);
		$temp2[$temp1[4]]=explode(',',$v);
	}
	return $temp2[$id][$keyWord];
}

//用户流量数据提供
function get_netflowdata($str,$stp,$sid=null){
	$sid = is_null($sid) ? '' : "static_information_id=$sid AND";
	$connection=Yii::app()->db;	
	$sql ="SELECT 
		round((sum(Tx_2g)+sum(Rx_2g))/1024/1024,4) as 2g, 
		round((sum(Tx_3g)+sum(Rx_3g))/1024/1024,4) as 3g, 
		round((sum(Tx_4g)+sum(Rx_4g))/1024/1024,4) as 4g,
		round((sum(Tx_2g)+sum(Rx_2g)+sum(Tx_3g)+sum(Rx_3g)+sum(Tx_4g)+sum(Rx_4g))/1024/1024,4) as All_count,
		static_information_id
	FROM `traffic_data` where $sid collection_time between '".$str."' and '".$stp."' and Name<>'全部应用' group by static_information_id";
	$command = $connection->createCommand($sql);
	$rs = $command->queryall();
	foreach($rs as $k=>$v){
		$rs[$k]=implode(',',$v);
	}
	return implode(';',$rs);
}

//根据用户id获取占用流量的应用程序
function getAppName($id){
	if(isset($_GET['str'])){
		$str = $_GET['str'];
	}else{
		$str=getTTime();
	}
	if(isset($_GET['stp'])){
		$stp = $_GET['stp'];
	}else{
		$stp=getPTime();
	}
	$app=array();
	$connection=Yii::app()->db;	
	$sql ="SELECT Name FROM `traffic_data` where static_information_id=".$id." and collection_time between '".$str."' and '".$stp."' group by Name";
	$command = $connection->createCommand($sql);
	$rs = $command->queryAll();
	foreach($rs as $row){
		$app[]=$row['Name'];
	}
	return $app;
}

//根据用户id和应用程序名获取2G流量
function getApp2G($id,$name){
	if(isset($_GET['str'])){
		$str = $_GET['str'];
	}else{
		$str=getTTime();
	}
	if(isset($_GET['stp'])){
		$stp = $_GET['stp'];
	}else{
		$stp=getPTime();
	}
	$g2=0;
	$connection=Yii::app()->db;	
	$sql ="SELECT Tx_2g,Rx_2g FROM `traffic_data` where static_information_id=".$id." and collection_time between '".$str."' and '".$stp."' group by Name";
	$command = $connection->createCommand($sql);
	$rs = $command->queryAll();
	foreach($rs as $row){
		$g2+=$row['Tx_2g']+$row['Rx_2g'];
	}
	$g2=$g2/(1024*1024);
	return round($g2,4);
}



//根据用户id和应用程序名获取3G流量
function getApp3G($id,$name){
	if(isset($_GET['str'])){
		$str = $_GET['str'];
	}else{
		$str=getTTime();
	}
	if(isset($_GET['stp'])){
		$stp = $_GET['stp'];
	}else{
		$stp=getPTime();
	}
	$g3=0;
	$connection=Yii::app()->db;	
	$sql ="SELECT Tx_3g,Rx_3g FROM `traffic_data` where static_information_id=".$id." and collection_time between '".$str."' and '".$stp."' group by Name";
	$command = $connection->createCommand($sql);
	$rs = $command->queryAll();
	foreach($rs as $row){
		$g3+=$row['Tx_3g']+$row['Rx_3g'];
	}
	$g3=$g3/(1024*1024);
	return round($g3,4);
}

//根据用户id和应用程序名获取应用的总流量
function getAppFlow($id,$name){
	if(isset($_GET['str'])){
		$str = $_GET['str'];
	}else{
		$str=getTTime();
	}
	if(isset($_GET['stp'])){
		$stp = $_GET['stp'];
	}else{
		$stp=getPTime();
	}
	$g=0;
	$connection=Yii::app()->db;	
	$sql ="SELECT Tx_2g,Rx_2g,Tx_3g,Rx_3g FROM `traffic_data` where static_information_id=".$id." and collection_time between '".$str."' and '".$stp."' group by Name";
	$command = $connection->createCommand($sql);
	$rs = $command->queryAll();
	foreach($rs as $row){
		$g+=$row['Tx_2g']+$row['Rx_2g']+$row['Tx_3g']+$row['Rx_3g'];
	}
	$g=$g/(1024*1024);
	return round($g,4);
}

function getAppRate($id,$name){
	$flow=getAppFlow($id,$name);
	$allF=getTotalFlow($id);
	if($allF!=0){
		$rate=$flow/$allF*100;
	}
	return round($rate,2);
}
/* -----------------------全网流量计算------------------------*/

/*--------------------------------------------------------------------------------网络综合服务能力分析---------------------------------------------------------------------------------------*/

//计算小区终端系统间互操作次数的方法
function getCellCishu($lac,$cid,$str,$stp){
	//将时间转化为时间戳
	$user_start_time_time = strtotime($str);
	$user_end_time_time = strtotime($stp);
	$count = 0;
	$temp_cach="cellSys".$user_start_time_time.$user_end_time_time; //定义缓存文件名		
	$sor=Yii::app()->cache ->get($temp_cach);
	if(empty($sor)){
		$sor = getCellSysCache($str,$stp,$temp_cach);
	}
	$index = $lac."-".$cid;
	if(isset($sor[$index])){
		$count = count($sor[$index]);
	}
	return $count;
}




/************************************************************************************************************************************************/



//计算某一时间段内小区平均RSSI
function getnetAvg($lac,$cid){
	$ids=array();
	$rssi=0;	
	$connection=Yii::app()->db;
	$sql="select voiceID from `dynamic_information` where lac=".$lac." and cellId=".$cid." and voiceID<>0";
	$command=$connection->createCommand($sql);
	$rs=$command->queryAll();
	$str1="-1";
	foreach($rs as $v){
		if($v!=null){
			$str1.=",".$v['voiceID'];	
		}
	}
	$sql1 ="select staticID from `voice_service` where id in (".$str1.") group by staticID";
	$command1 = $connection->createCommand($sql1);
	$rs1 = $command1->queryAll();
	foreach($rs1 as $row1){
		if(in_array($row1['staticID'],$ids)){
			continue;	
		}else{
			$ids[]=$row1['staticID'];
		}
	}
	$sql="SELECT dataID FROM `dynamic_information` where lac=".$lac." and cellId=".$cid." and dataID<>0";
	$command=$connection->createCommand($sql);
	$rs=$command->queryAll();
	$str2="-1";
	foreach($rs as $v){
		if($v!=null){
			$str2.=",".$v['dataID'];	
		}
	}
	$sql2 ="SELECT staticID FROM `data_service` where id in (".$str2.") group by staticID";
	$command2 = $connection->createCommand($sql2);
	$rs2 = $command2->queryAll();
	foreach($rs2 as $row2){
		if(in_array($row2['staticID'],$ids)){
			continue;	
		}else{
			$ids[]=$row2['staticID'];
		}		
	}
	//print_r($ids);
	foreach($ids as $id){		
		$rssi+=getUserR($lac,$cid,$id);
	}
	$count=getUserC($lac,$cid);
	//echo $count;
	if($count!=0){
		$rssi=$rssi/$count;
	}
	return round($rssi,4);
}

function getUserR($lac,$cid,$id){
	$rssi=0;
	$count=0;
	$connection=Yii::app()->db;	
	//语音业务
	$sql1 = "select id from `voice_service` where staticID=".$id;
	$command1 = $connection->createCommand($sql1);
	$rs1 = $command1->queryAll();
	foreach($rs1 as $row1){
		$sql = "select max(rssi) as rs from `dynamic_information` where lac=".$lac." and cellId=".$cid." and voiceID=".$row1['id'];
		$command = $connection->createCommand($sql);
		$rs = $command->queryRow();
		if($rs['rs']!=null){
			$rssi+=$rs['rs'];
			//echo $rs['rs']."@";
			$count++;
		}
	}
	//数据业务
	$sql2 = "select id from `data_service` where staticID=".$id;
	$command2 = $connection->createCommand($sql2);
	$rs2 = $command2->queryAll();
	foreach($rs2 as $row2){
		$sql = "select max(rssi) as rs from `dynamic_information` where lac=".$lac." and cellId=".$cid." and dataID=".$row2['id'];
		$command = $connection->createCommand($sql);
		$rs = $command->queryRow();
		if($rs['rs']!=null){
			$rssi+=$rs['rs'];
			$count++;
		}
	}
	if($count!=0){
		$rssi=$rssi/$count;
	}
	return $rssi;
}

function getUserC($lac,$cid){
	$sid=array();
	$connection=Yii::app()->db;	
	$sql="select voiceID from `dynamic_information` where lac=".$lac." and cellId=".$cid." and voiceID<>0";
	$command=$connection->createCommand($sql);
	$rs=$command->queryAll();
	$str1="-1";
	foreach($rs as $v){
		if($v!=null){
			$str1.=",".$v['voiceID'];	
		}
	}
	//语音
	$sql1 ="SELECT staticID FROM `voice_service` where id in (".$str1.")";
	$command1 = $connection->createCommand($sql1);
	$rs1 = $command1->queryAll();
	//print_r($rs1);exit;
	foreach($rs1 as $v){
		if(in_array($v['staticID'],$sid)){
			continue;
		}else{
			$sid[]=$v['staticID'];
		}
	}
	$sql="select dataID from `dynamic_information` where lac=".$lac." and cellId=".$cid." and dataID<>0";
	$command=$connection->createCommand($sql);
	$rs=$command->queryAll();
	$str1="-1";
	foreach($rs as $v){
		if($v!=null){
			$str2.=",".$v['dataID'];	
		}
	}
	//数据
	$sql2 ="SELECT staticID FROM `data_service` where id in (".$str2.")";
	$command2 = $connection->createCommand($sql2);
	$rs2 = $command2->queryAll();
	//print_r($rs1);exit;
	foreach($rs2 as $v){
		if(in_array($v['staticID'],$sid)){
			continue;
		}else{
			$sid[]=$v['staticID'];
		}
	}
	$a=count($sid);
	return $a;
}
function getlowU($lac,$cid){
	$avgRssi=getnetAvg($lac,$cid);
	$count=getUserC($lac,$cid);
	$ids=array();
	$lcount=0;
	$rate=0;
	$connection=Yii::app()->db;	
	$sql="select voiceID from `dynamic_information` where lac=".$lac." and cellId=".$cid." and voiceID<>0";
	$command=$connection->createCommand($sql);
	$rs=$command->queryAll();
	$str1="-1";
	foreach($rs as $v){
		if($v!=null){
			$str1.=",".$v['voiceID'];	
		}
	}
	//语音业务用户
	$sql1 ="select staticID from `voice_service` where id in (".$str1.") group by staticID";
	$command1 = $connection->createCommand($sql1);
	$rs1 = $command1->queryAll();
	foreach($rs1 as $row1){
		if(in_array($row1['staticID'],$ids)){
			continue;	
		}else{
			$ids[]=$row1['staticID'];
		}
	}	
	$sql="SELECT dataID FROM `dynamic_information` where lac=".$lac." and cellId=".$cid." and dataID<>0";
	$command=$connection->createCommand($sql);
	$rs=$command->queryAll();
	$str2="-1";
	foreach($rs as $v){
		if($v!=null){
			$str2.=",".$v['dataID'];	
		}
	}
	//数据业务用户
	$sql2 ="SELECT staticID FROM `data_service` where id in (".$str2.") group by staticID";
	$command2 = $connection->createCommand($sql2);
	$rs2 = $command2->queryAll();
	foreach($rs2 as $row2){
		if(in_array($row2['staticID'],$ids)){
			continue;	
		}else{
			$ids[]=$row2['staticID'];
		}
	}
	foreach($ids as $id){
		$urssi=getUserR($lac,$cid,$id);
		if($avgRssi>$urssi){
			$lcount++;
		}
	}
	if($count!=0){
		$rate=$lcount/$count*100;
	}
	return round($rate,2);
}

//计算指定小区发生T-G切换的用户id
function getTids($lac,$cid){
	$ids=array();
	$connection=Yii::app()->db;
	$sql="select voiceID from `dynamic_information` where voiceID<>0 and lac=".$lac." and cellId=".$cid;
	$command=$connection->createCommand($sql);
	$rs=$command->queryAll();
	$str1="-1";
	foreach($rs as $v){
		if($v!=null){
			$str1.=",".$v['voiceID'];	
		}
	}
	$sql1="select staticID from `voice_service` where id in (".$str1.")";
	$command1=$connection->createCommand($sql1);
	$rs1=$command1->queryAll();
	//print_r($rs1);echo "---";
	foreach($rs1 as $row1){
		if($row1!=null&&(in_array($row1['staticID'],$ids)==false)){
			$ids[]=$row1['staticID'];
		}	
	}	
	$sql="select dataID from `dynamic_information` where dataID<>0 and lac=".$lac." and cellId=".$cid;
	$command=$connection->createCommand($sql);
	$rs=$command->queryAll();
	$str2="-1";
	foreach($rs as $v){
		if($v!=null){
			$str2.=",".$v['dataID'];	
		}
	}
	$sql2="select staticID from `data_service` where id in (".$str2.")";
	$command2 = $connection->createCommand($sql2);
	$rs2 = $command2->queryAll();
	foreach ($rs2 as $row2){
		if($row2!=null&&(in_array($row2['staticID'],$ids)==false)){
			$ids[]=$row2['staticID'];
		}
	}
	//print_r($ids);
	return $ids;
}

function test($md,$left,$on,$right,$down,$zoom,$str,$stp){
	$network=null;
	$time=microtime(1);
	if($zoom>=2){
		$connection=YII::app()->db;	
		//=======================================================================================================
		if($md=="GSM"){
			//GSM id
			$sql="select id from network_type where nettype in ('GPRS','EDGE','GSM')";
			$command=$connection->createCommand($sql);
			$row=$command->queryAll();
			
			//GSM 小区号
			$network=Yii::app()->cache->get('CellCover_GSM');
			if($network==null){
				$sql="select * from site_gsm";
				$command=$connection->createCommand($sql);
				$network=$command->queryAll();
				Yii::app()->cache->set("cellcover_GSM",$network);
			}
		}else if($md=="TD-SCDMA"){
			//TD id
			$sql="select id from network_type where nettype in ('TD-SCDMA','HSDPA')";
			$command=$connection->createCommand($sql);
			$row=$command->queryAll();
			
			//TD 小区号
			$network=Yii::app()->cache->get('CellCover_TD');
			if($network==null){	
				$sql="select * from site_td";
				$command=$connection->createCommand($sql);
				$network=$command->queryAll();
				
				Yii::app()->cache->set("cellcover_TD",$network);
				
			}
			
		}
		$nettype="null";
		foreach($row as $v){
			if($nettype=="null"){
				$nettype='';
				$nettype.=$v['id'];
			}else{
				$nettype.=",".$v['id'];
			}
		}
		
		//筛选 有数据的小区
		$sql="select lac from dynamic_information where startDateTime<='".$stp."' and startDateTime>='".$str."' and netType in (".$nettype.") group by lac";
		$command=$connection->createCommand($sql);
		$row=$command->queryAll();
		$lacs="null";
		foreach($row as $v){
			if($v['lac']!==null){
				if($lacs=="null"){
					$lacs='';
					$lacs.="'".$v['lac']."'";
				}else{
					$lacs.=",'".$v['lac']."'";
				}
			}	
		}
		$sql="select lac,cellId from dynamic_information where startDateTime<='".$stp."' and startDateTime>='".$str."' and lac in (".$lacs.") and netType in (".$nettype.") group by cellId";
		$command=$connection->createCommand($sql);
		$lc=$command->queryAll();
		//有数据的小区号
	//=======================================================================================================
		//所有小区数
		$data=array();

		$str1='';
		$strs='';
		foreach($network as $v){
			$lng=floatval($v['lng']);
			$lat=floatval($v['lat']);
			$left=floatval($left);
			$on=floatval($on);
			$right=floatval($right);
			$down=floatval($down);
			$cData = json_encode($coverData);
			
			if($lng>$left&&$lng<$right&&$lat>$on&&$lat<$down){
				foreach($lc as $vlc){
					if($vlc['lac']==$v['lac']&&$vlc['cellId']==$v['cellId']){
					//print_r($v);
						$rand=rand(1,5)/10000;
						//$rand=0;
						// $lac=$v['lac'];
						 //$cellId=$v['cellId'];
						 $lng=($v['lng']+$rand);
						 $lat=($v['lat']+$rand);
						 $name=$v['cell_name'];
						 $rssi=getCoverData($v['lac'],$v['cellId'],"avgR",'$cData');
						// $rssi=$v['rssi']=getAvgRssi($v['lac'],$v['cellId'],$md,$str,$stp);
						 $count=$v['count']=getCount($v['lac'],$v['cellId'],$md,$str,$stp);
						// $lowcount=$v['lowcount']=getLowCount($v['lac'],$v['cellId'],$md,$str,$stp);
						// $lowrate=$v['lowrate']=getLowRate($v['lac'],$v['cellId'],$md,$str,$stp);
						 
						 $count=0;
						 $lowcount=0;
						 $lowrate=0;
						
						$data[]=$v;
						
						if($str1!=''){
							$str1.=',';
						}
						$str1.='{ "lac": "'.$v['lac'].'","cellId": "'.$v['cellId'].'","lng": "'.$lng.'","lat": "'.$lat.'","rssi": "'.$rssi.'","count": "'.$count.'","lowcount": "'.$lowcount.'","lowrate": "'.$lowrate.'","name": "'.$name.'" }';
					}
				}
			}
			$strs='['.$str1.']';
		}
		//在地图上符合范围的小区号
	$time1=microtime(1);
	//echo $time1-$time;
	return $strs;
	}	
}

function getAjaxcellCovering($md,$str,$stp){
	$connection=YII::app()->db;
	
	if($md=="GSM"){
		$sql="select id from network_type where nettype in ('GPRS','EDGE','GSM')";
		$command=$connection->createCommand($sql);
		$row=$command->queryAll();
	}else if($md=="TD-SCDMA"){
		$sql="select id from network_type where nettype in ('TD-SCDMA','HSDPA')";
		$command=$connection->createCommand($sql);
		$row=$command->queryAll();
	}
	$nettype="null";
	foreach($row as $v){
		if($nettype=="null"){
			$nettype='';
			$nettype.=$v['id'];
		}else{
			$nettype.=",".$v['id'];
		}
	}
	$sql="select lac from dynamic_information where startDateTime<='".$stp."' and startDateTime>='".$str."' and netType in (".$nettype.") group by lac";
	$command=$connection->createCommand($sql);
	$row=$command->queryAll();
	$lacs="null";
	foreach($row as $v){
		if($v['lac']!==null){
			if($lacs=="null"){
				$lacs='';
				$lacs.="'".$v['lac']."'";
			}else{
				$lacs.=",'".$v['lac']."'";
			}
		}	
	}
	$sql="select lac,cellId from dynamic_information where startDateTime<='".$stp."' and startDateTime>='".$str."' and lac in (".$lacs.") and netType in (".$nettype.") group by cellId";
	$command=$connection->createCommand($sql);
	$row=$command->queryAll();
	
	$str1='';
	$strs='';
	
	//print_r($row);

	foreach($row as $v){
		$rand=rand(1,5)/10000;
		
		if($md=="GSM"){
			$sql="select * from site_gsm where lac='".$v['lac']."' and cellId='".$v['cellId']."'";
			$command=$connection->createCommand($sql);
			$row=$command->queryRow();
		}else{
		//if(empty($row)){
			$sql="select * from site_td where lac='".$v['lac']."' and cellId='".$v['cellId']."'";
			$command=$connection->createCommand($sql);
			$row=$command->queryRow();
		}
		
		//print_r($row);
		
		if(!empty($row)){
			 $lac=$v['lac'];
			 $cellId=$v['cellId'];
			 $lng=($row['lng']+$rand);
			 $lat=($row['lat']+$rand);
			 $name=$row['cell_name'];
			 $rssi=getAvgRssi($v['lac'],$v['cellId'],$md,$str,$stp);
			 $count=getCount($v['lac'],$v['cellId'],$md,$str,$stp);
			// $lowcount=getLowCount($v['lac'],$v['cellId'],$md,$str,$stp);
			// $lowrate=getLowRate($v['lac'],$v['cellId'],$md,$str,$stp);
			 
			 $lowcount=0;
			 $lowrate=0;
			 
			 if($str1!=''){
				$str1.=',';
			}
			$str1.='{ "lac": "'.$v['lac'].'","cellId": "'.$v['cellId'].'","lng": "'.$lng.'","lat": "'.$lat.'","rssi": "'.$rssi.'","count": "'.$count.'","lowcount": "'.$lowcount.'","lowrate": "'.$lowrate.'","name": "'.$name.'" }';
		}
	}
	$strs='['.$str1.']';
	return $strs;	
}	

function getGis_rssi($md,$left,$on,$right,$down,$zoom,$str,$stp){
	
	//if($zoom>5){
		$c=$md.$str.$stp."gis_rssi";
		$str='';
		$strs='';
		$datas=Yii::app()->cache->get($c);
		//print_r($datas);
		if($datas==null){
			if(count($datas)!=0){
				$connection=Yii::app()->db;
				if($md=="GSM"){
					$sql="select * from site_gsm";
					$command=$connection->createCommand($sql);
					$row=$command->queryAll();
				}else{
					$sql="select * from site_td";
					$command=$connection->createCommand($sql);
					$row=$command->queryAll();
				}
				foreach($row as $v){
					$id=$v['id'];
					$datas[$id]=$v;
				}
			}
		}
		if(!empty($datas)){
			foreach($datas as $key=>$val){
				if($val['lng']>$left&&$val['lng']<$right&&$val['lat']>$on&&$val['lat']<$down){
					$val['rssi']=-rand(50,115);
					$val['count']=rand(1,10);
					$data[]=$val;
					unset($datas[$key]);
				}
			}
			Yii::app()->cache->set($c,$datas,1200);
			//Yii::app()->cache->flush();
			if(!empty($data)){		
				foreach ($data as $v){
					if($str!=''){
						$str.=',';
					}
					$str.='{ "rssi": '.'"'.$v['rssi'].'", "lng": '.'"'.$v['lng'].'", "lat": '.'"'.$v['lat'].'", "lac": '.'"'.$v['lac'].'", "cellId": '.'"'.$v['cellId'].'", "name": '.'"'.$v['cell_name'].'"'.'}';
				}
				$strs='['.$str.']';
			}
		}
	//}
	return $strs;
}
function returnstate($state){
	$result="";
	if(!is_numeric($state))
	{
		 
	}
	else 
	{
		if($state<197)	
		{
			$result="很差";
		}elseif ($state<398)
		{
			$result="较差";
		}elseif ($state<601)
		{
			$result='一般';
		}
		else 
		{
			$result="良好";
		}					
	}
	return $result;
}

/*
 * 按时间段统计终端语音业务次数
 */
function voiceCount($staticID){
	$data=array();
	$connection=Yii::app()->db;
	//今天
	$sql1="select count(*) as num from voice_service where staticID=".$staticID." and startDateTime>'".date('Y-m-d',time())." 00:00:00'";
	$command1=$connection->createCommand($sql1);
	$row1=$command1->queryRow();
	$data[1]=$row1['num'];
	//3天
	$sql3="select count(*) as num from voice_service where staticID=".$staticID." and startDateTime>'".date('Y-m-d',strtotime("-2 day"))." 00:00:00'";
	$command3=$connection->createCommand($sql3);
	$row3=$command3->queryRow();
	$data[3]=$row3['num'];	
	//7天
	$sql7="select count(*) as num from voice_service where staticID=".$staticID." and startDateTime>'".date('Y-m-d',strtotime("-6 day"))." 00:00:00'";
	$command7=$connection->createCommand($sql7);
	$row7=$command7->queryRow();
	$data[7]=$row7['num'];	
	//15天
	$sql15="select count(*) as num from voice_service where staticID=".$staticID." and startDateTime>'".date('Y-m-d',strtotime("-14 day"))." 00:00:00'";
	$command15=$connection->createCommand($sql15);
	$row15=$command15->queryRow();
	$data[15]=$row15['num'];
	//30天
	$sql30="select count(*) as num from voice_service where staticID=".$staticID." and startDateTime>'".date('Y-m-d',strtotime("-29 day"))." 00:00:00'";
	$command30=$connection->createCommand($sql30);
	$row30=$command30->queryRow();
	$data[30]=$row30['num'];	
	//总计
	$sql99="select count(*) as num from voice_service where staticID=".$staticID." ";
	$command99=$connection->createCommand($sql99);
	$row99=$command99->queryRow();
	$data[99]=$row99['num'];
	return $data;
}
/*
 * 获取城市名词 递归获取省份
 */
function cityName($province_city_id){
	$parent='';
	$connection=Yii::app()->db;
	$sql="select * from province_city where id=".intval($province_city_id)." ";
	$command=$connection->createCommand($sql);
	$row=$command->queryRow();
	if($row['parent_id']){
		$parent=cityName($row['parent_id']);
	}
	$name=$parent.$row['region_name'];
	if(!$name) return '无法获取';
	return $parent.$row['region_name'];

}

	/*
	 * 
	 */
function userNetWorkAnalysis($staticId, $data = NULL){
	$valueArray = array(
		0=>array('color'=>'red', 'str'=>'差'),
		1=>array('color'=>'orange', 'str'=>'良'),
		2=>array('color'=>'green', 'str'=>'优'),
		3=>array('color'=>'gray', 'str'=>'无'),
	);

	$onclick = $underline = $title = null;

	if(!is_null($data)){
		$value = $valueArray[$data];
		$url = HelpTool::getStrValue('double',Yii::app()->createUrl('staticInformation/view', array('staticid'=>$staticId, 'usernet'=>'usernet')));
		$onclick = "onclick='javascript:showMore({$url});'";
		$underline = 'text-decoration:underline';
		$title = 'title="点击查看详情"';
	}else
		$value = $valueArray[3];

	echo 
<<<eot
	<a $onclick style="color:{$value['color']};$underline" href="#" $title >{$value['str']}</a>
eot;
}

function unJsonData($jsonData, $type='%')
{
	$returnData = '无合理分析数据';
	if(!!$jsonData && $jsonData!='null')
	{

		$jsonData = json_decode($jsonData,true);
//HelpTool::logTrace('aaaaaa', $jsonData);
		$returnData = $jsonData['analysis'];
		$Data = $jsonData['data'];
		if(!is_null($returnData))
		{
			if(is_array($returnData))
				if(!!($dataNum = count($returnData)))
					$returnData = array_sum($jsonData)/count($jsonData);

			if(is_numeric($returnData)){
				if(isset($Data['stream'])) $returnData = (100-round($returnData, 2)).$type;
				else $returnData = round($returnData, 2).$type;
			}
		}else
			$returnData = '无合理分析数据';
	}

	return $returnData;
}

?>