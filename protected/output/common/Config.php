<?php
/****************************************************
* File Name:config.inc.php				    *
* File Description:配置系统文件                     *
* Last Modified : 2010-8-27		    *
* author : http:/// 		    *
*****************************************************/
//header('Content-Type:text/html; charset=utf-8');
@session_start();
error_reporting(E_ALL ^ E_NOTICE);
//$sys_vars['site_url']     = 'http://120.195.219.209:9090/y/mis/';
$sys_vars['site_url']     = 'http://localhost/y/mis/';
$sys_vars['dbhost']       = "localhost";
$sys_vars['dbname']       = "mis";
$sys_vars['dbuser']       = "root";
$sys_vars['dbpwd']        = "";
$sys_vars['db_pre']       = "mis_";   //数据表名前缀             
$sys_vars['upload_file_size']=1024*2000;//允许最在上传文件大小,默认2MB(单位:B)
$sys_vars['upload_file_filter']="xls|xml|zip|apk";//允许上传文件类型
$sys_vars['xml_nodes']	  = array('radio','check','input','pic');

require_once("MysqlClass.php");
require_once('FunInc.php');
//require_once('CheckLogin.php');
$db=new db_sql();

//设置时区
date_default_timezone_set('asia/shanghai');

//网站编码转换
$db->query("set names 'utf8'");
?>