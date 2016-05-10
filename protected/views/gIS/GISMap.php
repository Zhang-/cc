<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-ui-1.8.20.custom.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/DatePicker/WdatePicker.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/gis.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ui-lightness/jquery-ui-1.8.20.custom.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main_in1.css" />
<title>移动网络质量感知系统V2.0 - GISMap</title>
</head>
<body class="gis">

<div id="blackDiv" class="blackdiv" style="position:absolute;z-index:999999999;width:100%;height:100%;display:none">
		<div id="loader_container">
			<div id="loader">
				<div>
					<img src="images/loading1.gif">地图加载中...
				</div>
			</div>
		</div>
</div>

<div class="header" id="mainmenu">
  <ul class="fir_menu" id="yw0">
	<?php HelpTool::getMenuList('gisgismap');?>
	</ul>
</div>


 <div class="table_search">
		<ul>
			<div id="searchGrids" style="display:none">
				<li class="p1" style="margin-right:0">
					<input id="search_gridid" class="searhbutton self_go" type="text" onblur="javascript:if(this.value==''){ this.value='请输入区域编号进行搜索'};" onclick="javascript:if(this.value=='请输入区域编号进行搜索'){ this.value=''};" value="请输入区域编号进行搜索" maxlength="255" size="200" style="width:150px" />
					<input id="submit"  class="sisearch self_bt notload" type="button" onclick="search_info()" value="" />
				</li>
			</div>
			<div id="searchSites" style="display:none">
				<li class="p1" >
				<input id="search_lac" class="searhbutton self_go gis_search" type="text" onblur="javascript:if(this.value==''){ this.value='请输入LAC搜索'};" onclick="javascript:if(this.value=='请输入LAC搜索'){ this.value=''};" value="请输入LAC搜索" maxlength="255" size="30" style="width:105px" />
				</li>
				<li class="gang">--</li>
				<li class="p1" style="margin-right:0">
				<input id="search_cellid" class="searhbutton self_go" type="text" onblur="javascript:if(this.value==''){ this.value='请输入CELLID搜索'};" onclick="javascript:if(this.value=='请输入CELLID搜索'){ this.value=''};" value="请输入CELLID搜索" maxlength="255" size="30" style="width:120px"/>
				<input id="submit"  class="sisearch self_bt notload" type="button" onclick="search_info()" value="" />
				</li>
			</div>
			<!--<li><span  onclick="javascript:alertExtent();">aaaa</span></li>-->
			<li class="brand">
				<div class="sele_div" id="select_date_div" >
					<input type="text" id="select_date" readonly="readonly" />
					<span class="btn"></span>
				</div>
				<div class="show_div" ></div>
			</li>
			<li>
				<div id="vipUser" style="display:none">
					<span class="subupdate" onclick="searchVip()">用户轨迹查询</span>
				</div>
			</li>
			<li style="margin-left:-8px">
				<div id="complainUser" style="display:none">
					<span class="subupdate" onclick="searchComplain()">投诉点查询</span>
				</div>
			</li>
			<li class="gis_change" style="display:none;">
			</li>
			<li>
				<div id="siteSelect"  style="display:none">
					<span class="subupdate" onclick="searchSite()">基站显示选择</span>
				</div>
			</li>
	<?php
	if(HelpTool::checkActionAccess('networkanalysisgisoutput')){
	?>				
		<li class="p1">
			<div id="exprot_box" class="exprot_box gis"> 
				<span class="load_out" title="导出表格"></span>
				<div id="img_box" style="display:none;"></div>
				<div id="adminshow"></div>
			</div>	
		</li>				
	<?php }	?>
			<li class="windows_all"><span>导航</span></li>

		</ul>
        <div class="box_shadow">
		  <div class="ds ds1"></div>
		  <div class="ds ds2"></div>
		  <div class="ds ds3"></div>
		  <div class="ds ds4"></div>
		  <div class="ds ds5"></div>
		</div>
		<div class="clear"></div>
</div>

