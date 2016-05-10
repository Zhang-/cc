<?php
class ApiController extends Controller
{
	//$userCache = CacheR::getInstance(CacheR::USERDSN);
	//$driverCache = CacheR::getInstance(CacheR::DRIVERDSN);

	// tid=230 imsi=460017275041884 imei=864687011372504 md5Im=de8a15eab54ef2b9bc2d0e26658bda67
	
	//hupu 121.47343894665399,31.271054569791875,121.47686680971132,31.27280605471306
	//大明湖 117.015203660834,36.67141504150635,117.02891511308138,36.677989222900266
	//趵突泉 117.0138204032578,36.660222192719935,117.01724826631514,36.6618660297814
	
	/** 
	  * @name   注册新用户
	  * @api    index.php?r=Api/UserRegister
	  *
	  * @param  boolean  $isDriver   //是否是司机注册
	  * @param  Array    $params     //注册参数
	  * 
	  * @return json    $return      Register status
	  * 
	  * @author zhanghy
	  * @date 2015-6-29 23:19:56
	  *
	  **/
	public function newUserRegister($isDriver, array $params)
	{
		if ($isDriver)
			$model = new DriverInformation(); //司机信息表
		else
			$model = new UserInformation(); //用户信息表

		$model->attributes = $params;

		if ($model->save()) 
		{
			if ($isDriver)
			{
				$cacheDsn = CacheR::DRIVERDSN;
				$cacheKey = CacheR::DRIVERINFO.$model->id;
			}
			else
			{
				$cacheDsn = CacheR::USERDSN;
				$cacheKey = CacheR::USERINFO.$model->id;
			}
			$userCache = CacheR::getInstance($cacheDsn); //用户缓存
			$userCache->set($cacheKey, json_encode($model->attributes)); //cache 用户基本信息
			//HelpTool::getActionInfo ( $model->id, 2 ); // 新建操作
			$this->_outPut(['tid' => $model->id]);
		}
		else
		{
			$this->_doError(2); //保存失败
		}
	}

	/** 
	  * @name   用户端注册
	  * @api    index.php?r=Api/UserRegister
	  *
	  * @param  string  $phone_num   //手机号码
	  * @param  string  $imsi        //sim卡号
	  * @param  string  $imei        //设备号
	  * @param  string  $u_name      //用户名
	  * @param  int     $u_sex       //用户性别 0:man 1:woman
	  * @param  string  $brand       //设备型号
	  * @param  string  $app_ver     //软件版本
	  * @param  string  $os_ver      //系统版本
	  * @param  string  $net_type    //网络类型
	  * @param  int     $city_id     //所处城市id
	  * 
	  * @return json    $return      //成功：{"s":200,"d":{"tid":"403"}}
	  * 失败：{"s":500,"m":"\u624b\u673a\u53f7\u7801\u586b\u5199\u4e0d\u6b63\u786e,\u8bf7\u68c0\u67e5!"}
	  * 
	  * @author zhanghy
	  * @date 2015-6-29 23:19:56
	  *
	  **/
	public function userRegister()
	{
		$imsi        = Common::request('imsi', 'R', '');        //get reg imsi info
		$imei        = Common::request('imei', 'R', '');        //get reg imei info
		$phone_num   = Common::request('phone_num', 'R', 0);    //phone number
		$user_name   = Common::request('u_name','R', '');       //用户名
		$user_sex    = Common::request('u_sex','R', 0);         //用户性别
		$phone_brand = Common::request('brand','R', '');        //手机品牌
		$app_version = Common::request('app_ver','R', '');  	//客户端软件版本号
		$os_version  = Common::request('os_ver','R', '');     	//客户端系统版本号
		$net_type    = Common::request('net_type','R', '');     //网络制式
		$city_id     = Common::request('city_id','R', 0);       //所在城市(客户端判断)
 		
		if (strlen($imsi) == 15 && strlen($imei) == 15)
		{
			$model =    new UserInformation(); //用户信息表
			$userInfo = $model->findByAttributes( array( 'imsi'=>$imsi, 'imei' => $imei ) ); //查询是否注册过

			if ($userInfo)
			{
				//'active_count'=> +1
				$this->_outPut(['tid' => $userInfo->id]); //已注册 返回tid
			}
			else
			{
				$params = 
				[
					'time' 		  => NOW_TIME,
					'imsi' 		  => $imsi,
					'imei' 		  => $imei,
					'phone_num'   => $phone_num,
					'user_name'   => $user_name,
					'user_sex'    => $user_sex,
					'phone_brand' => $phone_brand,
					'app_version' => $app_version,
					'os_version'  => $os_version,
					'net_type'    => $net_type,
					'city_id'     => $city_id,
					'active_count'=> 1
				];

				$this->newUserRegister(false, $params); //用户注册
			}
		}
		else //设备号不正确
		{
			$this->_doError(11);
		}
	}

