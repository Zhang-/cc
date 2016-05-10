<?php 
if(isset($_GET['StaticInformation'])){
			$sa = $_GET['StaticInformation'];
			$str=$sa['startDateTime']; 
			$stp=$sa['stopDateTime'];
		}else{
			$str=date('Y-m-d',strtotime("-1 day")).' 00:00';
			$stp=date('Y-m-d').' 23:59';
		}
		
		$sid=array();
		$lat=0;
		$lng=0;
		$angle=0;
		$i=0;
		$connection=Yii::app()->db;	
		$sql="select * from net_gis";
		$command=$connection->createCommand($sql);
		$rs=$command->queryAll();
		foreach($rs as $v){
			if($v['type']==2){
				$sql="select * from dynamic_information where (lac=".$v['g_lac']." and cellId=".$v['g_cellId'].") or (lac=".$v['t_lac']." and cellId=".$v['t_cellId'].") and startDateTime between '".$str."' and '".$stp."' group by voiceID,dataID";
			}else{
				$sql="select * from dynamic_information where lac=".$v['g_lac']." and cellId=".$v['g_cellId']." and startDateTime between '".$str."' and '".$stp."' group by voiceID,dataID";
			}
			$count=0;
			$command=$connection->createCommand($sql);
			$rs=$command->queryAll();
			foreach($rs as $row){
				if($row['voiceID']!=0){
					//语音
					$sql1="select staticID from voice_service where id=".$row['voiceID']." and ((startDateTime >= '".$str."' or stopDateTime <= '".$stp."') or (startDateTime < '".$str."' and stopDateTime > '".$stp."'))";
					$command1=$connection->createCommand($sql1);
					$rs1=$command1->queryRow();
					if($rs1['staticID']!=null&&in_array($rs1['staticID'],$sid)==false){
						$sid[]=$rs1['staticID'];
						$count++;
					}
				}
				if($row['dataID']!=0){
					//数据
					$sql2="select staticID from data_service where id=".$row['dataID']." and ((startDateTime >= '".$str."' or stopDateTime <= '".$stp."') or (startDateTime < '".$str."' and stopDateTime > '".$stp."'))";
					$command2=$connection->createCommand($sql2);
					$rs2=$command2->queryRow();
					if($rs2['staticID']!=null&&in_array($rs2['staticID'],$sid)==false){
						$sid[]=$rs2['staticID'];
						$count++;
					}
				}			
			}
			$comm['id']=$i;	
			$comm['s_lng']=$v['g_lng'];
			$comm['s_lat']=$v['g_lat'];
			$comm['l_lng']=0;
			$comm['l_lat']=0;
			$comm['azimuth']=$angle;
			$color="";
			if($count<=10){
				$color="red";
			 }else if($count>10&&$count<=30){
				$color="yellow";
			 }else if($count>40){
				$color="green";
			 }
			 if($count==0){
				 $color="blue";
			 }
			$comm['color']=$color;
			$data[]=$comm;
			$i++;
		}		
		YII::app()->cache->set('userdata',$data,60*60*24*7);//存
//$data=YII::app()->cache->get("userdata"); 
	print_r($data);exit;
?>