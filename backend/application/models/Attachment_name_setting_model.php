<?php

Class Attachment_name_setting_model extends CI_Model {

    function Get($id = NULL, $search = array()) {
        $this->db->select('SQL_CALC_FOUND_ROWS attachment_name_setting.*', FALSE);


        $this->db->from('attachment_name_setting');
        // Check if we're getting one row or all records
        if ($id != NULL) {
            // Getting only ONE row
            $this->db->where('attachment_name_setting.id', $id);
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
                    'description' => '',
                    'invoice_name' => '',
                    'document_name_1' => '',
                    'document_name_2' => '',
                    'document_name_3' => '',
                    'selected_type' => '',
                );
                $search = array_merge($defaultSearch, $search);
                if (!empty($search['description'])) {
                    $this->db->where('description', $search['description']);
                }
              
                if (isset($search['order'])) {
                    $order = $search['order'];
                    if ($order[0]['column'] == 1) {
                        $orderby = "description " . strtoupper($order[0]['dir']);
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

            $query = $this->db->get();

            $data["records"] = array();
            if ($query->num_rows() > 0) {
                // Got some rows, return as assoc array
                $data["records"] = $query->result();
            }
            $count = $this->db->query('SELECT FOUND_ROWS() AS Count');
            $data["countTotal"] = $this->db->count_all('attachment_name_setting');
            $data["countFiltered"] = $count->row()->Count;
            return $data;
        }
    }

    function Add($data) {

        $this->db->insert('attachment_name_setting', $data);
        // Get id of inserted record
        $guid = $this->db->insert_id();

        return $guid;
    }

    function Edit($id, $data) {
        $this->db->where('id', $id);
        $result = $this->db->update('attachment_name_setting', $data);
        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    function Delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('attachment_name_setting');
        return true;
    }
   

}
