
<div class="span-19">
<div id="content">

<!-- sidebar -->
<div class="form">
<form id="config-config-form" action="<?php echo $this->createUrl('/site/config');?>" method="post">
    <!--<p class="note">带 <span class="required">*</span> 为必填项</p>-->
    <?php
    /*$obj = simplexml_load_file('update/xml/conf.xml');
	$a = array();
	$a = $obj;*/
	$connection=Yii::app()->db;
	$sql = 'select * from config where type=1';
	$command = $connection->createCommand($sql);
	$cr = $command -> queryAll();
	foreach($cr as $k=>$v){
	?>
	<div class="row">
      <label for="Config_<?php echo $v['tagname'];?>"><?php echo $v['tagdes'];?></label>
      <input name="Config[<?php echo $v['tagname'];?>]" id="Config_<?php echo $v['tagname'];?>" type="text" size="40" value="<?php echo $v['tagvalue'];?>" />
    </div>
    <?php }?>
    <div class="row buttons">
      <input type="submit" name="yt0" value="提交" />
    </div>
</form>
</div><!-- form -->

</div>
</div>