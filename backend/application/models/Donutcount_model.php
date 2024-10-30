<?php
Class Donutcount_model extends CI_Model {

    function add_if_not_exist($batchData){
        if(!empty($batchData)){
            foreach ($batchData as $_batchData) {
                $this->db->where('store_key',$_batchData['store_key']);
                $this->db->where('donut_type',$_batchData['donut_type']);
                $this->db->where('daily_date',$_batchData['daily_date']);
                $q = $this->db->get('donut_count');

                if(!$q->num_rows() && $_batchData['total_order'] && $_batchData['total_sale'])
                {
                    $this->db->insert('donut_count',$_batchData);
                }
            }
            return true;
        }
    }

    public function getDonutCounts()
    {
        $query = $this->db->get('donut_count');
        return $query->result();  // Return the query result as an array of objects
    }

    public function getDonutCountsGroupedByStoreKey($storeKey = null, $type = null, $days = null, $timeRange = null, $month = null, $date = null, $year = null,  $weekEnding = null, $numYears = null, $numMonths = null,  $startDate = null, $endDate = null, $yearsrange = null, $storeKeys = null)
    {
        $storeKeyArray = array_map('trim', explode(',', $storeKey));
        if ($storeKey !== null && !empty($storeKeyArray)) {
            $this->db->where_in('donut_count.store_key', $storeKeyArray);
        }

        $storeKeysArray = array_map('trim', explode(',', $storeKeys));
        if ($storeKeys !== null && !empty($storeKeysArray)) {
            $this->db->where_in('donut_count.store_key', $storeKeysArray);
        }

        if ($type !== null) {
            $this->db->where('LOWER(donut_type)', strtolower($type));
        }

        $yearArray = array_map('trim', explode(',', $yearsrange));
        if ($yearsrange !== null && !empty($yearArray)) {
            $this->db->where_in('YEAR(daily_date)', $yearArray);
        }

        //last n days month and years

        if ($days !== null) {
            $this->db->where('DATE(daily_date) >=', date('Y-m-d', strtotime("-$days days")));
        }

        if ($numYears !== null) {
            $yearsAgo = date('Y-m-d', strtotime("-$numYears years"));
            $this->db->where('DATE(daily_date) >=', $yearsAgo);
        }

        if ($numMonths !== null) {
            $monthsAgo = date('Y-m-d', strtotime("-$numMonths months"));
            $this->db->where('DATE(daily_date) >=', $monthsAgo);
        }

        if ($timeRange !== null) {
            switch ($timeRange) {
                case 'last_month':
                    $this->db->where('DATE(daily_date) >=', date('Y-m-01', strtotime('first day of last month')));
                    $this->db->where('DATE(daily_date) <=', date('Y-m-t', strtotime('last day of last month')));
                    break;
                case 'last_year':
                    $this->db->where('DATE(daily_date) >=', date('Y-01-01', strtotime('-1 year')));
                    $this->db->where('DATE(daily_date) <=', date('Y-12-31', strtotime('-1 year')));
                    break;
            }
        }

        //Specific MONTH - format must be Y-m
        if ($month !== null && $yearsrange !== null) {
            $monthParts = explode('-', $month);
            $month = $monthParts[1];

            $this->db->group_start();
            $this->db->where('MONTH(daily_date)', $month);
            $this->db->or_where('DATE_FORMAT(daily_date, "%Y-%m") =', $month);
            $this->db->group_end();
        } else if ($month !== null) {
            $this->db->where('DATE_FORMAT(daily_date, "%Y-%m") =', $month);
        }

        //Specific date - format must be Y-m-d
        if ($date !== null && $yearsrange !== null) {
            $dateParts = explode('-', $date);
            $month = $dateParts[1];
            $day = $dateParts[2];
            $this->db->group_start();
            $this->db->where('MONTH(daily_date)', $month);
            $this->db->where('DAY(daily_date)', $day);
            $this->db->or_where('DATE(daily_date)', $date);
            $this->db->group_end();
        } else if ($date !== null) {
            $this->db->where('DATE(daily_date)', $date);
        }

        //specific year
        if ($year !== null) {
            $this->db->where('YEAR(daily_date)', $year);
        }

        // Assuming $weekEnding is in 'Y-m-d' format
        if ($weekEnding !== null && $yearsrange !== null) {
            $weekStart = date('Y-m-d', strtotime($weekEnding . ' -6 days'));
            $weekStartMonth = date('m', strtotime($weekStart));
            $weekStartDay = date('d', strtotime($weekStart));
            $weekEndMonth = date('m', strtotime($weekEnding));
            $weekEndDay = date('d', strtotime($weekEnding));

            // Condition for the specific week
            $this->db->group_start();
            $this->db->where('MONTH(daily_date) >=', $weekStartMonth);
            $this->db->where('DAY(daily_date) >=', $weekStartDay);

            $this->db->where('MONTH(daily_date) <=', $weekEndMonth);
            $this->db->where('DAY(daily_date) <=', $weekEndDay);
            $this->db->group_end();
        } else if ($weekEnding !== null) {
            $weekStart = date('Y-m-d', strtotime($weekEnding . ' -6 days'));
            $this->db->where('DATE(daily_date) >=', $weekStart);
            $this->db->where('DATE(daily_date) <=', $weekEnding);
        }

        if ($startDate !== null && $endDate !== null && $yearsrange !== null) {
            $startMonth = date('m', strtotime($startDate));
            $startDay = date('d', strtotime($startDate));
            $endMonth = date('m', strtotime($endDate));
            $endDay = date('d', strtotime($endDate));

            // Condition for the specific week
            $this->db->group_start();
            $this->db->where('MONTH(daily_date) >=', $startMonth);
            $this->db->where('DAY(daily_date) >=', $startDay);

            $this->db->where('MONTH(daily_date) <=', $endMonth);
            $this->db->where('DAY(daily_date) <=', $endDay);
            $this->db->group_end();
        } else if ($startDate !== null && $endDate !== null) {
            $this->db->where('DATE(daily_date) >=', $startDate);
            $this->db->where('DATE(daily_date) <=', $endDate);
        }

        $this->db->order_by('daily_date', 'ASC');
        $this->db->select('donut_count.id,donut_count.store_key,donut_type,week_day,daily_date,total_order,total_sale, special_day.name as special_day_name');
        $this->db->from('donut_count');

        $subQuery = "(SELECT MAX(date) FROM special_day WHERE MONTH(special_day.date) = MONTH(donut_count.daily_date) AND DAY(special_day.date) = DAY(donut_count.daily_date))";
        $this->db->join('special_day', "MONTH(donut_count.daily_date) = MONTH(special_day.date) AND DAY(donut_count.daily_date) = DAY(special_day.date) AND special_day.date = {$subQuery}", 'left', false);


        $query = $this->db->get();
        //echo $this->db->last_query();

        $result = $query->result();

        $groupedData = [];
        foreach ($result as $row) {
            $groupedData[$row->store_key][$row->donut_type][] = $row;
        }

        return $groupedData;
    }

    public function getBestSaleByType($days = null, $months = null, $years = null, $type = null, $storeKeys = null) {
        $this->db->select('store_key, SUM(total_order) AS total_order_sum, SUM(total_sale) AS total_sale_sum');
        $this->db->from('donut_count');

        if ($days) {
            $this->db->where('DATE(daily_date) >=', date('Y-m-d', strtotime("-$days days")));
        }

        if ($months != null) {
            if ($months == 0) {
                // Fetch data for the current month
                $firstDayOfCurrentMonth = date('Y-m-01'); // First day of the current month
                $this->db->where('DATE(daily_date) >=', $firstDayOfCurrentMonth);
            } else {
                $this->db->where('DATE_FORMAT(daily_date, "%Y-%m") =', $months);
            }
        }

        if ($years != null) {
            if ($years == 0) {
                // Fetch data for the current year
                $firstDayOfCurrentYear = date('Y-01-01'); // First day of the current month
                $this->db->where('DATE(daily_date) >=', $firstDayOfCurrentYear);
            } else {
                $this->db->where('YEAR(daily_date) >=', $years);
            }
        }

        if ($type) {
            $this->db->where('LOWER(donut_type)', strtolower($type));
        } else {
            $this->db->where_in('LOWER(donut_type)', ['donuts', 'fancy', 'munkins']);
        }

        $storeKeysArray = array_map('trim', explode(',', $storeKeys));
        if ($storeKeys !== null && !empty($storeKeysArray)) {
            $this->db->where_in('donut_count.store_key', $storeKeysArray);
        }

        $this->db->group_by('store_key');
        $this->db->order_by('store_key');
        $query = $this->db->get();
        //echo $this->db->last_query();
        $result = $query->result_array();
        //print_r($result);
        $bestSaleStore = [];
        $highestSale = 0;

        foreach ($result as $row) {
            if ($row['total_sale_sum'] > 0) {
                $order_per_sale_percentage = ($row['total_sale_sum'] / $row['total_order_sum']) * 100;
                $row['order_per_sale_percentage'] = round($order_per_sale_percentage,2);
            } else {
                $row['order_per_sale_percentage'] = 0;
            }

            if ($row['total_sale_sum'] > $highestSale) {
                $highestSale = $row['total_sale_sum'];
                $bestSaleStore = $row;
            }
        }

        return $bestSaleStore;
    }

    public function salesReport($storeKey = null, $donutType = null, $timeRange = null, $total = 0, $storeKeys = null) {
        $this->db->select('`store_key`,`donut_type`, SUM(total_order) as total_orders, SUM(total_sale) as total_sales');

        // Apply store_key filter if provided
        if ($storeKey !== null) {
            $storeKeyArray = array_map('trim', explode(',', $storeKey));
            $this->db->where_in('store_key', $storeKeyArray);
        }

        $storeKeysArray = array_map('trim', explode(',', $storeKeys));
        if ($storeKeys !== null && !empty($storeKeysArray)) {
            $this->db->where_in('store_key', $storeKeysArray);
        }

        // Apply donut_type filter if provided
        if ($donutType !== null) {
            $donutTypeArray = array_map('trim', explode(',', strtolower($donutType)));
            $this->db->where_in('LOWER(donut_type)', $donutTypeArray);
        }

        $this->db->group_by('store_key');
        $this->db->group_by('donut_type');
        // Apply time range filter based on the provided value


        switch ($timeRange) {
            case 'last_7_days':
                if(!$total)
                {
                    $this->db->select('DATE(daily_date) as daily_date');
                    $this->db->group_by('DATE(daily_date)');
                    $this->db->order_by('daily_date', 'ASC');
                }
                $this->db->where('daily_date >=', date('Y-m-d', strtotime('-7 days')));
                break;
            case 'current_month':
                if(!$total)
                {
                    $this->db->select('DATE(daily_date) as daily_date');
                    $this->db->group_by('DATE(daily_date)');
                    $this->db->order_by('daily_date', 'ASC');
                }
                $this->db->where('MONTH(daily_date)', date('m'));
                $this->db->where('YEAR(daily_date)', date('Y'));
                break;
            case 'last_month':
                if(!$total)
                {
                    $this->db->select('DATE(daily_date) as daily_date');
                    $this->db->group_by('DATE(daily_date)');
                    $this->db->order_by('daily_date', 'ASC');
                }
                $this->db->where('DATE(daily_date) >=', date('Y-m-01', strtotime('first day of last month')));
                $this->db->where('DATE(daily_date) <=', date('Y-m-t', strtotime('last day of last month')));                break;
            case 'YTD':
                if(!$total) {
                    $this->db->select('YEAR(daily_date) as daily_date_year');
                    $this->db->select('MONTH(daily_date) as daily_date_month');
                    $this->db->group_by('YEAR(daily_date)');
                    $this->db->group_by('MONTH(daily_date)');
                    $this->db->order_by('YEAR(daily_date)', 'ASC');
                    $this->db->order_by('MONTH(daily_date)', 'ASC');
                }
                $this->db->where('YEAR(daily_date)', date('Y'));
                break;
            case '2YTD':
                if(!$total) {
                    $this->db->select('YEAR(daily_date) as daily_date_year');
                    $this->db->select('MONTH(daily_date) as daily_date_month');
                    $this->db->group_by('YEAR(daily_date)');
                    $this->db->group_by('MONTH(daily_date)');
                    $this->db->order_by('YEAR(daily_date)', 'ASC');
                    $this->db->order_by('MONTH(daily_date)', 'ASC');
                }
                $this->db->where('YEAR(daily_date) >=', date('Y', strtotime('-1 year')));
                break;
        }

        $query = $this->db->get('donut_count');
        $result = $query->result();
        //echo $this->db->last_query();

        $groupedData = array();
        foreach ($result as $row) {

            if(!$total) {
                $key = '';
                switch ($timeRange) {
                    case 'last_7_days':
                        $key = date('M-d', strtotime($row->daily_date));
                        break;
                    case 'current_month':
                        $key = date('M-d', strtotime($row->daily_date));
                        break;
                    case 'last_month':
                        $key = date('M-d', strtotime($row->daily_date));
                        break;
                    case 'YTD':
                        $key = date("M", mktime(0, 0, 0, $row->daily_date_month, 10)).'-'.$row->daily_date_year;
                        break;
                    case '2YTD':
                        $key = date("M", mktime(0, 0, 0, $row->daily_date_month, 10)).'-'.$row->daily_date_year;
                        break;
                }
                $groupedData[$row->store_key][$row->donut_type][$key] = $row;
            } else {
                $groupedData[$row->store_key][$row->donut_type][] = $row;
            }
        }

        return $groupedData;
    }
}