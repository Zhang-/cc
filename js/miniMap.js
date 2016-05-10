var map,EPSGGPS,EPSGGOOGLE,format;
var vectorLayers = new Array();
var markerLayers = new Array();

function MQSMap(initConfig,complainInfo,sitesGeom) {
	display(true);
	EPSGGPS = new OpenLayers.Projection("EPSG:4326"); //gps坐标
	EPSGGOOGLE = new OpenLayers.Projection("EPSG:900913"); //google坐标
	format = 'image/png';

	var initExtent = initConfig.initExtent; //初始范围
	var maxExtent = initConfig.maxExtent; //最大可视/拖动范围
	initExtent = new OpenLayers.Bounds(initExtent[0], initExtent[1], initExtent[2], initExtent[3]).transform(EPSGGPS, EPSGGOOGLE); //初始地图范围
	maxExtent = new OpenLayers.Bounds(maxExtent[0], maxExtent[1], maxExtent[2], maxExtent[3]).transform(EPSGGPS, EPSGGOOGLE); //最大地图范围

	var google_map=[new OpenLayers.Layer.XYZ(
				"jiedao",
				"/google_street/" ,
				{zoomOffset: (initConfig.zoomOffset + initConfig.addLevel),'getURL':get_my_url_street} 
				),new OpenLayers.Layer.XYZ(
				"weixing",
				"/google_Satellite/" ,
				{zoomOffset: (initConfig.zoomOffset + initConfig.addLevel),'getURL':get_my_url_Satellite})];

	map = new OpenLayers.Map({
		div: "map",
		restrictedExtent: maxExtent,
		maxResolution: initConfig.maxResolution / Math.pow(2,initConfig.addLevel),
		numZoomLevels: initConfig.maxLevel - initConfig.addLevel,
		controls: [
			new OpenLayers.Control.LayerSwitcher({
				ascending: false,
				'div': OpenLayers.Util.getElement('layerSwitcher'),
				roundedCorner: true
			}),
			new OpenLayers.Control.Navigation(),
			new OpenLayers.Control.PanZoomBar()
		],
		projection: this.EPSGGOOGLE,  //投影为 900913,google墨卡托投影 
		units: "m", //屏幕坐标以米为单位
		layers: google_map
	});
	map.zoomToExtent(initExtent);

	var base_layer = new OpenLayers.Layer.WMS('all', 
		initConfig.serverUrl,
		{layers: initConfig.layerName,format: format, transparent: 'true',STYLES:initConfig.initStyle},
		{displayInLayerSwitcher : false}
	);
	if (initConfig.startDateTime && initConfig.stopDateTime) {
		var filterstr = '<Filter><Or><PropertyIsBetween><PropertyName>starttime</PropertyName><LowerBoundary><Literal>'+initConfig.startDateTime+'</Literal></LowerBoundary><UpperBoundary><Literal>'+initConfig.stopDateTime+'</Literal></UpperBoundary></PropertyIsBetween><PropertyIsBetween><PropertyName>endtime</PropertyName><LowerBoundary><Literal>'+initConfig.startDateTime+'</Literal></LowerBoundary><UpperBoundary><Literal>'+initConfig.stopDateTime+'</Literal></UpperBoundary></PropertyIsBetween><And><PropertyIsGreaterThanOrEqualTo><PropertyName>endtime</PropertyName><Literal>'+initConfig.stopDateTime+'</Literal></PropertyIsGreaterThanOrEqualTo><PropertyIsLessThanOrEqualTo><PropertyName>starttime</PropertyName><Literal>'+initConfig.startDateTime+'</Literal></PropertyIsLessThanOrEqualTo></And></Or></Filter>';
		var statement_layer = new OpenLayers.Layer.WMS('口径信息影响区域', 
			initConfig.serverUrl,
			{layers: initConfig.statement.layerName,format: format, transparent: 'true',filter: filterstr,STYLES:'statement_red'},
			{displayInLayerSwitcher : true}
		);
		map.addLayers([base_layer,statement_layer]); //添加基站基础图层 与口径影响范围图层
		eventsRegister(initConfig.statement.layerName);

	var publicMarkerLayer = new OpenLayers.Layer.Markers('用户轨迹起点'); //共用marker图层
		publicMarkerLayer.events.register("visibilitychanged", publicMarkerLayer, function() { //注册图层切换事件
			removeFeature();
			removeAllPopup();
		});
	var publicVectorLayer = new OpenLayers.Layer.Vector('victor',{alpha:true,displayInLayerSwitcher:false}); //共用vector图层
	publicVectorLayer.events.register("visibilitychanged", publicVectorLayer, function() { //注册图层切换事件
		removeFeature();
		removeAllPopup();
	});

	var userPointArray = new Array();
	var lastUserPoint = false;
	for ( var i in complainInfo) {
		var tempMarkerLayer = new OpenLayers.Layer.Markers(complainInfo[i].startDateTime); //当前业务的marker图层
		tempMarkerLayer.events.register("visibilitychanged", tempMarkerLayer, function() { //注册图层切换事件
			removeFeature();
			removeAllPopup();
		});
		var tempVectorLayer = new OpenLayers.Layer.Vector(complainInfo[i].id,{alpha:true,displayInLayerSwitcher:false}); //当前业务的vector图层
		tempVectorLayer.events.register("visibilitychanged", tempVectorLayer, function() { //注册图层切换事件
			removeFeature();
			removeAllPopup();
		});
		var thisPop = ''; //每一个marker点击事件中弹框的内容
		var siteLngLats = new Array(); //每一次业务中所有基站的中心点经纬度集合数组，用于绘制用户点辐射所用基站示意线
		var serviceSites = complainInfo[i].site; //当前业务中所使用的所有基站的相关信息

		if(sitesGeom[serviceSites]){ //如果在数据库中存在本基站的位置信息
			thisPop = getPopup(sitesGeom[serviceSites],'site'); //生成基站信息弹窗内容
			addmaker(sitesGeom[serviceSites].centerlng,sitesGeom[serviceSites].centerlat,thisPop,'site_blue',37,32,false,tempMarkerLayer,'site',sitesGeom[serviceSites].pointdata,tempVectorLayer); //添加基站marker，并添加相关点击事件
			siteLngLats.push(sitesGeom[serviceSites].centerlng + " " + sitesGeom[serviceSites].centerlat); //将本次业务使用的所有基站的中心点存入数组
		}

		thisPop = getPopup(complainInfo[i],complainInfo[i].popupType); //生成用户信息弹窗内容
		var lng = complainInfo[i].lng; //用户位置经度
		var lat = complainInfo[i].lat; //用户位置纬度

		
		if(lng>10 && lng<180 && lat>10 && lat<90) //如果经纬度范围合理
		{
			addmaker(lng,lat,thisPop,complainInfo[i].markerName,30,32,true,tempMarkerLayer,'user',siteLngLats,tempVectorLayer); //添加用户marker，并添加相关点击事件
			if(i!=='complaindata'){
				//userPointArray.push(lng+" "+lat);
				ss_o= new OpenLayers.Geometry.Point(lng,lat);
				ss_o.transform(this.EPSGGPS,this.EPSGGOOGLE);
				if(lastUserPoint == false){
					lastUserPoint = ss_o;
					var marker = addOneMarker(new OpenLayers.LonLat(ss_o.x, ss_o.y),'user_start',30,32);
					publicMarkerLayer.addMarker(marker);
				}
				drawLine(lastUserPoint,[lng+" "+lat],publicVectorLayer,true);
				lastUserPoint = ss_o;
			}
			markerLayers.push(tempMarkerLayer); //首先加载vector图层
			vectorLayers.push(tempVectorLayer); //继续加载marker图层
		}
	}
		drawLine("",userPointArray,publicVectorLayer,true);
		map.addLayer(publicVectorLayer);
		map.addLayers(vectorLayers);
		map.addLayers(markerLayers);
		map.addLayer(publicMarkerLayer);
	}

	display(false);	
}

