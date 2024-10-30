<?php $currency_symbol = $this->session->userdata('currency'); ?>
<a id="new_account" href="javascript:void(0);"  class="btn btn-sm blue" data-toggle="modal" data-target="#customModal" style="margin-bottom:10px;display: none;">Add an Account</a>
<?php
$debit_entry = number_format($expenses_res->amount, 2);
$debit_entry = str_replace(',', '', $debit_entry);
$total_paid_balance = 0;
$count_denom = 2;
$bill_payment = 0;

if (isset($bill_payment_transaction['records']) && !empty($bill_payment_transaction['records'])) {
    $count_denom = substr_count($bill_payment_transaction['records'][0]->value_denom, '0');
    foreach ($bill_payment_transaction['records'] as $pRow) {
        if(isset($bill_payment_res) && !empty($bill_payment_res) && $bill_payment_res->bill_transaction_guid == $pRow->tx_guid) {
            $bill_payment = ($pRow->value_num / $pRow->value_denom);
            //$total_paid_balance += ($pRow->value_num / $pRow->value_denom);
        } else {
            $total_paid_balance += ($pRow->value_num / $pRow->value_denom);
        }
    }
}

//echo $debit_entry."===".$total_paid_balance;
$remain_balance = (($debit_entry - $total_paid_balance));
//if($bill_payment > 0) {
//    $remain_balance = (($remain_balance - $bill_payment));
//}
$total_paid_balance = number_format($total_paid_balance, $count_denom);

$attributes = array('class' => 'form-horizontal validate', 'id' => 'frmAddBillEntry');
echo form_open_multipart('expenses/bill_payment_save', $attributes, array('expense_guid' => isset($expenses_res->guid) ? $expenses_res->guid : ''));
?>
<input type="hidden" name="type_accounts[]" id="type_accounts" value="<?php echo isset($type_accounts) ? $type_accounts : ''; ?>" />
<input type="hidden" name="bill_payment_id" value="<?php echo (isset($bill_payment_res) && !empty($bill_payment_res)) ? $bill_payment_res->id : ''; ?>" />
<input type="hidden" name="bill_transaction_guid" value="<?php echo (isset($bill_payment_res) && !empty($bill_payment_res)) ? $bill_payment_res->bill_transaction_guid : ''; ?>" />

<div id="error_div" class="alert alert-danger" style="display: none;">
    <button class="close" data-close="alert"></button>
    <strong>Error! </strong><p style="display: inline;"></p>
</div>
<div class="col-xs-12">
    <div class="row">
        <div class="col-md-12" style="margin-bottom: 12px;">
            <div class="btn-group pull-right">
                <button type="button" class="btn green btn-sm btn-outline dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> <i class="fa fa-history" aria-hidden="true"></i></button>
                <ul class="dropdown-menu pull-right" role="menu" style="max-height: 320px;overflow: hidden; width: 400px; ">
                    <li> <a href="javascript:void(0);"><b>Recent Bill Payment</b></a></li>
                    <li class="divider"></li>
                    <li>
                        <div style="margin: 10px; ">
                            <table class="table table-bordered table-hover">
                                <tr>
                                    <th>Type</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Payee</th>
                                </tr>
                                <?php
                                if (isset($logs) && !empty($logs)) {
                                    foreach ($logs as $key => $log) {
                                        if(empty($bill_payment_res) || (isset($bill_payment_res) && !empty($bill_payment_res) && $bill_payment_res->id != $log->id)) {
                                        if ($key < 5) { ?>
                                            <tr>
                                                <td>Bill Payment</td>
                                                <td><?php echo date('m-d-Y', strtotime($log->bill_payment_date)); ?></td>
                                                <td style="text-align: right;"><?php echo $currency_symbol . number_format($log->payment_amount, 2); ?></td>
                                                <td><?php echo $log->payee; ?></td>
                                            </tr>
                                <?php }}}}?>
                            </table>
                        </div>
                    </li>
                    <?php if (isset($logs) && !empty($logs) && sizeof($logs) > 5) { ?>
                        <li><a href="<?php echo base_url('expenses') . '?expense_type=Bill Payment'; ?>">View more</a></li>
                    <?php } ?>
                </ul>
            </div>  
        </div>
    </div>
    <div class="row">
        <div class="col-xs-3">
            <div class="form-group">
                <?php
                $options = array();
