<?php

/**
 * User_model
 */

class User_model extends CI_Model {
	public function __construct() {
		parent::__construct();
		//加载密码库
		$this->load->library('passwordhash');
		// 初始化散列器为不可移植(这样更安全) 
		$this->passwordhash->setPasswordHash(8, FALSE);
		$this->base_field = array(
			'id','name','pic','sex','school','phone','self_tags'
		);
	}

	/**
	 * [check_phone 检查手机号码是否有重复]
	 * @param  [string] $phone [11位手机号码]
	 * @return [boolean]        [TRUE:有重复, FALSE:没有重复]
	 */
	public function check_phone($phone) {
		$query = $this->db->select("phone")
						  ->where('phone', $phone)
						  ->get('user')
						  ->row_array();
		return ! empty($query) ? TRUE : FALSE;
	}

	/**
	 * [check_username 检查用户名是否有重复]
	 * @param  [string] $name [用户名]
	 * @return [boolean]       [TRUE:有重复, FALSE:没有重复]
	 */
	public function check_username($name) {
		$query = $this->db->select('name')
				 		  ->where('name', $name)
						  ->get('user')				 		  
				 		  ->row_array();
		return ! empty($query) ? TRUE : FALSE;
	}

	/**
	 * [user_resgister 用户注册]
	 * @param  [type] $name   [用户名]
	 * @param  [type] $phone  [手机号码]
	 * @param  [type] $pwd    [密码]
	 * @param  [type] $sex    [性别]
	 * @param  [type] $school [学校]
	 * @return [type]         [注册成功为:TRUE, other: FALSE]
	 */
	public function user_register($name, $phone, $pwd, $sex, $school) 
	{
		$check_result = $this->check_phone($phone);
		if( ! $check_result)
		{
			$user = array(
					'name'	=> $name,
					'pwd'	=> $this->passwordhash->HashPassword($pwd),	//对密码加密
					'phone'	=> $phone,
					'sex'	=> $sex,
					'school'=> $school
			);
			$this->db->insert('user', $user);
			$uid = $this->db->insert_id();
			//注册成功
			if( ! empty($uid))
			{
				unset($user['pwd']);
				$user['self_tags'] = '';
				$user['id'] 	   = $uid;
				//$data = $this->get_user_base_id($uid);
				return $user;
			}
			else 
			{
				return FALSE;
			}			
		}
		else
		{
			$this->error->output('PHONE_REPEAT');
		}
	}


	/**
	 * [confirm 验证登陆]
	 * @param  [type] $phone [description]
	 * @param  [type] $pwd   [description]
	 * @return [type]        [description]
	 */
	public function confirm($phone, $pwd)
	{
		$data = $this->db->select('id,name,sex,pic,school,phone,pwd,self_tags')
						 ->from('user')
						 ->where('phone', $phone)
						 ->get()
						 ->result_array();

		//查询成功
		if(count($data) === 1) 
		{
			$data = $data[0];
			//比较密码
			if($this->passwordhash->CheckPassword($pwd, $data['pwd'])) 
			{
				//密码正确
				//登陆成功
				unset($data['pwd']);
				return $data;
			}
		}

		return FALSE;
	}

	/**
	 * [get_user_base_id 根据id获取用户基本信息]
	 * @param  [type] $uid [用户id]
	 * @return [type]      [description]
	 */
	public function get_user_base_id($uid)
	{
		return $this->get_user_by_id($uid,$this->base_field);
	}

	/**
	 * [get_user_by_id 获取用户信息]
	 * @param  [type] $uid [description]
	 * @return [type]      [description]
	 */
	public function get_user_by_id($uid,$custom = array())
	{

		$query = $this->db->where('id',$uid)
						  ->get('user')
						  ->row_array();
						  
		if(! empty($custom) && ! empty($query))
		{
			$arr = array();
			foreach ($custom as $k => $v) {
				$arr[$v] = $query[$v];
			}
			return $arr;
		}				  
		return $query;
	}

	/**
	 * [update_info 更新个人资料]
	 * @param  [type] $info [description]
	 * @return [type]       [description]
	 */
	public function update_user($uid,$arr)
	{
		$where= array(
			'id' => $uid
		);
		$this->db->where($where)->update('user',$arr);

		return $this->db->affected_rows() === 1;
	}

	/**
	 * [check_pwd 检查密码是否正确]
	 * @param  [type] $pwd [description]
	 * @return [type]      [description]
	 */
	public function check_pwd($uid, $pwd){
		$where = array(
			'id' => $uid
		);
		$data = $this->db->select('pwd')
						 ->where($where)
						 ->get('user')
						 ->row_array();
		if(!empty($data))
		{
			return $this->passwordhash->CheckPassword($pwd, $data['pwd']);				
		}
		else
		{
			return FALSE;
		}
	 	
	}

	/**
	 * [change_pwd 修改密码]
	 * @param  [type] $pwd [description]
	 * @return [type]      [description]
	 */
	public function change_pwd($uid, $pwd){
		$data = array(
			'pwd' => $this->passwordhash->HashPassword($pwd)
		);
		$where = array(
			'id'  => $uid
		);
		 $this->db->where($where)->update('user',$data);
		
		return $this->db->affected_rows() === 1;
	}

    public function add_count($uid,$field)
    {
        $table_name = $this->db->protect_identifiers('user', TRUE);
        $this->db->query("UPDATE {$table_name} SET {$table_name}.`{$field}` = {$table_name}.`{$field}` + 1 WHERE {$table_name}.id = {$uid}");
    	return $this->db->affected_rows() === 1;        
    }

    public function reduce_count($uid,$field)
    {
    	$table_name = $this->db->protect_identifiers('user', TRUE);
    	$this->db->query("UPDATE {$table_name} SET {$table_name}.`{$field}` = {$table_name}.`{$field}` - 1 WHERE {$table_name}.id = {$uid} and {$table_name}.`{$field}` > 0");
    	return $this->db->affected_rows() === 1;
    }

    public function update_count($uid,$field,$amount)
    {
    	$table_name = $this->db->protect_identifiers('user', TRUE);
    	$this->db->query("UPDATE {$table_name} SET {$table_name}.`{$field}` = {$table_name}.`{$field}` + {$amount} WHERE {$table_name}.id = {$uid}");
    	return $this->db->affected_rows() === 1;
    }
	/**
	 * [get_field_value 获取特定的键值]
	 * @return [type] [description]
	 */
	public function get_fields_value($uid,$fields){
		$where = array();
		if(!empty($uid)){
			$where['id'] = $uid;
		}
		$query = $this->db->select($fields)
						  ->where($where)
						  ->get('user')
						  ->row_array();
		return $query;
	}
}