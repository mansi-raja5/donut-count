<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Common extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('masterpos_model');
        $this->load->model('common_model');
        $this->load->model('store_master_model');
        $this->load->helper('kick');
    }

    public function index() {
        $data['title'] = 'Common';
        $data['store_list'] = $this->store_master_model->Get(null, array("status" => 'A'));
        $this->template->load('listing', 'common', $data);
    }

    public function getPostData() {
        $data = [];
        $data = $this->input->post();
        return $data;
    }

    //MASTER POS
    public function getMasterPosGrid() {
        $data = $this->getPostData();
        $data['dynamic_column'] = $this->common_model->getDynamiccolumn("pos_master_key", "key_name", "key_label");
        $data['masterpos'] = $this->common_model->getData($data, "master_pos_daily");
        $data['masterpos_weekly'] = $this->common_model->getPosMonthlyWeeklyData($data, "master_pos_weekly");
        $data['masterpos_monthly'] = $this->common_model->getPosMonthlyWeeklyData($data, "master_pos_monthly");
        $data['alldates'] = $this->common_model->get_dates($data['month'], $data['year'], "month");
        echo $this->load->view('common/masterpos_grid', $data, TRUE);
        exit();
    }

    public function getPaidoutGrid() {
        $data = $this->getPostData();
        $data['dynamic_column'] = $this->common_model->getDynamiccolumn("dynamic_dailysales_column", "key_name", "column_name");
        $data['paidoutdata'] = $this->common_model->getData($data, "paid_out_recap");
        $data['alldates'] = $this->common_model->get_dates($data['month'], $data['year'], "month");
        $data['paidoutamount'] = $this->common_model->getPaidAmount($data);
        $data['invoice_uploaded'] = $this->common_model->getInvoiceUpload($data);
        echo $this->load->view('common/paidout_grid', $data, TRUE);
        exit();
    }

    public function getCardrecapGrid() {
        $data = $this->getPostData();
        $data['dynamic_column'] = $this->common_model->getDynamiccolumn("dynamic_cardrecap_column", "key_name", "key_label");
        $data['cardrecapdata'] = $this->common_model->getDatewiseData($data, "card_recap");
        $data['alldates'] = $this->common_model->get_dates($data['month'], $data['year'], "month");
        echo $this->load->view('common/cardrecap_grid', $data, TRUE);
        exit();
    }
    public function getDeliveryrecapGrid() {
        $data = $this->getPostData();
        $data['dynamic_column'] = $this->common_model->getDynamiccolumn("dynamic_deliveryrecap_column", "key_name", "key_label");
        $data['deliveryrecapdata'] = $this->common_model->getDatewiseData($data, "delivery_recap");
        $data['alldates'] = $this->common_model->get_dates($data['month'], $data['year'], "month");
        echo $this->load->view('common/deliveryrecap_grid', $data, TRUE);
        exit();
    }

    public function getMonthlyrecapGrid() {
        $data = $this->getPostData();
        $data['dynamic_column'] = $this->common_model->getDynamiccolumn("dynamic_monthlyrecap_column", "key_name", "key_label");
        $data['monthlydata'] = $this->common_model->getDatewiseData($data, "monthly_recap");
        $data['alldates'] = $this->common_model->get_dates($data['month'], $data['year'], "month");
        echo $this->load->view('common/monthlyrecap_grid', $data, TRUE);
        exit();
    }

    public function getPayrollGrid() {
        $data = $this->getPostData();
        $data['dynamic_column'] = $this->common_model->getDynamiccolumn("dynamic_payroll_column", "key_name", "key_label");
        // $current_date = date("Y-m-d", strtotime("first day of this ".$month));
        $current_date = date($data['year'] . '-' . $data['month'] . '-' . '01');
        $alldates = getWeekStartingEndingDateFromMonth($current_date);
        $data['payroll'] = $this->common_model->getPayroll($data, $alldates);
        $data['start_date'] = array_column($alldates, 'start_of_week');
        $data['end_date'] = array_column($alldates, 'end_of_week');
        echo $this->load->view('common/payroll_grid', $data, TRUE);
        exit();
    }

    public function getDonutcountGrid() {
        $data = $this->getPostData();
        $data['dynamic_column'] = $this->common_model->getDonutDynamiccolumn("dynamic_donutcount_column");
        $donutData = $this->common_model->getDonutCount($data);
        $data['donutdata'] = $donutData['data'];
        $data['alldates'] = $this->common_model->get_dates($data['month'], $data['year'], "month");
        echo $this->load->view('common/donutcount_grid', $data, TRUE);
        exit();
    }

    public function addpaidout() {
        $data = $this->getPostData();
        $single_row = $this->input->post('single_row');
        $result = $this->common_model->adddailysales($data, $single_row = 1);
        $this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Paid out data added successfully!!!</div>');
        // redirect('common/?tab=paid-out-recap');
        echo $result;
        exit;
    }

    public function getAttachmentmodal() {
        $data = $this->getPostData();
        $data['uploaded_attachments'] = $this->common_model->getuploadattachment($data);
        echo $this->load->view('modal/dailysales_modal', $data, TRUE);
        exit();
    }

    public function setuploadAttachments() {
        $data = $this->getPostData();
        $result = $this->common_model->adduploadattachment($data);
        echo $result;
        exit();
    }

    public function addactualdeposit() {
        $single_row = $this->input->post('single_row');
        if ($single_row == 1) {
            $up_data['actual_bank_deposit'] = $this->input->post('value');
            $up_data['actual_over_shot'] = $this->input->post('actual_over_Shot');
            $result = $this->common_model->Edit(array("id" => $this->input->post('id')), $up_data, "monthly_recap");
        } else {
            $data = $this->getPostData();
            $actual_deposit = json_decode($data['data'], true);
            $result = $this->common_model->addactualdeposit($data, $actual_deposit);
        }

        echo $result;
        exit();
    }

    public function getUploadAttachmentmodal() {
        $data = $this->getPostData();
        $data['uploaded_attachments'] = $this->common_model->getuploadattachment($data);
        // print_r($data);exit;
        echo $this->load->view('modal/showattachment_modal', $data, TRUE);
        exit();
    }

    public function deleteAttachment() {
        $data = $this->getPostData();
        $result = $this->common_model->deleteattachment($data);
        echo $result;
        exit;
    }
    function addcharityData(){
     
        $key = $this->input->post('key');
        $actual_over_Shot = $this->input->post('final_value');
        $is_checked = $this->input->post('is_checked');
      
        $upData['actual_over_Shot'] = $actual_over_Shot;
        $upData['is_'.$key] = $is_checked;
        
        
        $result = $this->common_model->Edit(array("id" => $this->input->post('id')), $upData, "monthly_recap");
        echo $result;
        exit;
        
    }
    function LockUnlockEntry(){
        $tableName = $this->input->post('type');
        $id = $this->input->post('id');
        $data['is_lock'] = $this->input->post('is_lock') == 1 ? 0 : 1;
        $this->common_model->updateData($tableName, $id, $data);
        echo "Record updated successfully!";
        exit;
    }

    public function history() {
        $this->load->model('file_history_model');
        $data['title'] = 'File History';
        $data['file_history'] = $this->file_history_model->Get();
        $data['file_types'] = $this->file_history_model->getFileTypes();
        $this->template->load('listing', 'common/file_history', $data);
    }

    public function history_revert() {
        redirect('/common/history');
    }

    public function history_download($fileId = false,$type="pos") {
        $this->load->model('file_history_model');
        $file = $this->file_history_model->Get($fileId);
        if ($file) {
            if(strtolower($type) == 'fail')
                $file_path = $file->failure_file_path;
            else
                $file_path = $file->file_path;

            if (file_exists($file_path)) {
                $this->load->helper('download');
                force_download($file_path, file_get_contents($file_path));
            }
        }
        $this->session->set_flashdata('msg', '<div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Something went wrong. file is not downloaded.</div>');
        redirect('/common/history');
    }
}