//                $options[''] = '-- Select Vendor --';
//                $options['CA'] = 'Add Vendor';
                $customer = set_value('customer', (isset($expenses_res->customer_guid)) ? $expenses_res->customer_guid : NULL);
                foreach ($customer_data['records'] as $row) {
                    $options[$row->guid] = $row->name_display_as;
                }
                echo form_dropdown(array('id' => 'customer', 'options' => $options, 'class' => 'form-control require jselect2me', 'selected' => $customer, 'type' => 'V', 'onchange' => 'add_customer(this);', 'disabled' => 'disabled'));
                ?>
                <input type="hidden" name="customer" value="<?php echo $customer; ?>">
            </div>
        </div>
        <div class="col-xs-6">
            <div class="form-group" style="margin-left: 15px;">
                <label class="control-label pt0 col-xs-5">Bank / Credit Account</label>
                <div class="col-xs-7">
                    <?php
                    $options = array();
                    $options[''] = '-- Select Account --';
                    if(isset($this->has_account_create_permission) && $this->has_account_create_permission) {
                        $options['CA'] = 'Custom Account';
                    }
                    $bank_credit_account = set_value('bank_credit_account', (isset($bill_res->bill_credit_acc_guid)) ? $bill_res->bill_credit_acc_guid : NULL);
                    foreach ($credit_acc as $row) {
                        $options[$row->guid] = $row->hierarchy_path;
                    }
                    echo form_dropdown(array('id' => 'bank_credit_account', 'name' => 'bank_credit_account', 'options' => $options, 'class' => 'form-control require jselect2me', 'onchange' => 'set_account(this, 1)', 'selected' => $bank_credit_account));
                    ?>
                    <input type="hidden" name="old_bank_credit_account" id="old_bank_credit_account" value="<?php echo $bank_credit_account; ?>"/>
                </div>
            </div>
        </div>
        <div class="col-xs-1">
            <label class="label label-primary label-sm" id="balance" name="balance"><?php echo isset($balance) ? $balance : 0; ?></label>
            <input type="hidden" id="acc_balance" name="acc_balance"  value="<?php echo isset($balance) ? $balance : 0; ?>"/>
        </div>
        <div class="col-xs-2 pull-right">
            <div class="form-group">
                <label class="control-label pt0">AMOUNT PAID </label>
                <h2 class="disp_total" id="disp_total" style="font-weight: bold;">
                    <?php 
                        echo isset($remain_balance) && $remain_balance != '' ? number_format($remain_balance, 2): '0.00';
                    ?>
                </h2>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group">
            <div class="col-xs-3">
                <label class="control-label">Mailing Address</label>
                <?php
                $mailing_address = field(set_value('mailing_address', NULL), (isset($expenses_res->mailing_address)) ? $expenses_res->mailing_address : NULL);
                $data = array(
                    'name' => 'mailing_address',
                    'value' => $mailing_address,
                    'rows' => '3',
                    'cols' => '15',
                    'class' => 'form-control',
                    'id' => 'mailing_address',
                    'placeholder' => 'Mailing Address'
                );
                echo form_textarea($data);
                ?>
            </div>
            <div class="col-xs-2">
                <label class="control-label">Payment Date</label>
                <?php
                // $payment_date = date("m/d/Y");
                $payment_date = field(set_value('bill_date', NULL), (isset($bill_res->bill_payment_date)) ? date('m/d/Y', strtotime($bill_res->bill_payment_date)) : date("m/d/Y"));
                echo form_input(array('name' => 'bill_date', 'id' => 'bill_date', 'class' => 'form-control lock_datepicker', 'required' => 'required', 'readonly' => 'readonly'), $payment_date);
                ?>
            </div>

            <div class="col-xs-2 pull-right">
                <label class="control-label">Ref No</label>
                <?php
                echo form_input(array('name' => 'expense_no', 'id' => 'expense_no', 'class' => 'form-control'));
                ?>
            </div>
        </div>
    </div>
    <h4 class="text-bold">Account Details</h4>
    <table class="table table-striped table-hover table-bordered">
        <thead>
            <tr>
                <th>
                    Description
                </th>
                <th>
                    Due Date
                </th>
                <th>
                    Original Amount
                </th>
                <th>
                    Paid Amount
                </th>
                <th>
                    Open Balance
                </th>
                <th>
                    Payment
                </th>
            </tr>
        </thead>
        <tbody>
            <td><?php echo "Bill # " . $expenses_res->expense_no; ?></td>
            <td><?php echo date("m/d/Y", strtotime($expenses_res->due_date)); ?></td>
            <td>
                <?php
                echo $debit_entry;
                ?>
            </td>
            <td>
                <?php
                echo $total_paid_balance;
                ?>
            </td>
            <td id="remain_balance">
                <?php
                echo number_format($remain_balance, 2);
                ?>
                <input type="hidden" name="invoice_amount" id="invoice_amount" value="<?php echo $debit_entry; ?>"/>
                <input type="hidden" name="remain_balance" id="remain" value="<?php echo $remain_balance; ?>"/>
            </td>
            <?php if($bill_payment > 0) { ?>
            <td>
                <input type="text" class="form-control require integers" name="payment" id="payment" value="<?php echo number_format($bill_payment,2); ?>" onblur="get_format(this);"/>
                <?php echo form_error('payment'); ?>
            </td>
            <?php } else { ?>
            <td>
                <input type="text" class="form-control require integers" name="payment" id="payment" value="<?php echo number_format($remain_balance,2); ?>" onblur="get_format(this);"/>
                <?php echo form_error('payment'); ?>
            </td>
            <?php } ?>
        </tbody>
        <tfoot>
            <tr>
                <td colspan="5">&nbsp;</td>
                <td>Amount To Apply <span id="apply_amount"><?php echo number_format($remain_balance,2); ?></span>
                    <input type="button" name="btnClear" id="btnClear" class="btn btn-danger pull-right" value="Clear" onclick="clear_payment();"/></td>
            </tr>
        </tfoot>
    </table>
