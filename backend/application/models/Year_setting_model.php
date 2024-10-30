<?php
Class year_setting_model extends CI_Model {
    public function query_result($sql = FALSE) {
        if ($sql) {
            $query = $this->db->query($sql);
            return $query->result();
        }else{
            return FALSE;
        }
    }

    public function getByYear($year, $month = '', $row = false){
		$this->db->from('admin_year_setting');
		$this->db->where('admin_year_setting.year', $year);
                if($month != ''){
                    $this->db->where('month', $month);
                }
        $query = $this->db->get();
        if ($row) {
            return $query->row();
        }
		return $query->result();
	}

    public function getAllWeeksOfYear($year, $calcWeekPosition = false) {
        $this->db->from('admin_year_setting');
        $this->db->where('admin_year_setting.year', $year);
        $query = $this->db->get();
        $years = [];
        $yearSetting = $query->result();
        foreach ($yearSetting as $_yearSetting) {
            $years = array_merge($years,json_decode($_yearSetting->weeks,true));
        }
        if ($calcWeekPosition) {
            if ($this->getNumberOfWeeks($year) == 52) {
                $weekPosition = $this->getWeekPosition($year);
                if ($this->isShifted($year)) {
                    $years = array_merge(array($years[$weekPosition - 1]), $years);
                } else {
                    $years = array_merge($years, array($years[$weekPosition - 1]));
                }    
            } else if ($this->getNumberOfWeeks($year) == 53) {
                $weekPosition = $this->getWeekPosition($year);
                if ($weekPosition == 1) {
                    $years = array_splice($years, 1, 52);
                } else if ($weekPosition == 53) {
                    $years = array_splice($years, 0, 52);
                }
            }
        }
        return $years;
    }

    public function getAllWeeksOfYearWithMonth($year){
        $this->db->from('admin_year_setting');
        $this->db->where('admin_year_setting.year', $year);
        $query = $this->db->get();
        $years = [];
        $yearSetting = $query->result();
        foreach ($yearSetting as $_yearSetting) {
            $years[$_yearSetting->month] = json_decode($_yearSetting->weeks, true);
        }
        return $years;
    }

    public function getDateInfo($date) {
        // return getDateInfo($date);
        $data['date'] = date('Y-m-d', strtotime($date));
        $data['year'] = date('Y', strtotime($date));
        $yearSettings = $this->getAllWeeksOfYearWithMonth($data['year']);
        $yearWeek = 1;
        foreach ($yearSettings as $month => $weeks) {
            foreach ($weeks as $week => $_week) {
                if (strtotime($_week['start_of_week']) <= strtotime($date) && strtotime($date) <= strtotime($_week['end_of_week'])) {
                    $data['week_number_of_year']  = $yearWeek;
                    $data['week_number_of_month'] = $week + 1;
                    $data['start_of_week']        = $_week['start_of_week'];
                    $data['end_of_week']          = $_week['end_of_week'];
                    $data['month']                = $month;
                    break;
                }
                $yearWeek++;
            }
        }
        if (!isset($data['week_number_of_month'])) {
            return getDateInfo($date);
        }
        $data['day_number_of_week'] = date_diff(date_create($date), date_create($data['start_of_week']))->days + 1;
        return $data;
    }

    public function getPreviousYearSameDay($dayNumber, $weekNumber, $year)
    {
        // return getPreviousYearSameDay($dayNumber, $weekNumber, $year);
        $yearSettings = $this->getAllWeeksOfYear($year);
        if ($weekStartDate = ($yearSettings[$weekNumber - 1]['start_of_week'] ?? false)) {
            return date('Y-m-d', strtotime($weekStartDate . "+ " . --$dayNumber . " day"));
        }
        return getPreviousYearSameDay($dayNumber, $weekNumber, $year);
    }

    public function getNumberOfWeeks($year = null) {
        if (is_null($year)) {
            $year = date('Y');
        }
        $query = $this->db->query("SELECT SUM(month_weeks) as weeks FROM admin_year_setting WHERE YEAR = {$year}");
        return $query->row()->weeks;
    }

    public function getWeekPosition($year = null) {
        if (is_null($year)) {
            $year = date('Y');
        }
        $query = $this->db->query("SELECT week_position FROM admin_year_setting WHERE YEAR = {$year} LIMIT 1");
        return $query->row()->week_position;
    }

    public function isShifted($year = null) {
        if (is_null($year)) {
            $year = date('Y');
        }
        $query = $this->db->query("SELECT is_shifted FROM admin_year_setting WHERE YEAR = {$year} LIMIT 1");
        return (1 == $query->row()->is_shifted);
    }

    public function getYearStartingDate($year = null) {
        if (is_null($year)) {
            $year = date('Y');
        }
        $query = $this->db->query("SELECT year_starting_date FROM admin_year_setting WHERE YEAR = {$year} LIMIT 1");
        return date('Y-m-d', strtotime($query->row()->year_starting_date));
    }

    public function getYearEndingDate($year = null) {
        if (is_null($year)) {
            $year = date('Y');
        }
        $date  = $this->getYearStartingDate($year);
        $weeks = $this->getNumberOfWeeks($year);
        $dateTime = DateTime::createFromFormat('Y-m-d', $date);
        $dateTime->add(DateInterval::createFromDateString($weeks. ' weeks'));
        $dateTime->modify('saturday');
        return $dateTime->format('Y-m-d');
    }

    public function getWeekStartingEndingDate($date) {
        $weekData = [];
        $yearSettings = $this->getAllWeeksOfYear(date('Y', strtotime($date)));
        foreach ($yearSettings as $week => $_week) {
            $weekData[] = array(
                'week_number' => $week + 1,
                'start_of_week' => $_week['start_of_week'],
                'end_of_week' => $_week['end_of_week']
            );
            if (strtotime($_week['start_of_week']) <= strtotime($date) && strtotime($date) <= strtotime($_week['end_of_week'])) {
                break;
            }
        }
        return $weekData;
    }

    public function getLedger($year){
        $this->db->from('ledger');
        $this->db->where('year', $year);
        $query = $this->db->get();
        return $query->result();
    }

    public function Add_Batch($data) {
        $this->db->insert_batch('admin_year_setting', $data);
        $guid = $this->db->insert_id();

        return $guid;
    }

    public function deleteByYear($year)
    {
        $this->db->where('year', $year);
        $this->db->delete('admin_year_setting');
        return true;
    }
}