
<div class="form_modify">
  <?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'manage-list-form',
)); ?>
  <p class="note">带有 <span class="required">*</span> 标记的为必填项.</p>
  <div id="return" style="color:#F00; text-align:center; line-height:20px;"></div>

 <input maxlength="20"  type="password" value=""  style="display:none"/>
  <div class="row">
    <label for="Changpwd_oldpwd" class="required">原密码 <span class="required">*</span></label>
    <input maxlength="20" name="Changpwd[oldpwd]" id="Changpwd_oldpwd" type="password" value="" />
  </div>
  <div class="row">
    <label for="Changpwd_newpwd" class="required">输入新密码 <span class="required">*</span></label>
    <input maxlength="20" name="Changpwd[newpwd]" id="Changpwd_newpwd" type="password" value="" />
  </div>
  <div class="row">
    <label for="Changpwd_newpwds" class="required">再次输入新密码 <span class="required">*</span></label>
    <input maxlength="20" name="Changpwd[newpwds]" id="Changpwd_newpwds" type="password" value="" />
  </div>
  <div class="row buttons" style="text-align:left;padding:9px 0 9px 270px"> <input id="chgbtn" type="button" value="提交" name="yt0"> </div>
  <?php $this->endWidget(); ?>
</div>

<script type="text/javascript">
$('#chgbtn').click(function(){
	var url = '<?php echo  Yii::app()->controller->createUrl('site/changpwd');?>'; 
	var data = $("#manage-list-form").serialize();
	$.ajax({
		type: 'POST',
		url: url,
		dataType: 'html',
		data: data,
		success: function(msg){
			$('#return').html(msg);
			if(msg=='修改成功！'){
				var timer = setInterval(function(){
					$("#dialog").dialog("close");
					clearInterval(timer);
				},1000);
			}
		}
	});
});
</script>