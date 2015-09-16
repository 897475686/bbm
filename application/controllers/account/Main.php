<?php


class Main extends MY_Controller {
	function __construct() 
	{
		parent::__construct();
		$this->load->service('user_service');
	}

	/**
	 * [update_userinfo 更新个人资料]
	 * @return [type] [description]
	 */
	public function update_userinfo()
	{
		$info   = $this->sc->input(null);
		$result = $this->user_service->update_userinfo($this->user['id'],$info);
	
		if(!empty($result))
		{
			$output['success'] 	= 0;
			echo json_encode($output);
		}
		else
		{
			$this->error->output('invalid_request');
		}
	}	

	/**
	 * [save_headpic 保存头像]
	 * @return [type] [description]
	 */
	public function save_headpic()
	{
		$result = $this->user_service->save_headpic('headpic', $this->user['id']);
		echo json_encode($result);
	}

	/**
	 * [change_pwd 修改密码]
	 * @return [type] [description]
	 */
	public function change_pwd()
	{
		$pwd 	= $this->sc->input(array('pwd','new_pwd'));
		$result = $this->user_service->change_pwd($this->user['id'],$pwd['pwd'],$pwd['new_pwd']);
		if($result)
		{
			echo json_encode(array('success' => 0));
		}
		else
		{
			$this->error->output('invalid_request');
		}
	}

}