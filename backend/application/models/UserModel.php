<?php
class UserModel extends CI_Model {
    protected $table = 'user';

    public function insertUser($data) {
        $this->db->insert('user', $data);
        return $this->db->insert_id(); // Returns 0 if insert failed
    }

    public function getUserByUsername($username) {
        $this->db->where('username', $username);
        $query = $this->db->get('user');
        return $query->row();
    }

    public function getUserByEmail($email) {
        $this->db->where('email', $email);
        $query = $this->db->get('user');
        return $query->row();
    }

}
