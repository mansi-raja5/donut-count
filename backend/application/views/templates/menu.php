<?php
$budget_menu_item = array(
    'controller' => array('home'),
    'title' => 'Home',
    'icon' => 'icon-home',
    'url' => site_url('home')
);
$store_menu = array(
    'controller' => array('store'),
    'title' => 'Stores',
    'icon' => 'icon-basket',
    'url' => site_url('store')
);
$statement_menu = array(
    'controller' => array('statement'),
    'title' => 'Ledger',
    'icon' => 'icon-calculator',
    'url' => site_url('statement'),
    'class' => 'start'
);
$bank_statement_menu = array(
    'controller' => array('bank'),
    'title' => 'Bank',
    'icon' => 'icon-bar-chart',
    'url' => site_url('bank'),
    'class' => 'start'
);

$reconcile_menu = array(
    'controller' => array('reconcile'),
    'title' => 'Reconcile',
    'icon' => 'icon-bar-chart',
    'url' => site_url('reconcile'),
    'class' => 'start'
);

$common = array(
    'controller' => array('common'),
    'title' => 'Common',
    'icon' => 'icon-bar-chart',
    'url' => site_url('common'),
    'class' => 'start'
);
$common['submenu'][] = array(
    'controller' => array('common'),
    'method' => array('history'),
    'title' => 'File History',
    'url' => site_url('common/history')
);

$labor = array(
    'controller' => array('labor'),
    'title' => 'Labor Summary',
    'icon' => 'icon-bar-chart',
    'url' => site_url('labor'),
    'class' => 'start'
);

$bill = array(
    'controller' => array('bill'),
    'title' => 'Bill',
    'icon' => 'icon-bar-chart',
    'url' => site_url('bill'),
    'class' => 'start'
);

$billCategory = array(
    'controller' => array('category'),
    'title' => 'Bill Category',
    'icon' => 'icon-bar-chart',
    'url' => site_url('category'),
    'class' => 'start'
);

$vendors = array(
    'controller' => array('vendors'),
    'title' => 'Vendors',
    'icon' => 'icon-bar-chart',
    'url' => site_url('vendors'),
    'class' => 'start'
);

$report = array(
    'controller' => array('reports'),
    'title' => 'Snapshot',
    'icon' => 'icon-bar-chart',
    'url' => site_url('reports/ledger_report'),
    'class' => 'start'
);
$report['submenu'][] = array(
    'controller' => array('reports'),
    'method' => array('ledger_report'),
    'title' => 'Ledger Report',
    'url' => site_url('reports/ledger_report')
);
$report['submenu'][] = array(
    'controller' => array('reports'),
    'method' => array('sales_comparision_report'),
    'title' => 'Sales Comparision Report',
    'url' => site_url('reports/sales_comparision_report')
);

$report_menu = array(
    'controller' => array('reports'),
    'title' => 'Reports',
    'icon' => 'icon-bar-chart',
    'url' => site_url('reports/ledger_report'),
    'class' => 'start'
);

$car_menu = array(
    'controller' => array('cars_entry'),
    'title' => 'Cars Entry',
    'icon' => 'fa fa-car',
    'url' => site_url('cars_entry'),
    'class' => 'start'
);
$customer_review = array(
    'controller' => array('review/view'),
    'title' => 'Customer Review',
    'icon' => 'fa fa-star',
    'url' => site_url('review/view'),
    'class' => 'start'
);
$admin_settings = array(
    'controller' => array("settings"),
    'title' => 'Settings',
    'icon' => 'fa fa-gears',
    'url' => site_url('settings/store_setting'),
    'class' => 'start'
);
$admin_settings['submenu'][] = array(
    'controller' => array('settings'),
    'method' => array('store_setting'),
    'title' => 'Store Settings',
    'url' => site_url('settings/store_setting')
);
$admin_settings['submenu'][] = array(
    'controller' => array('settings'),
    'method' => array('general_setting'),
    'title' => 'General Settings',
    'url' => site_url('settings/general_setting')
);
$admin_settings['submenu'][] = array(
    'controller' => array('settings'),
    'method' => array('exclude'),
    'title' => 'Exclude Calculation Settings',
    'url' => site_url('settings/exclude')
);
$admin_settings['submenu'][] = array(
    'controller' => array('settings'),
    'method' => array('labor'),
    'title' => 'Labor Bonus Settings',
    'url' => site_url('settings/labor')
);
$admin_settings['submenu'][] = array(
    'controller' => array('settings'),
    'method' => array('conditional'),
    'title' => 'Conditional Formatting Settings',
    'url' => site_url('settings/conditional')
);
$admin_settings['submenu'][] = array(
    'controller' => array('statement'),
    'title' => 'Upload Setting',
    'method' => array('list_upload_setting'),
    'url' => site_url('statement/list_upload_setting'),
);
$admin_settings['submenu'][] = array(
    'controller' => array('special_day'),
    'title' => 'Special Day',
    'method' => array('special_day'),
    'url' => site_url('special_day'),
);
$admin_settings['submenu'][] = array(
    'controller' => array('season'),
    'title' => 'Season',
    'method' => array('season'),
    'url' => site_url('season'),
);

