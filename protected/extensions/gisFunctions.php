<?php

//require_once(Yii::app()->basePath.'/extensions/gisFunctions.php');

	set_time_limit(0);
	ini_set('memory_limit', '2048M');

/** 
  *@name 根据指定经纬度和指定半径返回周边基站信息
  *@param float $lon 经度
  *@param float $lat 纬度
  *@param integer $radius 限制半径长度
  *@param integer $siteType 返回基站类型，0为不限制，1为GSM基站，3为TD基站，4为LET基站
  *@param integer $sitesNum 返回个数限制，0为不限制
  *@return Array $aroundSites 返回含有指定查询列的查询数据，无则返回空数组
  *@author 张洪源
  *@date 2014-06-10 16:26:04
  *
  **/
function getPointAroundSites($lon, $lat, $radius, $siteType = 0, $sitesNum = 0 )
{
	$ColumnNames = 'name,cellid,lac,angle,lon,lat,target'; //查询列
	$lon = lngrectify($lon); //经度纠偏
	$lat = latrectify($lat); //纬度纠偏
	$limit = ($sitesNum == 0) ? '' : "LIMIT $sitesNum"; //构造limit语句
	$condition = ($siteType == 0) ? '' : "AND target=$siteType"; //构造where语句
	$gisConfig = GISConfig::get(); //获取数据库配置参数
	pg_connect($gisConfig->pgConnectStr) or die ('connection failed'); //连接数据库
	$gisTableName = $gisConfig->layer->sitesLayer; //地理数据库基站表名称

	$queryReturn = pg_query("SELECT $ColumnNames FROM $gisTableName WHERE ST_DWithin($gisTableName.geom_data,ST_Transform(st_geometryfromtext('POINT($lon $lat)',4326),900913),$radius)  $limit"); //查询落在查询点指定半径内的基站信息
	$aroundSites = array(); //返回数据
	if(!!pg_num_rows($queryReturn)) //遍历
		$aroundSites = pg_fetch_all($queryReturn);

	return $aroundSites;
}

/** 
  *@name 根据指定经纬度返回地点信息(soso地图)
  *@param float $lon 经度
  *@param float $lat 纬度
  *@param int $returnType 返回值类型: 0为stdClass(取值:$return->value) 1为String
  *@param int $returnPointsType 是否返回附近的点: 0为不返回,1为返回
  *@return $returnType==0 : stdClass $addressInfo(nation,province,city,district,street,street_number)
  *@return $returnType==1 : String $addressInfo 
  *@author 张洪源
  *@date 2014-07-28 09:25:48
  *
  **/
function getPointAddressSoSo($lon,$lat,$returnType,$returnPointsType=0)
{
	$sGeoVer = 'v1';
	$sMapKey_array = array('5JZBZ-LTBAQ-BGO5M-GOI7G-FH6IH-HHFHS', 'SJOBZ-VBHAD-M4E42-PQFFF-6U7VK-MZB5S', 'MANBZ-HF42U-VQKVZ-4DJIV-3QLDF-FXFIR', 'UYDBZ-WUQW4-3IOUQ-XC4HH-J5T7K-B6FAQ', 'EUSBZ-4QH3Q-SGV5X-GVHOK-2TAIE-Y4BRS'); //soso map Key
	$sMapKey = $sMapKey_array[array_rand($sMapKey_array)]; //soso map Key


	$outputType = 'json'; // 返回数据类型
	$addressInfo = null; //返回值
	//var_dump($sMapKey);exit;
	$postUrl = "http://apis.map.qq.com/ws/geocoder/{$sGeoVer}/?location={$lat},{$lon}&coord_type=1&key={$sMapKey}&get_poi={$returnPointsType}&output={$outputType}";

	//API文档：http://open.map.qq.com/webservice_v1/guide-gcoder.html
	//http://apis.map.qq.com/ws/geocoder/v1/?location=31.580024103572,120.28798635757&coord_type=1&key=SJOBZ-VBHAD-M4E42-PQFFF-6U7VK-MZB5S&get_poi=1&output=json

	$phpCurl = curl_init();
	
	curl_setopt($phpCurl, CURLOPT_URL, $postUrl);
	//curl_setopt($phpCurl, CURLOPT_PORT , 80);
	curl_setopt($phpCurl, CURLOPT_RETURNTRANSFER, 1);  //返回结果
	curl_setopt($phpCurl, CURLOPT_CONNECTTIMEOUT_MS,3000); 
	curl_exec($phpCurl); 
	$jsonReturn = curl_multi_getcontent($phpCurl);//返回的数据
	curl_close($phpCurl);
	
	if($jsonReturn)
	{
		$returnData = json_decode($jsonReturn);
		if( isset($returnData->result) )
		{
			if ( $returnType == 0 )
				$addressInfo = $returnData->result->address_component; //std Class 
			if ( $returnType == 1 )
				$addressInfo = $returnData->result->address; //String
		}
	}
	return $addressInfo;
}


