<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Expenses extends MY_Controller {

    private $tx_log_id;

    function __construct() {
        parent::__construct();
        $this->load->model('accounts_model');
        $this->load->model('account_map_model');
        $this->load->model('account_stype_model');
        $this->load->model('account_type_model');
        $this->load->model('Phonebook_model');
        $this->load->model('TitleList_model');
        $this->load->model('SuffixList_model');
        $this->load->model('Phonetype_model');
        $this->load->model('product_model');
        $this->load->model('terms_master_model');
        $this->load->model('Phonenumbers_model');
        $this->load->model('expenses_model');
        $this->load->model('expenses_item_model');
        $this->load->model('sys_default_accounts_model');
        $this->load->model('bill_payment_model');
        $this->load->model('splits_model');
        $this->load->model('company_docs_model');
        $this->load->model('project_model');
        $this->load->model('accounts_closing_model');
        $this->load->model('books_closing_model');
        $this->load->model('phonebook_payment_mapping_model');
        $this->company_guid = $this->session->userdata('company_id');
        $this->load->library('logfunction');
        $this->load->library('classifierfunction');
    }

    function index() {
        $data['title'] = 'Expenses';
        //$data['expenses_permission_record'] = $this->company_to_user_permissions_model->Get(NULL, array('module_name' => 'expenses', 'user_guid' => $this->session->userdata('user_id'), 'company_guid' => $this->session->userdata('company_id')));
        $data['has_add_bill_perm'] = true;
        $data['has_add_check_perm'] = true;
        $data['has_add_vendor_perm'] = true;
        $data['has_add_expense_perm'] = true;
        
        if(!empty($this->user_permissions)) {
            foreach($this->user_permissions as $permission) {
                if($permission->module_name == 'expenses') {
                    if ($permission->submodule_name == "expense") {
                        if (isset($permission->create_action) && $permission->create_action == 0) {
                            $data['has_add_expense_perm'] = false;
                        }
                    } else if ($permission->submodule_name == "bill") {
                        if (isset($permission->create_action) && $permission->create_action == 0) {
                            $data['has_add_bill_perm'] = false;
                        }
                    } else if ($permission->submodule_name == "vendor_credit") {
                        if (isset($permission->create_action) && $permission->create_action == 0) {
                            $data['has_add_vendor_perm'] = false;
                        }
                    } else if ($permission->submodule_name == "check") {
                        if (isset($permission->create_action) && $permission->create_action == 0) {
                            $data['has_add_check_perm'] = false;
                        }
                    }
                }
            }
        }

//        if (isset($data['expenses_permission_record']['records']) && !empty($data['expenses_permission_record']['records'])) {
//            foreach ($data['expenses_permission_record']['records'] as $permission) {
//                if ($permission->submodule_name == "expense") {
//                    if (isset($permission->create_action) && $permission->create_action == 0) {
//                        $data['has_add_expense_perm'] = false;
//                    }
//                } else if ($permission->submodule_name == "bill") {
//                    if (isset($permission->create_action) && $permission->create_action == 0) {
//                        $data['has_add_bill_perm'] = false;
//                    }
//                } else if ($permission->submodule_name == "vendor_credit") {
//                    if (isset($permission->create_action) && $permission->create_action == 0) {
//                        $data['has_add_vendor_perm'] = false;
//                    }
//                } else if ($permission->submodule_name == "check") {
//                    if (isset($permission->create_action) && $permission->create_action == 0) {
//                        $data['has_add_check_perm'] = false;
//                    }
//                }
//            }
//        }

        if ($this->input->is_ajax_request()) {
            $_POST['company_guid'] = $this->session->userdata('company_id');
//            $search = array();
//            $search['company_guid'] = $this->session->userdata('company_id');
//            $search['order'] = $this->input->post('order');
//            $search['start'] = $this->input->post('start');
//            $search['length'] = $this->input->post('length');
            $expenses = $this->expenses_model->Get(NULL, $this->input->post());
            $this->getListing($expenses, $this->input->post());
        } else {
            //$expenses = $this->expenses_model->Get(NULL, array('company_guid' => $this->session->userdata('company_id')));
            //$data['invoice'] = $expenses;
            $data['expense_type'] = $this->input->get('expense_type');
            $data['overdue_Res'] = $this->splits_model->get_overdue_balance($this->session->userdata('company_id'), '', 'B');
            $data['total_Res'] = $this->splits_model->get_total_balance($this->session->userdata('company_id'), '', 'B');
            $data['paid_Res'] = $this->splits_model->get_paid_balance($this->session->userdata('company_id'), '', 'B');
            $data['paid_Res_Except_bill'] = $this->splits_model->get_paid_balance_except_bill($this->session->userdata('company_id'));
//            echo "<pre>";
//            print_r($data['paid_Res_Except_bill']);
//            exit;

            $data_a = array();
            $data['no_unprinted_check'] = 0;
            $accounts = $this->accounts_model->Get(NULL, array('company_guid' => $this->session->userdata('company_id'), 'is_bank' => 1));

            foreach ($accounts["records"] as $acc) {
                $data_a[] = $acc->guid;
            }

            if (!empty($data_a)) {

                $data_result = $this->splits_model->get_splits_check_details_register_check($data_a, array('printed' => 'unprinted'));

                if (isset($data_result['records']) && !empty($data_result['records'])) {
                    $data['no_unprinted_check'] = sizeof($data_result['records']);
                }
            }

            $this->template->load('listing', 'list-expense', $data);
        }
    }

    public function expense($guid = FALSE) {
        $dir = FCPATH . "/files_upload/user_docs/$this->company_guid/tmp";
        clear_temp($dir);
        $account_tree = $this->accounts_model->get_tree();
        $data['title'] = 'Expense Entry';
        $data['accounts_tree'] = $account_tree;
        $customer_data = $this->Phonebook_model->Get(NULL, $this->input->post(), NULL, $this->session->userdata('company_id'));
        $data['customer_data'] = $customer_data;
        $default_title_list = $this->TitleList_model->Get();
        $data['title_list'] = $default_title_list['records'];
        $default_phone_type = $this->Phonetype_model->Get();
        $data['phonetype'] = $default_phone_type['records'];
        $default_suffix_list = $this->SuffixList_model->Get();
        $data['suffix_list'] = $default_suffix_list['records'];
        $product_list = $this->product_model->Get_Expenses_product();
        $data['product_list'] = $product_list;
        $terms_master_list = $this->terms_master_model->Get(NULL, array('status' => 'A'));
        $data['terms_master_list'] = $terms_master_list['records'];

        $acc_types = $this->account_stype_model->Get(NULL, array("is_display_expenses" => 1));
        $allow_type = array();
        if (isset($acc_types['records']) && !empty($acc_types['records'])) {
            foreach ($acc_types['records'] as $row) {
                $allow_type[] = $row->guid;
            }
        }
        if (!empty($allow_type)) {
            $acc_res = $this->accounts_model->Get_category_accounts($allow_type);
        }

        $payment_date = $this->input->get('payment_date');
        $data['logs'] = $this->expenses_model->Get(NULL, array('expense_type' => 'Expense', 'company_guid' => $this->session->userdata('company_id'), 'logs_entry' => true));
        $data['is_entry_exist'] = 1;
        //if edit expenses

        if ($guid) {
            $this->load->model('splits_model');
            $data['expenses_res'] = $expenses_res = $this->expenses_model->Get($guid);
            if (isset($expenses_res) && !empty($expenses_res)) {

                $payment_date = $expenses_res->payment_date;
                $data['expenses_item_res'] = $expenses_item_res = $this->expenses_item_model->Get(NULL, array("expense_guid" => $guid));
                $data['expenses_account_res'] = $expenses_account_res = $this->splits_model->Get(NULL, array("expense_guid" => $guid, "balance_type" => "E"));
                $data['expenses_item_total'] = $this->expenses_item_model->expenses_item_total(array("expense_guid" => $guid));

//            if($close_date != '' && sizeof($data['expenses_res']) > 0) {
//                $post_date = date('m/d/Y', strtotime($data['expenses_res']->payment_date));
//                
//                if(strtotime($post_date) < strtotime($close_date)) {
////                    $this->session->set_flashdata('failure', 'This transaction is lock. Please unlock account before start edit.');
////                    redirect('expenses');
//                    $data['closed_transaction'] = 1;
//                } else {
//                    $data['closed_transaction'] = 0;
//                }
//            }

                $res = $this->splits_model->Get(NULL, array('expense_guid' => $expenses_res->guid, 'charge' => 1));

                if (isset($res['records']) && !empty($res['records'])) {
                    foreach ($res['records'] as $row) {
                        $acc_guid = $row->account_guid;
                    }
                }

                $tot_bal = 0;
                if (isset($acc_guid)) {
                    $res = $this->splits_model->Get(NULL, array('account_guid' => $acc_guid));
                    if (isset($res['records']) && !empty($res['records'])) {
                        foreach ($res['records'] as $row) {
                            $tot_bal += $row->value_num / $row->value_denom;
                        }
                    }
                }

                $balance = number_format($tot_bal, 2);
                $data['balance'] = $balance;

//              Attachment Files
                $split_res = $this->splits_model->Get(NULL, array('balance_type' => 'E', 'expense_guid' => $guid));
                if (isset($split_res['records']) && !empty($split_res['records'])) {
                    foreach ($split_res['records'] as $row) {
                        $tx_guid = $row->tx_guid;
                        break;
                    }
                    $docs_files = $this->company_docs_model->Get(NULL, array("transaction_guid" => $tx_guid));
                    $data['files'] = isset($docs_files['records']) ? $docs_files['records'] : array();
                    $data['expense_txns_guid'] = $tx_guid;
                }
            } else {
                $data['is_entry_exist'] = 0;
            }
            $pro_search_arr = array('p_company_guid' => $this->session->userdata('company_id'), "pb_guid" => $expenses_res->customer_guid);
            $data['projects'] = $this->project_model->Get(NULL, $pro_search_arr);
        }

        $closed_split = $this->input->get('closed_splits');
        $data['closed_splits'] = $closed_split;

        if ($closed_split == '1' && $payment_date != '') {
            $payment_date = date('Y-m-d', strtotime($payment_date));
            $book_details = $this->books_closing_model->getBookByJournalDate($payment_date);
            $data['book_closing_start_date'] = $book_details->start_date;
            $data['book_closing_end_date'] = $book_details->close_date;
        }

        $data['bank_accounts'] = isset($acc_res) ? $acc_res : array();
        $data['type_accounts'] = !empty($allow_type) ? implode(",", $allow_type) : "";

        $this->template->load('listing', 'expense_entry', $data);
    }

    public function bill($guid = FALSE) {
        $dir = FCPATH . "/files_upload/user_docs/$this->company_guid/tmp";
        clear_temp($dir);
        $account_tree = $this->accounts_model->get_tree();
        $data['title'] = 'Bill Entry';
        $data['accounts_tree'] = $account_tree;
        $customer_data = $this->Phonebook_model->Get(NULL, $this->input->post(), 'is_vendor', $this->session->userdata('company_id'));
        $data['customer_data'] = $customer_data;
        $default_title_list = $this->TitleList_model->Get();
        $data['title_list'] = $default_title_list['records'];
        $default_phone_type = $this->Phonetype_model->Get();
        $data['phonetype'] = $default_phone_type['records'];
        $default_suffix_list = $this->SuffixList_model->Get();
        $data['suffix_list'] = $default_suffix_list['records'];
        $product_list = $this->product_model->Get_Expenses_product();
        $data['product_list'] = $product_list;
        $terms_master_list = $this->terms_master_model->Get(NULL, array('status' => 'A'));
        $data['terms_master_list'] = $terms_master_list['records'];

        $payment_date = $this->input->get('payment_date');
        $data['logs'] = $this->expenses_model->Get(NULL, array('expense_type' => 'Bill', 'company_guid' => $this->session->userdata('company_id')));
        //if edit bill
        $data['is_entry_exist'] = 1;
        if ($guid) {
            $this->load->model('splits_model');
            $data['expenses_res'] = $expenses_res = $this->expenses_model->Get($guid);
            if (isset($expenses_res) && !empty($expenses_res)) {
                $payment_date = $expenses_res->payment_date;
                $data['expenses_item_res'] = $expenses_item_res = $this->expenses_item_model->Get(NULL, array("expense_guid" => $guid));
                $data['expenses_account_res'] = $expenses_account_res = $this->splits_model->Get(NULL, array("expense_guid" => $guid, "balance_type" => "E"));
                $data['expenses_item_total'] = $this->expenses_item_model->expenses_item_total(array("expense_guid" => $guid));

//            $close_date = $this->session->userdata('close_date');
//        
//            if($close_date != '' && sizeof($data['expenses_res']) > 0) {
//                $post_date = date('m/d/Y', strtotime($data['expenses_res']->payment_date));
//
//                if(strtotime($post_date) < strtotime($close_date)) {
//                    $this->session->set_flashdata('failure', 'This transaction is lock. Please unlock account before start edit.');
//                    redirect('expenses');
//                }
//            }
                $res = $this->splits_model->Get(NULL, array('expense_guid' => $expenses_res->guid, 'charge' => 1));
                if (isset($res['records']) && !empty($res['records'])) {
                    foreach ($res['records'] as $row) {
                        $acc_guid = $row->account_guid;
                    }
                }

                $res = $this->splits_model->Get(NULL, array('account_guid' => $acc_guid));
                $tot_bal = 0;

                if (isset($res['records']) && !empty($res['records'])) {
                    foreach ($res['records'] as $row) {
                        $tot_bal += $row->value_num / ($row->value_denom > 0) ? $row->value_denom : 100;
                    }
                }  

                $balance = number_format($tot_bal, 2);
                $data['balance'] = $balance;

//               Attachment Files
                $split_res = $this->splits_model->Get(NULL, array('balance_type' => 'E', 'expense_guid' => $guid));
                if (isset($split_res['records']) && !empty($split_res['records'])) {
                    foreach ($split_res['records'] as $row) {
                        $tx_guid = $row->tx_guid;
                        break;
                    }
                    $docs_files = $this->company_docs_model->Get(NULL, array("transaction_guid" => $tx_guid));
                    $data['files'] = isset($docs_files['records']) ? $docs_files['records'] : array();
                    $data['bill_txns_guid'] = $tx_guid;
                }
                $data['closed_splits'] = ($this->input->get('closed_splits')) ? $this->input->get('closed_splits') : 0;
            } else {
                $data['is_entry_exist'] = 0;
            }
        }

        $closed_split = $this->input->get('closed_splits');

        if ($closed_split == '1' && $payment_date != '') {
            $payment_date = date('Y-m-d', strtotime($payment_date));
            $book_details = $this->books_closing_model->getBookByJournalDate($payment_date);
            $data['closed_splits'] = '1';
            $data['book_closing_start_date'] = $book_details->start_date;
            $data['book_closing_end_date'] = $book_details->close_date;
        }

        $acc_types = $this->account_stype_model->Get(NULL, array("is_display_expenses" => 1));
        $allow_type = array();
        if (isset($acc_types['records']) && !empty($acc_types['records'])) {
            foreach ($acc_types['records'] as $row) {
                $allow_type[] = $row->guid;
            }
        }
        $pro_search_arr = array('p_company_guid' => $this->session->userdata('company_id'), 'status' => 1);
        $data['projects'] = $this->project_model->Get(NULL, $pro_search_arr);
        $data['type_accounts'] = !empty($allow_type) ? implode(",", $allow_type) : "";
        $this->template->load('listing', 'bill_entry', $data);
    }

    public function check($guid = FALSE) {
        $dir = FCPATH . "/files_upload/user_docs/$this->company_guid/tmp";
        clear_temp($dir);
        $account_tree = $this->accounts_model->get_tree();
        $data['title'] = 'Check Entry';
        $data['accounts_tree'] = $account_tree;
        $customer_data = $this->Phonebook_model->Get(NULL, $this->input->post(), NULL, $this->session->userdata('company_id'));
        $data['customer_data'] = $customer_data;
        $default_title_list = $this->TitleList_model->Get();
        $data['title_list'] = $default_title_list['records'];
        $default_phone_type = $this->Phonetype_model->Get();
        $data['phonetype'] = $default_phone_type['records'];
        $default_suffix_list = $this->SuffixList_model->Get();
        $data['suffix_list'] = $default_suffix_list['records'];
        $product_list = $this->product_model->Get_Expenses_product();
        $data['product_list'] = $product_list;
        $terms_master_list = $this->terms_master_model->Get(NULL, array('status' => 'A'));
        $data['terms_master_list'] = $terms_master_list['records'];

        $acc_types = $this->account_stype_model->Get(NULL, array("is_display_check" => 1));
        $allow_type = array();
        if (isset($acc_types['records']) && !empty($acc_types['records'])) {
            foreach ($acc_types['records'] as $row) {
                $allow_type[] = $row->guid;
            }
        }
        if (!empty($allow_type)) {
            $acc_res = $this->accounts_model->Get_category_accounts($allow_type);
        }

        $data['bank_accounts'] = isset($acc_res) ? $acc_res : array();
        $data['type_accounts'] = !empty($allow_type) ? implode(",", $allow_type) : "";
        $payment_date = $this->input->get('payment_date');
        $data['logs'] = $this->expenses_model->Get(NULL, array('expense_type' => 'Check', 'company_guid' => $this->session->userdata('company_id'), 'logs_entry' => true));

        //if edit check
        if ($guid) {
            $this->load->model('splits_model');
            $data['expenses_res'] = $expenses_res = $this->expenses_model->Get($guid);
            $payment_date = $expenses_res->payment_date;
            $data['expenses_item_res'] = $expenses_item_res = $this->expenses_item_model->Get(NULL, array("expense_guid" => $guid));
            $data['expenses_account_res'] = $expenses_account_res = $this->splits_model->Get(NULL, array("expense_guid" => $guid, "balance_type" => "E"));
//            $close_date = $this->session->userdata('close_date');
//        
//            if($close_date != '' && sizeof($data['expenses_res']) > 0) {
//                $post_date = date('m/d/Y', strtotime($data['expenses_res']->payment_date));
//
//                if(strtotime($post_date) < strtotime($close_date)) {
//                    $this->session->set_flashdata('failure', 'This transaction is lock. Please unlock account before start edit.');
//                    redirect('expenses');
//                }
//            }
            $res = $this->splits_model->Get(NULL, array('expense_guid' => $expenses_res->guid, 'charge' => 1));
            if (isset($res['records']) && !empty($res['records'])) {
                foreach ($res['records'] as $row) {
                    $acc_guid = $row->account_guid;
                }
            }
            if (isset($acc_guid) && $acc_guid != '') {
                $this->load->model('account_bank_info_model');
                $get_bank_info = $this->account_bank_info_model->Get($acc_guid);
                $data['check_no'] = isset($get_bank_info->chk_next_check_number) ? $get_bank_info->chk_next_check_number : "";
                $res = $this->splits_model->Get(NULL, array('account_guid' => $acc_guid));
            }

            
            $tot_bal = 0;
            if (isset($res['records']) && !empty($res['records'])) {
                foreach ($res['records'] as $row) {
                    $tot_bal += $row->value_num / $row->value_denom;
                }
            }
            $balance = number_format($tot_bal, 2);
            $data['balance'] = $balance;
            //Attachment Files
            $split_res = $this->splits_model->Get(NULL, array('balance_type' => 'E', 'expense_guid' => $guid));
            if (isset($split_res['records']) && !empty($split_res['records'])) {
                foreach ($split_res['records'] as $row) {
                    $tx_guid = $row->tx_guid;
                    break;
                }
                $docs_files = $this->company_docs_model->Get(NULL, array("transaction_guid" => $tx_guid));
                $data['files'] = isset($docs_files['records']) ? $docs_files['records'] : array();
                $data['check_txns_guid'] = $tx_guid;
            }
            $pro_search_arr = array('p_company_guid' => $this->session->userdata('company_id'), "pb_guid" => $expenses_res->customer_guid);
            $data['projects'] = $this->project_model->Get(NULL, $pro_search_arr);
            // $data['closed_splits'] = ($this->input->get('closed_splits')) ? $this->input->get('closed_splits') : 0;
        }
        $closed_split = $this->input->get('closed_splits');

        if ($closed_split == '1' && $payment_date != '') {
            $payment_date = date('Y-m-d', strtotime($payment_date));
            $book_details = $this->books_closing_model->getBookByJournalDate($payment_date);
            $data['closed_splits'] = '1';
            $data['book_closing_start_date'] = $book_details->start_date;
            $data['book_closing_end_date'] = $book_details->close_date;
        }


        $this->template->load('listing', 'check_entry', $data);
    }

    public function vendor_credit($guid = FALSE) {
        $dir = FCPATH . "/files_upload/user_docs/$this->company_guid/tmp";
        clear_temp($dir);
        $account_tree = $this->accounts_model->get_tree();
        $data['title'] = 'Vendor Credit';
        $data['accounts_tree'] = $account_tree;
        $customer_data = $this->Phonebook_model->Get(NULL, $this->input->post(), 'is_vendor', $this->session->userdata('company_id'));
        $data['customer_data'] = $customer_data;
        $default_title_list = $this->TitleList_model->Get();
        $data['title_list'] = $default_title_list['records'];
        $default_phone_type = $this->Phonetype_model->Get();
        $data['phonetype'] = $default_phone_type['records'];
        $default_suffix_list = $this->SuffixList_model->Get();
        $data['suffix_list'] = $default_suffix_list['records'];
        $product_list = $this->product_model->Get_Expenses_product();
        $data['product_list'] = $product_list;
        $terms_master_list = $this->terms_master_model->Get(NULL, array('status' => 'A'));
        $data['terms_master_list'] = $terms_master_list['records'];
        $payment_date = $this->input->get('payment_date');
        $data['logs'] = $this->expenses_model->Get(NULL, array('expense_type' => 'Credit', 'company_guid' => $this->session->userdata('company_id')));
        //if edit credit entry
        if ($guid) {
            $this->load->model('splits_model');
            $data['expenses_res'] = $expenses_res = $this->expenses_model->Get($guid);
            $payment_date = $expenses_res->payment_date;
            $data['expenses_item_res'] = $expenses_item_res = $this->expenses_item_model->Get(NULL, array("expense_guid" => $guid));
            $data['expenses_account_res'] = $expenses_account_res = $this->splits_model->Get(NULL, array("expense_guid" => $guid, "balance_type" => "E"));
            $data['expenses_item_total'] = $this->expenses_item_model->expenses_item_total(array("expense_guid" => $guid));
            $res = $this->splits_model->Get(NULL, array('expense_guid' => $expenses_res->guid, 'payment' => 1));
//            $res = $this->splits_model->Get(NULL, array('expense_guid' => $expenses_res->guid));

            $acc_guid = '';
            if (isset($res['records']) && !empty($res['records'])) {
                foreach ($res['records'] as $row) {
                    $acc_guid = $row->account_guid;
                }
            }

            $res = $this->splits_model->Get(NULL, array('account_guid' => $acc_guid));
            $tot_bal = 0;
            if (isset($res['records']) && !empty($res['records'])) {
                foreach ($res['records'] as $row) {
                    $tot_bal += $row->value_num / $row->value_denom;
                }
            }

            $balance = number_format($tot_bal, 2);
            $data['balance'] = $balance;
            //Attachment Files
            $split_res = $this->splits_model->Get(NULL, array('balance_type' => 'E', 'expense_guid' => $guid));
            if (isset($split_res['records']) && !empty($split_res['records'])) {
                foreach ($split_res['records'] as $row) {
                    $tx_guid = $row->tx_guid;
                    break;
                }
                $docs_files = $this->company_docs_model->Get(NULL, array("transaction_guid" => $tx_guid));
                $data['files'] = isset($docs_files['records']) ? $docs_files['records'] : array();
                $data['credit_txns_guid'] = $tx_guid;
            }
            // $data['closed_splits'] = ($this->input->get('closed_splits')) ? $this->input->get('closed_splits') : 0;
        }

        $closed_split = $this->input->get('closed_splits');

        if ($closed_split == '1' && $payment_date != '') {
            $payment_date = date('Y-m-d', strtotime($payment_date));
            $book_details = $this->books_closing_model->getBookByJournalDate($payment_date);
            $data['closed_splits'] = '1';
            $data['book_closing_start_date'] = $book_details->start_date;
            $data['book_closing_end_date'] = $book_details->close_date;
        }

        $acc_types = $this->account_stype_model->Get(NULL, array("is_display_exp_vendor_credit_dr" => 1));
        $allow_type = array();
        if (isset($acc_types['records']) && !empty($acc_types['records'])) {
            foreach ($acc_types['records'] as $row) {
                $allow_type[] = $row->guid;
            }
        }
        if (!empty($allow_type)) {
            $acc_res = $this->accounts_model->Get_category_accounts($allow_type);
        }
        $data['credit_acc'] = $acc_res;
        $data['type_accounts'] = !empty($allow_type) ? implode(",", $allow_type) : "";
        $pro_search_arr = array('p_company_guid' => $this->session->userdata('company_id'), "status" => 1);
        $data['projects'] = $this->project_model->Get(NULL, $pro_search_arr);

        $this->template->load('listing', 'vendor_credit_entry', $data);
    }

    public function bill_payment($guid = FALSE, $bill_payment_guid = FALSE) {
        $account_tree = $this->accounts_model->get_tree();
        $data['title'] = 'Bill Payment Entry';
        $data['accounts_tree'] = $account_tree;
        $customer_data = $this->Phonebook_model->Get(NULL, $this->input->post(), 'is_vendor', $this->session->userdata('company_id'));
        $data['customer_data'] = $customer_data;
        $data['logs'] = $this->bill_payment_model->GetAllWithBillDetails($this->session->userdata('company_id'));

        //if edit bill
        if ($guid) {
            $this->load->model('splits_model');
            $data['expenses_res'] = $expenses_res = $this->expenses_model->Get($guid);
            $data['bill_res'] = $this->bill_payment_model->Get(NULL, array('bill_guid' => $guid));

            if (isset($data['bill_res']['records']) && !empty($data['bill_res']['records'])) {
                $data['bill_res'] = $payment_res = $data['bill_res']['records'][0];
            }

            $data['bill_payment_res'] = array();

            if ($bill_payment_guid) {
                $data['bill_payment_res'] = $this->bill_payment_model->Get($bill_payment_guid);
            }
            $data['bill_payment_transaction'] = $this->splits_model->Get(NULL, array("expense_guid" => $guid, "balance_type" => "P", "payment" => 1));

            if (isset($payment_res) && !empty($payment_res)) {
                $res = $this->splits_model->Get(NULL, array('account_guid' => $payment_res->bill_credit_acc_guid));
            }

            $tot_bal = 0;
            if (isset($res['records']) && !empty($res['records'])) {
                foreach ($res['records'] as $row) {
                    $tot_bal += $row->value_num / $row->value_denom;
                }
            }

            $balance = number_format($tot_bal, 2);
            $data['balance'] = $balance;

            if (is_dir('files_upload/expenses_documents/' . $guid)) {
                $files = scandir(FCPATH . "/files_upload/expenses_documents/$guid/");
                foreach ($files as $file) {
                    if (in_array($file, array(".", "..")))
                        continue;
                    $expenses_files[] = $file;
                }

                $data['files'] = isset($expenses_files) && !empty($expenses_files) ? $expenses_files : array();
            }

            $acc_types = $this->account_stype_model->Get(NULL, array("is_display_exp_bill_payment_dr" => 1));
            $allow_type = array();
            if (isset($acc_types['records']) && !empty($acc_types['records'])) {
                foreach ($acc_types['records'] as $row) {
                    $allow_type[] = $row->guid;
                }
            }

            if (!empty($allow_type)) {
                $acc_res = $this->accounts_model->Get_category_accounts($allow_type);
            }

            $data['credit_acc'] = $acc_res;
            $data['type_accounts'] = !empty($allow_type) ? implode(",", $allow_type) : "";
        }
        $this->template->load('listing', 'bill_payment', $data);
    }

    public function vendors() {
        if ($this->input->is_ajax_request()) {
            $data = $this->Phonebook_model->Get(NULL, $this->input->post(), 'is_vendor', $this->session->userdata('company_id'));
            $this->get_VendorListing($data);
        } else {
            $category = $this->input->get('cat');
            $data['overdue_Res'] = $this->splits_model->get_overdue_balance($this->session->userdata('company_id'), '', 'B');
            $data['total_Res'] = $this->splits_model->get_total_balance($this->session->userdata('company_id'), '', 'B');
            $data['paid_Res'] = $this->splits_model->get_paid_balance($this->session->userdata('company_id'), '', 'B');
            $data['category'] = $category;
            $data['title'] = "Vendor Contacts";
            $this->template->load('listing', 'listcontacts', $data);
        }
    }

    public function get_VendorListing($result = array()) {
        $tableData = array();
        foreach ($result['records'] as $key => $row) {
            $where = array('phonebook_guid' => $row->guid);
            $phone_number = $this->Phonenumbers_model->GetFromField($where);
            $name = $row->name_f . " " . $row->name_l;
            if ($row->name_f == "" && $row->name_l == "") {
                $name = $row->name_display_as;
            }
            $phone_number_text = '';

            if (isset($phone_number['records']) && !empty($phone_number['records'])) {
                foreach ($phone_number['records'] as $ph_no) {
                    $phone_number_text .= $ph_no->phone_num . '<br/>';
                }
            }

            $tableData[$key]['name'] = '<a href="' . base_url() . 'contacts/view/' . $row->guid . '">' . $name . '</a>';
            $tableData[$key]['phone'] = $phone_number_text;
//            $tableData[$key]['phone2'] = (!empty($phone_number['records']) ? (count($phone_number['records']) >= 2 ? $phone_number['records'][1]->phone_num : '') : '');
            $tableData[$key]['address'] = $row->phys_addr1 . " " . $row->phys_addr2;
            $tableData[$key]['city'] = $row->phys_city;
            $tableData[$key]['state'] = $row->phys_state;
            // $tableData[$key]['call_time'] = "";
            $tableData[$key]['guid'] = $row->guid;
        }
        $data['recordsTotal'] = $result['countTotal'];
        $data['recordsFiltered'] = $result['countFiltered'];
        $data['data'] = $tableData;

        echo json_encode($data);
    }

    public function getListing($result = array(), $post_data) {
        $have_expense_edit_permission = true;
        $have_print_permission = true;
        
        if(!empty($this->user_permissions)) {
            foreach($this->user_permissions as $permission) {
                if($permission->module_name == 'expenses') {
                    if ($permission->submodule_name == "expense") {
                        if (isset($permission->edit_action) && $permission->edit_action == 0) {
                            $have_expense_edit_permission = false;
                        }
                    } else if ($permission->submodule_name == "check") {
                        if (isset($permission->view_action) && $permission->view_action == 0) {
                            $have_print_permission = false;
                        }
                    }
                }
            }
        }

//        if (isset($expenses_permission_record['records']) && !empty($expenses_permission_record['records'])) {
//            foreach ($expenses_permission_record['records'] as $permission) {
//                if ($permission->submodule_name == "expense") {
//                    if (isset($permission->edit_action) && $permission->edit_action == 0) {
//                        $have_expense_edit_permission = false;
//                    }
//                } else if ($permission->submodule_name == "check") {
//                    if (isset($permission->view_action) && $permission->view_action == 0) {
//                        $have_print_permission = false;
//                    }
//                }
//            }
//        }

        $tableData = array();
        $tot_balance = 0;
        $close_date = $this->session->userdata('close_date');
        $count = 0;
        $expense_type = $post_data['expense_type'];
        foreach ($result['records'] as $key => $row) {
            $check_no = '';
            $action = array();
            $bill_amount = 0;
            if ($row->expense_type == "Expense") {
                $date = $row->payment_date;
                $type = 'Expense';
                if ($close_date != '' && $date != '') {
                    $post_date = date('m/d/Y', strtotime($date));
                    if ($have_expense_edit_permission) {
                        if (strtotime($post_date) < strtotime($close_date)) {
                            $action[] = '<a data-type="expense" data-guid="' . $row->guid . '" data-post-date="' . $post_date . '" onclick="return open_password_prompt(this)" >Edit</a>';
                        } else {
                            $action[] = anchor('expenses/expense/' . $row->guid, 'Edit');
                        }
                    }
                } else {
                    if ($have_expense_edit_permission) {
                        $action[] = anchor('expenses/expense/' . $row->guid, 'Edit');
                    }
                }
            }

            if ($row->expense_type == "Bill") {
                $date = $row->bill_date;
                $type = 'Bill';
                $paid = $total_payment = 0;
                $payment_res = $this->splits_model->Get(NULL, array("expense_guid" => $row->guid, "balance_type" => 'P', "payment" => 1));
                if (isset($payment_res['records']) && !empty($payment_res['records'])) {

                    foreach ($payment_res['records'] as $pRow) {
                        $total_payment += ($pRow->value_num / $pRow->value_denom);
                    }
                    if ($total_payment == $row->amount) {
                        $paid = 1;
                    }
                }
                if ($close_date != '' && $date != '') {
                    $post_date = date('m/d/Y', strtotime($date));

                    if (strtotime($post_date) < strtotime($close_date)) {
                        $action[] = '<a data-type="bill" data-guid="' . $row->guid . '" data-post-date="' . $post_date . '" onclick="return open_password_prompt(this)" >Edit</a>';
                    } else {
                        $action[] = anchor('expenses/bill/' . $row->guid, 'Edit');
                    }
                } else {
                    $action[] = anchor('expenses/bill/' . $row->guid, 'Edit');
                }
                if ($paid == 0) {
                    $action[] = anchor('expenses/bill_payment/' . $row->guid, 'Make Payment');
                } else {
                    $action[] = '<a onclick="return confirm_unpay_bill(this); " data-id="' . $row->guid . '">Unpay</a>';
                }
                $bill_amount = $row->amount - $total_payment;

                if ($expense_type != 'Bill') {
                    $bill_payments = $this->bill_payment_model->GetWithSplit($row->guid, $post_data);
                }
            }

            if ($row->expense_type == "Credit") {
                $date = $row->bill_date;
                $type = 'Credit';
                if ($close_date != '' && $date != '') {
                    $post_date = date('m/d/Y', strtotime($date));

                    if (strtotime($post_date) < strtotime($close_date)) {
                        $action[] = '<a data-type="vendor_credit" data-guid="' . $row->guid . '" data-post-date="' . $post_date . '" onclick="return open_password_prompt(this)" >Edit</a>';
                    } else {
                        $action[] = anchor('expenses/vendor_credit/' . $row->guid, 'Edit');
                    }
                } else {
                    $action[] = anchor('expenses/vendor_credit/' . $row->guid, 'Edit');
                }
            }

            if ($row->expense_type == "Check") {
                $date = $row->payment_date;
                $type = 'Check';
                $check_no = $row->expense_no;
                // $action[] = anchor('expenses/check/' . $row->guid, 'Edit');
                if ($close_date != '' && $date != '') {
                    $post_date = date('m/d/Y', strtotime($date));

                    if (strtotime($post_date) < strtotime($close_date)) {
                        $action[] = '<a data-type="check" data-guid="' . $row->guid . '" data-post-date="' . $post_date . '" onclick="return open_password_prompt(this)" >Edit</a>';
                    } else {
                        $action[] = anchor('expenses/check/' . $row->guid, 'Edit');
                    }
                } else {
                    $action[] = anchor('expenses/check/' . $row->guid, 'Edit');
                }

                if ($have_print_permission) {
                    if ($row->print_later == 1 && $row->check_printed == 0 && $row->is_bank == 1) {
                        $action[] = '<a href="javascript:void(0);" id="print_check" data-acc_id ="' . $row->aguid . '" data-expens_id ="' . $row->guid . '" data-check-no="' . $check_no . '">Print Check</a>';
                    } else {
                        $action[] = '<a href="javascript:void(0);" id="reprint_check" data-acc_id ="' . $row->aguid . '" data-expens_id ="' . $row->guid . '" data-check-no="' . $check_no . '">Reprint Check</a>';
                    }
                }
            }

            if ($expense_type != 'Bill Payment') {
                $check_no = $row->expense_no;
                $tableData[$count]['srNo'] = $count + 1;
                $tableData[$count]['date'] = date('m-d-Y', strtotime($date));
                $tableData[$count]['type'] = $type;
                $tableData[$count]['check_no'] = $check_no;
                $tableData[$count]['payee'] = $row->payee;
                $tableData[$count]['total'] = number_format($row->amount, 2);

                if ($row->expense_type == 'Check' || $row->expense_type == 'Expense') {
                    $tableData[$count]['balance'] = '';
                } else {
                    $tableData[$count]['balance'] = $row->expense_type == 'Bill' ? number_format($bill_amount, 2) : $row->amount;
                }

                $tableData[$count]['guid'] = $row->guid;
                $tableData[$count]['action'] = implode(' | ', $action);
                $tot_balance += $row->amount;
            }

            if (isset($bill_payments) && !empty($bill_payments) && $row->expense_type == "Bill") {
                if ($expense_type != 'Bill Payment') {
                    ++$count;
                }
                foreach ($bill_payments as $bill) {
                    $tableData[$count]['srNo'] = $count + 1;
                    $tableData[$count]['date'] = date('m-d-Y', strtotime($bill['bill_payment_date']));
                    $tableData[$count]['type'] = 'Bill Payment';
                    $tableData[$count]['check_no'] = '';
                    $tableData[$count]['payee'] = $bill['payee'];
                    $tableData[$count]['total'] = number_format($bill['payment_amount'], 2);
                    $tableData[$count]['balance'] = '';
                    $tableData[$count]['guid'] = $bill['bill_guid'];
                    $action = array();
                    $action[] = anchor(base_url('expenses/bill_payment_view/' . $bill['payment_id']), 'View', array("target" => "_blank"));
                    $action[] = anchor(base_url('expenses/bill_payment/' . $bill['bill_id'] . '/' . $bill['payment_id']), 'Edit');
                    $action[] = '<a data-toggle="modal" data-target="#confirmModal"onclick="return confirm_delete_bill_payment(this); " data-id="' . $bill['payment_id'] . '">Delete</a>';
                    $tableData[$count]['action'] = implode(' | ', $action);
                    // $tableData[$count]['action'] = anchor(base_url('expenses/bill_payment_view/' . $bill['payment_id']), 'View', array("target" => "_blank"));
                    $count++;
                }
            } else {
                if ($expense_type != 'Bill Payment') {
                    $count++;
                }
            }
        }

        $data['recordsTotal'] = $result['countTotal'];
        $data['recordsFiltered'] = $result['countFiltered'];
        $data['tot_balance'] = $tot_balance;
        $data['data'] = $tableData;
        echo json_encode($data);
        exit;
    }

    public function get_account_balance() {
        $this->load->model('splits_model');
        $guid = $this->input->post('guid');
        $res = $this->splits_model->Get(NULL, array('account_guid' => $guid));
        $tot_bal = 0;

        if (isset($res['records']) && !empty($res['records'])) {
            foreach ($res['records'] as $row) {
                $tot_bal += $row->value_num / $row->value_denom;
            }
        }
//        $accoount_res = $this->accounts_model->Get($guid);
//        if ($accoount_res->normal_bal == "CREDIT" && $tot_bal < 0) {
//            $tot_bal = $tot_bal * -1;
//        }
        $balance = number_format($tot_bal, 2);
        echo json_encode(array('tot_bal' => $balance));
    }

    public function expense_entry_save() {
        $this->load->library('modulefunction');
        $return_data_Res = $this->modulefunction->expense_save($this->input->post());
        echo json_encode($return_data_Res);
        exit;
    }

    public function bill_entry_save() {
        $this->load->library('modulefunction');
        $return_data_Res = $this->modulefunction->bill_save($this->input->post());
        echo json_encode($return_data_Res);
        exit;
    }

    public function vendor_credit_save() {
        $this->load->library('modulefunction');
        $return_data_Res = $this->modulefunction->vendor_credit_save($this->input->post());
        echo json_encode($return_data_Res);
        exit;
    }

    public function check_entry_save() {
        $this->load->library('modulefunction');
        $return_data_Res = $this->modulefunction->check_save($this->input->post());
        echo json_encode($return_data_Res);
        exit;
    }

    public function bill_payment_save() {
        $remain_balance = str_replace(',', '', $this->input->post('remain_balance'));

        //Including validation library
        $this->load->library('form_validation');

        $this->form_validation->set_error_delimiters('<span class="help-block">', '</span>');

        //Validating Fields
        $rules[] = array('field' => 'payment', 'label' => 'Payment', 'rules' => 'required|callback_bill_amount_check');

        $this->form_validation->set_rules($rules);
        $bill_payment_id = $this->input->post('bill_payment_id');

        if ($this->form_validation->run() == FALSE) {
            if ($bill_payment_id != '') {
                $this->bill_payment($this->input->post('expense_guid', true), $bill_payment_id);
            } else {
                $this->bill_payment($this->input->post('expense_guid', true));
            }
        } else {
            $expense_guid = $this->input->post('expense_guid');
            $total_bill_amount = $this->input->post('invoice_amount');
            $paid_bill_amount = str_replace(',', '', $this->input->post('payment'));
            $bank_credit_account = $this->input->post('bank_credit_account');
            $mailing_address = $this->input->post('mailing_address');
            $bill_date = $this->input->post('bill_date');
            $bill_reference_no = $this->input->post('expense_no');
            $customer = $this->input->post('customer');

            $check_payment_exist = $this->bill_payment_model->Get($expense_guid);

            if ($total_bill_amount == $paid_bill_amount) {
                $status = 'paid';
            } else if ($check_payment_exist) {
                $status = 'closed';
            } else {
                $status = 'partial';
            }

            $payable_Acc = create_accounts("Liability:Accounts Payable");
            if ($bill_payment_id != '') {
                $txns_guid = $this->input->post('bill_transaction_guid');

                // get splits for transaction
                $split_res = $this->splits_model->Get(NULL, array('tx_guid' => $txns_guid, 'expense_guid' => $expense_guid));
                $balance_val = ($paid_bill_amount * $this->company_fraction);

                if (isset($split_res['records']) && !empty($split_res['records'])) {
                    foreach ($split_res['records'] as $split) {
                        if ($split->value_num < 0) {
                            // credit split
                            $splits_update_arr[] = array('guid' => $split->guid,
                                'tx_guid' => $txns_guid,
                                'account_guid' => $bank_credit_account,
                                'value_num' => $balance_val * -1,
                                'value_denom' => $this->company_fraction,
                                'quantity_num' => $balance_val * -1,
                                'quantity_denom' => $this->company_fraction
                            );
                        } else {
                            // debit split
                            $splits_update_arr[] = array('guid' => $split->guid,
                                'tx_guid' => $txns_guid,
                                'account_guid' => $payable_Acc,
                                'value_num' => $balance_val,
                                'value_denom' => $this->company_fraction,
                                'quantity_num' => $balance_val,
                                'quantity_denom' => $this->company_fraction
                            );
                        }
                    }

                    if (isset($splits_update_arr) && !empty($splits_update_arr)) {
                        $res = $this->splits_model->Update_Batch($splits_update_arr);
                    }
                }
                $this->classifierfunction->call_classifier_for_tx_update($txns_guid, true);
                
            //update the credit account guid in bill payment table
                  $payment_updata['bill_credit_acc_guid'] = $bank_credit_account;
                  $this->bill_payment_model->Edit($bill_payment_id, $payment_updata, '', $by_id = 1);
                
            } else {
                $txns_guid = $this->add_transaction($payable_Acc, $paid_bill_amount, $expense_guid, 'add', 'P', $customer, $bank_credit_account, "", $bill_date);
            }

            if ($bill_payment_id == '') {
                $data['bill_guid'] = $expense_guid;
                $data['created_on'] = date('Y-m-d H:i:s');
            }

            $data['bill_transaction_guid'] = $txns_guid;
            $data['bill_credit_acc_guid'] = $bank_credit_account;
            $data['bill_mailing_address'] = $mailing_address;
            $data['bill_payment_date'] = date("Y-m-d", strtotime($bill_date));
            $data['bill_reference_no'] = $bill_reference_no;

            if ($bill_payment_id == '') {
                $insert_id = $this->bill_payment_model->Add($data, isset($customer) ? $customer : "");
                $data['id'] = $insert_id;
            } else {
                $insert_id = $this->bill_payment_model->Edit($bill_payment_id, $data, isset($customer) ? $customer : "");
                $data['id'] = $bill_payment_id;
            }


            $this->session->set_flashdata('msg_class', "success");
            $this->session->set_flashdata('msg', "Bill payment entry has been saved successfully.");

            redirect('expenses');
        }
    }

    public function bill_amount_check($payment_amt) {
        $remain_balance = $this->input->post('remain_balance');

        if ($payment_amt != '' && $remain_balance < $payment_amt) {
            $this->form_validation->set_message('bill_amount_check', 'Please enter lesser amount to the current open balance');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function add_transaction($debit_acc_guid, $tot_price, $expenses_guid, $action, $type, $customer, $credit_acc_id = "", $check_no = "", $post_date = "", $txns_recurring_guid = NULL) {
        $this->load->model('splits_model');
        $this->load->model('transactions_model');
        $this->load->model('transaction_recurring_model');
        $payment_method = $this->input->post('payment_method');

        $splits_guid_arr = $product_split_guid_arr = $delete_splits_guids = array();

        if ($type != "P") {
            $parent_guid_arr = array_filter($this->input->post('parent_guid'));
            $splits_guid_arr = $this->input->post('splits_guid');
            $acc_amount_arr = array_filter($this->input->post('acc_amount'));
            $acc_description_arr = array_filter($this->input->post('acc_description'));

            $product_guid_arr = array_filter($this->input->post('product_guid'));
            $product_split_guid_arr = array_filter($this->input->post('p_splits_guid'));
            $pro_description_arr = array_filter($this->input->post('pro_description'));
            $pro_amount_arr = array_filter($this->input->post('pro_amount'));
        }

        if ($type == "V" || $type == "P") {
            $balance_val = ($tot_price * $this->company_fraction);
        } else {
            $balance_val = -($tot_price * $this->company_fraction);
        }

        if ($action == "add") {
            $data['currency_guid'] = $this->company_currency_guid;
            $data['num'] = $check_no;
            $data['post_date'] = ($post_date != '') ? date('Y-m-d', strtotime($post_date)) : date('Y-m-d');
            $data['enter_date'] = date('Y-m-d H:i:s');
            $data['description'] = "";
            $data['expense_guid'] = $expenses_guid;
            $data['transaction_recurring_guid'] = $txns_recurring_guid;

            if (is_null($txns_recurring_guid)) {
                $data['guid'] = md5(random_string('alnum', 16) . time());
                $tx_guid = $data['guid'];
                $tx_log_id = $this->transactions_model->Add($data);
            } else {
                $tx_guid = NULL;
                $recurr_data['transaction_data'] = serialize($data);
                $this->transaction_recurring_model->Edit($txns_recurring_guid, $recurr_data);
            }

            //add first split for Credit against the "Bank/Credit Account" chosen in the drop down
            $splits_log = array();
            $data = array();
            $data['tx_guid'] = $tx_guid;
            $data['account_guid'] = $debit_acc_guid;
            $data['reconcile_state'] = 'n';
            $data['reconcile_date'] = '0000-00-00 00:00:00';
            $data['value_num'] = $balance_val;
            $data['value_denom'] = $this->company_fraction;
            $data['quantity_num'] = $balance_val;
            $data['quantity_denom'] = $this->company_fraction;
            $data['balance_type'] = isset($type) && $type == 'P' ? 'P' : 'E';
            // $data['transaction_recurring_guid'] = $txns_recurring_guid;
            // bill payment or not check
            if ($type == "P" || $credit_acc_id != '') {
                $data['customer_guid'] = NULL;
            } else {
                $data['customer_guid'] = $customer;
            }

            if (is_null($txns_recurring_guid)) {
                $data['guid'] = md5(random_string('alnum', 16) . time());
                $data['expense_guid'] = $expenses_guid;
                if ($this->input->post('btn_type')) {
                    if ($this->input->post('btn_type') == "PrintCheck") {
                        $data['check_printed'] = 'P';
                    } else if ($this->input->post('btn_type') == "save" || $this->input->post('btn_type') == "save_new") {
                        $data['check_printed'] = 'U';
                    }
                }
                if ($payment_method == 'cheque') {
                    $data['check_printed'] = 'U';
                    if ($check_no != "") {
                        $data['num_check'] = $check_no;
                    }
                }
                $this->splits_model->Add($data);
            } else {
                $data['check_printed'] = 'U';
                if ($payment_method == 'cheque') {
                    $data['check_printed'] = 'U';
                    if ($check_no != "") {
                        $data['num_check'] = $check_no;
                    }
                }
                $recurr_data[] = $data;
            }
        }

        if ($action == "update") {
            $old_bank_acc_guid = $this->input->post('old_bank_credit_account');
            $old_balance = $this->input->post('old_balance');
            $split_res = $this->splits_model->Get(NULL, array('balance_type' => 'E', 'expense_guid' => $expenses_guid));

            if (isset($split_res['records']) && !empty($split_res['records'])) {
//                echo "<pre>";
//                print_r($split_res['records']);
//                echo "</pre>";

                foreach ($split_res['records'] as $row) {
                    if (($row->value_num / $row->value_denom) < 0 && $row->account_guid == $old_bank_acc_guid) {

                        $tx_guid = $row->tx_guid;
                        $bank_splits_guid = $splits_guid[] = $row->guid;
                        array_push($splits_guid_arr, $bank_splits_guid);
                    } else {
                        $tx_guid = $row->tx_guid;
                        $splits_guid[] = $row->guid;
                    }
                    if (!in_array($row->guid, $splits_guid_arr) && !in_array($row->guid, $product_split_guid_arr)) {
                        if (($type == 'V' || $type == 'P') && $credit_acc_id == $row->account_guid) {
                            $vendor_credit_guid = $row->guid;
                        } else if (($type == 'V' || $type == 'P') && $old_bank_acc_guid == $row->account_guid) {
                            $bank_splits_guid = $row->guid;
                        } else {
                            $delete_splits_guids[] = $row->guid;
                        }
                    }
                }

                if (isset($delete_splits_guids) && !empty($delete_splits_guids)) {
                    $this->splits_model->batchDelete(array('guid' => $delete_splits_guids));
                }
            }

            $data = array();
            $data['currency_guid'] = $this->company_currency_guid;
            $data['num'] = $check_no;
            $data['post_date'] = ($post_date != '') ? date('Y-m-d', strtotime($post_date)) : date('Y-m-d');
            $data['enter_date'] = date('Y-m-d H:i:s');
            $data['description'] = "";
            $data['expense_guid'] = $expenses_guid;
            $data['guid'] = $tx_guid;
            $tx_log_id = $this->logfunction->transactionLogRecord(35, serialize($data));

            $splits_log = array();
            $data = array();

            //add first split for Credit against the "Bank/Credit Account" chosen in the drop down
            $data['tx_guid'] = $tx_guid;
            $data['account_guid'] = $debit_acc_guid;
            $data['reconcile_state'] = 'n';
            $data['reconcile_date'] = '0000-00-00 00:00:00';
            $data['value_num'] = $balance_val;
            $data['value_denom'] = $this->company_fraction;
            $data['quantity_num'] = $balance_val;
            $data['quantity_denom'] = $this->company_fraction;
            $data['balance_type'] = isset($type) && $type == 'P' ? 'P' : 'E';
            $data['customer_guid'] = $customer;

            if ($this->input->post('btn_type')) {
                if ($this->input->post('btn_type') == "PrintCheck") {
                    $data['check_printed'] = 'P';
                } else if ($this->input->post('btn_type') == "save") {
                    $data['check_printed'] = 'U';
                }
            }

            if ($payment_method == 'cheque') {
                $data['check_printed'] = 'U';
                if ($check_no != "") {
                    $data['num_check'] = $check_no;
                }
            }

            $this->splits_model->Edit($bank_splits_guid, $data);


            $up_check_no['num'] = $check_no;
            $this->transactions_model->Edit($tx_guid, $up_check_no);
        }

        $splits_log[0] = $data;
        if ($type == "P") {
            //add first split for Credit against the "Bank/Credit Account" chosen in the drop down
            $data = array();
            $data['account_guid'] = $credit_acc_id;
            $data['reconcile_state'] = 'n';
            $data['reconcile_date'] = '0000-00-00 00:00:00';
            $data['value_num'] = $balance_val * -1;
            $data['value_denom'] = $this->company_fraction;
            $data['quantity_num'] = $balance_val * -1;
            $data['quantity_denom'] = $this->company_fraction;
            $data['balance_type'] = isset($type) && $type == 'P' ? 'P' : 'E';

//            $data['check_printed'] = 'N';
//            if ($payment_method == 'cheque') {
//                $data['check_printed'] = 'U';
//                if ($check_no != "") {
//                    $data['num_check'] = $check_no;
//                    $data['check_printed'] = 'N';
//                }
//            }

            $data['customer_guid'] = $customer;
            $data['expense_guid'] = $expenses_guid;
            $data['memo'] = "testing";
            if ($action == "add") {
                $data['guid'] = md5(random_string('alnum', 16) . time());
                $data['tx_guid'] = $tx_guid;
                $this->splits_model->Add($data);
            } else {
                $this->splits_model->Edit($vendor_credit_guid, $data);
            }
        } else {
            //add split for Debit against all Accounts chosen in the Account Details 
            if (isset($parent_guid_arr) && !empty($parent_guid_arr)) {
                for ($sp = 0; $sp < count($parent_guid_arr); $sp++) {
                    $acc_guid = $parent_guid_arr[$sp];
                    $amount = $acc_amount_arr[$sp];
                    $memo = isset($acc_description_arr[$sp]) ? $acc_description_arr[$sp] : "";

                    //check splits guid for entry is updated or add
                    $split_guid = isset($splits_guid_arr[$sp]) ? $splits_guid_arr[$sp] : "";

                    if ($acc_guid != '') {


                        $balance_val = ($amount * $this->company_fraction);
                        if ($type == 'V') {
                            $balance_val = $balance_val * -1;
                        }
                        $data = array();
                        $data['account_guid'] = $acc_guid;
                        $data['reconcile_state'] = 'n';
                        $data['reconcile_date'] = '0000-00-00 00:00:00';
                        $data['value_num'] = $balance_val;
                        $data['value_denom'] = $this->company_fraction;
                        $data['quantity_num'] = $balance_val;
                        $data['quantity_denom'] = $this->company_fraction;
                        $data['balance_type'] = 'E';
                        $data['memo'] = $memo;
//                        if ($type == 'E') {
//                            $data['check_printed'] = 'U';
//                        } else if ($this->input->post('print_later') == 1) {
//                            $data['check_printed'] = 'U';
//                        } else {
//                            $data['check_printed'] = 'P';
//                        }
//                        if ($check_no != "") {
//                            $data['num_check'] = $check_no;
//                            if ($this->input->post('btn_type')) {
//                                if ($this->input->post('btn_type') == "PrintCheck") {
//                                    $data['check_printed'] = 'P';
//                                } else if ($this->input->post('btn_type') == "save") {
//                                    $data['check_printed'] = 'U';
//                                }
//                            }
//                        }
                        if ($credit_acc_id != '') {
                            $data['customer_guid'] = $customer;
                        } else {
                            $data['customer_guid'] = NULL;
                        }

                        $data['expense_guid'] = $expenses_guid;
                        if ($split_guid != "") {
                            $this->splits_model->Edit($split_guid, $data);
                        } else {
                            if (is_null($txns_recurring_guid)) {
                                $data['guid'] = md5(random_string('alnum', 16) . time());
                                $data['tx_guid'] = $tx_guid;
                                $this->splits_model->Add($data);
                            } else {
                                $data['tx_guid'] = NULL;
                                $recurr_data[] = $data;
                            }
                        }
                    }
                }
            }

            //add split for Debit against all Accounts chosen for inventory of Product/Services chosen in Item Details
            if (isset($product_guid_arr) && !empty($product_guid_arr)) {
                for ($sp = 0; $sp < count($product_guid_arr); $sp++) {
                    $pro_guid = isset($product_guid_arr[$sp]) ? $product_guid_arr[$sp] : "";
                    $pro_desc = isset($pro_description_arr[$sp]) ? $pro_description_arr[$sp] : "";

                    $pro_Res = $this->product_model->Get($pro_guid);

                    if ($pro_Res->p_type == 'I') {
                        $pro_inventory_acc_id = $pro_Res->p_inventory_asset_acc_guid;
                    } else {
                        $pro_inventory_acc_id = $pro_Res->p_expense_acc_guid;
                    }
                    $amount = $pro_amount_arr[$sp];

                    //check splits guid for entry is updated or add
                    $psplit_guid = isset($product_split_guid_arr[$sp]) ? $product_split_guid_arr[$sp] : "";

                    if ($pro_inventory_acc_id != '') {
                        $balance_val = ($amount * $this->company_fraction);

                        if ($type == 'V') {
                            $balance_val = $balance_val * -1;
                        }
                        $data = array();

                        $data['account_guid'] = $pro_inventory_acc_id;
                        $data['reconcile_state'] = 'n';
                        $data['reconcile_date'] = '0000-00-00 00:00:00';
                        $data['value_num'] = $balance_val;
                        $data['value_denom'] = $this->company_fraction;
                        $data['quantity_num'] = $balance_val;
                        $data['quantity_denom'] = $this->company_fraction;
                        $data['balance_type'] = 'E';
                        $data['customer_guid'] = NULL;
                        $data['expense_guid'] = $expenses_guid;
                        $data['product_guid'] = $pro_guid;
                        $data['memo'] = $pro_desc;

                        if ($psplit_guid != "") {

                            $this->splits_model->Edit($psplit_guid, $data);
                        } else {
                            if (is_null($txns_recurring_guid)) {
                                $data['guid'] = md5(random_string('alnum', 16) . time());
                                $data['tx_guid'] = $tx_guid;
                                // $data['transaction_recurring_guid'] = $txns_recurring_guid;
                                $this->splits_model->Add($data);
                            } else {
                                $recurr_data[] = $data;
                            }
                        }
                    }
                }
            }
        }

        if (is_null($txns_recurring_guid)) {
            $splits_log[1] = $data;
            $this->logfunction->updatetransactionLogRecord($tx_log_id, $splits_log);
        } else {
            $split_data['split_data'] = isset($recurr_data) && !empty($recurr_data) ? serialize($recurr_data) : '';
            $this->transaction_recurring_model->Edit($txns_recurring_guid, $split_data);
        }
        $this->classifierfunction->call_classifier_for_tx_update($tx_guid, ($action == "update") ? true : false);
        return isset($tx_guid) ? $tx_guid : "";
    }

    public function account_check($str, $parent_guid = "") {
        $condition['name'] = $str;
        $condition['am.company_guid'] = $this->session->userdata('company_id');
        $condition['am.parent_guid'] = $parent_guid;
        $this->db->join('account_map am', 'am.guid=accounts.guid', 'INNER');
        $this->db->where($condition);
        $num_row = $this->db->get('accounts')->num_rows();

        if ($num_row >= 1) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function add_expense_item($e_guid, $txns_recurring_guid = NULL) {
        $this->load->model('transaction_recurring_model');
        //expense item entry
        $product_guid_arr = $this->input->post('product_guid');
        $selected_product_arr = $this->input->post('selected_product');
        $pro_description_arr = $this->input->post('pro_description');
        $qty_arr = $this->input->post('qty');
        $rate_arr = $this->input->post('rate');
        $pro_amount_arr = $this->input->post('pro_amount');
        $expense_guid = $this->input->post('expense_guid');

        if (isset($product_guid_arr) && !empty($product_guid_arr)) {
            for ($sp = 0; $sp < count($product_guid_arr); $sp++) {
                $description = $pro_description_arr[$sp];
                $pro_guid = $product_guid_arr[$sp];
                $qty = $qty_arr[$sp];
                $rate = $rate_arr[$sp];
                $pro_amount = $pro_amount_arr[$sp];
                if ($pro_guid != '') {
                    $item_arr[] = array(
                        'expense_guid' => $e_guid,
                        'item_guid' => $pro_guid,
                        'item_desc' => $description,
                        'item_qty' => $qty,
                        'item_rate' => $rate,
                        'item_amount' => $pro_amount,
                        'created_on' => date("Y-m-d H:i:s")
                    );
                }
            }
        }
        if ($expense_guid != '') {
            $item_res = $this->expenses_item_model->Delete_item_by_exguid($expense_guid);
        }
        if (!empty($item_arr)) {
            if (is_null($txns_recurring_guid)) {
                $res = $this->expenses_item_model->Add_Batch($item_arr);
            } else {
                $recurr_data['module_product_data'] = serialize($item_arr);
                $res = $this->transaction_recurring_model->Edit($txns_recurring_guid, $recurr_data);
            }
        }
    }

    public function generate_check_expens() {
        $expens_id = $this->input->post('expens_id');
        $acc_id = $this->input->post('acc_id');
        $check_choice = $this->input->post('check_choice');
        $check_no = $this->input->post('check_no');
        $data = array();
        if (empty($expens_id)) {
            $data['flag'] = false;
            $data['msg'] = 'Something went wrong !! Please <a href="javascript:void(0)" onclick="return location.reload();">Reload Page</a>';
        } else if (empty($acc_id)) {
            $data['flag'] = false;
            $data['msg'] = 'Something went wrong !! Please <a href="javascript:void(0)" onclick="return location.reload();">Reload Page</a>';
        } else {
            $this->load->model('account_bank_info_model');
            $this->load->model('expenses_model');
            $get_bank_info = $this->account_bank_info_model->Get($acc_id);
            if (!$get_bank_info) {
                $data['flag'] = false;
                $data['msg'] = 'Please Setup Bank Account First';
            } else {
                $data['flag'] = true;
                $data['msg'] = 'Success';
                $expens_res = $this->expenses_model->Get($expens_id);

                if ($expens_res) {
                    if ($this->input->post('check_choice')) {
                        if ($check_choice == 1) {
                            $chk_next_check_number = $check_no;
                        } else {
                            $chk_next_check_number = $get_bank_info->chk_next_check_number;
                        }
                    } else {
                        if ($this->input->post('check_no')) {
                            $chk_next_check_number = $check_no;
                        } else {
                            $chk_next_check_number = $get_bank_info->chk_next_check_number;
                        }
                    }

                    $data_splits = array();
                    $data_splits['check_printed'] = 'P';
                    $data_splits['printed_date'] = date("Y-m-d");
                    $data_splits['num_check'] = $chk_next_check_number;
                    $this->splits_model->EditForCheck($expens_id, $data_splits);

                    $chk_acct = str_replace('-', '', $get_bank_info->chk_acct);
                    $chk_routing = str_replace('-', '', $get_bank_info->chk_routing);

                    include APPPATH . 'third_party/fpdf/fpdf.php';

                    $pdf = new FPDF();
                    $pdf->AddFont('MICR', '', 'MICR.php');

                    $filename = $expens_id . '.pdf';

                    $path = FCPATH . "files_upload/generate_check/";
                    $fpath = $path . $filename;
                    $i = 1;
                    $ycol1 = 8;

                    if ($get_bank_info->paperStyle == 0) {
                        $pdf->AliasNbPages();
                        $pdf->AddPage('P');
                        $transaction_res_count = 1;

                        if ($get_bank_info->chk_next_position == 'middle') {
                            $ycol1 = 92;
                            //$pdf->SetDash(5, 5); //5mm on, 5mm off
                            //$pdf->Line(0, $ycol1, 250, $ycol1);
                            //$pdf->SetDash();
                            $ycol1 = $ycol1 + 8;
                            $i = 2;
                            $transaction_res_count += 1;
                        } else if ($get_bank_info->chk_next_position == 'bottom') {
                            $ycol1 = 190;
                            //$pdf->SetDash(5, 5); //5mm on, 5mm off
                            //$pdf->Line(0, $ycol1, 250, $ycol1);
                            //$pdf->SetDash();
                            $ycol1 = $ycol1 + 8;
                            $i = 3;
                            $transaction_res_count += 2;
                        }
                        $next_start_position = 'top';
                        if ($transaction_res_count % 3 == 0) {
                            $next_start_position = 'top';
                        } else if ($transaction_res_count % 3 == 1) {
                            $next_start_position = 'middle';
                        } elseif ($transaction_res_count % 3 == 2) {
                            $next_start_position = 'bottom';
                        }
                        $data_chk['chk_next_position'] = $next_start_position;
                        $data_chk_info = $this->account_bank_info_model->Edit($acc_id, $data_chk);
                    }

                    $amount = formatCurrency($expens_res->amount);
                    $amount_without_comma = $expens_res->amount;
                    $print_amount = preg_replace('~[.,]~', '', $amount);
                    // get month for date
                    $month = date("M", strtotime($expens_res->payment_date));
                    if ($month == 'May') {
                        $post_date = date("M d, Y", strtotime($expens_res->payment_date));
                    } else {
                        $post_date = date("M.d, Y", strtotime($expens_res->payment_date));
                    }

                    if ($expens_res->print_checks_as_custom != '') {
                        $customer_name = ($expens_res->print_checks_as_displayname == 1) ? $expens_res->payee : $expens_res->print_checks_as_custom;
                    } else {
                        $customer_name = ($expens_res->print_checks_as_displayname == 1) ? $expens_res->payee : $expens_res->payee;
                    }
                    // $customer_name = ($expens_res->print_checks_as_displayname == 1) ? $expens_res->payee : ($expens_res->print_checks_as_custom != '') ? $expens_res->print_checks_as_custom : $expens_res->payee;
                    $memo = $expens_res->memo;

                    if ($get_bank_info->paperStyle == 1) {
                        if ($get_bank_info->onePageStyle == 'top') {
                            $pdf->AliasNbPages();
                            $pdf->AddPage('P');
                            $ycol1 = 8;
                        } else if ($get_bank_info->onePageStyle == 'middle') {
                            $pdf->AliasNbPages();
                            $pdf->AddPage('P');
                            $ycol1 = 92;
                            //$pdf->SetDash(5, 5); //5mm on, 5mm off
                            //$pdf->Line(0, $ycol1, 250, $ycol1);
                            //$pdf->SetDash();
                            $ycol1 = $ycol1 + 8;
                        } else if ($get_bank_info->onePageStyle == 'bottom') {

                            $pdf->AliasNbPages();
                            $pdf->AddPage('P');
                            $ycol1 = 190;
                            //$pdf->SetDash(5, 5); //5mm on, 5mm off
                            //$pdf->Line(0, $ycol1, 250, $ycol1);
                            //$pdf->SetDash();
                            $ycol1 = $ycol1 + 8;
                        }
                    }

                    $companyInfo = $get_bank_info->c_name . "\n" . $get_bank_info->c_addr1 .
                            ((strlen(trim($get_bank_info->c_addr2)) != 0) ? "\n" . $get_bank_info->c_addr2 . "\n" : "\n") .
                            $get_bank_info->c_city . ", " . $get_bank_info->c_stateName . " " . $get_bank_info->c_zip . "\n" . $get_bank_info->c_phone;

                    $bankInfo = $get_bank_info->b_name . "\n" . $get_bank_info->b_addr1 .
                            ((strlen(trim($get_bank_info->b_addr2)) != 0) ? "\n" . $get_bank_info->b_addr2 . "\n" : "\n") .
                            $get_bank_info->b_city . ", " . $get_bank_info->b_stateName . " " . $get_bank_info->b_zip . "\n" . $get_bank_info->b_phone;

                    $pdf->SetFont('helvetica', 'B', 10);
                    $pdf->SetXY(32, $ycol1);
                    $pdf->MultiCell(60, 4, $companyInfo);
                    $pdf->SetXY(105, $ycol1);
                    $pdf->MultiCell(70, 4, $bankInfo);

                    //check number (top-right)
                    $pdf->SetFont('helvetica', 'B', 13);
                    $pdf->SetXY(192, $ycol1);
                    $pdf->MultiCell(40, 4, $chk_next_check_number);

                    $ycol1 = $ycol1 + 21;  // 29
                    //check date
                    $pdf->SetFont('helvetica', 'B', 10);
                    $pdf->SetXY(168, $ycol1);
                    $pdf->Cell(40, 4, "DATE:");
                    $pdf->SetXY(180, $ycol1 - 1);
                    $pdf->Cell(40, 4, $post_date);
                    $pdf->Line(181, $ycol1 + 3, 204, $ycol1 + 3);

                    $ycol1 = $ycol1 + 8; // 37
                    //pay to the order of
                    $pdf->SetFont('helvetica', 'B', 10);
                    $pdf->SetXY(7, $ycol1);
                    $pdf->Cell(20, 4, "PAY TO THE");
                    $pdf->SetXY(7, $ycol1 + 6);
                    $pdf->Cell(20, 4, "ORDER OF");
                    $pdf->SetFont('helvetica', 'B', 12);
                    $pdf->SetXY(29, $ycol1 + 4);
                    // $pdf->Cell(70, 4, $customer_name);
                    $amtCWidth = $pdf->GetStringWidth($customer_name);
                    $amtC1Width = $pdf->GetStringWidth('ORDER OF');
                    $starCWidth = $pdf->GetStringWidth('*');
                    $remainC = 153 - $amtCWidth - $amtC1Width;
                    $numStarC = floor($remainC / $starCWidth) - 1;
                    $pdf->Cell(70, 4, $customer_name . str_repeat('*', $numStarC));
                    $pdf->Line(29, $ycol1 + 9, 160, $ycol1 + 9);


                    $ycol1 = $ycol1 + 3; // 40
                    $pdf->SetFont('helvetica', 'B', 14);
                    $pdf->SetXY(169, $ycol1);
                    $pdf->Cell(1, 4, $this->company_currency);
                    $pdf->SetXY(173, $ycol1);
                    $amtWidth = $pdf->GetStringWidth($amount);
                    $starWidth = $pdf->GetStringWidth('*');
                    $remain = 31 - $amtWidth;
                    $numStar = floor($remain / $starWidth) - 1;
                    $pdf->Cell(31, 4, $amount . str_repeat('*', $numStar));
                    $pdf->Rect(173, $ycol1 - 1, 31, 6);

                    $ycol1 = $ycol1 + 8; // 48
                    //pay amount in words
                    $pdf->SetFont('helvetica', 'B', 11);
                    $pdf->SetXY(7, $ycol1);
                    $amtInWords = amountToWords($amount_without_comma);
                    $amtWidth = $pdf->GetStringWidth($amtInWords);
                    $starWidth = $pdf->GetStringWidth('*');
                    $remain = 166 - $amtWidth;
                    $numStar = floor($remain / $starWidth) - 1;
                    $pdf->Cell(166, 4, $amtInWords . str_repeat('*', $numStar));
                    $pdf->SetFont('helvetica', 'B', 12);
                    $pdf->SetXY(182, $ycol1 + 1);
                    $pdf->Cell(20, 4, $this->company_currency_name);
                    $pdf->Line(8, $ycol1 + 4, 179, $ycol1 + 4);

                    $ycol1 = $ycol1 + 20; //68
                    $pdf->SetFont('helvetica', 'B', 10);
                    $pdf->SetXY(7, $ycol1);
                    $pdf->Cell(10, 4, 'MEMO:');
                    $pdf->SetXY(21, $ycol1);
                    $pdf->MultiCell(80, 4, $memo);
                    $pdf->Line(21, $ycol1 + 4, 109, $ycol1 + 4);

                    $fn = md5("signature_$acc_id");
                    if (file_exists("files_upload/signatures/$fn.png")) {
                        $time = time();
                        $pdf->Image("files_upload/signatures/$fn.png", 134, $ycol1 - 8);
                    }

                    $pdf->Line(134, $ycol1 + 4, 205, $ycol1 + 4);
                    $pdf->SetFont('MICR', '', 10.5);

                    $ycol1 = $ycol1 + 13; // 81
                    $pdf->SetXY(50, $ycol1);  // make the 2nd number smaller to move routing/checking number line higher on page
//                    $pdf->Cell(100, 4, "t" . $chk_next_check_number . "t o" . $chk_routing . "o " .
//                            $chk_acct . "   a" . str_replace('.', '', round($amount)) . "a");
//                    $pdf->Cell(100, 4, "t" . $chk_routing . "t o" . $chk_acct . "o " .
//                            $chk_next_check_number . "   a" . str_replace('.', '', $amount) . "a");

                    $print_amount = preg_replace('~[.,]~', '', $amount);
                    $pdf->Cell(100, 4, "o" . $chk_next_check_number . "o t" . $chk_routing . "t " .
                            $chk_acct . "o a" . $print_amount . "a");

                    if ($get_bank_info->paperStyle == 1) {
                        if ($get_bank_info->onePageStyle == 'top') {
                            $ycol1 = $ycol1 + 10;
                            //$pdf->SetDash(5, 5); //5mm on, 5mm off
                            //$pdf->Line(0, $ycol1, 250, $ycol1);
                            //$pdf->SetDash();
                        }
                        if ($get_bank_info->onePageStyle == 'middle') {
                            $ycol1 = $ycol1 + 10;
                            //$pdf->SetDash(5, 5); //5mm on, 5mm off
                            //$pdf->Line(0, $ycol1, 250, $ycol1);
                            //$pdf->SetDash();
                        }
                    }

                    if ($get_bank_info->paperStyle == 0) {
                        if ($i % 3 == 0) {
                            if ($i != $transaction_res_count) {
                                $pdf->AliasNbPages();
                                $pdf->AddPage('P');
                                $ycol1 = 8;
                            }
                        } else {
                            $ycol1 = $ycol1 + 13;
                            //$pdf->SetDash(5, 5); //5mm on, 5mm off
                            //$pdf->Line(0, $ycol1, 250, $ycol1);
                            //$pdf->SetDash();
                            $ycol1 = $ycol1 + 8;
                        }
                    }
                    $data_expens['expense_no'] = $chk_next_check_number;
//                    }
                    $pdf->Output($fpath, "F");
                    if ($this->input->post('check_choice')) {
                        if ($check_choice == 1) {
                            
                        } else {
                            $chk_next_check_number++;
                            $data_chk_no['chk_next_check_number'] = $chk_next_check_number;
                            $data_chk_info = $this->account_bank_info_model->Edit($acc_id, $data_chk_no);
                        }
                    } else {
                        $chk_next_check_number++;
                        $data_chk_no['chk_next_check_number'] = $chk_next_check_number;
                        $data_chk_info = $this->account_bank_info_model->Edit($acc_id, $data_chk_no);
                    }
                    $data_expens['check_printed'] = 1;
                    $data_expens_info = $this->expenses_model->Edit($expens_id, $data_expens);
                    $data['filename'] = $filename;
                }
            }
        }
        echo json_encode($data);
    }

    public function get_prev_account() {
        $result = array();
        $guid = $this->input->post('guid');
        $expense_type = ucfirst($this->input->post('expense_type'));
        $result = $this->expenses_model->get_customer_last_account(array("expenses.customer_guid" => $guid, "expense_type" => $expense_type), $this->session->userdata('company_id'));
//        echo $this->db->last_query();
//        exit;
        if (empty($result)) {
            $result = $this->expenses_model->get_customer_last_account(array("expense_type" => $expense_type), $this->session->userdata('company_id'));
        }
        $last_used_account = isset($result->account_guid) ? $result->account_guid : "";
        $last_used_payment = isset($result->payment_method) ? $result->payment_method : "";
        echo json_encode(array("account_id" => $last_used_account, "payment_method" => $last_used_payment, "status" => "success"));
        exit;
    }

    function remove_file() {
        $file = $this->input->post('file_path');
        $file = FCPATH . 'files_upload/' . $file;
        if (file_exists($file)) {
            unlink($file);
        }
        echo json_encode(array("flag" => TRUE));
    }

    function already_check_entry() {
        $JSON = array();
        $check_no = $this->input->post('expense_no');
        $expense_guid = $this->input->post('expense_guid');
        $account_guid = $this->input->post('bank_credit_account');
        $result = $this->expenses_model->Get(NULL, array("expense_guid" => $expense_guid, "expense_no" => $check_no, "expense_type" => 'Check', "account_guid" => $account_guid, "company_guid" => $this->session->userdata('company_id')));
        if (count($result['records']) > 0) {
            // already used
            $JSON['status'] = false;
        } else {
            // not used
            $JSON['status'] = true;
        }
        echo json_encode($JSON);
    }

    public function delete_expenses($guid) {
        $company_guid = $this->company_guid;
        $this->load->model('splits_model');
        $this->load->model('transactions_model');
        $tx_guids = array();
        $search = array('expense_guid' => $guid);
        $get_split = $this->splits_model->Get(NULL, $search);
        if (isset($get_split['records']) && !empty($get_split['records'])) {
            foreach ($get_split['records'] as $Row) {
                $tx_guids[] = $Row->tx_guid;
            }
        }

        $expense_data = $this->expenses_model->Get($guid);
        $type = isset($expense_data->expense_type) ? $expense_data->expense_type : "";

        if ($type == "Bill") {
            //log entry of delete bill entry
            $this->logfunction->LogRecords('57', $expense_data, isset($expense_data->customer_guid) ? $expense_data->customer_guid : "");
        } else if ($type == "Expense") {
            //log entry of delete expense entry
            $this->logfunction->LogRecords('49', $expense_data, isset($expense_data->customer_guid) ? $expense_data->customer_guid : "");
        }

        $this->expenses_model->Delete($guid);
        if (!empty($tx_guids)) {
            $temp = array_unique($tx_guids);
            foreach ($temp as $k => $v) {
                $search = array();
                $search['tx_guid'] = $v;
                $split_log = $this->splits_model->GetDelete(NULL, $search)["records"];
                $transactions_log = (array) $this->transactions_model->Get($v);
                $this->logfunction->transactionDeleteLogRecord(36, $transactions_log, json_decode(json_encode($split_log), true));
                // Classifier API to delete tx
                $request_payload_for_delete_tx = array();
                $request_payload_for_delete_tx['guid'] = $v;
                $request_payload_for_delete_tx = json_encode($request_payload_for_delete_tx);

                //$url = base_url() . "classification/process_classification?api_payload=" . $request_payload_for_delete_tx . '&api_action=TX_DELETED&company_guid=' . $this->session->userdata('company_id');
                //shell_exec('wget --tries=0 --timeout=0 ' . $url . '> /dev/null 2>/dev/null &');
                // $delete_api_response = $this->classifierfunction->callapi('TX_DELETED', $request_payload_for_delete_tx);
                $this->classifierfunction->generate_shell_cmd('TX_DELETED', $request_payload_for_delete_tx, $this->session->userdata('company_id'));
            }

            $this->splits_model->batchDelete(array('tx_guid' => $tx_guids));
            $this->transactions_model->batchDelete(array('guid' => $tx_guids));

            foreach ($tx_guids as $tRow) {
                $dir = FCPATH . "/files_upload/user_docs/$company_guid/$tRow";
                recursiveRemove($dir);
                if (file_exists(FCPATH . "/files_upload/user_docs/" . $company_guid . "/" . $tRow)) {
                    rmdir(FCPATH . "/files_upload/user_docs/" . $company_guid . "/" . $tRow);
                }
            }

            $this->splits_model->Delete_by_txguid($tx_guids);
            $this->transactions_model->Delete($tx_guids);
            $cdocs_res = $this->company_docs_model->Delete_by_txguid($tx_guids);
        }

        if ($this->input->is_ajax_request()) {
            echo json_encode(array('status' => 'success', 'type' => 'closed_splits'));
            exit;
        } else {
            $this->session->set_flashdata('msg_class', "success");
            $this->session->set_flashdata('msg', "Record has been deleted successfully.");
            redirect('expenses');
        }
    }

    public function bill_payment_view($id = NULL) {
        if ($id == NULL) {
            $id = $this->uri->segment(3);
        }
        $data['payment_data'] = $this->bill_payment_model->Get_row($id);
        $data['title'] = 'View Bill Payment';
        $this->template->load('listing', 'bill_payment_view', $data);
    }

    public function get_payment_mapping_entries() {
        $response = $this->phonebook_payment_mapping_model->Get(NULL, $this->input->post(), NULL, $this->session->userdata('company_id'));
        if (isset($response['records']) && !empty($response['records'])) {
            echo json_encode(array('status' => true, 'data' => $response['records']));
            exit;
        } else {
            echo json_encode(array('status' => false));
            exit;
        }
    }
    public function get_account_mapping_entries() {
        $acc_guid = $this->input->post('credit_acc_guid');
        $response = $this->splits_model->Get_account_mapping_payment($acc_guid);
        if (isset($response) && !empty($response)) {
            echo json_encode(array('status' => true, 'payment_method' => $response->payment_method));
            exit;
        } else {
            echo json_encode(array('status' => false));
            exit;
        }
    }

    public function unpay_bill() {
        if ($this->input->is_ajax_request()) {
            $bill_payment_data = $this->bill_payment_model->Get(NULL, array('bill_guid' => $this->input->post('bill_id')));

            $expense_data = $this->expenses_model->Get($this->input->post('bill_id'));
            $bill_tx_guids = array();
            if (isset($bill_payment_data['records']) && !empty($bill_payment_data['records']) > 0) {
                foreach ($bill_payment_data['records'] as $bill_payment) {
                    $this->logfunction->LogRecords('57', $bill_payment, isset($expense_data->customer_guid) ? $expense_data->customer_guid : "");
                    $bill_tx_guids[] = $bill_payment->bill_transaction_guid;
                }
            }

            $status = $this->bill_payment_model->DeleteAllBillPayment($this->input->post('bill_id'));

            if (isset($bill_tx_guids) && !empty($bill_tx_guids)) {
                foreach ($bill_tx_guids as $tx_guid) {
                    // Classifier API to delete tx
                    $request_payload_for_delete_tx = array();
                    $request_payload_for_delete_tx['guid'] = $tx_guid;
                    $request_payload_for_delete_tx = json_encode($request_payload_for_delete_tx);
                    $this->classifierfunction->generate_shell_cmd('TX_DELETED', $request_payload_for_delete_tx, $this->session->userdata('company_id'));
                }
            }
            if ($status) {
                $this->session->set_flashdata('msg_class', "success");
                $this->session->set_flashdata('msg', "Record has been deleted successfully.");
                echo json_encode(array('status' => true));
                exit;
            } else {
                echo json_encode(array('status' => false));
                exit;
            }
        }
    }

    public function delete_bill_payment() {
        if ($this->input->is_ajax_request()) {
            $bill_payment_data = $this->bill_payment_model->Get($this->input->post('bill_id'));
            $expense_data = $this->expenses_model->Get($bill_payment_data->bill_guid);
            $this->logfunction->LogRecords('57', $bill_payment_data, isset($expense_data->customer_guid) ? $expense_data->customer_guid : "");
            $status = $this->bill_payment_model->Delete($this->input->post('bill_id'));

            if (isset($bill_payment_data->bill_transaction_guid)) {
                // Classifier API to delete tx
                $request_payload_for_delete_tx = array();
                $request_payload_for_delete_tx['guid'] = $bill_payment_data->bill_transaction_guid;
                $request_payload_for_delete_tx = json_encode($request_payload_for_delete_tx);

                $this->classifierfunction->generate_shell_cmd('TX_DELETED', $request_payload_for_delete_tx, $this->session->userdata('company_id'));
            }

            if ($status) {
                $this->session->set_flashdata('msg_class', "success");
                $this->session->set_flashdata('msg', "Record has been deleted successfully.");
                echo json_encode(array('status' => true));
                exit;
            } else {
                echo json_encode(array('status' => false));
                exit;
            }
        }
    }

    public function export_expenses_report() {
        $_POST['company_guid'] = $this->session->userdata('company_id');
        $_POST['order'][0]['column'] = 0;
        $_POST['order'][0]['dir'] = 'DESC';

        $expenses = $this->expenses_model->Get(NULL, $this->input->post());
        $tableData = array();
        $tot_balance = 0;
        $count = 0;
        $expense_type = $this->input->post('expense_type');
        $export_action = $this->input->post('type');

        if (isset($expenses['records']) && !empty($expenses['records'])) {
            foreach ($expenses['records'] as $key => $row) {
                $check_no = '';
                $action = array();
                $bill_amount = 0;

                if ($row->expense_type == "Expense") {
                    $date = $row->payment_date;
                    $type = 'Expense';
                }

                if ($row->expense_type == "Bill") {
                    $date = $row->bill_date;
                    $type = 'Bill';
                    $paid = $total_payment = 0;
                    $payment_res = $this->splits_model->Get(NULL, array("expense_guid" => $row->guid, "balance_type" => 'P', "payment" => 1));
                    if (isset($payment_res['records']) && !empty($payment_res['records'])) {
                        foreach ($payment_res['records'] as $pRow) {
                            $total_payment += ($pRow->value_num / $pRow->value_denom);
                        }
                        if ($total_payment == $row->amount) {
                            $paid = 1;
                        }
                    }
                    $bill_amount = $row->amount - $total_payment;
                    if ($expense_type != 'Bill') {
                        $bill_payments = $this->bill_payment_model->GetWithSplit($row->guid);
                    }
                }

                if ($row->expense_type == "Credit") {
                    $date = $row->bill_date;
                    $type = 'Credit';
                }

                if ($row->expense_type == "Check") {
                    $date = $row->payment_date;
                    $type = 'Check';
                    $check_no = $row->expense_no;
                }

                if ($expense_type != 'Bill Payment') {
                    $check_no = $row->expense_no;
                    $tableData[$count]['srNo'] = $count + 1;
                    $tableData[$count]['date'] = date('m/d/Y', strtotime($date));
                    $tableData[$count]['type'] = $type;
                    $tableData[$count]['check_no'] = $check_no;
                    $tableData[$count]['payee'] = $row->payee;
                    $tableData[$count]['memo'] = $row->memo;
                    $tableData[$count]['description'] = $row->description;
                    $tableData[$count]['total'] = number_format($row->amount, 2);

                    if ($row->expense_type == 'Check' || $row->expense_type == 'Expense') {
                        $tableData[$count]['balance'] = '0.00';
                    } else {
                        $tableData[$count]['balance'] = $row->expense_type == 'Bill' ? number_format($bill_amount, 2) : number_format($row->amount, 2);
                    }

                    $tot_balance += $row->amount;
                }

                if (isset($bill_payments) && !empty($bill_payments) && $row->expense_type == "Bill") {
                    if ($expense_type != 'Bill Payment') {
                        ++$count;
                    }

                    foreach ($bill_payments as $bill) {
                        $tableData[$count]['srNo'] = $count + 1;
                        $tableData[$count]['date'] = date('m/d/Y', strtotime($bill['bill_payment_date']));
                        $tableData[$count]['type'] = 'Bill Payment';
                        $tableData[$count]['check_no'] = '';
                        $tableData[$count]['payee'] = $bill['payee'];
                        $tableData[$count]['total'] = number_format($bill['payment_amount'], 2);
                        $tableData[$count]['memo'] = '';
                        $tableData[$count]['description'] = '';
                        $tableData[$count]['balance'] = '0.00';
                        $count++;
                    }
                } else {
                    if ($expense_type != 'Bill Payment') {
                        $count++;
                    }
                }
            }
        }

        if ($export_action == 'PDF') {
            // PDF Generation 
            include APPPATH . 'third_party/fpdf/fpdf.php';

            /** Constant for PDF * */
            $height_cell = 10;
            $header_font_size = 10;
            $data_font_size = 10;
            $font_family = "Courier";

            $pdf = new FPDF();
            $pdf->AliasNbPages();
            $pdf->AddPage('P', 'A4');
            //$pdf->Rect(5, 5, 200, 287, 'D');

            $pdf->SetFont($font_family, '', 14);
            $pdf->SetXY(10, 10);
            $pdf->Cell(0, 4, strtoupper($this->session->userdata('company_name')), 0, 0, 'L');
            $pdf->Line(10, 16, 200, 16);

            $pdf->SetFont($font_family, '', 11);
            $pdf->SetXY(10, 24);
            $pdf->Cell(0, 4, "List of Expenses", 0, 0, 'L');

            $pdf->SetFont($font_family, '', 8);
            $pdf->SetXY(10, 40);

            $width_cell = array(25, 30, 25, 55, 30, 25);
            $pdf->SetFillColor(231, 236, 241);

            // Header Data
            $pdf->SetFont($font_family, 'B', $header_font_size);
            $pdf->Cell($width_cell[0], $height_cell, 'Date', 1, 0, 'C', true);
            $pdf->Cell($width_cell[1], $height_cell, 'Type', 1, 0, 'C', true);
            $pdf->Cell($width_cell[2], $height_cell, 'Check No', 1, 0, 'C', true);
            $pdf->Cell($width_cell[3], $height_cell, 'Payee', 1, 0, 'C', true);
            $pdf->Cell($width_cell[4], $height_cell, 'Total', 1, 0, 'C', true);
            $pdf->Cell($width_cell[5], $height_cell, 'Balance', 1, 1, 'C', true);

            // Table Data
            $pdf->SetFont($font_family, '', $data_font_size);

            if (!empty($tableData)) {
                foreach ($tableData as $key => $row) {
                    $pdf->Cell($width_cell[0], $height_cell, $row['date'], 1, 0, 'L', false);
                    $pdf->Cell($width_cell[1], $height_cell, $row['type'], 1, 0, 'L', false);
                    $pdf->Cell($width_cell[2], $height_cell, $row['check_no'], 1, 0, 'L', false);
                    $pdf->SetFont($font_family, '', 8);
                    $pdf->Cell($width_cell[3], $height_cell, (strlen($row['payee']) > 50) ? substr($row['payee'], 0, 50) . '..' : $row['payee'], 1, 0, 'L', false);
                    $pdf->SetFont($font_family, '', $data_font_size);

                    //$pdf->Cell($width_cell[3], $height_cell, $row['payee'], 1, 0, 'L', false);
                    $pdf->Cell($width_cell[4], $height_cell, $row['total'], 1, 0, 'R', false);
                    $pdf->Cell($width_cell[5], $height_cell, ($row['balance'] != "") ? $row['balance'] : '', 1, 1, 'R', false);

                    // $pdf->Row(array($row['date'], $row['type'], $row['check_no'], $row['payee'], $row['total'], $row['balance']));
                }
            }

            $filename = 'expenses_' . md5(time()) . '.pdf';
            $path = FCPATH . "files_upload/reports_pdf/";
            $fpath = $path . $filename;
            $pdf->Output($fpath, "F");
            $Json['flag'] = true;
            $Json['filename'] = $filename;
            echo json_encode($Json);
            exit;
        } else if ($export_action == 'EXCEL') {
            //load our new PHPExcel library
            $this->load->library('excel');
            //activate worksheet number 1
            $this->excel->setActiveSheetIndex(0);
            //name the worksheet
            $this->excel->getActiveSheet()->setTitle('Expenses');
            // load database
            $this->load->database();

            $report_arr[] = array(
                "date" => strtoupper($this->session->userdata('company_name')),
                "type" => "",
                "check_no" => "",
                "payee" => "",
                "total" => "",
                "memo" => "",
                "description" => "",
                "balance" => "",
            );

            $report_arr[] = array(
                "date" => "List of Expenses",
                "type" => "",
                "check_no" => "",
                "payee" => "",
                "total" => "",
                "memo" => "",
                "description" => "",
                "balance" => "",
            );

            $report_arr[] = array(
                "date" => "",
                "type" => "",
                "check_no" => "",
                "payee" => "",
                "total" => "",
                "memo" => "",
                "description" => "",
                "balance" => "",
            );

            $report_arr[] = array(
                "date" => "Date",
                "type" => "Type",
                "check_no" => "Check No",
                "payee" => "Payee",
                "total" => "Total",
                "memo" => "Memo",
                "description" => "Description",
                "balance" => "Balance",
            );

            if (!empty($tableData)) {
                foreach ($tableData as $key => $value) {
                    $report_arr[] = array(
                        "date" => $value['date'],
                        "type" => $value['type'],
                        "check_no" => $value['check_no'],
                        "payee" => $value['payee'],
                        "total" => $value['total'],
                        "memo" => $value['memo'],
                        "description" => $value['description'],
                        "balance" => $value['balance'],
                    );
                }
            }

            $style = array(
                'alignment' => array(
                    'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                )
            );
            $border_bottom = array(
                'borders' => array(
                    'bottom' => array(
                        'style' => PHPExcel_Style_Border::BORDER_THICK
                    )
                )
            );
            $data_count = sizeof($tableData) + 4;
            $this->excel->getActiveSheet()->fromArray($report_arr, null, 'A1', true);
            $this->excel->getActiveSheet()->getStyle("A1:H4")->getFont()->setBold(true);
            $this->excel->getActiveSheet()->mergeCells('A1:H1')->getStyle("A1:B1")->applyFromArray($style);
            $this->excel->getActiveSheet()->mergeCells('A2:H2')->getStyle("A2:B2")->applyFromArray($style);
            $this->excel->getActiveSheet()->mergeCells('A3:H3')->getStyle("A3:B3")->applyFromArray($style);
            $this->excel->getActiveSheet()->getStyle('A4:H4')->applyFromArray($border_bottom);
//            $this->excel->getActiveSheet()->mergeCells('A3:B3')->getStyle("A3:B3")->applyFromArray($style);
//            // $this->excel->getActiveSheet()->getStyle("A4:B4")->applyFromArray($style);
//            $this->excel->getActiveSheet()->getStyle('A5:A8')->getAlignment()->setIndent(1);
            $this->excel->getActiveSheet()->getStyle('C5:C' . $data_count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $this->excel->getActiveSheet()->getStyle('E5:E' . $data_count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
            $this->excel->getActiveSheet()->getStyle('H5:H' . $data_count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);

            $this->excel->getActiveSheet()->getStyle('E5:E' . $data_count)->getNumberFormat()->setFormatCode('#,##0.00');
            $this->excel->getActiveSheet()->getStyle('H5:H' . $data_count)->getNumberFormat()->setFormatCode('#,##0.00');

            $filename = 'expenses_' . md5(time()) . '.xls';
            $path = FCPATH . "files_upload/reports_excel/";
            $fpath = $path . $filename;

            if (!is_dir('files_upload/reports_excel')) {
                mkdir('files_upload/reports_excel');
                chmod('files_upload/reports_excel', 0777);
            }

//            header('Content-Type: application/vnd.ms-excel');
//            header('Content-Disposition: attachment;filename="' . $filename . '"');
//            header('Cache-Control: max-age=0');
            foreach (range('A', 'H') as $columnID) {
                $this->excel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
            // $objWriter->save('php://output');
            $objWriter->save(str_replace(__FILE__, $fpath, __FILE__));

            $Json['flag'] = true;
            $Json['filename'] = $filename;
            echo json_encode($Json);
            exit;
        }
    }

}
