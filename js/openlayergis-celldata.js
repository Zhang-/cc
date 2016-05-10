var map;
var cacheID='';//缓存ID
var filterstr='';//请求filter字符串
var taggtype='lashen';//拉升标记
var proj = new OpenLayers.Projection("EPSG:4326");//gps坐标
var mapproj = new OpenLayers.Projection("EPSG:900913");//google坐标
var INITextent = new OpenLayers.Bounds(init_exten[0],init_exten[1], init_exten[2], init_exten[3]);
var MAXextent = new OpenLayers.Bounds(
	   108.816471, 27.539679, 129.910221, 34.418000 );
INITextent.transform(proj,mapproj); //初始位置
MAXextent.transform(proj,mapproj); //超过此范围为左右不能拖动
var is_search_cellinfo=false;//用户判断是否为输入lac-cellid进行查找的条件
/* 即时搜索图层 */
var downLoadLayer = new OpenLayers.Layer.Vector("平均下载速率", {
							style: layer_style,
							alpha:true,
							//transparent:true,
							isBaseLayer: false
							});
var delayLayer = new OpenLayers.Layer.Vector("平均延时", {
							style: layer_style,
							alpha:true,
							//transparent:true,
							isBaseLayer: false
							});
var lossLayer = new OpenLayers.Layer.Vector("平均丢包率", {
							style: layer_style,
							alpha:true,
							//transparent:true,
							isBaseLayer: false
							});


/*
google底层地图
*/
var google_map=[new OpenLayers.Layer.XYZ(
		"街景模式",
		"/google_street/" ,
		{zoomOffset: 1,'getURL':get_my_url_street} 
		),new OpenLayers.Layer.XYZ(
		"卫星地图",
		"/google_Satellite/" ,
		{zoomOffset: 1,'getURL':get_my_url_Satellite})];
/*
创建map对象
*/	
map = new OpenLayers.Map({
	div: "map1",
	maxExtent: new OpenLayers.Bounds(
		-128 * 156543.0339, -128 * 156543.0339,
		128 * 156543.0339, 128 * 156543.0339
	),
	restrictedExtent: MAXextent,
	maxResolution: 611.496226171875*2*2*2*2*2*2*2, 
	controls: [],
	projection: new OpenLayers.Projection("EPSG:900913"),//
	units: "m",
	layers: google_map
});
/*初始范围*/
//var INITextent = new OpenLayers.Bounds(13380769.379044,3750665.8250743,13383831.63749,3752022.5823261);
map.zoomToExtent(INITextent);

var celllayers = CreateCellLayer();

if(outputKey == 'cellDataAnalysisGIS')
{
	var celllayers = [celllayers[0],downLoadLayer,delayLayer,lossLayer];
}
map.addLayers(celllayers);

