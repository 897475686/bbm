<?php



class My extends MY_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->service('task_service');
	}

	public function get_my_task(){
		$task=$this->sc->input(array('role','page'));
		$data = $this->task_service->get_my_task($task['role'],$task['page'],$this->user['id']);
		echo json_encode($data);			
	}
}