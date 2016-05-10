<?php 
function newTable(){
	set_time_limit(3600);
	
	$con = mysql_connect("192.168.1.100","root","");
	if (!$con){
	  die('Could not connect: ' . mysql_error());
	}
	else{ echo "connect success";}
	exit;
	
	mysql_select_db("mqs", $con);
	
	$count=0;
	$num=0;
	$td=array();
	$flag=0;
	$a=0.0004;
	//查找出已经匹配td小区的gsm小区的id
	$strs="00";
	$sql ="select gid FROM `net_gis` where type=2";
	$rs = mysql_query($sql,$con);
	while($row = mysql_fetch_array($rs)){
		$strs.=",".$row['gid'];
	}
	//查找出已经匹配gsm小区的td小区的id
	$strt="00";
	$sql ="select tid FROM `net_gis` where type=2";
	$rs = mysql_query($sql,$con);
	while($row = mysql_fetch_array($rs)){
		$strt.=",".$row['tid'];
	}
	while($flag==0){	
		//以td小区为出发点，循环
		$sql1 ="select * FROM `site_td` where id not in (".$strt.")";
		$rs1 = mysql_query($sql1,$con);
		while($v = mysql_fetch_array($rs1)){
			$gsm=array();
			$tf=0;
			if(in_array($v['id'],$td)==false){//如果这个td小区没有找到对应的gsm小区，则进入
				$lat1=$v['lat']-$a;
				$lat2=$v['lat']+$a;
				$lng1=$v['lng']-$a;
				$lng2=$v['lng']+$a;
				//检索没有匹配到td小区的gsm小区信息
				$sql2 ="select * FROM `site_gsm` where lat>=".$lat1." and lat<".$lat2." and lng>=".$lng1." and lng<".$lng2." and id not in (".$strs.")";
				$rs2 = mysql_query($sql2,$con);
				while(($v2 = mysql_fetch_array($rs2))&&$tf==0&&(in_array($v2['id'],$gsm)==false)){//如果这个gsm小区没有被td小区匹配，则进入
					if($v2['angle']>=($v['angle']-60)&&$v2['angle']<($v['angle']+60)){
						$csql="insert into net_gis(gid,g_lat,g_lng,g_lac,g_cellId,tid,t_lat,t_lng,t_lac,t_cellId,g_angle,t_angle,type) values('".$v2['id']."','".$v2['lat']."','".$v2['lng']."','".$v2['lac']."','".$v2['cellId']."','".$v['id']."','".$v['lat']."','".$v['lng']."','".$v['lac']."','".$v['cellId']."','".$v2['angle']."','".$v['angle']."','2')"; 
						mysql_query($csql,$con);
						$gsm[]=$v2['id'];
						$td[]=$v['id'];
						$tf=1;
						$strs.=",".$v2['id'];
						$strt.=",".$v['id'];
						$count++;
					}
				}
			}
		}
		$sa ="select count(*) as count FROM `site_td` where id not in (".$strt.")";
		$ra = mysql_query($sa,$con);
		$v = mysql_fetch_array($ra);
		if($v['count']==0){
			$a+=0.0001;
		}else{
			$flag=1;	
		}
	}
	$s ="select * FROM `site_gsm` where id not in (".$strs.")";
	$r = mysql_query($s,$con);
	while($row = mysql_fetch_array($r)){
		if(in_array($row['id'],$gsm)==false){
			$csql="insert into net_gis(gid,g_lat,g_lng,g_lac,g_cellId,g_angle,type) values('".$row['id']."','".$row['lat']."','".$row['lng']."','".$row['lac']."','".$row['cellId']."','".$row['angle']."','1')";
			$result1=mysql_query($csql,$con);
			$num++;
		}
	}
	$cc=mysql_close($con);
	if($cc){
		echo "update sucess"."<hr />";	
	}
}
?>