/** 
  *@name 
  *@param float $lon 经度
  *@author caofang
  *@date 2014-02-12 
  *
  **/
function getValueStyle($data ,$type)
{
	if($type == 0){
		$value = '<span style='.$data['launchHtmlValue'].'>'.$data['launchLac'].','. $data['launchCellid'].'</span>';
		return $value;
	}
	if($type == 1){
		$value = '<span style='.$data['receiveHtmlValue'].'>'.$data['receiveLac'].','. $data['receiveCellid'].'</span>';
		return $value;
	}
}
	

	
/** 
  *@name 根据指定经纬度返回地点信息
  *@param float $lon 经度
  *@param float $lat 纬度
  *@param int $returnType 返回值类型: 0为stdClass(取值:$return->value) 1为String
  *@return $returnType==0 : stdClass $addressInfo(province,city,district,street,street_number)
  *@return $returnType==1 : String $addressInfo 
  *@author 张洪源
  *@date 2013-11-05 08:04:16
  *
  **/
function getPointAddress($lon,$lat,$returnType,$returnPointsType=0)
{
	$bGeoVer = 'v2';
	$bMapKey = 'CC3836109a75a8a3c97efc912ab5ee33'; //baidu map Key
	$outputType = 'json'; // 返回数据类型
	$returnPoints = $returnPointsType; //是否返回附近的点(0为不返回,1为返回)
	$addressInfo = null; //返回值
	$postUrl = "http://api.map.baidu.com/geocoder/{$bGeoVer}/?ak={$bMapKey}&coordtype=wgs84ll&location={$lat},{$lon}&output={$outputType}&pois={$returnPoints}"; //请求地址
	//http://api.map.baidu.com/geocoder/v2/?ak=CC3836109a75a8a3c97efc912ab5ee33&coordtype=wgs84ll&location={$lat},{$lon}&output=json&pois=0
	//https://maps.google.com/maps/api/geocode/json?latlng=31.9625000000000000,120.1875000000000000&language=zh-CN&sensor=false
	$phpCurl = curl_init();
	
	curl_setopt($phpCurl, CURLOPT_URL, $postUrl);
	//curl_setopt($phpCurl, CURLOPT_PORT , 80);
	curl_setopt($phpCurl, CURLOPT_RETURNTRANSFER, 1);  //返回结果
	curl_setopt($phpCurl, CURLOPT_CONNECTTIMEOUT_MS,5000); 
	curl_exec($phpCurl); 
	$jsonReturn = curl_multi_getcontent($phpCurl);//返回的数据
	curl_close($phpCurl);
	
	if($jsonReturn)
	{
		$returnData = json_decode($jsonReturn);
		if( isset($returnData->result) )
		{
			if ( $returnType == 0 )
				$addressInfo = $returnData->result->addressComponent; //std Class
			if ( $returnType == 1 )
				$addressInfo = $returnData->result->formatted_address; //String
		}
	}
	return $addressInfo;
}

