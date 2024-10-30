<?php
defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';
class Donutcount extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('donutcount_model');
    }
    public function index()
    {
        $data['title'] = 'Dount Count';
        $this->template->load('listing', 'donut_count', $data);
    }

    public function import()
    {
        if (!empty($_FILES['import_file']['name'])) {
            // get file extension
            $file_name  = $_FILES['import_file']['name']; //3
            $targetPath = FCPATH . "/files_upload/donut_count/";
            $tempFile   = $_FILES['import_file']['tmp_name'];
            $readerfile = "files_upload/donut_count/" . $file_name;
            $targetFile = $targetPath . $file_name;
            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0777, true);
            }
            $this->load->model('file_history_model');
            $fileId = $this->file_history_model->Add(array(
                'file_name' => $file_name,
                'file_type' => 'donutcount',
                'file_path' => $readerfile,
                'upload_at' => date("Y-m-d H:i:s", time()),
            ));
            if ($fileId && move_uploaded_file($tempFile, $targetFile)) {
                $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);
                try
                {
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
                    //get store key
                    $worksheetNames = $reader->listWorksheetNames($readerfile);
                    if (($key = array_search('348544', $worksheetNames)) !== false) {
                        unset($worksheetNames[$key]);
                    }
                    if (($key = array_search('DT Score', $worksheetNames)) !== false) {
                        unset($worksheetNames[$key]);
                    }
                    if (($key = array_search('Sheet1', $worksheetNames)) !== false) {
                        unset($worksheetNames[$key]);
                    }

                    //donuts
                    $daysAry[5]  = 6;
                    $daysAry[6]  = 5;
                    $daysAry[7]  = 4;
                    $daysAry[8]  = 3;
                    $daysAry[9]  = 2;
                    $daysAry[10] = 1;
                    $daysAry[11] = 0;

                    //fancy
                    $daysAry[30] = 6;
                    $daysAry[31] = 5;
                    $daysAry[32] = 4;
                    $daysAry[33] = 3;
                    $daysAry[34] = 2;
                    $daysAry[35] = 1;
                    $daysAry[36] = 0;

                    //Monkey
                    $daysAry[18] = 6;
                    $daysAry[19] = 5;
                    $daysAry[20] = 4;
                    $daysAry[21] = 3;
                    $daysAry[22] = 2;
                    $daysAry[23] = 1;
                    $daysAry[24] = 0;

                    foreach ($worksheetNames as $sheetNumber => $_worksheetNames) {
                        $donutData = [];
                        $count     = -1;
                        $currentworksheet   = $spreadsheet->getSheet($sheetNumber);
                        $highestColumn      = $currentworksheet->getHighestColumn();
                        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

                        $weekEndDate = "";
                        $count       = -1;
                        for ($col = 1; $col <= $highestColumnIndex; ++$col) {
                            $cell = $currentworksheet->getCellByColumnAndRow($col, 3);
                            if (is_numeric($cell->getCalculatedValue())) {
                                $unixTimestamp = ($cell->getCalculatedValue() - 25569) * 86400;
                                $weekEndDate   = date('Y-m-d', $unixTimestamp);
                                $donut_type    = "Donuts";
                                for ($row = 5; $row <= 36; $row++) {
                                    $donutData[++$count]                   = [];
                                    $donutData[$count]['file_id']          = $fileId;
                                    $donutData[$count]['store_key']        = $_worksheetNames;
                                    $donutData[$count]['donut_type']       = $donut_type;
                                    $donutData[$count]['week_ending_date'] = $weekEndDate;
                                    $donutData[$count]['week_day']         = $currentworksheet->getCellByColumnAndRow(1, $row)->getValue();
                                    $donutData[$count]['total_order']      = $currentworksheet->getCellByColumnAndRow($col, $row)->getValue();
                                    $donutData[$count]['total_sale']       = $currentworksheet->getCellByColumnAndRow($col + 1, $row)->getValue();
                                    $donutData[$count]['daily_date']       = date("Y-m-d", strtotime("-{$daysAry[$row]} day", strtotime($weekEndDate)));

                                    if ($row == 11) {
                                        $row        = 17;
                                        $donut_type = "Fancy";
                                    } elseif ($row == 24) {
                                        $row        = 29;
                                        $donut_type = "Munkins";
                                    }
                                }
                                $col++;
                            }
                        }
                        $this->donutcount_model->add_if_not_exist($donutData);
                    }
                    $this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                 Imported successfully!!!</div>');
                    redirect('common');
                } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {

                    $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' . $e->getMessage() . '</div>');
                    redirect('common');
                }
            } else {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                                 Something went wrong file is not uploaded</div>');
                redirect('common');
            }

        }
        $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                         Something went wrong file is not uploaded</div>');
        redirect('common');
    }
}
