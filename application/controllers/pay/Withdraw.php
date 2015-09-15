<?php
/**
 * 提现
 */
class Withdraw extends MY_Controller{
	function __construct()
	{
		parent::__construct();
		$this->load->service('pay_service');
	}


	public function index()
	{
		$this->pay_service->withdraw_init($this->user['id']);
	}

	/**
	 * [withdraw 提现]
	 * @return [type] [description]
	 */
	public function withdraw()
	{
		$withdraw = $this->sc->input(array('withdraw_amount','pid'));
		$result = $this->pay_service->withdraw($this->user['id'],$withdraw['pid'],$withdraw['withdraw_amount']);
		if( ! empty($result))
		{
			echo json_encode(array('success' => 0));
		}
		else
		{
			$this->error->output('INVALID_REQUEST');
		}
	}
}	