<?php 
function netSig(){
	ignore_user_abort();
	set_time_limit(0);
	require_once(Yii::app()->basePath.'/extensions/functions.php');	
	
	$str=date('Y-m-d',strtotime("-1 day"))." 00:00";
	$stp=date('Y-m-d')." 23:59";
	
	/*$str=$_GET['str'];
	$stp=$_GET['stp'];*/
	$data=YII::app()->cache->get("sigdata"); 
	if($data==null){
		$color="";
		$connection=Yii::app()->db;	
		//普通小区
		$sql="select * from net_gis";
		$command=$connection->createCommand($sql);
		$rs=$command->queryAll();
		foreach($rs as $row){
			$lc1=0;$c1=0;$rate1=0;
			if($row['type']==1){
				$sql1="select * from dynamic_information where rssi<>0 and lac=".$row['g_lac']." and cellId=".$row['g_cellId']." and startDateTime>='".$str."' and startDateTime<='".$stp."'";
				$com1=$connection->createCommand($sql1);
				$rs1=$com1->queryAll();
				foreach($rs1 as $v1){
					if($v1['rssi']!=null){
						if($v1['rssi']<-90){
							$lc1++;
						}
						$c1++;
					}
				}
				if($c1!=0){
					$rate1=$lc1/$c1*100;	
				}
			}
			if($row['type']==2){
				$lc2=0;$c2=0;$rate2=0;
				$sql1="select * from dynamic_information where rssi<>0 and lac=".$row['t_lac']." and cellId=".$row['t_cellId']." and startDateTime>='".$str."' and startDateTime<='".$stp."'";;
				$com1=$connection->createCommand($sql1);
				$rs1=$com1->queryAll();
				foreach($rs1 as $v1){
					if($v1['rssi']!=null){
						if($v1['rssi']<-90){
							$lc2++;
						}
						$c2++;
					}
				}
				if($c2!=0){
					$rate2=$lc2/$c2*100;	
				}
				if($rate1>=20&&$rate2>=20){
					$color="red";
				}else if($rate1>=10&&$rate2>=10){
					$color="yellow";
				}else{
					$color="green";	
				}
			}else{
				if($rate1>=20){
					$color="red";
				}else if($rate1>=10){
					$color="orange";
				}else{
					$color="green";	
				}
			}
			$angle="";
			if($row['g_angle']>=0&&$row['g_angle']<120){
				$angle=1;
			}else if($row['g_angle']>=120&&$row['g_angle']<240){
				$angle=120;
			}else if($row['g_angle']>=240&&$row['g_angle']<360){
				$angle=240;
			}
			$comm['id']=$row['id'];	
			$comm['s_lng']=$row['g_lng'];
			$comm['s_lat']=$row['g_lat'];
			$comm['l_lng']=0;
			$comm['l_lat']=0;
			$comm['azimuth']=$angle;
			$comm['color']=$color;
			$data[]=$comm;
		}		
		//高铁小区
		$sql="select * from site_grru";
		$command=$connection->createCommand($sql);
		$rs=$command->queryAll();
		$gc=0;$glc=0;
		foreach($rs as $row){
			$sql1="select * from dynamic_information where rssi<>0 and lac=".$row['lac']." and cellId=".$row['cellId']." and startDateTime>='".$str."' and startDateTime<='".$stp."'";;
			$com1=$connection->createCommand($sql1);
			$rs1=$com1->queryAll();
			foreach($rs1 as $v1){
				if($v1['rssi']!=null){
					if($v1['rssi']<-90){
						$glc++;
					}
					$gc++;
				}
			}	
			$rate3=0;
			if($gc!=0){
				$rate3=$glc/$gc*100;	
			}
			if($rate3>=20){
				$color="red";
			}else if($rate3>=10){
				$color="orange";
			}else{
				$color="green";	
			}
			$comm['id']=$row['id'];	
			$comm['s_lng']=$row['s_lng'];
			$comm['s_lat']=$row['s_lat'];
			$comm['l_lng']=$row['l_lng'];
			$comm['l_lat']=$row['l_lat'];
			$comm['azimuth']=0;
			$comm['color']=$color;
			$data[]=$comm;
		}
		YII::app()->cache->set("sigdata", $strs,60*60*24*7);//存
	}
	return $data;
}
?>