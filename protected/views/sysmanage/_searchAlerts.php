<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	<ul>
		<li class='p1'>
			<input name="SystemAlerts[alerts_tag]" class="searhbutton self_go" type="text" size="30" maxlength="255" value="<?php $thisSearchStr='请输入关键字进行搜索';if(isset($_GET['SystemAlerts']['alerts_tag'])){$thisSearchStr=$_GET['SystemAlerts']['alerts_tag'];} echo $thisSearchStr;?>" onclick="javascript:if(this.value=='请输入关键字进行搜索'){ this.value=''};" onblur="javascript:if(this.value==''){ this.value='请输入关键字进行搜索'};" />
			<input class='self_bt' type='submit' id="submit" value='' />
		</li>
	  
		<li class='p1 ret'>
			<a href="<?php echo Yii::app()->createUrl($this->route);?>">重 置</a> 
		</li>
		
		<li class='p1 ret'>
			<a href="<?php echo $_SERVER["REQUEST_URI"];?>">刷 新</a>
		</li>
    </ul>
	 
	<div class="clear"></div>

<?php $this->endWidget(); ?>