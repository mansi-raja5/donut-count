<?php
defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';
class Payroll extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('payroll_model');
    }
    public function index()
    {
        $data['title'] = 'Master Payroll';
        $this->template->load('listing', 'payroll', $data);
    }
    public function import()
    {
        try {
            if (empty($_FILES['import_file']['name'])) {
                $msg = "Something went wrong file is not uploaded!";
                $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' . $msg . '</div>');
                redirect('common');
            }

            // get file extension
            $targetPath = FCPATH . "files_upload/master_payroll/";
            $fileInfo = pathinfo($_FILES['import_file']['name']);
            $fileName = $fileInfo['filename'] . '_' . date('Ymd') . '_' . date('His') . '.' . $fileInfo['extension'];
            $relativePath = 'files_upload/master_payroll/' . $fileName;
            $targetFile = $targetPath . $fileName;
            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0777, true);
            }

            if (!move_uploaded_file($_FILES['import_file']['tmp_name'], $targetFile)) {
                $msg = "Something went wrong file is not uploaded!";
                $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' . $msg . '</div>');
                redirect('common');
            }

            if ($fileInfo['extension'] == 'csv') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
                $reader->setLoadAllSheets();
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($targetFile);
            } else if ($fileInfo['extension'] == 'xlsx' || $fileInfo['extension'] == 'xlsm') {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
                $reader->setLoadAllSheets();
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($targetFile);
            } else {
                $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
                $reader->setLoadAllSheets();
                $reader->setReadDataOnly(true);
                $spreadsheet = $reader->load($targetFile);
            }

            $currentworksheet = $spreadsheet->getSheetByName('Payroll');
            if (!$currentworksheet) {
                $msg = "Payroll sheet is not present in the workbook!";
                $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' . $msg . '</div>');
                redirect('common');
            }

            //$worksheetcount = $spreadsheet->getSheetCount();
            $header_array = array("store_key", "start_date", "end_date", "fed_941_sum", "futa_sum", "swt_ga_sum", "sui_ga_sum", "total_tax_recap_sum", "gross_wages_sum", "health_insurance", "net_sum", "no_of_checks");
            $rowCounter = 0;
            $data = [];
            foreach ($currentworksheet->getRowIterator() as $key => $row) {
                ++$rowCounter;
                if ($key > 1) {
                    $cellIterator = $row->getCellIterator();
                    $cell_counter = 0;
                    foreach ($cellIterator as $cell) {
                        if (isset($header_array[$cell_counter])) {
                            if (in_array($cell->getColumn(), ['B', 'C'])):
                                $unixTimestamp = ($cell->getCalculatedValue() - 25569) * 86400;
                                $data[$rowCounter][$header_array[$cell_counter]] = is_int($unixTimestamp) ? date('Y-m-d', $unixTimestamp) : 0;
                            else:
                                $data[$rowCounter][$header_array[$cell_counter]] = $cell->getCalculatedValue();
                            endif;
                        }
                        $cell_counter++;
                    }
                    $isValid = 0;
                    $diff = date_diff(date_create($data[$rowCounter]['start_date']), date_create($data[$rowCounter]['end_date']));
                    if (date("l", strtotime($data[$rowCounter]['start_date'])) == 'Sunday'
                        && date("l", strtotime($data[$rowCounter]['end_date'])) == 'Saturday'
                        && $diff->days == 6) {
                        $isValid = 1;
                    }
                }
            }

            //DB operation starts
            $this->load->model('file_history_model');
            $fileId = $this->file_history_model->Add(array(
                'file_name' => $fileName,
                'file_type' => 'payroll',
                'file_path' => $relativePath,
                'success' => 0,
                'failure' => 0,
                'failure_file_path' => '',
                'upload_at' => date("Y-m-d H:i:s", time()),
            ));

            if ($fileId) {
                $updateCount = 0;
                $addCount = 0;
                foreach ($data as $key => $dataToStore) {
                    if (sizeof($dataToStore) > 0) {
                        $dataToStore['file_id'] = $fileId;
                        $dataToStore['is_lock'] = 1;
                        if ($dataToStore['store_key'] != ""
                            && $dataToStore['start_date'] != ""
                            && $dataToStore['end_date'] != "" && $isValid) {
                            $resultData = $this->payroll_model->add('master_payroll', $dataToStore);
                            if (isset($resultData['type']) && $resultData['type'] == 'add') {
                                ++$addCount;
                            } else if (isset($resultData['type']) && $resultData['type'] == 'update') {
                                ++$updateCount;
                            }
                        }
                    }
                }
            }
            $this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' .$addCount . ' records are added successfully!<br>' .$updateCount . ' records are updated successfully!</div>');
            redirect('common');
        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' . $e->getMessage() . '</div>');
            redirect('common');
        }

        redirect('common');
    }

}
