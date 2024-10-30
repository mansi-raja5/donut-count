<?php
defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Masterpos extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('masterpos_model');
        $this->load->model('store_master_model');
    }
    public function index()
    {
        $data['title'] = 'Master POS';
        $this->template->load('listing', 'masterpos', $data);
    }

    public function import()
    {
        try {  
            if(empty($_FILES['import_file']['name'])){
                $msg = "Something went wrong file is not uploaded!";
                $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$msg.'</div>');
                if ($this->input->is_ajax_request()) {
                    echo json_encode(array('type'=> 'error', 'popup' => 0, 'message'=> $msg));
                    return;
                }
            }         

            // get file extension
            $targetPath = FCPATH . "files_upload/master_pos/";
            $fileInfo = pathinfo($_FILES['import_file']['name']);
            $file_name = $fileInfo['filename'] . '_' . date('Ymd') . '_' . date('His') . '.' . $fileInfo['extension'];
            $file_failure_name = urlencode("fail_".$fileInfo['filename'] . '_' . date('Ymd') . '_' . date('His') . '.xls');
            $relativePath = 'files_upload/master_pos/' . $file_name;
            $targetFile = $targetPath . $file_name;
            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0777, true);
            }

            ini_set('memory_limit', '-1'); //need to add for large files
            if(!move_uploaded_file($_FILES['import_file']['tmp_name'], $targetFile)){
                $msg = "Something went wrong file is not uploaded!";
                $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$msg.'</div>');
                if ($this->input->is_ajax_request()) {
                    echo json_encode(array('type'=> 'error', 'popup' => 0, 'message'=> $msg));
                    return;
                }
            }            

            if ($fileInfo['extension'] == 'csv') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                $reader->setLoadAllSheets();
                $spreadsheet = $reader->load($targetFile);
            } else if ($fileInfo['extension'] == 'xlsx' || $fileInfo['extension'] == 'xlsm') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $reader->setLoadAllSheets();
                $spreadsheet = $reader->load($targetFile);
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                $reader->setLoadAllSheets();
                $spreadsheet = $reader->load($targetFile);
            }

            $getallkey = $this->masterpos_model->getallkey();
            
            $currentworksheet = $spreadsheet->getSheetByName('POS');
            if(!$currentworksheet){
                $msg = "POS sheet is not present in the workbook!";
                $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$msg.'</div>');
                if ($this->input->is_ajax_request()) {
                    echo json_encode(array('type'=> 'error', 'popup' => 0, 'message'=> $msg));
                    return;
                }
            }

            $databaseRows = $keys_failed_data = $header_array = [];
            $keys_failed_data['date_diff'] = $keys_failed_data['mismatch'] = $keys_failed_data['duplicate_db'] = $keys_failed_data['duplicate'] = $keys_failed_data['other'] = $removeUids = [];            
            $table = "";
            $rowCounter = 0;

            //create new instance of spreadsheet for failed records
            $this->failedSpreadsheet = new Spreadsheet();
            $this->writeFailDataInSheet("Row Number", "Reason");

            foreach ($currentworksheet->getRowIterator() as $key => $row) {
                $data = $json_data = [];
                $start_date = $end_date = "";
                $cell_counter = 0;
                $validRow = 1;
                $cellIterator = $row->getCellIterator();
                foreach ($cellIterator as $cell) {
                    if ($key == 1) {
                        $header_array[] = trim($cell->getValue());
                    } else {
                        if ($cell_counter == 0) {
                            if($cell->getValue()) {
                                //check for store exist in system or not
                                $storeData = $this->store_master_model->Get_By_Key(trim($cell->getValue()));
                                if(count((array)$storeData)) {
                                    $data["store_key"] = trim($cell->getValue());
                                } else {
                                    $keys_failed_data['other'][$key] = 1; //added 1 as no data found to store
                                    $this->writeFailDataInSheet($key,"Store number is not registered in the system! Please register it from the Stores tab!");
                                    $validRow = 0;
                                }
                            } else {
                                $keys_failed_data['other'][$key] = 1; //added 1 as no data found to store
                                $this->writeFailDataInSheet($key,"Store Number can not be blank!");
                                $validRow = 0;
                            }
                        } else if ($cell_counter == 1) {
                            if($cell->getCalculatedValue()) {
                                $unixTimestamp = ($cell->getCalculatedValue() - 25569) * 86400;
                                $start_date = date('Y-m-d', $unixTimestamp);
                            } else {
                                $keys_failed_data['other'][$key] = 1;
                                $this->writeFailDataInSheet($key,"Start Business Date can not be blank!");
                                $validRow = 0;
                            }                            
                        } else if ($cell_counter == 2 && $cell->getCalculatedValue()) {
                            if($cell->getCalculatedValue()) {
                                $unixTimestamp = ($cell->getCalculatedValue() - 25569) * 86400;
                                $end_date = date('Y-m-d', $unixTimestamp);
                            } else {
                                $keys_failed_data['other'][$key] = 1;
                                $this->writeFailDataInSheet($key,"End Business Date can not be blank!");
                                $validRow = 0;
                            }                             

                            $start_date_obj = new DateTime($start_date);
                            $end_date_obj = new DateTime($end_date);
                            $days = $end_date_obj->diff($start_date_obj)->format('%a');
                            if ($days == 0) {
                                $table = "master_pos_daily";
                                $data['cdate'] = $start_date;
                            } else if ($days == 6 && date('w',strtotime($end_date)) == 6  && date('w',strtotime($start_date)) == 0) {
                                $table = "master_pos_weekly";
                                $data["start_date"] = $start_date;
                                $data["end_date"] = $end_date;
                            } else if (in_array($days,[29,30,31])) {
                                $table = "master_pos_monthly";
                                $data["start_date"] = $start_date;
                                $data["end_date"] = $end_date;
                            } else {
                                $keys_failed_data['date_diff'][$key]['store_key'] = $data["store_key"];
                                $keys_failed_data['date_diff'][$key]['start_date'] = date('m/d/Y', strtotime($start_date));
                                $keys_failed_data['date_diff'][$key]['end_date'] = date('m/d/Y', strtotime($end_date));
                                $keys_failed_data['date_diff'][$key]['result'] = $days;
                                $this->writeFailDataInSheet($key,"Differance between start date and end date is not valid!");
                            }
                        } else {
                            if ($cell_counter >= 3) {
                                $json_data[array_search($header_array[$cell_counter], $getallkey)] = $cell->getValue();
                            }
                        }
                    }
                    $cell_counter++;
                }
                if ($key > 1 && sizeof($data) > 0 && $validRow) {
                    $cardrecap = [];
                    $deliveryrecap = [];
                    $monthlyrecap = [];

                    if (isset($data['cdate']) && count($json_data)) {
                        //card recap data
                        $cardrecap['store_key'] = $data["store_key"];
                        $cardrecap['month'] = date("M", strtotime($data['cdate']));
                        $cardrecap['year'] = date("Y", strtotime($data['cdate']));
                        $cardrecap['cdate'] = $data['cdate'];
                        $cardrecap['day'] = date("D", strtotime($data['cdate']));
                        $cardrecap['master_transaction'] = $json_data['mastercard_qty'];
                        $cardrecap['master_amount'] = $json_data['mastercard_amount'];
                        $cardrecap['visa_transaction'] = $json_data['visa_qty'];
                        $cardrecap['visa_amount'] = $json_data['visa_amount'];
                        $cardrecap['amex_transaction'] = $json_data['american_express_qty'];
                        $cardrecap['amex_amount'] = $json_data['american_express_amount'];
                        $cardrecap['discover_transaction'] = $json_data['discover_novis_qty'];
                        $cardrecap['discover_amount'] = $json_data['discover_novis_amount'];
                        $cardrecap['cc_recap_total_sales'] = $cardrecap['master_amount'] + $cardrecap['visa_amount'] + $cardrecap['amex_amount'] + $cardrecap['discover_amount'];
                        $cardrecap['dunkin_transaction'] = $json_data['gift_card_sales_amount'];
                        $cardrecap['dunkin_amount'] = $json_data['gift_card_amount'];
                        $cardrecap['dd_cards_total'] = $cardrecap['dunkin_amount'] - $cardrecap['dunkin_transaction'];
                        $cardrecap['dd_paper_redeemed'] = null;

                        //delivery recap
                        $deliveryrecap['store_key'] = $data["store_key"];
                        $deliveryrecap['month'] = date("M", strtotime($data['cdate']));
                        $deliveryrecap['year'] = date("Y", strtotime($data['cdate']));
                        $deliveryrecap['cdate'] = $data['cdate'];
                        $deliveryrecap['day'] = date("D", strtotime($data['cdate']));
                        $deliveryrecap['grubhub_net'] = $json_data['delivery_grubhub_net_sales'];
                        $deliveryrecap['uber_eats_transactions'] = $json_data['delivery_uber_eats_qty'];
                        $deliveryrecap['uber_easts_amount'] = $json_data['delivery_uber_eats_amount'];
                        $deliveryrecap['uber_easts_net_amount'] = $json_data['delivery_uber_eats_net_sales'];
                        $deliveryrecap['delivery_net_recap_total_sales'] = $deliveryrecap['grubhub_net'] + $deliveryrecap['uber_easts_net_amount'];
                        $deliveryrecap['visa_transactions'] = $json_data['external_visa_amount'];
                        $deliveryrecap['visa_amount'] = $json_data['external_visa_qty'];
                        $deliveryrecap['mastercard_transactions'] = $json_data['external_mastercard_qty'];
                        $deliveryrecap['mastercard_amount'] = $json_data['external_mastercard_amount'];
                        $deliveryrecap['american_express_transactions'] = $json_data['external_amex_qty'];
                        $deliveryrecap['american_express_amount'] = $json_data['external_amex_amount'];
                        $deliveryrecap['discover_transactions'] = $json_data['external_discover_novis_qty'];
                        $deliveryrecap['discover_amount'] = $json_data['external_discover_novis_amount'];
                        $deliveryrecap['order_amount'] = $json_data['external_order_amount'];
                        $deliveryrecap['gift_card_amount'] = $json_data['external_giftcard_amount'];

                        //monthly recap
                        $monthlyrecap['store_key'] = $data["store_key"];
                        $monthlyrecap['month'] = date("M", strtotime($data['cdate']));
                        $monthlyrecap['year'] = date("Y", strtotime($data['cdate']));
                        $monthlyrecap['cdate'] = $data['cdate'];
                        $monthlyrecap['day'] = date("D", strtotime($data['cdate']));
                        $monthlyrecap['baskin_sales'] = $json_data['br_retail_net_sales'];
                        $monthlyrecap['dunkin_sales'] = $json_data['dd_retail_net_sales'];
                        $monthlyrecap['net_sales'] = $json_data['br_retail_net_sales'] + $json_data['dd_retail_net_sales'];
                        $monthlyrecap['newspaper'] = 0;
                        $monthlyrecap['sales_tax'] = $json_data['sales_tax'];
                        $monthlyrecap['gross_sales'] = $monthlyrecap['net_sales'] + $monthlyrecap['newspaper'] + $monthlyrecap['sales_tax'];
                        $monthlyrecap['all_card_totals'] = $cardrecap['cc_recap_total_sales'] + $cardrecap['dd_cards_total'] + $cardrecap['dd_paper_redeemed'];
                        $monthlyrecap['bank_deposit'] = $json_data['deposit_total'];
                        $monthlyrecap['actual_bank_deposit'] = 0;
                        $monthlyrecap['paidout'] = $json_data['paid_out'];
                        //$monthlyrecap['pos_over_short'] = round(($monthlyrecap['all_card_totals']+$monthlyrecap['paidout'] ) - $monthlyrecap['gross_sales'],2);
                        $monthlyrecap['pos_over_short'] = $json_data['over_shot'] ? $json_data['over_shot'] : 0;
                        $monthlyrecap['actual_over_shot'] = 0;
                        $monthlyrecap['guess_count'] = $json_data['trans_count_qty'];
                        $monthlyrecap['avg_ticket'] = $monthlyrecap['guess_count'] > 0 ? round($monthlyrecap['net_sales'] / $monthlyrecap['guess_count'], 2) : 0;
                        $monthlyrecap['item_del_bef_total'] = $json_data['item_deletions_before_total_amount'];
                        $monthlyrecap['item_del_aft_total'] = $json_data['item_deletions_after_total_qty_amount'];
                        $monthlyrecap['cancel_transaction'] = $json_data['cancelled_transactions_amount'];
                        $monthlyrecap['tracked_fee_exempt_net_sales'] = $json_data['tracked_fee_exempt_net_sales'];
                        $monthlyrecap['charity_net_sales'] = $json_data['charity_net_sales'];
                        $monthlyrecap['paid_ins'] = $json_data['paid_ins'];
                        $monthlyrecap['gift_certificate_sales'] = $json_data['gift_certificate_sales'];
                        $monthlyrecap['grubhub_total_gross'] = $json_data['delivery_grubhub_amount'];
                        $monthlyrecap['uber_eats_total_gross'] = $json_data['delivery_uber_eats_amount'];
                    }

                    $data['data'] = json_encode($json_data);

                    //check for duplications,mismatched and invalid date diff present in sheet
                    if (isset($data['cdate']) || (isset($data['start_date']) && isset($data['end_date']))) {
                        $row = new stdClass();
                        $row->table = $table;
                        $row->rownumber = $key;
                        $row->data = $data;
                        $row->cardrecap = $cardrecap;
                        $row->monthlyrecap = $monthlyrecap;
                        $row->deliveryrecap = $deliveryrecap;
                        $uId =  $data["store_key"].date('Ymd', strtotime($start_date)).date('Ymd', strtotime($end_date));
                        if(isset($databaseRows[$uId]))
                        {
                            $compareToRow = $databaseRows[$uId]->data;
                            $result = diff(json_decode($compareToRow['data']), json_decode($data['data']));                            
                            // if data is different the its mismatched and if same then its duplicate
                            if($databaseRows[$uId]->data != $row->data)
                            {
                                $keys_failed_data['mismatch'][$key]['store_key'] = $data["store_key"];
                                $keys_failed_data['mismatch'][$key]['mismatched_row'] = $databaseRows[$uId]->rownumber;
                                $keys_failed_data['mismatch'][$key]['start_date'] = date('m/d/Y', strtotime($start_date));
                                $keys_failed_data['mismatch'][$key]['end_date'] = date('m/d/Y', strtotime($end_date));
                                $keys_failed_data['mismatch'][$key]['result'] = $result;

                                $this->writeFailDataInSheet($key,"This row is ignored as it has mismatched data with row number ". $databaseRows[$uId]->rownumber);

                                if(!isset($keys_failed_data['mismatch'][$databaseRows[$uId]->rownumber])){
                                    $keys_failed_data['mismatch'][$databaseRows[$uId]->rownumber]['store_key'] = $compareToRow['store_key'];
                                    $keys_failed_data['mismatch'][$databaseRows[$uId]->rownumber]['mismatched_row'] = $key;
                                    if ($databaseRows[$uId]->table == 'master_pos_daily') {
                                        $keys_failed_data['mismatch'][$databaseRows[$uId]->rownumber]['start_date'] = date('m/d/Y', strtotime($compareToRow['cdate']));
                                        $keys_failed_data['mismatch'][$databaseRows[$uId]->rownumber]['end_date'] = date('m/d/Y', strtotime($compareToRow['cdate']));                                        
                                    } else {
                                        $keys_failed_data['mismatch'][$databaseRows[$uId]->rownumber]['start_date'] = date('m/d/Y', strtotime($compareToRow['start_date']));
                                        $keys_failed_data['mismatch'][$databaseRows[$uId]->rownumber]['end_date'] = date('m/d/Y', strtotime($compareToRow['end_date']));                              
                                    }                                    

                                    $keys_failed_data['mismatch'][$databaseRows[$uId]->rownumber]['result'] = $result;
                                    $this->writeFailDataInSheet($databaseRows[$uId]->rownumber,"This row is ignored as it has mismatched data with row number ". $key);
                                }

                            } else {
                                $keys_failed_data['duplicate'][$key]['store_key'] = $data["store_key"];
                                $keys_failed_data['duplicate'][$key]['duplicated_row'] = $databaseRows[$uId]->rownumber;
                                $keys_failed_data['duplicate'][$key]['start_date'] = date('m/d/Y', strtotime($start_date));
                                $keys_failed_data['duplicate'][$key]['end_date'] = date('m/d/Y', strtotime($end_date));
                                $keys_failed_data['duplicate'][$key]['result'] = $result;
                                $this->writeFailDataInSheet($key,"This row is ignored as it has duplicated data with row number ". $databaseRows[$uId]->rownumber);

                                if(!isset($keys_failed_data['duplicate'][$databaseRows[$uId]->rownumber])){
                                    $keys_failed_data['duplicate'][$databaseRows[$uId]->rownumber]['store_key'] = $compareToRow['store_key'];
                                    $keys_failed_data['duplicate'][$databaseRows[$uId]->rownumber]['duplicated_row'] = $key;
                                    if ($databaseRows[$uId]->table == 'master_pos_daily') {
                                        $keys_failed_data['duplicate'][$databaseRows[$uId]->rownumber]['start_date'] = date('m/d/Y', strtotime($compareToRow['cdate']));
                                        $keys_failed_data['duplicate'][$databaseRows[$uId]->rownumber]['end_date'] = date('m/d/Y', strtotime($compareToRow['cdate']));                                       
                                    } else {
                                        $keys_failed_data['duplicate'][$databaseRows[$uId]->rownumber]['start_date'] = date('m/d/Y', strtotime($compareToRow['start_date']));
                                        $keys_failed_data['duplicate'][$databaseRows[$uId]->rownumber]['end_date'] = date('m/d/Y', strtotime($compareToRow['start_date']));;                              
                                    }                                     
                                    $keys_failed_data['duplicate'][$databaseRows[$uId]->rownumber]['result'] = $result;
                                    $this->writeFailDataInSheet($databaseRows[$uId]->rownumber,"This row is ignored as it has duplicated data with row number ". $key);
                                }
                            }                            
                            $removeUids[$uId] = $uId;
                        } else {
                            $databaseRows[$uId] = $row;
                        }
                    }
                }
                $rowCounter++;                
            }

            //remove the row from array if its duplicate with others
            $databaseRows = array_diff_key($databaseRows,$removeUids);
        
            //get locked_entries
            $cleanedUid = [];
            $lockedUid = $this->masterpos_model->locked_entry_Res(array_keys($databaseRows));
            //get entries which shall pass further 
            $cleanedUid = array_diff(array_keys($databaseRows),array_keys($lockedUid));
            //now grab the unique ids which are locked and present in sheet.
            foreach($databaseRows as $keyUid => $databaseRow){
                if(!in_array($keyUid, $cleanedUid))
                {
                    if($databaseRow->data != $lockedUid[$keyUid]['data']) {
                        $result = diff(json_decode($lockedUid[$keyUid]['data']['data']),json_decode($databaseRow->data['data']));
                        $keys_failed_data['duplicate_db'][$keyUid]['store_key'] = $databaseRow->data['store_key'];
                        if ($databaseRow->table == 'master_pos_daily') {
                            $keys_failed_data['duplicate_db'][$keyUid]['start_date'] = $keys_failed_data['duplicate_db'][$keyUid]['end_date'] = date('m/d/Y', strtotime($databaseRow->data['cdate']));
                        } else {
                            $keys_failed_data['duplicate_db'][$keyUid]['start_date'] = date('m/d/Y', strtotime($databaseRow->data['start_date']));
                            $keys_failed_data['duplicate_db'][$keyUid]['end_date'] = date('m/d/Y', strtotime($databaseRow->data['end_date']));                            
                        }
                        $keys_failed_data['duplicate_db'][$keyUid]['result'] = $result;
                        $keys_failed_data['duplicate_db'][$keyUid]['duplicated_row'] = $databaseRow->rownumber;
                    } else {
                        $keys_failed_data['other'][$databaseRow->rownumber] = 1; 
                    }
                    
                    $this->writeFailDataInSheet($databaseRow->rownumber,"This row is already present and locked in database. To overwrite please unlock it");
                    unset($databaseRows[$keyUid]);
                }
            }

            if (sizeof($keys_failed_data) != 0 && $this->input->post('is_forced') !== "true" && (isset($keys_failed_data['mismatch']) || isset($keys_failed_data['date_diff']) )) {
                $msg = 'something wrong in below records.';
                echo json_encode(
                        [
                            'type'          => 'error', 
                            'popup'         => 1, 
                            'message'       => $msg, 
                            'duplicate'     => $keys_failed_data['duplicate'] + $keys_failed_data['mismatch'], // mismatched and duplicate data from sheet are displayed in single section 
                            'date_diff'     => $keys_failed_data['date_diff'],
                            'other'         => $keys_failed_data['other'],
                            'duplicate_db'  => $keys_failed_data['duplicate_db']
                        ]);
                return;
            }         

            $successCount = count($databaseRows);
            $failureCount = (count($keys_failed_data['duplicate']))  
                            + (count($keys_failed_data['mismatch']))  
                            + count($keys_failed_data['date_diff']) 
                            + count($keys_failed_data['duplicate_db']) 
                            + count($keys_failed_data['other']);

            //DB operation starts
            $this->load->model('file_history_model');
            $fileId = $this->file_history_model->Add(array(
                'file_name' => $file_name,
                'file_type' => 'masterpos',
                'file_path' => $relativePath,
                'success' => $successCount,
                'failure' => $failureCount,
                'failure_file_path' => 'files_upload/master_pos/'.$file_failure_name,
                'upload_at' => date("Y-m-d H:i:s", time()),
            ));
            if ($fileId) {
                $finalResult['posDailyTotalAddCount'] = 0;
                $finalResult['posDailyTotalUpdatedCount'] = 0;
                $finalResult['posWeeklyTotalAddCount'] = 0;
                $finalResult['posWeeklyTotalUpdateCount'] = 0;
                $finalResult['posMonthlyTotalAddCount'] = 0;
                $finalResult['posMonthlyTotalUpdateCount'] = 0;
                $finalResult['cardRecapTotalcount'] = 0;
                $finalResult['monthlyRecapTotalcount'] = 0;
                $finalResult['deliveryRecapTotalcount'] = 0;
                foreach ($databaseRows as $row) {
                    $row->data['file_id'] = $row->cardrecap['file_id'] = $row->monthlyrecap['file_id'] = $row->deliveryrecap['file_id'] = $fileId;
                    $row->data['is_lock'] = $row->cardrecap['is_lock'] = $row->monthlyrecap['is_lock'] = $row->deliveryrecap['is_lock'] = 1;
                    $posStoredData = $this->masterpos_model->add($row->table, $row->data);
                    if ($row->table == 'master_pos_daily') {
                        $finalResult['posDailyTotalAddCount'] += ($posStoredData['id'] && $posStoredData['type'] == 'add') ? 1 : 0;
                        $finalResult['posDailyTotalUpdatedCount'] += ($posStoredData['id'] && $posStoredData['type'] == 'update') ? 1 : 0;
                        $row->cardrecap['pos_id'] = $row->monthlyrecap['pos_id'] = $row->deliveryrecap['pos_id'] = $posStoredData['id'];
                        $cardRecapResult = $this->masterpos_model->add("card_recap", $row->cardrecap);
                        $montlyRecapResult = $this->masterpos_model->add("monthly_recap", $row->monthlyrecap);
                        $deliveryRecapResult = $this->masterpos_model->add("delivery_recap", $row->deliveryrecap);
                        $finalResult['cardRecapTotalcount'] += $cardRecapResult ? 1 : 0;
                        $finalResult['monthlyRecapTotalcount'] += $montlyRecapResult ? 1 : 0;
                        $finalResult['deliveryRecapTotalcount'] += $deliveryRecapResult ? 1 : 0;
                    } else if ($row->table == 'master_pos_weekly') {
                        $finalResult['posWeeklyTotalAddCount'] += ($posStoredData['id'] && $posStoredData['type'] == 'add') ? 1 : 0;
                        $finalResult['posWeeklyTotalUpdateCount'] += ($posStoredData['id'] && $posStoredData['type'] == 'update') ? 1 : 0;
                    } else if ($row->table == 'master_pos_monthly') {
                        $finalResult['posMonthlyTotalAddCount'] += ($posStoredData['id'] && $posStoredData['type'] == 'add') ? 1 : 0;
                        $finalResult['posMonthlyTotalUpdateCount'] += ($posStoredData['id'] && $posStoredData['type'] == 'update') ? 1 : 0;
                    } else {
                        $keys_failed_data['other'][$row->rownumber] = 1; 
                        $this->writeFailDataInSheet($row->rownumber,"Reason not found!");
                    }
                }
            } else {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Something went wrong file is not uploaded</div>');
                if ($this->input->is_ajax_request()) {
                    echo json_encode(array('type'=> 'error', 'message'=> 'Something went wrong file is not uploaded'));
                    return;
                }
            }

            $writer = new Xlsx($this->failedSpreadsheet);
            $writer->save('files_upload/master_pos/'.$file_failure_name);   
            
            if ($this->input->is_ajax_request()) {
                echo json_encode(
                        [
                            'type'      => 'success', 
                            'popup'     => 1, 
                            'success'   => $finalResult, 
                            'failure'   => $keys_failed_data,
                            'sheet_total_records' => --$rowCounter, //row counter is dcresed by one because title is not included in number of row
                            'success_count'     => $successCount,
                            'failure_count'     => $failureCount,
                            'failed_sheet'      => 'files_upload/master_pos/'.$file_failure_name
                        ]);                
                return;
            }
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' . $e->getMessage() . '</div>');
            if ($this->input->is_ajax_request()) {
                echo json_encode(array('type' => 'error', 'message' => $e->getMessage()));
                return;
            }
        }
    }

    function writeFailDataInSheet($rowNumer, $reason)
    {     
        $currentFailedworksheet = $this->failedSpreadsheet->getActiveSheet();
        $currentRow = $currentFailedworksheet->getHighestDataRow();
        $currentFailedworksheet->setCellValue('A'.(++$currentRow), $rowNumer); 
        $currentFailedworksheet->setCellValue('B'.$currentRow, $reason);
    }

}
