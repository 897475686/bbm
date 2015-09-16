<?php

class MY_Controller extends CI_Controller {

	public function __construct() {
		parent::__construct();

        $this->user = array();
        $this->default_lang = 'zh-CN';        
        //加载语言
        $this->lang->load('error', $this->default_lang);
        $this->lang->load('base', $this->default_lang);   

		$session_id = $this->sc->input('session_id');
		if(empty($session_id))
		{
			$session_id = $this->input->get('session_id');
		}
		$this->load->service('auth_service');
		if(empty($session_id) || ($user = $this->auth_service->get_session($session_id)) === FALSE) {

            $user['name'] = '请登录';
           // $user['pic']  = base_url().'public/img/icon/question_icon.png';
            $user['role'] = 0;
            $this->user = $user;			
		}
		else {
			$this->user = $user;
		}
		$this->auth_service->check_user_auth();     
		
	}

	public function is_Admin() {

		if($this->login_info !== FALSE
			&& isset($this->login_info->group_id)
			&& $this->login_info->group_id == 1) {
			return TRUE;
		}
		else {
			return FALSE;
		}
		
	}
}