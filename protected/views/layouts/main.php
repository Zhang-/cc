<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-1.7.2.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery-ui-1.8.20.custom.min.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/login.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/sysmanage.js"></script>
<script type="text/javascript" src="<?php echo Yii::app()->request->baseUrl; ?>/js/DatePicker/WdatePicker.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/ui-lightness/jquery-ui-1.8.20.custom.css" />
<link rel="stylesheet" type="text/css" href="<?php echo Yii::app()->request->baseUrl; ?>/css/main_in1.css" />
<title><?php echo CHtml::encode($this->pageTitle); ?></title>
</head>
<div style="display:none">
 <span class="x"></span>
 <span class="xx"></span>
 <span class="xxx"></span>
 <span class="xxxx"></span>
 <span class="xxxxx"></span>
</div>
<body 
<?php 
if(isset($_GET['r']) && $_GET['r'] == 'site/login'){echo 'class="sitelogin sitelogin2"';}else if(!isset($_GET['r']) || $_GET['r']=='site/index'){echo 'class="home"';}?> >
<div class="header" id="mainmenu">
 <div class="header1">
  <div class="theme" title="系统主题"></div>
  <script>
    var name=$.cookie("theme_name");
	 if(name)
	  {$('body').css("background",name)}
	 else
	 {}
   function mytheme(num){
        $('body').css("background",num)
	    $.cookie("theme_name",num,{path:'/',expirse:10});
  }
   $(function(){
      $('#theme div').each(function(){
	      var b;
		  var a=$(this).index();
		  $(this).attr("class","div"+a);
		  $(this).click(function(){
			b="url('images/background"+a+".jpg') fixed 0 0 / cover ";
			mytheme(b);
		  })
		  
      })
      
	  //
	   $('.theme').click(function(){
		$("#theme").dialog('open');
	   })
	   $( "#theme").dialog({
			autoOpen:false,
			width: 360,
			height: 185,
			modal: true,
			position: ['top','right'],
			resizable: false, 
			draggable :false
		});
	})
  </script>
  <div class="help">
		<div class="alertsListDiv" id="alertsListDiv">
		    <span style=""><?php echo Yii::app()->user->name;?></span>
			<span class="massage"></span>
			<ul class="alertsMenu">
			<?php $alerts = new Alerts(); echo $alerts->listAlerts();?>
			<li class="chilun"><a href="#">提醒设置</a></li>
			
			</ul>
			<div class="flow" style=""></div>
		</div>
		<a class="a1" title="注销" href="index.php?r=site/logout"></a>
		<a class="a2" title="清除缓存" href="index.php?r=site/clearcache&url=<?php echo Yii::app()->createUrl($this->route); ?>" onclick = "if(confirm('您真的要清除所有缓存吗?')){return true;}else{return false;} "></a>
   </div>
  <div class="logo">
    <p class="p1"></p>
    <p class="p2"></p>
  </div>
 </div>
 <div class="top1">
  <div class="top2">
  <?php
  
  CacheFile::getUserState(false); //更新帐号在线用户控制缓存
  HelpTool::getActionInfo(0,4);//日志信息，记录导航网址访问情况
  
  //CShort::iShort();
	
		$myRoleActions = array();
		$userSessionName = Yii::app()->params->userSessionName;
		
		if(!isset($_SESSION[$userSessionName]) && empty($_SESSION[$userSessionName]))
		{
			$jason=json_encode(HelpTool::getActionByID(Yii::app()->user->getId()));
			$_SESSION[$userSessionName] = $jason; //存储可访问action的SESSION
		}
		
		$myRoleActions = json_decode( $_SESSION[$userSessionName] );
		
		$pdata = Yii::app()->params->powermenu;
		
		foreach($pdata['items'] as $key=>$val){
		$firstMenuUrl=strtolower(str_replace('/','',$val['url']));
			$fm = explode(',',strtolower($val['tag']));
			if(in_array(strtolower($this->id),$fm)){
				$fit['active'] = true;	
			}
			else{
				$fit['active'] = false;	
			}
			//$fit['class'] = "123";
			$fit['label']='<p class="p0"></p><p class="p1"><span>'.$val['label'].'</span></p>';
			$fit['url'] = array('/'.$val['url']);
			$fit['visible'] = is_array($myRoleActions)?in_array($firstMenuUrl,$myRoleActions):!Yii::app()->user->isGuest;
			$fitems[] = $fit;
			//print_r ($fit);
		}
		
		$this->widget('zii.widgets.CMenu',array(
			'items'=>$fitems,
			'htmlOptions'=>array('class'=>'fir_menu'),
			'encodeLabel'=>false,
			'activeCssClass'=>'li_menu',
			'activateParents'=>true,
		)); ?>
  </div>
 </div>
