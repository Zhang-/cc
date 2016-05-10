<?php 
	$form=$this->beginWidget('CActiveForm', 
		array(
			'action'=>Yii::app()->createUrl($this->route),
			'method'=>'get',
		)
	); 
?>

<?php 
	$getLogsSearch=CacheFile::logsSearch();//所有搜索缓存

?>

	<ul>
      
		<li>
			<span class="behaviortime">记录时间</span>	 
			<input id="user_create_time" class="Wdate" type="text" name="ViewActionLogs[actiontime]" value="<?php $thisTime='';if(isset($_GET['ViewActionLogs']['actiontime'])){$thisTime=$_GET['ViewActionLogs']['actiontime'];} echo $thisTime;?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,isShowOK:false,onpicked:function(){$('#submit').click();},maxDate:'%y-%M-%d %H:%m:$s'})">

		</li>
		
		<li class="brand">
			<div class="sele_div"><input type="text" value="<?php $thisLevel="所有用户组"; if(isset($_GET['ViewActionLogs'])){$thisLevel=$_GET['ViewActionLogs']['userrole'];} echo $thisLevel;?>"  name="ViewActionLogs[userrole]" readonly="readonly" /><span class="btn"></span></div>
			<div class="show_div">
				<span  <?php if(isset($_GET['ViewActionLogs'])){ if($_GET['ViewActionLogs']['userrole']=='所有用户组') echo 'class="select"';}else echo 'class="select"';?>>所有用户组</span>
				<?php
				if(!empty($getLogsSearch['userrole']))
				{
					$slt = '';
					foreach($getLogsSearch['userrole'] as $rolesKey=>$rolesVal)
					{
						if(isset($_GET['ViewActionLogs']))
							$slt = ($_GET['ViewActionLogs']['userrole']==$rolesVal) ? 'class="select"' : '';
							
						echo '<span '.$slt.' id="'.$rolesKey.'">'.$rolesVal.'</span>';
					}
				}
				?>
			</div>
		</li>
	  
		<li class="brand">
			<div class="sele_div"><input type="text" value="<?php $thisCategory="所有类型"; if(isset($_GET['ViewActionLogs'])){$thisCategory=$_GET['ViewActionLogs']['actiontype'];} echo $thisCategory;?>"  name="ViewActionLogs[actiontype]" readonly="readonly" /><span class="btn"></span></div>
			<div class="show_div">
				<span <?php if(isset($_GET['ViewActionLogs'])){ if($_GET['ViewActionLogs']['actiontype']=='所有类型') echo 'class="select"';}else echo 'class="select"';?>>所有类型</span>
				<?php
				if(!empty($getLogsSearch['actiontype']))
				{
					foreach($getLogsSearch['actiontype'] as $categoryKey=>$categoryVal)
					{
						if(isset($_GET['ViewActionLogs']))
						$slt = ($_GET['ViewActionLogs']['actiontype']==$categoryVal) ? 'class="select"' : '';
						
						echo '<span '.$slt.' id="'.$categoryKey.'">'.$categoryVal.'</span>';
					}
				}
				?>
			</div>
		</li>
	  
		<li class='p1'>
			<input name="ViewActionLogs[username]" class="searhbutton self_go" type="text" size="30" maxlength="255" value="<?php $thisUserName="请输入用户名进行搜索";if(isset($_GET['ViewActionLogs'])){$thisUserName=$_GET['ViewActionLogs']['username'];} echo $thisUserName;?>" onclick="javascript:if(this.value=='请输入用户名进行搜索'){ this.value=''};" onblur="javascript:if(this.value==''){ this.value='请输入用户名进行搜索'};" />
			<input class='self_bt' type='submit' id="submit" value='' />
		</li>
	  
		<li class='p1 ret'>
			<a href="<?php echo Yii::app()->createUrl($this->route);?>">重 置</a>
		</li>
		
		<li class='p1 ret'>
			<a href="<?php echo $_SERVER["REQUEST_URI"];?>">刷 新</a>
		</li>
		
		<?php if(HelpTool::checkActionAccess('sysmanageclearlogs')){ ?>
		
		<li class='p1 ret' >
			<a  href="index.php?r=sysmanage/clearlogs" onclick = "if(confirm('您真的要清除所有日志吗?')){return true;}else{return false;} ">清除日志</a>
		</li>
		<?php } ?>
	  
    </ul>
	 
	<div class="clear"></div>

<?php 
	$this->endWidget(); 
?>