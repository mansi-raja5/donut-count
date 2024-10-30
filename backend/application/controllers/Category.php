<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Category extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('category_model');
        $this->load->model('vendor_model');
        $this->load->model('ledger_document_model');
    }

    public function index()
    {
        $data['title'] = 'Category List';
        if ($this->input->is_ajax_request()) {
            $_POST['status'] = 'A';
            $item_Res        = $this->category_model->Get(null, $this->input->post());
            $this->getListing($item_Res);
        } else {
            $data['category_list'] = $this->category_model->Get(null, array("status" => 'A'));
            $this->template->load('listing', 'category/list-category', $data);
        }
    }

    public function getListing($result = array())
    {
        $tableData = array();
        foreach ($result['records'] as $key => $row) {
            $action   = array();
            $action[] = anchor('category/edit/' . $row->id, 'Edit');
            $action[] = anchor('javascript:void(0);', 'Delete', array('data-toggle' => 'modal', 'data-id' => $row->id, 'onclick' => 'setConfirmDetails(this)', ' data-target' => '#ConfirmDeleteModal', 'data-url' => 'category/delete/' . $row->id));

            $tableData[$key]['srNo']                  = $key + 1;
            $tableData[$key]['name']                  = $row->category_name;
            $tableData[$key]['company']               = $row->company;
            $tableData[$key]['description']           = $row->description;
            $tableData[$key]['breakdown_description'] = $row->breakdown_description;
            $tableData[$key]['status']                = $row->status == 'A' ? 'Active' : 'Inactive';
            $tableData[$key]['action']                = implode(" | ", $action);
            $tableData[$key]['id']                    = $row->id;
        }
        $data['data']            = $tableData;
        $data['recordsTotal']    = $result['countTotal'];
        $data['recordsFiltered'] = $result['countFiltered'];
        echo json_encode($data);
    }

    public function add()
    {
        $data['title']    = 'Add Category';
        $data['action']   = 'add';
        $data['vendor']   = $this->vendor_model->Get();
        $data['document'] = $this->ledger_document_model->Get(null, array("is_active" => 1));
        $this->template->load('listing', 'category/category', $data);
    }

    public function edit($id = null)
    {
        // Get ID
        if ($id == null) {
            $id = $this->uri->segment(3);
        }
        $data['title']              = 'Edit Category';
        $data['category']           = $edit_res           = $this->category_model->Get($id);
        $data['breakdown_category'] = $edit_res = $this->category_model->Get_breakdown_category($id);
        $data['vendor']             = $this->vendor_model->Get();
        $data['document']           = $this->ledger_document_model->Get(null, array("is_active" => 1));
        $this->template->load('listing', 'category/category', $data);
    }

    public function save()
    {
        $this->load->library('form_validation');
        $this->form_validation->set_error_delimiters('', '');
        //Validating Fields
        $rules[] = array('field' => 'name', 'label' => 'Name', 'rules' => 'required');
        $rules[] = array('field' => 'type', 'label' => 'Type', 'rules' => 'required');
        $this->form_validation->set_rules($rules);
        if ($this->form_validation->run() == false) {
            // Validation failed
            if ($this->input->post('id') != 0) {
                return $this->edit($this->input->post('id'));
            } else {
                return $this->add();
            }
        } else {
            // Validation succeeded!
            // Create array for database fields & data
            
            $data                  = array();
            $type                  = $this->input->post('type');
            $data['category_key']  = $this->input->post('name');
            $data['category_name'] = $this->input->post('category_name');
            $data['type']          = $this->input->post('type');
            $data['description']   = $this->input->post('description');
            $data['vendor_id']     = $this->input->post('vendor_id');
            $data['week_date_display_option']  = isset($_POST['week_date_display_option']) && $_POST['week_date_display_option'] == 1 ? 1 : 0;
            $data['is_display_calender']     = isset($_POST['week_date_display_option']) && $_POST['week_date_display_option'] == 2 ? 1 : 0;
            $data['is_display_last_week_ending_date_as_last_date_of_month']  = isset($_POST['is_display_last_week_ending_date_as_last_date_of_month']) ? $_POST['is_display_last_week_ending_date_as_last_date_of_month'] : 0;
            $data['status']        = $this->input->post('status');

            // Now see if we are editing or adding
            if ($this->input->post('id') == null || $this->input->post('id') == '') {
                $id = $this->category_model->Add($data);
                $this->session->set_flashdata('success', 'Category detail has been inserted successfully');
            } else {
                $id = $this->input->post('id');
                // We have an ID, updating existing record
                $this->category_model->Edit($this->input->post('id'), $data);
                $this->category_model->Delete_Batch_Description($this->input->post('id'));
                $this->session->set_flashdata('success', 'Category detail has been updated successfully');
            }
            if ($type == 'breakdown_description') {
                $breakdown_description_Arr = array_filter($this->input->post('breakdown_description'));
                $breakdown_vendor_Arr      = array_filter($this->input->post('breakdown_vendor_id'));
                if (!empty($breakdown_description_Arr)) {
                    for ($i = 0; $i < count($breakdown_description_Arr); $i++) {
                        $ins_arr[] = array("bill_category_id" => $id,
                            "description"                         => isset($breakdown_description_Arr[$i]) ? $breakdown_description_Arr[$i] : '',
                            "vendor_id"                           => isset($breakdown_vendor_Arr[$i]) ? $breakdown_vendor_Arr[$i] : '',
                            "status"                              => 'A',
                        );
                    }
                    if (isset($ins_arr) && !empty($ins_arr)) {
                        $this->category_model->AddBatch($ins_arr);
                    }
                }
            }
            redirect('category', 'redirect');
        }
    }

    public function delete($id = null)
    {
        if ($id) {
            $res = $this->category_model->Delete($id);
            redirect('category', 'redirect');
        }
    }
    public function get_category_desc()
    {
        $category = $this->input->post('category');
        $type     = $this->input->post('type') == 'BD' ? "breakdown_description" : "description";
        $Res      = $this->category_model->get_category_desc($type, $category);
//        $Res = $this->category_model->Get(NULL, array("category" => $category));
        echo json_encode(array("status" => 'success', "res" => $Res));
        exit;
    }
    public function get_category_breakdown_desc()
    {
        $cat_id = $this->input->post('id');
        $Res    = $this->category_model->Get_breakdown_category($cat_id);
        echo json_encode(array("status" => 'success', "res" => $Res));
        exit;
    }
    public function get_week_dates()
    {
        $ledger_date    = $this->input->post('date');
        $is_display_last_date    = $this->input->post('is_display_last_date');
        $ledger_date_Ar = explode("/", $ledger_date);
        $ledger_date    = $ledger_date_Ar[1] . "-" . $ledger_date_Ar[0] . "-01";
        $year = $ledger_date_Ar[1];
        $month = $ledger_date_Ar[0];
        $this->load->model('year_setting_model');
        $year_result         = $this->year_setting_model->getByYear($year, $month);
        $date_Arr = array();
        if (isset($year_result) && !empty($year_result)) {
            foreach ($year_result as $cRow) {
               $week_Res = json_decode($cRow->weeks);
               if(is_array($week_Res) && !empty($week_Res)){
                    foreach ($week_Res as $WRow){
                        if(date('m', strtotime($WRow->end_of_week)) != $month){
                            if($is_display_last_date == 1){
                                break;  
                            }else {
                                $date_Arr[] = $WRow->end_of_week;
                            }
                        }else {
                            $date_Arr[] = $WRow->end_of_week;
                        }
                        
                    }
               }
            }
        }
        if(empty($date_Arr)){
            echo json_encode(array("status" => FALSE));
        } else {
            echo json_encode(array("status" => TRUE, "dates" => $date_Arr));
        }
        
        exit;
    }

}
