<?php
/**
 * @author Qian Su <aoxue.1988.su.qian@163.com> 2010-11-3
 * @link http://www.phpwind.com
 * @copyright Copyright &copy; 2003-2110 phpwind.com
 * @license 
 */

/**
 * 调试工具
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package
 */
class WDebug {
	
	private static $breakpoint = array();
	public static function setBreakPoint($point = ''){
		if(isset(self::$breakpoint[$point])) return false;
		self::$breakpoint[$point]['time'] = microtime(true);
		self::$breakpoint[$point]['mem'] = memory_get_usage();
		return true;
	}
	/**
	 * 设置调试点
	 * @param string $point 调试点
	 */
	public static function removeBreakPoint($point = ''){
		if($point){
			if(isset(self::$breakpoint[$point])) unset (self::$breakpoint[$point]);
		}else{
			self::$breakpoint = array();
		}
	}
	
	/**
	 * 取得系统运行所耗内存
	 */
	public static function getMemUsage(){
		$useMem = memory_get_usage()- USEMEM_START;
		return $useMem ? round($useMem/1024,4) : 0;
	}
	
	/**
	 * 取得系统运行所耗时间
	 */
	public static function getExecTime(){
		$useTime = microtime(true) - RUNTIME_START;
		return $useTime ? round($useTime,4) : 0;
	}
	
	/**
	 * 获取调试点
	 * @param $point
	 * @param $label
	 */
	public static function getBreakPoint($point,$label = ''){
		if(!isset(self::$breakpoint[$point])) return array();
		return $label ? self::$breakpoint[$point][$label] : self::$breakpoint[$point];
	}
	

	/**
	 * 调试点之间系统运行所耗内存
	 * @param string $beginPoint 开始调试点
	 * @param string $endPoint   结束调试点
	 * @return float 
	 */
	public static function getMemUsageOfp2p($beginPoint,$endPoint = ''){
		if(!isset(self::$breakpoint[$beginPoint])) return 0;
		$endMemUsage = isset(self::$breakpoint[$endPoint]) ? self::$breakpoint[$endPoint]['mem'] : memory_get_usage();
		$useMemUsage = $endMemUsage - self::$breakpoint[$beginPoint]['mem'];
		return round($useMemUsage/1024,4);
	}
	
	/**
	 * 调试点之间的系统运行所耗时间
	 * @param string $beginPoint 开始调试点
	 * @param string $endPoint   结束调试点
	 * @return float 
	 */
	public static function getExecTimeOfp2p($beginPoint,$endPoint = ''){
		if(!isset(self::$breakpoint[$beginPoint])) return 0;
		$endTime = self::$breakpoint[$endPoint] ? self::$breakpoint[$endPoint]['time'] : microtime(true);
		$useTime = $endTime - self::$breakpoint[$beginPoint]['time'];
		return round($useTime,4);
	}
	
	/**
	 * 堆栈情况
	 * @param array $trace 堆栈引用，如异常
	 * @return array 
	 */
	public static function trace($trace = array()){
    	$debugTrace = $trace ? $trace : debug_backtrace();
       	$traceInfo = array();
        foreach($debugTrace as $info){
        	$info['args'] = self::traceArgs($info['args']);
            $str = '['.date("Y-m-d H:i:m").'] '.$info['file'].' (line:'.$info['line'].') ';
            $str .= $info['class'].$info['type'].$info['function'].'(';
            $str .= implode(', ', $info['args']);
            $str .= ")";
            $traceInfo[] = $str;
         }
         return $traceInfo;
	}
	/**
	 * 获取系统所加载的文件
	 */
	public static function loadFiles(){
		return get_included_files();
	}
	
	public static function debug($trace = array(),$begin = '',$end = ''){
		$runtime = self::getExecTime();
		$useMem = self::getMemUsage();
		$separate = "\r\n";
		$trace = implode("{$separate}",self::trace($trace));
		$debug  = "您系统整体运行情况:{$separate}";
		$debug .= "系统运行时间:{$runtime}s{$separate}";
		$debug .= "系统运行所耗内存:{$useMem}byte{$separate}";
		$debug .= "系统堆栈情况:{$separate}{$trace}{$separate}";
		if($begin && $end){
			$PointUseTime = self::getExecTimeOfp2p($begin,$end);
			$PointUseMem = self::getMemUsageOfp2p($begin,$end);
			$debug .= "调试点{$begin}与{$end}之间的系统运行情况:{$separate}";
			$debug .= "调试点之间系统运行时间:{$PointUseTime}s{$separate}";
			$debug .= "调试点之间系统运行所耗内存:{$PointUseMem}byte{$separate}";
		}
		return $debug;
	}

	private static function traceArgs($args = array()){
		foreach($args as $key => $arg){
			if(is_array($arg)) $args[$key] = 'array('.implode(',',$arg).')';
			elseif(is_object($arg)) $args[$key] = 'class '.get_class($arg);
			else $args[$key] = $arg;
		}
		return $args;
	}
	
	
	
	
	
}
?>