</div>
<!-- mainmenu -->
<div class="left">
 <div class="to_left"></div>
 <div class='left1'>
  <div class='left2'>
  <?php
	
	foreach($pdata['items'] as $key=>$val){
		$fm = explode(',',strtolower($val['tag']));
		if(in_array(strtolower($this->id),$fm) && isset($val['items'])){
			foreach($val['items'] as $k=>$v){
			$secondMenuUrl=strtolower(str_replace('/','',$v['url']));
				$ft['label'] = '<span class="click_menu span_active">'.$v['label'].'</span>';
				$ft['url'] = array('/'.$v['url']);
				$ft['visible'] = is_array($myRoleActions)?in_array($secondMenuUrl,$myRoleActions):!Yii::app()->user->isGuest;
				$ft['linkOptions'] = array('class'=>'fmenu');
				$ft['submenuOptions'] = array('class'=>'show_menu');
				$tt[$k] = $ft;
				if(isset($v['items'])){
					foreach($v['items'] as $kk=>$vv){
					$thirdMenuUrl=strtolower(str_replace('/','',$vv['url']));
						$sit['label']=$vv['label'];
						$sit['url'] = array('/'.$vv['url']);
						$sit['visible'] = is_array($myRoleActions)?in_array($thirdMenuUrl,$myRoleActions):!Yii::app()->user->isGuest;
						$sit['template'] = '<span>{menu}</span>';
						
						$tt[$k]['items'][] = $sit;		
					}
				}
			}
			$sitems = $tt;
		}
	}
	
	
	if(isset($sitems)){
		$this->widget('zii.widgets.CMenu',array(
			'items'=>$sitems,
			'htmlOptions'=>array('class'=>'sec_menu'),
			'encodeLabel'=>false,
			//'firstItemCssClass'=>'show_active',
			'activeCssClass'=>'show_active',
			'activateParents'=>true,
		));
	}
	
	
	?>
 </div>
 <script>
  $('.fir_menu li').each(function(){
    var eq=$(this).index()
	$(this).addClass("icon"+eq)
  })
 
 </script>
 </div>
   <script type="text/javascript">
    var win_height=$(window).height();
    $('.sec_menu').height(win_height-220);
   </script>
</div>
<?php /*if(isset($this->breadcrumbs)):?>
		<?php $this->widget('zii.widgets.CBreadcrumbs', array(
			'links'=>$this->breadcrumbs,
		)); ?><!-- breadcrumbs -->
	<?php endif*/?>

  <div class="main">
    <div class="to_right"></div>
    <div class="round_top">
	   <div class="left_r"></div>
	   <div class="center_r"></div>
	   <div class="right_r"></div>
	</div>
    <div class="main2">
      <div class="main1">
       <?php echo $content; ?>
      </div>
	   <script type="text/javascript">
   $('.main_table .hello,.biao,.biao1,#tag_grid,.hellos .form,#map,#openlayer_map1').wrap("<div class='nano'><div class='content' ></div></div>")
   $('.main1').not('.home .main1').height(win_height-133);



 
 </script>
	
    </div>
 <div class="round_bottom">
    <div class="left_r"></div>
	<div class="center_r"></div>
	<div class="right_r"></div>
 </div>
 </div>


<div id='theme' title='我的主题' style="display:none">
  <div></div>
  <div></div>
  <div></div>
  <div style="display:none"></div>
  <div style="display:none"></div>
</div>

</body>
</html>