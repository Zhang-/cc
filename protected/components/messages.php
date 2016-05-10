<?php 
class messages{
	static function show_msg($url='', $show=''){
		$msg = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
				<html xmlns="http://www.w3.org/1999/xhtml">
				<head>
				<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
				<meta http-equiv="refresh" content="3; URL='.$url.'" />
				<title>信息提示</title>
				<style type="text/css">
				body,td,th{
					font-size: 12px;
				}
				body {
					margin-left: 0px;
					margin-top: 100px;
					margin-right: 0px;
					margin-bottom: 0px;
					line-height:200%;
					background-color:#EFEFEF;
				}
				a:link {font-size: 10pt;color: #000000;text-decoration: none;font-family: ""宋体"";}
				a:visited{font-size: 10pt;color: #000000;text-decoration: none;font-family: ""宋体"";}
				a:hover {color: red;font-family: ""宋体"";text-decoration: underline;}
				table{border:1px solid #D1DDAA;background-color:#FFF;}
				th{ background-color:#D1DDAA; font-size:14px;}
				td{padding:5px 10px 10px 10px;}
				</style>
				</head>
				
				<body>
				<table width="450" border="0" align="center" cellpadding="0" cellspacing="0">
				  <tr>
					<th height="34">提示信息</th>
				  </tr>
				  <tr align="center">
					<td height="131"><p>'.$show.'<br />
					  3秒后自动返回指定页面！<br />
					如果浏览器无法跳转，<a href="'.$url.'">请点击此处</a>。</p></td>
				  </tr>
				</table>
				</body>
				</html>
				';
		echo $msg;
		exit();
	}//end show_msg
}