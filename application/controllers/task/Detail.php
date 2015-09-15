<?php
/**
 * Detail 任务详细内容
 */
class Detail extends MY_Controller {
	public function __construct() 
	{
		parent::__construct();
		$this->load->service('task_service');		
	}
	
	/**
	 * [get_task_detail 获取任务详细信息]
	 * @return [type] [description]
	 */
	public function get_task_detail() 
	{
		$tid = $this->sc->input('tid');
		$data = $this->task_service->get_task_detail($tid);
		echo json_encode($data);
	}
		
	/**
	 * [get_comment_by_id 根据任务id获得评论]
	 * @return [type] [description]
	 */
	public function get_comment_by_id() 
	{
		$tid = $this->sc->input('tid');
		$data = $this->task_service->get_comment_by_id($tid);
		echo json_encode($data);
	}

	/**
	 * [choose_task_compe 选择竞标者]
	 * @return [type] [description]
	 */
	/*
	public function choose_task_compe()
	{
		$id 	= $this->sc->input(array('tid','uid'));
		$result = $this->task_service->choose_task_compe($this->user['id'],$id['tid'],$id['uid']);
		if( ! $result) 
		{			
			$this->error->output('invalid_request');
		}
	}
	*/
	/**
	 * [compe_task 竞标任务]
	 * @return [type] [description]
	 */
	public function compe_task()
	{
		$tid = $this->sc->input('tid');
		
		$result = $this->task_service->compe_task($this->user['id'],$tid);
		if( ! $result)
		{
			$this->error->output('invalid_request');
		}	
	}

	/**
	 * [get_compe_by_id 获取任务下的所有竞标]
	 * @return [type] [description]
	 */
	/*
	public function get_compe_by_id(){
		$tid = $this->sc->input('tid');
		$compe = $this->task_service->get_compe_by_id($tid);
		echo json_encode($compe);
	}
	*/
	/**
	 * [delete_task 放弃任务]
	 * @return [type] [description]
	 */
	public function delete_task()
	{
		$tid = $this->sc->input('tid');		
		$data = $this->task_service->delete_task($this->user['id'],$tid);
	}

	/**
	 * [cancel_task 取消任务]
	 * @return [type] [description]
	 */
	public function cancel_task()
	{
		$cancel = $this->sc->input(array('reason','tid'));
		$result = $this->task_service->cancel_task($this->user['id'],$cancel['tid'],$cancel['reason']);		 
	}

	/**
	 * [confirm_cancel_task 确认取消任务]
	 * @return [type] [description]
	 */
	public function confirm_cancel_task()
	{
		$tid = $this->sc->input('tid');
		$data = $this->task_service->confirm_cancel_task($this->user['id'],$tid);		
	}

	/**
	 * [complete_task 完成任务]
	 * @return [type] [description]
	 */
	public function complete_task()
	{
		$tid 	= $this->sc->input('tid');
		$result = $this->task_service->complete_task($this->user['id'],$tid);
		if( ! $result)
		{
			$this->error->output('invalid_request');
		}
	}

	/**
	 * [confirm_complete_task 确认完成任务]
	 * @return [type] [description]
	 */
	public function confirm_complete_task()
	{
		$tid 	= $this->sc->input('tid');
		$result = $this->task_service->confirm_complete_task($this->user['id'],$tid);	
	}

}