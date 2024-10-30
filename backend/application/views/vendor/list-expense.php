<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//overdue amount
$value_denom = isset($total_Res->value_denom) ? $total_Res->value_denom : 2;
$count_denom = substr_count($value_denom, '0');
$overdue_total_amount = $overdue_paid_amount = 0;
if (isset($overdue_Res) && !empty($overdue_Res)) {
    foreach ($overdue_Res as $oRow) {
        $overdue_total_amount += isset($oRow->total_amount) ? $oRow->total_amount : 0;
        $overdue_paid_amount += isset($oRow->paid_amount) ? $oRow->paid_amount : 0;
    }
}
$overdue_remain_amount = number_format(($overdue_total_amount - $overdue_paid_amount), 2);
$total_bill = isset($total_Res->total_bal) ? $total_Res->total_bal : 0;
$paid_bill = isset($paid_Res->paid_bal) ? ($paid_Res->paid_bal) : 0;
$paid_expenses_except_bill = isset($paid_Res_Except_bill->paid_bal) ? ($paid_Res_Except_bill->paid_bal) : 0;
$paid_expenses = $paid_bill + $paid_expenses_except_bill;
$pending_amount = $total_bill - $paid_bill;
$total_pending_amount = number_format($pending_amount, 2);
?>
<style>
    .btn-group.expense-dropdown>.dropdown-menu:before, .dropdown-toggle.expense-dropdown>.dropdown-menu:before, .dropdown.expense-dropdown>.dropdown-menu:before {
        right: 9px !important;
        left: auto !important;
    }
    .btn-group.expense-dropdown>.dropdown-menu:after, .dropdown-toggle.expense-dropdown>.dropdown-menu:after, .dropdown.expense-dropdown>.dropdown-menu:after {
        right: 10px !important;
        left: auto !important;
    }
    .check_note {
        margin-bottom: 0px !important;
        margin-top: -30px;
        font-size: 16px;
        font-weight: 600;
        text-align: center;
    }
    
    .search_div {
        width: 200px;
        float: right;
        margin-right: 10px;
        margin-top: 20px;
        z-index: 999;
        position: relative;
    }
    
    .search_div .input-icon, .search_div .btn {
        display: inline-block;
    }
    #export_expenses, #print_expenses {
        float: right;
        position: relative;
        margin-right: 15px;
        z-index: 999;
        margin-top: 20px;
    }
