<?php
class Notification_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('notification_model');
    }

    /**
     * [get_notification_list 获取个人通知列表]
     * @param  [type] $page [页数]
     * @param  [type] $uid  [用户id]
     * @return [type]       [description]
     */
    public function get_notification_list($page, $uid)
    {
    	return $this->notification_model->get_notification_list($page,$uid);
    }

    /**
     * [read_notification 阅读通知]
     * @param  [type] $id  [description]
     * @param  [type] $uid [description]
     * @return [type]      [description]
     */
    public function read_notification($id,$uid){
    	return $this->notification_model->read_notification($id,$uid);
    }

    /**
     * [check_has_new_notification 查看是否有新通知]
     * @param  [type] $uid [用户id]
     * @return [type]      [description]
     */
    public function check_has_new_notification($uid){
    	return $this->notification_model->check_has_new_notification($uid);
    }
}