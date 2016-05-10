<?php

/**
 * This is the model class for table "static_information".
 *
 * The followings are the available columns in table 'static_information':
 * @property integer $id
 * @property string $startDateTime
 * @property integer $time
 * @property string $mobileType
 * @property string $phoneBrand
 * @property string $phoneModel
 * @property string $appVersion
 * @property string $cpuName
 * @property double $cpuSpeed
 * @property integer $memory
 * @property string $osVersion
 * @property string $imsi
 * @property string $imei
 * @property string $baseband
 * @property string $modified_at
 * @property integer $active_count
 * @property integer $operator_list_id
 * @property integer $province_city_id
 */
class StaticInformation extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return StaticInformation the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'static_information';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('startDateTime, time, modified_at', 'required'),
			array('time, memory, active_count, operator_list_id, province_city_id, netAggregation, dataNetAggregation', 'numerical', 'integerOnly'=>true),
			array('cpuSpeed', 'numerical'),
			array('mobileType, phoneBrand, phoneModel, imsi, imei', 'length', 'max'=>30),
			array('appVersion, osVersion', 'length', 'max'=>10),
			array('cpuName, baseband', 'length', 'max'=>50),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, startDateTime, time, mobileType, phoneBrand, phoneModel, appVersion, cpuName, cpuSpeed, memory, osVersion, imsi, imei, baseband, modified_at, active_count, operator_list_id, province_city_id, terminal_config_content_change, terminal_config_time, netType, support_netType, change_time, auto_operator_list_id, netAggregation, dataNetAggregation', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			'complainClients' => array(self::HAS_MANY, 'ComplainClient', 'staticID'),
			'userNetAnalysises' => array(self::HAS_MANY, 'UserNetAnalysis', 'staticId'),
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'startDateTime' => 'Start Date Time',
			'time' => 'Time',
			'mobileType' => '运营商',
			'phoneBrand' => '终端品牌',
			'phoneModel' => '终端型号',
			'appVersion' => 'App Version',
			'cpuName' => 'Cpu型号',
			'cpuSpeed' => 'Cpu Speed',
			'memory' => 'Memory',
			'osVersion' => '系统版本信息',
			'imsi' => 'IMSI',
			'imei' => 'IMEI',
			'baseband' => 'Baseband',
			'modified_at' => 'Modified At',
			'active_count' => 'Active Count',
			'operator_list_id' => 'Operator List',
			'province_city_id' => 'Province City',
			'sdcount' => '异常上网次数',
			'vscount' => '异常通话次数',
			'oscount' => '脱网次数',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		
		$criteria=new CDbCriteria;
		$criteria->compare('id',$this->id);
		$criteria->compare('startDateTime',$this->startDateTime,true);
		$criteria->compare('time',$this->time);
		$criteria->compare('imsi',$this->imsi,true,'and');
		$criteria->compare('imei',$this->imei,true,'or');
		$criteria->compare('mobileType',$this->mobileType,true);
		$criteria->compare('phoneBrand',$this->phoneBrand,true,'or');
		$criteria->compare('phoneModel',$this->phoneModel,true,'or');
		$criteria->compare('appVersion',$this->appVersion,true);
		$criteria->compare('cpuName',$this->cpuName,true);
		$criteria->compare('cpuSpeed',$this->cpuSpeed);
		$criteria->compare('memory',$this->memory);
		$criteria->compare('osVersion',$this->osVersion,true);
		$criteria->compare('baseband',$this->baseband,true);
		$criteria->compare('modified_at',$this->modified_at,true);
		$criteria->compare('active_count',$this->active_count);
		$criteria->compare('operator_list_id',$this->operator_list_id);
		$criteria->compare('province_city_id',$this->province_city_id);
		$criteria->compare('netAggregation',$this->netAggregation);
		$criteria->compare('dataNetAggregation',$this->dataNetAggregation);
		
		$criteria->addCondition('imsi<>0 ');
				
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'modified_at DESC', //设置默认排序
				'attributes'=>array('phoneBrand','phoneModel','imsi','imei'),
			),
		)); 
	}
	
	
	public function netflow_user_search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		$connection=Yii::app()->db;	
		if($this->memory)
		{
			if(!empty($this->baseband))
				$str=$this->baseband;
			if(!empty($this->cpuName))
				$stp=$this->cpuName;
		}
		else{
			if(isset($_GET['StaticInformation']['startDateTime'])){$str=$_GET['StaticInformation']['startDateTime'];}else{ $str=date('Y-m-d')." 00:00";}
			if(isset($_GET['StaticInformation']['stopDateTime'])){$stp=$_GET['StaticInformation']['stopDateTime'];}else{ $stp=date('Y-m-d')." 23:59";}
		}
		$criteria=new CDbCriteria;
		$ids=array();
		$md=isset($_GET['md'])?$_GET['md']:'user';		
		if($md=='user'){
			$sql ="SELECT round(sum(`Tx_2g`+`Rx_2g`+`Tx_3g`+`Rx_3g`)/1024/1024,4) AS a,static_information_id FROM `traffic_data`
			WHERE static_information_id IN (SELECT id FROM static_information) and collection_time>='".$str."' and collection_time<='".$stp."' 
			and Name<>'全部应用' GROUP BY static_information_id";
			$command = $connection->createCommand($sql);
			$rs = $command->queryAll();
			$ids=array();
			foreach($rs as $k=>$v){
				if($v['a']>0)
					array_push($ids,$v['static_information_id']);
			}
		}
		$criteria->compare('imsi',$this->imsi,true);
		$criteria->compare('imei',$this->imei,true,'or');
		$criteria->addInCondition('id',$ids);
		return new CActiveDataProvider($this, array(
				'pagination'=>array(
					'pageSize'=>15,//设置每页显示条数
				),
			'criteria'=>$criteria,
			'sort'=>array(
			'attributes'=>array('phoneBrand','phoneModel','imsi','imei'),
			),
		)); 
	}
	
	//全网流量分析
	public function netflow_search()
	{
		
		if(!empty($this->memory))
		{
			if(!empty($this->baseband))
				$str=$this->baseband;
			if(!empty($this->cpuName))
				$stp=$this->cpuName;
		}
		else{
			if(isset($_GET['StaticInformation']['startDateTime'])){$str=$_GET['StaticInformation']['startDateTime'];}else{ $str=date('Y-m-d')." 00:00";}
			if(isset($_GET['StaticInformation']['stopDateTime'])){$stp=$_GET['StaticInformation']['stopDateTime'];}else{ $stp=date('Y-m-d',strtotime("+1 day")).' 00:00:00';}
		}
		$connection=Yii::app()->db;	
		$sql ="SELECT sum(`Tx_2g`+`Rx_2g`) AS g2,
		sum(`Tx_3g`+`Rx_3g`) AS g3,
		sum(`Tx_4g`+`Rx_4g`) AS g4,
		static_information_id FROM `traffic_data`
		WHERE static_information_id IN (SELECT id FROM static_information) and collection_time>='".$str."' and collection_time<='".$stp."' 
		and Name<>'全部应用' GROUP BY static_information_id";
		$command = $connection->createCommand($sql);
		$rs = $command->queryAll();
		$_data=array();
		$sid = "-1";
		foreach($rs as $k=>$v){
			if($v['g2']>0 || $v['g3']>0 || $v['g4']>0){
				$_data[$v['static_information_id']]['g2'] = $v['g2'];
				$_data[$v['static_information_id']]['g3'] = $v['g3'];
				$_data[$v['static_information_id']]['g4'] = $v['g4'];
				$sid .=",".$v['static_information_id'];
			}
		}
//		echo "<br>1";print_r($_data);
		$sql_city = "select id,province_city_id from static_information where province_city_id<>0 and id in (".$sid.")";
		$com_city = $connection->createCommand($sql_city);
		$rs_city = $com_city->queryAll();
		$city_sid = array();
		$city_id = "-1";
		foreach($rs_city as $val){
			$city_sid[$val['province_city_id']][] = $_data[$val['id']];
			$city_id .= ",".$val['province_city_id'];
		}
//		echo "城市id:".$city_id;
		$result = array();
		if(!empty($this->imsi)){
			$city = $this->imsi;
			$sql_cityname = "select id,region_name from province_city where region_name like '%".$city."%' and id in (".$city_id.")";
		}else{
			$sql_cityname = "select id,region_name from province_city where id in (".$city_id.")";
		}
//		echo $sql_cityname;
		$com_cityname = $connection->createCommand($sql_cityname);
		$rs_cityname = $com_cityname->queryAll();
		if(!empty($rs_cityname)){
			foreach($rs_cityname as $v){
				$city_sid[$v['id']]['city_name'] = $v['region_name'];
				$result[$v['id']] = $city_sid[$v['id']];
			}
		}
//		echo "<br>2";print_r($result);
		$arr_data = array();
		if(!empty($result)){
			$index = 0;
			foreach($result as $key=>$val){
				$c = "";
				$G2 = 0;
				$G3 = 0;
				$G4 = 0;
				$All = 0;
				foreach($val as $k=>$v){
					if($k === 'city_name'){
						$c = $v;
					}else{
						$G2 += $v['g2'];
						$G3 += $v['g3'];
						$G4 += $v['g4'];
						$All += $v['g2'] + $v['g3'] + $v['g4'];
					}					
				}
//				echo "<br>".$key."--".$G2."--".$G3."--".$G4."--".$All."<br>";
				$G2 = round($G2/1024/1024,4);
				$G3 = round($G3/1024/1024,4);
				$G4 = round($G4/1024/1024,4);
				if($G2 > 0 || $G3 > 0 || $G4 > 0){
					$arr_data[$key]['id']= $index+1;
					$arr_data[$key]['city_id']= $key;
					$arr_data[$key]['city']= $c;
					$arr_data[$key]['G2']= $G2;
					$arr_data[$key]['G3']= $G3;
					$arr_data[$key]['G4']= $G4;
					$arr_data[$key]['All']= round($All/1024/1024,4);
					$index++;
				}
			}
		}
//		echo "<br>123";print_r($arr_data);echo "<br>";
		return new CArrayDataProvider($arr_data, array(
				'id'=>'fuck',
				'pagination'=>array(
					'pageSize'=>15,//设置每页显示条数
				),
				'sort'=>array(
					'attributes'=>array('city','All','G2','G3','G4'),
				),
			));
	}
		
	
	//用户网络占用分析
	public function ocpysearch()
	{
		require_once(Yii::app()->basePath.'/extensions/functions.php');
		
		$limit_time = Yii::app()->params->limit_time_search_back;
		if(!empty($this->modified_at))
		{	$ad=explode(',',$this->modified_at);
				$str=$ad[0];
				$stp=$ad[1];
		}
		else{
			if(isset($_GET['StaticInformation'])){
				$sa = $_GET['StaticInformation'];
				
				$str = $sa['startDateTime'];
				$stp = $sa['stopDateTime'];
			}else{
				$str=date('Y-m-d H',strtotime("-1 hours")).':00:00';
				$stp=date('Y-m-d H').':00:00';
			}
		}
		//转化为时间戳
		$user_start_time_time = strtotime($str);
		$user_end_time_time = strtotime($stp);
		
		$temp_cach="ocpy".$user_start_time_time.$user_end_time_time;
		$temp_cach_user="ocpyD".$user_start_time_time.$user_end_time_time;
		
		$criteria=new CDbCriteria;
		$r=array();	
		if(!empty($this->cpuSpeed)){
			$r = array_keys($this->cpuSpeed);
		}else{		
			$result=Yii::app()->cache ->get($temp_cach);
			if(empty($result) && ($user_end_time_time - $user_start_time_time <= $limit_time)){
				$result = getOcpyCache($str,$stp,$temp_cach,$temp_cach_user);
			}
			if(!empty($result))
				$r = array_keys($result);
			else
				$r[]=null;
		}
		
		$criteria->compare('imsi',$this->imsi,true);
		$criteria->compare('imei',$this->imei,true,'OR');
		$criteria->compare('phoneBrand',$this->phoneBrand);
		$criteria->compare('phoneModel',$this->phoneModel);
		$criteria->addincondition('id',$r);
		
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'imsi ASC', //设置默认排序
				'attributes'=>array('phoneBrand','phoneModel','imsi','imei'),
			),
		));
	}
	
	
	//用户终端系统间互操作分析
	public function usersearch()
	{
		$limit_time = Yii::app()->params->limit_time_search_back;
		if(!empty($this->modified_at)){	$ad=explode(',',$this->modified_at);
				$str=$ad[0];
				$stp=$ad[1];
		}else{
			if(isset($_GET['StaticInformation'])){
				$sa = $_GET['StaticInformation'];
				
				$str = $sa['startDateTime'];
				$stp = $sa['stopDateTime'];
			}else{
				$str=date('Y-m-d H',strtotime("-1 hours")).':00:00';
				$stp=date('Y-m-d H').':00:00';
			}
		}
		require_once(Yii::app()->basePath.'/extensions/functions.php');
		$criteria=new CDbCriteria;
		$r = array();
		if(!empty($this->id)){
			$r = $this->id;
		}else{
			$str_time=strtotime($str);
			$stp_time=strtotime($stp);

			$sys_cache='userSys'.$str_time.$stp_time;
			$result=Yii::app()->cache->get($sys_cache);
			if(!empty($result)){
				$r = array_keys($result);
			}else{
				if($stp_time - $str_time <= $limit_time){
					$result = getUserSysCache($str,$stp,$sys_cache);
					$r = array_keys($result);
				}
			}
		}
		
		$criteria->compare('imsi',$this->imsi,true);
		$criteria->compare('imei',$this->imei,true,'OR');
		$criteria->compare('phoneBrand',$this->phoneBrand);
		$criteria->compare('phoneModel',$this->phoneModel);		
		$criteria->addincondition('id',$r);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
				'defaultOrder'=>'imsi ASC', //设置默认排序
				'attributes'=>array('phoneBrand','phoneModel','imsi','imei'),
			),
		));
	}
	
	
	
	
	/**
	 *	终端数行为监控搜索条件类
	 */
	public function bsearch()
	{
		$sql = '';
		if(isset($_GET['behavior'])){
			$sa = $_GET['behavior'];
			$str = 	$sa['startDateTime'];
			$stp = 	$sa['stopDateTime'];
		}else{
			$str = date('Y-m-d').' 00:00';
			$stp = date('Y-m-d H:i');
		}
		
		$r = strtolower($_GET['r']);
		if(strstr($r,'smalldata')){
			$md = isset($_GET['md'])?addslashes($_GET['md']):'2';
			$sendByte = Yii::app()->params->sendByte; //收发字节参数;
			$sql = "and id in (select t2.staticID from data_service t2 where (t2.startDateTime >= '".$str."' and t2.stopDateTime <= '".$stp."') and t2.stopDateTime > t2.startDateTime and (SELECT DISTINCT SUM(upload_traffic+download_traffic) FROM dynamic_information t4 WHERE dataID<>0 AND netType=".$md." AND (download_traffic+upload_traffic)>0 AND t2.id=t4.dataID)<".($sendByte*1024).")";
		}elseif(strstr($r,'smallvoice')){
			$narray[0]='(0,2,19)';
			$narray[24]='(3,24)';
			$md = isset($_GET['md'])?$narray[$_GET['md']]:$narray[0];
			$callDuration = Yii::app()->params->callDuration;  //通话时长参数
			$sql = "and id in (select staticID from voice_service where (UNIX_TIMESTAMP(stopDateTime) - UNIX_TIMESTAMP(startDateTime))< ".$callDuration."  and (startDateTime >= '".$str."' and stopDateTime <= '".$stp."') and stopDateTime > startDateTime and id in ( select voiceID from dynamic_information where netType in ".$md." and voiceID<>0 and (lac<>0 or cellId<>0) and startDateTime between '".$str."' and '".$stp."'))";				
		}
		elseif(strstr($r,'offnet')){
			$sql = "and id in (select static_information_id from off_network where (start_time >= '".$str."' and end_time <= '".$stp."')  and end_time > start_time)";
		}		
		
		$criteria=new CDbCriteria;

		$criteria->distinct = true;
		$criteria->compare('id',$this->id);
		$criteria->compare('phoneBrand',$this->phoneBrand,true);
		$criteria->compare('phoneModel',$this->phoneModel,true);
		$criteria->addCondition('imsi<>0 '.$sql);		
		$criteria->select='id,imsi,imei,phoneBrand,phoneModel';
		
		$criteria1=new CDbCriteria;
		
		$criteria1->compare('imsi',$this->imsi,true);
		$criteria1->compare('imei',$this->imei,true,'or');		
		$criteria->mergeWith($criteria1,true);
		
		return new CActiveDataProvider($this, array(
			'pagination'=>array(
            	'pageSize'=>15,//设置每页显示条数
        	),
			'sort'=>array(
				'defaultOrder'=>'startDateTime DESC', //设置默认排序
			),
			'criteria'=>$criteria,
		));
	}
	
	
	/**
	 *	终端数据量超小数据导出类
	 */
	public function bhsearch()
	{
		require_once(Yii::app()->basePath.'/extensions/behavior.php');
		
		$sendByte = Yii::app()->params->sendByte; //收发字节参数
		
		$md = isset($_GET['md'])?$_GET['md']:'2';
		
		$rowdata = array();

		
		$attr=array('imsi','phoneBrand','phoneModel','imei','sdcount');
		
		if(isset($_GET['behavior']))
		{
			$sa = $_GET['behavior'];
			$str = $sa['startDateTime'];
			$stp = $sa['stopDateTime'];
		}
		else
		{
			$str = date('Y-m-d').' 00:00';
			$stp = date('Y-m-d H:i');
		}
		
		
		$criteria=new CDbCriteria;
		$criteria1=new CDbCriteria;
		
		$criteria1->compare('imsi',$this->imsi,true);
		$criteria1->compare('imei',$this->imei,true,'or');
		
		$criteria->select='id,imsi,imei,phoneBrand,phoneModel';
		$criteria->order = 'modified_at DESC';
		$sql = "and id in (select t2.staticID from data_service t2 where (t2.startDateTime >= '".$str."' and t2.stopDateTime <= '".$stp."') and t2.stopDateTime > t2.startDateTime and (SELECT SUM(upload_traffic+download_traffic) FROM dynamic_information t4 WHERE dataID<>0 AND netType=".$md." AND (download_traffic+upload_traffic)>0 AND t2.id=t4.dataID GROUP BY dataID)<".($sendByte*1024).")";
		$criteria->addCondition('imsi<>0 '.$sql);
		
		$criteria->mergeWith($criteria1,true);
		
		$row=$this->findAll($criteria);
		
		foreach($row as $key=>$val){
			$rowdata[$key] = $val->attributes;
			$rowdata[$key]['sdcount'] = sdcount($val->attributes['id'],$str,$stp);
			$rowdata[$key]['phoneBrand'] = $val->attributes['phoneBrand'];
		}
		
		
		return new CArrayDataProvider($rowdata, array(
			'pagination'=>array(
            	'pageSize'=>15,//设置每页显示条数
        	),
			'sort'=>array(
				'attributes'=>$attr,
			),
		));
	}
	
	
	/**
	 *	终端通话时间超短数据导出类
	 */
	public function bvsearch()
	{
		require_once(Yii::app()->basePath.'/extensions/behavior.php');
		
		$callDuration = Yii::app()->params->callDuration;  //通话时长参数
		
		$narray=array();
		$narray[0]='(0,2,19)';
		$narray[24]='(3,24)';
		$md = isset($_GET['md'])?$narray[$_GET['md']]:$narray[0];
		
		$rowdata = array();
		
		$attr=array('imsi','phoneBrand','phoneModel','imei','vscount');
		
		if(isset($_GET['behavior']))
		{
			$sa = $_GET['behavior'];
			$str = $sa['startDateTime'];
			$stp = $sa['stopDateTime'];
		}
		else
		{
			$str = date('Y-m-d').' 00:00';
			$stp = date('Y-m-d H:i');
		}
		
		
		$criteria=new CDbCriteria;
		$criteria1=new CDbCriteria;
		
		$criteria1->compare('imsi',$this->imsi,true);
		$criteria1->compare('imei',$this->imei,true,'or');
		
		$criteria->select='id,imsi,imei,phoneBrand,phoneModel';
		$criteria->order = 'modified_at DESC';
		$sql = "and id in (select staticID from voice_service where (UNIX_TIMESTAMP(stopDateTime) - UNIX_TIMESTAMP(startDateTime))< ".$callDuration."  and (startDateTime >= '".$str."' and stopDateTime <= '".$stp."') and stopDateTime > startDateTime and id in ( select voiceID from dynamic_information where netType in ".$md." and voiceID<>0 and (lac<>0 or cellId<>0) and startDateTime between '".$str."' and '".$stp."'))";
		$criteria->addCondition('imsi<>0 '.$sql);
		
		$criteria->mergeWith($criteria1,true);
		
		$row=$this->findAll($criteria);
		
		foreach($row as $key=>$val){
			$rowdata[$key] = $val->attributes;
			$rowdata[$key]['vscount'] = vscount($val->attributes['id'],$str,$stp);
			$rowdata[$key]['phoneBrand'] = $val->attributes['phoneBrand'];
		}
		
		
		return new CArrayDataProvider($rowdata, array(
			'pagination'=>array(
            	'pageSize'=>15,//设置每页显示条数
        	),
			
		));
	}
	
	
	/**
	 *	终端脱网分析数据导出类
	 */
	public function bosearch()
	{
		require_once(Yii::app()->basePath.'/extensions/behavior.php');
	
		$rowdata = array();
		$attr=array('imsi','phoneBrand','phoneModel','imei','oscount');
		
		if(isset($_GET['behavior']))
		{
			$sa = $_GET['behavior'];
			$str = $sa['startDateTime'];
			$stp = $sa['stopDateTime'];	
		}
		else
		{
			$str = date('Y-m-d').' 00:00';
			$stp = date('Y-m-d H:i');
		}
		
		
		$criteria=new CDbCriteria;
		$criteria1=new CDbCriteria;
		
		$criteria1->compare('imsi',$this->imsi,true);
		$criteria1->compare('imei',$this->imei,true,'or');
		
		$criteria->select='id,imsi,imei,phoneBrand,phoneModel';
		$criteria->order = 'modified_at DESC';
		$sql = "and id in (select static_information_id from off_network where (start_time >= '".$str."' and end_time <= '".$stp."')  and end_time > start_time)";
		$criteria->addCondition('imsi<>0 '.$sql);
		
		$criteria->mergeWith($criteria1,true);
		
		$row=$this->findAll($criteria);
		
		foreach($row as $key=>$val){
			$rowdata[$key] = $val->attributes;
			$rowdata[$key]['oscount'] = oscount($val->attributes['id'],$str,$stp);
			$rowdata[$key]['phoneBrand'] = $val->attributes['phoneBrand'];
		}
		
		
		return new CArrayDataProvider($rowdata, array(
			'pagination'=>array(
            	'pageSize'=>15,//设置每页显示条数
        	),
			'sort'=>array(
				'attributes'=>$attr,
			),
		));
	}
	
	
	
	public function staticsearch()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		
		
		require_once(Yii::app()->basePath.'/extensions/staticInformation.php');
		$criteria=new CDbCriteria;
		$criteria->compare('imsi',$this->imsi,true,'or');
		$criteria->compare('imei',$this->imsi,true,'or');
		foreach($this->attributes as $k=>$v)
		{
			if(!empty($v))
			{
				if($k=='phoneBrand')
					$criteria->addcondition('phoneBrand="'.$v.'"');
				elseif($k=='phoneModel')
					$criteria->addcondition('phoneModel="'.$v.'"');
			}
		}	
		$criteria->addCondition('imsi<>0 ');
		
		return new CActiveDataProvider($this, array(
		'sort'=>array(
            'defaultOrder'=>' startDateTime  DESC', //设置默认排序是create_time倒序
			'attributes'=>array('phoneBrand','phoneModel','imei'),
        ),
		'criteria'=>$criteria,
		'pagination'=>array(
			'pageSize'=>15,
		),
		));
	}
