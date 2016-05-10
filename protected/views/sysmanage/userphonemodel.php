
<script type="text/javascript">

$(function() {
//帮助
		$( "#modelclick").dialog({
			autoOpen: false,
			//height: 500,
			width: 800,
			modal: true,
			resizable: false,
			draggable :true
		});
//查看窗口
		$( "#getUserViewPage").dialog({
			autoOpen: false,
			//height: 373,
			width: 690,
			modal: true,
			resizable: false,
			draggable :true
		});
 //修改窗口
		$('#userUpdatePage').dialog({autoOpen: false,width:770,modal: true,
		resizable: false,
		draggable :true,
		close: function() {
		parent.location.reload(true);
			 }
		});
			//单个添加
		$( "#userCreate").dialog({
			autoOpen: false,
			//height: 373,
			width: 800,
			modal: true,
			resizable: false,
			draggable :true
		});
	});

 //取消按钮
   function cancelcheck(){
			$("#alertwindows").click();
		  }

	//批量删除
function delcheck(){
		var arr = document.getElementsByName('checkboxs[]');
		var arrValue = [];
		var i=0;
		for(var n=0;n <arr.length;n++){
			if(arr[n].type=='checkbox' && arr[n].checked){
				arrValue[i++] = arr[n].value;
						}
					}
		var ids=arrValue.toString();
		if(ids!=''){
		if(confirm('您真的要删除吗?')){
		$.post("index.php?r=sysmanage/phonemodeldeleteall&ids="+ids+"");
		setTimeout(function(){window.location.reload(true);},300);
				}else{
			return false;
			}
		}else{
		alert("请选择要删除的型号！");
	}
}

function viewUserInfo(id)
{
	var srcval = 'index.php?r=sysmanage/phonemodelview&id='+id;
	$('#getUserViewPage').dialog( 'open' );
	$('.UserViewPage').attr('src',srcval);
}


function updateUserInfo(id)
{
	var srcval="index.php?r=sysmanage/updatephonemodel&id="+id+"";
	$("#userUpdatePage").dialog("open");
	$('.dataview').attr('src',srcval);
}


  </script>
