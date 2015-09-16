<?php

class Pay_service extends MY_Service
{
	function __construct() 
	{
		require_once('/application/third_party/pingpp/init.php');
		$this->appId = "app_fX9ijPX1qTK4DeLK";
		\Pingpp\Pingpp::setApiKey(\Pingpp\Pingpp::getApiKey());
		$this->load->model('order_model');
		$this->load->model('user_payment_model');
		$this->load->model('withdraw_model');
	}

	/**
	 * [create_payment 创建支付]
	 * @param  [type] $subject  [description]
	 * @param  [type] $body     [description]
	 * @param  [type] $order_no [description]
	 * @param  [type] $amount   [description]
	 * @param  [type] $channel  [description]
	 * @param  [type] $metadata [description]
	 * @return [type]           [description]
	 */
	public function create_payment($subject,$body,$order_no,$amount,$channel,$metadata){		
		$channel_ok = true;
		switch ($channel) {
			case 'alipay':
				break;
			case 'wx':
				break;

			default:$channel_ok=false;break;
		}				
		if($channel_ok)
		{

			$data = $this->create_charge($subject,$body,$order_no,$amount,$channel,$metadata);	
			return $data;			
		}
		else
		{
			return FALSE;
		}		
	}

	/**
	 * [creat_charge 创建支付]
	 * @param  string $subject  [标题]
	 * @param  string $body     [内容]
	 * @param  [type] $order_no [订单号]
	 * @param  [type] $amount   [金额]
	 * @param  [type] $channel  [支付渠道]
	 * @param  array  $metadata [元数组]
	 * @param  array  $extra    [额外参数]
	 * @return [type]           [description]
	 */
	private function create_charge(
					$subject  = "",
					$body 	  = "",
					$order_no,
					$amount,					
					$channel,
					$metadata = array(),
					$extra 	  = array()
		){		
		try {

		    $ch = \Pingpp\Charge::create(
		        array(
		            "subject"   => $subject,
		            "body"      => $body,
		            "amount"    => $amount,
		            "order_no"  => $order_no,
		            "currency"  => "cny",
		            "extra"     => $extra,
		            "channel"   => $channel,
		            "client_ip" => Common::getIP(),
		            "app"       => array("id" => $this->appId),
		            "metadata" 	=> $metadata
		        )
		    );

		    return $ch;

		} catch (\Pingpp\Error\Base $e) {

			return FALSE;
		  // header('Status: ' . $e->getHttpStatus());
		   //echo($e->getHttpBody());
		}		
	}

	/**
	 * [check_payment 查询支付]
	 * @param  [type] $charge_id [description]
	 * @return [type]            [description]
	 */
	public function check_payment($charge_id)
	{

		$charge = $this->check_charge($charge_id);
		//支付成功
		if(!empty($charge))
		{
			//保存订单 
			$this->save_order($charge);		
			return $charge;	
		}
		//未支付
		else
		{
			$this->error->output('PAY_WAIT');
		}
	}
	/**
	 * [check_charge 查询支付]
	 * @param  [type] $charge_id [支付id]
	 * @return [type]            [description]
	 */
	private function check_charge($charge_id){
		try {
 			$charge = \Pingpp\Charge::retrieve($charge_id);
 			$charge = json_decode($charge);
 			if(isset($charge->paid)){
 				if($charge->paid){
 					return $charge;
 				}
 			}
 			return FALSE;
		} catch (\Pingpp\Error\Base $e) {
			return FALSE;
		}		
	}


	/**
	 * [get_payment_cb 支付异步回调]
	 * @return [type] [description]
	 */
	public function get_payment_cb(){
	  // 读取异步通知数据
	  $charge = json_decode(file_get_contents("php://input"));

	  // 对异步通知做处理
	  if( ! isset($charge->object)){
	     exit('fail');
	  }
	  switch($charge->object)
	  {
	    case "charge":
	    // 开发者在此处加入对支付异步通知的处理代码
		//保存订单 
		$result = $this->save_order($charge);
		if($result)
		{
			exit('success');				
		}
	    break;
	    case "refund":
	    // 开发者在此处加入对退款异步通知的处理代码
		exit('success');
	    default:
	     exit('fail');
	    break;
	  }
	
	}

	/**
	 * [save_order 保存支付订单信息]
	 * @param  [type] $charge [支付凭据]
	 * @return [type]         [description]
	 */
	private function save_order($charge)
	{
		//支付类型
		switch ($charge->metadata->type) {
			//任务托管支付
			case '1':
				$result = $this->order_model->save_order($charge->order_no, 1, $charge->metadata->type, $charge->id, $charge->amount / 100, $charge->metadata->uid, $charge->metadata->tid, "", $charge->client_ip,$charge->time_paid);
				if($result)
				{
					$this->load->model('task_model');
					//更新任务支付状态
					$this->task_model->update_task_pay_status($charge->metadata->tid,1);					
					return TRUE;
				}
				else
				{
					return FALSE;
				}
				break;
			//充值
			case '2':
				$result = $this->order_model->save_order($charge->order_no, 2, $charge->metadata->type, $charge->id, $charge->amount, $charge->metadata->uid, NULL , "充值", $charge->client_ip,$charge->time_paid);			
				if($result)
				{
					//更新帐户余额
					$this->load->model('user_model');
					$this->user_model->update_count($charge->metadata->uid,'amount',$charge->amount);
					return TRUE;
				}
				else
				{
					return FALSE;
				}
				break;
			default:
				return FALSE;
		}
	}
	