//-----------------------------------------------------------------------terminal------------------------------------------------------	
/**
	 * 个人终端
	 *
	 */
	public function tsearch()
	{
		//搜索条件语句
		$criteria=new CDbCriteria;
		if(isset($_GET['terminal']['over'])&&isset($_GET['terminal']['ei']))
		{
			$imsi=trim($_GET['terminal']['over']);
			$imei=trim($_GET['terminal']['ei']);
			if($imsi!='输入IMSI或IMEI进行搜索')
			{
				if($imei!='imei')
				{
					$criteria->addCondition("imsi like '%".$imsi."%' and imei like '%".$imei."%'");
				}
				else 
				{
					$criteria->addCondition("imsi like '%".$imsi."%' or imei like '%".$imsi."%'");
				}
				
			}
			
		}
		//$criteria->compare('imsi',$this->imsi,true,'and');
		//$criteria->compare('imei',$this->imei,true,'or');
		$criteria->compare('phoneBrand',$this->phoneBrand,false,'and');
		$criteria->compare('phoneModel',$this->phoneModel,false,'and');
		$criteria->compare('cpuName',$this->cpuName,false,'and');
		//筛选没有数据的ID;
		$id=$this->baseband;
		$str="null";
		foreach($id as $v){
			if($str=="null"){
				$str='';
				$str.=$v;
			}else{
				$str.=",".$v;
			}
		}
		$criteria->addcondition("id in (".$str.")");
		//返回data
		
		//print_r($criteria);
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
			'sort'=>array(
			'attributes'=>array('phoneBrand','phoneModel','imsi','imei'),
			),
		)); 
	}
	/**
	 * 型号终端
	 *
	 */
	public function Msearch()
	{
		require_once(Yii::app()->basePath.'/extensions/terminal_function.php');
		//筛选
		$connection=Yii::app()->db;
		$id=get_sift_rssiID();
		$str="null";
		foreach($id as $v){
			if($str=="null"){
				$str='';
				$str.=$v;
			}else{
				$str.=','.$v;
			}
		}
		$sql="select phoneModel from static_information where id in (".$str.") group by phoneModel";
		$command=$connection->createCommand($sql);
		$row=$command->queryALL();
		
		$str2="null";
		foreach($row as $v){
			if($str2=="null"){
				$str2='';
				$str2.="'".$v['phoneModel']."'";
			}else{
				$str2.=","."'".$v['phoneModel']."'";
			}
		}	
		$criteria=new CDbCriteria;
		$criteria->group ='phoneModel'; 
		$criteria->addcondition("phoneModel in (".$str2.")");
		$criteria->compare('phoneBrand',$this->phoneBrand,false);
		$criteria->compare('phoneModel',$this->phoneModel,false);
			
		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		)); 
	}
	/**
	 * 型号数据业务能力
	 *
	 */
	public function Mservicesearch()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.	
		require_once(Yii::app()->basePath.'/extensions/terminal_function.php');	
		$attr=array('phoneBrand','phoneModel','loadRate','packetLoss','avgTime','throughputRate');
		if(!empty($this->attributes['imei'])){
			$md=$this->attributes['imei'];
		}
		if(isset($_GET['md'])){
			$md=$_GET['md'];
		}else{
			$md="GPRS";
		}
	//筛选没有数据的ID
		$connection=Yii::app()->db;
		$sql="select id from network_type where nettype='".$md."'";
		$command=$connection->createCommand($sql);
		$row=$command->queryRow();
		if($row['id']==''){
			$row['id']='null';
		}
		
		$sql="select dataID from dynamic_information where netType=".$row['id']." group by dataID";
		$command=$connection->createCommand($sql);
		$row=$command->queryAll();
		$str="null";
		foreach($row as $v){
			if($str=="null"){
				$str='';
				$str.=$v['dataID'];		
			}else{
				$str.=",".$v['dataID'];
			}
		}
		$sql="select staticID from data_service where id in (".$str.") group by staticID";
		$command=$connection->createCommand($sql);
		$row=$command->queryAll();
		$str1="null";
		foreach($row as $v){
			if($str1=="null"){
				$str1='';
				$str1.=$v['staticID'];		
			}else{
				$str1.=",".$v['staticID'];
			}
		}
		$sql='select phoneModel from static_information where id in ('.$str1.') group by phoneModel';
		$command=$connection->createCommand($sql);
		$row=$command->queryAll();
		$str2="null";
		foreach($row as $v){
			if($str2=="null"){
				$str2='';
				$str2.="'".$v['phoneModel']."'";
			}else{
				$str2.=","."'".$v['phoneModel']."'";
			}
		}	
		$criteria=new CDbCriteria;
		$criteria->addcondition("phoneModel in (".$str2.")");
		$criteria->group ='phoneModel';
		foreach($this->attributes as $k=>$v)
		{
			if(!empty($v))
			{
				if($k=='phoneBrand')
					$criteria->addcondition('phoneBrand="'.$v.'"');
				elseif($k=='phoneModel')
					$criteria->addcondition('phoneModel="'.$v.'"');
			}
		}		
		$data=$this->findAll($criteria);
		$dataProvider=array();
		foreach($data as $k=>$v)
		{	
			$all[$k]['id']=$k+1;
			$all[$k]['phoneModel']=$v->attributes['phoneModel'];
			$all[$k]['phoneBrand']=$v->attributes['phoneBrand'];
			$all[$k]['loadRate']=get_Model_updown($v->attributes['phoneModel'],$md);
			$all[$k]['packetLoss']=get_Model_packetloss($v->attributes['phoneModel'],$md);
			$all[$k]['avgTime']=get_Model_avgTime($v->attributes['phoneModel'],$md);
			$all[$k]['throughputRate']=get_Model_DownLoadRate($v->attributes['phoneModel'],$md);
		
			if(!empty($this->attributes['imsi']))
			{
				foreach($all as $key=>$val)
				{
					foreach($val as $_key=>$_val)
					{
						if(strpos($_val,$this->attributes['imsi'])!==false&&$_key!=='id')
							$dataProvider[$key]=$all[$key];
					}				
				}		
			}
			else
				$dataProvider=$all;	
		}
		return new CArrayDataProvider($dataProvider, array(
		'id'=>'user',
		'sort'=>array(
			'attributes'=>$attr,
		),
		'pagination'=>array(
			'pageSize'=>15,
		),
		));
	}
	/**
	 * 型号信号RSSI
	 *
	 */
	public function Mrssisearch()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.
		//require_once(Yii::app()->basePath.'/extensions/newtll.php');
		require_once(Yii::app()->basePath.'/extensions/terminal_function.php');
		$attr=array('phoneBrand','phoneModel','TdRssiAverage','TdRssi','TdAbility','GsmRssiAverage','GsmRssi','GsmAbility','LteRssiAverage','LteRssi','LteAbility');

	//筛选没有数据的ID
		$connection=Yii::app()->db;
		$id=get_sift_rssiID();
		$str="null";
		foreach($id as $v){
			if($str=="null"){
				$str='';
				$str.=$v;
			}else{
				$str.=','.$v;
			}
		}
		$sql="select phoneModel from static_information where id in (".$str.") group by phoneModel";
		$command=$connection->createCommand($sql);
		$row=$command->queryALL();
		
		$str2="null";
		foreach($row as $v){
			if($str2=="null"){
				$str2='';
				$str2.="'".$v['phoneModel']."'";
			}else{
				$str2.=","."'".$v['phoneModel']."'";
			}
		}
		$criteria=new CDbCriteria;
		$criteria->addcondition("phoneModel in (".$str2.")");
		$criteria->group ='phoneModel';
		foreach($this->attributes as $k=>$v)
		{
			if(!empty($v))
			{
				if($k=='phoneBrand')
					$criteria->addcondition('phoneBrand="'.$v.'"');
				elseif($k=='phoneModel')
					$criteria->addcondition('phoneModel="'.$v.'"');
			}
		}		
		$data=$this->findAll($criteria);

		$dataProvider=array();
		$AllRssi=get_All_Rssi();
		foreach($data as $k=>$v)
		{
			$phoneModel=$v->attributes['phoneModel'];
			$TDRssi=get_MTD_Rssi($phoneModel);//型号终端TD网络下 RSSI
			$GSMRssi=get_MGSM_Rssi($phoneModel);//型号终端GSM下 RSSI
			$LTERssi=get_MLTE_Rsrp($phoneModel);//型号终端GSM下 RSrp

			$all[$k]['id']=$k+1;
			$all[$k]['phoneModel']=$v->attributes['phoneModel'];
			$all[$k]['phoneBrand']=$v->attributes['phoneBrand'];	
			$all[$k]['TdRssiAverage']=$TDRssi;//型号终端TD网络下 RSSI
			$all[$k]['TdRssi']=$AllRssi['TD'];
			$all[$k]['TdAbility']=get_Rssi_Contrast($TDRssi,$AllRssi['TD']);//评价
			$all[$k]['GsmRssiAverage']=$GSMRssi;//型号终端GSM下 RSSI
			$all[$k]['GsmRssi']=$AllRssi['GSM'];
			$all[$k]['GsmAbility']=get_Rssi_Contrast($GSMRssi,$AllRssi['GSM']);//评价
			$all[$k]['LteRssiAverage']=$LTERssi;//型号终端GSM下 RSSI
			$all[$k]['LteRssi']=$AllRssi['LTE'];
			$all[$k]['LteAbility']=get_Rssi_Contrast($LTERssi,$AllRssi['LTE']);//评价

			if(!empty($this->attributes['imsi']))
			{
				foreach($all as $key=>$val)
				{
					foreach($val as $_key=>$_val)
					{
						if(strpos($_val,$this->attributes['imsi'])!==false&&$_key!=='id')
							$dataProvider[$key]=$all[$key];
					}				
				}	
				
			}
			else
				$dataProvider=$all;		
		}
		return new CArrayDataProvider($dataProvider, array(
			'id'=>'user',
			'sort'=>array(
			'attributes'=>$attr,
		),
		'pagination'=>array(
			'pageSize'=>15,
		),
		));
	}
	
	/**
	 * 隐藏imei信息
	 * 
	 * 调用方式:
	 * 	StaticInformation::ReplaceImei("350027626020241")
	 * 
	 * @param string $imei
	 */
	public static function ReplaceImei($imei = null){
		return self::Imsi_Imei_Hide($imei);
	}
	
	/**
	 * 隐藏imsi信息
	 * 
	 * 调用方式:
	 * 	StaticInformation::ReplaceImsi("460027626020241")
	 * 
	 * @param string $imsi
	 */
	public static function ReplaceImsi($imsi = null){
		return self::Imsi_Imei_Hide($imsi);
	}
	
	/**
	 * 根据传入的值,以用户角色返回对应的值[从配置文件中读取信息]
	 * 	例:传入460027626020241
	 * 	当前用户是只读用户:460********0241
	 * 
	 * 调用方式:
	 * 	StaticInformation::imsi("460027626020241")
	 * 
	 * 输出当前用户角色
	 * echo $_SESSION["userRole"].",abcd";exit;
	 */
	private static function Imsi_Imei_Hide($v=null){
		if(!Yii::app()->params->hide_info_enabled || !isset($_SESSION["userRole"]))
			return $v;
		
		if(Yii::app()->params->hide_info_Admin && $_SESSION["userRole"] == "Admin")
			return $v;
		if(Yii::app()->params->hide_info_Manager && $_SESSION["userRole"] == "Manager")
			return $v;
		if(Yii::app()->params->hide_info_ViewUser && $_SESSION["userRole"] == "ViewUser")
			return $v;
		if(empty($v) || strlen($v)<Yii::app()->params->hide_info_start_from)
			return $v;
		return substr_replace($v, substr("**********************", 0, Yii::app()->params->hide_info_length), (Yii::app()->params->hide_info_start_from-1), Yii::app()->params->hide_info_length);
	}

	/**
	 * @name 查询静态信息表中所有符合条件的数据
	 * @param String $condition 查询条件
	 * @param String $select 查询字段
	 * @return array 查询结果数组
	 */
	public function allStaticId($select = '*',$condition = '')
	{
		$return = array();
		$result = $this->findAll(array(
			'condition'=>$condition,
			'select'=>$select,
		));
		if(!!$result)
			foreach($result as $i=>$user)
    			$return[$user->attributes['id']]=$user->attributes;

		return $return;
	}
		
}