	/** 
	  * @name   司机端注册
	  * @api    index.php?r=Api/DriverRegister
	  *
	  * @param  string  $phone_num   //手机号码
	  * @param  string  $imsi        //sim卡号
	  * @param  string  $imei        //设备号
	  * @param  string  $u_name      //用户名
	  * @param  int     $u_sex       //用户性别 0:man 1:woman
	  * @param  string  $brand       //设备型号
	  * @param  string  $app_ver     //软件版本
	  * @param  string  $os_ver      //系统版本
	  * @param  string  $net_type    //网络类型
	  * @param  int     $city_id     //所处城市id
	  * 
	  * @return json     $return     //{"s":200,"d":{"tid":"403"}}
	  * 
	  * @author zhanghy
	  * @date 2015-6-29 23:19:56
	  *
	  **/
	public function actionDriverRegister()
	{
		$phone_num   = Common::request('phone_num', 'R', '');   //phone number

		if (strlen($phone_num) !== 11) $this->_doError(10);     //手机号不正确

		$imsi        = Common::request('imsi', 'R', '');        //get reg imsi info
		$imei        = Common::request('imei', 'R', '');        //get reg imei info
		$user_name   = Common::request('u_name','R', '');       //用户名
		$user_sex    = Common::request('u_sex','R', 0);         //用户性别
		$phone_brand = Common::request('brand','R', '');        //手机品牌
		$app_version = Common::request('app_ver','R', '');  	//客户端软件版本号
		$os_version  = Common::request('os_ver','R', '');     	//客户端系统版本号
		$net_type    = Common::request('net_type','R', '');     //网络制式
		$city_id     = Common::request('city_id','R', 0);       //所在城市(客户端判断)
 		
		if (strlen($imsi) == 15 && strlen($imei) == 15)
		{
			$model =    new DriverInformation(); //用户信息表
			$userInfo = $model->findByAttributes( array( 'imsi'=>$imsi, 'imei' => $imei ) ); //查询是否注册过

			if ($userInfo)
			{
				//'active_count'=> +1
				$this->_outPut(['tid' => $userInfo->id]); //已注册 返回tid
			}
			else
			{
				$params = 
				[
					'time' 		  => NOW_TIME,
					'imsi' 		  => $imsi,
					'imei' 		  => $imei,
					'phone_num'   => $phone_num,
					'user_name'   => $user_name,
					'user_sex'    => $user_sex,
					'start_date_time' => date('Y-m-d H:i:s'),
					'phone_brand' => $phone_brand,
					'app_version' => $app_version,
					'os_version'  => $os_version,
					'net_type'    => $net_type,
					'city_id'     => $city_id,
					'active_count'=> 1
				];

				$this->newUserRegister(true, $params);
			}
		}
		else //设备号不正确
		{
			$this->_doError(11);
		}
	}

	/** 
	  * @name   用户基本信息修改
	  * @api    index.php?r=Api/UpdateUserInfo
	  *
	  * @param  int     $tid         //用户id
	  * @param  string  $im          //md5(imsi + imei)
	  * @param  int     $is_driver   //是否为司机 司机=1
	  * @param  string  $phone_num   //手机号码
	  * @param  string  $u_name      //用户名
	  * @param  int     $u_sex       //用户性别 0:man 1:woman
	  * 
	  * @return json     $return     //{"s":200,"d":{"tid":"403"}}
	  * 
	  * @author zhanghy
	  * @date 2015-11-25 15:31:10
	  *
	  **/
	public function actionUpdateUserInfo()
	{
		$tid         = intval(Common::request('tid', 'R', 0));       //phone number
		$im          = Common::request('im', 'R', '');               //md5(imsi+imei)

		$isDriver    = intval(Common::request('is_driver', 'R', 0)); //是否为司机

		$user_name   = Common::request('u_name','R', '');            //用户名
		$user_sex    = Common::request('u_sex','R', 0);              //用户性别
		$phone_num   = Common::request('phone_num', 'R', '');        //phone number

		if (!$tid || !$im || !$user_name || !$phone_num) $this->_doError(1); //缺少参数

		if (strlen($phone_num) !== 11) $this->_doError(10); //手机号不正确

		if ($isDriver)
			$thisUser = $this->loadDriverModel($tid, true); //查询库中是否有注册信息
		else
			$thisUser = $this->loadUserModel($tid, true); //查询库中是否有注册信息

		if (empty($thisUser->imsi) || empty($thisUser->imei))
		{
			$this->_doError(20); //未注册
		}
		else
		{
			$md5Im = md5($thisUser->imsi.$thisUser->imei);
			if ($md5Im !== strtolower($im))
				$this->_doError(22); //验证未通过
		}

		$thisUser->user_name = $user_name;
		$thisUser->user_sex  = $user_sex;
		$thisUser->phone_num = $phone_num;

		if ($thisUser->save()) 
		{
			$cacheKey = $isDriver ? CacheR::DRIVERINFO.$thisUser->id : CacheR::USERINFO.$thisUser->id;

			if ($isDriver)
			{
				$cacheKey  = CacheR::DRIVERINFO.$thisUser->id;
				$userCache = CacheR::getInstance(CacheR::DRIVERDSN); //司机缓存
			}
			else
			{
				$cacheKey  = CacheR::USERINFO.$thisUser->id;
				$userCache = CacheR::getInstance(CacheR::USERDSN); //用户缓存
			}

			$userCache->set($cacheKey, json_encode($thisUser->attributes)); //cache 司机基本信息
			//HelpTool::getActionInfo ( $thisDriver->id, 2 ); // 新建操作
			$this->_outPut(['tid' => $thisUser->id]);
		}
		else
		{
			$this->_doError(2); //保存失败
		}
	}

