<?php 

class Auth_service extends MY_Service{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('auth_model');
        $this->load->library('crypto');        
        $this->login_in_session_name    = 'bbm_user';    
    }
    
    /**
     * [check_user_auth 检查用户权限]
     * @return [type] [description]
     */
    public function check_user_auth()
    {

        //if(!empty($auths = $this->cache->memcached->get('role_auth')))
        //{
        //  return $this->_is_auth_success($auths);
        //}
        //else
        //{
            $auths = $this->auth_model->get_user_auth(0,NULL);
            $new_auths = array();
            foreach ($auths as $k => $v)
            {
                if( ! empty($v['route']) && ! empty($v['role_group']))
                {
                    $new_auths[$v['route']] = $v['role_group'];
                }
            }
            //$this->cache->memcached->save('role_auth',$new_auths,60);
            return $this->_is_auth_success($new_auths);
        //}

    }

    /**
     * [_is_auth_success 验证权限是否成功]
     * @param  [type]  $auths [description]
     * @return boolean        [description]
     */
    private function _is_auth_success($auths)
    {

        $route = Common::get_route();
        //需权限
        if(array_key_exists($route,$auths))
        {
            $user      = isset($_SESSION[$this->login_in_session_name]) ? $_SESSION[$this->login_in_session_name] : NULL;
            $user_role = isset($user['role'])     ? $user['role']     : NULL; 
            //有权限
            if(strstr($auths[$route],"|{$user_role}|"))
            {
                return TRUE;
            }
            else
            {

                //有登录权限
                if(strstr($auths[$route],"|1|"))
                {
                    //已登录
                    if( ! empty($user))
                    {
                        return TRUE;                        
                    }
                    //未登录
                    else
                    {
                        $this->error->output('NOTLOGIN_ERROR',array('script' => 'redirect("login");'));
                    }
                }
                //没有权限   
                $this->error->output('NOAUTH_ERROR',array('script' => 'appcan.window.close(-1);'));
                        
            }
        }
        //无需权限
        else
        {
            return TRUE;
        }       
    }

    /**
     * [set_session 设置session]
     * @param [type] $user[用户信息]
     */
    public function set_session($user) {
        session_start();
        $_SESSION[$this->login_in_session_name] = $user;
        $key = Crypto::CreateNewRandomKey();
        $safe_session_id = base64_encode($key.Crypto::Encrypt(session_id(), $key));
        return $safe_session_id;
    }



    public function get_session($session_id) {
        //加密后的 session_id
        $decode = base64_decode($session_id);
        //检验 session_id 是否被篡改
        $keySize = Crypto::KEY_BYTE_SIZE;
        $key = mb_substr($decode, 0, $keySize, '8bit');
        $decode = mb_substr($decode, $keySize, null, '8bit');
        try {
            $decrypted = Crypto::Decrypt($decode, $key);
        } catch (InvalidCiphertextException $ex) { // VERY IMPORTANT
            // Either:
            //   1. The ciphertext was modified by the attacker,
            //   2. The key is wrong, or
            //   3. $ciphertext is not a valid ciphertext or was corrupted.
            // Assume the worst.
            //被人恶意修改Cookie
            return FALSE;
        } catch (CryptoTestFailedException $ex) {
            die('Cannot safely perform encryption');
        } catch (CannotPerformOperationException $ex) {
            die('Cannot safely perform decryption');
        }

        if(empty($decrypted)) {
            return FALSE;
        }

        //session id 
        $session_id = $decrypted;
        session_id($session_id);
        session_start();

        return $_SESSION[$this->login_in_session_name];
    }

}