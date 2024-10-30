<?php

class Ledger_statement_model extends CI_Model
{

    public function Get($id = null, $search = array())
    {
        $this->db->select('SQL_CALC_FOUND_ROWS ledger_statement.*', false);
        $this->db->from('ledger_statement');
        // Check if we're getting one row or all records
        if ($id != null) {
            // Getting only ONE row
            $this->db->where('ledger_statement.id', $id);
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
                    'ledger_id'        => '',
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
                if (!empty($search['ledger_id'])) {
                    $this->db->like('ledger_id', $search['ledger_id']);
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
                if (isset($search['is_reconcile'])) {
                    $this->db->where('is_reconcile', $search['is_reconcile']);
                }
                if (isset($search['is_reconciled_current'])) {
                    $this->db->where('is_reconciled_current', $search['is_reconciled_current']);
                }

                //$this->db->order_by('accounts.account_number asc,account_map.lft asc');
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
            $data["countTotal"]    = $this->db->count_all('ledger_statement');
            $data["countFiltered"] = $count->row()->Count;
            return $data;
        }
    }

    public function Add($data)
    {

        $this->db->insert('ledger_statement', $data);
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
        $result = $this->db->update('ledger_statement', $data);
        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    public function Delete($id)
    {
        if (is_array($id)) {
            $this->db->where_in('id', $id);
        } else {
            $this->db->where('id', $id);
        }
        $this->db->delete('ledger_statement');
        return true;
    }

    public function Get_report_data($search = array())
    {
        $is_from_date = $is_to_date = 0;
        $where        = 1;
        $this->db->select("ledger.month, `ledger_statement`.`id`, IF(ledger_statement.transaction_type = 'credit', credit_date, 'N/A') as l_date, ledger_statement.description as ledger_desc, IF(ledger_statement.transaction_type = 'credit', credit_amt, debit_amt) as ledger_amount, `bank_statement_entries`.`id`, bank_statement_entries.date as b_date, bank_statement_entries.description as bank_desc, bank_statement_entries.amount as bank_amount, `ledger_statement`.`bank_statement_id`, IF(`ledger_statement`.`is_reconcile` = 1, 'completed', 'pending') as status, `ledger_statement`.`reconcile_type`, `ledger_statement`.`reconcile_date`,`ledger_statement`.`total_attachment`");
        $this->db->join('ledger', 'ledger.id = ledger_statement.ledger_id', 'INNER');
        $this->db->join('bank_statement_entries', 'FIND_IN_SET(bank_statement_entries.id, ledger_statement.bank_statement_id)', 'LEFT');
        if (isset($search['from_date']) && !empty($search['from_date'])) {
            $from_date_arr = explode("-", $search['from_date']);
            $from_month    = $from_date_arr[0];
            $from_year     = $from_date_arr[1];
            $this->db->where('month >= ', $from_month);
            $this->db->where('year >= ', $from_year);

            $is_from_date = 1;
        }
        if (isset($search['to_date']) && !empty($search['to_date'])) {
            $to_date_arr = explode("-", $search['to_date']);
            $to_month    = $to_date_arr[0];
            $to_year     = $to_date_arr[1];
            $this->db->where('month <= ', $to_month);
            $this->db->where('year <= ', $to_year);
            $is_to_date = 1;
        }
        if (isset($search['is_reconcile']) && !empty($search['is_reconcile'])) {
            $this->db->where('ledger_statement.is_reconcile', $search['is_reconcile'] == 'C' ? 1 : 0);
        }
        if (isset($search['reconcile_type']) && !empty($search['reconcile_type'])) {
            $this->db->where('ledger_statement.reconcile_type', $search['reconcile_type']);
        }
        if (isset($search['ledger_desc']) && !empty($search['ledger_desc'])) {
            $this->db->where('ledger_statement.description', $search['ledger_desc']);
        }
        if (isset($search['bank_desc']) && !empty($search['bank_desc'])) {
            $this->db->where('bank_statement_entries.description', $search['bank_desc']);
        }
        if ($is_from_date == 0 && $is_to_date == 0) {
            $prev_month = date('m', strtotime(date('m') . " -1 month"));
            $prev_year  = date('Y', strtotime(date('m') . " -1 month"));
            $this->db->where('month', $prev_month);
            $this->db->where('year', $prev_year);
        }
        $query = $this->db->get('ledger_statement');
//        echo $this->db->last_query();

        return $query->result();
    }

    public function Get_desc_data()
    {
        $this->db->select('DISTINCT description', false);
        $query = $this->db->get('ledger_statement');
        return $query->result();
    }

    public function get_entries($ids = array())
    {
        if (!empty($ids)) {
            $this->db->where_in('id', $ids);
        }
        $query = $this->db->get('ledger_statement');
        return $query->result();
    }

    public function reset_all_entries($ledgerId, $updata = array(), $bankId)
    {
        $this->db->where('ledger_id', $ledgerId);
        $result = $this->db->update('checkbook_record', $updata);

        $this->db->where('ledger_id', $ledgerId);
        $updata['with_point_diff'] = 0;
        $result                    = $this->db->update('ledger_statement', $updata);

        $sql    = "UPDATE bank_statement_entries SET `with_point_diff`=0,`is_reconcile`= 0,`is_void`= 0,`ledger_statement_id` = '',`reconcile_type` = NULL ,`reconcile_date` = NULL, `is_reconciled_current` = 0 WHERE ledger_statement_id IN (SELECT id FROM `ledger_statement` WHERE ledger_id = {$ledgerId}) OR ledger_statement_id IN (SELECT id FROM `checkbook_record` WHERE ledger_id = {$ledgerId}) OR bank_statement_id = {$bankId}";
        $result = $this->query_result($sql);

        $sql    = "UPDATE ledger_statement SET `with_point_diff`=0,`is_reconcile`= 0,`bank_statement_id` = '',`reconcile_type` = NULL ,`reconcile_date` = NULL, `is_reconciled_current` = 0 WHERE bank_statement_id IN (SELECT id FROM `bank_statement_entries` WHERE bank_statement_id = {$bankId})";
        $result = $this->query_result($sql);

        updateLedgerStatus($ledgerId);
        updateBankStatus($bankId);

        return true;
    }

    public function query_result($sql = false)
    {
        if ($sql) {
            $query = $this->db->query($sql);
            return is_object($query) ? $query->result() : false;
        } else {
            return false;
        }
    }

    public function update_where($bank_statement_id = array(), $data)
    {
        if (!empty($bank_statement_id)) {
            $this->db->where_in('bank_statement_id', $bank_statement_id);
            $result = $this->db->update('ledger_statement', $data);
            return true;
        } else {
            return false;
        }
    }

    public function getLedgerViewData($ledger_id)
    {
        $data['ledger_id'] = $ledger_id;
        $sql               = "(SELECT
                        ls.id as id,
                        lsg.id as db_debit_id,
                        lsi.id as db_impound_id,
                        ls.reconcile_type as reconcile_type,
                        lsg.reconcile_type as debit_reconcile_type,
                        lsi.reconcile_type as impound_reconcile_type,
                        ls.credit_date as credits,
                        ls.description as credit_desc,
                        ls.total_attachment as credit_attachment,
                        ls.credit_amt as credits_amt,
                        lsg.description as debits,
                        lsg.total_attachment as debit_attachment,
                        lsg.debit_amt as debit_amt,
                        lsi.credit_date as we_dates,
                        IF(lsi.transaction_type = 'credit', CONCAT('(',lsi.credit_amt,')'),lsi.debit_amt) as impound_amt,
                        lsi.transaction_type as impound_transaction_type,
                        (CASE WHEN ls.transaction_type = 'credit' THEN ls.is_reconcile ELSE NULL END) as credit_reconcile,
                        (CASE WHEN lsg.transaction_type = 'debit' THEN lsg.is_reconcile ELSE NULL END) as debit_reconcile,
                        (CASE WHEN lsi.document_type != 'general_section' THEN lsi.is_reconcile ELSE NULL END) as document_reconcile,
                        ls.bank_statement_id,
                        lsg.bank_statement_id as debit_bank_statement_id,
                        lsi.bank_statement_id as impound_bank_statement_id,
                        l.month,
                        l.year,
                        lsi.id as impound_id,
                        lsi.description as impound_desc,
                        lsi.total_attachment as impound_attachment
                    FROM
                        ledger_statement ls
                    LEFT JOIN
                        ledger_statement lsg ON lsg.parent_id = ls.id AND lsg.document_type = 'general_section'
                    LEFT JOIN
                        ledger_statement lsi ON lsi.parent_id = ls.id AND lsi.document_type = 'impound'
                    INNER JOIN
                        ledger l ON l.id = ls.ledger_id
                    WHERE
                        ls.`ledger_id` = " . $ledger_id . "
                        AND ls.parent_id = 0
                        AND ls.document_type LIKE 'general_section'
                        AND ls.transaction_type  LIKE 'credit'
                        AND ls.description  LIKE 'DEPOSIT'
                        AND ls.is_manual != 1
                    Order BY ls.id)";
        $data['ledger_data'] = $this->ledger_model->query_result($sql);

        $sql                          = "SELECT * FROM `ledger_statement` WHERE transaction_type = 'credit' AND `document_type` LIKE 'general_section' AND (is_manual = 1 OR (is_manual = 1 AND reconcile_type = 'adjustment')) AND `ledger_id` = " . $ledger_id;
        $data['credit_extra_entries'] = $this->ledger_model->query_result($sql);

        $sql                         = "SELECT * FROM `ledger_statement` WHERE transaction_type = 'debit' AND `document_type` LIKE 'general_section' AND parent_id = 0 AND `ledger_id` = " . $ledger_id;
        $data['debit_extra_entries'] = $this->ledger_model->query_result($sql);

        $sql                = "SELECT *,IF(transaction_type = 'credit', CONCAT('(',credit_amt,')'),debit_amt) as debit_amt FROM `ledger_statement` WHERE `document_type` LIKE 'donut_purchases' AND `ledger_id` = " . $ledger_id;
        $data['donut_data'] = $this->ledger_model->query_result($sql);

        $sql                      = "SELECT * FROM `ledger_statement` WHERE `document_type` LIKE 'payroll_net' AND `ledger_id` = " . $ledger_id;
        $data['payroll_net_data'] = $this->ledger_model->query_result($sql);

        $sql              = "SELECT *, IF(transaction_type = 'credit', CONCAT('(',credit_amt,')'),debit_amt) as debit_amt FROM `ledger_statement` WHERE `document_type` LIKE 'dcp_efts' AND `ledger_id` = " . $ledger_id;
        $data['dcp_data'] = $this->ledger_model->query_result($sql);

        $sql                        = "SELECT * FROM `ledger_statement` WHERE `document_type` LIKE 'payroll_gross' AND `ledger_id` = " . $ledger_id;
        $data['payroll_gross_data'] = $this->ledger_model->query_result($sql);

        $sql = "SELECT *  FROM `ledger_statement`
                WHERE document_type = 'general_section'
                AND transaction_type = 'credit'
                AND description != 'Credit Card Credits'
                AND description != 'DEPOSIT'
                AND `ledger_id` = " . $ledger_id . " AND is_manual != 1";
        $data['credit_extra_entries_from_import'] = $this->ledger_model->query_result($sql);

        $sql              = "SELECT * FROM `ledger_statement` WHERE `document_type` LIKE 'roy_adv' AND `ledger_id` = " . $ledger_id;
        $data['roy_data'] = $this->ledger_model->query_result($sql);

        $sql                = "SELECT *,IF(transaction_type = 'credit', CONCAT('(',credit_amt,')'),debit_amt) as debit_amt FROM `ledger_statement` WHERE `document_type` LIKE 'dean_foods' AND `ledger_id` = " . $ledger_id;
        $data['dean_foods'] = $this->ledger_model->query_result($sql);

        $data['ledger'] = $this->ledger_model->Get($ledger_id);

        $sql = "SELECT `statement_id`,`description` FROM `ledger_statement_splits` WHERE `ledger_id` = " . $ledger_id;

        $data['splits_data'] = $this->ledger_statement_splits_model->query_result($sql);

        $sql                                 = "SELECT * FROM `ledger_credit_received_from` WHERE `ledger_id` = " . $ledger_id;
        $data['ledger_credit_received_from'] = $this->checkbook_model->query_result($sql);

        $sql                   = "SELECT * FROM `ledger_statement` WHERE  `document_type`='impound' AND parent_id = 0 AND `ledger_id` = " . $ledger_id;
        $data['impound_extra'] = $this->checkbook_model->query_result($sql);

        $sql                      = "SELECT * FROM `checkbook_record` WHERE `ledger_id` = " . $ledger_id;
        $data['checkbook_record'] = $this->checkbook_model->query_result($sql);

        $sql = "SELECT GROUP_CONCAT(ls.id) as reconciledids FROM `ledger_statement` ls
                LEFT JOIN `ledger` l ON ls.ledger_id = l.id
                WHERE l.store_key = " . $data['ledger']->store_key . " AND ledger_id != " . $ledger_id . " AND is_reconcile = 1 AND document_type != 'payroll_gross' AND l.month != " . $data['ledger']->month . " ORDER BY ledger_id";
        $data['previous_reconciled_ledger'] = $this->checkbook_model->query_result($sql);

        $sql = "SELECT *
                 FROM `ledger_statement`
                 WHERE ledger_id = " . $ledger_id . "
                 AND `description` LIKE '%Credit Card Credits%' limit 1";
        $data['credit_card_credits'] = $this->ledger_model->query_result($sql);
        return $data;
    }

    public function getAutoLedgerViewData($store_key, $month, $year)
    {
        $sql                = "SELECT * FROM `admin_year_setting` WHERE month = '{$month}' AND `year` =  '{$year}'";
        $admin_year_setting = $this->ledger_model->query_result($sql);
        if (!isset($admin_year_setting[0])) {
            $data['error'] = "Year Setting is not done yet. <a target='_blank' href='" . base_url('settings/yearsetting') . "'>View Settings</a>";
            return $data;
        }
        $weeks = json_decode($admin_year_setting[0]->weeks);

        $weekCondition           = '1=0';
        $weekEndingCondition     = '1=0';
        $billWeekEndingCondition = '1=0';
        foreach ($weeks as $_weeks) {
            $weekCondition .= " OR (start_date = '" . $_weeks->start_of_week . "' AND end_date = '" . $_weeks->end_of_week . "')";
            $weekEndingCondition .= " OR (week_ending_date = '" . $_weeks->end_of_week . "')";
            $billWeekEndingCondition .= " OR (description = '" . $_weeks->end_of_week . "')";
        }

        //General Credit Section
        $sql = "SELECT cdate, actual_bank_deposit
                FROM `monthly_recap` MR
                WHERE MR.`store_key` = '{$store_key}' AND month(`cdate`) = '{$month}' AND year(`cdate`) = '{$year}'";
        $data['credits'] = $this->ledger_model->query_result($sql);

        //fill not found dates
        $creditDates = [];
        foreach ($data['credits'] as $_creditDates) {
            $creditDates[] = date('Y-m-d', strtotime($_creditDates->cdate));
        }
        $totalMonthDays = date("t", mktime(0, 0, 0, $month, 1, $year));
        $totalCredits   = count($data['credits']) - 1;
        for ($i = 1; $i <= $totalMonthDays; $i++) {
            $creditDate = $year . "-" . $month . "-" . $i;
            $creditDate = date('Y-m-d', strtotime($creditDate));
            if (!in_array($creditDate, $creditDates)) {
                $tempObject                       = new stdClass();
                $tempObject->cdate                = $creditDate;
                $tempObject->actual_bank_deposit  = 0;
                $data['credits'][++$totalCredits] = $tempObject;
            }
        }

        $sql = "SELECT
                    SUM(`fed_941_sum`) as fed_941_sum,
                    SUM(`futa_sum`) as futa_sum,
                    SUM(`swt_ga_sum`) as swt_ga_sum,
                    SUM(`sui_ga_sum`) as sui_ga_sum
                  FROM `master_payroll` WHERE `store_key` = '{$store_key}' AND month(`end_date`) = '{$month}' AND year(`end_date`) = '{$year}'";
        $debits = $this->ledger_model->query_result($sql);

        $key                             = -1;
        $data['debits'][++$key]['debit'] = 'Sales Tax (last month)';
        $data['debits'][$key]['amt']     = 0; //TBD

        $data['debits'][++$key]['debit'] = 'Federal Tax 941';
        $data['debits'][$key]['amt']     = $debits[0]->fed_941_sum;

        $data['debits'][++$key]['debit'] = 'Federal Tax 940';
        $data['debits'][$key]['amt']     = $debits[0]->futa_sum;

        $data['debits'][++$key]['debit'] = 'Department of Revenue';
        $data['debits'][$key]['amt']     = $debits[0]->swt_ga_sum;

        $data['debits'][++$key]['debit'] = 'Deptartment Of Labor';
        $data['debits'][$key]['amt']     = $debits[0]->sui_ga_sum;

        //General Debit section
        $sql = "SELECT BI.amount,BC.description as bc_description FROM `bill_item_entries` BI
                    LEFT JOIN `bill` B ON B.id = BI.bill_id
                    LEFT JOIN `bill_category` BC ON BC.id = BI.description
                    WHERE BI.`category_key` = 'debit' AND store_key = '{$store_key}' AND B.month = '{$month}' AND B.year = '{$year}' AND BC.type != 'breakdown_description'";
        $generalDebitData = $this->ledger_model->query_result($sql);
        foreach ($generalDebitData as $_generalDebitData) {
            $data['debits'][++$key]['debit'] = $_generalDebitData->bc_description;
            $data['debits'][$key]['amt']     = $_generalDebitData->amount;
        }

        //Get Breakdown descriptions category - debit
        $sql = "SELECT SUM(BI.amount) as amount ,BC.description as bc_description FROM `bill_item_entries` BI
                    LEFT JOIN `bill` B ON B.id = BI.bill_id
                    LEFT JOIN `bill_category` BC ON BC.id = BI.description
                    WHERE BI.`category_key` = 'debit' AND store_key = '{$store_key}' AND B.month = '{$month}' AND B.year = '{$year}' AND BC.type = 'breakdown_description' GROUP BY BC.id";
        $breakdownDebitData = $this->ledger_model->query_result($sql);
        foreach ($breakdownDebitData as $_breakdownDebitData) {
            $data['debits'][++$key]['debit'] = $_breakdownDebitData->bc_description;
            $data['debits'][$key]['amt']     = $_breakdownDebitData->amount;
        }

        //Extra Credit section
        $sql = "SELECT BI.amount,BC.description as bc_description FROM `bill_item_entries` BI
                    LEFT JOIN `bill` B ON B.id = BI.bill_id
                    LEFT JOIN `bill_category` BC ON BC.id = BI.description
                    WHERE BI.`category_key` = 'credit' AND store_key = '{$store_key}' AND B.month = '{$month}' AND B.year = '{$year}' AND BC.type != 'breakdown_description'";
        $extraCreditData      = $this->ledger_model->query_result($sql);
        $data['extra_credit'] = [];
        $extraCreditTotal     = -1;
        foreach ($extraCreditData as $_extraCreditData) {
            $tempObject                                = new stdClass();
            $tempObject->credit                        = $_extraCreditData->bc_description;
            $tempObject->amount                        = $_extraCreditData->amount;
            $data['extra_credit'][++$extraCreditTotal] = $tempObject;
        }

        //Get Breakdown descriptions category - Credit Card Credits
        $sql = "SELECT (BI.amount) as amount,BC.description as bc_description, BI.breakdown_description FROM `bill_item_entries` BI
                    LEFT JOIN `bill` B ON B.id = BI.bill_id
                    LEFT JOIN `bill_category` BC ON BC.id = BI.description
                    WHERE BI.`category_key` = 'credit' AND store_key = '{$store_key}' AND B.month = '{$month}' AND B.year = '{$year}' AND BC.type = 'breakdown_description'";
        $extraBreakdownCreditData           = $this->ledger_model->query_result($sql);
        $data['all_breakdown_extra_credit'] = [];
        $extraBreakDownCreditTotal          = -1;
        $totalCreditCardAmount              = 0;
        foreach ($extraBreakdownCreditData as $_extraBreakdownCreditData) {
            $tempObject                                                       = new stdClass();
            $tempObject->breakdown_description                                = $_extraBreakdownCreditData->breakdown_description;
            $tempObject->amount                                               = $_extraBreakdownCreditData->amount;
            $data['all_breakdown_extra_credit'][++$extraBreakDownCreditTotal] = $tempObject;
            $totalCreditCardAmount += $_extraBreakdownCreditData->amount;
        }
        $tempObject                        = new stdClass();
        $tempObject->amount                = $totalCreditCardAmount;
        $data['breakdown_extra_credit'][0] = $tempObject;

        //Impound Section
        $sql         = "SELECT `end_date`,`total_tax_recap_sum` FROM `master_payroll` WHERE `store_key` = '{$store_key}' AND ({$weekCondition})";
        $impoundData = $this->ledger_model->query_result($sql);
        $impound     = [];
        foreach ($impoundData as $_impoundData) {
            $impound[date('Y-m-d', strtotime($_impoundData->end_date))] = $_impoundData;
        }
        $data['impounds'] = [];
        $totalImpound     = -1;
        foreach ($weeks as $_weeks) {
            $tempObject                        = new stdClass();
            $tempObject->end_date              = $_weeks->end_of_week;
            $tempObject->amount                = isset($impound[$_weeks->end_of_week]) ? $impound[$_weeks->end_of_week]->total_tax_recap_sum : '';
            $data['impounds'][++$totalImpound] = $tempObject;
        }

        //payroll gross section
        $sql              = "SELECT `end_date`,`gross_wages_sum` FROM `master_payroll` WHERE `store_key` = '{$store_key}' AND ({$weekCondition})";
        $payrollGrossData = $this->ledger_model->query_result($sql);
        $payrollGross     = [];
        foreach ($payrollGrossData as $_payrollGrossData) {
            $payrollGross[date('Y-m-d', strtotime($_payrollGrossData->end_date))] = $_payrollGrossData;
        }
        $data['payroll_gross'] = [];
        $totalPayroll          = -1;
        foreach ($weeks as $_weeks) {
            $tempObject                             = new stdClass();
            $tempObject->end_date                   = $_weeks->end_of_week;
            $tempObject->amount                     = isset($payrollGross[$_weeks->end_of_week]) ? $payrollGross[$_weeks->end_of_week]->gross_wages_sum : '';
            $data['payroll_gross'][++$totalPayroll] = $tempObject;
        }

        //payroll net section
        $sql            = "SELECT `end_date`,`net_sum` FROM `master_payroll` WHERE `store_key` = '{$store_key}' AND ({$weekCondition})";
        $payrollNetData = $this->ledger_model->query_result($sql);
        $payrollNet     = [];
        foreach ($payrollNetData as $_payrollNetData) {
            $payrollNet[date('Y-m-d', strtotime($_payrollNetData->end_date))] = $_payrollNetData;
        }
        $data['payroll_net'] = [];
        $totalPayrollNet     = -1;
        foreach ($weeks as $_weeks) {
            $tempObject                              = new stdClass();
            $tempObject->end_date                    = $_weeks->end_of_week;
            $tempObject->amount                      = isset($payrollNet[$_weeks->end_of_week]) ? $payrollNet[$_weeks->end_of_week]->net_sum : '';
            $data['payroll_net'][++$totalPayrollNet] = $tempObject;
        }

        //Donut section
        $sql       = "SELECT * FROM `bill_item_entries` WHERE `category_key` = 'donut_purchases' AND store_key = '{$store_key}' AND ({$billWeekEndingCondition} OR (description like '%" . $year . "-" . $month . "%')) ";
        $donutData = $this->ledger_model->query_result($sql);
        $donut     = [];
        foreach ($donutData as $_donutData) {
            $donut[date('Y-m-d', strtotime($_donutData->description))] = $_donutData;
        }
        $data['donut'] = [];
        $totalDonut    = -1;
        // echo '<pre>';print_r($weeks);die;

        //Last date of Month
        $monthLastDate  = date("Y-m-t", strtotime($year . '-' . $month . '-01'));
        $monthStartDate = date("Y-m-d", strtotime($year . '-' . $month . '-01'));
        //Logic to add starting days of month from previous month last week
        // if start date of first week is ame as month and not 1 the first week is merged with previous month too ;)
        if ((int) (date('m', strtotime($weeks[0]->start_of_week))) == (int) $month
            && strtotime($monthStartDate) != strtotime($weeks[0]->start_of_week)) {
            $firstActualWeekLastDate      = date('Y-m-d', strtotime('-1 day', strtotime($weeks[0]->start_of_week)));
            $tempObject                   = new stdClass();
            $tempObject->end_date         = $firstActualWeekLastDate;
            $tempObject->amount           = isset($donut[$firstActualWeekLastDate]) ? ($donut[$firstActualWeekLastDate]->amount - $donut[$firstActualWeekLastDate]->last_week_amount) : '';
            $data['donut'][++$totalDonut] = $tempObject;
        }

        foreach ($weeks as $_weeks) {
            $tempObject = new stdClass();

            //Logic to display last month date as last week - weekending date
            if ((int) (date('m', strtotime($_weeks->start_of_week))) == (int) $month &&
                (int) (date('m', strtotime($_weeks->end_of_week))) == (int) $month) {
                $tempObject->end_date = $_weeks->end_of_week;
                $tempObject->amount   = isset($donut[$_weeks->end_of_week]) ? $donut[$_weeks->end_of_week]->amount : '';
            } else if ((int) (date('m', strtotime($_weeks->end_of_week))) != (int) $month) {
                $tempObject->end_date = date("Y-m-t", strtotime($_weeks->start_of_week));
                $tempObject->amount   = isset($donut[$_weeks->end_of_week]) ? $donut[$_weeks->end_of_week]->last_week_amount : '';
            } else if ((int) (date('m', strtotime($_weeks->start_of_week))) != (int) $month) {
                $tempObject->end_date = date("Y-m-d", strtotime($_weeks->end_of_week));
                $tempObject->amount   = isset($donut[$_weeks->end_of_week]) ? ($donut[$_weeks->end_of_week]->amount - $donut[$_weeks->end_of_week]->last_week_amount) : '';
            }
            $data['donut'][++$totalDonut] = $tempObject;
        }

        //Logic to add remaning days of the months which are not covered in weeks
        //last week logic
        if ((int) (date('m', strtotime($weeks[count($weeks) - 1]->end_of_week))) == (int) $month
            && strtotime($weeks[count($weeks) - 1]->end_of_week) != strtotime($monthLastDate)) {
            $tempObject                   = new stdClass();
            $tempObject->end_date         = date("Y-m-t", strtotime($weeks[count($weeks) - 1]->end_of_week));
            $dateInfo                     = getDateInfo($tempObject->end_date);
            $tempObject->amount           = isset($donut[$dateInfo['end_of_week']]) ? ($donut[$dateInfo['end_of_week']]->last_week_amount) : '';
            $data['donut'][++$totalDonut] = $tempObject;
        }

        //DCP section
        $sql     = "SELECT * FROM `bill_item_entries` WHERE `category_key` = 'dcp_efts' AND store_key = '{$store_key}' AND ({$billWeekEndingCondition})";
        $dcpData = $this->ledger_model->query_result($sql);
        $dcp     = [];
        foreach ($dcpData as $_dcpData) {
            $dcp[date('Y-m-d', strtotime($_dcpData->description))] = $_dcpData;
        }
        $data['dcp_data'] = [];
        $totalDcp         = -1;
        foreach ($weeks as $_weeks) {
            $tempObject                    = new stdClass();
            $tempObject->end_date          = $_weeks->end_of_week;
            $tempObject->amount            = isset($dcp[$_weeks->end_of_week]) ? $dcp[$_weeks->end_of_week]->amount : '';
            $data['dcp_data'][++$totalDcp] = $tempObject;
        }

        //Dean Section
        $sql      = "SELECT * FROM `bill_item_entries` WHERE `category_key` = 'dean_foods' AND store_key = '{$store_key}' AND ({$billWeekEndingCondition})";
        $deanData = $this->ledger_model->query_result($sql);
        $dean     = [];
        foreach ($deanData as $_deanData) {
            $dean[date('Y-m-d', strtotime($_deanData->description))] = $_deanData;
        }
        $data['dean'] = [];
        $totalDean    = -1;
        foreach ($weeks as $_weeks) {
            $tempObject                 = new stdClass();
            $tempObject->end_date       = $_weeks->end_of_week;
            $tempObject->amount         = isset($dean[$_weeks->end_of_week]) ? $dean[$_weeks->end_of_week]->amount : '';
            $data['dean'][++$totalDean] = $tempObject;
        }

        //Roy section
        $sql         = "SELECT * FROM `royalty` WHERE `store_key` = '{$store_key}' AND ({$weekEndingCondition})";
        $royaltyData = $this->ledger_model->query_result($sql);
        $royalty     = [];
        foreach ($royaltyData as $_royaltyData) {
            $royalty[date('Y-m-d', strtotime($_royaltyData->week_ending_date))][$_royaltyData->royal_type] = $_royaltyData;
        }
        $data['roy'] = [];
        $totalRoy    = -1;
        foreach ($weeks as $_weeks) {
            $brRoyObject              = new stdClass();
            $brRoyObject->end_date    = $_weeks->end_of_week;
            $brRoyObject->amount      = isset($royalty[$_weeks->end_of_week]['BR']) ? $royalty[$_weeks->end_of_week]['BR']->actual_eft_amt : '';
            $data['roy'][++$totalRoy] = $brRoyObject;

            $dunkinRoyObject           = new stdClass();
            $dunkinRoyObject->end_date = $_weeks->end_of_week;
            $dunkinRoyObject->amount   = isset($royalty[$_weeks->end_of_week]['DD']) ? $royalty[$_weeks->end_of_week]['DD']->actual_eft_amt : '';
            $data['roy'][++$totalRoy]  = $dunkinRoyObject;
        }

        //check Section
        $sql                      = "SELECT * FROM `bill_check_entries` BCE INNER JOIN `bill` b ON b.`id` = BCE.`bill_id` WHERE bc_store_key = '{$store_key}' AND b.month = '{$month}' AND b.year = '{$year}'";
        $checkData                = $this->ledger_model->query_result($sql);
        $data['checkbook_record'] = $checkData;
        $data['ledger_id']        = 0;
        return $data;
    }

    public function getLedgerFormatRecords()
    {
        $sql = "SELECT YS.* FROM `admin_year_setting` YS
                LEFT JOIN ledger L ON L.year = YS.year AND L.month = YS.month
                WHERE L.id is null GROUP BY YS.year,YS.month";
        return $this->ledger_model->query_result($sql);
    }
}
