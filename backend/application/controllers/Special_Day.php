<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Special_day extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('special_day_model');
        $this->load->model('store_master_model');
    }

    public function index() {
        $data['title'] = 'Special Day List';
        if ($this->input->is_ajax_request()) {
            $item_Res = $this->special_day_model->Get(null, $this->input->post());
            $this->getListing($item_Res);
        } else {
            $data['special_day_list'] = $this->special_day_model->Get(null, $this->input->post());
            $this->template->load('listing', 'special_day/list', $data);
        }
    }

    public function getListing($result = array()) {
        $tableData = array();
        foreach ($result['records'] as $key => $row) {
            $action = array();
            $action[] = anchor('special_day/edit/' . $row->id, 'Edit');
            $action[] = anchor('javascript:void(0);', 'Delete', array('data-toggle' => 'modal', 'data-id' => $row->id, 'onclick' => 'setConfirmDetails(this)', ' data-target' => '#ConfirmDeleteModal', 'data-url' => 'special_day/delete/' . $row->id));

            $tableData[$key]['srNo'] = $key + 1;
            $tableData[$key]['store_key'] = $row->store_key;
            $tableData[$key]['date'] = DB2Disp($row->date);
            $tableData[$key]['name'] = $row->name;
            $tableData[$key]['status'] = $row->status == 'A' ? 'Active' : 'Inactive';
            $tableData[$key]['action'] = implode(" | ", $action);
            $tableData[$key]['id'] = $row->id;
        }
        $data['data'] = $tableData;
        $data['recordsTotal'] = $result['countTotal'];
        $data['recordsFiltered'] = $result['countFiltered'];
        echo json_encode($data);
    }

    public function add() {
        $data['title'] = 'Add Special Day';
        $data['store_list'] = $this->store_master_model->Get(null, array("status" => 'A'));
        $this->template->load('listing', 'special_day/add', $data);
    }

    public function edit($id = NULL) {

        // Get ID
        if ($id == NULL) {
            $id = $this->uri->segment(3);
        }
        $data['title'] = 'Edit Special Day';
        $data['store_list'] = $this->store_master_model->Get(null, array("status" => 'A'));
        $data['special_day'] = $edit_res = $this->special_day_model->Get($id);
        $this->template->load('listing', 'special_day/add', $data);
    }

    public function save() {
        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('', '');

        //Validating Fields
        $rules[] = array('field' => 'store_key', 'label' => 'Store', 'rules' => 'required|callback_is_exist[]', 'errors' => array('is_exist' => 'Selected store have this Special day already exist for this date'));
        $rules[] = array('field' => 'date', 'label' => 'Date', 'rules' => 'required|callback_is_exist[]', 'errors' => array('is_exist' => 'Selected store have this Special day already exist for this date'));
        $rules[] = array('field' => 'name', 'label' => 'Name', 'rules' => 'required');
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
            if ($this->input->post('store_key') == 'all') {
                $store_list = $this->store_master_model->Get(null, array("status" => 'A'));
                if(isset($store_list['records']) && !empty($store_list['records'])){
                    $ins_Arr = array();
                    foreach ($store_list['records'] as $sRow){
                         $ins_Arr[] = array("store_key" => $sRow->key,
                            'date' => date("Y-m-d", strtotime($this->input->post('date'))),
                            'name' => $this->input->post('name'),
                            'status' => $this->input->post('status')
                        );
                    }
                }
                  
            } else {
                $data['store_key'] = $this->input->post('store_key');
                $data['date'] = date("Y-m-d", strtotime($this->input->post('date')));
                $data['name'] = $this->input->post('name');
                $data['status'] = $this->input->post('status');
            }


            // Now see if we are editing or adding
            if ($this->input->post('id') == NULL || $this->input->post('id') == '') {
                 if ($this->input->post('store_key') == 'all') {
                     $this->special_day_model->Add_batch($ins_Arr);
                 }else{
                     $this->special_day_model->Add($data);
                 }
                
                $this->session->set_flashdata('success', 'Special Day detail has been inserted successfully');
            } else {
                $id = $this->input->post('id');
                // We have an ID, updating existing record
                $this->special_day_model->Edit($this->input->post('id'), $data);
                $this->session->set_flashdata('success', 'Special Day detail has been updated successfully');
            }
            redirect('special_day', 'redirect');
        }
    }

    public function delete($id = NULL) {
        if ($id) {
            $res = $this->special_day_model->Delete($id);
            redirect('special_day', 'redirect');
        }
    }

    public function is_exist($str) {
        if ($this->input->post('id') != '0') {
            $condition['special_day.id <>'] = $this->input->post('id');
        }
        if ($this->input->post('store_key') != '') {
            $condition['store_key'] = $this->input->post('store_key');
        }
        if ($this->input->post('date') != '') {
            $condition['date'] = date("Y-m-d", strtotime($this->input->post('date')));
        }

        $this->db->where($condition);
        $num_row = $this->db->get('special_day')->num_rows();
        if ($num_row >= 1) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

}
