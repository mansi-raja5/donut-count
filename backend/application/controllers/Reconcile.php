<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Reconcile extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('bank_statement_model');
        $this->load->model('ledger_model');
        $this->load->model('ledger_statement_model');
        $this->load->model('bank_statement_entries_model');
        $this->load->model('store_master_model');
        $this->load->model('reconcile_model');
        $this->load->model('ledger_statement_splits_model');
        $this->load->model('checkbook_model');
    }

    public function process()
    {
        try {
            $ignoredDescBeingReconciled = ['Federal Tax 940','Federal Tax 941','Department of Revenue','Deptartment Of Labor','City Employee Taxes','Certipay Payroll Services'];
            $data['ledger_id'] = $ledger_id = $this->input->get('ledger_id');
            $data['bank_id']   = $bank_id   = $this->input->get('bank_id');
            $data['ledger']    = $this->ledger_model->Get($ledger_id);
            if(!$data['ledger'])
            {
                $this->session->set_flashdata('display-message','Ledger Entry not found!');
                redirect('reconcile');
            }
            $data['bank']      = $this->bank_statement_model->Get($bank_id);
            if(!$data['bank'])
            {
                $this->session->set_flashdata('display-message','Bank Entry not found!');
                redirect('reconcile');
            }

            $data['is_locked'] = $this->_getIsLocked($data['ledger']);

            $sql = "SELECT count(*) as total,
                    SUM(IF(`transaction_type` = 'credit',1,0)) as total_credit,
                    SUM(IF(`transaction_type` = 'debit',1,0)) as total_debit,
                    SUM(IF(`is_reconcile` = 1 ,1,0)) as total_reconciled,
                    SUM(IF(`transaction_type` = 'credit'  AND `is_reconcile` = 1 ,1,0)) as total_reconciled_credit,
                    SUM(IF(`transaction_type` = 'debit' AND `is_reconcile` = 1 ,1,0)) as total_reconciled_debit,
                    ROUND(SUM(IF(`transaction_type` = 'credit',`credit_amt`,0)),2) as total_credit_amt,
                    ROUND(SUM(IF(`transaction_type` = 'debit',`debit_amt`,0)),2) as total_debit_amt,
                    ROUND(SUM(IF(`transaction_type` = 'credit'  AND `is_reconcile` = 1 ,`credit_amt`,0)),2) as total_reconciled_credit_amt,
                    ROUND(SUM(IF(`transaction_type` = 'debit' AND `is_reconcile` = 1 ,`debit_amt`,0)),2) as total_reconciled_debit_amt,
                    l.status
            FROM `ledger_statement` ls
            LEFT JOIN `ledger` l ON ls.ledger_id = l.id
            WHERE ls.ledger_id = " . $ledger_id ." AND document_type != 'payroll_gross' AND ls.description NOT IN ('".implode("','", $ignoredDescBeingReconciled)."')";
            $data['leder_info'] = $this->ledger_model->query_result($sql);

            $sql = "SELECT count(*) as total FROM `ledger_statement` ls
            WHERE ls.ledger_id = " . $ledger_id ." AND (document_type = 'payroll_gross' OR ls.description IN ('".implode("','", $ignoredDescBeingReconciled)."'))";
            $data['ignored_ledger'] = $this->ledger_model->query_result($sql);

            $sql = "SELECT count(*) as total,
                        SUM(IF(`is_reconcile` = 1 ,1,0)) as total_check_debit,
                        ROUND(SUM(`amount1`),2) as total_check_amt,
                        ROUND(SUM(IF(`is_reconcile` = 1 ,`amount1`,0)),2) as total_check_reconciled_amt
                        FROM `checkbook_record`
                        WHERE ledger_id = " . $ledger_id;
            $data['check_info'] = $this->ledger_model->query_result($sql);

            $sql = "SELECT
                count(*) as total,
                SUM(IF(`transaction_type` = 'credit',1,0)) as total_credit,
                SUM(IF(`transaction_type` = 'debit',1,0)) as total_debit,
                SUM(IF(`is_reconcile` = 1 ,1,0)) as total_reconciled,
                SUM(IF(`transaction_type` = 'credit'  AND `is_reconcile` = 1 ,1,0)) as total_reconciled_credit,
                SUM(IF(`transaction_type` = 'debit' AND `is_reconcile` = 1 ,1,0)) as total_reconciled_debit,
                ROUND(SUM(IF(`transaction_type` = 'credit',`amount`,0)),2) as total_credit_amt,
                ROUND(SUM(IF(`transaction_type` = 'debit',`amount`,0)),2) as total_debit_amt,
                ROUND(SUM(IF(`transaction_type` = 'credit'  AND `is_reconcile` = 1 ,`amount`,0)),2) as total_reconciled_credit_amt,
                ROUND(SUM(IF(`transaction_type` = 'debit' AND `is_reconcile` = 1 ,`amount`,0)),2) as total_reconciled_debit_amt,
            b.status FROM `bank_statement_entries` bs
            LEFT JOIN `bank_statement` b ON b.id = bs.bank_statement_id
            WHERE bank_statement_id = " . $bank_id;
            $data['bank_info'] = $this->ledger_model->query_result($sql);

            //baki
            $sql = "SELECT * FROM `ledger_credit_received_from` WHERE `ledger_id` = " . $ledger_id;
            $data['ledger_credit_received_from'] = $this->checkbook_model->query_result($sql);

            //for adjustment
            $sql = "SELECT ls.*,l.month,l.year FROM `ledger_statement` ls
                    INNER JOIN `ledger` l ON ls.ledger_id = l.id
                    INNER JOIN `bank_statement` b ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                    WHERE l.store_key = " . $data['ledger']->store_key . " AND is_reconcile = 0 AND l.month <= " . $data['ledger']->month . " AND document_type != 'payroll_gross' AND ls.description NOT IN ('".implode("','", $ignoredDescBeingReconciled)."')";
            $data['all_unreconciled_ledger'] = $this->checkbook_model->query_result($sql);

            $data['title'] = $data['ledger']->store_key . " - " . monthName($data['ledger']->month) . " - " . $data['ledger']->year;
            $this->template->load('listing', 'reconcile', $data);
        } catch (Exception $e) {
            //alert the user then kill the process
            og_message('error', $e->getMessage());
            return;
        }
    }

    protected function _getPreviousReconciledBankIds($ledgerData, $currentBankId)
    {
        $sql = "SELECT GROUP_CONCAT(bs.id) as reconciledids FROM `bank_statement_entries` bs
                INNER JOIN `bank_statement` b ON b.id = bs.bank_statement_id
                INNER JOIN `ledger` l ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                WHERE b.store_key = " . $ledgerData->store_key . " AND bank_statement_id != " . $currentBankId . " AND is_reconcile = 1 AND b.month < " . $ledgerData->month . " ORDER BY bank_statement_id";
        $GLOBALS['previous_reconciled_bank'] = $this->checkbook_model->query_result($sql);

        $sql = "SELECT GROUP_CONCAT(bs.id) as reconciledids FROM `bank_statement_entries` bs
                INNER JOIN `bank_statement` b ON b.id = bs.bank_statement_id
                INNER JOIN `ledger` l ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                WHERE b.store_key = " . $ledgerData->store_key . " AND bank_statement_id != " . $currentBankId . " AND is_reconcile = 1 AND b.month > " . $ledgerData->month . " ORDER BY bank_statement_id";
        $GLOBALS['next_reconciled_bank'] = $this->checkbook_model->query_result($sql);

        $sql = "SELECT GROUP_CONCAT(ls.id) as reconciledids FROM `ledger_statement` ls
                        INNER JOIN `ledger` l ON ls.ledger_id = l.id
                        WHERE `reconcile_type` = 'adjustment' AND l.month > " . $ledgerData->month;
        $GLOBALS['next_adjustment_ledger'] = $this->checkbook_model->query_result($sql);
    }

    public function getLedgerData()
    {
        $data['is_previous']    = 0;
        $data['title']          = "LEDGER STATEMENT";
        $data['table_id']       = "current_ledger_entries";
        $data['ledger_id']      = $this->input->post('ledger_id');
        $data['bank_id']        = $this->input->post('bank_id');
        $data['ledger']         = $this->ledger_model->Get($data['ledger_id']);
        $data['ledger_data']    = $this->ledger_statement_model->Get(null, array("ledger_id" => $data['ledger_id']));

        $ledger_reconciled_data = $this->ledger_statement_model->Get(null, array("ledger_id" => $data['ledger_id'],'is_reconcile'=>1));
        $data['ledger_reconciled_count'] = $ledger_reconciled_data['countFiltered'];

        $this->_getPreviousReconciledBankIds($data['ledger'],$data['bank_id']);
        $data['is_locked'] = $this->_getIsLocked($data['ledger']);

        $sql                   = "SELECT * FROM `ledger_document`";
        $data['document_data'] = $this->checkbook_model->query_result($sql);
        echo $this->load->view('reconcile/ledger_data.php', $data, true);
    }

    public function showLedgerView()
    {
        $ledger_id      = $this->input->post('ledger_id');
        $bank_id      = $this->input->post('bank_id');
        $data = $this->ledger_statement_model->getLedgerViewData($ledger_id);
        $data['title'] = "LEDGER VIEW";
        $data['call_from'] = "reconcile_".$ledger_id."_".$bank_id;
        echo $this->load->view('ledger/view_statement', $data, true);
        exit();
    }

    public function getPreviousLedgerData()
    {
        $data['is_previous']    = 1;
        $data['title']          = "PREVIOUS LEDGER STATEMENT";
        $data['table_id']       = "previous_ledger_entries";
        $data['ledger_id']      = $this->input->post('ledger_id');
        $data['bank_id']        = $this->input->post('bank_id');
        $data['ledger']         = $this->ledger_model->Get($data['ledger_id']);

        $ignoredDescBeingReconciled = ['Federal Tax 940','Federal Tax 941','Department of Revenue','Deptartment Of Labor','City Employee Taxes','Certipay Payroll Services'];

        $sql = "SELECT ls.*,l.month,l.year FROM `ledger_statement` ls
                INNER JOIN `ledger` l ON ls.ledger_id = l.id
                INNER JOIN `bank_statement` b ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                WHERE l.store_key = " . $data['ledger']->store_key . " AND is_reconcile = 1 AND document_type != 'payroll_gross' AND l.month < " . $data['ledger']->month . " AND ( is_reconciled_current = 2 )";

        $previous_reconciled_ledger_data = $this->checkbook_model->query_result($sql);

        $sql = "SELECT ls.*,l.month,l.year FROM `ledger_statement` ls
                    INNER JOIN `ledger` l ON ls.ledger_id = l.id
                    INNER JOIN `bank_statement` b ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                    WHERE l.store_key = " . $data['ledger']->store_key . " AND ledger_id != " . $data['ledger_id'] . " AND is_reconcile = 0 AND document_type != 'payroll_gross' AND l.month < " . $data['ledger']->month . " AND ls.description NOT IN ('".implode("','", $ignoredDescBeingReconciled)."') ORDER BY ledger_id";

        $previous_unreconciled_ledger_data = $this->checkbook_model->query_result($sql);

        $data['ledger_data']['records'] = array_merge($previous_reconciled_ledger_data,$previous_unreconciled_ledger_data);

        $data['ledger_reconciled_count'] = count($previous_reconciled_ledger_data);

        $data['is_locked'] = $this->_getIsLocked($data['ledger']);

        $sql                   = "SELECT * FROM `ledger_document`";
        $data['document_data'] = $this->checkbook_model->query_result($sql);
        echo $this->load->view('reconcile/ledger_data.php', $data, true);
    }

    public function getCreditReceivedFromData(){
        $data['is_previous']    = 0;
        $data['title']          = "CREDIT RECEIVED FROM";
        $data['table_id']       = "ledger_credit_received_from";
        $data['ledger_id']      = $this->input->post('ledger_id');
        $data['bank_id']        = $this->input->post('bank_id');
        $data['ledger']         = $this->ledger_model->Get($data['ledger_id']);

        $sql = "SELECT * FROM `ledger_credit_received_from` WHERE `ledger_id` = " . $data['ledger_id'];
        $data['credit_received_record'] = $this->checkbook_model->query_result($sql);

        echo $this->load->view('reconcile/credit_received_form_data.php', $data, true);
    }

    public function getCheckData()
    {
        $data['is_previous']    = 0;
        $data['title']          = "CHECK BOOK RECORDS";
        $data['table_id']       = "current_check_entries";
        $data['ledger_id']      = $this->input->post('ledger_id');
        $data['bank_id']        = $this->input->post('bank_id');
        $data['ledger']         = $this->ledger_model->Get($data['ledger_id']);

        $sql = "SELECT * FROM `checkbook_record` WHERE `ledger_id` = " . $data['ledger_id'];
        $data['checkbook_record'] = $this->checkbook_model->query_result($sql);

        $sql = "SELECT count(*) as checkbook_reconcile_count FROM `checkbook_record` WHERE `ledger_id` = " . $data['ledger_id']." AND `is_reconcile` = 1";
        $checkbook_reconcile_count = $this->checkbook_model->query_result($sql);
        $data['checkbook_reconcile_count'] = $checkbook_reconcile_count[0]->checkbook_reconcile_count;

        $this->_getPreviousReconciledBankIds($data['ledger'],$data['bank_id']);
        $data['is_locked'] = $this->_getIsLocked($data['ledger']);

        $sql                   = "SELECT * FROM `ledger_document`";
        $data['document_data'] = $this->checkbook_model->query_result($sql);
        echo $this->load->view('reconcile/checkbook_data.php', $data, true);
    }

    public function getPreviousCheckData()
    {
        $data['is_previous']    = 1;
        $data['title']          = "PREVIOUS CHECK BOOK RECORDS";
        $data['table_id']       = "previous_check_entries";
        $data['ledger_id']      = $this->input->post('ledger_id');
        $data['bank_id']        = $this->input->post('bank_id');
        $data['ledger']         = $this->ledger_model->Get($data['ledger_id']);

        $sql = "SELECT cr.* FROM `checkbook_record` cr
                    INNER JOIN `ledger` l ON cr.ledger_id = l.id
                    INNER JOIN `bank_statement` b ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                    WHERE l.store_key = " . $data['ledger']->store_key . " AND `ledger_id` != " . $data['ledger_id'] . " AND is_reconcile = 1 AND l.month < " . $data['ledger']->month."
                        AND cr.id IN (SELECT `ledger_statement_id` FROM `bank_statement_entries` bs INNER JOIN `bank_statement` b ON b.id = bs.bank_statement_id WHERE bs.transaction = 'check' AND b.store_key = " . $data['ledger']->store_key . " AND is_reconcile = 1 AND bank_statement_id = " . $data['bank_id'] . ")";

        $previous_reconciled_checkbook_record = $this->checkbook_model->query_result($sql);

        $sql = "SELECT cr.* FROM `checkbook_record` cr
                    INNER JOIN `ledger` l ON cr.ledger_id = l.id
                    INNER JOIN `bank_statement` b ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                    WHERE l.store_key = " . $data['ledger']->store_key . " AND `ledger_id` != " . $data['ledger_id'] . " AND is_reconcile = 0 AND l.month < " . $data['ledger']->month;

        $previous_unreconciled_checkbook_record = $this->checkbook_model->query_result($sql);

        $data['checkbook_record'] = array_merge($previous_reconciled_checkbook_record, $previous_unreconciled_checkbook_record);

        $data['checkbook_reconcile_count'] = count($previous_reconciled_checkbook_record);

        $data['is_locked'] = $this->_getIsLocked($data['ledger']);
        echo $this->load->view('reconcile/checkbook_data.php', $data, true);
    }

    public function getBankData()
    {
        $data['is_previous']    = 0;
        $data['title']          = "BANK STATEMENT";
        $data['table_id']       = "current_bank_entries";
        $data['ledger_id']      = $this->input->post('ledger_id');
        $data['bank_id']        = $this->input->post('bank_id');
        $data['ledger']         = $this->ledger_model->Get($data['ledger_id']);

        $data['statement_data'] = $this->bank_statement_entries_model->Get(null, array("bank_statement_id" => $data['bank_id']));

        $bank_reconciled_count = $this->bank_statement_entries_model->Get(null, array("bank_statement_id" => $data['bank_id'],'is_reconcile'=>1));
        $data['bank_reconciled_count'] = $bank_reconciled_count['countFiltered'];

        $sql = "SELECT GROUP_CONCAT(ls.id) as reconciledids FROM `ledger_statement` ls
                INNER JOIN `ledger` l ON ls.ledger_id = l.id
                INNER JOIN `bank_statement` b ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                WHERE l.store_key = " . $data['ledger']->store_key . " AND ledger_id != " . $data['ledger_id'] . " AND is_reconcile = 1 AND document_type != 'payroll_gross' AND l.month < " . $data['ledger']->month . " ORDER BY ledger_id";
        $GLOBALS['previous_reconciled_ledger'] = $this->checkbook_model->query_result($sql);

        $sql = "SELECT GROUP_CONCAT(ls.id) as reconciledids FROM `ledger_statement` ls
                INNER JOIN `ledger` l ON ls.ledger_id = l.id
                INNER JOIN `bank_statement` b ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                WHERE l.store_key = " . $data['ledger']->store_key . " AND ledger_id != " . $data['ledger_id'] . " AND is_reconcile = 1 AND document_type != 'payroll_gross' AND l.month > " . $data['ledger']->month . " ORDER BY ledger_id";
        $GLOBALS['next_reconciled_ledger'] = $this->checkbook_model->query_result($sql);

        $sql = "SELECT GROUP_CONCAT(check_number) as reconciled_checks FROM `checkbook_record` cr
                    INNER JOIN `ledger` l ON cr.ledger_id = l.id
                    INNER JOIN `bank_statement` b ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                    WHERE l.store_key = " . $data['ledger']->store_key . " AND `ledger_id` != " . $data['ledger_id'] . " AND is_reconcile = 1 AND l.month < " . $data['ledger']->month;
        $GLOBALS['previous_reconciled_checkbook_numbers'] = $this->checkbook_model->query_result($sql);

        $sql = "SELECT GROUP_CONCAT(check_number) as reconciled_checks FROM `checkbook_record` cr
                    INNER JOIN `ledger` l ON cr.ledger_id = l.id
                    INNER JOIN `bank_statement` b ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                    WHERE l.store_key = " . $data['ledger']->store_key . " AND `ledger_id` != " . $data['ledger_id'] . " AND is_reconcile = 1 AND l.month > " . $data['ledger']->month;
        $GLOBALS['next_reconciled_checkbook_numbers'] = $this->checkbook_model->query_result($sql);

        $data['is_locked'] = $this->_getIsLocked($data['ledger']);
        echo $this->load->view('reconcile/bank_data.php', $data, true);
    }

    public function getPreviousBankData()
    {
        $data['is_previous']    = 1;
        $data['title']          = "PREVIOUS BANK STATEMENT";
        $data['table_id']       = "previous_bank_entries";
        $data['ledger_id']      = $this->input->post('ledger_id');
        $data['bank_id']        = $this->input->post('bank_id');
        $data['ledger']         = $this->ledger_model->Get($data['ledger_id']);

        $sql = "SELECT bs.*,b.month,b.year FROM `bank_statement_entries` bs
                INNER JOIN `bank_statement` b ON b.id = bs.bank_statement_id
                INNER JOIN `ledger` l ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                WHERE b.store_key = " . $data['ledger']->store_key . " AND is_reconcile = 1 AND b.month < " . $data['ledger']->month . "
                AND bs.id IN (SELECT `bank_statement_id` FROM `ledger_statement` ls INNER JOIN `ledger` l ON ls.ledger_id = l.id WHERE l.store_key = " . $data['ledger']->store_key . " AND ledger_id = " . $data['ledger_id'] . " AND is_reconcile = 1 AND document_type != 'payroll_gross' ORDER BY ledger_id)";
        $previous_reconciled_bank_data = $this->checkbook_model->query_result($sql);

        $sql = "SELECT bs.*,b.month,b.year FROM `bank_statement_entries` bs
                INNER JOIN `bank_statement` b ON b.id = bs.bank_statement_id
                INNER JOIN `ledger` l ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                WHERE b.store_key = " . $data['ledger']->store_key . " AND bank_statement_id != " . $data['bank_id'] . " AND is_reconcile = 0 AND b.month < " . $data['ledger']->month . " ORDER BY bank_statement_id";
        $previous_unreconciled_bank_data = $this->checkbook_model->query_result($sql);

        $data['statement_data']['records'] = array_merge($previous_reconciled_bank_data,$previous_unreconciled_bank_data);

        $data['bank_reconciled_count'] = count($previous_reconciled_bank_data);

        $data['is_locked'] = $this->_getIsLocked($data['ledger']);
        echo $this->load->view('reconcile/bank_data.php', $data, true);
    }

    protected function _getIsLocked($ledgerData)
    {
        $sql       = "SELECT is_locked as islocked FROM ledger WHERE id = " . $ledgerData->id;
        $is_locked = $this->checkbook_model->query_result($sql);

        $sql = "SELECT if(l.status = 'unreconcile' AND b.status = 'unreconcile',1,0) as islocked
                    FROM ledger l
                    INNER JOIN `bank_statement` b ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                    WHERE l.month < " . $ledgerData->month;
        $is_locked_status = $this->checkbook_model->query_result($sql);

        $is_locked[0]->islocked = ($is_locked && $is_locked[0]->islocked) || ($is_locked_status && $is_locked_status[0]->islocked) ? 1 :0;

        return $is_locked;
    }

    public function check_document_type()
    {
        try {
            $postData                = $this->input->post();
            $selectedDocumentType    = trim($postData['selected_document_type']);
            $selectedTransactionType = trim($postData['transaction_type']);
            $sql                     = "SELECT ls.`transaction_type`,ls.`document_type`,ld.`label`, COUNT(*) as total, sum(if(transaction_type = 'debit' AND document_type = 'general_section',1,0)) as general_section_debit, sum(if(transaction_type = 'credit' AND document_type = 'general_section',1,0)) as general_section_credit FROM `ledger_statement` ls
                INNER JOIN `ledger_document` ld ON ld.key_name=ls.document_type WHERE ledger_id = " . $postData['ledger_id'] . " AND `document_type` is not null GROUP BY  `document_type`";
            $total_document_wise_count = $this->checkbook_model->query_result($sql);

            $totalDocumentWiseCountAry = [];
            foreach ($total_document_wise_count as $_total_document_wise_count) {
                $totalDocumentWiseCountAry[$_total_document_wise_count->document_type]['count'] = $_total_document_wise_count->total;
                $totalDocumentWiseCountAry[$_total_document_wise_count->document_type]['label'] = $_total_document_wise_count->label;

                if ($_total_document_wise_count->document_type == 'general_section') {
                    $totalDocumentWiseCountAry['general_section_debit']['count']  = $_total_document_wise_count->general_section_debit;
                    $totalDocumentWiseCountAry['general_section_credit']['count'] = $_total_document_wise_count->general_section_credit;
                }
            }

            if ($selectedTransactionType == 'debit' && $selectedDocumentType == 'general_section' && $totalDocumentWiseCountAry['general_section_debit']['count'] < totalEntriesDocumentWise('general_section_debit')) {
                echo json_encode(array("status" => true, "value" => "General Section Debit Entries"));
                exit;
            } else if ($selectedTransactionType == 'credit' && $selectedDocumentType == 'general_section' && $totalDocumentWiseCountAry['general_section_credit']['count'] < totalEntriesDocumentWise('general_section_credit')) {
                echo json_encode(array("status" => true, "value" => $totalDocumentWiseCountAry[$selectedDocumentType]['label']));
                exit;
            } else if (isset($totalDocumentWiseCountAry[$selectedDocumentType]) && $totalDocumentWiseCountAry[$selectedDocumentType]['count'] < totalEntriesDocumentWise($selectedDocumentType)) {
                echo json_encode(array("status" => true, "value" => $totalDocumentWiseCountAry[$selectedDocumentType]['label']));
                exit;
            } else if (!isset($totalDocumentWiseCountAry[$selectedDocumentType])) {
                echo json_encode(array("status" => true, "value" => ''));
            }
            else
            {
                echo json_encode(array("status" => false, "value" => $totalDocumentWiseCountAry[$selectedDocumentType]['label']));
            }
            exit;
        } catch (Exception $e) {
            //alert the user then kill the process
            og_message('error', $e->getMessage());
            return;
        }
    }

    public function auto()
    {
        try {
            $ledgerId = $this->input->get('ledger_id');
            $bankId   = $this->input->get('bank_id');

            $coveredLedger = [];
            $coveredBank   = [];

            //ledger data
            $data['ledger']      = $this->ledger_model->Get($ledgerId);
            $data['ledger_data'] = $this->ledger_statement_model->Get(null, array("ledger_id" => $ledgerId, "is_reconcile" => 0));
            $ledgerAry           = [];
            if (isset($data['ledger_data']['records']) && !empty($data['ledger_data']['records'])) {
                foreach ($data['ledger_data']['records'] as $lRow) {
                    if ($lRow->transaction_type == 'credit') {
                        $ledgerAry['deposit'][$lRow->id]['id']          = $lRow->id;
                        $ledgerAry['deposit'][$lRow->id]['description'] = $lRow->description;
                        $ledgerAry['deposit'][$lRow->id]['amt']         = $lRow->credit_amt;
                    } else {
                        $ledgerAry[sanitize($lRow->description)][$lRow->id]['id']          = $lRow->id;
                        $ledgerAry[sanitize($lRow->description)][$lRow->id]['description'] = $lRow->description;
                        $ledgerAry[sanitize($lRow->description)][$lRow->id]['amt']         = $lRow->debit_amt;
                    }
                }
            }

            //bank data
            $data['bank']      = $this->bank_statement_model->Get($bankId);
            $data['bank_data'] = $this->bank_statement_entries_model->Get(null, array("bank_statement_id" => $bankId, "is_reconcile" => 0));
            $bankAry           = [];
            if (isset($data['bank_data']['records']) && !empty($data['bank_data']['records'])) {
                foreach ($data['bank_data']['records'] as $bRow) {
                    if (strpos(sanitize($bRow->description), 'deposit') !== false) {
                        //found
                        $bankAry['deposit'][$bRow->id]['id']          = $bRow->id;
                        $bankAry['deposit'][$bRow->id]['description'] = $bRow->description;
                        $bankAry['deposit'][$bRow->id]['amt']         = $bRow->amount;
                    } else {
                        $bankAry[sanitize($bRow->description)][$bRow->id]['id']          = $bRow->id;
                        $bankAry[sanitize($bRow->description)][$bRow->id]['description'] = $bRow->description;
                        $bankAry[sanitize($bRow->description)][$bRow->id]['amt']         = $bRow->amount;
                    }
                }
            }

            //previous ledger entries which has bank uploaded
            $sql = "SELECT ls.*,l.month,l.year FROM `ledger_statement` ls
                    INNER JOIN `ledger` l ON ls.ledger_id = l.id
                    INNER JOIN `bank_statement` b ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                    WHERE l.store_key = " . $data['ledger']->store_key . " AND ledger_id != " . $ledgerId . " AND is_reconcile = 0 AND l.month < " . $data['ledger']->month . " ORDER BY ledger_id";
            $data['previous_unreconciled_ledger_data'] = $this->checkbook_model->query_result($sql);

            $previousLedgerAry = [];
            if (isset($data['previous_unreconciled_ledger_data']) && !empty($data['previous_unreconciled_ledger_data'])) {
                foreach ($data['previous_unreconciled_ledger_data'] as $prow) {
                    $coveredLedger[] = $prow->ledger_id;
                    if ($prow->transaction_type == 'credit') {
                        $previousLedgerAry['deposit'][$prow->id]['id']          = $prow->id;
                        $previousLedgerAry['deposit'][$prow->id]['description'] = $prow->description;
                        $previousLedgerAry['deposit'][$prow->id]['amt']         = $prow->credit_amt;
                    } else {
                        $previousLedgerAry[sanitize($prow->description)][$prow->id]['id']          = $prow->id;
                        $previousLedgerAry[sanitize($prow->description)][$prow->id]['description'] = $prow->description;
                        $previousLedgerAry[sanitize($prow->description)][$prow->id]['amt']         = $prow->debit_amt;
                    }
                }
            }

            // echo "<pre>";
            // print_r($previousLedgerAry);
            // print_r($bankAry);
            // exit;
            //Type-2 reconcile previous ledger with current bank statement
            foreach (reconcileLedgerWithBankMapping() as $ledgerDesc => $bankDesc) {
                $ledgerDesc = sanitize($ledgerDesc);
                // echo "<br>";
                // echo $ledgerDesc;
                // echo "=";
                // echo $bankDesc;
                $ledgerCompareAmtAry = isset($previousLedgerAry[$ledgerDesc]) ? $previousLedgerAry[$ledgerDesc] : [];
                $bankCompareAmtAry   = [];
                if ($bankDesc[0] == '%' && $bankDesc[strlen($bankDesc) - 1] == '%') {
                    // echo "both found";
                    foreach ($bankAry as $bankAryDesc => $bankAryAmt) {
                        if (strpos($bankAryDesc, trim($bankDesc, '%')) !== false) {
                            $bankCompareAmtAry = $bankCompareAmtAry + $bankAryAmt;
                        }
                    }
                } elseif ($bankDesc[0] == '%') {
                    // echo "first found";
                    $endPosition   = strlen($bankAryDesc) - 1;
                    $startPosition = (strlen($bankAryDesc) - 1 - (strlen($bankDesc) - 1));
                    // echo substr($bankAryDesc, $startPosition + 1 ,$endPosition);exit;
                    foreach ($bankAry as $bankAryDesc => $bankAryAmt) {
                        if ("%" . substr($bankAryDesc, $startPosition + 1, $endPosition) == $bankDesc) {
                            $bankCompareAmtAry = $bankCompareAmtAry + $bankAryAmt;
                        }
                    }
                } elseif ($bankDesc[strlen($bankDesc) - 1] == '%') {
                    // echo "last found";
                    foreach ($bankAry as $bankAryDesc => $bankAryAmt) {
                        if (substr($bankAryDesc, 0, strlen($bankDesc) - 1) . "%" == $bankDesc) {
                            $bankCompareAmtAry = $bankCompareAmtAry + $bankAryAmt;
                        }
                    }
                } else {
                    // echo "not found";
                    foreach ($bankAry as $bankAryDesc => $bankAryAmt) {
                        if ($bankAryDesc == $bankDesc) {
                            $bankCompareAmtAry = $bankCompareAmtAry + $bankAryAmt;
                        }
                    }
                }

                // if($ledgerDesc == strtolower("DEPOSIT"))
                // {
                //     echo $ledgerDesc."<br>";
                //     echo $bankDesc;
                //     echo "<pre>bankCompareAmtAry=";
                //     print_r($bankCompareAmtAry);
                //     echo "<pre>ledgerCompareAmtAry=";
                //     print_r($ledgerCompareAmtAry);
                // }

                $matchedAmount = [];
                foreach ($bankCompareAmtAry as $bankStatementId => $_bankCompareAmtAry) {
                    foreach ($ledgerCompareAmtAry as $ledgerStatementId => $_ledgerCompareAmtAry) {
                        if ($_ledgerCompareAmtAry['amt'] == $_bankCompareAmtAry['amt'] && !in_array($_ledgerCompareAmtAry['amt'], $matchedAmount)
                        ) {
                            $ledgerStatementdata                          = [];
                            $ledgerStatementdata['is_reconcile']          = 1;
                            $ledgerStatementdata['reconcile_type']        = 'auto';
                            $ledgerStatementdata['bank_statement_id']     = $bankStatementId;
                            $ledgerStatementdata['reconcile_date']        = date('Y-m-d H:i:s');
                            $ledgerStatementdata['is_reconciled_current'] = 2;
                            $this->ledger_statement_model->edit($ledgerStatementId, $ledgerStatementdata);

                            $bankStatementdata                          = [];
                            $bankStatementdata['is_reconcile']          = 1;
                            $bankStatementdata['reconcile_type']        = 'auto';
                            $bankStatementdata['ledger_statement_id']   = $ledgerStatementId;
                            $bankStatementdata['reconcile_date']        = date('Y-m-d H:i:s');
                            $bankStatementdata['is_reconciled_current'] = 2;
                            $this->bank_statement_entries_model->edit($bankStatementId, $bankStatementdata);

                            $matchedAmount[] = $_ledgerCompareAmtAry['amt'];
                        }
                    }
                }
            }
            // exit;
            //previous bank entries which has respective ledger uploaded
            $sql = "SELECT bs.*,b.month,b.year FROM `bank_statement_entries` bs
                    INNER JOIN `bank_statement` b ON b.id = bs.bank_statement_id
                    INNER JOIN `ledger` l ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                    WHERE b.store_key = " . $data['ledger']->store_key . " AND bank_statement_id != " . $bankId . " AND is_reconcile = 0 AND b.month < " . $data['bank']->month . " ORDER BY bank_statement_id";
            $data['previous_unreconciled_bank_data'] = $this->checkbook_model->query_result($sql);
            $previousBankAry                         = [];
            if (isset($data['previous_unreconciled_bank_data']) && !empty($data['previous_unreconciled_bank_data'])) {
                foreach ($data['previous_unreconciled_bank_data'] as $prow) {
                    $coveredBank[] = $prow->bank_statement_id;
                    if (strpos(sanitize($prow->description), 'deposit') !== false) {
                        //found
                        $previousBankAry['deposit'][$prow->id]['id']          = $prow->id;
                        $previousBankAry['deposit'][$prow->id]['description'] = $prow->description;
                        $previousBankAry['deposit'][$prow->id]['amt']         = $prow->amount;
                    } else {
                        $previousBankAry[sanitize($prow->description)][$prow->id]['id']          = $prow->id;
                        $previousBankAry[sanitize($prow->description)][$prow->id]['description'] = $prow->description;
                        $previousBankAry[sanitize($prow->description)][$prow->id]['amt']         = $prow->amount;
                    }
                }
            }

            //Type-3 reconcile previous bank with current ledger statement
            foreach (reconcileLedgerWithBankMapping() as $ledgerDesc => $bankDesc) {
                $ledgerDesc          = sanitize($ledgerDesc);
                $ledgerCompareAmtAry = isset($ledgerAry[$ledgerDesc]) ? $ledgerAry[$ledgerDesc] : [];
                $bankCompareAmtAry   = [];
                if ($bankDesc[0] == '%' && $bankDesc[strlen($bankDesc) - 1] == '%') {
                    // echo "both found";
                    foreach ($previousBankAry as $bankAryDesc => $bankAryAmt) {
                        if (strpos($bankAryDesc, trim($bankDesc, '%')) !== false) {
                            $bankCompareAmtAry = $bankCompareAmtAry + $bankAryAmt;
                        }
                    }
                } elseif ($bankDesc[0] == '%') {
                    // echo "first found";
                    $endPosition   = strlen($bankAryDesc) - 1;
                    $startPosition = (strlen($bankAryDesc) - 1 - (strlen($bankDesc) - 1));
                    // echo substr($bankAryDesc, $startPosition + 1 ,$endPosition);exit;
                    foreach ($previousBankAry as $bankAryDesc => $bankAryAmt) {
                        if ("%" . substr($bankAryDesc, $startPosition + 1, $endPosition) == $bankDesc) {
                            $bankCompareAmtAry = $bankCompareAmtAry + $bankAryAmt;
                        }
                    }
                } elseif ($bankDesc[strlen($bankDesc) - 1] == '%') {
                    // echo "last found";
                    foreach ($previousBankAry as $bankAryDesc => $bankAryAmt) {
                        if (substr($bankAryDesc, 0, strlen($bankDesc) - 1) . "%" == $bankDesc) {
                            $bankCompareAmtAry = $bankCompareAmtAry + $bankAryAmt;
                        }
                    }
                } else {
                    // echo "not found";
                    foreach ($previousBankAry as $bankAryDesc => $bankAryAmt) {

                        if ($bankAryDesc == $bankDesc) {
                            $bankCompareAmtAry = $bankCompareAmtAry + $bankAryAmt;
                        }
                    }
                }

                $matchedAmount = [];
                foreach ($bankCompareAmtAry as $bankStatementId => $_bankCompareAmtAry) {
                    foreach ($ledgerCompareAmtAry as $ledgerStatementId => $_ledgerCompareAmtAry) {
                        if ($_ledgerCompareAmtAry['amt'] == $_bankCompareAmtAry['amt'] && !in_array($_ledgerCompareAmtAry['amt'], $matchedAmount)
                        ) {

                            $ledgerStatementdata                          = [];
                            $ledgerStatementdata['is_reconcile']          = 1;
                            $ledgerStatementdata['reconcile_type']        = 'auto';
                            $ledgerStatementdata['bank_statement_id']     = $bankStatementId;
                            $ledgerStatementdata['reconcile_date']        = date('Y-m-d H:i:s');
                            $ledgerStatementdata['is_reconciled_current'] = 2;
                            $this->ledger_statement_model->edit($ledgerStatementId, $ledgerStatementdata);

                            $bankStatementdata                          = [];
                            $bankStatementdata['is_reconcile']          = 1;
                            $bankStatementdata['reconcile_type']        = 'auto';
                            $bankStatementdata['ledger_statement_id']   = $ledgerStatementId;
                            $bankStatementdata['reconcile_date']        = date('Y-m-d H:i:s');
                            $bankStatementdata['is_reconciled_current'] = 2;
                            $this->bank_statement_entries_model->edit($bankStatementId, $bankStatementdata);

                            $matchedAmount[] = $_ledgerCompareAmtAry['amt'];
                        }
                    }
                }
            }

            //remaining unreconciled entries from current BS
            $data['bank_data'] = $this->bank_statement_entries_model->Get(null, array("bank_statement_id" => $bankId, 'is_reconcile' => 0));
            $bankAry           = [];
            if (isset($data['bank_data']['records']) && !empty($data['bank_data']['records'])) {
                foreach ($data['bank_data']['records'] as $bRow) {
                    $coveredBank[] = $bRow->bank_statement_id;
                    if (strpos(sanitize($bRow->description), 'deposit') !== false) {
                        //found
                        $bankAry['deposit'][$bRow->id]['id']          = $bRow->id;
                        $bankAry['deposit'][$bRow->id]['description'] = $bRow->description;
                        $bankAry['deposit'][$bRow->id]['amt']         = $bRow->amount;
                    } else {
                        $bankAry[sanitize($bRow->description)][$bRow->id]['id']          = $bRow->id;
                        $bankAry[sanitize($bRow->description)][$bRow->id]['description'] = $bRow->description;
                        $bankAry[sanitize($bRow->description)][$bRow->id]['amt']         = $bRow->amount;
                    }
                }
            }
            //remaining unreconciled entries from current Ledger
            $data['ledger_data'] = $this->ledger_statement_model->Get(null, array("ledger_id" => $ledgerId, 'is_reconcile' => 0));
            $ledgerAry           = [];
            if (isset($data['ledger_data']['records']) && !empty($data['ledger_data']['records'])) {
                foreach ($data['ledger_data']['records'] as $lRow) {
                    $coveredLedger[] = $lRow->ledger_id;
                    if (strtolower($lRow->description) == strtolower('DEPOSIT')) {
                        $ledgerAry['deposit'][$lRow->id]['id']          = $lRow->id;
                        $ledgerAry['deposit'][$lRow->id]['description'] = $lRow->description;
                        $ledgerAry['deposit'][$lRow->id]['amt']         = $lRow->credit_amt;
                    } else {
                        $ledgerAry[sanitize($lRow->description)][$lRow->id]['id']          = $lRow->id;
                        $ledgerAry[sanitize($lRow->description)][$lRow->id]['description'] = $lRow->description;
                        if ($lRow->transaction_type == 'credit') {
                            $ledgerAry[sanitize($lRow->description)][$lRow->id]['amt'] = $lRow->credit_amt;
                        } else {
                            $ledgerAry[sanitize($lRow->description)][$lRow->id]['amt'] = $lRow->debit_amt;
                        }
                    }
                }
            }

            //Type-1 reconcile current ledger with current bank
            foreach (reconcileLedgerWithBankMapping() as $ledgerDesc => $bankDesc) {
                $ledgerDesc          = sanitize($ledgerDesc);
                $ledgerCompareAmtAry = isset($ledgerAry[$ledgerDesc]) ? $ledgerAry[$ledgerDesc] : [];
                $bankCompareAmtAry   = [];

                // echo $ledgerDesc.'='.$bankDesc."<br>";
                if ($bankDesc[0] == '%' && $bankDesc[strlen($bankDesc) - 1] == '%') {
                    // echo "both found";
                    foreach ($bankAry as $bankAryDesc => $bankAryAmt) {
                        if (strpos($bankAryDesc, trim($bankDesc, '%')) !== false) {
                            $bankCompareAmtAry = $bankCompareAmtAry + $bankAryAmt;
                        }
                    }
                } elseif ($bankDesc[0] == '%') {
                    // echo "first found";
                    $endPosition   = strlen($bankAryDesc) - 1;
                    $startPosition = (strlen($bankAryDesc) - 1 - (strlen($bankDesc) - 1));
                    // echo substr($bankAryDesc, $startPosition + 1 ,$endPosition);exit;
                    foreach ($bankAry as $bankAryDesc => $bankAryAmt) {
                        if ("%" . substr($bankAryDesc, $startPosition + 1, $endPosition) == $bankDesc) {
                            $bankCompareAmtAry = $bankCompareAmtAry + $bankAryAmt;
                        }
                    }
                } elseif ($bankDesc[strlen($bankDesc) - 1] == '%') {
                    // echo "last found";
                    foreach ($bankAry as $bankAryDesc => $bankAryAmt) {
                        if (substr($bankAryDesc, 0, strlen($bankDesc) - 1) . "%" == $bankDesc) {
                            $bankCompareAmtAry = $bankCompareAmtAry + $bankAryAmt;
                        }
                    }
                } else {
                    // echo "not found";
                    foreach ($bankAry as $bankAryDesc => $bankAryAmt) {
                        if ($bankAryDesc == $bankDesc) {
                            $bankCompareAmtAry = $bankCompareAmtAry + $bankAryAmt;
                        }
                    }
                }

                /*                 if ($ledgerDesc == strtolower("Dcp Efts")) {
                echo $ledgerDesc . "<br>";
                echo $bankDesc;
                echo "<pre>bankCompareAmtAry=";
                print_r($bankCompareAmtAry);
                echo "<pre>ledgerCompareAmtAry=";
                print_r($ledgerCompareAmtAry);
                exit;
                } */
                $matchedAmount = [];
                foreach ($bankCompareAmtAry as $bankStatementId => $_bankCompareAmtAry) {
                    foreach ($ledgerCompareAmtAry as $ledgerStatementId => $_ledgerCompareAmtAry) {
                        if ($_ledgerCompareAmtAry['amt'] == $_bankCompareAmtAry['amt'] && !in_array($_ledgerCompareAmtAry['amt'], $matchedAmount)
                        ) {
                            $ledgerStatementdata                          = [];
                            $ledgerStatementdata['is_reconcile']          = 1;
                            $ledgerStatementdata['reconcile_type']        = 'auto';
                            $ledgerStatementdata['bank_statement_id']     = $bankStatementId;
                            $ledgerStatementdata['reconcile_date']        = date('Y-m-d H:i:s');
                            $ledgerStatementdata['is_reconciled_current'] = 1;
                            $this->ledger_statement_model->edit($ledgerStatementId, $ledgerStatementdata);

                            $bankStatementdata                          = [];
                            $bankStatementdata['is_reconcile']          = 1;
                            $bankStatementdata['reconcile_type']        = 'auto';
                            $bankStatementdata['ledger_statement_id']   = $ledgerStatementId;
                            $bankStatementdata['reconcile_date']        = date('Y-m-d H:i:s');
                            $bankStatementdata['is_reconciled_current'] = 1;
                            $this->bank_statement_entries_model->edit($bankStatementId, $bankStatementdata);
                            $matchedAmount[] = $_ledgerCompareAmtAry['amt'];
                        }
                    }
                }
            }

            //Payroll net reconcilation process
            $sql = "SELECT bs.*
                     FROM `bank_statement_entries` bs
                     INNER JOIN `bank_statement` b ON b.id = bs.bank_statement_id
                     INNER JOIN `ledger` l ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                     WHERE bs.is_reconcile != 1
                     AND b.month <= " . $data['bank']->month . "
                     AND (bs.description LIKE '%payroll%')
                     ORDER BY bs.bank_statement_id,bs.date";
            $bankPayrollData = $this->ledger_model->query_result($sql);
            // echo '<pre>';print_r($bankPayrollData);die;

            $sql = "SELECT bs.*
                     FROM `bank_statement_entries` bs
                     INNER JOIN `bank_statement` b ON b.id = bs.bank_statement_id
                     INNER JOIN `ledger` l ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                     WHERE bs.is_reconcile != 1
                     AND b.month <= " . $data['bank']->month . "
                     AND (bs.`description` LIKE '%payroll%' OR bs.`description` LIKE '%CKS CERTISTAFF%')
                     ORDER BY bs.bank_statement_id,bs.date";
            $bankCksData = $this->ledger_model->query_result($sql);
            // echo '<pre>';print_r($bankCksData);die;

            $sql = "SELECT ls.*
                     FROM `ledger_statement` ls
                     INNER JOIN `ledger` l ON ls.ledger_id = l.id
                     INNER JOIN `bank_statement` b ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                     WHERE ls.is_reconcile != 1
                     AND l.month <= " . $data['ledger']->month . "
                     AND ls.`document_type` LIKE 'payroll_net'
                     ORDER BY ls.ledger_id,ls.credit_date";
            $ledgerPayrollData = $this->ledger_model->query_result($sql);
            $ledgerPayrollAry  = [];
            foreach ($ledgerPayrollData as $_ledgerPayrollData) {
                $ledgerPayrollAry[$_ledgerPayrollData->id] = $_ledgerPayrollData->debit_amt;
            }
            foreach ($bankPayrollData as $_bankPayrollData) {
                foreach ($bankCksData as $_bankCksData) {
                    $compareAmt        = $_bankPayrollData->amount + $_bankCksData->amount;
                    $ledgerStatementId = array_search((string) $compareAmt, $ledgerPayrollAry);
                    if ($ledgerStatementId) {
                        $ledgerStatementdata                      = [];
                        $ledgerStatementdata['is_reconcile']      = 1;
                        $ledgerStatementdata['reconcile_type']    = 'auto';
                        $ledgerStatementdata['bank_statement_id'] = $_bankPayrollData->id . "," . $_bankCksData->id;
                        $ledgerStatementdata['reconcile_date']    = date('Y-m-d H:i:s');

                        $sql   = "SELECT ledger_id FROM `ledger_statement` WHERE `id` = " . $ledgerStatementId;
                        $lData = $this->ledger_model->query_result($sql);
                        if ($lData[0]->ledger_id == $ledgerId) {
                            $ledgerStatementdata['is_reconciled_current'] = 1;
                        } else {
                            $ledgerStatementdata['is_reconciled_current'] = 2;
                        }
                        $this->ledger_statement_model->edit($ledgerStatementId, $ledgerStatementdata);

                        $bankStatementdata                        = [];
                        $bankStatementdata['is_reconcile']        = 1;
                        $bankStatementdata['reconcile_type']      = 'auto';
                        $bankStatementdata['ledger_statement_id'] = $ledgerStatementId;
                        $bankStatementdata['reconcile_date']      = date('Y-m-d H:i:s');
                        if ($_bankCksData->bank_statement_id == $bankId) {
                            $bankStatementdata['is_reconciled_current'] = 1;
                        } else {
                            $bankStatementdata['is_reconciled_current'] = 2;
                        }
                        $this->bank_statement_entries_model->edit($_bankCksData->id, $bankStatementdata);
                        $this->bank_statement_entries_model->edit($_bankPayrollData->id, $bankStatementdata);
                        unset($ledgerPayrollAry[$ledgerStatementId]);
                        break;
                    }
                }
            }

            //Donut reconcilation process
            $sql = "SELECT bs.*
                     FROM `bank_statement_entries` bs
                     INNER JOIN `bank_statement` b ON b.id = bs.bank_statement_id
                     INNER JOIN `ledger` l ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                     WHERE bs.is_reconcile != 1
                     AND b.month <= " . $data['bank']->month . "
                     AND (bs.`description` LIKE '%CustmerCol Bluemont Group%' or bs.`description` LIKE  '%Golden Donut LLC%')
                     ORDER BY bs.bank_statement_id,bs.date";
            $bankDonutData = $this->ledger_model->query_result($sql);
            $bankDonutAry  = [];
            foreach ($bankDonutData as $_bankDonutData) {
                $coveredBank[]                     = $_bankDonutData->bank_statement_id;
                $bankDonutAry[$_bankDonutData->id] = $_bankDonutData->amount;
            }

            $sql = "SELECT ls.*
                     FROM `ledger_statement` ls
                     INNER JOIN `ledger` l ON ls.ledger_id = l.id
                     INNER JOIN `bank_statement` b ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                     AND l.month <= " . $data['ledger']->month . "
                     WHERE ls.is_reconcile != 1
                     AND ls.document_type LIKE 'donut_purchases'
                     ORDER BY ls.ledger_id,ls.credit_date";
            $ledgerDonutData = $this->ledger_model->query_result($sql);
            $ledgerDonutAry  = [];
            foreach ($ledgerDonutData as $_ledgerDonutData) {
                $coveredLedger[] = $_ledgerDonutData->ledger_id;
                foreach ($ledgerDonutData as $_ledgerDonutDataInner) {
                    $compareAmt      = $_ledgerDonutData->debit_amt + $_ledgerDonutDataInner->debit_amt;
                    $bankStatementId = array_search((string) $compareAmt, $bankDonutAry);
                    $withPointDiff = 0;
                    if(!$bankStatementId)
                    {
                        $compareAmt += 0.01;
                        $bankStatementId = $compareAmt;
                        $bankStatementId = array_search((string) $compareAmt, $bankDonutAry);
                        $withPointDiff = 1;
                    }
                    if ($bankStatementId) {
                        // echo "<br>===<br>ledger amount = " . $compareAmt . "== bank amount" . $bankDonutAry[$bankStatementId];
                        $ledgerStatementdata                      = [];
                        $ledgerStatementdata['is_reconcile']      = 1;
                        $ledgerStatementdata['reconcile_type']    = 'auto';
                        $ledgerStatementdata['bank_statement_id'] = $bankStatementId;
                        $ledgerStatementdata['reconcile_date']    = date('Y-m-d H:i:s');
                        $ledgerStatementdata['with_point_diff']   = $withPointDiff;


                        if ($_ledgerDonutData->ledger_id == $ledgerId) {
                            $ledgerStatementdata['is_reconciled_current'] = 1;
                        } else {
                            $ledgerStatementdata['is_reconciled_current'] = 2;
                        }
                        $this->ledger_statement_model->edit($_ledgerDonutData->id, $ledgerStatementdata);
                        $this->ledger_statement_model->edit($_ledgerDonutDataInner->id, $ledgerStatementdata);

                        $bankStatementdata                        = [];
                        $bankStatementdata['is_reconcile']        = 1;
                        $bankStatementdata['reconcile_type']      = 'auto';
                        $bankStatementdata['ledger_statement_id'] = $_ledgerDonutData->id . "," . $_ledgerDonutDataInner->id;
                        $bankStatementdata['reconcile_date']      = date('Y-m-d H:i:s');
                        $bankStatementdata['with_point_diff']     = $withPointDiff;

                        $sql   = "SELECT bank_statement_id FROM `bank_statement_entries` WHERE `id` = " . $bankStatementId;
                        $bData = $this->ledger_model->query_result($sql);
                        if ($bData[0]->bank_statement_id == $bankId) {
                            $bankStatementdata['is_reconciled_current'] = 1;
                        } else {
                            $bankStatementdata['is_reconciled_current'] = 2;
                        }
                        $this->bank_statement_entries_model->edit($bankStatementId, $bankStatementdata);
                        unset($bankDonutAry[$bankStatementId]);
                        break;
                    }
                }
            }

            //credit card section auto reconcillation
            $sql = "SELECT *
                     FROM `bank_statement_entries`
                     WHERE is_reconcile != 1
                     AND bank_statement_id = " . $bankId . "
                     AND (`description` LIKE '%DEPOSIT BOFA MERCH%'
                            OR `description` LIKE '%FDCLGIFT DD%'
                            OR `description` LIKE '%SETTLEMENT AMERICAN EXPRESS%')
                     ORDER BY bank_statement_id,date";
            $bankDepositeData = $this->ledger_model->query_result($sql);

            $sql = "SELECT id,credit_amt
                     FROM `ledger_statement`
                     WHERE is_reconcile != 1
                     AND ledger_id = " . $ledgerId . "
                     AND `description` LIKE '%Credit Card Credits%' limit 1";
            $ledgerCreditData = $this->ledger_model->query_result($sql);
            // echo '<pre>';print_r($ledgerCreditData);

            $bankDepositeAmt = 0;
            $bankDepositeIds = [];
            foreach ($bankDepositeData as $_bankDepositeData) {
                $coveredBank[] = $_bankDepositeData->bank_statement_id;
                $bankDepositeAmt += $_bankDepositeData->amount;
                $bankDepositeIds[] = $_bankDepositeData->id;
            }
            // echo $bankDepositeAmt;
            if (isset($ledgerCreditData[0]) && (string) $bankDepositeAmt == $ledgerCreditData[0]->credit_amt) {
                $ledgerStatementdata                          = [];
                $ledgerStatementdata['is_reconcile']          = 1;
                $ledgerStatementdata['reconcile_type']        = 'auto';
                $ledgerStatementdata['bank_statement_id']     = implode(",", $bankDepositeIds);
                $ledgerStatementdata['reconcile_date']        = date('Y-m-d H:i:s');
                $ledgerStatementdata['is_reconciled_current'] = 1;
                $this->ledger_statement_model->edit($ledgerCreditData[0]->id, $ledgerStatementdata);

                $bankStatementdata                          = [];
                $bankStatementdata['is_reconcile']          = 1;
                $bankStatementdata['reconcile_type']        = 'auto';
                $bankStatementdata['ledger_statement_id']   = $ledgerCreditData[0]->id;
                $bankStatementdata['reconcile_date']        = date('Y-m-d H:i:s');
                $bankStatementdata['is_reconciled_current'] = 1;
                foreach ($bankDepositeIds as $_bankDepositeId) {
                    $this->bank_statement_entries_model->edit($_bankDepositeId, $bankStatementdata);
                }
            }

            //check book section auto reconcilation
            $sql = "SELECT cr.*
                        FROM `checkbook_record` cr
                        INNER JOIN `ledger` l ON cr.ledger_id = l.id
                        INNER JOIN `bank_statement` b ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                        AND l.month <= " . $data['ledger']->month . "
                        WHERE cr.check_number != '' AND cr.`is_reconcile` != 1";

            $ledgerChecks    = $this->ledger_model->query_result($sql);
            $ledgerChecksAry = [];
            $count           = -1;
            foreach ($ledgerChecks as $_ledgerChecks) {
                $ledgerChecksAry[$_ledgerChecks->check_number]['id']  = $_ledgerChecks->id;
                $ledgerChecksAry[$_ledgerChecks->check_number]['amt'] = $_ledgerChecks->amount1;
            }

            $sql = "SELECT bs.*
                     FROM `bank_statement_entries` bs
                     INNER JOIN `bank_statement` b ON b.id = bs.bank_statement_id
                     INNER JOIN `ledger` l ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                     WHERE b.month <= " . $data['bank']->month . "
                     AND `check_num` != 0 AND `is_reconcile` != 1";
            $bankChecks    = $this->ledger_model->query_result($sql);
            $bankChecksAry = [];
            foreach ($bankChecks as $_bankChecks) {
                $coveredBank[] = $_bankChecks->bank_statement_id;
                if (isset($ledgerChecksAry[$_bankChecks->check_num]) && $ledgerChecksAry[$_bankChecks->check_num]['amt'] == $_bankChecks->amount) {
                    $ledgerStatementdata                      = [];
                    $ledgerStatementdata['is_reconcile']      = 1;
                    $ledgerStatementdata['reconcile_type']    = 'auto';
                    $ledgerStatementdata['bank_statement_id'] = $_bankChecks->id;
                    $ledgerStatementdata['reconcile_date']    = date('Y-m-d H:i:s');
                    $sql                                      = "SELECT ledger_id FROM `checkbook_record` WHERE `id` = " . $ledgerChecksAry[$_bankChecks->check_num]['id'];
                    $lData                                    = $this->ledger_model->query_result($sql);
                    if ($lData[0]->ledger_id == $ledgerId) {
                        $ledgerStatementdata['is_reconciled_current'] = 1;
                    } else {
                        $ledgerStatementdata['is_reconciled_current'] = 2;
                    }
                    $this->checkbook_model->edit($ledgerChecksAry[$_bankChecks->check_num]['id'], $ledgerStatementdata);

                    $bankStatementdata                        = [];
                    $bankStatementdata['is_reconcile']        = 1;
                    $bankStatementdata['reconcile_type']      = 'auto';
                    $bankStatementdata['ledger_statement_id'] = $ledgerChecksAry[$_bankChecks->check_num]['id'];
                    $bankStatementdata['reconcile_date']      = date('Y-m-d H:i:s');
                    if ($_bankChecks->bank_statement_id == $bankId) {
                        $bankStatementdata['is_reconciled_current'] = 1;
                    } else {
                        $bankStatementdata['is_reconciled_current'] = 2;
                    }
                    $this->bank_statement_entries_model->edit($_bankChecks->id, $bankStatementdata);
                }
            }

            //zero entries auto reconcilation ledger
            $sql = "SELECT ls.*
                     FROM `ledger_statement` ls
                     INNER JOIN `ledger` l ON ls.ledger_id = l.id
                     INNER JOIN `bank_statement` b ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                     WHERE ls.is_reconcile != 1
                     AND l.month = " . $data['ledger']->month . "
                     AND (ls.credit_amt = 0 or ls.credit_amt is null)
                     AND (ls.debit_amt = 0 or ls.debit_amt is null)";
            $ledgerData = $this->ledger_model->query_result($sql);
            foreach ($ledgerData as $_ledgerData) {
                $coveredLedger[]                              = $_ledgerData->ledger_id;
                $ledgerStatementdata                          = [];
                $ledgerStatementdata['is_reconcile']          = 1;
                $ledgerStatementdata['reconcile_type']        = 'auto';
                $ledgerStatementdata['reconcile_date']        = date('Y-m-d H:i:s');
                $ledgerStatementdata['is_reconciled_current'] = 1;
                $this->ledger_statement_model->edit($_ledgerData->id, $ledgerStatementdata);
            }

            //zero entries auto reconcilation bank
            $sql = "SELECT bs.*
                     FROM `bank_statement_entries` bs
                     INNER JOIN `bank_statement` b ON b.id = bs.bank_statement_id
                     INNER JOIN `ledger` l ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                     WHERE (bs.`amount` is null or bs.`amount` = 0)
                     AND b.month = " . $data['bank']->month . "
                    AND bs.`is_reconcile` != 1";
            $bankData = $this->ledger_model->query_result($sql);
            foreach ($bankData as $_bankData) {
                $coveredBank[]                              = $_bankData->bank_statement_id;
                $bankStatementdata                          = [];
                $bankStatementdata['is_reconcile']          = 1;
                $bankStatementdata['reconcile_type']        = 'auto';
                $bankStatementdata['reconcile_date']        = date('Y-m-d H:i:s');
                $bankStatementdata['is_reconciled_current'] = 1;
                $this->bank_statement_entries_model->edit($_bankData->id, $bankStatementdata);
            }
            /***************************/
            foreach (array_unique($coveredBank) as $_coveredBank) {
                updateBankStatus($_coveredBank);
            }
            foreach (array_unique($coveredLedger) as $_coveredLedger) {
                updateLedgerStatus($_coveredLedger);
            }

            redirect('reconcile/process?ledger_id=' . $ledgerId . '&bank_id=' . $bankId);
        } catch (Exception $e) {
            //alert the user then kill the process
            og_message('error', $e->getMessage());
            return;
        }
    }

    public function manual()
    {
        try {

            $postData = $this->input->post();
            $ledgerId = $postData['ledger_id'];
            $bankId = $postData['bank_id'];
            if (isset($postData['txt_manual_ledger_balance']) && isset($postData['txt_manual_ledger_balance']) && (($postData['txt_manual_ledger_balance']) != $postData['txt_manual_ledger_balance'])) {
                og_message('Ledger balance and Bank balance does not match.', $e->getMessage());
                return;
            }
            $coveredLedger = [];
            $coveredBank   = [];

            if (isset($postData['ledger_statement_id']) && isset($postData['bank_statement_id'])) {
                foreach ($postData['ledger_statement_id'] as $key => $_ledgerStatement) {
                    foreach ($_ledgerStatement as $__ledgerStatementId) {
                        $data                   = [];
                        $data['reconcile_type'] = 'manual';
                        $data['is_reconcile']   = 1;

                        $data['reconcile_date']        = date('Y-m-d H:i:s');

                        $sql   = "SELECT ledger_id FROM `ledger_statement` WHERE `id` = " . $__ledgerStatementId;
                        $lData = $this->ledger_model->query_result($sql);
                        $coveredLedger[] = $lData[0]->ledger_id;
                        if ($lData[0]->ledger_id == $ledgerId) {
                            $data['is_reconciled_current'] = 1;
                        } else {
                            $data['is_reconciled_current'] = 2;
                        }

                        $bankStatementIds          = implode(",", $postData['bank_statement_id'][$key]);
                        $data['bank_statement_id'] = $bankStatementIds;

                        $this->ledger_statement_model->edit($__ledgerStatementId, $data);
                    }
                }
                foreach ($postData['bank_statement_id'] as $key => $_bankStatement) {
                    foreach ($_bankStatement as $__bankStatementId) {
                        $data                        = [];
                        $data['reconcile_type']      = 'manual';
                        $data['is_reconcile']        = 1;

                        $data['reconcile_date']        = date('Y-m-d H:i:s');

                        $sql   = "SELECT bank_statement_id FROM `bank_statement_entries` WHERE `id` = " . $__bankStatementId;
                        $bData = $this->ledger_model->query_result($sql);
                        $coveredBank[] = $bData[0]->bank_statement_id;
                        if ($bData[0]->bank_statement_id == $bankId) {
                            $data['is_reconciled_current'] = 1;
                        } else {
                            $data['is_reconciled_current'] = 2;
                        }
                        $ledgerStatementIds          = implode(",", $postData['ledger_statement_id'][$key]);
                        $data['ledger_statement_id'] = $ledgerStatementIds;
                        $this->bank_statement_entries_model->edit($__bankStatementId, $data);
                    }
                }
            }
            foreach ($coveredBank as $_coveredBank) {
                updateBankStatus($_coveredBank);
            }
            foreach ($coveredLedger as $_coveredLedger) {
                updateLedgerStatus($_coveredLedger);
            }
            redirect('reconcile/process?ledger_id=' . $postData['ledger_id'] . '&bank_id=' . $postData['bank_id']);
        } catch (Exception $e) {
            //alert the user then kill the process
            og_message('error', $e->getMessage());
            return;
        }
    }

    public function index()
    {
        $data['title'] = 'Reconcile Statement List';

        $store_id = $this->input->get('store_id');
        $month    = $this->input->get('month');
        $year     = $this->input->get('year');

        $search_arr       = array("store_id" => $store_id, "month" => $month, "year" => $year);
        $ledger_Res       = $this->reconcile_model->ledger_bank_join($search_arr);
        $bank_Res         = $this->reconcile_model->bank_ledger_join($search_arr);
        $posPayroll       = $this->reconcile_model->getPosPayrollIfNoLedger($search_arr);

        $final_arr        = array();
        $final_ledger_arr = [];

        foreach ($ledger_Res as $lRow) {
            $Findbank_Res = $this->find_bank_id($lRow->store_key, $lRow->month, $lRow->year, $bank_Res);

            $final_ledger_arr[$lRow->ledger_id] = array(
                "ledger_id"   => $lRow->ledger_id,
                "store_key"   => $lRow->store_key,
                "monthnumber" => $lRow->month,
                "year"        => $lRow->year,
                "status"      => $lRow->status,
                "is_locked"   => $lRow->is_locked,
                "posid"       => $lRow->posid,
                "payrollid"   => $lRow->payrollid,
                "isautoledger"   => $lRow->isautoledger,
                "bank_id"     => isset($Findbank_Res['bank_id']) ? $Findbank_Res['bank_id'] : 0,
                "bank_status" => isset($Findbank_Res['status']) ? $Findbank_Res['status'] : 0,
            );
            if (isset($Findbank_Res['bank_id']) && isset($Findbank_Res['bank_id']) > 0) {
                $index_arr[] = $Findbank_Res['search_index'];
            }
        }
        if (isset($index_arr)) {
            foreach ($index_arr as $key => $val) {
                if (isset($bank_Res[$val])) {
                    unset($bank_Res[$val]);
                }
            }
        }
        $final_bank_Res     = json_decode(json_encode($bank_Res), true);
        $final_pos_payroll  = json_decode(json_encode($posPayroll), true);

        $merge_arr       = array_merge($final_ledger_arr, $final_bank_Res, $final_pos_payroll);
        $data['records'] = $merge_arr;

        $data['store_list'] = $this->store_master_model->Get(null, array("status" => "A"));
        $this->template->load('listing', 'list-reconcilation', $data);
    }

    public function view($statement_id = null)
    {
        if ($statement_id == null) {
            $statement_id = $this->uri->segment(3);
        }
        $data['title']          = 'View Bank Statement';
        $data['statement_data'] = $this->bank_statement_entries_model->Get(null, array("bank_statement_id" => $statement_id));
        $data['bank_data']      = $this->bank_statement_model->Get($statement_id);
        $this->template->load('listing', 'view_statement', $data);
    }

    public function import()
    {
        $data['title']      = "Import Bank Statement";
        $data['store_list'] = $this->store_master_model->Get(null, array("status" => 'A'));
        $this->template->load('listing', 'import_bank_statement', $data);
    }

    public function upload_bank_statement_file()
    {
        $tempFile   = $_FILES['import_file']['tmp_name'];
        $targetPath = FCPATH . "/files_upload/bank_statement/";
        $file_name  = $_FILES['import_file']['name'];

        if (!file_exists($targetPath)) {
            mkdir($targetPath, 0777, true);
        }

        $store_id = $month = $year = '';
        if ($file_name != '') {
            $file_name_arr = explode("-", $file_name);
            if (!empty($file_name_arr)) {
                $store_id = isset($file_name_arr[0]) ? $file_name_arr[0] : '';
                $month    = isset($file_name_arr[1]) ? $file_name_arr[1] : '';
                $year     = isset($file_name_arr[2]) ? $file_name_arr[2] : '';
            }
        }

        if ($store_id != '' && $month != '' && $year != '') {
            $year       = (int) filter_var($year, FILTER_SANITIZE_NUMBER_INT);
            $targetFile = $targetPath . $file_name; //5
            $res        = move_uploaded_file($tempFile, $targetFile);
            $file_path  = $targetFile;
            $csv        = $file_path;

            $file = $csv;

            //Bank Statement Master Entry
            $bank_arr              = array();
            $bank_arr['store_key'] = $store_id;
            $bank_arr['month']     = $month;
            $bank_arr['year']      = "20" . $year;

            $is_Exist_Res = $this->bank_statement_model->Get(null, array("store_key" => $bank_arr['store_key'], "month" => $bank_arr['month'], "year" => $bank_arr['year']));
            if ($is_Exist_Res['countFiltered'] > 0) {
                $this->session->set_flashdata('msg_class', "failure");
                $this->session->set_flashdata('msg', "For Selected store, this month of year bank statement entry already exist");
                redirect('bank/import');
            }

            $bank_id = $this->bank_statement_model->Add($bank_arr);

            //Bank Statement Child Entry
            //load the excel library
            $this->load->library('excel');
            //read file from path
            $objPHPExcel = PHPExcel_IOFactory::load($file);

            //get only the Cell Collection
            $cell_collection = $objPHPExcel->getActiveSheet()->getCellCollection();

            //extract to a PHP readable array format
            foreach ($cell_collection as $cell) {
                $column     = $objPHPExcel->getActiveSheet()->getCell($cell)->getColumn();
                $row        = $objPHPExcel->getActiveSheet()->getCell($cell)->getRow();
                $data_value = $objPHPExcel->getActiveSheet()->getCell($cell)->getValue();

                //The header will/should be in row 1 only. of course, this can be modified to suit your need.
                if ($row == 1) {
                    $header[$row][$column] = $data_value;
                } else {
                    $arr_data[$row][$column] = $data_value;
                }
            }

            $data['cells']  = array_merge($header, $arr_data);
            $data['header'] = $header;
            $data['cells']  = $arr_data;
//        print_r($data['cells']);
            //        exit;

            $ins_data          = array();
            $ins_data['month'] = date('m');
            $ins_data['year']  = date('k');
            $transaction_type  = 'credit';
            foreach ($data['cells'] as $key => $val) {
                $amount = isset($val['E']) ? ($val['E']) : '';
                if ($amount != '') {
                    if (strpos($amount, '(') !== false) {
                        $transaction_type = 'debit';
                    } else {
                        $transaction_type = 'credit';
                    }
                    $a1 = str_replace("(", "", $amount);
                    $a2 = str_replace(")", "", $a1);
                    $a3 = str_replace(",", "", $a2);
                    $a4 = str_replace("$", "", $a3);
                }

                $statement_entries[] = array("bank_statement_id" => $bank_id, "date" => isset($val['A']) ? date("Y-m-d", strtotime($val['A'])) : '', "transaction" => isset($val['B']) ? $val['B'] : '', "check_num" => isset($val['C']) ? $val['C'] : '', "description" => isset($val['D']) ? $val['D'] : '', "transaction_type" => $transaction_type, "amount" => floatval($a4));
            }

            $this->bank_statement_entries_model->add_batch($statement_entries);
            redirect('bank', true);
        } else {
            $this->session->set_flashdata('msg_class', "failure");
            $this->session->set_flashdata('msg', "Please check uploaded file format");
            redirect('bank/import');
        }
    }

    public function delete()
    {
        $ledgerId = $this->input->get('lid');
        $bankId   = $this->input->get('bid');

        $ledgerData = $this->ledger_model->Get($ledgerId);
        $bankData   = $this->bank_statement_model->Get($bankId);
        // echo '<pre>';print_r($ledgerData);die;
        // echo '<pre>';print_r($bankData);die;

        if ($ledgerId && $ledgerData && $ledgerData->status == "unreconcile") {
            $this->ledger_model->Delete($ledgerId);
        }

        if ($bankId && $bankData && $bankData->status == "unreconcile") {
            $this->bank_statement_model->Delete($bankId);
        }

        if ($ledgerId && $ledgerData && $ledgerData->status != 'unreconcile') {

            //Load all ledger statement Ids
            $sql = "SELECT group_concat(id) as ledger_statement_ids FROM `ledger_statement` WHERE `ledger_id` = " . $ledgerId;

            $ledger_statement_ids = $this->ledger_model->query_result($sql);

            //unreconcile bank with above ledger Ids
            $sql = "SELECT id FROM `bank_statement_entries` WHERE `ledger_statement_id` IN (" . $ledger_statement_ids[0]->ledger_statement_ids . ")";

            $bank_statement_ids = $this->ledger_model->query_result($sql);

            if (isset($bank_statement_ids) && !empty($bank_statement_ids)) {
                foreach ($bank_statement_ids as $_bank_statement_id) {
                    $up_data                          = array();
                    $up_data['is_void']               = 0;
                    $up_data['is_reconcile']          = 0;
                    $up_data['ledger_statement_id']   = '';
                    $up_data['reconcile_type']        = null;
                    $up_data['reconcile_date']        = null;
                    $up_data['is_reconciled_current'] = 0;
                    $this->bank_statement_entries_model->Edit($_bank_statement_id->id, $up_data);
                }
            }

            //load all bank statements ids
            $sql = "SELECT group_concat(id) as bank_statement_ids FROM `bank_statement_entries` WHERE `bank_statement_id` = " . $bankId;

            $bank_statement_ids = $this->ledger_model->query_result($sql);

            if ($bank_statement_ids && $bank_statement_ids[0]->bank_statement_ids) {
                //unreconcile ledger with above bank ids
                $sql = "SELECT * FROM `ledger_statement` WHERE `bank_statement_id` IN (" . $bank_statement_ids[0]->bank_statement_ids . ")";

                $ledger_statement_ids = $this->ledger_model->query_result($sql);
                if (isset($ledger_statement_ids) && !empty($ledger_statement_ids)) {
                    foreach ($ledger_statement_ids as $_ledger_statement_id) {
                        $up_data                          = array();
                        $up_data['is_reconcile']          = 0;
                        $up_data['bank_statement_id']     = '';
                        $up_data['reconcile_type']        = null;
                        $up_data['reconcile_date']        = null;
                        $up_data['is_reconciled_current'] = 0;
                        $this->ledger_statement_model->Edit($_ledger_statement_id->id, $up_data);
                    }
                }
            }

            //Delete ledger and bank
            $this->ledger_model->Delete($ledgerId);
            $this->bank_statement_model->Delete($bankId);
        }
        redirect('reconcile');
    }

    public function extra_manual_entry()
    {
        $postData          = $this->input->post();
        $date              = isset($postData['manual-ledger-credit-date']) ? $postData['manual-ledger-credit-date'] : "";
        $type              = $postData['manual-ledger-transaction-type'];
        $desc              = isset($postData['manual-ledger-description']) ? $postData['manual-ledger-description'] : "";
        $amount            = $postData['manual-ledger-amount'];
        $ledgerStatementId = isset($postData['ledger_statement_id']) ? $postData['ledger_statement_id'] : 0;

        $data = [];
        if ($type == 'credit') {
            $data['credit_amt'] = $amount;
        } else {
            $data['debit_amt'] = $amount;
        }

        if ($desc != "") {
            $data['description'] = $desc;
        }

        if ($date != "") {
            $data['credit_date'] = date("Y-m-d", strtotime($date));
        } else {
            $data['credit_date'] = null;
        }

        $data['document_type'] = $postData['manual-ledger-document-type'];

        if ($ledgerStatementId) {
            $this->ledger_statement_model->Edit($ledgerStatementId, $data);
        } else {
            $data['is_manual']        = 1;
            $data['store_key']        = $postData['store_key'];
            $data['ledger_id']        = $postData['ledger_id'];
            $data['transaction_type'] = $type;
            $this->ledger_statement_model->Add($data);
        }
        updateLedgerBalance($postData['ledger_id']);
        echo json_encode(array("status" => "success"));
        exit;
    }

    public function extra_manual_entry_bank()
    {
        $postData        = $this->input->post();
        $type            = $postData['manual-bank-transaction-type'];
        $amount          = $postData['manual-bank-amount'];
        $bankStatementId = isset($postData['bank_statement_id']) ? $postData['bank_statement_id'] : 0;

        $data                     = [];
        $data['date']             = date("Y-m-d", strtotime($postData['manual-bank-credit-date']));
        $data['transaction']      = $postData['manual-bank-transaction-type'];
        $data['transaction_type'] = $postData['manual-bank-transaction-type'] == 'credit' ? 'credit' : 'debit';
        $data['check_num']        = $postData['manual-bank-check_num'];
        $data['description']      = isset($postData['manual-bank-description']) ? $postData['manual-bank-description'] : "";
        $data['amount']           = $amount;

        if ($bankStatementId) {
            $this->bank_statement_entries_model->Edit($bankStatementId, $data);
        } else {
            $data['is_manual']         = 1;
            $data['bank_statement_id'] = $postData['bank_id'];
            $this->bank_statement_entries_model->Add($data);
        }
        echo json_encode(array("status" => "success"));
        exit;
    }

    public function unreconciled_entry()
    {
        $postData = $this->input->post();
        $ledger_statement_id_arr = $this->input->post('ledger_id') ? array_filter($this->input->post('ledger_id')) : 0;
        $checkid                 = $this->input->post('checkid') ? $this->input->post('checkid') : 0;
        $bank_statement_id_arr   = is_array($this->input->post('bank_statement_id')) ? array_filter($this->input->post('bank_statement_id')) : [$this->input->post('bank_statement_id')];

        $coveredLedger = [];
        $coveredBank = [];
        if (isset($postData['reconcilation_type']) && $postData['reconcilation_type'] == 'adjustment') {
            $up_data                          = array();
            $up_data['is_reconcile']          = 0;
            $up_data['bank_statement_id']     = '';
            $up_data['reconcile_type']        = null;
            $up_data['reconcile_date']        = null;
            $up_data['is_reconciled_current'] = 0;
            $this->ledger_statement_model->Delete($ledger_statement_id_arr);
            $this->ledger_statement_model->Edit($bank_statement_id_arr, $up_data);

            //reconciled bank statement id would be ledger id in adjustments
            $sql = "SELECT ledger_id FROM `ledger_statement` WHERE `id` IN (". implode(",", array_unique($bank_statement_id_arr)) .")";
            $lData = $this->ledger_model->query_result($sql);
            foreach ($lData as $_lData) {
                $coveredLedger[] = $_lData->ledger_id;
            }
        }
        else
        {
            if (!empty($bank_statement_id_arr) && $bank_statement_id_arr) {
                $bidStr = is_array($bank_statement_id_arr) ? implode(',', $bank_statement_id_arr) : $bank_statement_id_arr;
                $sql = "SELECT * FROM `bank_statement_entries` WHERE `id` IN ({$bidStr})";
                $bankStatementdata = $this->ledger_model->query_result($sql);
                $coveredBank[] = $bankStatementdata[0]->bank_statement_id;
                $relatedBid = [];
                if(strtolower($bankStatementdata[0]->transaction) == "check" && strtolower($bankStatementdata[0]->description) == "check")
                {
                    $checkid = !$checkid ? $bankStatementdata[0]->ledger_statement_id : $checkid;
                    if (!empty($checkid)) {
                        $up_data                      = [];
                        $up_data['is_reconcile']      = 0;
                        $up_data['is_void']           = 0;
                        $up_data['bank_statement_id'] = '';
                        $up_data['reconcile_type']    = null;
                        $up_data['reconcile_date']    = null;
                        $this->checkbook_model->Edit($checkid, $up_data);
                    }
                }
                else
                {
                    if (!empty($ledger_statement_id_arr)) {
                        $statement_Res  = $this->ledger_statement_model->get_entries($ledger_statement_id_arr);
                        if (isset($statement_Res) && !empty($statement_Res)) {
                            foreach ($statement_Res as $sRow) {
                                $relatedBid     = array_merge($relatedBid,explode(',', $sRow->bank_statement_id));
                            }
                        }
                        $up_data                          = [];
                        $up_data['is_reconcile']          = 0;
                        $up_data['bank_statement_id']     = '';
                        $up_data['reconcile_type']        = null;
                        $up_data['reconcile_date']        = null;
                        $up_data['is_reconciled_current'] = 0;
                        $this->ledger_statement_model->Edit($ledger_statement_id_arr, $up_data);
                    }
                }

                $up_data                        = [];
                $up_data['is_reconcile']        = 0;
                $up_data['ledger_statement_id'] = '';
                $up_data['reconcile_type']      = null;
                $up_data['reconcile_date']      = null;
                $up_data['is_reconciled_current'] = 0;
                $bank_statement_id_arr          = array_merge($bank_statement_id_arr,$relatedBid);
                $this->bank_statement_entries_model->Edit(array_unique($bank_statement_id_arr), $up_data);
            }
        }
        $sql   = "SELECT bank_statement_id FROM `bank_statement_entries` WHERE `id` IN (" . implode(',', array_unique($bank_statement_id_arr)).")";
        $bData = $this->ledger_model->query_result($sql);
        foreach ($bData as $_bData) {
            $coveredBank[] = $_bData->bank_statement_id;
        }

        $sql = "SELECT ledger_id FROM `ledger_statement` WHERE `id` IN (". implode(",", array_unique($ledger_statement_id_arr)) .")";
        $lData = $this->ledger_model->query_result($sql);
        foreach ($lData as $_lData) {
            $coveredLedger[] = $_lData->ledger_id;
        }
        foreach (array_unique($coveredBank) as $_coveredBank) {
            updateBankStatus($_coveredBank);
        }
        foreach (array_unique($coveredLedger) as $_coveredLedger) {
            updateLedgerStatus($_coveredLedger);
        }
        echo json_encode(array("status" => "success"));
        exit;
    }

    public function ledger_adjustment()
    {
        try {

            $ledger_adjustment_ids          = $this->input->post('ledger_adjustment');
            $ledger_adjustment_descriptions = $this->input->post('ledger_adjustment_desc');
            $ledger_id                      = $this->input->post('ledger_id');
            $key                            = 0;

            $coveredLedger[] = $ledger_id;
            $currentLedgerData = $this->ledger_model->Get($ledger_id);

            foreach ($ledger_adjustment_ids as $ledger_adjustment_id) {
                //new adjustment entry start
                $ledgerAdjustmentData          = $this->ledger_statement_model->Get($ledger_adjustment_id);
                $ledger_adjustment_description = isset($ledger_adjustment_descriptions[$key]) ? $ledger_adjustment_descriptions[$key] : "";

                if ($ledgerAdjustmentData->transaction_type == 'debit') {
                    $ledgerAdjustmentData->credit_amt       = $ledgerAdjustmentData->debit_amt;
                    $ledgerAdjustmentData->debit_amt        = "";
                    $ledgerAdjustmentData->transaction_type = 'credit';
                } else {
                    $ledgerAdjustmentData->debit_amt        = $ledgerAdjustmentData->credit_amt;
                    $ledgerAdjustmentData->credit_amt       = "";
                    $ledgerAdjustmentData->transaction_type = 'debit';
                }

                $ledgerAdjustmentData->credit_date          = date('Y-m-d',strtotime($currentLedgerData->month.'-'.$currentLedgerData->month.'-01'));
                $ledgerAdjustmentData->id                   = null;
                $ledgerAdjustmentData->ledger_id            = $ledger_id;
                $ledgerAdjustmentData->parent_id            = 0;
                $ledgerAdjustmentData->is_reconcile         = 1;
                $ledgerAdjustmentData->is_manual            = 1;
                $ledgerAdjustmentData->is_adjustment_entry  = 1;
                $ledgerAdjustmentData->bank_statement_id    = $ledger_adjustment_id;
                $ledgerAdjustmentData->reconcile_type       = 'adjustment';
                $ledgerAdjustmentData->document_type        = 'general_section';
                $ledgerAdjustmentData->description          = $ledger_adjustment_description;
                $insertedId                                 = $this->ledger_statement_model->Add((array) $ledgerAdjustmentData);
                $key++;

                //update existing entry
                $data                      = [];
                $data['bank_statement_id'] = $insertedId;
                $data['reconcile_type']    = 'adjustment';
                $data['is_reconcile']      = 1;
                $data['reconcile_date']    = date('Y-m-d H:i:s');

                $sql   = "SELECT ledger_id FROM `ledger_statement` WHERE `id` = " . $ledger_adjustment_id;
                $lData = $this->ledger_model->query_result($sql);
                $coveredLedger[] = $lData[0]->ledger_id;
                if ($lData[0]->ledger_id == $ledger_id) {
                    $data['is_reconciled_current'] = 1;
                } else {
                    $data['is_reconciled_current'] = 2;
                }
                $this->ledger_statement_model->edit($ledger_adjustment_id, $data);
            }
            updateLedgerBalance($ledger_id);
            foreach (array_unique($coveredLedger) as $_lid) {
                updateLedgerStatus($_lid);
            }
            echo json_encode(array("status" => "success"));
            exit;
        } catch (Exception $e) {
            //alert the user then kill the process
            og_message('error', $e->getMessage());
            return;
        }
    }

    public function get_reconciled_info()
    {
        try {
            $postData = $this->input->post();
            // $reconciledIds = explode(",", $postData['reconciled_ids']);
            $reconcileHtml = "<table border=1 class='reconciled-info-table'>";
            $withPointDiff = 0;
            if ($postData['reconciled_ids'] && ((isset($postData['bank_type']) && $postData['bank_type'] == 'bank') || (isset($postData['reconciliation_type']) && $postData['reconciliation_type'] == 'adjustment'))) {
                $sql                     = "SELECT * FROM `ledger_statement` WHERE id in (" . $postData['reconciled_ids'] . ")";
                $data['reconciled_data'] = $this->ledger_model->query_result($sql);
                $reconcileHtml .= "<tr><th>No</th><th>Date</th><th>Transaction Type</th><th>Description</th><th>Amount</th><th>Reconcilation Type</th></tr>";
                $count = 0;
                foreach ($data['reconciled_data'] as $row) {
                    $amount = ($row->transaction_type == "credit") ? $row->credit_amt : $row->debit_amt;
                    $reconcileHtml .= '<tr class="reconciled-info-class" scroll-to-id="ledger-' . $row->id . '">';

                    $reconcileHtml .= '<td>' . ++$count . '</td>';
                    $ledgerId   = $row->ledger_id;
                    $ledgerData = $this->ledger_model->Get($ledgerId);

                    if ($row->credit_date) {
                        $reconcileHtml .= '<td class="date-display-class">' . ucwords(date('m/d/Y', strtotime($row->credit_date))) . '</td>';
                    } else {
                        $reconcileHtml .= '<td class="date-display-class">' . monthName($ledgerData->month) . " " . $ledgerData->year . '</td>';
                    }

                    $reconcileHtml .= '<td>' . $row->transaction_type . '</td><td>' . $row->description . '</td><td>' . $amount . '</td><td>' . ucfirst($row->reconcile_type) . '</td>';
                    $reconcileHtml .= '</tr>';

                    if($row->with_point_diff)
                        $withPointDiff = 1;
                }
            } else if (isset($postData['bank_type']) && $postData['bank_type'] == 'check' && isset($postData['reconciled_ids']) && $postData['reconciled_ids'] != '') {
                $sql                     = "SELECT * FROM `checkbook_record` WHERE id in (" . $postData['reconciled_ids'] . ")";
                $data['reconciled_data'] = $this->ledger_model->query_result($sql);
                $reconcileHtml .= "<tr><th>No</th><th>Check number</th><th>Memo</th><th>Amount</th><th>Reconcilation Type</th></tr>";
                $count = 0;
                foreach ($data['reconciled_data'] as $row) {
                    $reconcileHtml .= '<tr class="reconciled-info-class" scroll-to-id="ledger-check-' . $row->id . '"><td>' . ++$count . '</td><td>' . $row->check_number . '</td><td>' . $row->memo . '</td><td>' . $row->amount1 . '</td><td>' . ucfirst($row->reconcile_type) . '</td></tr>';
                    if($row->with_point_diff)
                        $withPointDiff = 1;
                }
            } else {

                if (isset($postData['reconciled_ids']) && $postData['reconciled_ids'] != '') {
                    $reconcile_ids           = rtrim($postData['reconciled_ids'], ",");
                    $sql                     = "SELECT * FROM `bank_statement_entries` WHERE id in (" . $reconcile_ids . ")";
                    $data['reconciled_data'] = $this->ledger_model->query_result($sql);
                    $reconcileHtml .= "<tr><th>No</th><th>Date</th><th>Transaction Type</th><th>Check number</th><th>Description</th><th>Amount</th><th>Reconcilation Type</th></tr>";
                    $count = 0;
                    foreach ($data['reconciled_data'] as $row) {
                        $reconcileHtml .= '<tr class="reconciled-info-class" scroll-to-id="bank-' . $row->id . '"><td>' . ++$count . '</td><td>' . date('m/d/Y', strtotime($row->date)) . '</td><td>' . $row->transaction . '</td><td>' . $row->check_num . '</td><td>' . $row->description . '</td><td>' . $row->amount . '</td><td>' . ucfirst($row->reconcile_type) . '</td></tr>';
                        if($row->with_point_diff)
                            $withPointDiff = 1;
                    }
                }
            }
            $reconcileHtml .= "</table>";

            if($withPointDiff)
                $reconcileHtml = "<span class='tooltip-top-note'>Reconciled with $0.01 diff</span><br>".$reconcileHtml;
            echo json_encode(array("status" => "success", "reconcileHtml" => $reconcileHtml));
            exit;
        } catch (Exception $e) {
            //alert the user then kill the process
            og_message('error', $e->getMessage());
            return;
        }
    }

    public function find_bank_id($store_key, $month, $year, $bank_Res)
    {
        $i = 0;
        foreach ($bank_Res as $brow) {
            if ($brow->store_key == $store_key && $brow->month == $month && $brow->year == $year) {
                $bank_id = $brow->bank_id;
//                unset($bank_Res[$i]);
                return array("bank_id" => $bank_id, "status" => $brow->status, "search_index" => $i);
            }
            $i++;
        }
        return false;
    }

    public function reset($ledger_id = false, $bank_id = false)
    {
        //Remove adjustment entries
        $sql    = "DELETE FROM `ledger_statement` WHERE `reconcile_type` = 'adjustment' and `is_adjustment_entry` = 1 and ledger_id = {$ledger_id} ";
        $result = $this->checkbook_model->query_result($sql);

        if ($ledger_id &&  $bank_id) {
            $up_data                          = array();
            $up_data['is_reconcile']          = 0;
            $up_data['bank_statement_id']     = '';
            $up_data['reconcile_date']        = null;
            $up_data['is_reconciled_current'] = 0;
            $up_data['reconcile_type']        = null;

            //reset all reconcilation in ledger and bank
            $this->ledger_statement_model->reset_all_entries($ledger_id, $up_data, $bank_id);
        }

        $this->session->set_flashdata('msg_class', "success");
        $this->session->set_flashdata('msg', "Process reset successfully");
        redirect('reconcile/process?ledger_id=' . $ledger_id . '&bank_id=' . $bank_id);
    }

    public function getnotes()
    {
        $ledger_id = $this->input->post('ledger_id');
        $result    = $this->reconcile_model->getnotes($ledger_id);
        echo json_encode($result);
    }

    public function setnotes()
    {
        $notes     = $this->input->post('notes');
        $ledger_id = $this->input->post('ledger_id');
        $bank_id   = $this->input->post('bank_id');
        $result    = $this->reconcile_model->setnotes($notes, $ledger_id);
        if ($result) {
            $msg = 'Notes updated successfully!!';
        } else {
            $msg = 'Nothing change in notes!!';
        }
        echo json_encode($msg);
    }

    public function getManualReconcilationData()
    {
        $data              = [];
        $data['ledger_id'] = $this->input->post('ledger_id');
        $data['bank_id']   = $this->input->post('bank_id');
        $data['ledger']    = $this->ledger_model->Get($data['ledger_id']);

        $ignoredDescBeingReconciled = ['Federal Tax 940','Federal Tax 941','Department of Revenue','Deptartment Of Labor','City Employee Taxes','Certipay Payroll Services'];

        $sql = "SELECT ls.*,l.month,l.year FROM `ledger_statement` ls
                INNER JOIN `ledger` l ON ls.ledger_id = l.id
                INNER JOIN `bank_statement` b ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                WHERE l.store_key = " . $data['ledger']->store_key . " AND is_reconcile = 0 AND document_type != 'payroll_gross' AND l.month <= " . $data['ledger']->month . " AND ls.description NOT IN ('".implode("','", $ignoredDescBeingReconciled)."') ORDER BY ls.id desc";
        $data['unreconciled_ledger_statement_data'] = $this->checkbook_model->query_result($sql);

        $sql = "SELECT bs.*,b.month,b.year FROM `bank_statement_entries` bs
                INNER JOIN `bank_statement` b ON b.id = bs.bank_statement_id
                INNER JOIN `ledger` l ON (b.month = l.month AND b.year = l.year AND b.store_key = l.store_key)
                WHERE b.store_key = " . $data['ledger']->store_key . " AND is_reconcile = 0 AND b.month <= " . $data['ledger']->month . " ORDER BY date desc";
        $data['unreconciled_bank_statement_data'] = $this->checkbook_model->query_result($sql);

        $sql                   = "SELECT * FROM `ledger_document` WHERE key_name != 'payroll_gross'";
        $data['document_data'] = $this->checkbook_model->query_result($sql);
        echo $mr               = $this->load->view('modal/mr', $data, true);
        exit();
    }

    public function getCheckBookData()
    {
        $data              = [];
        $data['ledger_id'] = $this->input->post('ledger_id');
        $data['ledger']    = $this->ledger_model->Get($data['ledger_id']);

        $sql                      = "SELECT * FROM `checkbook_record` WHERE `ledger_id` = " . $data['ledger_id'] . " AND `is_reconcile` != 1";
        $data['checkbook_record'] = $this->checkbook_model->query_result($sql);
        echo $this->load->view('modal/check_book_modal', $data, true);
        exit();
    }

    public function addEditChecks()
    {
        
        $data              = [];
        $data['checkid']   = $this->input->post('checkid');
        $data['ledger_id'] = $this->input->post('ledger_id');
        $sql           = "SELECT * FROM `ledger` WHERE `id` = " . $data['ledger_id'];
        $ledger_data = $this->checkbook_model->query_result($sql);
        $store_key = isset($ledger_data[0]->store_key) ? $ledger_data[0]->store_key : '';
        if ($data['checkid']) {
            $sql           = "SELECT * FROM `checkbook_record` WHERE `id` = " . $data['checkid'];
            $data['check'] = $this->checkbook_model->query_result($sql);
             $data['check_no'] = '';
        }else{
            $sql = "SELECT * FROM `checkbook_record`  LEFT JOIN `ledger` ON ledger.id = checkbook_record.ledger_id WHERE ledger.store_key = ".$store_key." ORDER BY checkbook_record.id DESC LIMIT 1";
            $check_Res = $this->checkbook_model->query_result($sql);
            if (!empty($check_Res)) {
                $data['check_no'] = isset($check_Res[0]->check_number) ? $check_Res[0]->check_number + 1 : '';
            } else {
                $sql = "SELECT * FROM `admin_settings` WHERE key_name = 'check_number_starting' AND store_key=".$store_key;
                $check_Res = $this->checkbook_model->query_result($sql);
                $data['check_no'] = isset($check_Res[0]->key_value) ? $check_Res[0]->key_value : '';

            }
        }
        echo $this->load->view('modal/add_check', $data, true);
        exit();
    }

    public function submitCheck()
    {
        $checkBookData = $this->input->post();

        if ($checkBookData['id']) {
            $this->checkbook_model->edit($checkBookData['id'], $checkBookData);
        } else {
            $this->checkbook_model->add($checkBookData);
        }

        $data['ledger_id']        = $this->input->post('ledger_id');
        $data['ledger']           = $this->ledger_model->Get($data['ledger_id']);
        $sql                      = "SELECT * FROM `checkbook_record` WHERE `ledger_id` = " . $data['ledger_id'] . " and is_reconcile != 1";
        $data['checkbook_record'] = $this->checkbook_model->query_result($sql);

        updateLedgerBalance($data['ledger_id']);
        echo $this->load->view('modal/check_book_modal', $data, true);
    }

    public function deleteChecks()
    {
        $data            = [];
        $data['checkid'] = $this->input->post('checkid');
        if ($data['checkid']) {
            $data['check'] = $this->checkbook_model->Delete($data['checkid']);
        }
        exit();
    }

    public function voidChecks()
    {
        $data            = [];
        $data['checkid'] = $this->input->post('checkid');
        $amount = $this->input->post('amount');
        $memo = $this->input->post('memo');

        if ($data['checkid']) {
            $checkData                 = [];
            $checkData['memo']      = $memo."-".$amount."-void";
            $checkData['amount1']      = 0;
            $checkData['is_void']      = 1;
            $checkData['is_reconcile'] = 1;
            $data['check']             = $this->checkbook_model->Edit($data['checkid'], $checkData);

            $data['ledger_id']        = $this->input->post('ledger_id');
            $data['ledger']           = $this->ledger_model->Get($data['ledger_id']);
            $sql                 = "SELECT * FROM `checkbook_record` WHERE `ledger_id` = " . $data['ledger_id'] . " and is_reconcile != 1";
            $data['checkbook_record'] = $this->checkbook_model->query_result($sql);
            echo $this->load->view('modal/check_book_modal', $data, true);
        }
        exit();
    }

    public function lock_unlock_process()
    {
        $ledger_id           = $this->input->post('ledger_id');
        $bank_id             = $this->input->post('bank_id');
        $value               = $this->input->post('value');
        $updata['is_locked'] = $value == 1 ? 0 : 1;
        $this->ledger_model->Edit($ledger_id, $updata);
        $this->bank_statement_model->Edit($bank_id, $updata);
        if ($value == 1) {
            $this->session->set_flashdata('msg_class', "success");
            $this->session->set_flashdata('msg', "This process Locked successfully");
        } else {
            $this->session->set_flashdata('msg_class', "success");
            $this->session->set_flashdata('msg', "This process Unlocked successfully");
        }

    }

    public function refreshStatus()
    {
        $postData = $this->input->post();
        updateLedgerStatus($postData['ledger_id']);
        updateBankStatus($postData['bank_id']);
        echo json_encode(array("status" => "success"));
        exit();
    }

}
