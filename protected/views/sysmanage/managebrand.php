<?php
function editBrand($id){
	echo "<span onclick='editBrand(".$id.",1)' style='color:#417EB7;cursor:pointer;'><u>编辑</u></span><span style='color:#417EB7;'>&nbsp;&nbsp;/&nbsp;&nbsp;</span><span onclick='editBrand(".$id.",2)' style='color:#417EB7;cursor:pointer;'><u>删除</u></span>";
}
?>
<ul class="table_menu">
	<li class="li1 <?php if($type=='list') echo 'select';?>">
		<p class="p1"></p>
		<a href="<?php echo Yii::app()->createUrl("sysmanage/managebrand",array("type"=>'list'));?>">品牌列表</a>
		<p class="p2"></p>
	</li>
	<li class="li1 <?php if($type=='add') echo 'select';?>">
		<p class="p1"></p>
		<a href="<?php echo Yii::app()->createUrl("sysmanage/managebrand",array("type"=>'add'));?>">添加品牌</a>
		<p class="p2"></p>
	</li>
</ul>
<?php if($type == 'list'){?>
<div class="main_table">
	<div class="table_search">
		<ul>
			<?php 
			$form=$this->beginWidget('CActiveForm', array(
				'action'=>Yii::app()->createUrl($this->route),
				'method'=>'get',
			));?>
			<li class='p1'>
				<input id='sear' name="sbrand" class="searhbutton self_go" type="text" size="30" maxlength="255" value="<?php if(isset($_GET['sbrand'])){echo $_GET['sbrand'];}else{echo "输入品牌中文进行搜索";}?>" />
				<input class='self_bt' type='submit' id="submit" value='' />
			</li>
			<?php $this->endWidget(); ?>		  
			<li class='p1 ret'>
				<a href="<?php echo Yii::app()->createUrl($this->route);?>">重 置</a>
			</li>			
			<li class='p1 ret'>
				<a href="<?php echo Yii::app()->request->getUrl();?>">刷 新</a>
			</li>			
			<li class='p1 ret'>
				<a href='<?php echo Yii::app()->createUrl("sysmanage/updatebrand&up=1");?>' onclick='{if(confirm("更新期间建议不要进行其他操作,确定继续吗?")){$("#updatetip").dialog( "open" );}else{return false;}}' style='color: blue;'>更新品牌库</a>
			</li>
		</ul>
	
		<div class="clear"></div>
	</div>
  
	<div class="table_order">
		<ul class="order"></ul>
	</div>
<?php 

	$this->widget('zii.widgets.grid.CGridView', 
		array(
			'id'=>'view-action-logs-grid',
			'dataProvider'=>$model->search(),
			'cssFile'=>false,
			'beforeAjaxUpdate'=>'function(id,data){
				$("#loader_container1").css("display","block");
			}',
		    'afterAjaxUpdate'=>'function(id,data){
		    	$("#loader_container1").css("display","none");
			}',
			'columns'=>array(
				'phoneBrand',
				'phoneBrandCN',
				array(
					'name'=>'操作',
					'value'=>'editBrand($data->id)'
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
	 
<div id="editBrand" title="编辑详情">
	<iframe src="" marginwidth="0" height="165" width="100%" marginheight="0"  frameborder="0" id="brandView" class="brandView iframe_autoheight"></iframe>
</div>
<?php if(isset($_GET['up']) && $_GET['up'] == 1){?>
<div id="updatetip" title="终端品牌更新"></br></br></br>
	<div id="viewPageData">
		<h1 align="center">正在更新终端品牌库，请稍候...</h1>
	</div>
</div>
<?php }?>
<script type="text/javascript">
$(document).ready(function(){	
	$('#sear').click(function(){
		if($(this).val()=='输入品牌中文进行搜索'){
			$(this).val('');	 
		}
	});
	
	$('#sear').blur(function(){
		if($(this).val()==''){
			$(this).val('输入品牌中文进行搜索');	 
		}
	});   
});
</script>
<?php }elseif ($type == 'add'){?>
<div class="form_modify hellos" id='addForm'>
	<div class='form'>
	<?php 
		$form=$this->beginWidget('CActiveForm', 
		array(
				'id'=>'edit-brand-form',
				'htmlOptions'=>array('name'=>'brand-form'),
				'enableAjaxValidation'=>false,
			)
		); 
		if($ad){
	?>
		<p class="note" style="color:green;">品牌记录添加成功</p>
	<?php }?>
	<p class="note">带有 <span class="required">*</span> 标记的为必填项.</p>
	<?php 
		echo $form->errorSummary($model); 
	?>
		<div class="row">
			<?php echo $form->labelEx($model,'phoneBrand'); ?>
			<?php echo $form->textField($model,'phoneBrand',array('size'=>45,'maxlength'=>45)); ?>
			<input type="text" style="display:none" value="" />
		</div>
		<div class="row">
			<?php echo $form->labelEx($model,'phoneBrandCN'); ?>
			<?php echo $form->textField($model,'phoneBrandCN',array('size'=>45,'maxlength'=>45)); ?>
			<input type="text" style="display:none" value="" />
		</div>
		<div class="row buttons" style="text-align:left;padding:9px 0 9px 270px">
			<?php echo CHtml::submitButton('添加'); ?>
		</div>
	<?php 
		$this->endWidget(); 
	?>
	</div>
</div>
<?php }?>