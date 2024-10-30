<?php
class SpecialDayModel extends CI_Model {

    public function addSpecialDay($data)
    {
        return $this->db->insert('special_day', $data);
    }

    public function getSpecialDays($status = null, $name = null, $type = null)
    {
        if ($status !== null) {
            $this->db->where('status', $status);
        }

        if ($name !== null) {
            $this->db->like('name', $name);
        }

        if ($type !== null && $type == 'upcoming') {
            $this->db->select('name as event_name');
            $this->db->select("CONCAT(DAYNAME(CONCAT(YEAR(CURDATE()),'-',MONTH(`date`),'-', DAY(`date`))), ', ', DAY(`date`), ' ', MONTHNAME(`date`)) as event_date");
            $this->db->where('MONTH(`date`) > MONTH(CURDATE())');
            $this->db->or_group_start();
            $this->db->or_where('MONTH(`date`) = MONTH(CURDATE())');
            $this->db->where('DAY(`date`) >= DAY(CURDATE())');
            $this->db->group_end();
        }

        $this->db->order_by('MONTH(`date`)', 'ASC');
        $this->db->order_by('DAY(`date`)', 'ASC');
        $query = $this->db->get('special_day');
        //echo $this->db->last_query();

        return $query->result_object();
    }

    public function getDonutCountBySpecialDayName($specialDayName, $year = null, $storeKey = null, $storeKeys = null) {
        // Fetch the date from special_day table
        $this->db->select('date,name');
        $this->db->from('special_day');

        if($specialDayName != 'all')
        {
            $this->db->like('name', $specialDayName);
        }

        $specialDayQuery = $this->db->get();
        if ($specialDayQuery->num_rows() > 0) {
            $specialDayResult = $specialDayQuery->result();

            $groupedData = array();

            foreach ($specialDayResult as $specialDayRow) {
                $specialDayDate = $specialDayRow->date;

                $month = date('m', strtotime($specialDayDate));
                $day = date('d', strtotime($specialDayDate));
                $this->db->select("*, '" . $specialDayRow->name . "' as special_day_name");

                // Fetch records from donut_count table where daily_date matches
                $this->db->from('donut_count');
                $this->db->where('MONTH(daily_date)', $month);
                $this->db->where('DAY(daily_date)', $day);

                $storeKeyArray = array_map('trim', explode(',', $storeKey));
                if ($storeKey !== null && !empty($storeKeyArray)) {
                    $this->db->where_in('store_key', $storeKeyArray);
                }

                $storeKeysArray = array_map('trim', explode(',', $storeKeys));
                if ($storeKeys !== null && !empty($storeKeysArray)) {
                    $this->db->where_in('store_key', $storeKeysArray);
                }

                $yearArray = array_map('trim', explode(',', $year));
                if ($year !== null && !empty($yearArray)) {
                    $this->db->where_in('YEAR(daily_date)', $yearArray);
                }

                $this->db->order_by('daily_date', 'ASC');
                $donutCountQuery = $this->db->get();

                //echo $this->db->last_query();
                $donutCount = $donutCountQuery->result();
                foreach ($donutCount as $row) {
                    $groupedData[$row->store_key][$row->donut_type][] = $row;

                }
            }

            return $groupedData;
        } else {
            return []; // No special day found
        }

    }
}
