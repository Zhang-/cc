<?php 
class PieHighCharts
{	
	public $showtag='phoneBrand';
	public function creathighcharts()
	{	
		function getdata($name)
			{
				$model=new StaticInformation;
				$criteria=new CDbCriteria;
				$column='phoneBrand';
				$criteria->group=$column;	
				$modelas=$model->findAll($criteria);
				$_data=array();	
				foreach($modelas as $v)
				{
					$criteria=new CDbCriteria;
					$criteria->addcondition($column."='".$v->attributes[$column]."'");	
					$dataaa=new CActiveDataProvider($model, array(
					'criteria'=>$criteria));
					array_push($_data,"['".$v->attributes[$column]."',".$dataaa->TotalItemCount."]");
					$listdata[$v->attributes[$column]]=$dataaa->TotalItemCount;
				}
				if(empty($name))
					return $_data;
				else
					return $listdata[$name];
			}
		
		function getperdata($name)
			{	
				$model=new StaticInformation;
				$criteria=new CDbCriteria;
				$dataaa=new CActiveDataProvider($model, array(
					'criteria'=>$criteria));
				$tollno=$dataaa->TotalItemCount;
				$no=getdata($name);
				return $per=round($no/$tollno,4)*(100).'%';
			}
	
	
			$model=new StaticInformation;
			$_data=getdata('');
			$_data=implode(',',$_data);		
			$tagtans=$model->attributeLabels();
			echo CHtml::openTag('div',array('id'=>'container1','style'=>'height:400px;width:500px')).CHtml::closeTag('div');
			$js="var chart;
				$(document).ready(function() {
					chart = new Highcharts.Chart({
						chart: {
							renderTo: 'container1',
							plotBackgroundColor: null,
							plotBorderWidth: null,
							plotShadow: false
						},
						//用来设置是否显示'打印','导出'等功能按钮，不设置时默认为显示 
						//导出图片的URL，默认导出是需要连到官方网站去的哦
						 exporting:{ 
              			  	enabled: true  ,
							url: 'http://export.highcharts.com/'
						}, 
						title: {
							text: '".$tagtans[$this->showtag]."'
						},
						tooltip: {
							formatter: function() {
								return '<b>'+ this.point.name +'</b>: '+ this.percentage +' %';
							}
						},
						plotOptions: {
							pie: {
								allowPointSelect: true,
								cursor: 'pointer',
								dataLabels: {
									enabled: true
								},
								showInLegend: true
							}
						},
						series: [{
							type: 'pie',
							name: 'Browser share',
							data: [
								".$_data."
							]
						}]
					});
				});";
			echo CHtml::script($js);
			$criteria1=new CDbCriteria;
			$criteria1->group=$this->showtag;
			$prod=new CActiveDataProvider($model, array(
			'criteria'=>$criteria1,
			));
			
			
					
			
			Yii::app()->controller->widget('zii.widgets.grid.CGridView', array(
			'id'=>'showpielist',
			'dataProvider'=>$prod,
			'itemsCssClass'=>'table_content small',
			'columns'=>array(
				'phoneBrand',
				array('name'=>'数量','value'=>'getdata($data->phoneBrand)'),
				array('name'=>'百分比','value'=>'getperdata($data->phoneBrand)'),
			),
			'summaryText'=>'第 {start}-{end} 条, 共 {count} 条 当前第 {page} 页，共 {pages} 页',
			'pager'=>array(
				'class'=>'CLinkPager',
				'header'=>'',
				'firstPageLabel'=>'首页',
				'prevPageLabel'=>'上一页',
				'nextPageLabel'=>'下一页',
				'lastPageLabel'=>'尾页'
			),
			'template'=>'{items}{pager}'	
		));
	}
}
?>