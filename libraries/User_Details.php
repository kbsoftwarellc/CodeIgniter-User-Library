<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @package Simple User Library [getsparks.org]
 * @name CodeIgniter User Details Library
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

class User_Details
{
    protected $_user_details;
    protected $_user_id;
    
    public function __construct()
    {
        $this->_CI = get_instance();
        $this->_CI->load->model('user_details_model');
    }
    
    public function find($userId)
    {
        $this->_user_id = $userId;
        $this->_user_details = $this->_CI->user_details_model->findDetails($userId);
        
        return $this;
    }
    
    public function get($name)
    {
        return (isset($this->_user_details[$name])) ? $this->_user_details[$name] : false;
    }
    
    public function add($name, $value)
    {
        if (!isset($this->_user_details[$name]) && $this->_CI->user_details_model->addDetail($this->_user_id, $name, $value)) {
            $this->_user_details[$name] = $value; 
        }
        
        return $this;
    }
    
    public function update($name, $value)
    {
        if ($this->get($name) != $value && $this->_CI->user_details_model->updateDetail($this->_user_id, $name, $value)) {
            $this->_user_details[$name] = $value; 
        }
        
        return $this;
    }
    
    public function remove($name)
    {
        if ($this->_CI->user_details_model->removeDetail($this->_user_id, $name)) {
            unset($this->_user_details[$name]);
        }
        
        return $this;
    }
}
