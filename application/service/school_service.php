<?php
class School_service extends MY_Service{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('school_model');
	}

	public function get_school_list()
	{
		$school = $this->school_model->get_all();
		if( ! empty($school))
		{
			return array('school' => $school, 'onlyschool' => $school['onlyschool']);
		}
		else
		{
			return FALSE;
		}
	}

}