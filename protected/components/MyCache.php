<?php
class MyCache
{
    /**
     * 级缓配置信息
     * 
     * @var array
     */
    private static $_config = array();
    
    /**
     * 缓存句柄
     * 
     * @var array
     */
    private static $_handles = array();
    
    /**
     * 对象的例
     * 
     * @var array
     */
    private static $_instance= array();
    
    /**
     * 实例化
     * 
     * @param string $dsn 缓存dsn 格式:redis:缓存配置信处中key
     */
    private function __construct($dsn)
    {
        if (empty(self::$_config))
        {
            self::$_config = Yii::app()->params->cache[strtolower(Yii::app()->params->env)];
        }

        list($dirver, $source) = explode(':', $dsn);
        
        $method = '_get'. ucfirst($dirver). 'Handle';
        self::$_handles[$dsn] = $this->$method($source);
    }
    
    /**
     * 请求结束时调用
     * 
     */
    public static function _unload()
    {
        foreach (self::$_handles as $dsn => $handle)
        {
            if (method_exists($handle, 'close')) $handle->close();
            unset(self::$_handles[$dsn]);
            unset(self::$_instance[$dsn]);
        }
    }
    
    /**
     * 获取实例
     *
     * @param string $dsn 源
     * @return object
     */
    public static function &getInstance($dsn)
    {
        if (!isset(self::$_instance[$dsn]))
        {
            self::$_instance[$dsn] = new self($dsn);
        }
        return self::$_handles[$dsn];
    }

    /**
     * 获取redis的缓存句柄
     * 
     * @param string $source 缓存配置信息key
     * @return object Redis的实例
     */
    private function _getRedisHandle($source)
    {
        $handle = new Redis();
        $config = self::$_config['redis'][$source];
        if (isset($config['socket']) && file_exists($config['socket']))
        {
            $handle->pconnect($config['socket']);
        }
        else
        {
            $handle->connect($config['host'], $config['port']);
            $handle->select($config['db']);
        }

        return $handle;
    }
    
    /**
     * 获取memcache的句柄
     * 
     * @param string $source 缓存配置信息key
     * @return object memcache的实例
     */
    private function _getMemcacheHandle($source)
    {
        $handle = new Memcache();
        $config = self::$_config['memcache'][$source];
        
        $handle->connect($config['host'], $config['port']);
        return $handle;
    }
    
    /**
     * 获取ttserver的句柄
     * 
     * @param string $source
     * @return object ttserver的实例
     */
    private function _getTTHandle($source)
    {
        $handle = new Memcache();
        $config = self::$_config['tt'][$source];
        
        $handle->connect($config['host'], $config['port']);
        return $handle;
    }
}
