<?php
	$gis_grid_config = Yii::app()->params->gis_grid;
	if( !empty($mysql_GridTbIsExist) ){
		$mysql_GridTbIsExist ='true';//表示存在名为grids_information的数据库
	}else{
		$mysql_GridTbIsExist ='false';//表示不存在名为grids_information的数据库
	}
	if($pg_GridTbIsExist!=false){
		$pg_GridTbIsExist ='true';
	}else{
		$pg_GridTbIsExist ='false';
	}
?>

<ul class='table_menu'>
	<li class="select">
		<p class="p1"></p>
		<a href="<?php echo Yii::app()->createUrl($this->route);?>">创建栅格数据库</a>
		<p class="p2"></p>
	</li>
</ul>

<p class="shange_p">点击<b>创建栅格数据库</b>,会为mySQL数据库中的表"grids_information"生成栅格网格单元的基本信息,同时为地理数据库postgreSQL中的表"grids_市名首字母"（如泰州为grids_tz）生成栅格网格单元的基本信息,为地图提供数据！创建数据表的时间可能较长，请耐心等待！</br>
<b style="color:red">此操作期间地图功能可能失效，请务必在系统维护期间执行此操作</b></br>
<input class="shange" id="submit"  type="button" value="创建栅格数据库" onclick="create_griddb()"/>
</p>


<script type="text/javascript"> 

function create_griddb(){
	var mysql_GridTbIsExist= '<?php echo $mysql_GridTbIsExist ?>'; 
	var pg_GridTbIsExist= '<?php echo $pg_GridTbIsExist ?>'; 
	var p_grid_tbname = '<?php echo $gis_grid_config['p_grid_tbname']; ?>';
	var m_grid_tbname = '<?php echo $gis_grid_config['m_grid_tbname']; ?>';
	if( pg_GridTbIsExist=='false' )
	{
		alert('请先在postgreSQL中建立'+ p_grid_tbname +'表');
	}
	else if( pg_GridTbIsExist=='true' )
	{
		var isOK = false;
		if(mysql_GridTbIsExist=='true' ){
			if(confirm('您的操作会清空数据表'+ m_grid_tbname +'和'+ p_grid_tbname +'中的原有数据，您确定执行此操作吗?')){
				isOK=true;
			}
		}else if(mysql_GridTbIsExist=='false'){
			if( confirm('您的操作会清空数据表'+ p_grid_tbname +'中的原有数据，您确定执行此操作吗?')){
				isOK=true;
			}
		}
		if(isOK == true ){
			var params = {
				province: '<?php echo $gis_grid_config['province']; ?>',
				city: '<?php echo $gis_grid_config['city']; ?>',
				minLat: <?php echo $gis_grid_config['minLat']; ?>,
				maxLat: <?php echo $gis_grid_config['maxLat']; ?>,
				minLon: <?php echo $gis_grid_config['minLon']; ?>,
				maxLon: <?php echo $gis_grid_config['maxLon']; ?>, 
				deltaLon: <?php echo $gis_grid_config['deltaLon']; ?>,  
				deltaLat: <?php echo $gis_grid_config['deltaLat']; ?>,
				mysql_GridTbIsExist: mysql_GridTbIsExist
			};
			$.ajax({
				type : 'post',
				url :	'index.php?r=Sysmanage/Gis_NewGriddb',
				data : params,
				success : function(){
					alert('栅格数据库初始化成功！');
				}
			});  
		}
	}	
}
</script> 