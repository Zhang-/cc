<?php
/**
 * GIS配置参数
 */
class GISConfig {

/** 
  * @name 获取GIS全局配置
  * @param 请遵从以下格式调用：
  * 	$gisConfig = GISConfig::get(); //获取
  * 	$gisConfig->server->host; //服务器地址
  * 	$gisConfig->server->port; //服务器端口
  * 	$gisConfig->layer->gridsLayer; //栅格图层名称
  * 	$gisConfig->layer->sitesLayer; //基站图层名称
  * 	$gisConfig->maxExten; //地图最大移动范围
  * 	$gisConfig->searchLevel; //框选显示最小等级
  * 	$gisConfig->projectName; //项目使用服务器空间名称
  * 	$gisConfig->initExten; //地图初始化区域
  *     $gisConfig->city; //城市名称
  * 	$gisConfig->lngrectifyVal; //WGS84经度纠偏
  * 	$gisConfig->latrectifyVal; //WGS84纬度纠偏
  * 	$gisConfig->pgConnectStr; //pgsql连接字段
  *  或直接使用 GISConfig::get()->searchLevel; 
  * 
  * @autor zhanghy
  * @date 2014-01-03 16:29:40
  * 
  **/
	static function get()
	{
		$config = require('./protected/config/gisConfig.php');
		$cityNum = Yii::app()->params->city;
		$cityConfig = $config->area[$cityNum];
		foreach($cityConfig as $key=>$val)
			$config->$key = $val;
		$config->pgConnectStr = "host={$config->server->host} port={$config->db->port} dbname={$config->projectName} user={$config->db->user} password={$config->db->password}";
		unset($config->area,$config->db);
		return $config;
	}

/** 
  * 返回MQSMap类所需的地图初始化配置
  *
  * @param 
  * @return Array $return 
  * 
  * @author zhanghy
  * @date 2014-01-11 14:43:40
  *
  **/
	static function getMQSMapConfig()
	{
		$gisConfig = self::get();
		return array(
			'layerName' => $gisConfig->projectName.':'.$gisConfig->layer->sitesLayer,
			'statement' => array(
				'layerName'=>$gisConfig->projectName.':'.$gisConfig->layer->statementLayer,
				'keyList'=>'serial_id,state_type,affect_radius,lac,cellid,starttime,endtime,state_title,affect_scope,affect_area,problem,project_status'
			),
			'serverUrl' => 'http://'.$gisConfig->server->host.':'.$gisConfig->server->port.'/geoserver/'.$gisConfig->projectName.'/wms?service=WMS&srs=EPSG:900913', //地图工作空间
			'initExtent' => $gisConfig->initExten, 
			'maxExtent' => $gisConfig->maxExten, 
			'initLevel' => $gisConfig->maxLevel, 
			'maxResolution'=>$gisConfig->maxResolution,
			'zoomOffset'=>$gisConfig->zoomOffset,
			'maxLevel'=>$gisConfig->maxLevel,
			'addLevel'=>8
		);
	}


/** 
  * 返回地理信息数据库相应表信息
  *
  * @param String $layerName  需要返回的表名称 grids: 栅格 sites: 基站
  * @return Array $return 
  * 
  * @author zhanghy
  * @date 2013-12-16 08:21:32
  *
  **/

	static function getPGTableConfig($layerName)
	{
		$gisConfig = self::get();
		$siteLayer = $gisConfig->projectName.':'.$gisConfig->layer->sitesLayer;
		$gridLayer = $gisConfig->projectName.':'.$gisConfig->layer->gridsLayer;
		$gridsTable = array(
			$gridLayer=>array(
				'gridid'=>'区域编号',
				'address'=>'区域地址',
				'centerlon'=>'中心经度',
				'centerlat'=>'中心纬度',
				'information'=>'附加信息'
			),
			$siteLayer=>array(
				'name'=>'小区名称',
				'cellid'=>'CELLID',
				'lac'=>'LAC',
				'lon'=>'经度',
				'lat'=>'纬度',
				'angle'=>'角度'
			)
		);
		if(!!$layerName)
			return $gridsTable[$layerName];
		else
			return $gridsTable;
	}


/** 
  * 返回GIS初始页面配置
  **/
  
	static function initConfig()
	{
		$gisConfig = self::get();
		return array(
			'page'=>'comNum', //初始页面
			'queryTime'=>'seven', //初始查询时间
			'sites'=>array(
				'name'=>$gisConfig->projectName.':'.$gisConfig->layer->sitesLayer,
				'keyList'=>'name,cellid,lac,lon,lat,centerlon,centerlat,angle,'
			), //基站图层
			'grids'=>array(
				'name'=>$gisConfig->projectName.':'.$gisConfig->layer->gridsLayer,
				'keyList'=>'gridid,address,centerlon,centerlat,'
			),//栅格图层
			'serverUrl'=>'http://'.$gisConfig->server->host.':'.$gisConfig->server->port.'/geoserver/'.$gisConfig->projectName.'/wms?service=WMS&srs=EPSG:900913', //地图工作空间
			'searchLevel'=>$gisConfig->searchLevel,
			'initExtent'=>$gisConfig->initExten,
			'maxExtent'=>$gisConfig->maxExten,
			'maxResolution'=>$gisConfig->maxResolution,
			'zoomOffset'=>$gisConfig->zoomOffset,
			'maxLevel'=>$gisConfig->maxLevel
		);
	}
	
	
  
/** 
  * 返回GIS页面配置
  * 
  * @param String $page  需要返回页面的名称，如果为空，则返回所有页面配置
  * @param String $tag  需要返回页面的配置模块，如果为空，则返回该页面所有配置
  * @return Array $return 
  * 
  * @author zhanghy
  * @date 2013-12-09 08:37:22
  *
  **/
  
