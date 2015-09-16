<?php


class Task_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}


	/**
	 * [get_task_list 取得任务列表]
	 * @param  [type]  $page            [页数]
	 * @param  [type]  $school          [学校id]
	 * @param  integer $status          [任务状态]
	 * @param  integer $area          	[专区]
	 * @param  array   $forbidden_topic [description]
	 * @param  string  $order           [description]
	 * @param  integer $limit           [description]
	 * @return [type]                   [description]
	 */
	public function get_task_list(
						$page,
						$schoool 	= NULL,
						$area   	= NULL,
						$status 	= NULL,
						$pay_status = NULL, 
						$type 		= NULL,
						$order  	= 'publish_time DESC', 
						$limit  	= 10
		) {

		//生成 where 的条件
		$where = array();


		if( ! empty($school))
		{
			$where['task.school'] = intval($school);				
		}					
		else
		{
			//$where['task.school'] = 1;
		}
		if($status !== NULL)
		{
			$where['status'] 	= intval($status);
		}
		//支付状态
		if($pay_status !== NULL)
		{
			$where['pay_status'] = $pay_status;				
		}
		if($area !== NULL)
		{
			$where['area'] = $area;			
		}
		if($type !== NULL)
		{
			$where['type'] = $type;
		}

		$query =   $this->db->select('task.id, task.title, task.type, task.area, task.status, reward, task.employer, task.employee, task.time_limit, task.publish_time')
						->where($where)
						->order_by($order)
						->get('task', $limit, $page * $limit)
						->result_array();

		return $query;
	}

	/**
	 * [get_detail_by_id 获取任务详情]
	 * @param  [type] $uid [用户id]
	 * @param  [type] $tid [任务id]
	 * @return [type]      [description]
	 */
	public function get_detail_by_id($tid)
	{

		$query = $this->db->where('id',$tid)
					      ->get('task')
					  	  ->row_array();

		return $query;
	}	

	/**
	 * [get_custom_by_id 获取任务的自定义字段]
	 * @param  [type] $tid [任务id]
	 * @return [type]      [description]
	 */
	public function get_custom_by_id($custom,$tid)
	{
		$query = $this->db->select($custom)
						  ->where('id',$tid)
						  ->get('task')
						  ->row_array();
		return $query;
	}

	/**
	 * [choose_task_compe 选择竞标者]
	 * @param  [type] $me  [我的id] 
	 * @param  [type] $tid [任务id]
	 * @param  [type] $uid [用户id]
	 * @return [type]      [description]
	 */
	
	public function choose_task_compe($tid, $uid) 
	{

		$data = array(
			'employee' 	=> $uid,
			'status'	=> 1, 		//任务进行中
			'start_time'=> date('Y-m-d H-i-s')
		);
		$this->db->where(array('id' => $tid, 'status' => 0, 'employee' => -1))->update('task', $data);
		return $this->db->affected_rows() === 1;
	}
	

	/**
	 * [delete_task 放弃任务]
	 * @return [type] [description]
	 */
	public function delete_task($uid,$tid,$pay_status = NULL)
	{
		
		//权限验证
		if( ! empty($uid))
		{
			$where['employer'] = $uid;
		}
		$where['id'] 	 = $tid;
		$where['status'] = 0;
		 
		if(isset($pay_status)){
			$where['pay_status'] = $pay_status;
		}

		$this->db->where($where)->delete('task');

		return $this->db->affected_rows() === 1;

	}


	/**
	 * [cancel_task 取消任务]
	 * @param  [type] $uid    [取消人id]
	 * @param  [type] $tid    [任务id]
	 * @param  [type] $reason [取消理由]
	 * @param  [type] $status [状态(待取消或已发布)]
	 * @return [type]         [description]
	 */
	public function cancel_task($uid,$tid,$reason,$status)
	{
		$where = array(
			'id' 	   => $tid,
			'status'   => 1,
			'employee' => -1
		);

		$this->db->where($where)
				 ->update('task',
				  	array(
					  	'cancel_reason' => $reason,
					  	'cancel_id' 	=> $uid,
					  	'cancel_time' 	=> date('Y-m-d H-i-s'),
					  	'status' 		=> $status
				  		)
				  );
		return $this->db->affected_rows() === 1;		
	}

	/**
	 * [confirm_cancel_task 确认取消任务]
	 * @param  [type] $tid [任务id]
	 * @return [type] [description]
	 */
	public function confirm_cancel_task($uid,$tid)
	{
		if( ! empty($uid))
		{
			$where['employee'] = $uid;
		}
		$where['id'] 	 = $tid;
		$where['status'] = 5;

		$this->db->where($where)->update('task',array('status' => 3));

		return $this->db->affected_rows() === 1;		
	}

	/**
	 * [complete_task 完成任务]
	 * @param  [type] $tid [任务id]
	 * @return [type]      [description]
	 */
	public function complete_task($uid,$tid)
	{
		if( ! empty($uid))
		{
			$where['employee'] = $uid;
		}
		$where['id'] 	 = $tid;
		$where['status'] = 1;

		$data = array(
			'status' 	  => 4,
			'finish_time' => date('Y-m-d H-i-s',time())
		);
		$this->db->where($where)->update('task',$data);
		return $this->db->affected_rows() === 1;		
	}

	/**
	 * [confirm_complete_task 确认完成任务]
	 * @param  [type] $tid [任务id]
	 * @return [type]      [description]
	 */
	public function confirm_complete_task($uid,$tid)
	{
		if( ! empty($uid))
		{
			$where['employer'] = $uid;
		}
		$where['id'] 	 = $tid;
		$where['status'] = 4;

		$this->db->where($where)->update('task',array('status' => 2));
		return $this->db->affected_rows() === 1;		
	}

	/**
	 * [publish_task 发布任务]
	 * @param  [type] $title      [标题]
	 * @param  [type] $content    [内容]
	 * @param  [type] $type    	  [类型]
	 * @param  [type] $uid    	  [发布者id]
	 * @param  [type] $area    	  [专区]
	 * @param  [type] $schoool    [学校]
	 * @param  [type] $sex     	  [性别]
	 * @param  [type] $time_limit [截止时间]
	 * @param  [type] $reward     [奖励]
	 * @return [type]             [description]
	 */
	public function publish_task($title,$content,$type,$uid,$area,$school,$sex,$time_limit,$reward){
		$data = array(
			'title'   	  => $title,
			'content' 	  => $content,
			'type' 		  => $type,
			'employer' 	  => $uid,
			'area' 		  => $area,
			'school' 	  => $school,
			'reward' 	  => $reward,
			'time_limit'  => $time_limit,
			'publish_time'=> date('Y-m-d H-i-s')
 		);
		$query = $this->db->insert('task',$data);
		return $this->db->insert_id();			
	}
	
	/**
	 * [get_my_task 获取我的任务]
	 * @param  [type] $role  [获取规则]
	 * @param  [type] $page  [页面数]
	 * @param  integer $limit[description]
	 * @return [type]        [description]
	 */
	public function get_my_task(
				$role,
				$page,
				$uid,
				$order = 'publish_time DESC', 				
				$limit = 10
	){
		$where = array();
		//发布者
		if($role == 0)
		{
			$where['employer'] = $uid;
		}
		//接受者
		else if($role == 1)
		{
			$where['employee'] = $uid;
		}
		else
		{
			return null;
		}

		$query = $this->db->select('task.id, task.title, task.type, task.status, task.area, reward, task.employer, task.employee, task.time_limit, task.publish_time')
					  ->where($where)
					  ->order_by($order)
					  ->limit($limit,$page *$limit)
					  ->get('task')
					  ->result_array();

		return $query;		

	}

    public function add_count($tid,$field)
    {
        $table_name = $this->db->protect_identifiers('task', TRUE);
        $this->db->query("UPDATE {$table_name} SET {$table_name}.`{$field}` = {$table_name}.`{$field}` + 1 WHERE {$table_name}.id = {$tid}");
    	return $this->db->affected_rows() === 1;        
    }

	/**
	 * [update_task_pay_status 更新任务支付状态]
	 * @param  [type] $tid        [任务id]
	 * @param  [type] $pay_status [支付状态]
	 * @return [type]             [description]
	 */
	public function update_task_pay_status($tid,$pay_status)
	{
		$this->db->where('id',$tid)->update('task',array('pay_status' =>$pay_status));
		return $this->db->affected_rows() === 1;	
	}
}