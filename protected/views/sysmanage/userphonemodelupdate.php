<?php
	echo CHtml::openTag('ul',array('class'=>'table_menu'));
	echo CHtml::openTag('li',array('class'=>'li1')).CHtml::openTag('p',array('class'=>'p1')).CHtml::closeTag('p').CHtml::openTag('a',array('href'=>'index.php?r=sysmanage/userphonemodel')).'终端型号管理'.CHtml::closeTag('a').CHtml::openTag('p',array('class'=>'p2')).CHtml::closeTag('p').CHtml::closeTag('li');
	echo CHtml::openTag('li',array('class'=>'li1 select')).CHtml::openTag('p',array('class'=>'p1')).CHtml::closeTag('p').CHtml::openTag('a',array('href'=>'index.php?r=sysmanage/userphonemodel&md=update')).'终端型号同步'.CHtml::closeTag('a').CHtml::openTag('p',array('class'=>'p2')).CHtml::closeTag('p').CHtml::closeTag('li');	
	echo CHtml::closeTag('ul');
 ?>
	<p class="p_4"></p>
	<div id="loader_container1" style="display:none;">
		<div>
			<div>
			<img src="images/loading2.gif">
			数据计算中...
			</div>
		</div>
	</div>
<?php
		if(isset($_GET['error'])&&!isset($_GET['tabla_text'])&&!empty($_GET['error']))
			echo "<div class='errorSummary'>".json_decode($_GET['error'])."</div>";
		if(isset($_GET['success']))
			echo "<div class='errorSummary oprationseccess'>".json_decode($_GET['success'])."</div>";
		echo '<div class="main_table"><div class="home_p relation_table">'.CHtml::openTag('div',array('class'=>'td'));
		echo CHtml::openTag('span',array('class'=>'tag')).'终端型号同步是为了从控制端更新最新的终端信息库，请确定更新！更新数据表的时间可能较长，请耐心等待！'.CHtml::closeTag('span').'</br><b style="color:red">此操作需要更新数据库，请务必在系统维护期间执行此操作</b></br>';
		echo CHtml::closeTag('div');
		echo '<div class="updateSitePoints">';
	$form=$this->beginWidget('CActiveForm', 
		array(
			'id'=>'backup-form',
			'action'=>$this->createUrl('sysmanage/userphonemodel&md=update'),
		)
	);		
		echo 
			CHtml::submitButton('一键同步',
				array(
				'class'=>'sub',
				'style'=>'color:blue',
				'id'=>'onekeyback',
				'onclick'=>'{if(confirm("一键同步时建议不要进行其他操作,确定继续吗?")){$("#userphonemodelPage").dialog( "open" );}else{return false;}}'
				)
			);	
			echo '</div>';
	$this->endWidget();		
		echo '</div></div>';
?>
	<div id="userphonemodelPage" title="终端信息一键同步"></br></br></br>
		<div id="viewPageData">
			<h1 align="center">正在同步终端信息库，请稍候...</h1>
		</div>
	</div>