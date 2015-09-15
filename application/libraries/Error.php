<?php
/**
 * 错误消息类
 * 定义错误的代码及其错误名称
 * 错误类的命名规则
 */

class Error {

	function __construct() {
	}

	/**
	 * [output 错误处理]
	 * @param  [type] $key            [错误代号]
	 * @param  [type] $error_redirect [错误重定向数组]
	 * @return [type]                 [description]
	 */
	public function output($key, $error_redirect = array('script' => "")) {
		$msg = array();
		$msg['error'] = lang('error_'.strtoupper($key));

		//如果重定向脚本不为空
		if( ! empty($error_redirect['script']))
		{
			$msg['script'] = $error_redirect['script'];
		}				
		
		echo json_encode($msg);
		//遇到错误终止运行
		exit();			

	}
}
