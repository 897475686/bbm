<?php
/**
 * Register 用户注册的控制器
 */
class Register extends MY_Controller {
	function __construct() {
		parent::__construct();
		$this->load->service('user_service');
	}

	/**
	 * [register_process 用户注册方法]
	 * @return [type] [description]
	 */
	public function register_process()
	{
		$data = array(
			'username' => 'hhhh',
			'phone'    => '13623017382',
			'pwd'	   => '1234',
			'sex' 	   => '1',
			'school'   => '1'
		);
		//接受数据并验证数据
		//$data = $this->sc->input(array('username', 'phone', 'pwd', 'sex', 'school'));
	
		//写入数据库
		$result = $this->user_service->register_action($data['username'], $data['phone'], $data['pwd'], $data['sex'], $data['school']);
		if( ! empty($result))
		{
			$result['success'] 	  = 0;
			echo json_encode($result);			
		}
		else
		{
			$this->error->output('REGISTER_ERROR');
		}
	}


	/**
	 * [check_phone 检查手机号是否重复]
	 * @return [type] [description]
	 */
	public function check_phone()
	{
		$phone = $this->sc->input('phone');

		if($this->user_service->check_phone($phone)) 
		{
			$this->error->output('phone_repeat');
		}
		else 
		{
			$output['success'] = 0;
			echo json_encode($output);
		}
	}


	/**
	 * [check_username 检查用户名是否重复]
	 * @return [type] [description]
	 */
	/*
	public function check_username()
	{
		$username = $this->sc->input('username');

		if(is_array($username) && isset($username['error'])) {
			$this->error->output($username['error']);
		}
		if($this->user_model->check_username($username)) {
			$this->error->output('same_username');
		}
		else {
			$output['success'] = 0;
			echo json_encode($output);
		}
	}
	*/
}