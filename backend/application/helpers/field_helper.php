<?php

function field($validation, $database = null, $default = '')
{
    $value = (isset($validation)) ? $validation : ((isset($database)) ? $database : $default);
    return $value;
}

function checked($db_id, $url_id)
{
    if ($db_id == $url_id) {
        return true;
    }
    return false;
}

function DB2Disp($date)
{
    if (strlen($date) == 19) {
        if ($date != "0000-00-00 00:00:00") {
            return date('m/d/Y h:i:s A', strtotime($date));
        }
    } elseif (strlen($date) == 10) {
        if ($date != "0000-00-00") {
            return date('m/d/Y', strtotime($date));
        }
    }
    return "";
}

function Disp2DB($date)
{
    if (strlen($date) >= 19) {
        return date('Y-m-d H:i:s', strtotime($date));
    } else {
        return date('Y-m-d', strtotime($date));
    }
}

function weeks_in_month($month, $year)
{
    // Get timestamp for the 1st day of the requested month (using current year)
    $startMonth = strtotime($month . '/1/' . $year);
    // Get the ISO week number for the 1st day of the requested month
    $startWeek = date('W', $startMonth);

    // Get timestamp for the last day of the requested month (using current year)
    $endMonth = strtotime('+1 Month -1 Day', $startMonth);
    // Get the ISO week number for the last day of the requested month
    $endWeek = date('W', $endMonth);

    // get a range of weeks from the start week to the end week
    if ($startWeek > $endWeek) {
        // start week for january in previous year
        $weekRange = range(1, $endWeek);
        array_unshift($weekRange, 0);
    } else {
        $weekRange = range($startWeek, $endWeek);
    }
    return $weekRange;
}

function sanitize($value)
{
    return trim(strtolower($value));
}

//returns number if argument passed otherwise whole array
function monthNumber($monthName)
{
    $months = array(
        'JAN'   => 1,
        'FEB'   => 2,
        'MARCH' => 3,
        'APRIL' => 4,
        'MAY'   => 5,
        'JUNE'  => 6,
        'JULY'  => 7,
        'AUG'   => 8,
        'SEPT'  => 9,
        'OCT'   => 10,
        'NOV'   => 11,
        'DEC'   => 12,
    );
    return $months[$monthName] ? $months[$monthName] : $months;
}

function monthName($monthNumber)
{
    $months = array(
        1   => 'JAN',
        2   => 'FEB',
        3   => 'MARCH',
        4   => 'APRIL',
        5   => 'MAY',
        6   => 'JUNE',
        7   => 'JULY',
        8   => 'AUG',
        9   => 'SEPT',
        10  => 'OCT',
        11  => 'NOV',
        12  => 'DEC'
    );
    return $months[$monthNumber] ? $months[$monthNumber] : $months;
}

function get_splits($ledger_statement_id)
{
    $this->db->where('ledger_statement_id', $ledger_statement_id);
    $query = $this->db->get('ledger_statement_splits');
    return $query->result();
}

//convert currency string to number format
function getAmountFromString($currency)
{
    return ($currency) ? floatval(preg_replace('/[^\d\.\-]/', '', $currency)) : 0;
}

