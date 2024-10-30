<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Store extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('store_master_model');
    }

    public function index() {
        $data['title'] = 'Store List';
        $data['stores'] = $this->store_master_model->Get();
        $this->template->load('listing', 'store/list_store', $data);
    }

    public function postData() {
        $data = $this->input->post();
        return $data;
    }

    public function add() {
        $data['title'] = 'Store details';
        $data['action'] = 'save';
        $data['stores'] = "";
        $this->template->load('listing', 'store/add_store', $data);
    }

    public function save() {
        $this->load->library('form_validation');

        if ($this->input->is_ajax_request()) {
            $this->load->helper('form');
        }

        $this->form_validation->set_error_delimiters('', '');

        //Validating Fields
//        $rules[] = array('field' => 'id', 'rules' => 'required');
        $rules[] = array('field' => 'certipay_control', 'label' => 'Certipay Control', 'rules' => 'required|callback_is_exist[certipay_control]', 'errors' => array('is_exist' => 'This %s already exists.'));
        $rules[] = array('field' => 'business_name', 'label' => 'Business Name', 'rules' => 'required|callback_is_exist[business_name]', 'errors' => array('is_exist' => 'This %s already exists.'));
        $rules[] = array('field' => 'key', 'label' => 'Key', 'rules' => 'required|callback_is_exist[key]', 'errors' => array('is_exist' => 'This %s already exists.'));
        $rules[] = array('field' => 'name', 'label' => 'Name', 'rules' => 'required|callback_is_exist[name]', 'errors' => array('is_exist' => 'This %s already exists.'));
        $rules[] = array('field' => 'location', 'label' => 'Address', 'rules' => 'required|callback_is_exist[location]', 'errors' => array('is_exist' => 'This %s already exists.'));
        $rules[] = array('field' => 'tax_id', 'label' => 'Tax ID', 'rules' => 'required|callback_is_exist[tax_id]', 'errors' => array('is_exist' => 'This %s already exists.'));

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run() == FALSE) {
           
            if ($this->input->post('store_id') != '' && $this->input->post('store_id') > 0) {
                $this->Edit($this->input->post('store_id'));
            } else {
                $this->Add();
            }
        } else {
            $data = $this->postData();

            $result = $this->store_master_model->Add($data);
            if ($result) {
                $this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Store added successfully!!!</div>');
            } else {
                $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Something went wrong!!!</div>');
            }
            redirect("store");
        }
    }

    public function edit() {
        $id = $this->uri->segment(3);
        $data['action'] = 'update';
        $data['title'] = 'Store details';
        $data['stores'] = $this->store_master_model->Get($id, NULL);
        $this->template->load('listing', 'store/add_store', $data);
    }

    public function update() {
        $id = $this->input->post("store_id");
        $data = $this->postData();
        unset($data['id']);
        $result = $this->store_master_model->Edit($id, $data);
        if ($result) {
            $this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Store updated successfully!!!</div>');
        } else {
            $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Something went wrong!!!</div>');
        }
        redirect("store/");
    }

    public function delete() {
        $id = $this->uri->segment(3);
        $result = $this->store_master_model->Delete($id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Store deleted successfully!!!</div>');
        redirect("store/");
    }

    public function is_exist($str, $field = 'certipay_control') {
        if ($this->input->post('store_id') != '0') {
            $condition['store.store_id <>'] = $this->input->post('store_id');
        }
        $condition[$field] = $str;
        $this->db->where($condition);
        $num_row = $this->db->get('store_master')->num_rows();
        if ($num_row >= 1) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
    public function get_address(){
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        $Res = $this->store_master_model->Get_By_Key($id);
        if(!empty($Res)){
            echo json_encode(array("status" => 'success', 'address' => $Res->location));
        }else{
            echo json_encode(array("status" => 'failure'));
        }
        exit;
        
    }

}
