/* 
	<script src="<?php echo Yii::app()->request->baseUrl; ?>/js/sysmanage.js" type="text/javascript"></script>
 */
 
$(function() {
	$("#brand").children(".show_div").children("span").click(function(){
		$("#phoneModel").val('所有型号');
	});
	$("#model").children(".show_div").children("span").click(function(){
	});	
	$("#cpu").children(".show_div").children("span").click(function(){
	});	
		
	$(".show_div").children("span").click(function()
	{
		$("#submit").click();
	});

	/* 
		数据备份
	*/
		
	//全选按钮
	$("#allcheckbtn").click(function() 
	{
		$("input").prop("checked", this.checked);
	});
	

	
	//备份提示窗口
	$( "#userphonemodelPage,#dbBackupPage,#getUserViewPage,#getUserViewLogPage,#getStatementViewPage,#referTip").dialog({
		autoOpen: false,
		resizable: false,
		width: 690,
		modal: true
     }); 
	 
	 $( "#autoBackup").dialog({
		autoOpen: false,
		resizable: false,
		width: 690,
		height:235,
		modal: true
     }); 
	 
	 $( "#refertip").dialog({
			autoOpen: false,
			resizable: false,
			width: 600,
			height:450,
			modal: true
	     }); 

	//修改窗口
   $('#userUpdatePage').dialog({autoOpen: false,resizable: false,width:770,modal: true,
		close: function() { 
			parent.location.reload(true);
		}
   }); 
   
   $('#createrole').dialog({autoOpen: false,resizable: false,width:770,modal: true,
		close: function() { 
			parent.location.reload(true);
		}
   });
   
   $( "#modifyKpi").dialog({
		autoOpen: false,
		width: 700,
		modal: true,
		position: ['top','right'],
		resizable: false, 
		draggable :true,
		close: function() { 
			parent.location.reload(true);
		}
	});
   $( "#modifyConfig").dialog({
		autoOpen: false,
//		height: 250,
		width: 700,
		modal: true,
		position: ['top','right'],
		resizable: false, 
		draggable :false,
		close: function() { 
			parent.location.reload(true);
		}
	});
   $( "#editBrand").dialog({
		autoOpen: false,
//		height: 220,
		width: 700,
		modal: true,
		position: ['top','right'],
		resizable: false, 
		draggable :false,
		close: function() { 
			parent.location.reload(true);
		}
	});
   /* 
	*用户投诉
   */
	$(".filestyle").change(function(){
		var arytype = ['xls'];
		var strfile = $(".filestyle").val();
		var strtype = strfile.split(".");
		var index = strtype.length-1;
		var thistype = strtype[index].replace(/(^\s*)|(\s*$)/g, ""); 
		if(thistype != ""){
			if(jQuery.inArray( thistype, arytype ) != -1){
				$(".subupdate").removeAttr("disabled");	
				$(".p_4").empty();							
			}else{
				$(".p_4").empty();
				$(".subupdate").attr({'disabled':'disabled'});
				$(".p_4").append('<span calss="label_wran" style="color:red;">文件类型错误</span>');
			}
		}else{
			$(".p_4").empty();
			$(".subupdate").attr({'disabled':'disabled'});
			$(".p_4").append('<span calss="label_wran" style="color:red;">请选择文件类型</span>');
		}
	});
	
	//修改窗口
   $('#statementUpdate').dialog({autoOpen: false,resizable: false,width:770,modal: true,
		close: function() { 
			parent.location.reload(true);
		}
   }); 
   //新建窗口
   $('#statementCreate').dialog({autoOpen: false,resizable: false,width:770,modal: true,
   close: function() { 
			parent.location.reload(true);
		}});
   
    $( "#help_info_click").dialog({
		autoOpen: false,
		height: 260,
		width: 800,
		modal: true,
		position: ['top','right'],
		resizable: false, 
		draggable :true
	}); 
   
    //批量导入口径信息
   $('#inputData').dialog({autoOpen: false,width:800,height:300,modal: true,resizable: false, draggable :false,
   close: function() { 
		parent.location.reload(true);
			 }
   });

   //系统提醒子项修改窗口
   $('#alertUpdatePage').dialog({autoOpen: false,resizable: false,width:770,modal: true,
		close: function() { 
			parent.location.reload(true);
		}
   }); 
   //系统提醒子项详情
   $('#alertViewPage').dialog({autoOpen: false,resizable: false,width:770,modal: true,
		close: function() { 
			parent.location.reload(true);
		}
   }); 
   
});

