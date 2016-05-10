<?php
/**
 * 这是公共函数库文件
 *
 * @version 1.2
 * @author heige
 * @date 2013-03-15
 *
 * @rewrite zhanghy
 * @date 2013-12-02 17:00:24
 */

if(!defined('MAP_STRING')) exit;

	/**
     * 下载远程图片
     * @param string $url 图片的绝对url
     * @param string $filepath 文件的完整路径（包括目录，不包括后缀名,例如/www/images/test） ，此函数会自动根据图片url和http头信息确定图片的后缀名
     * @return mixed 下载成功返回一个描述图片信息的数组，下载失败则返回false
     */
    function downloadImage($config , $param, $filepath, $filename) {
	
        $a = rand(0,1);
		$server = $config["server{$a}"]; //首选服务器
		$b = abs($a-1);
		$server1 = $config["server{$b}"]; //备用服务器
		$re = getServerMap($server, $param, $filepath, $filename); //获取远程切片
		if($re)
			echo $re; //如果第一次可以请求到
		else{
			$re = getServerMap($server1, $param, $filepath, $filename); //第一次请求不到，请求备用服务器
			if($re)
				echo $re;
			else
				echo file_get_contents('./NoPicture.png'); //如果都不能获取，返回没有图片
		}
    }

	/* 获取外网服务器切片 获取成功则保存  获取失败返回false */
	function getServerMap($serverUrl, $param, $filepath, $filename){
		$serverUrl="http://{$serverUrl}/location/mapserver/?{$param}"; //远程服务器url
		$tuCurl = curl_init();
		curl_setopt($tuCurl, CURLOPT_URL,$serverUrl);
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($tuCurl, CURLOPT_CONNECTTIMEOUT_MS,3000);  
		$re = curl_exec($tuCurl);
		if($re){
			//echo $filepath;exit;
			if(!file_exists($filepath))
				mkdir($filepath, 0777);
			$local_file = fopen($filename, 'w');
			fwrite($local_file, $re);
			fclose($local_file);
			//file_put_contents($filepath,$re); //存储至本地
		}
		
		return $re;
	}
	
	
