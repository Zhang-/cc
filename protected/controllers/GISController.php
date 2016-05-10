<?php
class GISController extends Controller{
   
 /** 
  * 投诉信息及追溯历史业务信息地图展示
  *
  * @param int $_GET['complainid'] //投诉信息id
  * @return json GISConfig::getMQSMapConfig(), //地图初始化数据
  * @return json $complainInfo //投诉信息及追溯业务数据
  * @return json $sitesGeom //基站位置点数组
  * 
  * @author zhanghy
  * @date 2014-01-23 08:06:56
  *
  **/
	public function actionMQSMap()
	{
		$searchHistoryKeys = 'lac,cellid,weakrssiratethirty,offnetnumberthirty,downloadratethirty,packetlossratethirty,delaytimethirty';
		$complainInfo = array();
		$initConfig = array();

		if(isset($_GET['complainid']) && !!$_GET['complainid'])
		{
			$complainid = $_GET['complainid']; //诊断id
			$complainInfo = ComplainServiceLog::model()->find(array(
			    'select'=>'service',
			    'condition'=>"complainid={$complainid}",
			)); //查询符合该诊断id的service信息
			//$complainInfo = $complainInfo->attributes['service']; //取出json格式的service信息
			//$complainInfo = json_decode($complainInfo); //获取数组，数组的值为数个stdClass对象
			//var_dump($complainInfo);exit();
			$sitesGeom = array(); //基站位置点数组
			$initConfig = GISConfig::getMQSMapConfig(); //地图初始化数据
			//如果存在投诉信息
			if($complainInfo)
			{
				require_once(Yii::app()->basePath.'/extensions/gisFunctions.php'); //引入纠偏文件
				$complainInfo = $complainInfo->attributes['service']; //取出json格式的service信息
				$complainInfo = json_decode($complainInfo); //获取数组，数组的值为数个stdClass对象
				$sites = array(); //查询所有涉及基站的位置点信息时需要的
				$searchSites = array(); //查询所有涉及基站的基本信息时需要的
				$searchHistory = array(); //查询所有涉及基站的历史信息时需要的
				if (!isset($complainInfo->dynamicData)) //如果在查询的service_log表中还没有查询并保存过动态信息数据，则查询动态信息表并生成查询信息存储
				{ 
					if(!!($complainInfo->complaindata->beginT) && !!($complainInfo->complaindata->endT))
					{
						$beginTime = HelpTool::getDateTime($complainInfo->complaindata->beginT); //查询动态信息开始时间
						$endTime = HelpTool::getDateTime($complainInfo->complaindata->endT); //查询结束时间
						//$beginTime = '2014-05-01 12:00:00'; //查询动态信息开始时间
						//$endTime = '2014-05-02 12:00:00'; //查询结束时间
						$initConfig['startDateTime'] = $beginTime; //关联口径表查询开始时间
						$initConfig['stopDateTime'] = $endTime; //关联口径表查询结束时间
						$complainInfo->complaindata->startDateTime = $beginTime; //构造complaindata的startDateTime,为下面将complaindata放入dynamicData做准备
						if($complainInfo->complaindata->complain_type == 1) //业务类型
						{
							$serviceType = 'voiceID';
							$complainInfo->complaindata->complain_type = '语音业务';
						}else{
							$serviceType = 'dataID';
							$complainInfo->complaindata->complain_type = '数据业务';
						}
						$dynamicInfo = DynamicInformation::model()->findAll("startDateTime BETWEEN '$beginTime' AND '$endTime' AND staticID={$complainInfo->complaindata->static_information_id} AND $serviceType <>0 ORDER BY startDateTime ASC,$serviceType DESC"); //查询动态数据表，符合开始时间和结束时间的所有动态信息
						//print_R($dynamicInfo->totalItemCount);exit;
						$dynamicNum=count($dynamicInfo);
						$perNum=15;
						if($dynamicNum>$perNum){
							$display=true;
							$g=ceil($dynamicNum/$perNum);
						}else{
							$display=false;
							$g=1;
						}
						$tempDynamicInfo = $keys = array();

						foreach ($dynamicInfo as $value) {
							$tempDynamicInfo[$value->attributes[$serviceType]][$value->attributes['startDateTime']] = $value;
							$keys[] = $value->attributes[$serviceType];
						}

						if (!!$keys) 
						{
							$littleKey = min($keys);
							$dynamicInfo = $tempDynamicInfo[$littleKey];
						}else
							$dynamicInfo = array();

						$dynamicInfo['complaindata'] = (object)array('attributes'=>$complainInfo->complaindata);
						$c=0;
						$se = 0;
						foreach($dynamicInfo as $title => $val)
						{
							$se++;
							if($display&&$title!=='complaindata'){
								if($c!=0){
									$c++;
									if($c>=$g) $c=0;
									continue;
								}
								$c++;
								if($c>=$g) $c=0;
							}
							$val = (object)$val->attributes;
							$val->lng = lngrectify($val->lng); //纠偏lng
							$val->lat = latrectify($val->lat); //纠偏lat
							$val->markerName = 'user'; //所有业务初始的markerName都为user
							$val->popupType = 'service'; //所有业务初始的popupType都为service

							if($title === 'complaindata')
							{
								//var_dump($val);exit;
								$val->complain_time = HelpTool::getDateTime($val->complain_time);
								//$val->complain_type = ($val->complain_type == 1) ? '语音业务':'数据业务'; //数据类型
								$val->startDateTime = '诊断业务'; //投诉信息图层名名称
								$val->markerName = 'user_red'; //投诉信息markerName为user_red
								$val->popupType = 'complainService'; //投诉信息popupTyep为complainService
							}else{
								if($val->dataID !== 0)
								{
									$val->type = '数据业务';
									$val->rssi = ($val->netType == 8) ? ($val->RSRP.'(RSRP)') : ($val->rssi.'(RSSI)');
								}else{
									$val->type = '语音业务';
									$val->rssi .= '(RSSI)';
								}
							}

							$thisValLacCellid = $val->lac . ',' . $val->cellId; //生成'lac,cellid'字符串
							$val->site = $thisValLacCellid; //构造适合前台展示的基站格式
							$sites[$thisValLacCellid] = $thisValLacCellid; //将所有'lac,cellid'放入数组并去重
							$searchSites[$thisValLacCellid] = $val->lac . "+':'+" . $val->cellId; //查询基站表信息的基站查询条件
							$dynamicInfoArray[$title] = (object)$val; //将动态信息中所有的记录存入数组
						}


						//echo $dynamicNum.'  '.$se;exit;
						$complainInfo = (object)$dynamicInfoArray; //将所有的动态数据信息放入$complainInfo->querydata中,待存入数据库

						//print_r($complainInfo);exit;

						foreach ($sites as $historyKey=>$historyVal) 
							$searchHistory[] = '(lac = '. str_replace(',', ' AND cellid = ', $historyVal) . ')';
						$searchHistory = implode(' or ', $searchHistory); //搜索地理数据库关于基站历史数据

						$sites = implode("','", $sites); //基站位置点信息查询字符串
						$searchSites = implode(',', $searchSites); //基站基本信息查询字符串
						$sitesData = SitePoints::model()->findAll("laccellid in ('{$sites}')"); //查询所有涉及基站的位置点信息
						$searchSetesInfo = Site::model()->findAll("(lac+'-'+cellId) in ({$searchSites})"); //查询所有涉及基站的基本信息

						$gisInfo = GISConfig::get();
						$conn_string = $gisInfo->pgConnectStr;//pgsql链接
						$tableName = $gisInfo->layer->sitesLayer; //基站图层名称

						$dbconn4 = pg_connect($conn_string) or die ('connection failed');

						$siteHistoryData = @pg_query("SELECT {$searchHistoryKeys} FROM {$tableName} WHERE {$searchHistory};");
						//var_dump(pg_num_rows($siteHistoryData));exit;
						$historyData = array();
						if(!!pg_num_rows($siteHistoryData))
							while ($rowData = pg_fetch_object($siteHistoryData)) {
								$laccellid = $rowData->lac.','.$rowData->cellid;
								$historyData[$laccellid] = (array)$rowData;
							}
						pg_close ($dbconn4); //关闭pg数据路链接

						if(!!$sitesData) //如果查询有数据
							foreach($sitesData as $val) //将基站位置点信息加入基站位置点数组中
								$sitesGeom[$val['laccellid']] = array(
									'centerlng'=>$val['centerlng'],
									'centerlat'=>$val['centerlat'],
									'pointdata'=>$val['pointdata']
								);

						$sitesGeom = array_merge_recursive($historyData,$sitesGeom);

						if(!!$searchSetesInfo) //如果查询有数据
							foreach($searchSetesInfo as $siteVal){ //将基站基本信息加入基站位置点数组中
								$laccellid = $siteVal['lac'].','.$siteVal['cellId'];
								$sitesGeom[$laccellid]['lac'] = $siteVal['lac'];
								$sitesGeom[$laccellid]['cellid'] = $siteVal['cellId'];
								$sitesGeom[$laccellid]['cell_name'] = $siteVal['cell_name'];
								$sitesGeom[$laccellid]['angle'] = $siteVal['angle'];
							}
					}
				}
			}
		}
		//print_r((array)$complainInfo);exit;
		$this->renderPartial('MQSMap',
			array(
				'initConfig'=>json_encode($initConfig), //地图初始化数据
				'complainInfo'=>json_encode($complainInfo), //投诉信息及追溯业务数据
				'sitesGeom'=>json_encode($sitesGeom) //基站位置点数组
			)
		);
	}