/*定义地图点击事件，查询口径图层信息*/
function eventsRegister(statementLayerName,filters){
	map.events.register('click', map, function (e) {
		var pixel = new OpenLayers.Pixel(e.xy.x,e.xy.y);
  		var lonlat = map.getLonLatFromPixel(pixel);
  		lonlat.transform(EPSGGOOGLE, EPSGGPS);
        var params = {
            REQUEST: "GetFeatureInfo",
            EXCEPTIONS: "application/vnd.ogc.se_xml",
            BBOX: map.getExtent().toBBOX(),
            SERVICE: "WMS",
            VERSION: "1.1.1",
			x: Math.round(e.xy.x),
			y: Math.round(e.xy.y),
            INFO_FORMAT: 'text/html',
            QUERY_LAYERS: statementLayerName,
            FEATURE_COUNT: 50,
            Layers: statementLayerName,
            filter: filters,
            WIDTH: map.size.w,
            HEIGHT: map.size.h,
            format: format,
            styles: '',
            srs: 'EPSG:900913',
            propertyName : initConfig.statement.keyList
        };
        $.ajax({
			type : 'post',
			url  :  'index.php?r=GIS/gisClickPoints',
			data : params,
			beforeSend : display(true),
			success : function(data){
				display(false);
				data = eval('(' + data + ')');
				if(data.length!=0){
					var allPopup = "";
					for(var tempPupup in data)
					{
						var thisPopup = data[tempPupup];
						allPopup += getPopup(thisPopup, 'statement');
					}
					allPopup = "<b>口径信息详情</b><div class='popupDiv'><ul>"+allPopup+"</ul></div>";
					addmaker(lonlat.lon,lonlat.lat,allPopup);
				}else
					alert("未选择有效区域！");
			}
		});
        OpenLayers.Event.stop(e);
    });
}


