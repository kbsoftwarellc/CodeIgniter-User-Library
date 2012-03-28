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
    /**
     * user details object
     * 
     * @var object
     * @access protected
     */
    protected $_user_details;
    /**
     * users id
     * 
     * @var integer
     * @access protected
     */
    protected $_user_id;
    /**
     * __construct() function to set up the basic stuff
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        $this->_CI = get_instance();
        $this->_CI->load->model('user_details_model');
    }
    /**
     * find a users additional details/info based on a user id
     * 
     * @access public
     * @return object User_Details instance.
     * @param integer $userId. The Id of the user you want to look up details on.
     */
    public function find($userId)
    {
        $this->_user_id = $userId;
        $this->_user_details = $this->_CI->user_details_model->findDetails($userId);
        
        return $this;
    }
    /**
     * return the value of an existing detail. If it does not exist, it will return false
     * 
     * @access public
     * @return mixed value. The value stored in the detail. Or false if not set.
     * @param string name. The name of the key you want to get a value for.
     */
    public function get($name)
    {
        return (isset($this->_user_details[$name])) ? $this->_user_details[$name] : false;
    }
    /**
     * add a new user detail record. If a record with the key already exists, it will be overwritten.
     * 
     * @access public
     * @return object User_Detail instance.
     * @param string $name. The key name of the detail to be stored in database.
     * @param mixed $value. The value to be associated with the key.
     */
    public function add($name, $value)
    {
        if (!isset($this->_user_details[$name]) && $this->_CI->user_details_model->addDetail($this->_user_id, $name, $value)) {
            $this->_user_details[$name] = $value; 
        }
        
        return $this;
    }
    /**
     * update an existing key in the database. If the record does not exist, nothing is updated.
     * 
     * @access public
     * @return objet User_Detail instance.
     * @param string $name. The key of the user detail to be updated.
     * @param mixed $value. The value to be associated with the key.
     */
    public function update($name, $value)
    {
        if ($this->get($name) != $value && $this->_CI->user_details_model->updateDetail($this->_user_id, $name, $value)) {
            $this->_user_details[$name] = $value; 
        }
        
        return $this;
    }
    /**
     * sets a user detail to a null value. If record does not exist, nothing is updated.
     * 
     * @access public
     * @return object User_Detail instance.
     * @param string $name. The key, or name, of the user detail to be set to null.
     */
    public function remove($name)
    {
        if ($this->_CI->user_details_model->removeDetail($this->_user_id, $name)) {
            unset($this->_user_details[$name]);
        }
        
        return $this;
    }
}
