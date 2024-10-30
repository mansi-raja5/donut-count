<?php

use PhpOffice\PhpSpreadsheet\IOFactory;

defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';

class Weekly extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('weekly_model');
        $this->load->model('common_model');
        $this->load->model('labor_model');
        $this->load->model('masterpos_model');
        $this->load->model('masterpos_model');
        $this->load->model('special_day_model');
        $this->load->model('season_model');
        $this->load->model('year_setting_model');
    }

    public function index()
    {
        $data['title'] = 'Weelkly';
        $sql = "SELECT * FROM `store_master` SM ORDER BY CASE WHEN SM.key = 3082 THEN 2 WHEN SM.status = 'A' THEN 1 ELSE 3 END ASC, SM.key ASC";
        $data['store_list'] = $this->weekly_model->query_result($sql);
        $data['weekend_date'] = $thisDate = (date('D') != 'Sat') ? date('Y-m-d', strtotime('last Saturday')) : date('Y-m-d');
        $this->template->load('listing', 'weekly/layout', $data);
    }

    public function showMainTabbing()
    {
        $postData = $this->input->post();
        $mainActiveTab = ltrim($postData['maintab'], '#');
        $advance_data['mainActiveTab'] = $mainActiveTab;

        $data['main_tab_title'] = strtoupper(str_replace("_", " ", $mainActiveTab));
        //store Data
        $sql = "SELECT * FROM `store_master` SM ORDER BY CASE WHEN SM.key = 3082 THEN 2 WHEN SM.status = 'A' THEN 1 ELSE 3 END ASC, SM.key ASC";
        $advance_data['stores'] = $data['stores'] = $this->weekly_model->query_result($sql);
        $sql = "SELECT group_concat(id) as special_ids,`name`  FROM `special_day` GROUP BY `name`";
        $advance_data['special_day_list'] = $this->weekly_model->query_result($sql);
        $sql = "SELECT group_concat(id) as id,`name`  FROM `season` GROUP BY `name`";
        $advance_data['season_list'] = $this->weekly_model->query_result($sql);

        if (isset($postData['weekend_date'])) {
            $advance_data['weekend_date'] = $data['weekend_date'] = $thisDate = date('Y-m-d', strtotime($postData['weekend_date'][0]));
        } else {
            $advance_data['weekend_date'] = $data['weekend_date'] = $thisDate = date('Y-m-d', strtotime('last Saturday'));
        }
        $data['weeks'] = getWeekStartingEndingDateFromMonth($thisDate, "year", $is_previous = 1, $is_report = 1);

        foreach ($advance_data['stores'] as $store) {
            $data['store_list'][$store->key] = $store->name;
        }

        if (isset($postData['advance_store_key'])) {
            foreach ($postData['advance_store_key'] as $key => $_advance_store_key) {
                $advanceStore[] = $_advance_store_key;
            }
        } else {
            foreach ($advance_data['stores'] as $key => $_storeData) {
                $advanceStore[] = $_storeData->key;
            }
        }
        $data['advanceStore'] = $advanceStore;
        $advance_data['advanceStore'] = $advanceStore;

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
            $advanceYear['85c1e9'] = date('Y', strtotime($thisDate)) - 1;
            $advanceYear['5dade2'] = date('Y', strtotime($thisDate));
        }

        $data['advanceYear'] = $advanceYear;
        $advance_data['advanceYear'] = $advanceYear;

        //advance advance_status
        $dateGranularity = isset($postData['advance_status']) ? $postData['advance_status'] : '';
        $advance_data['advance_status_selected'] = isset($postData['advance_status']) ? $postData['advance_status'][0] : '';

        //advance sub options code
        $advance_data['special_day_selected'] = isset($postData['advance_special_day']) ? $postData['advance_special_day'][0] : '';
        $advance_data['advance_date_selected'] = isset($postData['advance_date']) ? $postData['advance_date'][0] : '';
        $advance_data['advance_week_date_selected'] = isset($postData['advance_week_date']) ? $postData['advance_week_date'][0] : '';
        $advance_data['advance_season_selected'] = isset($postData['advance_season']) ? $postData['advance_season'][0] : '';
        $advance_data['advance_from_date_selected'] = isset($postData['advance_from_date']) ? $postData['advance_from_date'][0] : '';
        $advance_data['advance_to_date_selected'] = isset($postData['advance_to_date']) ? $postData['advance_to_date'][0] : '';
        $advance_data['filter_view'] = $data['filter_view'] = $postData['filter_view'][0] ?? null;
        $data['advance_search_html'] = $this->load->view('weekly/advance_search', $advance_data, true);

        $data['display_dates'] = [];
        if ($advance_data['special_day_selected']) // Special Day
        {
            $sql = "SELECT DISTINCT `date` FROM `special_day` WHERE id IN (" . $advance_data['special_day_selected'] . ")";
            $special_day_data = $this->weekly_model->query_result($sql);

            $display_dates = [];
            foreach ($special_day_data as $_display_dates) {
                $display_dates[] = $_display_dates->date;
            }
            $data['display_dates'] = array_map("showInDateFormat", $display_dates);
        } elseif ($advance_data['advance_date_selected']) // Day
        {
            $selectDate = $advance_data['advance_date_selected'];
            $weekInfo = getDateInfo($selectDate);
            foreach ($advance_data['advanceYear'] as $_advanceYear) {
                $getDate = getPreviousYearSameDay($weekInfo['day_number_of_week'], $weekInfo['week_number_of_year'], ($_advanceYear));
                $display_dates[$_advanceYear] = date('Y-m-d', strtotime($getDate));
            }
            $data['display_week_dates'] = $display_dates;
            $data['display_dates'][] = "Week " . $weekInfo['week_number_of_year'] . "<br> Day " . $weekInfo['day_number_of_week'];
        } elseif ($advance_data['advance_season_selected']) //Season
        {
            $sql = "SELECT * FROM `season` WHERE id IN (" . $advance_data['advance_season_selected'] . ")";
            $season_data = $this->weekly_model->query_result($sql);
            $season_dates['from_date'] = $season_data[0]->from_date;
            $season_dates['to_date'] = $season_data[0]->to_date;
            $data['display_dates'][] = $season_data[0]->name . "<br>" . showInDateFormat($season_dates['from_date']) . ' to ' . showInDateFormat($season_dates['to_date']);
        } elseif ($advance_data['advance_week_date_selected']) //Season
        {
            $selectDate = $advance_data['advance_week_date_selected'];
            $weekInfo = getDateInfo($selectDate);
            $week_dates['from_date'] = $weekInfo['start_of_week'];
            $week_dates['to_date'] = $weekInfo['end_of_week'];
            $data['display_dates'][] = "Week " . $weekInfo['day_number_of_week'] . "<br>" . showInDateFormat($week_dates['from_date']) . ' to ' . showInDateFormat($week_dates['to_date']);
//            $data['display_dates'][] = $weekInfo['day_number_of_week'] < 10 ? "0".$weekInfo['day_number_of_week'] : $weekInfo['day_number_of_week'];
        } elseif ($advance_data['advance_from_date_selected'] && $advance_data['advance_to_date_selected']) //Custom Date Range
        {
            $season_dates['from_date'] = $advance_data['advance_from_date_selected'];
            $season_dates['to_date'] = $advance_data['advance_to_date_selected'];
            $data['display_dates'][] = showInDateFormat($season_dates['from_date']) . ' to ' . showInDateFormat($season_dates['to_date']);
        } else {
            $data['display_dates'] = $data['advanceYear'];
        }

        if ($mainActiveTab == 'labor') {
            $whereCondition = '1';
            $whereCondition = "YEAR(week_ending_date) IN (" . implode(',', $advanceYear) . ")
                    AND `store_key` IN (" . implode(',', $advanceStore) . ")";
            if ($advance_data['special_day_selected'] || $advance_data['advance_date_selected']) {
                $whereCondition = "`week_ending_date` IN ('" . implode("','", $display_dates) . "')";
            }
            if ($advance_data['advance_season_selected']) {
                $whereCondition = " `week_ending_date` > '" . $season_dates['from_date'] . "' AND `week_ending_date` < '" . $season_dates['to_date'] . "'";
            }

            $labor = $this->labor_model->get("labor_summary", $whereCondition);
            $data['tax_percent'] = $this->labor_model->getTaxpercent();
            $dateInfo = getDateInfo(date('Y-m-d'));

            if (isset($labor) && !empty($labor)) {
                $firstDate = '';
                foreach ($labor as $lRow) {

                    if ($advance_data['advance_week_date_selected'] || $advance_data['special_day_selected'] || $advance_data['advance_season_selected'] || ($advance_data['advance_from_date_selected'] && $advance_data['advance_to_date_selected'])) {
                        $firstDate = $data['display_dates'][0];
                        if (isset($data['labordara_data'][$lRow['store_key']][$lRow['lyear']][$firstDate])) {
                            $data['labordara_data'][$lRow['store_key']][$lRow['lyear']][$firstDate]['gross_pay'] += (float) $lRow['gross_pay'];
                            $data['labordara_data'][$lRow['store_key']][$lRow['lyear']][$firstDate]['tax_amount'] += (float) $lRow['tax_amount'];
                            $data['labordara_data'][$lRow['store_key']][$lRow['lyear']][$firstDate]['net_sales'] += (float) $lRow['net_sales'];
                            $data['labordara_data'][$lRow['store_key']][$lRow['lyear']][$firstDate]['tax_percentage'] += (float) $lRow['tax_percentage'];
                            $data['labordara_data'][$lRow['store_key']][$lRow['lyear']][$firstDate]['bonus'] += (float) $lRow['bonus'];
                            $data['labordara_data'][$lRow['store_key']][$lRow['lyear']][$firstDate]['total_pay'] += (float) $lRow['total_pay'];
                            $data['labordara_data'][$lRow['store_key']][$lRow['lyear']][$firstDate]['labor_percentage'] += (float) $lRow['labor_percentage'];
                        } else {
                            $data['labordara_data'][$lRow['store_key']][$lRow['lyear']][$firstDate] = $lRow;
                        }
                    } else {
                        $dateInfo = getDateInfo($lRow['week_ending_date']);
                        $weekNumber = $dateInfo['week_number_of_year'];
                        $data['labordara_data'][$lRow['store_key']][$lRow['lyear']][$weekNumber] = $lRow;
                    }

                }

                foreach ($data['labordara_data'] as $sk => $store) {
                    foreach ($store as $yk => $year) {
                        foreach ($year as $wk => $week) {
                            foreach ($week as $vk => $value) {
                                if (isset($data['labordara_data']['Accumulate'][$yk][$wk][$vk])) {
                                    $data['labordara_data']['Accumulate'][$yk][$wk][$vk] += (float) $value;
                                } else {
                                    $data['labordara_data']['Accumulate'][$yk][$wk][$vk] = (float) $value;
                                }
                            }
                        }
                    }
                }
            }

            if (($postData['is_export'] ?? 'false') == 'true') {
                $this->generateExcelForLabor($data);
                return;
            }
        } else if ($mainActiveTab == 'sales_comparision') {
            $data['weeks'] = $this->year_setting_model->getWeekStartingEndingDate($thisDate);

            $date_list = [];
            if ($advance_data['advance_date_selected']) {
                $selectDate = $advance_data['advance_date_selected'];
                $weekInfo = $this->year_setting_model->getDateInfo($selectDate);
                foreach ($advance_data['advanceYear'] as $_advanceYear) {
                    $getDate = $this->year_setting_model->getPreviousYearSameDay($weekInfo['day_number_of_week'], $weekInfo['week_number_of_year'], $_advanceYear);
                    $data['column_data'][$_advanceYear] = showInDateFormat($getDate);
                    $date_list[] = $getDate;
                }
                $data['row_data'][] = $weekInfo['week_number_of_year'];
                $data['row_data_label'][] = "P{$weekInfo['month']}-W{$weekInfo['week_number_of_month']}<br>{$weekInfo['day_number_of_week']} Day";
            } elseif ($advance_data['advance_week_date_selected']) {
                $selectDate = $advance_data['advance_week_date_selected'];
                $weekInfo = $this->year_setting_model->getDateInfo($selectDate);
                foreach ($advance_data['advanceYear'] as $_advanceYear) {
                    for ($i = 0; $i <= 7; $i++) {
                        $getDate = $this->year_setting_model->getPreviousYearSameDay($i, $weekInfo['week_number_of_year'], $_advanceYear);
                        $date_list[] = $getDate;
                    }
                    $startDate = showInDateFormat($this->year_setting_model->getPreviousYearSameDay(1, $weekInfo['week_number_of_year'], $_advanceYear));
                    $endDate = showInDateFormat($this->year_setting_model->getPreviousYearSameDay(7, $weekInfo['week_number_of_year'], $_advanceYear));
                    $data['column_data'][$_advanceYear] = "{$startDate} to<br>{$endDate}";
                }
                $data['row_data'][] = $weekInfo['week_number_of_year'];
                $data['row_data_label'][] = "P{$weekInfo['month']}-W{$weekInfo['week_number_of_month']}";
            } elseif ($advance_data['advance_from_date_selected'] && $advance_data['advance_to_date_selected']) {
                $date_from = strtotime($advance_data['advance_from_date_selected']);
                $date_to = strtotime($advance_data['advance_to_date_selected']);
                foreach ($advance_data['advanceYear'] as $_advanceYear) {
                    for ($i = $date_from; $i <= $date_to; $i += 86400) {
                        $date_list[] = $_advanceYear . "-" . date("m-d", $i);
                    }
                    $startDate = showInDateFormat($_advanceYear . "-" . date("m-d", $date_from));
                    $endDate = showInDateFormat($_advanceYear . "-" . date("m-d", $date_to));
                    // $data['column_data'][$_advanceYear] = "{$startDate} to<br>{$endDate}";
                }
                $data['row_data'][] = 'date_range';
                $data['row_data_label'][] = date("m/d", $date_from) . " to " . date("m/d", $date_to);
            } elseif ($advance_data['special_day_selected']) {
                $sql = "SELECT DISTINCT `date` FROM `special_day` WHERE id IN (" . $advance_data['special_day_selected'] . ")";
                $special_day_data = $this->weekly_model->query_result($sql);
                foreach ($special_day_data as $_special_day_data) {
                    $selectDate = $_special_day_data->date;
                    $date_list[] = $selectDate;
                    $weekInfo = $this->year_setting_model->getDateInfo($selectDate);
                    $data['row_data'][] = $weekInfo['week_number_of_year'];
                    $data['row_data_label'][] = showInDateFormat($selectDate);
                }
                $data['advanceYear'] = [];
                $data['advanceYear']['00000'] = 'special_day';
            } else {
                $date_from = strtotime($this->year_setting_model->getYearStartingDate(date('Y', strtotime($advance_data['weekend_date']))));
                $date_to = strtotime($advance_data['weekend_date']);
                foreach ($advance_data['advanceYear'] as $_advanceYear) {
                    for ($i = $date_from; $i <= $date_to; $i += 86400) {
                        $date_list[] = $_advanceYear . "-" . date("m-d", $i);
                    }
                }
            }

            $whereCondition = "cdate IN ('" . implode("','", $date_list) . "')";
            $sql = "SELECT * FROM master_pos_daily WHERE store_key IN (" . implode(',', $advanceStore) . ")
                                AND {$whereCondition} ORDER BY store_key";
            $posData = $this->weekly_model->query_result($sql);

            $data['pos_data'] = [];
            $data['added_week'] = [];
            $baseYearWeeks = $this->year_setting_model->getNumberOfWeeks(date('Y', strtotime($thisDate)));
            $this->load->model('settings_model');
            $excludeCalcData = $this->settings_model->getExcludeCalcData();
            foreach ($posData as $_posData) {
                $posInfo = json_decode($_posData->data);
                if ($advance_data['advance_from_date_selected'] && $advance_data['advance_to_date_selected']) {
                    $dateInfo = getDateInfo($_posData->cdate);
                    $dateInfo['week_number_of_year'] = 'date_range';
                } else {
                    $dateInfo = $this->year_setting_model->getDateInfo($_posData->cdate);
                    if ($advance_data['special_day_selected']) {
                        $dateInfo['year'] = 'special_day';
                    }
                }
                foreach ($posInfo as $_posKey => $_posInfo) {
                    if (!in_array($_posKey, array('dd_retail_net_sales', 'br_retail_net_sales', 'trans_count_qty'))) {
                        continue;
                    }
                    $currentYearWeeks = $this->year_setting_model->getNumberOfWeeks($dateInfo['year']);
                    $weekPosition = $this->year_setting_model->getWeekPosition($dateInfo['year']);
                    $updatedWeek = $dateInfo['week_number_of_year'];
                    if ($currentYearWeeks == 52 && $baseYearWeeks == 53) {
                        $isShifted = $this->year_setting_model->isShifted($dateInfo['year']);
                        if ($dateInfo['week_number_of_year'] == $weekPosition) {
                            if (isset($data['pos_data'][$_posData->store_key][$dateInfo['year']][$isShifted ? 1 : 53][$_posKey])) {
                                $data['pos_data'][$_posData->store_key][$dateInfo['year']][$isShifted ? 1 : 53][$_posKey] += (float) $_posInfo;
                            } else {
                                $data['pos_data'][$_posData->store_key][$dateInfo['year']][$isShifted ? 1 : 53][$_posKey] = (float) $_posInfo;
                            }
                            if (!$this->isExcludeCalcData($excludeCalcData, $_posData->store_key, $dateInfo['date'])) {
                                if (isset($data['pos_data']['Accumulate'][$dateInfo['year']][$isShifted ? 1 : 53][$_posKey])) {
                                    $data['pos_data']['Accumulate'][$dateInfo['year']][$isShifted ? 1 : 53][$_posKey] += (float) $_posInfo;
                                } else {
                                    $data['pos_data']['Accumulate'][$dateInfo['year']][$isShifted ? 1 : 53][$_posKey] = (float) $_posInfo;
                                }
                            }
                            $data['added_week'][$dateInfo['year']][$isShifted ? 1 : 53] = true;
                        }
                        if ($isShifted) {
                            $updatedWeek = $updatedWeek + 1;
                        }
                    } else if ($currentYearWeeks == 53 && $baseYearWeeks == 52) {
                        if ($weekPosition == 1) {
                            $updatedWeek = $updatedWeek - 1;
                        }
                    }
                    if (isset($data['pos_data'][$_posData->store_key][$dateInfo['year']][$updatedWeek][$_posKey])) {
                        $data['pos_data'][$_posData->store_key][$dateInfo['year']][$updatedWeek][$_posKey] += (float) $_posInfo;
                    } else {
                        $data['pos_data'][$_posData->store_key][$dateInfo['year']][$updatedWeek][$_posKey] = (float) $_posInfo;
                    }
                    if (!$this->isExcludeCalcData($excludeCalcData, $_posData->store_key, $dateInfo['date'])) {
                        if (isset($data['pos_data']['Accumulate'][$dateInfo['year']][$updatedWeek][$_posKey])) {
                            $data['pos_data']['Accumulate'][$dateInfo['year']][$updatedWeek][$_posKey] += (float) $_posInfo;
                        } else {
                            $data['pos_data']['Accumulate'][$dateInfo['year']][$updatedWeek][$_posKey] = (float) $_posInfo;
                        }
                    }
                }
            }

            if (($postData['is_export'] ?? 'false') == 'true') {
                $this->generateExcelForSalesComparision($data);
                return;
            }
        } elseif ($mainActiveTab == 'snapshot') {

            $data['title'] = 'Weekly Snapshot';
            $todayInfo = getDateInfo(date('Y-m-d'));
            $week_ending_date = $thisDate;
            $data['week_ending_date'] = date('Y-m-d', strtotime($week_ending_date));

            $weekInfo = getDateInfo($data['week_ending_date']);
            $weekStartDate = $weekInfo['start_of_week'];
            $weekEndDate = $weekInfo['end_of_week'];
            $data['week_starting_date'] = $weekStartDate;
            $data['current_year'] = $weekInfo['year'];

            //store Data
            $sql = "SELECT * FROM `store_master` SM ORDER BY SM.key";
            $data['stores'] = $this->masterpos_model->query_result($sql);

            //get Admin settings
            $sql = "SELECT * FROM `admin_settings` WHERE key_name IN ('projection_lbr_percentage','refund_amount_limit')";
            $adminSettings = $this->masterpos_model->query_result($sql);

            $data['refund_amount_limit'] = 0;
            foreach ($adminSettings as $_admin_settings) {
                if ($_admin_settings->key_name == 'refund_amount_limit') {
                    $data['refund_amount_limit'] = $_admin_settings->key_value;
                }

                if ($_admin_settings->key_name == 'projection_lbr_percentage') {
                    $data['projection_lbr_percentage'] = $_admin_settings->key_value;
                }
            }

            //get Admin settings
            $sql = "SELECT * FROM `admin_store_setting`";
            $adminStoreSettings = $this->masterpos_model->query_result($sql);
            $data['admin_store_settings'] = [];
            foreach ($adminStoreSettings as $_adminStoreSettings) {
                $data['admin_store_settings'][$_adminStoreSettings->store_key] = json_decode($_adminStoreSettings->data);
            }

            //pos data for current date
            $sql = "SELECT POS.*
                FROM `store_master` SM
                LEFT JOIN `master_pos_daily` POS ON POS.store_key = SM.key
                WHERE POS.cdate >= '{$weekStartDate}' AND POS.cdate <= '{$weekEndDate}'
                ORDER BY SM.key";
            $posData = $this->masterpos_model->query_result($sql);
            $data['pos_data'] = [];
            foreach ($posData as $_posData) {
                foreach (json_decode($_posData->data) as $posKey => $posValue) {
                    if (isset($data['pos_data'][$_posData->store_key][$posKey])) {
                        $data['pos_data'][$_posData->store_key][$posKey] += $posValue;
                    } else {
                        $data['pos_data'][$_posData->store_key][$posKey] = $posValue;
                    }
                }
            }
            //get previous year same date
            $previousDate = getPreviousYearSameDay($weekInfo['day_number_of_week'], $weekInfo['week_number_of_year'], ($weekInfo['year'] - 1));

            $previousWeekInfo = getDateInfo($previousDate);
            $data['previous_starting_date'] = $previousWeekInfo['start_of_week'];
            $data['previous_ending_date'] = $previousWeekInfo['end_of_week'];
            $data['previous_year'] = $previousWeekInfo['year'];

            //pos data for previous date
            $sql = "SELECT POS.*
                FROM `store_master` SM
                LEFT JOIN `master_pos_daily` POS ON POS.store_key = SM.key
                WHERE POS.cdate >= '{$data['previous_starting_date']}' AND POS.cdate <= '{$data['previous_ending_date']}'
                ORDER BY SM.key";
            $pos_previous_data = $this->masterpos_model->query_result($sql);
            $data['pos_previous_data'] = [];
            foreach ($pos_previous_data as $_pos_previous_data) {
                foreach (json_decode($_pos_previous_data->data) as $posKey => $posValue) {
                    if (isset($data['pos_previous_data'][$_pos_previous_data->store_key][$posKey])) {
                        $data['pos_previous_data'][$_pos_previous_data->store_key][$posKey] += $posValue;
                    } else {
                        $data['pos_previous_data'][$_pos_previous_data->store_key][$posKey] = $posValue;
                    }
                }
            }

            //donut count data
            $sql = "SELECT SM.key,
                    MAX(IF(donut_type = 'Donuts',total_order,0)) as donut_total,
                    MAX(IF(donut_type = 'Donuts',total_sale,0)) as donut_sale,
                    MAX(IF(donut_type = 'Munkins',total_order,0)) as munckins_total,
                    MAX(IF(donut_type = 'Munkins',total_sale,0)) as munckins_sale,
                    MAX(IF(donut_type = 'Fancy',total_order,0)) as fancy_total,
                    MAX(IF(donut_type = 'Fancy',total_sale,0)) as fancy_sale
                FROM `store_master` SM
                LEFT JOIN `donut_count` DC ON DC.store_key = SM.key
                WHERE DC.daily_date >= '{$weekStartDate}' AND DC.daily_date <= '{$weekEndDate}'
                GROUP BY SM.key
                ORDER BY SM.key";
            $donutCount = $this->masterpos_model->query_result($sql);
            $data['donut_count'] = [];
            foreach ($donutCount as $_donutCount) {
                $data['donut_count'][$_donutCount->key] = $_donutCount;
            }

            //customer_review data - GSS-OSAT - 45 days
            $sql = "SELECT SM.key,
                    MAX(IF(type = 'overall_satisfaction',n_number,0)) as overall_satisfaction_number,
                    MAX(IF(type = 'overall_satisfaction',five_star,0)) as overall_satisfaction_five_star,
                    MAX(IF(type = 'taste_of_beverage',five_star,0)) as taste_of_beverage_five_star,
                    MAX(IF(type = 'taste_of_food',five_star,0)) as taste_of_food_five_star,
                    MAX(IF(type = 'speed_of_service',five_star,0)) as speed_of_service_five_star,
                    MAX(IF(type = 'cleanliness',five_star,0)) as cleanliness_five_star,
                    MAX(IF(type = 'crew_manager',five_star,0)) as crew_manager_five_star
                FROM `store_master` SM
                LEFT JOIN `customer_review` CR ON CR.store_key = SM.key
                WHERE CR.end_date = '{$data['week_ending_date']}'
                AND CR.duration_type = '45_days'
                GROUP BY SM.key
                ORDER BY SM.key";
            $customerReview = $this->masterpos_model->query_result($sql);
            // echo '<pre>';print_r($customerReview);die;
            $data['customer_review'] = [];
            foreach ($customerReview as $_customerReview) {
                $data['customer_review'][$_customerReview->key] = $_customerReview;
            }

            //customer_review data - GSS-OSAT - YTD - 3 months
            $sql = "SELECT SM.key,
                    MAX(IF(type = 'overall_satisfaction' AND CR.duration_type = 'year_to_date',five_star,0)) as overall_satisfaction_five_star_ytd,
                    MAX(IF(type = 'overall_satisfaction' AND CR.duration_type = '3_months',five_star,0)) as overall_satisfaction_five_star_three
                FROM `store_master` SM
                LEFT JOIN `customer_review` CR ON CR.store_key = SM.key
                WHERE CR.end_date = '{$data['week_ending_date']}'
                AND CR.duration_type IN ('3_months','year_to_date')
                GROUP BY SM.key
                ORDER BY SM.key";
            $customerReviewOther = $this->masterpos_model->query_result($sql);
            // echo '<pre>';print_r($$customerReviewOther);die;
            $data['customer_review_other'] = [];
            foreach ($customerReviewOther as $_customerReviewOther) {
                $data['customer_review_other'][$_customerReviewOther->key] = $_customerReviewOther;
            }

            //cars_entry data - SOS
            $sql = "SELECT *
                FROM `store_master` SM
                LEFT JOIN `cars_entry` C ON C.store_key = SM.key
                WHERE C.weekend_date = '{$data['week_ending_date']}'
                GROUP BY SM.key
                ORDER BY SM.key";
            $cars = $this->masterpos_model->query_result($sql);
            $data['cars'] = [];
            foreach ($cars as $_cars) {
                $data['cars'][$_cars->key] = $_cars;
            }

            //labor data
            $sql = "SELECT SM.key, ls.*
                FROM `store_master` SM
                LEFT JOIN `labor_summary` ls ON ls.store_key = SM.key
                WHERE ls.week_ending_date = '{$data['week_ending_date']}'
                ORDER BY SM.key";
            $labor = $this->masterpos_model->query_result($sql);
            $data['labor'] = [];
            foreach ($labor as $_labor) {
                $data['labor'][$_labor->key] = $_labor;
            }
        } else if ($mainActiveTab == 'paid_out_recap') {

            $data['month'] = isset($data['month']) && $data['month'] != '' ? $data['month'] : 05;
            $data['year'] = isset($data['year']) && $data['year'] != '' ? $data['year'] : date('Y');
            $data = array_merge($data, $this->input->post());
            $data['store_key'] = 350432;
            $data['dynamic_column'] = $this->common_model->getDynamiccolumn("dynamic_dailysales_column", "key_name", "column_name");
            $paidoutdata = $this->common_model->getData($data, "paid_out_recap", 1);

            if (isset($paidoutdata) && !empty($paidoutdata)) {
                foreach ($paidoutdata as $pRow) {
                    $cdate = str_replace("-", "", $pRow['cdate']);
                    $data['paidout'][$pRow['store_key']][$cdate] = $pRow;
                }
            }
            $data['alldates'] = $this->common_model->get_dates($data['month'], $data['year'], "month");
            $data['paidoutamount_data'] = $this->common_model->getPaidAmount($data, 1);
            $data['invoice_uploaded_data'] = $this->common_model->getInvoiceUpload($data, 1);
        } else if ($mainActiveTab == 'card_recap') {
            $data['month'] = isset($data['month']) && $data['month'] != '' ? $data['month'] : 05;
            $data['year'] = isset($data['year']) && $data['year'] != '' ? $data['year'] : date('Y');
            $data = array_merge($data, $this->input->post());
            $data['dynamic_column'] = $this->common_model->getDynamiccolumn("dynamic_cardrecap_column", "key_name", "key_label");
            $data['cardrecapdata'] = $this->common_model->getDatewiseData($data, "card_recap", 1);
            $data['alldates'] = $this->common_model->get_dates($data['month'], $data['year'], "month");
        } else if ($mainActiveTab == 'monthly_recap') {
            $data['month'] = isset($data['month']) && $data['month'] != '' ? $data['month'] : 05;
            $data['year'] = isset($data['year']) && $data['year'] != '' ? $data['year'] : date('Y');
            $data = array_merge($data, $this->input->post());
            $data['dynamic_column'] = $this->common_model->getDynamiccolumn("dynamic_monthlyrecap_column", "key_name", "key_label");
            $data['monthlydata'] = $this->common_model->getDatewiseData($data, "monthly_recap", 1);
            $data['alldates'] = $this->common_model->get_dates($data['month'], $data['year'], "month");
        }
        if ($mainActiveTab == 'snapshot') {
            echo $this->load->view('snapshot/weekly', $data, true);
        } else {
            echo $this->load->view('weekly/' . $mainActiveTab, $data, true);
        }
        exit;
    }

    public function sales_comp_weekly_data_bk()
    {
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $store_key = $this->input->post('store_key');
        $week_no = $this->input->post('week_no');
        $start_date_format = date("Y-m-d", strtotime($start_date));
        $end_date_format = date("Y-m-d", strtotime($end_date));
        $previous_start_date_format = date('Y-m-d', strtotime('-1 year', strtotime($start_date_format)));
        $previous_end_date_format = date('Y-m-d', strtotime('-1 year', strtotime($end_date_format)));
        $date_year = date("Y", strtotime($start_date));
        $previous_year = $date_year - 1;

        //pos data for previous date
        $sql = "SELECT * FROM master_pos_daily WHERE cdate BETWEEN '" . $previous_start_date_format . "' AND '" . $previous_end_date_format . "' AND store_key = " . $store_key;
        $prev_posData = $this->weekly_model->query_result($sql);

        $sql = "SELECT * FROM master_pos_daily WHERE cdate BETWEEN '" . $start_date . "' AND '" . $end_date . "' AND store_key = " . $store_key;
        $posData = $this->weekly_model->query_result($sql);

        echo "<pre>";
        print_r($posData);
        echo "</pre>";
        exit;
        $data_arr = [];

        $html = '<tr class="detail-tr"><td colspan="16"><div class="slider"><table class="table table-striped table-bordered" style="padding-left:50px;"><tbody></tbody><thead>';
        $html .= '<tr><th> Date </th><th>DD ' . $previous_year . '</th><th >DD ' . $date_year . '</th><th>DD%+-</th><th width="15%">BR ' . $previous_year . '</th><th>BR ' . $date_year . '</th><th>BR%+-</th><th>Total ' . $previous_year . '</th><th>Total ' . $date_year . '</th><th>Total%+/-</th><th>Cust ' . $previous_year . '</th><th>Cust ' . $date_year . '</th><th>Cust %+-</th></tr></thead>';

        $count = 0;
        foreach ($posData as $_posData) {
            $dateInfo = getDateInfo($_posData->cdate);
            $weekNumber = $dateInfo['week_number_of_year'];
            $weekdate = $dateInfo['date'];
            $weekyear = $dateInfo['year'];
            $data_arr['year'][$count]['date'] = $weekdate;
            $posInfo = json_decode($_posData->data);
            foreach ($posInfo as $_posKey => $_posInfo) {
                if ($_posKey == 'dd_retail_net_sales' || $_posKey == 'br_retail_net_sales' || $_posKey == 'trans_count_qty') {
                    $data_arr['year'][$count][$_posKey] = $_posInfo;
                } else {
                    continue;
                }
            }
            $count++;
        }

        $prev_count = 0;
        foreach ($prev_posData as $_posData) {
            $dateInfo = getDateInfo($_posData->cdate);
            $weekNumber = $dateInfo['week_number_of_year'];
            $weekdate = $dateInfo['date'];
            $weekyear = $dateInfo['year'];

            $posInfo = json_decode($_posData->data);
            foreach ($posInfo as $_posKey => $_posInfo) {
                if ($_posKey == 'dd_retail_net_sales' || $_posKey == 'br_retail_net_sales' || $_posKey == 'trans_count_qty') {
                    $data_arr['previous_year'][$prev_count][$_posKey] = $_posInfo;
                } else {
                    continue;
                }
            }
            $prev_count++;
        }

        if (isset($data_arr['year']) && !empty($data_arr['year'])) {
            foreach ($data_arr['year'] as $key => $dRow) {
                $week_date = isset($dRow['date']) ? $dRow['date'] : 0;
                $dd_retail_net_sales = isset($dRow['dd_retail_net_sales']) ? $dRow['dd_retail_net_sales'] : 0;
                $prev_dd_retail_net_sales = isset($data_arr['previous_year'][$key]['dd_retail_net_sales']) ? $data_arr['previous_year'][$key]['dd_retail_net_sales'] : 0;
                $br_retail_net_sales = isset($dRow['br_retail_net_sales']) ? $dRow['br_retail_net_sales'] : 0;
                $prev_br_retail_net_sales = isset($data_arr['previous_year'][$key]['br_retail_net_sales']) ? $data_arr['previous_year'][$key]['br_retail_net_sales'] : 0;
                $trans_count_qty = isset($dRow['trans_count_qty']) ? $dRow['trans_count_qty'] : 0;
                $prev_trans_count_qty = isset($data_arr['previous_year'][$key]['trans_count_qty']) ? $data_arr['previous_year'][$key]['trans_count_qty'] : 0;

                $dd_retail_percentage = $prev_dd_retail_net_sales ? (($dd_retail_net_sales - $prev_dd_retail_net_sales) / $prev_dd_retail_net_sales) * 100 : 0;
                $br_retail_percentage = $prev_br_retail_net_sales ? (($br_retail_net_sales - $prev_br_retail_net_sales) / $prev_br_retail_net_sales) * 100 : 0;
                $total_year = $dd_retail_net_sales + $br_retail_net_sales + $trans_count_qty;
                $total_prev_year = $prev_dd_retail_net_sales + $prev_br_retail_net_sales + $prev_trans_count_qty;
                $total_percentage = $total_prev_year ? (($total_year - $total_prev_year) / $total_prev_year) * 100 : 0;
                $total_cust_percentage = $prev_trans_count_qty ? (($trans_count_qty - $prev_trans_count_qty) / $prev_trans_count_qty) * 100 : 0;

                $html .= '<tr class="detail-tr">';
                $html .= '<td>' . $week_date . '</td>';
                $html .= '<td>' . number_format($prev_dd_retail_net_sales, 2) . '</td>';
                $html .= '<td>' . number_format($dd_retail_net_sales, 2) . '</td>';
                $html .= '<td>' . $dd_retail_percentage . '</td>';
                $html .= '<td>' . $prev_br_retail_net_sales . '</td>';
                $html .= '<td>' . $br_retail_net_sales . '</td>';
                $html .= '<td>' . $br_retail_percentage . '</td>';
                $html .= '<td>' . $total_prev_year . '</td>';
                $html .= '<td>' . $total_year . '</td>';
                $html .= '<td>' . $total_percentage . '</td>';
                $html .= '<td>' . $prev_trans_count_qty . '</td>';
                $html .= '<td>' . $trans_count_qty . '</td>';
                $html .= '<td>' . $total_cust_percentage . '</td>';
                $html .= '</tr>';
            }
        } else {
            $html .= '<tr class="detail-tr"><td colspan="16"><center>No Record Found</center></td></tr>';
        }
        $html .= '</div>';
        $html .= '</td>';
        $html .= '</tr>';
        echo $html;
        exit;
    }

    public function sales_comp_weekly_data()
    {
        $postData = $this->input->post();
        $start_date = $this->input->post('start_date');
        $end_date = $this->input->post('end_date');
        $store_key = $this->input->post('store_key');
        $advanceStore = [];
        $sql = "SELECT * FROM `store_master` SM ORDER BY CASE WHEN SM.key = 3082 THEN 2 WHEN SM.status = 'A' THEN 1 ELSE 3 END ASC, SM.key ASC";
        $stores = $this->weekly_model->query_result($sql);
        foreach ($stores as $store) {
            $advanceStore[] = $store->key;
        }

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
            $advanceYear['85c1e9'] = date('Y', strtotime($start_date)) - 1;
            $advanceYear['5dade2'] = date('Y', strtotime($start_date));
        }
        $data['advanceYear'] = $advanceYear;
        $advance_data['advanceYear'] = $advanceYear;
        $dateInfo = getDateInfo(date('Y-m-d'));
        $start_date_info = $this->year_setting_model->getDateInfo($start_date);
        $end_date_info = $this->year_setting_model->getDateInfo($end_date);

        $where = '(';
        if (!empty($advanceYear)) {
            foreach ($advanceYear as $YRow) {
                $start_date = $this->year_setting_model->getPreviousYearSameDay($start_date_info['day_number_of_week'], $start_date_info['week_number_of_year'], $YRow);
                $end_date = $this->year_setting_model->getPreviousYearSameDay($end_date_info['day_number_of_week'], $end_date_info['week_number_of_year'], $YRow);
                $where .= '(cdate BETWEEN "' . $start_date . '" AND "' . $end_date . '") OR ';
            }
        }
        $where_cond = rtrim($where, " OR ");
        $where_cond .= ')';

        if ($store_key != 'Accumulate') {
            $where_cond .= " AND store_key = '" . $store_key . "'";
        }

        $sql = "SELECT * FROM master_pos_daily WHERE store_key IN (" . implode(',', $advanceStore) . ")
                            AND {$where_cond} ORDER BY store_key";
        $posData = $this->weekly_model->query_result($sql);

        $pos_data = [];
        foreach ($posData as $_posData) {
            $posInfo = json_decode($_posData->data);
            $dateInfo = $this->year_setting_model->getDateInfo($_posData->cdate);
            $day = 'day' . ($dateInfo['day_number_of_week'] - 1);
            foreach ($posInfo as $_posKey => $_posInfo) {
                if (!in_array($_posKey, array('dd_retail_net_sales', 'br_retail_net_sales', 'trans_count_qty'))) {
                    continue;
                }
                if (isset($pos_data[$_posData->store_key][$dateInfo['year']][$day][$_posKey])) {
                    $pos_data[$_posData->store_key][$dateInfo['year']][$day][$_posKey] += (float) $_posInfo;
                } else {
                    $pos_data[$_posData->store_key][$dateInfo['year']][$day][$_posKey] = (float) $_posInfo;
                }
                if (isset($pos_data['Accumulate'][$dateInfo['year']][$day][$_posKey])) {
                    $pos_data['Accumulate'][$dateInfo['year']][$day][$_posKey] += (float) $_posInfo;
                } else {
                    $pos_data['Accumulate'][$dateInfo['year']][$day][$_posKey] = (float) $_posInfo;
                }
            }
        }

        $html = '<tr class="detail-tr"><td colspan="16"><div class="slider"><table class="table table-striped table-bordered" style="padding-left:50px;"><tbody></tbody><thead>';
        $html .= '<tr><th> Date </th>';
        foreach ($advanceYear as $_advanceKey => $_advanceYear) {
            $html .= '<th style="background: "' . $_advanceKey . '"">DD ' . $_advanceYear . '</th>';
        }
        foreach ($advanceYear as $_advanceKey => $_advanceYear) {
            $html .= '<th style="background: "' . $_advanceKey . '"">BR ' . $_advanceYear . '</th>';
        }
        foreach ($advanceYear as $_advanceKey => $_advanceYear) {
            $html .= '<th style="background: "' . $_advanceKey . '"">Total ' . $_advanceYear . '</th>';
        }
        foreach ($advanceYear as $_advanceKey => $_advanceYear) {
            $html .= '<th style="background: "' . $_advanceKey . '"">Cust ' . $_advanceYear . '</th>';
        }
        $html .= '</tr>';
        $html .= '</thead>';
        $weeks = week_days($start_date);
        $total_dd_next = 0;
        $total_br_next = 0;
        $total_total_next = 0;
        $total_qty_next = 0;
        // dd_retail_net_sales
        foreach ($weeks as $weekKey => $weekRow) {
            $html .= '<tr class="detail-tr">';
            $html .= '<td>' . showInDateFormatWithMonthName($weekRow) . '</td>';
            foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                $html .= '<td style="background:#' . $_advanceKey . '">';
                $dd_next = isset($pos_data[$store_key][$_advanceYear][$weekKey]['dd_retail_net_sales']) ? $pos_data[$store_key][$_advanceYear][$weekKey]['dd_retail_net_sales'] : 0;
                $html .= showInDollar($dd_next);
                $total_dd_next += $dd_next;
                $html .= '</td>';
            }

            // br_retail_net_sales
            foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                $html .= '<td style="background:#' . $_advanceKey . '">';
                $br_next = isset($pos_data[$store_key][$_advanceYear][$weekKey]['br_retail_net_sales']) ? $pos_data[$store_key][$_advanceYear][$weekKey]['br_retail_net_sales'] : 0;
                $html .= showInDollar($br_next);
                $total_br_next += $br_next;
                $html .= '</td>';
            }
