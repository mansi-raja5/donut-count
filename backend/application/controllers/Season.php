<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Season extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('season_model');
        $this->load->model('store_master_model');
    }

    public function index() {
        $data['title'] = 'Season List';
        if ($this->input->is_ajax_request()) {
            $item_Res = $this->season_model->Get(null, $this->input->post());
            $this->getListing($item_Res);
        } else {
            $data['season_list'] = $this->season_model->Get(null, $this->input->post());
            $this->template->load('listing', 'season/list', $data);
        }
    }
       public function getListing($result = array()) {
        $tableData = array();
        foreach ($result['records'] as $key => $row) {
            $action = array();
            $action[] = anchor('season/edit/' . $row->id, 'Edit');
            $action[] = anchor('javascript:void(0);', 'Delete', array('data-toggle' => 'modal', 'data-id' => $row->id, 'onclick' => 'setConfirmDetails(this)', ' data-target' => '#ConfirmDeleteModal', 'data-url' => 'season/delete/' . $row->id));

            $tableData[$key]['srNo'] = $key + 1;
            $tableData[$key]['store_key'] = $row->store_key;
            $tableData[$key]['from_date'] = DB2Disp($row->from_date);
            $tableData[$key]['to_date'] = DB2Disp($row->to_date);
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
        $data['title'] = 'Add Season';
        $data['store_list'] = $this->store_master_model->Get(null, array("status" => 'A'));
        $this->template->load('listing', 'season/add', $data);
    }

    public function edit($id = NULL) {

        // Get ID
        if ($id == NULL) {
            $id = $this->uri->segment(3);
        }
        $data['title'] = 'Edit Season';
        $data['store_list'] = $this->store_master_model->Get(null, array("status" => 'A'));
        $data['season'] = $edit_res = $this->season_model->Get($id);
        $this->template->load('listing', 'season/add', $data);
    }

    public function save() {
          $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('', '');

        //Validating Fields
        $rules[] = array('field' => 'store_key', 'label' => 'Store', 'rules' => 'required');
        $rules[] = array('field' => 'from_date', 'label' => 'From Date', 'rules' => 'required');
        $rules[] = array('field' => 'to_date', 'label' => 'To Date', 'rules' => 'required');
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
                         $ins_Arr[] = array(
                            'store_key' => $sRow->key,
                            'from_date' => date("Y-m-d", strtotime($this->input->post('from_date'))),
                            'to_date' => date("Y-m-d", strtotime($this->input->post('to_date'))),
                            'name' => $this->input->post('name'),
                            'status' => $this->input->post('status')
                        );
                    }
                }
            } else {
                $data['store_key'] = $this->input->post('store_key');
                $data['from_date'] = date("Y-m-d", strtotime($this->input->post('from_date')));
                $data['to_date'] = date("Y-m-d", strtotime($this->input->post('to_date')));
                $data['name'] = $this->input->post('name');
                $data['status'] = $this->input->post('status');
            }
               
            // Now see if we are editing or adding
            if ($this->input->post('id') == NULL || $this->input->post('id') == '') {
                if ($this->input->post('store_key') == 'all') {
                    $this->season_model->Add_batch($ins_Arr);
                }else{
                    $this->season_model->Add($data);
                }
                $this->session->set_flashdata('success', 'Season detail has been inserted successfully');
            } else {
                $id = $this->input->post('id');
                // We have an ID, updating existing record
                $this->season_model->Edit($this->input->post('id'), $data);
                $this->session->set_flashdata('success', 'Season detail has been updated successfully');
            }
             redirect('season', 'redirect');
        }
    }

    public function delete($id = NULL) {
        if ($id) {
            $res = $this->season_model->Delete($id);
                redirect('season', 'redirect');
        }
    }

 

}