</style>
<!-- BEGIN EXAMPLE TABLE PORTLET-->
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body">
        <div class="row mb10">
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <p style="color: #68779b;margin-bottom: 5px;">Unpaid Last 365 Days</p>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12 pl0">
                <p style="color: #68779b;margin-bottom: 5px;">Open Last 365 Days</p>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                <p style="color: #68779b;margin-bottom: 5px;">Paid Last 365 Days</p>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 pr0">
                <div class="dashboard-stat red-intense">
                    <div class="visual">
                        <i class="fa fa-bar-chart-o"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                            <?php
                            echo isset($overdue_remain_amount) ? $this->company_currency . $overdue_remain_amount : 0.00;
                            ?>
                        </div>
                        <div class="desc">
                            OVERDUE
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12 pl0">
                <div class="dashboard-stat blue-madison">
                    <div class="visual">
                        <i class="fa fa-comments"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                            <?php
                            echo isset($total_pending_amount) ? $this->company_currency . $total_pending_amount : 0.00;
                            ?>
                        </div>
                        <div class="desc">
                            OPEN
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-6 col-xs-12">
                <div class="dashboard-stat green-haze">
                    <div class="visual">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                    <div class="details">
                        <div class="number">
                            <?php
                            echo isset($paid_expenses) ? $this->company_currency . number_format($paid_expenses, 2) : 0.00;
                            ?>
                        </div>
                        <div class="desc">
                            PAID
                        </div>
                    </div>
                </div>
            </div>
            <br/>
            <hr/>
        </div>
        <?php if ($err = $this->session->flashdata('failure')): ?>
            <div class="alert alert-danger" style="margin-top:20px;">
                <button class="close" data-close="alert"></button>
                <span><?php echo $err; ?></span>
            </div>
        <?php endif; ?>
        <div class="col-md-4 mb10 pull-right text-right">

        </div>
        <br/>
        <div id="error_msg" style="padding-top: 25px;"></div>
        <div class="row">
            <!--<div class="col-md-12 form-group " style="margin-bottom: -35px;z-index: 999">-->
            <div class="col-md-12 form-group"  style="margin-bottom: -35px !important;">
                <?php if(isset($no_unprinted_check) && $no_unprinted_check > 0) {?>
                    <p class="check_note"><a href="<?php echo base_url('bank_account/check_register'); ?>">Checks in Queue to Print : <?php echo $no_unprinted_check; ?></a></p>
                <?php } ?>
                <div class="btn-group pull-right mt20 expense-dropdown text-right" style="position:relative;z-index: 999;">
                    <!--<button type="button" class="btn btn-primary">Add </button>-->
                    <button type="button" class="btn  btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-expanded="false">Add&nbsp;&nbsp;&nbsp;<i class="fa fa-angle-down"></i></button>
                    <ul class="dropdown-menu" role="menu">
                        <?php if(isset($has_add_bill_perm) && $has_add_bill_perm) {?>
                        <li>
                            <a href="<?php echo base_url("expenses/bill") ?>">
                                Bill
                            </a>
                        </li>
                        <?php } ?>
                        
                        <?php if(isset($has_add_expense_perm) && $has_add_expense_perm) {?>
                        <li>
                            <a href="<?php echo base_url("expenses/expense") ?>">
                                Expense 
                            </a>
                        </li>
                        <?php } ?>
                        
                        <?php if(isset($has_add_check_perm) && $has_add_check_perm) {?>
                        <li>
                            <a href="<?php echo base_url("expenses/check") ?>">
                                Check 
                            </a>
                        </li>
                        <?php } ?>
                        
                        <?php if(isset($has_add_vendor_perm) && $has_add_vendor_perm) {?>
                        <li>
                            <a href="<?php echo base_url("expenses/vendor_credit") ?>">
                                Vendor Credit
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </div>
                <?php $attributes = array('name' => 'frmSearch', 'id' => 'frmSearch');
                    echo form_open("", $attributes);
                ?>
                <a id="print_expenses" title="Print PDF" class="btn btn-default btn-circle btn-sm"><i class="fa fa-file-pdf-o"></i> PDF </a>
                <a id="export_expenses" title="Export to Excel" class="btn btn-default btn-circle btn-sm"><i class="fa fa-file-excel-o"></i> Excel </a>
                <div class="feature_wrapper pull-right mt20 " style="position:relative; margin-right: 15px;z-index: 999;">
                    <button type="button" class="btn btn-sm btn-warning save_btn" name="btnFeature" id="btnFeature" onclick="set_filter(this);">Filter</button>
                    <ul class="dropdown-menu feature_dropdown" role="menu" style="top: -245px !important; overflow: hidden !important;">
                        <input type="hidden" name="TypePreview" id="TypePreview"/>
                        <input type="hidden" name="chk_ids" id="chk_ids"/>
                        <li>
                            <div class="col-md-12" style="margin-bottom: 10px;">
                                <label class="control-label">Type</label>
                                <?php
                                $options = array();
                                $options[''] = '-- Select Type --';
                                $options['All'] = 'All Transactions';
                                $options['Expense'] = 'Expense';
                                $options['Bill'] = 'Bill';
                                $options['Credit'] = 'Credit';
                                $options['Check'] = 'Check';
                                $options['Bill Payment'] = 'Bill Payment';
                                echo form_dropdown(array('id' => 'type_status', 'name' => 'expense_type', 'options' => $options, 'class' => 'form-control'));
                                ?>
                            </div>
                        </li>
                        <li>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Date</label>
                                    <?php
                                    $options = array();
                                    $options['1'] = 'Custom';
                                    $options['2'] = 'Today';
                                    $options['3'] = 'Yesterday';
                                    $options['4'] = 'This Week';
                                    $options['5'] = 'This Month';
                                    $options['6'] = 'Last Month';
                                    $options['7'] = 'This Quarter';
                                    $options['8'] = 'This Year';

                                    echo form_dropdown(array('id' => 'date_selection', 'name' => 'date_selection', 'options' => $options, 'class' => 'form-control', 'onchange' => 'change_dates(this);'));
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">From Date</label>
                                    <?php
                                    echo form_input(array('name' => 'from_date', 'id' => 'from_date', 'class' => 'form-control datepicker'));
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">To Date</label>
                                    <?php
                                    echo form_input(array('name' => 'to_date', 'id' => 'to_date', 'class' => 'form-control datepicker'));
                                    ?>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input class="btn btn-success" type="button" name="btnSubmit" id="btnInvoiceFilter" value="Apply" />
                                    <input class="btn btn-success" type="button" name="btnClear" id="btnClear" value="Reset" onclick="Reset_Filter();"/>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
                <div class="search_div">
                    <div class="input-icon">
                        <i class="fa fa-search"></i>
                        <input class="form-control search input-sm" placeholder="Search" type="text" name="term"/>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover"  id="tblListing">
                    <thead>
                        <tr>
        <!--                    <th width="5%">Sr No.</th>-->
                            <th style="width: 10%;">Date</th>
                            <th style="width: 10%;">Type</th>
                            <th style="width: 5%;">Check No</th>
                            <th>Payee</th>
                            <th style="width: 5%;">Total</th>
                            <th style="width: 5%;">Balance</th>
                            <th style="width: 10%;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="password_prompt_model">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Edit Already closed Transaction</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger display-hide confirmError">
                    <button class="close" data-close="alert"></button>
                    <strong>Error!</strong> <span class="error-msg"></span>
                </div>

                <p>This transaction occurs in a time period that has already been closed. Please enter the password to make edits, if this is really what you want to do</p>

                <div class="form-group">
                    <label>Password <span class="required" aria-required="true"> * </span></label>
                    <input class="form-control require" type="password" name="password" required="required"/>
                </div>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="type"/>
                <input type="hidden" name="guid"/>
                <input type="hidden" name="post_date"/>
                <button  class="btn btn-success pull-left" type="button" onclick="return check_password();">Submit</button>
                <button class="btn btn-danger pull-left" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div class="modal fade" id="reprint_prompt_model">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Already Printed Check</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <button class="close" data-close="alert"></button>
                    <strong>Warning!</strong> <span class="error-msg"></span>
                </div>

                <p>This check was already printed. Are you sure you want to print it again?</p>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="acc_id"/>
                <input type="hidden" name="expens_id"/>
                <input type="hidden" name="check_no"/>
                <button  class="btn btn-success pull-left" type="button" onclick="return reprint_check();">Print Check</button>
                <button class="btn btn-danger pull-left" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div class="modal fade" id="confirm_unpay_bill_model">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Confirmation to Unpay bill</h4>
            </div>
            <div class="modal-body">
                <div id="error-msg"></div>

                <p>Are you sure to delete all bill payments?</p>
            </div>
            <div class="modal-footer">
                <input type="hidden" name="bill_id"/>
                <button class="btn btn-default pull-right" type="button" data-dismiss="modal">Cancel</button>
                <button  class="btn btn-danger pull-right" type="button" onclick="return unpay_bill();" style="margin-right: 10px; ">Delete</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<script>
    $(document).ready(function () {
        var cOptions = [];
        cOptions.columnDefs = [
//            {
//                data: "srNo",
//                targets: 0,
//                orderable: false,
//                width: 50
//            },
            {
                data: "date",
                targets: 0
            }, {
                data: "type",
                targets: 1,
//                orderable: false
            }, {
                data: "check_no",
                targets: 2,
            }, {
                data: "payee",
                targets: 3,
            }, {
                data: "total",
                targets: 4,
                className: "text-right"
            }, {
                data: "balance",
                targets: 5,
                orderable: false,
                className: "text-right"
            },
            {
                data: "action",
                targets: 6,
                orderable: false,
                className: "dt-center",
                width: 150
            }];
        cOptions.order = [
            [0, 'desc']
        ];
        cOptions.mColumns = [1, 2, 3, 4];
        Custom.initListingTable(cOptions);
        jQuery(document).on('click', '#print_check', function (event) {
            var expens_id = jQuery(this).data('expens_id');
            var acc_id = jQuery(this).data('acc_id');
            var check_no = jQuery(this).data('check-no');
            event.preventDefault();
            if (expens_id == "") {
                var msg_html = '<br/><br/><div class="alert alert-danger alert-message"><button class="close" data-close="alert"></button><span>Something went wrong !! Please <a href="javascript:void(0)" onclick="return location.reload();">Reload Page</a></span></div>';
                jQuery("#error_msg").html(msg_html);
                jQuery("html").animate({scrollTop: 0}, "fast");
                return false;
            }
            if (acc_id == "") {
                var msg_html = '<br/><br/><div class="alert alert-danger alert-message"><button class="close" data-close="alert"></button><span>Something went wrong !! Please <a href="javascript:void(0)" onclick="return location.reload();">Reload Page</a></span></div>';
                jQuery("#error_msg").html(msg_html);
                jQuery("html").animate({scrollTop: 0}, "fast");
                return false;
            }
            jQuery.ajax({
                url: site_url + 'expenses/generate_check_expens',
                type: "POST",
                data: 'expens_id=' + expens_id + '&acc_id=' + acc_id + '&check_no=' + check_no,
                dataType: 'JSON',
                beforeSend: function (xhr) {
                    jQuery("#loadingmessage").show();
                },
                success: function (responseText) {
                    jQuery("#loadingmessage").hide();
                    if (responseText.flag) {
                        jQuery("#error_msg").html("");
                        window.open(site_url + 'files_upload/generate_check/' + responseText.filename, '_blank');
                        location.reload(true);
                    } else {

                        var msg_html = '<div class="alert alert-danger alert-message"><button class="close" data-close="alert"></button><span>' + responseText.msg + '</span></div>';
                        jQuery("#trans_Modal .modal-body").animate({scrollTop: 0}, "fast");
                        jQuery("#error_msg").html(msg_html);
                        jQuery("html").animate({scrollTop: 0}, "fast");
                    }
                }
            });
        });
        jQuery(document).on('click', '#reprint_check', function (event) {
            var expens_id = jQuery(this).data('expens_id');
            var acc_id = jQuery(this).data('acc_id');
            var check_no = jQuery(this).data('check-no');
            jQuery('#reprint_prompt_model input[name=expens_id]').val(expens_id);
            jQuery('#reprint_prompt_model input[name=acc_id]').val(acc_id);
            jQuery('#reprint_prompt_model input[name=check_no]').val(check_no);
            jQuery('#reprint_prompt_model').modal('show');
        });
        <?php if (isset($expense_type) && $expense_type != '') { ?>
            $('#type_status').val('<?php echo $expense_type; ?>');
            $('#btnInvoiceFilter').trigger('click');
        <?php } ?>
        jQuery(document).on('click', '#confirmModal .confirmYes', function (event) {
            var bill_id = jQuery(this).data('id');
            jQuery.ajax({
                url: site_url + 'expenses/delete_bill_payment',
                type: "POST",
                data: 'bill_id=' + bill_id,
                dataType: 'JSON',
                beforeSend: function (xhr) {
                    jQuery("#loadingmessage").show();
                },
                success: function (response) {
                    jQuery("#loadingmessage").hide();
                    if (response.status) {
                        location.reload();
                    } else {
                        $("#confirmModal .confirmError .error-msg").text("Something is missing. Please try again after sometime.");
                    }
                }
            });
        });
        $('.search').focus();
        $('.search').on('keypress',function(e) {
            if(e.which == 13 && ($(this).val().length > 2 || $(this).val().length == 0)) {
                $('#btnInvoiceFilter').trigger('click');
            }
        });
        jQuery(document).on('click', '#print_expenses', function (event) {
            var file_name;
            jQuery.ajax({
                url: site_url + 'expenses/export_expenses_report',
                type: "POST",
                data: jQuery('#frmSearch').serialize() + '&type=PDF',
                dataType: 'JSON',
                async: true,
                beforeSend: function (xhr) {
                    jQuery("#loadingmessage").show();
                },
                success: function (responseText) {
                    jQuery("#loadingmessage").hide();

                    if (responseText.flag) {
                        var link = document.createElement('a');
                        link.href = site_url + 'files_upload/reports_pdf/' + responseText.filename;
                        link.download = responseText.filename;
                        file_name = responseText.filename;
                        link.dispatchEvent(new MouseEvent('click'));
                    }
                }
            }).complete(function () {
                setTimeout(function () {
                    jQuery.ajax({
                        url: site_url + 'reports/remove_file',
                        type: "POST",
                        data: 'file_path=reports_pdf/' + file_name,
                        dataType: 'JSON',
                        beforeSend: function (xhr) {
                        },
                        success: function (responseText) {

                        }
                    });
                }, 1000);
            });
        });
        
        jQuery(document).on('click', '#export_expenses', function (event) {
            var file_name;
            jQuery.ajax({
                url: site_url + 'expenses/export_expenses_report',
                type: "POST",
                data: jQuery('#frmSearch').serialize() + '&type=EXCEL',
                dataType: 'JSON',
                async: true,
                beforeSend: function (xhr) {
                    jQuery("#loadingmessage").show();
                },
                success: function (responseText) {
                    jQuery("#loadingmessage").hide();

                    if (responseText.flag) {
                        var link = document.createElement('a');
                        link.href = site_url + 'files_upload/reports_excel/' + responseText.filename;
                        link.download = responseText.filename;
                        file_name = responseText.filename;
                        link.dispatchEvent(new MouseEvent('click'));
                    }
                }
            }).complete(function () {
                setTimeout(function () {
                    jQuery.ajax({
                        url: site_url + 'reports/remove_file',
                        type: "POST",
                        data: 'file_path=reports_excel/' + file_name,
                        dataType: 'JSON',
                        beforeSend: function (xhr) {
                        },
                        success: function (responseText) {

                        }
                    });
                }, 1000);
            });
        });
    });
    function open_password_prompt(ele) {
        $('#password_prompt_model input[name=type]').val($(ele).attr('data-type'));
        $('#password_prompt_model input[name=guid]').val($(ele).attr('data-guid'));
        $('#password_prompt_model input[name=post_date]').val($(ele).attr('data-post-date'));
        $('#password_prompt_model').modal('show');
    }
    function check_password() {
        var password = $('#password_prompt_model').find('input[name=password]').val();
        var type = $('#password_prompt_model input[name=type]').val();
        var guid = $('#password_prompt_model input[name=guid]').val();

        if (password != '') {
            $('#password_prompt_model').find('.alert').hide();

            $.ajax({
                url: site_url + 'settings/check_password',
                type: "POST",
                data: {'password': password},
                beforeSend: function (xhr) {
                    $("#loadingmessage").show();
                },
                success: function (responseText) {
                    $("#loadingmessage").hide();
                    $('#password_prompt_model').modal('hide');
                    var response = JSON.parse(responseText);

                    if (response.status) {
                        if (type == 'expense') {
                            window.location.href = site_url + "expenses/expense/" + guid + "?closed_splits=1";
                        }

                        if (type == 'bill') {
                            window.location.href = site_url + "expenses/bill/" + guid + "?closed_splits=1";
                        }

                        if (type == 'vendor_credit') {
                            window.location.href = site_url + "expenses/vendor_credit/" + guid + "?closed_splits=1";
                        }

                        if (type == 'check') {
                            window.location.href = site_url + "expenses/check/" + guid + "?closed_splits=1";
                        }
                    } else {
                        $('#password_prompt_model').find('.alert').show();
                        $('#password_prompt_model').find('.error-msg').text('Please enter valid password.');
                    }
                }
            });
        } else {
            $('#password_prompt_model').find('.alert').show();
            $('#password_prompt_model').find('.error-msg').text('Please enter password.');
        }
    }
    function reprint_check() {
        var expens_id = jQuery('#reprint_prompt_model input[name=expens_id]').val();
        var acc_id = jQuery('#reprint_prompt_model input[name=acc_id]').val();
        var check_no = jQuery('#reprint_prompt_model input[name=check_no]').val();

        if (expens_id == "") {
            var msg_html = '<br/><br/><div class="alert alert-danger alert-message"><button class="close" data-close="alert"></button><span>Something went wrong !! Please <a href="javascript:void(0)" onclick="return location.reload();">Reload Page</a></span></div>';
            jQuery("#error_msg").html(msg_html);
            jQuery("html").animate({scrollTop: 0}, "fast");
            return false;
        }
        if (acc_id == "") {
            var msg_html = '<br/><br/><div class="alert alert-danger alert-message"><button class="close" data-close="alert"></button><span>Something went wrong !! Please <a href="javascript:void(0)" onclick="return location.reload();">Reload Page</a></span></div>';
            jQuery("#error_msg").html(msg_html);
            jQuery("html").animate({scrollTop: 0}, "fast");
            return false;
        }
        jQuery.ajax({
            url: site_url + 'expenses/generate_check_expens',
            type: "POST",
            data: 'expens_id=' + expens_id + '&acc_id=' + acc_id + '&check_no=' + check_no,
            dataType: 'JSON',
            beforeSend: function (xhr) {
                jQuery("#loadingmessage").show();
            },
            success: function (responseText) {
                jQuery("#loadingmessage").hide();
                if (responseText.flag) {
                    jQuery("#error_msg").html("");
                    window.open(site_url + 'files_upload/generate_check/' + responseText.filename, '_blank');
                    location.reload(true);
                } else {
                    var msg_html = '<div class="alert alert-danger alert-message"><button class="close" data-close="alert"></button><span>' + responseText.msg + '</span></div>';
                    jQuery("#trans_Modal .modal-body").animate({scrollTop: 0}, "fast");
                    jQuery("#error_msg").html(msg_html);
                    jQuery("html").animate({scrollTop: 0}, "fast");
                }
            }
        });
    }
    function change_dates(e) {
        var value = $(e).val();
        if (value != '') {
            if (value == 1) {
                $("#from_date, #to_date").val("");
            } else if (value == 2) {
                var date = new Date();
                $('#from_date, #to_date').datepicker("setDate", new Date(date));
            } else if (value == 3) {
                var date1 = new Date(Date.now() - 864e5);
                $('#from_date, #to_date').datepicker("setDate", date1);
            } else if (value == 4) {
                var curr = new Date;
                var firstday = new Date(curr.setDate(curr.getDate() - curr.getDay()));
                var lastday = new Date(curr.setDate(curr.getDate() - curr.getDay() + 6));
                $('#from_date').datepicker("setDate", firstday);
                $('#to_date').datepicker("setDate", lastday);
            } else if (value == 5) {
                var date = new Date();
                var firstDay = new Date(date.getFullYear(), date.getMonth(), 1);
                var lastDay = new Date(date.getFullYear(), date.getMonth() + 1, 0);
                $('#from_date').datepicker("setDate", firstDay);
                $('#to_date').datepicker("setDate", lastDay);
            } else if (value == 6) {
                var date = new Date();
                var firstDay = new Date(date.getFullYear(), date.getMonth() - 1, 1);
                var lastDay = new Date(date.getFullYear(), date.getMonth(), 0);
                $('#from_date').datepicker("setDate", firstDay);
                $('#to_date').datepicker("setDate", lastDay);
            } else if (value == 7) {
                var date = new Date();
                var quarter = parseInt(date.getMonth() / 3) + 1;
                var dtFirstDay = new Date(date.getFullYear(), 3 * quarter - 3, 1);
                var dtLastDay = new Date(date.getFullYear(), 3 * quarter, 0);
                $('#from_date').datepicker("setDate", dtFirstDay);
                $('#to_date').datepicker("setDate", dtLastDay);
            } else if (value == 8) {
                var date = new Date();
                var dtFirstDay = new Date(date.getFullYear(), 0, 1);
                var dtLastDay = new Date(date.getFullYear(), 12, 0);
                $('#from_date').datepicker("setDate", dtFirstDay);
                $('#to_date').datepicker("setDate", dtLastDay);
            }
        }
    }
    function set_filter(element) {
        if ($(element).parent(".feature_wrapper").find("ul").css("display") == 'none') {
            $(element).css("background-color", '#dab10d');
            $(element).css("border-color", '#a08209');
        } else {
            $(element).css("background-color", '#F1C40F');
            $(element).css("border-color", '#dab10d');
        }
        $(element).parent(".feature_wrapper").find("ul").toggle("fast");
        $(element).parent(".feature_wrapper").find("ul").css("top", "-215px");
    }
    function Reset_Filter() {
        $('#frmSearch')[0].reset();
    }
    function confirm_unpay_bill(ele) {
        $('#confirm_unpay_bill_model').modal('show');
        $('#confirm_unpay_bill_model input[name=bill_id]').val($(ele).attr('data-id'));
    }
    function unpay_bill() {
        var bill_id = $('#confirm_unpay_bill_model input[name=bill_id]').val();

        jQuery.ajax({
            url: site_url + 'expenses/unpay_bill',
            type: "POST",
            data: 'bill_id=' + bill_id,
            dataType: 'JSON',
            beforeSend: function (xhr) {
                jQuery("#loadingmessage").show();
            },
            success: function (response) {
                jQuery("#loadingmessage").hide();
                if (response.status) {
                    location.reload();
                } else {
                    var msg_html = '<div class="alert alert-danger alert-message"><button class="close" data-close="alert"></button><span>Something is missing. Please try again</span></div>';
                    jQuery("#confirm_unpay_bill_model #error-msg").html(msg_html);
                }
            }
        });
    }
    function confirm_delete_bill_payment(ele) {
        $("#confirmModal").find(".modal-body").children("p").show();
        $("#confirmModal").find(".confirmYes").show();
        $("#confirmModal").find(".modal-title").html("Bill Payment Deletion");
        $("#confirmModal").find(".modal-body").children("p").html("Do you really want to delete this bill payment?");
        $("#confirmModal").find(".confirmYes").attr('data-id', $(ele).attr('data-id'));
    }
    function search_expense() {
        
    }
</script>