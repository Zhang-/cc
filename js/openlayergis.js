var map,sitesLayer,gridsLayer,page,typeTagDisplay,layerName,layerStyle,queryTime,queryKey,tagName,initExten,serverUrl,keyArray,searchLevel,initExtent,maxExtent,EPSGGPS,EPSGGOOGLE,google_map,gisConfig,baseLayer,filterLayers,queryKeyList,sitesKeyList,gridsKeyList,keyList,lableLayer,boxLayer,select_box,pageTag,pageUnit,childMenu,isCreateBaseLayer,otherKey,currentZoom,moreInfoKey,ppLableLayer,ppLaunchLayer,ppReceiveLayer,ppLaunchName,ppReceiveName,init_style,userPathLayer,userPathName,complainLayer,complainName,filterLayerReload,siteFilterstr;//google坐标;
getMapConfig('init'); //获取当前页面配置
/* 获取当前页面配置 thisPage */
function getMapConfig(thisPage){
	var layerReload = true;
	$.ajax({
		type: 'post',
		url: 'index.php?r=GIS/GetMapConfig',
		data: {
			page: thisPage
		},
		beforeSend: display(true),
		success: function(data){
			gisConfig = eval('('+data+')');
			if(thisPage == 'init'){
				layerReload = false;
				filterLayerReload = false;
				// isCreateBaseLayer=true;
				EPSGGPS = new OpenLayers.Projection("EPSG:4326");//gps坐标
				EPSGGOOGLE = new OpenLayers.Projection("EPSG:900913");//google坐标
				sitesLayer = gisConfig.initConfig.sites.name; //基站图层名称
				gridsLayer = gisConfig.initConfig.grids.name; //栅格图层名称
				sitesKeyList = gisConfig.initConfig.sites.keyList; //基站图层名称
				gridsKeyList = gisConfig.initConfig.grids.keyList; //栅格图层名称
				page = gisConfig.initConfig.page; //页面名称
				queryTime = gisConfig.initConfig.queryTime; //首次加载查询时间标签
				serverUrl = gisConfig.initConfig.serverUrl; //地图图层地址
				searchLevel = gisConfig.initConfig.searchLevel; //触发事件级别
				initExtent = gisConfig.initConfig.initExtent;
				maxExtent = gisConfig.initConfig.maxExtent;
				initExtent = new OpenLayers.Bounds( initExtent[0], initExtent[1], initExtent[2], initExtent[3] ); //初始地图范围
				maxExtent = new OpenLayers.Bounds( maxExtent[0], maxExtent[1], maxExtent[2], maxExtent[3] ); //最大地图范围
				initExtent.transform(EPSGGPS,EPSGGOOGLE); //初始位置
				maxExtent.transform(EPSGGPS,EPSGGOOGLE); //超过此范围为左右不能拖动
								
				createMap(); //初始化map
				tagName = gisConfig.queryConfig[queryTime].tag; 
			}
			
			typeTagDisplay = gisConfig.layerType.display; //是否显示sites/grids切换按钮
			layerName = gisConfig.layerType.first; //地图第一次加载时使用的图层名称
			if( page != 'siteBusiness'){
				pasteTimeTag(gisConfig.queryConfig); //添加日期选择按钮
				if( isSwitchOrReselect() ){
					if( map.getZoom() >= searchLevel ){
						layerName = sitesLayer;
					}else{
						layerName = gridsLayer;
					}
				}
				// if(isCreateBaseLayer){
					// isCreateBaseLayer = false;
				//baseLayer = createBaseLayer(layerName,layerReload,''); //基础图层
				// }
				layerStyle = gisConfig.queryConfig[queryTime].style; //当前page的map style
				queryKey = gisConfig.queryConfig[queryTime].queryKey; //查询键名
				if( typeof(gisConfig.queryConfig[queryTime].moreInfoKey)!='undefined' ){
					moreInfoKey = gisConfig.queryConfig[queryTime].moreInfoKey;//乒乓切换/小区重选其他键名
				}else{
					moreInfoKey = '';
				}
				if( typeof(gisConfig.queryConfig[queryTime].otherKey)!='undefined' ){
					otherKey = gisConfig.queryConfig[queryTime].otherKey;//乒乓切换其他键名
				}else{
					otherKey = '';
				}
							
				//初始查询条件名称
				keyArray = gisConfig.layerSwitcherConfig; //初始图层配置参数
				filterLayers = createFilterLayer(layerName,queryKey,layerStyle,keyArray,filterLayerReload,false,''); //创建切换图层
				map.addLayers(filterLayers); //添加选择图层
			}else{
				//baseLayer = createBaseLayer(layerName,layerReload,''); //基础图层
				removeAllLayers();
				pasteTimeTag();
			}
			
			if(page == 'userNumber'){
				$('#vipUser').show();
			}else{
				$('#vipUser').hide();
			}
			if(page == 'comNum'){
				$('#complainUser').show();
			}else{
				$('#complainUser').hide();
			}
			if( layerName == sitesLayer){
				$('#siteSelect').show();
			}else{
				$('#siteSelect').hide();
			}
			changeKeyList(); //改变查询键集合
			pageTag = gisConfig.tags.pageTag; //指标名称标签
			pageUnit = gisConfig.tags.unit; //指标单位
			changeSearchTag(); //改变查询表单
			pastTypeTag(); //添加类型切换按钮
			if(thisPage == 'init'){
				addMapTools(); //添加工具栏
				addProfile(); //加载小框地图
				eventStart(); //注册监听事件
				$('#'+page).addClass('select'); //初始页面按钮选中
			}
			childMenu = gisConfig.childMenu.display; //是否显示子菜单
			showChildMenu();
			display(false);
		}
	});
}

/* 初始化map */
function createMap(){

	google_map=[new OpenLayers.Layer.XYZ(
					"jiedao",
					"/google_street/" ,
					{zoomOffset: gisConfig.initConfig.zoomOffset,'getURL':get_my_url_street} 
					),new OpenLayers.Layer.XYZ(
					"weixing",
					"/google_Satellite/" ,
					{zoomOffset: gisConfig.initConfig.zoomOffset,'getURL':get_my_url_Satellite})];
					
					map = new OpenLayers.Map({
					div: "map1",
					restrictedExtent: maxExtent,
					maxResolution: gisConfig.initConfig.maxResolution,
					numZoomLevels: gisConfig.initConfig.maxLevel,
					controls: [],
					projection: EPSGGOOGLE,
					units: "m", //单位
					layers: google_map
				});
				map.zoomToExtent(initExtent);
}

