<?php



class Main extends MY_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->service('pay_service');
	}

	public function index()
	{
		$result = $this->pay_service->get_pay_statistic($this->user['id']);
		echo json_encode($result);
	}

	
	/**
	 * [get_order 获取我的所有订单]
	 * @return [type] [description]
	 */
	/*
	public function get_my_order(){
		$order=$this->sc->input(array('role','page'));
		if(is_array($order) && isset($order['error'])) {
			$this->error->output($order['error']);
			return ;
		}

		$data = $this->order_model->get_my_order($order['role'],$order['page']);
		if(!empty($data)){
			$output['success'] = 0;
			$output['order']   = $data;
			echo json_encode($output);
		}
		else{
			$this->error->output('invalid_request');
		}
	}

	*/

}