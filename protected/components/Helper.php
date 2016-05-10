<?php
class Helper extends CController
{
    /*
	 *	截取字符串方法
	 */
	public static function truncate_utf8_string($string, $length, $etc = '...')
    {
        $result = '';
        $string = html_entity_decode(trim(strip_tags($string)), ENT_QUOTES, 'UTF-8');
        $strlen = strlen($string);
        for ($i = 0; (($i < $strlen) && ($length > 0)); $i++)
        {
        if ($number = strpos(str_pad(decbin(ord(substr($string, $i, 1))), 8, '0', STR_PAD_LEFT), '0'))
            {
            if ($length < 1.0)
                {
            break;
            }
            $result .= substr($string, $i, $number);
            $length -= 1.0;
            $i += $number - 1;
        }
            else
            {
            $result .= substr($string, $i, 1);
            $length -= 0.5;
        }
        }
        $result = htmlspecialchars($result, ENT_QUOTES, 'UTF-8');
        if ($i < $strlen)
        {
        $result .= $etc;
        }
        return $result;
    }
	
	

	/*
	 *	存储快捷方式方法
	 */
	public static function iShort()
	{
		/*
		 *	判断元素是否在多维数组中
		 */
		function array_multi_search($p_needle, $p_haystack)
		{  
			if(in_array($p_needle, $p_haystack))
			{
				return true;
			}
			foreach($p_haystack as $row)
			{
				if(@array_multi_search($p_needle, $row ))
				{
				return true;
				}
			}
			
			return false;
		} 
		

		
		
		$title='';
		$ru = $_SERVER['REQUEST_URI'];
		$qs = isset($_GET['r'])?$_GET['r']:'';
		$pdata = Yii::app()->params->powermenu;
		
		foreach($pdata['items'] as $key=>$val){
			if(isset($val['items'])){
				if($val['url']==$qs){
					$title = $val['label'];
				}
				if(isset($val['items'])){
					foreach($val['items'] as $k=>$v){
						if($v['url']==$qs){
							$title = $v['label'];
						}
						if(isset($v['items'])){
							foreach($v['items'] as $kk=>$vv){
								if($vv['url']==$qs){
									$title = $vv['label'];	
								}
							}
						}
					}
				}
			}
		}
	
		if(!empty($title) && $qs!='site/index'){
			$connection=Yii::app()->db;	
			
			$sql = "select * from `user_shortcut` where url='".$ru."'";
			$command = $connection->createCommand($sql);
			$rs = $command->queryRow();	
			$uname = Yii::app()->user->name;
			if(!$rs){
				$sql = "insert into `user_shortcut`(uname,url,title,hits) values('".$uname."','".$ru."','".$title."','1')";
				$connection->createCommand($sql)->execute();
			}
			else{
				$sql = "update `user_shortcut` set hits=hits+1 where uname='".$uname."' and url='".$ru."'";
				$connection->createCommand($sql)->execute();	
			}
		}
		
		
	}
	
}
?>
