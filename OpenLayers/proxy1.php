<?php 
	$tuCurl = curl_init();
	//参数
	$srtarr=array();
	$url='http://127.0.0.1:8080/geoserver/MQS/wfs?';
	$srt="<?xml version	'1.0' encoding='UTF-8'?><wfs:GetFeature service='WFS' version='1.0.0' xmlns:wfs='http://www.opengis.net/wfs' xmlns:gml='http://www.opengis.net/gml' xmlns:ogc='http://www.opengis.net/ogc' xmlns:xsi='http://www.w3.org/2001/XMLSchema-instance' xsi:schemaLocation='http://www.opengis.net/wfs http://schemas.opengis.net/wfs/1.0.0/wfs.xsd'><wfs:Query typeName='MQS:MQS_GisData' srsName='EPSG:900913' ><wfs:PropertyName>MQS:name</wfs:PropertyName><ogc:Filter xmlns:ogc='\"http:'\//www.opengis.net/ogc'\"><ogc:And><ogc:PropertyIsLike wildCard='\"*'\" singleChar='\".'\" escape='\"!'\"><ogc:PropertyName>MQS:name</ogc:PropertyName><ogc:Literal>*中联*</ogc:Literal></ogc:PropertyIsLike></ogc:And></ogc:Filter></wfs:Query></wfs:GetFeature>";
	curl_setopt($tuCurl, CURLOPT_URL,$url.$srt);
	curl_setopt($tuCurl, CURLOPT_PORT , 8080);

	curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);  
	curl_setopt($tuCurl, CURLOPT_CONNECTTIMEOUT_MS,3000);  
	$tuData = curl_exec($tuCurl); 
	$re=curl_multi_getcontent($tuCurl);
	curl_close($tuCurl);
	print_r($re);?>