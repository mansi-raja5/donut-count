<?php
defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';
class Statement extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ledger_model');
        $this->load->model('ledger_document_model');
        $this->load->model('ledger_statement_model');
        $this->load->model('ledger_statement_splits_model');
        $this->load->model('store_master_model');
        $this->load->model('checkbook_model');
        $this->load->model('attachment_name_setting_model');
        $this->load->model('attachment_upload_model');
        $this->load->model('bank_statement_entries_model');
        $this->load->model('ledger_credit_received_from_model');
    }

    public function index()
    {
        $data['title'] = 'Ledger List';
        if ($this->input->is_ajax_request()) {
            $ledger_Res = $this->ledger_model->Get(null, $this->input->post());
            // echo '<pre>';print_r($ledger_Res);die;
            $this->getListing($ledger_Res);
        } else {
            $data['store_list'] = $this->store_master_model->Get(null, array("status" => 'A'));
            $this->template->load('listing', 'ledger/list-ledger', $data);
        }
    }

    public function getListing($result = array())
    {
        $tableData = array();
        $storeKey  = 0;
        foreach ($result['records'] as $key => $row) {
            $action   = array();
            $action[] = anchor('statement/view/' . $row->id, 'View');
            /*if($storeKey != $row->store_key)
            {
            $max_year  = $row->year;
            $max_month = $row->month;
            }
            if ($row->month >= $max_month && $row->is_locked != 1) {
            $action[] = anchor('javascript:void(0);', 'Delete', array('data-toggle' => 'modal', 'data-id' => $row->id, 'onclick' => 'setConfirmDetails(this)', ' data-target' => '#ConfirmDeleteModal', 'data-url' => 'statement/delete/' . $row->id));
            }*/

            $tableData[$key]['srNo']            = $key + 1;
            $tableData[$key]['store_key']       = $row->store_key;
            $tableData[$key]['month']           = monthName($row->month);
            $tableData[$key]['year']            = $row->year;
            $tableData[$key]['opening_balance'] = $row->ledger_balance;
            $tableData[$key]['ending_balance']  = $row->ending_balance;
            $tableData[$key]['is_locked']       = ($row->is_locked == 1) ? "YES" : "NO";
            $tableData[$key]['action']          = implode(" | ", $action);
            $tableData[$key]['id']              = $row->id;
        }
        $getLedgerFormatRecords = $this->ledger_statement_model->getLedgerFormatRecords();
        if(!isset($key)) $key = -1;
        foreach ($getLedgerFormatRecords as $_getLedgerFormatRecords) {
            $key++;
            $store_key      = 'XXXXX';
            $action   = array();
            $action[] = anchor("statement/auto/{$store_key}/{$_getLedgerFormatRecords->month}/{$_getLedgerFormatRecords->year}", 'Ledger Format');
            $tableData[$key]['srNo']            = $key;
            $tableData[$key]['store_key']       = $store_key;
            $tableData[$key]['month']           = monthName($_getLedgerFormatRecords->month);
            $tableData[$key]['year']            = $_getLedgerFormatRecords->year;
            $tableData[$key]['opening_balance'] = 0;
            $tableData[$key]['ending_balance']  = 0;
            $tableData[$key]['is_locked']       = "NO";
            $tableData[$key]['action']          = implode(" | ", $action);
        }

        $data['data']            = $tableData;
        $data['recordsTotal']    = $result['countTotal'];
        $data['recordsFiltered'] = $result['countFiltered'];
        echo json_encode($data);
    }

    public function view($ledger_id = null)
    {
        if ($ledger_id == null) {
            $ledger_id = $this->uri->segment(3);
        }
        $data = $this->ledger_statement_model->getLedgerViewData($ledger_id);
        $data['title'] = $data['ledger']->store_key . " - " . monthName($data['ledger']->month) . " - " . $data['ledger']->year;
        $data['call_from'] = "ledger";
        $this->template->load('listing', 'ledger/view_statement', $data);
    }

    public function auto()
    {
        $storeKey       = $this->uri->segment(3);
        $monthNumber    = $this->uri->segment(4);
        $year           = $this->uri->segment(5);
        $month          = monthName($monthNumber);
        $data = $this->ledger_statement_model->getAutoLedgerViewData($storeKey, $monthNumber, $year);
        $data['store_key']  = $storeKey;
        $data['month']      = $monthNumber;
        $data['year']       = 2020;
        $data['title']      = $storeKey." - ". $month . " - ".$year;

        $ledgerExist = $this->ledger_model->Get(null, array(
            "store_key" => $storeKey,
            "month"     => $monthNumber,
            "year"      => $year));
        if ($ledgerExist['countFiltered'] > 0) {
            $data['ledger_id'] = $ledgerExist['records'][0]->id;
        }
        $this->template->load('listing', 'ledger/auto_view_statement', $data);
    }

    public function createLedgerFromAutoView()
    {
        $postData = $this->input->post();
        //ledger master entry
        $ledgerExist = $this->ledger_model->Get(null, array(
            "store_key" => $postData['ledger']['store_key'],
            "month"     => $postData['ledger']['month'],
            "year"      => $postData['ledger']['year']));

        if ($ledgerExist['countFiltered'] > 0) {
            $this->session->set_flashdata('msg_class', "failure");
            $this->session->set_flashdata('msg', "Ledger is already imported for the selected store and month and year. You can delete it create auto ledger anytime ;) ");
            redirect("statement/auto/{$postData['ledger']['store_key']}/{$postData['ledger']['month']}/{$postData['ledger']['year']}");
        }

        $ledger['filename']         = "Auto";
        $ledger['year']             = $postData['ledger']['year'];
        $ledger['store_key']        = $postData['ledger']['store_key'];
        $ledger['month']            = $postData['ledger']['month'];
        $ledger['ledger_balance']   = $postData['ledger']['ledger_balance'];
        $ledgerId = $this->ledger_model->Add($ledger);

        foreach ($postData['ledger']['credits'] as $key => $_credits) {

            //general credit entries
            $ledgerStatementData              = [];
            $ledgerStatementData['store_key'] = $postData['ledger']['store_key'];
            $ledgerStatementData['ledger_id'] = $ledgerId;
            $ledgerStatementData['credit_date'] = date('Y-m-d', strtotime($_credits['key']));
            $ledgerStatementData['description'] = 'DEPOSIT';

            $ledgerStatementData['credit_amt'] = $_credits['amt'];
            $ledgerStatementData['transaction_type'] = 'credit';
            $ledgerStatementData['document_type']    = 'general_section';
            $ledgerStatementData['created_on']    = date('Y-m-d H:i:s');

            if($ledgerStatementData['credit_amt'] != '' && $ledgerStatementData['credit_date'] != '')
                $creditId = $this->ledger_statement_model->Add($ledgerStatementData);

            //general debit entries
            if(isset($postData['ledger']['debits'][$key]))
            {
                $ledgerStatementData                        = [];
                $ledgerStatementData['store_key']           = $postData['ledger']['store_key'];
                $ledgerStatementData['ledger_id']           = $ledgerId;
                $ledgerStatementData['transaction_type']    = 'debit';
                $ledgerStatementData['document_type']       = 'general_section';
                $ledgerStatementData['description']         = $postData['ledger']['debits'][$key]['key'];
                $ledgerStatementData['debit_amt']           = $postData['ledger']['debits'][$key]['amt'];
                $ledgerStatementData['created_on']    = date('Y-m-d H:i:s');

                $credit_date = $postData['ledger']['year'].'-'.$postData['ledger']['month'].'-01';
                $ledgerStatementData['credit_date'] = date('Y-m-d', strtotime($credit_date));

                if ($creditId) {
                    $ledgerStatementData['parent_id'] = $creditId;
                }

                if($ledgerStatementData['credit_date'] != '' && $ledgerStatementData['description'] != ''){
                    $debitId = $this->ledger_statement_model->Add($ledgerStatementData);
                }
            }

            //impound entries
            if(isset($postData['ledger']['impound'][$key]) && $postData['ledger']['impound'][$key]['key'] != '' && $postData['ledger']['impound'][$key]['amt'] != '')
            {
                $ledgerStatementData                    = [];
                $ledgerStatementData['store_key']       = $postData['ledger']['store_key'];
                $ledgerStatementData['ledger_id']       = $ledgerId;
                $ledgerStatementData['document_type']   = 'impound';
                $ledgerStatementData['description']     = 'Impound';
                $ledgerStatementData['credit_date']     = date('Y-m-d', strtotime($postData['ledger']['impound'][$key]['key']));
                $ledgerStatementData['created_on']    = date('Y-m-d H:i:s');

                $impoundAmt = $postData['ledger']['impound'][$key]['amt'];
                if ($impoundAmt < 0) {
                    //found
                    $ledgerStatementData['transaction_type'] = 'credit';
                    $ledgerStatementData['credit_amt']       = getAmountFromString($impoundAmt * -1);
                } else {
                    $ledgerStatementData['transaction_type'] = 'debit';
                    $ledgerStatementData['debit_amt']        = getAmountFromString($impoundAmt);
                }
                if ($creditId) {
                    $ledgerStatementData['parent_id'] = $creditId;
                }

                if($ledgerStatementData['credit_date'] != '' && $ledgerStatementData['description'] != ''){
                    $debitId = $this->ledger_statement_model->Add($ledgerStatementData);
                }
            }

            //payroll net entries
            if(isset($postData['ledger']['payroll_net'][$key]))
            {
                $ledgerStatementData                        = [];
                $ledgerStatementData['store_key']           = $postData['ledger']['store_key'];
                $ledgerStatementData['ledger_id']           = $ledgerId;
                $ledgerStatementData['document_type']       = 'payroll_net';
                $ledgerStatementData['description']         = "Payroll Net";
                $ledgerStatementData['debit_amt']           = $postData['ledger']['payroll_net'][$key]['amt'];
                $ledgerStatementData['created_on']    = date('Y-m-d H:i:s');

                $credit_date = $postData['ledger']['payroll_net'][$key]['key'] .''. $postData['ledger']['year'];
                $ledgerStatementData['credit_date'] = date('Y-m-d', strtotime($credit_date));

                if($ledgerStatementData['credit_date'] != '' && $ledgerStatementData['description'] != ''){
                    $debitId = $this->ledger_statement_model->Add($ledgerStatementData);
                }
            }

            //payroll gross entries
            if(isset($postData['ledger']['payroll_gross'][$key]))
            {
                $ledgerStatementData                        = [];
                $ledgerStatementData['store_key']           = $postData['ledger']['store_key'];
                $ledgerStatementData['ledger_id']           = $ledgerId;
                $ledgerStatementData['document_type']       = 'payroll_gross';
                $ledgerStatementData['description']         = "Payroll Gross";
                $ledgerStatementData['debit_amt']           = $postData['ledger']['payroll_gross'][$key]['amt'];
                $ledgerStatementData['created_on']    = date('Y-m-d H:i:s');

                $credit_date = $postData['ledger']['payroll_gross'][$key]['key'] .''. $postData['ledger']['year'];
                $ledgerStatementData['credit_date'] = date('Y-m-d', strtotime($credit_date));

                if($ledgerStatementData['credit_date'] != '' && $ledgerStatementData['description'] != ''){
                    $debitId = $this->ledger_statement_model->Add($ledgerStatementData);
                }
            }

            //roy entries
            if(isset($postData['ledger']['roy'][$key]))
            {
                $ledgerStatementData                        = [];
                $ledgerStatementData['store_key']           = $postData['ledger']['store_key'];
                $ledgerStatementData['ledger_id']           = $ledgerId;
                $ledgerStatementData['document_type']       = 'roy_adv';
                $ledgerStatementData['description']         = "Roy Adv";
                $ledgerStatementData['debit_amt']           = $postData['ledger']['roy'][$key]['amt'];
                $ledgerStatementData['created_on']    = date('Y-m-d H:i:s');

                $credit_date = $postData['ledger']['roy'][$key]['key'] .''. $postData['ledger']['year'];
                $ledgerStatementData['credit_date'] = date('Y-m-d', strtotime($credit_date));

                if($ledgerStatementData['credit_date'] != '' && $ledgerStatementData['description'] != ''){
                    $debitId = $this->ledger_statement_model->Add($ledgerStatementData);
                }
            }

            //donut entries
            if(isset($postData['ledger']['donut'][$key]))
            {
                $ledgerStatementData                        = [];
                $ledgerStatementData['store_key']           = $postData['ledger']['store_key'];
                $ledgerStatementData['ledger_id']           = $ledgerId;
                $ledgerStatementData['document_type']       = 'donut_purchases';
                $ledgerStatementData['description']         = "Donut";
                $ledgerStatementData['debit_amt']           = $postData['ledger']['donut'][$key]['amt'];
                $ledgerStatementData['created_on']    = date('Y-m-d H:i:s');

                $credit_date = $postData['ledger']['donut'][$key]['key'] .''. $postData['ledger']['year'];
                $ledgerStatementData['credit_date'] = date('Y-m-d', strtotime($credit_date));

                if($ledgerStatementData['credit_date'] != '' && $ledgerStatementData['description'] != ''){
                    $debitId = $this->ledger_statement_model->Add($ledgerStatementData);
                }
            }

            //dcp_efts entries
            if(isset($postData['ledger']['dcp'][$key]))
            {
                $ledgerStatementData                        = [];
                $ledgerStatementData['store_key']           = $postData['ledger']['store_key'];
                $ledgerStatementData['ledger_id']           = $ledgerId;
                $ledgerStatementData['document_type']       = 'dcp_efts';
                $ledgerStatementData['description']         = "Dcp Efts";
                $ledgerStatementData['debit_amt']           = $postData['ledger']['dcp'][$key]['amt'];
                $ledgerStatementData['created_on']    = date('Y-m-d H:i:s');

                $credit_date = $postData['ledger']['dcp'][$key]['key'] .''. $postData['ledger']['year'];
                $ledgerStatementData['credit_date'] = date('Y-m-d', strtotime($credit_date));

                if($ledgerStatementData['credit_date'] != '' && $ledgerStatementData['description'] != ''){
                    $debitId = $this->ledger_statement_model->Add($ledgerStatementData);
                }
            }

            //dean_foods entries
            if(isset($postData['ledger']['dean'][$key]))
            {
                $ledgerStatementData                        = [];
                $ledgerStatementData['store_key']           = $postData['ledger']['store_key'];
                $ledgerStatementData['ledger_id']           = $ledgerId;
                $ledgerStatementData['document_type']       = 'dean_foods';
                $ledgerStatementData['description']         = "DEAN FOODS";
                $ledgerStatementData['debit_amt']           = $postData['ledger']['dean'][$key]['amt'];
                $ledgerStatementData['created_on']    = date('Y-m-d H:i:s');

                $credit_date = $postData['ledger']['dean'][$key]['key'] .''. $postData['ledger']['year'];
                $ledgerStatementData['credit_date'] = date('Y-m-d', strtotime($credit_date));

                if($ledgerStatementData['credit_date'] != '' && $ledgerStatementData['description'] != ''){
                    $debitId = $this->ledger_statement_model->Add($ledgerStatementData);
                }
            }

            //add extra_credit  entries
            if(isset($postData['ledger']['extra_credit'][$key]))
            {

                $ledgerStatementData              = [];
                $ledgerStatementData['store_key'] = $postData['ledger']['store_key'];
                $ledgerStatementData['ledger_id'] = $ledgerId;
                $ledgerStatementData['credit_date'] = '';
                $ledgerStatementData['description'] = $postData['ledger']['extra_credit'][$key]['key'];

                $ledgerStatementData['credit_amt'] = $postData['ledger']['extra_credit'][$key]['amt'];
                $ledgerStatementData['transaction_type'] = 'credit';
                $ledgerStatementData['document_type']    = 'general_section';
                $ledgerStatementData['created_on']    = date('Y-m-d H:i:s');

                if($ledgerStatementData['credit_amt'] != '' && $ledgerStatementData['description'] != '')
                    $creditId = $this->ledger_statement_model->Add($ledgerStatementData);
            }

            //add credit_card  entry
            if(isset($postData['ledger']['credit_card'][$key]))
            {

                $ledgerStatementData              = [];
                $ledgerStatementData['store_key'] = $postData['ledger']['store_key'];
                $ledgerStatementData['ledger_id'] = $ledgerId;
                $ledgerStatementData['credit_date'] = '';
                $ledgerStatementData['description'] = 'Credit Card Credits';

                $ledgerStatementData['credit_amt'] = $postData['ledger']['credit_card'][$key]['amt'];
                $ledgerStatementData['transaction_type'] = 'credit';
                $ledgerStatementData['document_type']    = 'general_section';
                $ledgerStatementData['created_on']    = date('Y-m-d H:i:s');

                if($ledgerStatementData['credit_amt'] != '' && $ledgerStatementData['description'] != '')
                    $creditId = $this->ledger_statement_model->Add($ledgerStatementData);
            }
        }
        //checkbook record
        if(isset($postData['ledger']['checkbook_record']))
        {
            foreach ($postData['ledger']['checkbook_record'] as $_checkbook_record) {
                $checkbookRecordData                    = [];
                $checkbookRecordData['ledger_id']       = $ledgerId;
                $checkbookRecordData['payble_to']       = $_checkbook_record['bc_payable'];
                $checkbookRecordData['check_number']    = $_checkbook_record['bc_check_no'];
                $checkbookRecordData['memo']            = $_checkbook_record['bc_memo'];
                $checkbookRecordData['amount1']         = $_checkbook_record['bc_amount'];
                $checkbookRecordData['created_on']      = date('Y-m-d H:i:s');

                if($checkbookRecordData['check_number'] != '')
                    $creditId = $this->checkbook_model->Add($checkbookRecordData);
            }

        }
        redirect("statement/auto/{$postData['ledger']['store_key']}/{$postData['ledger']['month']}/{$postData['ledger']['year']}");
    }

    public function delete($ledger_id = null)
    {
        $ledger_Res = $this->ledger_model->Get($ledger_id);
        $month      = isset($ledger_Res->month) ? $ledger_Res->month : "";
        $year       = isset($ledger_Res->year) ? $ledger_Res->year : "";
        $status     = isset($ledger_Res->status) ? $ledger_Res->status : "";
        if ($status != 'unreconcile') {
            $this->load->model('bank_statement_model');
            $bank_Res     = $this->bank_statement_model->Delete_where(array("month" => $month, "year" => $year));
            $ledger_datas = $this->ledger_statement_model->Get(null, array("is_reconciled_current" => 2));
            if (isset($ledger_datas['records']) && $ledger_datas['records']) {
                foreach ($ledger_datas['records'] as $lRow) {
                    $bank_statement_arr = explode(",", $lRow->bank_statement_id);
                    for ($i = 0; $i < count($bank_statement_arr); $i++) {
                        $bank_statement_ids[] = $bank_statement_arr[$i];
                    }
                }
            }
            if (isset($bank_statement_ids) && !empty($bank_statement_ids)) {
                $up_data                        = array();
                $up_data['is_void']             = 0;
                $up_data['is_reconcile']        = 0;
                $up_data['ledger_statement_id'] = '';
                $up_data['reconcile_type']      = '';
                $this->bank_statement_entries_model->Edit($bank_statement_ids, $up_data);
            }
        } else if ($status == 'unreconcile') {
            $this->load->model('bank_statement_model');
            $this->bank_statement_model->Delete_where(array("month" => $month, "year" => $year));
            $this->ledger_model->Delete($ledger_id);
        }
        redirect('statement');
    }

    public function import()
    {
        $data['title']      = "Import Ledger Statement";
        $data['store_list'] = $this->store_master_model->Get(null, array("status" => 'A'));
        $this->template->load('listing', 'ledger/import_ledger', $data);
    }

    public function import_ledger()
    {
        $selectedStoreId = isset($_POST['store_id']) ? $_POST['store_id'] : "";
        $selectedYear    = isset($_POST['year']) ? $_POST['year'] : "";
        $selectedMonth   = isset($_POST['month']) ? $_POST['month'] : "";

        $datesCellNumber = ['A4', 'A5', 'A6', 'A7', 'A8', 'A9', 'A10', 'A11', 'A12', 'A13', 'A14', 'A15', 'A16', 'A17', 'A18', 'A19', 'A20', 'A21', 'A22', 'A23', 'A24', 'A25', 'A26', 'A27', 'A28', 'A29', 'A30', 'A31', 'A32', 'A33', 'A34', 'F4', 'F5', 'F6', 'F7', 'F8', 'E36', 'E37', 'E38', 'E39', 'E40', 'E41', 'E44', 'E45', 'E46', 'E47', 'E48', 'E49', 'E50', 'E51', 'E52', 'E53', 'E54', 'E55', 'E56', 'E57', 'E58', 'E59', 'E60', 'E61', 'E64', 'E65', 'E66', 'E67', 'E68', 'C45', 'C46', 'C47', 'C48', 'C49', 'C52', 'C53', 'C54', 'C55', 'C56', 'C59', 'C60', 'C61', 'C62', 'C63', 'C64', 'C65', 'C66', 'C67', 'C68'];

        if (!empty($_FILES['import_file']['name'])) {

            $file_name = $_FILES['import_file']['name'];
            //fatch year from filename
            $fileNameAry  = explode("DDBR Ledger", $file_name);
            $yearFromFile = substr($fileNameAry[1], 2, 2);

            if (sanitize($selectedYear) != sanitize("20" . $yearFromFile)) {
                $this->session->set_flashdata('msg_class', "failure");
                $this->session->set_flashdata('msg', "Wrong File! Selected year does not match with the file year.");
                redirect('statement/import');
            }

            $targetPath = FCPATH . "/files_upload/ledger/";
            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0777, true);
            }

            $file_name  = $selectedStoreId . "_" . $file_name;
            $tempFile   = $_FILES['import_file']['tmp_name'];
            $readerfile = "files_upload/ledger/" . $file_name;
            $targetFile = $targetPath . $file_name;
            if (move_uploaded_file($tempFile, $targetFile)) {
                $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);
                try {
                    if ($extension == 'csv') {
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                        $reader->setLoadAllSheets();
                        $reader->setReadDataOnly(true);
                        $spreadsheet = $reader->load($targetFile);
                    } elseif ($extension == 'xlsx') {
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                        $reader->setReadDataOnly(true);
                        $reader->setLoadAllSheets();
                        $spreadsheet = $reader->load($targetFile);
                    } else {
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                        $reader->setLoadAllSheets();
                        $reader->setReadDataOnly(true);
                        $spreadsheet = $reader->load($targetFile);
                    }

                    $worksheetcount = $spreadsheet->getSheetCount();
                    //$i is a month number
                    for ($i = 0; $i < $worksheetcount; $i++) {
                        if ($i > 12) {
                            continue; // code is done till december sheet only
                        }

                        $currentworksheet = $spreadsheet->getSheet($i);
                        //get only the Cell Collection
                        if ($i == 0) {
                            //get store key and month details
                            $store_key = $currentworksheet->getCellByColumnAndRow(4, 3)->getValue();
                            if (sanitize($store_key) != sanitize($selectedStoreId)) {
                                $this->session->set_flashdata('msg_class', "failure");
                                $this->session->set_flashdata('msg', "Wrong File! Selected store does not match with the file store number.");
                                redirect('statement/import');
                            }
                            continue;
                        } else {

                            if ($selectedMonth != "all" && $selectedMonth != $i) {
                                continue;
                            }
                            //ledger master entry
                            $ledgerExist = $this->ledger_model->Get(null, array(
                                "store_key" => $store_key,
                                "month"     => $i, //$i is a month number
                                "year"      => $selectedYear));

                            if ($ledgerExist['countFiltered'] > 0) {
                                $this->session->set_flashdata('msg_class', "failure");
                                $this->session->set_flashdata('msg', "Ledger is already imported for the selected store and month and year");
                                continue;
                            }
                            foreach ($currentworksheet->getRowIterator() as $key => $row) {
                                $ledgerData[$row->getRowIndex() - 1] = [];
                                foreach ($row->getCellIterator() as $cell) {
                                    /*if($cell->getCoordinate() == 'F4')
                                    {
                                    echo "<br>".$cell->getCalculatedValue();
                                    echo "<br>".$cell->getValue();
                                    echo "<br>".$cell->getFormattedValue();
                                    $unixTimestamp = ($cell->getCalculatedValue() - 25569) * 86400;
                                    echo "<br>".date('d F Y', $unixTimestamp);
                                    die;
                                    }*/
                                    // if(strpos($cell->getValue(),"=DATE(") !== false)
                                    if (trim($cell->getCalculatedValue()) != "") {
                                        if (in_array($cell->getCoordinate(), $datesCellNumber)) {
                                            $unixTimestamp                                           = ($cell->getCalculatedValue() - 25569) * 86400;
                                            $ledgerData[$row->getRowIndex() - 1][$cell->getColumn()] = date('d F Y', $unixTimestamp);
                                        } else {
                                            $ledgerData[$row->getRowIndex() - 1][$cell->getColumn()] = $cell->getCalculatedValue();
                                        }
                                    }
                                }
                            }

                            $ledger                      = [];
                            $updateLedger                = [];
                            $count                       = -1;
                            $isImpountEntry              = 1;
                            $impoundSectionCommentStarts = 0;
                            $isDonutStarts               = 0;
                            $isPayrollNetStarts          = 0;
                            $isPayrollGrossStarts        = 0;
                            $isRoyAdvStarts              = 0;
                            $isDeanFoodStarts            = 0;
                            $isDcpEftsStarts             = 0;
                            $isCheckboardStarts          = 0;
                            $splitDebitId                = 0;

                            $ledger['year']     = sanitize($selectedYear);
                            $ledger['filename'] = $readerfile;

                            foreach ($ledgerData as $key => $_ledgerData) {
                                $creditId = 0;
                                if ($key == 1) {
                                    //A column section
                                    if (isset($_ledgerData['A'])) {
                                        if (sanitize($_ledgerData['A']) == sanitize("LEDGER STATEMENT")) {
                                            continue;
                                        } elseif (sanitize($_ledgerData['A']) == sanitize("STORE #")) {
                                            $storeKey = $ledger['store_key'] = $_ledgerData['B'];
                                        }
                                    }

                                    //F column section
                                    if (isset($_ledgerData['F']) && $_ledgerData['E']) {
                                        if (sanitize($_ledgerData['F']) == sanitize("Balance")) {
                                            $ledger['ledger_balance'] = getAmountFromString($_ledgerData['E']);
                                        }
                                    }

                                    //C column section
                                    if (isset($_ledgerData['C'])) {
                                        if (sanitize($_ledgerData['C']) == sanitize("MONTH")) {
                                            $ledger['month'] = monthNumber(trim($_ledgerData['D']));
                                        }
                                    }
                                    $ledgerId = $this->ledger_model->Add($ledger);
                                } elseif ($key > 2 && $key < 75) {
                                    //GENERAL SECTION
                                    if ($key < 34) {
                                        //ledger statement credit entry start
                                        $ledgerStatementData              = [];
                                        $ledgerStatementData['store_key'] = $store_key;
                                        $ledgerStatementData['ledger_id'] = $ledgerId;

                                        $ledgerStatementData['credit_date'] = isset($_ledgerData['A']) ? date('Y-m-d', strtotime($_ledgerData['A'])) : '';
                                        $ledgerStatementData['description'] = 'DEPOSIT';

                                        $creditAmt                         = isset($_ledgerData['B']) ? $_ledgerData['B'] : '';
                                        $ledgerStatementData['credit_amt'] = getAmountFromString($creditAmt);

                                        $ledgerStatementData['transaction_type'] = 'credit';
                                        $ledgerStatementData['document_type']    = 'general_section';

                                        if($ledgerStatementData['credit_amt'] != '' && $ledgerStatementData['credit_date'] != '')
                                            $creditId = $this->ledger_statement_model->Add($ledgerStatementData);

                                        $ledgerStatementData = []; //clear array
                                    } elseif ($key >= 34 && $isCheckboardStarts != 1) {
                                        // To cover credit section after 31st to checkbook record
                                        //update ladger Data
                                        if (isset($_ledgerData['A']) && sanitize($_ledgerData['A']) == sanitize('TOTAL CREDITS')) {
                                            $updateLedger['final_credit_total'] = getAmountFromString($_ledgerData['B']);
                                        } elseif (isset($_ledgerData['A']) && sanitize($_ledgerData['A']) == sanitize('TOTAL:') && $key == 34) {
                                            $updateLedger['general_credit_total'] = getAmountFromString($_ledgerData['B']);
                                        } elseif (((isset($_ledgerData['A']) && sanitize($_ledgerData['A']) != '') || (isset($_ledgerData['B']) && sanitize($_ledgerData['B']) != '')) && sanitize($_ledgerData['A']) != sanitize('TOTAL:') && sanitize($_ledgerData['A']) != sanitize('CHECKBOOK RECORD')) {
                                            //ledger statement credit entry start
                                            $ledgerStatementData              = [];
                                            $ledgerStatementData['store_key'] = $storeKey;
                                            $ledgerStatementData['ledger_id'] = $ledgerId;

                                            $ledgerStatementData['description'] = trim($_ledgerData['A']);

                                            $creditAmt                         = isset($_ledgerData['B']) ? $_ledgerData['B'] : '';
                                            $ledgerStatementData['credit_amt'] = getAmountFromString($creditAmt);

                                            $ledgerStatementData['transaction_type'] = 'credit';
                                            $ledgerStatementData['document_type']    = 'general_section';
                                            $credit_date = $ledger['year'].'-'.$ledger['month'].'-01';
                                            $ledgerStatementData['credit_date'] = date('Y-m-d', strtotime($credit_date));

                                            if ($ledgerStatementData['credit_amt'] || $ledgerStatementData['description']) {
                                                // echo "<pre>";
                                                // echo ($ledgerStatementData['description']);
                                                // echo "===";
                                                // echo ($ledgerStatementData['credit_amt']);
                                                // echo "<br>";
                                                $creditId = $this->ledger_statement_model->Add($ledgerStatementData);
                                            }
                                            $ledgerStatementData = []; //clear array
                                        }
                                    }

                                    if ($isCheckboardStarts != 1) {
                                        //ledger statement debit entry start
                                        $ledgerStatementData              = [];
                                        $ledgerStatementData['store_key'] = $storeKey;
                                        $ledgerStatementData['ledger_id'] = $ledgerId;

                                        $description = isset($_ledgerData['C']) ? trim($_ledgerData['C']) : '';

                                        $debitAmt                         = isset($_ledgerData['D']) ? $_ledgerData['D'] : '';
                                        $ledgerStatementData['debit_amt'] = getAmountFromString($debitAmt);

                                        $ledgerStatementData['transaction_type'] = 'debit';

                                        if ($isPayrollNetStarts == 1) {
                                            $ledgerStatementData['document_type']  = 'payroll_net';
                                            $ledgerStatementData['description']    = "Payroll Net";
                                            $ledgerStatementData['credit_date']    = isset($_ledgerData['C']) ? date('Y-m-d', strtotime($_ledgerData['C'])) : '';
                                        } elseif ($isPayrollGrossStarts == 1) {
                                            $ledgerStatementData['document_type']    = 'payroll_gross';
                                            $ledgerStatementData['description']      = "Payroll Gross";
                                            $ledgerStatementData['credit_date']      = isset($_ledgerData['C']) ? date('Y-m-d', strtotime($_ledgerData['C'])) : '';
                                        } elseif ($isRoyAdvStarts == 1) {
                                            $ledgerStatementData['document_type'] = 'roy_adv';
                                            $ledgerStatementData['description']   = "Roy Adv";
                                            $ledgerStatementData['credit_date']   = isset($_ledgerData['C']) ? date('Y-m-d', strtotime($_ledgerData['C'])) : '';
                                        } else {
                                            $ledgerStatementData['document_type'] = 'general_section';
                                            $ledgerStatementData['description']   = isset($_ledgerData['C']) ? trim($_ledgerData['C']) : '';
                                            $credit_date = $ledger['year'].'-'.$ledger['month'].'-01';
                                            $ledgerStatementData['credit_date'] = date('Y-m-d', strtotime($credit_date));
                                        }

                                        if ($creditId) {
                                            $ledgerStatementData['parent_id'] = $creditId;
                                        }

                                        if (($ledgerStatementData['debit_amt'] || sanitize($description)) && sanitize($description) != sanitize('Total:') && sanitize($description) != sanitize('Total DEBITS') && sanitize($description) != sanitize('PAYROLL NET') && sanitize($description) != sanitize('PAYROLL GROSS') && sanitize($description) != sanitize('ROY. & ADV. (First BR; Second Dunkin)')) {
                                            // echo "<pre>";
                                            // echo ($ledgerStatementData['description']);
                                            // echo "===";
                                            // echo ($ledgerStatementData['debit_amt']);
                                            // echo "<br>";
                                            $debitId = $this->ledger_statement_model->Add($ledgerStatementData);
                                        }

                                        $ledgerStatementData = []; //clear array
                                    }
                                    //ledger Impound entry start
                                    if ($isImpountEntry) {
                                        if (isset($_ledgerData['E']) && (sanitize($_ledgerData['E']) == sanitize('Comments'))) {
                                            $isImpountEntry              = 0;
                                            $impoundSectionCommentStarts = 1;
                                            continue; //skip entries when comment label arrives
                                        }
                                        $ledgerStatementData              = [];
                                        $ledgerStatementData['store_key'] = $storeKey;
                                        $ledgerStatementData['ledger_id'] = $ledgerId;

                                        $impoundAmt = isset($_ledgerData['E']) ? $_ledgerData['E'] : '';

                                        $ledgerStatementData['description'] = 'Impound';
                                        $ledgerStatementData['credit_date'] = isset($_ledgerData['F']) ? date('Y-m-d', strtotime($_ledgerData['F'])) : '';
                                        $ledgerStatementData['credit_date'] = isset($_ledgerData['F']) ? date('Y-m-d', strtotime(str_replace("-", "/", $_ledgerData['F']))) : '';

                                        $debitAmt                         = isset($_ledgerData['E']) ? $_ledgerData['E'] : '';
                                        $ledgerStatementData['debit_amt'] = $ledgerStatementData['credit_amt'] = '';

                                        if ($impoundAmt < 0) {
                                            //found
                                            $ledgerStatementData['transaction_type'] = 'credit';
                                            $ledgerStatementData['credit_amt']       = getAmountFromString($impoundAmt * -1);
                                        } else {
                                            $ledgerStatementData['transaction_type'] = 'debit';
                                            $ledgerStatementData['debit_amt']        = getAmountFromString($impoundAmt);
                                        }

                                        $ledgerStatementData['document_type']  = 'impound';
                                        $ledgerStatementData['parent_id']      = $creditId;

                                        if ($ledgerStatementData['debit_amt'] != '' || $ledgerStatementData['debit_amt'] != '') {
                                            $this->ledger_statement_model->Add($ledgerStatementData);
                                        }

                                        $ledgerStatementData = []; //clear array
                                    }

                                    //ledger_statement_splits enties start
                                    if ($impoundSectionCommentStarts) {
                                        if (isset($_ledgerData['E']) && (sanitize($_ledgerData['E']) == sanitize('DONUT PURCHASES from CML'))) {
                                            $impoundSectionCommentStarts = 0;
                                            $isDonutStarts               = 1;
                                            $isDcpEftsStarts             = 0;
                                            $isDeanFoodStarts            = 0;
                                            continue; //skip entries when donut label arrives
                                        }

                                        $splitDescription = isset($_ledgerData['E']) ? $_ledgerData['E'] : '';

                                        //separation of each different splits statement wise is blank
                                        if ($splitDebitId == 0 && $splitDescription != '') {
                                            $splitDebitId = $debitId;
                                        }

                                        if (sanitize($splitDescription) != "") {
                                            $splitData['ledger_id']     = $ledgerId;
                                            $splitData['statement_id']  = $splitDebitId; //debit entry id
                                            $splitData['description']   = $splitDescription;
                                            $splitDescriptionAry        = explode(':', $splitDescription);
                                            $splitData['amount']        = (isset($splitDescriptionAry[1])) ? getAmountFromString($splitDescriptionAry[1]) : 0;
                                            $splitData['document_type'] = 'general_section';
                                            $splitId                    = $this->ledger_statement_splits_model->Add($splitData);

                                            // echo "<pre>";
                                            // print_r($splitData);
                                            // exit;
                                        }

                                        if (isset($_ledgerData['G']) && $_ledgerData['G'] == '*') {
                                            $splitDebitId = 0;
                                        }
                                    }

                                    //e column total entry
                                    if (isset($_ledgerData['E'])) {
                                        // echo $key . "==" . $_ledgerData['E'] . "==" . $isDcpEftsStarts;
                                        // echo "<br>";
                                        if (sanitize($_ledgerData['E']) == sanitize('Total:') && $isDonutStarts == 1 && $isDcpEftsStarts == 0 && $isDeanFoodStarts == 0
                                        ) {
                                            $updateLedger['donut_total'] = getAmountFromString($_ledgerData['F']);
                                        } elseif (sanitize($_ledgerData['E']) == sanitize('Total:') && $isDonutStarts == 0 && $isDcpEftsStarts == 1 && $isDeanFoodStarts == 0
                                        ) {
                                            $updateLedger['dcp_total'] = getAmountFromString($_ledgerData['F']);
                                        } elseif (sanitize($_ledgerData['E']) == sanitize('Total:') && $isDonutStarts == 0 && $isDcpEftsStarts == 0 && $isDeanFoodStarts == 1
                                        ) {
                                            $updateLedger['dean_total'] = getAmountFromString($_ledgerData['F']);
                                        } elseif (sanitize($_ledgerData['E']) == sanitize('TOTAL FOOD')) {
                                            $updateLedger['food_total'] = getAmountFromString($_ledgerData['F']);
                                        } elseif (sanitize($_ledgerData['E']) == sanitize('Balance C/F')) {
                                            $updateLedger['balance_cf'] = getAmountFromString($_ledgerData['F']);
                                        }
                                    }

                                    //ledger donut purchases from CML entries start
                                    if ($isDonutStarts || $isDeanFoodStarts || $isDcpEftsStarts) {
                                        if (isset($_ledgerData['E']) && (sanitize($_ledgerData['E']) == sanitize('Total:')) && $isDonutStarts == 1 && $isDeanFoodStarts == 0 && $isDcpEftsStarts == 0) {
                                            $isDonutStarts = 0;
                                            //continue; //skip entries when donut label arrives
                                        } elseif (isset($_ledgerData['E']) && (sanitize($_ledgerData['E']) == sanitize('Total:')) && $isDonutStarts == 0 && $isDeanFoodStarts == 1 && $isDcpEftsStarts == 0) {
                                            $isDeanFoodStarts = 0;
                                            //continue; //skip entries when donut label arrives
                                        } elseif (isset($_ledgerData['E']) && (sanitize($_ledgerData['E']) == sanitize('Total:')) && $isDonutStarts == 0 && $isDeanFoodStarts == 0 && $isDcpEftsStarts == 1) {
                                            $isDcpEftsStarts = 0;
                                            //continue; //skip entries when donut label arrives
                                        }

                                        $donutAmt = isset($_ledgerData['F']) ? $_ledgerData['F'] : '';

                                        if (sanitize($donutAmt) != "") {
                                            $ledgerStatementData                          = [];
                                            $ledgerStatementData['store_key']             = $storeKey;
                                            $ledgerStatementData['ledger_id']             = $ledgerId;

                                            if ($isDonutStarts == 1) {
                                                $ledgerStatementData['document_type'] = 'donut_purchases';
                                                $ledgerStatementData['description']   = 'Donut';
                                            } elseif ($isDcpEftsStarts == 1) {
                                                $ledgerStatementData['document_type'] = 'dcp_efts';
                                                $ledgerStatementData['description']   = 'Dcp Efts';
                                            } elseif ($isDeanFoodStarts == 1) {
                                                $ledgerStatementData['document_type'] = 'dean_foods';
                                                $ledgerStatementData['description']   = 'Dean Foods';
                                            }

                                            $ledgerStatementData['credit_date'] = isset($_ledgerData['E']) ? date('Y-m-d', strtotime($_ledgerData['E'])) : '';


                                            if ($donutAmt < 0) {
                                                //found
                                                $ledgerStatementData['transaction_type'] = 'credit';
                                                $ledgerStatementData['credit_amt']       = getAmountFromString($donutAmt * -1);
                                            } else {
                                                $ledgerStatementData['transaction_type'] = 'debit';
                                                $ledgerStatementData['debit_amt']        = getAmountFromString($donutAmt);
                                            }

                                            if (isset($ledgerStatementData['description']) && sanitize($ledgerStatementData['description']) != sanitize('Total:')) {
                                                $this->ledger_statement_model->Add($ledgerStatementData);
                                            }
                                            $ledgerStatementData = []; //clear array
                                        }
                                    }

                                    //c column total entry
                                    if (isset($_ledgerData['C'])) {
                                        if (sanitize($_ledgerData['C']) == sanitize('Total:') && $key == 42) {
                                            $updateLedger['general_debit_total'] = getAmountFromString($_ledgerData['D']);
                                        } elseif (sanitize($_ledgerData['C']) == sanitize('TOTAL DEBITS') && $key == 69) {
                                            $updateLedger['final_debit_total'] = getAmountFromString($_ledgerData['D']);
                                        } elseif (sanitize($_ledgerData['C']) == sanitize('Total:') && $isPayrollNetStarts == 1 && $isPayrollGrossStarts == 0 && $isRoyAdvStarts == 0
                                        ) {
                                            $updateLedger['payroll_net_total'] = getAmountFromString($_ledgerData['D']);
                                        } elseif (sanitize($_ledgerData['C']) == sanitize('Total:') && $isPayrollNetStarts == 0 && $isPayrollGrossStarts == 1 && $isRoyAdvStarts == 0
                                        ) {
                                            $updateLedger['payroll_gross_total'] = getAmountFromString($_ledgerData['D']);
                                        } elseif (sanitize($_ledgerData['C']) == sanitize('Total:') && $isPayrollNetStarts == 0 && $isPayrollGrossStarts == 0 && $isRoyAdvStarts == 1
                                        ) {
                                            $updateLedger['roy_total'] = getAmountFromString($_ledgerData['D']);
                                        }
                                    }

                                    //Payroll net, Payrollgross, roy entries flag
                                    if (isset($_ledgerData['C']) && isset($_ledgerData['D'])) {
                                        if ((sanitize($_ledgerData['C']) == sanitize('PAYROLL NET')) && (sanitize($_ledgerData['D']) == sanitize('DOLLAR AMT.'))
                                        ) {
                                            $isPayrollNetStarts   = 1;
                                            $isPayrollGrossStarts = 0;
                                            $isRoyAdvStarts       = 0;
                                        } elseif ((sanitize($_ledgerData['C']) == sanitize('PAYROLL GROSS')) && (sanitize($_ledgerData['D']) == sanitize('DOLLAR AMT.'))) {
                                            $isPayrollNetStarts   = 0;
                                            $isPayrollGrossStarts = 1;
                                            $isRoyAdvStarts       = 0;
                                        } elseif ((sanitize($_ledgerData['C']) == sanitize('ROY. & ADV. (First BR; Second Dunkin)')) && (sanitize($_ledgerData['D']) == sanitize('DOLLAR AMT.'))) {
                                            $isPayrollNetStarts   = 0;
                                            $isPayrollGrossStarts = 0;
                                            $isRoyAdvStarts       = 1;
                                        }
                                    }
                                    if (isset($_ledgerData['A']) && sanitize($_ledgerData['A']) == sanitize('CHECKBOOK RECORD')) {
                                        $isPayrollNetStarts   = 0;
                                        $isPayrollGrossStarts = 0;
                                        $isRoyAdvStarts       = 0;
                                        $isCheckboardStarts   = 1;
                                    }

                                    //donut, dcpeft, dean entries flag
                                    if (isset($_ledgerData['E']) && isset($_ledgerData['F'])) {
                                        if ((sanitize($_ledgerData['E']) == sanitize('DCP EFTS')) && (sanitize($_ledgerData['F']) == sanitize('DOLLAR AMT.'))) {
                                            $isImpountEntry   = 0;
                                            $isDonutStarts    = 0;
                                            $isDcpEftsStarts  = 1;
                                            $isDeanFoodStarts = 0;
                                        } elseif ((sanitize($_ledgerData['E']) == sanitize('DEAN FOODS')) && (sanitize($_ledgerData['F']) == sanitize('DOLLAR AMT.'))) {
                                            $isImpountEntry   = 0;
                                            $isDonutStarts    = 0;
                                            $isDcpEftsStarts  = 0;
                                            $isDeanFoodStarts = 1;
                                        }
                                    }
                                } elseif ($key >= 76) {
                                    if (isset($_ledgerData['E']) && sanitize($_ledgerData['E']) == sanitize('Ending Balance')) {
                                        $updateLedger['ending_balance'] = getAmountFromString($_ledgerData['F']);
                                        $isCheckboardStarts             = 0;
                                    }

                                    if ($isCheckboardStarts == 1) {
                                        if(isset($_ledgerData['B']) && $_ledgerData['B'] != '' && $_ledgerData['B'] != 0)
                                        {
                                            $checkbook                 = [];
                                            $checkbook['ledger_id']    = $ledgerId;
                                            $checkbook['payble_to']    = isset($_ledgerData['A']) ? $_ledgerData['A'] : '';
                                            $checkbook['check_number'] = isset($_ledgerData['B']) ? $_ledgerData['B'] : '';
                                            $checkbook['memo']         = isset($_ledgerData['C']) ? $_ledgerData['C'] : '';
                                            $checkbook['amount1']      = isset($_ledgerData['D']) ? getAmountFromString($_ledgerData['D']) : '';
                                            $this->checkbook_model->Add($checkbook);
                                        }

                                        $creditReceivedFrom              = [];
                                        $creditReceivedFrom['ledger_id'] = $ledgerId;
                                        $creditReceivedFrom['label']     = isset($_ledgerData['E']) ? $_ledgerData['E'] : '';
                                        $creditReceivedFrom['amount']    = isset($_ledgerData['F']) ? getAmountFromString($_ledgerData['F']) : '';

                                        if ($creditReceivedFrom['label'] != '' && $creditReceivedFrom['amount'] != '') {
                                            $this->ledger_credit_received_from_model->Add($creditReceivedFrom);
                                        }
                                    }
                                }
                            }
                            $ledgerId = $this->ledger_model->Edit($ledgerId, $updateLedger);
                        }
                    }
                    $this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                         Imported successfully!!!</div>');
                    redirect('statement');
                } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {

                    $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' . $e->getMessage() . '</div>');
                    redirect('statement/import');
                }
            } else {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                 Something went wrong file is not uploaded</div>');
                redirect('statement/import');
            }
        }
        $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                         Something went wrong file is not uploaded</div>');
        redirect('statement/import');
    }

    public function upload_setting($id = null)
    {
        $data['title'] = 'Upload Setting';
        if ($id == null) {
            $id = $this->uri->segment(3);
        }
        if (!is_null($id)) {
            $data['upload_data'] = $this->attachment_name_setting_model->Get($id);
            $data['action']      = "edit";
        } else {
            $data['action'] = "add";
        }
        $data['desc_data'] = $this->ledger_statement_model->Get_desc_data();
//           print_r($data);
        //           exit;
        $this->template->load('listing', 'upload_setting', $data);
    }

    public function save_upload_setting()
    {
        $data                    = array();
        $data['description']     = $this->input->post('desc_data');
        $data['invoice_name']    = $this->input->post('invoice_name');
        $data['selected_type']   = $this->input->post('selected_type');
        $data['document_name_1'] = $this->input->post('document_name_1');
        $data['document_name_2'] = $this->input->post('document_name_2');
        $data['document_name_3'] = $this->input->post('document_name_3');
        $id                      = $this->input->post('id');
        if ($id > 0) {
            $this->attachment_name_setting_model->Edit($id, $data);
        } else {
            $this->attachment_name_setting_model->Add($data);
        }

        $this->session->set_flashdata('msg_class', "success");
        $this->session->set_flashdata('msg', "Upload setting name saved successfully");
        redirect('statement/list_upload_setting');
    }

    public function list_upload_setting()
    {
        $data['title'] = 'Upload Setting List';
        if ($this->input->is_ajax_request()) {
            $upload_Res = $this->attachment_name_setting_model->Get();
            $this->getUploadListing($upload_Res);
        } else {
            $upload_Res = $this->attachment_name_setting_model->Get();
            $this->template->load('listing', 'list_upload_setting', $data);
        }
    }

    public function getUploadListing($result = array())
    {
        $tableData = array();
        foreach ($result['records'] as $key => $row) {

            $action   = array();
            $action[] = anchor('statement/upload_setting/' . $row->id, 'Edit');

            $tableData[$key]['srNo']            = $key + 1;
            $tableData[$key]['selected_type']   = $row->selected_type;
            $tableData[$key]['description']     = $row->description;
            $tableData[$key]['invoice_text']    = $row->invoice_name;
            $tableData[$key]['document_name_1'] = $row->document_name_1;
            $tableData[$key]['document_name_2'] = $row->document_name_2;
            $tableData[$key]['document_name_3'] = $row->document_name_3;
            $tableData[$key]['created_on']      = $row->created_on;
            $tableData[$key]['action']          = implode(" | ", $action);
            $tableData[$key]['id']              = $row->id;
        }
        $data['data']            = $tableData;
        $data['recordsTotal']    = $result['countTotal'];
        $data['recordsFiltered'] = $result['countFiltered'];
        echo json_encode($data);
    }

    public function upload_attachment()
    {
        $this->load->model('attachment_upload_model');
        $total_doc_file      = count($_FILES['doc_file']['name']);
        $ledger_id           = $this->input->post('ledger_id');
        $ledger_statement_id = $this->input->post('ledger_statement_id');
        $desc                = $this->input->post('description_txt');
        $source                = $this->input->post('source');
        $ledger_Records      = $this->ledger_model->Get($ledger_id);
        if (isset($ledger_Records) && !empty($ledger_Records)) {
            $store_key = $ledger_Records->store_key;
            $month     = $ledger_Records->month;
            $year      = $ledger_Records->year;
        }
        $desc_Records = $this->attachment_name_setting_model->Get(null, array("description" => $desc));

        if (isset($desc_Records['records']) && !empty($desc_Records['records'])) {
            foreach ($desc_Records['records'] as $cRow) {
                $invoice_name    = $cRow->invoice_name;
                $document_name_1 = $cRow->document_name_1;
                $document_name_2 = $cRow->document_name_2;
                $document_name_3 = $cRow->document_name_3;
            }
        }

//            upload invoice
        $tempFile   = $_FILES['invoice_file']['tmp_name']; //3
        $targetPath = FCPATH . "/files_upload/import_file/";
        $file_name  = $_FILES['invoice_file']['name'];
        $tmp        = explode('.', $file_name);
        $file_ext   = end($tmp);
        $targetPath = FCPATH . "/files_upload/ledger_statement/" . $store_key . "/" . $year . "/" . $month . "/" . $ledger_statement_id . "/invoice/";

        if (!file_exists($targetPath)) {
            mkdir($targetPath, 0777, true);
        }

        if (isset($invoice_name) && $invoice_name != '') {
            $final_invoice_name = $invoice_name . "_" . $month . "_" . $year . "_" . $store_key . "." . $file_ext;
        } else {
            $final_invoice_name = $_FILES['invoice_file']['name'];
        }

        $file_name  = $final_invoice_name;
        $targetFile = $targetPath . $file_name; //5
        $res        = move_uploaded_file($tempFile, $targetFile); //6

        $ins_data   = array();
        $ins_data[] = array('statement_id' => $ledger_statement_id,
            'original_file_name'               => $_FILES['invoice_file']['name'],
            'uploaded_file_name'               => $file_name,
            'type'                             => 'invoice',
            'uploaded_url'                     => "files_upload/ledger_statement/" . $store_key . "/" . $year . "/" . $month . "/" . $ledger_statement_id . "/invoice/" . $file_name,
            'source'                     => $source);

        //upload other documents
        $doc_tempFile  = $_FILES['doc_file']['tmp_name']; //3
        $doc_file_name = $_FILES['doc_file']['name'];

        $doc_targetPath = FCPATH . "/files_upload/ledger_statement/" . $store_key . "/" . $year . "/" . $month . "/" . $ledger_statement_id . "/documents/";

        if (!file_exists($doc_targetPath)) {
            mkdir($doc_targetPath, 0777, true);
        }
        for ($i = 0; $i < count($doc_tempFile); $i++) {
            $tmp      = explode('.', $doc_file_name[$i]);
            $file_ext = end($tmp);
            if ($i == 0) {
                if (isset($document_name_1) && $document_name_1 != '') {
                    $final_document_name = $document_name_1 . "_" . $month . "_" . $year . "_" . $store_key . "." . $file_ext;
                } else {
                    $final_document_name = $doc_file_name[$i];
                }
            }
            if ($i == 1) {
                if (isset($document_name_2) && $document_name_2 != '') {
                    $final_document_name = $document_name_2 . "_" . $month . "_" . $year . "_" . $store_key . "." . $file_ext;
                } else {
                    $final_document_name = $doc_file_name[$i];
                }
            }
            if ($i == 2) {
                if (isset($document_name_3) && $document_name_3 != '') {
                    $final_document_name = $document_name_3 . "_" . $month . "_" . $year . "_" . $store_key . "." . $file_ext;
                } else {
                    $final_document_name = $doc_file_name[$i];
                }
            }

            $file_name   = $final_document_name;
            $targetFile  = $doc_targetPath . $file_name; //5
            $doctempFile = $doc_tempFile[$i];
            $res         = move_uploaded_file($doctempFile, $targetFile); //6
            $ins_data[]  = array('statement_id' => $ledger_statement_id,
                'original_file_name'                => $doc_file_name[$i],
                'uploaded_file_name'                => $file_name,
                'type'                              => 'documents',
                'uploaded_url'                      => "files_upload/ledger_statement/" . $store_key . "/" . $year . "/" . $month . "/" . $ledger_statement_id . "/documents/" . $file_name,
                'source'                     => $source);
        }
        //Save upload data in DB
        if (isset($ins_data) && !empty($ins_data)) {
            $this->attachment_upload_model->add_batch_data($ins_data);
        }

        //update the attachment count
        $updata                     = array();
        $updata['total_attachment'] = $total_doc_file + 1;
        $this->ledger_statement_model->Edit($ledger_statement_id, $updata);
        $this->session->set_flashdata('msg_class', "success");
        $this->session->set_flashdata('msg', "Attachment uploaded successfully");

        $call_from = $this->input->post('call_from');
        if($call_from == 'ledger')
            redirect('statement/view/' . $ledger_id, 'redirect');
        else
        {
            $call_from = explode('_', $call_from);
            redirect('reconcile/process?ledger_id='.$call_from[1].'&bank_id='.$call_from[2], 'redirect');
        }
    }

    public function view_attachement_info($statement_id)
    {
        $statement_data   = $this->ledger_statement_model->Get($statement_id);
        $description      = isset($statement_data->description) ? $statement_data->description : "";
        $transaction_type = isset($statement_data->transaction_type) ? $statement_data->transaction_type : "";
        $ledger_id        = isset($statement_data->ledger_id) ? $statement_data->ledger_id : "";
        $is_reconcile     = isset($statement_data->is_reconcile) ? $statement_data->is_reconcile : 0;

        $ledger_data  = $this->ledger_model->Get($ledger_id);
        $ledger_month = $ledger_data->month;

        if ($is_reconcile == 1) {
            $bank_statement_id = isset($statement_data->bank_statement_id) ? $statement_data->bank_statement_id : '';
            if ($bank_statement_id != '') {
                $bank_statement_arr = explode(",", $bank_statement_id);
                //Get bank statement info
                $statement_entries = $this->bank_statement_entries_model->get_bank_statements($bank_statement_arr);
                if ($statement_entries) {
                    $data['bank_statement_entries'] = $statement_entries;
                }
            }
        }

        $data['ledger_statement_data'] = $statement_data;

        if ($transaction_type == 'debit') {
            $ledger_year = $ledger_data->year;
            $date        = $ledger_year . "-" . $ledger_month . "-01";

            //get 5 previous month
            for ($i = 1; $i < 6; $i++) {
                $months[] = date("m", strtotime(date($date) . " -$i months"));
            }
            $data['month_data'] = $this->attachment_upload_model->month_desc_details($description, $months, $ledger_year);
//            echo $this->db->last_query();
            //get 5 previous year
            for ($i = 1; $i < 6; $i++) {
                $years[] = date("Y", strtotime(date($date) . " -$i years"));
            }

            $data['year_data'] = $this->attachment_upload_model->year_desc_details($description, $ledger_month, $years);

//            echo "<pre>";
            //        print_r($month_data);
            //        print_r($year_data);
        }
        //Attachment data
        $data['attachment_data'] = $this->attachment_upload_model->Get(null, array("statement_id" => $statement_id));

//        echo "<pre>";
        //        print_r($attachment_data);
        //        exit;

        $data['title'] = 'View Attachments';
        //get current Attachments
        //        $data['statement_Attchements'] = $this->attachment_upload_model->Get(NULL, array("statement_id" => $statement_id));
        $this->template->load('listing', 'view_attachments', $data);
    }

    public function download_attachment($statement_id)
    {
        $this->load->library('zip');
        $attachment_data = $this->attachment_upload_model->Get(null, array("statement_id" => $statement_id));
        if (isset($attachment_data['records']) && !empty($attachment_data['records'])) {
            foreach ($attachment_data['records'] as $row) {
                $this->zip->read_file(FCPATH . $row->uploaded_url);
            }

            $this->zip->download('download.zip');
        }
    }

    public function copy_info($ledger_id)
    {

        $data['title']           = 'Copy Attachement Name';
        $data['attachment_data'] = $this->attachment_upload_model->Get_ledger_attachment($ledger_id);
        $this->load->view('copy_info', $data);
    }

    public function delete_attachment($id, $statement_id)
    {
        $attachment_Res = $this->attachment_upload_model->Get($id);
        $upload_path    = isset($attachment_Res->uploaded_url) ? $attachment_Res->uploaded_url : "";
        $image_path     = FCPATH . $upload_path;
        unlink($image_path);
        $this->attachment_upload_model->Delete($id);
        $this->session->set_flashdata('msg_class', "success");
        $this->session->set_flashdata('msg', "Attachment deleted successfully");
        redirect('statement/view_attachement_info/' . $statement_id, 'redirect');
    }

    public function download_ledger($ledger_id)
    {
        $this->load->library('excel');
        //MONTH WISE LEDGER BALANCE
        $ledger_balances = $this->attachment_upload_model->ledger_balance($ledger_id);
        $store_key       = $ledger_balances[0]['store_key'];

        $objPHPExcel = new PHPExcel();
        $columns     = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z');

        //Sheet details
        $sheet_details = array(
            //1st sheet details
            0  => array('sheet_title' => 'Jan',
                'sheet_heading'           => array('STORE #', $store_key, "MONTH", "JAN", "$", "Balance"),
                "sheet_heading_part"      => array("CREDITS", "DOLLAR AMT.", "DEBITS", "DOLLAR AMT.", "Impound Amt.", "W/E Dates"),
                //ledger data
                'sheet_data'              => $this->attachment_upload_model->Download_ledger(1, $store_key),
                //checkbook record
                "checkbook_heading"       => array('MONTH', "JAN", "STORE#", $store_key, "Balance C/F", "$"),
                'checkbook_data'          => $this->attachment_upload_model->checkbook_record(1, $store_key),
                'debit_entry'             => $this->attachment_upload_model->debit_entry(1, $store_key),
                'donut_data'              => $this->attachment_upload_model->donut_data(1, $store_key),
                'payroll_net_data'        => $this->attachment_upload_model->payroll_net_data(1, $store_key),
                'dcp_data'                => $this->attachment_upload_model->dcp_data(1, $store_key),
                'dcp_data_credit'         => $this->attachment_upload_model->dcp_data_credit(1, $store_key),
                'dcp_data_credit'         => $this->attachment_upload_model->dcp_data_credit(1, $store_key),
                'dcp_data_credit'         => $this->attachment_upload_model->dcp_data_credit(1, $store_key),
                'payroll_gross_data'      => $this->attachment_upload_model->payroll_gross_data(1, $store_key),
                'roy_data'                => $this->attachment_upload_model->roy_data(1, $store_key),
                'dean_foods'              => $this->attachment_upload_model->dean_foods(1, $store_key),
                'credit_entry'            => $this->attachment_upload_model->credit_entry(1, $store_key),
                'ledger_data'             => $this->attachment_upload_model->ledger_data(1, $store_key),
            ),
            //2nd Sheet Details
            1  => array('sheet_title' => 'Feb',
                'sheet_heading'           => array('STORE #', $store_key, "MONTH", "FEB", "$", "Balance"),
                "sheet_heading_part"      => array("CREDITS", "DOLLAR AMT.", "DEBITS", "DOLLAR AMT.", "Impound Amt.", "W/E Dates"),
                'sheet_data'              => $this->attachment_upload_model->Download_ledger(2, $store_key),
                //checkbook record
                "checkbook_heading"       => array('MONTH', "FEB", "STORE#", $store_key, "Balance C/F", "$"),
                'checkbook_data'          => $this->attachment_upload_model->checkbook_record(2, $store_key),
                'debit_entry'             => $this->attachment_upload_model->debit_entry(2, $store_key),
                'donut_data'              => $this->attachment_upload_model->donut_data(2, $store_key),
                'payroll_net_data'        => $this->attachment_upload_model->payroll_net_data(2, $store_key),
                'dcp_data'                => $this->attachment_upload_model->dcp_data(2, $store_key),
                'dcp_data_credit'         => $this->attachment_upload_model->dcp_data_credit(2, $store_key),
                'payroll_gross_data'      => $this->attachment_upload_model->payroll_gross_data(2, $store_key),
                'roy_data'                => $this->attachment_upload_model->roy_data(2, $store_key),
                'dean_foods'              => $this->attachment_upload_model->dean_foods(2, $store_key),
                'credit_entry'            => $this->attachment_upload_model->credit_entry(2, $store_key),
                'ledger_data'             => $this->attachment_upload_model->ledger_data(2, $store_key),
            ),
            //3nd Sheet Details
            2  => array('sheet_title' => 'March',
                'sheet_heading'           => array('STORE #', $store_key, "MONTH", "MARCH", "$", "Balance"),
                "sheet_heading_part"      => array("CREDITS", "DOLLAR AMT.", "DEBITS", "DOLLAR AMT.", "Impound Amt.", "W/E Dates"),
                'sheet_data'              => $this->attachment_upload_model->Download_ledger(3, $store_key),
                //checkbook record
                "checkbook_heading"       => array('MONTH', "MARCH", "STORE#", $store_key, "Balance C/F", "$"),
                'checkbook_data'          => $this->attachment_upload_model->checkbook_record(3, $store_key),
                'debit_entry'             => $this->attachment_upload_model->debit_entry(3, $store_key),
                'donut_data'              => $this->attachment_upload_model->donut_data(3, $store_key),
                'payroll_net_data'        => $this->attachment_upload_model->payroll_net_data(3, $store_key),
                'dcp_data'                => $this->attachment_upload_model->dcp_data(3, $store_key),
                'dcp_data_credit'         => $this->attachment_upload_model->dcp_data_credit(3, $store_key),
                'payroll_gross_data'      => $this->attachment_upload_model->payroll_gross_data(3, $store_key),
                'roy_data'                => $this->attachment_upload_model->roy_data(3, $store_key),
                'dean_foods'              => $this->attachment_upload_model->dean_foods(3, $store_key),
                'credit_entry'            => $this->attachment_upload_model->credit_entry(3, $store_key),
                'ledger_data'             => $this->attachment_upload_model->ledger_data(3, $store_key),
            ),
            //4nd Sheet Details
            3  => array('sheet_title' => 'April',
                'sheet_heading'           => array('STORE #', $store_key, "MONTH", "APRIL", "$", "Balance"),
                "sheet_heading_part"      => array("CREDITS", "DOLLAR AMT.", "DEBITS", "DOLLAR AMT.", "Impound Amt.", "W/E Dates"),
                'sheet_data'              => $this->attachment_upload_model->Download_ledger(4, $store_key),
                //checkbook record
                "checkbook_heading"       => array('MONTH', "APRIL", "STORE#", $store_key, "Balance C/F", "$"),
                'checkbook_data'          => $this->attachment_upload_model->checkbook_record(4, $store_key),
                'debit_entry'             => $this->attachment_upload_model->debit_entry(4, $store_key),
                'donut_data'              => $this->attachment_upload_model->donut_data(4, $store_key),
                'payroll_net_data'        => $this->attachment_upload_model->payroll_net_data(4, $store_key),
                'dcp_data'                => $this->attachment_upload_model->dcp_data(4, $store_key),
                'dcp_data_credit'         => $this->attachment_upload_model->dcp_data_credit(4, $store_key),
                'payroll_gross_data'      => $this->attachment_upload_model->payroll_gross_data(4, $store_key),
                'roy_data'                => $this->attachment_upload_model->roy_data(4, $store_key),
                'dean_foods'              => $this->attachment_upload_model->dean_foods(4, $store_key),
                'credit_entry'            => $this->attachment_upload_model->credit_entry(4, $store_key),
                'ledger_data'             => $this->attachment_upload_model->ledger_data(4, $store_key),
            ),
            //5nd Sheet Details
            4  => array('sheet_title' => 'May',
                'sheet_heading'           => array('STORE #', $store_key, "MONTH", "MAY", "$", "Balance"),
                "sheet_heading_part"      => array("CREDITS", "DOLLAR AMT.", "DEBITS", "DOLLAR AMT.", "Impound Amt.", "W/E Dates"),
                'sheet_data'              => $this->attachment_upload_model->Download_ledger(5, $store_key),
                //checkbook record
                "checkbook_heading"       => array('MONTH', "MAY", "STORE#", $store_key, "Balance C/F", "$"),
                'checkbook_data'          => $this->attachment_upload_model->checkbook_record(5, $store_key),
                'debit_entry'             => $this->attachment_upload_model->debit_entry(5, $store_key),
                'donut_data'              => $this->attachment_upload_model->donut_data(5, $store_key),
                'payroll_net_data'        => $this->attachment_upload_model->payroll_net_data(5, $store_key),
                'dcp_data'                => $this->attachment_upload_model->dcp_data(5, $store_key),
                'dcp_data_credit'         => $this->attachment_upload_model->dcp_data_credit(5, $store_key),
                'payroll_gross_data'      => $this->attachment_upload_model->payroll_gross_data(5, $store_key),
                'roy_data'                => $this->attachment_upload_model->roy_data(5, $store_key),
                'dean_foods'              => $this->attachment_upload_model->dean_foods(5, $store_key),
                'credit_entry'            => $this->attachment_upload_model->credit_entry(5, $store_key),
                'ledger_data'             => $this->attachment_upload_model->ledger_data(5, $store_key),
            ),
            //6nd Sheet Details
            5  => array('sheet_title' => 'June',
                'sheet_heading'           => array('STORE #', $store_key, "MONTH", "JUNE", "$", "Balance"),
                "sheet_heading_part"      => array("CREDITS", "DOLLAR AMT.", "DEBITS", "DOLLAR AMT.", "Impound Amt.", "W/E Dates"),
                'sheet_data'              => $this->attachment_upload_model->Download_ledger(6, $store_key),
                //checkbook record
                "checkbook_heading"       => array('MONTH', "JUNE", "STORE#", $store_key, "Balance C/F", "$"),
                'checkbook_data'          => $this->attachment_upload_model->checkbook_record(6, $store_key),
                'debit_entry'             => $this->attachment_upload_model->debit_entry(6, $store_key),
                'donut_data'              => $this->attachment_upload_model->donut_data(6, $store_key),
                'payroll_net_data'        => $this->attachment_upload_model->payroll_net_data(6, $store_key),
                'dcp_data'                => $this->attachment_upload_model->dcp_data(6, $store_key),
                'dcp_data_credit'         => $this->attachment_upload_model->dcp_data_credit(6, $store_key),
                'payroll_gross_data'      => $this->attachment_upload_model->payroll_gross_data(6, $store_key),
                'roy_data'                => $this->attachment_upload_model->roy_data(6, $store_key),
                'dean_foods'              => $this->attachment_upload_model->dean_foods(6, $store_key),
                'credit_entry'            => $this->attachment_upload_model->credit_entry(6, $store_key),
                'ledger_data'             => $this->attachment_upload_model->ledger_data(6, $store_key),
            ),
            //7nd Sheet Details
            6  => array('sheet_title' => 'July',
                'sheet_heading'           => array('STORE #', $store_key, "MONTH", "JULY", "$", "Balance"),
                "sheet_heading_part"      => array("CREDITS", "DOLLAR AMT.", "DEBITS", "DOLLAR AMT.", "Impound Amt.", "W/E Dates"),
                'sheet_data'              => $this->attachment_upload_model->Download_ledger(7, $store_key),
                //checkbook record
                "checkbook_heading"       => array('MONTH', "JULY", "STORE#", $store_key, "Balance C/F", "$"),
                'checkbook_data'          => $this->attachment_upload_model->checkbook_record(7, $store_key),
                'debit_entry'             => $this->attachment_upload_model->debit_entry(7, $store_key),
                'donut_data'              => $this->attachment_upload_model->donut_data(7, $store_key),
                'payroll_net_data'        => $this->attachment_upload_model->payroll_net_data(7, $store_key),
                'dcp_data'                => $this->attachment_upload_model->dcp_data(7, $store_key),
                'dcp_data_credit'         => $this->attachment_upload_model->dcp_data_credit(7, $store_key),
                'payroll_gross_data'      => $this->attachment_upload_model->payroll_gross_data(7, $store_key),
                'roy_data'                => $this->attachment_upload_model->roy_data(7, $store_key),
                'dean_foods'              => $this->attachment_upload_model->dean_foods(7, $store_key),
                'credit_entry'            => $this->attachment_upload_model->credit_entry(7, $store_key),
                'ledger_data'             => $this->attachment_upload_model->ledger_data(7, $store_key),
            ),
            //8nd Sheet Details
            7  => array('sheet_title' => 'Aug',
                'sheet_heading'           => array('STORE #', $store_key, "MONTH", "AUG", "$", "Balance"),
                "sheet_heading_part"      => array("CREDITS", "DOLLAR AMT.", "DEBITS", "DOLLAR AMT.", "Impound Amt.", "W/E Dates"),
                'sheet_data'              => $this->attachment_upload_model->Download_ledger(8, $store_key),
                //checkbook record
                "checkbook_heading"       => array('MONTH', "AUG", "STORE#", $store_key, "Balance C/F", "$"),
                'checkbook_data'          => $this->attachment_upload_model->checkbook_record(8, $store_key),
                'debit_entry'             => $this->attachment_upload_model->debit_entry(8, $store_key),
                'donut_data'              => $this->attachment_upload_model->donut_data(8, $store_key),
                'payroll_net_data'        => $this->attachment_upload_model->payroll_net_data(8, $store_key),
                'dcp_data'                => $this->attachment_upload_model->dcp_data(8, $store_key),
                'dcp_data_credit'         => $this->attachment_upload_model->dcp_data_credit(8, $store_key),
                'payroll_gross_data'      => $this->attachment_upload_model->payroll_gross_data(8, $store_key),
                'roy_data'                => $this->attachment_upload_model->roy_data(8, $store_key),
                'dean_foods'              => $this->attachment_upload_model->dean_foods(8, $store_key),
                'credit_entry'            => $this->attachment_upload_model->credit_entry(8, $store_key),
                'ledger_data'             => $this->attachment_upload_model->ledger_data(8, $store_key),
            ),
            //9nd Sheet Details
            8  => array('sheet_title' => 'Sep',
                'sheet_heading'           => array('STORE #', $store_key, "MONTH", "SEP", "$", "Balance"),
                "sheet_heading_part"      => array("CREDITS", "DOLLAR AMT.", "DEBITS", "DOLLAR AMT.", "Impound Amt.", "W/E Dates"),
                'sheet_data'              => $this->attachment_upload_model->Download_ledger(9, $store_key),
                //checkbook record
                "checkbook_heading"       => array('MONTH', "SEP", "STORE#", $store_key, "Balance C/F", "$"),
                'checkbook_data'          => $this->attachment_upload_model->checkbook_record(9, $store_key),
                'debit_entry'             => $this->attachment_upload_model->debit_entry(9, $store_key),
                'donut_data'              => $this->attachment_upload_model->donut_data(9, $store_key),
                'payroll_net_data'        => $this->attachment_upload_model->payroll_net_data(9, $store_key),
                'dcp_data'                => $this->attachment_upload_model->dcp_data(9, $store_key),
                'dcp_data_credit'         => $this->attachment_upload_model->dcp_data_credit(9, $store_key),
                'payroll_gross_data'      => $this->attachment_upload_model->payroll_gross_data(9, $store_key),
                'roy_data'                => $this->attachment_upload_model->roy_data(9, $store_key),
                'dean_foods'              => $this->attachment_upload_model->dean_foods(9, $store_key),
                'credit_entry'            => $this->attachment_upload_model->credit_entry(9, $store_key),
                'ledger_data'             => $this->attachment_upload_model->ledger_data(9, $store_key),
            ),
            //10nd Sheet Details
            9  => array('sheet_title' => 'Oct',
                'sheet_heading'           => array('STORE #', $store_key, "MONTH", "OCT", "$", "Balance"),
                "sheet_heading_part"      => array("CREDITS", "DOLLAR AMT.", "DEBITS", "DOLLAR AMT.", "Impound Amt.", "W/E Dates"),
                'sheet_data'              => $this->attachment_upload_model->Download_ledger(10, $store_key),
                //checkbook record
                "checkbook_heading"       => array('MONTH', "OCT", "STORE#", $store_key, "Balance C/F", "$"),
                'checkbook_data'          => $this->attachment_upload_model->checkbook_record(10, $store_key),
                'debit_entry'             => $this->attachment_upload_model->debit_entry(10, $store_key),
                'donut_data'              => $this->attachment_upload_model->donut_data(10, $store_key),
                'payroll_net_data'        => $this->attachment_upload_model->payroll_net_data(10, $store_key),
                'dcp_data'                => $this->attachment_upload_model->dcp_data(10, $store_key),
                'dcp_data_credit'         => $this->attachment_upload_model->dcp_data_credit(10, $store_key),
                'payroll_gross_data'      => $this->attachment_upload_model->payroll_gross_data(10, $store_key),
                'roy_data'                => $this->attachment_upload_model->roy_data(10, $store_key),
                'dean_foods'              => $this->attachment_upload_model->dean_foods(10, $store_key),
                'credit_entry'            => $this->attachment_upload_model->credit_entry(10, $store_key),
                'ledger_data'             => $this->attachment_upload_model->ledger_data(10, $store_key),
            ),
            //11nd Sheet Details
            10 => array('sheet_title' => 'Nov',
                'sheet_heading'           => array('STORE #', $store_key, "MONTH", "NOV", "$", "Balance"),
                "sheet_heading_part"      => array("CREDITS", "DOLLAR AMT.", "DEBITS", "DOLLAR AMT.", "Impound Amt.", "W/E Dates"),
                'sheet_data'              => $this->attachment_upload_model->Download_ledger(11, $store_key),
                //checkbook record
                "checkbook_heading"       => array('MONTH', "NOV", "STORE#", $store_key, "Balance C/F", "$"),
                'checkbook_data'          => $this->attachment_upload_model->checkbook_record(11, $store_key),
                'debit_entry'             => $this->attachment_upload_model->debit_entry(11, $store_key),
                'donut_data'              => $this->attachment_upload_model->donut_data(11, $store_key),
                'payroll_net_data'        => $this->attachment_upload_model->payroll_net_data(11, $store_key),
                'dcp_data'                => $this->attachment_upload_model->dcp_data(11, $store_key),
                'dcp_data_credit'         => $this->attachment_upload_model->dcp_data_credit(11, $store_key),
                'payroll_gross_data'      => $this->attachment_upload_model->payroll_gross_data(11, $store_key),
                'roy_data'                => $this->attachment_upload_model->roy_data(11, $store_key),
                'dean_foods'              => $this->attachment_upload_model->dean_foods(11, $store_key),
                'credit_entry'            => $this->attachment_upload_model->credit_entry(11, $store_key),
                'ledger_data'             => $this->attachment_upload_model->ledger_data(11, $store_key),
            ),
            //12nd Sheet Details
            11 => array('sheet_title' => 'Dec',
                'sheet_heading'           => array('STORE #', $store_key, "MONTH", "DEC", "$", "Balance"),
                "sheet_heading_part"      => array("CREDITS", "DOLLAR AMT.", "DEBITS", "DOLLAR AMT.", "Impound Amt.", "W/E Dates"),
                'sheet_data'              => $this->attachment_upload_model->Download_ledger(12, $store_key),
                //checkbook record
                "checkbook_heading"       => array('MONTH', "DEC", "STORE#", $store_key, "Balance C/F", "$"),
                'checkbook_data'          => $this->attachment_upload_model->checkbook_record(12, $store_key),
                'debit_entry'             => $this->attachment_upload_model->debit_entry(12, $store_key),
                'donut_data'              => $this->attachment_upload_model->donut_data(12, $store_key),
                'payroll_net_data'        => $this->attachment_upload_model->payroll_net_data(12, $store_key),
                'dcp_data'                => $this->attachment_upload_model->dcp_data(12, $store_key),
                'dcp_data_credit'         => $this->attachment_upload_model->dcp_data_credit(12, $store_key),
                'payroll_gross_data'      => $this->attachment_upload_model->payroll_gross_data(12, $store_key),
                'roy_data'                => $this->attachment_upload_model->roy_data(12, $store_key),
                'dean_foods'              => $this->attachment_upload_model->dean_foods(12, $store_key),
                'credit_entry'            => $this->attachment_upload_model->credit_entry(12, $store_key),
                'ledger_data'             => $this->attachment_upload_model->ledger_data(12, $store_key),
            ),
        );

        $sheet_count = 0;
        $row         = 1;
        $column      = 0;
        while ($sheet_count < count($sheet_details)) {
            $objWorkSheet = '';
            if ($sheet_count > 0) {
                $objWorkSheet = $objPHPExcel->createSheet($sheet_count);
            } else {
                $objWorkSheet = $objPHPExcel->getActiveSheet();
            }
            //STYLING
            $leftstyle = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                ),
            );
            $rightstyle = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                ),
            );
            $centerstyle = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            );
            $bordertopsubheaderArray = array(
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            );
            $borderrightsubheaderArray = array(
                'borders' => array(
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            );
            $borderleftsubheaderArray = array(
                'borders' => array(
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            );
            $borderbottomsubheaderArray = array(
                'borders' => array(
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            );
            $allborderssubheaderArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THIN,
                    ),
                ),
            );
            $topheadersubheaderArray = array(
                'borders' => array(
                    'top' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THICK,
                    ),
                ),
            );
            $bottomheadersubheaderArray = array(
                'borders' => array(
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THICK,
                    ),
                ),
            );
            $rightheadersubheaderArray = array(
                'borders' => array(
                    'right' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THICK,
                    ),
                ),
            );
            $leftheadersubheaderArray = array(
                'borders' => array(
                    'left' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THICK,
                    ),
                ),
            );
            $allbordersthicksubheaderArray = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THICK,
                    ),
                ),
                'font'    => array(
                    'bold' => true,
                    'size' => 12,
                    'name' => 'Arial',
                ),
            );
            $checkbook_font = array('font' => array(
                'size' => 12,
                'name' => 'Arial',
            ));
            $checkbook_subheader = array('font' => array(
                'bold' => true,
                'size' => 12,
                'name' => 'Arial',
            ));

            for ($col = 'A'; $col != 'G'; $col++) {
                $objWorkSheet->getColumnDimension($col)->setAutoSize(true);
            }
            $objWorkSheet->getColumnDimension('A')->setWidth(26);

            $objWorkSheet->getColumnDimension('B')->setWidth(14);
            $objWorkSheet->getColumnDimension('C')->setWidth(34);
            $objWorkSheet->getColumnDimension('D')->setWidth(14);
            $objWorkSheet->getColumnDimension('E')->setWidth(14);
            $objWorkSheet->getColumnDimension('F')->setWidth(8);
            //all border apply

            $subheaderArray = array(
                'font' => array(
                    'size' => 10,
                    'name' => 'Arial',
                ),
            );
            $headersubheaderArray = array(
                'font'      => array(
                    'bold' => true,
                    'size' => 16,
                    'name' => 'Arial',
                ),
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                ),
            );
            $subheaderArray = array(
                'font' => array(
                    'bold' => true,
                    'size' => 10,
                    'name' => 'Arial',
                ),
            );
            $fontstyle = array(
                'font' => array(
                    'bold' => true,
                    'size' => 10,
                    'name' => 'Comic Sans MS',
                ),
            );
            $checkstyle = array(
                'font' => array(
                    'bold' => true,
                    'size' => 12,
                    'name' => 'Arial',
                ),
            );
            $row    = 2;
            $column = 0;

            //ledger data
            $ledger_data = [];
            if ($sheet_details[$sheet_count]['ledger_data']):
                $ledger_data = json_decode(json_encode($sheet_details[$sheet_count]['ledger_data']), true);
            endif;
            //for first row
            $objWorkSheet->mergeCells("A1:F1");
            $objWorkSheet->setCellValue("A1", "LEDGER STATEMENT");
            $objWorkSheet->getStyle("A1")->applyFromArray($headersubheaderArray);
            foreach ($sheet_details[$sheet_count]['sheet_heading'] as $key => $head) {
                if ($head == '$') {
                    if (isset($ledger_data[0]['ledger_balance'])) {
                        $head = '$' . $ledger_data[0]['ledger_balance'];
                    } else {
                        $head = '$0.00';
                    }

                }
                if ($key == 0) {
                    $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($rightstyle);
                }

                if ($key == 1) {
                    $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($centerstyle);
                }

                if ($key == 2) {
                    $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($rightstyle);
                }

                if ($key == 3) {
                    $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($centerstyle);
                }

                if ($key == 4) {
                    $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($rightstyle);
                }

                if ($key == 5) {
                    $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($leftstyle);
                    $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($fontstyle);
                }
                $objWorkSheet->setCellValue($columns[$column] . $row, $head);
                $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($subheaderArray);
                $column++;
            }
            //for second row heading row
            $row    = 3;
            $column = 0;
            foreach ($sheet_details[$sheet_count]['sheet_heading_part'] as $key => $head) {
                $objWorkSheet->setCellValue($columns[$column] . $row, $head);
                $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($allborderssubheaderArray);
                $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($topheadersubheaderArray);
                if ($key == 5 || $key == 4) {
                    $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($centerstyle);
                }
                if ($key == 1) {
                    $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($bordertopsubheaderArray);
                }
                if ($column == 5) {
                    $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($rightheadersubheaderArray);
                }

                $column++;
            }

            //for data set
            $row           = $objWorkSheet->getHighestRow() + 1; //row count
            $debit_totals  = 0;
            $credit_totals = 0;
            $only_credits  = 0;
            foreach ($sheet_details[$sheet_count]['sheet_data'] as $key => $report_details) {
                $column = 0;
                if (isset($report_details) > 0) {
                    foreach ($report_details as $key => $rowdata) {
                        if ($column == 0 || $column == 1 || $column == 3 || $column == 4) {
                            $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($leftstyle);
                        }
                        if ($column == 2 || $column == 5) {
                            $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($centerstyle);
                        }
                        if ($key == "debit_dollar_amt") {
                            $debit_totals += $rowdata;
                        }
                        if ($key == "credit_dollar_amt") {
                            $credit_totals += $rowdata;
                            $only_credits += $rowdata;
                        }
                        if (($key == "credit_dollar_amt" || $key == "debit_dollar_amt" || $key == "impound") && ($rowdata != "" && $rowdata != "0")) {
                            $rowdata = '$' . number_format($rowdata, 2);
                        }

                        if ($rowdata == "0"):
                            $rowdata = "";
                        endif;
                        $objWorkSheet->setCellValue($columns[$column] . $row, $rowdata);
                        $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($allborderssubheaderArray);
                        // $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($subheaderArray);
                        if ($column == 5) {
                            $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($rightheadersubheaderArray);
                        }

                        $column++;
                    }
                }

                $row++;
            }

            // for debit entry
            $i         = 0;
            $lastdebit = 0;
            // $counter = $position;
            foreach ($sheet_details[$sheet_count]['debit_entry'] as $key => $report_details) {
                $column = 2;
                if (isset($report_details) > 0) {
                    foreach ($report_details as $key => $rowdata) {
                        if ($column == 0 || $column == 1 || $column == 3 || $column == 4) {
                            $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($leftstyle);
                        }
                        if ($column == 2 || $column == 5) {
                            $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($centerstyle);
                        }
                        if ($key == "debit_dollar_amt" && $rowdata != "") {
                            $debit_totals += $rowdata;
                            $rowdata = '$' . number_format($rowdata, 2);
                        }
                        // if($i==0){
                        //     $row-=1;
                        // }
                        if ($rowdata == "0"):
                            $rowdata = "";
                        endif;
                        $objWorkSheet->setCellValue($columns[$column] . $row, $rowdata);

                        if ($column == 5) {
                            $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($rightheadersubheaderArray);
                        }

                        $column++;
                        $i++;
                    }
                }
                $objWorkSheet->getStyle("A" . $row . ":F" . $row)->applyFromArray($allborderssubheaderArray);
                $lastdebit = $row;
                $row++;
            }
            //---------------------------------
            $objWorkSheet->setCellValue("A35", "TOTAL");
            $objWorkSheet->setCellValue("B35", '$' . number_format($only_credits, 2));

            $objWorkSheet->getStyle("A35")->applyFromArray($subheaderArray);
            $objWorkSheet->getStyle("B35")->applyFromArray($subheaderArray);
            //for donut entry
            if ($sheet_details[$sheet_count]['donut_data']):
                $counter = $lastdebit;
                $donuts  = $lastdebit - 1;
                $objWorkSheet->mergeCells("E" . $donuts . ":F" . $donuts);
                $objWorkSheet->setCellValue("E" . $donuts, "DONUT PURCHASES FROM CML");
                $objWorkSheet->getStyle("E" . $donuts)->applyFromArray($subheaderArray);

                $donut_totals = 0;
                foreach ($sheet_details[$sheet_count]['donut_data'] as $key => $report_details) {
                    $column = 4;
                    if (isset($report_details) > 0) {
                        foreach ($report_details as $key => $rowdata) {
                            if ($column == 0 || $column == 1 || $column == 3 || $column == 4) {
                                $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($leftstyle);
                            }
                            if ($column == 2 || $column == 5) {
                                $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($centerstyle);
                            }
                            if ($key == "debit_dollar_amt" && $rowdata != "") {
                                $donut_totals += $rowdata;
                                $rowdata = '$' . number_format($rowdata, 2);
                            }
                            if ($rowdata == "0"):
                                $rowdata = "";
                            endif;
                            if ($i == 0) {
                                $counter += 1;
                            }
                            $objWorkSheet->setCellValue($columns[$column] . $counter, $rowdata);
                            $objWorkSheet->getStyle($columns[$column] . $counter)->applyFromArray($allborderssubheaderArray);
                            if ($column == 5) {
                                $objWorkSheet->getStyle($columns[$column] . $counter)->applyFromArray($rightheadersubheaderArray);
                            }

                            $column++;
                            $i++;
                        }
                    }
                    $objWorkSheet->getStyle("A" . $counter . ":F" . $counter)->applyFromArray($allborderssubheaderArray);
                    $objWorkSheet->getStyle("A" . $counter . ':F' . $counter)->applyFromArray($rightheadersubheaderArray);
                    $counter++;
                }
            endif;
            //---------------------------------
            //TOTAL DONUTS RECORDS
            if ($sheet_details[$sheet_count]['donut_data']):
                $row = $counter;
                $row++;

                $objWorkSheet->setCellValue("E" . $row, "TOTAL");
                $objWorkSheet->setCellValue("F" . $row, '$' . number_format($donut_totals, 2));
                $objWorkSheet->getStyle("E" . $row)->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("F" . $row)->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("A" . $row . ':F' . $row)->applyFromArray($allborderssubheaderArray);
                $objWorkSheet->getStyle("A" . $row . ':F' . $row)->applyFromArray($rightheadersubheaderArray);

                $objWorkSheet->getStyle("A" . $row . ':F' . $row)->applyFromArray($rightheadersubheaderArray);

            endif;

            //---------------------------------
            if ($sheet_details[$sheet_count]['sheet_data']):
                $row++;
                $objWorkSheet->setCellValue("C" . $row, "TOTAL");
                $objWorkSheet->setCellValue("D" . $row, '$' . number_format($debit_totals, 2));
                $objWorkSheet->setCellValue("E" . $row, 'DCP EFTS:');
                $objWorkSheet->setCellValue("F" . $row, 'DOLLAR AMT.');
                $objWorkSheet->getStyle("C" . $row)->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("D" . $row)->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("E" . $row)->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("F" . $row)->applyFromArray($subheaderArray);

                $objWorkSheet->getStyle("A" . $row)->applyFromArray($allborderssubheaderArray);
                $objWorkSheet->getStyle("B" . $row)->applyFromArray($allborderssubheaderArray);
                $objWorkSheet->getStyle("C" . $row)->applyFromArray($allborderssubheaderArray);
                $objWorkSheet->getStyle("D" . $row)->applyFromArray($allborderssubheaderArray);
                $objWorkSheet->getStyle("E" . $row)->applyFromArray($allborderssubheaderArray);
                $objWorkSheet->getStyle("F" . $row)->applyFromArray($allborderssubheaderArray);

                $objWorkSheet->getStyle("A" . $row . ':F' . $row)->applyFromArray($rightheadersubheaderArray);

            endif;
            ////---------------------------------
            //for payrollnet
            $payrollposition    = 0;
            $payroll_net_totals = 0;
            if ($sheet_details[$sheet_count]['sheet_data']):
                if ($sheet_details[$sheet_count]['payroll_net_data']):
                    $row++;
                    $payrollposition = $row;
                    $objWorkSheet->setCellValue("C" . $row, "PAYROLL NET");
                    $objWorkSheet->setCellValue("D" . $row, 'DOLLAR AMT.');
                    $objWorkSheet->getStyle("C" . $row)->applyFromArray($subheaderArray);
                    $objWorkSheet->getStyle("D" . $row)->applyFromArray($subheaderArray);
                    $row++;
                    foreach ($sheet_details[$sheet_count]['payroll_net_data'] as $key => $report_details) {
                        $column = 2;
                        $objWorkSheet->getStyle("A" . $row, ':F' . $row)->applyFromArray($allborderssubheaderArray);
                        if (isset($report_details) > 0) {
                            foreach ($report_details as $key => $rowdata) {
                                if ($column == 0 || $column == 1 || $column == 3 || $column == 4) {
                                    $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($leftstyle);
                                }
                                if ($column == 2 || $column == 5) {
                                    $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($centerstyle);
                                }
                                if ($key == "debit_dollar_amt" && $rowdata != "") {
                                    $payroll_net_totals += $rowdata;
                                    $rowdata = '$' . number_format($rowdata, 2);
                                }
                                if ($rowdata == "0"):
                                    $rowdata = "";
                                endif;
                                $objWorkSheet->setCellValue($columns[$column] . $row, $rowdata);

                                if ($column == 5) {
                                    $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($rightheadersubheaderArray);
                                }

                                $column++;
                            }
                        }

                        $row++;
                    }

                endif;
                if ($sheet_details[$sheet_count]['dcp_data']):
                    $dcp_totals_credit = 0;
                    $dcp_totals_debit  = 0;
                    $dcpposition       = $payrollposition;
                    foreach ($sheet_details[$sheet_count]['dcp_data'] as $key => $report_details) {
                        $objWorkSheet->getStyle("A" . $row . ":F" . $row)->applyFromArray($allborderssubheaderArray);
                        $column = 4;
                        if (isset($report_details) > 0) {
                            foreach ($report_details as $key => $rowdata) {
                                if ($column == 0 || $column == 1 || $column == 3 || $column == 4) {
                                    $objWorkSheet->getStyle($columns[$column] . $payrollposition)->applyFromArray($leftstyle);
                                }
                                if ($column == 2 || $column == 5) {
                                    $objWorkSheet->getStyle($columns[$column] . $payrollposition)->applyFromArray($centerstyle);
                                }
                                if ($key == "debit_dollar_amt" && $rowdata != "") {
                                    $dcp_totals_debit += $rowdata;
                                    $rowdata = '$' . number_format($rowdata, 2);
                                }
                                if ($rowdata == "0"):
                                    $rowdata = "";
                                endif;
                                $objWorkSheet->setCellValue($columns[$column] . $payrollposition, $rowdata);

                                if ($column == 5) {
                                    $objWorkSheet->getStyle($columns[$column] . $payrollposition)->applyFromArray($rightheadersubheaderArray);
                                }

                                $column++;
                            }
                        }
                        $payrollposition++;
                        $dcpposition++;
                    }
                    $dcpposition--;
                    $objWorkSheet->setCellValue("C" . $dcpposition, "TOTAL");
                    $objWorkSheet->setCellValue("D" . $dcpposition, '$' . number_format($payroll_net_totals, 2));
                    $objWorkSheet->getStyle("C" . $dcpposition)->applyFromArray($subheaderArray);
                    $objWorkSheet->getStyle("D" . $dcpposition)->applyFromArray($subheaderArray);
                    $objWorkSheet->getStyle('F' . $dcpposition)->applyFromArray($rightheadersubheaderArray);
                    $payrollposition++;
                    foreach ($sheet_details[$sheet_count]['dcp_data_credit'] as $key => $report_details) {
                        $column = 4;
                        // $dcpposition = $row;
                        $objWorkSheet->getStyle("A" . $row . ":F" . $row)->applyFromArray($allborderssubheaderArray);
                        if (isset($report_details) > 0) {
                            foreach ($report_details as $key => $rowdata) {
                                if ($column == 0 || $column == 1 || $column == 3 || $column == 4) {
                                    $objWorkSheet->getStyle($columns[$column] . $payrollposition)->applyFromArray($leftstyle);
                                }
                                if ($column == 2 || $column == 5) {
                                    $objWorkSheet->getStyle($columns[$column] . $payrollposition)->applyFromArray($centerstyle);
                                }
                                if ($key == "credit_amt" && $rowdata != "") {
                                    $dcp_totals_credit += $rowdata;
                                    $rowdata = '($' . number_format($rowdata, 2) . ')';
                                }
                                if ($rowdata == "0"):
                                    $rowdata = "";
                                endif;
                                $objWorkSheet->setCellValue($columns[$column] . $payrollposition, $rowdata);

                                if ($column == 5) {
                                    $objWorkSheet->getStyle($columns[$column] . $payrollposition)->applyFromArray($rightheadersubheaderArray);
                                }

                                $column++;
                            }
                        }
                        $payrollposition++;
                    }
                endif;

            endif;

            if ($sheet_details[$sheet_count]['payroll_gross_data']):
                //$row++;
                $payrollposition = $dcpposition;
                $payrollposition++;
                $objWorkSheet->setCellValue("C" . $payrollposition, "PAYROLL GROSS");
                $objWorkSheet->setCellValue("D" . $payrollposition, 'DOLLAR AMT.');
                $objWorkSheet->getStyle("C" . $payrollposition)->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("D" . $payrollposition)->applyFromArray($subheaderArray);
                $payrollposition++;
                $payroll_gross_totals = 0;
                foreach ($sheet_details[$sheet_count]['payroll_gross_data'] as $key => $report_details) {
                    $objWorkSheet->getStyle("A" . $payrollposition . ":F" . $payrollposition)->applyFromArray($allborderssubheaderArray);
                    $column = 2;
                    if (isset($report_details) > 0) {
                        foreach ($report_details as $key => $rowdata) {
                            if ($column == 0 || $column == 1 || $column == 3 || $column == 4) {
                                $objWorkSheet->getStyle($columns[$column] . $payrollposition)->applyFromArray($leftstyle);
                            }
                            if ($column == 2 || $column == 5) {
                                $objWorkSheet->getStyle($columns[$column] . $payrollposition)->applyFromArray($centerstyle);
                            }
                            if ($key == "debit_dollar_amt" && $rowdata != "") {
                                $payroll_gross_totals += $rowdata;
                                $rowdata = '$' . number_format($rowdata, 2);
                            }
                            if ($rowdata == "0"):
                                $rowdata = "";
                            endif;
                            $objWorkSheet->setCellValue($columns[$column] . $payrollposition, $rowdata);

                            if ($column == 5) {
                                $objWorkSheet->getStyle($columns[$column] . $payrollposition)->applyFromArray($rightheadersubheaderArray);
                            }

                            $column++;
                        }
                    }
                    $payrollposition++;
                }

                $payrollposition++;

                $objWorkSheet->setCellValue("C" . $payrollposition, "TOTAL");
                $objWorkSheet->setCellValue("D" . $payrollposition, '$' . number_format($payroll_gross_totals, 2));
                $objWorkSheet->getStyle("C" . $payrollposition)->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("D" . $payrollposition)->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("A" . $payrollposition . ":F" . $payrollposition)->applyFromArray($allborderssubheaderArray);

            endif;

            if ($sheet_details[$sheet_count]['roy_data']):
                $row = $payrollposition;
                $row++;
                $objWorkSheet->setCellValue("C" . $row, " ROY. & ADV. (First BR; Second Dunkin)");
                $objWorkSheet->setCellValue("D" . $row, 'DOLLAR AMT.');
                $objWorkSheet->getStyle("C" . $row)->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("D" . $row)->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("A" . $row . ":F" . $row)->applyFromArray($allborderssubheaderArray);
                $row++;
                $roy_totals = 0;
                foreach ($sheet_details[$sheet_count]['roy_data'] as $key => $report_details) {
                    $column = 2;
                    if (isset($report_details) > 0) {
                        foreach ($report_details as $key => $rowdata) {
                            if ($column == 0 || $column == 1 || $column == 3 || $column == 4) {
                                $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($leftstyle);
                            }
                            if ($column == 2 || $column == 5) {
                                $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($centerstyle);
                            }
                            if ($key == "debit_dollar_amt" && $rowdata != "") {
                                $roy_totals += $rowdata;
                                $rowdata = '$' . number_format($rowdata, 2);
                            }
                            if ($rowdata == "0"):
                                $rowdata = "";
                            endif;
                            $objWorkSheet->setCellValue($columns[$column] . $row, $rowdata);
                            $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($allborderssubheaderArray);
                            if ($column == 5) {
                                $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($rightheadersubheaderArray);
                            }

                            $column++;
                        }
                    }
                    $row++;
                }

                $row++;

                $objWorkSheet->setCellValue("C69", "TOTAL");
                $objWorkSheet->setCellValue("D69", '$' . number_format($roy_totals, 2));
                $objWorkSheet->getStyle("C69")->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("D69")->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("A69:F69")->applyFromArray($allborderssubheaderArray);

            endif;

            //deans foods
            $deansfood_totals = 0;
            $deans_count      = 5;

            $deans_row = 64;
            if ($sheet_details[$sheet_count]['dean_foods']):
                $objWorkSheet->setCellValue("E63", " DEAN FOODS");
                $objWorkSheet->setCellValue("F63", 'DOLLAR AMT.');
                $objWorkSheet->getStyle("E63")->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("F63")->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("A63:F63")->applyFromArray($allborderssubheaderArray);

                foreach ($sheet_details[$sheet_count]['dean_foods'] as $key => $report_details) {
                    $column = 4;
                    if (isset($report_details) > 0) {
                        foreach ($report_details as $key => $rowdata) {
                            if ($column == 0 || $column == 1 || $column == 3 || $column == 4) {
                                $objWorkSheet->getStyle($columns[$column] . $deans_row)->applyFromArray($leftstyle);
                            }
                            if ($column == 2 || $column == 5) {
                                $objWorkSheet->getStyle($columns[$column] . $deans_row)->applyFromArray($centerstyle);
                            }
                            if ($key == "debit_dollar_amt" && $rowdata != "") {
                                $deansfood_totals += $rowdata;
                                $rowdata = '$' . number_format($rowdata, 2);
                            }
                            if ($rowdata == "0"):
                                $rowdata = "";
                            endif;
                            $objWorkSheet->setCellValue($columns[$column] . $deans_row, $rowdata);
                            $objWorkSheet->getStyle($columns[$column] . $deans_row)->applyFromArray($allborderssubheaderArray);
                            if ($column == 5) {
                                $objWorkSheet->getStyle($columns[$column] . $deans_row)->applyFromArray($rightheadersubheaderArray);
                            }

                            $column++;
                        }
                    }
                    $deans_row++;
                }

                // $row++;

                $objWorkSheet->setCellValue("E69", "TOTAL");
                $objWorkSheet->setCellValue("F69", '$' . number_format($deansfood_totals, 2));
                $objWorkSheet->getStyle("E69")->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("F69")->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("A69:F69")->applyFromArray($allborderssubheaderArray);

            endif;
            //for credit entry record

            $position           = 65;
            $total_credit_count = count($sheet_details[$sheet_count]['credit_entry']);
            $c                  = 0;
            // $credit_totals =0 ;
            foreach ($sheet_details[$sheet_count]['credit_entry'] as $key => $report_details) {
                $column = 0;
                $c++;
                if (isset($report_details) > 0) {
                    if ($c == $total_credit_count) {
                        $objWorkSheet->setCellValue("A" . $position, "TOTAL");
                        $objWorkSheet->setCellValue("B" . $position, "$" . $credit_totals);
                        $objWorkSheet->getStyle("A" . $position)->applyFromArray($subheaderArray);
                        $objWorkSheet->getStyle("B" . $position)->applyFromArray($subheaderArray);

                        $position++;
                        $objWorkSheet->getStyle("A" . $position)->applyFromArray($subheaderArray);
                        $objWorkSheet->getStyle("B" . $position)->applyFromArray($subheaderArray);
                    }
                    foreach ($report_details as $key => $rowdata) {

                        if (($key == "credit_dollar_amt" || $key == "debit_dollar_amt") && $rowdata != "") {

                            if ($key == "credit_dollar_amt") {
                                $credit_totals += $rowdata;
                            }
                            $rowdata = '$' . number_format($rowdata, 2);
                        }
                        if ($rowdata == "0"):
                            $rowdata = "";
                        endif;

                        $objWorkSheet->setCellValue($columns[$column] . $position, $rowdata);
                        $objWorkSheet->getStyle($columns[$column] . $position)->applyFromArray($allborderssubheaderArray);
                        if ($column == 5) {
                            $objWorkSheet->getStyle($columns[$column] . $position)->applyFromArray($rightheadersubheaderArray);
                        }

                        $column++;
                    }
                }
                $position++;
            }
            //TOTAL CREDIT AND DEBITS
            if ($sheet_details[$sheet_count]['sheet_data']):
                $total_foods   = $deansfood_totals + $dcp_totals_debit + $donut_totals;
                $totals_debits = $payroll_net_totals + $roy_totals + $debit_totals;

                $objWorkSheet->setCellValue("A70", "TOTAL CREDITS");
                $objWorkSheet->setCellValue("B70", '$' . number_format($credit_totals, 2));
                $objWorkSheet->setCellValue("C70", "TOTAL DEBITS");
                $objWorkSheet->setCellValue("D70", '$' . number_format($totals_debits, 2));
                $objWorkSheet->setCellValue("E70", "TOTAL FOODS");
                $objWorkSheet->setCellValue("F70", '$' . number_format($total_foods, 2));
                $objWorkSheet->getStyle("A70")->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("B70")->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("C70")->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("D70")->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("E70")->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("F70")->applyFromArray($subheaderArray);
                $objWorkSheet->getStyle("A70")->applyFromArray($allborderssubheaderArray);
                $objWorkSheet->getStyle("B70")->applyFromArray($allborderssubheaderArray);
                $objWorkSheet->getStyle("C70")->applyFromArray($allborderssubheaderArray);
                $objWorkSheet->getStyle("D70")->applyFromArray($allborderssubheaderArray);
                $objWorkSheet->getStyle("E70")->applyFromArray($allborderssubheaderArray);
                $objWorkSheet->getStyle("F70")->applyFromArray($allborderssubheaderArray);
            endif;
            $objWorkSheet->getStyle("F3:F70")->applyFromArray($rightheadersubheaderArray);
            $row += 2;
            ////---------------------------------
            //for checkbook record
            $count = 0;
            if ($sheet_details[$sheet_count]['checkbook_data']):
                $row += 2;
                $objWorkSheet->mergeCells("A" . $row . ":F" . $row);
                $objWorkSheet->setCellValue("A" . $row, "CHECKBOOK RECORD");
                $objWorkSheet->getStyle("A" . $row)->applyFromArray($headersubheaderArray);
                $row++;
                $column = 0;

                foreach ($sheet_details[$sheet_count]['checkbook_heading'] as $key => $head) {
                    if ($key == 0 || $key == 2 || $key == 4) {
                        $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($rightstyle);
                    }
                    if ($key == 1 || $key == 3) {
                        $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($leftstyle);
                    }

                    if ($head == '$') {
                        if (isset($ledger_data[0]['ledger_balance'])) {
                            $head = '$' . $ledger_data[0]['balance_cf'];
                            $objWorkSheet->getStyle("F" . $row)->getFont()
                                ->getColor()->setRGB('FF0000');
                            $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($rightstyle);
                            $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($allbordersthicksubheaderArray);
                        } else {
                            $head = '$0.00';
                            $objWorkSheet->getStyle("F" . $row)->getFont()
                                ->getColor()->setRGB('FF0000');
                            $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($rightstyle);
                            $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($checkstyle);
                        }
                    }
                    $objWorkSheet->setCellValue($columns[$column] . $row, $head);
                    $objWorkSheet->getStyle("A" . $row . ":F" . $row)->applyFromArray($checkstyle);
                    $column++;
                }
                $row += 2;
                for ($j = 0; $j <= 5; $j++) {
                    $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($topheadersubheaderArray);
                    if ($j == 0) {
                        $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($checkstyle);
                        $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($allborderssubheaderArray);
                        $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($centerstyle);
                        $objWorkSheet->setCellValue($columns[$j] . $row, "Check Payable To");
                    }
                    if ($j == 1) {
                        $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($checkstyle);
                        $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($allborderssubheaderArray);
                        $objWorkSheet->setCellValue($columns[$j] . $row, "Check#");
                    }
                    if ($j == 2) {
                        $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($checkstyle);
                        $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($allborderssubheaderArray);
                        $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($centerstyle);

                        $objWorkSheet->setCellValue($columns[$j] . $row, "Memo");
                    }
                    if ($j == 3) {
                        $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($checkstyle);
                        $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($allborderssubheaderArray);
                        $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($centerstyle);
                        $objWorkSheet->setCellValue($columns[$j] . $row, "Amount");
                    }
                    if ($j == 4) {
                        $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($checkstyle);
                        $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($allborderssubheaderArray);
                        $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($centerstyle);
                        $objWorkSheet->setCellValue($columns[$j] . $row, "Credit Recived From");
                    }
                    if ($j == 5) {
                        $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($checkstyle);
                        $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($centerstyle);
                        $objWorkSheet->getStyle($columns[$j] . $row)->applyFromArray($allborderssubheaderArray);
                        $objWorkSheet->setCellValue($columns[$j] . $row, "Amount");
                    }
                }
                // $objWorkSheet->getStyle("A".$row.":F".$row)->applyFromArray($subheaderArray);

                $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->applyFromArray($bordertopsubheaderArray);

                $row++;
                $amount_due = 0;
                $credit_due = 0;
                $column     = 0;
                $count      = 0;
                foreach ($sheet_details[$sheet_count]['checkbook_data'] as $key => $report_details) {
                    $column = 0;

                    if (isset($report_details) > 0) {
                        $count++;
                        foreach ($report_details as $key => $rowdata) {

                            if ($key == "check_number") {
                                $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($centerstyle);
                            }
                            if ($key == "amount_due") {
                                $amount_due += $rowdata;
                                $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($rightstyle);
                            }
                            if ($key == "credit_due") {
                                $credit_due += $rowdata;
                                $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($rightstyle);
                            }
                            if (($key == "credit_due" || $key == "amount_due") && $rowdata != "" && $rowdata != "0") {
                                $rowdata = '$' . number_format($rowdata, 2);
                            }

                            if ($rowdata == "0"):
                                $rowdata = "";
                            endif;
                            $objWorkSheet->setCellValue($columns[$column] . $row, $rowdata);
                            $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($allborderssubheaderArray);
                            $objWorkSheet->getStyle($columns[$column] . $row)->applyFromArray($checkbook_font);

                            if ($column == 5) {
                                $objPHPExcel->getActiveSheet()->getStyle($columns[$column] . $row)->applyFromArray($rightheadersubheaderArray);
                            }

                            $column++;
                        }
                    }
                    $row++;
                }
            endif;

            if ($count != 0):
                // echo $count;exit;
                $increase_row = 31 - $count;

                for ($j = 0; $j <= $increase_row; $j++) {

                    $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->applyFromArray($allborderssubheaderArray);
                    if ($j == $increase_row) {
                        $objPHPExcel->getActiveSheet()->getStyle('A' . $row . ':F' . $row)->applyFromArray($bottomheadersubheaderArray);
                    }

                    $row++;
                }
                $objWorkSheet->getStyle("A3:F70")->applyFromArray($allborderssubheaderArray);
                $objPHPExcel->getActiveSheet()->getStyle('F75:F107')->applyFromArray($rightheadersubheaderArray);
                $objPHPExcel->getActiveSheet()->getStyle('A107:F107')->applyFromArray($bottomheadersubheaderArray);
                $objPHPExcel->getActiveSheet()->getStyle('A75:A107')->applyFromArray($leftheadersubheaderArray);
                $objPHPExcel->getActiveSheet()->getStyle('A75:F75')->applyFromArray($topheadersubheaderArray);

            endif;
            $objPHPExcel->getActiveSheet()->getStyle('F3:F70')->applyFromArray($rightheadersubheaderArray);
            $objPHPExcel->getActiveSheet()->getStyle('A70:F70')->applyFromArray($bottomheadersubheaderArray);
            $objPHPExcel->getActiveSheet()->getStyle('A3:A70')->applyFromArray($leftheadersubheaderArray);
            $objPHPExcel->getActiveSheet()->getStyle('A3:F3')->applyFromArray($topheadersubheaderArray);
            if ($sheet_details[$sheet_count]['sheet_data']):
                $row += 2;
                if (isset($ledger_data[0]['ledger_balance'])) {
                    $checkbook = $ledger_data[0]['balance_cf'];
                }

                $ending_balance = $checkbook - ($amount_due + $credit_due);
                $objWorkSheet->setCellValue("E" . $row, "ENDING BALANCE");
                $objWorkSheet->setCellValue("F" . $row, "$" . $ending_balance);
                $objWorkSheet->getStyle("E" . $row)->applyFromArray($checkstyle);
                $objWorkSheet->getStyle("F" . $row)->applyFromArray($allbordersthicksubheaderArray);
                $objWorkSheet->getStyle("F" . $row)->applyFromArray($rightstyle);
                $objWorkSheet->getStyle("E" . $row)->applyFromArray($rightstyle);
                $objWorkSheet->getStyle("F" . $row)->getFont()
                    ->getColor()->setRGB('FF0000');
            endif;

            $objWorkSheet->setTitle($sheet_details[$sheet_count]['sheet_title']);
            $sheet_count++;
        }

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="DDBR Ledger.xls"');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
        $objWriter->save('php://output');
        exit;
    }

    public function add_ledger_comment()
    {
        $data      = $this->input->post();
        $ledger_id = $this->input->post("comment_ledger_id");
        $result    = $this->ledger_statement_splits_model->add_comment_ledgersplit($data);
        if ($result >= 1) {
            $this->session->set_flashdata('success', 'Comments added successfully!!');
        } else {
            $this->session->set_flashdata('success', 'Nothing change in comments!!');
        }

        $call_from = $this->input->post('call_from');
        if($call_from == 'ledger')
            redirect('statement/view/' . $ledger_id, 'redirect');
        else
        {
            $call_from = explode('_', $call_from);
            redirect('reconcile/process?ledger_id='.$call_from[1].'&bank_id='.$call_from[2], 'redirect');
        }
    }

    public function get_assign_comment()
    {
        $ledger_id    = $this->input->post("ledger_id");
        $statement_id = $this->input->post("statement_id");
        $result       = $this->ledger_statement_splits_model->get_assign_comments($ledger_id, $statement_id);
        echo json_encode($result);
    }

    public function get_unassign_comment()
    {
        $ledger_id = $this->input->post("ledger_id");
        $result    = $this->ledger_statement_splits_model->get_unassign_comments($ledger_id);
        echo json_encode($result);
    }

    public function assign_ledger_comment()
    {
        $ledger_id    = $this->input->post("ledger_id");
        $statement_id = $this->input->post("statement_id");
        $hd_ids       = $this->input->post("hd_ids");
        $data         = $this->input->post();
        $result       = $this->ledger_statement_splits_model->set_unassign_comments($ledger_id, $statement_id, $hd_ids, $data);
        if ($result >= 1) {
            $this->session->set_flashdata('success', 'Comments reset successfully!!');
        } else {
            $this->session->set_flashdata('success', 'Nothing change in comments!!');
        }

        $call_from = $this->input->post('call_from');
        if($call_from == 'ledger')
            redirect('statement/view/' . $ledger_id, 'redirect');
        else
        {
            $call_from = explode('_', $call_from);
            redirect('reconcile/process?ledger_id='.$call_from[1].'&bank_id='.$call_from[2], 'redirect');
        }
    }

    public function get_upload_details()
    {
        $statement_id      = $this->input->post("statement_id");
        $description       = $this->input->post("description");
        $res               = $this->attachment_upload_model->Get(null, array("statement_id" => $statement_id));
        $is_invoice        = 0;
        $document_cnt      = 0;
        $uploaded_name_arr = array("is_invoice" => 0, "document_1" => 0, "document_2" => 0, "document_3" => 0);
        $Description_Res   = $description != '' ? $this->attachment_name_setting_model->Get(null, array("description" => $description)) : array();
        if (isset($Description_Res['records']) && !empty($Description_Res)) {
            foreach ($Description_Res['records'] as $dRow) {
                $invoice_name                                = $dRow->invoice_name;
                $document_name_1                             = $dRow->document_name_1;
                $document_name_2                             = $dRow->document_name_2;
                $document_name_3                             = $dRow->document_name_3;
                $uploaded_name_arr['custom_invoice_name']    = $invoice_name;
                $uploaded_name_arr['custom_document_name_1'] = $document_name_1;
                $uploaded_name_arr['custom_document_name_2'] = $document_name_2;
                $uploaded_name_arr['custom_document_name_3'] = $document_name_3;
            }
        }
        if (isset($res['records']) && !empty($res['records'])) {
            foreach ($res['records'] as $lRow) {
                $type = $lRow->type;
                if ($type == 'invoice') {
                    $uploaded_name_arr['is_invoice']   = 1;
                    $uploaded_name_arr['invoice_name'] = $lRow->uploaded_file_name;
                }
                if ($type == 'documents') {
                    $document_cnt++;
                    $uploaded_name_arr['document_' . $document_cnt]      = 1;
                    $uploaded_name_arr['document_name_' . $document_cnt] = $lRow->uploaded_file_name;
                }
            }
        }
        echo json_encode($uploaded_name_arr);
    }

    public function delete_entry()
    {
        $type      = $this->input->post('type');
        $delete_id = $this->input->post('id');

        if ($type == 'ledger') {
            $this->ledger_statement_model->Delete($delete_id);
        } else {
            $this->bank_statement_entries_model->Delete($delete_id);
        }
        echo json_encode(array("status" => 'success'));
        exit;
    }

    public function getValidMonthsForTheYear($value = '')
    {
        $importyear  = $this->input->post('importyear');
        $importstore = $this->input->post('importstore');

// month,sum(is_manual) as is_manual,
        //                         sum(is_reconcile) as is_reconcile
        $sql = "SELECT month FROM `ledger` l
                    JOIN `ledger_statement` ls ON ls.ledger_id = l.id
                    WHERE l.`store_key` = '{$importstore}' AND l.`year` = '{$importyear}'
                    GROUP BY l.`store_key`,l.`year`,l.`month`";

        $months = $this->ledger_model->query_result($sql);

        $validMonthsData = [1 => 1, 2 => 1, 3 => 1, 4 => 1, 5 => 1, 6 => 1, 7 => 1, 8 => 1, 9 => 1, 10 => 1, 11 => 1, 12 => 1];
        foreach ($months as $_months) {
            $valid = 1;
            /*if ($_months->is_manual) {
            $valid = 0;
            }

            if ($_months->is_reconcile) {
            $valid = 0;
            }*/

            if ($_months->month) {
                $valid = 0;
            }

            $validMonthsData[$_months->month] = $valid;
        }
        $data['valid_months'] = $validMonthsData;
        echo $this->load->view('ledger/import_month', $data, true);
        exit;
    }

}
