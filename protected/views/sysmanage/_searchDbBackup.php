
<?php 
	$form=$this->beginWidget('CActiveForm', 
		array(
			'action'=>Yii::app()->createUrl($this->route),
			'method'=>'get',
		)
	); 
?>
	<ul>
		<li class='p1'>
			<span class="behaviortime">备份时间搜索</span>
			<input type="text" id="Backup_time" class="Wdate" name="DbbackupList[backupTime]" value="<?php $thisTime=""; if(isset($_GET['DbbackupList'])){$thisTime=$_GET['DbbackupList']['backupTime'];} echo $thisTime;?>" onFocus="WdatePicker({startDate:'%y-%M-%d',dateFmt:'yyyy-MM-dd',alwaysUseStartDate:true,onpicked:function(){$('#submit').click();}})"/>
		</li >
	  
		<li class='p1'>
			<input class='self_bt' id="submit" type='submit' value='' style="display:none" />  
		</li>
	  
		<li class='p1 ret'> 
			<a href="<?php echo Yii::app()->createUrl($this->route);?>">重 置</a>
		</li>
		
		<li class='p1 ret'>
			<a href="<?php echo $_SERVER["REQUEST_URI"];?>">刷 新</a>
		</li>
		
		<li class='p1 ret'>
			<a  href="#" onclick="javascript:autobackup();">自动备份</a>
		</li>
	</ul>

<?php 
	$this->endWidget(); 
?>

<?php 
	$form=$this->beginWidget('CActiveForm', 
		array(
			'id'=>'backup-form',
			'action'=>$this->createUrl('databack/backup'),
		)
	); 
?>
<?php 
	getOneKeyBackButton();
?>
      <?php $this->endWidget(); ?>
	  
	<div class="clear"></div>