<?php

Class Expenses_model extends CI_Model {

    function Get($id = NULL, $search = array()) {
        $this->db->select('SQL_CALC_FOUND_ROWS expenses.*,phonebook.name_display_as as payee, accounts.name,account_stype.is_bank,accounts.guid as aguid, GROUP_CONCAT(splits.reconcile_state) as reconcile_state, phonebook.print_checks_as_displayname, phonebook.print_checks_as_custom', FALSE);
        $this->db->join('phonebook', 'phonebook.guid = expenses.customer_guid', 'LEFT');
        $this->db->join('splits', 'splits.expense_guid = expenses.guid AND splits.value_num < 0', 'LEFT');
        $this->db->join('accounts', 'accounts.guid = splits.account_guid', 'LEFT');
        $this->db->join('account_stype', 'account_stype.guid = accounts.account_type_guid', 'LEFT');
        //$this->db->join('bill_payment', 'expenses.guid = bill_payment.bill_guid', 'LEFT');
        //$this->db->join('splits', 'bill_payment.bill_transaction_guid = splits.tx_guid AND splits.account_guid != bill_payment.bill_credit_acc_guid', 'LEFT');
        $this->db->from('expenses');
        // Check if we're getting one row or all records
        if ($id != NULL) {
            // Getting only ONE row
            $this->db->where('expenses.guid', $id);
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
            $this->db->group_by('expenses.guid');
            // Get all
            if (!empty($search)) {
                $defaultSearch = array(
                    'account_guid' => '',
                    'payment_date' => '',
                    'payment_method' => '',
                    'expense_type' => '',
                    'company_guid' => '',
                    'customer_guid' => '',
                    'expense_no' => '',
                    'project_guid' => '',
                    'from_date' => '',
                    'created_date_from' => '',
                    'created_date_to' => '',
                    'expense_guid' => '',
                    'term' => '',
                );
                $search = array_merge($defaultSearch, $search);
                if ($search['expense_no'] != '') {
                    $this->db->where('expense_no', $search['expense_no']);
                }
                if ($search['expense_guid'] != '') {
                    $this->db->where('expense_guid != ', $search['expense_guid']);
                }
                if ($search['account_guid'] != '') {
                    $this->db->where('splits.account_guid', $search['account_guid']);
                }
                if ($search['payment_date'] != '') {
                    $this->db->where('payment_date', date("Y-m-d", strtotime($search['payment_date'])));
                }
                if ($search['payment_method'] != '') {
                    $this->db->where('payment_method', $search['payment_method']);
                }
                if ($search['company_guid'] != '') {
                    $this->db->where('expenses.company_guid', $search['company_guid']);
                }
                if ($search['customer_guid'] != '') {
                    $this->db->where('expenses.customer_guid', $search['customer_guid']);
                }
                if ($search['project_guid'] != '') {
                    $this->db->where('expenses.project_guid', $search['project_guid']);
                }
                if ($search['expense_type'] != '') {
                    if ($search['expense_type'] != 'All' && $search['expense_type'] != 'Bill Payment') {
                        $this->db->where('expenses.expense_type', $search['expense_type']);
                    }

                    if (isset($search['logs_entry']) && $search['logs_entry']) {
                        if ($search['expense_type'] == 'Expense' || $search['expense_type'] == 'Check') {
                            $this->db->order_by('expenses.payment_date', 'DESC');
                        } else if ($search['expense_type'] == 'Credit' || $search['expense_type'] == 'Bill') {
                            $this->db->order_by('expenses.bill_date', 'DESC');
                        }
                    }
                }
                if ($search['from_date'] != '') {
                    $this->db->group_start();
                    $this->db->where("CASE WHEN expenses.expense_type = 'Expense' THEN expenses.payment_date >='" . date('Y-m-d', strtotime($search['from_date'])) . "' AND expenses.payment_date <='" . date('Y-m-d', strtotime($search['to_date'])) . "' END", NULL, false);
                    $this->db->or_where("CASE WHEN expenses.expense_type = 'Check' THEN expenses.payment_date >='" . date('Y-m-d', strtotime($search['from_date'])) . "' AND expenses.payment_date <='" . date('Y-m-d', strtotime($search['to_date'])) . "' END", NULL, false);
                    $this->db->or_where("CASE WHEN expenses.expense_type = 'Credit' THEN expenses.bill_date >='" . date('Y-m-d', strtotime($search['from_date'])) . "' AND expenses.bill_date <='" . date('Y-m-d', strtotime($search['to_date'])) . "' END", NULL, false);
                    $this->db->or_where("CASE WHEN expenses.expense_type = 'Bill' THEN expenses.bill_date >='" . date('Y-m-d', strtotime($search['from_date'])) . "' AND expenses.bill_date <='" . date('Y-m-d', strtotime($search['to_date'])) . "' END", NULL, false);
                    $this->db->group_end();
                }
                if (isset($search['order'])) {
                    $order = $search['order'];
                    if ($order[0]['column'] == 0) {
                        $orderby = "payment_date " . strtoupper($order[0]['dir']);
                    }
                    if ($order[0]['column'] == 1) {
                        $orderby = "expense_type " . strtoupper($order[0]['dir']);
                    }
                    if ($order[0]['column'] == 2) {
                        $orderby = "expense_no " . strtoupper($order[0]['dir']);
                    }
                    if ($order[0]['column'] == 3) {
                        $orderby = "payee " . strtoupper($order[0]['dir']);
                    }
                    if ($order[0]['column'] == 4) {
                        $orderby = "amount " . strtoupper($order[0]['dir']);
                    }
                    $this->db->order_by($orderby);
                }
                if (isset($search['start'])) {
                    $start = $search['start'];
                    $length = $search['length'];
                    if ($length != -1) {
                        $this->db->limit($length, $start);
                    }
                }
                if (!empty($search['created_date_from'])) {
                    $this->db->where('DATE(expenses.created_on) >=', date('Y-m-d', strtotime($search['created_date_from'])));
                }
                if (!empty($search['created_date_to'])) {
                    $this->db->where('DATE(expenses.created_on) <=', date('Y-m-d', strtotime($search['created_date_to'])));
                }
                if (!empty($search['term'])) {
                    $this->db->group_start();
                    $this->db->like('phonebook.name_display_as', $search['term']);
                    $this->db->or_like('expenses.expense_no', $search['term']);
                    $this->db->group_end();
                } 
                $query = $this->db->get();

                $data["records"] = array();
                if ($query->num_rows() > 0) {
                    $data["records"] = $query->result();
                }
                $count = $this->db->query('SELECT FOUND_ROWS() AS Count');
                $data["countTotal"] = $this->db->where('company_guid', $search['company_guid'])->from("expenses")->count_all_results();
                $data["countFiltered"] = $count->row()->Count;
                return $data;
            } else {
                $data["records"] = array();
                $data["countTotal"] = 0;
                $data["countFiltered"] = 0;
                return $data;
            }
        }
    }

    function Add($data) {
        $this->load->library('logfunction');
        // Run query to insert blank row

        $this->db->insert('expenses', $data);
        // Get id of inserted record
        $id = $this->db->insert_id();
        if (isset($data['expense_type']) && $data['expense_type'] == 'Expense') {
            $this->logfunction->LogRecords('47', $data, isset($data['customer_guid']) ? $data['customer_guid'] : "");
        } else if (isset($data['expense_type']) && $data['expense_type'] == 'Bill') {
            $this->logfunction->LogRecords('50', $data, isset($data['customer_guid']) ? $data['customer_guid'] : "");
        } else if (isset($data['expense_type']) && $data['expense_type'] == 'Credit') {
            $this->logfunction->LogRecords('52', $data, isset($data['customer_guid']) ? $data['customer_guid'] : "");
        } else if (isset($data['expense_type']) && $data['expense_type'] == 'Check') {
            $this->logfunction->LogRecords('54', $data, isset($data['customer_guid']) ? $data['customer_guid'] : "");
        }
        return $id;
    }

    function Edit($id, $data) {
        $this->load->library('logfunction');
        $this->db->where('guid', $id);
        $result = $this->db->update('expenses', $data);
        if (isset($data['expense_type']) && $data['expense_type'] == 'Expense') {
            $data['guid'] = $id;
            $this->logfunction->LogRecords('48', $data, isset($data['customer_guid']) ? $data['customer_guid'] : "");
        } else if (isset($data['expense_type']) && $data['expense_type'] == 'Bill') {
            $data['guid'] = $id;
            $this->logfunction->LogRecords('51', $data, isset($data['customer_guid']) ? $data['customer_guid'] : "");
        } else if (isset($data['expense_type']) && $data['expense_type'] == 'Credit') {
            $data['guid'] = $id;
            $this->logfunction->LogRecords('53', $data, isset($data['customer_guid']) ? $data['customer_guid'] : "");
        } else if (isset($data['expense_type']) && $data['expense_type'] == 'Check') {
            $data['guid'] = $id;
            $this->logfunction->LogRecords('55', $data, isset($data['customer_guid']) ? $data['customer_guid'] : "");
        }
        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    function Delete($id) {
        $this->db->where('guid', $id);
        $this->db->delete('expenses');
    }

    function get_customer_last_account($where = array(), $company_guid) {
        if (!empty($where)) {
            $this->db->where($where);
        }
        if(isset($where['expense_type']) && $where['expense_type'] == 'Credit'){
            $this->db->where('splits.value_num > ', 0);
        }else{
            $this->db->where('splits.value_num < ', 0);
        }
        $this->db->select('expenses.*, splits.account_guid');
//        SELECT * FROM `splits` INNER JOIN expenses on splits.expense_guid = expenses.guid WHERE expenses.customer_guid = 'ff00732d4dbe54b1d534e8d9c3df4ac2' and expenses.expense_type = 'expense' and splits.value_num < 0
        $this->db->join('expenses', 'expenses.guid = splits.expense_guid', "INNER");
        
        $this->db->where('expenses.company_guid', $company_guid);
        $this->db->order_by('expenses.guid', 'DESC');
        $query = $this->db->get('splits');
        return $query->row();
    }
    
    function check_no_exist($check_no, $account_guid, $company_guid) {
        $this->db->select('max(expense_no) as expense_no');
        $this->db->join('splits', 'splits.expense_guid = expenses.guid', "LEFT");
        $this->db->join('account_map', 'account_map.guid = splits.account_guid', "LEFT");
        $this->db->where("expense_no >= ", $check_no);
        $this->db->where("expense_type", "Check");
        $this->db->where("splits.account_guid", $account_guid);
        $this->db->where("account_map.company_guid", $company_guid);
        $query = $this->db->get('expenses');
        return $query->row();
    }

    public function GetAllByDates($start_date, $end_date, $company_guids) {
        $this->db->select("expenses.*");
        $this->db->where('DATE(expenses.created_on) >=', date('Y-m-d', strtotime($start_date)));
        $this->db->where('DATE(expenses.created_on) <=', date('Y-m-d', strtotime($end_date)));
        $this->db->where_in('expenses.company_guid', $company_guids);
        $query = $this->db->get('expenses');
        $res = $query->result_array();
        return $res;
    }

}
