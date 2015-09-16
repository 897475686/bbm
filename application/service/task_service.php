<?php
class Task_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('task_model');
        $this->load->model('compe_model');
        $this->load->model('comment_model');
        $this->load->model('user_model');
        $this->load->model('order_model');
        $this->load->service('push_service');
    }

    /*******************任务*********************/

	/**
	 * [get_task_detail 获取任务详情]
	 * @param  [type] $uid [description]
	 * @param  [type] $tid [description]
	 * @return [type]      [description]
	 */
	public function get_task_detail($tid){
		$task = $this->task_model->get_detail_by_id($tid);
		if( ! empty($task))
		{
			$this->load->model('task_status_model');
			//$this->load->model('task_type_model');
			$status = $this->task_status_model->get_status_arr();
			//$type   = $this->task_type_model->get_type_arr();
			if( ! empty($task['employer']))
			{
				$task['employer'] 	  = $this->user_model->get_user_base_id($task['employer']);				
			}
			else
			{
				$task['employer']  = NULL;
			}

			if( ! empty($task['employee']))
			{
				$task['employee'] = $this->user_model->get_user_base_id($task['employee']);
			}
			else
			{
				$task['employee'] = NULL;
			}

			//$task['type_name']   = isset($type[$task['type']])     ? $type[$task['type']] 	  : "";
			$task['status_name'] = isset($status[$task['status']]) ? $status[$task['status']] : "";
			//时限
			if( ! empty($task['time_limit']))
			{
				$task['time_limit'] = Common::get_time_limit($task['time_limit'],$task['publish_time']);
			}			
			/*
			switch($task['status'])
			{
				case 1:
					//获取选中的竞标者信息
					$task['choosen_compe'] 		   = $this->compe_model->get_compe_detail($task['employee']['id'],$tid);
					break;
			}
			*/			
		}				
		return $task;					
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
	public function get_task_list($page, $school, $area, $status, $pay_status, $type) 
	{
		$tasks = $this->task_model->get_task_list($page,$school,$area,$status,$pay_status,$type);
		if( ! empty($tasks))
		{
			/*
			$this->load->model('task_status_model');
			$this->load->model('task_type_model');
			$status = $this->task_status_model->get_status_arr();
			$type   = $this->task_type_model->get_type_arr();
			*/
			foreach ($tasks as $k => $v) 
			{
				if( ! empty($v['employer']))
				{
					$tasks[$k]['employer'] = $this->user_model->get_user_base_id($v['employer']);				
				}
				else
				{
					$tasks[$k]['employer'] = NULL;
				}
				//时限
				if( ! empty($tasks[$k]['time_limit']))
				{
					$tasks[$k]['time_limit'] = Common::get_time_limit($tasks[$k]['time_limit'],$tasks[$k]['publish_time']);
				}
				/*
				$tasks[$k]['type_name']   = isset($type[$v['type']])     ? $type[$v['type']] 	 : "";
				$tasks[$k]['status_name'] = isset($status[$v['status']]) ? $status[$v['status']] : "";			
				*/
			}			
		}		
		return $tasks;
	}

	/**
	 * [get_my_task 获取我的任务]
	 * @param  [type] $role [description]
	 * @param  [type] $page [description]
	 * @param  [type] $uid  [description]
	 * @return [type]       [description]
	 */
	public function get_my_task(
				$role,
				$page,
				$uid
	){
		$task = $this->task_model->get_my_task($role,$page,$uid);
		/*
		$this->load->model('task_status_model');
		$this->load->model('task_type_model');
		$status = $this->task_status_model->get_status_arr();
		$type   = $this->task_type_model->get_type_arr();		
		*/
		foreach ($task as $k => $v)
		{
			/*
			$task[$k]['type_name']   = isset($type[$v['type']])     ? $type[$v['type']] 	  : "";
			$task[$k]['status_name'] = isset($status[$v['status']]) ? $status[$v['status']] : "";
			*/
			if( ! empty($v['employer']) && $v['employer'] != $uid)
			{
				$task[$k]['employer'] = $this->user_model->get_user_base_id($v['employer']);								
			}
			else
			{
				$task[$k]['employer'] = NULL;
			}
			//时限
			if( ! empty($task[$k]['time_limit']))
			{
				$task[$k]['time_limit'] = Common::get_time_limit($task[$k]['time_limit'],$task[$k]['publish_time']);
			}			
		}
		return $task;
	}

	/**
	 * [publish_task 发布任务]
	 * @param  [type] $title      [description]
	 * @param  [type] $content    [description]
	 * @param  [type] $type       [description]
	 * @param  [type] $uid        [description]
	 * @param  [type] $area       [description]
	 * @param  [type] $school     [description]
	 * @param  [type] $sex        [description]
	 * @param  [type] $time_limit [description]
	 * @param  [type] $reward     [description]
	 * @return [type]             [description]
	 */
	public function publish_task($title, $content, $type, $uid, $area, $school, $sex, $time_limit, $reward, $channel)
	{
		$result = $this->task_model->publish_task($title,$content,$type,$uid,$area,$school,$sex,$time_limit,$reward);
		if($result)
		{	
			if($area == 0)
			{
				/*
				创建支付
				 */				
				$metadata = array('role' => 0, 'type' => 1, 'tid' => $result, 'uid' => $uid);
				$subject  = "任务：{$title} 的金额托管";
				$body     = "您将为任务支付托管金额";				
				$order_no = time()."u{$uid}";

				$this->load->service('pay_service');
				$pay_result = $this->pay_service->create_payment($subject, $body, $order_no, (double)$reward * 100, $channel, $metadata);				
				if( ! empty($pay_result))
				{
					echo json_encode(array('success' => 0, 'payment' => json_decode($pay_result)));
					//增加用户发布熟
					$this->user_model->add_count($uid, 'publish_count');

				}
				else
				{
					//删除任务
					$this->task_model->delete_task($uid,$result,0);
					$this->error->output('invalid_request');								
				}
			}
			else
			{
				echo json_encode(array('success' => 0, 'tid' => $result));
				return ;
			}			
		}
		else
		{
			$this->error->output('invalid_request');
		}
	}
	
	/**
	 * [delete_task 删除任务]
	 * @param  [type] $uid [用户id]
	 * @param  [type] $tid [任务id]
	 * @return [type]      [description]
	 */
	public function delete_task($uid,$tid)
	{

		$task   = $this->task_model->get_detail_by_id($tid);
		if(empty($task))
		{
			$this->error->output('INVALID_REQUEST');
		}

		$result = $this->task_model->delete_task($uid,$tid);
		if($result)
		{
			//已支付
			if($task['pay_status'] == 1 && $task['area'] == 0)
			{
				//保存退款订单信息
            	$order_result  = $this->order_model->save_order(time()."u{$uid}",2,1,NULL,$task['reward'],$uid,$tid,'放弃任务',Common::getIP(),date('Y-m-d H:i:s',time()));
            	if($order_result)
            	{
            		//更新用户帐户
					$update_result = $this->user_model->update_count($uid,'amount',$task['reward']);   
					if($update_result)
					{
						echo json_encode(array('success' => 0));
						return ;
					}
					else
					{
						//删除订单
						$this->order_model->delete_order($order_result);
					}  					         		
            	}
            	$this->error->output('TRANSACT_ERROR');				

			}
			echo json_encode(array('success' => 0));
		}
		else
		{
			$this->error->output('INVALID_REQUEST');			
		}
	}   

	/**
	 * [compe_task 竞标任务]
	 * @param  [type] $uid     [竞标者id]
	 * @param  [type] $tid     [任务id]
	 * @param  [type] $content [竞标内容]
	 * @return [type]          [description]
	 */
	public function compe_task($uid,$tid)
	{
		$task 	   = $this->task_model->get_detail_by_id($tid);		
		if(empty($task))
		{
			return FALSE;
		}
		
		/*
		$has_compe = $this->compe_model->has_compe($tid,$uid);
		if($has_compe)
		{
			return FALSE;
		}
		*/
		if($task['employee'] != -1)
		{
			return FALSE;
		}
		
		$choose_result = $this->task_model->choose_task_compe($tid,$uid);
		//$result = $this->compe_model->compe_task($uid,$tid,$content);
		if($choose_result)
		{
			
			echo json_encode(array('success' => 0));
			//增加竞标数
			//$this->task_model->add_count($tid,'compe');
			/**
			 * 推送
			 */
			$employer  = $task['employer'];
			$this->push_service->add_n_push($employer,'compe_task',$tid);				
			return TRUE;				
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * [cancel_task 取消任务]
	 * @param  [type] $uid    [用户id]
	 * @param  [type] $tid    [任务id]
	 * @param  [type] $reason [取消原因]
	 * @return [type]         [description]
	 */
	public function cancel_task($uid,$tid,$reason)
	{

		$task = $this->task_model->get_detail_by_id($tid);
		if(empty($task))
		{
			$this->error->output('INVALID_REQUEST');
		}

		//发布者
		if($task['employer'] == $uid)
		{
			$result = $this->task_model->cancel_task($uid,$tid,$reason,5);
			if($result)
			{
				echo json_encode(array('success' => 0));
				$this->push_service->add_n_push($task['employee'],'employer_cancel_task',$tid);		
			}
			else
			{
				$this->error->output('INVALID_REQUEST');
			}


		}
		//接受者
		else if($user['employee'] == $uid)
		{
			$result = $this->task_model->cancel_task($uid,$tid,$reason,0);
			if($result)
			{
				echo json_encode(array('success' => 0));				
				$this->push_service->add_n_push($task['employer'],'employee_cancel_task',$tid);		
			}
			else
			{
				$this->error->output('INVALID_REQUEST');
			}				
		}
		//越权操作
		else
		{
			$this->error->output('INVALID_REQUEST');
		}
	}

	/**
	 * [confirm_cancel_task 确认取消任务]
	 * @param  [type] $uid [接受者id]
	 * @param  [type] $tid [任务id]
	 * @return [type]      [description]
	 */
	public function confirm_cancel_task($uid,$tid)
	{
		$result = $this->task_model->confirm_cancel_task($uid,$tid);
		if($result)
		{
			$task = $this->task_model->get_detail_by_id($tid);
			//已支付
			if($task['pay_status'] == 1 && $task['area'] == 0)
			{
				//保存退款订单信息
            	$order_result  = $this->order_model->save_order(time()."u{$task['employer']}",2,1,NULL,$task['reward'],$task['employer'],$tid,'取消任务',Common::getIP(),date('Y-m-d H:i:s',time()));
            	if($order_result)
            	{
            		//更新用户帐户
					$update_result = $this->user_model->update_count($task['employer'],'amount',$task['reward']);   
					if($update_result)
					{
						echo json_encode(array('success' => 0));				
						//推送消息给发布者
						$this->push_service->add_n_push($task['employer'],'confirm_cancel_task',$tid);	
						return ;
					}
					else
					{
						//删除订单
						$this->order_model->delete_order($order_result);
					}         		
            	}
            	$this->error->output('TRANSACT_ERROR');				
			}
			echo json_encode(array('success' => 0));
		}
		else
		{
			$this->error->output('INVALID_REQUEST');
		}
	}

	/**
	 * [complete_task 完成任务]
	 * @param  [type] $uid [接受者id]
	 * @param  [type] $tid [任务id]
	 * @return [type]      [description]
	 */
	public function complete_task($uid,$tid)
	{
		$result = $this->task_model->complete_task($uid,$tid);
		if($result)
		{			
			echo json_encode(array('success' => 0));
			$task = $this->task_model->get_detail_by_id($tid);					
			$this->push_service->add_n_push($task['employer'],'complete_task',$tid);						
			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	/**
	 * [confirm_complete_task 确认完成任务]
	 * @param  [type] $uid [发布者id]
	 * @param  [type] $tid [任务id]
	 * @return [type]      [description]
	 */
	public function confirm_complete_task($uid,$tid)
	{
		$result = $this->task_model->confirm_complete_task($uid,$tid);
		if($result)
		{
			$task = $this->task_model->get_detail_by_id($tid);
			//已支付
			if($task['pay_status'] == 1 && $task['area'] == 0)
			{
				//保存转账订单信息
            	$order_result  = $this->order_model->save_order(time()."u{$task['employee']}",2,1,NULL,$task['reward'],$task['employee'],$tid,'完成任务',Common::getIP(),date('Y-m-d H:i:s',time()));
            	if($order_result)
            	{
            		//更新用户帐户
					$update_result = $this->user_model->update_count($task['employee'],'amount',$task['reward']);   
					if($update_result)
					{
						echo json_encode(array('success' => 0));				
						//推送消息给发布者
						$this->push_service->add_n_push($task['employee'],'confirm_complete_task',$tid);	
						return ;
					}
					else
					{
						//删除订单
						$this->order_model->delete_order($order_result);
					}         		
            	}
            	$this->error->output('TRANSACT_ERROR');				
			}
			echo json_encode(array('success' => 0));			
		}
		else
		{
			$this->error->output('INVALID_REQUEST');
		}
	}

    /*******************END-任务*********************/

	/****************竞标**********************/	

	/**
	 * [get_compe_by_id 获取任务的所有竞标]
	 * @param  [type] $tid [任务id]
	 * @return [type]      [description]
	 */
	/*
	public function get_compe_by_id($tid)
	{
		$compe = $this->compe_model->get_compe_by_id($tid);
		foreach ($compe as $k => $v) {
			if( ! empty($v['uid']))
			{
				//获取竞标用户信息
				$compe[$k]['user'] = $this->user_model->get_user_base_id($v['uid']);	
				unset($compe[$k]['uid']);						
			}

		}
		return $compe;
	}
	*/
	/**
	 * [choose_task_compe 选择竞标者]
	 * @param  [type] $me  [我的id] 
	 * @param  [type] $tid [任务id]
	 * @param  [type] $uid [用户id]
	 * @return [type]      [description]
	 */
	//public function choose_task_compe($me,$tid,$uid)
	//{
	//	$result = $this->task_model->choose_task_compe($me,$tid,$uid);
	//	if($result)
	//	{
	//		echo json_encode(array('success' => 0));
			/**
			 * 推送
			 */
	//		$uid = $uid;
	//		$tid = $tid;
	//		$this->push_service->add_n_push($uid,'choose_task_compe',$tid);		

			/*
			 增加接活数
			 */
	//		$this->user_model->add_count($uid,'recive_count');	
	//		return TRUE;
	//	}
	//	else
	//	{
	//		return FALSE;
	//	}
	//}

	/****************END-竞标**********************/



	/****************评论*************************/
	/**
	 * [get_comment_by_id 获取评论]
	 * @param  [type] $tid [任务id]
	 * @return [type]      [description]
	 */
	public function get_comment_by_id($tid)
	{
		$comments = $this->comment_model->get_comment_by_id($tid);
		foreach ($comments as $k => $v) 
		{
			$comments[$k]['user'] = $this->user_model->get_user_base_id($v['uid']);
			unset($comments[$k]['uid']);
		}
		return $comments;
	}

	/****************END-评论*************************/
}