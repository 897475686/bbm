<?php
class User_payment_model extends CI_Model{
	public function __construct()
	{
		parent::__construct();
	} 

    /**
     * [get_user_payment 获取用户的绑定支付账号]
     * @param  [type] $uid     [用户id]
     * @param  [type] $channel [支付渠道]
     * @return [type]          [description]
     */	
	public function get_user_payment($uid)
	{
		$query = $this->db->where('uid',$uid)
						  ->get('user_payment')
						  ->result_array();				  
		return $query;				  

	}

	/**
	 * [insert_payment 绑定新提现账户]
	 * @param  [type] $uid   [用户id]
	 * @param  array  $field [字段数组]
	 * @return [type]        [description]
	 */
	public function insert_payment($uid,$channel,$payment,$realname)
	{
		$field = array(
			'uid' 	   => $uid,
			'channel'  => $channel,
			'payment'  => $payment,
			'realname' => $realname,
			'add_time' => date('Y-m-d H:i:s',time())
		);

		$query = $this->db->insert('user_payment',$field);

		return $this->db->affected_rows() === 1;
	}	

	/**
	 * [delete_payment 删除提现帐户]
	 * @param  [type] $uid     [description]
	 * @param  [type] $channel [description]
	 * @return [type]          [description]
	 */
	public function delete_payment($uid, $pid)
	{
		$this->db->delete('user_payment',array('id' => $pid,'uid' => $uid));

		return $this->db->affected_rows() === 1;
	}
}