<?php if (!defined('BASEPATH')) exit('No direct script access is allowed');
/**
 * @package Simple User Library [getsparks.org]
 * @name CodeIgniter User Request Model
 * @author Kenny Brown
 * @link NA
 */

class User_Request_Model extends CI_Model
{
    /**
     * Sets up the required DB connections.
     * 
     * @access protected
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * create a new request key
     * 
     * @access public
     * @return bool true if successful, else false.
     */
    public function newRequest($type, $userId, $key, $active = 1)
    {
        $data = array(
            'type' => $type,
            'user_id' => $userId,
            'request_key' => $key,
            'active' => $active
        );
        
        $this->db->insert('users_request', $data);
        
        return ($this->db->insert_id() > 0) ? true : false; 
    }
    /**
     * mark an existing request completed
     * 
     * @access public
     * @return void
     */
    public function completeRequest($id)
    {
        if (!$id) return;
        
        $datetime = new DateTime;
        
        $data = array(
            'active' => 0,
            'date_completed' => $datetime->format('Y-m-d H:i:s')
        );
        
        $this->db->update('users_request', $data, array('id' => $id), 1);
    }
    /**
     * find an existing request
     * 
     * @access public
     * @return mixed. DB Object if match found, else false.
     */
    public function find($requestKey, $active)
    {
        $q = $this->db->get_where('users_request', array('request_key' => $requestKey, 'active' => $active), 1);
        
        return ($q->num_rows() > 0) ? $q->row() : false;
    }
}
