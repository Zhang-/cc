<?php
return array(
		'version'        =>  'V0.1',
		'env'            =>  'TEST', //DEV 开发 TEST测试 PROD发布版
		'dev'            =>  '20150619',
		'city'           =>  '14',//7无锡 8泰州 9南京 10宁波 16测试 ...
		'cache'          =>  require(dirname(__FILE__).'/cache.php'),
		'language'       =>  'zh_cn',	
		'adminEmail'     =>  'webmaster@example.com',
		'installDate'    =>  '2015-06-01 23:59:59', //系统安装日期，用来控制系统数据查询最早时间
		'pageSize'       =>  15, //每页显示数据条数
		'userIdName'     =>  'myUserId',
		'userSessionName'=>  'myRoleActions_view',
		'alertsCacheName'=>  'systemAlertsCache', //系統提醒緩存
		'apk_address'    =>  "http://127.0.0.1/index.php?r=site/tdown&v=",//下载apk地址 v=city值
			
		'mqtt_is_send'       =>  false,//是否向手机推送
		'mqtt_server_url'    =>  "58.215.186.235",//mqtt服务器地址,与接收手机接口要对应
		'mqtt_server_port'   =>  1883,//mqtt端口
		'mqtt_server_target' =>  1,//推送模式0为开发模式，1为正式发布模式

		'limit_time_search'      =>  31,//时间控件范围限制
		'limit_time_search_back' =>  3600*(24*31+1),//后台验证时间范围限制

		//不同用户登录后,看到的关键信息用*号代替
		'hide_info_enabled'=>true,//是否启用信息隐藏
		'hide_info_Admin'=>true,//超级管理员可以看到所有信息
		'hide_info_Manager'=>false,//管理员看到的信息以*代王替
		'hide_info_ViewUser'=>false,//只读用户看到的信息以*代王替
		'hide_info_start_from'=>4,//imsi和imiei从第4个位置开始用*号代替
		'hide_info_length'=>9,//共代替9个字符,前面留3位,后面留3位
		
		//账户在线控制类缓存
		'loginControl'=>
		[
			'debug'            =>  true, //debug为 false 时生效, true,则每个用户可以随时登录
			'cacheDestroyTime' =>  3*60,  //非法退出缓存保留时间（s）
			'loginLimit'       =>  //不同用户组帐号允许同时在线的人数
			[ 
				'Admin'        =>  5, 
				'Manager'      =>  5,
				'ViewUser'     =>  10,
			]
		],
		
		// 配置泰州生成栅格的信息
		'gis_grid'    =>
		[
			'display' =>  true, //是否开启栅格地图
		],

		//日志保留条数
		'logsLimitNum'=>10000, //超过此数字，将清除超出的最早的日志记录

		'error' => require(dirname(__FILE__).'/error.php'),
		
		'powermenu'=>array(
			'items'=>array(
				array('label'=>'首页','url'=>'site/index',
					'items'=>array(
						array('label'=>'我的首页','url'=>'site/index',
							'items'=>array(
								array('label'=>'系统信息','url'=>'site/index'),
							),
						),
					),
					'tag'=>'site',
				),
				array('label'=>'订单统计','url'=>'placeOrder/admin',
					'items'=>array( 
						array('label'=>'终端统计','url'=>'placeOrder/admin',
							'items'=>array(
								array('label'=>'订单列表','url'=>'placeOrder/admin'),
								//array('label'=>'历史用户查询','url'=>'placeOrder/history'),
							),
						),
					),
					'tag'=>'placeOrder',
				),
				array('label'=>'GIS','url'=>'GIS/GISMap',
					'items'=>array(
						array('label'=>'GIS','url'=>'GIS/GISMap',
						),
					),
					'tag'=>'GIS',
				),				
				array('label'=>'系统管理','url'=>'sysmanage/userlogadmin',
					'items'=>array(
						array('label'=>'系统管理','url'=>'sysmanage/userlogadmin',
							'items'=>array(
								array('label'=>'用户日志','url'=>'sysmanage/userlogadmin'),
								array('label'=>'系统用户管理','url'=>'sysmanage/useradmin'),
								array('label'=>'添加系统用户','url'=>'sysmanage/usercreate'),
								array('label'=>'数据备份','url'=>'sysmanage/admin'),
								array('label'=>'更新地理信息','url'=>'sysmanage/dataInput'),
								array('label'=>'初始化栅格数据库','url'=>'sysmanage/initgisgriddb'),
								array('label'=>'系统配置','url'=>'sysmanage/sysconfig'),
							),
						),
					),
					'tag'=>'sysmanage',
				),
				array('label'=>'系统帮助','url'=>'help/index',
					'items'=>array(
						array('label'=>'系统帮助','url'=>'help/index',
							'items'=>array(
								array('label'=>'系统帮助','url'=>'help/index'),
							),
						),
					),
					'tag'=>'help',
				),	
			),
		),
	);