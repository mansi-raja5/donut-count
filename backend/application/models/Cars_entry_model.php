<?php

Class Cars_entry_model extends CI_Model {

    function Get($id = NULL, $search = array()) {
        $this->db->select('SQL_CALC_FOUND_ROWS cars_entry.*, store_master.name', FALSE);
        $this->db->join('store_master', 'store_master.key = cars_entry.store_key', 'INNER');
        $this->db->from('cars_entry');
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
                    'store_key' => '',
                    'dates' => '',
                    'weekend_date' => ''
                );
                $search = array_merge($defaultSearch, $search);
                if ($search['store_key'] != '') {
                    $this->db->where('store_key', $search['store_key']);
                }
                if ($search['dates'] != '') {
                    $this->db->where_in('date', $search['dates']);
                }
                if ($search['weekend_date'] != '') {
                    $this->db->where('weekend_date', date("Y-m-d", strtotime($search['weekend_date'])));
                }
              
                if (isset($search['order'])) {
                    $order = $search['order'];
                    if ($order[0]['column'] == 1) {
                        $orderby = "name " . strtoupper($order[0]['dir']);
                    }
                    if ($order[0]['column'] == 2) {
                        $orderby = "weekend_date " . strtoupper($order[0]['dir']);
                    }
//                    if ($order[0]['column'] == 3) {
//                        $orderby = "day " . strtoupper($order[0]['dir']);
//                    }
//                    if ($order[0]['column'] == 4) {
//                        $orderby = "no_of_cars " . strtoupper($order[0]['dir']);
//                    }
//                    if ($order[0]['column'] == 5) {
//                        $orderby = "avg_time " . strtoupper($order[0]['dir']);
//                    }
                    $this->db->order_by($orderby);
                }
                if (isset($search['start'])) {
                    $start = $search['start'];
                    $length = $search['length'];
                    if ($length != -1) {
                        $this->db->limit($length, $start);
                    }
                }
                $query = $this->db->get();
//                echo $this->db->last_query();
//                exit;
                $data["records"] = array();
                if ($query->num_rows() > 0) {
                    // Got some rows, return as assoc array
                    $data["records"] = $query->result();
                }
                $count = $this->db->query('SELECT FOUND_ROWS() AS Count');
                $data["countTotal"] = $this->db->count_all('cars_entry');
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

        $this->db->insert('cars_entry', $data);
        // Get id of inserted record
        $id = $this->db->insert_id();

        return $id;
    }
    function AddBatch($data) {
        // Run query to insert blank row

        $this->db->insert_batch('cars_entry', $data);
        // Get id of inserted record
//        $id = $this->db->insert_id();

        return true;
    }

    function Edit($id, $data) {
        $this->db->where('id', $id);
        $result = $this->db->update('cars_entry', $data);

        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    function Delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('cars_entry');
    }

}
