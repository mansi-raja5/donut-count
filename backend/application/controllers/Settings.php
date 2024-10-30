<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Settings extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('settings_model');
        $this->load->model('store_master_model');
        $this->load->model('auto_reconcilation_model');
    }
    public function autoreconsetting()
    {
        try {
            $data['title']              = "Auto Reconcilation Settings";
            $data['auto_reconcilation'] = $this->auto_reconcilation_model->Get();
            $postData                   = $this->postData();
            if ($postData) {
                $id   = isset($postData['desc_id']) ? $postData['desc_id'] : 0;
                $type = isset($postData['type']) ? $postData['type'] : '';

                if ($type == 'delete') {
                    $result = $this->auto_reconcilation_model->Delete($id);
                } else {
                    $insertData['ledger_desc'] = $postData['ledger_desc'];
                    $insertData['bank_desc']   = $postData['bank_desc'];
                    $insertData['created_on']  = date('Y-m-d H:i:S');
                    if ($id) {
                        $result = $this->auto_reconcilation_model->Edit($id, $insertData);
                    } else {
                        $result = $this->auto_reconcilation_model->Add($insertData);
                    }

                }
                echo json_encode(array("status" => "success"));
                exit;
            }
            $this->template->load('listing', 'settings/auto_recon_settings', $data);
        } catch (Exception $e) {
            //alert the user then kill the process
            og_message('error', $e->getMessage());
            return;
        }
    }
    public function postData()
    {
        $data = $this->input->post();
        return $data;
    }
    public function general_setting()
    {
        $data['title']            = 'General Settings';
        $data['general_settings'] = $this->settings_model->getData(null, "admin_settings");
        $this->template->load('listing', 'settings/general_settings', $data);
    }
    public function edit()
    {
        $data['title']            = 'Edit Settings';
        $data['general_settings'] = "";
        $table                    = "";
         $data['store_list']     = $this->store_master_model->Get(null, array("status" => 'A'));
        if ($this->uri->segment(3) == "general") {
            $table    = "admin_settings";
            $template = "add_general_settings";
        } elseif ($this->uri->segment(3) == "exclude") {
            $data['action']         = "edit";
            $table                  = "admin_excludecalculation_settings";
            $template               = "add_excludecalculation_settings";
            $data['dynamic_column'] = $this->settings_model->getDynamiccolumn("dynamic_excludecalculation_column", "key_name", "key_label");
        } else if ($this->uri->segment(3) == "labor") {
            $data['action']     = "edit";
            $table              = "admin_labor_bonus_setting";
            $template           = "add_laborbonus_settings";
        }
        $data['id']               = $this->uri->segment(4);
        $where                    = array("id" => $data['id']);
        $data['general_settings'] = $this->settings_model->getData($where, $table);
        $this->template->load('listing', 'settings/' . $template, $data);

    }
    public function add()
    {
        $data['title']            = 'Add Settings';
        $data['general_settings'] = "";
        if ($this->uri->segment(3) == "general") {
            $template = "add_general_settings";
        } elseif ($this->uri->segment(3) == "exclude") {
            $data['action']         = "add";
            $data['store_list']     = $this->store_master_model->Get(null, array("status" => 'A'));
            $data['dynamic_column'] = $this->settings_model->getDynamiccolumn("dynamic_excludecalculation_column", "key_name", "key_label");
            $template               = "add_excludecalculation_settings";
        } elseif ($this->uri->segment(3) == "labor") {
            $data['action']     = "add";
            $data['store_list'] = $this->store_master_model->Get(null, array("status" => 'A'));
            $template           = "add_laborbonus_settings";
        }
        $this->template->load('listing', 'settings/' . $template, $data);
    }
    public function delete()
    {
        $id = $this->uri->segment(4);

        $table  = "admin_settings";
        $result = $this->settings_model->delete($table, $id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Admin settings deleted successfully!!!</div>');
        redirect("settings/general_setting");
    }
    public function save()
    {
        $data   = $this->postData();
       
        $result = $this->settings_model->save("admin_settings", $data);
        if (isset($data['id'])) {
            $msg = "has changed";
        } else {
            $msg = "added successfully";
        }

        $this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Admin settings ' . $msg . ' successfully!!!</div>');
        redirect("settings/general_setting");
    }
    //store settings
    public function store_setting()
    {
        $data['title']      = 'Store Settings';
        $data['store_list'] = $this->store_master_model->Get(null, array("status" => 'A'));
        $data['pos_key']    = $this->settings_model->getDynamiccolumn("pos_master_key", "key_name", "key_label");
        $this->template->load('listing', 'settings/store_settings', $data);
    }

    //year settings
    public function yearsetting()
    {
        $this->load->model('year_setting_model');
        $postData = $this->postData();
        if (count($postData)) {
            $yearData = [];
            $weeks    = json_decode($postData['weeks'], true);

            foreach ($postData['monthweeks'] as $monthnumber => $_monthweeks) {
                $yearData[] = array(
                    'year'               => $postData['year'],
                    'year_starting_date' => date('Y-m-d', strtotime($postData['year_starting_date'])),
                    'year_weeks'         => $postData['yearweeks'],
                    'month'              => $monthnumber,
                    'month_weeks'        => $_monthweeks,
                    'weeks'              => json_encode($weeks[$monthnumber]),
                    'week_position'      => $postData['week_position'],
                    'is_shifted'         => ($postData['is_shifted'] ?? null),
                    'created_on'         => date('Y-m-d'),
                );
            }
            $this->year_setting_model->deleteByYear($postData['year']);
            $this->year_setting_model->Add_Batch($yearData);
            $data['selected_year'] = $postData['year'];
            $data['week_position'] = $postData['week_position'];
            $data['is_shifted'] = $postData['is_shifted'] ?? null;
            $this->session->set_flashdata('msg', "Year Settings Saved.");
        }
        $data['title'] = 'Year Settings';
        $this->template->load('listing', 'settings/year/year', $data);
    }

    //royalty settings
    public function royaltysetting()
    {
        $this->load->model('royal_setting_model');
        $postData  = $this->input->post();
        $sql       = "SELECT * FROM `store_master` SM ORDER BY SM.key";
        $stores    = $this->royal_setting_model->query_result($sql);
        $royalType = ['DD', 'BR'];

        $sql               = "SELECT * FROM `admin_royalty`";
        $adminSetting      = $this->royal_setting_model->query_result($sql);
        $adminSettingStore = [];
        foreach ($adminSetting as $_adminSetting) {
            $adminSettingStore[$_adminSetting->store_key][$_adminSetting->type] = $_adminSetting;
        }

        $postData = $this->postData();
        if (count($postData)) {
            $saveAdminSettingData = [];
            foreach ($postData['royal'] as $storekey => $_royalSettingData) {
                foreach ($_royalSettingData as $_royalType) {
                    $saveAdminSettingData[] = array(
                        'store_key'             => $_royalType['store_key'],
                        'type'                  => $_royalType['royal_type'],
                        'royalty_percentage'    => $_royalType['royalty_per'],
                        'adfund_percentage'     => $_royalType['adfund_per'],
                        'customer_count_for_br' => $_royalType['cust_count'],
                        'created_on'            => date('Y-m-d'),
                    );
                }
            }
            $this->db->query("DELETE FROM `admin_royalty`");
            $this->royal_setting_model->Add_Batch($saveAdminSettingData);
        }
        if (count($stores)) {
            foreach ($stores as $_stores) {
                foreach ($royalType as $_royalType) {
                    $data['royal_data'][$_stores->key][$_royalType]['store_key']             = $_stores->key;
                    $data['royal_data'][$_stores->key][$_royalType]['type']                  = $_royalType;
                    $data['royal_data'][$_stores->key][$_royalType]['royalty_percentage']    = isset($adminSettingStore[$_stores->key][$_royalType]) ? $adminSettingStore[$_stores->key][$_royalType]->royalty_percentage : 0;
                    $data['royal_data'][$_stores->key][$_royalType]['adfund_percentage']     = isset($adminSettingStore[$_stores->key][$_royalType]) ? $adminSettingStore[$_stores->key][$_royalType]->adfund_percentage : 0;
                    $data['royal_data'][$_stores->key][$_royalType]['customer_count_for_br'] = isset($adminSettingStore[$_stores->key][$_royalType]) ? $adminSettingStore[$_stores->key][$_royalType]->customer_count_for_br : 0;
                }
            }
        }
        $data['title'] = 'Royalty Settings';
        $this->template->load('listing', 'settings/royalty_settings', $data);
    }

    public function getyearmonthsetting()
    {
        $this->load->model('year_setting_model');
        $postData     = $this->postData();
        $year         = $this->year_setting_model->getByYear($postData['year']);
        $ledger       = $this->year_setting_model->getLedger($postData['year']);
        $data['year'] = [];
        foreach ($year as $_year) {
            $data['year_starting_date']   = $_year->year_starting_date;
            $data['weeks'][$_year->month] = json_decode($_year->weeks);
            $data['year'][$_year->month]  = $_year;
            $data['year_weeks']           = $_year->year_weeks;
            $data['week_position']        = $_year->week_position;
            $data['is_shifted']           = $_year->is_shifted;
        }
        $data['ledger_month'] = [];
        foreach ($ledger as $_ledger) {
            $data['ledger_month'][] = $_ledger->month;
        }
        $lastYearLastMonthRecord = $this->year_setting_model->getByYear($postData['year'] - 1, 12, true);
        if (!is_null($lastYearLastMonthRecord)) {
            $weeks = json_decode($lastYearLastMonthRecord->weeks);
            $lastEndOfWeek = $weeks[$lastYearLastMonthRecord->month_weeks - 1]->end_of_week;
            if (!is_null($lastEndOfWeek)) {
                $data['default_year_starting_date'] = date('Y-m-d', strtotime('next sunday', strtotime($lastEndOfWeek)));
            }
        }
        $data['year_weeks_post'] = $postData['year_week'] ?? null;
        echo $this->load->view('settings/year/month', $data, true);
    }

    public function getMonthStartDateEnddateYearly()
    {
        $this->load->model('year_setting_model');
        $postData            = $this->postData();
        $year_starting_date  = $postData['year_starting_date'];
        $no_of_weeks_in_year = $postData['no_of_weeks_in_year'];
        $monthweeks          = $postData['monthweeks'];

        $allWeeks       = getyearlymonthstartenddate($year_starting_date, $no_of_weeks_in_year);
        $monthWiseWeeks = [];
        $weeknumber     = 1;
        foreach ($monthweeks as $_monthNumber => $_noOfWeeks) {
            for ($i = 1; $i <= $_noOfWeeks; $i++) {
                if ($weeknumber <= $no_of_weeks_in_year) {
                    $monthWiseWeeks[$_monthNumber][] = $allWeeks[$weeknumber];
                    $weeknumber++;
                }
            }
        }
        echo json_encode($monthWiseWeeks);
    }

    public function getstore()
    {
        $data   = $this->postData();
        $where  = array("store_key" => $data['store_key'], "year" => $data['year']);
        $result = $this->settings_model->getData($where, "admin_store_setting");
        if (sizeof($result) > 0) {
            $result = $result[0]['data'];
        } else {
            $result = "1";
        }
        echo $result;
        exit;
    }

    public function savestore()
    {
        $data   = $this->postData();
        $result = $this->settings_model->savestore("admin_store_setting", $data);
        echo $result;
    }

    //excludecalculation setting

    public function exclude()
    {
        $data['title']    = 'Exclude Calculation Settings';
        $data['settings'] = $this->settings_model->getData(null, "admin_excludecalculation_settings");
        $this->template->load('listing', 'settings/excludecalculations_settings', $data);
    }
    public function saveexclude()
    {
        $data              = $this->postData();
        $data['from_date'] = date("Y-m-d", strtotime($data['from_date']));
        if ($data['to_date'] != "") {
            $data['to_date'] = date("Y-m-d", strtotime($data['to_date']));
        } else {
            $data['to_date'] = "";
        }
        $result = $this->settings_model->save("admin_excludecalculation_settings", $data);
        if (isset($data['id'])) {
            $msg = "has changed";
        } else {
            $msg = "added successfully";
        }

        $this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Exclude settings ' . $msg . ' successfully!!!</div>');
        redirect("settings/exclude");
    }

    //labor settings
    public function labor()
    {
        $data['title']            = 'Labor Bonus Settings';
        $data['general_settings'] = $this->settings_model->getData(null, "admin_labor_bonus_setting");
        $this->template->load('listing', 'settings/laborbonus_settings', $data);
    }
    public function savelabor()
    {
        $data = $this->postData();
        if ($data['store_key'] > 0) {
            $data['store_key'] = implode(",", $data['store_key']);
        }

        $result = $this->settings_model->save("admin_labor_bonus_setting", $data);
        if (isset($data['id'])) {
            $msg = "has changed";
        } else {
            $msg = "added successfully";
        }

        $this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Admin settings ' . $msg . ' successfully!!!</div>');
        redirect("settings/labor");
    }
    public function deletelabor()
    {
        $id = $this->uri->segment(3);

        $table  = "admin_labor_bonus_setting";
        $result = $this->settings_model->delete($table, $id);
        $this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>labor bonus settings deleted successfully!!!</div>');
        redirect("settings/labor");
    }

    //Snapshot setting conditional
    public function conditional()
    {
        $data['title']      = 'Conditional Formatting';
        $data['store_list'] = $this->store_master_model->Get(null, array("status" => 'A'));
        $data['pos_key']    = $this->settings_model->getDynamiccolumn("pos_master_key", "key_name", "key_label");
        $this->template->load('listing', 'settings/conditional_settings', $data);
    }

    public function conditional_save()
    {
        $data   = $this->postData();
        $result = $this->settings_model->saveconditional("admin_calculationconditional_settings", $data);
        echo $result;
    }

    public function getconditional()
    {
        $data   = $this->postData();
        $where  = array("year" => $data['year'], "store_key" => $data['store_key']);
        $result = $this->settings_model->getData($where, "admin_calculationconditional_settings");

        $html = "";
        if (sizeof($result) > 0) {
            $pos_key    = $this->settings_model->getDynamiccolumn("pos_master_key", "key_name", "key_label");
            $store_list = $this->store_master_model->Get(null, array("status" => 'A'));
            $html .= "<tr id=" . $data['counter'] . ">";
            $html .= '<td><select name="pos_key" id="poskey_' . $data['counter'] . '" required>
                                    <option value="">Select Key</option>';
            if (isset($pos_key)):
                foreach ($pos_key as $key => $value):
                    $selected = "";
                    if ($key == $result[0]['pos_key']) {
                        $selected = "selected";
                    }
                    $html .= '<option value="' . $key . '" ' . $selected . '>' . $value . '</option>';
                endforeach;
            endif;
            $html .= '</select>';
            $html .= '</td>';
            $html .= '<td><select name="value_type" id="valuetype_' . $data['counter'] . '" required>
                                    <option value="">Select Value</option>';
            $selected = "";
            if ($result[0]['value_type'] == "Percentage") {
                $selected = "selected";
            }
            $html .= '<option value="Percentage"' . $selected . '>Percentage</option>';
            $selected = "";
            if ($result[0]['value_type'] == "amount") {
                $selected = "selected";
            }
            $html .= '<option value="amount" ' . $selected . '>Amount</option>
                                </select>
                            </td>';
            $html .= '<td>
                                <select name="expression_type" id="expressiontype_' . $data['counter'] . '" required>
                                    <option value="">Select Expression </option>';
            $selected = "";
            if ($result[0]['expression_type'] == "<") {
                $selected = "selected";
            }
            $html .= '<option value="<" ' . $selected . '><</option>';
            $selected = "";
            if ($result[0]['expression_type'] == "<=") {
                $selected = "selected";
            }
            $html .= '<option value="<=" ' . $selected . '><=</option>';
            $selected = "";
            if ($result[0]['expression_type'] == ">") {
                $selected = "selected";
            }
            $html .= '<option value=">" ' . $selected . '>></option>';
            $selected = "";
            if ($result[0]['expression_type'] == ">=") {
                $selected = "selected";
            }
            $html .= '<option value=">="> ' . $selected . '>=</option>';
            $html .= '</select>
                            </td>';
            if (isset($store_list)):
                foreach ($store_list['records'] as $row):
                    $color  = "#FFFFFF";
                    $amount = "";
                    if ($data['store_key'] == $row->key) {
                        $color  = $result[0]['color'];
                        $amount = $result[0]['amount'];

                    }
                    $html .= '<td><input type="number" name="amount" value="' . $amount . '" id="amount_' . $row->key . '_' . $data['counter'] . '" class="form-control">&nbsp;<input type="color" class="form-control" id="color_' . $row->key . '_' . $data['counter'] . '" name="color" value="' . $color . '">
                                                </td>';
                endforeach;
            endif;
            $html .= "</tr>";
        }
        echo $html;
    }

    public function dbcleansetting() {

        if($postData = $this->postData())
        {
            if( isset($postData['type']) && ($postData['type'] == "ledger" || $postData['type'] == "all")) {
                $tables[] = 'checkbook_record';
                $tables[] = 'ledger_attachments';
                $tables[] = 'ledger_credit_received_from';
                $tables[] = 'ledger_document';
                $tables[] = 'ledger_statement';
                $tables[] = 'ledger_statement_comment';
                $tables[] = 'ledger_statement_splits';
                $tables[] = 'ledger';
            } 
            
            if(  isset($postData['type']) && ($postData['type'] == "bank" || $postData['type'] == "all")) {
                $tables[] = 'bank_statement_entries';
                $tables[] = 'bank_statement';
            } 
    
            if(  isset($postData['type']) && ($postData['type'] == "reconcilation" || $postData['type'] == "all")) {
                $tables[] = 'reconcile_document';
            }         
            
            if(  isset($postData['type']) && ($postData['type'] == "bill" ||$postData['type'] == "all")) {
                $tables[] = 'bill_item_entries';
                $tables[] = 'bill_check_entries';
                $tables[] = 'bill_category';
                $tables[] = 'bill_breakdown_category';
                $tables[] = 'bill';
            } 
            
            if(  isset($postData['type']) &&  ($postData['type'] == "donut" || $postData['type'] == "all")) {
                $tables[] = 'donut_count';         
            } 
            
            if( isset($postData['type']) &&  ($postData['type'] == "labor" || $postData['type'] == "all")) {
                $tables[] = 'labor_summary';
            }
    
            if( isset($postData['type']) &&  ($postData['type'] == "payroll" || $postData['type'] == "all")) {
                $tables[] = 'master_payroll';
            } 
            
            if( isset($postData['type']) && ($postData['type'] == "pos" || $postData['type'] == "all")) {
                $tables[] = 'master_pos_daily';
                $tables[] = 'master_pos_weekly';
                $tables[] = 'master_pos_monthly';
                $tables[] = 'monthly_recap';
                $tables[] = 'paid_out_recap';
                $tables[] = 'card_recap';
                $tables[] = 'delivery_recap';
                $tables[] = 'dailysales_attachments_upload';
            }
            
            if( isset($postData['type']) && $postData['type'] == "all") {
                $tables[] = 'admin_royalty';
                $tables[] = 'cars_entry';
                $tables[] = 'customer_review';
                $tables[] = 'royalty';
                $tables[] = 'season';
                $tables[] = 'special_day';
                $tables[] = 'vendor';
                $tables[] = 'vendor_attachments';            
            }
    
            if($tables && count($tables)){
                $this->settings_model->dbcleansetting($tables);
                $msg = "Following Tables are cleaned up! <br><br>";
                $msg .= implode("<br>",$tables);
                $msg .= "<br><br>Enjoy! Have a good day!";
                $this->session->set_flashdata('msg', '<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' . $msg . '</div>');
                redirect("settings/dbcleansetting");                
            }
        }
        $data['title'] = 'DB cleanup';
        $this->template->load('listing', 'settings/dbcleanup_settings', $data);        
    }
}
