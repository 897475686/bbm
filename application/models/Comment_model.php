<?php

class Comment_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * [get_comment_by_id 根据任务id获得评论]
	 * @param  [type] $tid [任务id]
	 * @return [type]      [description]
	 */
	public function get_comment_by_id($tid) {
		$query = $this->db->where('tid', $tid)
					      ->get('task_comment')
						  ->result_array();

		return $query;
	}

}