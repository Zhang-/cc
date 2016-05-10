<?php

//require_once(Yii::app()->basePath.'/extensions/gisFunctions.php');

	set_time_limit(0);
	ini_set('memory_limit', '2048M');

/** 
  *@name ����ָ����γ�Ⱥ�ָ���뾶�����ܱ߻�վ��Ϣ
  *@param float $lon ����
  *@param float $lat γ��
  *@param integer $radius ���ư뾶����
  *@param integer $siteType ���ػ�վ���ͣ�0Ϊ�����ƣ�1ΪGSM��վ��3ΪTD��վ��4ΪLET��վ
  *@param integer $sitesNum ���ظ������ƣ�0Ϊ������
  *@return Array $aroundSites ���غ���ָ����ѯ�еĲ�ѯ���ݣ����򷵻ؿ�����
  *@author �ź�Դ
  *@date 2014-06-10 16:26:04
  *
  **/
function getPointAroundSites($lon, $lat, $radius, $siteType = 0, $sitesNum = 0 )
{
	$ColumnNames = 'name,cellid,lac,angle,lon,lat,target'; //��ѯ��
	$lon = lngrectify($lon); //���Ⱦ�ƫ
	$lat = latrectify($lat); //γ�Ⱦ�ƫ
	$limit = ($sitesNum == 0) ? '' : "LIMIT $sitesNum"; //����limit���
	$condition = ($siteType == 0) ? '' : "AND target=$siteType"; //����where���
	$gisConfig = GISConfig::get(); //��ȡ���ݿ����ò���
	pg_connect($gisConfig->pgConnectStr) or die ('connection failed'); //�������ݿ�
	$gisTableName = $gisConfig->layer->sitesLayer; //�������ݿ��վ������

	$queryReturn = pg_query("SELECT $ColumnNames FROM $gisTableName WHERE ST_DWithin($gisTableName.geom_data,ST_Transform(st_geometryfromtext('POINT($lon $lat)',4326),900913),$radius)  $limit"); //��ѯ���ڲ�ѯ��ָ���뾶�ڵĻ�վ��Ϣ
	$aroundSites = array(); //��������
	if(!!pg_num_rows($queryReturn)) //����
		$aroundSites = pg_fetch_all($queryReturn);

	return $aroundSites;
}

/** 
  *@name ����ָ����γ�ȷ��صص���Ϣ(soso��ͼ)
  *@param float $lon ����
  *@param float $lat γ��
  *@param int $returnType ����ֵ����: 0ΪstdClass(ȡֵ:$return->value) 1ΪString
  *@param int $returnPointsType �Ƿ񷵻ظ����ĵ�: 0Ϊ������,1Ϊ����
  *@return $returnType==0 : stdClass $addressInfo(nation,province,city,district,street,street_number)
  *@return $returnType==1 : String $addressInfo 
  *@author �ź�Դ
  *@date 2014-07-28 09:25:48
  *
  **/
