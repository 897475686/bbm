<?php


class Notification_model extends CI_Model {
	public function __construct() {
		parent::__construct();
	}

	/**
	 * [get_notify_msg 获取通知内容]
	 * @param  [type] $str [通知索引]
	 * @return [type]      [description]
	 */
	public function get_notify_msg($str){
		$this->notify_msg = array(
			'compe_task' 			=> array('title' => '您发布的任务有新的认领',				'type' => 0,'href' => 'task_detail'),
			'choose_task_compe'		=> array('title' => '您的认领已被雇主确认',	 				'type' => 0,'href' => 'task_detail'),
			'employee_cancel_task' 	=> array('title' => '您任务已被取消,任务状态变为已发布',	'type' => 0,'href' => 'task_detail'),
			'employer_cancel_task' 	=> array('title' => '您的任务被取消,请确认',				'type' => 0,'href' => 'task_detail'),			
			'confirm_cancel_task' 	=> array('title' => '您取消的任务已被确认',					'type' => 0,'href' => 'task_detail'),
			'complete_task' 		=> array('title' => '您发布的任务已完成,请确认',			'type' => 0,'href' => 'task_detail'),
			'confirm_complete_task' => array('title' => '您完成的任务已被雇主确认,钱已打进您账户,请确认','type' => 0,'href' => 'task_detail')
		);		
		if(isset($this->notify_msg[$str])){
			return $this->notify_msg[$str];
		}else{
			return false;
		}
	}

	/**
	 * [get_notification_list 获取个人通知列表]
	 * @param  [type]  $page      [分页]
	 * @param  [type]  $uid       [用户id]
	 * @param  string  $order     [排序]
	 * @param  integer $limit     [页面限制内容个数]
	 * @param  [type]  $read_flag [阅读状态]
	 * @return [type]             [description]
	 */
	public function get_notification_list(
							$page,
							$uid,
                            $read_flag = NULL,
                            $order = "read_flag,publish_time DESC",
                            $limit = 10
	){
		$where = array();
		if( ! empty($uid)){
			$where['uid'] = $uid;			
		}

		if( ! empty($read_flag)){
			$where['read_flag'] = $read_flag;			
		}

		$query = $this->db->select('a.id, a.type, b.name as type_name, a.title, 
						a.content, a.param, a.publish_time, a.read_flag')
						  ->from('notification as a')
						  ->join('notification_type as b','a.type = b.id','left')
						  ->where($where)
						  ->order_by($order)
						  ->limit($limit, $page * $limit)
						  ->get()
						  ->result_array();
		return $query;

	}

	/**
	 * [add_notification 添加通知]
	 * @param [type] $uid     [接收者id]
	 * @param [type] $type    [通知类型]
	 * @param [type] $title   [通知标题]
	 * @param [type] $content [通知内容]
	 * @param [type] $param   [通知参数]
	 */
	public function add_notification(
					$uid,
					$type,
					$title,
					$content,
					$param
	){
		$data = array(
			'uid'  	 	 	=> $uid,
			'type'	  		=> $type,
			'title'   		=> $title,
			'content' 		=> $content,
			'param'   		=> $param,
			'publish_time'	=> date('Y-m-d H-i-s')
		);

		$query = $this->db->insert('notification',$data);

		return $this->db->affected_rows() === 1;
	}

	/**
	 * [read_notification 改变通知状态为已阅]
	 * @param  [type] $id [通知id]
	 * @return [type]     [description]
	 */
	public function read_notification($id,$uid){
		$where['id'] = $id;
		if(!empty($uid)){
			$where['uid'] = $uid;			
		}		

		$query = $this->db->where($where)->update('notification',array('read_flag' => 1));

		return $this->db->affected_rows() === 1;		
	}

	/**
	 * [check_has_new_notification 查看是否有新的通知]
	 * @return [type] [description]
	 */
	public function check_has_new_notification($uid){
		$where = array();
		if(!empty($uid)){
			$where['uid'] = $uid;
		}
		$where['read_flag'] = 0;

		$query = $this->db->select('id')
						  ->from('notification')
						  ->where($where)
						  ->get()
						  ->result_array();

		return $query;			
	}
	
	/**
	 * [get_type_name 获取消息类型的名称]
	 * @return [type] [description]
	 */
	public function get_type_name($type){
		$query = $this->db->select('name')
						  ->where(array('id' => $type))
		 				  ->get('notification_type')
		 				  ->row_array();
		if(!empty($query)){
			return $query['name'];
		}
		else{
			return "通知";
		}
	}
	
}