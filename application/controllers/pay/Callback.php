<?php



class Callback extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
	}
	
	/**
	 * [get_payment_cb 支付异步回调]
	 * @return [type] [description]
	 */
	public function get_payment_cb()
	{
		$this->load->service('pay_service');
		$this->pay_service->get_payment_cb();
	}
}