<?php

/**
 * order_model
 */

class Order_model extends CI_Model {
	public function __construct() 
	{
		parent::__construct();
		
	}

	/**
	 * [save_order 保存成功支付订单]
	 * @return [type] [description]
	 */
	public function save_order($order_no, $role, $type, $charge_id =NULL, $amount, $uid, $tid = -1, $extra = "", $client_ip, $time)
	{
		
		$query = $this->db->select('id')
						  ->from('pay_order')
						  ->where('order_no',$order_no)
						  ->get()
						  ->row_array();
		//已保存
		if( ! empty($query))
		{
			return TRUE;
		}

		$data = array(
			'order_no' => $order_no,
			'role' 	   => $role,
			'type' 	   => $type,			
			'charge_id'=> $charge_id,
			'amount'   => $amount,
			'uid' 	   => $uid,
			'tid' 	   => $tid,
			'extra'    => $extra,
			'client_ip'=> $client_ip,
			'time'	   => $time
		);
		$query = $this->db->insert('pay_order',$data);

		return $this->db->insert_id();		
	}

	/**
	 * [delete_order 删除订单]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function delete_order($id)
	{
		$this->db->delete('pay_order',array('id' => $id));
		return $this->db->affected_rows();
	}

	/**
	 * [check_has_task_order 查看是否拥有订单]
	 * @param  [type] $tid  [任务id]
	 * @param  [type] $uid  [用户id]
	 * @param  [type] $role [订单类型]
	 * @return [type]       [description]
	 */
	public function check_has_order($order_no)
	{
		$query = $this->db->where(array('order_no' => $order_no))
						  ->get('pay_order')
						  ->row_array();
		return $query;
	}

	/**
	 * [get_my_order 获取我的账单]
	 * @param  [type]  $role  [支出还是收入]
	 * @param  [type]  $page  [页面数]
	 * @param  string  $order [排序]
	 * @param  integer $limit [页面限制个数]
	 * @return [type]         [description]
	 */
	public function get_my_order(
				$role,
				$page,
				$order = 'time DESC', 				
				$limit = 10
	){
		$table_name = "";
		$query = $this->db->select('amount,metadata,time')
						  ->from($table_name)
						  ->where(array('uid' => $this->user['id'],'role' => $role))
						  ->order_by($order)
						  ->limit($limit,($page-1)*$limit)						  
						  ->get()
						  ->result_array();
		return $query;
	}

	/**
	 * [get_my_statistic 获取账户数据]
	 * @param  [type] $role [description]
	 * @return [type]       [description]
	 */
	public function get_pay_statistic($role, $uid)
	{
		$query = $this->db->select('SUM(amount) as total')
						  ->where(array('uid' => $uid, 'role' => $role))				  
						  ->get('pay_order')
						  ->row_array();
		return  $query;				
	}

}