<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- BEGIN EXAMPLE TABLE PORTLET-->
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body">
        <?php if(isset($payment_data) && !empty($payment_data)) { ?>
        <div class="row">
<!--            <div class="col-md-12">-->
<!--                <button class="btn btn-sm btn-info" onclick="javascript:history.go(-1)" type="button">Back</button>-->
<!--            </div>-->
            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('<b>Bill No.</b>', 'type', array('class' => 'control-label col-md-4')); ?>
                    <div class="col-md-8">
                        <?php echo isset($payment_data->expense_no) && $payment_data->expense_no != '' ? $payment_data->expense_no : "N/A"; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('<b>Payment Status</b>', 'type', array('class' => 'control-label col-md-4')); ?>
                    <div class="col-md-8">
                        <?php
                            echo isset($payment_data->bill_status) && isset($payment_data->bill_status) != '' ? $payment_data->bill_status : "N/A";
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('<b>Total Amount</b>', 'type', array('class' => 'control-label col-md-4')); ?>
                    <div class="col-md-8">
                        <?php echo isset($payment_data->amount) ? $this->company_currency.number_format($payment_data->amount, 2) : "N/A"; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('<b>Payment Date</b>', 'type', array('class' => 'control-label col-md-4')); ?>
                    <div class="col-md-8">
                        <?php echo isset($payment_data->bill_payment_date) && $payment_data->bill_payment_date != '' ? date('m/d/Y', strtotime($payment_data->bill_payment_date)) : "N/A"; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('<b>Bill Date</b>', 'type', array('class' => 'control-label col-md-4')); ?>
                    <div class="col-md-8">
                        <?php echo isset($payment_data->bill_date) && $payment_data->bill_date != '' ? date('m/d/Y',strtotime($payment_data->bill_date)) : "N/A"; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('<b>Payment Amount</b>', 'type', array('class' => 'control-label col-md-4')); ?>
                    <div class="col-md-8">
                        <?php echo isset($payment_data->value_num) ? $this->company_currency.number_format(abs($payment_data->value_num / $payment_data->value_denom), 2) : "N/A"; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('<b>Due Date</b>', 'type', array('class' => 'control-label col-md-4')); ?>
                    <div class="col-md-8">
                        <?php echo isset($payment_data->due_date) && $payment_data->due_date != '' ? date('m/d/Y', strtotime($payment_data->due_date)) : "N/A"; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('<b>Reference No</b>', 'type', array('class' => 'control-label col-md-4')); ?>
                    <div class="col-md-8">
                        <?php echo isset($payment_data->bill_reference_no) && $payment_data->bill_reference_no != '' ? ($payment_data->bill_reference_no) : "N/A"; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('<b>Bill Credit Account</b>', 'type', array('class' => 'control-label col-md-4')); ?>
                    <div class="col-md-8">
                        <?php echo isset($payment_data->credit_acc) && $payment_data->credit_acc != '' ? ($payment_data->credit_acc) : "N/A"; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <?php echo form_label('<b>Bill Mailing Address</b>', 'type', array('class' => 'control-label col-md-4')); ?>
                    <div class="col-md-8">
                        <?php echo isset($payment_data->bill_mailing_address) && $payment_data->bill_mailing_address != '' ? nl2br($payment_data->bill_mailing_address) : "N/A"; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } else {
        ?>
            <div id="error_div" class="alert alert-danger">
                <button class="close" data-close="alert"></button>
                <strong>Error! </strong>
                <p style="display: inline;">This bill payment entry is no longer available</p>
            </div>
        <?php
    } ?>
    </div>
</div>