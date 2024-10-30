<?php

Class Phonenumbers_model extends CI_Model {

    function Get($id = NULL) {
        $this->db->select('*', FALSE)->from('phonebook_phonenumbers');
        if ($id != NULL) {
            // Getting only ONE row
            $query = $this->db->where('guid', $id)->get();

            if ($query->num_rows() > 0) {
                $data["records"] = $query->row();
                return $data;
            } else {
                return false;
            }
        } else {
            // Get all
//           
            $this->db->order_by('phone_num', 'ASC');
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
        $this->db->insert('phonebook_phonenumbers', $data);
        // Get id of inserted record
        $id = $this->db->insert_id();
        return $id;
    }

    function Edit($id, $data) {
        $this->db->where('guid', $id);
        $result = $this->db->update('phonebook_phonenumbers', $data);

        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    function Delete($id) {
        $this->db->where('guid', $id);
        $this->db->delete('phonebook_phonenumbers');
        if ($this->db->affected_rows() == '1') {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    function GetFromField($where) {
        $this->db->select('*', FALSE)->from('phonebook_phonenumbers');
        $query = $this->db->where($where)->get();
        $data["records"] = $query->result();
        return $data;
    }

}
