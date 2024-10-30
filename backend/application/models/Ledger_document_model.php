<?php

Class Ledger_document_model extends CI_Model {

    function Get($id = NULL, $search = array()) {
        $this->db->select('SQL_CALC_FOUND_ROWS ledger_document.*', FALSE);


        $this->db->from('ledger_document');
        // Check if we're getting one row or all records
        if ($id != NULL) {
            // Getting only ONE row
            $this->db->where('ledger_document.id', $id);
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
                    'key_name' => '',
                    'label' => '',
                    'is_active' => '',
                );
                $search = array_merge($defaultSearch, $search);
                if (!empty($search['key_name'])) {
                    $this->db->where('key_name', $search['key_name']);
                }
                if (!empty($search['label'])) {
                    $this->db->like('label', $search['label']);
                }
                if (!empty($search['is_active'])) {
                    $this->db->where('is_active', $search['is_active']);
                }

                if (isset($search['order'])) {
                    $order = $search['order'];
                    if ($order[0]['column'] == 1) {
                        $orderby = "key_name " . strtoupper($order[0]['dir']);
                    }
                    if ($order[0]['column'] == 2) {
                        $orderby = "label " . strtoupper($order[0]['dir']);
                    }
                    if ($order[0]['column'] == 3) {
                        $orderby = "is_active " . strtoupper($order[0]['dir']);
                    }

                    $this->db->order_by($orderby);
                } else {
//                    $this->db->order_by('accounts.account_number asc,account_map.lft asc');
                    $this->db->order_by('label asc');
                }
            }
            if (isset($search['start'])) {
                $start = $search['start'];
                $length = $search['length'];
                if ($length != -1) {
                    $this->db->limit($length, $start);
                }
            }

            $this->db->group_by('ledger_document.id');
            $query = $this->db->get();
            $data['last_Q'] = $this->db->last_query();

            $data["records"] = array();
            if ($query->num_rows() > 0) {
                // Got some rows, return as assoc array
                $data["records"] = $query->result();
            }
            $count = $this->db->query('SELECT FOUND_ROWS() AS Count');
            $data["countTotal"] = $this->db->count_all('ledger_document');
            $data["countFiltered"] = $count->row()->Count;
            return $data;
        }
    }

   

    function Add($data) {
      
        $this->db->insert('ledger_document', $data);
        // Get id of inserted record
        $guid = $this->db->insert_id();
      
        return $guid;
    }

    function Edit($id, $data) {
        $this->db->where('id', $id);
        $result = $this->db->update('ledger_document', $data);
        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    function Delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('ledger_document');
        return true;
    }
   
}