<?php

Class Phonetype_model extends CI_Model {

    function Get($id = NULL) {
      
        $this->db->select('*', FALSE)->from('phone_type');
        if ($id != NULL) {
            // Getting only ONE row
            $query = $this->db->where('id', $id)->get();

            if ($query->num_rows() > 0) {
                $data["records"] = $query->row();
                return $data;
            } else {
                return false;
            }
        } else {
            // Get all
            $query = $this->db->order_by("type_name ASC")->get();

            $data["records"] = array();
            if ($query->num_rows() > 0) {
                // Got some rows, return as assoc array
                $data["records"] = $query->result();
            }
            return $data;
        }
    }

    function Add($data) {
        $this->db->insert('phone_type', $data);
        // Get id of inserted record
        $id = $this->db->insert_id();
        return $id;
    }

    function Edit($id, $data) {
        $this->db->where('id', $id);
        $result = $this->db->update('phone_type', $data);

        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    function Delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('phone_type');
    }

    function GetFromField($where) {
        $this->db->select('*', FALSE)->from('phone_type');
        $query = $this->db->where($where)->get();
        $data["records"] = $query->result();
        return $data;
    }

}
