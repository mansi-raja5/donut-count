<?php

defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';

class Daily extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('weekly_model');
        $this->load->model('common_model');
        $this->load->model('labor_model');
        $this->load->model('masterpos_model');
    }

    public function index() {
        $data['title'] = 'Daily';
        $sql = "SELECT * FROM `store_master` SM ORDER BY SM.key";
        $data['store_list'] = $this->weekly_model->query_result($sql);
        $data['default_store_key']  = $data['store_list'][0]->key;
        $data['default_date']       = date('Y-m-d');
        $data['daily_date']         = $thisDate = date('Y-m-d');
        $this->template->load('listing', 'daily/layout', $data);
    }

    public function showMainTabbing() {
        $postData       = $this->input->post();
        $mainActiveTab  = isset($postData['maintab']) ? ltrim($postData['maintab'], '#') : 'snapshot';

        $data['main_tab_title'] = strtoupper(str_replace("_", " ", $mainActiveTab));
        //store Data
        $sql            = "SELECT * FROM `store_master` SM ORDER BY SM.key";
        $data['stores'] = $this->weekly_model->query_result($sql);

        if(isset($postData['daily_date'])){
            $data['daily_date'] =  $thisDate = date('Y-m-d', strtotime($postData['daily_date']));
        }else{
            $data['daily_date'] = $thisDate = date('Y-m-d');
        }

        $data['weeks']              = getWeekStartingEndingDateFromMonth($thisDate, "year");
        $data['default_store_key']  = isset($postData['store_key']) ? $postData['store_key'] : $data['stores'][0]->key;
        $data['default_date']       = $data['daily_date'];

        if ($mainActiveTab == 'snapshot') {
            $data['title']               = 'Daily Snapshot';
            $current_date                = $data['daily_date'];
            $data['current_date']        = $current_date;
            $data['refund_amount_limit'] = 25; //Refund Limit -  make it dynamic from setting

            //store Data
            $sql            = "SELECT * FROM `store_master` SM ORDER BY SM.key";
            $data['stores'] = $this->masterpos_model->query_result($sql);

            //pos data for current date
            $sql = "SELECT POS.*
                    FROM `store_master` SM
                    LEFT JOIN `master_pos_daily` POS ON POS.store_key = SM.key
                    WHERE POS.cdate = '{$current_date}'
                    ORDER BY SM.key";
            $posData          = $this->masterpos_model->query_result($sql);
            $data['pos_data'] = [];
            foreach ($posData as $_posData) {
                $data['pos_data'][$_posData->store_key] = $_posData->data;
            }

            //get previous year same date
            $currentDateInfo       = getDateInfo($current_date);
            $previousDate          = getPreviousYearSameDay($currentDateInfo['day_number_of_week'], $currentDateInfo['week_number_of_year'], ($currentDateInfo['year'] - 1));
            $data['previous_date'] = $previousDate;

            //pos data for previous date
            $sql = "SELECT POS.*
                    FROM `store_master` SM
                    LEFT JOIN `master_pos_daily` POS ON POS.store_key = SM.key
                    WHERE POS.cdate = '{$previousDate}'
                    ORDER BY SM.key";
            $pos_previous_data         = $this->masterpos_model->query_result($sql);
            $data['pos_previous_data'] = [];
            foreach ($pos_previous_data as $_pos_previous_data) {
                $data['pos_previous_data'][$_pos_previous_data->store_key] = $_pos_previous_data->data;
            }

        } else if ($mainActiveTab == 'donut_count') {
            $data['month']               = isset($data['daily_date']) && $data['daily_date'] != '' ? date("m", strtotime($data['daily_date'])) : date('m');
            $data['year']                = isset($data['daily_date']) && $data['daily_date'] != '' ? date("Y", strtotime($data['daily_date'])) : date('Y');
            $data                        = array_merge($data, $this->input->post());
            $data['dynamic_column']      = $this->common_model->getDonutDynamiccolumn("dynamic_donutcount_column");

            //advanced store years
            $advanceColorCode = ['ebf5fb', 'd6eaf8', 'aed6f1', '85c1e9', '5dade2', '3498db', '2e86c1', '2874a6', '21618c', '1b4f72'];
            if (isset($postData['advance_years'])) {
                foreach ($postData['advance_years'] as $key => $_advance_years) {
                    if (isset($advanceColorCode[$key])) {
                        $advanceYear[$advanceColorCode[$key]] = $_advance_years;
                    } else {
                        $advanceYear['00000'] = $_advance_years;
                    }
                }
            } else {
                $advanceYear['5dade2'] = date('Y', strtotime($thisDate));
            }
            $data['advanceYear'] = $advance_data['advanceYear'] = $advanceYear;
            //advance advance_status
            $dateGranularity = isset($postData['advance_status']) ? $postData['advance_status'] : '';
            $advance_data['advance_status_selected']    = isset($postData['advance_status']) ? $postData['advance_status'][0] : '';

            //advance sub options code
            $advance_data['special_day_list']           = $this->weekly_model->query_result("SELECT group_concat(id) as special_ids,`name`  FROM `special_day` GROUP BY `name`");
            $advance_data['special_day_selected']       = isset($postData['advance_special_day']) ? $postData['advance_special_day'][0] : '';
            $advance_data['advance_date_selected']      = isset($postData['advance_date']) ? $postData['advance_date'][0] : '';
            $advance_data['advance_week_date_selected'] = isset($postData['advance_week_date']) ? $postData['advance_week_date'][0] : '';
            $advance_data['advance_from_date_selected'] = isset($postData['advance_from_date']) ? $postData['advance_from_date'][0] : '';
            $advance_data['advance_to_date_selected']   = isset($postData['advance_to_date']) ? $postData['advance_to_date'][0] : '';

            $data['advance_search_html']    = $this->load->view('daily/advance_search', $advance_data, true);
            $data['special_day_selected']   = 0;
            if($advance_data['special_day_selected']) // Special Day
            {
                $sql = "SELECT `date` FROM `special_day` WHERE id IN (".$advance_data['special_day_selected'].")";
                $special_day_data = $this->weekly_model->query_result($sql);
                $advanceYear = [];
                foreach ($special_day_data as $_display_dates) {
                    $data['list'][] = $_display_dates->date;
                    $advanceYear[]  = date("Y",strtotime($_display_dates->date));
                }
                $data['advanceYear'] = $advance_data['advanceYear'] = $advanceYear;
                $data['special_day_selected'] = 1;
            }
            elseif(isset($postData['advance_status'])
                && $postData['advance_status'][0] == 'day'
                && $advance_data['advance_date_selected']) // Day
            {
                $data['to_date']      = $advance_data['advance_date_selected'];
                $data['from_date']    = $advance_data['advance_date_selected'];
            }
            elseif(isset($postData['advance_status'])
                && $postData['advance_status'][0] == 'week'
                && $advance_data['advance_week_date_selected']) //Weekending date
            {
                $data['to_date']    = $advance_data['advance_week_date_selected'];
                $data['from_date']  = date('Y-m-d',strtotime($advance_data['advance_week_date_selected'].'- 6 Day'));
            }
            elseif(isset($postData['advance_status'])
                && $postData['advance_status'][0] == 'custom_date'
                && $advance_data['advance_from_date_selected']
                && $advance_data['advance_to_date_selected']) //Custom Date Range
            {
                $data['to_date']      = $advance_data['advance_to_date_selected'];
                $data['from_date']    = $advance_data['advance_from_date_selected'];
            }
            elseif(isset($postData['advance_status'])
                && $postData['advance_status'][0] == 'year')
            {
                $data['from_date']  = max($data['advanceYear']) . '-01-01';
                $data['to_date']    = max($data['advanceYear']) . '-12-31';
                $data['is_full_year'] = true;
            }
            else
            {
                $data['from_date']  = date('Y',strtotime($data['daily_date'])) . '-01-01';
                $data['to_date']    = $data['daily_date'];
            }
            $data['donutdata']  = $this->common_model->getDonutCount($data,true);
             //echo '<pre>';print_r($data['donutdata']);die;

        } else if ($mainActiveTab == 'paid_out_recap') {
            $data['month']          = isset($data['daily_date']) && $data['daily_date'] != '' ? date("m", strtotime($data['daily_date'])) : date('m');
            $data['year']           = isset($data['daily_date']) && $data['daily_date'] != '' ? date("Y", strtotime($data['daily_date'])) : date('Y');
            $data                   = array_merge($data, $this->input->post());
            $data['store_key']      = 350432;
            $data['dynamic_column'] = $this->common_model->getDynamiccolumn("dynamic_dailysales_column", "key_name", "column_name");
            $paidoutdata            = $this->common_model->getData($data, "paid_out_recap", 1);


            if (isset($paidoutdata) && !empty($paidoutdata)) {
                foreach ($paidoutdata as $pRow) {
                    $cdate                                       = str_replace("-", "", $pRow['cdate']);
                    $data['paidout'][$pRow['store_key']][$cdate] = $pRow;
                }
            }
            $data['alldates']              = $this->common_model->get_dates($data['month'], $data['year'], "month");
            $data['paidoutamount_data']    = $this->common_model->getPaidAmount($data, 1);
            $data['invoice_uploaded_data'] = $this->common_model->getInvoiceUpload($data, 1);
        } else if ($mainActiveTab == 'card_recap') {
            $data['month']          = isset($data['daily_date']) && $data['daily_date'] != '' ? date("m", strtotime($data['daily_date'])) : date('m');
            $data['year']           = isset($data['daily_date']) && $data['daily_date'] != '' ? date("Y", strtotime($data['daily_date'])) : date('Y');
            $data                   = array_merge($data, $this->input->post());
            $data['dynamic_column'] = $this->common_model->getDynamiccolumn("dynamic_cardrecap_column", "key_name", "key_label");
            $data['cardrecapdata']  = $this->common_model->getDatewiseData($data, "card_recap", 1);
            $data['alldates']       = $this->common_model->get_dates($data['month'], $data['year'], "month");
        } else if ($mainActiveTab == 'monthly_recap') {
              $data['month']          = isset($data['daily_date']) && $data['daily_date'] != '' ? date("m", strtotime($data['daily_date'])) : date('m');
            $data['year']           = isset($data['daily_date']) && $data['daily_date'] != '' ? date("Y", strtotime($data['daily_date'])) : date('Y');
            $data                   = array_merge($data, $this->input->post());
            $data['dynamic_column'] = $this->common_model->getDynamiccolumn("dynamic_monthlyrecap_column", "key_name", "key_label");
            $data['monthlydata']    = $this->common_model->getDatewiseData($data, "monthly_recap", 1);
            $data['alldates']       = $this->common_model->get_dates($data['month'], $data['year'], "month");
        }
        if ($mainActiveTab == 'snapshot') {
            echo $this->load->view('snapshot/daily', $data, true);
        } else {
            echo $this->load->view('daily/' . $mainActiveTab, $data, true);
        }
        exit;
    }
}