<?php
	echo CHtml::openTag('ul',array('class'=>'table_menu'));
	echo CHtml::openTag('li',array('class'=>'li1 select')).CHtml::openTag('p',array('class'=>'p1')).CHtml::closeTag('p').CHtml::openTag('a',array('href'=>'index.php?r=sysmanage/userphonemodel')).'终端型号管理'.CHtml::closeTag('a').CHtml::openTag('p',array('class'=>'p2')).CHtml::closeTag('p').CHtml::closeTag('li');
	echo CHtml::openTag('li',array('class'=>'li1')).CHtml::openTag('p',array('class'=>'p1')).CHtml::closeTag('p').CHtml::openTag('a',array('href'=>'index.php?r=sysmanage/userphonemodel&md=update')).'终端型号同步'.CHtml::closeTag('a').CHtml::openTag('p',array('class'=>'p2')).CHtml::closeTag('p').CHtml::closeTag('li');	
	echo CHtml::closeTag('ul');	
	echo CHtml::closeTag('ul');

	function getbutton($id){

			echo "
		 <div class='checks'><input name='checkboxs[]' type='checkbox' value=".$id." />
		  <div class='config'>
			 <span class='sp normal'></span>
			 <div class='down'>
			   <span onclick='viewUserInfo(".$id.")' >型号详情</span>
			  <span onclick='updateUserInfo(".$id.")'>型号信息修改</span>
			  <span>";?>
		<?php echo CHtml::link('删除该型号',
						array(
						'/sysmanage/phonemodeldelete',
							'id'=>$id,
						),
						array(
							"title"=>"删除",
							"id"=>"deleteUserButton",
							'onclick'=>"{if(confirm('您真的要删除吗?')){return true;}return false;}"
						));
					?>
		<?php  echo
			  "</span>
			 </div>
		  </div>
		 </div>
	  ";
		}

		function getterminalnum($model)
		{
			$count=Yii::app()->db->createCommand("select count(*) from static_information where phoneModel='".$model."'")->queryrow();
			return $count['count(*)'];
		}

	$model=new ModelNettype;
		 echo '<div class="main_table"><div class="home_p1"><h2 style="border-top:1px solid #ccc">添加终端型号：<span class="help_info" onclick="$(\'#modelclick\').dialog( \'open\' );">批量导入帮助</span></h2>';
 ?>
	<form method="post" action="index.php?r=sysmanage/modelbrandupdate" enctype="multipart/form-data" OnSubmit="return confirm('确定进行此操作吗?');">
	<span>单个添加：</span> <span onclick="$('#userCreate').dialog( 'open' );$('.user_Create').attr('src','index.php?r=sysmanage/createphonemodel');" class="subupdate">添加</span>
	<p style="line-height:24px"><span style="float:left">批量导入：</span><input class='filestyle' type='file' name="modelexcel"/><input type="submit" disabled="disabled" value="上传" class="subupdate"/></p>
	</form>
	<p class="p_4"></p>
	<div id="loader_container1" style="display:none;">
		<div>
			<div>
			<img src="images/loading2.gif">
			数据计算中...
			</div>
		</div>
	</div>
	<div class="table_search">
	<style>
	<!--
	#phoneBrandSearch{padding-top:0px;}
	-->
	</style>
	<?php	
	$form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route,array()),
		'method'=>'GET',
		'id'=>'phoneBrandSearch',
	));
	?>	
	<ul>
		<li class="brand" id="brand">
			<div class="sele_div">
			    <input type="text" id='phoneBrand' value="<?php $str="所有品牌"; if(isset($_GET['phoneBrand'])){$str=$_GET['phoneBrand'];} echo $str;?>" name='phoneBrand' readonly="readonly" />
			    <span class="btn"></span>
			</div>
			         
			<div class="show_div">
				<span <?php if(isset($_GET['phoneBrand'])){ if($_GET['phoneBrand']=='所有品牌') echo 'class="select"';}else echo 'class="select"';?>>所有品牌</span>
				<?php 
					$s = '';
					$connection=Yii::app()->db;
					$sql="SELECT phoneBrand FROM `model_nettype`  group by phoneBrand";
					$command = $connection->createCommand($sql);
					$row=$command->queryAll(); 
					if($row!=null){
						$phoneBrand=array();
						foreach($row as $k=>$v){
							$v['phoneBrand']=trim($v['phoneBrand']);
							if(!in_array($v['phoneBrand'],$phoneBrand))	$phoneBrand[]=$v['phoneBrand'];
						}
						foreach($phoneBrand as $v){
							if(isset($_GET['phoneBrand'])){
								if($_GET['phoneBrand']==$v) $s = 'class="select"';
								else $s = '';
							}
							if($v!=null&&$v!=""){
								echo "<span $s id='".$v."'>".$v."</span>";
							}
						}
					}
		  		?>
			 </div>
		</li>
		<li class='p1' style="display:none;">
			<input class="sisearch self_bt" type="submit" id="submit" value="" />
		</li>		
		<li class='p1 ret'>
			<a href="<?php echo Yii::app()->createUrl($this->route,array());?>">重 置</a>
	  </li>
      <li class="p1 ret"><a href="<?php echo  Yii::app()->request->getUrl();?>">刷 新</a></li>
     </ul>
<?php $this->endWidget(); ?>	 
     <div class="clear"></div>	
	</div>
