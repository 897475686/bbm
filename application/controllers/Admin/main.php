<?php

class Main extends MY_Controller 
{
	public function __construct() {
		parent::__construct();
		if(! $this->is_Admin()) {
			redirect(base_url().'404');
		}
	}

	public function index() {
		$data['title'] = 'BBM后台管理';
		$this->load->view('admin/main_view', $data);
	}
}