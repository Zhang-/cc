<?php 
	$tuCurl = curl_init();
	//参数
	$srtarr=array();
	switch ($_POST['SERVICE']){
		case 'WMS':
		$url='http://192.168.1.162:8080/geoserver/MQS/wms?';
		break;
		case 'WFS':
		$url='http://192.168.1.162:8080/geoserver/MQS/ows?';
		break;
	}
	foreach($_POST as $key=>$val){
		array_push($srtarr,$key.('='.$val));
		
	}
	curl_setopt($tuCurl, CURLOPT_URL,$url.implode('&',$srtarr));
	curl_setopt($tuCurl, CURLOPT_PORT , 8080);
	curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);  
	curl_setopt($tuCurl, CURLOPT_CONNECTTIMEOUT_MS,3000);  
	$tuData = curl_exec($tuCurl); 
	$re=curl_multi_getcontent($tuCurl);
	curl_close($tuCurl);
	print_r($re);?>