<?php
class Bill_model extends CI_Model
{
    public function Get($id = null, $search = array())
    {
        $this->db->select('SQL_CALC_FOUND_ROWS bill.*', false);
        $this->db->from('bill');
        // Check if we're getting one row or all records
        if ($id != null) {
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
                    'vendor_id' => '',
                    'month' => '',
                    'year' => '',
                );
                $search = array_merge($defaultSearch, $search);
                if ($search['vendor_id'] != '') {
                    $this->db->where('vendor_id', $search['vendor_id']);
                }
                if ($search['month'] != '') {
                    $this->db->where('month', $search['month']);
                }
                if ($search['year'] != '') {
                    $this->db->where('year', $search['year']);
                }
                if (isset($search['order'])) {
                    $order = $search['order'];
                    if ($order[0]['column'] == 1) {
                        $orderby = "month " . strtoupper($order[0]['dir']);
                    }
                    $this->db->order_by($orderby);
                }
                if (isset($search['start'])) {
                    $start  = $search['start'];
                    $length = $search['length'];
                    if ($length != -1) {
                        $this->db->limit($length, $start);
                    }
                }
                $query           = $this->db->get();
                $data["records"] = array();
                if ($query->num_rows() > 0) {
                    // Got some rows, return as assoc array
                    $data["records"] = $query->result();
                }
                $count                 = $this->db->query('SELECT FOUND_ROWS() AS Count');
                $data["countTotal"]    = $this->db->count_all('bill');
                $data["countFiltered"] = $count->row()->Count;
                return $data;
            } else {
                $data["records"]       = array();
                $data["countTotal"]    = 0;
                $data["countFiltered"] = 0;
                return $data;
            }
        }
    }

    public function Add($data)
    {
        // Run query to insert blank row

        $this->db->insert('bill', $data);
        // Get id of inserted record
        $id = $this->db->insert_id();

        return $id;
    }

    public function Edit($id, $data)
    {
        $this->db->where('id', $id);
        $result = $this->db->update('bill', $data);

        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    public function Delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('bill');
    }

}
