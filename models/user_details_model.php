<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User_Details_Model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
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
    
    public function addDetail($userId, $name, $value)
    {
        $sql = "REPLACE INTO users_info SET user_id = ?, key = ?, value = ? WHERE user_id = ? AND key = ?";
        $this->db->query($sql, array($userId, $name, $value, $userId, $name));
        
        return ($this->db->affected_rows() > 0) ? true : false;
    }
    
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
