<?php

/**
 * Withdraw_model
 */

class Withdraw_model extends CI_Model {
	
	public function __construct() {
		parent::__construct();
	}

	/**
	 * [withdraw_count 获取特定时间内提现次数]
	 * @param  [type] $uid  [用户id]
	 * @param  [type] $time [时间段]
	 * @return [type]       [description]
	 */
	public function get_withdraw_count($uid,$time = NULL)
	{
		$where = 'uid = '.$uid;
		$next_day = date('Y-m-d',strtotime("+1 day"));		
		if( ! empty($time))
		{
			$where.=' and time between "'.$time.'" and "'.$next_day.'"';
		}
		$query = $this->db->select('COUNT(*) as count')
						  ->from('pay_withdraw')
						  ->where($where)
						  ->get()
						  ->row_array();
		if(!empty($query))
		{
			return $query['count'];
		}
		else
		{
			return 0;
		}
	}

	/**
	 * [add_withdraw 添加提现申请]
	 * @param [type] $uid     [用户id]
	 * @param [type] $amount  [金额]
	 * @param [type] $channel [渠道]
	 * @param [type] $payment [提现账号]
	 */
	public function add_withdraw($uid,$amount,$pid){

		$data = array(
			'uid' 	   	  => $uid,
			'amount'      => $amount,
			'pid'  	      => $pid,
			'status'   	  => 0,
			'time' 		  => date('Y-m-d H-i-s',time())
		);
		$query  = $this->db->insert('pay_withdraw',$data);

		return $this->db->affected_rows() === 1;
	}
}