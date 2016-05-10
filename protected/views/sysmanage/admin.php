
<?php
	require_once(Yii::app()->basePath.'/extensions/pageFunction.php'); 
?>
	<ul class="table_menu">
	  <li class="select">
	  <p class="p1"></p>
	  <a href="<?php echo Yii::app()->createUrl($this->route);?>">数据备份</a>
		<p class="p2"></p>
	  </li>
	</ul>
	
	<div class="main_table">
		<div class="table_search">
			<?php 
				$this->renderPartial('_searchDbBackup'); 
			?>
		<div class="clear"></div>
		</div>
		<div id="static-information-grid" class="grid-view hello">
		<table class="items">
			<thead>
				<tr>
					<?php 
						getChecksNameSA();
					?>
					<th  align="center"  ><span class="STYLE1">备份记录</span></th>
					<th  align="center"  >文件大小</th>
					<th  align="center"  >备份日期</th>
				</tr>
			</thead>
			
		<?php 		
			$thisURL=Yii::app()->createUrl($this->route);
			getBackupTable($data,$thisURL);
		?>
</div>
    </div>

	<div style="display:none" id="dbBackupPage" title="数据库备份"></br></br></br>
		<div id="viewPageData">
			<h1 align="center" style="">正在备份数据库，请稍候...</h1>
		</div>
	</div>
	
	<div id="autoBackup" title="自动备份设置">
	  <iframe src="" marginwidth="0" height="235" width="100%" marginheight="0"  frameborder="0" id = "autoBackFrame" class="dataview UserViewPage"></iframe>
	</div>

