<?php 
set_time_limit(1200);
$con = mysql_connect("192.168.1.215","root","");
if (!$con){
  die('Could not connect: ' . mysql_error());
}
mysql_select_db("mqs", $con);
/*
$s="drop table if exists net_gis_test";
$aa=mysql_query($s,$con);
if($aa){
	//echo "drop sucess"."<hr />";	
}
exit;
*/
$sql = "CREATE TABLE if not exists net_gis_test 
(
id int(3) not null auto_increment,
primary key(id),
g_lat varchar(20) not null,
g_lng varchar(20) not null,
g_lac int(10) not null,
g_cellId int(10) not null,
t_lat varchar(20),
t_lng varchar(20),
t_lac int(10),
t_cellId int(10),
g_angle int(5),
t_angle int(5),
type int(2)
)";
mysql_query($sql,$con); 
//exit;
$sql1 ="select * FROM `site_gsm`";
$rs1 = mysql_query($sql1,$con);
$count=0;
//while(($v = mysql_fetch_array($rs1))&&$count<10){
while(($v = mysql_fetch_array($rs1))&&$count<100){
	$flag=0;
	
	$lat1=$v['lat']-0.000389000;
	$lat2=$v['lat']+0.000389000;
	$lng1=$v['lng']-0.000389000;
	$lng2=$v['lng']+0.000389000;
	$sql2 ="select * FROM `site_td` where lat>=".$lat1." and lat<".$lat2." and lng>=".$lng1." and lng<".$lng2;
	$rs2 = mysql_query($sql2,$con);
	while($v2 = mysql_fetch_array($rs2)){
			if($v2['angle']>=($v['angle']-60)&&$v2['angle']<=($v['angle']+60)){
				$csql="insert into net_gis_test(g_lat,g_lng,g_lac,g_cellId,t_lat,t_lng,t_lac,t_cellId,g_angle,t_angle,type) values('".$v['lat']."','".$v['lng']."','".$v['lac']."','".$v['cellId']."','".$v2['lat']."','".$v2['lng']."','".$v2['lac']."','".$v2['cellId']."','".$v['angle']."','".$v2['angle']."','2')"; 
				$result2=mysql_query($csql,$con);
			}else{
				if($flag==0){
					$csql="insert into net_gis_test(g_lat,g_lng,g_lac,g_cellId,g_angle,type) values(".$v['lat'].",".$v['lng'].",".$v['lac'].",".$v['cellId'].",".$v['angle'].",1)";
					$result1=mysql_query($csql,$con);
				}
			}
	}	
	$count++;
}
mysql_close($con);
?>