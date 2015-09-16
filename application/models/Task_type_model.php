<?php
class Task_type_model extends CI_Model{
	public function __construct()
	{
		parent::__construct();
	}

	public function get_type_list()
	{
		$query = $this->db->get('task_type')
						  ->result_array();
		return $query;
	}

	public function get_type_arr()
	{
		$result = $this->get_type_list();
		$arr    = array();
		foreach ($result as $k => $v) {
			$arr[$v['id']] = $v['name'];
		}
		return $arr;
	}
}