/* 加载监听事件 */
function eventStart(){
	map.events.register('click', map, function (e) {
		alertExtent();
		var filterstr = '<Filter>'+ siteFilterstr + '</Filter>';
		currentZoom = map.getZoom();
		var params = {
			REQUEST: "GetFeatureInfo",
			EXCEPTIONS: "application/vnd.ogc.se_xml",
			BBOX: map.getExtent().toBBOX(),
			SERVICE: "WMS",
			VERSION: "1.1.1",
			x: Math.round(e.xy.x),
			y: Math.round(e.xy.y),
			INFO_FORMAT: 'text/html',
			QUERY_LAYERS: layerName,
			FEATURE_COUNT: 10000,
			Layers: layerName,
			Styles: '',
			WIDTH: map.size.w,
			HEIGHT: map.size.h,
			format: 'image/png',
			srs: 'EPSG:900913',
			sortBy : queryKey+" desc", 
			propertyName : keyList+queryKey,
			queryKey: queryKey,
			page: page,
			otherKey: otherKey,
			currentZoom: currentZoom,
			queryTime: queryTime,
			filter : filterstr
		};
		var lonlat=map.getLonLatFromPixel(e.xy);
		
		//SearchReset();
		$.ajax({
			type : 'post',
			url  :  'index.php?r=GIS/gisClickPoints',
			data : params,
			beforeSend : display(true),
			success : function(data){
				data = eval('(' + data + ')');
				var allPopup = "";
				for(var tempPupup in data)
				{
					var thisPopup = data[tempPupup];
					var oneLon = thisPopup['centerlon'];
					var oneLat = thisPopup['centerlat'];
					allPopup += getPopup(thisPopup);
				}
				addmaker(oneLon,oneLat,allPopup,false);
			}
		});

		OpenLayers.Event.stop(e);
	});

	map.events.register("moveend", map ,function(){   //拖拽事件,用来实现在执行搜索后用户拖动地图触发新的搜索请求
		// removeFeature();
		if(map.getZoom() < searchLevel ){
			
			select_box.deactivate();
			$('.olControlDrawFeatureItemInactive').hide();
			if( isSwitchOrReselect() && layerName == sitesLayer ){
				layerName = gridsLayer;
				typeChange(layerName);
			}
		}else{
			if( isSwitchOrReselect() && layerName == gridsLayer ){
				layerName = sitesLayer;
				typeChange(layerName);
			}
			$('.olControlDrawFeatureItemInactive').show();
		} 
	});

	//事件，为街景模式地图和卫星地图设置不同的小区样式
	google_map[1].events.register("visibilitychanged", google_map[1] ,function(){
		if(google_map[1].visibility ){
			if(layerName == gridsLayer)
				init_style ='init_grids_RS';
			else init_style ='init_style_RS';
		}else{
			if(layerName == gridsLayer)
				init_style ='init_grids';
			else init_style ='init_style';
		}
		baseLayer.mergeNewParams({STYLES:init_style});	
	});
}
/* 是否显示加载缓冲图 */
function display(display){
	if(display)
		$('#blackDiv').show();
	else
		setTimeout(function(){$('#blackDiv').hide();}, 900);
}

/* 清空搜索搜索内容 */
function SearchReset(){
	$("#search_lac").val("输入LAC搜索");
	$("#search_cellid").val("输入CELLID搜索");
}

function addMapTools(){

 // //建立label图层
// lableLayer = new OpenLayers.Layer.Vector("lable", {
	// styleMap: new OpenLayers.StyleMap({'default':{
		// label : "${celldata}",
		// fontColor: "${favColor}",
		// fontSize: "16px",
		// fontFamily: "Courier New, monospace",
		// fontWeight: "bold",
		// labelOutlineColor: "white",
		// labelOutlineWidth: 3
	// }}),
	// displayInLayerSwitcher:false
// });
// map.addLayer(lableLayer);

//建立ppLableLayer图层
// ppLableLayer = new OpenLayers.Layer.Vector("ppLable", {
	// displayInLayerSwitcher:false
// });
// map.addLayer(ppLableLayer);

//建立ppLaunchLayer图层
ppLaunchName = '<span class="colorSmall" style="border-color:#CC0033; "></span><span class="val">乒乓切换发起小区</span>';
ppLaunchLayer = new OpenLayers.Layer.Vector( ppLaunchName, {
	styleMap: ppStyleMap
});

//建立ppReceiveLayer图层
ppReceiveName = '<span class="colorSmall" style="border-color:#000000"></span><span class="val">乒乓切换接收小区</span>';
ppReceiveLayer = new OpenLayers.Layer.Vector( ppReceiveName, {
	styleMap: ppStyleMap
});

//建立用户轨迹图层
userPathName = "<span style='border:none;background:url(images/rounds.png) no-repeat center;height:18px;width:18px'></span><span class='val'>用户轨迹</span>";
userPathLayer = new OpenLayers.Layer.Vector(userPathName, {
	 styleMap: new OpenLayers.StyleMap({'default':{
					strokeColor: "#00FF00",
					strokeOpacity: 1,
					strokeWidth: 1,
					fillColor: "#CC0033",//#FF5500/CC0033/00FF00
					fillOpacity: 1,
					pointRadius: 4,
					pointerEvents: "visiblePainted"
				}})
});

//建立用户投诉点展示图层
complainName = "<span style='border:none;background:url(images/rounds.png) no-repeat center;height:18px;width:18px'></span><span class='val'>用户投诉展示</span>";
complainLayer = new OpenLayers.Layer.Vector(complainName, {
	 styleMap: new OpenLayers.StyleMap({'default':{
					strokeColor: "#00FF00",
					strokeOpacity: 1,
					strokeWidth: 1,
					fillColor: "#CC0033",//#FF5500/CC0033/00FF00
					fillOpacity: 1,
					pointRadius: 4,
					pointerEvents: "visiblePainted"
				}})
});

//地图框选功能

boxLayer = new OpenLayers.Layer.Vector("Box layer");
select_box=new OpenLayers.Control.DrawFeature(boxLayer,
			OpenLayers.Handler.RegularPolygon, {
				title:'框选查看数据',
				handlerOptions: {
					sides: 4,
					irregular: true	
				},
				featureAdded : function(feature){
						var bounds=(feature.geometry.getBounds()).toString();
						var spletArr = bounds.split(",");
						var filterstr = '<Filter xmlns:gml=\'http://www.opengis.net/gml\'><And>'+ siteFilterstr + '<BBOX><PropertyName>geom_data</PropertyName><gml:Box srsName=\'EPSG:900913\'><gml:coordinates>'+spletArr[0]+','+spletArr[1]+' '+spletArr[2]+','+spletArr[3]+'</gml:coordinates></gml:Box></BBOX></And></Filter>';
						currentZoom = map.getZoom();
						var DataParams = 
						{
							REQUEST : 'GetFeature',
							SERVICE : 'WFS',
							VERSION : '1.0.0',
							outputFormat : 'json',
							TYPENAME : layerName,
							sortBy : queryKey+" desc", 
							filter:	filterstr,
							MAXFEATURES : 10000,
							propertyName : keyList+queryKey,
							queryKey: queryKey,
							page: page,
							otherKey: otherKey,
							currentZoom: currentZoom,
							queryTime: queryTime
						};

						$.ajax({
							type : 'post',
							url  :  'index.php?r=GIS/gisClickPoints',
							data : DataParams,
							beforeSend : function(){removeAllPopup();display(true);},
							success : function(data){
								data = eval('(' + data + ')');
								if(data.length != 0){
									// if(layerName == sitesLayer){
										var allPopup = '';
										for(var num in data ){
											var oneLon = data[num]['centerlon'];
											var oneLat = data[num]['centerlat'];
											allPopup += getPopup(data[num]);
										}
										addmaker(oneLon,oneLat,allPopup,true);
										display(false);
									// }else{
										// // lableLayer.removeAllFeatures();
										// var labelOffsetFeatureList = new Array();
										// for(var key in data){
											// var lon = data[key].centerlon;
											// var lat = data[key].centerlat;
											// var celldata = data[key][queryKey];
											// var favColor = 'black'; 
											// if( celldata != '无数据'){
												// for(var s in keyArray){
													// if(s==0 && celldata == keyArray[s].min) {
														// favColor = keyArray[s].color ;
														// break;
													// }
													// if(celldata > keyArray[s].min && celldata <= keyArray[s].max){
														// favColor =  keyArray[s].color ;
														// break;
													// }
												// }
												// celldata = Math.round(celldata);
											// }
											// var labelOffsetPoint = new OpenLayers.Geometry.Point(lon, lat);
											// labelOffsetPoint.transform( EPSGGPS, EPSGGOOGLE );
											// var labelOffsetFeature = new OpenLayers.Feature.Vector(labelOffsetPoint);
											// labelOffsetFeature.attributes = {
												// celldata: celldata,
												// favColor: favColor
											// };
											// labelOffsetFeatureList.push(labelOffsetFeature);
										// }
										// lableLayer.addFeatures(labelOffsetFeatureList);
										// // select_box.deactivate();
									// }
								}else
									alert('未查询到相关信息！');
								display(false);
							}
						});
				}
			}
		);

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
var layerSwitcher = new OpenLayers.Control.LayerSwitcher({ascending:false,'div':OpenLayers.Util.getElement('layerSwitcher'),roundedCorner: true});
map.addControl(layerSwitcher);
layerSwitcher.baseLbl.innerHTML="";
layerSwitcher.dataLbl.innerHTML="";
map.addControl(new OpenLayers.Control.Scale($('scale')));

map.addControl(panel);
//if(page!=='gridsLowRssi')
$('.olControlDrawFeatureItemInactive').hide(); //初始条件：隐藏框选功能

}

