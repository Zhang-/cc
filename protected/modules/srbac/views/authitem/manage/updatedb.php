
<div class="srbacForm">
<?php echo SHtml::beginForm(); ?>
<?php echo SHtml::errorSummary($model); ?>
<div class="simple">
<h2>
<?php
$thisName=$model->name;
echo $thisName; 
?>
</h2>
</div>
<hr />
<br />
<br />
<div class="simple">
	<?php 
		HelpTool::selectActionDBConnect($model->name);
	?>
  </div>
  <br />
    <div class="action">
	<input type="submit"  value="保存" />
  <?php echo SHtml::endForm(); ?>
  </div>