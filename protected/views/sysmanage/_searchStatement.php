<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'GET',
)); 
$allProjectStatus = CacheFile::getProjectStatus(); //获取所有项目状态
$allStatementType = CacheFile::getStatementType(); //获取所有口径类型
?>
 	<ul>
	
	<li class="brand">
			<div class="sele_div"><input type="text" value="<?php $thisStateType="所有类型"; if(isset($_GET['ComplainStatementList'])){$thisStateType=$_GET['ComplainStatementList']['state_type'];} echo $thisStateType;?>"  name="ComplainStatementList[state_type]" readonly="readonly" /><span class="btn"></span></div>
			<div class="show_div">
				<span <?php if(isset($_GET['ComplainStatementList'])){ if($_GET['ComplainStatementList']['state_type']=='所有类型') echo 'class="select"';}else echo 'class="select"';?>>所有类型</span>
				<?php
				if(!empty($allStatementType))
				{
					$slt = '';
					foreach($allStatementType as $stateTypeKey=>$stateTypeVal)
					{
						if(isset($_GET['ComplainStatementList']))
						$slt = ($_GET['ComplainStatementList']['state_type']==$stateTypeVal) ? 'class="select"' : '';
						
						echo '<span '.$slt.' id="'.$stateTypeKey.'">'.$stateTypeVal.'</span>';
					}
				}
				?>
			</div>
		</li>
		
			<li class="brand">
			<div class="sele_div"><input type="text" value="<?php $thisStateType="所有项目状态"; if(isset($_GET['ComplainStatementList'])){$thisStateType=$_GET['ComplainStatementList']['project_status'];} echo $thisStateType;?>"  name="ComplainStatementList[project_status]" readonly="readonly" /><span class="btn"></span></div>
			<div class="show_div">
				<span <?php if(isset($_GET['ComplainStatementList'])){ if($_GET['ComplainStatementList']['project_status']=='所有项目状态') echo 'class="select"';}else echo 'class="select"';?>>所有项目状态</span>
				<?php
				if(!empty($allProjectStatus))
				{
					$slt = '';
					foreach($allProjectStatus as $projectStatusKey=>$projectStatusVal)
					{
						if(isset($_GET['ComplainStatementList']))
						$slt = ($_GET['ComplainStatementList']['project_status']==$projectStatusVal) ? 'class="select"' : '';
						
						echo '<span '.$slt.' id="'.$projectStatusKey.'">'.$projectStatusVal.'</span>';
					}
				}
				?>
			</div>
		</li>
		
		<!-- 时间搜索 -->    
		<li>
			<span class="behaviortime">开始时间</span>
			<input id='startdate' class="Wdate" onClick="WdatePicker({dateFmt:'yyyy-MM-dd 00:00',onpicked:function(){$('#submit').click();},isShowClear:false,isShowOK:false,maxDate:enddate.value})" name="ComplainStatementList[starttime]" value="<?php if(isset($_GET['ComplainStatementList']['starttime'])){echo $_GET['ComplainStatementList']['starttime'];};?>" />
		</li>
		<li class="gang">--</li>
        <li>
			<span class="behaviortime">结束时间</span>
			<input id='enddate' class="Wdate" onClick="WdatePicker({dateFmt:'yyyy-MM-dd 23:59',onpicked:function(){$('#submit').click();},isShowClear:false,isShowOK:false,minDate:startdate.value})" name="ComplainStatementList[endtime]" value="<?php if(isset($_GET['ComplainStatementList']['endtime'])){echo $_GET['ComplainStatementList']['endtime'];};?>" />
		</li>
		<!-- 搜索框+提交 -->
		<li class='p1'>
			
			<input name="ComplainStatementList[state_content]" class="searhbutton self_go" style="width:190px;"  type="text" id="state_content" size="30" maxlength="255" value="<?php $str="输入口径内容或影响范围进行搜索"; if(isset($_GET['ComplainStatementList'])){$str=$_GET['ComplainStatementList']['state_content'];} echo $str;?>" onclick="javascript:if(this.value=='输入口径内容或影响范围进行搜索'){ this.value=''};" onblur="javascript:if(this.value==''){ this.value='输入口径内容或影响范围进行搜索'};" /> 
			<input class="sisearch self_bt" type="submit" id="submit" value="" />		
			
		</li>
		<!-- 重 置 -->
		<li class='p1 ret'>
			<a href="<?php echo Yii::app()->createUrl($this->route);?>">重 置</a>
		</li>
		<!-- 刷新 -->	
		<li class="p1 ret"><a href="<?php echo  Yii::app()->request->getUrl();?>">刷 新</a></li>
		
		<!--<li class='p1 ret'>
			<input  class="beifen" type="button" value="导入口径信息" style="color:red" onclick="javascript:inputStatement();" />
		</li>-->
		<li><span class="user_stat">共有<a><?php 
						$allFindIdsNum = $model->search()->totalItemCount;
						echo $allFindIdsNum;
						?></a>条数据</span></li>
     </ul>
     <div class="clear"></div>
<?php $this->endWidget(); ?>