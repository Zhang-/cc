<?php
/**
 * 这是配置文件
 *
 * @version 1.1
 * @author heige
 * @date 2013-03-15
 *
 */

if(!defined('MAP_STRING')) exit;

return array(
	'imgformat' => 'png', //#图片格式,为png、jpg
	'imgdir' => 'cache/', //缓存图片存放的目录
	'imgtype' => 'm', //地图类型  m:是道路地图，s:是卫星地图， h:是卫星地图上信息图层（包括道路、地区名等）
	'lc'=>'~_~',
	'server0'=>'117.135.133.66', //请求117切片
	'server1'=>'58.215.186.249' //请求58切片
);