function getPointAddressSoSo($lon,$lat,$returnType,$returnPointsType=0)
{
	$sGeoVer = 'v1';
	$sMapKey_array = array('5JZBZ-LTBAQ-BGO5M-GOI7G-FH6IH-HHFHS', 'SJOBZ-VBHAD-M4E42-PQFFF-6U7VK-MZB5S', 'MANBZ-HF42U-VQKVZ-4DJIV-3QLDF-FXFIR', 'UYDBZ-WUQW4-3IOUQ-XC4HH-J5T7K-B6FAQ', 'EUSBZ-4QH3Q-SGV5X-GVHOK-2TAIE-Y4BRS'); //soso map Key
	$sMapKey = $sMapKey_array[array_rand($sMapKey_array)]; //soso map Key


	$outputType = 'json'; // ������������
	$addressInfo = null; //����ֵ
	//var_dump($sMapKey);exit;
	$postUrl = "http://apis.map.qq.com/ws/geocoder/{$sGeoVer}/?location={$lat},{$lon}&coord_type=1&key={$sMapKey}&get_poi={$returnPointsType}&output={$outputType}";

	//API�ĵ���http://open.map.qq.com/webservice_v1/guide-gcoder.html
	//http://apis.map.qq.com/ws/geocoder/v1/?location=31.580024103572,120.28798635757&coord_type=1&key=SJOBZ-VBHAD-M4E42-PQFFF-6U7VK-MZB5S&get_poi=1&output=json

	$phpCurl = curl_init();
	
	curl_setopt($phpCurl, CURLOPT_URL, $postUrl);
	//curl_setopt($phpCurl, CURLOPT_PORT , 80);
	curl_setopt($phpCurl, CURLOPT_RETURNTRANSFER, 1);  //���ؽ��
	curl_setopt($phpCurl, CURLOPT_CONNECTTIMEOUT_MS,3000); 
	curl_exec($phpCurl); 
	$jsonReturn = curl_multi_getcontent($phpCurl);//���ص�����
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
  *@param float $lon ����
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
  *@name ����ָ����γ�ȷ��صص���Ϣ
  *@param float $lon ����
  *@param float $lat γ��
  *@param int $returnType ����ֵ����: 0ΪstdClass(ȡֵ:$return->value) 1ΪString
  *@return $returnType==0 : stdClass $addressInfo(province,city,district,street,street_number)
  *@return $returnType==1 : String $addressInfo 
  *@author �ź�Դ
  *@date 2013-11-05 08:04:16
  *
  **/
function getPointAddress($lon,$lat,$returnType,$returnPointsType=0)
{
	$bGeoVer = 'v2';
	$bMapKey = 'CC3836109a75a8a3c97efc912ab5ee33'; //baidu map Key
	$outputType = 'json'; // ������������
	$returnPoints = $returnPointsType; //�Ƿ񷵻ظ����ĵ�(0Ϊ������,1Ϊ����)
	$addressInfo = null; //����ֵ
	$postUrl = "http://api.map.baidu.com/geocoder/{$bGeoVer}/?ak={$bMapKey}&coordtype=wgs84ll&location={$lat},{$lon}&output={$outputType}&pois={$returnPoints}"; //�����ַ
	//http://api.map.baidu.com/geocoder/v2/?ak=CC3836109a75a8a3c97efc912ab5ee33&coordtype=wgs84ll&location={$lat},{$lon}&output=json&pois=0
	//https://maps.google.com/maps/api/geocode/json?latlng=31.9625000000000000,120.1875000000000000&language=zh-CN&sensor=false
	$phpCurl = curl_init();
	
	curl_setopt($phpCurl, CURLOPT_URL, $postUrl);
	//curl_setopt($phpCurl, CURLOPT_PORT , 80);
	curl_setopt($phpCurl, CURLOPT_RETURNTRANSFER, 1);  //���ؽ��
	curl_setopt($phpCurl, CURLOPT_CONNECTTIMEOUT_MS,5000); 
	curl_exec($phpCurl); 
	$jsonReturn = curl_multi_getcontent($phpCurl);//���ص�����
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
  *@name ���ݴ���������������ͬ��PostgreSQL���
  *@param string $tableName ���ݿ������
  *@param int $gridID դ��ID
  *@param string $address դ���ַ
  *@param float $centerLon ���ľ���
  *@param float $centerLat ���ľ�γ��
  *@param float $minLon դ����С����
  *@param float $minLat դ����Сγ��
  *@param float $maxLon դ����󾭶�
  *@param float $maxLat դ�����γ��
  *@return array( 0=>PgSQL INSERT ��� , 1=> ��ƫ�߷�Χ , 2=> array(��ƫ���ĵ㾭γ��) )
  *@author �ź�Դ
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
  * @name ���ݴ��������ƫ�����ɹ��ɶ���εĵ㼯
  * @param float $lon ����
  * @param float $lat γ��
  * @param float $angle �Ƕ�
  * @author �ź�Դ
  * @return array( 'lineX'=>��ƫ���� , 'lineY'=> ��ƫγ�� , 'points'=>����ι��ɵ㼯 )
  * @date 2014-07-23 15:38:37
  **/

	function getPOLYGONPoints($lon,$lat,$angle){
		$lon=lngrectify($lon);
		$lat=latrectify($lat);
		if($angle >= 0 && $angle <= 90)
			$angle = 90 - $angle;
		else if($angle > 90 && $angle <= 360)
			$angle = 450 - $angle;
		
		if($angle==-1){ //���ΪԲ�λ�վ
			$k=240;
			$start_angle=0;
			$sizeX=0.00085000;
			$sizeY=0.00075556;
			$lineX=$lon;
			$lineY=$lat;
		}else{ //��ΪԲ�λ�վ
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
		//�����ֹ��
		if($angle==-1)
			$temp_arr[]=$temp_arr[0]; //Բ����������ֹ������ʼ����ͬ
		else
			$temp_arr[]=$lon." ".$lat; //���ε���ֹ��ͻ�վԭ����ͬ
		$points = implode(",",$temp_arr);
		return array('lineX'=>$lineX, 'lineY'=>$lineY, 'points'=>$points);
	}
	
/** 
  * @name ���ݴ���������������ͬ��PostgreSQL���
  * @param string $tableName ���ݿ������
  * @param string $name С������
  * @param string $lac LAC
  * @param string $cellId CELLID
  * @param string $angle ���߽Ƕ�
  * @param string $lon ��վ����
  * @param string $lat ��վγ��
  * @param string $type ��վ����
  * @param string $GRRUData GRRU���ͻ�վ���������
  * @return array( 0=>PgSQL INSERT ��� , 1=> ��ƫ�ߵ㼯 , 2=> ��ƫ���ľ���, 3=> ��ƫ����γ�� )
  * @author �ź�Դ
  * @date 2014-07-22 17:33:26
  * @rewrite 2013-11-07 09:30:09
  **/
	function CommonCell($tableName,$name,$lac,$cellId,$angle,$lon,$lat,$type,$GRRUData = null){
		set_time_limit(0);
		ini_set('memory_limit', '2048M');
		$char='INSERT INTO '.$tableName.' ("name","cellid","lac","angle","target","lon","lat","centerlon","centerlat",geom_data) 
		VALUES (\''.$name.'\',\''.$cellId.'\',\''.$lac.'\',\''.$angle.'\',\''.$type.'\',\''.round($lon,4).'\',\''.round($lat,4).'\','; //�������

		$points = is_null($GRRUData) ? getPOLYGONPoints($lon,$lat,$angle) : $GRRUData; //���$GRRUData��ΪNULL �����ɶ��������

		$char .='\''.$points['lineX'].'\',\''.$points['lineY'].'\',ST_Transform(st_geometryfromtext(\'MULTIPOLYGON((('; //������ĵ㼰��������
		$char .=$points['points']; //��Ӷ���ι��ɵ㼯
		$char .=")))',4326),900913));"; //���ת������ϵ
		return array(0=>$char,1=>$points['points'],2=>$points['lineX'],3=>$points['lineY']); //�������ɵ���Ϣ����
	}
	
	/**
	 * Google ���Ⱦ�ƫ
	 */
	function lngrectify($lng,$zoom=18)
	{
	    $x = ($lng + 180) * (256 << $zoom) / 360;
		//����:849 ̩��:905 ����:923 ����:1070 ����:775 �Ͼ�:964 //����=ƫ��
	    $x = round($x + GISConfig::get()->lngrectifyVal); 
	    $x = $x * 360/(256 << $zoom)-180;
	    return $x;
	}
	
	/**
	 * Google γ�Ⱦ�ƫ
	 */
	function latrectify($lat,$zoom=18)
	{
	    $siny = sin($lat * pi() / 180);
	    $y = log((1 + $siny) / (1 - $siny));
		//����:415 ̩��:500 ����:553 ����:310 ����:553 �Ͼ�:463 ����=ƫ��
	    $y = round((128 << $zoom) * (1 - $y / (2 * pi())) + GISConfig::get()->latrectifyVal); 
	    $y = 2 * pi() * (1 - $y / (128 << $zoom));
	    $z = pow(exp(1), $y);
	    $siny = ($z - 1) / ($z + 1);
	    $y = asin($siny) * 180 / pi();
	    return $y;
	}
	
	/**
	 * @name ��������ľ�γ������� 
	 * @return ��������֮��ľ��룬��λΪ��
	 * @author �ܷ�
	 * @date 2014-04-01 
	 */
	function getDistance($beginLng,$beginLat,$endLng,$endLat){
		$dis = sqrt(pow(($endLng-$beginLng)*M_PI/180*6371229*cos(($beginLat+$endLat)/2*M_PI/180),2)+pow(($endLat-$beginLat)*M_PI/180*6371229,2));//ƽ���뾶
		return $dis;
	}
	
	