    /**
     * [get_pay_statistic 获取我的账户数据]
     * @param  [type] $uid  [description]
     * @return [type]       [description]
     */
    public function get_pay_statistic($uid)
    {
		$outcome  = $this->order_model->get_pay_statistic(1,$uid);
		$outcome  = empty($outcome) ? 0 : $outcome['total'];
		$income   = $this->order_model->get_pay_statistic(2,$uid);
		$income   = empty($income) ? 0 : $income['total'];
		$this->load->model('user_model');
		$amount   = $this->user_model->get_fields_value($uid,array('amount'))['amount'];
		
		return array(
			'outcome' => $outcome,
			'income'  => $income,
			'amount'  => $amount
		);

    }

    /**
     * [get_user_payment 获取用户的绑定支付账号]
     * @param  [type] $uid     [用户id]
     * @param  [type] $pid     [渠道id]
     * @return [type]          [description]
     */
    public function get_user_payment($uid,$pid = NULL)
    {
        $result = $this->user_payment_model->get_user_payment($uid);
        if( ! empty($pid))
        {
            foreach ($result as $k => $v) 
            {
            	if($v['id'] == $pid)
            	{
            		return $v['payment'];
            	}
            }
            return FALSE;
        }
        return $result;

    } 


    /**
     * [bind_user_payment 绑定提现账户]
     * @param  [type] $uid     [用户id]
     * @param  [type] $channel [账户类型]
     * @param  [type] $payment [帐户号]
     * @return [type]          [description]
     */
    public function bind_user_payment($uid,$channel,$payment,$realname)
    {

        $result = $this->get_user_payment($uid);  
        //绑定帐户超出5个
        if(count($result) >= 5)
        {
        	$this->error->output('over_payment');
        }
        else
        {
        	$insert_result = $this->user_payment_model->insert_payment($uid,$channel,$payment,$realname);
        	return $insert_result;
        } 
    }       

    /**
     * [delete_payment 删除绑定帐户]
     * @param  [type] $uid     [description]
     * @param  [type] $channel [description]
     * @return [type]          [description]
     */
    public function delete_payment($uid, $pid)
    {
    	return $this->user_payment_model->delete_payment($uid,$pid);
    }

    /**
     * [has_withdraw 是否提现过]
     * @param  [type]  $uid [description]
     * @return boolean      [description]
     */
    public function has_withdraw($uid)
    {
    	$time  = date('Y-m-d',time());
    	$limit = 3;
    	$count = $this->withdraw_model->get_withdraw_count($uid,$time);
    	return $limit-$count;
    }

    /**
     * [withdraw_init 提现初始化]
     * @param  [type] $uid [用户id]
     * @return [type]      [description]
     */
    public function withdraw_init($uid)
    {
		$payment = $this->user_payment_model->get_user_payment($uid);
		//已绑定支付账号
		if( ! empty($payment))
		{
			$count = $this->has_withdraw($uid);
			//可以提现
			//if($count > 0)
			//{
				$output['success'] = 0;
				$this->load->model('user_model');
				$amount = $this->user_model->get_fields_value($uid,array('amount'))['amount'];
				$output['count']   = $count;
				$output['amount']  = $amount;
				$output['payment'] = $payment;
			//}
			//超过提现次数
			/*
			else if($count <= 0)
			{
				$output['success'] = 1;
			}
			else{
				$this->error->output('invalid_request');
				return ;
			}
			*/			
		}
		//未绑定支付账号
		else
		{
			$output['success'] = 2;
		}
		echo json_encode($output);    	
    }

    /**
     * [withdraw 提现]
     * @param  [type] $uid             [用户id]
     * @param  [type] $pid         [账户类型]
     * @param  [type] $withdraw_amount [提现金额]
     * @return [type]                  [description]
     */
    public function withdraw($uid,$pid,$withdraw_amount)
    {
		$has_withdraw = $this->has_withdraw($uid);
		$payment 	  = $this->get_user_payment($uid,$pid);
		//未超过提现次数同时已绑定渠道账号
		if($has_withdraw && $payment)
		{
			$this->load->model('user_model');
			$amount = $this->user_model->get_fields_value($uid,array('amount'))['amount'];

			//提现金额小于或等于现有的账户余额
			if($withdraw_amount > 0 && $withdraw_amount <= $amount)
			{
				$withdraw_result = $this->withdraw_model->add_withdraw($uid,$withdraw_amount,$pid,$payment);
				//申请成功
				if($withdraw_result)
				{					
					//保存订单
					$this->load->model('order_model');
					$order_no = time()."u{$uid}";
					$role     = 1;
					$type     = 3;				
					$save_result = $this->order_model->save_order($order_no, $role, $type, NULL, $withdraw_amount, $uid, -1, "", Common::getIP(), date('Y-m-d H:i:s',time()));
					if($save_result)
					{
						//更新账户余额
						$this->load->model('user_model');
						$update_result = $this->user_model->update_count($uid,'amount',"-".$withdraw_amount);
						if($update_result)
						{
							return TRUE;						
						}						
					}
				}
				else
				{
					return FALSE;
				}
			}				
		}
		return FALSE; 	
    }

    /**
     * [recharge 充值]
     * @param  [type] $channel [支付渠道]
     * @param  [type] $amount  [充值金额]
     * @param  [type] $uid     [用户id]
     * @return [type]          [description]
     */
    public function recharge($channel, $amount, $uid)
    {
    	$subject  = "充值";
    	$body     = "充值";
    	$order_no = time()."u{$uid}";
		$metadata = array('role' => 2, 'type' => 2, 'tid' => -1, 'uid' => $uid);    	
		$pay_result = $this->create_payment($subject, $body, $order_no, (double)$amount * 100, $channel, $metadata);
		if( ! empty($pay_result))
		{	
			echo json_encode(array('success' => 0, 'payment' => json_decode($pay_result)));
		}
		else
		{
			$this->error->output('invalid_request');
		}
    }
}