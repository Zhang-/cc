<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

<ul>

	<li>
		<span class="behaviortime">下单时间</span>	 
		<input id="user_create_time" class="Wdate" type="text" name="PlaceOrder[o_time]" value="<?php $thisTime='';if(isset($_GET['PlaceOrder']['o_time'])){$thisTime=$_GET['PlaceOrder']['o_time'];} echo $thisTime;?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,isShowOK:false,onpicked:function(){$('#submit').click();},maxDate:'%y-%M-%d %H:%m:$s'})">

	</li>

	<li class='p1'>
		<input name="PlaceOrder[user_name]" class="searhbutton self_go" type="text" size="30" maxlength="255" value="<?php $thisUserName="请输入用户名进行搜索";if(isset($_GET['PlaceOrder'])){$thisUserName=$_GET['PlaceOrder']['user_name'];} echo $thisUserName;?>" onclick="javascript:if(this.value=='请输入用户名进行搜索'){ this.value=''};" onblur="javascript:if(this.value==''){ this.value='请输入用户名进行搜索'};" />
		<input class='self_bt' type='submit' id="submit" value='' />
	</li>
  
	<li class='p1 ret'>
		<a href="<?php echo Yii::app()->createUrl($this->route);?>">重 置</a>
	</li>
	
	<li class='p1 ret'>
		<a href="<?php echo $_SERVER["REQUEST_URI"];?>">刷 新</a>
	</li>

<?php $this->endWidget(); ?>

</ul>
 
<div class="clear"></div>