//          total
            foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                $html .= '<td style="background: #' . $_advanceKey . '">';
                $total_next = $br_next + $dd_next;
                $html .= showInDollar($total_next);
                $total_total_next += $total_next;
                $html .= '</td>';
            }
//                                    trans_count_qty
            foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                $html .= '<td style="background:#' . $_advanceKey . '">';
                $qty_next = isset($pos_data[$store_key][$_advanceYear][$weekKey]['trans_count_qty']) ? $pos_data[$store_key][$_advanceYear][$weekKey]['trans_count_qty'] : 0;
                $html .= showInDollar($qty_next);
                $total_qty_next += $qty_next;
                $html .= '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tr>';
        $html .= '</div>';
        $html .= '</td>';
        $html .= '</tr>';
        echo $html;
        exit;
    }

    protected function generateExcelForSalesComparision($data)
    {
        $yearlist = array_values($data['advanceYear']);

        $this->load->library('spreadsheet');
        $sheetNo = 0;
        foreach ($data['stores'] as $_stores) {
            if (in_array($_stores->key, $data['advanceStore'])) {
                $this->spreadsheet->createSheet();
                $this->spreadsheet->setActiveSheetIndex($sheetNo);
                $this->spreadsheet->getActiveSheet()->setTitle($_stores->key);
                $sheet = $this->spreadsheet->getActiveSheet();

                $sheet->mergeCells("A1:" . $sheet->getCellByColumnAndRow(3 + 4 * (2 * count($yearlist) - 1), 1)->getCoordinate());
                $sheet->setCellValue('A1', ($_stores->key == 'Accumulate' ? "Accumulate" : ucfirst($data['store_list'][$_stores->key]) . " #{$_stores->key}") . " Sales Comparison " . implode(' - ', $data['advanceYear']));
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $startingRowNo = $rowNo = 2;
                if ($rowNo == 2) {
                    $colNo = 1;

                    $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "Period");
                    $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "Week");
                    $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "Week Ending");

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "DD " . substr($_advanceYear, -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "DD % +/- " . substr($yearlist[$i], -2) . "~" . substr($yearlist[$i + 1], -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "BR " . substr($_advanceYear, -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "BR % +/- " . substr($yearlist[$i], -2) . "~" . substr($yearlist[$i + 1], -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "Total " . substr($_advanceYear, -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "Total % +/- " . substr($yearlist[$i], -2) . "~" . substr($yearlist[$i + 1], -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "Cust " . substr($_advanceYear, -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "Cust % +/- " . substr($yearlist[$i], -2) . "~" . substr($yearlist[$i + 1], -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
                    }

                    $startingRowNo++;
                    $rowNo++;
                }

                $periodNo = 0;
                $weekNo = 0;
                sort($data['weeks']);
                foreach ($data['weeks'] as $_weeks) {
                    $colNo = 1;

                    $totalCalc = [];

                    $week_number = $_weeks['week_number'];

                    if ($weekNo == 4 && $periodNo != 13) {
                        $weekNo = 1;
                    } else {
                        $weekNo++;
                    }
                    if ($weekNo == 1) {
                        $periodNo++;
                    }
                    $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, $weekNo == 1 ? "Period {$periodNo}" : '');

                    $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "Week " . trim($weekNo));

                    $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, date('j-M', strtotime($_weeks['end_of_week'])));

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $totalCalc[$_advanceYear][] = $sheet->getCellByColumnAndRow($colNo, $rowNo)->getCoordinate();
                        $dd = $data['pos_data'][$_stores->key][$_advanceYear][$week_number]['dd_retail_net_sales'] ?? 0;
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, $dd);
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $dd1 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist), $rowNo)->getCoordinate();
                        $dd2 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist) + 1, $rowNo)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=({$dd2}-{$dd1})/{$dd1}");
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $totalCalc[$_advanceYear][] = $sheet->getCellByColumnAndRow($colNo, $rowNo)->getCoordinate();
                        $br = $data['pos_data'][$_stores->key][$_advanceYear][$week_number]['br_retail_net_sales'] ?? 0;
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, $br);
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $br1 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist), $rowNo)->getCoordinate();
                        $br2 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist) + 1, $rowNo)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=({$br2}-{$br1})/{$br1}");
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=" . implode('+', $totalCalc[$_advanceYear]));
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $total1 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist), $rowNo)->getCoordinate();
                        $total2 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist) + 1, $rowNo)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=({$total2}-{$total1})/{$total1}");
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $cust = $data['pos_data'][$_stores->key][$_advanceYear][$week_number]['trans_count_qty'] ?? 0;
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, $cust);
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $cust1 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist), $rowNo)->getCoordinate();
                        $cust2 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist) + 1, $rowNo)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=({$cust2}-{$cust1})/{$cust1}");
                    }

                    $rowNo++;
                }

                if (true) {
                    $sheet->mergeCells($sheet->getCellByColumnAndRow(1, $rowNo)->getCoordinate() . ":" . $sheet->getCellByColumnAndRow(3, $rowNo)->getCoordinate());
                    $sheet->setCellValueByColumnAndRow(1, $rowNo, "Total");

                    $colNo = 4;

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $first = $sheet->getCellByColumnAndRow($colNo, $startingRowNo)->getCoordinate();
                        $last = $sheet->getCellByColumnAndRow($colNo, $rowNo - 1)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=SUM({$first}:{$last})");
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $dd1 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist), $rowNo)->getCoordinate();
                        $dd2 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist) + 1, $rowNo)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=({$dd2}-{$dd1})/{$dd1}");
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $first = $sheet->getCellByColumnAndRow($colNo, $startingRowNo)->getCoordinate();
                        $last = $sheet->getCellByColumnAndRow($colNo, $rowNo - 1)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=SUM({$first}:{$last})");
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $br1 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist), $rowNo)->getCoordinate();
                        $br2 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist) + 1, $rowNo)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=({$br2}-{$br1})/{$br1}");
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $first = $sheet->getCellByColumnAndRow($colNo, $startingRowNo)->getCoordinate();
                        $last = $sheet->getCellByColumnAndRow($colNo, $rowNo - 1)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=SUM({$first}:{$last})");
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $total1 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist), $rowNo)->getCoordinate();
                        $total2 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist) + 1, $rowNo)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=({$total2}-{$total1})/{$total1}");
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $first = $sheet->getCellByColumnAndRow($colNo, $startingRowNo)->getCoordinate();
                        $last = $sheet->getCellByColumnAndRow($colNo, $rowNo - 1)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=SUM({$first}:{$last})");
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $cust1 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist), $rowNo)->getCoordinate();
                        $cust2 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist) + 1, $rowNo)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=({$cust2}-{$cust1})/{$cust1}");
                    }

                    $rowNo++;
                }

                $styleArray = array(
                    'borders' => array(
                        'allBorders' => array(
                            'borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => array('argb' => '00000000'),
                        ),
                    ),
                );
                $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())->applyFromArray($styleArray);

                $styleArray = ['font' => ['bold' => true]];
                $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())->applyFromArray($styleArray);
                $styleArray = ['font' => ['bold' => false]];
                $sheet->getStyle('D3:' . $sheet->getHighestColumn() . ($sheet->getHighestRow() - 1))->applyFromArray($styleArray);

                foreach (range('A', $sheet->getHighestColumn()) as $columnId) {
                    $sheet->getColumnDimension($columnId)->setAutoSize(true);
                }

                $sheetNo++;
            }
        }

        $this->spreadsheet->setActiveSheetIndexByName('Worksheet');
        $sheetIndex = $this->spreadsheet->getActiveSheetIndex();
        $this->spreadsheet->removeSheetByIndex($sheetIndex);
        $this->spreadsheet->setActiveSheetIndex(0);

        $objWriter = IOFactory::createWriter($this->spreadsheet, 'Xlsx');

        ob_start();
        $objWriter->save('php://output');
        $xlsxData = ob_get_contents();
        ob_end_clean();

        $response = array(
            'file_content' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsxData),
            'file_name' => "sales_comparision_report.xlsx",
        );

        die(json_encode($response));
    }

    protected function generateExcelForLabor($data)
    {
        $yearlist = array_values($data['advanceYear']);

        $this->load->library('spreadsheet');
        $sheetNo = 0;
        foreach ($data['stores'] as $_stores) {
            if (in_array($_stores->key, $data['advanceStore'])) {
                $this->spreadsheet->createSheet();
                $this->spreadsheet->setActiveSheetIndex($sheetNo);
                $this->spreadsheet->getActiveSheet()->setTitle($_stores->key);
                $sheet = $this->spreadsheet->getActiveSheet();

                $sheet->mergeCells("A1:" . $sheet->getCellByColumnAndRow(3 + 3 * count($yearlist) + 4 * (2 * count($yearlist) - 1), 1)->getCoordinate());
                $sheet->setCellValue('A1', ($_stores->key == 'Accumulate' ? "Accumulate" : ucfirst($data['store_list'][$_stores->key]) . " #{$_stores->key}") . " Labor Percentage " . implode(' - ', $data['advanceYear']));
                $sheet->getStyle('A1')->getAlignment()->setHorizontal(PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $startingRowNo = $rowNo = 2;
                if ($rowNo == 2) {
                    $colNo = 1;

                    $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "Period");
                    $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "Week");
                    $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "Week Ending");

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "Gross Pay " . substr($_advanceYear, -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "Gross Pay % +/- " . substr($yearlist[$i], -2) . "~" . substr($yearlist[$i + 1], -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "Bonus " . substr($_advanceYear, -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "Covid " . substr($_advanceYear, -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "Tax " . substr($_advanceYear, -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "Total Pay " . substr($_advanceYear, -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "Total Pay % +/- " . substr($yearlist[$i], -2) . "~" . substr($yearlist[$i + 1], -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "Net Sales " . substr($_advanceYear, -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "Net Sales % +/- " . substr($yearlist[$i], -2) . "~" . substr($yearlist[$i + 1], -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "Labor % " . substr($_advanceYear, -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1);
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $sheet->setCellValueByColumnAndRow($colNo, $rowNo, "Labor % +/- " . substr($yearlist[$i], -2) . "~" . substr($yearlist[$i + 1], -2));
                        $sheet->getStyle($sheet->getCellByColumnAndRow($colNo++, $rowNo)->getColumn())->getNumberFormat()->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
                    }

                    $startingRowNo++;
                    $rowNo++;
                }

                $periodNo = 0;
                $weekNo = 0;
                sort($data['weeks']);
                foreach ($data['weeks'] as $_weeks) {
                    $colNo = 1;

                    $totalCalc = [];

                    $week_number = $_weeks['week_number'];

                    if ($weekNo == 4 && $periodNo != 13) {
                        $weekNo = 1;
                    } else {
                        $weekNo++;
                    }
                    if ($weekNo == 1) {
                        $periodNo++;
                    }
                    $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, $weekNo == 1 ? "Period {$periodNo}" : '');

                    $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "Week " . trim($weekNo));

                    $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, date('j-M', strtotime($_weeks['end_of_week'])));

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $totalCalc[$_advanceYear]['gross_pay'] = $sheet->getCellByColumnAndRow($colNo, $rowNo)->getCoordinate();
                        $g_pay = $data['labordara_data'][$_stores->key][$_advanceYear][$week_number]['gross_pay'] ?? 0;
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, $g_pay);
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $g_pay1 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist), $rowNo)->getCoordinate();
                        $g_pay2 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist) + 1, $rowNo)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=({$g_pay2}-{$g_pay1})/{$g_pay1}");
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $totalCalc[$_advanceYear]['bonus'] = $sheet->getCellByColumnAndRow($colNo, $rowNo)->getCoordinate();
                        $bonus = $data['labordara_data'][$_stores->key][$_advanceYear][$week_number]['bonus'] ?? 0;
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, $bonus);
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $totalCalc[$_advanceYear]['covid'] = $sheet->getCellByColumnAndRow($colNo, $rowNo)->getCoordinate();
                        $covid = $data['labordara_data'][$_stores->key][$_advanceYear][$week_number]['covid'] ?? 0;
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, $covid);
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $totalCalc[$_advanceYear]['tax_amount'] = $sheet->getCellByColumnAndRow($colNo, $rowNo)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=({$totalCalc[$_advanceYear]['gross_pay']}+{$totalCalc[$_advanceYear]['bonus']}-{$totalCalc[$_advanceYear]['covid']})*7.65%");
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $totalCalc[$_advanceYear]['total_pay'] = $sheet->getCellByColumnAndRow($colNo, $rowNo)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=SUM(({$totalCalc[$_advanceYear]['gross_pay']}-{$totalCalc[$_advanceYear]['covid']})+{$totalCalc[$_advanceYear]['bonus']}+{$totalCalc[$_advanceYear]['tax_amount']})");
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $total_pay1 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist), $rowNo)->getCoordinate();
                        $total_pay2 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist) + 1, $rowNo)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=({$total_pay2}-{$total_pay1})/{$total_pay1}");
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $totalCalc[$_advanceYear]['net_sales'] = $sheet->getCellByColumnAndRow($colNo, $rowNo)->getCoordinate();
                        $net_sales = $data['labordara_data'][$_stores->key][$_advanceYear][$week_number]['net_sales'] ?? 0;
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, $net_sales);
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $net_sales1 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist), $rowNo)->getCoordinate();
                        $net_sales2 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist) + 1, $rowNo)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=({$net_sales2}-{$net_sales1})/{$net_sales1}");
                    }

                    foreach ($data['advanceYear'] as $_advanceYear) {
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "={$totalCalc[$_advanceYear]['total_pay']}/{$totalCalc[$_advanceYear]['net_sales']}");
                    }
                    for ($i = 0; $i < sizeof($yearlist) - 1; $i++) {
                        $labor_percentage1 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist), $rowNo)->getCoordinate();
                        $labor_percentage2 = $sheet->getCellByColumnAndRow($colNo - sizeof($yearlist) + 1, $rowNo)->getCoordinate();
                        $sheet->setCellValueByColumnAndRow($colNo++, $rowNo, "=({$labor_percentage2}-{$labor_percentage1})/{$labor_percentage1}");
                    }

                    $rowNo++;
                }

                $styleArray = array(
                    'borders' => array(
                        'allBorders' => array(
                            'borderStyle' => PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => array('argb' => '00000000'),
                        ),
                    ),
                );
                $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())->applyFromArray($styleArray);

                $styleArray = ['font' => ['bold' => true]];
                $sheet->getStyle('A1:' . $sheet->getHighestColumn() . $sheet->getHighestRow())->applyFromArray($styleArray);
                $styleArray = ['font' => ['bold' => false]];
                $sheet->getStyle('D3:' . $sheet->getHighestColumn() . ($sheet->getHighestRow()))->applyFromArray($styleArray);

                foreach (range('A', $sheet->getHighestColumn()) as $columnId) {
                    $sheet->getColumnDimension($columnId)->setAutoSize(true);
                }

                $sheetNo++;
            }
        }

        $this->spreadsheet->setActiveSheetIndexByName('Worksheet');
        $sheetIndex = $this->spreadsheet->getActiveSheetIndex();
        $this->spreadsheet->removeSheetByIndex($sheetIndex);
        $this->spreadsheet->setActiveSheetIndex(0);

        $objWriter = IOFactory::createWriter($this->spreadsheet, 'Xlsx');

        ob_start();
        $objWriter->save('php://output');
        $xlsxData = ob_get_contents();
        ob_end_clean();

        $response = array(
            'file_content' => "data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;base64," . base64_encode($xlsxData),
            'file_name' => "labor_summary.xlsx",
        );

        die(json_encode($response));
    }

    public function isExcludeCalcData($excludeCalcData, $store_key, $date)
    {
        $excludeCalcData = $excludeCalcData[$store_key] ?? [];
        foreach ($excludeCalcData as $row) {
            if (strtotime($row['from_date']) <= strtotime($date) && ($row['to_date'] == null ? true : strtotime($date) <= strtotime($row['to_date']))) {
                return true;
            }
        }
        return false;
    }
}
