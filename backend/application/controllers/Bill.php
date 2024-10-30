<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Bill extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('vendor_model');
        $this->load->model('vendor_attachments_model');
        $this->load->model('category_model');
        $this->load->model('bill_model');
        $this->load->model('bill_item_model');
        $this->load->model('store_master_model');
        $this->load->model('checkbook_model');
        $sql           = "SELECT * FROM `admin_settings` WHERE key_name = 'check_number_starting'";
        $check_Res     = $this->checkbook_model->query_result($sql);
        $store_chk_Arr = [];
        if (count($check_Res)) {
            $check_Arr = isset($check_Res[0]->key_value) ? json_decode($check_Res[0]->key_value) : '';
            if (!empty($check_Arr) && is_array(($check_Arr))) {
                foreach ($check_Arr as $row) {
                    $store_chk_Arr[$row->store_key] = $row->key_value;
                }
            } else {
                $this->session->set_flashdata('msg_class', "failure");
                $this->session->set_flashdata('msg', "Please enter check numbers in admin settings for each stores.");
            }
        } else {
            $this->session->set_flashdata('msg_class', "failure");
            $this->session->set_flashdata('msg', "Please enter check numbers in admin settings for each stores.");
        }
        $this->storeCheckArr = $store_chk_Arr;
    }

    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $data = $this->bill_model->Get(null, $this->input->post());
            $this->getListing($data);
        } else {
            $data['title'] = "Bill List";
            $this->template->load('listing', 'bill/listbill', $data);
        }
    }

    public function getListing($result = array())
    {
        $tableData = array();
        foreach ($result['records'] as $key => $row) {
            $srNo                          = $key + 1;
            $action                        = array();
            $action[]                      = anchor('bill/add/' . $row->id, 'Edit');
            $action[]                      = anchor('javascript:void(0);', 'Delete', array('data-toggle' => 'modal', 'data-id' => $row->id, 'onclick' => 'setConfirmDetails(this)', ' data-target' => '#ConfirmDeleteModal', 'data-url' => 'bill/delete/' . $row->id));
            $tableData[$key]['srNo']       = $srNo;
            $tableData[$key]['month']      = monthName($row->month);
            $tableData[$key]['year']       = $row->year;
            $tableData[$key]['created_on'] = DB2Disp($row->created_on);
            $tableData[$key]['action']     = implode(" | ", $action);
            $tableData[$key]['id']         = $row->id;
        }
        $data['recordsTotal']    = $result['countTotal'];
        $data['recordsFiltered'] = $result['countFiltered'];
        $data['data']            = $tableData;

        echo json_encode($data);
    }

    public function delete($bill_id)
    {

        //phonebook attachment record
        $this->bill_model->Delete($bill_id);

        //Remove all attachment related to this phonebook
        $dir = FCPATH . "/files_upload/bill_attachment/" . $bill_id;
        recursiveRemove($dir);
        if (file_exists(FCPATH . "/files_upload/bill_attachment/" . $id)) {
            rmdir(FCPATH . "/files_upload/bill_attachment/" . $id);
        }

        //log entry of phonebook
        $this->session->set_flashdata('success', 'You have successfully Deleted Bill Entry!');
        redirect('bill');
    }

    public function add($id = false)
    {
        $dir = FCPATH . "/files_upload/tmp";
        clear_temp($dir);
        $category            = $this->category_model->Get(null, array("status" => 'A', 'exclude_category' => 'breakdown_description'));
        $br_category         = $this->category_model->Get(null, array("status" => 'A', 'type' => 'breakdown_description'));
        $store               = $this->store_master_model->Get(null, array("status" => 'A'));
        $vendor              = $this->vendor_model->Get();
        $data['title']       = 'Bill Entry';
        $data['vendor']      = $vendor;
        $data['store']       = $store;
        $data['category']    = $category;
        $data['br_category'] = $br_category;

        $payment_date = $this->input->get('payment_date');

        if ($id) {
            $data['bill_res'] = $bill_res = $this->bill_model->Get($id);
            if (isset($bill_res) && !empty($bill_res)) {
                $data['bill_item_desc_res']      = $bill_item_res      = $this->bill_item_model->Get(null, array("bill_id" => $id, "exclude_type" => 'breakdown_description'));
                $data['bill_item_breakdown_res'] = $bill_br_item_res = $this->bill_item_model->Get(null, array("bill_id" => $id, "type" => 'breakdown_description'));
                $data['bill_item_check_res']     = $bill_item_check_res     = $this->bill_item_model->Get_Check_Entry(null, array("bill_id" => $id));

            }
        }
        $this->template->load('listing', 'bill/bill_entry', $data);
    }

    public function bill_entry_save()
    {

        $bill_id       = $this->input->post('edit_bill_id');
        $date_arr      = $this->input->post('ledger_bill_date') != '' ? explode("/", $this->input->post('ledger_bill_date')) : array();
        $data['month'] = isset($date_arr[0]) ? $date_arr[0] : date('m');
        $data['year']  = isset($date_arr[1]) ? $date_arr[1] : date('Y');
        if ($bill_id > 0) {
            $this->bill_model->Edit($bill_id, $data);
            $this->bill_item_model->Delete_by_billid($bill_id);
            $this->bill_item_model->Delete_by_billid($bill_id, "bill_check_entries");
        } else {
            $bill_id = $this->bill_model->Add($data);
        }

        $bill_date_arr           = array_filter($this->input->post('bill_date'));
        $store_arr               = array_filter($this->input->post('store'));
        $physcial_address_arr    = array_filter($this->input->post('store_physical_address'));
        $bill_no_arr             = array_filter($this->input->post('bill_no'));
        $category_arr            = array_filter($this->input->post('category'));
        $desc_arr                = array_filter($this->input->post('description'));
        $qty_arr                 = array_filter($this->input->post('qty'));
        $rate_arr                = array_filter($this->input->post('rate'));
        $amount_arr              = array_filter($this->input->post('amount'));
        $staus_arr               = array_filter($this->input->post('status'));
        $last_paid_date_arr      = array_filter($this->input->post('last_paid_date'));
        $last_paid_amt_arr       = array_filter($this->input->post('last_paid_amount'));
        $is_paid_arr             = array_filter($this->input->post('is_paid'));
        $hidden_attachment_arr   = isset($_POST['hidden_attachment']) && !empty($_POST['hidden_attachment']) ? array_filter($this->input->post('hidden_attachment')) : array();
        $bill_type_arr           = array_filter($this->input->post('bill_type'));
        $attachment_name_arr     = isset($_FILES['attachment']['name']) ? array_filter($_FILES['attachment']['name']) : array();
        $attachment_tmp_name_arr = isset($_FILES['attachment']['tmp_name']) ? array_filter($_FILES['attachment']['tmp_name']) : array();

        foreach ($bill_date_arr as $key => $val) {

            $attachment = isset($attachment_name_arr[$key]) ? $attachment_name_arr[$key] : '';
            if ($bill_id > 0) {
                if ($attachment == '') {
                    $attachment = isset($hidden_attachment_arr[$key]) ? $hidden_attachment_arr[$key] : '';
                }
            }
            $paid_status = isset($is_paid_arr[$key]) ? $is_paid_arr[$key] : '';
            $status      = 'Unpaid';
            $is_paid     = 0;
            if ($paid_status == 'Paid') {
                $is_paid = 1;
                $status  = $paid_status;
            }
            $tempFile      = isset($attachment_tmp_name_arr[$key]) ? $attachment_tmp_name_arr[$key] : array();
            $file_name_Arr = $file_name_Arr_final = $oneDimensionalArray = $br_oneDimensionalArray = $chk_oneDimensionalArray = array();
            if (!empty($tempFile)) {
                $file_name_Arr = array();
                foreach ($tempFile as $tkey => $tval) {
                    $targetPath = FCPATH . "/files_upload/bill_attachment/" . $bill_id . "/";
                    if (!file_exists($targetPath)) {
                        mkdir($targetPath, 0777, true);
                    }

                    $file_name          = isset($attachment_name_arr[$key][$tkey]) ? $attachment_name_arr[$key][$tkey] : '';
                    $temp_uploaded_name = isset($tempFile[$tkey]) ? $tempFile[$tkey] : '';
                    if ($temp_uploaded_name != '') {
                        $file_name_Arr[] = $file_name;
                        $targetFile      = $targetPath . $file_name; //5
                        $res             = move_uploaded_file($temp_uploaded_name, $targetFile);
                    }
                }

            }
            if (isset($hidden_attachment_arr[$key]) && !empty($hidden_attachment_arr[$key])) {
                foreach ($hidden_attachment_arr[$key] as $k1 => $v1) {
                    $oneDimensionalArray[] = $v1;
                }
            }
            $file_name_Arr_final = array();
            $file_name_Arr_final = !empty(array_filter($oneDimensionalArray)) ? array_filter(array_merge($oneDimensionalArray, $file_name_Arr)) : $file_name_Arr;

            $ins_arr[] = array("bill_id" => $bill_id,
                "store_key"                  => isset($store_arr[$key]) ? $store_arr[$key] : '',
                "store_physical_address"     => isset($physcial_address_arr[$key]) ? $physcial_address_arr[$key] : '',
                "bill_date"                  => isset($bill_date_arr[$key]) ? date("Y-m-d", strtotime($bill_date_arr[$key])) : '',
                "bill_no"                    => isset($bill_no_arr[$key]) ? $bill_no_arr[$key] : '',
                "category_key"               => isset($category_arr[$key]) ? $category_arr[$key] : '',
                "description"                => isset($desc_arr[$key]) ? $desc_arr[$key] : '',
                "qty"                        => isset($qty_arr[$key]) ? $qty_arr[$key] : '',
                "rate"                       => isset($rate_arr[$key]) ? $rate_arr[$key] : '',
                "amount"                     => isset($amount_arr[$key]) ? $amount_arr[$key] : '',
                "last_paid_date"             => isset($last_paid_date_arr[$key]) ? $last_paid_date_arr[$key] : '',
                "last_paid_amount"           => isset($last_paid_amt_arr[$key]) ? $last_paid_amt_arr[$key] : '',
                "is_paid"                    => $is_paid,
                "status"                     => $status,
                "attachment"                 => isset($file_name_Arr_final) && !empty($file_name_Arr_final) ? implode(",", $file_name_Arr_final) : '',
                "type"                       => isset($bill_type_arr[$key]) ? $bill_type_arr[$key] : 'description',
            );

        }
//        exit;

        if (isset($ins_arr) && !empty($ins_arr)) {
            $this->bill_item_model->Add_Batch($ins_arr);
        }
        $br_store_arr                  = array_filter($this->input->post('br_store'));
        $br_store_physical_address_arr = array_filter($this->input->post('br_store_physical_address'));
        $br_bill_date_arr              = array_filter($this->input->post('br_bill_date'));
        $br_bill_no_arr                = array_filter($this->input->post('br_bill_no'));
        $br_category_arr               = array_filter($this->input->post('br_category'));
        $br_description_arr            = array_filter($this->input->post('br_description'));
        $br_breakdown_description_arr  = array_filter($this->input->post('br_breakdown_description'));
        $br_qty_arr                    = array_filter($this->input->post('br_qty'));
        $br_rate_arr                   = array_filter($this->input->post('br_rate'));
        $br_amount_arr                 = array_filter($this->input->post('br_amount'));
        $br_last_paid_date_arr         = array_filter($this->input->post('br_last_paid_date'));
        $br_last_paid_amount_arr       = array_filter($this->input->post('br_last_paid_amount'));
        $br_status_arr                 = array_filter($this->input->post('br_status'));
        $br_is_paid_arr                = array_filter($this->input->post('br_is_paid'));
        $br_hidden_attachment          = isset($_POST['br_hidden_attachment']) && !empty($_POST['br_hidden_attachment']) ? array_filter($this->input->post('br_hidden_attachment')) : array();
        $br_attachment_arr             = isset($_FILES['br_attachment']['name']) ? array_filter($_FILES['br_attachment']['name']) : array();
        $br_attachment_tmp_arr         = isset($_FILES['br_attachment']['tmp_name']) ? array_filter($_FILES['br_attachment']['tmp_name']) : array();
        foreach ($br_store_arr as $key => $val) {
            $attachment = isset($br_attachment_arr[$key]) ? $br_attachment_arr[$key] : '';
            if ($bill_id > 0) {
                if ($attachment == '') {
                    $attachment = isset($br_hidden_attachment[$key]) ? $br_hidden_attachment[$key] : '';
                }
            }
            $paid_status = isset($br_is_paid_arr[$key]) ? $br_is_paid_arr[$key] : '';
            $status      = 'Unpaid';
            $is_paid     = 0;
            if ($paid_status == 'Paid') {
                $is_paid = 1;
                $status  = $paid_status;
            }
            $tempFile = isset($br_attachment_tmp_arr[$key]) ? $br_attachment_tmp_arr[$key] : array();
            if (!empty($tempFile)) {
                $br_file_name_Arr = array();
                foreach ($tempFile as $tkey => $tval) {
                    $targetPath = FCPATH . "/files_upload/bill_attachment/" . $bill_id . "/";

                    if (!file_exists($targetPath)) {
                        mkdir($targetPath, 0777, true);
                    }

                    $file_name          = isset($br_attachment_arr[$key][$tkey]) ? $br_attachment_arr[$key][$tkey] : '';
                    $temp_uploaded_name = isset($tempFile[$key][$tkey]) ? $tempFile[$key][$tkey] : '';
                    if ($temp_uploaded_name != '') {
                        $br_file_name_Arr[] = $file_name;
                        $targetFile         = $targetPath . $file_name; //5
                        $res                = move_uploaded_file($temp_uploaded_name, $targetFile);
                    }
                }
            }

            $br_oneDimensionalArray = [];
            if (isset($br_hidden_attachment[$key]) && !empty($br_hidden_attachment[$key])) {
                foreach ($br_hidden_attachment[$key] as $k1 => $v1) {
                    $br_oneDimensionalArray[] = $v1;
                }
            }
            $br_file_name_Arr_final = array_filter(array_merge($br_oneDimensionalArray, $br_file_name_Arr));
            $br_ins_arr[]           = array("bill_id" => $bill_id,
                "store_key"                               => isset($br_store_arr[$key]) ? $br_store_arr[$key] : '',
                "store_physical_address"                  => isset($br_store_physical_address_arr[$key]) ? $br_store_physical_address_arr[$key] : '',
                "bill_date"                               => isset($br_bill_date_arr[$key]) ? date("Y-m-d", strtotime($br_bill_date_arr[$key])) : '',
                "bill_no"                                 => isset($br_bill_no_arr[$key]) ? $br_bill_no_arr[$key] : '',
                "category_key"                            => isset($br_category_arr[$key]) ? $br_category_arr[$key] : '',
                "description"                             => isset($br_description_arr[$key]) ? $br_description_arr[$key] : '',
                "breakdown_description"                   => isset($br_breakdown_description_arr[$key]) ? $br_breakdown_description_arr[$key] : '',
                "qty"                                     => isset($br_qty_arr[$key]) ? $br_qty_arr[$key] : '',
                "rate"                                    => isset($br_rate_arr[$key]) ? $br_rate_arr[$key] : '',
                "amount"                                  => isset($br_amount_arr[$key]) ? $br_amount_arr[$key] : '',
                "last_paid_date"                          => isset($br_last_paid_date_arr[$key]) ? $br_last_paid_date_arr[$key] : '',
                "last_paid_amount"                        => isset($br_last_paid_amount_arr[$key]) ? $br_last_paid_amount_arr[$key] : '',
                "last_paid_amount"                        => isset($br_status_arr[$key]) ? $br_status_arr[$key] : '',
                "is_paid"                                 => $is_paid,
                "status"                                  => $status,
                "attachment"                              => isset($br_file_name_Arr_final) && !empty($br_file_name_Arr_final) ? implode(",", $br_file_name_Arr_final) : '',
                "type"                                    => 'breakdown_description',
            );

        }
        if (isset($br_ins_arr) && !empty($br_ins_arr)) {
            $this->bill_item_model->Add_Batch($br_ins_arr);
        }

        //check entry
        $bc_store_arr          = array_filter($this->input->post('bc_store_key'));
        $bc_payabke_arr        = array_filter($this->input->post('bc_payable'));
        $bc_check_no_arr       = array_filter($this->input->post('bc_check_no'));
        $bc_memo_arr           = array_filter($this->input->post('bc_memo'));
        $bc_amount_arr         = array_filter($this->input->post('bc_amount'));
        $bc_checkDate_arr      = array_filter($this->input->post('bc_check_date'));
        $bc_hidden_attachment  = isset($_POST['bc_hidden_attachment']) && !empty($_POST['bc_hidden_attachment']) ? array_filter($this->input->post('bc_hidden_attachment')) : array();
        $bc_attachment_arr     = isset($_FILES['bc_attachment']['name']) ? array_filter($_FILES['bc_attachment']['name']) : array();
        $bc_attachment_tmp_arr = isset($_FILES['bc_attachment']['tmp_name']) ? array_filter($_FILES['bc_attachment']['tmp_name']) : array();

        foreach ($bc_store_arr as $key => $val) {
            $attachment = isset($bc_attachment_arr[$key]) ? $bc_attachment_arr[$key] : '';
            if ($bill_id > 0) {
                if ($attachment == '') {
                    $attachment = isset($bc_hidden_attachment[$key]) ? $bc_hidden_attachment[$key] : '';
                }
            }

            $tempFile         = isset($bc_attachment_tmp_arr[$key]) ? $bc_attachment_tmp_arr[$key] : array();
            $bc_file_name_Arr = array();
            if (!empty($tempFile)) {
                $br_file_name_Arr = array();
                foreach ($tempFile as $tkey => $tval) {
                    $targetPath = FCPATH . "/files_upload/bill_attachment/" . $bill_id . "/check/";

                    if (!file_exists($targetPath)) {
                        mkdir($targetPath, 0777, true);
                    }

                    $file_name          = $bc_attachment_arr[$key][$tkey];
                    $temp_uploaded_name = $tempFile[$tkey];
                    $bc_file_name_Arr[] = $file_name;
                    $targetFile         = $targetPath . $file_name; //5
                    $res                = move_uploaded_file($temp_uploaded_name, $targetFile);
                }
            }

            $bc_file_name_Arr_final = [];
            if (isset($bc_hidden_attachment[$key]) && !empty($bc_hidden_attachment[$key])) {
                foreach ($bc_hidden_attachment[$key] as $k1 => $v1) {
                    $chk_oneDimensionalArray[] = $v1;
                }
            }

            $bc_file_name_Arr_final = array_filter(array_merge($chk_oneDimensionalArray, $bc_file_name_Arr));

            $bc_ins_arr[] = array("bill_id" => $bill_id,
                "bc_store_key"                  => isset($bc_store_arr[$key]) ? $bc_store_arr[$key] : '',
                "bc_payable"                    => isset($bc_payabke_arr[$key]) ? $bc_payabke_arr[$key] : '',
                "bc_check_no"                   => isset($bc_check_no_arr[$key]) ? $bc_check_no_arr[$key] : '',
                "bc_memo"                       => isset($bc_memo_arr[$key]) ? $bc_memo_arr[$key] : '',
                "bc_amount"                     => isset($bc_amount_arr[$key]) ? $bc_amount_arr[$key] : '',
                "bc_check_date"                 => isset($bc_checkDate_arr[$key]) && $bc_checkDate_arr[$key] != '' ? date("Y-m-d", strtotime($bc_checkDate_arr[$key])) : '',
                "bc_attachment"                 => isset($bc_file_name_Arr_final) && !empty($bc_file_name_Arr_final) ? implode(",", $bc_file_name_Arr_final) : '',
            );

        }
        if (isset($bc_ins_arr) && !empty($bc_ins_arr)) {
            $this->bill_item_model->Add_Batch($bc_ins_arr, 'bill_check_entries');
        }

        if ($bill_id > 0) {
            $this->session->set_flashdata('success', 'Bill detail has been updated successfully');
        } else {
            $this->session->set_flashdata('success', 'Bill detail has been inserted successfully');
        }
        redirect('bill', 'redirect');
    }
    public function get_prev_data()
    {
        $store       = $this->input->post('store');
        $category    = $this->input->post('category');
        $description = $this->input->post('description');
        $where_arr   = array("store_key" => $store, "category_key" => $category, "description" => $description);
        $Res         = $this->bill_item_model->get_latest_item($where_arr);
        $billDate    = isset($Res->bill_date) && $Res->bill_date != '' ? DB2Disp($Res->bill_date) : '';
        $amt         = isset($Res->amount) ? $Res->amount : '';
        echo json_encode(array("bill_date" => $billDate, "amount" => $amt));
        exit;
    }
    public function get_br_prev_data()
    {
        $store                    = $this->input->post('store');
        $category                 = $this->input->post('category');
        $description              = $this->input->post('description');
        $br_breakdown_description = $this->input->post('br_breakdown_description');
        $where_arr                = array("store_key" => $store, "category_key" => $category, "description" => $description, "breakdown_description" => $br_breakdown_description);
        $Res                      = $this->bill_item_model->get_latest_item($where_arr);
        $billDate                 = isset($Res->bill_date) && $Res->bill_date != '' ? DB2Disp($Res->bill_date) : '';
        $amt                      = isset($Res->amount) ? $Res->amount : '';
        echo json_encode(array("bill_date" => $billDate, "amount" => $amt));
        exit;
    }
    public function update_status()
    {
        $id     = $this->input->post('bill_id');
        $status = $this->input->post('status');
        if ($status == 'paid') {
            $up_data['status']  = 'Paid';
            $up_data['is_paid'] = 1;
        } else {
            $up_data['status']  = 'Unpaid';
            $up_data['is_paid'] = 0;
        }
        $this->bill_item_model->Edit($id, $up_data);
        echo json_encode(array("status" => 'success'));
        exit;
    }
    public function get_checkno_data()
    {
        $this->load->model('checkbook_model');
        $store     = $this->input->post('store');
        $sql       = "SELECT * FROM `checkbook_record`  LEFT JOIN `ledger` ON ledger.id = checkbook_record.ledger_id WHERE ledger.store_key = " . $store . " ORDER BY checkbook_record.id DESC LIMIT 1";
        $check_Res = $this->checkbook_model->query_result($sql);
        if (!empty($check_Res)) {
            $data['check_no'] = isset($check_Res[0]->check_number) ? $check_Res[0]->check_number + 1 : '';
        } else {
            $data['check_no'] = $this->storeCheckArr[$store];
        }
        echo json_encode(array("status" => "success", "check_no" => $data['check_no']));
        exit;
    }
}