/* 
	备份
 */

//取消按钮
function cancelcheck()
{
	$("#alertwindows").click();
} 

//自动备份设置
function autobackup()
{
	var srcval = 'index.php?r=sysmanage/autobackup';
	$('#autoBackup').dialog( 'open' );
	$('#autoBackFrame').attr('src',srcval);
}

//批量删除备份
function delcheckbackup()
{  
	var arr = document.getElementsByName('checkboxs[]');
	var arrValue = [];
	var i=0;
	for(var n=0;n <arr.length;n++)
	{
		if(arr[n].type=='checkbox' && arr[n].checked)
		{
			var temp = arr[n].value;
			var reg="/";
			var result=temp.replace(reg,'');
			arrValue[i++]=result;
		}
	}
	
	var results=arrValue.toString();
	
	if(results!='')
	{
		if(confirm('您真的要删除吗?'))
		{
		$.post("index.php?r=databack/delete_all_back&files="+results);
		}else{
			return false;	
		}
	setTimeout(function(){window.location.reload(true);},300);
	}else{
		alert('请选择要删除的备份！');
	} 
}

/* 
	用户管理
 */
 
 //创建新用户角色
 function createRole()
 {
	var srcval = 'index.php?r=sysmanage/createrole';
	$('#createrole').dialog( 'open' );
	$('.createrole').attr('src',srcval);
 }
 
 	//批量删除
function delcheckuser(thisuserid)
{  
	var arr = document.getElementsByName('checkboxs[]');
	var arrValue = [];
	var i=0;
	for(var n=0;n <arr.length;n++){
		if(arr[n].type=='checkbox' && arr[n].checked && arr[n].value != thisuserid )
		{
			arrValue[i++] = arr[n].value;	
		}
	}
	var ids=arrValue.toString();
	
	 if(ids!=''){
		if(confirm('您真的要删除吗?'))
		{
			$.post("index.php?r=sysmanage/userdeleteall&ids="+ids+"");
			setTimeout(function(){window.location.reload(true);},300);
		}else{
			return false;	
		}
	}else{
		alert("请选择要删除的用户！");
	} 
}

function viewUserInfo(id)
{
	var srcval = 'index.php?r=sysmanage/userupdateview&id='+id;
	$('#getUserViewPage').dialog( 'open' );
	$('.UserViewPage').attr('src',srcval);
}


function updateUserInfo(id)
{
	var srcval="index.php?r=sysmanage/userupdate&id="+id+"";
	$("#userUpdatePage").dialog("open");
	$('.dataview').attr('src',srcval);
}

function modifyKpi(id,type)//kpi标准值修改
{
	if(type == 0){
		$('#modifyKpi').dialog("option","title", "添加KPI指标"); 
		$("#modifyKpi").dialog("open");
	}else if(type == 1){
		$('#modifyKpi').dialog("option","title", "修改KPI指标"); 
		$("#modifyKpi").dialog("open");
	}else if(type == 2){
		if( confirm('您确定要删除该KPI指标？')){
			alert('删除成功！');
			parent.location.reload(true);
		}else{
			type = -1;
		}
	}
	var srcval="index.php?r=sysmanage/modifyKpi&id="+id+"&type="+type+"";
	$('.dataview').attr('src',srcval);
}

function modifyConfig(id)//客户端配置项修改
{
	var srcval="index.php?r=sysmanage/modifyConfig&id="+id+"";
	$("#modifyConfig").dialog("open");
	$('.configView').attr('src',srcval);
}
function editBrand(id,type)//客户端配置项修改
{
	if(type == 1){
		$("#editBrand").dialog("open");
	}else if(type == 2){
		if( confirm('您确定要删除该品牌？')){
			alert('删除成功！');
			parent.location.reload(true);
		}else{
			type = -1;
		}
	}
	var srcval="index.php?r=sysmanage/editBrand&id="+id+"&type="+type+"";
	$('.brandView').attr('src',srcval);
}
/* 
	用户日志
 */
 
//查看窗口
function viewUserLogInfo(id)
{
	var srcval = 'index.php?r=sysmanage/userlogview&id='+id;
	$('#getUserViewLogPage').dialog( 'open' );
	$('.UserViewPage').attr('src',srcval);
}

