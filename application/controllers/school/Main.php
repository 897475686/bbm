<?php
class Main extends MY_Controller{
	public function __construct()
	{
		parent::__construct();
		$this->load->service('school_service');
	}

	public function get_school_list()
	{
		$result = $this->school_service->get_school_list();
		if($result)
		{
			$result['success']	  = 0;
			echo json_encode($result);
		}
		else
		{
			$this->error->output('nodata_error');
		}	
	}
}