	public function actionGISMap()
	{
		$this->renderPartial('GISMap');
	}

	/* 获取gis页面配置 */
	public function actionGetMapConfig(){
		$page = isset($_POST['page']) ? $_POST['page'] : false; //获取配置的页面
		$gisConfig = array();
		if($page){
			if($page == 'init'){
				$initConfig = GISConfig::initConfig(); //初始配置
				$gisConfig = GISConfig::getGISLayers($initConfig['page']);
				$gisConfig['initConfig'] = $initConfig;
			}else{
				$gisConfig = GISConfig::getGISLayers($page); 
			}
		}
		echo json_encode($gisConfig);
	}
	
	
	/* GIS页面点击事件 */
	public function actionGisClickPoints()
	{
		
		$gisConfig = GISConfig::get();
		
		date_default_timezone_set("UTC");  //UTC时间
		$today = date('Y-m-d'); //获取当前时间
		$yesterday = date('Y-m-d',strtotime("$today - 1 days"));
		$yesterday = HelpTool::getStrValue('single',$yesterday);

		//print_r($_POST);exit;
		
		$tuCurl = curl_init();
		//参数
		$srtarr=array();
		$queryKey = isset( $_POST['queryKey'] ) ? $_POST['queryKey'] : false; //当前图层名称
		unset($_POST['queryKey']);
		$otherKey = isset( $_POST['otherKey'] ) ? $_POST['otherKey'] : '';
		$page = isset( $_POST['page'] ) ? $_POST['page'] : ''; //当前页面名称
		unset($_POST['page']);
		$keyList = isset( $_POST['propertyName'] ) ? $_POST['propertyName'] : ''; //当前查询键列表
		$currentZoom = isset( $_POST['currentZoom'] ) ? $_POST['currentZoom'] : '';
		$queryTime = isset( $_POST['queryTime'] ) ? $_POST['queryTime'] : '';
		$url = ( $_POST['SERVICE'] == 'WMS' ) ? 'wms' : 'ows';
		
		$url = "http://".$gisConfig->server->host.":".$gisConfig->server->port."/geoserver/".$gisConfig->projectName."/{$url}?";

		foreach($_POST as $key=>$val)
			array_push($srtarr,$key.('='.urlencode($val)));
		curl_setopt($tuCurl, CURLOPT_URL,$url.implode('&',$srtarr));
		curl_setopt($tuCurl, CURLOPT_PORT , $gisConfig->server->port);
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($tuCurl, CURLOPT_CONNECTTIMEOUT_MS,3000);  
		$tuData = curl_exec($tuCurl); 
		$re=curl_multi_getcontent($tuCurl);//返回的数据
		curl_close($tuCurl);
		//echo $re;exit;
		$mat=array();
		$key_list = explode(',',$keyList);

		function getAreaInfo($layerName,$matInfos, $gisConfig, $queryKey){
			if( $layerName == ($gisConfig->projectName.':'.$gisConfig->layer->gridsLayer)){ //如果是栅格图层
				$allInfos = GridBussinessInfo::getGridsInfo(HelpTool::my_array_column($matInfos,'gridid')); //查询栅格详细信息
			}else{
				foreach ($matInfos as $key => $value) {
					$matInfos[$key]['gridid'] = $value['lac'].'-'.$value['cellid']; //构造唯一识别标识
					$allSites[] = $value['lac']."+':'+".$value['cellid']; //构造查询语句
				}
				$allInfos = Site::getSitesInfo($allSites); //查询基站详细信息
			}

			if(count($allInfos)){ //如果有返回
				foreach ($matInfos as $key => $value) {
					if(isset($allInfos[$value['gridid']])){
						$infoData = &$allInfos[$value['gridid']];
						// var_dump($infoData);exit;
						foreach ($infoData as $k => $v){
							if(!!$v && ($k == 'num_2g' ||$k == 'num_3g'||$k == 'num_4g'))
								$infoData[$k] = "<a class='popup_a {$k}' href='#' onclick='getAreaDataList(this)'>{$v}</a>"; //从site表中查出的数据都有点击事件
						}
						$infoData['norm'] = "<a class='popup_a norm_2g' href='#' onclick='getAreaDataList(this)'>2G</a>、<a class='popup_a norm_3g' href='#' onclick='getAreaDataList(this)'>3G</a>、<a class='popup_a norm_4g' href='#' onclick='getAreaDataList(this)'>4G</a>"; 
						$matInfos[$key] = $value + $infoData; //合并两数据库查询信息
						if($matInfos[$key][$queryKey]!=='无数据')
							$matInfos[$key]['com_problem'] = "<a class='popup_a com_problem' href='#' onclick='getAreaDataList(this)'>查询</a>";
					}
				}
				// print_r($matInfos);exit;
			}

			return $matInfos;

			//print_r($allInfos);
			//print_r($matInfos);exit;
			/*$mat[$k]['gsmNum'] = "<a href='#' class='gsm_click'>2</a>"; //2G基站个数
			$mat[$k]['tdNum'] = "<a href='#' class='td_click'>2</a>";
			$mat[$k]['lteNum'] = "<a href='#' class='lte_click'>2</a>";*/
			
		}

		if($_POST['SERVICE']=='WMS'){
			preg_match_all('/<td[^>]*>[\s\S]*?<\/td>/',$re,$matches);//在正则匹配的元素放到 
			$mat=array_chunk($matches[0],sizeof($key_list)+1);
			//print_r($mat);exit;
			foreach($mat as $k=>$info){
			//print_r($info);exit;
				array_splice($info,0,1);
				foreach($info as $infoKey=>$infoVal)
					$info[$infoKey] = str_replace(array('<td>','</td>'),'',$infoVal);
				$mat[$k]=array_combine($key_list,$info);
				//var_dump($mat[$k]);exit;
				if($queryKey){
					$mat[$k][$queryKey] = str_replace(array('-2.0','-2'),'无数据',$mat[$k][$queryKey]);
					if( $mat[$k][$queryKey] == '' ){
						$mat[$k][$queryKey] = '无数据';
					}
				}
			}
			$layerName = $_POST['QUERY_LAYERS'];
		}else{
			$feature=json_decode($re, true);
			if(isset($feature['features']))
			{
				foreach($feature['features'] as $key=>$info){
					$mat[$key] = $info['properties'];
					foreach($mat[$key] as $matKey=>$matVal){
						$mat[$key][$queryKey] = str_replace('-2','无数据',$matVal);
					}
					if( $mat[$key][$queryKey] == '' ){
						$mat[$key][$queryKey] = '无数据';
					}
				}
			}
			$layerName = $_POST['TYPENAME'];
		}
		$searchLevel = $gisConfig->searchLevel;
		if( $page == 'pingPongSwitch' && $currentZoom >= $searchLevel ){
			foreach ( $mat as $key => $v ){
				if( $mat[$key][$queryKey] != '无数据' ){
					$search_lac = $v['lac'];
					$search_cellid = $v['cellid']; 
					$ppOtherKey = explode(',' , $otherKey);
					$queryPpInfo = LacGtExchangeNumberRecently::model()->findAll("yyyymmdd = {$yesterday} AND lac = {$search_lac} AND cellid = {$search_cellid}"); 
					foreach ( $queryPpInfo as $value ){
						$ppValue = $value->attributes;
						foreach( $ppOtherKey as $val ){
							$mat[$key][$val]= $ppValue[$val];
						}
					}
				}
			}
		}
		
		if( $page == 'siteBusiness' ){
			foreach ( $mat as $key => $v ){
				$search_gridId = $v['gridid'];
				$bussinessInfo = GridBussinessInfo::model()->findAll("time = '{$queryTime}' AND gridId = {$search_gridId}");
				
				foreach ( $bussinessInfo as $value ){
					$bValue = $value->attributes;
					foreach ( $bValue as $k =>$val ){
						$mat[$key][$k] = $val;
						if(!!$val && ($k == 'num_2g' ||$k == 'num_3g'||$k == 'num_4g'))
							$mat[$key][$k] = "<a class='popup_a {$k}' href='#' onclick='getAreaDataList(this)'>{$val}</a>"; //从site表中查出的数据都有点击事件
					}
				}
			}
		}else{
			if(count($mat))
			$mat = getAreaInfo($layerName,$mat, $gisConfig, $queryKey);
		}

		// print_r($mat);
		echo json_encode($mat);
	}

