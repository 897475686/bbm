<?php



class Main extends MY_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->service('notification_service');

	}

	public function index() 
	{
		$page = $this->sc->input('page');
		$notification = $this->notification_service->get_notification_list($page,$this->user['id']);
		echo json_encode($notification);
	}

	/**
	 * [read_notification 改变通知状态为已阅]
	 * @return [type] [description]
	 */
	public function read_notification()
	{
		$id = $this->input->post('id');
		$data = $this->notification_service->read_notification($id,$this->user['id']);
		if( ! empty($data))
		{
			$output['success']=0;
			echo json_encode($output);						
		}
		else
		{
			$this->error->output('invalid_request');
		}

	}
	/**
	 * [check_has_new 查看是否有新的未读通知]
	 * @return [type] [description]
	 */
	public function check_has_new_notification()
	{
		$data = $this->notification_service->check_has_new_notification($this->user['id']);
		if( ! empty($data))
		{
			$output['success'] = 0;
			$output['count']   = count($data);
			echo json_encode($output);						
		}
	}	

}