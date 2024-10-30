<?php
class Bill_payment_model extends CI_Model
{
    public function Get($id = null, $search = array())
    {
        $this->db->select('SQL_CALC_FOUND_ROWS bill_payment.*', false);
        $this->db->from('bill_payment');
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
                    'bill_guid'            => '',
                    'bill_credit_acc_guid' => '',
                    'created_date_from'    => '',
                    'created_date_to'      => '',
                    'bill_guids'           => '',
                );
                $search = array_merge($defaultSearch, $search);
                if ($search['bill_guid'] != '') {
                    $this->db->where('bill_guid', $search['bill_guid']);
                }
                if ($search['bill_credit_acc_guid'] != '') {
                    $this->db->where('bill_credit_acc_guid', $search['bill_credit_acc_guid']);
                }

                if (!empty($search['created_date_from'])) {
                    $this->db->where('DATE(created_on) >=', date('Y-m-d', strtotime($search['created_date_from'])));
                }

                if (!empty($search['created_date_to'])) {
                    $this->db->where('DATE(created_on) <=', date('Y-m-d', strtotime($search['created_date_to'])));
                }

                if ($search['bill_guids'] != '') {
                    $this->db->where_in('bill_guid', $search['bill_guids']);
                    $this->db->order_by('id', 'ASC');
                }

                if (isset($search['start'])) {
                    $start  = $search['start'];
                    $length = $search['length'];
                    if ($length != -1) {
                        $this->db->limit($length, $start);
                    }
                }

                $query = $this->db->get();
                if (isset($search['bill_guids']) && !empty($search['bill_guids'])) {
                    $data = $query->result_array();
                    return $data;
                } else {
                    $data["records"] = array();
                    if ($query->num_rows() > 0) {
                        // Got some rows, return as assoc array
                        $data["records"] = $query->result();
                    }
                    $count                 = $this->db->query('SELECT FOUND_ROWS() AS Count');
                    $data["countTotal"]    = $this->db->count_all('bill_payment');
                    $data["countFiltered"] = $count->row()->Count;
                    return $data;
                }
            } else {
                $data["records"]       = array();
                $data["countTotal"]    = 0;
                $data["countFiltered"] = 0;
                return $data;
            }
        }
    }

    public function Add($data, $customer = '')
    {
        $this->load->library('logfunction');
        // Run query to insert blank row
        $this->db->insert('bill_payment', $data);
        // Get id of inserted record
        $id = $this->db->insert_id();
        //log entry of add bill payment
        $data['bill_payment_id'] = $id;
        $this->logfunction->LogRecords('56', $data, isset($customer) ? $customer : "");

        return $id;
    }

    public function Edit($id, $data, $customer = '', $by_id = '')
    {
        if ($by_id == 1) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('bill_guid', $id);
        }

        $result = $this->db->update('bill_payment', $data);
        //log entry of edit bill payment
        $data['bill_payment_id'] = $id;
        $this->logfunction->LogRecords('56', $data, isset($customer) ? $customer : "");
        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    public function Delete($id)
    {
        $this->db->select('bill_transaction_guid');
        $this->db->from('bill_payment');
        $this->db->where('id', $id);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result      = $query->result_array();
            $tx_guid_arr = array_column($result, 'bill_transaction_guid');

            $this->db->where_in('tx_guid', $tx_guid_arr);
            $this->db->delete('splits');

            $this->db->where_in('bill_transaction_guid', $tx_guid_arr);
            $this->db->delete('bill_payment');

            $this->db->where_in('guid', $tx_guid_arr);
            $this->db->delete('transactions');
            return true;
        } else {
            return false;
        }
    }

    public function Delete_by_billguid($id)
    {
        $this->db->where('bill_guid', $id);
        $this->db->delete('bill_payment');
    }

    public function GetWithSplit($bill_guid, $filter_array = array())
    {
        $this->db->select('expenses.guid as bill_id, bill_payment.id as payment_id, bill_payment.bill_guid, phonebook.name_display_as as payee, bill_payment.bill_payment_date, (splits.value_num / splits.value_denom) as payment_amount');
        $this->db->from('bill_payment');
        $this->db->join('expenses', 'bill_payment.bill_guid = expenses.guid');
        $this->db->join('phonebook', 'phonebook.guid = expenses.customer_guid', 'LEFT');
        $this->db->join('splits', 'bill_payment.bill_transaction_guid = splits.tx_guid AND splits.account_guid != bill_payment.bill_credit_acc_guid', 'LEFT');
        $this->db->where('bill_payment.bill_guid', $bill_guid);

        if (isset($filter_array['from_date']) && $filter_array['from_date'] != '') {
            $this->db->where("bill_payment.bill_payment_date >= '" . date('Y-m-d', strtotime($filter_array['from_date'])) . "' AND bill_payment.bill_payment_date <= '" . date('Y-m-d', strtotime($filter_array['to_date'])) . "'");
        }
        $this->db->group_by('bill_payment.id');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result_array();
        } else {
            return array();
        }
    }

    public function GetAllWithBillDetails($company_guid)
    {
        $this->db->select('bill_payment.id, bill_payment.bill_guid, phonebook.name_display_as as payee, bill_payment.bill_payment_date, (splits.value_num / splits.value_denom) as payment_amount');
        $this->db->from('bill_payment');
        $this->db->join('expenses', 'bill_payment.bill_guid = expenses.guid');
        $this->db->join('phonebook', 'phonebook.guid = expenses.customer_guid', 'LEFT');
        $this->db->join('splits', 'bill_payment.bill_transaction_guid = splits.tx_guid AND splits.account_guid != bill_payment.bill_credit_acc_guid', 'LEFT');
        $this->db->where('expenses.company_guid', $company_guid);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return $query->result();
        } else {
            return array();
        }
    }

    public function Get_row($id)
    {
        $this->db->where('id', $id);
        $this->db->select('bill_payment.*, accounts.name as credit_acc, expenses.bill_date, expenses.due_date, expenses.expense_no, expenses.amount, expenses.created_on, expenses.bill_status, splits.value_num, splits.value_denom');
        $this->db->join('splits', 'splits.tx_guid = bill_payment.bill_transaction_guid', 'LEFT');
        $this->db->join('expenses', 'expenses.guid = bill_payment.bill_guid', 'LEFT');
        $this->db->join('accounts', 'accounts.guid = bill_payment.bill_credit_acc_guid', 'LEFT');
        $query = $this->db->get('bill_payment');
        return $query->row();
    }

    public function DeleteAllBillPayment($bill_guid)
    {
        $this->db->select('bill_transaction_guid');
        $this->db->from('bill_payment');
        $this->db->where('bill_guid', $bill_guid);
        $query = $this->db->get();

        if ($query->num_rows() > 0) {
            $result      = $query->result_array();
            $tx_guid_arr = array_column($result, 'bill_transaction_guid');

            $this->db->where_in('tx_guid', $tx_guid_arr);
            $this->db->delete('splits');

            $this->db->where_in('bill_transaction_guid', $tx_guid_arr);
            $this->db->delete('bill_payment');

            $this->db->where_in('guid', $tx_guid_arr);
            $this->db->delete('transactions');
            return true;
        } else {
            return false;
        }
    }

}