	/** 
	  * @name   用户端登陆
	  * @api    index.php?r=Api/UserLogin
	  *
	  * @param  int     $tid      //用户id
	  * @param  string  $im       //md5(imsi + imei)
	  * @return json    $return   //{"s":200,"d":{"tid":"230"}}
	  * 
	  * @author zhanghy
	  * @date 2015-6-29 23:27:09
	  *
	  **/
	public function actionUserLogin()
	{
		$tid  = intval(Common::request('tid', 'R', 0));  //phone number
		$im   = Common::request('im', 'R', '');          //get md5(imsi+imei) info

		//if (!$tid || !$im) $this->_doError(1); //缺少参数

		//不存在tid 则执行注册流程
		if (!$tid)
			$this->userRegister();

		$thisUser = $this->loadUserModel($tid); //查询库中是否有注册信息

		if (!empty($thisUser->imsi) && !empty($thisUser->imei))
		{
			$md5Im = md5($thisUser->imsi.$thisUser->imei);
			if ($md5Im !== strtolower($im))
				$this->_doError(22); //验证未通过

			$this->_outPut(['tid' => $thisUser->id]);
		}
		else
		{
			$this->_doError(20); //未注册
		}
	}

	/** 
	  * @name   司机端登陆
	  * @api    index.php?r=Api/DriverLogin
	  *
	  * @param  int     $tid      //司机id
	  * @param  string  $im       //md5(imsi + imei)
	  * @return json    $return   //{"s":200,"d":{"tid":"230"}}
	  * 
	  * @author zhanghy
	  * @date 2015-6-29 23:27:09
	  *
	  **/
	public function actionDriverLogin()
	{
		$tid  = intval(Common::request('tid', 'R', 0));  //phone number
		$im   = Common::request('im', 'R', '');          //get md5(imsi+imei) info

		if (!$tid || !$im) $this->_doError(1); //缺少参数

		//不存在tid 则执行注册流程
		/*if (!$tid)
			$this->driverRegister();*/

		$thisDriver = $this->loadDriverModel($tid); //查询库中是否有注册信息

		if (!empty($thisDriver->imsi) && !empty($thisDriver->imei))
		{
			$md5Im = md5($thisDriver->imsi.$thisDriver->imei);
			if ($md5Im !== strtolower($im))
				$this->_doError(22); //验证未通过

			$this->_outPut(['tid' => $thisDriver->id]);
		}
		else
		{
			$this->_doError(20); //未注册
		}
	}

	/** 
	  * @name   用户下单
	  * @api    index.php?r=Api/PlaceOrder
	  *
	  * @param  int     $tid      //用户id
	  * @param  string  $im       //md5(imsi + imei)
	  * @param  string  $phone_num//手机号码
	  * @param  string  $start    //起点经纬度 lon,lat
	  * @param  string  $sname    //起点名称
	  * @param  string  $des      //终点经纬度 lon,lat #支持多终点后 字段暂时废弃
	  * @param  json    $desname  //终点集合 [['des'=>'23.13,13.13', 'desname'=>'地点']]
	  * @param  int     $otype    //订单类型 选填
	  * @param  int     $ostatus  //订单状态 选填
	  * @param  int     $cartype  //车型 默认小 1=>小 2=>中 3=>大
	  * @param  string  $deliverytime //预约送货时间 选填 YYYY-mm-dd HH:ii:ss
	  * @param  int     $needporter   //需要搬运 选填 (需要=>1 不需要=>0)
	  * @param  string  $omessage     //给司机留言 选填
	  * @return json $return          //{"s":200,"d":{"oid":"47"}} 订单id
	  * 
	  * @author zhanghy
	  * @date 2015-6-29 23:27:09
	  *
	  **/
	public function actionPlaceOrder()
	{
		$tid     = intval(Common::request('tid', 'R', 0));   //user id
		$im      = Common::request('im', 'R', '');           //get md5(imsi+imei) info
		$phone   = Common::request('phone_num', 'R', '');    //phone number
		$start   = Common::request('start', 'R', '');        //start lon,lat
		$sname   = Common::request('sname', 'R', '');        //start name
		$des     = Common::request('des', 'R', 'empty');          //destination lon,lat
		$desname = Common::request('desname', 'R', '');      //destination name
		$otype   = intval(Common::request('otype', 'R', 0)); //order name
		$ostatus = intval(Common::request('ostatus', 'R', 0));//order name
		$cartype = intval(Common::request('cartype', 'R', 1));//car type
		$deliverytime = Common::request('deliverytime', 'R', '');//delivery time
		$needporter = intval(Common::request('needporter', 'R', 0));//is need porter
		$omessage = Common::request('omessage', 'R', '');//order message

		if (!$tid || !$im || !$start || !$phone || !$cartype) $this->_doError(1); //缺少参数

		if (is_null(json_decode($desname, true))) $this->_doError(31); //des json error

		if ($deliverytime)
		{
			if (!strtotime($deliverytime)) $this->_doError(31); //des json error
		}
		else
		{
			$deliverytime = date('Y-m-d H:i:s');
		}

		if (strlen($phone) !== 11) $this->_doError(10); //手机号不正确

		$thisUser = $this->loadUserModel($tid); //查询库中是否有注册信息

		if (!empty($thisUser->imsi) && !empty($thisUser->imei))
		{
			$md5Im = md5($thisUser->imsi.$thisUser->imei);
			if ($md5Im !== strtolower($im))
				$this->_doError(22); //验证未通过

			$model =  new PlaceOrderInfo();

			$model->attributes = 
			[		
				'tid' 		  => $tid,    //user id
				'did' 		  => 0,       //driver id
				'start' 	  => $start,  //start point location
				'startname'   => $sname,  //start ponit name
				'destination' => $des,    //destination point location
				'desname'     => $desname,//destination point name 
				'o_voice'     => '',      //order voice data
				'o_type'      => $otype,  //order type
				'o_status'    => $ostatus,//order status
				'o_time'      => NOW_TIME,//order placed time
				'phone_num'   => $phone,  //order placed time
				'car_type'    => $cartype, //car type
				'delivery_time'=> $deliverytime, //delivery time
				'need_porter'  => $needporter,   //needporter
				'o_message'    => $omessage,     //car type
			];

			if ($model->save() && $model->id) 
			{
				$userCache = CacheR::getInstance(CacheR::USERDSN);
				$userCache->set(CacheR::ORDERINFO.$model->id, json_encode($model->attributes)); //订单信息
				$userCache->hset(CacheR::ORDERLOCAL, $model->id, $start); //打车起始位置
				$userCache->delete(PlaceOrderInfo::USERORDERSKEY . $tid); //删除订单列表缓存
				$this->_outPut(['oid' => $model->id]);
			}
			else
			{
				$this->_doError(2); //保存失败
			}
		}
		else
		{
			$this->_doError(20); //未注册
		}
	}


