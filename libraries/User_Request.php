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
    protected $_user_request; 
    
    public function __construct()
    {
        $this->_CI = get_instance();
        $this->_CI->load->model('user_request_model');
    }
    
    public function find($requestKey)
    {
        $this->_user_request = $this->_CI->user_request_model->find($requestKey);
        return $this;
    }
    
    public function create($type, $userId)
    {
        $success = false; 
        
        do {
            $requestKey = md5(uniqid(mt_rand(), true));
            $success = $this->_CI->user_request_model->newRequest($type, $userId, $requestKey);
        } while (!$success);
        
        return $this->find($requestKey);
    }
    
    public function complete()
    {
        $this->_CI->user_request_model->completeRequest($this->getId());
        return $this;
    }
    
    public function getId()
    {
        return (property_exists($this->_user_request, 'id')) ? $this->_user_request->id : false;
    }
    
    public function getUserId()
    {
        return (property_exists($this->_user_request, 'user_id')) ? $this->_user_request->user_id : false;
    }
    
    public function getType()
    {
        return (property_exists($this->_user_request, 'type')) ? $this->_user_request->type : false;
    }
    
    public function getRequestKey()
    {
        return (property_exists($this->_user_request, 'request_key')) ? $this->_user_request->request_key : false;
    }
    
    public function isActive()
    {
        return (property_exists($this->_user_request, 'active')) ? $this->_user_request->active : false;
    }
    
    public function getDateRequested()
    {
        return (property_exists($this->_user_request, 'date_requested')) ? $this->_user_request->date_requested : false;
    }
    
    public function getDateCompleted()
    {
        return (property_exists($this->_user_request, 'date_completed')) ? $this->_user_request->date_completed : false;
    }
}
