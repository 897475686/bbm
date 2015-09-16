<?php


class Compe_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}
	/**
	 * 获取某任务id下的所有竞标
	 * @param  [type] $tid [description]
	 * @return [type]      [description]
	 */
	/*
	public function get_compe_by_id($tid){
		$where['tid'] = $tid;

		$query = $this->db->where($where)
						 ->get('task_compe')
						 ->result_array();
		return $query;

	}
	*/
	/**
	 * 获取选标详情
	 * @param  [type] $tid [description]
	 * @return [type]      [description]
	 */
	public function get_compe_detail($uid,$tid){

		$where['uid'] = $uid;
		$where['tid'] = $tid;
		$query = $this->db->where($where)
						 ->get('task_compe')
						 ->row_array();
		return $query;		
	}

	/**
	 * [compe_task 认领任务]
	 * @param [type] $tid [任务id]
	 */
	public function compe_task($uid,$tid,$content){

		$data = array('uid' 		 => $uid,
					  'tid' 		 => $tid,
					  'content' 	 => $content,
					  'publish_time' => date('Y-m-d H-i-s')
					);
		$this->db->insert('task_compe',$data);
		return $this->db->insert_id();
	}	

	/**
	 * [has_compe 查看是否竞标过]
	 * @param [type] $tid [任务id]
	 * @return boolean [description]
	 */
	public function has_compe($tid,$uid)
	{
		$where=array('tid' => $tid,'uid' => $uid);
		$query = $this->db->where($where)
				   		  ->get('task_compe')
				 		  ->row_array();
		return ! empty($query) ? TRUE : FALSE;
	}

	/**
	 * [delete_compe 删除竞标]
	 * @param  [type] $cid [竞标id]
	 * @return [type]      [description]
	 */
	public function delete_compe($cid)
	{
		$this->db->delete('task_compe',array('id' => $cid));
		return $this->db->affected_rows() === 1;
	}

}
