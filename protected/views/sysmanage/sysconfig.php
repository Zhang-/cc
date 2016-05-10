
<ul class="table_menu">
  <li class="<?php if($md=='bh') echo 'select';?>"><p class="p1"></p><a href="<?php echo Yii::app()->createUrl('sysmanage/sysconfig');?>">系统配置</a><p class="p2"></p></li>
<!--  
  <div style="display:none;">
  <li class="<?php if($md=='dm') echo 'select';?>"><p class="p1"></p><a href="<?php echo Yii::app()->createUrl('sysmanage/sysconfig',array('md'=>'dm'));?>">小区数据业务分析</a><p class="p2"></p></li>
  <li class="<?php if($md=='fm') echo 'select';?>"><p class="p1"></p><a href="<?php echo Yii::app()->createUrl('sysmanage/sysconfig',array('md'=>'fm'));?>">小区覆盖能力分析</a><p class="p2"></p></li>
  <li class="<?php if($md=='um') echo 'select';?>"><p class="p1"></p><a href="<?php echo Yii::app()->createUrl('sysmanage/sysconfig',array('md'=>'um'));?>">网络用户分布</a><p class="p2"></p></li>
  <li class="<?php if($md=='sm') echo 'select';?>"><p class="p1"></p><a href="<?php echo Yii::app()->createUrl('sysmanage/sysconfig',array('md'=>'sm'));?>">小区信号覆盖能力</a><p class="p2"></p></li>
  <li class="<?php if($md=='tg') echo 'select';?>"><p class="p1"></p><a href="<?php echo Yii::app()->createUrl('sysmanage/sysconfig',array('md'=>'tg'));?>">易发生T-G切换区域</a><p class="p2"></p></li>
  <li class="<?php if($md=='tw') echo 'select';?>"><p class="p1"></p><a href="<?php echo Yii::app()->createUrl('sysmanage/sysconfig',array('md'=>'tw'));?>">易脱网区域</a><p class="p2"></p></li>
  </div>
-->
</ul>

<div class="form_modify hellos" style="border-top:1px solid #ccc" >

  <div class="form hello">
    <form name="manage_form" id="manage-list-form" action="<?php echo Yii::app()->createUrl('sysmanage/sysconfig',array('md'=>$md));?>" method="post" onsubmit="return checksub(this)">
      <p class="note">带有 <span class="required">*</span> 标记的为必填项.</p>
      <?php if($md=='bh'){?>
      <div class="row">
        <label for="sysconfig_sendByte" class="required">收发字节参数 <span class="required">*</span></label>
        <input size="20" maxlength="20" name="sysconfig[sendByte]" id="sysconfig_sendByte" type="text" value="<?php echo Yii::app()->params->sendByte;?>" />  (单位:kb)
      </div>
      <div class="row">
        <label for="sysconfig_callDuration" class="required">通话时长参数 <span class="required">*</span></label>
        <input size="50" maxlength="50" name="sysconfig[callDuration]" id="sysconfig_callDuration" type="text" value="<?php echo Yii::app()->params->callDuration;?>" /> (单位:s)
      </div>
      <?php 
        }else{
        $gmodel=GisConfig::model()->findByAttributes(array('type'=>$md));
        if($gmodel){
      ?>
      <input type="hidden" name="gisconfig[id]" value="<?php echo $gmodel->id;?>" />
      <div class="row">
        <label for="gisconfig_tag" class="required">所在目录 <span class="required">*</span></label>
        <input size="50" maxlength="50" name="gisconfig[tag]" id="gisconfig_tag" type="text" value="<?php echo $gmodel->tag;?>" /> flexgis 目录的子目录名
      </div>
      <div class="row">
        <label for="gisconfig_level" class="required">地图缩放级别 <span class="required">*</span></label>
        <input size="20" maxlength="20" name="gisconfig[level]" id="gisconfig_level" type="text" value="<?php echo $gmodel->level;?>" /> 0-19的整数 (单位:级)
      </div>
      <div class="row">
        <label for="gisconfig_clon" class="required">地图中心位置经度 <span class="required">*</span></label>
        <input size="50" maxlength="50" name="gisconfig[clon]" id="gisconfig_clon" type="text" value="<?php echo $gmodel->clon;?>" /> GPS经度
      </div>
      <div class="row">
        <label for="gisconfig_clat" class="required">地图中心位置纬度 <span class="required">*</span></label>
        <input size="50" maxlength="50" name="gisconfig[clat]" id="gisconfig_clat" type="text" value="<?php echo $gmodel->clat;?>" /> GPS纬度
      </div>
      <div class="row">
        <label for="gisconfig_dataurl" class="required">小区数据url <span class="required">*</span></label>
        <input size="50" maxlength="255" name="gisconfig[dataurl]" id="gisconfig_dataurl" type="text" value="<?php echo $gmodel->dataurl;?>" /> 一个完整的网址
      </div>
      <div class="row">
        <label for="gisconfig_mapurl" class="required">底层地图url <span class="required">*</span></label>
        <input size="50" maxlength="255" name="gisconfig[mapurl]" id="gisconfig_mapurl" type="text" value="<?php echo $gmodel->mapurl;?>" /> 一个完整的网址
      </div>
      <?php }}?>
      <div class="row buttons" style="text-align:left;padding:9px 0 9px 270px">
        <input type="submit" name="yt0" value="保 存" />
      </div>
    </form>
  </div>
  <!-- form --> </div>
<script type="text/javascript" src="js/numeral.js"></script> 
<script type="text/javascript">
function checksub(the){
	var sb = the.sysconfig_sendByte;
	var cd = the.sysconfig_callDuration;
	if(sb!=undefined && sb.value.trim()==''){
		alert('收发字节参数不能为空！');
		return false
	}
	else if(cd!=undefined && cd.value.trim()==''){
		alert('通话时长参数不能为空！');
		return false
	}
	else if(!confirm('您真的要保存此配置吗?')){
		return false;
	}
	else 
		return true;
}
$(document).ready(function(){
	$("#sysconfig_sendByte").numeral(4);
	$("#sysconfig_callDuration").numeral(4);
});
</script>
