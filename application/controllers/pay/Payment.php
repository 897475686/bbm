<?php
/**
 * 我的账户
 */
class Payment extends MY_Controller{
	function __construct(){
		parent::__construct();
		$this->load->service('pay_service');
	}


	public function index()
	{
		$payment = $this->pay_service->get_user_payment($this->user['id']);
		echo json_encode($payment);
	}

	/**
	 * [bind_user_payment 绑定提现绑定账户]
	 * @return [type] [description]
	 */
	public function bind_user_payment(){
		$payment = $this->sc->input(array('channel','payment','realname'));
		$result = $this->pay_service->bind_user_payment($this->user['id'],$payment['channel'],$payment['payment'],$payment['realname']);					
		if(!empty($result))
		{
			echo json_encode(array('success' => 0));
		}
		else
		{
			$this->error->output('invalid_request');
		}
	}

	/**
	 * [delete_payment 删除绑定帐户]
	 * @return [type] [description]
	 */
	public function delete_payment()
	{
		$pid = $this->sc->input('pid');
		$result = $this->pay_service->delete_payment($this->user['id'],$pid);
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