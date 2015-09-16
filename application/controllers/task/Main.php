<?php



class Main extends MY_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->service('task_service');		
	}

	/**
	 * [index 显示全校专区列表]
	 * @return [type] [description]
	 */
	public function index() 
	{
		$page   = $this->sc->input('page');
		$school = isset($this->user['schoool']) ? $this->user['school'] : NULL;
		$type   = $this->sc->input('type');
		$type	= $type == -1 ? NULL : $type;
		$tasks  = $this->task_service->get_task_list($page,$school,0,0,1,$type);
		echo json_encode($tasks);
	}

}