<?php

class User_Request_Model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
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
    
    public function find($requestKey)
    {
        $q = $this->db->get_where('users_request', array('request_key' => $requestKey), 1);
        
        return ($q->num_rows() > 0) ? $q->row() : false;
    }
}
