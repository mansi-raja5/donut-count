<?php
class Bill_item_model extends CI_Model
{
    public function Get($id = null, $search = array())
    {
        $this->db->select('SQL_CALC_FOUND_ROWS bill_item_entries.*,bill_category.category_name', false);
        $this->db->join('bill_category', 'bill_category.id = bill_item_entries.category_key', 'LEFT');
        $this->db->from('bill_item_entries');
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
                    'bill_id'      => '',
                    'type'         => '',
                    'exclude_type' => '',
                );
                $search = array_merge($defaultSearch, $search);
                if (isset($search['bill_id']) && $search['bill_id'] != '') {
                    $this->db->where('bill_item_entries.bill_id', $search['bill_id']);
                }
                if (isset($search['type']) && $search['type'] != '') {
                    $this->db->where('bill_item_entries.type', $search['type']);
                }
                if (isset($search['exclude_type']) && $search['exclude_type'] != '') {
                    $this->db->where('bill_item_entries.type != ', $search['exclude_type']);
                }
                if (isset($search['store_key']) && $search['store_key'] != '') {
                    $this->db->where('bill_item_entries.store_key', $search['store_key']);
                }
                if (isset($search['category_key']) && $search['category_key'] != '') {
                    $this->db->where('bill_item_entries.category_key', $search['category_key']);
                }
                if (isset($search['description']) && $search['description'] != '') {
                    $this->db->where('bill_item_entries.description', $search['description']);
                }
                if (isset($search['not_matched_amount']) && $search['not_matched_amount'] != '') {
                    $this->db->where('bill_item_entries.amount !=', $search['not_matched_amount']);
                }

                if (isset($search['start'])) {
                    $start  = $search['start'];
                    $length = $search['length'];
                    if ($length != -1) {
                        $this->db->limit($length, $start);
                    }
                }

                $this->db->group_by('bill_item_entries.id');
                $query           = $this->db->get();
                $data["records"] = array();
                if ($query->num_rows() > 0) {
                    $data["records"] = $query->result();
                }
                $count                 = $this->db->query('SELECT FOUND_ROWS() AS Count');
                $data["countTotal"]    = $this->db->count_all('bill_item_entries');
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

    public function Get_Check_Entry($id = null, $search = array())
    {
        $this->db->select('SQL_CALC_FOUND_ROWS bill_check_entries.*', false);
        $this->db->from('bill_check_entries');
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
                    'bill_id' => '',
                );
                $search = array_merge($defaultSearch, $search);
                if ($search['bill_id'] != '') {
                    $this->db->where('bill_check_entries.bill_id', $search['bill_id']);
                }

                if (isset($search['start'])) {
                    $start  = $search['start'];
                    $length = $search['length'];
                    if ($length != -1) {
                        $this->db->limit($length, $start);
                    }
                }

                $this->db->group_by('bill_check_entries.bc_id');
                $query           = $this->db->get();
                $data["records"] = array();
                if ($query->num_rows() > 0) {
                    $data["records"] = $query->result();
                }
                $count                 = $this->db->query('SELECT FOUND_ROWS() AS Count');
                $data["countTotal"]    = $this->db->count_all('bill_check_entries');
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

        $this->db->insert('bill_item_entries', $data);
        // Get id of inserted record
        $id = $this->db->insert_id();

        return $id;
    }

    public function Edit($id, $data)
    {
        $this->db->where('id', $id);
        $result = $this->db->update('bill_item_entries', $data);

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
        $this->db->delete('bill_item_entries');
    }
    public function Delete_by_billid($bill_id, $tbl_name = '')
    {
        if ($tbl_name == '') {
            $tbl_name = 'bill_item_entries';
        }
        $this->db->where('bill_id', $bill_id);
        $this->db->delete($tbl_name);
    }

    public function Add_Batch($data, $tbl = '')
    {
        // Run query to insert blank row
        if ($tbl == '') {
            $tbl = 'bill_item_entries';
        }
        $this->db->insert_batch($tbl, $data);
        // Get id of inserted record
        $id = $this->db->insert_id();

        return $id;
    }

    public function Delete_item_by_exguid($bill_id)
    {
        $this->db->where('bill_id', $bill_id);
        $this->db->delete('bill_item_entries');
    }
    public function get_latest_item($where)
    {
        $this->db->where($where);
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get('bill_item_entries');
        return $query->row();
    }

}