<div id="childMenu">
	<div id="netWorkMenu" class='data_all' style="display:none">
	   <div>
			<a id="downLoad" class="li1 select" onclick="javascript:pageChange('downLoad');" ><span>下载速率</span></a>
			<a id="delayTime" class="li1" onclick="javascript:pageChange('delayTime');" ><span>延时</span></a>
			<a id="packetLoss" class="li1" onclick="javascript:pageChange('packetLoss');" ><span>丢包率</span></a>
	   </div>
	</div>
	<div id="switchMenu" class='data_all' style="display:none">
		<div>
			<!-- T2G all -->
			<a id="T2GSwitch" class="li1 select" onclick="javascript:pageChange('T2GSwitch');" ><span>T->G切换</span></a>
			<a id="allReselect" class="li1" onclick="javascript:pageChange('allReselect');" ><span>小区重选</span></a>
			<a id="pingPongSwitch" class="li1" onclick="javascript:pageChange('pingPongSwitch');" ><span>乒乓切换</span></a>
		</div>
	</div>
</div>

   
<div class="gis_tool">
 <div class="navitool_div">
					<div id="navtooldiv" class="olControlNavToolbar olControlDrawFeature"></div>
					<div id="paneldiv" class="olControlPanel"></div>
					<div class="map_help"><span id="map_help"></span></div>
 </div>
</div> 
  <div id="layerSwitcher" class="five_color"></div>
<div class="main_table" style="">
   <script>
   function mapx(){
	var jian2 = $('.gis .table_search').height();
    var he = $(window).height();
    $('.main_table').height(he-jian2-1);
   }
   mapx();
   $(window).resize(function() {
   mapx();
   }) 
   </script>
	<div class="small"  style="height:100%">
		<div id="map1" style="width:100%;height:100%;float:left;"></div>
		<script src="OpenLayers/OpenLayers.debug-min.js"></script>
		<script src="js/openlayergis-style.js"></script>
		<script src="js/openlayergis.js"></script>
		<script>
		 $('.olControlNoSelect div').addClass('ad')
		</script>


	
		<div id="loader_container1">
			<div>
				<div>
				<img src="images/loading2.gif">
				数据加载中...
				</div>
			</div>
		</div>
		<iframe frameborder="no"  allowtransparency=true id="info_table" class='gis_iframe' name="gis_POST" style="height:600px;float:left;display:none">
		</iframe>
	
  </div>
</div>

<div id="gisVipUser" >
  <!--<iframe src="" marginwidth="0" width="100%" height="185" marginheight="0"  frameborder="0" id="gisSearchUser" class="dataview UserViewPage"></iframe>-->
  <div class="form_modify alert_iframe" style="border:1px solid #ddd">
	<div class="form">	
		<div class="row">
			<label>用户IMSI<span style="color:red" >&nbsp;*</span> </label>
			<input type="text" id="inputImsi" maxlength="20" name="searchVipUser[imsi]" />
		</div>
		<div class="row">
			<label>用户IMEI<span style="color:red" >&nbsp;*</span> </label>
			<input type="text" id="inputImei" maxlength="20" name="searchVipUser[imei]" />
		</div>
		<div class="row select">
			<label>时间<span style="color:red" >&nbsp;*</span></label>
			<select id ="selectTime" name="searchVipUser[time]">
				<option value="1" >今天</option>
				<option value="2" >昨天</option>
				<option value="3" >最近3天</option>
				<option value="4" >最近7天</option>
			</select>
		</div>
		
		<div class="row buttons" style="text-align:left;padding:9px 0 9px 270px">
			<input id="but_sub" type="button" onclick="searchVipUser()" value="查找"/>
		</div>
	</div><!-- form -->
   </div>
</div>

<div id="gisComplain">
	<div class="form_modify alert_iframe" style="border:1px solid #ddd">
		<div class="form">	
			<div class="row">
				<label>开始时间<span style="color:red" >&nbsp;*</span> </label>
				<input type="text" readonly="readonly" id="start" class="Wdate" name="startDateTime" value="<?php echo  date('Y-m-d'); ?>" onClick="WdatePicker({startDate:'%y-%M-01',dateFmt:'yyyy-MM-dd', maxDate:'%y-%M-%d'})"/>
			</div>
			<div class="row">
				<label>结束时间<span style="color:red" >&nbsp;*</span> </label>
				<input type="text" readonly="readonly" id="end" class="Wdate" name="stopDateTime" value="<?php echo date('Y-m-d')?>" onClick="WdatePicker({dateFmt:'yyyy-MM-dd',minDate:start.value,maxDate:'%y-%M-%d'})"/>
			</div>
			<div class="row buttons" style="text-align:left;padding:9px 0 9px 270px">
				<input id="but_sub" type="button" onclick="searchComplainUser()" value="查找"/>
			</div>
		</div>
	</div>
