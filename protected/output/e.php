<?php
require_once ('common/PHPExcel.php');
require_once ('common/PHPExcel/IOFactory.php');
require_once ('common/PHPExcel/Writer/IWriter.php');
require_once ('common/PHPExcel/Worksheet/Drawing.php');
require_once ('common/PHPExcel/Writer/Excel5.php');
require_once ('common/PHPExcel/Writer/Excel2007.php');

/**
 * 
 * 导出excel类
 * 
 * 调用方法

$data = array(
	array("name"=>"名子1","age"=>18),
	array("name"=>"名子2","age"=>2),
	array("name"=>"名子3","age"=>34),
	array("name"=>"名子4","age"=>22),
);
$cntxt = array('姓名','年龄');
$entxt = array('name','age');

require_once(Yii::app()->basePath.'/output/e.php');
$filename = "导出文件名";
$excel = new ChangeArrayToExcel(Yii::app()->basePath.'/../cache/'.$filename.'.xls');		
$excel->getExcel($data,$cntxt,$entxt,'other');	//导出表格
$url = 'index.php?r=site/download&fn='.json_encode($filename.'.xls');
echo "<iframe src='$url' style='display:none'></iframe>";

	 * 
 * 
 * @author 王鹏勇
 * @date 2013.5.22
 */
class ChangeArrayToExcel {
	
	private $excelName; //xls文件名，包括生成路径
	

	public function __construct($name = 'excel.xls') {
		if ($name != "") {
			$this->excelName = iconv ( "UTF-8", "gb2312", $name );
		}
	}
	/**
	 * 通过PHPExcel类生成Excel文件
	 * 
	 * @param Array $data 包含excel文件内容的数组
	 * @param Array $txArr 包含excel表头信息（中文)  例如array('编号',"姓名")
	 * @param Array $txArrEn excel表头信息（英文） 例如array('id','username')
	 * @param String $excelVersion 生成excel文件的版本  可选值为other,2007
	 * 
	 * @renturn String excel文件的绝对路径
	 */
	public function getExcel($data, $txArr, $txArrEn, $excelVersion = "other") {
		
		if (count ( $txArr ) != count ( $txArrEn ) && count ( $txArrEn ) != count ( $data ['0'] ) && ! empty ( $data )) {
			echo "表头数组错误，请仔细检查！";
			exit ();
		}
		
		$excelObj = new PHPExcel (); //实例化PHPExcel
		$excelObj->setActiveSheetIndex ( 0 );
		$objActSheet = $excelObj->getActiveSheet ();
		/*确定表头宽度，将表头内容添加到excel文件里*/
		foreach ( $txArr as $key => $value ) {
			$objActSheet->setCellValue ( $this->numToEn ( $key ) . "1", $value );
			$objActSheet->getStyle ( $this->numToEn ( $key ) . "1" )->getAlignment ()->setHorizontal ( PHPExcel_Style_Alignment::HORIZONTAL_CENTER );
			$objActSheet->getStyle ( $this->numToEn ( $key ) . "1" )->getFont ()->setBold ( true );
			$objActSheet->getColumnDimension ( $this->numToEn ( $key ) )->setwidth ( 25 );
		}
		
		/*将数据添加到excel里*/
		foreach ( $data as $key => $value ) {
			foreach ( $txArrEn as $k => $val ) {
				$objActSheet->setCellValueExplicit ( $this->numToEn ( $k ) . ($key + 2), $value [$val] ); //在写入Excels单元格的内容之前加一个空格，防止长数字被转化成科学计数法
			}
		}
		
		/*判断生成excel文件版本*/
		$objWriter = "";
		if ($excelVersion == "other") {
			$objWriter = new PHPExcel_Writer_Excel5 ( $excelObj );
		}
		if ($excelVersion == "2007") {
			$objWriter = new PHPExcel_Writer_Excel2007 ( $excelObj );
		}
		$objWriter->save ( $this->excelName );
		return $this->excelName;
	}
	
	/**
	 * 根据给定的数字生成至多两位对应EXCEL文件列的字母
	 */
	private function numToEn($num) {
		$asc = 0;
		$en = "";
		$num = ( int ) $num + 1;
		//判断指定的数字是否需要用两个字母表示
		if ($num <= 26){
			if (( int ) $num < 10) {
				$asc = ord ( $num );
				$en = chr ( $asc + 16 );
			} else {
				$num_g = substr ( $num, 1, 1 );
				$num_s = substr ( $num, 0, 1 );
				$asc = ord ( $num_g );
				$en = chr ( $asc + 16 + 10 * $num_s );
			}
		} else {
			$num_complementation = floor ( $num / 26 );
			$en_q = $this->numToEn ( $num_complementation - 1 );
			$en_h = $num % 26 != 0 ? $this->numToEn ( $num - $num_complementation * 26 - 1 ) : "A";
			$en = $en_q . $en_h;
		}
		return $en;
	}
}