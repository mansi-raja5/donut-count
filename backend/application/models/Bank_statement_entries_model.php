<?php
class Bank_statement_entries_model extends CI_Model
{
    public function Get($id = null, $search = array())
    {
        $this->db->select('SQL_CALC_FOUND_ROWS bank_statement_entries.*', false);
        $this->db->from('bank_statement_entries');
        // Check if we're getting one row or all records
        if ($id != null) {
            // Getting only ONE row
            $this->db->where('bank_statement_entries.id', $id);
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
                    'bank_statement_id' => '',
                );
                $search = array_merge($defaultSearch, $search);

                if (!empty($search['bank_statement_id'])) {
                    $this->db->where('bank_statement_id', $search['bank_statement_id']);
                }
                if (isset($search['is_reconcile']) && in_array($search['is_reconcile'], [0, 1])) {
                    $this->db->where('is_reconcile', $search['is_reconcile']);
                }
                if (!empty($search['is_reconciled_current'])) {
                    $this->db->where('is_reconciled_current', $search['is_reconciled_current']);
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
            $data["countTotal"]    = $this->db->count_all('bank_statement_entries');
            $data["countFiltered"] = $count->row()->Count;
            return $data;
        }
    }

    public function Add($data)
    {

        $this->db->insert('bank_statement_entries', $data);
        // Get id of inserted record
        $guid = $this->db->insert_id();

        return $guid;
    }

    public function Edit($id, $data)
    {
        if (is_array($id)) {
            $this->db->where_in('id', $id);
        } else {
            $this->db->where('id', $id);
        }
        $result = $this->db->update('bank_statement_entries', $data);
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }
    public function Delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('bank_statement_entries');
        return true;
    }
    public function add_batch($statement_entries)
    {
        if (!empty($statement_entries)) {
            $this->db->insert_batch('bank_statement_entries', $statement_entries);
            return true;
        }
    }

    public function get_bank_statements($bank_statement_ids = array())
    {
        if (!empty($bank_statement_ids)) {
            $this->db->select('bank_statement_entries.*,ledger_attachments.id as attach_id, ledger_attachments.uploaded_file_name, ledger_attachments.uploaded_url');

            $this->db->join('ledger_statement', 'ledger_statement.bank_statement_id = bank_statement_entries.id', 'LEFT');
            $this->db->join('ledger_attachments', 'ledger_attachments.statement_id = ledger_statement.id', 'LEFT');
            $this->db->where_in('bank_statement_entries.id', $bank_statement_ids);
            $this->db->where('type', 'invoice');
            $this->db->group_by('bank_statement_entries.id');
            $query = $this->db->get('bank_statement_entries');

            return $query->result();
        } else {
            return false;
        }

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
}