/*点击获取小区信息*/
map.events.register('click', map, function (e) {
	var params = {
			REQUEST: "GetFeatureInfo",
			EXCEPTIONS: "application/vnd.ogc.se_xml",
			BBOX: map.getExtent().toBBOX(),
			SERVICE: "WMS",
			VERSION: "1.1.1",
			x: Math.round(e.xy.x),
			y: Math.round(e.xy.y),
			INFO_FORMAT: 'text/html',
			QUERY_LAYERS: layer_name,
			FEATURE_COUNT: 10000,
			Layers: layer_name,
			Styles: '',
			WIDTH: map.size.w,
			HEIGHT: map.size.h,
			format: 'image/png',
			srs: 'EPSG:900913',
			key_word: key_word,
			sortBy : query_key+" desc", 
			propertyName : 'angle,'+query_key+',name,lac,cellid,lon,lat,centerlon,centerlat',
			md : md,
			startTime : str,
			stopTime : stp,
			page : page
		};
		var lonlat=map.getLonLatFromPixel(e.xy);
		SearchReset();
		$.ajax({
			type : 'post',
			url  :  'index.php?r=networkAnalysis/GisClickPoints',
			data : params,
			beforeSend : function(){$('#blackDiv').show();},
			success : function(data){
				data = eval('(' + data + ')');
				var allPopup = "";
				for(var tempPupup in data)
				{
					var thisPopup = data[tempPupup];
					var oneLon = thisPopup['lon'];
					var oneLat = thisPopup['lat'];
					allPopup += getPopup(thisPopup,page);
					
				}
				addmakerlayer(oneLon,oneLat,allPopup,false);
				$('#blackDiv').hide();
			}
		});
	OpenLayers.Event.stop(e);
});
function display(){
	$("#loader_container1").css('display','none');
	$("#info_table").css('display','block');
}
function SearchReset(){
	$("#search_lac").val("输入LAC搜索");
	$("#search_cellid").val("输入CELLID搜索");
}
/*地图框选功能*/
var boxLayer = new OpenLayers.Layer.Vector("Box layer");
var select_box=new OpenLayers.Control.DrawFeature(boxLayer,
			OpenLayers.Handler.RegularPolygon, {
				title:'框选查看数据',
				handlerOptions: {
					sides: 4,
					irregular: true	
				},
				featureAdded : function(feature){
					var bounds=feature.geometry.getBounds();
					var params1 = {
						key_word: key_word,
						REQUEST : 'GetFeature',
						SERVICE : 'WFS',
						VERSION : '1.0.0',
						outputFormat : 'json',
						TYPENAME : layer_name,
						sortBy : query_key+" desc", 
						filter:	'<Filter xmlns:gml=\'http://www.opengis.net/gml\'><BBOX><PropertyName>geom_data</PropertyName><gml:Box srsName=\'EPSG:900913\'><gml:coordinates>'+bounds.left.toFixed(4)+','+bounds.bottom.toFixed(4)+' '+bounds.right.toFixed(4)+','+bounds.top.toFixed(4)+'</gml:coordinates></gml:Box></BBOX></Filter>',
						MAXFEATURES : 10000,
						propertyName : 'angle,'+query_key+',name,lac,cellid,lon,lat,centerlon,centerlat',
						md : md,
						startTime : str,
						stopTime : stp,
						page : page
					};
					SearchReset();
					$.ajax({
						type : 'post',
						url  :  'index.php?r=networkAnalysis/GisClickPoints',
						data : params1,
						beforeSend : function(){$('#blackDiv').show();},
						success : function(data){
							data = eval('(' + data + ')');
							var allPopup = "";
							for(var tempPupup in data)
							{
								var thisPopup = data[tempPupup];
								var oneLon = thisPopup['lon'];
								var oneLat = thisPopup['lat'];
								allPopup += getPopup(thisPopup,page);
							}
							addmakerlayer(oneLon,oneLat,allPopup,false);
							$('#blackDiv').hide();
						}
					});	
				}
			}
		);
map.events.register("moveend", map ,function(){   
	//drag, pan, zoom事件,用来实现在执行搜索后用户拖动/放大/缩小地图触发新的搜索请求
	// alert("地图缩放至：" + this.getZoom() + "级");    
	if(!is_search_cellinfo)
	{
		SearchReset();
		removeAllPopup();
	}else{
		is_search_cellinfo=false;
	}
	if(map.getZoom() < timeZoomLevel){
		select_box.deactivate();
		$('.olControlDrawFeatureItemInactive').hide();	
	}else{
		$('#serchTime').show();
		$('.olControlDrawFeatureItemInactive').show();
	}
});  
/*前一视角后一视角功能*/
var panel = new OpenLayers.Control.Panel({'div':OpenLayers.Util.getElement('paneldiv')});
nav = new OpenLayers.Control.NavigationHistory();
map.addControl(nav);
panel.addControls([nav.next, nav.previous]);//前一视角，后一视角	
/*工具栏（拖拽，放大，框选）*/
var select_box_con=new OpenLayers.Control.NavToolbar({'div':OpenLayers.Util.getElement('navtooldiv')});
var nav = new OpenLayers.Control.NavigationHistory();
select_box_con.addControls(select_box);
map.addControl(new OpenLayers.Control.PanZoomBar());
map.addControl(new OpenLayers.Control.Navigation());
map.addControl(select_box_con);
map.addControl(new OpenLayers.Control.LayerSwitcher());
map.addControl(new OpenLayers.Control.Scale($('scale')));