$admin_settings['submenu'][] = array(
    'controller' => array('settings'),
    'method' => array('autoreconsetting'),
    'title' => 'Auto Reconcile',
    'url' => site_url('settings/autoreconsetting')
);

$admin_settings['submenu'][] = array(
    'controller' => array('settings'),
    'method' => array('yearsetting'),
    'title' => 'Year Settings',
    'url' => site_url('settings/yearsetting')
);

$admin_settings['submenu'][] = array(
    'controller' => array('settings'),
    'method' => array('royaltysetting'),
    'title' => 'Royalty Settings',
    'url' => site_url('settings/royaltysetting')
);

$admin_settings['submenu'][] = array(
    'controller' => array('settings'),
    'method' => array('dbcleansetting'),
    'title' => 'Database Cleanup',
    'url' => site_url('settings/dbcleansetting')
);

$daily_report = array(
    'controller' => array('snapshot'),
    'title' => 'Daily Report',
    'icon' => 'icon-bar-chart',
    'url' => site_url('daily'),
    'class' => 'start'
);

$weekly_report = array(
    'controller' => array('weekly'),
    'title' => 'Weekly',
    'icon' => 'fa fa-bar-chart-o',
    'url' => site_url('weekly'),
    'class' => 'start'
);
$nav_menu[] = $budget_menu_item;
$nav_menu[] = $store_menu;
$nav_menu[] = $statement_menu;
$nav_menu[] = $bank_statement_menu;
$nav_menu[] = $reconcile_menu;
$nav_menu[] = $bill;
$nav_menu[] = $billCategory;
$nav_menu[] = $vendors;
$nav_menu[] = $common;
$nav_menu[] = $labor;
$nav_menu[] = $report_menu;
$nav_menu[] = $car_menu;
$nav_menu[] = $customer_review;
$nav_menu[] = $admin_settings;
$nav_menu[] = $daily_report;
$nav_menu[] = $weekly_report;
?>
<!-- BEGIN SIDEBAR -->
<div class="page-sidebar-wrapper">
    <!-- BEGIN SIDEBAR -->
    <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
    <!-- DOC: Change data-auto-speed="200" to adjust the sub menu slide up/down speed -->
    <div class="page-sidebar navbar-collapse collapse">
        <!-- BEGIN SIDEBAR MENU -->
        <!-- DOC: Apply "page-sidebar-menu-light" class right after "page-sidebar-menu" to enable light sidebar menu style(without borders) -->
        <!-- DOC: Apply "page-sidebar-menu-hover-submenu" class right after "page-sidebar-menu" to enable hoverable(hover vs accordion) sub menu mode -->
        <!-- DOC: Apply "page-sidebar-menu-closed" class right after "page-sidebar-menu" to collapse("page-sidebar-closed" class must be applied to the body element) the sidebar sub menu mode -->
        <!-- DOC: Set data-auto-scroll="false" to disable the sidebar from auto scrolling/focusing -->
        <!-- DOC: Set data-keep-expand="true" to keep the submenues expanded -->
        <!-- DOC: Set data-auto-speed="200" to adjust the sub menu slide up/down speed -->
        <ul class="page-sidebar-menu  page-header-fixed " data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top: 20px">
            <!-- BEGIN SIDEBAR TOGGLER BUTTON -->
            <li class="sidebar-toggler-wrapper hide">
                <div class="sidebar-toggler">
                    <span></span>
                </div>
            </li>
            <!-- END SIDEBAR TOGGLER BUTTON -->
            <?php foreach ($nav_menu as $menu_key => $menu_value) { ?>
                <li class="s0 nav-item <?php
                echo isset($menu_value['class']) ? $menu_value['class'] : '';
                $active_module_cls = '';
                if ($this->router->fetch_class() == 'product') {
                    $active_module_cls = "sales";
                }
                if ($this->router->fetch_class() == 'rules') {
                    $active_module_cls = "accounts";
                }
                if (in_array($this->router->fetch_class(), $menu_value['controller']) || in_array($active_module_cls, $menu_value['controller'])) {
                    echo ' active open';
                }
                ?>">
                    <a href="<?php echo $menu_value['url']; ?>" class="nav-link nav-toggle">
                        <?php if (isset($menu_value['icon'])) { ?><i class="<?php echo $menu_value['icon']; ?>"></i><?php } ?>
                        <span class="title"><?php echo $menu_value['title']; ?></span>
                        <?php if (in_array($this->router->fetch_class(), $menu_value['controller'])) { ?><span class="selected"></span><?php } ?>
                    <?php if (isset($menu_value['submenu'])) { ?><span class="arrow <?php if (in_array($this->router->fetch_class(), $menu_value['controller'])) { ?>open <?php } ?>"></span><?php } ?>
                    </a>
                        <?php if (isset($menu_value['submenu'])) { ?>
                        <ul class="sub-menu">
                            <?php
                            foreach ($menu_value['submenu'] as $submenu_key => $submenu_value) {

                                if ($submenu_value['controller'] != "customer" && $submenu_value['method'][0] != 'view_account' && $submenu_value['method'][0] != 'entry') {
                                    $active_cat = $this->input->get('cat');
                                    $vendor_active = $customer_active = $rules_active = '';
                                    if ($active_cat == 'is_vendor') {
                                        $vendor_active = 'Vendors';
                                    }
                                    if ($active_cat == 'is_customer') {
                                        $customer_active = 'Customer';
                                    }
                                    if ($active_cat == 'is_employee') {
                                        $customer_active = 'Employees';
                                    }
                                    if ($this->router->fetch_class() == 'rules' && in_array($this->router->fetch_class(), $submenu_value['method'])) {
                                        $rules_active = 'rules';
                                    }
                                    ?>
                                    <li class="nav-item <?php
                                    echo isset($submenu_value['class']) ? $submenu_value['class'] : '';
                                    if (in_array($this->router->fetch_class(), $submenu_value['controller']) && $vendor_active == '' && $customer_active == '' && (isset($submenu_value['submenu']) || in_array($this->router->fetch_method(), $submenu_value['method'])) || ($vendor_active == $submenu_value['title']) || ($customer_active == $submenu_value['title']) || ($rules_active == $this->router->fetch_class())) {
                                        echo ' active open';
                                    }
                                    ?>">
                                        <a href="<?php echo $submenu_value['url']; ?>" class="nav-link" id="<?php echo isset($submenu_value['id']) ? $submenu_value['id'] : "" ?>">
                                            <?php if (isset($submenu_value['icon'])) { ?><i class="<?php echo $submenu_value['icon']; ?>"></i><?php } ?>
                                            <span class="title"><?php echo $submenu_value['title']; ?></span>
                                        <?php if (in_array($this->router->fetch_class(), $submenu_value['controller']) && (isset($submenu_value['submenu']) || in_array($this->router->fetch_method(), $submenu_value['method']))) { ?><span class="selected"></span><?php } ?>
                                        <?php if (isset($submenu_value['submenu'])) { ?><span class="arrow <?php if (in_array($this->router->fetch_class(), $submenu_value['controller'])) { ?>open<?php } ?>"></span><?php } ?>
                                        </a>
                                            <?php if (isset($submenu_value['submenu'])) { ?>
                                            <ul class="sub-menu">
                                                <?php foreach ($submenu_value['submenu'] as $ssubmenu_key => $ssubmenu_value) { ?>
                                                    <li class="<?php
                                                        echo isset($ssubmenu_value['class']) ? $ssubmenu_value['class'] : '';
                                                        if (in_array($this->router->fetch_class(), $ssubmenu_value['controller']) && in_array($this->router->fetch_method(), $ssubmenu_value['method'])) {
                                                            echo ' active';
                                                        }
                                                        ?>">
                                                        <a href="<?php echo $ssubmenu_value['url']; ?>">
                                                        <?php if (isset($ssubmenu_value['icon'])) { ?><i class="<?php echo $ssubmenu_value['icon']; ?>"></i><?php } ?>
                                                            <span class="title"><?php echo $ssubmenu_value['title']; ?></span>
                                                        </a>
                                                            <?php if (isset($ssubmenu_value['submenu'])) { ?>
                                                            <ul class="sub-menu">
                                                                <?php foreach ($ssubmenu_value['sub_menu']['sub_menu1'] as $ssubmenu_key => $ssubmenu_value) { ?>
                                                                    <li class="<?php
                                    echo isset($ssubmenu_value['class']) ? $ssubmenu_value['class'] : '';
                                    if (in_array($this->router->fetch_class(), $ssubmenu_value['controller']) && in_array($this->router->fetch_method(), $ssubmenu_value['method'])) {
                                        echo ' active';
                                    }
                                    ?>">
                                                                        <a href="<?php echo $ssubmenu_value['url']; ?>">
                                                                    <?php if (isset($ssubmenu_value['icon'])) { ?><i class="<?php echo $ssubmenu_value['icon']; ?>"></i><?php } ?>
                                                                            <span class="title"><?php echo $ssubmenu_value['title']; ?></span>
                                                                        </a>


                                                                    </li>
                                                    <?php } ?>
                                                            </ul>
                                            <?php } ?>
                                                    </li>
                                        <?php } ?>
                                            </ul>
                                    <?php } ?>
                                    </li>
                                    <?php
                                } else {
//                                    echo $submenu_value['html'];
                                }
                                ?>
        <?php } ?>
                        </ul>
    <?php } ?>
                </li>
<?php } ?>
        </ul>
        <!-- END SIDEBAR MENU -->
    </div>
</div>
<!-- END SIDEBAR -->