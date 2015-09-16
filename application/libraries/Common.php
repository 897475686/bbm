<?php
class Common{
	/**
	 * 获取真实的IP地址
	 * @return [type] [description]
	 */
	static function getIP() {

	    if (getenv("HTTP_CLIENT_IP") && strcasecmp(getenv("HTTP_CLIENT_IP"), "unknown"))
	        $ip = getenv("HTTP_CLIENT_IP");
	    else
	        if (getenv("HTTP_X_FORWARDED_FOR") && strcasecmp(getenv("HTTP_X_FORWARDED_FOR"), "unknown"))
	            $ip = getenv("HTTP_X_FORWARDED_FOR");
	        else
	            if (getenv("REMOTE_ADDR") && strcasecmp(getenv("REMOTE_ADDR"), "unknown"))
	                $ip = getenv("REMOTE_ADDR");
	            else
	                if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], "unknown"))
	                    $ip = $_SERVER['REMOTE_ADDR'];
	                else
	                    $ip = "unknown";
	    return ($ip);
	}
	
	/**
	 * [get_route 获取路由]
	 * @return [type] [description]
	 */
	static function get_route()
	{
		$CI = &get_instance();
		$class = $CI->router->fetch_class();
		$dir   = $CI->router->fetch_directory();
		$method= $CI->router->fetch_method();
		$route = "{$dir}{$class}/{$method}";
		return $route;
	}

	/**
	 * [get_time_limit 获取截止时间]
	 * @param  [type] $time_limit   [description]
	 * @param  [type] $publish_time [description]
	 * @return [type]               [description]
	 */
	static function get_time_limit($time_limit, $publish_time)
	{
		$time_limit = explode(',', $time_limit);
		if(count($time_limit) == 3)
		{
			$starttime = strtotime($publish_time);
			$endtime   = date('Y-m-d H:i', strtotime("+".$time_limit[0]."day +".$time_limit[1]."hour +".$time_limit[2]."minute",$starttime));
			$time_limit = Common::timediff(time(),strtotime($endtime));
			if( ! empty($time_limit))
			{
				return "{$time_limit['day']},{$time_limit['hour']},{$time_limit['min']}";
			}						
		}
		return "0,0,0";
			
	}

	static function timediff( $begin_time, $end_time ) 
	{ 
	    if ( $begin_time < $end_time ) { 
	        $starttime = $begin_time; 
	        $endtime = $end_time; 
	    } else { 
	    	return FALSE;
	    } 
	    $timediff = $endtime - $starttime; 
	    $days = intval( $timediff / 86400 ); 
	    $remain = $timediff % 86400; 
	    $hours = intval( $remain / 3600 ); 
	    $remain = $remain % 3600; 
	    $mins = intval( $remain / 60 ); 
	    $secs = $remain % 60; 
	    $res = array( "day" => $days, "hour" => $hours, "min" => $mins, "sec" => $secs ); 
	    return $res; 
	} 	
}