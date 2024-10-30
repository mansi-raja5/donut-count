<?php

class Labor_model extends CI_Model {

    public function add($table, $data) {
        $store_key = $data['store_key'];
        if ($store_key == 'all') {
            $store_Q = $this->db->get_where("store_master", array(
                'status' => 'A',
            ));
            $Store_Result = $store_Q->result();
            if (isset($Store_Result) && !empty($Store_Result)) {

                $start_date = date('Y-m-d', strtotime($data['week_ending_date'] . ' - 6 days'));
                $end_date = date("Y-m-d", strtotime($data['week_ending_date']));

                $alldates = $this->getDatesFromRange($start_date, $end_date);

                $insert_Arr = $update_Arr = array();

                foreach ($Store_Result as $sRow) {
                    //grosspay
                    $query = $this->db->select("*")->where('store_key', $sRow->key)->like(array('start_date' => $start_date, "end_date" => $end_date))->get("master_payroll");
                    $grosspay = 0;
                    if ($query->num_rows() > 0) {
                        foreach ($query->result_array() as $row) {
                            $grosspay += $row['gross_wages_sum'];
                        }
                    }
                    //nettsales
                    $query = $this->db->select("*")->where('store_key', $sRow->key)->where_in('cdate', $alldates)->get("master_pos_daily");
                    $net_sales = 0;
                    if ($query->num_rows() > 0) {
                        foreach ($query->result_array() as $row) {
                            $jsondata = json_decode($row['data']);
                            $net_sales += ((float) $jsondata->dd_retail_net_sales + (float) $jsondata->br_retail_net_sales);
                        }
                    }
                    $return = array();
                    $bonus = $data['bonus'] != '' ? $data['bonus'] : 0;
                    $tax_percent = $this->getTaxpercent() ? $this->getTaxpercent() : 0;


                    // check if entry is there date and store
                    $data['week_ending_date'] = date("Y-m-d", strtotime($data['week_ending_date']));
                    $squery = $this->db->get_where($table, array(
                        'week_ending_date' => $data['week_ending_date'],
                        'store_key' => $sRow->key,
                    ));

                    $count = $squery->num_rows();
                    $row = $squery->row();

                    //insert batch array
                    $data['week_ending_date'] = date("Y-m-d", strtotime($data['week_ending_date']));
                    $data['store_key'] = $sRow->key;
                    $grosspay = $bonus + $data['covid'] + $grosspay;
                    $tax = ($grosspay * $tax_percent) / 100;
                    $totalpay = $grosspay + $tax;
                    $labor_percentage = $net_sales ? ($totalpay / $net_sales) * 100 : 0;


                    if ($count == 0) {
                        $data['gross_pay'] = $grosspay;
                        $data['tax_percentage'] = $tax_percent;
                        $data['tax_amount'] = $tax;
                        $data['bonus'] = $bonus;
                        $data['total_pay'] = $totalpay;
                        $data['net_sales'] = $net_sales;
                        $data['labor_percentage'] = $labor_percentage;
                        unset($data['id']);
                        $insert_Arr[] = $data;
                    } else {
                        //update batch array
                        $data['id'] = $row->id;
                        $data['week_ending_date'] = date("Y-m-d", strtotime($data['week_ending_date']));
                        $data['store_key'] = $sRow->key;

                        $data['gross_pay'] = $grosspay;
                        $data['tax_percentage'] = $tax_percent;
                        $data['tax_amount'] = $tax;
                        $data['bonus'] = $bonus;
                        $data['total_pay'] = $totalpay;
                        $data['net_sales'] = $net_sales;
                        $data['labor_percentage'] = $labor_percentage;

                        $update_Arr[] = $data;
                    }
                }
                if (!empty($insert_Arr)) {
                    $this->db->insert_batch('labor_summary', $insert_Arr);
                }
                if (!empty($update_Arr)) {
                    $this->db->update_batch('labor_summary', $update_Arr, 'id');
                }
            }
        } else {
            // check if entry is there date and store
            $data['week_ending_date'] = date("Y-m-d", strtotime($data['week_ending_date']));
            $query = $this->db->get_where($table, array(
                'week_ending_date' => $data['week_ending_date'],
                'store_key' => $data['store_key'],
            ));
            $count = $query->num_rows();
            $row = $query->row();
            if ($count === 0) {
                $this->db->insert($table, $data);
                $guid = $this->db->insert_id();
            } else {
                unset($data['id']);
                $this->db->where('id', $row->id);
                $this->db->update($table, $data);
                $guid = $this->db->insert_id();
            }
        }

        return true;
    }