</div>

<div class="form-group">
    <div class="col-xs-6">
        <label class="control-label">Memo</label>
        <?php
        $memo = field(set_value('memo', NULL), (isset($expenses_res->memo)) ? $expenses_res->memo : NULL);
        $data = array(
            'name' => 'memo',
            'value' => $memo,
            'rows' => '3',
            'cols' => '20',
            'class' => 'form-control',
            'id' => 'memo',
        );
        echo form_textarea($data);
        ?>
    </div>
</div>
<div class="journal_footer">
    <div class="pull-left">
        <?php echo anchor('expenses', 'Cancel', array("class" => "btn btn-danger")); ?>
    </div>
    <div class="pull-right">
        <button type="submit" class="btn btn-success save_btn" name="btnSave" id="btnSave">Save</button>
    </div>
</div>
<?php echo form_close(); ?>
<div style="margin: 1rem 0 5rem;">
    <label class="control-label">Attachments</label>
    <form enctype="multipart/form-data" action="<?php echo base_url('accounts/dropzone?tguid=' . $expenses_res->guid) ?>" class="dropzone" id="my-dropzone">
        <?php
        if (isset($files) && !empty($files)) {
            foreach ($files as $key => $val) {
                $ext = strtolower(pathinfo($val, PATHINFO_EXTENSION));
                ?>
                <div class="dz-preview dz-file-preview dz-processing dz-success dz-complete">
                    <a href="<?php echo base_url() . "files_upload/expenses_documents/" . $expenses_res->guid . "/" . $val; ?>"  target="_blank">                
                        <div class="dz-image text-center"><img class="file_icon" src="<?php echo base_url("assets/layouts/layout/img/file_icons/" . $ext . ".png") ?>" alt="<?php echo $ext; ?>"></div>
                        <div class="dz-details">
                            <div class="dz-filename">
                                <span data-dz-name=""><?php echo $val; ?></span>
                            </div>
                        </div>
                        <a href="javascript:;" class="btn red btn-sm btn-block" onclick="remove_file(this);">Remove</a>
                        <input type="hidden" value="<?php echo $val; ?>" />
                    </a>
                </div>
                <?php
            }
        }
        ?>
    </form>
</div>
<script>
    $(document).ready(function () {
        $(".jselect2me").select2();
    });
    $(document).on('keydown', '.integers', function (e) {
        if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                (e.keyCode == 65 && e.ctrlKey === true) ||
                (e.keyCode == 67 && e.ctrlKey === true) ||
                (e.keyCode == 88 && e.ctrlKey === true) ||
                (e.keyCode >= 35 && e.keyCode <= 39)) {
            return;
        }
        if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
            e.preventDefault();
        }
    });
    function clear_payment() {
        $("#payment").val("");
    }
    function get_format(element) {
        var value = parseFloat($(element).val().replace(/,/g, '')).toFixed(2);
        var remain_amount = $("#remain_balance").html().replace(/,/g, '');
        var remain_amount_bal = parseFloat(remain_amount).toFixed(2);
        if (!isNaN(value) && (parseFloat($(element).val()) <= remain_amount_bal) && value > 0) {
            $(element).val(value);
            $("#apply_amount").html(value);
        } else {
            $(element).val("");
            $("#apply_amount").html("0.00");
        }
    }
</script>