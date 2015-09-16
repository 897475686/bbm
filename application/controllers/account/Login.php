<?php


class Login extends MY_Controller {
	function __construct() 
	{
		parent::__construct();
		$this->load->service('user_service');		
	}

	/**
	 * [index 登录请求]
	 * @return [type] [description]
	 */
	public function index() 
	{
		$data   = $this->sc->input(array('phone', 'pwd'));
		$result = $this->user_service->login_action($data['phone'], $data['pwd']);
		if( ! empty($result)) 
		{
			$result['success'] = 0;
			echo json_encode($result);
		}
		else
		{
			//登录失败
			$this->error->output('LOGIN_ERROR');
		}
	}


}