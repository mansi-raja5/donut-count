<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Vendors extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model('vendor_model');
        $this->load->model('vendor_attachments_model');
        $this->load->model('category_model');
        $this->load->model('bill_model');
        $this->load->model('bill_item_model');
        $this->load->model('store_master_model');
    }

    public function index() {
        if ($this->input->is_ajax_request()) {
            $data = $this->vendor_model->Get(NULL, $this->input->post());
            $this->getListing($data);
        } else {
            $data['title'] = "All Vendors";
            $this->template->load('listing', 'vendor/listcontacts', $data);
        }
    }
    
    public function billListing(){
        if ($this->input->is_ajax_request()) {
            $data = $this->bill_model->Get();
            $this->getListing($data);
        } else {
            $data['title'] = "All Bills";
            $this->template->load('listing', 'vendors/listbills', $data);
        }
    }
        
    public function listbills(){
        
    }

    public function getListing($result = array()) {
        $tableData = array();
        foreach ($result['records'] as $key => $row) {
            $name = $row->username;
            $tableData[$key]['company'] = '<a href="' . base_url() . '/vendors/view/' . $row->id . '">' . $row->company . '</a>';
            $tableData[$key]['name'] = $name;
            $tableData[$key]['password'] = $row->password;
            $tableData[$key]['schedule_payment_date'] = DB2Disp($row->schedule_payment_date);
            $tableData[$key]['phone'] = $row->phone_no;
            $tableData[$key]['email'] = $row->email;
            $tableData[$key]['address'] = $row->phys_addr1;
            $tableData[$key]['preferred_payment_method'] = $row->preferred_payment_method;
            $tableData[$key]['notes'] = $row->notes;
            $tableData[$key]['id'] = $row->id;
        }
        $data['recordsTotal'] = $result['countTotal'];
        $data['recordsFiltered'] = $result['countFiltered'];
        $data['data'] = $tableData;
        echo json_encode($data);
    }

    public function entry($id = NULL) {

        $data['title'] = "Entry of Vendor";
        $this->template->load('listing', 'vendor/addcontacts', $data);
    }

    public function view($id) {
        $data['title'] = "View Vendor";
        if ($id) {

            $record = $this->vendor_model->Get($id);
            $data['phonedata'] = array(
                'id' => (isset($record['records']->id)) ? $record['records']->id : '',
                'username' => (isset($record['records']->username)) ? $record['records']->username : '',
                'password' => (isset($record['records']->password)) ? $record['records']->password : '',
                'name_f' => (isset($record['records']->name_f)) ? $record['records']->name_f : '',
                'name_m' => (isset($record['records']->name_m)) ? $record['records']->name_m : '',
                'name_l' => (isset($record['records']->name_l)) ? $record['records']->name_l : '',
                'company' => (isset($record['records']->company)) ? $record['records']->company : '',
                'attention' => (isset($record['records']->attention)) ? $record['records']->attention : '',
                'phys_addr1' => (isset($record['records']->phys_addr1)) ? $record['records']->phys_addr1 : '',
                'phys_addr2' => (isset($record['records']->phys_addr2)) ? $record['records']->phys_addr2 : '',
                'phys_city' => (isset($record['records']->phys_city)) ? $record['records']->phys_city : '',
                'phys_state' => (isset($record['records']->phys_state)) ? $record['records']->phys_state : '',
                'phys_zip' => (isset($record['records']->phys_zip)) ? $record['records']->phys_zip : '',
                'email' => (isset($record['records']->email)) ? $record['records']->email : '',
                'website' => (isset($record['records']->website)) ? $record['records']->website : '',
                'preferred_payment_method' => (isset($record['records']->preferred_payment_method)) ? $record['records']->preferred_payment_method : '',
                'account_no' => (isset($record['records']->account_no)) ? $record['records']->account_no : '',
                'emp_no' => isset($record['records']->emp_no) ? $record['records']->emp_no : '',
                'phone_no' => isset($record['records']->phone_no) ? $record['records']->phone_no : '',
                'notes' => isset($record['records']->notes) ? $record['records']->notes : '',
                'schedule_payment_date' => isset($record['records']->schedule_payment_date) ? $record['records']->schedule_payment_date : '',
                'bill_due_date' => isset($record['records']->bill_due_date) ? $record['records']->bill_due_date : '',
                'recurring_period' => isset($record['records']->recurring_period) ? $record['records']->recurring_period : '',
            );


            //phonebook attachments
            if (isset($record['records']->id)) {
                $where = array('vendor_id' => $record['records']->id);
                $data['phone_attachments'] = $this->vendor_attachments_model->GetFromField($where);
            } else {
                $data['phone_attachments'] = array();
                $data['phone_attachments_docs'] = array();
            }
        }
        $this->template->load('listing', 'vendor/addcontacts', $data);
    }

    public function delete() {
        $id = $this->input->post('phone_book_id');


        //phonebook attachment record
        $where3 = array('vendor_id' => $id);
        $phonebook_attach = $this->vendor_attachments_model->GetFromField($where3);

        foreach ($phonebook_attach['records'] as $val3) {
            $attachment = $this->vendor_attachments_model->Get($val3['id']);
            if (isset($attachment) && !empty($attachment)) {
                if (file_exists($attachment->file_md5)) {
                    unlink($attachment->file_md5);
                }
                $this->vendor_attachments_model->Delete($val3->id);
            }
        }


        //Remove all attachment related to this phonebook
        $dir = FCPATH . "/files_upload/vendor_attachment/" . $id;
        recursiveRemove($dir);
        if (file_exists(FCPATH . "/files_upload/vendor_attachment/" . $id)) {
            rmdir(FCPATH . "/files_upload/vendor_attachment/" . $id);
        }

        $this->vendor_model->Delete($id);

        //log entry of phonebook
        $this->session->set_flashdata('success', 'You have successfully Deleted Phone Book Entry!');
        redirect('vendors');
    }

    public function convert($size, $unit) {
        if ($unit == "KB") {
            return $fileSize = round($size / 1024, 2) . 'KB';
        }
        if ($unit == "MB") {
            return $fileSize = round($size / 1024 / 1024, 2) . 'MB';
        }
        if ($unit == "GB") {
            return $fileSize = round($size / 1024 / 1024 / 1024, 2) . 'GB';
        }
    }

    public function deleteAttach() {
        $id = $this->input->post('attach_id');
        $vendor_id = $this->input->post('Phb_id');
        $cdocs_id = $this->input->post('cdocs_id');

        $DocRes = $this->vendor_docs_model->Get($cdocs_id);


        $this->vendor_attachments_model->Delete($id);
        $this->vendor_docs_model->Delete($cdocs_id);

        if (isset($DocRes) && !empty($DocRes)) {
            $files = $DocRes->current_fname . "." . $DocRes->current_fext;

            $extension = $DocRes->current_fext;
            $name = $DocRes->current_fname;
            $thumbnail_status = $DocRes->thumbnail_status;
            $img_ext_arr = array("png", "jpg", "jpeg", "gif");

            if (in_array($extension, $img_ext_arr) && $thumbnail_status == 1) {
                $image_path = FCPATH . 'files_upload/user_docs/' . $this->company_id . '/phone_attachments/' . $vendor_id . '/docs/' . $files;
            } else {
                $image_path = FCPATH . 'files_upload/user_docs/' . $this->company_id . '/phone_attachments/' . $vendor_id . '/' . $files;
            }
            if (file_exists($image_path)) {
                unlink($image_path);
                if (isset($vendor_id) && $vendor_id != '') {
                    if (in_array($extension, $img_ext_arr)) {
                        $thumb_path = FCPATH . 'files_upload/user_docs/' . $this->company_id . '/phone_attachments/' . $vendor_id . '/thumb/' . $files;
                    } else {
                        $thumb_path = FCPATH . 'files_upload/user_docs/' . $this->company_id . '/phone_attachments/' . $vendor_id . '/thumb/' . $name . ".png";
                        $mask = FCPATH . 'files_upload/user_docs/' . $this->company_id . '/' . $vendor_id . '/thumb/' . $name . '*.*';

                        array_map('unlink', glob($mask));
                    }
                    if (file_exists($thumb_path)) {
                        unlink($thumb_path);
                    }
                }
            }
        }

        echo json_encode(array('status' => 'success', 'id' => $id));
        exit;
    }

    public function dropzone() {
        $id = $this->input->get_post('id');

        if (!empty($_FILES)) {

            $tempFile = $_FILES['file']['tmp_name'];          //3  
            $filename = $_FILES['file']['name'];          //3  

            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $name = pathinfo($_FILES['file']['name'], PATHINFO_FILENAME);

            if (!file_exists(FCPATH . "/files_upload/vendor_attachment/tmp")) {
                mkdir(FCPATH . "/files_upload/vendor_attachment/tmp", 0777, true);
            }
            $targetPath = FCPATH . "/files_upload/vendor_attachment/tmp/";
            $upload_file_name = ($name) . "_" . uniqid() . "." . $extension;
            $targetFile = $targetPath . $upload_file_name;  //5
            $res = move_uploaded_file($tempFile, $targetFile); //6
            if ($res) {
                echo json_encode(array("name" => $upload_file_name, "size" => filesize($targetFile)));
                exit;
            }
//            }
        }
    }

    public function delete_all_files() {
        $files = glob(FCPATH . "/files_upload/vendor_attachment/tmp/*"); // get all file names
        foreach ($files as $file) { // iterate files
            if (is_file($file))
                unlink($file); // delete file
        }
        echo "success";
        exit;
    }

    public function add_customer_detail() {
        $id = $this->input->post('id');
        $emp_no = $this->input->post('emp_no');

        $general_tab_data = array(
            'username' => $this->input->post('username'),
            'password' => ($this->input->post('password')),
            'name_f' => $this->input->post('first_name'),
            'name_m' => $this->input->post('middle_name'),
            'name_l' => $this->input->post('last_name'),
            'company' => $this->input->post('company'),
            'email' => $this->input->post('email'),
            'website' => $this->input->post('website'),
            'emp_no' => isset($emp_no) ? $emp_no : '',
            'phys_addr1' => $this->input->post('address_1'),
            'phys_addr2' => $this->input->post('address_2'),
            'phys_city' => $this->input->post('city'),
            'phys_state' => $this->input->post('state'),
            'phys_zip' => $this->input->post('zip'),
            'preferred_payment_method' => $this->input->post('payment_method'),
            'account_no' => $this->input->post('account_no'),
            'notes' => $this->input->post('notes'),
            'phone_no' => $this->input->post('phone_no'),
            'recurring_period' => $this->input->post('recurring_period'),
            'schedule_payment_date' => date("Y-m-d", strtotime($this->input->post('schedule_payment_date'))),
            'bill_due_date' => date("Y-m-d", strtotime($this->input->post('bill_due_date')))
        );
        if ($id == "") {
            $id = $this->vendor_model->Add($general_tab_data);
            $operation = 'Add';
        } else {
            $this->vendor_model->Edit($id, $general_tab_data);
            $operation = 'Edit';
        }


        if (file_exists(FCPATH . "/files_upload/vendor_attachment/tmp")) {
            // Get array of all source files
            $files = scandir(FCPATH . "/files_upload/vendor_attachment/tmp");

            // Identify directories
            $source = FCPATH . "/files_upload/vendor_attachment/tmp/";
            $foldername = $id;

            if (!is_dir('files_upload/vendor_attachment/' . $foldername)) {
                mkdir('files_upload/vendor_attachment/' . $foldername);
                chmod('files_upload/vendor_attachment/' . $foldername, 0777);
            }

            $destination = "files_upload/vendor_attachment/" . $foldername . "/";

            // Cycle through all source files
            foreach ($files as $file) {
                if (in_array($file, array(".", "..")))
                    continue;

                $original_extension = (pathinfo($file, PATHINFO_EXTENSION));
                $moved_filename = md5(file_get_contents($source . $file)) . "." . $original_extension;
                // If we copied this successfully, mark it for deletion
                if (copy($source . $file, $destination . $moved_filename)) {
                    $delete[] = $source . $file;
                }
            }
        }

        // Delete all successfully-copied files
        if (isset($delete) && !empty($delete)) {
            foreach ($delete as $file) {

                $get_filename_arr = explode("/", $file);
                $get_filename = end($get_filename_arr);
                $ext = pathinfo($get_filename, PATHINFO_EXTENSION);
//
                $filename_arr = explode("_", $get_filename);
                array_pop($filename_arr);
                $filename_str = implode("_", $filename_arr);
                $filename = $filename_str . "." . $ext;
//
//                //add files in company_docs
//                $cd_data['vendor_id'] = $id;
//                $cd_data['current_fname'] = md5(file_get_contents($file));
//                $cd_data['current_fext'] = $ext;
//                $cd_data['original_fname'] = $filename;
//                $cd_data['original_fext'] = $ext;
//                $cd_data['original_size'] = filesize($file);
//                $cd_data['compressed_and_optimized'] = 0;
//                $cd_data['thumbnail_status'] = 0;
//                $cd_data['upload_timestamp'] = date("Y-m-d H:i:s");
//                $cd_data['ocr_status'] = 0;
//                $cd_data['ocr_text'] = '';
//                $cd_data['uploaded_url'] = $destination . (md5(file_get_contents($file))) . "." . $ext;
//                $cd_data['module'] = 'Phonebook';
//                $this->vendor_docs_model->Add($cd_data);

                $ins = array(
                    'vendor_id' => $id,
                    'file_md5' => $destination . (md5(file_get_contents($file))) . "." . $ext,
                    'filename' => $filename,
                    'filename_ext' => "." . $ext,
                    'size' => filesize($file),
                    'module' => 'vendor'
                );
                $this->vendor_attachments_model->Add($ins);

                unlink($file);
            }
        }
        $this->session->set_flashdata('msg_class', "success");
        $this->session->set_flashdata('msg', "Record successfully saved");
        echo json_encode(array('status' => 'success', 'id' => $id));
        exit;
//        }
    }

    public function create_sale_product() {
        $res = $this->product_model->Get(NULL, array("p_name" => "Sale"));
        if ($res['countFiltered'] > 0) {
            if (isset($res['records']) && !empty($res['records'])) {
                foreach ($res['records'] as $row) {
                    $p_id = $row->p_id;
                }
            }
        } else {
            $p_id = md5(random_string('alnum', 16) . time());
            $insert_data = array(
                'p_id' => $p_id,
                'p_company_id' => $this->session->userdata('company_id'),
                'p_name' => "Sale",
                'p_sku' => "",
                'p_pc_id' => "",
                'p_low_stock_alert' => 0,
                'p_inventory_asset_acc_id' => "",
                'p_sales_detail' => "",
                'p_sales_price' => 0,
                'p_income_acc_id' => "",
                'p_purchasing_detail' => "",
                'p_cost' => "",
                'p_expense_acc_id' => "",
                'p_image' => "",
                'p_type' => "S",
                'p_is_purchase_vendor' => "Y",
            );
            $this->product_model->Add($insert_data);
        }
        return $p_id;
    }

    public function user_check($str, $parent_id = "") {
        $condition['username'] = $str;
        $this->db->where($condition);
        $num_row = $this->db->get('vendors')->num_rows();

        if ($num_row >= 1) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function renameAttach() {
        $renamefile = $this->input->post('rename');
        $attchid = $this->input->post('attchid');
        $docsid = $this->input->post('docsid');
        $update = array(
            'filename' => $renamefile,
        );
        $this->vendor_attachments_model->Edit($attchid, $update);
        $doc_update = array(
            'original_fname' => $renamefile,
        );
        $this->vendor_docs_model->Edit($docsid, $doc_update);
        echo 'true';
        exit(0);
    }

    public function download_doc($docGuid) {
        $img_extension = array("png", "jpg", "jpeg", "gif");
        $this->load->helper('download');
        $Res = $this->vendor_docs_model->Get($docGuid);
        $file_ext = $Res->current_fext;
        $upload_file_name = $Res->current_fname;
        $original_file_name = $Res->original_fname;
        $company_id = $Res->company_id;
        $id = $Res->vendor_id;
        $thumbnail_status = $Res->thumbnail_status;

        $not_exist = 0;
        if (in_array($file_ext, $img_extension) && $thumbnail_status == 1) {
            if (file_exists(FCPATH . "files_upload/user_docs/" . $company_id . "/phone_attachments/" . $id . "/docs/" . ($upload_file_name) . "." . $file_ext)) {
                $pth = file_get_contents(FCPATH . "files_upload/user_docs/" . $company_id . "/phone_attachments/" . $id . "/docs/" . ($upload_file_name) . "." . $file_ext);
                $nme = $original_file_name;
                force_download($nme, $pth);
            } else {
                $not_exist = 1;
            }
        } else {
            if (file_exists(FCPATH . "files_upload/user_docs/" . $company_id . "/phone_attachments/" . $id . "/docs/" . ($upload_file_name) . "." . $file_ext)) {
                $pth = file_get_contents(FCPATH . "files_upload/user_docs/" . $company_id . "/phone_attachments/" . $id . "/docs/" . ($upload_file_name) . "." . $file_ext);
                $nme = $original_file_name;
                force_download($nme, $pth);
            } else {
                $not_exist = 1;
            }
        }
        if ($not_exist == 1) {
            $this->session->set_flashdata('msg_class', "failure");
            $this->session->set_flashdata('msg', "Your downloaded file is not found");
        }
        redirect('vendors/view/' . $id, 'redirect');
    }

    public function delete_attachment() {
        $files = $this->input->post('file_name');
        $image_path = FCPATH . 'files_upload/user_docs/' . $this->company_id . '/phone_attachments/tmp/' . $files;
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }

    public function export_vendors_report() {
        $category = $this->input->post('cat');
        $selected_pdf_columns = $this->input->post('pdf_columns');
        $terms_master_list = $this->terms_master_model->Get(NULL, array('status' => 'A'));

        $terms_list = array();
        if (isset($terms_master_list['records']) && !empty($terms_master_list['records'])) {
            foreach ($terms_master_list['records'] as $key => $value) {
                $terms_list[$key] = $value->title;
            }
        }
        $_POST['order'][0]['column'] = 0;
        $_POST['order'][0]['dir'] = 'ASC';
        if ($category == 'is_vendor') {
            $data = $this->vendor_model->Get(NULL, $this->input->post(), 'is_vendor', $this->session->userdata('company_id'));
        } else if ($category == 'is_customer') {
            $data = $this->vendor_model->Get(NULL, $this->input->post(), 'is_customer', $this->session->userdata('company_id'));
        } else if ($category == 'is_employee') {
            $data = $this->vendor_model->Get(NULL, $this->input->post(), 'is_employee', $this->session->userdata('company_id'));
        } else {
            $data = $this->vendor_model->Get(NULL, $this->input->post(), NULL, $this->session->userdata('company_id'));
        }

        $tableData = array();
        $export_action = $this->input->post('type');

        foreach ($data['records'] as $key => $row) {
            $tableData[$key]['name'] = $row->name_title . ' ' . $row->name_f . ' ' . $row->name_m . ' ' . $row->name_l . ' ' . $row->name_suff;
            $tableData[$key]['name_display_as'] = $row->name_display_as;
            $tableData[$key]['phone'] = str_replace('<br/>', ', ', $row->phone_number);
            $tableData[$key]['phys_addr1'] = $row->phys_addr1;
            $tableData[$key]['phys_addr2'] = $row->phys_addr2;
            $tableData[$key]['phys_city'] = $row->phys_city;
            $tableData[$key]['phys_state'] = $row->phys_state;
            $tableData[$key]['phys_zip'] = $row->phys_zip;
            $tableData[$key]['attention'] = $row->attention;
            $tableData[$key]['mailing_addr1'] = $row->mailing_addr1;
            $tableData[$key]['mailing_addr2'] = $row->mailing_addr2;
            $tableData[$key]['mailing_city'] = $row->mailing_city;
            $tableData[$key]['mailing_state'] = $row->mailing_state;
            $tableData[$key]['mailing_zip'] = $row->mailing_zip;
            $tableData[$key]['ssn_ein'] = $row->ssn_ein;
            $tableData[$key]['company'] = $row->company;
            $tableData[$key]['email'] = $row->email;
            $tableData[$key]['website'] = $row->website;
            $tableData[$key]['receives_1099'] = ($row->receives_1099 == 1) ? 'Y' : 'N';
            $tableData[$key]['fatcha_filling_required'] = ($row->fatcha_filling_required == 1) ? 'Y' : 'N';
            $tableData[$key]['terms'] = (isset($terms_list[$row->terms])) ? $terms_list[$row->terms] : '';
            $tableData[$key]['billing_rate'] = $row->billing_rate;
            $tableData[$key]['account_no'] = $row->account_no;
            $tableData[$key]['is_system_account'] = $row->is_system_account;
            $tableData[$key]['is_vendor'] = $row->is_vendor;
            $tableData[$key]['is_customer'] = $row->is_customer;
            $tableData[$key]['is_employee'] = $row->is_employee;
            $tableData[$key]['is_bldg_owner'] = $row->is_bldg_owner;
            $tableData[$key]['is_tenant'] = $row->is_tenant;
            $tableData[$key]['emp_no'] = $row->emp_no;
        }

        if ($export_action == 'PDF') {
            $pdf_col_names = array(
                'name_display_as' => 'Name',
                'phone' => 'Phone',
                'phys_addr1' => 'Address 1',
                'phys_addr2' => 'Address 2',
                'phys_city' => 'City',
                'phys_state' => 'State',
                'ssn_ein' => 'SSN / EIN',
                'company' => 'Company',
                'email' => 'Email',
                'website' => 'Website',
                'phys_zip' => 'Zip',
                'receives_1099' => 'Receives 1099',
                'fatcha_filling_required' => 'Fatcha Filling',
                'terms' => 'Terms',
                'billing_rate' => 'Billing Rate',
                'account_no' => 'Account No',
            );
            // PDF Generation 
            include APPPATH . 'third_party/fpdf/cellpdf.php';

            /** Constant for PDF * */
            $height_cell = 10;
            $header_font_size = 9;
            $data_font_size = 9;
            $font_family = "Courier";

            $pdf = new CellPDF();
            $pdf->AliasNbPages();
            $pdf->AddPage('L', 'A4');
            //$pdf->Rect(5, 5, 200, 287, 'D');

            $pdf->SetFont($font_family, '', 14);
            $pdf->SetXY(5, 10);
            $pdf->Cell(0, 4, strtoupper($this->session->userdata('company_name')), 0, 0, 'L');
            $pdf->Line(5, 16, 290, 16);

            $pdf->SetFont($font_family, '', 11);
            $pdf->SetXY(5, 24);
            $pdf->Cell(0, 4, "List of Vendors", 0, 0, 'L');

            $pdf->SetFont($font_family, '', 8);
            $pdf->SetXY(5, 40);

            $column_width = floor(290 / count($selected_pdf_columns));
            $pdf->SetFillColor(231, 236, 241);

            // Header Data
            $pdf->SetFont($font_family, 'B', $header_font_size);
            $count = 1;
            foreach ($pdf_col_names as $key_col => $col_name) {
                if (in_array($key_col, $selected_pdf_columns)) {
                    $is_last = (count($selected_pdf_columns) == $count) ? 1 : 0;
                    $pdf->Cell($column_width, $height_cell, $col_name, 1, $is_last, 'C', true);
                    $count++;
                }
            }
            // Table Data
            $pdf->SetFont($font_family, '', $data_font_size);
            if (!empty($tableData)) {
                foreach ($tableData as $key => $row) {
                    $pdf->SetXY(5, 50 + ($key * 10));
                    $col_count = 1;
                    foreach ($pdf_col_names as $key_col => $col_name) {
                        if (in_array($key_col, $selected_pdf_columns)) {
                            $is_last = (count($selected_pdf_columns) == $col_count) ? 1 : 0;
                            $data = (isset($row[$key_col])) ? $row[$key_col] : '';
                            if (strlen($data) > 20)
                                $data = substr($row[$key_col], 0, 20) . '..';
                            $pdf->Cell($column_width, $height_cell, $data, 1, $is_last, 'L', false);
                            $col_count++;
                        }
                    }
                }
            }

            $filename = 'vendors_' . md5(time()) . '.pdf';
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
                "name" => strtoupper($this->session->userdata('company_name')),
                "company" => "",
                "name_display_as" => "",
                "emp_no" => "",
                "phone" => "",
                "attention" => "",
                "phys_addr1" => "",
                "phys_addr2" => "",
                "phys_city" => "",
                "phys_state" => "",
                "phys_zip" => "",
                "phys_zip4" => "",
                "mailing_addr1" => "",
                "mailing_addr2" => "",
                "mailing_city" => "",
                "mailing_state" => "",
                "mailing_zip" => "",
                "email" => "",
                "website" => "",
                "ssn_ein" => "",
                "receives_1099" => "",
                "fatcha_filling_required" => "",
                "terms" => "",
                "account_no" => "",
                "is_system_account" => "",
                "is_vendor" => "",
                "is_customer" => "",
                "is_employee" => "",
                "is_bldg_owner" => "",
                "is_tenant" => "",
            );

            $report_arr[] = array(
                "name" => "List of Vendors",
                "company" => "",
                "name_display_as" => "",
                "emp_no" => "",
                "phone" => "",
                "attention" => "",
                "phys_addr1" => "",
                "phys_addr2" => "",
                "phys_city" => "",
                "phys_state" => "",
                "phys_zip" => "",
                "mailing_addr1" => "",
                "mailing_addr2" => "",
                "mailing_city" => "",
                "mailing_state" => "",
                "mailing_zip" => "",
                "email" => "",
                "website" => "",
                "ssn_ein" => "",
                "receives_1099" => "",
                "fatcha_filling_required" => "",
                "terms" => "",
                "account_no" => "",
                "is_system_account" => "",
                "is_vendor" => "",
                "is_customer" => "",
                "is_employee" => "",
                "is_bldg_owner" => "",
                "is_tenant" => "",
            );

            $report_arr[] = array(
                "name" => "",
                "company" => "",
                "name_display_as" => "",
                "emp_no" => "",
                "phone" => "",
                "attention" => "",
                "phys_addr1" => "",
                "phys_addr2" => "",
                "phys_city" => "",
                "phys_state" => "",
                "phys_zip" => "",
                "mailing_addr1" => "",
                "mailing_addr2" => "",
                "mailing_city" => "",
                "mailing_state" => "",
                "mailing_zip" => "",
                "email" => "",
                "website" => "",
                "ssn_ein" => "",
                "receives_1099" => "",
                "fatcha_filling_required" => "",
                "terms" => "",
                "account_no" => "",
                "is_system_account" => "",
                "is_vendor" => "",
                "is_customer" => "",
                "is_employee" => "",
                "is_bldg_owner" => "",
                "is_tenant" => "",
            );

            $report_arr[] = array(
                "name" => "Name",
                "company" => "Company",
                "name_display_as" => "Name Display as",
                "emp_no" => "Emp No",
                "phone" => "Phone",
                "attention" => "Attention",
                "phys_addr1" => "Physical Address 1",
                "phys_addr2" => "Physical Address 2",
                "phys_city" => "Physical City",
                "phys_state" => "Physical State",
                "phys_zip" => "Physical Zip",
                "mailing_addr1" => "Mailing Address 1",
                "mailing_addr2" => "Mailing Address 2",
                "mailing_city" => "Mailing City",
                "mailing_state" => "Mailing State",
                "mailing_zip" => "Mailing Zip",
                "email" => "Email",
                "website" => "Website",
                "ssn_ein" => "SSN / EIN",
                "receives_1099" => "Received 1099",
                "fatcha_filling_required" => "Fatcha Filling Required",
                "terms" => "Terms",
                "account_no" => "Account no",
                "is_system_account" => "is_system_account",
                "is_vendor" => "is_vendor",
                "is_customer" => "is_customer",
                "is_employee" => "is_employee",
                "is_bldg_owner" => "is_bldg_owner",
                "is_tenant" => "is_tenant",
            );

            if (!empty($tableData)) {
                foreach ($tableData as $key => $value) {
                    $report_arr[] = array(
                        "name" => $value['name'],
                        "company" => $value['company'],
                        "name_display_as" => $value['name_display_as'],
                        "emp_no" => $value['emp_no'],
                        "phone" => $value['phone'],
                        "attention" => $value['attention'],
                        "phys_addr1" => $value['phys_addr1'],
                        "phys_addr2" => $value['phys_addr2'],
                        "phys_city" => $value['phys_city'],
                        "phys_state" => $value['phys_state'],
                        "phys_zip" => $value['phys_zip'],
                        "mailing_addr1" => $value['mailing_addr1'],
                        "mailing_addr2" => $value['mailing_addr2'],
                        "mailing_city" => $value['mailing_city'],
                        "mailing_state" => $value['mailing_state'],
                        "mailing_zip" => $value['mailing_zip'],
                        "email" => $value['email'],
                        "website" => $value['website'],
                        "ssn_ein" => $value['ssn_ein'],
                        "receives_1099" => $value['receives_1099'],
                        "fatcha_filling_required" => $value['fatcha_filling_required'],
                        "terms" => $value['terms'],
                        "account_no" => $value['account_no'],
                        "is_system_account" => $value['is_system_account'],
                        "is_vendor" => $value['is_vendor'],
                        "is_customer" => $value['is_customer'],
                        "is_employee" => $value['is_employee'],
                        "is_bldg_owner" => $value['is_bldg_owner'],
                        "is_tenant" => $value['is_tenant'],
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
            $this->excel->getActiveSheet()->getStyle("A1:AD4")->getFont()->setBold(true);
            $this->excel->getActiveSheet()->mergeCells('A1:AD1')->getStyle("A1:B1")->applyFromArray($style);
            $this->excel->getActiveSheet()->mergeCells('A2:AD2')->getStyle("A2:B2")->applyFromArray($style);
            $this->excel->getActiveSheet()->mergeCells('A3:AD3')->getStyle("A3:B3")->applyFromArray($style);
            $this->excel->getActiveSheet()->getStyle('A4:AD4')->applyFromArray($border_bottom);
            $this->excel->getActiveSheet()->getStyle('Y5:Y' . $data_count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $this->excel->getActiveSheet()->getStyle('Z5:Z' . $data_count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $this->excel->getActiveSheet()->getStyle('AA5:AA' . $data_count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $this->excel->getActiveSheet()->getStyle('AB5:AB' . $data_count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $this->excel->getActiveSheet()->getStyle('AC5:AC' . $data_count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
            $this->excel->getActiveSheet()->getStyle('AD5:AD' . $data_count)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

            $filename = 'vendors_' . md5(time()) . '.xls';
            $path = FCPATH . "files_upload/reports_excel/";
            $fpath = $path . $filename;

            if (!is_dir('files_upload/reports_excel')) {
                mkdir('files_upload/reports_excel');
                chmod('files_upload/reports_excel', 0777);
            }

            foreach (range('A', 'AD') as $columnID) {
                $this->excel->getActiveSheet()->getColumnDimension($columnID)->setAutoSize(true);
            }
            $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
            $objWriter->save(str_replace(__FILE__, $fpath, __FILE__));

            $Json['flag'] = true;
            $Json['filename'] = $filename;
            echo json_encode($Json);
            exit;
        }
    }

}
