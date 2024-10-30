<?php
defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';
class Donut extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('bill_item_model');
        $this->load->model('bill_model');
        $this->load->model('store_master_model');
    }

    public function donut()
    {
        $data['title'] = 'Weelkly';
        echo $this->load->view('bill/donut', $data, true);
        exit;
    }

    public function getDataFromOtherSite()
    {
        $postData    = $this->input->post();
        $websitedata = $postData['websitedata'];
        $returnData  = [];
        // echo '<pre>';print_r($websitedata);die;

        $totalPos    = strpos($websitedata, "Total");
        $totalLength = strlen("Total");

        $privacyPos    = strpos($websitedata, "Privacy Policy");
        $privacyLength = strlen("Privacy Policy");

        if (!$totalPos || !$privacyPos) {
            $returnData['status'] = "failure";
        } else {

            $returnData['status']     = "success";
            $allInvoiceData           = substr($websitedata, ($totalPos + $totalLength), $privacyPos - ($totalPos + $totalLength));
            $allInvoiceDataAry        = explode("Print", $allInvoiceData);
            $returnData['donut_data'] = [];
            foreach ($allInvoiceDataAry as $_allInvoiceData) {
                if (removeSpaces($_allInvoiceData)) {
                    $totalInvoiceLength = strlen($_allInvoiceData);
                    $invoicePos         = strpos($_allInvoiceData, "Invoice #");
                    $invoiceLength      = strlen("Invoice #");
                    $invoice            = substr($_allInvoiceData, ($invoicePos + $invoiceLength), $totalInvoiceLength);
                    // $mansi = [];
                    // foreach (str_split($invoice) as $_invoice) {
                    //     $mansi[] = $_invoice ."=". ord($_invoice);
                    // }
                    // echo '<pre>';print_r($mansi);die;
                    $donut_import_data = array_filter(explode(chr(9), $invoice)); //9 is a ascii value of space

                    $donut_data['store_key']     = getStoreNumberFromCustomerForDonut($donut_import_data[1]);
                    $donut_data['bill_number']   = $donut_import_data[0];
                    $donut_data['store_address'] = $donut_import_data[1];
                    $donut_data['bill_date']     = $donut_import_data[2];
                    $donut_data['bill_desc']     = $donut_import_data[3];
                    $donut_data['bill_cat']      = "donut_purchases";
                    $donut_data['bill_qty']      = 1;
                    $donut_data['bill_rate']     = $donut_import_data[4];
                    $donut_data['bill_amt']      = $donut_import_data[4];

                    $weekDatesArray = explode("-", $donut_data['bill_desc']);
                    $startMonth     = date('m', strtotime($weekDatesArray[0]));
                    $endMonth       = date('m', strtotime($weekDatesArray[1]));

                    $donut_data['bill_week_start_date'] = $weekDatesArray[0];
                    $donut_data['bill_week_end_date']   = $weekDatesArray[1];

                    if ($startMonth == $endMonth) {
                        //dates
                        $startDate = date('d', strtotime($weekDatesArray[0]));
                        $endDate   = date('d', strtotime($weekDatesArray[1]));
                        if (($endDate - $startDate) == 6) {
                            $donut_data['result'] = "success";
                        } else {
                            $donut_data['result'] = "fail";
                        }
                    } else {
                        $donut_data['result'] = "fail";
                    }

                    $returnData['donut_data'][] = $donut_data;
                }
            }

        }
        echo $this->load->view('bill/donut_data', $returnData, true);
        exit;
    }

    public function saveDonutData()
    {
        $postData = $this->input->post();
        if (count($postData['donut'])) {
            $donutInsertData = [];
            // echo '<pre>';print_r($postData['donut']);die;
            foreach ($postData['donut'] as $_donutData) {
                if ($_donutData['result'] == 'success') {
                    $month = date('m', strtotime($_donutData['bill_week_end_date']));
                    $year  = date('Y', strtotime($_donutData['bill_week_end_date']));

                    //get Bill Id
                    $bills = $this->bill_model->Get(null, array("month" => $month, "year" => $year));
                    if (!count($bills['records'])) {
                        $billData['month']      = (int) $month;
                        $billData['year']       = $year;
                        $billData['created_on'] = date('Y-m-d H:i:s');
                        $billData['updated_on'] = date('Y-m-d H:i:s');

                        $billId = $this->bill_model->Add($billData);
                    } else {
                        $billId = $bills['records'][0]->id;
                    }

                    $billItem = $this->bill_item_model->Get(null, array('bill_id' => $billId, 'store_key' => $_donutData['store_key'], 'category_key' => $_donutData['bill_cat'], 'description' => $_donutData['bill_week_end_date']));

                    if (count($billItem['records'])) {

                        //Donut Purchase Already Exist
                        if (!$billItem['records'][0]->amount) {
                            continue;
                        }

                        $donutUpdateData = array(
                            "store_physical_address" => isset($_donutData['store_address']) ? $_donutData['store_address'] : '',
                            "bill_date"              => isset($_donutData['bill_date']) ? date("Y-m-d", strtotime($_donutData['bill_date'])) : '',
                            "bill_no"                => isset($_donutData['bill_number']) ? $_donutData['bill_number'] : '',
                            "qty"                    => isset($_donutData['bill_qty']) ? $_donutData['bill_qty'] : '',
                            "rate"                   => isset($_donutData['bill_rate']) ? remove_format(trim($_donutData['bill_rate'], '$')) : '',
                            "amount"                 => isset($_donutData['bill_amt']) ? remove_format(trim($_donutData['bill_amt'], '$')) : '',
                        );
                        $this->bill_item_model->Edit($billItem['records'][0]->id, $donutUpdateData);
                    }
                    else
                    {
                        $donutInsertData[] = array("bill_id" => $billId,
                            "store_key"                          => isset($_donutData['store_key']) ? $_donutData['store_key'] : '',
                            "store_physical_address"             => isset($_donutData['store_address']) ? $_donutData['store_address'] : '',
                            "bill_date"                          => isset($_donutData['bill_date']) ? date("Y-m-d", strtotime($_donutData['bill_date'])) : '',
                            "bill_no"                            => isset($_donutData['bill_number']) ? $_donutData['bill_number'] : '',
                            "category_key"                       => isset($_donutData['bill_cat']) ? $_donutData['bill_cat'] : '',
                            "description"                        => isset($_donutData['bill_week_end_date']) ? date("Y-m-d", strtotime($_donutData['bill_week_end_date'])) : '',
                            "qty"                                => isset($_donutData['bill_qty']) ? $_donutData['bill_qty'] : '',
                            "rate"                               => isset($_donutData['bill_rate']) ? remove_format(trim($_donutData['bill_rate'], '$')) : '',
                            "amount"                             => isset($_donutData['bill_amt']) ? remove_format(trim($_donutData['bill_amt'], '$')) : '',
                            "last_paid_date"                     => '',
                            "last_paid_amount"                   => '',
                            "is_paid"                            => 0,
                            "status"                             => 'Unpaid',
                            "attachment"                         => '',
                            "type"                               => 'week_description',
                        );
                    }
                }
            }
            if (isset($donutInsertData) && !empty($donutInsertData)) {
                $this->bill_item_model->Add_Batch($donutInsertData);
            }
        }
        $data['status']   = "success";
        $data['redirect'] = "bill/add/$billId";
        echo json_encode($data);
    }

    public function importDonutPurchaseForWholeMonthWeekWise()
    {
        if (!empty($_FILES['file']['name'])) {
            $file_name  = $_FILES['file']['name'];
            $targetPath = FCPATH . "/files_upload/donut_purchases/";
            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0777, true);
            }
            $file_name  = date("YmdHis") . $file_name;
            $tempFile   = $_FILES['file']['tmp_name'];
            $readerfile = "files_upload/donut_purchases/" . $file_name;
            $targetFile = $targetPath . $file_name;
            if (move_uploaded_file($tempFile, $targetFile)) {
                $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
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

                    $allStoreKeys   = $this->store_master_model->get_all_store_keys();
                    $worksheetcount = $spreadsheet->getSheetCount();
                    for ($i = 0; $i < $worksheetcount; $i++) {
                        $goOn                  = 0;
                        $donutPurchaseBillData = [];
                        $currentworksheet      = $spreadsheet->getSheet($i);
                        $storeKey              = 0;

                        //Validations for the correct sheet
                        if ((trim($currentworksheet->getCell('A3')->getCalculatedValue()) != 'Statement')
                            || (trim($currentworksheet->getCell('E3')->getCalculatedValue()) != 'Start Date:')
                            || (trim($currentworksheet->getCell('E4')->getCalculatedValue()) != 'End Date:')
                            || (trim($currentworksheet->getCell('I3')->getCalculatedValue()) != 'Group By:')
                            || (trim($currentworksheet->getCell('K3')->getCalculatedValue()) != '(none)')
                            || (trim($currentworksheet->getCell('I4')->getCalculatedValue()) != 'Group Total:')
                            || (trim($currentworksheet->getCell('A5')->getCalculatedValue()) != 'Invoices from (BMG Bakery)')) {
                            $data['status'] = "failure";
                            $data['msg']    = "Wrong File Uploaded";
                            echo $this->load->view('bill/donut_data', $data, true);
                            exit;
                        }

                        //check whole month data is available in the sheet
                        $startDateOfMonth = trim($currentworksheet->getCell('G3')->getCalculatedValue());
                        $endDateOfMonth   = trim($currentworksheet->getCell('G4')->getCalculatedValue());

                        // echo date('m', strtotime($startDateOfMonth));
                        // echo "===";
                        // echo date('m', strtotime($endDateOfMonth));
                        // die;
                        if (date('m', strtotime($startDateOfMonth)) != date('m', strtotime($endDateOfMonth))
                            || date('d', strtotime($startDateOfMonth)) != 1
                            || !in_array(date('d', strtotime($endDateOfMonth)), [30, 31])) {
                            $data['status'] = "failure";
                            $data['msg']    = "Wrong File Uploaded";
                            echo $this->load->view('bill/donut_data', $data, true);
                            exit;
                        }

                        foreach ($currentworksheet->getRowIterator() as $key => $row) {
                            $weekEndingDateIsValid = 0;
                            foreach ($row->getCellIterator() as $cell) {
                                if (trim($cell->getCalculatedValue()) != "") {

                                    //Get Store Ids
                                    foreach ($allStoreKeys as $_allStoreKey) {
                                        $storeKeyPos = strpos($cell->getCalculatedValue(), $_allStoreKey);
                                        if ($storeKeyPos !== false) {
                                            $storeKey = $_allStoreKey;
                                        }
                                    }
                                    $invoicePos = strpos($cell->getCalculatedValue(), "Invoice #");
                                    if (($invoicePos !== false || $goOn == 1) && $storeKey) {
                                        if (!isset($donutPurchaseBillData[$row->getRowIndex() - 1])) {
                                            $donutPurchaseBillData[$row->getRowIndex() - 1] = [];
                                        }
                                        $donutPurchaseBillData[$row->getRowIndex() - 1]['store_key'] = $storeKey;

                                        //get Bill Id from date
                                        if ($cell->getColumn() == 'F') {
                                            $donutPurchaseBillData[$row->getRowIndex() - 1]['bill_date']          = date('Y-m-d', strtotime($cell->getCalculatedValue()));
                                            $donutPurchaseBillData[$row->getRowIndex() - 1]['bill_desc']          = date('Y-m-d', strtotime($cell->getCalculatedValue()));
                                            $donutPurchaseBillData[$row->getRowIndex() - 1]['bill_week_end_date'] = date('Y-m-d', strtotime($cell->getCalculatedValue()));

                                            if ($donutPurchaseBillData[$row->getRowIndex() - 1]['bill_week_end_date']) {
                                                $dateInfo = getDateInfo($donutPurchaseBillData[$row->getRowIndex() - 1]['bill_week_end_date']);

                                                $donutPurchaseBillData[$row->getRowIndex() - 1]['bill_week_start_date'] = $dateInfo['start_of_week'];
                                                if ($dateInfo['end_of_week'] == $donutPurchaseBillData[$row->getRowIndex() - 1]['bill_week_end_date']) {
                                                    $weekEndingDateIsValid = 1;
                                                }
                                            }
                                        }

                                        //get Bill Info from Invoice
                                        if ($invoicePos !== false) {
                                            $invoiceInfo = $cell->getCalculatedValue();

                                            $donutPurchaseBillData[$row->getRowIndex() - 1]['store_address'] = $invoiceInfo;

                                            $billNo = substr($invoiceInfo, (strpos($invoiceInfo, "#") + 1), strpos($invoiceInfo, " on ") - (strpos($invoiceInfo, "#") + 1));

                                            $donutPurchaseBillData[$row->getRowIndex() - 1]['bill_number'] = trim($billNo);
                                        }
                                        $donutPurchaseBillData[$row->getRowIndex() - 1]['bill_cat'] = "donut_purchases";
                                        $donutPurchaseBillData[$row->getRowIndex() - 1]['bill_qty'] = 1;

                                        // get rate,amount
                                        if ($cell->getColumn() == 'H') {
                                            $donutPurchaseBillData[$row->getRowIndex() - 1]['bill_rate'] = remove_format(trim($cell->getCalculatedValue(), '$'));
                                            $donutPurchaseBillData[$row->getRowIndex() - 1]['bill_amt']  = remove_format(trim($cell->getCalculatedValue(), '$'));
                                        }
                                        $goOn = 1;
                                    }
                                }
                            }
                            if ($weekEndingDateIsValid == 1) {
                                $donutPurchaseBillData[$row->getRowIndex() - 1]['result'] = "success"; // for 7 days data its success

                                $data['donut_data'] = $donutPurchaseBillData;
                            }
                            $goOn = 0;
                        }
                    }
                    $data['status'] = "success";
                } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                    $data['status'] = "failure";
                    $data['msg']    = $e->getMessage();
                }
            } else {
                $data['status'] = "failure";
                $data['msg']    = "File not found";
            }
        }
        echo $this->load->view('bill/donut_data', $data, true);
        exit;
    }

    public function importDonutPurchaseForDays()
    {
        if (!empty($_FILES['file']['name'])) {
            $file_name  = $_FILES['file']['name'];
            $targetPath = FCPATH . "/files_upload/donut_purchases/last_week/";
            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0777, true);
            }
            $file_name  = date("YmdHis") . "_" . $file_name;
            $tempFile   = $_FILES['file']['tmp_name'];
            $targetFile = $readerfile = $targetPath . $file_name;
            if (move_uploaded_file($tempFile, $targetFile)) {
                $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
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

                    $allStoreKeys   = $this->store_master_model->get_all_store_keys();
                    $worksheetcount = $spreadsheet->getSheetCount();
                    for ($i = 0; $i < $worksheetcount; $i++) {
                        $goOn                  = 0;
                        $donutPurchaseBillData = [];
                        $currentworksheet      = $spreadsheet->getSheet($i);
                        $storeKey              = 0;
                        $amountColumnName      = 0;

                        //Validations for the correct sheet
                        if ((trim($currentworksheet->getCell('A3')->getCalculatedValue()) != 'Order Summary')
                            || (trim($currentworksheet->getCell('E3')->getCalculatedValue()) != 'Start Date')
                            || (trim($currentworksheet->getCell('E4')->getCalculatedValue()) != 'End Date')
                            || (trim($currentworksheet->getCell('A8')->getCalculatedValue()) != 'Purchase orders')
                            || (trim($currentworksheet->getCell('A5')->getCalculatedValue()) != 'Network Report')) {

                            $data['status'] = "failure";
                            $data['msg']    = "Wrong File Uploaded";
                            echo $this->load->view('bill/donut_data', $data, true);
                            exit;
                        }

                        //check whole month data is available in the sheet
                        $startDate = trim($currentworksheet->getCell('G3')->getCalculatedValue());
                        $endDate   = trim($currentworksheet->getCell('G4')->getCalculatedValue());

                        if (date('m', strtotime($startDate)) != date('m', strtotime($endDate))
                            || !in_array(date('d', strtotime($endDate)), [30, 31])
                            || dateDiffInDays($startDate, $endDate) >= 6) {
                            $data['status'] = "failure";
                            $data['msg']    = "Wrong File Uploaded - Days Duration is wrong/Month Ending Date is not matched";
                            echo $this->load->view('bill/donut_data', $data, true);
                            exit;
                        }

                        $data['start_date'] = date('Y-m-d', strtotime($startDate));
                        $data['end_date']   = date('Y-m-d', strtotime($endDate));
                        foreach ($currentworksheet->getRowIterator() as $key => $row) {
                            $weekEndingDateIsValid = 0;
                            foreach ($row->getCellIterator() as $cell) {
                                if (trim($cell->getCalculatedValue()) != "") {

                                    //Get Store Ids
                                    foreach ($allStoreKeys as $_allStoreKey) {
                                        $storeKeyPos = strpos($cell->getCalculatedValue(), $_allStoreKey);
                                        if ($storeKeyPos !== false) {
                                            $storeKey = $_allStoreKey;
                                        }
                                    }
                                    if ($cell->getCalculatedValue() == "Sum Value Invoiced") {
                                        $amountColumnName = $cell->getColumn();
                                    }

                                    if ($storeKey && $amountColumnName) {

                                        // echo $amountColumnName.$row->getRowIndex();exit;
                                        $amount = $currentworksheet->getCell($amountColumnName . ($row->getRowIndex() - 1))->getCalculatedValue();
                                        if ($amount) {
                                            $donutPurchaseBillData[$storeKey]['store_key'] = $storeKey;
                                            $donutPurchaseBillData[$storeKey]['bill_amt']  = remove_format(trim($amount, '$'));
                                            break;
                                        }
                                    }
                                }
                            }
                        }
                        $data['donut_data'] = $donutPurchaseBillData;
                    }
                    $data['status'] = "success";
                } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
                    $data['status'] = "failure";
                    $data['msg']    = $e->getMessage();
                }
            } else {
                $data['status'] = "failure";
                $data['msg']    = "File not found";
            }
        }
        echo $this->load->view('bill/donut_week_data', $data, true);
        exit;
    }

    public function saveDonutLastWeekData()
    {
        $postData = $this->input->post();
        // echo '<pre>';print_r($postData);die;
        if (count($postData['donut'])) {
            $donutInsertData = [];
            foreach ($postData['donut'] as $_donutData) {

                //get Week endign date
                $endDate  = date('Y-m-d', strtotime($postData['end_date']));
                $dateInfo = getDateInfo($endDate);

                $weekEndingDate = $dateInfo['end_of_week'];
                $month          = date('m', strtotime($weekEndingDate));
                $year           = date('Y', strtotime($weekEndingDate));
                //get Bill Id
                $bills = $this->bill_model->Get(null, array("month" => $month, "year" => $year));
                if (!count($bills['records'])) {
                    $billData['month']      = (int) $month;
                    $billData['year']       = $year;
                    $billData['created_on'] = date('Y-m-d H:i:s');
                    $billData['updated_on'] = date('Y-m-d H:i:s');

                    $billId = $this->bill_model->Add($billData);
                } else {
                    $billId = $bills['records'][0]->id;
                }

                $billItem = $this->bill_item_model->Get(null, array('bill_id' => $billId, 'store_key' => $_donutData['store_key'], 'category_key' => "donut_purchases", 'description' => $dateInfo['end_of_week']));
                if (count($billItem['records'])) {
                    $donutUpdateData = array(
                        "last_week_amount" => isset($_donutData['bill_amt']) ? $_donutData['bill_amt'] : '',
                    );
                    $this->bill_item_model->Edit($billItem['records'][0]->id, $donutUpdateData);
                } else {
                    $donutInsertData = array("bill_id" => $billId,
                        "store_key"                        => isset($_donutData['store_key']) ? $_donutData['store_key'] : '',
                        "store_physical_address"           => isset($_donutData['store_address']) ? $_donutData['store_address'] : '',
                        "bill_date"                        => '',
                        "bill_no"                          => '',
                        "category_key"                     => 'donut_purchases',
                        "description"                      => isset($dateInfo['end_of_week']) ? date("Y-m-d", strtotime($dateInfo['end_of_week'])) : '',
                        "qty"                              => 0,
                        "rate"                             => 0,
                        "amount"                           => 0,
                        "last_week_amount"                 => isset($_donutData['bill_amt']) ? $_donutData['bill_amt'] : '',
                        "last_paid_date"                   => '',
                        "last_paid_amount"                 => '',
                        "is_paid"                          => 0,
                        "status"                           => 'Unpaid',
                        "attachment"                       => '',
                        "type"                             => 'week_description',
                    );
                    $this->bill_item_model->Add($donutInsertData);
                }
            }
        }
        $data['status']   = "success";
        $data['redirect'] = "bill/add/$billId";
        echo json_encode($data);
    }
}
