<?php

require (dirname(__FILE__) . '/yi/framework/YiiBase.php'); 
class Yii extends YiiBase 
{ 
	/** 
	 * 重写createWebApplication函数,自动加载全站配置参数 
	 */ 
	public static function createWebApplication ($config = 'protected/config/main.php') 
	{ 
		if (is_string($config)) 
			$config = require ($config); 
		$sysconfig = require (dirname(__FILE__) . '/protected/config/init.php');
		//print_r( $sysconfig);exit;
		$config = array_merge_recursive($sysconfig, $config); 
		return new CWebApplication($config); 
	} 
} 
//分支版本
echo '';