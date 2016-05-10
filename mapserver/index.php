<?php
/** 
 * 请求并缓存地图切片
 * 
 * 使用方法：
 * url: http://hostname/mapserver/?t=m&z=13&y=3434&x=6848&s=Ga
 * 
 * @version 1.2
 * @author heige
 * @date 2013-03-15
 * 
 * @rewrite zhanghy
 * @date 2013-12-02 17:00:24
 */

define ( 'MAP_STRING', 'comebaby' );

$config = require ('config.php');
require ('function.php');

if (isset ( $_GET ['x'] ) && isset ( $_GET ['y'] ) && isset ( $_GET ['z'] ) && isset ( $_GET ['s'] )) {
	$x = intval ( $_GET ['x'] );
	$y = intval ( $_GET ['y'] );
	$z = intval ( $_GET ['z'] );
	$s = intval ( $_GET ['s'] );
	$t = isset ( $_GET ['t'] ) ? $_GET ['t'] : $config ['imgtype'];

	$imgpath = $config ['imgdir']. $t . '/' . $z . '/'; //图片路径
	$img = $imgpath . $t . '_' . $z . '_' . $y . '_' . $x . '.' . $config ['imgformat']; //图片路径+格式

	header("content-type:image/{$config ['imgformat']}"); //添加浏览器输出头部

	if (file_exists ( $img ))
		echo file_get_contents ( $img ); //获取本地图片
	else{
		$param = "&x=" . $x . "&y=" . $y . "&z=" . $z . "&s=" . $s . "&t=" . $t . "&lc=" . $config['lc'] ;
		downloadImage ( $config, $param ,$imgpath ,$img); //下载远程图片
	}
}


?>