	public function actionDataList()
	{
		$isGIS = (isset($_GET['gis']) && !!$_GET['gis']) ? true : false;
		$queryid = isset($_GET['queryid']) ? $_GET['queryid'] : null;
		$param = isset($_GET['param']) ? $_GET['param'] : 'site';
		//$querytime = isset($_GET['querytime']) ? $_GET['querytime'] : null;
		//var_dump($isGIS);exit;
		$md = isset($_GET['md']) ? intval($_GET['md']) : -1;//-1:全部，0:2G，1:3G，4:4G
		$ulist=array();
		$page = 'site';
		$model = 'alert';
		if( in_array($param, array('num_2g', 'num_3g', 'num_4g')) || $param == 'site' ) //如果为基站详情
		{
			$model=new Site();
			$ulist['gridId'] = &$queryid;
			if(in_array($md,array(0,1,4)))
			$ulist['type']=$md;
		}
		else
		{
			$thisGridSites = Site::getGridSites($queryid, 'lac,cellId');
			if(count($thisGridSites))
			{
				foreach ($thisGridSites as $siteKey => $siteValue)
					$querySites[] = $siteValue['lac'].$siteValue['cellId'];
				$thisGridSites = implode(',', $querySites);
			}else{
				$thisGridSites = '';
			}
				
			$ulist['lac'] = $thisGridSites;
			if( in_array($param, array('speechTraffic_2g', 'dataTraffic_2g', 'wirelessRate_2g','norm_2g')) ) //如果为2G数据详情
			{
				$model=new SiteBussinessNorm2g();
				$page = '2g';
			}
			if( in_array($param, array('speechTraffic_3g', 'dataTraffic_3g', 'wirelessRate_3g','norm_3g')) ) //如果为3G数据详情
			{
				$model=new SiteBussinessNorm3g();
				$page = '3g';
			}
			if( in_array($param, array('dataTraffic_4g', 'wirelessRate_4g','norm_4g')) ) //如果为4G数据详情
			{
				$model=new SiteBussinessNorm4g();
				$page = '4g';
			}
		}
		
		if($model == 'alert'){
			echo "<script>alert('\u6682\u65e0\u8be6\u7ec6\u4fe1\u606f\u53ef\u4f9b\u67e5\u770b\uff01')</script>";
			return;
		}else
			$model->attributes=$ulist;

		$renderType = $isGIS ? 'renderPartial' : 'render';
		$this->$renderType('dataList',array(
			'model'=>$model,
			'md'=>$md,
			'isGIS'=>$isGIS,
			'page'=>$page,
			'queryid'=>$queryid
		));
		
	}

	
	public function actionGetPpShowInfo(){
		date_default_timezone_set("UTC");  //UTC时间
		$endDate = date('Y-m-d'); //获取当前时间
		$yesterday = date('Y-m-d',strtotime("$endDate - 1 days"));// 昨天
		$yesterday = HelpTool::getStrValue('single',$yesterday);
		
		$lac = isset($_POST['lac']) ? $_POST['lac'] : '';
		$cellid = isset($_POST['cellid']) ? $_POST['cellid'] : '';
		
		//查找该小区作为发起小区的乒乓切换详细信息
		$launchReceive = array();
		$launchInfo = LacPpLaunchReceiveRecently::model()->findAll("yyyymmdd = {$yesterday} AND launchLac = {$lac} AND launchCellid = {$cellid}");
		// $count = 0;
		foreach( $launchInfo as $launchValue ){
			$launchValue = $launchValue->attributes;
			if( !empty($launchValue) ){
				$_ppMoreData = array();	
				// if( $count == 0 ){
					// $_ppMoreData['lac'] = $lac;
					// $_ppMoreData['cellid'] = $cellid;
					// array_push( $launchReceive, $_ppMoreData );
					// $count = 1;
				// }
				$_ppMoreData['lac'] = $launchValue['receiveLac'];
				$_ppMoreData['cellid'] = $launchValue['receiveCellid'];
				array_push( $launchReceive, $_ppMoreData );
			}
		}
		
		foreach( $launchReceive as $k=>$val){
			$searchStr = "'".$val['lac'].",".$val['cellid']."'";
			$pointsData = SitePoints::model()->findAll("laccellid = {$searchStr}") ;
			$pointsDataValue = $pointsData[0]->attributes;
			$launchReceive[$k]['centerLon'] = $pointsDataValue['centerlng'];
			$launchReceive[$k]['centerLat'] = $pointsDataValue['centerlat'];
			$launchReceive[$k]['pointData'] = $pointsDataValue['pointdata'];
			$siteData = Site::model()->findAll("lac={$val['lac']} AND cellId={$val['cellid']}") ;
			$siteDataValue = $siteData[0]->attributes;
			$launchReceive[$k]['angle'] = $siteDataValue['angle'];
		}
		
		//查找该小区作为接受小区的乒乓切换详细信息
		$receiveLaunch = array();
		$receiveInfo = LacPpLaunchReceiveRecently::model()->findAll("yyyymmdd = {$yesterday} AND receiveLac = {$lac} AND receiveCellid = {$cellid}");
		// $count = 0;
		foreach( $receiveInfo as  $receiveValue ){
			$receiveValue = $receiveValue->attributes;
			if( !empty($receiveValue) ){
				$_ppMoreData = array();
				// if( $count == 0 ){
					// $_ppMoreData['lac'] = $lac;
					// $_ppMoreData['cellid'] = $cellid;
					// array_push( $receiveLaunch, $_ppMoreData );
					// $count = 1;
				// }
				$_ppMoreData['lac'] = $receiveValue['launchLac'];
				$_ppMoreData['cellid'] = $receiveValue['launchCellid'];
				array_push( $receiveLaunch, $_ppMoreData );
			}
		}
		foreach( $receiveLaunch as $k=>$val){
			$searchStr = "'".$val['lac'].",".$val['cellid']."'";
			$pointsData = SitePoints::model()->findAll("laccellid = {$searchStr}") ;
			$pointsDataValue = $pointsData[0]->attributes;
			$receiveLaunch[$k]['centerLon'] = $pointsDataValue['centerlng'];
			$receiveLaunch[$k]['centerLat'] = $pointsDataValue['centerlat'];
			$receiveLaunch[$k]['pointData'] = $pointsDataValue['pointdata'];
			$siteData = Site::model()->findAll("lac={$val['lac']} AND cellId={$val['cellid']}") ;
			$siteDataValue = $siteData[0]->attributes;
			$receiveLaunch[$k]['angle'] = $siteDataValue['angle'];
		}
		$mat = array();
		if( !empty($launchReceive) ){
			$mat['launchReceive'] = $launchReceive;
		}else{
			$mat['launchReceive'] = '无数据';
		}
		if( !empty($receiveLaunch) ){
			$mat['receiveLaunch'] = $receiveLaunch;
		}else{
			$mat['receiveLaunch'] = '无数据';
		}
		echo json_encode($mat);
	}
	
	
	/* 查看更多 */
	public function actionGetMoreInfo(){
		require_once(Yii::app()->basePath.'/extensions/gisFunctions.php');
		$page = isset($_GET['page']) ? $_GET['page'] : '';
		$queryId = isset($_GET['queryid']) ? $_GET['queryid'] : '';
		$areaName = isset($_GET['areaname']) ? $_GET['areaname'] : '';
		$layerType = isset($_GET['layertype']) ? $_GET['layertype'] : '';
		$moreInfoKey = isset($_GET['moreinfokey'])? $_GET['moreinfokey'] :'';
		if( $page != 'siteBusiness' ){
			$ppMoreInfoKey = explode(',' , $moreInfoKey);
			$ppKey = array('launchLac','launchCellid','receiveLac','receiveCellid','ppNumber','ppLaunchRssi','ppReceiveRssi');
			//if(!empty($page))
				$moreInfoConfig = GISConfig::getGISLayers($page, 'moreInfo'); //引入该页面的配置
			date_default_timezone_set("UTC");  //UTC时间
			$endDate = date('Y-m-d'); //获取当前时间
			$startDate = date('Y-m-d',strtotime($endDate."- 30 days")); //一月前时间
			$moreInfoConfig['startDate'] = $startDate;
			$moreInfoConfig['endDate'] = date('Y-m-d',strtotime("$endDate - 1 days"));// 昨天
			$moreInfoConfig['queryId'] = $queryId;
			$moreInfoConfig['areaName'] = $areaName;
			// $moreInfoConfig['stockName'] = explode(',',$stockConfig['stockName']);;
			$endDate = HelpTool::getStrValue('single',$endDate);
			$startDate = HelpTool::getStrValue('single',$startDate);
			$yesterday = HelpTool::getStrValue('single',$moreInfoConfig['endDate']);//昨天		
			
			//构造ID查询条件语句,栅格为gridId；基站为lac,cellid；
			$searchSql = '';
			$searchKey = explode(',',$moreInfoConfig['searchId'][$layerType]);
			$searchVal = explode(',',$queryId);
			foreach($searchKey as $k=>$v)
				$searchSql .= $v.' = '.$searchVal[$k].' AND ';
		}
		
		$moreInfoTableTitle = "";
		//若是乒乓切换基站显示模式，则展示乒乓切换发起小区、接受小区等详细情况
		$moreData = array();
		if(	$page == 'pingPongSwitch' && $layerType == 'sites' ){
			$id=0;
			//查找该小区作为发起小区的乒乓切换详细信息
			$launchInfo = LacPpLaunchReceiveRecently::model()->findAll("yyyymmdd = {$yesterday} AND launchLac = {$searchVal[0]} AND launchCellid = {$searchVal[1]}");
			foreach( $launchInfo as $launchValue ){
				$launchValue = $launchValue->attributes;
				$_ppMoreData = array();
				$_ppMoreData['id'] =  ++$id;
				foreach( $ppMoreInfoKey as $k => $val ){
					$_ppMoreData[$ppKey[$k]] = $launchValue[$val];
				}
				$_ppMoreData['launchHtmlValue'] = '';
				$_ppMoreData['receiveHtmlValue'] = 'color:black;font-weight:bold;';
				array_push( $moreData, $_ppMoreData );
			}
			
			//查找该小区作为接受小区的乒乓切换详细信息
			$receiveInfo = LacPpLaunchReceiveRecently::model()->findAll("yyyymmdd = {$yesterday} AND receiveLac = {$searchVal[0]} AND receiveCellid = {$searchVal[1]}");
			foreach( $receiveInfo as $key => $receiveValue ){
				$receiveValue = $receiveValue->attributes;
				$_ppMoreData = array();
				$_ppMoreData['id'] =  ++$id;
				foreach( $ppMoreInfoKey as $k => $val ){
					$_ppMoreData[$ppKey[$k]] = $receiveValue[$val];
				}
				$_ppMoreData['launchHtmlValue'] = 'color:red;font-weight:bold;';
				$_ppMoreData['receiveHtmlValue'] = '';
				array_push( $moreData, $_ppMoreData );
			}
		}
		
		//网络切换统计模块，显示2G->3G、3G->2G、2G->4G、4G->2G、3G->4G、4G->3G详情
		if(	$page == 'allReselect' ){
			$reselect_sql = '';
			if($layerType == 'sites'){
				$reselect_sql = "select " .$moreInfoKey. " from lac_gt_exchange_number_recently where ". $searchSql ." yyyymmdd = {$yesterday}";
			}
			if($layerType == 'grids'){
				$reselect_sql = "select " .$moreInfoKey. " from grid_gt_exchange_number_recently where ". $searchSql ." yyyymmdd = {$yesterday}";
			} 
			$reselect = Yii::app()->db->createCommand($reselect_sql)->queryAll();
			// var_dump($reselect);exit;
			if( !empty($reselect) ){
				$reselectValue = $reselect[0];
				$reselectKey = array( 'g2tReselectNumber','t2gReselectNumber','g2fourReselectNumber','four2gReselectNumber','t2fourReselectNumber','four2tReselectNumber' );
				
				$_reselectMoreData = array_combine( $reselectKey, $reselectValue );
				$_reselectMoreData['id'] = 1;
				array_push( $moreData , $_reselectMoreData );
				// var_dump($reselectMoreData);exit;
			}
			$arr_moreinfoKey = explode(",",$moreInfoKey);
			// var_dump($arr_moreinfoKey);
			if( $arr_moreinfoKey[0] === 'g2tReselectNumber1' ){
				$moreInfoTableTitle = "昨日小区重选次数详情";
			}elseif( $arr_moreinfoKey[0] === 'g2tReselectNumber7' ){
				$moreInfoTableTitle = "最近七天小区重选平均次数详情";
			}elseif( $arr_moreinfoKey[0] === 'g2tReselectNumber15' ){
				$moreInfoTableTitle = "最近十五天小区重选平均次数详情";
			}elseif( $arr_moreinfoKey[0] === 'g2tReselectNumber30' ){
				$moreInfoTableTitle = "最近三十天小区重选平均次数详情";
			}
		}
		//小区业务统计模块，显示栅格区域内的所有基站业务统计详情
		if(	$page == 'siteBusiness' ){
			$_date = explode("-",$moreInfoKey);
			$moreInfoTableTitle = $_date[0]."年".$_date[1]."月区域内基站业务统计日均数据";
			
			$siteBusinessKey = array( 'lac','cellId','speechTraffic','dataTraffic','wirelessRate');
			
			$_siteResult = Yii::app()->db->createCommand("select lac,cellId from site where gridId = {$queryId}")->queryAll();
			$siteResult = array();
			foreach( $_siteResult as $v ){
				if( !isset($siteResult[$v['lac']."_".$v['cellId']]) ){
					$siteResult[$v['lac']."_".$v['cellId']] = $v;
				}
			}
			// var_dump($siteResult);
			$siteBusinessId = 0;
			foreach( $siteResult as $val ){
				$_siteBusinessMoreData = array();
				$siteInfo = Yii::app()->db->createCommand(" select * from site_bussiness_info where lac = {$val['lac']} and cellId = {$val['cellId']} and time = '{$moreInfoKey}' ")->queryAll();
				if( !empty($siteInfo) ){
					$_siteBusinessMoreData['id'] = ++$siteBusinessId;
					foreach( $siteBusinessKey as $k=>$v ){
						$_siteBusinessMoreData[$v] = $siteInfo[0][$v];
					}
					array_push( $moreData , $_siteBusinessMoreData);
				}
			}
		}
		
		//求展示曲线趋势图的数据
		$stockConfig = array(); //存储stock曲线的数据配置 
		$allData = array();
		if( isset($moreInfoConfig['stockChart']) ){
			$stockConfig = $moreInfoConfig['stockChart'];
			$queryData = $stockConfig['modelName'][$layerType]::model()->findAll("$searchSql {$moreInfoConfig['date']} < {$endDate} AND {$moreInfoConfig['date']} >= {$startDate} ORDER BY {$moreInfoConfig['date']} ASC"); 
			
			if( $page == 'allReselect' || $page == 'pingPongSwitch' || $page == 'T2GSwitch' ){
				$queryData = $stockConfig['modelName'][$layerType]::model()->findAll("$searchSql {$moreInfoConfig['date']} < {$endDate} AND {$moreInfoConfig['date']} >= {$startDate} AND {$stockConfig['queryKey']} <> 0 ORDER BY {$moreInfoConfig['date']} ASC"); 
			}
			
			foreach($queryData as $key=>$rowData){
				$rowData = $rowData->attributes;
				$allData['stockChart']['总'][] = array(strtotime($rowData[$moreInfoConfig['date']])*1000 , floatval($rowData[$stockConfig['queryKey']])); //因为前台的highstock时间戳为毫秒单位,所以*1000
				
				if($page == 'pingPongSwitch' && $layerType == 'sites'){
					$allData['stockChart']['发起'][] = array(strtotime($rowData[$moreInfoConfig['date']])*1000 , floatval($rowData['ppLaunchNumber']));
					$allData['stockChart']['接收'][] = array(strtotime($rowData[$moreInfoConfig['date']])*1000 , floatval($rowData['ppReceiveNumber']));
				}
				
				if( $page == 'allReselect' ){
					$allData['stockChart']['2G->3G'][] = array(strtotime($rowData[$moreInfoConfig['date']])*1000 , floatval($rowData['g2tReselectNumber']));
					$allData['stockChart']['3G->2G'][] = array(strtotime($rowData[$moreInfoConfig['date']])*1000 , floatval($rowData['t2gReselectNumber']));
					$allData['stockChart']['2G->4G'][] = array(strtotime($rowData[$moreInfoConfig['date']])*1000 , floatval($rowData['g2fourReselectNumber']));
					$allData['stockChart']['4G->2G'][] = array(strtotime($rowData[$moreInfoConfig['date']])*1000 , floatval($rowData['four2gReselectNumber']));
					$allData['stockChart']['3G->4G'][] = array(strtotime($rowData[$moreInfoConfig['date']])*1000 , floatval($rowData['t2fourReselectNumber']));
					$allData['stockChart']['4G->3G'][] = array(strtotime($rowData[$moreInfoConfig['date']])*1000 , floatval($rowData['four2tReselectNumber']));
				}
			}
		}else
			$moreInfoConfig['stockChart']=false;
		
		//求展示饼状图的数据
		$pieConfig = array(); //存储饼状图的数据配置 
		if( isset($moreInfoConfig['pieChart']) ){
			$pieConfig = $moreInfoConfig['pieChart'];
			$queryData = $pieConfig['modelName'][$layerType]::model()->findAll("$searchSql {$moreInfoConfig['date']} = {$yesterday} ORDER BY {$pieConfig['queryKey']} DESC");
			foreach($queryData as $key=>$rowData){
				$rowData = $rowData->attributes;
				if($page == 'complain'){
					//查询投诉问题名称
					$sql_cause = Yii::app()->db->createCommand("select cause from complain_type where number = {$rowData['complainMatter']} ")->queryAll();
					$cause = $sql_cause[0]['cause'];
					//求得生成饼状图的数据
					$allData['pieChart'][] = array( $cause, $rowData[$pieConfig['queryKey']]*100 );
					
					//如果某类投诉问题三十天所占平均比例大于等于20%，则也在折线图里进行显示
					if( $rowData[$pieConfig['queryKey']] >= 0.2 ){
						$queryPartData = GridComplainMatterNumberDay::model()->findAll("$searchSql {$moreInfoConfig['date']} < {$endDate} AND {$moreInfoConfig['date']} >= {$startDate} AND complainMatter = {$rowData['complainMatter']} ORDER BY {$moreInfoConfig['date']} ASC"); 
						foreach ( $queryPartData as $k=>$partValue ){
							$partValue = $partValue->attributes;
							$allData['stockChart'][$cause][] =array( strtotime($partValue[$moreInfoConfig['date']])*1000 ,floatval($partValue['complainMatterNumber']));
						}
					}
				}
			}
		}else
			$moreInfoConfig['pieChart']=false;
			// var_dump($allData);
		$this->renderPartial(
			'moreInfo',
			array(
				/* 
				'gridid'=>
				'tagname'=>
				'search'=> */
				'layerType'=>$layerType,
				'page'=>$page,
				'moreInfoConfig'=>$moreInfoConfig,
				'allData'=>$allData,
				'moreData'=>$moreData,
				'moreInfoTableTitle'=>$moreInfoTableTitle,
			)
		);
	}
	
	
	/* 栅格数据表格导出 */
	public function actionGridsGISOutput(){
		set_time_limit(0);
		ini_set('memory_limit', '2048M');
		if(isset($_POST['layerName']) && isset($_POST['pageName'])){
			if( $_POST['pageName'] != 'siteBusiness' ){
				$tableName = $_POST['layerName'];
				$thisPage = $_POST['pageName'];
				$pageTag = $_POST['pageTag'];
				$pageUnit = $_POST['pageUnit'];
				
				HelpTool::getActionInfo(0,3);
				$conn_string = GISConfig::get()->pgConnectStr;//pgsql链接
				$dbconn4 = pg_connect($conn_string) or die ('connection failed');

				$keyArray = GISConfig::getPGTableConfig($tableName); //获取图层基本配置键
				$tableName = substr($tableName,strpos($tableName,':')+1); //获取数据库表名
				//echo $tableName;exit;
				//print_r($keyArray);exit;
				$queryKeysConfig = GISConfig::getGISLayers($thisPage,'queryConfig'); //获取查询键
				$timeQueryKeyArray = array();
				if(!!$queryKeysConfig)
					foreach($queryKeysConfig as $queryKeyConfig)
						$timeQueryKeyArray[$queryKeyConfig['queryKey']] = $pageTag.$queryKeyConfig['tag'].$pageUnit;
				$keyArray = array_merge($keyArray,$timeQueryKeyArray);
				$cnKeyArray = array_values($keyArray);
				$enKeyArray = array_keys($keyArray);
				$queryKeys = implode(',',$enKeyArray);
				$pgResult = @pg_query("SELECT $queryKeys FROM $tableName;");
				$allData = array();
				
				if(!!pg_num_rows($pgResult))
					$allData = pg_fetch_all($pgResult);
				if(!!$allData){
					foreach($allData as $dataKey=>$dataVal)
						foreach($timeQueryKeyArray as $queryKey=>$queryVal)
							$allData[$dataKey][$queryKey] = str_replace('-2','无数据',$dataVal[$queryKey]);
				}
				pg_close ($dbconn4); //关闭pg数据路链接
				$filename = "{$pageTag}_{$tableName}_data_".date('Y-m-d');
			}else{
				HelpTool::getActionInfo(0,3);
				$time = $_POST['queryTime'];
				$cnKeyArray = array('栅格编号','2G小区数','3G小区数','4G小区数','时间','日均2G话务量','日均3G话务量','日均3G/2G话务量','日均2G数据流量','日均3G数据流量','日均4G数据流量','日均3G/2G数据流量','日均4G/3G数据流量','日均4G/2G数据流量','日均2G无线利用率','日均3G无线利用率','日均4G无线利用率');
				$enKeyArray = array('gridId','num_2g','num_3g','num_4g','time','speechTraffic_2g','speechTraffic_3g','speechTraffic_3_2g','dataTraffic_2g','dataTraffic_3g','dataTraffic_4g','dataTraffic_3_2g','dataTraffic_4_3g','dataTraffic_4_2g','wirelessRate_2g','wirelessRate_3g','wirelessRate_4g');
				$queryKeys = implode(',',$enKeyArray);
				$allData = Yii::app()->db->createCommand("select {$queryKeys} from grid_bussiness_info where time = '{$time}' ")->queryAll();
				$filename = "业务统计信息_grids_data_".date('Y-m-d');
			}
			
			require_once(Yii::app()->basePath.'/output/e.php');//导出表格主程序
			$excel = new ChangeArrayToExcel(Yii::app()->basePath.'/../cache/'.$filename.'.xls');	
			$excel->getExcel($allData,$cnKeyArray,$enKeyArray,'other');	//导出表格
			$url = 'index.php?r=site/download&fn='.json_encode($filename.'.xls');
			echo "<iframe src='$url' style='display:none'></iframe>";
		}
	}
	
