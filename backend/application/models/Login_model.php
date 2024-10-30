<?php

class login_model extends CI_Model
{

    public function verify($data)
    {
        $this->db->select('username,type,password');
        $this->db->from('user');
        $this->db->where(array("username" => $data['username']));
        $this->db->limit('1');
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            $result = $query->row();
            $hash   = $result->password;
            if (password_verify($data['userpassword'], $hash)) {
                return $query->row();
            } else {
                return $query->row();

                return false;
            }
        } else {
            return $query->row();

            return false;
        }

    }

}
