<?php 
	$form=$this->beginWidget('CActiveForm', 
		array(
			'action'=>Yii::app()->createUrl($this->route),
			'method'=>'get',
		)
	); 
?>

<?php 

	$getUserSearch = CacheFile::userSearch();//获取用户搜索缓存

?>

	<ul> 
		<li>
			<span class="behaviortime">注册时间</span>
			<input id="user_create_time" class="Wdate" type="text" name="ManageList[regDateTime]" value="<?php $thisTime='';if(isset($_GET['ManageList']['regDateTime'])){$thisTime=$_GET['ManageList']['regDateTime'];} echo $thisTime;?>" onclick="WdatePicker({dateFmt:'yyyy-MM-dd',isShowClear:false,isShowOK:false,onpicked:function(){$('#submit').click();},maxDate:'%y-%M-%d %H:%m:$s'})">
		</li>
		
		<li class="brand">
			<div class="sele_div"><input type="text"  name="ManageList[itemname]"  value="<?php $thisItemname="所有用户组"; if(isset($_GET['ManageList']['itemname'])){$thisItemname=$_GET['ManageList']['itemname'];} echo $thisItemname;?>"  readonly="readonly" /><span class="btn"></span></div>
			<div class="show_div">
				<span   <?php if(isset($_GET['ManageList'])){ if($_GET['ManageList']['itemname']=='所有用户组') echo 'class="select"';}else echo 'class="select"';?>>所有用户组</span>
				<?php
				if(!empty($getUserSearch['userrole']))
				{
					$slt = '';
					foreach($getUserSearch['userrole'] as $rolesKey=>$rolesVal)
					{
					if(isset($_GET['ManageList']))
						{
							if($_GET['ManageList']['itemname']==$rolesVal)
								$slt = 'class="select"';
							else
								$slt = '';
						}
						echo '<span '.$slt.' id="'.$rolesKey.'">'.$rolesVal.'</span>';
					}
				}
				?>
			</div>
		</li>
		
		<li class="brand">
			<div class="sele_div"><input type="text"  name="ManageList[province]"  value="<?php $thisProvince="所有省份"; if(isset($_GET['ManageList']['province'])){$thisProvince=$_GET['ManageList']['province'];} echo $thisProvince;?>" readonly="readonly" /><span class="btn"></span></div>
			<div class="show_div">
			<span  <?php if(isset($_GET['ManageList'])){ if($_GET['ManageList']['province']=='所有省份') echo 'class="select"';}else echo 'class="select"';?>>所有省份</span>
				<?php
				if(!empty($getUserSearch['address']))
				{
					foreach($getUserSearch['address'] as $ProvinceKey=>$ProvinceVal)
					{
						if($ProvinceKey!='')
						{
							if(isset($_GET['ManageList']))
							{
								if($_GET['ManageList']['province']==$ProvinceKey)
									$slt = 'class="select"';
								else
									$slt = '';
							}
							echo '<span '.$slt.' id="'.$ProvinceKey.'">'.$ProvinceKey.'</span>';
						}
					}
				}
				?>
			</div>
		</li>
	  
<?php
	$show='display:none';
	if(isset($_GET['ManageList'])&&$_GET['ManageList']['province']!="所有省份"){
		$show='';
	} 
?>  
	<li class="brand" style="<?php echo $show;?>">
			<div class="sele_div"><input type="text"  name="ManageList[city]"  value="<?php $thisCity="所有城市"; if(isset($_GET['ManageList']['city'])){$thisCity=$_GET['ManageList']['city'];} echo $thisCity;?>" /><span class="btn"></span></div>
			<div class="show_div">
				<?php
				
					if(isset($_GET['ManageList'])&&$_GET['ManageList']['province']!="所有省份")
					{
						$selectedProvince=$_GET['ManageList']['province'];
						$thisProvinceCity=$getUserSearch['address'][$selectedProvince];
						
						$classSelect='';
						if($_GET['ManageList']['city']=='所有城市')
						{
							$classSelect='class="select"';
						}
						
						echo '<span '.$classSelect.'>所有城市</span>';
						foreach($thisProvinceCity as $cityKey=>$cityVal)
						{
							if(isset($_GET['ManageList']))
							{
								if($_GET['ManageList']['city']==$cityVal)
									$slt = 'class="select"';
								else
									$slt = '';
							}
							if($cityVal) echo '<span '.$slt.' id="'.$cityVal.'">'.$cityVal.'</span>';
						}
					}
				?>
			</div>
		</li>


		
		<li class='p1'>
			<input name="ManageList[username]" class="searhbutton self_go" type="text" size="30" maxlength="255" value="<?php $thisUserName='请输入用户名进行搜索';if(isset($_GET['ManageList']['username'])){$thisUserName=$_GET['ManageList']['username'];} echo $thisUserName;?>" onclick="javascript:if(this.value=='请输入用户名进行搜索'){ this.value=''};" onblur="javascript:if(this.value==''){ this.value='请输入用户名进行搜索'};" />
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

<?php 
	$this->endWidget(); 
?>