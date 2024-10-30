<?php

defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
class Dailysales extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->model('dailysales_model');
        $this->load->model('store_master_model');
    }
    public function index() {
    	$data['title'] = 'Daily Sales';
        $this->template->load('listing', 'dailysales',$data);
    }

  	public function import(){
  		if(!empty($_FILES['import_file']['name'])) {
                // get file extension
  				$file_name = $_FILES['import_file']['name'];          //3
        		$targetPath =  FCPATH ."/files_upload/import_daily/";
				if (!file_exists($targetPath)) {
                    mkdir($targetPath, 0777, true);
				}
				$tempFile = $_FILES['import_file']['tmp_name'];
        		$readerfile = "files_upload/import_daily/".$file_name;
				$targetFile = $targetPath . $file_name;
				$this->load->model('file_history_model');
				$fileId = $this->file_history_model->Add(array(
					'file_name' => $file_name,
					'file_type' => 'dailysales',
					'file_path' => $readerfile,
					'upload_at' => date("Y-m-d H:i:s", time()),
				));
		        if($fileId && move_uploaded_file($tempFile, $targetFile)):
	                $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);
	 				try {
		                if($extension == 'csv'){
		                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
		                    $reader->setLoadAllSheets();
		                    $reader->setReadDataOnly(true);
		                    $spreadsheet = $reader->load($targetFile);
		                } elseif($extension == 'xlsx') {
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
						//looping through sheet
						$data = array();
						$store_key = "";
						$increment_date = "";
						$month = "";
						$cdate = new DateTime();
						$ocdate = new DateTime();
						$header_array = array('file_id','store_key','month','year','cdate','day','baskin_foods','newspaper','dunkin_foods','employee_meals','employee_bonus','repairs','maintenance','office_supplies','cleaning_supplies','gas','other_expenses','total');

						$card_recap_header = array('file_id','store_key','month','year','cdate','day','master_transaction','master_amount','visa_transaction','visa_amount','amex_transaction','amex_amount','discover_transaction','discover_amount','cc_recap_total_sales','dunkin_transaction','dunkin_amount','dd_cards_total','dd_paper_redeemed');

						$monthly_header = array('file_id','store_key','month','year','cdate','day','baskin_sales','dunkin_sales','net_sales','newspaper','sales_tax','gross_sales','all_card_totals','bank_deposit', 'actual_bank_deposit', 'paidout','actual_over_shot', 'pos_over_shot','guess_count','avg_ticket','item_del_bef_total','item_del_aft_total','cancel_transaction');
						for($i=0;$i<$worksheetcount;$i++){

							$currentworksheet = $spreadsheet->getSheet($i);
							//get only the Cell Collection
							if($i==0){
								//get store key and month details
								$store_key = $currentworksheet->getCellByColumnAndRow(4, 3)->getValue();
								$month = $currentworksheet->getCellByColumnAndRow(4, 7)->getValue();
								$cdate = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($month);
								$odate = new ReflectionObject($cdate);
								$p = $odate->getProperty('date');
								$date = date("d-m-yy",strtotime($p->getValue($cdate)));
								$month = date("M-y",strtotime($date));
								continue;
							}

							$position =0;
							$rowcount =0;
							foreach ($currentworksheet->getRowIterator() as $key=>$row) {

							   $cellIterator = $row->getCellIterator();
							    // $cellIterator->setIterateOnlyExistingCells(FALSE);
							   	//paid out recap
							    if($i==1):
							  		if($key ==36  || $key ==37 || $key ==38 || $key ==39)
							   			continue;
							   		$data=array();

								   	if($rowcount > 3):
										$data[$header_array[0]] = $fileId;
										$data[$header_array[1]] = $store_key;
								   		$data[$header_array[2]] = $month;
								   		$data[$header_array[3]] = date("yy",strtotime($month));
								   		if($position > 0):
									   		$increment_date = date("d-m-yy",strtotime("+1 day", strtotime($increment_date)));
									   		$data[$header_array[4]] =  date("Y-m-d",strtotime($increment_date));
									   		$data[$header_array[5]] =  date('D', strtotime($increment_date));
									   	else:
									   		$data[$header_array[4]] =  date("Y-m-d",strtotime($date));
									   		$data[$header_array[5]] =  date('D', strtotime($date));
									   		$increment_date = $date;

									   		$position++;
									   	endif;
								   		$cell_counter =0 ;
								   		$counter =6 ;
								   		foreach ($cellIterator as $cell) {

								   			if($cell_counter > 1):
								   				$value=$cell->getCalculatedValue();
									   			$data[$header_array[$counter]] = $value;
									   			$counter++;

									   		endif;
									   		$cell_counter++;
								   		 }

								   		 //insert into table
								   		 $this->dailysales_model->add('paid_out_recap',$data);

								   	endif;

							    endif;
							    if($i==2):
							   		//card recap
							   		if($key ==37 || $key ==38 || $key ==39
							   		)
							   			continue;
							    	$data=array();
							    	if($rowcount > 4):
										$data[$card_recap_header[0]] = $fileId;
								   		$data[$card_recap_header[1]] = $store_key;
								   		$data[$card_recap_header[2]] = $month;
								   		$data[$card_recap_header[3]] = date("yy",strtotime($month));
								   		if($position > 0):
									   		$increment_date = date("d-m-yy",strtotime("+1 day", strtotime($increment_date)));
									   		$data[$card_recap_header[4]] =  date("Y-m-d",strtotime($increment_date));
									   		$data[$card_recap_header[5]] =  date('D', strtotime($increment_date));
									   	else:
									   		$data[$card_recap_header[4]] =  date("Y-m-d",strtotime($date));
									   		$data[$card_recap_header[5]] =  date('D', strtotime($date));
									   		$increment_date = $date;
									   		$position++;
									   	endif;

								   		$cell_counter =0 ;
								   		$counter =6 ;
								   		foreach ($cellIterator as $cell) {
								   			if($cell->getCoordinate()=="O".$key || $cell->getCoordinate()=="P".$key || $cell->getCoordinate()=="Q".$key || $cell->getCoordinate()=="R".$key)
								   				continue;
								   			if($cell_counter > 1):
								   				$value=$cell->getCalculatedValue();
									   			$data[$card_recap_header[$counter]] = $value;
									   			$counter++;

									   		endif;
									   		$cell_counter++;
								   		 }

								   		 //insert into table
								   		 $this->dailysales_model->add('card_recap',$data);

								   	endif;
							    endif;
							    if($i==3):
							   		//monthly recap
							   		if($key ==37 || $key==38 || $key==39)
							   			continue;
							    	$data=array();
							    	if($rowcount > 4):
										$data[$monthly_header[0]] = $fileId;
								   		$data[$monthly_header[1]] = $store_key;
								   		$data[$monthly_header[2]] = $month;
								   		$data[$monthly_header[3]] = date("yy",strtotime($month));
								   		if($position > 0):
									   		$increment_date = date("d-m-yy",strtotime("+1 day", strtotime($increment_date)));
									   		$data[$monthly_header[4]] = date("Y-m-d",strtotime($increment_date));
									   		$data[$monthly_header[5]] =  date('D', strtotime($increment_date));
									   	else:
									   		$data[$monthly_header[4]] =date("Y-m-d",strtotime($date));
									   		$data[$monthly_header[5]] = date('D', strtotime($date));
									   		$increment_date = $date;
									   		$position++;
									   	endif;

								   		$cell_counter =0 ;
								   		$counter =6 ;
								   		foreach ($cellIterator as $cell) {

								   			if($cell_counter > 1):
								   				$value=$cell->getCalculatedValue();
									   			$data[$monthly_header[$counter]] = $value;
									   			$counter++;

									   		endif;
									   		$cell_counter++;
								   		 }

								   		 //insert into table
								   		 $this->dailysales_model->add('monthly_recap',$data);
								   	endif;
							    endif;
							    $rowcount++;
							}

						}
						$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                         Imported successfully!!!</div>');
						redirect('common');
					} catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {

					    $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$e->getMessage().'</div>');
						redirect('common');
					}
	            else:
	            	$this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                         Something went wrong file is not uploaded</div>');
					redirect('common');
	            endif;

	    }
        $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                         Something went wrong file is not uploaded</div>');
		redirect('common');
  	}

  	public function add(){
  		$data['title'] = 'Daily Sales';
  		$data['store_list'] = $this->store_master_model->Get(null, array("status" => 'A'));
  		$data['dynamic_column'] = $this->dailysales_model->getAllColumn();
        $this->template->load('listing', 'adddailysales',$data);
  	}

  	public function adddailysales(){
  		$data = $this->input->post();
  		$result = $this->dailysales_model->adddailysales($data);
  		$this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Daily sales data added successfully!!!</div>');
  		redirect('dailysales/add');
  	}

  	public function paid_out(){
  		$data = $this->input->post();
  		$result = $this->dailysales_model->paid_out($data);
  		echo json_encode($result);
  	}
  	public function getColumns(){
  		$result = $this->dailysales_model->getColumns();
  		echo json_encode($result);
  	}

  	public function getdescription(){
  		$result = $this->dailysales_model->getdescription();
  		echo json_encode($result);
  	}
  	//for grid
  	public function getDailysalesGrid(){
  		$data = [];
        $data['store_key']  = $this->input->post('store_key');
        $data['month']  = $this->input->post('month');
        $data['year']  = $this->input->post('year');
        $data['days']  = sizeof( $this->dailysales_model->get_dates( $data['month'], $data['year']));
        $data['dynamic_column'] = $this->dailysales_model->getAllColumn();
        $data['dynamic_column_rows'] = $this->dailysales_model->getDailysalesGridRows($data['store_key'],$data['month'],$data['year']);
  		echo $this->load->view('dailysales/dailysales_grid', $data, TRUE);
        exit();
  	}

  	public function getAttachmentmodal(){
  		$data = [];
        $data['cdate']  = $this->input->post('cdate');
        $data['counter']  = $this->input->post('counter');
        $data['dynamic_column']  = $this->input->post('dynamic_column');
        echo $this->load->view('modal/dailysales_modal', $data, TRUE);
        exit();
  	}

  	public function setuploadAttachments(){
  		$data = $this->input->post();
  		// print_r($data);
  		// print_r($_FILES);
  		$result = $this->dailysales_model->adduploadattachment($data);
  		echo $result;
  		exit();
  	}
}
