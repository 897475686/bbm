<?php
class Recharge extends MY_Controller{

	public function __construct()
	{
		parent::__construct();
		$this->load->service('pay_service');		
	}

	/**
	 * [recharge 充值]
	 * @return [type] [description]
	 */
	public function recharge()
	{
		$r = $this->sc->input(array('channel', 'recharge_amount'));
		$this->pay_service->recharge($r['channel'], $r['recharge_amount'], $this->user['id']);

	}

	/**
	 * [check_payment 查看是否支付]
	 * @return [type] [description]
	 */
	public function check_payment()
	{			
		$charge_id = $this->sc->input('charge_id');
		$result = $this->pay_service->check_payment($charge_id);

		if( ! empty($result))
		{
			echo json_encode(array('success' => 0));
		}
	}	

}