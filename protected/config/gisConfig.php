<?php
/** 
  * @name GIS全局配置文件
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
  * 	$gisConfig->lngrectifyVal; //WGS84经度纠偏
  * 	$gisConfig->latrectifyVal; //WGS84纬度纠偏
  * 	$gisConfig->pgConnectStr; //pgsql连接字段
  *  或直接使用 GISConfig::get()->pgConnectStr; 
  * 
  * @autor zhanghy
  * @date 2014-01-03 16:29:40
  * 
  **/

return (object)array(
	'server'=>(object)array(
		'host'=>'58.215.186.249',//'192.168.1.164','58.215.186.249',
		'port'=>8080 //泰州用8990
	),
	'db'=>(object)array(
		'port'=>5432, //pgsql数据库端口
		'user'=>'postgres', //pgsql用户名
		'password'=>'' //pgsql用户密码
	),
	'layer'=>(object)array(
		'gridsLayer'=>'grids', //栅格图层名称
		'sitesLayer'=>'sites', //基站图层数名称
		'statementLayer'=>'statement', //口径信息图层数名称
		'districtLayer'=>'district' //片区图层数名称
	),
	'maxExten'=>array(66.978, 16.319, 154.852, 55.092), //可拖动范围
	'maxResolution'=>(20037508 * 2) / (256 * 2 * 2 * 2 * 2 * 2), //屏幕单像素点对应地图单位值
	'zoomOffset'=>5, //引用google图层的最小缩放等级，配合maxResolution
	'searchLevel'=>10, //部分地图实践触发级别
	'maxLevel'=>15, //最大缩放等级，配合maxResolution与zoomOffset
	//地区编号:7无锡 8泰州 9南京 10宁波 11徐州 12厦门 16测试
	//lngrectifyVal 无锡:849 泰州:905 厦门:923 徐州:1070 宁波:775 南京:964 //增加=偏东
	//latrectifyVal 无锡:415 泰州:500 厦门:553 徐州:310 宁波:553 南京:463 增加=偏南
	'queryTime'=>(object)array(
		'one'=>1,
		'seven'=>7,
		'fifteen'=>15,
		'thirty'=>30,
	),
	'area'=>array(
		'7'=>array(
			'projectName'=>'mqs_wx',
			'initExten'=>array(120.124489, 31.515425, 120.454079, 31.622339),
			'lngrectifyVal'=>849,
			'latrectifyVal'=>415,
			'city'=>'无锡市'
		),
		'8'=>array(
			'projectName'=>'mqs_tz',
			'initExten'=>array(119.771439, 32.422950, 120.101029, 32.534166),
			'lngrectifyVal'=>905,
			'latrectifyVal'=>500,
			'city'=>'泰州市'
		),
		'9'=>array(
			'projectName'=>'mqs_nj',
			'initExten'=>array(118.496338,31.904900,119.132172,32.177007),
			'lngrectifyVal'=>964,
			'latrectifyVal'=>463,
			'city'=>'南京市'
		),
		'10'=>array(
			'projectName'=>'mqs_nb',
			'initExten'=>array(121.40412246738259,29.791208113709228,121.72203933748979,29.93025385391675),
			'lngrectifyVal'=>775,
			'latrectifyVal'=>553,
			'city'=>'宁波市'
		),
		'11'=>array(
			'projectName'=>'mqs_xz',
			'initExten'=>array(117.00099611560586,34.13462751059682,117.44010591783788,34.327645789201114),
			'lngrectifyVal'=>1070,
			'latrectifyVal'=>310,
			'city'=>'徐州市'
		),
		'12'=>array(
			'projectName'=>'mqs_xm',
			'initExten'=>array(118.01816106765398,24.4402888616065,118.17763448683661,24.51481203574578),
			'lngrectifyVal'=>923,
			'latrectifyVal'=>553,
			'city'=>'厦门市'
		),
		'13'=>array(
			'projectName'=>'mqs_cq',
			'initExten'=>array(105.96567220873231,29.2290706780136,107.24145954059774,29.819379404359452),
			'lngrectifyVal'=>699,
			'latrectifyVal'=>605,
			'city'=>'重庆市'
		),
		'14'=>array(
			'projectName'=>'mqs_jn',
			'initExten'=>array(116.90677442655826, 36.61855664924085, 117.12615766258789, 36.710263234774075),
			'lngrectifyVal'=>1070,
			'latrectifyVal'=>310,
			'city'=>'济南市'
		),
		'16'=>array(
			'projectName'=>'mqs_test',
			'initExten'=>array(120.124489, 31.515425, 120.454079, 31.622339),
			'lngrectifyVal'=>849,
			'latrectifyVal'=>415,
			'city'=>'无锡市'
		),
	)
);
?>