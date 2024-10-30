<?php
$module = $this->uri->segment(1);
$sub_module = $this->uri->segment(2);
$get_category = $this->input->get('cat');
if (str_replace('_', ' ', $sub_module) == 'import qif') {
    $sub_module = 'Import QIF File';
} else if ($module == "product") {
    $module = "Sales";
    $sub_module = "Products & Services";
} else if ($module == "contacts") {
    if ($get_category == "is_vendor") {
        $module = "Contacts";
        $sub_module = "Vendors";
    } else if ($get_category == "is_customer") {
        $module = "Contacts";
        $sub_module = "Customers";
    } else if ($get_category == "is_employee") {
        $module = "Contacts";
        $sub_module = "Employees";
    }
}else if($sub_module == 'user_transactions'){
    $customer_guid = $this->uri->segment(3);
    $name = get_employee_name($customer_guid);
    $sub_module = 'User Transactions : '.$name;
}
if($sub_module == 'api_keys'){
    $sub_module = 'API Keys';
}
?>                     
<!-- BEGIN PAGE BAR -->
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li>
            <a href="<?php echo base_url('dashboard'); ?>"><?php echo ($this->session->userdata('company_name') != '') ? $this->session->userdata('company_name') : "Home"; ?></a>
            <i class="fa fa-angle-right"></i>
        </li>
        <li>
            <a href="<?php echo isset($module) && $module != '' ? base_url($module) : ''; ?>">    <span><?php echo isset($module) && $module != '' ? ucwords(str_replace('_', ' ', $module)) : 'Dashboard'; ?></span></a>
        </li>
<?php
if (isset($acc_res) && !empty($acc_res)) {
    ?>
            <li>
                <i class="fa fa-angle-right"></i>
                <span><?php echo isset($acc_res->hierarchy_path) && $acc_res->hierarchy_path != '' ? ucfirst($acc_res->hierarchy_path) : ''; ?></span>
            </li>
    <?php
} else if ($sub_module != '') {
    if ($module == "sales" && $sub_module == "customerdetail") {
        ?>
                <li>
                    <i class="fa fa-angle-right"></i>
                    <span>Customers</span>
                </li>
        <?php
    }
    ?>
            <li>
                <i class="fa fa-angle-right"></i>
                <span><?php echo isset($sub_module) && $sub_module != '' ? ucwords(str_replace('_', ' ', $sub_module)) : ''; ?></span>
            </li>
    <?php
}
?>
    </ul>
</div>
<!-- END PAGE BAR -->
