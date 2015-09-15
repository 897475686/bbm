<?php



class Issue extends MY_Controller {
	public function __construct()
	{
		parent::__construct();
		$this->load->service('task_service');
	}

	/**
	 * [publish_task 发布任务]
	 * @return [type] [description]
	 */
	public function publish_task()
	{
		$task = $this->sc->input(array('task_title','task_content','type','area','time_limit','reward','channel'));
		//验证有效时间
		$t = explode(',', $task['time_limit']);
		$validate_result = $this->_validate_time_limit($t);
		if( ! $validate_result)
		{		
			$this->error->output('invalid_time_limit');
		}
		if($task['area'] == 0)
		{
			//验证支付金额
			if($task['reward'] <= 0 || $task['reward'] >= 100000)
			{
				$this->error->output('invalid_reward');
			}
		}

		//非女生发女生专区
		if($task['area'] == 1 && $this->user['sex'] != 2)
		{
			$this->error->output('invalid_request');
		}
		
		$this->task_service->publish_task($task['task_title'],$task['task_content'],$task['type'],$this->user['id'],$task['area'],$this->user['school'],$this->user['sex'],$task['time_limit'],$task['reward'],$task['channel']);
	}

	/**
	 * [check_payment 查看是否支付]
	 * @return [type] [description]
	 */
	public function check_payment()
	{			
		$charge_id = $this->sc->input('charge_id');
		$this->load->service('pay_service');
		$result = $this->pay_service->check_payment($charge_id);
		if( ! empty($result))
		{
			$tid = isset($result->metadata->tid) ? $result->metadata->tid : NULL;
			echo json_encode(array('success' => 0, 'tid' => $tid));
		}
	}


	private function _validate_time_limit($time_limit)
	{
		if(empty($time_limit) || count($time_limit) != 3)
		{
			return FALSE;
		}
		if( ! is_numeric($time_limit[0]) || ! is_numeric($time_limit[1]) || !is_numeric($time_limit[2]))
		{
			return FALSE;
		}
		if($time_limit[0] < 0 || $time_limit[0] >= 10)
		{
			return FASLE;
		}
		if($time_limit[1] < 0 || $time_limit[1] > 23)
		{
			return FALSE;
		}
		if($time_limit[2] < 0 || $time_limit[2] > 59)
		{
			return FALSE;
		}
		if($time_limit[0] == 0 && $time_limit[1] == 0 && $time_limit[2] == 0)
		{
			return FALSE;
		}
		return TRUE;		
	}
}