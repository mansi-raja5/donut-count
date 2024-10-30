<?php

Class Common_model extends CI_Model {

    public function get_dates($month, $year, $format) {
        $numbers        = array('1', '2', '3', '4', '5', '6', '7', '8', '9');
        $datesArray     = array();
        $num_of_days    = date('t', mktime(0, 0, 0, $month, 1, $year));
        if (in_array($month, $numbers))
            $month = strlen($month) < 2 ? '0' . $month : $month;
        for ($i = 1; $i <= $num_of_days; $i++) {
            if (in_array($i, $numbers))
                $i = '0' . $i;
            if ($format == "month"):
                $datesArray[] = $i . '-' . $month . '-' . $year;
            else:
                $datesArray[] = $year . '-' . $month . '-' . $i;
            endif;
        }
        return $datesArray;
    }

    //get dynamc column for both paidout and masterpos
    function getDynamiccolumn($table, $key, $columnname) {
        $query = $this->db->select("*")->get_where($table, array(
            'is_active' => 1
        ));
        $return = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $return[$row[$key]] = $row[$columnname];
            }
        }
        return $return;
    }

    function getDonutDynamiccolumn($table) {
        $query = $this->db->select("*")->get_where($table, array(
            'is_active' => 1
        ));
        $return = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $return[] = $row["key_name"];
            }
        }
        return $return;
    }

    function weeks_in_month($month, $year) {
        // Start of month
        $start = mktime(0, 0, 0, $month, 1, $year);
        // End of month
        $end = mktime(0, 0, 0, $month, date('t', $start), $year);
        // Start week
        $start_week = date('W', $start);
        // End week
        $end_week = date('W', $end);

        if ($end_week < $start_week) { // Month wraps
            return ((52 + $end_week) - $start_week) + 1;
        }

        return ($end_week - $start_week) + 1;
    }

    function getFirstDate($year, $month, $week) {

        $thisWeek = 1;

        for ($i = 1; $i < $week; $i++) {
            $thisWeek = $thisWeek + 7;
        }

        $currentDay = date('Y-m-d', mktime(0, 0, 0, $month, $thisWeek, $year));

        $monday = strtotime('sunday this week', strtotime($currentDay));
        $sunday = strtotime('sunday this week', strtotime($currentDay));

        $weekStart = date('Y-m-d', $monday);

        return $weekStart;
    }

    function getEndDate($year, $month, $week) {

        $thisWeek = 1;

        for ($i = 1; $i < $week; $i++) {
            $thisWeek = $thisWeek + 7;
        }

        $currentDay = date('Y-m-d', mktime(0, 0, 0, $month, $thisWeek, $year));

        $monday = strtotime('monday this week', strtotime($currentDay));
        $sunday = strtotime('saturday this week', strtotime($currentDay));
        $weekEnd = date('Y-m-d', $sunday);

        return $weekEnd;
    }

    //common getdata method
    public function getData($data, $table, $is_view_paidout_report = NULL) {
        $list = $this->get_dates($data['month'], $data['year'], "year");

        if ($is_view_paidout_report == 1) {
            $query = $this->db->select("paid_out_recap.*,DATE_FORMAT(paid_out_recap.cdate, '%d-%m-%Y') as cdate, count(dailysales_attachments_upload.id) as total_attachments")->join('dailysales_attachments_upload', 'dailysales_attachments_upload.store_key = paid_out_recap.store_key AND dailysales_attachments_upload.cdate = paid_out_recap.cdate', 'LEFT')->where_in('paid_out_recap.cdate', $list)->group_by('paid_out_recap.id')->get($table);

//            $this->db->join('dailysales_attachments_upload', 'dailysales_attachments_upload.store_key = paid_out_recap.store_key AND dailysales_attachments_upload.cdate = paid_out_recap.cdate', 'LEFT');
        } else {
            $query = $this->db->select("*,DATE_FORMAT(cdate, '%d-%m-%Y') as cdate")->where('store_key', $data['store_key'])->where_in('cdate', $list)->order_by('cdate', 'ASC')->get($table);
        }

        $return = array();
        if ($query->num_rows() > 0) {
            $return = $query->result_array();
        }
        return $return;
    }

    public function getPosMonthlyWeeklyData($data, $table) {
        $sql = "SELECT *,'".$table."' as type FROM $table
                WHERE store_key = {$data['store_key']} AND ((month(start_date) = {$data['month']} AND year(start_date) = {$data['year']}) OR
                (month(end_date) = {$data['month']} AND year(end_date) = {$data['year']})) ";
        $query = $this->db->query($sql);
        return $query->result_array();
    }    

    public function getDatewiseData($data, $table, $is_view_report = NULL) {
        $list = $this->get_dates($data['month'], $data['year'], "year");
        if ($is_view_report == 1) {
            $query = $this->db->select("*,DATE_FORMAT(cdate, '%d-%m-%Y') as cdate")->where_in('cdate', $list)->get($table);
        } else {
            $query = $this->db->select("*,DATE_FORMAT(cdate, '%d-%m-%Y') as cdate")->where('store_key', $data['store_key'])->where_in('cdate', $list)->get($table);
        }
        $return = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                if ($is_view_report == 1) {
                    $cdate = str_replace("-", "", date("d-m-Y", strtotime($row['cdate'])));
                    $return[$row['store_key']][$cdate] = $row;
                } else {
                    $return[date("d-m-Y", strtotime($row['cdate']))] = $row;
                }
            }
        }

        return $return;
    }

    //------------------
    //PAID OUT GRID DATA
    //------------------
    public function getPaidAmount($data, $is_view_paidout_report = NULL) {
        $return = array();
        $list = $this->get_dates($data['month'], $data['year'], "year");
        if ($is_view_paidout_report == 1) {
            $query = $this->db->select("cdate,data,store_key")->where_in('cdate', $list)->get("master_pos_daily");
        } else {
            $query = $this->db->select("cdate,data,store_key")->where('store_key', $data['store_key'])->where_in('cdate', $list)->get("master_pos_daily");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $paid_out = "";
                $jsondata = json_decode($row['data']);
                $cdate = str_replace("-", "", date("d-m-Y", strtotime($row['cdate'])));
                if (isset($jsondata->paid_out)):
                    if ($is_view_paidout_report == 1):
                        $return[$row['store_key']][$cdate] = $jsondata->paid_out;
                    else:
                        $return[date("d-m-Y", strtotime($row['cdate']))] = $jsondata->paid_out;
                    endif;

                else:
                    if ($is_view_paidout_report == 1):
                        $return[$row['store_key']][$cdate] = 0;
                    else:
                        $return[date("d-m-Y", strtotime($row['cdate']))] = 0;
                    endif;

                endif;
            }
        }
        return $return;
    }

    public function getInvoiceUpload($data, $is_view_paidout_report = NULL) {
        $return = array();
        $list = $this->get_dates($data['month'], $data['year'], "year");
        $month_arr = array("January", "February", "March", "April", "May", "Jun", "July", "August", "September", "October", "November", "December");
        $dynamic_column = $this->getDynamiccolumn("dynamic_dailysales_column", "key_name", "column_name");
        if ($is_view_paidout_report == 1) {
            $query = $this->db->select("*")->where(array("year" => $data['year'], "month" => $month_arr[$data['month'] - 1]))->where_in('cdate', $list)->get("dailysales_attachments_upload");
        } else {
            $query = $this->db->select("*")->where(array("store_key" => $data['store_key'], "year" => $data['year'], "month" => $month_arr[$data['month'] - 1]))->where_in('cdate', $list)->get("dailysales_attachments_upload");
        }

        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                if ($is_view_paidout_report == 1):
                    $cdate = str_replace("-", "", date("d-m-Y", strtotime($row['cdate'])));
                    $return[$row['store_key']][$cdate][] = $row['dynamic_column_id'];
                else:
                    $return[date("d-m-Y", strtotime($row['cdate']))][] = $row['dynamic_column_id'];
                endif;
            }
        }
        return $return;
    }

    function adddailysales($data, $single_row = 0) {

        $store_key = $data['store_key'];
        $year = $data['year'];
        $month = $data['month'];
        $insert_array = array();
        $dynamic_column = $this->getDynamiccolumn("dynamic_dailysales_column", "key_name", "column_name");
        $month_arr = array("January", "February", "March", "April", "May", "Jun", "July", "August", "September", "October", "November", "December");
        if($single_row == 1){
            $insert_array['store_key'] = $store_key;
            $insert_array['year'] = $year;
            $insert_array['month'] = $month_arr[$month - 1];
            $insert_array['day'] = date('l', strtotime($data['cdate']));
            $insert_array['cdate'] = date("Y-m-d H:i:s", strtotime($data['cdate']));
            foreach ($dynamic_column as $key => $value) {
                $insert_array[$key] = $data[$key];
            }
            $insert_array['total'] = $data['total'];
            $insert_array['is_lock'] = 0;
            $this->adddata('paid_out_recap', $insert_array);
        }else{
            $counter = sizeof($data['cdate']);
         for ($i = 0; $i < $counter; $i++) {
            $insert_array['store_key'] = $store_key;
            $insert_array['year'] = $year;
            $insert_array['month'] = $month_arr[$month - 1];
            $insert_array['day'] = $data['day'][$i];
            $insert_array['cdate'] = date("Y-m-d H:i:s", strtotime($data['cdate'][$i]));
            foreach ($dynamic_column as $key => $value) {
                $insert_array[$key] = $data[$key][$i];
            }
            $insert_array['total'] = $data['total'][$i];
            $insert_array['is_lock'] = 0;
            $this->adddata('paid_out_recap', $insert_array);
        }
        }

        return true;
    }

    public function adddata($table, $insert_array) {
        // check if entry is there date and store
        $query = $this->db->get_where($table, array(
            'cdate' => date("Y-m-d", strtotime($insert_array['cdate'])),
            'store_key' => $insert_array['store_key'],
        ));
        $count = $query->num_rows();
        $row = $query->row();
        if ($count === 0) {
            $this->db->insert($table, $insert_array);
        } else {
            $this->db->where('id', $row->id);
            $this->db->update($table, $insert_array);
         }
        return true;
    }

    public function updateData($table, $id, $data) {
        $this->db->where('id', $id);
        $this->db->update($table, $data);
        return true;
    }
    
    function adduploadattachment($data) {
        $insert_array = array();
        $guid = 0;
        $month_arr = array("January", "February", "March", "April", "May", "Jun", "July", "August", "September", "October", "November", "December");
        $numbers = array('1', '2', '3', '4', '5', '6', '7', '8', '9');
        if (in_array($data['month'], $numbers))
            $month = '0' . $data['month'];
        else
            $month = $data['month'];
        if (!empty($_FILES["files"]["name"])) {
            $insert_array['store_key'] = $data['store_key'];
            $insert_array['month'] = $month_arr[$data['month'] - 1];
            $insert_array['year'] = $data['year'];
            $insert_array['dynamic_column_id'] = $data['dynamic_column'];
            $insert_array['cdate'] = date("Y-m-d", strtotime($data['hd_cdate']));
            $onlydate = date("d", strtotime($data['hd_cdate']));
            // File path config
            $fileName = basename($_FILES["files"]["name"]);
            $targetPath = FCPATH . "/files_upload/daily_sales_upload/" . $data['store_key'] . "/" . $data['year'] . "/" . $data['month'] . "/" . date("Ymd", strtotime($data['hd_cdate'])) . "/invoice/";
            $uploaded_url = "/files_upload/daily_sales_upload/" . $data['store_key'] . "/" . $data['year'] . "/" . $data['month'] . "/" . date("Ymd", strtotime($data['hd_cdate'])) . "/invoice/";

            if (!file_exists($targetPath)) {
                mkdir($targetPath, 0777, true);
            }
            $tmp = explode('.', $_FILES["files"]["name"]);
            $file_ext = end($tmp);
            $data['cdate'] = date("Y-m-d", strtotime($data['hd_cdate']));
            $uploaded_file_name = $this->checkuploadsetting($data['dynamic_column']);
            $existcount = sizeof($this->getuploadattachment($data));
            if ($uploaded_file_name != "") {
                if ($existcount > 0):
                    $uploaded_file_name = $uploaded_file_name . "_" . $onlydate . "_" . $month . "_" . $data['year'] . "_" . $data['store_key'] . "_" . $existcount . "." . $file_ext;
                else:
                    $uploaded_file_name = $uploaded_file_name . "_" . $onlydate . "_" . $month . "_" . $data['year'] . "_" . $data['store_key'] . "." . $file_ext;
                endif;
            }else {
                $uploaded_file_name = $_FILES['files']['name'];
            }
            $targetFilePath = $targetPath . $uploaded_file_name;
            if (move_uploaded_file($_FILES["files"]["tmp_name"], $targetFilePath)) {
                $uploadedFile = $fileName;
                $insert_array['original_file_name'] = $fileName;
                $insert_array['uploaded_file_name'] = $uploaded_file_name;
                $insert_array['uploaded_url'] = $uploaded_url;

                $this->db->insert('dailysales_attachments_upload', $insert_array);
                $guid = $this->db->insert_id();
            }
        }

        return $guid;
    }

    function checkuploadsetting($dynamic_column) {
        $query = $this->db->select("*")->where('description', $dynamic_column)->get("attachment_name_setting");
        $return = array();
        $invoice_name = "";
        if ($query->num_rows() > 0) {
            $return = $query->result_array();
            $invoice_name = $return[0]['invoice_name'];
        }
        return $invoice_name;
    }

    //---------paidout-------------
    //Donut count
    function getDonutCount($data, $is_view_report = 0) {
        $fromDate = isset($data['from_date']) ? date('Y-m-d',strtotime($data['from_date'])) : "";
        $toDate   = isset($data['to_date']) ? date('Y-m-d',strtotime($data['to_date'])) : "";
        $list     = isset($data['list']) ? $data['list'] : [];

        $donutCountData = $baseWeek = $yearWiseDates = [];
        $CI =& get_instance();
        $CI->load->model('year_setting_model');
        //echo 555;die;
        /*if($fromDate && $toDate)
        {
            echo 777;die;
            $baseDateYear  = date('Y',strtotime($toDate));
            $yearSettings  = $CI->year_setting_model->getAllWeeksOfYear($baseDateYear);
            $baseYearWeeks = count($yearSettings);
            if(strtotime($yearSettings[0]['start_of_week']) > strtotime($toDate)){
                $baseDateYear = date('Y',strtotime($toDate)) - 1;
                $yearSettings = $CI->year_setting_model->getAllWeeksOfYear($baseDateYear);
            }
            // $weekNumber = array_search($toDate, array_column($yearSettings, 'end_of_week'));
            // echo '<pre>';print_r($data['advanceYear']);die;
            if (strtotime($fromDate) != strtotime($toDate))
            {
                //week loops
                foreach ($yearSettings as $weekNumber => $_yearSettings) {
                    $baseDateDayNumber  = 0;
                    if(($data['is_full_year'] ?? false) || ((strtotime($_yearSettings['start_of_week']) >= strtotime($fromDate))
                        && (strtotime($_yearSettings['end_of_week']) <= strtotime($toDate))))
                    {
                        //week days loop
                        while(end($baseWeek) < $_yearSettings['end_of_week'] || count($baseWeek) == 0) {
                            $current_date   = date('Y-m-d', strtotime($_yearSettings['start_of_week'].' -1 day')); //to get the all 7 days in 1 loop subtracting 1 day
                            $current_date   = (count($baseWeek) == 0) ? $current_date : end($baseWeek);
                            $baseWeek[]     = $yearWiseDates[$baseDateYear][$weekNumber][] = date('Y-m-d', strtotime($current_date.' +1 day'));

                            foreach ($data['advanceYear'] as $_advanceKey => $_advanceYear) {
                                if($_advanceYear == $baseDateYear)
                                    continue;
                                $yearWeeks     = $CI->year_setting_model->getNumberOfWeeks($_advanceYear);
                                $yearSettings  = $CI->year_setting_model->getAllWeeksOfYear($_advanceYear, $baseYearWeeks != $yearWeeks);
                                $weekStartDate = isset($yearSettings[$weekNumber]) ? $yearSettings[$weekNumber]['start_of_week'] : '';
                                if($weekStartDate) {
                                    $calcualtedDate = date('Y-m-d', strtotime($weekStartDate.' +'.($baseDateDayNumber).' day'));
                                    $yearWiseDates[$_advanceYear][$weekNumber][] = $list[] = $calcualtedDate;
                                }
                            }
                            ++$baseDateDayNumber;
                        }
                    }
                }
            }
            else
            {
                $baseWeek[]         = date('Y-m-d',strtotime($fromDate));
                $weekNumber         = 0;
                $baseDateDayNumber  = 0;
                $yearSettings   = $CI->year_setting_model->getAllWeeksOfYear(date('Y',strtotime($fromDate)));
                foreach ($yearSettings as $weekKey => $_yearSettings) {
                    if((strtotime($_yearSettings['start_of_week']) <= strtotime($fromDate))
                        && (strtotime($_yearSettings['end_of_week']) >= strtotime($fromDate)))
                    {
                        $weekNumber         = $weekKey;
                        $baseDateDayNumber  = (strtotime($fromDate)-strtotime($_yearSettings['start_of_week']))/86400;
                        break;
                    }
                }
                $yearWiseDates[$baseDateYear][$weekNumber][] = date('Y-m-d',strtotime($fromDate));
                foreach ($data['advanceYear'] as $_advanceKey => $_advanceYear) {
                    if($_advanceYear == $baseDateYear)
                        continue;
                    $yearSettings  = $CI->year_setting_model->getAllWeeksOfYear($_advanceYear);
                    $weekStartDate = isset($yearSettings[$weekNumber]) ? $yearSettings[$weekNumber]['start_of_week'] : '';
                    if($weekStartDate){
                        $calcualtedDate = date('Y-m-d', strtotime($weekStartDate.' +'.($baseDateDayNumber).' day'));
                        $yearWiseDates[$_advanceYear][$weekNumber][] = $list[] = $calcualtedDate;
                    }
                }
            }
            $list = array_merge($baseWeek,$list);
        }
        if(count($list)) //case when special day is selected
        {
            foreach ($list as $_listDate) {
                $year                   = date('Y',strtotime($_listDate));
                $yearWiseDates[$year][] = $_listDate;
            }
        }
        else //This will be used in common tab -> donut count*/
        {
            //echo 444;die;
            $dates  = $this->get_dates($data['month'],  $data['year'], "year");
            $list   = array_merge($list,$dates);
        }
        if ($is_view_report == 1):
            $query = $this->db->select("store_key, `daily_date`, MAX(IF(donut_type = 'Donuts',total_order,0)) as donuts_order, MAX(IF(donut_type = 'Donuts',total_sale,0)) as donuts_sale, MAX(IF(donut_type = 'Fancy',total_order,0)) as fancy_order, MAX(IF(donut_type = 'Fancy',total_sale,0)) as fancy_sale, MAX(IF(donut_type = 'Munkins',total_order,0)) as munkins_order, MAX(IF(donut_type = 'Munkins',total_sale,0)) as munkins_sale")->where_in('daily_date', $list)->group_by(array("store_key", "daily_date"))->get("donut_count");
        else:
            $query = $this->db->select("store_key, `daily_date`,
                                    MAX(IF(donut_type = 'Donuts',total_order,0)) as donuts_order,
                                    MAX(IF(donut_type = 'Donuts',total_sale,0)) as donuts_sale,
                                    MAX(IF(donut_type = 'Fancy',total_order,0)) as fancy_order,
                                    MAX(IF(donut_type = 'Fancy',total_sale,0)) as fancy_sale,
                                    MAX(IF(donut_type = 'Munkins',total_order,0)) as munkins_order,
                                    MAX(IF(donut_type = 'Munkins',total_sale,0)) as munkins_sale")
                    ->where('store_key', $data['store_key'])
                    ->where_in('daily_date', $list)
                    ->group_by(array("store_key", "daily_date"))
                    ->get("donut_count");
        endif;
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                if ($is_view_report == 1):
                    $cdate = str_replace("-", "", date("d-m-Y", strtotime($row['daily_date'])));
                    $year = date("Y", strtotime($row['daily_date']));
                    $donutCountData[$row['store_key']][$year][$cdate] = $row;
                else:
                    $donutCountData[date("d-m-Y", strtotime($row['daily_date']))] = $row;
                endif;
            }
        }
        $donutCount['yearWiseDates']    = isset($yearWiseDates) ? $yearWiseDates : [];
        $donutCount['data']             = $donutCountData;
        $donutCount['baseDateYear']     = isset($baseDateYear) ? $baseDateYear : [];
        return $donutCount;
    }

    //Payroll
    function getPayroll($data, $alldates) {
        $return = array();
        $start_date = array_column($alldates, 'start_of_week');
        $end_date = array_column($alldates, 'end_of_week');
        $query = $this->db->select("*")->where('store_key', $data['store_key'])->where_in(array('start_date' => $start_date))->get("master_payroll");
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $return[date("d-m-Y", strtotime($row['end_date']))] = $row;
            }
        }
        return $return;
    }

    function getStartDate($data) {
        $start_date = array();
        $no_of_week = $this->weeks_in_month($data['month'], $data['year']);
        for ($i = 1; $i <= $no_of_week; $i++) {
            $start_date[] = $this->getFirstDate($data['year'], $data['month'], $i);
        }
        return $start_date;
    }

    function getAllEndDate($data) {
        $end_date = array();
        $no_of_week = $this->weeks_in_month($data['month'], $data['year']);
        for ($i = 1; $i <= $no_of_week; $i++) {
            $end_date[] = $this->getEndDate($data['year'], $data['month'], $i);
        }
        return $end_date;
    }

    //---payroll end

    function addactualdeposit($data, $actual_deposit) {
        if (sizeof($actual_deposit) > 0):
            foreach ($actual_deposit as $key => $value):
                $insert_array['actual_bank_deposit'] = $value;
                $query = $this->db->get_where("monthly_recap", array(
                    'cdate' => date("Y-m-d", strtotime($key)),
                    'store_key' => $data['store_key'],
                ));

                $count = $query->num_rows();
                $row = $query->row();
                if ($count > 0) {
                    $this->db->where('id', $row->id);
                    $this->db->update("monthly_recap", $insert_array);
                    $guid = $this->db->insert_id();
                }
            endforeach;
        endif;
        return true;
    }

    function getuploadattachment($data) {
        $return = array();
        $query = $this->db->get_where("dailysales_attachments_upload", array(
            'cdate' => date("Y-m-d", strtotime($data['cdate'])),
            'dynamic_column_id' => $data['dynamic_column'],
            'store_key' => $data['store_key'],
        ));

        $count = $query->num_rows();
        if ($count > 0) {
            $return = $query->result();
        }
        return $return;
    }

    function deleteattachment($data) {
        $return = array();
        $query = $this->db->get_where("dailysales_attachments_upload", array(
            'id' => $data['id']
        ));

        $count = $query->num_rows();
        if ($count > 0) {
            $return = $query->result_array();
            $filepath = FCPATH . $return[0]['uploaded_url'];
            unlink($filepath);
            $query = $this->db->where('id', $data['id'])->delete("dailysales_attachments_upload");
        }

        return $this->db->affected_rows();
    }

    function Edit($where_arr, $data, $tbl_name) {
        $this->db->where($where_arr);
        $this->db->update($tbl_name, $data);
        return true;
    }

}
