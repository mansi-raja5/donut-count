<?php
//$start_date = '01/01/' . date("Y");
$start_date = '';
//$end_date = date("m/d/Y");
$end_date = '';
?>
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption font-dark">
            <i class="icon-settings font-dark"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
        <div class="actions">
            <a title="Print PDF" class="btn btn-default btn-circle btn-sm" data-toggle="modal" data-target="#printPDFModal"><i class="fa fa-file-pdf-o"></i> PDF </a>
            <a id="export_contacts" title="Export to Excel" class="btn btn-default btn-circle btn-sm"><i class="fa fa-file-excel-o"></i> Excel </a>            
            <a  class="btn blue"  href="<?php echo base_url(); ?>vendors/entry"> Add New
                <i class="fa fa-plus"></i>
            </a>
        </div>

    </div>
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-12">
                <?php if ($err = $this->session->flashdata('success')): ?>
                    <div class="alert alert-success">
                        <button class="close" data-close="alert"></button>
                        <span><?php echo $err; ?></span>
                    </div>
                <?php
                endif;
                if ($err = $this->session->flashdata('failure')):
                    ?>
                    <div class="alert alert-danger">
                        <button class="close" data-close="alert"></button>
                        <span><?php echo $err; ?></span>
                    </div>
<?php endif; ?>
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-globe"></i>Search
                        </div>
                    </div>
                    <div class="portlet-body form">
                        <?php
                        $attributes = array('class' => 'horizontal-form', 'name' => 'frmSearch', 'id' => 'frmSearch', 'onsubmit' => 'return false');
                        echo form_open('', $attributes);
                        ?>
                        <div class="form-body">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <?php echo form_label('Company', 'company', array('class' => 'control-label')); ?>
<?php echo form_input(array('id' => 'company', 'name' => 'company', 'class' => 'form-control', 'placeholder' => 'Company')); ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
<?php echo form_label('Payment Date', 'date', array('class' => 'control-label')); ?>
                                    <div class="input-group input-large date-picker input-daterange">
                                        <input type="text" class="form-control require" placeholder="Payment From" name="payment_from" id="payment_from" value="<?php echo $start_date; ?>">
                                        <span class="input-group-addon"> to </span>
                                        <input type="text" class="form-control require" placeholder="Payment To" name="payment_to" id="payment_to" value="<?php echo $end_date; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
<?php echo form_label('Bill Date', 'date', array('class' => 'control-label')); ?>
                                    <div class="input-group input-large date-picker input-daterange">
                                        <input type="text" class="form-control require" placeholder="From" name="bill_from" id="bill_from" value="<?php echo $start_date; ?>">
                                        <span class="input-group-addon"> to </span>
                                        <input type="text" class="form-control require" placeholder="To" name="bill_to" id="bill_to" value="<?php echo $end_date; ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                  <?php echo form_label('Payment Mode', 'payment_mode', array('class' => 'control-label')); ?>
                                <select class="form-control" name="payment_mode"id="payment_mode">
                                    <option value="">--Select Payment Mode--</option>
                                    <option value="cash">Cash</option>
                                    <option value="auto">Auto</option>
                                    <option value="net_banking">Net Banking</option>
                                    <option value="card">Card</option>
                                </select>  
                            </div>

                        </div>
                        <div class="form-actions right">
                            <div class="col-md-12">
                                <?php echo form_button(array('id' => 'btnSearch', 'content' => 'Search', 'class' => 'btn blue')); ?>
<?php echo anchor('vendors', 'Cancel', array('class' => 'btn default')); ?>
                            </div>
                        </div>
<?php echo form_close(); ?>
                    </div>
                </div>
                <div class="portlet light bordered">
                    <div class="portlet-body">
                        <table class="table table-striped table-bordered table-hover" id="tblListing">
                            <thead>
                                <tr>
                                    <th width="20%"> Company </th>
                                    <th width="20%"> Name </th>
                                    <th width="20%"> Password </th>
                                    <th width="20%"> Scheduled Payment Date </th>
                                    <th width="10%"> Phone </th>
                                    <th width="10%"> Payment Mode </th>
                                    <th width="10%"> Comment </th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END CONTENT BODY -->