function addProfile(){
/*小框地图*/
 var mapOptions = {
            maxResolution: gisConfig.initConfig.maxResolution,
            projection: "EPSG:900913"	
        };
 var controlOptions = {
            maximized: true,
            mapOptions: OpenLayers.Util.extend(mapOptions, {
                maxResolution: gisConfig.initConfig.maxResolution,
                maxExtent: maxExtent
            }),
			layers: [google_map[0].clone()]
        };
        var overview = new OpenLayers.Control.OverviewMap(controlOptions);
        map.addControl(overview); 
}



/* 移除所有WMS图层 true 移除所有 false 保留底图 */
function removeAllLayers(){
	for(s in filterLayers){
		if(!(map.getLayersByName( filterLayers[s].name )=='')){
			map.removeLayer(filterLayers[s]);
		}
	}
}

function createBaseLayer(layer_name , loadBaseLayer , _filterstr){
	var filterstr = _filterstr;
	if( _filterstr != '' ){
		filterstr = '<Filter>'+ _filterstr +'</Filter>';
	}
	if(google_map[1].visibility ){
		if(layer_name == gridsLayer)
			init_style ='init_grids_RS';
		else init_style ='init_style_RS';
	}else{
		if(layer_name == gridsLayer)
			init_style ='init_grids';
		else init_style ='init_style';
	}
	var base_layer = new OpenLayers.Layer.WMS('all', 
		serverUrl,
		{layers: layer_name,format: 'image/png', transparent: true,filter: filterstr,STYLES:init_style},
		{displayInLayerSwitcher : false}
	); 
	if(loadBaseLayer)
		map.removeLayer(baseLayer);
	map.addLayer(base_layer);
	return base_layer;
}
		
/* 创建WMS图层 图层名称 范围查询键名 图层样式 图层配置  是否重新加载主图层 */
function createFilterLayer(layer_name,query_key,layer_style,key_array,removeOld,visibilityLayer,_filterstr){
	var filterstr = _filterstr;
	if( _filterstr != '' ){
		filterstr = '<Filter><And>'+ _filterstr;
	}
	if(removeOld)
		removeAllLayers();
	var layerArray = new Array();
	for(s in key_array){
		//最后一个条件的判断标签不同
		if(s==(key_array.length-1))
			var lastTag = 'PropertyIsLessThanOrEqualTo';
		else
			var lastTag = 'PropertyIsLessThan';
		var filterstr1='';
		if(  filterstr != '' ){
			filterstr1= filterstr+'<And><PropertyIsGreaterThanOrEqualTo><PropertyName>'+query_key+'</PropertyName><Literal>'+key_array[s].min+'</Literal></PropertyIsGreaterThanOrEqualTo><'+lastTag+'><PropertyName>'+query_key+'</PropertyName><Literal>'+key_array[s].max+'</Literal></'+lastTag+'></And></And></Filter>';
		}else{
			filterstr1='<Filter><And><PropertyIsGreaterThanOrEqualTo><PropertyName>'+query_key+'</PropertyName><Literal>'+key_array[s].min+'</Literal></PropertyIsGreaterThanOrEqualTo><'+lastTag+'><PropertyName>'+query_key+'</PropertyName><Literal>'+key_array[s].max+'</Literal></'+lastTag+'></And></Filter>';
		}
		var layerVisibility = false; //图层可见性
		if(visibilityLayer) //如果存在已选定图层
			if(in_array(key_array[s].keywords, visibilityLayer))
				layerVisibility = true;

		var layer = new OpenLayers.Layer.WMS(key_array[s].keywords, 
		serverUrl,
		{layers: layer_name,format: 'image/png', transparent:true,filter: filterstr1,STYLES: layer_style },{visibility:layerVisibility}
		);
		map.addLayer(layer);
		layerArray.push(layer);
	}
	return layerArray;
}

