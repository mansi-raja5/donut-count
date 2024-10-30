<?php

Class Attachment_upload_model extends CI_Model {

    function Get($id = NULL, $search = array()) {
        $this->db->select('SQL_CALC_FOUND_ROWS ledger_attachments.*, ledger_statement.credit_amt, ledger_statement.debit_amt, ledger_statement.transaction_type, ledger_statement.description, ledger.month, ledger.year', FALSE);
        $this->db->join('ledger_statement', 'ledger_statement.id = ledger_attachments.statement_id', 'INNER');
        $this->db->join('ledger', 'ledger.id = ledger_statement.ledger_id', 'INNER');


        $this->db->from('ledger_attachments');
        // Check if we're getting one row or all records
        if ($id != NULL) {
            // Getting only ONE row
            $this->db->where('ledger_attachments.id', $id);
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
                    'statement_id' => '',
                );
                $search = array_merge($defaultSearch, $search);
                if (!empty($search['statement_id'])) {
                    $this->db->where('ledger_attachments.statement_id', $search['statement_id']);
                }


                $this->db->order_by('created_on DESC');
            }
            if (isset($search['start'])) {
                $start = $search['start'];
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
            $count = $this->db->query('SELECT FOUND_ROWS() AS Count');
            $data["countTotal"] = $this->db->count_all('ledger_attachments');
            $data["countFiltered"] = $count->row()->Count;
            return $data;
        }
    }

    function Add($data) {

        $this->db->insert('ledger_attachments', $data);
        // Get id of inserted record
        $guid = $this->db->insert_id();

        return $guid;
    }

    function Edit($id, $data) {
        $this->db->where('id', $id);
        $result = $this->db->update('ledger_attachments', $data);
        // Return
        if ($result) {
            return $id;
        } else {
            return false;
        }
    }

    function Delete($id) {
        $this->db->where('id', $id);
        $this->db->delete('ledger_attachments');
        return true;
    }

    function add_batch_data($data = array()) {
        if (!empty($data)) {
            $this->db->insert_batch('ledger_attachments', $data);
            return true;
        } else {
            return FALSE;
        }
    }

    function month_desc_details($desc_str, $month, $year) {
        $this->db->select('ledger_attachments.*, ledger_statement.transaction_type, ledger_statement.description,ledger_statement.credit_amt, ledger_statement.debit_amt, ledger.month, ledger.year');
        $this->db->where('ledger_statement.description', trim($desc_str));
        $this->db->join('ledger', 'ledger_statement.ledger_id = ledger.id', 'INNER');
        $this->db->join('ledger_attachments', 'ledger_statement.id = ledger_attachments.statement_id', 'LEFT');
        $this->db->where_in('ledger.month', $month);
        $this->db->where('ledger.year', $year);
        $this->db->group_by('ledger_statement.id');
        $query = $this->db->get('ledger_statement');

        return $query->result();
//        $this->db->query('SELECT ledger_attachments.* FROM ledger_statement INNER JOIN ledger ON ledger_statement.ledger_id = ledger.id  LEFT JOIN ledger_attachments ON ledger_statement.id = ledger_attachments.statement_id WHERE description = "'. trim($desc_str).'" AND ledger.month IN('') AND ledger.year = 2019');
    }

    function year_desc_details($desc_str, $month, $year) {
        $this->db->select('ledger_attachments.*, ledger_statement.transaction_type, ledger_statement.description, ledger_statement.credit_amt, ledger_statement.debit_amt, ledger.month, ledger.year');
        $this->db->where('ledger_statement.description', trim($desc_str));
        $this->db->join('ledger', 'ledger_statement.ledger_id = ledger.id', 'INNER');
        $this->db->join('ledger_attachments', 'ledger_statement.id = ledger_attachments.statement_id', 'LEFT');
        $this->db->where('ledger.month', $month);
        $this->db->where_in('ledger.year', $year);
        $query = $this->db->get('ledger_statement');
        return $query->result();
//        $this->db->query('SELECT ledger_attachments.* FROM ledger_statement INNER JOIN ledger ON ledger_statement.ledger_id = ledger.id  LEFT JOIN ledger_attachments ON ledger_statement.id = ledger_attachments.statement_id WHERE description = "'. trim($desc_str).'" AND ledger.month IN('') AND ledger.year = 2019');
    }

    function Get_ledger_attachment($ledger_id) {
        $this->db->select('ledger_statement.description,ledger_attachments.id,ledger_attachments.statement_id, GROUP_CONCAT(ledger_attachments.uploaded_file_name) as uploaded_file_names, GROUP_CONCAT(ledger_attachments.type) as types');
        $this->db->join('ledger_statement', 'ledger_statement.id = ledger_attachments.statement_id', 'INNER');
        $this->db->join('ledger', 'ledger_statement.ledger_id = ledger.id', 'INNER');
        $this->db->where('ledger.id', $ledger_id);
        $this->db->group_by('ledger_attachments.statement_id');
        $query = $this->db->get('ledger_attachments');
        return $query->result();
    }

