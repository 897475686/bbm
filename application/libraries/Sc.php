<?php

/**
 * 黑魔法系列 ^_^
 */

/**
 * Sc 对 CI->input 的进一步封装，并加入了form_validation
 * 在 $this->rule 按下列格式填写规则
 * array(
 * 	'name' => 'max_length[12]|numeric'
 * )
 */

class Sc {
	function __construct() {
		$this->CI = &get_instance();
		$this->CI->load->library('form_validation');
		$this->rule = array(
				 'phone'		=> 'exact_length[11]|numeric',		//手机号码的规则
				 'pwd'			=> 'required|min_length[8]|max_length[36]',
				 'new_pwd' 		=> 'required|min_length[8]|max_length[36]',	
				 'name'			=> 'required|min_length[2]|max_length[30]',
				 'school'		=> 'numeric|required',
				 'role' 		=> 'required|numeric',
				 'pic' 			=> 'max_length[255]',
				 'pid'			=> 'required|numeric',
				 'id' 			=> 'required|numeric',
				 'page' 		=> 'required|numeric',				 
				 'uid' 			=> 'required|numeric',
				 'tid' 			=> 'required|numeric',
				 'uid' 			=> 'required|numeric',
				 'compe_content'=> 'max_length[125]',
				 'task_title'   => 'required|max_length[36]',
				 'task_content' => 'required',
				 'time_limit' 	=> 'max_length[50]',
				 'reward' 		=> 'max_length[255]',
				 'channel' 		=> 'required|max_length[20]',
				 'payment' 		=> 'required|max_length[50]',
				 'realname' 	=> 'required|max_length[25]',
				 'withdraw_amount'=> 'required|numeric|max_length[5]',
				 'recharge_amount'=> 'required|numeric|max_length[3]'		 				 
			);

		//验证错误重定向
		$this->error_redirect = array('script' => NULL);	
	}

	/**
	 * [set_error_redirect 设置错误重定向]
	 * @param [type] $error_redirect [description]
	 */
	public function set_error_redirect($error_redirect)
	{
		$this->error_redirect = $error_redirect;
	}
	/**
	 * [input 输入]
	 * 传入一个名称或一组名称,
	 * 就能根据定义好的规则进行验证，
	 * 如果没有错误就返回输入的值
	 * 否则就返回带有error的键的数组，它的值为没有通过验证的名称
	 * 
	 * @param  $name 的格式为:
	 *         1. 'phone'
	 *         2. array('phone', 'name')
	 *         
	 * @return 数据验证失败: array('error' => 'name')
	 *         成功: 
	 *         			1. phone_value
	 *         			2. array(
	 *         						'phone' => phone_value,
	 *         						'name'	=> name_value
	 *         					)
	 */
	public function input($name, $type = 'post' , $xss = TRUE){

		$ret = array();

		$type =  strtolower($type);
		if(strcmp($type, 'post') != 0 && strcmp($type, 'get') != 0) {
			return array('error' => 'type_error');
		}

		if(is_string($name)) {
			if(isset($this->rule[$name])) {
				$this->CI->form_validation->set_rules($name, $name, $this->rule[$name]);
				if($this->CI->form_validation->run() == FALSE) {
					$this->CI->error->output("invalid_".$name,$this->error_redirect);
				}
			}

			$ret = trim($this->CI->input->$type($name, $xss));
		}
		else if (is_array($name)) {
			foreach ($name as $key => $value) {
			
				if(isset($this->rule[$value])) {
					$this->CI->form_validation->set_rules($value, $value, $this->rule[$value]);
					if($this->CI->form_validation->run() == FALSE) {
						$this->CI->error->output("invalid_".$value,$this->error_redirect);
					}
				}
				
				$ret[$value] = trim($this->CI->input->$type($value, $xss));				
			}
		}
		else if(empty($name)){
			foreach ($_POST as $key => $value) {
				if($key != 'session_id'){
					if(isset($this->rule[$key])) {
						$this->CI->form_validation->set_rules($key, $key, $this->rule[$key]);
						if($this->CI->form_validation->run() == FALSE) {
							return array('error' => "invalid_".$key);
						}
					}				
					 $ret[$key] = trim($this->CI->input->$type($key, TRUE));
				}					
			}
		}		
		return $ret;
	}
}