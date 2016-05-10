<?php 
set_time_limit(3600);



$con = mysql_connect("192.168.1.100","root","");
if (!$con){
  die('Could not connect: ' . mysql_error());
}else{
	//echo "connect success";	
}
mysql_select_db("mqs", $con);

	$sql = "CREATE TABLE if not exists net_gis 
	(
	id int(3) not null auto_increment,
	primary key(id),
	gid int(10),
	g_name varchar(255) not null,
	g_lat varchar(20) not null,
	g_lng varchar(20) not null,
	g_lac int(10) not null,
	g_cellId int(10) not null,
	tid int(10),
	t_name varchar(255) not null,
	t_lat varchar(20),
	t_lng varchar(20),
	t_lac int(10),
	t_cellId int(10),
	g_angle int(5),
	t_angle int(5),
	type int(2)
	)";
	if(mysql_query($sql,$con)){
		echo "创建成功";
	} 
	
	?>