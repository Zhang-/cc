<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends SBaseController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $menu=array();
	/**
	 * @var array the breadcrumbs of the current page. The value of this property will
	 * be assigned to {@link CBreadcrumbs::links}. Please refer to {@link CBreadcrumbs::links}
	 * for more details on how to specify this property.
	 */
	public $breadcrumbs=array();

	//redis short code
	//public $redis = Yii::app()->cache;
	
	/**
	piehighcharts
	*/
	public function actionPieHighCharts()
	{
		$highcharts=new PieHighCharts;	
		$highcharts->creathighcharts();
	}

	/** 
	  * @name   输出json数据
	  *
	  * @param  array  $data   //返回数据
	  * @param  int    $status //服务状态(success=200)
	  * @param  string $msg    //错误信息($status!=200)
	  * @return json   $output //json数据
	  * 
	  * @author zhanghy
	  * @date 2015-06-22 02:27:02
	  *
	  **/
	public function _outPut($data, $status = 200, $msg = '')
	{
		$outPut = array('s'=>$status);

		if ($status === 200)
			$outPut['d'] = $data;
		else
			$outPut['m'] = $msg;

		HelpTool::logTrace('CallCar Api REQUEST', ['REQUEST'=>$_REQUEST, 'status'=>$outPut], false); //log request data

		echo json_encode($outPut);exit;
	}

	/** 
	  * @name   输出错误数据
	  *
	  * @param  int   $eCode   //错误代码
	  * @param  int   $eStatus //服务状态(success=500)
	  * @return json   $output //json数据
	  * 
	  * @author zhanghy
	  * @date 2015-6-29 23:08:37
	  *
	  **/
	public function _doError($eCode = 0, $eStatus = 500)
	{
		$errorInfo = Yii::app()->params->error;
		isset($errorInfo[$eCode]) && $errorInfo = $errorInfo[$eCode];
		$this->_outPut([], $eStatus, $errorInfo);
	}
	
	/**
	 *   保存init.php配置文件
	 */
	static function saveInit($array){
		$file = Yii::app()->basePath.'/config/init.php';
		$init = require $file;
		$str = var_export(array_merge($init['params'],$array),true);
		$str = 
"<?php
/**
 *	自定义配置,到main.php手动写配置,这个文件程序会动态修改它
 */
return array('params'=>{$str});";
		$fp = fopen($file,'w');
		fwrite($fp,$str);
		fclose($fp);
	}
	
}