	static function getGISLayers($page = '', $tag = ''){
		$gisConfig = self::get();
		$allConfig = array(
		
			// 用户数统计页面配置
			'userNumber'=>array(
				//点击信息配置
				'tags'=>array(
					'pageTag'=>'平均用户数',
					'unit'=>'(人)'
				),
				//页面图层名称
				'layerType'=>array(
					'display'=>true,
					'first'=>$gisConfig->projectName.':'.$gisConfig->layer->sitesLayer,
					'second'=>$gisConfig->projectName.':'.$gisConfig->layer->gridsLayer
				),
				//子菜单配置
				'childMenu'=>array(
					'display'=>false
				),
				//页面查询条件配置
				'queryConfig' => array(
					'one'=>array(
						'tag'=>'昨日数据',
						'style'=>'userNum_one',
						'queryKey'=>'usernumberone'
					),
					'seven'=>array(
						'tag'=>'七天数据',
						'style'=>'userNum_seven',
						'queryKey'=>'usernumberseven'
					),
					'fifteen'=>array(
						'tag'=>'十五天数据',
						'style'=>'userNum_fif',
						'queryKey'=>'usernumberfifteen'
					),
					'thirty'=>array(
						'tag'=>'三十天数据',
						'style'=>'userNum_thirty',
						'queryKey'=>'usernumberthirty'
					)
				),
				//详细信息页面配置
				'moreInfo'=>array(
					'searchId'=>array(
						'grids'=>'gridId',
						'sites'=>'lac,cellid'
					),
					'date'=>'yyyymmdd',
					'stockChart'=>array(
						'modelName'=>array(
							'grids'=>'GridUserNumberDay',
							'sites'=>'LacUserNumberDay'
						),
						'queryKey'=>'userNumber',
						'chartName'=>'最近三十天区域用户数量趋势图',
						'xTag'=>'用户数量(人)',
						'yTag'=>'数量(人)'
					)
				),
				//图层切换配置
				'layerSwitcherConfig' => array(
					array(
						'min'=>0,
						'max'=>197,
						'color'=>'green',
						'keywords'=>'<span class="color" style="background:#1cf411"></span><span class="val">用户数少(0-196人)</span>'
					),
					array(
						'min'=>197,
						'max'=>398,
						'color'=>'blue',
						'keywords'=>'<span class="color" style="background:#0009ff"></span><span class="val">用户数较少(197-397人)</span>'
					),
					array(
						'min'=>398,
						'max'=>601,
						'color'=>'orange',
						'keywords'=>'<span class="color" style="background:#f1a41e"></span><span class="val">用户数较多(398-600人)</span>'
					),
					array(
						'min'=>601,
						'max'=>803,
						'color'=>'purple',
						'keywords'=>'<span class="color" style="background:#b153f3"></span><span class="val">用户数多(601-802人)</span>'
					),
					array(
						'min'=>803,
						'max'=>9999,
						'color'=>'red',
						'keywords'=>'<span class="color" style="background:#fd0606"></span><span class="val">用户数很多(803人以上)</span>'
					)
				)
			),
			
			//弱信号比例页面配置
			'lowRssi'=>array(
				//点击信息配置
				'tags'=>array(
					'pageTag'=>'弱信号比',
					'unit'=>'(%)'
				),
				//页面图层名称
				'layerType'=>array(
					'display'=>true,
					'first'=>$gisConfig->projectName.':'.$gisConfig->layer->sitesLayer,
					'second'=>$gisConfig->projectName.':'.$gisConfig->layer->gridsLayer
				),
				//子菜单配置
				'childMenu'=>array(
					'display'=>false
				),
				//页面查询条件配置
				'queryConfig' => array(
					'one'=>array(
						'tag'=>'昨日数据',
						'style'=>'lowRssi_one',
						'queryKey'=>'weakrssirateone'
					),
					'seven'=>array(
						'tag'=>'七天数据',
						'style'=>'lowRssi_seven',
						'queryKey'=>'weakrssirateseven'
					),
					'fifteen'=>array(
						'tag'=>'十五天数据',
						'style'=>'lowRssi_fif',
						'queryKey'=>'weakrssiratefifteen'
					),
					'thirty'=>array(
						'tag'=>'三十天数据',
						'style'=>'lowRssi_thirty',
						'queryKey'=>'weakrssiratethirty'
					)
				),
				//详细信息页面配置
				'moreInfo'=>array(
					'searchId'=>array(
						'grids'=>'gridId',
						'sites'=>'lac,cellid'
					),
					'date'=>'yyyymmdd',
					'stockChart'=>array(
						'modelName'=>array(
							'grids'=>'GridWeakRssiRateDay',
							'sites'=>'LacWeakRssiRateDay'
						),
						'queryKey'=>'weakRssiRate',
						'chartName'=>'最近三十天区域平均弱信号比例趋势图',
						'xTag'=>'平均弱信号比例(%)',
						'yTag'=>'百分比(%)'
					)
				),
				//图层切换配置
				'layerSwitcherConfig' => array(
					array(
						'min'=>0,
						'max'=>10,
						'color'=>'green',
						'keywords'=>'<span class="color" style="background:#1cf411"></span><span class="val">区域信号很强(0%-10%)</span>'
					),
					array(
						'min'=>10,
						'max'=>20,
						'color'=>'blue',
						'keywords'=>'<span class="color" style="background:#0009ff"></span><span class="val">区域信号强(10%-20%)</span>'
					),
					array(
						'min'=>20,
						'max'=>30,
						'color'=>'orange',
						'keywords'=>'<span class="color" style="background:#f1a41e"></span><span class="val">区域信号弱(20%-30%)</span>'
					),
					array(
						'min'=>30,
						'max'=>100,
						'color'=>'red',
						'keywords'=>'<span class="color" style="background:#fd0606"></span><span class="val">区域信号很弱(30%以上)</span>'
					)
				)				
			),

			//脱网次数页面配置
			'netBreak'=>array(
				//点击信息配置
				'tags'=>array(
					'pageTag'=>'平均脱网次数',
					'unit'=>'(次)'
				),
				//页面图层名称
				'layerType'=>array(
					'display'=>true,
					'first'=>$gisConfig->projectName.':'.$gisConfig->layer->sitesLayer,
					'second'=>$gisConfig->projectName.':'.$gisConfig->layer->gridsLayer
				),
				//子菜单配置
				'childMenu'=>array(
					'display'=>false
				),
				//页面查询条件配置
				'queryConfig' => array(
					'one'=>array(
						'tag'=>'昨日数据',
						'style'=>'netBreak_one',
						'queryKey'=>'offnetnumberone'
					),
					'seven'=>array(
						'tag'=>'七天数据',
						'style'=>'netBreak_seven',
						'queryKey'=>'offnetnumberseven'
					),
					'fifteen'=>array(
						'tag'=>'十五天数据',
						'style'=>'netBreak_fif',
						'queryKey'=>'offnetnumberfifteen'
					),
					'thirty'=>array(
						'tag'=>'三十天数据',
						'style'=>'netBreak_thirty',
						'queryKey'=>'offnetnumberthirty'
					)
				),
				//详细信息页面配置
				'moreInfo'=>array(
					'searchId'=>array(
						'grids'=>'gridId',
						'sites'=>'lac,cellid'
					),
					'date'=>'yyyymmdd',
					'stockChart'=>array(
						'modelName'=>array(
							'grids'=>'GridOffnetNumberDay',
							'sites'=>'LacOffnetNumberDay'
						),
						'queryKey'=>'offnetNumber',
						'chartName'=>'最近三十天区域脱网次数趋势图',
						'xTag'=>'脱网次数(次)',
						'yTag'=>'数量(次)'
					)
				),
				//图层切换配置
				'layerSwitcherConfig' => array(
					array(
						'min'=>0,
						'max'=>101,
						'color'=>'green',
						'keywords'=>'<span class="color" style="background:#1cf411"></span><span class="val">脱网数很少(0-100次)</span>'
					),
					array(
						'min'=>101,
						'max'=>201,
						'color'=>'blue',
						'keywords'=>'<span class="color" style="background:#0009ff"></span><span class="val">脱网数少(101-200次)</span>'
					),
					array(
						'min'=>201,
						'max'=>401,
						'color'=>'orange',
						'keywords'=>'<span class="color" style="background:#f1a41e"></span><span class="val">脱网数多(201-400次)</span>'
					),
					array(
						'min'=>401,
						'max'=>9999,
						'color'=>'red',
						'keywords'=>'<span class="color" style="background:#fd0606"></span><span class="val">脱网数很多(401次以上)</span>'
					)
				)
			),
			
			// T->G切换次数页面配置
			'T2GSwitch'=>array(
				//点击信息配置
				'tags'=>array(
					'pageTag'=>'T->G切换次数',
					'unit'=>'(次)'
				),
				//页面图层名称
				'layerType'=>array(
					'display'=>false,
					'first'=>$gisConfig->projectName.':'.$gisConfig->layer->sitesLayer
				),
				//子菜单配置
				'childMenu'=>array(
					'display'=>true,
					'divId'=>'switchMenu'
				),
				//页面查询条件配置
				'queryConfig' => array(
					'one'=>array(
						'tag'=>'昨日数据',
						'style'=>'t2gSwitch_one',
						'queryKey'=>'t2gswitchnumber1'
					),
					'seven'=>array(
						'tag'=>'七天数据',
						'style'=>'t2gSwitch_seven',
						'queryKey'=>'t2gswitchnumber7'
					),
					'fifteen'=>array(
						'tag'=>'十五天数据',
						'style'=>'t2gSwitch_fif',
						'queryKey'=>'t2gswitchnumber15'
					),
					'thirty'=>array(
						'tag'=>'三十天数据',
						'style'=>'t2gSwitch_thirty',
						'queryKey'=>'t2gswitchnumber30'
					)
				),
				//详细信息页面配置
				'moreInfo'=>array(
					'searchId'=>array(
						'sites'=>'lac,cellid',
						'grids'=>'gridId'
					),
					'date'=>'yyyymmdd',
					'stockChart'=>array(
						'modelName'=>array(
							'sites'=>'LacGtExchangeNumberDay',
							'grids'=>'GridGtExchangeNumberDay'
						),
						'queryKey'=>'t2gSwitchNumber',
						'chartName'=>'最近三十天区域T->G切换次数趋势图',
						'xTag'=>'切换次数(次)',
						'yTag'=>'数量(次)'
					)
				),
				//图层切换配置
				'layerSwitcherConfig' => array(
					array(
						'min'=>0,
						'max'=>101,
						'color'=>'green',
						'keywords'=>'<span class="color" style="background:#1cf411"></span><span class="val">切换次数很少(0-100次)</span>'
					),
					array(
						'min'=>101,
						'max'=>201,
						'color'=>'blue',
						'keywords'=>'<span class="color" style="background:#0009ff"></span><span class="val">切换次数少(101-200次)</span>'
					),
					array(
						'min'=>201,
						'max'=>401,
						'color'=>'orange',
						'keywords'=>'<span class="color" style="background:#f1a41e"></span><span class="val">切换次数多(201-400次)</span>'
					),
					array(
						'min'=>401,
						'max'=>9999,
						'color'=>'red',
						'keywords'=>'<span class="color" style="background:#fd0606"></span><span class="val">切换次数很多(401次以上)</span>'
					)
				)
			),
			
			//小区重选次数页面配置
			'allReselect'=>array(
				//点击信息配置
				'tags'=>array(
					'pageTag'=>'小区重选次数',
					'unit'=>'(次)'
				),
				//页面图层名称
				'layerType'=>array(
					'display'=>false,
					'first'=> $gisConfig->projectName.':'.$gisConfig->layer->sitesLayer
				),
				//子菜单配置
				'childMenu'=>array(
					'display'=>true,
					'divId'=>'switchMenu'
				),
				//页面查询条件配置
				'queryConfig' => array(
					'one'=>array(
						'tag'=>'昨日数据',
						'style'=>'allReselect_one',
						'queryKey'=>'allreselectnumber1',
						'moreInfoKey'=>'g2tReselectNumber1,t2gReselectNumber1,g2fourReselectNumber1,four2gReselectNumber1,t2fourReselectNumber1,four2tReselectNumber1'
					),
					'seven'=>array(
						'tag'=>'七天数据',
						'style'=>'allReselect_seven',
						'queryKey'=>'allreselectnumber7',
						'moreInfoKey'=>'g2tReselectNumber7,t2gReselectNumber7,g2fourReselectNumber7,four2gReselectNumber7,t2fourReselectNumber7,four2tReselectNumber7'
					),
					'fifteen'=>array(
						'tag'=>'十五天数据',
						'style'=>'allReselect_fif',
						'queryKey'=>'allreselectnumber15',
						'moreInfoKey'=>'g2tReselectNumber15,t2gReselectNumber15,g2fourReselectNumber15,four2gReselectNumber15,t2fourReselectNumber15,four2tReselectNumber15'
					),
					'thirty'=>array(
						'tag'=>'三十天数据',
						'style'=>'allReselect_thirty',
						'queryKey'=>'allreselectnumber30',
						'moreInfoKey'=>'g2tReselectNumber30,t2gReselectNumber30,g2fourReselectNumber30,four2gReselectNumber30,t2fourReselectNumber30,four2tReselectNumber30'
					)
				),
				//详细信息页面配置
				'moreInfo'=>array(
					'searchId'=>array(
						'sites'=>'lac,cellid',
						'grids'=>'gridId'
					),
					'date'=>'yyyymmdd',
					'stockChart'=>array(
						'modelName'=>array(
							'sites'=>'LacGtExchangeNumberDay',
							'grids'=>'GridGtExchangeNumberDay'
						),
						'queryKey'=>'allReselectNumber',
						'chartName'=>'最近三十天小区重选次数趋势图',
						'xTag'=>'重选次数(次)',
						'yTag'=>'数量(次)'
					)
				),
				//图层切换配置
				'layerSwitcherConfig' => array(
					array(
						'min'=>0,
						'max'=>101,
						'color'=>'green',
						'keywords'=>'<span class="color" style="background:#1cf411"></span><span class="val">重选次数很少(0-100次)</span>'
					),
					array(
						'min'=>101,
						'max'=>201,
						'color'=>'blue',
						'keywords'=>'<span class="color" style="background:#0009ff"></span><span class="val">重选次数少(101-200次)</span>'
					),
					array(
						'min'=>201,
						'max'=>401,
						'color'=>'orange',
						'keywords'=>'<span class="color" style="background:#f1a41e"></span><span class="val">重选次数多(201-400次)</span>'
					),
					array(
						'min'=>401,
						'max'=>9999,
						'color'=>'red',
						'keywords'=>'<span class="color" style="background:#fd0606"></span><span class="val">重选次数很多(401次以上)</span>'
					)
				)
			),
			
			
			//G->T重选次数页面配置
			'G2TReselect'=>array(
				//点击信息配置
				'tags'=>array(
					'pageTag'=>'G->T切换次数',
					'unit'=>'(次)'
				),
				//页面图层名称
				'layerType'=>array(
					'display'=>false,
					'first'=>$gisConfig->projectName.':'.$gisConfig->layer->sitesLayer
				),
				//子菜单配置
				'childMenu'=>array(
					'display'=>true,
					'divId'=>'switchMenu'
				),
				//页面查询条件配置
				'queryConfig' => array(
					'one'=>array(
						'tag'=>'昨日数据',
						'style'=>'g2tReselect_one',
						'queryKey'=>'g2treselectnumber1'
					),
					'seven'=>array(
						'tag'=>'七天数据',
						'style'=>'g2tReselect_seven',
						'queryKey'=>'g2treselectnumber7'
					),
					'fifteen'=>array(
						'tag'=>'十五天数据',
						'style'=>'g2tReselect_fif',
						'queryKey'=>'g2treselectnumber15'
					),
					'thirty'=>array(
						'tag'=>'三十天数据',
						'style'=>'g2tReselect_thirty',
						'queryKey'=>'g2treselectnumber30'
					)
				),
				//详细信息页面配置
				'moreInfo'=>array(
					'searchId'=>array(
						'sites'=>'lac,cellid',
						'grids'=>'gridId'
					),
					'date'=>'yyyymmdd',
					'stockChart'=>array(
						'modelName'=>array(
							'sites'=>'LacGtExchangeNumberDay',
							'grids'=>'GridGtExchangeNumberDay'
						),
						'queryKey'=>'g2tReselectNumber',
						'chartName'=>'最近三十天区域G->T重选次数趋势图',
						'xTag'=>'切换次数(次)',
						'yTag'=>'数量(次)'
					)
				),
				//图层切换配置
				'layerSwitcherConfig' => array(
					array(
						'min'=>0,
						'max'=>101,
						'color'=>'green',
						'keywords'=>'<span class="color" style="background:#1cf411"></span><span class="val">切换次数很少(0-100次)</span>'
					),
					array(
						'min'=>101,
						'max'=>201,
						'color'=>'blue',
						'keywords'=>'<span class="color" style="background:#0009ff"></span><span class="val">切换次数少(101-200次)</span>'
					),
					array(
						'min'=>201,
						'max'=>401,
						'color'=>'orange',
						'keywords'=>'<span class="color" style="background:#f1a41e"></span><span class="val">切换次数多(201-400次)</span>'
					),
					array(
						'min'=>401,
						'max'=>9999,
						'color'=>'red',
						'keywords'=>'<span class="color" style="background:#fd0606"></span><span class="val">切换次数很多(401次以上)</span>'
					)
				)
			),
			
			//乒乓切换次数页面配置
			'pingPongSwitch'=>array(
				//点击信息配置
				'tags'=>array(
					'pageTag'=>'乒乓切换次数',
					'unit'=>'(次)'
				),
				//页面图层名称
				'layerType'=>array(
					'display'=>false,
					'first'=>$gisConfig->projectName.':'.$gisConfig->layer->sitesLayer
				),
				//子菜单配置
				'childMenu'=>array(
					'display'=>true,
					'divId'=>'switchMenu'
				),
				//页面查询条件配置
				'queryConfig' => array(
					'one'=>array(
						'tag'=>'昨日数据',
						'style'=>'pingP_one',
						'queryKey'=>'ppnumber1',
						'otherKey'=>'ppLaunchNumber1,ppLaunchRssi1,ppReceiveNumber1,ppReceiveRssi1',
						'moreInfoKey'=>'launchLac,launchCellid,receiveLac,receiveCellid,ppNumber1,ppLaunchRssi1,ppReceiveRssi1'
					),
					'seven'=>array(
						'tag'=>'七天数据',
						'style'=>'pingP_seven',
						'queryKey'=>'ppnumber7',
						'otherKey'=>'ppLaunchNumber7,ppLaunchRssi7,ppReceiveNumber7,ppReceiveRssi7',
						'moreInfoKey'=>'launchLac,launchCellid,receiveLac,receiveCellid,ppNumber7,ppLaunchRssi7,ppReceiveRssi7'
					),
					'fifteen'=>array(
						'tag'=>'十五天数据',
						'style'=>'pingP_fif',
						'queryKey'=>'ppnumber15',
						'otherKey'=>'ppLaunchNumber15,ppLaunchRssi15,ppReceiveNumber15,ppReceiveRssi15',
						'moreInfoKey'=>'launchLac,launchCellid,receiveLac,receiveCellid,ppNumber15,ppLaunchRssi15,ppReceiveRssi15'
					),
					'thirty'=>array(
						'tag'=>'三十天数据',
						'style'=>'pingP_thirty',
						'queryKey'=>'ppnumber30',
						'otherKey'=>'ppLaunchNumber30,ppLaunchRssi30,ppReceiveNumber30,ppReceiveRssi30',
						'moreInfoKey'=>'launchLac,launchCellid,receiveLac,receiveCellid,ppNumber30,ppLaunchRssi30,ppReceiveRssi30'
					)
				),
				//详细信息页面配置
				'moreInfo'=>array(
					'searchId'=>array(
						'sites'=>'lac,cellid',
						'grids'=>'gridId'
					),
					'date'=>'yyyymmdd',
					'stockChart'=>array(
						'modelName'=>array(
							'sites'=>'LacGtExchangeNumberDay',
							'grids'=>'GridGtExchangeNumberDay'
						),
						'queryKey'=>'ppNumber',
						'chartName'=>'最近三十天区域乒乓切换次数趋势图',
						'xTag'=>'乒乓切换次数(次)',
						'yTag'=>'数量(次)'
					)
				),
				//图层切换配置
				'layerSwitcherConfig' => array(
					array(
						'min'=>0,
						'max'=>101,
						'color'=>'green',
						'keywords'=>'<span class="color" style="background:#1cf411"></span><span class="val">切换次数很少(0-100次)</span>'
					),
					array(
						'min'=>101,
						'max'=>201,
						'color'=>'blue',
						'keywords'=>'<span class="color" style="background:#0009ff"></span><span class="val">切换次数少(101-200次)</span>'
					),
					array(
						'min'=>201,
						'max'=>401,
						'color'=>'orange',
						'keywords'=>'<span class="color" style="background:#f1a41e"></span><span class="val">切换次数多(201-400次)</span>'
					),
					array(
						'min'=>401,
						'max'=>9999,
						'color'=>'red',
						'keywords'=>'<span class="color" style="background:#fd0606"></span><span class="val">切换次数很多(401次以上)</span>'
					)
				)
			),
			
			//网络平均下载速率页面配置
			'downLoad'=>array(
				//点击信息配置
				'tags'=>array(
					'pageTag'=>'平均下载速率',
					'unit'=>'(Kbps)'
				),
				//页面图层名称
				'layerType'=>array(
					'display'=>false,
					'first'=>$gisConfig->projectName.':'.$gisConfig->layer->sitesLayer
				),
				//子菜单配置
				'childMenu'=>array(
					'display'=>true,
					'divId'=>'netWorkMenu'
				),
				//页面查询条件配置
				'queryConfig' => array(
					'one'=>array(
						'tag'=>'昨日数据',
						'style'=>'downLoad_one',
						'queryKey'=>'downloadrateone'
					),
					'seven'=>array(
						'tag'=>'七天数据',
						'style'=>'downLoad_seven',
						'queryKey'=>'downloadrateseven'
					),
					'fifteen'=>array(
						'tag'=>'十五天数据',
						'style'=>'downLoad_fif',
						'queryKey'=>'downloadratefifteen'
					),
					'thirty'=>array(
						'tag'=>'三十天数据',
						'style'=>'downLoad_thirty',
						'queryKey'=>'downloadratethirty'
					)
				),
				//详细信息页面配置
				'moreInfo'=>array(
					'searchId'=>array(
						'sites'=>'lac,cellid'
					),
					'date'=>'yyyymmdd',
					'stockChart'=>array(
						'modelName'=>array(
							'sites'=>'LacDownloadRateDay'
						),
						'queryKey'=>'downloadRate',
						'chartName'=>'最近三十天区域网络平均下载速率趋势图',
						'xTag'=>'平均下载速率(Kbps)',
						'yTag'=>'速率(Kbps)'
					)
				),
				//图层切换配置
				'layerSwitcherConfig' => array(
					array(
						'min'=>101,
						'max'=>9999,
						'color'=>'green',
						'keywords'=>'<span class="color" style="background:#1cf411"></span><span class="val">下载速率很高(101Kbps以上)</span>'
					),
					array(
						'min'=>81,
						'max'=>101,
						'color'=>'blue',
						'keywords'=>'<span class="color" style="background:#0009ff"></span><span class="val">下载速率高(81-100Kbps)</span>'
					),
					array(
						'min'=>51,
						'max'=>81,
						'color'=>'orange',
						'keywords'=>'<span class="color" style="background:#f1a41e"></span><span class="val">下载速率低(51-80Kbps)</span>'
					),
					array(
						'min'=>0,
						'max'=>51,
						'color'=>'red',
						'keywords'=>'<span class="color" style="background:#fd0606"></span><span class="val">下载速率很低(0-50Kbps)</span>'
					)
				)
			),
			
			//网络平均延时页面配置
			'delayTime'=>array(
				//点击信息配置
				'tags'=>array(
					'pageTag'=>'平均网络延时',
					'unit'=>'(ms)'
				),
				//页面图层名称
				'layerType'=>array(
					'display'=>false,
					'first'=>$gisConfig->projectName.':'.$gisConfig->layer->sitesLayer
				),
				//子菜单配置
				'childMenu'=>array(
					'display'=>true,
					'divId'=>'netWorkMenu'
				),
				'queryConfig' => array(
					'one'=>array(
						'tag'=>'昨日数据',
						'style'=>'delayTime_one',
						'queryKey'=>'delaytimeone'
					),
					'seven'=>array(
						'tag'=>'七天数据',
						'style'=>'delayTime_seven',
						'queryKey'=>'delaytimeseven'
					),
					'fifteen'=>array(
						'tag'=>'十五天数据',
						'style'=>'delayTime_fif',
						'queryKey'=>'delaytimefifteen'
					),
					'thirty'=>array(
						'tag'=>'三十天数据',
						'style'=>'delayTime_thirty',
						'queryKey'=>'delaytimethirty'
					)
				), 
				
				//详细信息页面配置
				'moreInfo'=>array(
					'searchId'=>array(
						'sites'=>'lac,cellid'
					),
					'date'=>'yyyymmdd',
					'stockChart'=>array(
						'modelName'=>array(
							'sites'=>'LacDelayTimeDay'
						),
						'queryKey'=>'delayTime',
						'chartName'=>'最近三十天区域网络平均延时趋势图',
						'xTag'=>'平均网络延时(ms)',
						'yTag'=>'时间(ms)'
					)
					
				),
				//图层切换配置
				'layerSwitcherConfig' => array(
					array(
						'min'=>0,
						'max'=>201,
						'color'=>'red',
						'keywords'=>'<span class="color" style="background:#1cf411"></span><span class="val">网络延时很低(0-200ms)</span>'
					),
					array(
						'min'=>201,
						'max'=>301,
						'color'=>'blue',
						'keywords'=>'<span class="color" style="background:#0009ff"></span><span class="val">网络延时低(201-300ms)</span>'
					),
					array(
						'min'=>301,
						'max'=>401,
						'color'=>'orange',
						'keywords'=>'<span class="color" style="background:#f1a41e"></span><span class="val">网络延时高(301-400ms)</span>'
					),
					array(
						'min'=>401,
						'max'=>9999,
						'color'=>'red',
						'keywords'=>'<span class="color" style="background:#fd0606"></span><span class="val">网络延时很高(401ms以上)</span>'
					)
				)
			),
			
			//网络平均丢包率页面配置
			'packetLoss'=>array(
				//点击信息配置
				'tags'=>array(
					'pageTag'=>'平均丢包率',
					'unit'=>'(%)'
				),
				//页面图层名称
				'layerType'=>array(
					'display'=>false,
					'first'=>$gisConfig->projectName.':'.$gisConfig->layer->sitesLayer
				),
				//子菜单配置
				'childMenu'=>array(
					'display'=>true,
					'divId'=>'netWorkMenu'
				),
				//页面查询条件配置
				'queryConfig' => array(
					'one'=>array(
						'tag'=>'昨日数据',
						'style'=>'packetLoss_one',
						'queryKey'=>'packetlossrateone'
					),
					'seven'=>array(
						'tag'=>'七天数据',
						'style'=>'packetLoss_seven',
						'queryKey'=>'packetlossrateseven'
					),
					'fifteen'=>array(
						'tag'=>'十五天数据',
						'style'=>'packetLoss_fif',
						'queryKey'=>'packetlossratefifteen'
					),
					'thirty'=>array(
						'tag'=>'三十天数据',
						'style'=>'packetLoss_thirty',
						'queryKey'=>'packetlossratethirty'
					)
				),
				
				//详细信息页面配置
				'moreInfo'=>array(
					'searchId'=>array(
						'sites'=>'lac,cellid'
					),
					'date'=>'yyyymmdd',
					'stockChart'=>array(
						'modelName'=>array(
							'sites'=>'LacPacketlossRateDay'
						),
						'queryKey'=>'packetlossRate',
						'chartName'=>'最近三十天区域平均网络丢包率趋势图',
						'xTag'=>'平均网络丢包率(%)',
						'yTag'=>'百分比(%)'
					)
				),
				//图层切换配置
				'layerSwitcherConfig' => array(
					array(
						'min'=>0,
						'max'=>5,
						'color'=>'green',
						'keywords'=>'<span class="color" style="background:#1cf411"></span><span class="val">丢包率很低(0-5%)</span>'
					),
					array(
						'min'=>5,
						'max'=>10,
						'color'=>'blue',
						'keywords'=>'<span class="color" style="background:#0009ff"></span><span class="val">丢包率低(5-10%)</span>'
					),
					array(
						'min'=>10,
						'max'=>15,
						'color'=>'orange',
						'keywords'=>'<span class="color" style="background:#f1a41e"></span><span class="val">丢包率高(10-15%)</span>'
					),
					array(
						'min'=>15,
						'max'=>100,
						'color'=>'red',
						'keywords'=>'<span class="color" style="background:#fd0606"></span><span class="val">丢包率很高(15%以上)</span>'
					)
				)
			),
			
			//全网诊断页面配置
			'complain'=>array(
				//点击信息配置
				'tags'=>array(
					'pageTag'=>'全网诊断',
					'unit'=>'(次)'
				),
				//页面图层名称
				'layerType'=>array(
					'display'=>false,
					'first'=>$gisConfig->projectName.':'.$gisConfig->layer->gridsLayer
				),
				//子菜单配置
				'childMenu'=>array(
					'display'=>false
				),
				//页面查询条件配置
				'queryConfig' => array(
					'one'=>array(
						'tag'=>'昨日数据',
						'style'=>'complain_one',
						'queryKey'=>'complaintotalnumberone'
					),
					'seven'=>array(
						'tag'=>'七天数据',
						'style'=>'complain_seven',
						'queryKey'=>'complaintotalnumberseven'
					),
					'fifteen'=>array(
						'tag'=>'十五天数据',
						'style'=>'complain_fif',
						'queryKey'=>'complaintotalnumberfifteen'
					),
					'thirty'=>array(
						'tag'=>'三十天数据',
						'style'=>'complain_thirty',
						'queryKey'=>'complaintotalnumberthirty'
					)
				),
				
				//详细信息页面配置
				'moreInfo'=>array(
					'searchId'=>array(
						'grids'=>'gridId'
					),
					'date'=>'yyyymmdd',
					'stockChart'=>array(
						'modelName'=>array(
							'grids'=>'GridComplainTotalNumberDay',	
						),
						'queryKey'=>'complainTotalNumber',
						'chartName'=>'最近三十天区域平均全网诊断次数趋势图',
						'xTag'=>'诊断次数(次)',
						'yTag'=>'数量(次)'
					),
					'pieChart'=>array(
						'modelName'=>array(
							'grids'=>'GridComplainMatterNumberRecently'
						),
						'queryKey'=>'complainMatterRateThirty',
						'chartName'=>'最近三十天区域各类诊断问题所占平均比例',
					)
				),
				//图层切换配置
				'layerSwitcherConfig' => array(
					array(
						'min'=>0,
						'max'=>101,
						'color'=>'green',
						'keywords'=>'<span class="color" style="background:#1cf411"></span><span class="val">诊断数量很少(0-100次)</span>'
					),
					array(
						'min'=>101,
						'max'=>201,
						'color'=>'blue',
						'keywords'=>'<span class="color" style="background:#0009ff"></span><span class="val">诊断数量少(101-200次)</span>'
					),
					array(
						'min'=>201,
						'max'=>301,
						'color'=>'orange',
						'keywords'=>'<span class="color" style="background:#f1a41e"></span><span class="val">诊断数量多(201-300次)</span>'
					),
					array(
						'min'=>301,
						'max'=>9999,
						'color'=>'red',
						'keywords'=>'<span class="color" style="background:#fd0606"></span><span class="val">诊断数量很多(301次以上)</span>'
					)
				)
			),
			// 基站业务统计页面配置
			'siteBusiness'=>array(
				//点击信息配置
				'tags'=>array(
					'pageTag'=>'平均用户数',
					'unit'=>'日均'
				),
				//页面图层名称
				'layerType'=>array(
					'display'=>false,
					'first'=>$gisConfig->projectName.':'.$gisConfig->layer->gridsLayer
				),
				//子菜单配置
				'childMenu'=>array(
					'display'=>false
				)
			),

			//投诉次数页面配置
			'comNum'=>array(
				//点击信息配置
				'tags'=>array(
					'pageTag'=>'平均投诉次数',
					'unit'=>'(次)'
				),
				//页面图层名称
				'layerType'=>array(
					'display'=>true,
					'first'=>$gisConfig->projectName.':'.$gisConfig->layer->sitesLayer,
					'second'=>$gisConfig->projectName.':'.$gisConfig->layer->gridsLayer
				),
				//子菜单配置
				'childMenu'=>array(
					'display'=>false
				),
				//页面查询条件配置
				'queryConfig' => array(
					'one'=>array(
						'tag'=>'昨日数据',
						'style'=>'comNum_one',
						'queryKey'=>'complainnumberone'
					),
					'seven'=>array(
						'tag'=>'七天数据',
						'style'=>'comNum_seven',
						'queryKey'=>'complainnumberseven'
					),
					'fifteen'=>array(
						'tag'=>'十五天数据',
						'style'=>'comNum_fif',
						'queryKey'=>'complainnumberfifteen'
					),
					'thirty'=>array(
						'tag'=>'三十天数据',
						'style'=>'comNum_thirty',
						'queryKey'=>'complainnumberthirty'
					)
				),
				//详细信息页面配置
				'moreInfo'=>array(
					'searchId'=>array(
						'grids'=>'gridId',
						'sites'=>'lac,cellid'
					),
					'date'=>'yyyymmdd',
					'stockChart'=>array(
						'modelName'=>array(
							'grids'=>'GridComplainProblemDay',
							'sites'=>'LacComplainProblemDay'
						),
						'queryKey'=>'complainNumber',
						'chartName'=>'最近三十天区域投诉次数趋势图',
						'xTag'=>'投诉次数(次)',
						'yTag'=>'数量(次)'
					)
				),
				//图层切换配置
				'layerSwitcherConfig' => array(
					array(
						'min'=>0,
						'max'=>21,
						'color'=>'green',
						'keywords'=>'<span class="color" style="background:#1cf411"></span><span class="val">投诉很少(0-20次)</span>'
					),
					array(
						'min'=>21,
						'max'=>51,
						'color'=>'blue',
						'keywords'=>'<span class="color" style="background:#0009ff"></span><span class="val">投诉较少(21-50次)</span>'
					),
					array(
						'min'=>51,
						'max'=>101,
						'color'=>'orange',
						'keywords'=>'<span class="color" style="background:#f1a41e"></span><span class="val">投诉较多(51-100次)</span>'
					),
					array(
						'min'=>101,
						'max'=>9999,
						'color'=>'red',
						'keywords'=>'<span class="color" style="background:#fd0606"></span><span class="val">投诉很多(101次以上)</span>'
					)
				)
			),
		);
		
		if($page)
			if($tag)
				return $allConfig[$page][$tag];
			else
				return $allConfig[$page];
		else
			return $allConfig;
	}
	
	
	
/**
  * 返回GIS图层切换配置与图例配置
  * 
  * @param String $page  需要返回页面的名称，如果为空，则返回所有页面配置
  * @param String $type  需要返回的配置类型，'switcher'为图层切换，'legend'为图例
  * @return Array $return 
  * 
  * @author zhanghy
  * @date 2013-12-09 08:37:22
  *
  **/
	static function getSwitcher($page,$type){
		$tags = self::getGISLayers($page,'layerSwitcherConfig');
	}
}
