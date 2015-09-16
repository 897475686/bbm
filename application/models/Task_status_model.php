<?php
class Task_status_model extends CI_Model{
	public function __construct()
	{
		parent::__construct();
	}

	public function get_status_list()
	{
		$query = $this->db->get('task_status')
						  ->result_array();
		return $query;
	}

	public function get_status_arr()
	{
		$result = $this->get_status_list();
		$arr    = array();
		foreach ($result as $k => $v) {
			$arr[$v['id']] = $v['name'];
		}
		return $arr;
	}
}