<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Setting 关于整个站点的全局设置
 * 在本目录下创建Setting.json文件，然后读取里面的设置
 * 功能:
 * 1.禁止注册
 * 2.后台维护
 */

class Setting {
	private $param;

	function __construct() {
		$this->param = array();
		$this->Setting_path = APPPATH."libraries\\"."Setting.json";
		
		if(file_exists($this->Setting_path)) {
			$str = file_get_contents($this->Setting_path);
			$this->param = json_decode($str);
		}
	}

	public function get($str) {
		if(isset($this->param->$str)) {
			return $this->param->$str == "on" ? true : false;	
		}
		else {
			return false;
		}
		
	}

	public function set($str, $status) {
		$this->param->$str = $status;
		$file = fopen($this->Setting_path, "w") or die("Unable to open file!");
		fwrite($file, json_encode($this->param));
		fclose($file);
	}
}
?>