	/** 
	  * @name   司机抢单
	  * @api    index.php?r=Api/GrabOrder
	  *
	  * @param  int     $tid       //司机id
	  * @param  int     $oid       //订单号
	  * @param  string  $im        //md5(imsi+imei)
	  * @param  int     $ostatus   //订单状态 选填
	  * @return json    $output    //{"s":200,"d":{"oid":"47"}} 订单id
	  * 
	  * @author zhanghy
	  * @date 2015-07-07 00:48:49
	  *
	  **/
	public function actionGrabOrder()
	{
		$tid     = intval(Common::request('tid', 'R', 0)); //driver id
		$oid     = intval(Common::request('oid', 'R', 0)); //order id
		$im      = Common::request('im', 'R', '');         //get md5(imsi+imei) info
		//$start   = Common::request('start', 'R', '');      //start lon,lat
		//$sname   = Common::request('sname', 'R', '');      //start name
		//$des     = Common::request('des', 'R', '');        //destination lon,lat
		//$desname = Common::request('desname', 'R', '');    //destination name
		//$otype   = Common::request('otype', 'R', 0);       //order type
		$ostatus = Common::request('ostatus', 'R', 1);     //order status

		if (!$tid || !$oid || !$im || !$ostatus) $this->_doError(1); //缺少参数

		$thisDriver  = $this->loadDriverModel($tid); //查询库中是否有注册信息

		if (!empty($thisDriver->imsi) && !empty($thisDriver->imei))
		{
			$md5Im = md5($thisDriver->imsi.$thisDriver->imei);
			if ($md5Im !== strtolower($im))
				$this->_doError(22); //验证未通过

			$thisOrder = $this->loadOrderModel($oid, true);    //查询库中是否有注册信息

			if (!$thisOrder) $this->_doError(40); //订单不存在
			if ($thisOrder->o_status) $this->_doError(41); //订单已被别人抢走了

			$thisOrder->attributes = 
			[
				'did' 		  => $tid,
				//'start' 	  => $lon,
				//'startname'   => $lat,
				//'destination' => $deslon,
				//'desname'     => $deslat,
				//'o_type'      => $otype,
				'o_status'    => $ostatus,
				'o_grab_time' => NOW_TIME
			];

			if ($thisOrder->save()) 
			{
				$userCache = CacheR::getInstance(CacheR::USERDSN);
				$userCache->set(CacheR::ORDERINFO.$thisOrder->id, json_encode($thisOrder->getAttributes())); //订单信息
				$userCache->hdel(CacheR::ORDERLOCAL, $thisOrder->id); //删除打车起始位置
				$this->_outPut(['oid' => $thisOrder->id]);
			}
			else
			{
				$this->_doError(2); //保存失败
			}
		}
		else
		{
			$this->_doError(20); //未注册
		}
	}