//批量删除
function delcheckuserlog()
{  
	var arr = document.getElementsByName('checkboxs[]');
	var arrValue = [];
	var i=0;
	for(var n=0;n <arr.length;n++){
		if(arr[n].type=='checkbox' && arr[n].checked){
			arrValue[i++] = arr[n].value;	
		}
	}
	
	var ids=arrValue.toString();
	 if(ids!='')
	{
		if(confirm('您真的要删除吗?'))
		{
		$.post("index.php?r=sysmanage/userlogdeleteall&ids="+ids+"");
			setTimeout(function(){window.location.reload(true);},300);
		}else{
			return false;	
		}
	}else{
		alert("请选择要删除的记录！");
	} 
}

/* 
 * 用户投诉口径表
 */
 
 //导出表格
function statementExprot()
{
	var url = 'index.php?r=sysmanage/statementexport';
	var j=0;
	$(".load_out").attr("onclick","");
	$("#img_box").show();
	var eptime = setInterval(function(){
		var t = j%100;
		var s = Math.floor(j/100);
		var g = "用时:"+s+"."+t+"秒";
		$("#img_box").html(g);
		j++;	
	},10);
	$.ajax({
		type : "POST",
		url : encodeURI(url),
		success : function(data){
			$("#adminshow").html(data);
			clearInterval(eptime);
			var timeer = setInterval(function(){
				$("#img_box").fadeOut();
				clearInterval(timeer);
				$(".load_out").attr("onclick","statementExprot()");
			},3000);
		} 
	});
}
 
 //批量导入口径信息
function inputStatement()
{
	var srcval="index.php?r=sysmanage/inputstatement";
		$("#inputData").dialog("open");
		$('#datainputview').attr('src',srcval);
}
 
 //查看窗口
function viewStatementInfo(id)
{
	var srcval = 'index.php?r=sysmanage/statementview&id='+id;
	$('#getStatementViewPage').dialog( 'open' );
	$('.UserViewPage').attr('src',srcval);
}

//修改口径信息
function updateStatementInfo(id)
{
	var srcval="index.php?r=sysmanage/statementupdate&id="+id+"";
	$('#statementUpdate').dialog('open');
	$('.dataview').attr('src',srcval);
}

//批量删除
function delcheckstatement()
{  
	var arr = document.getElementsByName('checkboxs[]');
	var arrValue = [];
	var i=0;
	for(var n=0;n <arr.length;n++){
		if(arr[n].type=='checkbox' && arr[n].checked){
			arrValue[i++] = arr[n].value;	
		}
	}
	
	var ids=arrValue.toString();
	 if(ids!='')
	{
		if(confirm('您真的要删除吗?'))
		{
		$.post("index.php?r=sysmanage/statementdeleteall&ids="+ids);
		setTimeout(function(){window.location.reload(true);},300);
		}else{
			return false;
		}
	}else{
		alert("请选择要删除的记录！");
	} 
}

/*
	系统消息提示
*/

//系统提醒详情
function viewAlertsInfo(id){
	var srcval = 'index.php?r=sysmanage/alertsView&id='+id+"";
	$('#alertViewPage').dialog( 'open' );
	$('#alertViewFrame').attr('src',srcval);
}

//系统提醒更新弹窗
function updateAlertsInfo(id){
	var srcval="index.php?r=sysmanage/alertsUpdate&id="+id+"";
	$('#alertUpdatePage').dialog('open');
	$('#alertUpdateFrame').attr('src',srcval);
}

//批量删除
function delcheckalerts()
{  
	var arr = document.getElementsByName('checkboxs[]');
	var arrValue = [];
	var i=0;
	for(var n=0;n <arr.length;n++){
		if(arr[n].type=='checkbox' && arr[n].checked){
			arrValue[i++] = arr[n].value;	
		}
	}
	
	var id=arrValue.toString();
	 if(id!='')
	{
		if(confirm('您真的要删除吗?'))
		{
			$.post("index.php?r=sysmanage/alertsDelete&id="+id);
			setTimeout(function(){window.location.reload(true);},300);
		}else{
			return false;
		}
	}else{
		alert("请选择要删除的记录！");
	} 
}

function delchecks(url)
{  
	var arr = document.getElementsByName('checkboxs[]');
	var arrValue = [];
	var i=0;
	for(var n=0;n <arr.length;n++){
		if(arr[n].type=='checkbox' && arr[n].checked){
			arrValue[i++] = arr[n].value;	
		}
	}
	
	var ids=arrValue.toString();
	 if(ids!='')
	{
		if(confirm('您真的要删除吗?'))
		{
			$.post("index.php?r="+url+"&id="+ids);
			setTimeout(function(){window.location.reload(true);},300);
		}else{
			return false;
		}
	}else{
		alert("请选择要删除的记录！");
	} 
}


