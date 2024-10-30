<?php

Class Special_day_model extends CI_Model {

    function Get($id = NULL, $search = array()) {
        $this->db->select('SQL_CALC_FOUND_ROWS special_day.*, store_master.key as store_key', FALSE);
        $this->db->join('store_master', 'store_master.key = special_day.store_key', 'LEFT');
        $this->db->from('special_day');
        // Check if we're getting one row or all records
        if ($id != NULL) {
            // Getting only ONE row
            $this->db->where('id', $id);
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
                    'date' => '',
                );
                $search = array_merge($defaultSearch, $search);
                if ($search['store_key'] != '') {
                    $this->db->where('special_day.store_key', $search['store_key']);
                }
                if ($search['name'] != '') {
                    $this->db->where('name', $search['name']);
                }
                if ($search['date'] != '') {
                    $this->db->where('date', $search['date']);
                }
             

                if (isset($search['order'])) {
                    $order = $search['order'];
                    if ($order[0]['column'] == 1) {
                        $orderby = "special_day.store_key " . strtoupper($order[0]['dir']);
                    }
                    if ($order[0]['column'] == 2) {
                        $orderby = "date " . strtoupper($order[0]['dir']);
                    }
                    if ($order[0]['column'] == 3) {
                        $orderby = "name " . strtoupper($order[0]['dir']);
                    }
                    $this->db->order_by($orderby);
                }
                if (isset($search['start'])) {
                    $start = $search['start'];
                    $length = $search['length'];
                    if ($length != -1) {
                        $this->db->limit($length, $start);
                    }
                }
                 // $this->db->group_by('name');
                $query = $this->db->get();
                $data["records"] = array();
                if ($query->num_rows() > 0) {
                    // Got some rows, return as assoc array
                    $data["records"] = $query->result();
                }
                $count = $this->db->query('SELECT FOUND_ROWS() AS Count');
                $data["countTotal"] = $this->db->count_all('special_day');
                $data["countFiltered"] = $count->row()->Count;
                return $data;
            } else {
                $data["records"] = array();
                $data["countTotal"] = 0;
                $data["countFiltered"] = 0;
                return $data;
            }
        }
    }

    function Add($data) {
        // Run query to insert blank row
        $this->db->insert('special_day', $data);
        // Get id of inserted record
        $id = $this->db->insert_id();

        return $id;
    }

    function Edit($id, $data) {
        $this->db->where('id', $id);
        $result = $this->db->update('special_day', $data);

        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    function Delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('special_day');
    }
    function Add_batch($data){
        $this->db->insert_batch('special_day', $data);
        // Get id of inserted record
        $id = $this->db->insert_id();

        return $id;
    }
}
