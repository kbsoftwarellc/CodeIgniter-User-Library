<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package Simple User Library [getsparks.org]
 * @name CodeIgniter User Request Library
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

class User_Request
{
    /**
     * user_request object. Contains information on a user request.
     * 
     * @var object
     * @access protected
     */
    protected $_user_request; 
    /**
     * __construct() sets up initial requirements for library to function.
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->_CI = get_instance();
        $this->_CI->load->model('user_request_model');
    }
    /**
     * find a request based on a key passed in. Optionally you can look up
     * inactive keys by passing in a second parameter.
     * 
     * @access public
     * @return object User_Request instance.
     * @param string $requestKey.
     * @param integer active. 1 to show search active keys, 0 to search inactive. 
     */
    public function find($requestKey, $active = 1)
    {
        $user_request = $this->_CI->user_request_model->find($requestKey, $active);
        $this->_user_request = ($user_request) ? $user_request : new stdClass(); 
        return $this;
    }
    /**
     * create a new request. You can specify one of the following types:
     * 'password', 'activate', 'disable', 'change_email', 'change_password'
     * 
     * @access public
     * @return object User_Request instance
     * @param string $type. Type of request to create.
     * @param integer $userId, the user id to associate the key with.
     */
    public function create($type, $userId)
    {
        $success = false; 
        
        do {
            $requestKey = md5(uniqid(mt_rand(), true));
            $success = $this->_CI->user_request_model->newRequest($type, $userId, $requestKey);
        } while (!$success);
        
        return $this->find($requestKey);
    }
    /**
     * Mark a request completed and set a completion date.
     * 
     * @access public
     * @return object User_Request instance
     */
    public function complete()
    {
        $this->_CI->user_request_model->completeRequest($this->getId());
        return $this;
    }
    /**
     * Return the Primary Key Id of the table associated with the key.
     * 
     * @access public
     * @return mixed. Returns the primary key or false if user request object not set.
     */
    public function getId()
    {
        return (property_exists($this->_user_request, 'id')) ? $this->_user_request->id : false;
    }
    /**
     * Return the User Id that is associated with the request.
     * 
     * @access public
     * @return mixed. If user request object, then returns user id, else returns false.
     */
    public function getUserId()
    {
        return (property_exists($this->_user_request, 'user_id')) ? $this->_user_request->user_id : false;
    }
    /**
     * Returns the request type associated with the request.
     * 
     * @access public
     * @return mixed. If user request object, then return request type, else return false.
     */
    public function getType()
    {
        return (property_exists($this->_user_request, 'type')) ? $this->_user_request->type : false;
    }
    /**
     * Returns the request key associated with the request.
     * 
     * @access public
     * @return mixed. If user request object set, return request key, else return false.
     */
    public function getRequestKey()
    {
        return (property_exists($this->_user_request, 'request_key')) ? $this->_user_request->request_key : false;
    }
    /**
     * Return whether or not request is active or not.
     * 
     * @access public
     * @return mixed. If user request object set, return active field (1 or 0), else returns false.
     */
    public function isActive()
    {
        return (property_exists($this->_user_request, 'active')) ? $this->_user_request->active : false;
    }
    /**
     * Returns the date the key was created or requested.
     * 
     * @access public
     * @return mixed. If user request object set, return the date requested/created, else return false.
     */
    public function getDateRequested()
    {
        return (property_exists($this->_user_request, 'date_requested')) ? $this->_user_request->date_requested : false;
    }
    /**
     * Returns the date the key was used.
     * 
     * @access public
     * @return mixed. If user request object set, return the date completed, else return false.
     */
    public function getDateCompleted()
    {
        return (property_exists($this->_user_request, 'date_completed')) ? $this->_user_request->date_completed : false;
    }
}