/* 创建popup内容 */
function getPopup(DataArray){
	var allPopup = '';
	var thisPage,queryId,nameArray,queryInfo,layerType,areaName,thisMoreInfoKey;
	thisPage = '"'+page+'"';
	thisMoreInfoKey = '"'+moreInfoKey+'"';
	if(layerName == gridsLayer){
		layerType = '"grids"';
		areaName = '"'+DataArray.address+'"';
		if( page != 'siteBusiness'){
			nameArray = {gridid:'区域编号',address:'区域地址',num_2g:'2G基站数量',num_3g:'3G基站数量',num_4g:'4G基站数量',speechTraffic_2g:'日均2G话务量(Erl)',speechTraffic_3g:'日均3G话务量(Erl)',speechTraffic_3_2g:'日均3G/2G话务量',dataTraffic_2g:'日均2G数据流量(MB)',dataTraffic_3g:'日均3G数据流量(MB)',dataTraffic_4g:'日均4G数据流量(MB)',dataTraffic_3_2g:'日均3G/2G数据流量',dataTraffic_4_3g:'日均4G/3G数据流量',dataTraffic_4_2g:'日均4G/2G数据流量',wirelessRate_2g:'日均2G无线利用率(%)',wirelessRate_3g:'日均3G无线利用率(%)',wirelessRate_4g:'日均4G无线利用率(%)',norm:'指标数据详情'};
			queryInfo  = thisPage+","+DataArray.gridid+","+areaName+","+layerType+","+thisMoreInfoKey;
		}else{
			nameArray = {gridid:'区域编号',address:'区域地址'};
			var searchTime = '"'+queryTime+'"';
			queryInfo  = thisPage+","+DataArray.gridid+","+areaName+","+layerType+","+searchTime;
		}
	}else{
		layerType = '"sites"';
		queryId = '"'+DataArray.lac+','+DataArray.cellid+'"';
		var areaName = '"'+DataArray.name+'"';
		nameArray = {name:'小区名',lac:'LAC',cellid:'CELLID',lon:'经度',lat:'纬度',angle:'角度',gridid:'唯一标识'};
		queryInfo  = thisPage+","+queryId+","+areaName+","+layerType+","+thisMoreInfoKey;
	}

	DataArray.gridid = "<p class='gridid_val'>"+DataArray.gridid+"</p>";
	
	if(page=='comNum')
		nameArray.com_problem = '总投诉数量('+tagName+')';

	if(page != 'siteBusiness'){
		nameArray[queryKey] = pageTag+pageUnit+tagName;
		if( page == 'pingPongSwitch' && DataArray[queryKey] != '无数据'&&layerName != gridsLayer){
			var ppOtherValue = ['发起切换的次数','发起时的平均信号值','接收切换的次数','接收时的平均信号值'];
			var  ppOtherKey = otherKey.split(',');
			for( var i in ppOtherKey ){
				var ppKey = ppOtherKey[i];
				nameArray[ppKey] = ppOtherValue[i]+'('+tagName+')';
			}
			
			for(var tempKey in DataArray){
				if( DataArray[tempKey] == 0){
					DataArray[tempKey] = "-";
				}
			}
		}
		if(!!DataArray[queryKey] && DataArray[queryKey] != '无数据'){
			DataArray.getmore = "<span class='sp1' onclick='getMoreInfo("+queryInfo+");'><a href='#' style=''>详细信息</a></span>";
			nameArray.getmore = '更多信息';
		}
	}else{
		if( typeof(DataArray.time) != 'undefined'){
			nameArray['num_2g'] = '2G基站数量';
			nameArray['num_3g'] = '3G基站数量';
			nameArray['num_4g'] = '4G基站数量';
			nameArray['time'] = '时间';
			nameArray['speechTraffic_2g'] = '日均2G话务量(Erl)';
			nameArray['speechTraffic_3g'] = '日均3G话务量(Erl)';
			nameArray['speechTraffic_3_2g'] = '日均3G/2G话务量';
			nameArray['dataTraffic_2g'] = '日均2G数据流量(MB)';
			nameArray['dataTraffic_3g'] = '日均3G数据流量(MB)';
			nameArray['dataTraffic_4g'] = '日均4G数据流量(MB)';
			nameArray['dataTraffic_3_2g'] = '日均3G/2G数据流量';
			nameArray['dataTraffic_4_3g'] = '日均4G/3G数据流量';
			nameArray['dataTraffic_4_2g'] = '日均4G/2G数据流量';
			nameArray['wirelessRate_2g'] = '日均2G无线利用率(%)';
			nameArray['wirelessRate_3g'] = '日均3G无线利用率(%)';
			nameArray['wirelessRate_4g'] = '日均4G无线利用率(%)';
			DataArray['time'] = DataArray['time']+'（区域业务统计日均数据）';
			DataArray.getmore = "<span class='sp1' onclick='getMoreInfo("+queryInfo+");'><a style=''>详细信息</a></span>";
			nameArray.getmore = '更多信息';
			for(var tempKey in DataArray){
				if( !DataArray[tempKey] ){
					DataArray[tempKey] = "-";
				}
			}
		}else{
			nameArray['time'] = '时间';
			DataArray['time'] = queryTime + '（无数据）';
		}
	}
	
	for(var tempKey in nameArray)
	{
		if( typeof(DataArray[tempKey]) == "undefined" || DataArray[tempKey] == null)
			DataArray[tempKey] = "无";
		allPopup +="<li><span class='sp1'>"+nameArray[tempKey]+":</span><span>"+DataArray[tempKey]+"</span></li>";
	}
	return allPopup = "<ul>"+allPopup+"</ul>";
}


