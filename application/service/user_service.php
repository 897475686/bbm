<?php
class User_service extends MY_Service
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model');
        $this->load->service('auth_service');        
    }

    /**
     * [login_action 用户登录]
     */
    public function login_action($phone, $pwd)
    {

        $user = $this->user_model->confirm($phone, $pwd);
        if( ! empty($user))
        {
            //设置 SESSION
            $session_id = $this->auth_service->set_session($user);
            
            return array('user' => $user, 'session_id' => $session_id);
        }
        else
        {
            return FALSE;
        }

    }  

    /**
     * [register_action 用户注册]
     */
    public function register_action($name, $phone, $pwd, $sex, $school)
    {
        $user = $this->user_model->user_register($name, $phone, $pwd, $sex, $school);
        //注册成功，设置 session
        if ( !empty($user))
        {
            //$user = $this->user_model->get_user_base_id($user_id);
            //邮箱注册，验证邮箱
            /*
            if( !empty($email) )
            {
                $this->validate_email($user['id'], $email);
            }
            else
            {
                //设置 SESSION
                $this->auth_service->set_login_session($user);
            }
            */
            //设置 SESSION
            $session_id = $this->auth_service->set_session($user);           
            return array('session_id' => $session_id, 'user' => $user);
        }
        return FALSE;
    }      

    public function check_phone($phone)
    {
       $result = $this->user_model->check_phone($phone);
       return $result;
    }

    /**
     * [update_account 更新个人资料]
     * @param  [type] $uid  [用户id]
     * @param  [type] $info [键值数组]
     * @return [type]       [description]
     */
    public function update_account($uid, $info)
    {
        $arr = array();
        if(isset($info['pic']))
        {
            $arr['pic'] = $info['pic'];
        }
        if(isset($info['name']))
        {
            $arr['name']= $info['name'];
        }
        if(isset($info['school']))
        {
            $arr['school'] = $info['school'];
        }        
        if(isset($info['self_tags']))
        {
            $arr['self_tags'] = $info['self_tags'];
        }        
        $result = $this->user_model->update_user($uid,$arr);
        return $result;
    }

    public function change_pwd($uid, $pwd, $new_pwd)
    {
        $result = $this->user_model->check_pwd($uid,$pwd);
        if($result)
        {
            $result = $this->user_model->change_pwd($uid,$new_pwd);
            return $result;
        }
        else
        {
            $this->error->output('OLD_PASSWORD_ERROR');
        }
    }

    /**
     * [update_count 更新user字段数量]
     * @param  [type] $name   [字段名]
     * @param  [type] $amount [数量]
     * @return [type]         [description]
     */
    public function update_count($uid,$name,$amount){		
		$this->user_model->update_count($uid,array('name' => $name,'amount' => $amount));    	
    }
    
    /**
     * [get_fields_value 获取特定的键值]
     * @param  [type] $fileds   [字段数组]
     * @return [type] [description]
     */
    public function get_fields_value($uid,$fields){
        $fileds =  implode(',', $fields);
        if(!empty($fileds)){
            $data = $this->user_model->get_fields_value($uid,$fields);
            return $data;
        }
        else{
            return FALSE;
        }
    }

    /**
     * [save_headpic 更换头像]
     * @param  [type] $form_name [表单名]
     * @return [type]            [description]
     */
    public function save_headpic($form_name, $uid)
    {
        $config['upload_path'] = './public/headpic/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = '2000';
        $config['remove_spaces']=TRUE;
        if( isset($_FILES[$form_name]))
        {
            $imgname = $this->security->sanitize_filename($_FILES[$form_name]["name"]); //获取上传的文件名称
            $filetype = pathinfo($imgname, PATHINFO_EXTENSION);//获取后缀
            $config['file_name']=time()."_{$uid}.".$filetype;
            //图片新路径
            $pic_path = substr($config['upload_path'],2).$config['file_name'];
            $this->load->library('upload', $config);
            $upload_result = $this->upload->do_upload($form_name);
            //上传成功
            if($upload_result)
            {
                //上传到oss
                $this->load->library('oss');
                $upload_result = $this->oss->upload_by_file($pic_path);
                if($upload_result)
                {
                    $osspath = OSS_URL."/{$pic_path}";                    
                    //保存到数据库
                    $update_result = $this->user_model->update_user($uid, array('pic' => $osspath));
                    if($update_result)
                    {
                        $result = array('success' => 0, 'pic_path' => $osspath); 

                    }
                    else
                    {
                        $result['error'] = lang('error_INVALID_REQUEST');
                        //删除oss上的文件
                        $this->oss->delete_object($pic_path);                        
                    }    
                }        
                //删除本地文件        
                @unlink("./{$pic_path}");
                              
            }
            //上传失败
            else
            {
                $result = array();
                $result['error'] = $this->upload->display_errors();
            }
        }
        else
        {
            $result = array();
            $result['error'] = lang('error_INVALID_REQUEST');
        }
        return $result;
    }   
}