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
				$forcountdata=new CActiveDataProvider($model, array(
					'criteria'=>$criteria));
				$tollno=$forcountdata->TotalItemCount;//总数
				$column='phoneBrand';
				$criteria->group=$column;	
				$modelas=$model->findAll($criteria);
				$_data=array();	
				foreach($modelas as $k=>$v)
				{
					$criteria=new CDbCriteria;
					$criteria->addcondition($column."='".$v->attributes[$column]."'");	
					$dataaa=new CActiveDataProvider($model, array(
					'criteria'=>$criteria));
					$criteria1=new CDbCriteria;
					$criteria1->addcondition($column."='".$v->attributes[$column]."'");
					$criteria1->group='phoneModel';
					$seconddata=new CActiveDataProvider($model, array(
					'criteria'=>$criteria1));					
					foreach($seconddata->data as $val)
					{
						$second[]="'".$val->attributes['phoneModel']."'";
						$criteria2=new CDbCriteria;
						$criteria2->addcondition("phoneModel='".$val->attributes['phoneModel']."'");	
						$thirddata=new CActiveDataProvider($model, array(
						'criteria'=>$criteria2));
						$percent[]="'".round($thirddata->TotalItemCount/$dataaa->TotalItemCount,4)*(100)."%'";
					}
					array_push($_data,"{y:'".round($dataaa->TotalItemCount/$tollno,4)*(100)."%',color:colors[".$k."],drilldown:{name:'".$v->attributes[$column]."',
					categories:[".implode(',',$second)."],data:[".implode(",",$percent)."],color:colors[".$k."]}}");					
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
			$criteria=new CDbCriteria;
			$firstdata=new CActiveDataProvider($model, array(
					'criteria'=>$criteria));
			foreach($firstdata->data as $v)
			{
				$connection=Yii::app()->db;
				$sql='select phoneBrandCN from phone_brand where phoneBrand="'.$v->attributes['phoneBrand'].'"';
				$command = $connection->createCommand($sql);
				//查询总手机的数量
				$row1=$command->queryRow();
				$n_data[]="'".$row1['phoneBrandCN']."'";
			}			
			$_data=getdata('');
			$_data=implode(',',$_data);		
			
			echo CHtml::openTag('div',array('id'=>'container','style'=>'min-width: 400px; height: 400px; margin: 0 auto')).CHtml::closeTag('div');
			$js="$(function () {
				var chart;
			$(document).ready(function() {
			var colors = Highcharts.getOptions().colors,
            categories = [".implode(',',$n_data)."],
            name = 'Browser brands',
            data = [".$_data."];
        // Build the data arrays
        var browserData = [];
        var versionsData = [];
        for (var i = 0; i < data.length; i++) {
    
            // add browser data
            browserData.push({
                name: categories[i],
                y: data[i].y,
                color: data[i].color
            });
    
            // add version data
            for (var j = 0; j < data[i].drilldown.data.length; j++) {
                var brightness = 0.2 - (j / data[i].drilldown.data.length) / 5 ;
                versionsData.push({
                    name: data[i].drilldown.categories[j],
                    y: data[i].drilldown.data[j],
                    color: Highcharts.Color(data[i].color).brighten(brightness).get()
                });
            }
        }
    
        // Create the chart
        chart = new Highcharts.Chart({
            chart: {
                renderTo: 'container',
                type: 'pie'
            },
			credits: {
            text: ''
            
        },
            title: {
                text: ''
            },
            yAxis: {
                title: {
                    text: ''
                }
            },
            plotOptions: {
                pie: {
                    shadow: false
                }
            },
            tooltip: {
        	    valueSuffix: '%'
            },
            series: [{
                name: '占总手机数量的',
                data: browserData,
                size: '60%',
                dataLabels: {
                    formatter: function() {
                        return this.y > 5 ? this.point.name : null;
                    },
                    color: 'white',
                    distance: -30
                }
            }, {
                name: '占总手机数量的',
                data: versionsData,
                innerSize: '60%',
                dataLabels: {
                    formatter: function() {
                        // display only if larger than 1
                        return this.y > 1 ? '<b>'+ this.point.name +':</b> '+ this.y +'%'  : null;
                    }
                }
            }]
        });
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