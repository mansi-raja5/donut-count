<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Cars_entry extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('store_master_model');
        $this->load->model('cars_entry_model');
        $this->load->helper('kick');
    }

    public function index() {
        $data['title'] = 'Car List';
        if ($this->input->is_ajax_request()) {
            $car_Res = $this->cars_entry_model->Get(null, $this->input->post());
            $this->getListing($car_Res);
        } else {
            $store_list = $this->store_master_model->Get(NULL, array('status' => 'A'));
            $data['store_list'] = $store_list;
            $data['weekend_date'] = (date('D') != 'Sat') ? date('Y-m-d', strtotime('next Saturday')) : date('Y-m-d');
            $this->template->load('listing', 'list-cars-entry', $data);
        }
    }

    public function getListing($result = array()) {

        $tableData = array();
        foreach ($result['records'] as $key => $row) {
            $action = array();
            $action[] = anchor('cars_entry/edit/' . $row->id, 'Edit');
            $action[] = anchor('javascript:void(0);', 'Delete', array('data-toggle' => 'modal', 'data-id' => $row->id, 'onclick' => 'setConfirmDetails(this)', ' data-target' => '#ConfirmDeleteModal', 'data-url' => 'cars_entry/delete/' . $row->id));

            $weekend_data = json_decode($row->weekend_data);

            $tableData[$key]['srNo'] = $key + 1;
            $tableData[$key]['store_key'] = $row->store_key;
            $tableData[$key]['date'] = date("m/d/Y", strtotime($row->weekend_date));

            foreach ($weekend_data as $wRow) {
                $day = $wRow->day;
                switch ($day) {
                    case 'monday':
                        $tableData[$key]['day1_day'] = $wRow->day;
                        $tableData[$key]['day1_date'] = $wRow->date;
                        $tableData[$key]['day1_no_of_cars'] = $wRow->no_of_cars;
                        $tableData[$key]['day1_avg_time'] = $wRow->avg_time;
                        break;
                    case 'tuesday':
                        $tableData[$key]['day2_day'] = $wRow->day;
                        $tableData[$key]['day2_date'] = $wRow->date;
                        $tableData[$key]['day2_no_of_cars'] = $wRow->no_of_cars;
                        $tableData[$key]['day2_avg_time'] = $wRow->avg_time;
                        break;
                    case 'wednesday':
                        $tableData[$key]['day3_day'] = $wRow->day;
                        $tableData[$key]['day3_date'] = $wRow->date;
                        $tableData[$key]['day3_no_of_cars'] = $wRow->no_of_cars;
                        $tableData[$key]['day3_avg_time'] = $wRow->avg_time;
                        break;
                    case 'thursday':
                        $tableData[$key]['day4_day'] = $wRow->day;
                        $tableData[$key]['day4_date'] = $wRow->date;
                        $tableData[$key]['day4_no_of_cars'] = $wRow->no_of_cars;
                        $tableData[$key]['day4_avg_time'] = $wRow->avg_time;
                        break;
                    case 'friday':
                        $tableData[$key]['day5_day'] = $wRow->day;
                        $tableData[$key]['day5_date'] = $wRow->date;
                        $tableData[$key]['day5_no_of_cars'] = $wRow->no_of_cars;
                        $tableData[$key]['day5_avg_time'] = $wRow->avg_time;
                        break;
                }
            }

            $tableData[$key]['action'] = implode(" | ", $action);
            $tableData[$key]['id'] = $row->id;
        }
        
        $data['data'] = $tableData;
        $data['recordsTotal'] = $result['countTotal'];
        $data['recordsFiltered'] = $result['countFiltered'];
        echo json_encode($data);
    }

    public function add() {
        $data['title'] = 'Add Cars Entry';
        $store_list = $this->store_master_model->Get(NULL, array('status' => 'A'));
        $data['store_list'] = $store_list;
        $data['action'] = 'add';
        $this->template->load('listing', 'cars_entry', $data);
    }

    public function edit($id = NULL) {
        // Get ID
        if ($id == NULL) {
            $id = $this->uri->segment(3);
        }
        $data['title'] = 'Edit Cars Entry';
        $data['cars_entry'] = $edit_res = $this->cars_entry_model->Get($id);
        $store_list = $this->store_master_model->Get(NULL, array('status' => 'A'));
        $data['store_list'] = $store_list;
        $this->template->load('listing', 'cars_entry', $data);
    }

    public function save() {
        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('', '');

        //Validating Fields
        $rules[] = array('field' => 'store_key', 'label' => 'Store', 'rules' => 'required');
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == FALSE) {
            // Validation failed
            if ($this->input->post('id') != 0) {
                return $this->edit($this->input->post('id'));
            } else {
                return $this->add();
            }
        } else {
            // Validation succeeded!
            // Create array for database fields & data
            $data = array();
            $store_key = $this->input->post('store_key');
            $weekend_date = $this->input->post('weekend_date');
            $dates_arr = $this->input->post('date');
            $days_arr = $this->input->post('day');
            $no_of_cars_arr = $this->input->post('no_of_cars');
            $avg_time_arr = $this->input->post('avg_time');

            $data = $ins_arr = array();
            $data['store_key'] = $store_key;
            $data['weekend_date'] = date("y-m-d", strtotime($weekend_date));
            for ($i = 0; $i < count($no_of_cars_arr); $i++) {
                $ins_arr[] = array(
                    "date" => isset($dates_arr[$i]) ? date("y-m-d", strtotime($dates_arr[$i])) : "",
                    "day" => isset($days_arr[$i]) ? $days_arr[$i] : "",
                    "no_of_cars" => isset($no_of_cars_arr[$i]) ? $no_of_cars_arr[$i] : "",
                    "avg_time" => isset($avg_time_arr[$i]) ? $avg_time_arr[$i] : "",
                );
            }

            $data['weekend_data'] = json_encode($ins_arr);

            // Now see if we are editing or adding
            if (($this->input->post('id') == NULL || $this->input->post('id') == '') && !empty($ins_arr)) {
                $this->cars_entry_model->Add($data);
                $this->session->set_flashdata('success', 'Cars entry has been inserted successfully');
            } else {
                $this->cars_entry_model->Edit($this->input->post('id'), $data);
                $this->session->set_flashdata('success', 'Cars entry has been updated successfully');
                $this->session->set_flashdata('msg_class', "success");
                $this->session->set_flashdata('msg', "Wrong File! Selected year does not match with the file year.");
            }
            redirect('cars_entry', 'redirect');
        }
    }

    public function delete($id = NULL) {
        if ($id) {
            $res = $this->cars_entry_model->Delete($id);
            redirect('cars_entry', 'redirect');
        }
    }

    public function get_dates() {
        $date = $this->input->post('selected_date');
        $store_key = $this->input->post('store_key');
        $weekend_data = $this->input->post('weekend_data');
        $weekend_data_arr = json_decode($weekend_data);
        if (isset($weekend_data_arr) && !empty($weekend_data_arr)) {
            foreach ($weekend_data_arr as $wRow) {
                $day = $wRow->day;
                switch ($day) {
                    case 'monday':
                        $data['day1_day'] = $wRow->day;
                        $data['day1_date'] = $wRow->date;
                        $data['day1_no_of_cars'] = $wRow->no_of_cars;
                        $data['day1_avg_time'] = $wRow->avg_time;
                        break;
                    case 'tuesday':
                        $data['day2_day'] = $wRow->day;
                        $data['day2_date'] = $wRow->date;
                        $data['day2_no_of_cars'] = $wRow->no_of_cars;
                        $data['day2_avg_time'] = $wRow->avg_time;
                        break;
                    case 'wednesday':
                        $data['day3_day'] = $wRow->day;
                        $data['day3_date'] = $wRow->date;
                        $data['day3_no_of_cars'] = $wRow->no_of_cars;
                        $data['day3_avg_time'] = $wRow->avg_time;
                        break;
                    case 'thursday':
                        $data['day4_day'] = $wRow->day;
                        $data['day4_date'] = $wRow->date;
                        $data['day4_no_of_cars'] = $wRow->no_of_cars;
                        $data['day4_avg_time'] = $wRow->avg_time;
                        break;
                    case 'friday':
                        $data['day5_day'] = $wRow->day;
                        $data['day5_date'] = $wRow->date;
                        $data['day5_no_of_cars'] = $wRow->no_of_cars;
                        $data['day5_avg_time'] = $wRow->avg_time;
                        break;
                }
            }
        }

        $data['week_days'] = working_days($date);
        $data['selected_date_month'] = date('m', strtotime($date));
        echo $this->load->view('car_week_days', $data, TRUE);
        exit();
    }

}