	/** 
	  * VIP用户轨迹查询
	  * 
	  * @author caof
	  * @date 2014-05-20
	  *
	  **/
	public function actionsearchVipUser(){
		require_once(Yii::app()->basePath.'/extensions/gisFunctions.php');
		$error = "";
		$today = date('Y-m-d'); //获取当前时间
		$now = date('Y-m-d H:i:s');//获取当前时间，精准到秒
		$yesterday = date('Y-m-d',strtotime("$today - 1 days"));// 昨天
		$three = date('Y-m-d',strtotime("$today - 3 days"));// 最近三天
		$seven = date('Y-m-d',strtotime("$today - 7 days"));// 最近七天
		// var_dump($today);exit;
		if(!empty($_POST['imsi'])){
			$imsi = trim($_POST['imsi']);
			if( !is_numeric($imsi)){
				$error = $error . '"用户IMSI"不能为非数字型;   ';
			}
		}else{
			$error = $error . '"用户IMSI"不能为空;   ';
		}
		if(!empty($_POST['imei'])){
			$imei = trim($_POST['imei']);
			if(!is_numeric($imei)){
				$error = $error . '"用户IMEI"不能为非数字型;   ';
			}
		}else{
			$error = $error . '"用户IMEI"不能为空;   ';
		}
		$type = 0;//表示获取定时数据还是实时动态计算数据，0表示实时，1表示定时
		if(isset($_POST['time'])){
			if( $_POST['time'] == 1 ){
				$timeSql = " startDateTime >= '{$today}' ";
			}elseif( $_POST['time'] == 2 ){
				$type = 1;
				$timeSql = " startDateTime >= '{$yesterday}' and startDateTime < '{$today}'";
			}elseif( $_POST['time'] == 3 ){
				$type = 1;
				$timeSql = " startDateTime >= '{$three}' and startDateTime < '{$today}'";
			}elseif( $_POST['time'] == 4 ){
				$type = 1;
				$timeSql = " startDateTime >= '{$seven}' and startDateTime < '{$today}'";
			}
		}
		$data = array();	
		if( $error == "" ){
			$userinfo_sql = "select * from vipuser_info where imsi = {$imsi} and imei = {$imei}";
			$user_info = Yii::app()->db->createCommand($userinfo_sql)->queryAll();
			$_staticId = -2; //初始化
			//更新vipuser_info表
			if( empty( $user_info ) ){
				$staticID_sql = "select id from static_information where imsi = {$imsi} and imei ={$imei}";
				$staticID = Yii::app()->db->createCommand($staticID_sql)->queryAll();
				if( empty($staticID) ){
					$data['error']='Sorry, 未找到该用户的轨迹信息！';
					$data= json_encode($data);
					echo $data;
					exit;
				}else{
					$_staticId = $staticID[0]['id'];
					$insert_userinfo_sql = "insert into vipuser_info( id, imsi ,imei,updatetime ) values ( {$_staticId},{$imsi},{$imei},'{$now}' )";
					Yii::app()->db->createCommand($insert_userinfo_sql)->execute();
				}
			}else{
				$_staticId = $user_info[0]['id'];
				$update_userinfo_sql = "update vipuser_info set updatetime = '{$now}' where id = {$_staticId}";
				Yii::app()->db->createCommand($update_userinfo_sql)->execute();
			}
						
			if($type==1){
				$userpath_sql = " select startDateTime,cellId,lac,lng,lat,address,cell_name from vipuser_pathinfo where staticID = {$_staticId} and ".$timeSql ;
				$userpath = Yii::app()->db->createCommand($userpath_sql)->queryAll();
				if(empty($userpath)){
					$type = 0;
				}else{
					$data = $userpath;
				}
			}
			if($type==0){
				
				$dynamic_sql = "select startDateTime,cellId,lac,lng,lat from dynamic_information where staticID = {$_staticId} and ".$timeSql." order by startDateTime ";
				$dynamic_info = Yii::app()->db->createCommand($dynamic_sql)->queryAll();
				// var_dump($data);
				if( empty( $dynamic_info ) ){
					$data['error']='Sorry, 未找到该用户的轨迹信息！';
				}else{
					//对查询出来的动态信息进行处理，提取用户轨迹数据
					$current = array();
					
					foreach( $dynamic_info as $k=>$val ){
						if( $val['lng'] > 180 || $val['lat'] > 90 || $val['lng'] <= 0 && $val['lat'] <= 0 ) continue;
						if( empty( $data ) ){
							array_push( $data, $val );
							$current = $val;
							continue;
						}
						
						if( $val['lac'] != $current['lac'] || $val['cellId'] != $current['cellId'] ){
							array_push( $data, $val );
							$current = $val;
						}else{
							if( $val['lng'] != $current['lng'] || $val['lat'] != $current['lat'] ){
								$dis = getDistance($val['lng'],$val['lat'],$current['lng'],$current['lat']);
								if( $dis > 500){
									array_push( $data, $val );
									$current = $val;
								}
							}     
						}
					}
					
					//查询用户在每一个地方所用的小区名和地址
					foreach($data as $k=>$val){
						//从site表中查询小区名
						$sql1 = "select cell_name from site where lac = {$val['lac']} and cellId = {$val['cellId']}";
						$cell_name = Yii::app()->db->createCommand($sql1)->queryAll();
						
						if( !empty($cell_name) ){
							$data[$k]['cell_name'] = $cell_name[0]['cell_name'];
						}else{
							$data[$k]['cell_name'] = '未知';
						}
						
						//请求百度API得到当前点的位置
						$address = getPointAddress( $val['lng'], $val['lat'], 1, 0 );
						if($address==null){
							for($k=1;$k<=5;$k++){
								$address = getPointAddress( $val['lng'], $val['lat'], 1, 0 );
								if($address!=null){
									break;
								}
							}
						}
						if($address==null){
							for($k=1;$k<=5;$k++){
								$address = getPointAddress( $val['lng'], $val['lat'], 1, 1);
								if($address!=null){
									break;
								}
							}
						}
						if($address==null){
							$address = '未知';
						}
						$data[$k]['address'] = $address;
						$data[$k]['lng'] = lngrectify( $val['lng'] );
						$data[$k]['lat'] = latrectify( $val['lat'] );
					}
					if( empty($data) ){
						$data['error']='Sorry, 未找到该用户的轨迹信息！';
					}
				}
			}
		}else{
			$data['error']= $error;
		}
		
		$data= json_encode($data);
		echo $data;
	}
	
