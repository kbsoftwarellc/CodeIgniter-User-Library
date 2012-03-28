<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @package Simple User Library [getsparks.org]
 * @name CodeIgniter User Model
 * @author Kenny Brown
 * @link NA
 */

class User_Model extends CI_Model
{
    /**
     * __construct() sets up the DB connection
     * 
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
    /**
     * find a user in the database based on $where parmeters passed in.
     * 
     * @access public
     * @return mixed. DB Object if user found, else false.
     * @param array $where_array. DB search parameters.
     */
    public function findUser($where_array)
    {
        $q = $this->db->get_where('users', $where_array, 1);
        return ($q->num_rows() > 0) ? $q->row() : false;
    }
    /**
     * create a new user in the database.
     * 
     * @access public
     * @return integer user id. 
     * @param string $username
     * @param string $email
     * @param string $password
     * @param integer $active
     */
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
    /**
     * update an existing user.
     * 
     * @access public
     * @return void
     * @param integer $userId. User Id to update.
     * @param array $data_array. Information array to update.
     */
    public function updateUser($userId, $data_array)
    {
        $this->db->where('id', $userId);
        $this->db->update('users', $data_array);
    }
    
}