<?php
/**
 * unauthorized.php
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @link http://code.google.com/p/srbac/
 */

/**
 * Default page shown when a not authorized user tries to access a page
 *
 * @author Spyros Soldatos <spyros@valor.gr>
 * @package srbac.views.authitem
 * @since 1.0.2
 */
 ?>
 <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="refresh" content="5; URL=<?php echo $error['url']; ?>" />

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
				.red{
				 color: red;
				}
				a:link {font-size: 10pt;color: #000000;text-decoration: none;font-family: ""宋体"";}
				a:visited{font-size: 10pt;color: #000000;text-decoration: none;font-family: ""宋体"";}
				a:hover {color: red;font-family: ""宋体"";text-decoration: underline;}
				table{border:1px solid #D1DDAA;background-color:#FFF;}
				th{ background-color:#D1DDAA; font-size:14px;}
				td{padding:5px 10px 10px 10px;}
				</style>
				
				<table width="450" border="0" align="center" cellpadding="0" cellspacing="0">
				  <tr>
					<th height="34" class="red" ><?php echo $error["title"];?></th>
				  </tr>
				  <tr align="center">
					<td height="131" ><p><?php echo $error["message"];?><br /><br />
					  5秒后将自动返回系统首页！<br /><br />
					如果浏览器无法跳转，<a class="red" href=<?php echo $error["url"];?>>请点击此处</a>。</p></td>
				  </tr>
				</table>