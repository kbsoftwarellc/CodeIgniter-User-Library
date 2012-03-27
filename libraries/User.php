<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package Simple User Library [getsparks.org]
 * @name CodeIgniter User Library
 * @author Kenny Brown
 * @link NA
 * @license MIT License Copyright (c) 2012 Kenny Brown
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

class User
{
    protected $_user;
    protected $_config;
    
    public function __construct()
    {
        $this->_CI = get_instance();
        $this->_CI->load->model('user_model');
        $this->_CI->load->spark('messages/1.0.2');
        $this->_config  = $this->_CI->config->item('user');
        $this->_user = new stdClass();
    }
    
    public function find($where_array)
    {
        $user = $this->_CI->user_model->findUser($where_array);
        $this->_user = ($user) ? $user : new stdClass();
        return $this;
    }
    
    public function create($username, $email, $password, $active = 0)
    {
        if ($this->_config['auto_encrypt']) {
            $password = $this->encryptPassword($password);
        }
        
        $userId = $this->_CI->user_model->createUser($username, $email, $password, $active);
        
        if ($userId) {
            $this->_user = (object) array(
                'id' => $userId,
                'username' => $username,
                'email' => $email,
                'password' => $password,
                'active' => $active
            );
        }
        
        return $this;
    }
    
    public function authenticate($username, $password)
    {
        if ($this->_config['auto_encrypt']) {
            $password = $this->encryptPassword($password);
        }
        
        $this->find(array('username' => $username, 'password' => $password));
        
        if (!$this->getId()) {
            $this->_CI->messages->add('Username/Password match not found', 'error');
            return false;
        } else {
            if ($this->isActive()) {
                $this->_CI->messages->add('Your account IS active! Good Job!', 'success');
                return true;
            } else {
                $this->_CI->messages->add('Your account is not active', 'error');
                return false;
            }
        }
    }
    
    public function encryptPassword($password)
    {
        if (empty($this->_config['pw_salt']))
            throw new Exception('No password salt provided');
        
        return sha1($this->_config['pw_salt'].$password);
    }
    
    public function getId()
    {
        return (property_exists($this->_user, 'id')) ? $this->_user->id : 0;
    }
    
    public function getUsername()
    {
        return (property_exists($this->_user, 'username')) ? $this->_user->username : '';
    }
    
    public function getEmail()
    {
        return (property_exists($this->_user, 'email')) ? $this->_user->email : '';
    }

    public function getPassword()
    {
        return (property_exists($this->_user, 'password')) ? $this->_user->password : '';
    }
    
    public function isActive()
    {
        return (property_exists($this->_user, 'active')) ? $this->_user->active : 0;
    }
    
    public function updateUsername($username)
    {
        $this->_CI->user_model->updateUser($this->getId(), array('username' => $username));
        $this->_user->username = $username;
    }
    
    public function updateEmail($email)
    {
        $this->_CI->user_model->updateUser($this->getId(), array('email' => $email));
        $this->_user->email = $email;
    }
    
    public function updatePassword($password)
    {
        if ($this->_config['auto_encrypt']) {
            $password = $this->encryptPassword($password);
        }
        
        $this->_CI->user_model->updateUser($this->getId(), array('password' => $password));
        $this->_user->password = $password;
    }
    
    public function deactivate()
    {
        $this->_CI->user_model->updateUser($this->getId(), array('active' => 0));
        $this->_user->active = 0;
    }
    
    public function reactivate()
    {
        $this->_CI->user_model->updateUser($this->getId(), array('active' => 1));
        $this->_user->active = 1;
    }
    
    public function activate()
    {
        $this->reactivate();
    }
}
