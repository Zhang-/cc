<?php
class Common
{
	/**
     * 获取Reqest数据
     * @param string $vn    变量名
     * @param string $type  获取类型
     * @param string $default   默认值
     * @param bool $mysqlEncode 是否使用mysql_escape_string
     * @return string/int/array
     */
    public static function request($vn, $type = 'G', $default = NULL, $mysqlEncode = false)
    {
        switch($type)
        {
            case 'G':
                $val = empty($_GET[$vn]) ? $default : $_GET[$vn];
                break;
            case 'P':
                $val = empty($_POST[$vn]) ? $default : $_POST[$vn];
                break;
            case 'C':
                $val = empty($_COOKIE[$vn]) ? $default : $_COOKIE[$vn];
                break;
            case 'S':
                $val = empty($_SESSION[$vn]) ? $default : $_SESSION[$vn];
                break;
            case 'R':
                $val = empty($_REQUEST[$vn]) ? $default : $_REQUEST[$vn];
                break;
            default :
                $val = empty($_REQUEST[$vn]) ? $default : $_REQUEST[$vn];
                break;
        }

        $val && !is_array($val) && $val = trim($val);

        if(!empty($val) && get_magic_quotes_gpc())
        {
            if(is_array($val))
            {
                foreach($val as $key => $v)
                {
                    $val[$key] = stripslashes($v);
                }
            }
            else
            {
                $val = stripslashes($val);
            }
        }

        if(!empty($val) && $mysqlEncode)
        {
            if(is_array($val))
            {
                foreach($val as $key => $v)
                {
                    $val[$key] = mysql_escape_string($v);
                }
            }
            else
            {
                $val = mysql_escape_string($val);
            }
        }

        return $val;
    }

    /**
     * 批量获取Request数据
     * @param array $cols
     * @param string $type
     * @return array
     */
    public static function multiRequest(array $cols, $type = 'G')
    {
        $result = array();

        foreach($cols as $k => $v)
        {
            $result[$v] = trim(self::request($v, $type));
        }

        return $result;
    }

    /**
     * 获取客户端IP
     * @return int(10)
     */
    public static function getip($ip2long = FALSE)
    {
        if(!empty($_SERVER["HTTP_CLIENT_IP"])) $ip = $_SERVER["HTTP_CLIENT_IP"];
        else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        else if(!empty($_SERVER["REMOTE_ADDR"])) $ip = $_SERVER["REMOTE_ADDR"];
        else $ip = NULL;

        $ip && $ip2long && $ip = bindec(decbin(ip2long($ip)));

        return $ip;
    }

    /**
     * [getClientIp description]
     * @return [string] [客户端ip]
     */
    public static function getClientIp()
    {
        $REMOTE_ADDR = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : false;
        $HTTP_CLIENT_IP = !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : false;
        $HTTP_X_FORWARDED_FOR = !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : false;

        $ip = $HTTP_X_FORWARDED_FOR ? $HTTP_X_FORWARDED_FOR : $REMOTE_ADDR;
        $ip = $HTTP_CLIENT_IP ? $HTTP_CLIENT_IP : $REMOTE_ADDR;
        
        return $ip ? $ip : '0';
    }
}