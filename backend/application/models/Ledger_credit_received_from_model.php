<?php
Class Ledger_credit_received_from_model extends CI_Model {

    function Get($id = NULL, $search = array()) {
        $this->db->select('SQL_CALC_FOUND_ROWS ledger_credit_received_from.*', FALSE);
        $this->db->from('ledger_credit_received_from');
        // Check if we're getting one row or all records
        if ($id != NULL) {
            // Getting only ONE row
            $this->db->where('ledger_credit_received_from.id', $id);
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
                );
                $search = array_merge($defaultSearch, $search);
                if (isset($search['order'])) {
                    $order = $search['order'];
                    $this->db->order_by($orderby);
                } else {
                    $this->db->order_by('created_on DESC');
                }
            }
            if (isset($search['start'])) {
                $start = $search['start'];
                $length = $search['length'];
                if ($length != -1) {
                    $this->db->limit($length, $start);
                }
            }

            $this->db->group_by('ledger_credit_received_from.id');
            $query = $this->db->get();
            // $data['last_Q'] = $this->db->last_query();
            $data["records"] = array();
            if ($query->num_rows() > 0) {
                // Got some rows, return as assoc array
                $data["records"] = $query->result();
            }
            $count = $this->db->query('SELECT FOUND_ROWS() AS Count');
            $data["countTotal"] = $this->db->count_all('ledger_credit_received_from');
            $data["countFiltered"] = $count->row()->Count;
            return $data;
        }
    }
    function Add($data) { 
        $this->db->insert('ledger_credit_received_from', $data);
        $guid = $this->db->insert_id();
        return $guid;
    }

    function Edit($id, $data) {
        $this->db->where('id', $id);
        $result = $this->db->update('ledger_credit_received_from', $data);
        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    function Delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('ledger_credit_received_from');
        return true;
    }

    function query_result($sql = FALSE) {
        if ($sql) {
            $query = $this->db->query($sql);
            return $query->result();
        }else{
            return FALSE;
        }
    }  
}