    public function get($table, $where = '') {
        $return = array();
        $this->db->select('lb.*, YEAR(lb.week_ending_date) as lyear');
        if ($where != '') {
            $this->db->where($where);
        }
        $query = $this->db->get("labor_summary lb");
        if ($query->num_rows() > 0):
            $return = $query->result_array();
        endif;
        return $return;
    }

    public function getEditdata($id) {
        $return = array();
        $query = $this->db->select('*')
                        ->from("labor_summary lb")->where("id", $id)->get();
        if ($query->num_rows() > 0):
            $return = $query->result_array();
            $return = $return[0];
        endif;
        return $return;
    }

    public function delete($table, $id) {
        $query = $this->db->where('id', $id)->delete($table);
        return $this->db->affected_rows();
    }

    public function getTaxpercent() {
        $tax_percent = "";
        $query = $this->db->get_where("admin_settings", array(
            'key_name' => 'labour_txt_percentage',
        ));
        if ($query->num_rows() > 0):
            $row = $query->row();
            $tax_percent = $row->key_value;
        endif;
        return $tax_percent;
    }

    public function getMasterpos() {
        $return = array();
        $query = $this->db->select("*")->get("master_pos_weekly");
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $paid_out = "";
                $jsondata = json_decode($row['data']);
                $return[date("d-m-Y", strtotime($row['end_date']))][] = array("store_key" => $row['store_key'], "net_sales" => "");
            }
        }
        return $return;
    }

    // Function to get all the dates in given range
    public function getDatesFromRange($start, $end, $format = 'Y-m-d') {
        $array = array();
        $interval = new DateInterval('P1D');
        $realEnd = new DateTime($end);
        $realEnd->add($interval);
        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);
        foreach ($period as $date) {
            $array[] = $date->format($format);
        }
        return $array;
    }

    public function getCalculation($data) {
        $start_date = date('Y-m-d', strtotime($data['selected_date'] . ' - 6 days'));
        $end_date = date("Y-m-d", strtotime($data['selected_date']));

        $alldates = $this->getDatesFromRange($start_date, $end_date);
        //grosspay
        $query = $this->db->select("*")->where('store_key', $data['store_key'])->like(array('start_date' => $start_date, "end_date" => $end_date))->get("master_payroll");
//        echo $this->db->last_query();
        $grosspay = 0;
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $grosspay += $row['gross_wages_sum'];
            }
        }
        //nettsales
        $query = $this->db->select("*")->where('store_key', $data['store_key'])->where_in('cdate', $alldates)->get("master_pos_daily");
        $net_sales = 0;
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $jsondata = json_decode($row['data']);
                $net_sales += ((float) $jsondata->dd_retail_net_sales + (float) $jsondata->br_retail_net_sales);
            }
        }
        $return = array();
        $bonus = $covid = "";
        $tax_percent = $this->getTaxpercent() ? $this->getTaxpercent() : 0;
        if ($data['action'] == "edit") {
            $query = $this->db->select("*")->where(array('store_key' => $data['store_key'], 'week_ending_date' => $end_date))->get("labor_summary");
            $row = $query->row();
            $tax = $row->tax_amount;
            $totalpay = $row->total_pay;
            $bonus = $row->bonus;
            $covid = $row->covid;
            $tax_percent = $row->tax_percentage;
            $labor_percentage = $row->labor_percentage;
        } else {
            $tax = ($grosspay * $tax_percent) / 100;
            $totalpay = $grosspay + $tax;
            $labor_percentage = $net_sales ? ($totalpay / $net_sales) * 100 : 0;
        }
        $return['grosspay'] = $grosspay;
        $return['tax_percent'] = $tax_percent;
        $return['tax_amount'] = $tax;
        $return['bonus'] = $bonus;
        $return['covid'] = $covid;
        $return['total_pay'] = $totalpay;
        $return['net_sales'] = $net_sales;
        $return['labor_percentage'] = $labor_percentage;
        return $return;
    }

    function getBonus($data) {
        $year = date('Y', strtotime($data['selected_date']));
        $month = date('M', strtotime($data['selected_date']));
        $weekly_date = date('Y-m-d', strtotime($data['selected_date']));
        ;
        $where = '(month like "%' . $month . '%" or year = "' . $year . '" or weekly_date="' . $weekly_date . '")';
        $query = $this->db->select("*")->where_in('store_key', $data['store_key'])->where($where)->get("admin_labor_bonus_setting");
        $bonus = "";
        if ($query->num_rows() > 0) {
            $row = $query->row();
            $bonus = $row->amount;
        }
        return $bonus;
    }

}