	/** 
	  * @name   修改指定订单信息
	  * @api    index.php?r=Api/UpdateOrderInfo
	  *
	  * @param  int     $tid       //用户id
	  * @param  int     $oid       //订单号
	  * @param  string  $im        //md5(imsi+imei)
	  * @param  string  $type      //修改类型 type=phone_num||start||desname||ostatus
	  * @param  string  $phone_num //手机号码
	  * @param  string  $start     //起点经纬度 lon,lat
	  * @param  string  $sname     //起点名称
	  * @param  json    $desname   //终点集合 [['des'=>'23.13,13.13', 'desname'=>'地点']]
	  * @param  int     $ostatus   //订单状态 0=>新订单 1=>已被抢 5=>已完成 10=>已取消 20=>已删除
	  * @param  int     $cartype   //车型 默认小 1=>小 2=>中 3=>大
	  * @return json    $output    //{"s":200,"d":{"oid":"47"}} 订单id
	  * 
	  * @author zhanghy
	  * @date 2015-11-28 08:32:08
	  *
	  **/
	public function actionUpdateOrderInfo()
	{
		$tid     = intval(Common::request('tid', 'R', 0)); //driver id
		$oid     = intval(Common::request('oid', 'R', 0)); //order id
		$im      = Common::request('im', 'R', '');         //get md5(imsi+imei) info

		$type   = Common::request('type', 'R', '');        //update type

		$phone   = Common::request('phone_num', 'R', '');  //phone num
		$start   = Common::request('start', 'R', '');      //start lon,lat
		$sname   = Common::request('sname', 'R', '');      //start name
		//$des     = Common::request('des', 'R', '');      //destination lon,lat
		$desname = Common::request('desname', 'R', '');    //destination name
		//$otype   = Common::request('otype', 'R', 0);      //order type
		$ostatus = intval(Common::request('ostatus', 'R', 0)); //order status
		$cartype = intval(Common::request('cartype', 'R', 1)); //car type


		if (!$tid || !$oid || !$im) $this->_doError(1); //缺少参数

		if (!in_array($type, ['phone_num', 'start', 'desname', 'ostatus'])) $this->_doError(1); //缺少参数

		$updateInfo = [];

		if ($type == 'phone_num')
		{
			if (strlen($phone) !== 11) $this->_doError(10); //手机号不正确
			$updateInfo = ['phone_num' => $phone];
		}
		else if ($type == 'start')
		{
			if (!$start || !$sname) $this->_doError(1); //缺少参数
			$updateInfo = ['start' => $start, 'startname' => $sname];
		}
		else if ($type == 'desname')
		{
			if (!$desname) $this->_doError(1); //缺少参数
			$updateInfo = ['desname' => $desname];
		}
		else if ($type == 'ostatus')
		{
			$updateInfo = ['o_status' => $ostatus];
		}
		else if ($type == 'cartype')
		{
			if (!$cartype) $this->_doError(1); //缺少参数
			$updateInfo = ['car_type' => $cartype];
		}

		$thisUser  = $this->loadUserModel($tid); //查询库中是否有注册信息

		if (!empty($thisUser->imsi) && !empty($thisUser->imei))
		{
			$md5Im = md5($thisUser->imsi.$thisUser->imei);
			if ($md5Im !== strtolower($im))
				$this->_doError(22); //验证未通过

			$thisOrder = $this->loadOrderModel($oid, true);    //查询库中是否有订单信息

			if (!$thisOrder) $this->_doError(40); //订单不存在
			
			$thisOrder->attributes = $updateInfo;

			if ($thisOrder->save()) 
			{
				$userCache = CacheR::getInstance(CacheR::USERDSN);
				$userCache->set(CacheR::ORDERINFO.$thisOrder->id, json_encode($thisOrder->getAttributes())); //订单信息
				$userCache->delete(PlaceOrderInfo::USERORDERSKEY . $tid); //删除订单列表缓存

				if ($type == 'start')
					$userCache->hset(CacheR::ORDERLOCAL, $thisOrder->id, $start); //更新打车起始位置
				else if ($type == 'ostatus' && $ostatus > 0)
					$userCache->hdel(CacheR::ORDERLOCAL, $thisOrder->id); //删除打车起始位置

				$this->_outPut(['oid' => $thisOrder->id]);
			}
			else
			{
				$this->_doError(2); //保存失败
			}
		}
		else
		{
			$this->_doError(20); //未注册
		}
	}


	/** 
	  * @name   司机定时检查状态
	  * @api    index.php?r=Api/DriverCheck
	  *
	  * @param  int     $tid       //司机id
	  * @param  string  $im        //md5(imsi+imei)
	  * @param  int     $local     //司机所在位置 lon,lat
	  * 
	  * @return json    $output     //json数据 
	  *{"s":200,"d":{"402":{"sname":"\u5927\u660e\u6e56","desname":"\u8db5\u7a81\u6cc9","distance":0},"409":{"sname":"\u5927\u660e\u6e561","desname":"\u8db5\u7a81\u6cc91","distance":1783}}}
	  * 
	  * @author zhanghy
	  * @date 2015-6-29 23:27:09
	  *
	  **/
	public function actionDriverCheck()
	{
		$tid     = intval(Common::request('tid', 'R', 0)); //driver id
		$im      = Common::request('im', 'R', '');         //get md5(imsi+imei) info
		$local   = Common::request('local', 'R', '');      //driver lon,lat

		if (!$tid || !$im || !$local) $this->_doError(1); //缺少参数

		$driverCache = CacheR::getInstance(CacheR::DRIVERDSN);
		$driverCache->hset(CacheR::DRIVERLOCAL, $tid, $local); //cache 司机位置集合

		$maxDis    = 2000; // 最大搜索距离
		$userCache = CacheR::getInstance(CacheR::USERDSN);
		$localArr  = $userCache->hgetall(CacheR::ORDERLOCAL); //cache 打车起始位置集合
		!$localArr && $oLocalArr = [];

		$grabOrder = [];  //可抢单的订单id集合

		$local  = explode(',', $local); //司机位置

		if (!$local) $this->_doError(); //不合法位置

		foreach ($localArr as $oid => $oLocal)
		{
			
			$oLocal  = explode(',', $oLocal);
			//获取每个点距离车辆的距离
			$thisDis = GISHelper::getDis($local[0],$local[1],$oLocal[0],$oLocal[1]);

			if ($thisDis < $maxDis)
			{
				$orderInfo = $this->loadOrderModel($oid);

				//添加订单号=>距离
				$orderInfo && $grabOrder[] = 
				[
					'oid'      =>  $oid,
					'start'    =>  $orderInfo->start,
					'sname'    =>  $orderInfo->startname,
					'desname'  =>  json_decode($orderInfo->desname, true),
					'distance' =>  $thisDis,
				];
			}
		}

		$this->_outPut($grabOrder);
	}

