<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Simple User Management Library
 * 
 * The goal of this library is to provide all the functions necessary to create new users,
 * - create new users
 * - authenticate their access
 * - allow email account activation
 * - gather additional info from users outside of scope of users table
 */
class User extends CI_Controller
{
    protected $_user_id;
    protected $_username;
    protected $_email;
    protected $_password;
    protected $_active = 0;
    protected $_pw_salt;
    protected $_enc_password;
    protected $_config;
    protected $_user_details;
    
        
    public function __construct()
    {
        parent::__construct();
        
        $this->_config = $this->config->item('user');
        $this->_pw_salt = $this->_config['pw_salt'];
    }
    
    public function getUserId()
    {
        return $this->_user_id; 
    }
    
    public function setUsername($username)
    {
        //Make sure something was passed to me and a email doesn't already exist
        if (strlen($username) > 0) {
            return $this->_username = $username;
        } else {
            return false;
        }
    }
    
    public function getUsername()
    {
        return $this->_username;
    }
    
    public function setEmail($email)
    {
        //Make sure something was passed to me and a username doesn't already exist.
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->_email = $email;
        } else {
            return false;
        }
    }
    
    public function getEmail()
    {
        return $this->_email;
    }
    
    public function setPassword($password)
    {
        if (strlen($password) > 0) {
            return $this->_password = $password;
        } else {
            return false;
        }
        
    }
    
    public function getPassword()
    {
        return $this->_password;
    }
    
    public function setActive($active)
    {
        if (strlen($active) > 0) {
            return $this->_active = (int)(bool)$active;
        } else {
            return false;
        }
    }
    
    public function getActive()
    {
        return (bool)$this->_active;
    }
    
    public function setDetail($key, $value)
    {
        $this->_user_details->$key = $value;
    }
    
    public function getUserDetail($key)
    {
        return (isset($this->_user_details->$key)) ? $this->_user_details->$key : false;
    }
    
    private function _encryptPassword()
    {
        if (empty($this->_pw_salt))
            throw new Exception('No password salt provided');
            
        return md5($this->_pw_salt.$this->_password);
    }
    
    private function _clearPrivateVars()
    {
        unset($this->_user_id);
        unset($this->_username);
        unset($this->_email);
        unset($this->_password);
        unset($this->_user_details);
        $this->_active = 0;
    }
    
    private function _setPrivateVars($user)
    {
        $this->_user_id         = $user->id;
        $this->_username        = $user->username;
        $this->_email           = $user->email;
        $this->_password        = $user->password;
        $this->_active          = $user->active;
        $this->_user_details    = $this->_parseUserDetails($user->details);
    }
    
    private function _parseUserDetails($details)
    {
        if (empty($details)) return array();
        
        $final_arr = array();
        $tmp_arr = explode('....,', $details);
        
        foreach ($tmp_arr as $row) {
            list($key, $value) = explode('@@@@', $row);
            $final_arr[$key] = $value;
        }
        
        return (object)$final_arr;
    }
    
    public function reset()
    {
        $this->_clearPrivateVars();
    }
    
    public function loadUserById($user_id)
    {
        $user = $this->user_model->loadUser('id', $user_id);
        
        if ($user) {
            $this->_setPrivateVars($user);
            return true;
        } else {
            return false;
        }
    }
    
    public function loadUserByUsername($username)
    {
        $user = $this->user_model->loadUser('username', $username);
        
        if ($user) {
            $this->_setPrivateVars($user);
            return true;
        } else {
            return false;
        }
    }
    
    public function loadUserByEmail($email)
    {                                 
        $user = $this->user_model->loadUser('email', $email);
        
        if ($user) {
            $this->_setPrivateVars($user);
            return true;
        } else {
            return false;
        }
    }
    
    public function loadUserByDetail($key, $value)
    {
        $user = $this->user_model->loadUserByDetail($$key, $value);
        
        if ($user) {
            $this->_setPrivateVars($user);
            return true;
        } else {
            return false;
        }
    }
    /**
     * Create a new user account with validation.
     * Required _username, _email, _password to be set.
     * @return int|string $this->_user_id int if successful, string containing error message if failed.
     */
    public function createAccount()
    {
        if (!$this->_user_id) {
            if (!$this->_username)
                throw new Exception('Username is not set', 1);
            
            if (!$this->_email) 
                throw new Exception('Email is not set', 1);
            
            if (!$this->_password) 
                throw new Exception('Password is not set', 1);
                
            //Make sure email doesn't already exist
            if ($this->user_model->emailExists($this->_email))
                //throw new Exception("Email[{$this->_email}] already exists");
                return "Email Address ({$this->_email}) already exists";
                
            //Make sure username doesn't already exist
            if ($this->user_model->usernameExists($this->_username))
                //throw new Exception("Username[{$this->_username}] already exists");
                return "Username ({$this->_username}) already exists";
                
            if ($this->_config['auto_encrypt'])
                $this->_enc_password = $this->_encryptPassword();
            else
                $this->_enc_password = $this->_password;
            
            //Validation completed
            $this->_user_id = $this->user_model->createUser($this->_username, $this->_email, $this->_enc_password, $this->_active);            
            
            if ($this->_user_id) {
                return $this->_user_id;
            } else {
                return 'Failed to create new user';
            }
        } else {
            throw new Exception('Cannot create account, User ID is already set!', 1);
        }
    }
    
    public function updateUser()
    {
        if ($this->_user_id) {
            if ($this->_config['auto_encrypt'])
                $this->_enc_password = $this->_encryptPassword();
            else
                $this->_enc_password = $this->_password;
                
            $updated_user = $this->user_model->saveUser(
                $this->_user_id, 
                $this->_username, 
                $this->_email, 
                $this->_enc_password, 
                $this->_active
            );
            
            if ($updated_user) {
                while (list($key, $val) = each($this->_user_details)) {
                    if (!$this->user_model->saveUserDetail($this->_user_id, $key, $val))
                        return 'Failed to update user detail: ' . $key . ' => ' . $val;
                }
            }
        } else {
            throw new Exception('No User ID Provided. Cannot update', 1);
        }
    }
    
    public function save()
    {
    
    }
    
    
} 