//------------------------------------------------------------------------
    //DOWNLOAD LEDGER FUNCTION
    function Download_ledger($month,$store_key){
        $data = [];
        $row_data = [];
        $sql = "(SELECT 
                    DATE_FORMAT(ls.credit_date, '%d %M %Y') as credits,
                    ls.credit_amt as credit_dollar_amt,                 
                    lsg.description as debits,
                    lsg.debit_amt as debit_dollar_amt,
                    lsi.debit_amt as impound,
                    DATE_FORMAT(lsi.credit_date, '%m-%d-%y') as we_dates
                FROM
                    ledger_statement ls
                LEFT JOIN
                    ledger_statement lsg ON lsg.parent_id = ls.id AND lsg.document_type = 'general_section'
                LEFT JOIN
                    ledger_statement lsi ON lsi.parent_id = ls.id AND lsi.document_type = 'impound'
                WHERE
                    ls.`ledger_id` IN (select id from ledger where month=".$month." and store_key=".$store_key.")
                    AND ls.parent_id = 0
                    AND ls.document_type LIKE 'general_section'
                    AND ls.transaction_type  LIKE 'credit'
                    AND ls.description  LIKE 'DEPOSIT'
                Order BY ls.id)";

        $data = $this->ledger_model->query_result($sql);
        return $data;
    }
    function ledger_data($month,$store_key){
        $sql = "SELECT
                   * FROM `ledger` WHERE  month=".$month."  and store_key=".$store_key;
        $query = $this->db->query($sql);
        return $query->result_array();
        // $data = $this->ledger_model->query_result($sql);
        // return $data;
    }
    function debit_entry($month,$store_key){
         $sql = "SELECT
                    description as debits,
                    debit_amt as debit_dollar_amt FROM `ledger_statement` WHERE transaction_type = 'debit' AND `document_type` LIKE 'general_section' AND parent_id = 0 AND `ledger_id` in (select id from ledger where month=".$month."  and store_key=".$store_key.")";
        $data = $this->ledger_model->query_result($sql);
        return $data;
    }
    function donut_data($month,$store_key){
         $sql = "SELECT DATE_FORMAT(credit_date, '%d %M') as credits,
                    debit_amt as debit_dollar_amt FROM `ledger_statement` WHERE `document_type` LIKE 'donut_purchases' AND `ledger_id` in (select id from ledger where month=".$month."  and store_key=".$store_key.")";
        $data = $this->ledger_model->query_result($sql);
        return $data;
    }

    function payroll_net_data($month,$store_key){

        $sql = "SELECT DATE_FORMAT(credit_date, '%d %M') as credits,
                debit_amt as debit_dollar_amt FROM `ledger_statement` WHERE `document_type` LIKE 'payroll_net' AND `ledger_id` in (select id from ledger where month=".$month."  and store_key=".$store_key.")";
        $data = $this->ledger_model->query_result($sql);
        return $data;
    }
    function dcp_data($month,$store_key){
        $sql = "SELECT DATE_FORMAT(credit_date, '%d %M') as credits,                
                    debit_amt as debit_dollar_amt  FROM `ledger_statement` WHERE `document_type` LIKE 'dcp_efts' and transaction_type='debit' AND `ledger_id` in (select id from ledger where month=".$month."  and store_key=".$store_key.")";
        $data = $this->ledger_model->query_result($sql);

        return $data;
    }
    function dcp_data_credit($month,$store_key){
        $sql = "SELECT DATE_FORMAT(credit_date, '%d %M') as credits,                
                    credit_amt as credit_amt  FROM `ledger_statement` WHERE `document_type` LIKE 'dcp_efts' and transaction_type='credit' AND `ledger_id` in (select id from ledger where month=".$month."  and store_key=".$store_key.")";
        $data = $this->ledger_model->query_result($sql);

        return $data;
    }
    function payroll_gross_data($month,$store_key){
        $sql = "SELECT DATE_FORMAT(credit_date, '%d %M') as credits,
                    debit_amt as debit_dollar_amt FROM `ledger_statement` WHERE `document_type` LIKE 'payroll_gross' AND `ledger_id` in (select id from ledger where month=".$month."  and store_key=".$store_key.")";
        $data = $this->ledger_model->query_result($sql);
        return $data;
    }
    function roy_data($month,$store_key){
         $sql = "SELECT DATE_FORMAT(credit_date, '%d %M') as credits,
                    debit_amt as debit_dollar_amt FROM `ledger_statement` WHERE `document_type` LIKE 'roy_adv' AND `ledger_id` in (select id from ledger where month=".$month."  and store_key=".$store_key.")";
        $data = $this->ledger_model->query_result($sql);
        return $data;
    }

    function dean_foods($month,$store_key){
        $sql = "SELECT DATE_FORMAT(credit_date, '%d %M') as credits,
                    debit_amt as debit_dollar_amt FROM `ledger_statement` WHERE `document_type` LIKE 'dean_foods' AND `ledger_id` in (select id from ledger where month=".$month."  and store_key=".$store_key.")";
        $data = $this->ledger_model->query_result($sql);
        return $data;
    }
    function credit_entry($month,$store_key){
         $sql = "SELECT  description as debits,
                    credit_amt as credit_dollar_amt  
                FROM `ledger_statement` WHERE transaction_type = 'credit' AND `document_type` LIKE 'general_section' AND credit_date is null AND `ledger_id` in (select id from ledger where month=".$month."  and store_key=".$store_key.")";
        $data = $this->ledger_model->query_result($sql);
        return $data;

    }

    function checkbook_record($month, $store_key){
        $sql = "SELECT payble_to as payable, check_number as check_number, memo as memo,amount1 as amount_due , credit_received_from as creditreceived, amount2 as credit_due FROM `checkbook_record` WHERE `ledger_id` in (select id from ledger where month=".$month.")";
        $data = $this->ledger_model->query_result($sql);
        return $data;
    }
     function ledger_balance($ledger_id){
        $this->db->select('store_key,GROUP_CONCAT(concat_ws(";",
                       ledger.month,
                      ledger.ledger_balance
                      ) ORDER BY ledger.month ASC
            separator "," ) as ledger_balance');
        $this->db->from('ledger ledger');
        $this->db->where('store_key IN (select led.store_key FROM ledger as led where led.id = '.$ledger_id.' )', NULL, FALSE);
        $query = $this->db->get();
        // echo $this->db->last_query();exit;
        return $query->result_array();
    }

}
