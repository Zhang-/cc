<?php
//文件上传
function uploadfile($upfile,$upload_path,$redirect,$f_size="102400",$f_type="flv|jpg|jpeg|gif|png",$file_names=''){
	if(!file_exists($upload_path)){mkdir($upload_path,0777);chmod($upload_path,0777);}//检测文件夹是否存,不存在则创建;
	$file_name=basename($_FILES[$upfile]['name']);
	if(empty($file_name))return false;
	$file_type=$_FILES[$upfile]['type'];
	$file_size=$_FILES[$upfile]['size'];
	$file_tmp=$_FILES[$upfile]['tmp_name'];
	$file_error=$_FILES[$upfile]['error'];
	if($file_error==1){
		die("
		<script language='javascript'>
			 alert('你上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值');
			 location.href='".$redirect."';
		 </script>");
	}
	elseif($file_error==2){
		die("
		<script language='javascript'>
			 alert('你上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值');
			 location.href='".$redirect."';
		 </script>");	
	}
	elseif($file_error==3){
		die("
		<script language='javascript'>
			 alert('文件只有部分被上传');
			 location.href='".$redirect."';
		 </script>");	
	}
	elseif($file_error==4){
		die("
		<script language='javascript'>
			 alert('没有文件被上传');
			 location.href='".$redirect."';
		 </script>");	
	}
	
	$upload_dir=$upload_path;
	if($file_size>$f_size and $file_size==0){
		die("
		<script language='javascript'>
			 alert('对不起！您上传的文件超出规定大小\\n\\n1KB - ".number_format($f_size/1024)."KB');
			 location.href='".$redirect."';
		 </script>");
	}
	$ext=explode(".",$file_name);
	$sub=count($ext)-1;
	$ext_type=strtolower($ext[$sub]);//转换成小写
	$up_type=explode("|",$f_type);
	if(!in_array($ext_type,$up_type)){
		die("
		<script language=javascript>
			 alert('您上传的文件类型不符合要求！请重新上传！\\n\\n上传类型只能是".$f_type."。');
			 location.href='".$redirect."';
		</script>");
	}
	$file_names=$file_names?$file_names.".".$ext[$sub]:md5(time()).".".$ext[$sub];
	$upload_file_name=$upload_dir.$file_names;
	$chk_file=move_uploaded_file($file_tmp,$upload_file_name);
	if($chk_file){ //判断文件上传是否成功
		/*
		//当图片超过1600px*1200px时等比例缩放图片,但要注意必需配合image_auto()函数的存在;
		$type_arr=array("jpg","jpeg","gif","png");
		if(in_array($ext_type,$type_arr)){
			$list = getimagesize($upload_file_name);//获取上传文件的尺寸
			if($list[0]>1600 or $list[1]>1200){
				image_auto($upload_file_name,$upload_file_name,1600,1200);
			}
		}
		*/
		chmod($upload_file_name,0777);//设置上传文件的权限
		unset($ext[$sub]);$file_name=implode(".",$ext);//先去除扩展名,后获取文件名
		//返回一个数组[存盘文件名称:$file_names],[文件大小:$file_size],[文件扩展名:$ext_type],[去扩展名的文件名:$file_name]
		return array($file_names,$file_size,$ext_type,$file_name); 
	}else{
		return false;
	}
}

//生成网点数据xml
function createdbxml($version,$title='网点数据'){
	global $db,$sys_vars;
	set_time_limit(0);
	$db->query('select * from '.$sys_vars['db_pre'].'sitecolumn');
	while($db->next_record()){
		$colarr[$db->f('columnname')] = $db->f('description'); 
	}
	$db->query('show columns from '.$sys_vars['db_pre'].'site from '.$sys_vars['dbname']);
	$gdcul = array('aid'=>'aid','id'=>'id','name'=>'name','address'=>'address');
	while($db->next_record()){
		if(!in_array($db->f(0),$gdcul)){
			$arr[$db->f(0)] = $colarr[$db->f(0)];	
		}
	}
	$colarr = array_merge($gdcul,$arr);
	//unset($colarr['aid']);
	$db->query('select * from '.$sys_vars['db_pre'].'site');
	$xml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
	$xml .= '<site version="'.$version.'" title="'.$title.'">'."\n";
	while($db->next_record()){
		$xml .= "<item aid='".$db->f('aid')."' id='".$db->f('id')."' name='".$db->f('name')."' address='".$db->f('address')."'>"."\n";
		foreach($colarr as $k=>$v){
			if($k!='aid' && $k!='id' && $k!='name' && $k!='address'){
				$str = trim($db->f($k));
				$str = (empty($str))?' - ':$str;
				if(in_array($k,$gdcul)){
					$xml .= '<'.$v.'>'.$str.'</'.$v.'>'."\n";
				}
				else{
					$xml .= '<'.$k.' tag="'.$v.'">'.$str.'</'.$k.'>'."\n";
				}
			}
		}
		$xml .= "</item>"."\n";
	}
	$xml .= '</site>';
	
	$fp = fopen('update/xml/db_'.$version.'.xml','w');
	fwrite($fp,$xml);
	fclose($fp);
}


//判断表是否存在
function check_table_is_exist($sql,$find_table){
	global $db,$sys_vars;
	$db->query($sql);
    $database=array();
	while($db->next_record())
    {
         $database[]=$db->f(0);
    }
    unset($result,$row);
        
    /*开始判断表是否存在*/
    if(in_array($find_table,$database))
    {
     	return true;
    }
    else 
    {
     	return false;
    }
}

//生成用户xml
function createuserxml(){
	global $db,$sys_vars;
	$xml = '<?xml version="1.0" encoding="utf-8"?>';
	$xml .= '<users>';
	$db -> query('select * from '.$sys_vars['db_pre'].'user');
	while($db->next_record()){
		$xml .= '<user>';
		$xml .= '<id>'.$db->f('ID').'</id>';
		$xml .= '<name>'.$db->f('name').'</name>';
		$xml .= '<password>'.$db->f('password').'</password>';
		$xml .= '<sex>'.$db->f('sex').'</sex>';
		$xml .= '<occupation>'.$db->f('Occupation').'</occupation>';
		$xml .= '<district>'.$db->f('district').'</district>';
		$xml .= '<address>'.$db->f('address').'</address>';
		$xml .= '<gps>'.$db->f('gps').'</gps>';
		$xml .= '<telephone>'.$db->f('telephone').'</telephone>';
		$xml .= '<imsi>'.$db->f('imsi').'</imsi>';
		$str = trim($db->f('client'));
		$str = (empty($str))?' - ':$str;
		$xml .= '<client>'.$str.'</client>';
		$str = trim($db->f('imei'));
		$str = (empty($str))?' - ':$str;
		$xml .= '<imei>'.$str.'</imei>';
		$xml .= '<siteid>'.$db->f('siteid').'</siteid>';
		$xml .= '</user>';
	}
	$xml .= '</users>';
	
	$fp = fopen('update/xml/userxml.xml','w');
	fwrite($fp,$xml);
	fclose($fp);
}


?>