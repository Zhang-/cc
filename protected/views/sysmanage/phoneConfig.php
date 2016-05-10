<?php
function getOperate($id){
	echo "<span onclick='modifyConfig(".$id.")' style='color:#417EB7;cursor:pointer;'><u>修改信息</u></span>";
}
?>
<ul class="table_menu">
	<li class="select">
		<p class="p1"></p>
		<a href="<?php echo Yii::app()->createUrl($this->route);?>">终端配置管理</a>
		<p class="p2"></p>
	</li>
</ul>
	
<div class="main_table">
	<div class="table_search">
	<?php 
	$form=$this->beginWidget('CActiveForm', array(
		'action'=>Yii::app()->createUrl($this->route),
		'method'=>'get',
	));?>
		<ul>
			<li class='p1'>
				<input id='sear' name="TerminalConfig[tagname]" class="searhbutton self_go" type="text" size="30" maxlength="255" value="<?php if(isset($_GET['TerminalConfig']['tagname'])){echo $_GET['TerminalConfig']['tagname'];}else{echo "输入配置名进行搜索";}?>" />
				<input class='self_bt' type='submit' id="submit" value='' />
			</li>		  
			<li class='p1 ret'>
				<a href="<?php echo Yii::app()->createUrl($this->route);?>">重 置</a>
			</li>			
			<li class='p1 ret'>
				<a href="<?php echo Yii::app()->request->getUrl();?>">刷 新</a>
			</li>
		</ul>
	<?php $this->endWidget(); ?> 
		<div class="clear"></div>
	</div>
  
	<div class="table_order">
		<ul class="order"></ul>
	</div>
<?php 

	$this->widget('zii.widgets.grid.CGridView', 
		array(
			'id'=>'view-config-grid',
			'dataProvider'=>$model->searchConfig(),
			'cssFile'=>false,
			'beforeAjaxUpdate'=>'function(id,data){
				$("#loader_container1").css("display","block");
			}',
		    'afterAjaxUpdate'=>'function(id,data){
		    	$("#loader_container1").css("display","none");
			}',
			'columns'=>array(
				'tagname',
				'tagvalue',
				'tagdes',
				array(
					'name'=>'操作',
					'value'=>'getOperate($data->id)'
				),
			),		
			'summaryText'=>'第 {start}-{end} 条, 共 {count} 条 当前第 {page} 页，共 {pages} 页',
			'pager'=>
				array(
					'class'=>'CLinkPager',
					'header'=>'',
					'firstPageLabel'=>'首页',
					'prevPageLabel'=>'上一页',
					'nextPageLabel'=>'下一页',
					'lastPageLabel'=>'尾页',
					'cssFile'=>false,
				),
			'template'=>'{summary}{items}{pager}'
		)
	); 
?>
</div>
	 
<div id="modifyConfig" title="配置详情">
	<iframe src="" marginwidth="0" height="220" width="100%" marginheight="0"  frameborder="0" id="configView" class="configView iframe_autoheight"></iframe>
</div>
	
<script type="text/javascript">
$(document).ready(function(){	
	$('#sear').click(function(){
		if($(this).val()=='输入配置名进行搜索'){
			$(this).val('');	 
		}
	});
	
	$('#sear').blur(function(){
		if($(this).val()==''){
			$(this).val('输入配置名进行搜索');	 
		}
	});   
});
</script>