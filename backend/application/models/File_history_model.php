<?php

Class File_history_model extends CI_Model {

    function Get($id = NULL, $search = array()) {
        $this->db->select('SQL_CALC_FOUND_ROWS file_history.*', FALSE);
        $this->db->from('file_history');
        if ($id != NULL) {
            $this->db->where('file_history.file_id', $id);
            $this->db->limit('1');
            $query = $this->db->get();
            return $query->num_rows() == 1 ? $query->row() : false;
        } else {
            if (!empty($search)) {
                if (!empty($search['file_id'] ?? '')) {
                    $this->db->where('file_id', $search['file_id']);
                }
                if (!empty($search['file_name'] ?? '')) {
                    $this->db->like('file_name', $search['file_name']);
                }
                if (!empty($search['file_type'] ?? '')) {
                    $this->db->like('file_type', $search['file_type']);
                }
                if (!empty($search['file_path'] ?? '')) {
                    $this->db->like('file_path', $search['file_path']);
                }
                if (!empty($search['upload_at'] ?? '')) {
                    $this->db->like('upload_at', $search['upload_at']);
                }
            }
        
            if (isset($search['start'])) {
                $start = $search['start'];
                $length = $search['length'];
                if ($length != -1) {
                    $this->db->limit($length, $start);
                }
            }

            $this->db->group_by('file_history.file_id');
            $query = $this->db->get();
            $data['last_Q'] = $this->db->last_query();

            $data["records"] = array();
            if ($query->num_rows() > 0) {
                $data["records"] = $query->result();
            }
            $count = $this->db->query('SELECT FOUND_ROWS() AS Count');
            $data["countTotal"] = $this->db->count_all('store_master');
            $data["countFiltered"] = $count->row()->Count;
            return $data;
        }
    }

    function Add($data) {
        $this->db->insert('file_history', $data);
        $id = $this->db->insert_id();
        return $id;
    }

    function Edit($id, $data) {
        $this->db->where('file_history', $id);
        $result = $this->db->update('file_history', $data);
        return $result ? $id : false;
    }

    function Delete($id) {
        $this->db->where('file_id', $id);
        $this->db->delete('file_history');
        return true;
    }

    public function getFileTypes()
    {
        return array(
            'dailysales' => 'Daily Sales',
            'masterpos' => 'Master Pos',
            'donutcount' => 'Donut Count',
            'payroll' => 'Master Payroll',
            'review' => 'Customer Review',
        );
    }
}