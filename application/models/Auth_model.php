<?php
class auth_model extends CI_Model{
	
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * [get_user_auth 获取所有权限集合]
	 * @return [type] [description]
	 */
	public function get_user_auth($page = 0,$limit = 10, $order = 'id DESC')
	{
		$this->db->order_by($order);
		if( ! empty($limit))
		{
			$this->db->limit($limit,$page*$limit);
		}
		$query = $this->db->get('user_auth')
				 		  ->result_array();
		return $query;
	}

	public function get_user_auth_by_id($aid)
	{
		$query = $this->db->where('id',$aid)->get('user_auth')->row_array();
		return $query;
	}

	public function get_auth_count()
	{
		return $this->db->count_all('user_auth');
	}

	public function delete_auth($auth)
	{
        $this->db->where_in('id',$auth)->delete('user_auth');
        return $this->db->affected_rows() > 0;
	}

	public function update_auth($aid,$arr)
	{
		unset($arr['id']);
		$this->db->where('id',$aid)
				 ->update('user_auth',$arr);
        return $this->db->affected_rows() === 1;		
	}

	public function insert_auth($arr)
	{
		$this->db->insert('user_auth',$arr);
        return $this->db->affected_rows() === 1;			
	}
}