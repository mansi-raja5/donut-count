<?php
Class Review_model extends CI_Model {

    function Get($id = NULL, $search = array()) {
        $this->db->select('SQL_CALC_FOUND_ROWS customer_review.*, store_master.name', FALSE);
        $this->db->join('store_master', 'store_master.key = customer_review.store_key', 'INNER');
        $this->db->from('customer_review');
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
                    'duration_type' => '',
                    'n_number' => '',
                    'five_star' => '',
                    'five_star' => '',
                    'end_date' => '',
                );
                $search = array_merge($defaultSearch, $search);
                if ($search['store_key'] != '') {
                    $this->db->where('store_key', $search['store_key']);
                }
                if ($search['duration_type'] != '') {
                    $this->db->where('duration_type', $search['duration_type']);
                }
                if ($search['n_number'] != '') {
                    $this->db->where('n_number', $search['n_number']);
                }
                if ($search['five_star'] != '') {
                    $this->db->where('five_star', $search['five_star']);
                }
                if ($search['end_date'] != '') {
                    $this->db->where('end_date', date("Y-m-d", strtotime($search['end_date'])));
                }
              
                if (isset($search['order'])) {
                    $order = $search['order'];
                    if ($order[0]['column'] == 1) {
                        $orderby = "store_key " . strtoupper($order[0]['dir']);
                    }
                    else if ($order[0]['column'] == 2) {
                        $orderby = "n_number " . strtoupper($order[0]['dir']);
                    }
                    else if ($order[0]['column'] == 2) {
                        $orderby = "five_star " . strtoupper($order[0]['dir']);
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
                $query = $this->db->get();
                $data["records"] = array();
                if ($query->num_rows() > 0) {
                    // Got some rows, return as assoc array
                    $data["records"] = $query->result();
                }
                $count = $this->db->query('SELECT FOUND_ROWS() AS Count');
                $data["countTotal"] = $this->db->count_all('customer_review');
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
    function add($table,$data) {
    	// check if entry is there date and store
    	$query = $this->db->get_where($table, array(
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'store_key' => $data['store_key'],
            'type' => $data['type'],
        ));

        $count = $query->num_rows();
        $row = $query->row();
        if ($count === 0) {
            $this->db->insert($table, $data);
        	$guid = $this->db->insert_id();
        }else{
        	$this->db->where('id', $row->id);
        	$this->db->update($table, $data);
        	$guid = $this->db->insert_id();
        }

        return true;
    }
    function Get_keys(){
        $this->db->select("DISTINCT(REPLACE(type,'_',' ')) as type");
        $query = $this->db->get('customer_review');
        return $query->result();
    }
}