</div>
<!-- END CONTENT -->
<div class="modal fade" id="printPDFModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Select column for PDF Report</h4>
            </div>
            <div class="modal-body">
                <form id="FrmPdfColumn" class="horizontal-form validate">
                    <div class="alert alert-danger display-hide confirmError">
                        <button class="close" data-close="alert"></button>
                        <strong>Error!</strong> <span class="error-msg"></span>
                    </div>

                    <p>Please check a list of column maximum <b>7</b> which do you want to print in report.</p>

                    <div class="col-sm-12 col-xs-12 pl0 checkbox_list">
                        <div class="form-group">
                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="name_display_as" checked="checked">Name
                                    <span></span>
                                </label>
                            </div>
                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="phone" checked="checked">Phone
                                    <span></span>
                                </label>
                            </div>
                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="phys_addr1" checked="checked">Address1
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="phys_addr2" checked="checked">Address2
                                    <span></span>
                                </label>
                            </div>
                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="phys_city" checked="checked">City
                                    <span></span>
                                </label>
                            </div>
                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="phys_state" checked="checked">State
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="ssn_ein" checked="checked">EIN/TIN
                                    <span></span>
                                </label>
                            </div>

                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="company">Company
                                    <span></span>
                                </label>
                            </div>

                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="email">Email
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="website">Website
                                    <span></span>
                                </label>
                            </div>

                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="phys_zip">Zip
                                    <span></span>
                                </label>
                            </div>

                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="attention">Attention
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="receives_1099">Receives 1099
                                    <span></span>
                                </label>
                            </div>

                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="fatcha_filling_required">Fatcha Filling
                                    <span></span>
                                </label>
                            </div>

                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="terms">Terms
                                    <span></span>
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="billing_rate">Billing Rate
                                    <span></span>
                                </label>
                            </div>

                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="account_no">Account No
                                    <span></span>
                                </label>
                            </div>

                            <div class="col-sm-4 col-xs-12 pl0">
                                <label class="mt-checkbox mt-checkbox-outline">
                                    <input type="checkbox" name="pdf_columns[]" value="business_id_no">Business ID No
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button  class="btn btn-success pull-left" type="button" onclick="return export_pdf_report();">Export PDF</button>
                <button class="btn btn-danger pull-left" type="button" data-dismiss="modal">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<script>
    $(document).ready(function () {
        jQuery('#payment_from, #payment_to, #bill_from, #bill_to').datepicker({
            rtl: App.isRTL(),
            orientation: "left",
            autoclose: true
        });
        var cOptions = [];
        cOptions.columnDefs = [{
                data: "company",
                targets: 0
            },
            {
                data: "name",
                targets: 1,
            },
            {
                data: "password",
                targets: 2,
                orderable: false
            },
            {
                data: "schedule_payment_date",
                targets: 3,
            },
            {
                data: "phone",
                targets: 4,
                orderable: false
            },
            {
                data: "preferred_payment_method",
                targets: 5,
                orderable: false
            }, {
                data: "notes",
                targets: 6
            }];
        cOptions.order = [
            [0, 'asc']
        ];
        cOptions.mColumns = [1, 2, 3, 4, 5];
        Custom.initListingTable(cOptions);
        var Validator = $("#FrmPdfColumn").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            highlight: function (element) { // hightlight error inputs
                $(element).closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                        .closest('.form-group').removeClass('has-error'); // set error class to the control group
            },
            errorPlacement: function (error, element) { // render error placement for each input type
                // error.insertAfter(element); // for other inputs, just perform default behavior
                error.insertAfter($('.checkbox_list')); // for other inputs, just perform default behavior

            },
//            rules: {
//                'pdf_columns[]': {
//                    required: true,
//                    maxlength: 7
//                }
//            },
//            messages: {
//                'pdf_columns[]': {
//                    required: "You must select at least 1 column",
//                    maxlength: "Please select max. {0} columns"
//                }
//            }
        });
//        jQuery(document).on('click', '#print_contacts', function (event) {
//            
////            var file_name;
////            var category = '<?php echo $this->input->get('cat'); ?>';
////            
////            jQuery.ajax({
////                url: site_url + 'vendors/export_contacts_report',
////                type: "POST",
////                data: jQuery('#frmSearch').serialize() + '&type=EXCEL&category=' + category,
////                dataType: 'JSON',
////                async: false,
////                beforeSend: function (xhr) {
////                    jQuery("#loadingmessage").show();
////                },
////                success: function (responseText) {
////                    jQuery("#loadingmessage").hide();
////
////                    if (responseText.flag) {
////                        var link = document.createElement('a');
////                        link.href = site_url + 'files_upload/reports_pdf/' + responseText.filename;
////                        link.download = responseText.filename;
////                        file_name = responseText.filename;
////                        link.dispatchEvent(new MouseEvent('click'));
////                    }
////                }
////            }).complete(function () {
////                setTimeout(function () {
////                    jQuery.ajax({
////                        url: site_url + 'reports/remove_file',
////                        type: "POST",
////                        data: 'file_path=reports_pdf/' + file_name,
////                        dataType: 'JSON',
////                        beforeSend: function (xhr) {
////                        },
////                        success: function (responseText) {
////
////                        }
////                    });
////                }, 1000);
////            });
//        });

        jQuery(document).on('click', '#export_contacts', function (event) {
            var category = '<?php echo $this->input->get('cat'); ?>';
            var file_name;
            jQuery.ajax({
                url: site_url + 'vendors/export_contacts_report',
                type: "POST",
                data: jQuery('#frmSearch').serialize() + '&type=EXCEL&category=' + category,
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

    function export_pdf_report() {
        if ($("#FrmPdfColumn").valid()) {
            var category = '<?php echo $this->input->get('cat'); ?>';
            jQuery.ajax({
                url: site_url + 'vendors/export_contacts_report',
                type: "POST",
                data: jQuery('#frmSearch').serialize() + '&type=PDF&category=' + category + '&' + jQuery('#FrmPdfColumn').serialize(),
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
                    $('#printPDFModal').modal('hide');
                }, 1000);
            });
        }
    }
</script>