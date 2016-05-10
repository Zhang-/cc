<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/highcharts-3.0.8.js"></script>
<?php 
$this->pageTitle=Yii::app()->name; 
$uname = Yii::app()->user->name;
$connection=Yii::app()->db;
?>
<div class="index">
	<div class='index_one'>
		<div class="index_wel">
			<div class="index_bg0">
				<p class='index_ti'>登录信息</p>
			    <div class="welcome">
					<span class="sp1"><?php echo Yii::app()->user->name;?></span>
					<span>您好 , 欢迎使用 <b>新能源货车助手</b> 管理系统</span>
			    </div>
			    <div class="time">
				  <span class="sp1">您上次登录的时间:</span>
				  <span><?php echo isset($_SESSION['lastLoginTime']) ? $_SESSION['lastLoginTime'] : HelpTool::getUserLastLoginTime() ;?>
				  </span><span style="padding:0 0 0 6px">(不是您登陆的？<a href="javascript:void(0);" id="chgpwd">修改密码</a>)</span>
				  <div class="clear"></div>
				  <p>当前版本:<?php if(isset(Yii::app()->params['version'])) echo Yii::app()->params['version'].Yii::app()->params['env'].Yii::app()->params['dev'];?>
				  </P>
			    </div>
			</div>
		</div>
	    <div class="links"> 
	        <div class="index_bg0">
				<p class='index_ti'>MQS安卓客户端下载</p>
		        <ul class="fast_btn">
	          	<li class="down">
			    <?php
				if(isset(Yii::app()->params->city)){
					$city=Yii::app()->params->city;
				}else{
					$city=7;
				}
			    ?>
			    <span>1.直接下载</span>
			    <?php $appUrl = Yii::app()->params->apk_address.$city;?>
			    <a href="<?php echo $appUrl;?>" title="客户端下载"></a>
		        </li>
				<li>
					<span>2.二维码扫描</span>
					 <?php
					   QRcode::png($appUrl, Yii::app()->basePath."/../cache/appDown.png",'L',4,0);     //生成png图片	
					   
					   echo '<img src="'.Yii::app()->request->baseUrl.'/cache/appDown.png" style="" />'; 
				   ?>
				</li>
				<?php
					/* $rs = isset( $_SESSION['oftenUseUrl'] ) ? $_SESSION['oftenUseUrl'] : CShort::getOftenUseUrl();
					
					foreach($rs as $key=>$val)
					{
						$thisUrl=strtolower(str_replace('/','',$val['url']));
						if(HelpTool::checkActionAccess($thisUrl))
						{ */
				?>
					<!--<li>
						<a href="<?php //echo  Yii::app()->controller->createUrl($val['url'])?>" title="<?php //echo $val['title'];?>">
						<?php //echo CShort::truncate_utf8_string($val['title'],4,'');?></a>
					</li>-->
				<?php
						/* }else{
							continue;
						}	
					} */
				?>
			    <div class="clear"></div>
		        </ul>
	        </div>
	    </div>
	    <div class='clear'></div>
	</div>
	<!--<div class="index_one">-->
	
</div>
 <script>	
    //圆角
    $('.index_bg0').wrap('<div class="index_bg2"><div class="index_bg1"></div></div>')
    $('.index_bg2').before("<div class='round_top'><div class='left_r'></div><div class='center_r'></div><div class='right_r'></div></div>");
	$('.index_bg2').after("<div class='round_bottom'><div class='left_r'></div><div class='center_r'></div><div class='right_r'></div></div>")
    $(function () {
    });
	</script>
  <div id="dialog" title="查看详情">
		<div class="alert_bg" width="750" height="250"></div>
    </div>
  <script type="text/javascript">
	$('#dialog').dialog({autoOpen: false,width: 750,modal: true});
	$('#chgpwd').click(function(){
		var url = '<?php echo  Yii::app()->controller->createUrl('site/changpwd');?>';
		$("#dialog").dialog("open");
		$.ajax({
			type: 'POST',
			url: url,
			dataType: 'html',
			success: function(data){
				$('.alert_bg').html(data);
			}
		});	
	});
  </script>

  
  