	/** 
	  * @name   用户定时检查状态
	  * @api    index.php?r=Api/UserCheck
	  *
	  * @param  int     $tid       //user id
	  * @param  string  $im        //md5(imsi+imei)
	  * @param  string  $oids      //订单id  22,33,44
	  * @return json    $output    //json数据
	  * 
	  * @author zhanghy
	  * @date 2015-6-29 23:27:09
	  *
	  **/
	public function actionUserCheck()
	{
		$tid     = intval(Common::request('tid', 'R', 0)); //user id
		$im      = Common::request('im', 'R', '');         //get md5(imsi+imei) info
		$oids    = Common::request('oids', 'R', '');       //driver lon,lat

		if (!$tid || !$im || !$oids) $this->_doError(1);   //缺少参数

		$userCache = CacheR::getInstance(CacheR::USERDSN);

		//从cache中获取所有数据
		if ($allOrders = $userCache->hgetall(PlaceOrderInfo::USERORDERSKEY))
		{
			array_walk($allOrders, function(&$v, $k){
				$v = json_decode($v, true);
			});
		}
		else
		{
			//cache 失效 重新获取数据
			$allOrders = PlaceOrderInfo::model()->findAll("tid = $tid AND ");
			$allOrders = HelpTool::getFindAllData($allOrders);

			if ($allOrders)
			{
				$jsonData = array();

				foreach ($allOrders as $order)
				{
					$jsonData[$order['id']] = json_encode($order);
				}

				$saveInfo  = $userCache->hmset(PlaceOrderInfo::USERORDERSKEY, $jsonData); //cache 用户所有订单
			}
		}

		$this->_outPut($grabOrder);
	}

	/** 
	  * @name   查找附近司机
	  * @api    index.php?r=Api/FindDriver
	  *
	  * @param  int     $tid       //用户id
	  * @param  string  $im        //md5(imsi+imei)
	  * @param  string  $local     //用户所在位置 lon,lat
	  * @return json    $output    //json数据
	  * {"s":200,"d":{"230":{"dis":0,"local":"117.015203660820,36.67141504150635","uname":"111"}}}
	  * 
	  * @author zhanghy
	  * @date 2015-6-29 23:27:09
	  *
	  **/
	public function actionFindDriver()
	{
		$tid     = intval(Common::request('tid', 'R', 0)); //driver id
		$im      = Common::request('im', 'R', '');         //get md5(imsi+imei) info
		$local   = Common::request('local', 'R', '');      //driver lon,lat

		if (!$tid || !$im || !$local) $this->_doError(1); //缺少参数

		$local  = explode(',', $local); //司机位置

		if (!$local) $this->_doError(); //不合法位置

		$maxDis    = 2000; // 最大搜索距离
		$userCache = CacheR::getInstance(CacheR::DRIVERDSN);
		$localArr  = $userCache->hgetall(CacheR::DRIVERLOCAL); //cache 司机位置集合
		!$localArr && $oLocalArr = [];

		foreach ($localArr as $tid => &$driverLocal)
		{
			$driverLocal  = explode(',', $driverLocal);
			//获取每个点距离车辆的距离
			$thisDis = GISHelper::getDis($local[0],$local[1],$driverLocal[0],$driverLocal[1]);

			if ($thisDis < $maxDis)
			{
				$driverInfo = $this->loadDriverModel($tid);

				if ($driverInfo)
				{
					$driverInfo = isset($driverInfo->user_name) ? $driverInfo->user_name : '';
					//添加司机id=>[距离, 位置, 司机名称]
					$driverLocal = 
					[
						'dis'    =>  $thisDis,
						'local'  =>  implode(',', $local),
						'uname'  =>  $driverInfo,
					];
				}				 
			}
		}

		$this->_outPut($localArr);
	}

