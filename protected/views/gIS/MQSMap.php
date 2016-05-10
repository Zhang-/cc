<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main_in1.css" />
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl;?>/js/jquery-1.7.2.min.js"></script>
<title>移动网络质量感知系统V2.0 - MQSMap</title>
</head>

<body class="gis">
<div id = "mapDiv">

<div id="blackDiv" class="blackdiv" style="position:absolute;z-index:999999999;width:100%;height:100%;display:none">
		<div id="loader_container">
			<div id="loader">
				<div>
					<img src="images/loading1.gif">地图加载中...
				</div>
			</div>
		</div>
</div>


<div id="layerSwitcher" class="five_color xiaomen"></div>

<div class="main_table" style="width:1000px;height:600px">
	<div class="small"  style="height:100%">
		<div id="map" style="width:100%;height:100%;float:left;"></div>
		<script src="OpenLayers/OpenLayers.debug-min.js"></script>
		<script src="js/openlayergis-style.js"></script>
		<script src="js/miniMap.js"></script>
		<script>
			var initConfig = <?php echo $initConfig; ?>;
			var complainInfo = <?php echo $complainInfo; ?>;
			var sitesGeom = <?php echo $sitesGeom; ?>;
			MQSMap(initConfig,complainInfo,sitesGeom);
		</script>
  </div>
</div>
</div>
</body>
</html>
