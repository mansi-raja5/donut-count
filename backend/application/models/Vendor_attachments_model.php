<?php

Class Vendor_attachments_model extends CI_Model {

    function Get($id = NULL) {
        $this->db->select('*', FALSE)->from('vendor_attachments');
        if ($id != NULL) {
            // Getting only ONE row
            $query = $this->db->where('id', $id)->get();

            if ($query->num_rows() > 0) {
                $data = $query->row();
                return $data;
            } else {
                return false;
            }
        } else {
            // Get all
            $this->db->order_by('filename', 'ASC');
            $query = $this->db->get();

            $data["records"] = array();
            if ($query->num_rows() > 0) {
                // Got some rows, return as assoc array
                $data["records"] = $query->result();
            }
            return $data;
        }
    }

    function Add($data) {
        $this->db->insert('vendor_attachments', $data);
        // Get id of inserted record
        $id = $this->db->insert_id();
        return $id;
    }

    function Edit($id, $data) {
        $this->db->where('id', $id);
        $result = $this->db->update('vendor_attachments', $data);

        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    function Delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('vendor_attachments');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function GetFromField($where) {
        $this->db->select('*', FALSE)->from('vendor_attachments');
        $query = $this->db->where($where)->get();
        $data["records"] = $query->result_array();
        return $data;
    }

}
