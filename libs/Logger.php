<?php

/**
 * 日志类
 *
 * @package    log
 * @version    $Id$
 */
define('DS', DIRECTORY_SEPARATOR);
define('LOG_PATH',dirname(__FILE__).DS.'..'.DS.'log'.DS);

class ALump_Logger {
	/**
	 * 单个日志文件大小限制
	 *
	 * @var int 字节数
	 */
	private static $i_log_size = 5242880; // 1024 * 1024 * 5 = 5M
	
	/**
	 * 设置单个日志文件大小限制
	 *
	 * @param int $i_size
	 *        	字节数
	 */
	public static function set_size($i_size) {
		if (is_numeric ( $i_size )) {
			self::$i_log_size = $i_size;
		}
	}
	
	/**
	 * 写日志
	 *
	 * @param string $s_message
	 *        	日志信息
	 * @param string $s_type
	 *        	日志类型
	 */
	public static function log($s_message, $s_type = 'log') {
		if((!defined('__DEBUG__') || __DEBUG__ == False) && ($s_type=='log' || $s_type=='debug')){
			return;
		}
		
		// 检查日志目录是否可写
		if (! file_exists ( LOG_PATH )) {
			@mkdir ( LOG_PATH );
		}
		chmod ( LOG_PATH, 0777 );
		if (! is_writable ( LOG_PATH ))
			exit ( 'LOG_PATH is not writeable !' );
		$s_now_time = date ( '[Y-m-d H:i:s]' );
		$s_now_day = date ( 'Ymd' );
		// 根据类型设置日志目标位置
		$s_target = LOG_PATH;
		switch ($s_type) {
			case 'debug' :
				$s_target .= 'Log_' . $s_now_day . '.log';
				break;
			case 'error' :
				$s_target .= 'Err_' . $s_now_day . '.log';
				break;
			case 'log' :
				$s_target .= 'Log_' . $s_now_day . '.log';
				break;
            case 'login' :
				$s_target .= 'Login_' . $s_now_day . '.log';
				break;
            case 'action' :
				$s_target .= 'Action_' . $s_now_day . '.log';
				break;
			default :
				$s_target .= 'Log_' . $s_now_day . '.log';
				break;
		}
		
		// 检测日志文件大小, 超过配置大小则重命名
		if (file_exists ( $s_target ) && self::$i_log_size <= filesize ( $s_target )) {
			$s_file_name = substr ( basename ( $s_target ), 0, strrpos ( basename ( $s_target ), '.log' ) ) . '_' . time () . '.log';
			rename ( $s_target, dirname ( $s_target ) . DS . $s_file_name );
		}
		clearstatcache ();
		// 写日志, 返回成功与否
		if(is_array($s_message) || is_object($s_message)){
			ob_start();
			print_r($s_message);
			$s_message = ob_get_contents();
			ob_end_clean();
		}
		return error_log ( "$s_now_time $s_message\n", 3, $s_target );
	}
	
	public static function err($s_message){
		return self::log($s_message, "error");
	}
    
    public static function login($s_message){
        return self::log($s_message, "login");
    }
    
    public static function action($s_message){
        $s_message = ALump_Common::loginUser().','.$s_message;
        return self::log($s_message, "action");
    }
}

?>