	/** 
	  * 投诉点查询
	  * 
	  * @author caof
	  * @date 2014-09-03
	  *
	  **/
	public function actionsearchComplainUser(){
		require_once(Yii::app()->basePath.'/extensions/gisFunctions.php');
		$startDateTime = trim($_POST['startDateTime']);
		$endDateTime = trim($_POST['endDateTime']);
		$startTime = strtotime("$startDateTime");
		$endTime = strtotime("$endDateTime + 1 days");
		
		$sql = "select * from complain_problem where complain_time >= {$startTime} and complain_time < {$endTime} ";
		$result = Yii::app()->db->createCommand($sql)->queryAll();
		
		$complainData = array();
		foreach( $result as $k => $val ){
			$tempVal = $val;
			$tempVal['complain_time'] = date('Y-m-d H:i:s',$tempVal['complain_time']);
			
			if( $val['longitude'] > 0 && $val['longitude'] < 180 && $val['latitude'] >0 && $val['latitude'] < 90){
				// $tempVal['longitude'] = lngrectify( $val['longitude'] );
				// $tempVal['latitude'] = latrectify( $val['latitude'] );
				$key = $tempVal['longitude'].','.$tempVal['latitude'];
			}else{
				$result1 = Yii::app()->db->createCommand("select * from site where lac ={$val['lac']} and cellId ={$val['cellId']} ")->queryAll();
				if( empty($result1) ){
					continue;
				}else{
					$tempVal['longitude'] = lngrectify( $result1[0]['lng'] );
					$tempVal['latitude'] = latrectify( $result1[0]['lat'] );
					$key = $tempVal['longitude'].','.$tempVal['latitude'];
					
				}
			}
			if( !isset( $complainData[$key] ) ){
				$complainData[$key] = array();
			}
			
			
			if( $tempVal['status'] == 0 ){
				$tempVal['status'] = '未处理';
			}elseif( $tempVal['status'] == 1 ){
				$tempVal['status'] = '已处理';
			}elseif( $tempVal['status'] == 2 ){
				$tempVal['status'] = '无需处理';
			}elseif( $tempVal['status'] == 3 ){
				$tempVal['status'] = '延期处理';
			}
			
			if( $tempVal['telephone'] == '11111111111' ){
				$tempVal['telephone'] = '-';
			}
			
			if( $tempVal['lac'] == 0 || $tempVal['cellId'] == 0 ){
				$tempVal['lac'] = '-';
				$tempVal['cellId'] = '-';
			}
			
			if( empty($tempVal['toTelephone']) ){
				$tempVal['toTelephone'] = '-';
			}
			if( $tempVal['startTime'] ==0 ){
				$tempVal['startTime'] = '-';
			}else{
				$tempVal['startTime'] =  date('Y-m-d H:i:s',$tempVal['startTime']);
			}
			if( $tempVal['stopTime'] ==0 ){
				$tempVal['stopTime'] = '-';
			}else{
				$tempVal['stopTime'] = date('Y-m-d H:i:s',$tempVal['stopTime']);
			}
			
			if( $tempVal['serviceType'] ==1 ){
				$tempVal['serviceType'] = '语音呼入';
			}elseif( $tempVal['serviceType'] ==2 ){
				$tempVal['serviceType'] = '语音呼出';
			}elseif( $tempVal['serviceType'] ==3 ){
				$tempVal['serviceType'] = '语音未接通';
			}elseif( $tempVal['serviceType'] ==4 ){
				$tempVal['serviceType'] = '数据业务';
			}
			array_push( $complainData[$key] , $tempVal );
		}
		$complainData= json_encode($complainData);
		echo $complainData;
	}
	
}

?>
