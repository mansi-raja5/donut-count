<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Reports extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->model('bank_statement_model');
        $this->load->model('ledger_statement_model');
        $this->load->model('bank_statement_entries_model');
        $this->load->model('store_master_model');
    }

    public function ledger_report() {

        $search = array();
        if (isset($_POST) && !empty($_POST)) {
//             print_r($_POST);
//        exit;
            $search['from_date'] = $this->input->post('from_date');
            $search['to_date'] = $this->input->post('to_date');
            $search['ledger_desc'] = $this->input->post('ledger_desc');
            $search['bank_desc'] = $this->input->post('bank_desc');
            $search['is_reconcile'] = $this->input->post('is_reconcile');
            $search['reconcile_type'] = $this->input->post('reconcile_type');
            $data['reports_data'] = $this->ledger_statement_model->Get_report_data($search);
//            echo "<pre>";
//            print_r($data['reports_data']);
//            exit;
        }


     
        $data['title'] = 'Ledger Report';
        $this->template->load('listing', 'ledger_report', $data);
    }
    public function sales_comparision_report(){
        $data['title'] = 'Sales Comparision Report';
        $this->template->load('listing', 'sales_tax_report', $data);
    }

}
