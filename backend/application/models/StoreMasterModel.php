<?php
class StoreMasterModel extends CI_Model {

    public function getAllStores($userId = null)
    {
        if ($userId !== null) {
            // Fetch stores associated with the user
            $this->db->select('store_master.*');
            $this->db->from('store_master');
            $this->db->join('user_store', 'store_master.key = user_store.store_key');
            $this->db->where('user_store.user_id', $userId);
            $query = $this->db->get();
        } else {
            // Fetch all stores if no user ID is provided
            $query = $this->db->get('store_master');
        }

        return $query->result_array();
    }
}
