<?php


class Login extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->library('cookie');
	}

	/**
	 * [index 登陆的主页]
	 * @return [type] [description]
	 */
	public function index() {
		$data['title'] = 'BBM';
		$this->load->view('admin/login_view', $data);
	}


	/**
	 * [login_process 登陆的请求]
	 * @return [type] [description]
	 */
	public function login_process() {
		$key = array('username', 'pwd');
		$data = $this->sc->input($key);

		//数据验证失败
		if(is_array($data) && isset($data['error'])) {
			echo json_encode($data);
			return ;
		}
		
		//黑魔法系列
		foreach ($key as $k => $v) {
			$$v = $data[$v];
		}

		if($username == "1" && $pwd == "1") {
			$this->cookie->set_session(array(
					'username'	=> 'Admin',
					'group_id'	=> 1
				));
			redirect(base_url().'Admin/main');
		}
	}
}