function addmaker(lon_p,lat_p,allPopup,setCenter){
	if(!!lon_p&&!!lat_p){
		var lon=parseFloat(lon_p);
		var lat=parseFloat(lat_p);
		removeAllPopup();
		var size = new OpenLayers.Size(140,170);
		var ss_o= new OpenLayers.Geometry.Point(lon,lat);
		ss_o.transform(EPSGGPS,EPSGGOOGLE);
		var makerpoint=new OpenLayers.LonLat(ss_o.x, ss_o.y);
 		var popup = new OpenLayers.Popup("chicken",
		makerpoint,
		null,
		"<b>区域信息</b><div class='popupDiv'>"+allPopup+"</div>",
		true);
		popup.autoSize=false;
		popup.opacity = 0.9;
		if(setCenter)
		map.setCenter(makerpoint);
		map.addPopup(popup);
		popup.panIntoView(); //将地图移动至弹框全部可见的位置
		$('.olPopup').css("z-index","999999");
		display(false);
	}
	else{
		display(false);
		alert('没有选择有效区域！');
	}
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
function checkSearchInfo(infoId,initText){
	var checkStr;
	var numError = '查询条件必须为正整数！';
	var inputError = '请输入正确的搜索信息！';
	
	if($.trim($('#'+infoId).val()) != initText){
		checkStr = $.trim($('#'+infoId).val());
		if(!forcheck(checkStr)){
			alert(numError);
			return false;
		}
	}else{
		alert(inputError);
		return false;
	}
	return checkStr;
}

/* 搜索信息 */
function search_info(){
	removeAllPopup();
	currentZoom = map.getZoom();
	var searchError = '未查询符合此搜索条件的信息！';
	var fileter;
	if(layerName == gridsLayer){
		var text_str_grid = checkSearchInfo('search_gridid','请输入区域编号进行搜索');
		if(!text_str_grid){
			return false;
		}
		fileter='<Filter>';
		fileter+='<PropertyIsEqualTo>';
		fileter+='<PropertyName>gridid</PropertyName>';
		fileter+='<Literal>'+text_str_grid+'</Literal>';
		fileter+='</PropertyIsEqualTo>';
		fileter+='</Filter>';
	}else{
		var text_str_lac = checkSearchInfo('search_lac','请输入LAC搜索');
	    var text_str_cellid = checkSearchInfo('search_cellid','请输入CELLID搜索');
		if(!text_str_lac || !text_str_cellid){
			return false;
		}
		fileter='<Filter><And>'+ siteFilterstr;
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
		fileter+='</And></Filter>';
	}
	$.ajax({
		type : 'post',
		url :	'index.php?r=GIS/gisClickPoints',
		data : {
			REQUEST: 'GetFeature',
			SERVICE: 'WFS',
			VERSION: '1.0.0',
			outputFormat: 'json',
			TYPENAME: layerName,
			MAXFEATURES: 10000,
			sortBy : queryKey+" desc",
			propertyName : keyList+queryKey,
			filter: fileter,
			queryKey: queryKey,
			page: page,
			otherKey: otherKey,
			currentZoom: currentZoom,
			queryTime: queryTime
		},
		beforeSend : display(true),
		success : function(searchData){
			removePpShowFeature();
			removePpShowLayer();
			searchData = eval('(' + searchData + ')');
			if( searchData.length != 0 ){
				var oneLon = searchData[0]['centerlon'];
				var oneLat = searchData[0]['centerlat'];
				allPopup = getPopup(searchData[0]);
				addmaker(oneLon,oneLat,allPopup,true);
				if( searchData[0][queryKey]!='无数据'&&page =='pingPongSwitch' && layerName == sitesLayer){
					$.ajax({
						type : 'post',
						url : 'index.php?r=GIS/GetPpShowInfo',
						data: {
							lac : searchData[0]['lac'],
							cellid : searchData[0]['cellid']
						},
						beforeSend : function(){
							display(true);
						},
						success : function( ppInfo ){
							ppInfo = eval('(' + ppInfo + ')');
							// var selfStyle = getFeatureStyle(searchData[0][queryKey]);
							// var islaunch = false; //用户表示该基站是否发起乒乓切换
							if( ppInfo.launchReceive != '无数据'){
								// islaunch = true;
								map.addLayer(ppReceiveLayer);
								var launchReceiveObj = ppInfo.launchReceive;
								for( var eachObj in launchReceiveObj ){
									// if(eachObj == 0){
										// drawVectorFeature(launchReceiveObj[eachObj], selfStyle,ppLableLayer,2,null);
									// }else{
									drawVectorFeature(launchReceiveObj[eachObj],null,ppReceiveLayer,2,'black');
										//生成从扇形中心指向扇形中心的线段
										// var lineData = launchReceiveObj[0].centerLon+" "+launchReceiveObj[0].centerLat+","+launchReceiveObj[eachObj].centerLon+" "+launchReceiveObj[eachObj].centerLat;
										// drawVectorFeature(lineData,line_style_black,1);
									// }
								}
							}
							if( ppInfo.receiveLaunch != '无数据'){
								map.addLayer(ppLaunchLayer);
								var receiveLaunchObj = ppInfo.receiveLaunch;
								for( var eachObj in receiveLaunchObj ){
									// if( eachObj == 0 ){
										// if( islaunch == false ){
											// drawVectorFeature(receiveLaunchObj[eachObj], selfStyle,ppLableLayer,2,null);
										// }
									// }else{
									drawVectorFeature(receiveLaunchObj[eachObj], null,ppLaunchLayer,2,'red');
										//生成从扇形中心指向扇形中心的线段
										// var lineData = launchReceiveObj[0].centerLon+" "+launchReceiveObj[0].centerLat+","+launchReceiveObj[eachObj].centerLon+" "+launchReceiveObj[eachObj].centerLat;
										// drawVectorFeature(lineData,line_style_red,1);
									// }
								}
							}
							display(false);
						}
					});
				}
			}else
				alert(searchError);
			display(false);
		}
	});
}

//函数:矢量图形（线、面）生成
function drawVectorFeature( featureData, featureStyle,featureLayer,type,featureColor ){
	var newPoint;
	var pointList = [];
	var newRing, newPolygon, newFeature;
	
	var thisPointData = featureData.pointData;
	var thisPoint = thisPointData.split(",");
	for( var temp in thisPoint ){
		var point = thisPoint[temp].split(" ");
		newPoint = new OpenLayers.Geometry.Point(point[0],point[1]);
		newPoint.transform(EPSGGPS,EPSGGOOGLE);
		pointList.push(newPoint);
	}
	
	//lableOffset的确定
	var lableOffset = lableXyOffset(featureData.angle);
	
	//线的生成
	if( type == 1){
		newRing = new OpenLayers.Geometry.LineString(pointList);
		newFeature = new OpenLayers.Feature.Vector( newRing,null,featureStyle);
		featureLayer.addFeatures(newFeature);
	}
	//多边形的生成
	if( type == 2 ){    
		newRing = new OpenLayers.Geometry.LinearRing(pointList);
		newPolygon = new OpenLayers.Geometry.Polygon([newRing]);
		newFeature = new OpenLayers.Feature.Vector( newPolygon);
		newFeature.attributes = {
			lac: featureData.lac,
			cellid: featureData.cellid,
			favorColor: featureColor,
			xOffset: lableOffset[0],
			yOffset: lableOffset[1]
		};
		newFeature.style = featureStyle;
		featureLayer.addFeatures(newFeature);
	}
}

//求标注偏差
function lableXyOffset(angle){
	var _angle = parseInt(angle);
	var lableOffset = [], lableXOffset=0, lableYOffset=0;
	if( _angle>=0&&_angle<15){
		lableXOffset = 0;
		lableYOffset=20;
	}else if(_angle>=15&&_angle<45){
		lableXOffset = 15;
		lableYOffset=25;
	}else if(_angle>=45&&_angle<75){
		lableXOffset = 20;
		lableYOffset=15;
	}else if(_angle>=75&&_angle<105){
		lableXOffset = 25;
		lableYOffset=0;
	}else if(_angle>=105&&_angle<135){
		lableXOffset = 20;
		lableYOffset=-25;
	}else if(_angle>=135&&_angle<165){
		lableXOffset = 15;
		lableYOffset=-15;
	}else if(_angle>=165&&_angle<195){
		lableXOffset = 0;
		lableYOffset=-20;
	}else if(_angle>=195&&_angle<225){
		lableXOffset = -15;
		lableYOffset=-20;
	}else if(_angle>=225&&_angle<255){
		lableXOffset = -20;
		lableYOffset=-25;
	}else if(_angle>=255&&_angle<285){
		lableXOffset = -25;
		lableYOffset=0;
	}else if(_angle>=285&&_angle<315){
		lableXOffset = -20;
		lableYOffset=15;
	}else if(_angle>=315&&_angle<345){
		lableXOffset = -15;
		lableYOffset=20;
	}else if(_angle>=345&&_angle<=360){
		lableXOffset = 0;
		lableYOffset=25;
	}
	lableOffset.push(lableXOffset);
	lableOffset.push(lableYOffset);
	return lableOffset;
}

//根据属性信息返回要素的style	
function getFeatureStyle(data){
	var style;
	for(var s in keyArray){
		if(s==0 && data == keyArray[s].min) {
			style = style_model( keyArray[s].color );
			break;
		}
		if(data > keyArray[s].min && data <= keyArray[s].max){
			style = style_model( keyArray[s].color ) ;
			break;
		}
	}
	return style;
}

/* 获取当前视图的Extent */
function alertExtent(){
	var thisExtent = map.getExtent();
	thisExtent.transform(EPSGGOOGLE,EPSGGPS); 
	alert(thisExtent.toString());
}

function getMapLayers(){
	var googleLayer = map.getLayersByName("街景模式");
	alert(googleLayer.toString());
	//"卫星地图",
		
}

/* 返回当前的图层类型 */
function isSiteLayer(){
	var layerType = false;
	if(layerName == sitesLayer)
		layerType = true;
	return layerType;
}
	

/* 查询键列表更改 */
function changeKeyList()
{
	if(layerName == gridsLayer)
		keyList = gridsKeyList;
	else
		keyList = sitesKeyList;
}

/* 查询表单更改 */
function changeSearchTag(){
	if(this.layerName == this.sitesLayer ){
		$('#searchGrids').css('display','none');
		$('#searchSites').css('display','block');
	}else{
		$('#searchSites').css('display','none');
		$('#searchGrids').css('display','block');
	}
}

/* 显示子菜单 */

function showChildMenu(){
	if(childMenu){
		var divId = gisConfig.childMenu.divId;
		$('#'+divId).show().siblings('div').hide();
		$('#childMenu').css('display','block');
		$('#'+page).addClass('select').siblings('a').removeClass('select');
	}else
		$('#childMenu').css('display','none');
}

/* 查询时间切换 */
function timeChange(query_time){
	var visibilityLayers = new Array();
	for( var tempLayer in filterLayers )
		if(filterLayers[tempLayer].visibility)
			visibilityLayers.push(filterLayers[tempLayer].name);
		
	display(true);
	removeAllPopup();
	queryTime = query_time;
	if( page != 'siteBusiness'){
		layerStyle = gisConfig.queryConfig[queryTime].style;
		queryKey = gisConfig.queryConfig[queryTime].queryKey;
		if( typeof(gisConfig.queryConfig[queryTime].moreInfoKey)!='undefined' ){
			moreInfoKey = gisConfig.queryConfig[queryTime].moreInfoKey;//乒乓切换/小区重选其他键名
		}else{
			moreInfoKey = '';
		}
		if( typeof(gisConfig.queryConfig[queryTime].otherKey)!='undefined' ){
			otherKey = gisConfig.queryConfig[queryTime].otherKey;//乒乓切换其他键名
		}else{
			otherKey = '';
		}
		removeAllLayers();
		filterLayers = createFilterLayer(layerName,queryKey,layerStyle,keyArray,false,visibilityLayers,'');
		tagName = $("#"+queryTime).text();
	}
	var tag1 = $("#"+queryTime).text();
	$("#select_date").val(tag1);
	display(false);
}

/* 栅格/基站切换 */
function typeChange(type){
	
	this.display(true);
	removeAllPopup();
	// removeFeature();
	removePpShowFeature();
	removePpShowLayer();
	this.layerName = type;
	this.removeAllLayers();
	visible_2g = true;
	visible_3g = true;
	visible_4g = true;
	$('#2GSite').prop('checked',true); 
	$('#3GSite').prop('checked',true); 
	$('#4GSite').prop('checked',true); 
	siteFilterstr = '';
	// if(isbaselayer){
		// isCreateBaseLayer = true;
	this.baseLayer = createBaseLayer(type,true,'');
	// }
	this.filterLayers = createFilterLayer(type,queryKey,layerStyle,keyArray,false,false,'');
	if( layerName == sitesLayer){
		$('#siteSelect').show();
	}else{
		$('#siteSelect').hide();
	}
	changeKeyList();
	changeSearchTag();
	this.display(false);
}

/* 指标页面切换 */
function pageChange(changed){
	removeAllPopup();
	// removeFeature();
	removePpShowFeature();
	removePpShowLayer();
	removeUserPathFeature();
	removeUserPathLayer();
	removeComplainFeature();
	removeComplainLayer();
	var isSwitchChanged = false;
	var isComplain = false;
	var isSiteBusiness = false;
	if(changed=='T2GSwitch'){
		isSwitchChanged = true;
	}
	if(page == 'complain' || changed=='complain'){
		isComplain = true;
	}
	if(page == 'siteBusiness' || changed=='siteBusiness'){
		isSiteBusiness = true;
	}
	// if( isComplain || isSwitchOrReselect() || isSwitchChanged || isSiteBusiness ){
		// isCreateBaseLayer = true;
	// }
	if(page == 'siteBusiness'){
		filterLayerReload = false;
	}else{
		filterLayerReload = true;
	}
	page = changed;
	visible_2g = true;
	visible_3g = true;
	visible_4g = true;
	$('#2GSite').prop('checked',true); 
	$('#3GSite').prop('checked',true); 
	$('#4GSite').prop('checked',true); 
	siteFilterstr = '';
	getMapConfig(page);
}

/* 移除框选图层 */
// function removeFeature(){
	// if( typeof(lableLayer) != 'undefined' ){
		// lableLayer.removeAllFeatures();
	// }
// }
//移除乒乓展示相关图层
function removePpShowFeature(){
	if( typeof(ppLaunchLayer) != 'undefined' ){
		ppLaunchLayer.removeAllFeatures();
	}
	if( typeof(ppReceiveLayer) != 'undefined' ){
		ppReceiveLayer.removeAllFeatures();
	}
	// if( typeof(ppLableLayer) != 'undefined' ){
		// ppLableLayer.removeAllFeatures();
	// }
}
//移除乒乓切换相关图层
function removePpShowLayer(){
	if(!(map.getLayersByName(ppLaunchName)=='')){
		map.removeLayer(ppLaunchLayer);
	}
	if(!(map.getLayersByName(ppReceiveName)=='')){
		map.removeLayer(ppReceiveLayer);
	}
}


/* 添加图层类型切换按钮 sitesName  gridsName  isDisplay为是否显示该按钮*/
function pastTypeTag(){
	if(typeTagDisplay){
		var sitesName = '"'+sitesLayer+'"';
		var gridsName = '"'+gridsLayer+'"';
		var tagContents = "<span id='sites' class='p1 select' onclick='javascript:typeChange("+sitesName+");' >基站</span><span id='grids' class='p1 ' onclick='javascript:typeChange("+gridsName+");' >栅格</span>";
		$(".gis_change").css('display','block');
		$(".gis_change").html(tagContents);
		$('.table_search ul li.gis_change span').click(function(){
			$(this).addClass('select').siblings('span').removeClass('select')
		})
	}else{
		$(".gis_change").css('display','none');
	}
}

/* 添加日期选择按钮  queryConfig 为日期配置*/
function pasteTimeTag(queryConfig){
	if( page == 'siteBusiness' ){
		var today = new Date();
		var monthKey = today.getMonth();
		var year = today.getFullYear();
		var lastYear = year - 1;
		var months = new Array();
		var thisMonth, lastMonth , beforeLastMonth, lastMonthKey , beforeLastMonthKey;
		if( monthKey == 0 ){
			thisMonth = lastYear+'-'+'12';
			lastMonth = lastYear+'-'+'11';
			beforeLastMonth = lastYear+'-'+'10';
		}else if ( monthKey == 1 ){
			thisMonth = year+'-'+'01';
			lastMonth = lastYear+'-'+'12';
			beforeLastMonth = lastYear+'-'+'11';
		}else if ( monthKey == 2 ){
			thisMonth = year+'-'+'02';
			lastMonth = year+'-'+'01';
			beforeLastMonth = lastYear+'-'+'12';
		}else {
			lastMonthKey = monthKey - 1;
			beforeLastMonthKey = monthKey - 2;
			thisMonth = year+'-'+'0'+ monthKey;
			lastMonth = year+'-'+'0'+ lastMonthKey;
			beforeLastMonth = year+'-'+'0'+ beforeLastMonthKey;
		}
		months[thisMonth] = thisMonth;
		months[lastMonth] = lastMonth;
		months[beforeLastMonth] = beforeLastMonth;
		var tagContents = '';
		for( var key in months)
		{
			var tag = months[key];
			key = '"'+key+'"';
			tagContents += "<span id="+key+" onclick='javascript:timeChange("+key+");' >"+tag+"</span>";
		}
		$(".show_div").html(tagContents);
		$('#select_date').val(months[thisMonth]); //显示初始日期	
		queryTime = months[thisMonth];		
	}else{
		if( tagName == '昨日数据' ){
			queryTime = 'one';
		}else if( tagName == '七天数据' ){
			queryTime = 'seven';
		}else if( tagName == '十五天数据' ){
			queryTime = 'fifteen';
		}else if( tagName == '三十天数据' ){
			queryTime = 'thirty';
		}
		var tagContents = '';
		for( var key in queryConfig)
		{
			var tag = queryConfig[key].tag;
			key = '"'+key+'"';
			tagContents += "<span id="+key+" onclick='javascript:timeChange("+key+");' >"+tag+"</span>";
		}
		$(".show_div").html(tagContents);
		$('#select_date').val(tagName); //显示初始日期
	}
}

/**
 * @name 定时器
 * @param diffTime 执行间隔时间(ms)
 * @param func 待执行方法名
 * @param times 执行次数, 0为停止执行
 **/
function timer(diffTime, func, times){
	var count = 0; //计数归零
	timer = window.setInterval(function(){
		eval(func); //执行指定的方法
		alert(count++); //弹出执行次数
		if (times == 0 || count > times) //如果count值达到规定次数，清空定时器
			window.clearInterval(timer); 
	},diffTime); //每隔diffTime ms 执行 func方法
}

/* DEBUG提示*/
function alertDebug(){
	alert('功能开发中...');
	//pageChange('init');
}

function isSwitchOrReselect(){
	if( page == 'T2GSwitch' || page == 'allReselect' || page == 'pingPongSwitch'){
		return true;
	}else{
		return false;
	}
}

/**
 * @name 定时器
 * @param diffTime 执行间隔时间(ms)
 * @param func 待执行方法名
 * @param times 执行次数, 0为停止执行
 **/
function getAreaDataList(thisa){
	// if(page == 'siteBusiness'){
		// alert("本模块暂不支持此功能！");
		// return false;
	// }
	var thisAreaId = $(thisa).parent().parent().parent().find(".gridid_val").text();
	var thisParam = $(thisa).attr('class').split(' ');
	var srcval = '';
	// alert(thisAreaId);
	// alert(thisParam[1]);

	//this.layerName

	//var areaName = encodeURIComponent(areaName);
	//var srcval = 'index.php?r=GIS/DataList&layer='+this.layerName+'&queryid='+thisAreaId+'&param='+thisParam+'&querytime='+queryTime;
	if(thisParam[1] == 'com_problem')
		srcval = 'index.php?r=ComplainProblem/ProblemList&layer='+this.layerName+'&queryid='+thisAreaId+'&param='+thisParam[1]+'&querytime='+queryTime+'&gis=1';
	else if(thisParam[1] == 'num_2g')
		srcval = 'index.php?r=GIS/DataList&layer='+this.layerName+'&queryid='+thisAreaId+'&param='+thisParam[1]+'&querytime='+queryTime+'&gis=1&md=0';
	else if(thisParam[1] == 'num_3g')
		srcval = 'index.php?r=GIS/DataList&layer='+this.layerName+'&queryid='+thisAreaId+'&param='+thisParam[1]+'&querytime='+queryTime+'&gis=1&md=1';
	else if(thisParam[1] == 'num_4g')
		srcval = 'index.php?r=GIS/DataList&layer='+this.layerName+'&queryid='+thisAreaId+'&param='+thisParam[1]+'&querytime='+queryTime+'&gis=1&md=4';
	else
		srcval = 'index.php?r=GIS/DataList&layer='+this.layerName+'&queryid='+thisAreaId+'&param='+thisParam[1]+'&querytime='+queryTime+'&gis=1';
	/*$('#gisMoreInfo').dialog( 'open' );
	$('#gisTableFrame').attr('src',srcval);*/
	//alert(srcval);

	window.open(srcval, '_blank', 'height=488, width=1080, toolbar=no, menubar=no, scrollbars=yes, resizable=yes, location=no, status=no, titlebar=yes, z-look=yes');
}

/*导出表格模块*/
$(document).ready(function(){

	$('.load_out').click(function(){
		$(".load_out").attr("onclick","");
		$("#img_box").show();
		var outputUrl = 'index.php?r=GIS/gridsGISOutput';	
		var j=0; 
		var eptime = setInterval(function(){
			var t = j%100;
			var s = Math.floor(j/100);
			var g = "用时:"+s+"."+t+"秒";
			$("#img_box").html(g);
			j++;	
		},10);
		$.ajax({
			type: 'post',
			url: encodeURI(outputUrl),
			data: {
				layerName: layerName,
				pageName: page,
				pageTag: pageTag,
				pageUnit: pageUnit,
				queryTime:queryTime
			},
			success :function(data){
				//alert(data);
				$("#adminshow").html(data);
				clearInterval(eptime);
				var timeer = setInterval(function(){ 
					$("#img_box").fadeOut();
					clearInterval(timeer);
					$(".load_out").attr("onclick","exprot()");
				},3000);
			}
		});
	});
});

function searchVipUser(){
	var imsi = $("#inputImsi").val();
	var imei= $("#inputImei").val();
	var time = $("#selectTime").val();
	
	$.ajax({
		type : 'post',
		url : 'index.php?r=GIS/searchVipUser',
		data : {
			imsi : imsi,
			imei : imei,
			time : time
		},
		beforeSend : function(){
			display(true);
		},
		success : function(searchData){
			removeUserPathFeature();
			removeUserPathLayer();
			searchData = eval('(' + searchData + ')');
			// alert(searchData);
			if( typeof(searchData.error) != 'undefined' ){
				alert(searchData.error);
			}else{
				map.addLayer(userPathLayer);
				userPathLayer.setVisibility(true);
				for( var i in  searchData ){
					var thisData = searchData[i];
					drawUserPath(thisData, userPathLayer);
					if(i == 0){
						var labelOffsetPoint = new OpenLayers.Geometry.Point(thisData['lng'], thisData['lat']);
						labelOffsetPoint.transform(EPSGGPS,EPSGGOOGLE);
						var makerpoint=new OpenLayers.LonLat(labelOffsetPoint.x, labelOffsetPoint.y);
						map.setCenter(makerpoint);
					}
				}
				map.zoomTo(5);
				$('#gisVipUser').dialog("close");
			}
			
			display(false);
		}
	});
}

//描绘用户轨迹
function drawUserPath( pathData, pathLayer ){
	var lon = pathData['lng'];
	var lat = pathData['lat'];
	var cell_name = pathData['cell_name'];
	var lac = pathData['lac'];
	var cellid = pathData['cellId'];
	var time = pathData['startDateTime'];
	var address = pathData['address'];
	
	var labelPoint = new OpenLayers.Geometry.Point(lon, lat);
	labelPoint.transform(EPSGGPS,EPSGGOOGLE);
	var labelFeature = new OpenLayers.Feature.Vector(labelPoint);
	labelFeature.attributes = {
		name: cell_name,
		lac: lac,
		cellid: cellid,
		time: time,
		address: address,
		lon: lon,
		lat: lat
	};
	// labelOffsetFeatureList.push(labelOffsetFeature);
	pathLayer.addFeatures(labelFeature);
	
	var options = {
		hover: true,
		onSelect: addPathMarker,
		onUnselect: removeAllPopup
	};
	var select = new OpenLayers.Control.SelectFeature(pathLayer, options);
	map.addControl(select);
	select.activate();
}

//移除用户轨迹图层
function removeUserPathLayer(){
	if(!(map.getLayersByName(userPathName)=='')){
		map.removeLayer(userPathLayer);
	}
}
//移除用户轨迹要素
function removeUserPathFeature(){
	if( typeof(userPathLayer) != 'undefined' ){
		userPathLayer.removeAllFeatures();
	}
}
//为轨迹信息添加Marker
function addPathMarker(feature) {
	var name = feature.attributes.name;
	var lac = parseFloat(feature.attributes.lac);
	var cellid = parseFloat(feature.attributes.cellid);
	var time = feature.attributes.time;
	var address = feature.attributes.address;
	var lon = parseFloat(feature.attributes.lon);
	var lat = parseFloat(feature.attributes.lat);
	
	var labelOffsetPoint = new OpenLayers.Geometry.Point(lon, lat);
	labelOffsetPoint.transform(EPSGGPS,EPSGGOOGLE);
	var makerpoint=new OpenLayers.LonLat(labelOffsetPoint.x, labelOffsetPoint.y);
	var popup = new OpenLayers.Popup("chicken",
	makerpoint,
	null,
	"<b>用户轨迹信息</b><div class='popupDiv'><ul><li><span class='sp1'>时间:</span><span>"+time+"</span></li><li ><span class='sp1'>地点:</span><span>"+address+"</span></li><li><span class='sp1'>LAC:</span><span>"+lac+"</span></li><li><span class='sp1'>CELLID:</span><span>"+cellid+"</span></li><li><span class='sp1'>小区名:</span><span>"+name+"</span></li></ul></div>");
	popup.autoSize=false;
	map.addPopup(popup);
	popup.panIntoView(); //将地图移动至弹框全部可见的位置
	$('.olPopup').css("z-index","999999");
	$('.olPopup').css("width","265px");
}

//搜索投诉点信息
function searchComplainUser(){
	var startDateTime = $("#start").val();
	var endDateTime = $("#end").val();
	var _diffDay = diffDay(startDateTime,endDateTime);
	if( _diffDay == -2 ){
		alert("开始时间不能大于结束时间，请重新选择！");
	}else if( _diffDay > 30 ){
		alert("所选择时间相差天数不能大于30天，请重新选择！");
	}else{
	  $.ajax({
		type : 'post',
		url : 'index.php?r=GIS/searchComplainUser',
		data : {
			startDateTime : startDateTime,
			endDateTime : endDateTime
		},
		beforeSend : function(){
			display(true);
		},
		success : function(searchData){
			removeAllPopup();
			removeComplainFeature();
			removeComplainLayer();
			searchData = eval('(' + searchData + ')');
			if(searchData.length != 0){
				map.addLayer(complainLayer);
				complainLayer.setVisibility(true);
				var i = 0;
				for( var temp in  searchData ){
					var lng_lat = temp.split(',');
					var thisData = searchData[temp];
					drawComplain(thisData, complainLayer);
					if(i == 0){
						var labelOffsetPoint = new OpenLayers.Geometry.Point(lng_lat[0], lng_lat[1]);
						labelOffsetPoint.transform(EPSGGPS,EPSGGOOGLE);
						var makerpoint=new OpenLayers.LonLat(labelOffsetPoint.x, labelOffsetPoint.y);
						map.setCenter(makerpoint);
						i=1;
					}
				}
				map.zoomTo(5);
				$('#gisComplain').dialog("close");
			}else{
				alert("未找到该时间段内的投诉信息！");
			}
			display(false);
		}
	  });
	}
	
}
//描绘投诉点
function drawComplain( thisData, complainLayer ){
	var lon = thisData[0]['longitude'];
	var lat = thisData[0]['latitude'];
	
	var labelPoint = new OpenLayers.Geometry.Point(lon, lat);
	labelPoint.transform(EPSGGPS,EPSGGOOGLE);
	var labelFeature = new OpenLayers.Feature.Vector(labelPoint);
	labelFeature.attributes = {
		popInfo: thisData
	};
	complainLayer.addFeatures(labelFeature);
	var options = {
		hover: true,
		onSelect: addComplainMarker
	};
	var select = new OpenLayers.Control.SelectFeature(complainLayer, options);
	map.addControl(select);
	select.activate();
}

//为投诉点信息添加Marker
function addComplainMarker(feature){
	removeAllPopup();
	var data = feature.attributes.popInfo;
	var lon = data[0].longitude;
	var lat = data[0].latitude;
	var popupInfo = '';
	for( var i in data){
		if( data[i].serviceType == '数据业务' ){
			popupInfo += "<ul><li><span class='sp1'>投诉时间:</span><span>"+data[i].complain_time+"</span></li><li><span class='sp1'>LAC:</span><span>"+data[i].lac+"</span></li><li><span class='sp1'>CELLID:</span><span>"+data[i].cellId+"</span></li><li><span class='sp1'>本机号码:</span><span>"+data[i].telephone+"</span></li><li><span class='sp1'>业务类型:</span><span>"+data[i].serviceType+"</span></li><li><span class='sp1'>投诉问题:</span><span>"+data[i].problem+"</span></li><li><span class='sp1'>投诉状态:</span><span>"+data[i].status+"</span></li><li><span class='sp1'>业务开始时间:</span><span>"+data[i].startTime+"</span></li><li><span class='sp1'>业务结束时间:</span><span>"+data[i].stopTime+"</span></li></ul>";
		}else{
			popupInfo += "<ul><li><span class='sp1'>投诉时间:</span><span>"+data[i].complain_time+"</span></li><li><span class='sp1'>LAC:</span><span>"+data[i].lac+"</span></li><li><span class='sp1'>CELLID:</span><span>"+data[i].cellId+"</span></li><li><span class='sp1'>本机号码:</span><span>"+data[i].telephone+"</span></li><li><span class='sp1'>业务类型:</span><span>"+data[i].serviceType+"</span></li><li><span class='sp1'>投诉问题:</span><span>"+data[i].problem+"</span></li><li><span class='sp1'>投诉状态:</span><span>"+data[i].status+"</span></li><li><span class='sp1'>业务对方号码:</span><span>"+data[i].toTelephone+"</span></li><li><span class='sp1'>业务开始时间:</span><span>"+data[i].startTime+"</span></li><li><span class='sp1'>业务结束时间:</span><span>"+data[i].stopTime+"</span></li></ul>";
		}
	}
	
	var labelOffsetPoint = new OpenLayers.Geometry.Point(lon, lat);
	labelOffsetPoint.transform(EPSGGPS,EPSGGOOGLE);
	var makerpoint=new OpenLayers.LonLat(labelOffsetPoint.x, labelOffsetPoint.y);
	
	var popup = new OpenLayers.Popup("chicken",
	makerpoint,
	null,
	"<b>用户投诉信息</b><div class='popupDiv'>"+popupInfo+"</div>",true);
	popup.autoSize=false;
	map.addPopup(popup);
	popup.panIntoView();
	$('.olPopup').css("z-index","999999");
}
//移除投诉信息图层
function removeComplainLayer(){
	if(!(map.getLayersByName(complainName)=='')){
		map.removeLayer(complainLayer);
	}
}
//移除用户投诉要素
function removeComplainFeature(){
	if( typeof(complainLayer) != 'undefined' ){
		complainLayer.removeAllFeatures();
	}
}

//求两日期之间相差的天数
function diffDay(startDateTime,endDateTime){
	if( startDateTime > endDateTime ){
		return -2;
	}
	var startTime = (new Date(startDateTime)).getTime();
	var endTime = (new Date(endDateTime)).getTime();
	var _diffDay = parseInt(Math.abs(startTime - endTime ) / 1000 / 60 / 60 /24);
	return _diffDay;
}

function in_array(search,array){
    for(var i in array){
        if(array[i]==search){
            return true;
        }
    }
    return false;
}

function siteReload(){
	// alert(document.getElementById("2GSite").checked);
	// alert(document.getElementById("3GSite").checked);
	// alert(document.getElementById("4GSite").checked);
	visible_2g = false;
	visible_3g = false;
	visible_4g = false;
	siteFilterstr = '';
	if(document.getElementById("2GSite").checked){
		visible_2g = true;
		siteFilterstr += '<PropertyIsEqualTo><PropertyName>target</PropertyName><Literal>0</Literal></PropertyIsEqualTo><PropertyIsEqualTo><PropertyName>target</PropertyName><Literal>2</Literal></PropertyIsEqualTo>';
	}
	if(document.getElementById("3GSite").checked){
		visible_3g=true;
		siteFilterstr += '<PropertyIsEqualTo><PropertyName>target</PropertyName><Literal>1</Literal></PropertyIsEqualTo>';
	}
	if(document.getElementById("4GSite").checked){
		visible_4g=true;
		siteFilterstr += '<PropertyIsEqualTo><PropertyName>target</PropertyName><Literal>4</Literal></PropertyIsEqualTo>';
	}
	if( siteFilterstr!='' ){
		siteFilterstr = '<Or>'+ siteFilterstr + '</Or>';
		baseLayer = createBaseLayer(sitesLayer,true,siteFilterstr);
		var visibilityLayers = new Array();
		for( var tempLayer in filterLayers )
			if(filterLayers[tempLayer].visibility)
				visibilityLayers.push(filterLayers[tempLayer].name);
		filterLayers = createFilterLayer(layerName,queryKey,layerStyle,keyArray,true,visibilityLayers,siteFilterstr);
		$('#site_Select').dialog("close");
	}else{
		alert("请至少选择一种类型基站显示");
	}
}