/** 
  *@name 根据传入坐标数据生成同步PostgreSQL语句
  *@param string $tableName 数据库表名称
  *@param int $gridID 栅格ID
  *@param string $address 栅格地址
  *@param float $centerLon 中心经度
  *@param float $centerLat 中心经纬度
  *@param float $minLon 栅格最小经度
  *@param float $minLat 栅格最小纬度
  *@param float $maxLon 栅格最大经度
  *@param float $maxLat 栅格最大纬度
  *@return array( 0=>PgSQL INSERT 语句 , 1=> 纠偏边范围 , 2=> array(纠偏中心点经纬度) )
  *@author 张洪源
  *@date 2013-11-06 15:15:50
  *@rewrite 2013-11-07 09:30:09
  **/
function drawGrid($tableName,$gridID,$address,$centerLon,$centerLat,$minLon,$minLat,$maxLon,$maxLat)
{
		$centerLon=lngrectify($centerLon);
		$centerLat=latrectify($centerLat);
		$minLon=lngrectify($minLon);
		$minLat=latrectify($minLat);
		$maxLon=lngrectify($maxLon);
		$maxLat=latrectify($maxLat);
		
		$points = $minLon." ".$maxLat.",".$minLon." ".$minLat.",".$maxLon." ".$minLat.",".$maxLon." ".$maxLat.",".$minLon." ".$maxLat;

		$char='INSERT INTO '.$tableName.' ("gid","gridid","address","centerlon","centerlat",geom_data) 
		VALUES ('.$gridID.','.$gridID.',\''.$address.'\','.$centerLon.','.$centerLat.',ST_Transform(st_geometryfromtext(\'POLYGON(('.$points.'))\',4326),900913));';
		
		$centerLonLat = $centerLon.",".$centerLat;
		return array(0=>$char,1=>$points,2=>$centerLonLat);
	}
	
	
	/** 
  * @name 根据传入坐标纠偏及生成构成多边形的点集
  * @param float $lon 经度
  * @param float $lat 纬度
  * @param float $angle 角度
  * @author 张洪源
  * @return array( 'lineX'=>纠偏经度 , 'lineY'=> 纠偏纬度 , 'points'=>多边形构成点集 )
  * @date 2014-07-23 15:38:37
  **/

	function getPOLYGONPoints($lon,$lat,$angle){
		$lon=lngrectify($lon);
		$lat=latrectify($lat);
		if($angle >= 0 && $angle <= 90)
			$angle = 90 - $angle;
		else if($angle > 90 && $angle <= 360)
			$angle = 450 - $angle;
		
		if($angle==-1){ //如果为圆形基站
			$k=240;
			$start_angle=0;
			$sizeX=0.00085000;
			$sizeY=0.00075556;
			$lineX=$lon;
			$lineY=$lat;
		}else{ //不为圆形基站
			$k=30;
			$start_angle=$angle - 22.5;
			$sizeX=0.00135000;
			$sizeY=0.00125581;
			$temp_arr[]=$lon." ".$lat;
			$lincenter = ($start_angle + 22.5) * pi() / 180;
			$lineX = $lon + $sizeX * cos($lincenter)/2;
			$lineY = $lat + $sizeY * sin($lincenter)/2;
		}
		
		for($i=0;$i <=$k ;$i++){
			$temp1=array();
			$cent_angle=($start_angle+$i*(3/2))*floatval(pi()/180);
			array_push($temp1,($lon+$sizeX*cos($cent_angle)));
			array_push($temp1,($lat+$sizeY*sin($cent_angle)));
			$temp_arr[]=implode(" ",$temp1);
		}
		//添加终止点
		if($angle==-1)
			$temp_arr[]=$temp_arr[0]; //圆形扇区的终止点与起始点相同
		else
			$temp_arr[]=$lon." ".$lat; //扇形的终止点和基站原点相同
		$points = implode(",",$temp_arr);
		return array('lineX'=>$lineX, 'lineY'=>$lineY, 'points'=>$points);
	}
	
/** 
  * @name 根据传入坐标数据生成同步PostgreSQL语句
  * @param string $tableName 数据库表名称
  * @param string $name 小区名称
  * @param string $lac LAC
  * @param string $cellId CELLID
  * @param string $angle 天线角度
  * @param string $lon 基站经度
  * @param string $lat 基站纬度
  * @param string $type 基站类型
  * @param string $GRRUData GRRU类型基站多边形数据
  * @return array( 0=>PgSQL INSERT 语句 , 1=> 纠偏边点集 , 2=> 纠偏中心经度, 3=> 纠偏中心纬度 )
  * @author 张洪源
  * @date 2014-07-22 17:33:26
  * @rewrite 2013-11-07 09:30:09
  **/
	function CommonCell($tableName,$name,$lac,$cellId,$angle,$lon,$lat,$type,$GRRUData = null){
		set_time_limit(0);
		ini_set('memory_limit', '2048M');
		$char='INSERT INTO '.$tableName.' ("name","cellid","lac","angle","target","lon","lat","centerlon","centerlat",geom_data) 
		VALUES (\''.$name.'\',\''.$cellId.'\',\''.$lac.'\',\''.$angle.'\',\''.$type.'\',\''.round($lon,4).'\',\''.round($lat,4).'\','; //构成语句

		$points = is_null($GRRUData) ? getPOLYGONPoints($lon,$lat,$angle) : $GRRUData; //如果$GRRUData不为NULL 则生成多边形数据

		$char .='\''.$points['lineX'].'\',\''.$points['lineY'].'\',ST_Transform(st_geometryfromtext(\'MULTIPOLYGON((('; //添加中心点及多边形语句
		$char .=$points['points']; //添加多边形构成点集
		$char .=")))',4326),900913));"; //添加转换坐标系
		return array(0=>$char,1=>$points['points'],2=>$points['lineX'],3=>$points['lineY']); //返回生成的信息数组
	}
	
	/**
	 * Google 经度纠偏
	 */
	function lngrectify($lng,$zoom=18)
	{
	    $x = ($lng + 180) * (256 << $zoom) / 360;
		//无锡:849 泰州:905 厦门:923 徐州:1070 宁波:775 南京:964 //增加=偏东
	    $x = round($x + GISConfig::get()->lngrectifyVal); 
	    $x = $x * 360/(256 << $zoom)-180;
	    return $x;
	}
	
	/**
	 * Google 纬度纠偏
	 */
	function latrectify($lat,$zoom=18)
	{
	    $siny = sin($lat * pi() / 180);
	    $y = log((1 + $siny) / (1 - $siny));
		//无锡:415 泰州:500 厦门:553 徐州:310 宁波:553 南京:463 增加=偏南
	    $y = round((128 << $zoom) * (1 - $y / (2 * pi())) + GISConfig::get()->latrectifyVal); 
	    $y = 2 * pi() * (1 - $y / (128 << $zoom));
	    $z = pow(exp(1), $y);
	    $siny = ($z - 1) / ($z + 1);
	    $y = asin($siny) * 180 / pi();
	    return $y;
	}
	
	/**
	 * @name 根据两点的经纬度求距离 
	 * @return 返回两点之间的距离，单位为米
	 * @author 曹芳
	 * @date 2014-04-01 
	 */
	function getDistance($beginLng,$beginLat,$endLng,$endLat){
		$dis = sqrt(pow(($endLng-$beginLng)*M_PI/180*6371229*cos(($beginLat+$endLat)/2*M_PI/180),2)+pow(($endLat-$beginLat)*M_PI/180*6371229,2));//平均半径
		return $dis;
	}
	
	