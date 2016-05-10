<?php
class CacheR
{
	/**
     * 缓存dsn设置
     *
     * @var string
     */
	CONST CACHEDSNPREFIX  = 'redis:';
    CONST DRIVERDSN       = 'driver';
	CONST USERDSN         = 'user';

    CONST USERINFO        = 'user:info:';   //用户基本信息
    CONST ORDERINFO       = 'order:info:';  //订单信息
    CONST ORDERLOCAL      = 'order:local';  //订单起点位置

    CONST DRIVERINFO      = 'driver:info:';  //司机基本信息
    CONST DRIVERLOCAL     = 'driver:local';  //司机所在位置


    

    private $_cache;

    private static $_instance;

    /**
     * 实例化
     * 
     * @param string $dsn 缓存dsn 格式:redis:缓存配置信处中key
     */
    private function __construct($dsn = self::USERDSN)
    {
        $this->_cache = MyCache::getInstance(self::CACHEDSNPREFIX.strtolower($dsn));
    }

    /**
     * 获取实例
     *
     * @param string $dsn 源
     * @return object
     */
    public static function &getInstance($dsn = USERDSN)
    {
        if (!isset(self::$_instance[$dsn]))
        {
            self::$_instance[$dsn] = new self($dsn);
        }
        return self::$_instance[$dsn];
    }

    /**
     * 检查指定key是否存在 存在返回值
     * 
     * @param string $key 缓存key
     * @return mixed
     */
    public function exists($key)
    {
        return $this->_cache->exists($key);
    }

    /**
     * 设置键的过期时间
     * 
     * @param string $key  缓存key
     * @param string $time key 过期时间
     * @return mixed
     */
    public function expire($key, $time)
    {
        return $this->_cache->expire($key, $time);
    }

    /**
     * 获取指定key的缓存信息
     * 
     * @param string $key 缓存key
     * @return mixed
     */
    public function get($key)
    {
        return $this->_cache->get($key);
    }


    /**
     * 获取多个key的缓存信息
     * 
     * @param array $keys 缓存key
     * @param string $dsn 缓存dsn
     * @return mixed
     */
    public function mget($keys)
    {
        return $this->_cache->mGet($keys);
    }

    
    /**
     * 设置缓存信息
     * 
     * @param string $key 缓存key
     * @param string $data 数据
     * @param string $expire 生命周期
     * @param string $dsn 缓存信息dsn
     * @return boolean
     */
    public function set($key, $data, $expire = 0)
    {   
        $ret = $this->_cache->set($key, $data);
        if ($ret && $expire > 0) $this->_cache->expire($key, $expire);

        return $ret;
    }
    
     /**
     * 设置多个缓存信息
     * 
     * @param array $key 缓存信息array('key' => $val)
     * @param string $dsn 缓存信息dsn
     * @return boolean
     */
    public function mset($datas)
    {
        return $this->_cache->mSet($datas);
    }


    /**
     * setnx方法
     * 
     * @param array $key 缓存信息array('key' => $val)
     * @param string $dsn 缓存信息dsn
     * @return boolean
     */
    public function setnx($key, $val,$expire = 0)
    {
        $ret = $this->_cache->setnx($key, $val);
        if ($ret && $expire > 0) $this->_cache->expire($key, $expire);
        return $ret;
    }


    /**
     * 写入一个hash值
     * 
     * @param string   $key   缓存键
     * @param int|str  $id    存入键
     * @param string   $data  存入值
     * @param int    $expire  生命周期
     */
    public function hset($key, $id, $data, $expire = 0)
    {
        $ret  = $this->_cache->hset($key, $id, $data);
        if ($ret && $expire > 0) $this->_cache->expire($key, $expire);
        return $ret;
    }

    /**
     * 写入一组hash值
     * 
     * @param string $key 缓存键
     * @param array  $array  存入数组
     * @param int    $expire 生命周期
     */
    public function hmset($key, $array, $expire = 0)
    {
        array_walk($array, 'json_encode');
        $ret = $this->_cache->hmset($key, $array);
        if ($ret && $expire > 0) $this->_cache->expire($key, $expire);
        return $ret;
    }

    /**
     * hash中所有
     */
    public function hgetall($key)
    {
        return $this->_cache->hgetall($key);
    }

    /**
     * hash del id
     */
    public function hdel($key, $id)
    {
        return $this->_cache->hdel($key, $id);
    }

    /**
     * delete key
     */
    public function delete($key)
    {
        return $this->_cache->delete($key);
    }

    /**
     * flush db
     */
    public function flushdb()
    {
        return $this->_cache->flushdb();
    }

    /**
     * 获取列表数据
     * 
     * @param string $key 缓存key
     * @param int $num 条数
     * @param int $start 偏移位置
     * @param string $dsn 缓存设置
     * @return NULL|array
     */
    public function getList($key, $num, $start = 0)
    {   
        $list = $this->_cache->zRange($key, $start, $this->_countOffset($num, $start));
        if (empty($list)) return NULL;
        
        array_walk($list, 'json_decodes');
        return $list;
    }
    
    /**
     * 获取内容
     * 
     * @param string $key 缓存key
     * @param int $id id
     * @return array|NULL
     */
    public function getDetail($key, $id)
    {
        $newsDetail = $this->_cache->hGet($key, $id);
        
        return empty($newsDetail)? NULL: json_decode($newsDetail, TRUE);
    }
    
    /**
     * 计算redis有序集合的正确位置
     * 
     * @param int $num
     */
    private function _countOffset($num, $start = 0)
    {
        return $start + $num - 1;
    }
    
    /**
     * 保存接口上面的列表数据
     * 
     * @param array $newsList
     * @return boolean
     */
    public function saveList(&$listData, $key)
    {
        $tmpKey = $key. '_tmp';
        foreach ($listData as $sort => $item)
        {
            $this->_cache->zAdd($tmpKey, $sort, json_encode($item));
        }
        $this->_cache->rename($tmpKey, $key);
        
        return TRUE;
    }
    
    /**
     * 保存新闻内容列表数据
     * 
     * @param array $details 新闻内容列表
     * @return boolean
     */
    public function saveDetails(&$details, $key)
    {
        return $this->_cache->hMset($key, $details);
    }
    
    /**
     * 保存单条内容
     * 
     * @param string $details 内容
     * @param int $id 内容对应的id
     * @param string $key 缓存key
     * @param string $dsn 缓存dsn
     */
    public function saveDetail(&$details, $id, $key)
    {
        return $this->_cache->hSet($key, $id, $details);
    }
    
}