<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Labor extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('labor_model');
        $this->load->model('store_master_model');
    }
    public function index()
    {
        $data['title']       = 'Labor';
        $data['store_list']  = $this->store_master_model->Get(null, array("status" => 'A'));
        $data['labor']       = $this->labor_model->get("labor_summary");
        $data['tax_percent'] = $this->labor_model->getTaxpercent();
        $data['masterpos']   = $this->labor_model->getMasterpos();
        $this->template->load('listing', 'labor/labor', $data);
    }
    public function getPostData()
    {
        $data = $this->input->post();
        return $data;
    }
    public function add()
    {
        $data['title']      = 'Add Labor Summary';
        $data['store_list'] = $this->store_master_model->Get(null, array('status' => 'A'));
        $data['action']     = 'add';
        $this->template->load('listing', 'labor/add_labor', $data);
    }
    public function adddata()
    {
        $data   = $this->getPostData();
        $return = $this->labor_model->add("labor_summary", $data);
        $this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                         Labor Summary added successfully</div>');
        redirect("labor");
    }
    public function edit($id = null)
    {
        // Get ID
        if ($id == null) {
            $id = $this->uri->segment(3);
        }
        $data['title']      = 'Edit Labor Summary';
        $data['labordata']  = $this->labor_model->getEditdata($id);
        $data['action']     = "edit";
        $store_list         = $this->store_master_model->Get(null, array('status' => 'A'));
        $data['store_list'] = $store_list;
        $this->template->load('listing', 'labor/add_labor', $data);
    }
    public function delete()
    {
        $id     = $this->uri->segment(3);
        $return = $this->labor_model->delete("labor_summary", $id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                         Labor Summary deleted successfully</div>');
        redirect("labor");
    }
    public function getCalculation()
    {
        $data                     = $this->getPostData();
        $data['admin_percentage'] = $this->labor_model->getTaxpercent();
        $data['labordata']        = $this->labor_model->getCalculation($data);
        $data['bonus'] = $this->labor_model->getBonus($data);
        $data['actionValue'] = $data['action'];
        echo $this->load->view('labor/add_labor_calculation', $data, true);
        exit();
    }
    public function checkExist(){
        $store_Key = $this->input->post('store_key');
        
        $week_ending_date = date("Y-m-d", strtotime($this->input->post('selected_date')));
        $Res = $this->labor_model->get("labor_summary", array("store_key" => $store_Key, "week_ending_date" =>$week_ending_date));
        if(empty($Res)){
            echo json_encode(array("status" => "true"));
        }else{
            echo json_encode(array("status" => "false"));
        }
    }
}
