<?php 

use JPush\Model as M;
use JPush\JPushClient;
use JPush\Exception\APIConnectionException;
use JPush\Exception\APIRequestException;

class Push_service extends MY_Service{
	function __construct() {
		require_once ('./application/third_party/jpush/autoload.php');

		$this->app_key 		 = '1942af43a66e3232f2e563d0';
		$this->master_secret = 'f64d23d282fc97ec2ecc7a4f';
	}

	/**
	 * [push 根据别名推送]
	 * @param  [type] $alias 	[对象别名]
	 * @param  [type] $msg 		[信息数组]
	 * @return [type]           [description]
	 */
	public function push_by_alias($alias,$title,$content,$msg){
		try {
			$client = new JPushClient($this->app_key,$this->master_secret);			
		    $result = $client->push()
		        			 ->setPlatform(M\all)
		        			 ->setAudience(M\audience(M\alias($alias)))
		        			 ->setNotification(M\notification(M\android($content,$title,null,$msg), M\ios($title,null, null,null,$msg)))
		       			     ->send();

		} catch (APIRequestException $e) {

		} catch (APIConnectionException $e) {
		}	    	
	}

	/**
	 * [push 根据标签推送]
	 * @param  [type] $tag 		[标签]
	 * @param  [type] $msg 		[信息数组]
	 * @return [type]           [description]
	 */
	public function push_by_tag($tag,$msg){
		try {
			$client = new JPushClient($this->app_key,$this->master_secret);			
		    $result = $client->push()
		        			 ->setPlatform(M\all)
		        			 ->setAudience(M\audience(M\tag($tag)))
		        			 ->setNotification(M\notification($msg))
		       			     ->send();

		} catch (APIRequestException $e) {

		} catch (APIConnectionException $e) {
		}	    	
	}

	/**
	 * [add_n_push 添加消息同时推送]
	 * @param [type] $uid               [接受者id]
	 * @param [type] $notification_name [消息索引]
	 * @param [type] $tid 				[任务id]
	 */
	public function add_n_push(
					$uid,
					$notification_name,
					$tid=null
	){

		$this->load->model('notification_model');		
		$notify_msg 	 = $this->notification_model->get_notify_msg($notification_name);
		$type 			 = $notify_msg['type'];
		if(intval($type) == 0){
			$content 		 = $notify_msg['title'];
			$param['href']   = $notify_msg['href'];
			$param['tid'] 	 = $tid;

			$title = $this->notification_model->get_type_name($type);			
			$this->notification_model->add_notification($uid,$type,$content,"",json_encode($param));
			$this->push_by_alias(array($uid),$title,$content,$param);				
		}
	
	}
	
}