function reconcileLedgerWithBankMapping()
{
    //ledger => bank
    $CI  = &get_instance();
    $sql = "SELECT * FROM auto_reconcilation WHERE `is_active` = 1";
    $descAry = [];
    if ($sql) {
        $query  = $CI->db->query($sql);
        $result = $query->result();

        foreach ($result as $_result) {
            $descAry[$_result->ledger_desc] = sanitize($_result->bank_desc);
        }
    }
    return $descAry;
    return array(
        ('DEPOSIT')                               => sanitize('DEPOSIT'),
        ('Sales Tax (last month)')                => sanitize('GA TX PYMT%'),
        ('Jesani Accounting')                     => sanitize('%JESANI ACCOUNTIN%'),
        ('Electricity - GA Power')                => sanitize('GPC EFT GPC%'),
        ('Exterminating - EcoLab  ')              => sanitize('%EcoLab%'),
        ('Exterminating - EcoLab ')               => sanitize('ePay Ecolab%'),
        ('Exterminating - EcoLab')                => sanitize('%Manual Bill for Ecolab%'),
        ('Garbage - Waste Management')            => sanitize('%WASTE MANAGEMENT%'),
        ('Garbage-Waste Management')              => sanitize('%WASTE MANAGEMENT%'),
        ('Garbage -Waste Management')             => sanitize('%WASTE MANAGEMENT%'),
        ('Garbage - Waste Management')            => sanitize('%WASTE MANAGEMENT%'),
        ('Gas - Infinite Energy')                 => sanitize('%INFINITE ENERGY%'),
        ('Real Estate Tax (FC Enterprise)')       => sanitize('FC Enterprises Inc'),
        ('Tillster Ordering')                     => sanitize('Baskin Rob EMN%'),
        ('Water & Sewer -City of Atlanta')        => sanitize('%CITY OF ATL%'),
        ('Worker\'s Compensation - Peachstate')   => sanitize('PeachState Concessioniares Inc'),
        ('Worker\'s Compensation - Peachstate ')   => sanitize('Peachstate Concessioniarres Inc'),
        ('Rent (FC Enterprise)')                  => sanitize('FC Enterprises Inc'),
        ('Bank Loan Payment')                     => sanitize('%COMM LOANS BBT%'),
        ('MTV Food Enterprises (Maint. Guy)')     => sanitize('MTV Food Enterprises LLC'),
        ('Gas - Scana Energy')                    => sanitize('%SCANA ENERGY%'),
        ('Telephone - Granite')                   => sanitize('%Granite%'),
        ('Bank Loan (Pacific Premier)')           => sanitize('%PACIFIC PREMIER%'),
        ('Bank Loan Real Estate (BB&T)')          => sanitize('%COMM LOANS BBT%'),
        ('MTV Food Enterprises (Main. person)')   => sanitize('MTV Food Enterprises LLC'),
        ('Payroll Net ')                          => sanitize('%CKS CERTISTAFF%'),
        ('Payroll Net')                           => sanitize('%PAYROLL%'),
        ('Roy Adv')                               => sanitize('%EFT DEBIT DUNKIN BRANDS%'),
        ('Impound')                               => sanitize('%TAX COL CERTITAX  LLC%'),
        ('Donut ')                                => sanitize('%CustmerCol Bluemont Group%'),
        ('Donut')                                 => sanitize('%Golden Donut LLC%'),
        ('Dcp Efts')                              => sanitize('%BNKCRD DEP NATIONAL DCP%'),
        ('Dean Foods')                            => sanitize('%DEAN FOODS%'),
        ('Georgia Power (Pole Lights)')           => sanitize('%GEORGIA POWER%'),
        ('Garbage - Grogan Disposal')             => sanitize('%Grogan Disposal%'),
        ('Gas - City of Sugar Hill')              => sanitize('%CITY OF SUGAR%'),
        ('DTT Camera Service')                    => sanitize('SERVICES DTT%'),
        ('Water & Sewer - Gwinnett County')       => sanitize('%GWINNETT COUNTY%'),
        ('Rent (FC Enterprises)')                 => sanitize('FC Enterprises Inc'),
        ('Property Tax (FC Enterprises)')         => sanitize('FC Enterprises Inc'),
        ('Bank Loan Payment')                     => sanitize('FRA PAY Franchise%'),
        ('City of Lawrenceville (Utilities)')     => sanitize('ONLINE PMT CITYOFLAWRENCEVI%'),
        ('Rent (V&W Horiuchi LLC)')               => sanitize('V&W Horiuchi LLC'),
        ('Bank Loan (Pacific Premier)')           => sanitize('%PACIFIC PREMIER%'),
        ('Garbage - Waste Industry')              => sanitize('%WASTE INDUSTRIES%'),
        ('Granite Telecommunition')               => sanitize('Granite Telecommunications'),
        ('Water & Sewer -  Dekalb County')        => sanitize('%DEKALB CNTY%'),
        ('Sharp Twist LLC')                       => sanitize('%Sharp Twist LLC%'),
    );
}

function updateLedgerStatus($ledgerId)
{
    $ignoredDescBeingReconciled = ['Federal Tax 940','Federal Tax 941','Department of Revenue','Deptartment Of Labor','City Employee Taxes','Certipay Payroll Services'];
    $CI  = &get_instance();

    $sql = "SELECT count(*) as total, sum(if(is_reconcile = 1,1,0)) as reconciled, if(count(*) = sum(if(is_reconcile = 1,1,0)),1,0) as allset FROM ledger_statement WHERE `ledger_id` = " . $ledgerId. " AND description NOT IN ('".implode("','", $ignoredDescBeingReconciled)."') AND document_type != 'payroll_gross'";
    $checksql = "SELECT count(*) as total,
                SUM(IF(`is_reconcile` = 1 ,1,0)) as reconciled,
                if(count(*) = sum(if(is_reconcile = 1,1,0)),1,0) as allset
                FROM `checkbook_record`
                WHERE ledger_id = " . $ledgerId;

    if ($sql && $checksql) {
        $query  = $CI->db->query($sql);
        $result = $query->result();

        $check_info = $CI->db->query($checksql);
        $check_result = $check_info->result();

        $data   = [];
        if ($result[0]->allset && $check_result[0]->allset) {
            $data['status'] = 'reconcile';
        } elseif ($result[0]->reconciled || $check_result[0]->reconciled) {
            $data['status'] = 'partially_reconcile';
        } else {
            $data['status'] = 'unreconcile';
        }
        $CI->db->where('id', $ledgerId);
        $result = $CI->db->update('ledger', $data);

        return $query->result();
    } else {
        return false;
    }
}

