<?php
Class Royal_model extends CI_Model {
    function Get($id = NULL) {
        $this->db->select('*', FALSE)->from('royalty');
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
        $this->db->insert('royalty', $data);
        // Get id of inserted record
        $id = $this->db->insert_id();
        return $id;
    }

    function Add_Batch($data) {
        $this->db->insert_batch('royalty', $data);
        $guid = $this->db->insert_id();

        return $guid;
    }

    function query_result($sql = FALSE) {
        if ($sql) {
            $query = $this->db->query($sql);
            return $query->result();
        }else{
            return FALSE;
        }
    }

    function Edit($id, $data) {
        $this->db->where('id', $id);
        $result = $this->db->update('royalty', $data);

        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    function Delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('royalty');
    }

    function GetFromField($where) {
        $this->db->select('*', FALSE)->from('royalty');
        $query = $this->db->where($where)->get();
        $data["records"] = $query->result();
        return $data;
    }

}
