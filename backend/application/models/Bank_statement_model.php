<?php
class Bank_statement_model extends CI_Model
{
    public function Get($id = null, $search = array())
    {
        $this->db->select('SQL_CALC_FOUND_ROWS bank_statement.*', false);

        $this->db->from('bank_statement');
        // Check if we're getting one row or all records
        if ($id != null) {
            // Getting only ONE row
            $this->db->where('bank_statement.id', $id);
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
                    'month'     => '',
                    'year'      => '',
                );
                $search = array_merge($defaultSearch, $search);
                if (!empty($search['key'])) {
                    $this->db->where('key', $search['store_id']);
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
                $start  = $search['start'];
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
            $count                 = $this->db->query('SELECT FOUND_ROWS() AS Count');
            $data["countTotal"]    = $this->db->count_all('bank_statement');
            $data["countFiltered"] = $count->row()->Count;
            return $data;
        }
    }

    public function Add($data)
    {

        $this->db->insert('bank_statement', $data);
        // Get id of inserted record
        $guid = $this->db->insert_id();

        return $guid;
    }

    public function Edit($id, $data)
    {
        $this->db->where('id', $id);
        $result = $this->db->update('bank_statement', $data);
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
        $this->db->delete('bank_statement');
        return true;
    }

    public function get_summary($bank_id)
    {
        $this->db->select("count(*)  as total_bank_count, sum(if(is_reconcile = 1,1,0)) as total_bank_reconciled");
        $this->db->Where('bank_statement_id', $bank_id);
        $query = $this->db->get('bank_statement_entries');
        return $query->row();
    }

    public function Delete_where($where = array())
    {
        if (!empty($where)) {
            $this->db->where($where);
            $this->db->delete('bank_statement');
            return true;
        } else {
            return false;
        }
    }
    public function get_max_month($max_year)
    {
        $this->db->select('max(month) as max_month');
        $this->db->where('year', $max_year);
        $query = $this->db->get('bank_statement');
        return $query->row()->max_month;
    }

}
