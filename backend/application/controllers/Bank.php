<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Bank extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('bank_statement_model');
        $this->load->model('bank_statement_entries_model');
        $this->load->model('store_master_model');
        $this->load->model('ledger_model');
    }

    public function index()
    {
        $data['title'] = 'Bank Statement List';
        if ($this->input->is_ajax_request()) {
            $bank_Res = $this->bank_statement_model->Get(null, $this->input->post());
            $this->getListing($bank_Res);
        } else {
            $data['store_list'] = $this->store_master_model->Get(null, array("status" => 'A'));
            $this->template->load('listing', 'bank/list-bank', $data);
        }
    }

    public function getListing($result = array())
    {
        $tableData = array();
        if (isset($result['records']) && !empty($result['records'])) {
            $max_year  = max(array_column($result['records'], 'year'));
            $max_month = $this->bank_statement_model->get_max_month($max_year);
        }
        foreach ($result['records'] as $key => $row) {

            $action   = array();
            $action[] = anchor('Bank/view/' . $row->id, 'View');
            /*if ($row->month == $max_month && $row->year == $max_year) {
            $action[] = anchor('javascript:void(0);', 'Delete', array('data-toggle' => 'modal', 'data-id' => $row->id, 'onclick' => 'setConfirmDetails(this)', ' data-target' => '#ConfirmDeleteModal', 'data-url' => 'bank/delete/' . $row->id));
            }*/

            $tableData[$key]['srNo']      = $key + 1;
            $tableData[$key]['store_key'] = $row->store_key;
            $tableData[$key]['month']     = monthName($row->month);
            $tableData[$key]['year']      = $row->year;
            $tableData[$key]['is_locked'] = ($row->is_locked == 1) ? "YES" : "NO";
            $tableData[$key]['action']    = implode(" | ", $action);
            $tableData[$key]['id']        = $row->id;
        }
        $data['data']            = $tableData;
        $data['recordsTotal']    = $result['countTotal'];
        $data['recordsFiltered'] = $result['countFiltered'];
        echo json_encode($data);
    }

    public function view($statement_id = null)
    {
        if ($statement_id == null) {
            $statement_id = $this->uri->segment(3);
        }
        $data['title']          = 'View Bank Statement';
        $data['statement_data'] = $this->bank_statement_entries_model->Get(null, array("bank_statement_id" => $statement_id));
        $data['bank_data']      = $this->bank_statement_model->Get($statement_id);
        $this->template->load('listing', 'bank/view_bank_statement', $data);
    }

    public function import()
    {
        $data['title']      = "Import Bank Statement";
        $data['store_list'] = $this->store_master_model->Get(null, array("status" => 'A'));
        $this->template->load('listing', 'bank/import_bank_statement', $data);
    }

    public function upload_bank_statement_file()
    {
        try {
            $selectedStoreId = isset($_POST['store_id']) ? $_POST['store_id'] : "";
            $selectedMonth   = isset($_POST['month']) ? $_POST['month'] : "";
            $selectedYear    = isset($_POST['year']) ? $_POST['year'] : "";

            if ($selectedStoreId != '' && $selectedMonth != '' && $selectedYear != '') {
                $tempFile   = $_FILES['import_file']['tmp_name'];
                $targetPath = FCPATH . "/files_upload/ledger_statement/" . $selectedStoreId . "/" . $selectedYear . "/" . $selectedMonth . "/";

                if (!file_exists($targetPath)) {
                    mkdir($targetPath, 0777, true);
                }

                $file_name  = $_FILES['import_file']['name'];
                $targetFile = $targetPath . $file_name; //5
                $res        = move_uploaded_file($tempFile, $targetFile);
                $file_path  = $targetFile;
                $csv        = $file_path;
                $file       = $csv;

                //fatch year from filename
                $store_idFromFile = $monthFromFile = $yearFromFile = '';
                if ($file_name != '') {
                    $file_name_arr = explode("-", $file_name);
                    if (!empty($file_name_arr)) {
                        $store_idFromFile = isset($file_name_arr[0]) ? $file_name_arr[0] : '';
                        $monthFromFile    = isset($file_name_arr[1]) ? $file_name_arr[1] : '';
                        $year             = isset($file_name_arr[2]) ? $file_name_arr[2] : '';
                        $yearFromFile     = (int) filter_var($year, FILTER_SANITIZE_NUMBER_INT);
                    }
                }
                if (sanitize($selectedYear) != sanitize("20" . $yearFromFile)) {
                    $this->session->set_flashdata('msg_class', "failure");
                    $this->session->set_flashdata('msg', "Wrong File! Selected year does not match with the file year.");
                    redirect('bank/import');
                }
                if (sanitize($selectedMonth) != sanitize($monthFromFile)) {
                    $this->session->set_flashdata('msg_class', "failure");
                    $this->session->set_flashdata('msg', "Wrong File! Selected month does not match with the file month.");
                    redirect('bank/import');
                }
                if (sanitize($selectedStoreId) != sanitize($store_idFromFile)) {
                    $this->session->set_flashdata('msg_class', "failure");
                    $this->session->set_flashdata('msg', "Wrong File! Selected store id does not match with the file store number.");
                    redirect('bank/import');
                }
                //Bank Statement Master Entry
                $bank_arr              = array();
                $bank_arr['store_key'] = $selectedStoreId;
                $bank_arr['month']     = $selectedMonth;
                $bank_arr['year']      = $selectedYear;

                $is_Exist_Res = $this->bank_statement_model->Get(null, array("store_key" => $bank_arr['store_key'], "month" => $bank_arr['month'], "year" => $bank_arr['year']));
                if ($is_Exist_Res['countFiltered'] > 0) {
                    $bank_id = $is_Exist_Res['records'][0]->id;
                    $type    = "update";
                } else {
                    $type    = "add";
                    $bank_id = $this->bank_statement_model->Add($bank_arr);
                }

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

                $data['cells']     = array_merge($header, $arr_data);
                $data['header']    = $header;
                $data['cells']     = $arr_data;
                $ins_data          = array();
                $ins_data['month'] = date('m');
                $ins_data['year']  = date('k');
                $transaction_type  = 'credit';

                //get number of entry logic starts
                $array = array_map(function ($element) {
                    $A = isset($element['A']) ? $element['A'] : '';
                    $B = isset($element['B']) ? $element['B'] : '';
                    $C = isset($element['C']) ? $element['C'] : '';
                    $D = isset($element['D']) ? $element['D'] : '';
                    $E = isset($element['E']) ? $element['E'] : '';
                    return $A . "-" . $B . "-" . $C . "-" . $D . "-" . $E;
                }, $data['cells']);
                $countOfEntries = (array_count_values($array));

                $addedCount = [];
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

                    if ($bank_id && isset($val['D']) && isset($val['A']) && isset($val['B']) && isset($val['D']) && $transaction_type && floatval($a4)) {

                        $date = $val['A'];
                        if (strstr($date, '-')) {
                            $date = str_replace("-", "/", $date);
                        }
                        $dt   = DateTime::createFromFormat('m/d/Y', $date);
                        $date = $dt->format('Y-m-d');

                        $condition = "";
                        $checkNum  = "";
                        if (isset($val['C'])) {
                            $condition = "AND `check_num` = " . $val['C'];
                            $checkNum  = $val['C'];
                        }
                        $sql = "SELECT * FROM `bank_statement_entries`
                                    WHERE
                                    `bank_statement_id` = " . $bank_id . "
                                    AND `date` = '" . $date . "'
                                    AND `transaction` = '" . $val['B'] . "'
                                    {$condition}
                                    AND `description` = '" . $val['D'] . "'
                                    AND `transaction_type` = '" . $transaction_type . "'
                                    AND `amount` = " . floatval($a4) . "
                                    ORDER BY `id` DESC";
                        $bData = $this->bank_statement_entries_model->query_result($sql);
                        $A     = isset($val['A']) ? $val['A'] : '';
                        $B     = isset($val['B']) ? $val['B'] : '';
                        $C     = isset($val['C']) ? $val['C'] : '';
                        $D     = isset($val['D']) ? $val['D'] : '';
                        $E     = isset($val['E']) ? $val['E'] : '';

                        $key              = $A . "-" . $B . "-" . $C . "-" . $D . "-" . $E;
                        $addedCount[$key] = isset($addedCount[$key]) ? $addedCount[$key] : 0;
                        if (!$bData || (isset($bData[0]) && (count($bData) + $addedCount[$key]) < $countOfEntries[$key]) || $type == "add") {
                            $dataAray = array("bank_statement_id" => $bank_id, "date" => $date, "transaction" => isset($val['B']) ? $val['B'] : '', "check_num" => isset($val['C']) ? $val['C'] : '', "description" => isset($val['D']) ? $val['D'] : '', "transaction_type" => $transaction_type, "amount" => floatval($a4));

                            if (strpos($val['D'], "TRANSFER TO CHECKING") !== false) {
                                $accountNumber              = substr($val['D'], strrpos($val['D'], '*') + 1);
                                $accountNumber              = substr($accountNumber, 0, 5);
                                $dataAray['account_number'] = str_replace(' ', '', $accountNumber);
                            } else {
                                $dataAray['account_number'] = "";
                            }
                            $dataAray['no_of_entries'] = $countOfEntries[$key];
                            $statement_entries[]       = $dataAray;
                            $addedCount[$key]++;
                            if (count($bData)) {
                                foreach ($bData as $_bData) {
                                    $bDataAry                  = [];
                                    $bDataAry['no_of_entries'] = $countOfEntries[$key];
                                    $this->bank_statement_entries_model->Edit($_bData->id, $bDataAry);
                                }
                            }
                        }
                    }
                }

                if (isset($statement_entries) && count($statement_entries)) {
                    $this->bank_statement_entries_model->add_batch($statement_entries);
                }

                redirect('bank', true);
            } else {
                $this->session->set_flashdata('msg_class', "failure");
                $this->session->set_flashdata('msg', "Please select all the required fields");
                redirect('bank/import');
            }
        } catch (Exception $e) {
            //alert the user then kill the process
            og_message('error', $e->getMessage());
            return;
        }
    }

    public function upload_bank_statement_file_bk()
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

    public function delete($id = null)
    {
        if ($id) {
            $bank_Res = $this->bank_statement_model->Get($id);
            $status   = isset($bank_Res->status) ? $bank_Res->status : "";
            if ($status != 'unreconcile') {
                $month = isset($ledger_Res->month) ? $ledger_Res->month : "";
                $year  = isset($ledger_Res->year) ? $ledger_Res->year : "";
                $this->load->model('ledger_model');
                $ledger_Res = $this->ledger_model->Delete_where(array("month" => $month, "year" => $year));

                $bank_entries_datas = $this->bank_statement_entries_model->Get(null, array("is_reconciled_current" => 2));

                if (isset($bank_entries_datas['records']) && !empty($bank_entries_datas['records'])) {
                    foreach ($bank_entries_datas['records'] as $bRow) {
                        $bank_statement_ids[] = $bRow->id;
                    }
                    if (isset($bank_statement_ids) && !empty($bank_statement_ids)) {
                        $this->load->model('ledger_statement_model');
                        $up_data                      = array();
                        $up_data['is_reconcile']      = 0;
                        $up_data['bank_statement_id'] = '';
                        $up_data['reconcile_type']    = '';
                        $up_data['reconcile_date']    = '';
                        $this->ledger_statement_model->update_where($bank_statement_ids, $up_data);
                    }
                }
            }
            $res = $this->bank_statement_model->Delete($id);

            if ($res) {
                redirect('bank', 'redirect');
            }
        }
    }

    public function void_entry()
    {
        $bank_statement_id = $this->input->post('bank_statement_id');
        if ($bank_statement_id) {
            $value                   = $this->input->post('void_val');
            $up_data                 = array();
            $up_data['is_void']      = $value;
            $up_data['is_reconcile'] = $value;
            $res                     = $this->bank_statement_entries_model->Edit($bank_statement_id, $up_data);
            $arr                     = array("status" => "success");
        } else {
            $arr = array("status" => "failureD");
        }
        $bankData = $this->bank_statement_entries_model->Get(null, array("id" => $bank_statement_id));
        updateBankStatus($bankData['records'][0]->bank_statement_id);
        echo json_encode($arr);
        exit;
    }
}
