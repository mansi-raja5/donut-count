<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Review extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('review_model');
        $this->load->model('store_master_model');
    }

    public function import() {
        if (!empty($_FILES['import_file']['name'])) {
            // get file extension
            $file_name = $_FILES['import_file']['name'];          //3
            $targetPath = FCPATH . "/files_upload/import_review/";
            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0777, true);
            }
            $tempFile = $_FILES['import_file']['tmp_name'];
            $readerfile = "files_upload/import_review/" . $file_name;
            $targetFile = $targetPath . $file_name;
            $this->load->model('file_history_model');
            $fileId = $this->file_history_model->Add(array(
                'file_name' => $file_name,
                'file_type' => 'review',
                'file_path' => $readerfile,
                'upload_at' => date("Y-m-d H:i:s", time()),
            ));
            if ($fileId && move_uploaded_file($tempFile, $targetFile)):
                $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);
                try {
                    if ($extension == 'csv') {
                        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                        $reader->setLoadAllSheets();
                        $reader->setReadDataOnly(true);
                        $reader->setReadDataOnly(true);
                        $reader->setInputEncoding('CP1252');
                        $reader->setDelimiter(';');
                        $reader->setEnclosure('');
                        $reader->setSheetIndex(0);
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
                    for ($i = 0; $i < $worksheetcount; $i++) {

                        $currentworksheet = $spreadsheet->getSheet($i);
                        // $n_type = array("Overall Satisfaction","Fresh (Taste of Beverage)","Fresh (Taste of Food)","Fast (Speed of Service)","Facility (Cleanliness)","Friendly (Crew/Manager)");
                        $n_type = array("overall_satisfaction", "taste_of_beverage", "taste_of_food", "speed_of_service", "cleanliness", "crew_manager");
                        $data = array();
                        $noofdays = 0;
                        foreach ($currentworksheet->getRowIterator() as $key => $row) {
                            $cellIterator = $row->getCellIterator();
                            if ($key == 2):
                                $cell_counter = 0;
                                foreach ($cellIterator as $cell) {
                                    if ($cell_counter == 0):
                                        $value = explode(",", $cell->getValue());
                                        $date = explode(":", $value[0]);
                                        $date = explode("-", $date[1]);
                                        $data['start_date'] = date("Y-m-d", strtotime($date[0]));
                                        $data['end_date'] = date("Y-m-d", strtotime($date[1]));
                                        $datediff = strtotime($data['start_date']) - strtotime($data['end_date']);
                                        $noofdays = round($datediff / (60 * 60 * 24));
                                        $d1 = new DateTime($data['start_date']);
                                        $d2 = new DateTime($data['end_date']);
                                        $Months = $d2->diff($d1);
                                        $howeverManyMonths = (($Months->y) * 12) + ($Months->m);
                                        $firstdate = date("Y-m-d", strtotime('first day of January ' . date('Y', strtotime($data['start_date']))));
                                        if ($firstdate == $data['start_date']) {
                                            $data['duration_type'] = "year_to_date";
                                        } else if ($howeverManyMonths == 3) {
                                            $data['duration_type'] = "3_months";
                                        } else if (($noofdays == 44 || $noofdays == 45) || ($noofdays == -44 || $noofdays == -45 )) {
                                            $data['duration_type'] = "45_days";
                                        } else if (($noofdays > 45 && $noofdays <= 366 ) || ($noofdays > -45 && $noofdays <= -366 )) {
                                            $data['duration_type'] = "full_year";
                                        } else {
                                            $data['duration_type'] = "full_year";
                                        }
                                    else:
                                        break;
                                    endif;
                                    $cell_counter++;
                                }
                            endif;
                            if ($key == 5):
                                $cell_counter = 0;
                                $visit_date = "";
                                foreach ($cellIterator as $cell) {
                                    if ($cell_counter == 0):
                                        $visit_date = explode(":", $cell->getValue());
                                        $visit_date = explode(" ", trim($visit_date[1]));
                                        $visit_date = date("Y-m-d", strtotime($visit_date[0]));
                                    else:
                                        break;
                                    endif;
                                    $cell_counter++;
                                }
                                $data['visit_date'] = date("Y-m-d H:i:s", strtotime($visit_date));
                            endif;
                            if ($key >= 10 && $key <= 18):
                                $cell_counter = 0;
                                $n_type_counter = 0;
                                foreach ($cellIterator as $cell) {
                                    if ($cell_counter == 0):
                                        $value = explode(",", $cell->getValue());
                                        $store_key = explode(" ", $cell->getValue());
                                        $store_key = $store_key[0];
                                        // print_r($value);
                                        $data['file_id'] = $fileId;
                                        $data['store_key'] = $store_key;
                                        $data['type'] = $n_type[$n_type_counter];
                                        $data['n_number'] = $value[1];
                                        $data['five_star'] = $value[2];
                                        $this->review_model->add('customer_review', $data);
                                        $n_type_counter++;
                                        $data['type'] = $n_type[$n_type_counter];
                                        $data['n_number'] = $value[7];
                                        $data['five_star'] = $value[8];
                                        $this->review_model->add('customer_review', $data);
                                        $n_type_counter++;
                                        $data['type'] = $n_type[$n_type_counter];
                                        $data['n_number'] = $value[13];
                                        $data['five_star'] = $value[14];
                                        $this->review_model->add('customer_review', $data);
                                        $n_type_counter++;
                                        $data['type'] = $n_type[$n_type_counter];
                                        $data['n_number'] = $value[19];
                                        $data['five_star'] = $value[20];
                                        $this->review_model->add('customer_review', $data);
                                        $n_type_counter++;
                                        $data['type'] = $n_type[$n_type_counter];
                                        $data['n_number'] = $value[25];
                                        $data['five_star'] = $value[26];
                                        $this->review_model->add('customer_review', $data);
                                        $n_type_counter++;
                                        $data['type'] = $n_type[$n_type_counter];
                                        $data['n_number'] = $value[31];
                                        $data['five_star'] = $value[32];
                                        $this->review_model->add('customer_review', $data);
                                    endif;
                                    $cell_counter++;
                                }
                            endif;
                        }
                    }
                    $this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                         Imported successfully!!!</div>');
                    redirect('common');
                } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {

                    $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' . $e->getMessage() . '</div>');
                    redirect('common');
                }
            else:
                $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                         Something went wrong file is not uploaded1</div>');
                redirect('common');
            endif;
        }
        $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                         Something went wrong file is not uploaded2</div>');
        redirect('common');
    }

    public function view() {

        $data['title'] = 'Customer Review List';
//        $_POST['store_key'] = '350432';
//        $_POST['duration_type'] = 'full_year';
        $review_Arr = array();
        if (isset($_POST) && !empty($_POST)) {
            $review_Res = $this->review_model->Get(null, $this->input->post());
            if (isset($review_Res['records']) && !empty($review_Res['records'])) {
                foreach ($review_Res['records'] as $rRow) {
//                    $review_Arr[$rRow->store_key][$rRow->duration_type][$rRow->type] = array("n" => $rRow->n_number,
//                        "five_star" => $rRow->five_star);
                    $review_Arr[$rRow->store_key][$rRow->duration_type][$rRow->type."_n"] = $rRow->n_number;
                    $review_Arr[$rRow->store_key][$rRow->duration_type][$rRow->type."_5"] = $rRow->five_star;
                }
            }
        }
        $store_list = $this->store_master_model->Get(NULL, array('status' => 'A'));
        $key_list = $this->review_model->Get_keys();
        $data['store_list'] = $store_list;
        $data['review_Arr'] = $review_Arr;
        $data['key_list'] = $key_list;
       
//        echo "<pre>";
//        print_r($key_list);
//        print_r($review_Arr);
//        echo "</pre>";
        
        $this->template->load('listing', 'list-customer-review', $data);
    }

    public function getListing($result = array()) {
//        echo "<pre>";
//        print_r($result);
//        exit;


        $tableData = array();
        foreach ($result['records'] as $key => $row) {
            $action = array();
//            $action[] = anchor('cars_entry/edit/' . $row->id, 'Edit');
//            $action[] = anchor('javascript:void(0);', 'Delete', array('data-toggle' => 'modal', 'data-id' => $row->id, 'onclick' => 'setConfirmDetails(this)', ' data-target' => '#ConfirmDeleteModal', 'data-url' => 'cars_entry/delete/' . $row->id));

            $tableData[$key]['srNo'] = $key + 1;
            $tableData[$key]['store_key'] = $row->store_key;


            $type = $row->type;

            switch ($type) {
                case 'cleanliness':
                    $tableData[$key]['k1_n'] = $row->n_number;
                    $tableData[$key]['k1_5'] = $row->five_star;
                    break;
                case 'crew_manager':
                    $tableData[$key]['k2_n'] = $row->n_number;
                    $tableData[$key]['k2_5'] = $row->five_star;
                    break;
                case 'overall_satisfaction':
                    $tableData[$key]['k3_n'] = $row->n_number;
                    $tableData[$key]['k3_5'] = $row->five_star;
                    break;
                case 'speed_of_service':
                    $tableData[$key]['k4_n'] = $row->n_number;
                    $tableData[$key]['k4_5'] = $row->five_star;
                    break;
                case 'taste_of_beverage':
                    $tableData[$key]['k5_n'] = $row->n_number;
                    $tableData[$key]['k5_5'] = $row->five_star;
                    break;
                case 'taste_of_food':
                    $tableData[$key]['k6_n'] = $row->n_number;
                    $tableData[$key]['k6_5'] = $row->five_star;
                    break;
            }

//            $tableData[$key]['action'] = implode(" | ", $action);
            $tableData[$key]['id'] = $row->id;
        }

//        echo "<pre>";
//        print_r($tableData);
//        exit;
        $data['data'] = $tableData;
        $data['recordsTotal'] = $result['countTotal'];
        $data['recordsFiltered'] = $result['countFiltered'];
        echo json_encode($data);
    }

}