/*函数功能：加载google街景地图*/
function get_my_url_street (bounds) {
	var res = this.map.getResolution();  
	var x = Math.round ((bounds.left - this.maxExtent.left) / (res * this.tileSize.w));
	var y = Math.round ((this.maxExtent.top - bounds.top) / (res * this.tileSize.h));
	var z = this.map.getZoom()+this.zoomOffset;
	var s = "Gali".substr(((3 * y + x) % 8));
	var path = "x=" + x + "&y=" + y + "&z=" + z + "&s="+s;
	return 'mapserver/?' + path;
}

/*函数功能：加载google卫星地图*/
function get_my_url_Satellite (bounds) {
	var res = this.map.getResolution();  
	var x = Math.round ((bounds.left - this.maxExtent.left) / (res * this.tileSize.w));
	var y = Math.round ((this.maxExtent.top - bounds.top) / (res * this.tileSize.h));
	var z = this.map.getZoom()+this.zoomOffset;
	var s = "Gali".substr(((3 * y + x) % 8));
	var path = "x=" + x + "&y=" + y + "&z=" + z + "&s="+s+"&t=s";
	return 'mapserver/?' + path;
}

/* 是否显示加载缓冲图 */
function display(display){
	if(display)
		$('#blackDiv').show();
	else
		setTimeout(function(){$('#blackDiv').hide();}, 900);
}

/* 移除所有标记信息 */
function removeAllPopup(){ 
    for(var i in map.popups)
      map.removePopup(map.popups[i]);
}

/* 移除所有标记信息 */
function removeFeature(){//移除标记信息
    for(var i in vectorLayers)
    	vectorLayers[i].removeAllFeatures();
}

/* 创建popup内容 */
function getPopup(dataArray,type){

	var allPopup = '';
	var dataType = '';
	var nameArray = new Array();

	switch(type){

		case 'service' : {
			nameArray = { 
				type: "业务类型",
				startDateTime: "采集时间",
	    		rssi: "信号强度(dbm)",
	    		nRssi: "邻区RSSI强度(dbm)",
	    		//maxrssi: "最强RSSI(dbm)",
	    		//minrssi: "最弱RSSI(dbm)",
	    		CUPuserate: "CPU占用率(%)",
	    		availableMemory: "内存占用率(%)"
		    };

			if(dataArray.type == 'data'){
	    		nameArray.download = "下载速率(KB/s)";
	    		nameArray.upload = "上传速率(KB/s)";
	    		dataArray.type = "数据业务";
		    }else if(dataArray.type == 'voice') {
		    	dataArray.type = "语音业务";
		    }
		    dataType = '动态数据信息';
		};break;

		case 'complainService' : {
			nameArray = {
				static_information_id:'终端ID',
				complain_type:'诊断类型',
				complain_time:'诊断时间',
				lac:'业务LAC',
				cellId:'业务CELLID'
			};
			dataType = '诊断业务信息';
		}break;

		case 'site' : {
			nameArray = {
				lac:'LAC',
				cellid:'CELLID',
				cell_name:'基站名称',
				angle:'天线角度',
				weakrssiratethirty:'近30天弱信号比例(%)',
				downloadratethirty:'近30天平均下载速率(KB/s)',
				packetlossratethirty:'近30天平均丢包率(%)',
				delaytimethirty:'近30天平均延时(ms)'
			};
			dataType = '基站信息';
		};break;

		case 'statement' : {
			nameArray = {
				serial_id: '流水号',
				state_type: '口径类型',
				affect_radius: '影响半径(米)',
				starttime: '开始时间',
				endtime: '结束时间',
				state_title: '口径标题',
				affect_scope: '影响范围',
				affect_area: '影响区(县)',
				problem: '存在问题',
				project_status: '项目目前状态'
			};
		};break;
	}

	for(var tempKey in nameArray)
	{
		if(!dataArray[tempKey])
			dataArray[tempKey] = '未知';
		else if(dataArray[tempKey] == -2)
			dataArray[tempKey] = '暂无';
		allPopup +="<li><span class='sp1'>"+nameArray[tempKey]+":</span><span>"+dataArray[tempKey]+"</span></li>";
	}
	if(type == 'statement')
		return "<ul>"+allPopup+"</ul>";
	else
		return "<b>"+dataType+"</b><div class='popupDiv'><ul>"+allPopup+"</ul></div>";
}