</div>

<div id="site_Select">
	<div class="form_modify alert_iframe" style="border:1px solid #ddd">
		<div class="form">	
			<div class="row1">
				<label><input type="checkbox" id="2GSite" name="2G" checked="checked"/>&nbsp;2G基站</label>
				<label><input type="checkbox" id="3GSite" name="3G" checked="checked"/>&nbsp;3G基站</label>
				<label><input type="checkbox" id="4GSite" name="4G" checked="checked"/>&nbsp;4G基站</label>
			</div>
			<div class="row buttons" style="text-align:left;padding:9px 0 9px 200px">
				<input id="but_sub" type="button" onclick="siteReload()" value="确定"/>
			</div>
		</div>
	</div>
</div>
		
<div id="tanchu" title="GIS使用帮助">
	<div id="view" class="gis_helps">
		<p class="p1"><span>GIS使用帮助</span>主要是展示各区域相应时间段内的相应数据，其中，不同的数据等级被赋予不同的颜色加以区分。</p>
		<div><p class="p2">功能详情：</div>
			<img src="OpenLayers/theme/default/img/pan_off.png"/>：选中后可拖拽地图查看不同区域，点击地图上渲染的区域会弹出相应区域的相关介绍和查看详细信息按钮；</br>
			<img src="OpenLayers/theme/default/img/drag-rectangle-on.png" style="width:24px;height:22px"/>：选中后框选地图会放大显示框选区域；</br>
			<img src="OpenLayers/theme/default/img/move_feature_off.png"/>：选中后框选地图上渲染的区域会弹出相应区域的相关介绍和查看详细信息按钮；</br>
			<img src="OpenLayers/theme/default/img/view_previous_off.png"/>：返回上一步操作；</br>
			<img src="OpenLayers/theme/default/img/view_next_off.png"/>：返回后一步操作；</br>
			<img src="images/down.png" style="width:24px;height:22px"/>：导出当前页面的所有的指标信息。</br>
		</p>
		<p class="p3">地图左下角图层切换区中的地图类型可以切换街道与卫星地图，右下角的鹰眼地图可以鸟瞰并快速浏览本区域。</p>
	</div>
	<div class="clear"></div>
</div>
	
	<div id="gisMoreInfo" title="区域历史数据详情">
		<div id="loader_container2">
			<img src="images/loading2.gif">
			页面加载中...
		</div>
		<iframe src="" marginwidth="0" height="450" width="100%" marginheight="0"  frameborder="0" id = "gisTableFrame" class="dataview UserViewPage"></iframe>
	</div>


<div class='gis_tab'>
 <ul class="">
  <li id="comNum">
    <a href="#" onclick="javascript:pageChange('comNum');" >
	<div class="dv_img"><span></span></div>
    <div class="dv2">投诉统计</div>
	</a>
  </li>
  <li id="userNumber">
    <a href="#" onclick="javascript:pageChange('userNumber');" >
	  <div class="dv_img"><span></span></div>
	  <div class="dv2">网络用户分布</div>
	</a>
  </li>
  <li id="lowRssi">
    <a href="#" onclick="javascript:pageChange('lowRssi');" >
	 <div class="dv_img"><span></span></div>
	 <div class="dv2">信号覆盖能力</div>
	</a>
  </li>
  <li id="TGSwitch">
    <a href="#" onclick="javascript:pageChange('T2GSwitch');" >
	<div class="dv_img"><span></span></div>
	<div class="dv2">网络切换统计</div>
	</a>
  </li>
  <li id="netBreak">
    <a href="#" onclick="javascript:pageChange('netBreak');" >
	<div class="dv_img"><span></span></div>
	<div class="dv2">易脱网区域</div>
	</a>
  </li>
  <li id="dataAnalysis">
    <a href="#" onclick="javascript:pageChange('downLoad');" >
	<div class="dv_img"><span></span></div>
    <div class="dv2">数据业务分析</div>
	</a>
  </li>
  <li id="siteBusiness">
    <a href="#" onclick="javascript:pageChange('siteBusiness');" >
	<div class="dv_img"><span></span></div>
    <div class="dv2">小区业务统计</div>
	</a>
  </li>
 </ul>
</div>
<?php
//搜索模块
//$this->renderPartial($page,array('data'=>$data));
?>

</body>
</html>
