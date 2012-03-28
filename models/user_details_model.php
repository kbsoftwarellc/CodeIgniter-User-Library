<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package Simple User Library [getsparks.org]
 * @name CodeIgniter User Details Model
 * @author Kenny Brown
 * @link NA
 */

class User_Details_Model extends CI_Model
{
    /**
     * Set up the database connection.
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * find user details based on user id
     * 
     * @access public
     * @return array $arr. Associative array of detail => value
     * @param integer $userId. The user id to search for.
     */
    public function findDetails($userId)
    {
        $q = $this->db->get_where('users_info', array('user_id' => $userId));
        
        $arr = array();
        
        if ($q->num_rows() > 0) {
            foreach($q->result() as $row) {
                $arr[$row->key] = $row->value;
            }
        }
        
        return $arr;
    }
    /**
     * add or overwrite an existing detail.
     * 
     * @access public
     * @return bool true if successful, otherwise false.
     * @param integer $userId. The user to update.
     * @param string $name. The name of the detail to update.
     * @param mixed $value. The value to store with the key name.
     */
    public function addDetail($userId, $name, $value)
    {
        $sql = "REPLACE INTO users_info SET user_id = ?, key = ?, value = ? WHERE user_id = ? AND key = ?";
        $this->db->query($sql, array($userId, $name, $value, $userId, $name));
        
        return ($this->db->affected_rows() > 0) ? true : false;
    }
    /**
     * update an existing user detail.
     * 
     * @access public
     * @return bool true if update was successful, otherwise false.
     * @param integer $userId. The user to update.
     * @param string $name. The name of the detail to update.
     * @param mixed $value. The value to store with the key name.
     */
    public function updateDetail($userId, $name, $value)
    {
        $where = array(
            'user_id' => $userId,
            'key' => $name
        );
        
        $data = array(
            'user_id' => $userId,
            'key' => $name,
            'value' => $value
        );
        
        $this->db->where($where)->update('users_info', $data);
        
        return ($this->db->affected_rows() > 0) ? true : false;
    }
    /**
     * set an existing user detail to null
     * 
     * @access public
     * @return bool true if update was successful, otherwise false.
     * @param integer $userId. The user to update.
     * @param string $name. The name of the detail to update.
     */
    public function removeDetail($userId, $name)
    {
        $where = array(
            'user_id' => $userId,
            'key' => $name
        );
        
        //$this->db->delete('users_info', $where, 1);
        $this->updateDetail($userId, $name, NULL);
        
        return ($this->db->affected_rows() > 0) ? true : false;
    }
}
