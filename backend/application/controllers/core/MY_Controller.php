<?php (defined('BASEPATH')) OR exit('No direct script access allowed');
 
class MY_Controller extends CI_Controller {
    public $user_data;
    function __construct() {
        parent::__construct();
        if(!$this->session->userdata('username')) {
            redirect('login', 'redirect');
        } else {
            $this->load->model('user_model');
            $this->load->model('commodities_model');
            $this->load->model('depreciation_reminder_model');
            $this->user_data = $this->user_model->Get($this->session->userdata('user_id'));
            $commodities = $this->commodities_model->Get(NULL, array('mnemonic' => 'USD'));
            $default_currency_guid = isset($commodities['records'][0]->guid) ? $commodities['records'][0]->guid : "";
            $this->company_currency_guid = $this->session->userdata('currency_guid') != '' ? $this->session->userdata('currency_guid') : $default_currency_guid;
            $this->company_currency = $this->session->userdata('currency') != '' ? $this->session->userdata('currency') : "$";
            $this->company_currency_name = $this->session->userdata('currency_name') != '' ? $this->session->userdata('currency_name') : "US Dollars";
            $this->company_fraction = $this->session->userdata('fraction') != '' ? $this->session->userdata('fraction') : "100";
            $dep_reminder_details = $this->depreciation_reminder_model->Get(NULL, array('book_guid' => $this->session->userdata('company_id')));
            
            if(!empty($dep_reminder_details['records'])) {
                $dep_details = $dep_reminder_details['records'][0];
                
                if($dep_details->status == 'Active' || $dep_details->status == 'Sleep') {
                    // check session for notification
                    $dep_reminder = $this->session->userdata('depreciation_reminder_status');
                    
                    if($dep_reminder == '') {
                        // set notification
                        $date_diff = date_diff(date_create($dep_details->reminder_date), date_create(date('Y-m-d H:i:s')));
                        $diff = '';
                        if(!empty($date_diff)) {
                            if($date_diff->days == 0) {
                               $diff =  $date_diff->h . ' hours'; 
                            } else {
                               $diff =  $date_diff->days . ' days';  
                            }
                        }
                        $notifiction = array('date_diff' => $diff, 'text' => 'Depreciation Reminder');
                        $this->notifications = array($notifiction);
                    }
                }
            }
            
            if(!$this->session->userdata('company_id')) {
                if($this->router->fetch_class() != 'company') {
                    $this->load->model('company_model');
                    $companies = $this->company_model->Get(NULL, array('user_guid' => $this->user_data->guid));
                    if($companies['countFiltered'] == 0) {
                        redirect('company/add', 'redirect');
                    } elseif($companies['countFiltered'] == 1) {
                        $this->session->set_userdata(array('currency' =>$companies['records'][0]->symbol,  'currency_name' => $companies['records'][0]->fullname,'company_id'  => $companies['records'][0]->guid, 'company_name'  => $companies['records'][0]->name, 'account_id' => $companies['records'][0]->account_guid));
                    } else {
                        redirect('company/clist', 'redirect');
                    }
                }
            }
        }
    }
}