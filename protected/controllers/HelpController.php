<?php
class HelpController extends Controller
{
	/**
	 * 系统帮助信息
	 */
	public function actionIndex(){
		$this->render('index');
	}


	public function actionOneKeyInput()
	{
		//order
		$userCache = CacheR::getInstance(CacheR::USERDSN); //用户缓存

		$ordersData = PlaceOrderInfo::model()->findAll();

		$ordersData = HelpTool::getFindAllData($ordersData);

		foreach ($ordersData as $oVal)
		{
			$userCache->set(CacheR::ORDERINFO.$oVal['id'], json_encode($oVal)); //订单信息
			
			if (intval($oVal['o_status']) === 0)
				$userCache->hset(CacheR::ORDERLOCAL, $oVal['id'], $oVal['start']);
		}

		echo 'finished input data...';exit;
	}

	public function actionTest()
	{
		//order
		$userCache = CacheR::getInstance(CacheR::USERDSN); //用户缓存
	}

	public function actionFlushDB()
	{
		$dsn  = Common::request('dsn', 'R', '');
		$pass = Common::request('lc', 'R', '');
		if ($dsn && $pass)
		{
			if ($dsn == 'driver')
			{
				$cacheDsn = CacheR::DRIVERDSN;
			}
			else if ($dsn == 'user')
			{
				$cacheDsn = CacheR::USERDSN;
			}
			$cacheHandle = CacheR::getInstance($cacheDsn); //用户缓存

			if ($cacheHandle->flushdb())
			{
				$msg =  'flush ' .$dsn . ' db finished!';
			}
			else
			{
				$msg =  'flush ' .$dsn . ' db unsuccess!';
			}
		}
		else
		{
			$msg = 'params error!';
			
		}
		$this->_outPut(['msg' => $msg]);
	}
}