/*添加图标*/
function addmaker(lon_p,lat_p,allPopup,imageName,imageW,imageH,isSetCenter,addLayer,drawType,drawData,drawLayer){
		if(!!lon_p&&!!lat_p){
			var ss_o= new OpenLayers.Geometry.Point(lon_p,lat_p);
			ss_o.transform(this.EPSGGPS,this.EPSGGOOGLE);
			var makerpoint=new OpenLayers.LonLat(ss_o.x, ss_o.y);

			
			var popup = new OpenLayers.Popup("chicken",
				makerpoint,
				null,
				allPopup,
				true,
				function(){
					popup.hide(); //关闭popup
					if (drawLayer)
						drawLayer.removeAllFeatures(); //vector图层清除所有Feature
				}
			);
			popup.autoSize = false;
			popup.opacity = 0.8;
			
			if(isSetCenter) //如果设置居中
				map.setCenter(makerpoint); //本点居中


			if(addLayer){ //如果有添加marker的图层
				var marker = addOneMarker(makerpoint,imageName,imageW,imageH);
				marker.events.register('click',this,function(){ //marker点击事件
					removeFeature();
					removeAllPopup();
					
					if(drawType == 'site')
						drawRing(drawData,drawLayer); //绘制基站扇形
					else if(drawType == 'user'){
						drawLine(ss_o,drawData,drawLayer,false); //绘制用户与本次业务使用基站连线
					}
					map.addPopup(popup,true);
					popup.show();
					popup.panIntoView(); //将地图移动至弹框全部可见的位置
				});
				addLayer.addMarker(marker);
			}else{
				removeFeature();
				removeAllPopup();
				map.addPopup(popup);
				popup.panIntoView(); //将地图移动至弹框全部可见的位置
			}
		}
	}

function addOneMarker(makerpoint,imageName,imageW,imageH){
	var sizeMarker = new OpenLayers.Size(imageW,imageH);
	var offset =new OpenLayers.Pixel(-(sizeMarker.w/2),-sizeMarker.h);
	var marker = new OpenLayers.Marker(makerpoint, new OpenLayers.Icon('OpenLayers/img/'+imageName+'.png', sizeMarker, offset));
	return marker;
}

/* 根据site_points中的对应地理信息点画圆/扇形 */
function drawRing(sitePoints,addLayer)
{
	var polygonFeatureList = [];
	var pointColor = style_model('deep_blue');
	var thisPoint = sitePoints.split(",");
	
	var pointList = [];
	var newPoint;
	var ring;
	var newPolygon;
	var polygonFeature;
	
	for( temp1 in thisPoint)
	{
		var point = thisPoint[temp1].split(" ");
		//alert(point[0]);
		newPoint = new OpenLayers.Geometry.Point(point[0],point[1]);
		newPoint.transform(EPSGGPS,EPSGGOOGLE);
		pointList.push(newPoint);
	}

	ring = new OpenLayers.Geometry.LinearRing(pointList);
	newPolygon = new OpenLayers.Geometry.Polygon([ring]);
	polygonFeature = new OpenLayers.Feature.Vector(newPolygon,null,pointColor);
	polygonFeatureList.push(polygonFeature);
	addLayer.addFeatures(polygonFeatureList);
}

/* 根据用户点及基站原点绘制用户与基站的连线 */
function drawLine(userPoint,sitePoints,addLayer,isall)
{

	var lineFeatureList = [];
	var pointColor;
	if (isall)
		pointColor = style_model('orange');
	else
		pointColor = style_model('deep_blue');

	for( temp1 in sitePoints)
	{
		var pointList = [];
		var newPoint;
		var ring;
		var lineFeature;

		var point = sitePoints[temp1].split(" ");
		newPoint = new OpenLayers.Geometry.Point(point[0],point[1]);
		newPoint.transform(EPSGGPS,EPSGGOOGLE);
		pointList.push(newPoint);
		pointList.push(userPoint);

		ring = new OpenLayers.Geometry.LinearRing(pointList);
		lineFeature = new OpenLayers.Feature.Vector(ring,null,pointColor);
		lineFeatureList.push(lineFeature);
	}
	addLayer.addFeatures(lineFeatureList);
}