function updateBankStatus($bankId)
{
    $CI  = &get_instance();
    $sql = "SELECT count(*) as total, sum(if(is_reconcile = 1,1,0)) as reconciled, if(count(*) = sum(if(is_reconcile = 1,1,0)),1,0) as allset FROM bank_statement_entries WHERE `bank_statement_id` = " . $bankId;
    if ($sql) {
        $query  = $CI->db->query($sql);
        $result = $query->result();
        $data   = [];
        if ($result[0]->allset) {
            $data['status'] = 'reconcile';
        } elseif ($result[0]->reconciled) {
            $data['status'] = 'partially_reconcile';
        } else {
            $data['status'] = 'unreconcile';
        }
        $CI->db->where('id', $bankId);
        $result = $CI->db->update('bank_statement', $data);

        return $query->result();
    } else {
        return false;
    }
}


function getStatusLabel($statusKey = "")
{
    $status = ['reconcile'=>'Reconciled',
                'partially_reconcile'=>'Partially Reconciled',
                'unreconcile'=> 'Unreconciled'];

    return $statusKey != "" ? $status[$statusKey] : $status;
}


function totalEntriesDocumentWise($key)
{
    $documenttype = [];
    $documenttype['impound'] = 5;
    $documenttype['donut_purchases'] = 6;
    $documenttype['dcp_efts'] = 18;
    $documenttype['dean_foods'] = 5;
    $documenttype['general_section_credit'] = 64;
    $documenttype['general_section_debit'] = 39;
    $documenttype['payroll_gross'] = 5;
    $documenttype['payroll_net'] = 5;
    $documenttype['roy_adv'] = 10;
    return isset($documenttype[$key]) ? $documenttype[$key] : -1;
}

function array_output($tableName,$where = 1, $select = "*")
{
    $ci=& get_instance();
    $ci->load->database();

    $sql = "SELECT {$select} FROM {$tableName} WHERE {$where}";
    $query = $ci->db->query($sql);
    $row = $query->result();
    return $row;
}

function update_helper($tableName,$where = 0, $setValues="")
{
    $ci=& get_instance();
    $ci->load->database();
    $sql = "UPDATE {$tableName} SET {$setValues} WHERE {$where}";
    $query = $ci->db->query($sql);
    return 1;
}

function sql_helper($sql)
{
    $ci=& get_instance();
    $ci->load->database();
    $query = $ci->db->query($sql);
    $row = $query->result();
    return $row;
}

function get_keys_result($id = NULL) {
    if ($id != NULL) {
        $CI = & get_instance();
        $CI->load->model('ledger_model');
        $result = $CI->ledger_model->Get($id);
        return $result;
    }
}

