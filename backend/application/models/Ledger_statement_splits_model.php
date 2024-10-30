<?php
class Ledger_statement_splits_model extends CI_Model
{
    public function Get($id = null, $search = array())
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ledger_statement_splits.*', false);
        $this->db->from('ledger_statement_splits');
        // Check if we're getting one row or all records
        if ($id != null) {
            // Getting only ONE row
            $this->db->where('ledger_statement_splits.id', $id);
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
                    'store_id'         => '',
                    'ledger_unique'    => '',
                    'credit_amt'       => '',
                    'debit_amt'        => '',
                    'transaction_type' => '',
                    'credit_date'      => '',
                    'document_type'    => '',
                );
                $search = array_merge($defaultSearch, $search);
                if (!empty($search['store_id'])) {
                    $this->db->where('store_id', $search['store_id']);
                }
                if (!empty($search['ledger_unique'])) {
                    $this->db->like('ledger_unique', $search['ledger_unique']);
                }
                if (!empty($search['credit_amt'])) {
                    $this->db->where('credit_amt', $search['credit_amt']);
                }
                if (!empty($search['debit_amt'])) {
                    $this->db->where('debit_amt', $search['debit_amt']);
                }
                if (!empty($search['credit_amt'])) {
                    $this->db->where('credit_amt', $search['credit_amt']);
                }
                if (!empty($search['transaction_type'])) {
                    $this->db->where('transaction_type', $search['transaction_type']);
                }
                if (!empty($search['credit_date'])) {
                    $this->db->where('credit_date', $search['credit_date']);
                }
                if (!empty($search['document_type'])) {
                    $this->db->where('document_type', $search['document_type']);
                }
                $this->db->order_by('created_on DESC');
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
            $data["countTotal"]    = $this->db->count_all('ledger_statement_splits');
            $data["countFiltered"] = $count->row()->Count;
            return $data;
        }
    }

    public function Add($data)
    {

        $this->db->insert('ledger_statement_splits', $data);
        // Get id of inserted record
        $guid = $this->db->insert_id();

        return $guid;
    }

    public function Edit($id, $data)
    {
        $this->db->where('id', $id);
        $result = $this->db->update('ledger_statement_splits', $data);
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
        $this->db->delete('ledger_statement_splits');
        return true;
    }

    public function query_result($sql = false)
    {
        if ($sql) {
            $query = $this->db->query($sql);
            return $query->result();
        } else {
            return false;
        }
    }

    //comments related functions

    public function add_comment_ledgersplit($data)
    {
        $assign_hd_ids = $data['assign_hd_ids'];
        $statement_id  = $data['comment_statement_id'];
        $update_count  = 0;
        if ($data['hd_comments'] != ""):
            $assign_comments = explode(",", $data['hd_comments']);

            $ledger_id     = $data['comment_ledger_id'];
            $comments      = $data['comment'];
            $amount        = $data['amount-comment'];
            $comments_data = array();

            foreach ($assign_comments as $key => $value) {
                if ($value == "false") {
                    $add_statement_id = 0;
                } else {
                    $add_statement_id = $data['comment_statement_id'];
                }

                $comment_data = array("ledger_id" => $ledger_id, "statement_id" => $add_statement_id, "description" => $comments[$key], "amount" => $amount[$key], "document_type" => "general_section", "created_on" => date("Y-m-d H:i:s"), "updated_on" => date("Y-m-d H:i:s"));
                $this->db->insert('ledger_statement_splits', $comment_data);
                $update_count++;
            }
        endif;
        //assign comments
        if ($assign_hd_ids != ""):
            $query  = $this->db->query("SELECT statement_id,id FROM `ledger_statement_splits` where id in (" . $assign_hd_ids . ")");
            $result = $query->result_array();
            foreach ($result as $key => $value):
                $this->db->query("UPDATE `ledger_statement_splits` set statement_id=" . $statement_id . " where id = " . $value['id']);
                $update_count++;
            endforeach;
        endif;
        return $update_count;
    }

    public function get_unassign_comments($ledger_id)
    {
        $query = $this->db->query("SELECT * FROM `ledger_statement_splits` where statement_id=0 and ledger_id=" . $ledger_id);
        return $query->result_array();
    }
    public function get_assign_comments($ledger_id, $statement_id)
    {
        $query = $this->db->query("SELECT * FROM `ledger_statement_splits` where statement_id=" . $statement_id . " and ledger_id=" . $ledger_id);
        return $query->result_array();
    }

    public function set_unassign_comments($ledger_id, $statement_id, $hd_ids, $data)
    {
        //unassign comments
        $update_count = 0;
        if ($hd_ids != "") {
            $query  = $this->db->query("SELECT statement_id,id FROM `ledger_statement_splits` where id in (" . $hd_ids . ")");
            $result = $query->result_array();
            foreach ($result as $key => $value):
                $this->db->query("UPDATE `ledger_statement_splits` set statement_id=0 where id = " . $value['id']);
                $update_count++;
            endforeach;

        }

        //update comments
        $edit_ids = $data['edit_ids'];
        if ($edit_ids != "") {
            $query  = $this->db->query("SELECT statement_id,id FROM `ledger_statement_splits` where id in (" . $edit_ids . ")");
            $result = $query->result_array();
            $ids    = explode(",", $edit_ids);
            foreach ($result as $key => $value):
                $search      = array_search($value['id'], $data['edit_ids_value']);
                $description = $data['comment'][$search];
                $amount      = $data['amount'][$search];
                $this->db->query("UPDATE `ledger_statement_splits` set description='" . $description . "',amount=" . $amount . " where id = " . $value['id']);
                $update_count++;
            endforeach;
        }
        return $update_count;
    }
    //----------------------------------------------------------
}
