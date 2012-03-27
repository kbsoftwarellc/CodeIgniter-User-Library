<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
  
class User_Model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    public function findUser($where_array)
    {
        $q = $this->db->get_where('users', $where_array, 1);
        return ($q->num_rows() > 0) ? $q->row() : false;
    }
    
    public function createUser($username, $email, $password, $active)
    {
        $data = array(
            'username' => $username,
            'email' => $email,
            'password' => $password,
            'active' => $active
        );

        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }
    
    public function updateUser($userId, $data_array)
    {
        $this->db->where('id', $userId);
        $this->db->update('users', $data_array);
    }
    
}