function updateLedgerBalance($ledgerId)
{
    $ledgerData = array_output("ledger","id = {$ledgerId}");

    $general_credit_total = "ROUND(SUM(if((is_manual = 0 AND document_type = 'general_section' AND description = 'DEPOSIT'),credit_amt,0)),2)";
    $general_debit_total = "ROUND(SUM(if((`transaction_type` = 'debit' and document_type='general_section'),debit_amt,0)),2)";
    $payrollnet_total = "ROUND(SUM(if(document_type='payroll_net',if(`transaction_type` = 'debit',debit_amt,(credit_amt * -1)),0)),2)";
    $payroll_gross_total = "ROUND(SUM(if(document_type='payroll_gross',if(`transaction_type` = 'debit',debit_amt,(credit_amt * -1)),0)),2)";
    $roy_total = "ROUND(SUM(if(document_type='roy_adv',if(`transaction_type` = 'debit',debit_amt,(credit_amt * -1)),0)),2)";
    $donut_total = "ROUND(SUM(if(document_type='donut_purchases',if(`transaction_type` = 'debit',debit_amt,(credit_amt * -1)),0)),2)";
    $dean_total = "ROUND(SUM(if(document_type='dean_foods',if(`transaction_type` = 'debit',debit_amt,(credit_amt * -1)),0)),2)";
    $dcp_total = "ROUND(SUM(if(document_type='dcp_efts',if(`transaction_type` = 'debit',debit_amt,(credit_amt * -1)),0)),2)";
    $final_credit_total = "ROUND(SUM(if((`transaction_type` = 'credit' and document_type='general_section'),credit_amt,0)),2)";
    $final_debit_total = "{$general_debit_total} + {$payrollnet_total} + {$roy_total}";
    $food_total = "{$donut_total} + {$dean_total} + {$dcp_total}";

    $ledger_balance = "({$final_credit_total}) - ({$final_debit_total}) - ({$food_total})";
    $ledger_balance_amount = array_output("ledger_statement","ledger_id = {$ledgerId}", "{$ledger_balance} as ledger_balance_amount")[0]->ledger_balance_amount;

    update_helper("ledger_credit_received_from", "label = 'LEDGER BALANCE' AND ledger_id = {$ledgerId}","amount = {$ledger_balance_amount}");

    $previousCarryForwardData = array_output("ledger","`store_key` = ".$ledgerData[0]->store_key." AND `month` = ".($ledgerData[0]->month - 1), "ending_balance");

    $previousCarryForward =  isset($previousCarryForwardData[0]) ? $previousCarryForwardData[0]->ending_balance : $ledgerData[0]->balance_cf;

    $checkBookTotal = array_output("checkbook_record","ledger_id = {$ledgerId}","sum(`amount1`) as checkBookTotal")[0]->checkBookTotal;

    $creditReceviedFromTotal = array_output("ledger_credit_received_from","ledger_id = {$ledgerId}","sum(`amount`) as creditReceviedFromTotal")[0]->creditReceviedFromTotal;

    $sql = "SELECT
    {$general_credit_total} as general_credit_total,
    {$general_debit_total} as general_debit_total,
    {$payrollnet_total} as payroll_net_total,
    {$payroll_gross_total} as payroll_gross_total,
    {$roy_total} as roy_total,
    {$donut_total} as donut_total,
    {$dean_total} as dean_total,
    {$dcp_total} as dcp_total,
    ($food_total) as food_total,
    ({$final_credit_total}) as final_credit_total,
    ({$final_debit_total}) as final_debit_total,
    ({$ledger_balance}) as ledger_balance,
    (({$previousCarryForward}) + ({$checkBookTotal}) + ({$creditReceviedFromTotal})) as ending_balance,
    $previousCarryForward as balance_cf
    FROM `ledger_statement` WHERE `ledger_id` = ".$ledgerId;

    $updateLedgerData = sql_helper($sql);

    $CI = & get_instance();
    $CI->load->model('ledger_model');
    $result = $CI->ledger_model->Edit($ledgerId, $updateLedgerData[0]);
    return $result;
}

function removeSpaces($str)
{
    $str = str_replace("&nbsp;", " ", $str);
    $str1 = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $str);
    return trim($str1);
}

function clear_temp($path) {
    $files = glob($path . '/*'); // get all file names
    foreach ($files as $file) { // iterate files
        if (is_file($file))
            unlink($file); // delete file
    }
}
function remove_format($text){
    $text = str_replace(",", "", $text);
    return $text;
}

function recursiveRemove($dir) {
    $CI = & get_instance();
    $structure = glob(rtrim($dir, "/") . '/*');
    if (is_array($structure)) {
        foreach ($structure as $file) {
            if (is_dir($file))
                recursiveRemove($file);
            elseif (is_file($file))
                unlink($file);
        }
    }
    if (file_exists($dir)) {
        rmdir($dir);
    }
}

function getStoreNumberFromCustomerForDonut($customerAddress){
    if(strtolower($customerAddress) == strtolower(trim("073-Boulevard"))):
        return $store_number = "350432";
    elseif(strtolower($customerAddress) == strtolower(trim("098-1841 Piedmont Ave NE"))):
        return $store_number = "350437";
    elseif(strtolower($customerAddress) == strtolower(trim("130-Sugarloaf Mill"))):
        return $store_number = "351323";
    elseif(strtolower($customerAddress) == strtolower(trim("Lawrenceville Tucker"))):
        return $store_number = "352613";
    elseif(strtolower($customerAddress) == strtolower(trim("Pike Street"))):
        return $store_number = "353927";
    elseif(strtolower($customerAddress) == strtolower(trim("071-Buford"))):
        return $store_number = "354030";
    elseif(strtolower($customerAddress) == strtolower(trim("099-2512 Blackmon Drive"))):
        return $store_number = "355439";
    elseif(strtolower($customerAddress) == strtolower(trim("pending"))):
        return $store_number = "357150";
    elseif(strtolower($customerAddress) == strtolower(trim("Spring Street"))):
        return $store_number = "358682";
    elseif(strtolower($customerAddress) == strtolower(trim("Peachtree Street"))):
        return $store_number = "359606";
    else:
        return false;
    endif;
}