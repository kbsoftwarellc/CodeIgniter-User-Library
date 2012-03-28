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
    /**
     * global class user object.
     * @var object
     * @access protected
     */
    protected $_user;
    /**
     * config file for library.
     * @var array
     * @access protected
     */
    protected $_config;
    /**
     * __construct() function loads basic info and libraries.
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->_CI = get_instance();
        $this->_CI->load->model('user_model');
        $this->_CI->load->spark('messages/1.0.2');
        $this->_config  = $this->_CI->config->item('user');
        $this->_user = new stdClass();
    }
    /**
     * Look up a user based on key => value array passed in.
     * 
     * @access public
     * @return User object instance
     * @param array $where_array. pass in search parameters for the db
     */
    public function find($where_array)
    {
        $user = $this->_CI->user_model->findUser($where_array);
        $this->_user = ($user) ? $user : new stdClass();
        return $this;
    }
    /**
     * create a new user
     * 
     * @access public
     * @return User object instance
     * @param string $username. The username for the new user.
     * @param string $email. The email address for the new user.
     * @param string $password. Can optionally be encrypted before being passed in.
     * @param bool $active. This field is optional, determines whether user is created as active or inactive for email validation.
     */
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
    /**
     * Used to authenticate a user. Returns true/false depending on if
     * login attempt was successful. Also provides messages you can display to user.
     * Also sets the global user object so you can access data.
     * 
     * @access public
     * @return bool true/false. If authentication was successful.
     * @param string $username. The username of the user to authenticate.
     * @param string $password. The password of the user. Can be optionally encrypted before.
     */
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
    /**
     * Encrypt a string/password and add a salt from the config file.
     * 
     * @access public
     * @return string $password. This value is sha1 encrypted.
     * @param string $password. The password or string you want to encrypt.
     */
    public function encryptPassword($password)
    {
        if (empty($this->_config['pw_salt']))
            throw new Exception('No password salt provided');
        
        return sha1($this->_config['pw_salt'].$password);
    }
    /**
     * Return the primary key ID for users table. Also known as the User ID.
     * 
     * @access public
     * @return integer. User ID or 0 if object not set.
     */
    public function getId()
    {
        return (property_exists($this->_user, 'id')) ? $this->_user->id : 0;
    }
    /**
     * Return the username from the user object.
     * 
     * @access public
     * @return string username if user object is set, else empty string.
     */
    public function getUsername()
    {
        return (property_exists($this->_user, 'username')) ? $this->_user->username : '';
    }
    /**
     * Return the email from the user object.
     * 
     * @access public
     * @return string email if the user object is set, else empty string.
     */
    public function getEmail()
    {
        return (property_exists($this->_user, 'email')) ? $this->_user->email : '';
    }
    /**
     * Return the encrypted password from the user object. Cannot be decrypted.
     * 
     * @access public
     * @return string password if user object is set, else empty string.
     */
    public function getPassword()
    {
        return (property_exists($this->_user, 'password')) ? $this->_user->password : '';
    }
    /**
     * Return the active flag.
     * 
     * @access public
     * @return integer. 1 if active, else 0.
     */
    public function isActive()
    {
        return (property_exists($this->_user, 'active')) ? $this->_user->active : 0;
    }
    /**
     * Change the current users username.
     * 
     * @access public
     * @param string $username. The new username to change to.
     */
    public function updateUsername($username)
    {
        $this->_CI->user_model->updateUser($this->getId(), array('username' => $username));
        $this->_user->username = $username;
    }
    /**
     * Change the current users email.
     * 
     * @access public
     * @param string $email. The new email address you want to use.
     */
    public function updateEmail($email)
    {
        $this->_CI->user_model->updateUser($this->getId(), array('email' => $email));
        $this->_user->email = $email;
    }
    /**
     * Change the current users password. You can optionally encrypt the password
     * before passing it in.
     * 
     * @access public
     * @param string $password. The password you want to change it to.
     */
    public function updatePassword($password)
    {
        if ($this->_config['auto_encrypt']) {
            $password = $this->encryptPassword($password);
        }
        
        $this->_CI->user_model->updateUser($this->getId(), array('password' => $password));
        $this->_user->password = $password;
    }
    /**
     * Deactivate the current user. Set user active flag to 0
     * 
     * @access public
     * @return void
     */
    public function deactivate()
    {
        $this->_CI->user_model->updateUser($this->getId(), array('active' => 0));
        $this->_user->active = 0;
    }
    /**
     * Reactivate the current user. Set user active flag to 1
     * 
     * @access public
     * @return void
     */
    public function reactivate()
    {
        $this->_CI->user_model->updateUser($this->getId(), array('active' => 1));
        $this->_user->active = 1;
    }
    /**
     * Alias for the reactivate function.
     * 
     * @access public
     * @return void
     */
    public function activate()
    {
        $this->reactivate();
    }
}
