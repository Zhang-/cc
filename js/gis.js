 var visible_2g = true,visible_3g=true,visible_4g=true;
 //地图使用帮助弹出框
$(function (){
    $('.gis_tab ul li').click(function(){
	   $(this).addClass('select').siblings('li').removeClass('select')
    })

	$("#gisTable").dialog({
		autoOpen: false,
		resizable: false,
		width: 690,
		height:380,
		modal: true
     })

	$("#mqsMap").dialog({
		autoOpen: false,
		resizable: false,
		draggable :true,
		width: 1000,
		height:631,
		modal: true,
		close: function(){
			//$("#gisTableFrame div").remove();
		}
     })
   
	$("#gisMoreInfo").dialog({
		autoOpen: false,
		resizable: false,
		width: 750,
		height:445,
		modal: true
     })

    $('.windows_all').click(function(){
	$('.header').toggle();
	})
	
	  //地图使用帮助弹出框
	$('#map_help').click(function (){
		$("#tanchu").dialog("open");
		
		})
	$("#tanchu" ).dialog({
			autoOpen: false,
			minHeight: 300,
			minWidth: 750 ,
			zIndex:1007,//层叠级别
			modal: true,
			resizable: false, 
			draggable :true
		})
	$("#gisVipUser").dialog({
		autoOpen: false,
		width: 700,
		modal: true,
		resizable: false, 
		draggable :true
	})
	$("#gisComplain").dialog({
		autoOpen: false,
		width: 700,
		modal: true,
		resizable: false, 
		draggable :true
	})
	$("#site_Select").dialog({
		autoOpen: false,
		width: 600,
		height: 125,
		modal: true,
		resizable: false, 
		draggable :true,
		close:function(){
			if(visible_2g == true){
				$('#2GSite').prop('checked',true); 
			}else{
				$('#2GSite').prop('checked',false); 
			}
			if(visible_3g == true){
				$('#3GSite').prop('checked',true); 
			}else{
				$('#3GSite').prop('checked',false); 
			}
			if(visible_4g == true){
				$('#4GSite').prop('checked',true); 
			}else{
				$('#4GSite').prop('checked',false); 
			}
		}
	})
});

function searchSite()
{
	$('#site_Select').dialog("option","title", "基站选择"); 
	$('#site_Select').dialog("open");
}

function searchComplain()
{
	$('#gisComplain').dialog("option","title", "投诉点查询"); 
	$('#gisComplain').dialog("open");
}

function searchVip()
{
	$('#gisVipUser').dialog("option","title", "用户轨迹查询"); 
	$('#gisVipUser').dialog("open");
}

function getGISTable(srcval)
{
	$('#gisTable').dialog( 'open' );
	$('#gisTableFrame').attr('src',srcval); 
}

function changePage(page)
{
	window.location.href="index.php?r=GIS/GISMap&page="+page;
}

function getMoreInfo(thisPage,queryId,areaName,layerType,moreInfoKey)
{
	/* $.ajax({
		type : 'post',
		url : 'index.php?r=GIS/getMoreInfo';
		data: {
			page : page,
			gridid : gridid,
			tag_name : tag_name,
			search : search
		},
		beforeSend : function(){
			$('#gisTable').dialog( 'open' );
		},
		success : function(data){
			
		}
	}); */
	var areaName = encodeURIComponent(areaName);
	var srcval = 'index.php?r=GIS/getMoreInfo&page='+thisPage+'&queryid='+queryId+'&areaname='+areaName+'&layertype='+layerType+'&moreinfokey='+moreInfoKey;
	$('#gisMoreInfo').dialog( 'open' );
	$('#gisTableFrame').attr('src',srcval);
}

function getMQSMap(complainid)
{
	var srcval = 'index.php?r=GIS/MQSMap&complainid='+complainid;
	$('#mqsMap').dialog( 'open' );
	$('#mqsMapFrame').attr('src',srcval);
}

function showFrameDiv(display){
	if(display)
		$('#loader_container2').show();
	else
		$('#loader_container2').hide();
}