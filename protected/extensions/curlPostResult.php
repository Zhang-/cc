<?php
/**
*author:fangj <ayoah12110808@163.com>
*date:2013-3-21
*keyword：MQS/投诉模块/运维人员处理投诉信息/php crul 跨域传输数据
*@param data 传输数据 map格式array('key'=>'value');
*@param url  传输目的地url
*/
class curlPostResult{
 
	private $data;
	
	private $url;
	
	public function __construct($Data=array(),$Url=''){
		$this->data=$Data;
		$this->url=$Url;
	}
	//return : success:0,unseccess:1
	public function crulExecute(){
	
		$tuCurl = curl_init();
		
		//参数
		curl_setopt($tuCurl, CURLOPT_URL,$this->url);
		curl_setopt($tuCurl, CURLOPT_PORT , 80);
		curl_setopt($tuCurl, CURLOPT_POST, 1);	
		curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $this->data);
		curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1);  
		curl_setopt($tuCurl, CURLOPT_CONNECTTIMEOUT_MS,3000);  
		$tuData = curl_exec($tuCurl); 
		/* if(!curl_errno($tuCurl)){
			$info = curl_getinfo($tuCurl);
			echo 'Took ' . $info['total_time'] . ' seconds to send a request to ' . $info['url'];
		} else {
			echo 'Curl error: ' . curl_error($tuCurl);
		}  */
		$re=curl_multi_getcontent($tuCurl);
		curl_close($tuCurl);
		
		return $re;
	}
}