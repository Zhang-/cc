<?php

/**
 * 系统提示类
 *
 * @author 张洪源
 * @date 2014-02-18 11:01:15
 * 
**/		
class Alerts
{
	private $cacheName; //cacheName
	public $alerts; //所有系统提醒子项状态信息
	private $managePageUrl; //跳转页面中转url
	

	/**
	 * @name 系统提醒类初始化
	 * @param String $type //初始化本类的参数 ('reset'=>更新缓存) 
	 *
	 * @author zhanghy
	 * @date 2014-02-18 13:52:43
	**/
	public function __construct($type = null)
	{
		$this->cacheName = Yii::app()->params->alertsCacheName; //系统提醒内容缓存名称
		$this->managePageUrl = "sysmanage/alertsManage"; //跳转页面中转url
		$reQuery = ($type === 'reset' ) ? true : false;
		//var_dump($reQuery);exit;
		$this->alerts = $this->queryAlerts($reQuery); //获取所有系统提醒子项的信息
	}

	/**
	 * @name 查询所有系统提醒子项
	 * @param $Boolean $reQuery 是否重新查询数据库生成缓存
	 * @return Array $alertsInfo 包含所有系统提醒子项信息的二维数组
	 *
	 * @author zhanghy
	 * @date 2014-02-18 16:17:31
	**/
	public function queryAlerts($reQuery = false,$queryDbOnly = false)
	{
		$alertsInfo = Yii::app()->cache->get($this->cacheName);
		if ($alertsInfo === false || $reQuery ) {
			$model = SystemAlerts::model()->findAll(); //查询所有系统提醒信息
			$alertsInfo = array();
			foreach ($model as $value) {
				$alertsInfo[$value->attributes['alerts_key']] = $value->attributes; //遍历并存入array
			}
			//var_dump($alertsInfo);exit;
			if($queryDbOnly === false)
				$this->updateCache(true,$alertsInfo); //更新缓存
		}
		return $alertsInfo;
	}

	/**
	 * @name 系统提醒栏初始化
	 * @param String $list 调用本方法的用途 (false=>获取最新数据库信息,'true'=>返回提醒子项列表
	 * @return String $alertsList 包含数个li元素的字符串 / 所有系统提醒子项信息二维数组
	 *
	 * @author zhanghy
	 * @date 2014-02-18 13:52:43
	**/
	public function listAlerts($list = true)
	{
		$alertsList = '';

		if($this->alerts){
			if ($list) {
				foreach($this->alerts as $alertVal){
					//$url = Yii::app()->createUrl($alertVal['alerts_link']); //创建链接URL
					$display = intval($alertVal['alerts_display']) === 0 ? 'none' : 'block'; //更新样式
					$url = Yii::app()->createUrl( $this->managePageUrl."&alertkey={$alertVal['alerts_key']}&link={$alertVal['alerts_link']}" );
					$alertsList .= "<li id='{$alertVal['alerts_key']}' style='display:{$display}'>".CHtml::checkBox($alertVal['alerts_key'],$alertVal['alerts_display'],array('class'=>'','type'=>'checkbox','value'=>$alertVal['alerts_key'],'onclick'=>"alertChecked(this.value,$(this).attr('checked'));"))."<a href='{$url}' target='_top'>{$alertVal['alerts_tag']}<span>{$alertVal['alerts_status']}</span></a></li>";
				}
			}else
				$alertsList = $this->queryAlerts(true);
		}
		return $alertsList;
		//return $this->queryAlerts();
	}

