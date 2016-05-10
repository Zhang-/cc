<?php
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'新能源货车助手管理系统',
	'language'=>'zh_cn',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
		'application.modules.srbac.controllers.SBaseController',
	),
	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'123',
		 	// If removed, Gii defaults to localhost only. Edit carefully to taste.
			//'ipFilters'=>array('192.168.1.*','::1'),
		),
		//Srbac 配置模块
		'srbac'     => array(   
		'userclass' => 'ManageList',//User表，可以自己改
		'userid'    =>  'userid',//id 可以自己改
		'username'  =>'username',
		'delimeter' => ' ',
		'debug'     => TRUE,//debug为false时生效
		'pageSize'  => 10,
		'superUser' => 'Admin',
		'css'       => 'srbac.css',
		'layout'    => 'application.views.layouts.main',
		'notAuthorizedView' => 'srbac.views.authitem.unauthorized',
		'alwaysAllowed'     => array(
							'SiteLogin',
							'SiteLogout',
							'SiteIndex',
							'SiteAdmin',
							'SiteError',
							'SiteContact',
							'SiteClearcache',
							'Backup',
							'SysmanageAlertsManage',
							'ApiUserLogin',
							'ApiUserRegister',
							'ApiGrabOrder',
							'ApiPlaceOrder',
							'ApiDriverCheck'
		),
		'userActions'       => array('Show','View','List'),
		'listBoxNumberOfLines' =>15,
		'imagesPath'        => 'srbac.images',
		'imagesPack'        => 'tango',
		'iconText'          => TRUE,
		'header'            => 'srbac.views.authitem.header',
		'footer'            => 'srbac.views.authitem.footer',
		'showHeader'        => TRUE,
		'showFooter'        => TRUE,
		'alwaysAllowedPath'=>'srbac.components',
		),
	),
	// application components
	'components'=>array(
		//file cache
		'cache'=>array(  
			'class'=>'system.caching.CFileCache'  
     	),
		//memory cache
     	/*'cache'=>array(
            'class'=>'CMemCache',
            'servers'=>array(
                array(
                    'host'=>'server1',
                    'port'=>11211,
                    'weight'=>60,
                ),
                array(
                    'host'=>'server2',
                    'port'=>11211,
                    'weight'=>40,
                ),
            ),
            'keyPrefix' => '', 
		    'hashKey' => false, 
		    'serializer' => false
        ),*/
		//redis
		/*'cache'=>array(
            'class'=>'ext.redis.CRedisCache',
            //if you dont set up the servers options it will use the default one 
            //"host=>'127.0.0.1',port=>6379"
            'servers'=>array(
                array(
                    'host'=>'121.42.155.121',
                    'port'=>6379,

                ),
                //if you use 2 servers
                //array(
                //    'host'=>'server2',
                //    'port'=>6379,
                //),
            ),
            //'keyPrefix' => '',
		    //'hashKey' => false,
		    //'serializer' => false,
        ),*/
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
			'authTimeout'=>2400, //用户无响应自动注销时间
		),
		//配置authManager,使用Srbac的SDbAuthManager
		'authManager'=>array(
		'class'=>'application.modules.srbac.components.SDbAuthManager',
		//'class'=>'CDbAuthManager',// Manager 的类型
		'defaultRoles'=>array('guest'),//默认角色
		'itemTable' => 'manage_view_item',//认证项表名称
		'itemChildTable' => 'manage_view_itemchild',//认证项父子关系
		'assignmentTable' => 'manage_list',//认证项赋权关系
		),
		/*'db'=>array(
			//'connectionString' => 'mysql:host=192.168.1.100;dbname=test',
			'emulatePrepare' => true,
			'username' => 'heige',
			'password' => 'heige',
			'charset' => 'utf8',
			'enableProfiling'=>true, //分析sql语句
			'enableParamLogging'=>true //日志中显示每次传参的参数
		),*/
		// PDO MSSQL
		'db'=>array(
		   // 'class'=>'application.components.MyMsSqlConnection', 
		 
		   // old MS PDO + MSSQL 2000:  
		   //'connectionString' => 'mssql:host=HOSTNAME\SQLEXPRESS;dbname=Client',
		 
		   // new MS PDO + MSSQL 2005 2008
		   'connectionString' => 'sqlsrv:Server=127.0.0.1;Database=callcar',
		   //'connectionString' => 'sqlsrv:Server=127.0.0.1;Database=callcar',
		      'username' => 'sa',
		      'password' => 'Shenqi74119',
		      'charset' => 'utf8',
		      'enableProfiling'=>true, //分析sql语句
			  'enableParamLogging'=>true //日志中显示每次传参的参数
		),

		'errorHandler'=>array(
			// use 'site/error' action to display errors
            'errorAction'=>'site/error',
        ),
		'log'=>array(
           'class'=>'CLogRouter',
		   'routes'=>array(
			   array(
				//'class'=>'CWebLogRoute',
				'class'=>'CFileLogRoute',
				'logFile'=>"log.".date("Y-m-d").".log",
				'levels'=>'',//为空时,显示所有的级别
			   ),
			),
        ),
		/*'log'=>array(
	        'class'=>'CLogRouter',
	        'routes'=>array(
	            array(
	                'class'=>'ext.yii-debug-toolbar.YiiDebugToolbarRoute',
	                'ipFilters'=>array('127.0.0.1','192.168.1.*', '116.231.19.187'),
	            ),
	        ),
	    ),*/
	),

    /**
	 * 自定义配置
	 * 
	 * 调用方式(在控制器和视图中都可以调用):
	 * Yii::app()->params->键名
	 * 例:
	 * Yii::app()->params->hide_info_enabled
	*/
	'params' => require(dirname(__FILE__).'/params.php'),
);