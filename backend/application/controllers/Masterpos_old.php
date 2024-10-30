<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
class Masterpos extends CI_Controller {
    function __construct() {
        parent::__construct();
        $this->load->model('masterpos_model');
    }
    public function index() {
    	$data['title'] = 'Master POS';
        $this->template->load('listing', 'masterpos',$data);
    }

  	public function import(){
  		if(!empty($_FILES['import_file']['name'])) {
                    //get locked_entries 
                    $locked_Date_Arr  = array();
                    $locked_Entry_Res = $this->masterpos_model->locked_entry_Res();
                    
                    if(!empty($locked_Entry_Res)){
                        foreach ($locked_Entry_Res as $lRow){
                            $locked_Date_Arr[] = $lRow->lock_date;
                        }
                    }
                   
                    
                // get file extension
  				$file_name = $_FILES['import_file']['name'];          //3
        		$targetPath =  FCPATH ."/files_upload/master_pos/";
        		$tempFile = $_FILES['import_file']['tmp_name'];
        		$readerfile = "files_upload/master_pos/".$file_name;
        		$targetFile = $targetPath . $file_name;
        		if (!file_exists($targetPath)) {
	                mkdir($targetPath, 0777, true);
	            }
		        if(move_uploaded_file($tempFile, $targetFile)):
	                $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);
	 				try {
		                if($extension == 'csv'){
		                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
		                    $reader->setLoadAllSheets();
		                    $spreadsheet = $reader->load($targetFile);
		                } elseif($extension == 'xlsx') {
		                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
							$reader->setLoadAllSheets();
							$spreadsheet = $reader->load($targetFile);
		                } else {
		                    $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
		                    $reader->setLoadAllSheets();
		                    $spreadsheet = $reader->load($targetFile);
		                }

		    		$worksheetcount = $spreadsheet->getSheetCount();
		    		$getallkey = $this->masterpos_model->getallkey();
		    		// echo "<pre>";
		    		$card_recap_header = array('store_key','file_name','month','year','cdate','day','master_transaction','master_amount','visa_transaction','visa_amount','amex_transaction','amex_amount','discover_transaction','discover_amount','cc_recap_total_sales','dunkin_transaction','dunkin_amount','dd_cards_total','dd_paper_redeemed');

		    		$card_recap_calculation['master_transaction']= array("mastercard_qty","external_mastercard_qty");
		    		$card_recap_calculation['master_amount']= array("mastercard_amount","external_mastercard_amount");
		    		$card_recap_calculation['visa_transaction']= array("external_order_qty","visa_qty","external_visa_qty");
		    		$card_recap_calculation['visa_amount']= array("external_order_amount","visa_amount","external_visa_amount");
		    		$card_recap_calculation['amex_transaction']= array("external_amex_qty","american_express_qty");
		    		$card_recap_calculation['amex_amount']= array("external_amex_amount","american_express_amount");
		    		$card_recap_calculation['discover_transaction']= array("discover_novis_qty","external_discover_novis_qty");
		    		$card_recap_calculation['discover_amount']= array("discover_novis_amount","external_discover_novis_amount");
		    		$card_recap_calculation['dunkin_transaction']= array("gift_card_sales_amount");
		    		$card_recap_calculation['dunkin_amount']= array("gift_card_amount","external_giftcard_amount");

		    		//calcualte
		    		$card_recap_calculation['cc_recap_total_sales'] =array("discover_amount","amex_amount","visa_amount","master_amount");
		    		$card_recap_calculation['dd_cards_total'] =array("dunkin_amount","dunkin_transaction");
		    		$card_recap_calculation['dd_paper_redeemed'] ="null";

		    		for($i=0;$i<$worksheetcount;$i++){
		    			$currentworksheet = $spreadsheet->getSheet($i);
						$cdate = new DateTime();
						$ocdate = new DateTime();
						$header_array = array();
						$table ="";
						$keys_failed_data = array();
						$totalRow = $currentworksheet->getHighestDataRow()-1;
						foreach ($currentworksheet->getRowIterator() as $key=>$row) {
							$data = array();
							$json_data = array();
							$start_date = "";
							$end_date = "";
							$cell_counter = 0;
							$cellIterator = $row->getCellIterator();
							foreach ($cellIterator as $cell) {                                                            
								if($key==1){
									$header_array[] = trim($cell->getValue());
								}
								else if($key > 1):
										if($cell_counter ==0 ){
											$data["store_key"] = $cell->getValue();
										}
										else if($cell_counter==1){
                                                                                    $unixTimestamp = ($cell->getCalculatedValue() - 25569) * 86400;
                                                                                    $start_date = date('Y-m-d', $unixTimestamp);
                                                                                    if(in_array($start_date, $locked_Date_Arr)){
                                                                                        continue;
                                                                                    }
                                                                                    $dates_Arr[] = $start_date;
										}
										else if($cell_counter==2){
                                                                                    $unixTimestamp = ($cell->getCalculatedValue() - 25569) * 86400;
                                                                                    $end_date = date('Y-m-d', $unixTimestamp);
//											
											$datediff = strtotime($end_date) - strtotime($start_date);
											$datediff = round($datediff / (60 * 60 * 24));
											if($datediff==0){
												$table = "master_pos_daily";
												$data['cdate']=  $start_date;
											}else{
												$table="master_pos_weekly";
												$data["start_date"] = $start_date;
												$data["end_date"] = $end_date;
											}

										}else{

											if($cell_counter >= 3){
												$json_data[array_search($header_array[$cell_counter],$getallkey)] = $cell->getValue();
											}

										}
								endif;
								$cell_counter++;
							}
                                                        
                                                        
							if($key >1 && sizeof($data) > 0 && isset($data['cdate'])){
								//card recap data
								$cardrecap = array();
								$cardrecap['store_key'] = $data["store_key"];
								$cardrecap['file_name'] = $targetFile;
								$cardrecap['month'] = date("M",strtotime($data['cdate']));
								$cardrecap['year'] = date("yy",strtotime($data['cdate']));
								$cardrecap['cdate'] = $data['cdate'];
								$cardrecap['day'] = date("D",strtotime($data['cdate']));
								$cardrecap['master_transaction'] = $json_data[$card_recap_calculation['master_transaction'][0]]+$json_data[$card_recap_calculation['master_transaction'][1]];
								$cardrecap['master_amount'] = $json_data[$card_recap_calculation['master_amount'][0]]+$json_data[$card_recap_calculation['master_amount'][1]];
								$cardrecap['visa_transaction'] = $json_data[$card_recap_calculation['visa_transaction'][0]]+$json_data[$card_recap_calculation['visa_transaction'][1]];
								$cardrecap['visa_amount'] = $json_data[$card_recap_calculation['visa_amount'][0]]+$json_data[$card_recap_calculation['visa_amount'][1]];
								$cardrecap['amex_transaction'] = $json_data[$card_recap_calculation['amex_transaction'][0]]+$json_data[$card_recap_calculation['amex_transaction'][1]];
								$cardrecap['amex_amount'] = $json_data[$card_recap_calculation['amex_amount'][0]]+$json_data[$card_recap_calculation['amex_amount'][1]];
								$cardrecap['discover_transaction'] = $json_data[$card_recap_calculation['discover_transaction'][0]]+$json_data[$card_recap_calculation['discover_transaction'][1]];
								$cardrecap['discover_amount'] = $json_data[$card_recap_calculation['discover_amount'][0]]+$json_data[$card_recap_calculation['discover_amount'][1]];

								$cardrecap['cc_recap_total_sales'] = $cardrecap['master_amount'] + $cardrecap['visa_amount'] + $cardrecap['amex_amount'] + $cardrecap['discover_amount'];

								$cardrecap['dunkin_transaction'] = $json_data[$card_recap_calculation['dunkin_transaction'][0]];
								$cardrecap['dunkin_amount'] = $json_data[$card_recap_calculation['dunkin_amount'][0]]+$json_data[$card_recap_calculation['dunkin_amount'][1]];
								$cardrecap['dd_cards_total'] = $cardrecap['dunkin_amount']-$cardrecap['dunkin_transaction'];
								$cardrecap['dd_paper_redeemed'] = null;

								//monthly recap
								$monthlyrecap = array();
								$monthlyrecap['store_key'] = $data["store_key"];
								$monthlyrecap['file_name'] = $targetFile;
								$monthlyrecap['month'] = date("M",strtotime($data['cdate']));
								$monthlyrecap['year'] = date("yy",strtotime($data['cdate']));
								$monthlyrecap['cdate'] = $data['cdate'];
								$monthlyrecap['day'] = date("D",strtotime($data['cdate']));
								$monthlyrecap['baskin_sales'] = $json_data['br_retail_net_sales'];
								$monthlyrecap['dunkin_sales'] = $json_data['dd_retail_net_sales'];
								$monthlyrecap['net_sales'] = $json_data['br_retail_net_sales'] + $json_data['dd_retail_net_sales'];
								$monthlyrecap['newspaper'] = 0;
								$monthlyrecap['sales_tax'] = $json_data['sales_tax'];
								$monthlyrecap['gross_sales'] = $monthlyrecap['net_sales'] + $monthlyrecap['newspaper'] + $monthlyrecap['sales_tax'];
								$monthlyrecap['all_card_totals'] = $cardrecap['cc_recap_total_sales'] + $cardrecap['dd_cards_total'] + $cardrecap['dd_paper_redeemed'] ;

								$monthlyrecap['bank_deposit'] = $json_data['deposit_total'];
								$monthlyrecap['actual_bank_deposit'] = 0;
								$monthlyrecap['paidout'] = $json_data['paid_out'];
//								$monthlyrecap['pos_over_short'] = round(($monthlyrecap['all_card_totals']+$monthlyrecap['paidout'] ) - $monthlyrecap['gross_sales'],2);
								$monthlyrecap['pos_over_short'] = $json_data['over_shot'];
								$monthlyrecap['actual_over_shot'] = 0;
								$monthlyrecap['guess_count'] = $json_data['trans_count_qty'];
								$monthlyrecap['avg_ticket'] = $monthlyrecap['guess_count'] > 0 ? round($monthlyrecap['net_sales'] / $monthlyrecap['guess_count'],2) : 0;
								$monthlyrecap['item_del_bef_total'] = $json_data['item_deletions_before_total_amount'];
								$monthlyrecap['item_del_aft_total'] = $json_data['item_deletions_after_total_qty_amount'];
								$monthlyrecap['cancel_transaction'] = $json_data['cancelled_transactions_amount'];
								$monthlyrecap['tracked_fee_exempt_net_sales'] = $json_data['tracked_fee_exempt_net_sales'];
								$monthlyrecap['charity_net_sales'] = $json_data['charity_net_sales'];
								$monthlyrecap['paid_ins'] = $json_data['paid_ins'];
								$monthlyrecap['gift_certificate_sales'] = $json_data['gift_certificate_sales'];

								$data['data']= json_encode($json_data);
                                                              
                                                                
								$this->masterpos_model->add($table,$data);
								$this->masterpos_model->add("card_recap",$cardrecap);
								$this->masterpos_model->add("monthly_recap",$monthlyrecap);
							}

						}
		    		}
						$msg = "";
						$class = "";
						if(sizeof($keys_failed_data) == 0){
							$msg = "Imported successfully!!";
							$class = "alert-success";
						}
						$this->session->set_flashdata('msg', '<div class="alert '.$class.' alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>'.$msg.'</div>');
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

}
