 <?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
  
 class User_Model extends CI_Model
 {
    public function __construct()
    {
        parent::__construct();
    }
    
    public function createUser($username, $email, $password, $active)
    {
        $data = array(
            'username'  => $username,
            'email'     => $email,
            'password'  => $password,
            'active'    => $active    
        );
        
        $this->db->insert('users', $data);
        
        if ($this->db->affected_rows() > 0)
            return $this->db->insert_id();
        else
            throw new Exception('Failed to create new user', 1);
    }
    
    public function emailExists($email)
    {
        $q = $this->db->select('id')->from('users')->where('email', $email)->limit(1)->get();
        return ($q->num_rows() > 0) ? $q->row()->id  : false;;
    }
    
    public function usernameExists($username)
    {
        $q = $this->db->select('id')->from('users')->where('username', $username)->limit(1)->get();
        return ($q->num_rows() > 0) ? $q->row()->id : false;
    }
    
    public function loadUser($key, $value)
    {
        $this->db   ->select('u.*, GROUP_CONCAT(CONCAT(ui.key, "@@@@", ui.value, "....")) as details', false)
                    ->from('users u')
                    ->join('users_info ui', 'u.id = ui.user_id', 'left')
                    ->where('u.'.$key, $value)
                    ->limit(1);
                    
        $q = $this->db->get();                
        
        return ($q->num_rows() > 0) ? $q->row() : false;
    }
    
    public function loadUserByDetail($key, $value)
    {
        $this->db   ->select('u.*, GROUP_CONCAT(CONCAT(ui.key, "@@@@", ui.value, ",,,")) as details', false)
                    ->from('users u')
                    ->join('users_info ui', 'u.id = ui.user_id', 'left')
                    ->where('ui.'.$key, $value)
                    ->limit(1);
                    
        $q = $this->db->get();                    
        
        return ($q->num_rows() > 0) ? $q->row() : false;
    }
    
    public function saveUser($user_id, $username, $email, $password, $active)
    {
        $data = array(
            'username'  => $username,
            'email'     => $email,
            'password'  => $password,
            'active'    => $active
        );
        
        $this->db->where('id', $user_id)->update('users', $data);
        
        return ($this->db->affected_rows() > 0) ? true : false;
    }
    
    public function saveUserDetail($user_id, $key, $value)
    {
        $sql = 'REPLACE INTO users SET ? = ? WHERE id = ?';
        $this->db->query($sql, array($key, $value, $user_id));
        
        return ($this->db->affected_rows() > 0) ? true : false;
    }
 }