	/** 
	  * @name   获取用户订单列表
	  * @api    index.php?r=Api/GetUserOrders
	  *
	  * @param  int     $tid      //user id
	  * @param  string  $im       //md5(imsi+imei)
	  * @param  string  $ostatus  //指定状态的订单 获取全部订单无需此参数 (20 || 0,10,20)
	  *                            0=>新订单 1=>已被抢 5=>已完成 10=>已取消 20=>已删除
	  * @param  int     $page     //页数 默认为第一页
	  * @param  int     $limit    //条数限制 默认为20条
	  * @return json    $output   //json数据
	  * {"s":200,"d":{"pageCount":1,"currentData":[{"id":"444","tid":"230","did":"0","start":"117.015203660830,36.67141504150635","startname":"\u5927\u660e\u6e561","destination":"117.0138204032577,36.660222192719935","o_voice":"","o_type":"0","o_status":"0","o_time":"1448935664","phone_num":"13196588255","car_type":"0","o_date":"2015-12-01 10:07:44"}],"dataCount":5,"restNum":0}}
	  * 
	  * @author zhanghy
	  * @date 2015-6-29 23:27:09
	  *
	  **/
	public function actionGetUserOrders()
	{
		$tid     = intval(Common::request('tid', 'R', 0));  //user id
		$im      = Common::request('im', 'R', '');          //get md5(imsi+imei) info
		$ostatus = Common::request('ostatus', 'R', null);   //获取指定状态订单
		$page    = intval(Common::request('page', 'R', 1)); //page num
		$limit   = intval(Common::request('limit', 'R', 20));//data limit 默认20条

		if (!$tid || !$im || !$page || !$limit) $this->_doError(1); //缺少参数

		if (!is_null($ostatus)) $ostatus = explode(',', $ostatus);


		$userCache = CacheR::getInstance(CacheR::USERDSN);

		//从cache中获取所有数据
		$cacheKey = PlaceOrderInfo::USERORDERSKEY . $tid;
		if ($allOrders = $userCache->hgetall($cacheKey))
		{
			$tempArray = [];
			foreach ($allOrders as $oVal)
			{
				$oVal = json_decode($oVal, true);
				if (is_null($ostatus) || in_array($oVal['o_status'], $ostatus))
				{
					$tempArray[] = $oVal;
				}
			}

			$allOrders = $tempArray;
		}
		else
		{
			//cache 失效 重新获取数据
			$allOrders = PlaceOrderInfo::model()->findAll("tid = $tid");
			$allOrders = HelpTool::getFindAllData($allOrders);

			if ($allOrders)
			{
				$jsonData = array();

				foreach ($allOrders as $oKye => &$order)
				{
					//!empty($order['desname']) && $order['desname'] = json_decode($order['desname'], true);
					unset($order['desname']);
					$order['o_date'] = date('Y-m-d H:i:s', $order['o_time']);
					$jsonData[$order['id']] = json_encode($order);
					if (!is_null($ostatus) && !in_array($order['o_status'], $ostatus))
					{
						unset($allOrders[$oKye]);
					}
				}

				$userCache->hmset($cacheKey, $jsonData); //cache 用户所有订单
				$userCache->expire($cacheKey, 600);
			}
		}

		$allOrders = HelpTool::my_array_multisort($allOrders, 'o_time', SORT_DESC);

		$pageData  = HelpTool::getPaging($allOrders, $page, $limit);

		$this->_outPut($pageData);
	}

	/** 
	  * @name   获取指定订单信息
	  * @api    index.php?r=Api/GetOrderInfo
	  *
	  * @param  int     $tid      //user id
	  * @param  string  $im       //md5(imsi+imei)
	  * @param  int     $oid      //order id
	  * @return json    $output   //json数据
	  * {"s":200,"d":{"id":"422","tid":"405","did":"0","start":"36.669366,117.14809","startname":"\u5c71\u4e1c\u7701\u6d4e\u5357\u5e02\u5386\u57ce\u533a\u821c\u534e\u8def2000","destination":"empty","desname":[{"desname":"\u5343\u4f5b\u5c71\u516c\u56ed","des":" 36.644733, 117.041787"},{"desname":"\u5343\u4f5b\u5c71\u516c\u56ed","des":" 36.644733, 117.041787"}],"o_voice":"","o_type":"0","o_status":"0","o_time":"1448352696","phone_num":"15668343112","o_date":"2015-11-24 16:11:36"}}
	  * 
	  * @author zhanghy
	  * @date 2015-11-25 14:23:13
	  *
	  **/
	public function actionGetOrderInfo()
	{
		$tid     = intval(Common::request('tid', 'R', 0));  //user id
		$im      = Common::request('im', 'R', '');          //get md5(imsi+imei) info
		$oid     = intval(Common::request('oid', 'R', 0));  //user id

		if (!$tid || !$im || !$oid) $this->_doError(1); //缺少参数

		$userCache = CacheR::getInstance(CacheR::USERDSN);

		//从cache中获取当前oid的数据
		$cacheKey = PlaceOrderInfo::ORDERINFOKEY . $oid;
		if ($orderInfo = $userCache->get($cacheKey))
		{
			$orderInfo = json_decode($orderInfo, true);
		}
		else
		{
			//cache 失效 重新获取数据
			$orderInfo = $this->loadOrderModel($oid, true);

			if ($orderInfo)
			{
				$orderInfo = $orderInfo->attributes;
				!empty($orderInfo['desname']) && $orderInfo['desname'] = json_decode($orderInfo['desname'], true);
				$orderInfo['o_date'] = date('Y-m-d H:i:s', $orderInfo['o_time']);

				$orderInfo['driverInfo'] = [];

				if ($orderInfo['did'])
				{
					$orderInfo['driverInfo'] = $this->loadDriverModel($orderInfo['did']);
				}

				$userCache->set($cacheKey, json_encode($orderInfo)); //cache 用户所有订单
				$userCache->expire($cacheKey, 1800);
			}
		}

		$this->_outPut($orderInfo);
	}