	/**
	 * @name 更新系统提醒子项状态
	 * @param String $alerts_key 调用系统提醒子项的关键词
	 * @param int $status_change 更新状态参数，如果$isCover为true，$status_change为覆盖值，反之为状态更新值（int）
	 * @param Boolean $isCover 是否覆盖状态信息，例如重新计数或文字类型的状态更新
	 * @param Boolean $alerts_display 该系统提醒子项是否在系统提醒栏显示
	 * @param Boolean $reSetCacheOnly 是否只更新缓存
	 * @return Boolean $flag 返回是否更新成功
	 *
	 * @author zhanghy
	 * @date 2014-02-18 17:38:23
	**/
	public function updateAlert($alerts_key,$status_change = null,$isCover = null,$alerts_display = null,$reSetCacheOnly = false)
	{
		$updateInfo = null;
		if (isset($this->alerts[$alerts_key])) 
		{
			if (!is_null($status_change)) 
			{
				if (!is_null($isCover) && !$isCover) //如果不覆盖新值
				{
					$statusCache = $this->alerts[$alerts_key]['alerts_status'];
					//var_dump($statusCache);exit;
					if(is_numeric($statusCache) || empty($statusCache) )
						$status_change += $statusCache; //重建更新信息
					else
						$this->throwException('当前系统提醒子项状态内容为非数字！如需覆盖保存请添加覆盖参数！');
				}
				$updateInfo['alerts_status'] = $status_change;
			}

			if ( $alerts_display === true )
				$updateInfo['alerts_display'] = 1; //更新显示状态
			elseif ($alerts_display === false)
				$updateInfo['alerts_display'] = 0; //更新显示状态
			
			if (!is_null($updateInfo)) {

				if ($reSetCacheOnly === false)
					$reSetCacheOnly = $this->updateDb($alerts_key,$updateInfo);
				if( $this->updateCache(false,$updateInfo,$alerts_key) && $reSetCacheOnly ) //如果数据库跟缓存都更新成功
					return true;
			}else{
				return $this->throwException('该系统提醒子项不存在！请清除缓存后重试！');
			}
		}
		
		
		//$this->destroyCache();
	}

	/**
	 * @name 更新系统提醒文件缓存
	 * @param Boolean $isReset 是否重建缓存
	 * @param Array $updateInfo 缓存更新信息，如果$isReset==true，$alertsInfo为包含所有提醒子项信息的数组；
	 * 		  如果$isReset==false $updateInfo为该$alertsKey更改信息的数组
	 * @param String $alertsKey 如果$isReset == false 此值不能为空
	 * @return Boolean $return
	 *
	 * @author zhanghy
	 * @date 2014-02-18 13:52:43
	**/
	private function updateCache($isReset,$updateInfo,$alertsKey = null)
	{
		if (!$isReset) //如果不重建缓存
		{ 
			foreach ($updateInfo as $updateKey => $updateValue)
				$this->alerts[$alertsKey][$updateKey] = $updateValue; //更新该$alertsKey提醒子项状态信息
			$updateInfo = $this->alerts; //更新所有提醒子项信息
		}	
		if( Yii::app()->cache->set($this->cacheName,$updateInfo) ) //更新缓存
			return true;
	}

	/**
	 * @name 更新系统提醒文件缓存
	 * @param String $alerts_key 系统提醒子项关键字
	 * @param Array $updateInfo 包含该提醒子项更改信息的数组；
	 *
	 * @author zhanghy
	 * @date 2014-02-19 12:24:20
	**/
	private function updateDb($alerts_key,$updateInfo)
	{
		if( SystemAlerts::model()->updateAll($updateInfo, " alerts_key='{$alerts_key}'") ) //更新数据库
			return true;
	}

	/*public function updateDisplay()
	{
		
	}*/

	/**
	 * @name 删除指定缓存值
	 *
	 * @author zhanghy
	 * @date 2014-02-19 16:38:25
	**/
	private function deleteCache($name)
	{
		Yii::app()->cache->delete($name); //删除缓存
	}


	/**
	 * @name 抛出异常信息
	 *
	 * @author zhanghy
	 * @date 2014-02-19 08:13:23
	**/
	private function throwException($error) 
	{
		throw new Exception($error);
	}
	
	

}
?>