<?php
	if(isset($_GET['phoneBrand'])&&$_GET['phoneBrand']!='所有品牌') $nowPhoneBrand=trim($_GET['phoneBrand']);
	else $nowPhoneBrand='';
	if(isset($_GET['error']))
		echo "<div class='errorSummary'>".json_decode($_GET['error'])."</div>";
	else if(isset($_GET['success']))
		echo "<div class='errorSummary oprationseccess'>".json_decode($_GET['success'])."</div>";
	$this->widget('zii.widgets.grid.CGridView', array(
		'id'=>'tag_grid',
		'cssFile'=>false,
		'dataProvider'=>$model->search($nowPhoneBrand),
		'beforeAjaxUpdate'=>'function(id,data){
			$("#loader_container1").css("display","block");
				}',
		'afterAjaxUpdate'=>'function (id, data) {
		$("#loader_container1").css("display","none");
			$("#tag_grid_c0").children("a").attr("title","点击按终端品牌排序");
			$("#tag_grid_c1").children("a").attr("title","点击按终端型号排序");
			$("#tag_grid_c2").children("a").attr("title","点击按终端网络能力排序");
			}',
		'columns'=>array(
		array(
				'name'=>'<div class="checks">
				<input id="allcheckbtn" class="all" type="checkbox" />
				<span title="批量操作" class="option" id="alertwindows"></span>
				<div class="allselect" >
				  <div class="headp">勾选项批量操作</div>
				  <p class="p2" onclick="javascript:delcheck();"><span>全部删除</span></p>
				  <p class="p2" onclick="javascript:cancelcheck();">取消</p>
				</div>
				</div>',
				'htmlOptions'=>array('class'=>'first_td'),
				'value'=>'getbutton($data->id)'
			),
			'phoneBrand',
			'phoneModel',
			'netType',
			array('name'=>'数量','value'=>'getterminalnum($data->phoneModel)'),
		),
		'summaryText'=>'第 {start}-{end} 条, 共 {count} 条 当前第 {page} 页，共 {pages} 页',
		'pager'=>array(
			'class'=>'CLinkPager',
			'header'=>'',
			'firstPageLabel'=>'首页',
			'prevPageLabel'=>'上一页',
			'nextPageLabel'=>'下一页',
			'lastPageLabel'=>'尾页',
			'cssFile'=>false,
		),
		'template'=>'{summary}{items}{pager}'
	));
	echo '</div></div>';
?>
<div id="getUserViewPage" title="终端型号详情">
  <iframe src="" marginwidth="0" height="175" width="100%" marginheight="0"  frameborder="0" class="UserViewPage"></iframe>
</div>
<div id="userUpdatePage" title="修改终端型号信息">
 <iframe src="" marginwidth="0" height="230" width="100%" marginheight="0"  frameborder="0" class="dataview UserViewPage"></iframe>
</div>
<div id="userCreate" title="单个添加">
 <iframe src="" marginwidth="0" height="230" width="100%" marginheight="0"  frameborder="0" class="user_Create UserViewPage"></iframe>
</div>
<?php
echo '<div id="modelclick" title="批量导入帮助">';
		echo CHtml::openTag('div',array('id'=>'tagshow'));
		echo '<p style="color: red;"><span></span>表格文件类型 ：Microsoft Office Excel 2003；若您的表格为其他格式，请务必在导入之前保存成Microsoft Office Excel 2003格式</p>';
		 echo '<p><span></span>表格格式 ：excel文件中必须为单张表格，多张表格请分开导入，默认为您导入第一张表格。</p>';
		echo '<p><span></span>表格内容 ：如下<a href=\'index.php?r=site/download&fn='.json_encode('example_model.xls').'\' style="color:blue">（点击下载范本文档）</a></p>';
		echo '<table class="items">
			<thead>
				<tr>
					<th>终端品牌</th>
					<th>终端型号</th>
					<th>终端网络能力</th>
				</tr>
			</thead>
			<tbody>
			<tr>
					<td>HTC</td>
					<td>HTC Incredible S</td>
					<td>G/T</td>
				</tr>
			</tbody>
		</table>';
		echo CHtml::closeTag('div');
		echo '</div>'; ?>
<script type="text/javascript">
	$(document).ready(function(){
		$("#tag_grid_c0").children("a").attr("title","点击按终端品牌排序");
		$("#tag_grid_c1").children("a").attr("title","点击按终端型号排序");
		$("#tag_grid_c2").children("a").attr("title","点击按终端网络能力排序");

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
				$(".p_4").append('<span calss="label_wran" style="color:red;">请选择文件</span>');
				$(".subupdate").attr({'disabled':'disabled'});
			}
		});
	});
</script>