map.addControl(panel);
$('.olControlDrawFeatureItemInactive').hide(); //初始条件：隐藏框选功能

/*小框地图*/
 var mapOptions = {
            maxExtent:MAXextent, 
            maxResolution: 611.496226171875*2*2*2*2*2*2*2,
            projection: "EPSG:900913"
        };
 var controlOptions = {
            maximized: true,
            mapOptions: OpenLayers.Util.extend(mapOptions, {
                maxResolution: 611.496226171875*2*2*2*2*2*2*2,
                maxExtent: new OpenLayers.Bounds(
				-128 * 156543.0339, -128 * 156543.0339,
				128 * 156543.0339, 128 * 156543.0339)
            }),
			layers: [google_map[0].clone()]
        };
        var overview = new OpenLayers.Control.OverviewMap(controlOptions);
        map.addControl(overview); 

function CreateCellLayer(){
	var layerarr=new Array();
	var layer = new OpenLayers.Layer.WMS('all', 
		serverurl,
		{layers: layer_name,format: 'image/png', transparent: 'true',STYLES:'init_style'},
		{displayInLayerSwitcher : false});
	layerarr.push(layer);
	for(s in key_array){	
		var layer = new OpenLayers.Layer.Vector(key_array[s].keywords, {
							style: layer_style,
							alpha:true,
							isBaseLayer: false
							});
		layerarr.push(layer);
	}
	return layerarr;
}


