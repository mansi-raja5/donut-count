<?php
Class Ledger_model extends CI_Model {

    function Get($id = NULL, $search = array()) {
        $this->db->select('SQL_CALC_FOUND_ROWS ledger.*', FALSE);
        $this->db->from('ledger');
        // Check if we're getting one row or all records
        if ($id != NULL) {
            // Getting only ONE row
            $this->db->where('ledger.id', $id);
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
                    'store_key' => '',
                    'month' => '',
                    'year' => '',
                );
                $search = array_merge($defaultSearch, $search);
                if (!empty($search['store_key'])) {
                    $this->db->where('store_key', $search['store_key']);
                }
                if (!empty($search['month'])) {
                    $this->db->where('month', $search['month']);
                }
                if (!empty($search['year'])) {
                    $this->db->where('year', $search['year']);
                }

                if (isset($search['order'])) {
                    $order = $search['order'];
                    if ($order[0]['column'] == 1) {
                        $orderby = "store_key " . strtoupper($order[0]['dir']);
                        $orderby .= ",year " . strtoupper($order[0]['dir']);
                        $orderby .= ",month " . strtoupper($order[0]['dir']);
                    }
                    if ($order[0]['column'] == 2) {
                        $orderby = "month " . strtoupper($order[0]['dir']);
                    }
                    if ($order[0]['column'] == 3) {
                        $orderby = "year " . strtoupper($order[0]['dir']);
                    }

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

            $this->db->group_by('ledger.id');
            $query = $this->db->get();
            // $data['last_Q'] = $this->db->last_query();
            $data["records"] = array();
            if ($query->num_rows() > 0) {
                // Got some rows, return as assoc array
                $data["records"] = $query->result();
            }
            $count = $this->db->query('SELECT FOUND_ROWS() AS Count');
            $data["countTotal"] = $this->db->count_all('ledger');
            $data["countFiltered"] = $count->row()->Count;
            return $data;
        }
    }
    function Add($data) { 
        $ledger = $this->get(null,$data);
        if(!$ledger['records']){
            $this->db->insert('ledger', $data);
            // Get id of inserted record
            $guid = $this->db->insert_id();
            return $guid;
        }
        return $ledger['records'][0]->id;
    }

    function Edit($id, $data) {
        $this->db->where('id', $id);
        $result = $this->db->update('ledger', $data);
        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    function Delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('ledger');
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

    function get_summary($ledger_id){
        $this->db->select("count(*)  as total_ledger_count, sum(if(is_reconcile = 1,1,0)) as total_ledger_reconciled");
        $this->db->Where('ledger_id', $ledger_id);
        $query = $this->db->get('ledger_statement');
        return $query->row();
    }
     public function Delete_where($where = array()) {
        if (!empty($where)) {
            $this->db->where($where);
            $this->db->delete('ledger');
            return true;
        } else {
            return FALSE;
        }
    }
    function get_max_month($max_year){
        $this->db->select('max(month) as max_month');
        $this->db->where('year', $max_year);
        $query = $this->db->get('ledger');
        return $query->row()->max_month;
    }
}