	/** 
	  * @name   获取司机订单列表
	  * @api    index.php?r=Api/GetDriverOrders
	  *
	  * @param  int     $tid      //driver id
	  * @param  string  $im       //md5(imsi+imei)
	  * @param  string  $ostatus  //指定状态的订单 获取全部订单无需此参数 (20 || 0,10,20)
	  *                            0=>新订单 1=>已被抢 5=>已完成 10=>已取消 20=>已删除
	  * @param  int     $page     //页数 默认为第一页
	  * @param  int     $limit    //条数限制 默认为20条
	  * @return json    $output   //json数据
	  * 
	  * @author zhanghy
	  * @date 2015-12-03 10:16:01
	  *
	  **/
	public function actionGetDriverOrders()
	{
		$tid     = intval(Common::request('tid', 'R', 0));  //user id
		$im      = Common::request('im', 'R', '');          //get md5(imsi+imei) info
		$ostatus = Common::request('ostatus', 'R', null);   //获取指定状态订单
		$page    = intval(Common::request('page', 'R', 1)); //page num
		$limit   = intval(Common::request('limit', 'R', 20));//data limit

		if (!$tid || !$im || !$page || !$limit) $this->_doError(1); //缺少参数

		if (!is_null($ostatus)) $ostatus = explode(',', $ostatus);

		$driverCache = CacheR::getInstance(CacheR::DRIVERDSN);

		//从cache中获取所有数据
		$cacheKey = PlaceOrderInfo::DRIVERORDERSKEY . $tid;
		if ($allOrders = $driverCache->hgetall($cacheKey))
		{
			$tempArray = [];
			foreach ($allOrders as $oVal)
			{
				$oVal = json_decode($oVal, true);
				if (is_null($ostatus) || in_array($oVal['o_status'], $ostatus))
				{
					$tempArray[] = $oVal;
				}
			}

			$allOrders = $tempArray;
		}
		else
		{
			//cache 失效 重新获取数据
			$allOrders = PlaceOrderInfo::model()->findAll("did = $tid");
			$allOrders = HelpTool::getFindAllData($allOrders);

			if ($allOrders)
			{
				$jsonData = array();

				foreach ($allOrders as $oKye => &$order)
				{
					//!empty($order['desname']) && $order['desname'] = json_decode($order['desname'], true);
					unset($order['desname']);
					$order['o_date'] = date('Y-m-d H:i:s', $order['o_time']);
					$jsonData[$order['id']] = json_encode($order);
					if (!is_null($ostatus) && !in_array($order['o_status'], $ostatus))
					{
						unset($allOrders[$oKye]);
					}
				}

				$driverCache->hmset($cacheKey, $jsonData); //cache 司机所有订单
				$driverCache->expire($cacheKey, 600);
			}
		}

		$allOrders = HelpTool::my_array_multisort($allOrders, 'o_time', SORT_DESC);

		$pageData  = HelpTool::getPaging($allOrders, $page, $limit);

		$this->_outPut($pageData);
	}

	public function loadUserModel($id, $new = null)
	{
		$userCache = CacheR::getInstance(CacheR::USERDSN); //用户缓存
		$cacheKey  = CacheR::USERINFO.$id;
		$hasCache    = $userCache->exists($cacheKey);
		if (is_null($new) && $hasCache)
		{
			$data  =  $userCache->get($cacheKey);
			return json_decode($data, false);
		}
		else
		{
			$model = UserInformation::model()->findByPk($id);
			if(is_null($model))
			{
				return null;
			}
			else
			{
				if (!$hasCache)
				{
					$data = $model->getattributes();
					!$userCache->set($cacheKey, json_encode($data)) && $model = NULL;
				}
				return $model;
			}
		}
	}

	public function loadDriverModel($id, $new = null)
	{
		$driverCache = CacheR::getInstance(CacheR::DRIVERDSN); //用户缓存
		$cacheKey    = CacheR::DRIVERINFO.$id;
		$hasCache    = $driverCache->exists($cacheKey);
		if (is_null($new) && $hasCache)
		{
			$data    =  $driverCache->get($cacheKey);
			return json_decode($data, false);
		}
		else
		{
			$model = DriverInformation::model()->findByPk($id);
			if(is_null($model))
			{
				return null;
			}
			else
			{
				if (!$hasCache)
				{
					$data = $model->getattributes();
					!$driverCache->set($cacheKey, json_encode($data)) && $model = NULL;
				}
				return $model;
			}
		}
	}

	public function loadOrderModel($id, $new = null)
	{
		$userCache = CacheR::getInstance(CacheR::USERDSN); //用户缓存
		$cacheKey  = CacheR::ORDERINFO.$id;
		$hasCache  = $userCache->exists($cacheKey);
		if (is_null($new) && $hasCache)
		{
			$data  =  $userCache->get($cacheKey);
			return json_decode($data, false);
		}
		else
		{
			$model = PlaceOrderInfo::model()->findByPk($id);
			if(is_null($model))
			{
				return null;
			}
			else
			{
				if (!$hasCache)
				{
					$data = $model->getattributes();
					!$userCache->set($cacheKey, json_encode($data)) && $model = NULL;
				}
				return $model;
			}
		}
	}

}