/*函数功能：添加标记*/
function addmakerlayer(lon_p,lat_p,allPopup,setCenter){
	if(!!lon_p&&!!lat_p){
		var lon=parseFloat(lon_p);
		var lat=parseFloat(lat_p);
		removeAllPopup();
		var size = new OpenLayers.Size(140,170);
		var ss_o= new OpenLayers.Geometry.Point(lon,lat);
		ss_o.transform(proj,mapproj);
		var makerpoint=new OpenLayers.LonLat(ss_o.x, ss_o.y);
 		var popup = new OpenLayers.Popup("chicken",
		makerpoint,
		null,
		"<b>小区信息</b><div class='popupDiv'>"+allPopup+"</div>",
		true);
		popup.autoSize=false;
		if(setCenter)
		map.setCenter(makerpoint);
		map.addPopup(popup);
	}
	else 
		alert('没有选择小区');
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
	
function removeAllPopup(){//移除标记信息
    var len =map.popups.length;   
    for(var i=len-1;i>=0;i--){
      map.removePopup(map.popups[i]);
    }
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

/**函数功能：判断是否为正整数*/
function forcheck(ss){
	var type="^[0-9]*[1-9][0-9]*$";
  //  var   re   =   new   RegExp(type);
    if(ss.match(type)==null){
		return false;
	}
	return true;
} 
/*
**功能：搜索小区
**描述：根据输入的lac和cellid查找，
  结果显示在表格中
*/
function search_cellInfo () {
	is_search_cellinfo=true;
	removeAllPopup();
	if($.trim($('#search_lac').val())!='输入LAC搜索'){
		var text_str_lac=$.trim($('#search_lac').val());
		if(!forcheck(text_str_lac)){
			alert('查询的CELLID,LAC必须为正整数');
			return false;
		}
	}
	else 
		var text_str_lac='';
	if($.trim($('#search_cellid').val())!='输入CELLID搜索'){
		var text_str_cellid=$.trim($('#search_cellid').val());
		if(!forcheck(text_str_cellid)){
			alert('查询的CELLID,LAC必须为正整数');
			return false;	
		}
	}
	else 
		var text_str_cellid='';
	if(!!text_str_lac&&!!text_str_cellid){
		if(!!text_str_lac){
			if(!!text_str_cellid){
				var fileter='<Filter>';
				fileter+='<And>';
				fileter+='<PropertyIsEqualTo>';
				fileter+='<PropertyName>lac</PropertyName>';
				fileter+='<Literal>'+text_str_lac+'</Literal>';
				fileter+='</PropertyIsEqualTo>';
				fileter+='<PropertyIsEqualTo>';
				fileter+='<PropertyName>cellid</PropertyName>';
				fileter+='<Literal>'+text_str_cellid+'</Literal>';
				fileter+='</PropertyIsEqualTo>';
				fileter+='</And>';
				fileter+='</Filter>';
			}
			else{
				var fileter='<Filter>';
				fileter+='<PropertyIsEqualTo>';
				fileter+='<PropertyName>lac</PropertyName>';
				fileter+='<Literal>'+text_str_lac+'</Literal>';
				fileter+='</PropertyIsEqualTo>';
				fileter+='</Filter>';
			}	
		}
		if(!!text_str_cellid)
		{
			if(!!text_str_lac){
				var fileter='<Filter>';
				fileter+='<And>';
				fileter+='<PropertyIsEqualTo>';
				fileter+='<PropertyName>lac</PropertyName>';
				fileter+='<Literal>'+text_str_lac+'</Literal>';
				fileter+='</PropertyIsEqualTo>';
				fileter+='<PropertyIsEqualTo>';
				fileter+='<PropertyName>cellid</PropertyName>';
				fileter+='<Literal>'+text_str_cellid+'</Literal>';
				fileter+='</PropertyIsEqualTo>';
				fileter+='</And>';
				fileter+='</Filter>';
			}
			else{
				var fileter='<Filter>';
				fileter+='<PropertyIsEqualTo>';
				fileter+='<PropertyName>cellid</PropertyName>';
				fileter+='<Literal>'+text_str_cellid+'</Literal>';
				fileter+='</PropertyIsEqualTo>';
				fileter+='</Filter>';
			}	
		}
		$.ajax({
			type : 'post',
			url :	'index.php?r=networkAnalysis/GisClickPoints',
			data : {
				key_word: key_word,
				REQUEST: 'GetFeature',
				SERVICE: 'WFS',
				VERSION: '1.0.0',
				outputFormat: 'json',
				TYPENAME: layer_name,
				MAXFEATURES: 10000,
				sortBy : query_key+" desc", 
				propertyName : 'angle,'+query_key+',name,lac,cellid,lon,lat,centerlon,centerlat',
				filter: fileter,
				md : md,
				startTime : str,
				stopTime : stp,
				page : page
			},
			beforeSend : function(){$('#blackDiv').show();},
			success : function(data){
				if(data!=='[]')
				{
				data = eval('(' + data + ')');
				var thisPopup = data[0];
				var oneLon = thisPopup['lon'];
				var oneLat = thisPopup['lat'];
				allPopup = getPopup(thisPopup,page);
				addmakerlayer(oneLon,oneLat,allPopup,true);
				$('#blackDiv').hide();
				}else{
					$('#blackDiv').hide();
					alert('未找到符合此搜索条件的小区信息');
				}
				
			}
		});
	}
	else
		alert("请输入完整的LAC与CELLID");
}


/* 根据site_points中的对应地理信息点画圆/扇形 */
function drawRing(alldata,allpoints)
{
	for(var tempType in alldata)
	{
		var dataType;
		switch(tempType)
		{
			case '0': dataType = downLoadLayer;break;
			case '1': dataType = delayLayer;break;
			case '2': dataType = lossLayer;break;
		}
		var thisTypeData = alldata[tempType];
		var polygonFeatureList = [];
		for(var tempData in thisTypeData)
		{
			var thisPointData = thisTypeData[tempData];
			if(thisPointData['color']!=='null') 
			{
				if(allpoints[thisPointData['laccellid']])
				{
					var thisPointGeom = allpoints[thisPointData['laccellid']];
					var pointColor = style_model(thisPointData['color']);
					var thisPoint = thisPointGeom.split(",");
					var pointList = [];
					var newPoint;
					var ring;
					var newPolygon;
					var polygonFeature;
					for( temp1 in thisPoint)
					{
						var point = thisPoint[temp1].split(" ");
						newPoint = new OpenLayers.Geometry.Point(point[0],point[1]);
						newPoint.transform(proj,mapproj);
						pointList.push(newPoint);
				
					}
					ring = new OpenLayers.Geometry.LinearRing(pointList);
					newPolygon = new OpenLayers.Geometry.Polygon([ring]);
					polygonFeature = new OpenLayers.Feature.Vector(newPolygon,null,pointColor);
					polygonFeatureList.push(polygonFeature);
				}
			}
		}
		dataType.removeAllFeatures();	
		dataType.addFeatures(polygonFeatureList);	
		map.addLayer(dataType);
	}
}


function cellCoveringDrawRing(allPoints,layerCell,gisPoints)
{
	for(var tempType in layerCell)
	{
		var dataType;
		switch(tempType)
		{
			case 'red': dataType = celllayers[1];break;
			case 'pink': dataType = celllayers[2];break;
			case 'yellow': dataType = celllayers[3];break;
			case 'green': dataType = celllayers[4];break;
			case 'brilliantBlue': dataType = celllayers[5];break;
			case 'cyan': dataType = celllayers[6];break;
			case 'blue': dataType = celllayers[7];break;
		}
		var thisTypeData = layerCell[tempType];
		var polygonFeatureList = [];
		for(var tempData in thisTypeData)
		{
			var thisPointData = thisTypeData[tempData];
			if(gisPoints[thisPointData])
			{
				var thisPointGeom = gisPoints[thisPointData];
				var pointColor = style_model(tempType);
				var thisPoint = thisPointGeom.split(",");
				
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
					newPoint.transform(proj,mapproj);
					pointList.push(newPoint);
			
				}
				ring = new OpenLayers.Geometry.LinearRing(pointList);
				newPolygon = new OpenLayers.Geometry.Polygon([ring]);
				polygonFeature = new OpenLayers.Feature.Vector(newPolygon,null,pointColor);
				polygonFeatureList.push(polygonFeature);
			}
		}
		dataType.removeAllFeatures();	
		dataType.addFeatures(polygonFeatureList);	
	}
}

function getPopup(DataArray,page)
{
	var allPopup = '';
	if(page == 'cellDataAnalysisGIS')
		var nameArray = {name:'小区名',lac:'LAC',cellid:'CELLID',angle:'角度',download:'平均下载速率',delay:'平均延迟',loss:'平均丢包率'}; 
	else
		var nameArray = {name:'小区名',lac:'LAC',cellid:'CELLID',angle:'角度',rssi:'平均RSSI',all:'用户总数',low:'低于平均值用户数',lowRate:'低于平均值用户比例'};
	for(var tempKey in nameArray)
	{
		allPopup +="<li><span class='sp1'>"+nameArray[tempKey]+":</span><span>"+DataArray[tempKey]+"</span></li>";
	}
	return allPopup = "<ul>"+allPopup+"</ul>";
}


//地图使用帮助弹出框
$(function (){
	
	$('#map_help').click(function (){
		$("#tanchuhelp").dialog("open");
		
		})
	$( "#tanchuhelp" ).dialog({
			autoOpen: false,
			minHeight: 300,
			minWidth: 750 ,
			modal: true,
			resizable: false, 
			draggable :true
			//close:function(){$('#view').empty();}
		});

		
})

/* $("#test_click").click(function(){
	layer.mergeNewParams({STYLES:'test_style'});	
}); */