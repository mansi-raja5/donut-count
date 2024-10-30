<?php

Class store_master_model extends CI_Model {

    function Get($id = NULL, $search = array()) {
        $this->db->select('SQL_CALC_FOUND_ROWS store_master.*', FALSE);
        $this->db->from('store_master');
        // Check if we're getting one row or all records
        if ($id != NULL) {
            // Getting only ONE row
            $this->db->where('store_master.store_id', $id);
            // $this->db->where('account_map.company_guid', $this->session->userdata('company_id'));
            $this->db->limit('1');
            $query = $this->db->get();
            if ($query->num_rows() == 1) {
                // One row, match!
                return $query->row();
            } else {
                // None
                return false;
            }
        } else {
            // Get all
            if (!empty($search)) {
                $defaultSearch = array(
                    'name' => '',
                    'store_key' => '',
                    'status' => '',
                );
                $search = array_merge($defaultSearch, $search);
                if (!empty($search['name'])) {
                    $this->db->where('name', $search['name']);
                }
                if (!empty($search['store_key'])) {
                    $this->db->like('store_key', $search['store_key']);
                }
                if (!empty($search['status'])) {
                    $this->db->where('status', $search['status']);
                }

                
            }
            if (isset($search['start'])) {
                $start = $search['start'];
                $length = $search['length'];
                if ($length != -1) {
                    $this->db->limit($length, $start);
                }
            }

            $this->db->group_by('store_master.store_id');
            $query = $this->db->get();
            $data['last_Q'] = $this->db->last_query();

            $data["records"] = array();
            if ($query->num_rows() > 0) {
                // Got some rows, return as assoc array
                $data["records"] = $query->result();
            }
            $count = $this->db->query('SELECT FOUND_ROWS() AS Count');
            $data["countTotal"] = $this->db->count_all('store_master');
            $data["countFiltered"] = $count->row()->Count;
            return $data;
        }
    }

   

    function Add($data) {
      
        $this->db->insert('store_master', $data);
        // Get id of inserted record
        $guid = $this->db->insert_id();
      
        return $guid;
    }

    function Edit($id, $data) {
        $this->db->where('store_id', $id);
        $result = $this->db->update('store_master', $data);
        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    function Delete($id) {
        $this->db->where('store_id', $id);
        $this->db->delete('store_master');
        return true;
    }
    function Get_By_Key($key){
        $this->db->where('key', $key);
        $query = $this->db->get('store_master');
        return $query->row();
    }
   
    function get_all_store_keys()
    {
        $this->db->select('`key`');
        $this->db->from('store_master');
        return  array_map (function($value){
            return $value['key'];
        } , $this->db->get()->result_array());
    }
}