<?php
class UserStoreModel extends CI_Model {

    public function getStoresByUserId($userId) {
        $this->db->select('u.id, u.name, u.role, GROUP_CONCAT(store_key) as stores');
        $this->db->from('user u');
        $this->db->join('user_store us', "us.user_id = u.id", 'left', false);
        $this->db->where('u.id', $userId);
        $this->db->group_by('u.id');
        $query = $this->db->get();
        return $query->row_array();
    }

    public function insert($data) {
        return $this->db->insert('user_store', $data);
    }
}
