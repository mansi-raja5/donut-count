<style type="text/css">
#desc_tbl td,
#br_desc_tbl td,
#check_desc_tbl td {
    padding: 0 !important;
}
#desc_tbl td input,#desc_tbl td select,
#br_desc_tbl td input,#br_desc_tbl td select,
#check_desc_tbl td input,#check_desc_tbl td select{
    padding: 2px !important;
}
.attachment-icon input{
    display: none;
}
.btn-icon-only{
    margin: 0 10px;
}
</style>
<?php $currency_symbol = $this->session->userdata('currency'); ?>
<a id="new_account" href="javascript:void(0);"  class="btn btn-sm blue" data-toggle="modal" data-target="#customModal" style="margin-bottom:10px;display: none;">Add an Account</a>
<a href="<?php echo base_url('product/add'); ?>" class="btn blue display-hide" id="new_product" data-toggle="modal" data-target="#add_product">Add Product</a>
<a href="<?php echo base_url('project/add'); ?>" class="btn blue display-hide" id="new_project" data-toggle="modal" data-target="#add_project">Add Project</a>
<?php
$count_entry = 2;
$attributes = array('class' => 'form-horizontal validate', 'id' => 'frmAddBillEntry');
echo form_open_multipart('bill/bill_entry_save', $attributes, array('expense_guid' => isset($bill_res->guid) ? $bill_res->guid : ''));
?>
<input type="hidden" name="edit_bill_id" id="no_rows" value="<?php echo isset($bill_res->id) ? $bill_res->id : 0; ?>" />
<input type="hidden" name="no_rows" id="no_rows" value="2" />
<div id="error_div" class="alert alert-danger" style="display: none;">
    <button class="close" data-close="alert"></button>
    <strong>Error! </strong><p style="display: inline;"></p>
</div>
<div class="col-xs-12">
    <div class="row">
        <div class="col-xs-4">
            <div class="form-group">
                <label class="control-label">Select Ledger Month and Year </label>
                <?php
                $bill_date = isset($bill_res->month) ? ($bill_res->month > 9 ? $bill_res->month : '0' . $bill_res->month) . "/" . $bill_res->year : date('m/Y');
                ?>
                <input type="text" name="ledger_bill_date" id="ledger_bill_date" class="form-control lock_datepicker" value="<?php echo $bill_date; ?>"/>
            </div>
        </div>
        <div class="pull-right">
            <button type="submit" class="btn btn-success save_btn" name="btnSave" id="btnSave">Generate Bill</button>
        </div>
    </div>
    <div class="row">
        <div class="portlet green box">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs"></i>Description Details
                </div>
                <div class="tools">
                    <a href="javascript:;" class="expand" data-original-title="" title="">
                    </a>
                </div>
            </div>
            <div class="portlet-body ">
                <div id="error_div_item"  style="display: none;">
                    <div class="alert alert-danger">
                        <button class="close" data-close="alert"></button>
                        <strong>Error!  </strong>
                        <div class="item_error_msg">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered" id="desc_tbl">
                        <thead>
                            <tr>
                                <th width="120px">
                                    Store Key
                                </th>
                                <th>
                                    Store Physical #
                                </th>
                                <th>
                                    Bill Date
                                </th>
                                <th>
                                    Bill Number
                                </th>
                                <th width="120px">
                                    Category
                                </th>
                                <th width="120px">
                                    Description
                                </th>
                                <th width="20px">
                                    Qty
                                </th>
                                <th>
                                    Rate
                                </th>
                                <th>
                                    Amount
                                </th>
                                <th>
                                    Status
                                </th>
                                <th>
                                    Last Paid Date
                                </th>
                                <th>
                                    Last Paid Amt
                                </th>
                                <th>
                                    Paid
                                </th>
                                <th>
                                </th>
                                <th>
                                </th>
                            </tr>
                        </thead>

                        <tbody class="desc">
                        <?php $i = 0; ?>
                        <tr id="itr_0">
                        <input type="hidden" name="bill_type[]" value="description"/>
                        <td>
                            <?php
                            $store_key = field(set_value('name', NULL), (isset($Row->store_key)) ? $Row->store_key : '');
                            $options = array();
                            $options[''] = '-- Select --';
                            foreach ($store['records'] as $row) {
                                $options[$row->key] = $row->key;
                            }
                            echo form_dropdown(array('name' => 'store[]', 'options' => $options, 'class' => 'form-control jselect2me store_cls', 'onchange' => 'set_store(this, \'D\');prev_data(this);', 'selected' => $store_key));
                            ?>
                        </td>
                        <td>
                            <input type="text" class="form-control store_physical_address" name="store_physical_address[]" id="store_physical_address_<?php echo $i; ?>" value="<?php echo isset($Row->store_physical_address) ? $Row->store_physical_address : ''; ?>"/>
                        </td>
                        <td>
                            <input type="text" class="form-control bill_date" name="bill_date[]" id="bill_date_<?php echo $i; ?>" value="<?php echo isset($Row->bill_date) && $Row->bill_date != '' ? DB2Disp($Row->bill_date) : ''; ?>"/>
                        </td>
                        <td>
                            <input type="text" class="form-control bill_no" name="bill_no[]" id="bill_no_<?php echo $i; ?>" value="<?php echo isset($Row->bill_no) ? $Row->bill_no : ''; ?>"/>
                        </td>
                        <td>
                            <?php
                            $category_key = field(set_value('name', NULL), (isset($Row->category_key)) ? $Row->category_key : '');
                            $options = array();
                            $options[''] = '-- Select --';
                            foreach ($category['records'] as $row) {
                                $options[$row->category_key] = $row->category_name;
                            }
                            echo form_dropdown(array('name' => 'category[]', 'options' => $options, 'class' => 'form-control category_cls', 'onchange' => 'get_desc(this, \'D\');prev_data(this);', 'selected' => $category_key));
                            ?>
                        </td>
                        <td class="td_description_cls">
                            <?php
                            $description = field(set_value('name', NULL), (isset($Row->description)) ? $Row->description : '');
                            $options = array();
                            $options[''] = '-- Select Category First--';
                            echo form_dropdown(array('name' => 'description[]', 'options' => $options, 'class' => 'form-control description_cls', 'selected' => $description, 'onchange' => 'prev_data(this);'));
                            ?>
                        </td>
                        <td>
                            <input type="text" class="form-control integers qty" name="qty[]" id="qty_<?php echo $i; ?>" value="<?php echo isset($Row->qty) ? $Row->qty : ''; ?>" onkeyup="qty(this, 'D');"/>
                        </td>
                        <td>
                            <input type="text" class="form-control rate" name="rate[]" id="rate_<?php echo $i; ?>" value="<?php echo isset($Row->rate) ? $Row->rate : ''; ?>" onkeyup="rate(this, 'D');"/>
                        </td>
                        <td>
                            <input type="text" class="form-control amount" name="amount[]" id="amount_<?php echo $i; ?>" value="<?php echo isset($Row->amount) ? $Row->amount : ''; ?>" readonly=""/>
                        </td>
                        <td>
                            <input type="text" readonly class="form-control status" name="status[]" id="status_<?php echo $i; ?>" value="<?php echo isset($Row->status) ? $Row->status : ''; ?>"/>
                        </td>
                        <td>
                            <input type="text" readonly="" class="form-control last_paid_date" name="last_paid_date[]" id="last_paid_date_<?php echo $i; ?>" value="<?php echo isset($Row->last_paid_date) && $Row->last_paid_date != '' ? DB2Disp($Row->last_paid_date) : ''; ?>"/>
                        </td>
                        <td>
                            <input type="text" readonly class="form-control last_paid_amount" name="last_paid_amount[]" id="last_paid_amount_<?php echo $i; ?>" value="<?php echo isset($Row->last_paid_amount) ? $Row->last_paid_amount : ''; ?>"/>
                        </td>
                        <td>
                            <?php $paid_status = isset($Row->is_paid) && $Row->is_paid == 1 ? "Unpaid" : 'Paid'; ?>
                            <button type="button" onclick="set_paid_val(this, 'D');" class="btn btn-success btn-sm"><?php echo $paid_status; ?></button>
                            <input type="hidden" class="is_paid" name="is_paid[]" id="is_paid_<?php echo $i; ?>" value="<?php echo isset($Row->is_paid) ? $Row->is_paid : ''; ?>"/>
                        </td>
                        <td  style="padding: 6px 0 !important">
                            <a class="btn-icon-only attachment-icon" href="javascript:;">
                                <i class="fa fa-paperclip fa-2x" aria-hidden="true"></i>
                                <input type="file" multiple="" class="form-control" name="attachment[0][]" id="attachment_<?php echo $i; ?>"/>
                            </a>
                        </td>
                        <td></td>
                        </tr>
                        <?php
                        if (isset($bill_item_desc_res['records']) && !empty($bill_item_desc_res['records']) && count($bill_item_desc_res['records']) > 0) {
                            $expenses_item_bal = 0;
                            $i = 1;
                            $this->load->model('category_model');
                            foreach ($bill_item_desc_res['records'] as $Row) {
                                $bill_category_type = $Row->type;
                                if ($bill_category_type == 'week_description') {
                                    $ledger_date = isset($bill_res->month) ? $bill_res->year . "/" . ($bill_res->month > 9 ? $bill_res->month : '0' . $bill_res->month)."/01" : date('Y/m/d');
                                    $result = getWeekStartingEndingDateFromMonth($ledger_date);

                                    $date_Arr = array();
                                    if (isset($result) && !empty($result)) {
                                        foreach ($result as $cRow) {
                                            if ($bill_res->month == date('m', strtotime($cRow['end_of_week']))) {
                                                $month_date_Res[] = $cRow['end_of_week'];
                                            }
                                        }
                                    }
                                }else{
                                        $cat_Res = $this->category_model->get_category_desc("description", $Row->category_key);
                                }
                                $expenses_item_bal += $Row->amount;


                                ?>
                                <tr id="itr_<?php echo $i; ?>">
                                <input type="hidden" name="bill_id" value="<?php echo isset($Row->id) ? $Row->id : ''; ?>"/>
                                <input type="hidden" name="bill_type[]" value="<?php echo isset($Row->type) ? $Row->type : ''; ?>"/>

                                <td>
                                    <?php
                                    $store_key = field(set_value('name', NULL), (isset($Row->store_key)) ? $Row->store_key : '');
                                    $options = array();
                                    $options[''] = '-- Select --';
                                    foreach ($store['records'] as $row) {
                                        $options[$row->key] = $row->key;
                                    }
                                    echo form_dropdown(array('name' => 'store[]', 'options' => $options, 'class' => 'form-control jselect2me store_cls', 'onchange' => 'set_store(this, \'D\');prev_data(this);', 'selected' => $store_key));
                                    ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control store_physical_address" name="store_physical_address[]" id="store_physical_address_<?php echo $i; ?>" value="<?php echo isset($Row->store_physical_address) ? $Row->store_physical_address : ''; ?>"/>
                                </td>
                                <td>
                                    <input type="text" class="form-control bill_date" name="bill_date[]" id="bill_date_<?php echo $i; ?>" value="<?php echo isset($Row->bill_date) && $Row->bill_date != '' ? DB2Disp($Row->bill_date) : ''; ?>"/>
                                </td>
                                <td>
                                    <input type="text" class="form-control bill_no" name="bill_no[]" id="bill_no_<?php echo $i; ?>" value="<?php echo isset($Row->bill_no) ? $Row->bill_no : ''; ?>"/>
                                </td>
                                <td>
                                    <?php
                                    $selected_category = field(set_value('name', NULL), (isset($Row->category_key)) ? $Row->category_key : '');
                                    $options = array();
                                    $options[''] = '-- Select --';

                                    foreach ($category['records'] as $row) {
                                        $options[$row->category_key] = $row->category_name;
                                    }
                                    echo form_dropdown(array('name' => 'category[]', 'options' => $options, 'class' => 'form-control category_cls', 'onchange' => 'get_desc(this, \'D\');prev_data(this);', 'selected' => $selected_category));
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $description = field(set_value('name', NULL), (isset($Row->description)) ? $Row->description : '');
                                    $options = array();
                                    $options[''] = '-- Select --';
                                    if ($bill_category_type == 'week_description') {
                                       if(isset($month_date_Res) && !empty($month_date_Res)){
                                            foreach ($month_date_Res as $row_Date) {
                                                $options[$row_Date] = $row_Date;
                                            }
                                       }
                                    }else{
                                         foreach ($cat_Res as $row) {
                                            $options[$row->id] = $row->description;
                                        }
                                    }
                                    echo form_dropdown(array('name' => 'description[]', 'options' => $options, 'class' => 'form-control description_cls', 'selected' => $description, 'onchange' => 'prev_data(this);'));
                                    ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control integers qty" name="qty[]" id="qty_<?php echo $i; ?>" value="<?php echo isset($Row->qty) ? $Row->qty : ''; ?>" onkeyup="qty(this, 'D');"/>
                                </td>
                                <td>
                                    <input type="text" class="form-control rate" name="rate[]" id="rate_<?php echo $i; ?>" value="<?php echo isset($Row->rate) ? $Row->rate : ''; ?>" onkeyup="rate(this, 'D');"/>
                                </td>
                                <td>
                                    <input type="text" class="form-control amount" name="amount[]" id="amount_<?php echo $i; ?>" value="<?php echo isset($Row->amount) ? $Row->amount : ''; ?>" readonly=""/>
                                </td>
                                <td>
                                    <input type="text" readonly class="form-control status" name="status[]" id="status_<?php echo $i; ?>" value="<?php echo isset($Row->status) ? $Row->status : ''; ?>"/>
                                </td>
                                <td>
                                    <input type="text" readonly="" class="form-control last_paid_date" name="last_paid_date[]" id="last_paid_date_<?php echo $i; ?>" value="<?php echo isset($Row->last_paid_date) && $Row->last_paid_date != '' ? DB2Disp($Row->last_paid_date) : ''; ?>"/>
                                </td>
                                <td>
                                    <input type="text" readonly class="form-control last_paid_amount" name="last_paid_amount[]" id="last_paid_amount_<?php echo $i; ?>" value="<?php echo isset($Row->last_paid_amount) ? $Row->last_paid_amount : ''; ?>"/>
                                </td>
                                <td><?php $paid_status = isset($Row->is_paid) && $Row->is_paid == 1 ? "Unpaid" : 'Paid'; ?>
                                    <button type="button" onclick="set_paid_val(this, 'D');" class="btn btn-success btn-sm"><?php echo $paid_status; ?></button>
                                    <input type="hidden" class="is_paid" name="is_paid[]" id="is_paid_<?php echo $i; ?>" value="<?php echo isset($Row->is_paid) ? $Row->is_paid : ''; ?>"/>
                                </td>
                                <td  style="padding: 6px 0 !important">
                                    <input type="hidden" class="is_attachment" name="hidden_attachment[<?php echo $i; ?>][]" id="hidden_attachment_<?php echo $i; ?>" value="<?php echo isset($Row->attachment) ? $Row->attachment : ''; ?>"/>
                                    <a class="btn-icon-only attachment-icon" href="javascript:;">
                                        <i class="fa fa-paperclip fa-2x" aria-hidden="true"></i>
                                        <input type="file" multiple name="attachment[<?php echo $i; ?>][]" id="attachment_<?php echo $i; ?>"/>
                                    </a>

                                    <!-- Display Attachment -->
                                    <?php if(isset($Row->attachment) && $Row->attachment != ''): ?>
                                    <a onclick="openBillItemAttachment(<?php echo $Row->id; ?>)">View</a>
                                    <div class="modal fade" id="attachment_modal_<?php echo $Row->id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <div class="col-md-10">
                                                        <h3 class="modal-title"><b>Attachments</b></h3>
                                                    </div>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <?php
                                                            if(isset($Row->attachment) && $Row->attachment != '')
                                                            {
                                                                $attachment = explode(",", $Row->attachment);
                                                                foreach ($attachment as $_attachment) {
                                                                    $extension  = pathinfo($_attachment,PATHINFO_EXTENSION);
                                                                    ?>
                                                                    <a href='<?php echo base_url()."files_upload/bill_attachment/$Row->bill_id/$_attachment"; ?>' class="<?php echo strtolower($extension).'-icon' ; ?>" target="_blank" download><?php echo $_attachment; ?></a>
                                                                    <?php
                                                                }
                                                            }
                                                            ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a class="btn-icon-only font-red" name="delete_btn[]" onclick="delete_row(this);"><i class="fa fa-trash fa-2x"></i></a>
                                </td>
                                </tr>
                                <?php
                                $i++;
                            }
                        }
                        ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="8" align="right">Total</td>
                                <td id="item_total" align="right"><?php echo isset($expenses_item_bal) && $expenses_item_bal != '' ? sprintf("%.2f", $expenses_item_bal) : '0'; ?></td>
                                <td colspan="6"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <button type="button" class="btn btn-sm btn-success" name="btnAddLines" id="btnAddLines" onclick="add_item_lines(this);">Add Lines</button>
                <button type="button" class="btn btn-sm btn-danger" name="btnRemoveLines" id="btnRemoveLines" onclick="remove_item_lines(this);">Remove Lines</button>
            </div>
        </div>

        <div class="portlet green box">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs"></i>Breakdown Description Details
                </div>
                <div class="tools">
                    <a href="javascript:;" class="expand" data-original-title="" title="">
                    </a>
                </div>
            </div>
            <div class="portlet-body ">
                <div id="br_error_div_item"  style="display: none;">
                    <div class="alert alert-danger">
                        <button class="close" data-close="alert"></button>
                        <strong>Error!  </strong>
                        <div class="item_error_msg">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered" id="br_desc_tbl">
                        <thead>
                            <tr>
                                <th width="120px">
                                    Store Key
                                </th>
                                <th>
                                    Store Physical #
                                </th>
                                <th>
                                    Bill Date
                                </th>
                                <th>
                                    Bill Number
                                </th>
                                <th width="120px">
                                    Category
                                </th>
                                <th width="120px">
                                    Description
                                </th>
                                <th >
                                    Breakdown Description
                                </th>
                                <th>
                                    Qty
                                </th>
                                <th>
                                    Rate
                                </th>
                                <th>
                                    Amount
                                </th>
                                <th>
                                    Status
                                </th>
                                <th>
                                    Last Paid Date
                                </th>
                                <th>
                                    Last Paid Amt
                                </th>
                                <th>
                                    Paid
                                </th>
                                <th>
                                </th>
                                <th>
                                </th>
                            </tr>
                        </thead>

                        <tbody class="br_desc">
                            <?php $i = 0; ?>
                            <tr id="itr_0">

                                <td>
                                    <?php
                                    $options = array();
                                    $options[''] = '-- Select --';
                                    foreach ($store['records'] as $row) {
                                        $options[$row->key] = $row->key;
                                    }
                                    echo form_dropdown(array('name' => 'br_store[]', 'options' => $options, 'class' => 'form-control jselect2me store_cls', 'onchange' => 'set_store(this, \'BD\');breakdown_desc_prev_data(this);'));
                                    ?>
                                </td>
                                <td>
                                    <input type="text" class="form-control store_physical_address" name="br_store_physical_address[]" id="store_physical_address_<?php echo $i; ?>" value=""/>
                                </td>
                                <td>
                                    <input type="text" class="form-control bill_date" name="br_bill_date[]" id="bill_date_<?php echo $i; ?>" value=""/>
                                </td>
                                <td>
                                    <input type="text" class="form-control bill_no" name="br_bill_no[]" id="bill_no_<?php echo $i; ?>" value=""/>
                                </td>
                                <td>
                                    <?php
                                    $options = array();
                                    $options[''] = '-- Select --';
                                    foreach ($br_category['records'] as $row) {
                                        $options[$row->category_key] = $row->category_name;
                                    }
                                    echo form_dropdown(array('name' => 'br_category[]', 'options' => $options, 'class' => 'form-control category_cls', 'onchange' => 'get_desc(this, \'BD\');breakdown_desc_prev_data(this);'));
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    $options = array();
                                    $options[''] = '-- Select Category First--';
                                    echo form_dropdown(array('name' => 'br_description[]', 'options' => $options, 'class' => 'form-control description_cls', 'onchange' => 'get_breakdown_description(this);breakdown_desc_prev_data(this);'));
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    $options = array();
                                    $options[''] = '-- Select Description First--';
                                    echo form_dropdown(array('name' => 'br_breakdown_description[]', 'options' => $options, 'class' => 'form-control br_description_cls', 'onchange' => 'breakdown_desc_prev_data(this);'));
                                    ?>
                                </td>

                                <td>
                                    <input type="text" class="form-control integers qty" name="br_qty[]" id="qty_<?php echo $i; ?>" value="" onkeyup="qty(this, 'BD');"/>
                                </td>
                                <td>
                                    <input type="text" class="form-control rate" name="br_rate[]" id="rate_<?php echo $i; ?>" value="" onkeyup="rate(this, 'BD');"/>
                                </td>
                                <td>
                                    <input type="text" class="form-control amount" name="br_amount[]" id="amount_<?php echo $i; ?>" value="" readonly=""/>
                                </td>
                                <td>
                                    <input type="text" readonly class="form-control status" name="br_status[]" id="status_<?php echo $i; ?>" value=""/>
                                </td>
                                <td>
                                    <input type="text" readonly="" class="form-control br_last_paid_date" name="br_last_paid_date[]" id="last_paid_date_<?php echo $i; ?>" value=""/>
                                </td>
                                <td>
                                    <input type="text" readonly class="form-control br_last_paid_amount" name="br_last_paid_amount[]" id="last_paid_amount_<?php echo $i; ?>" value=""/>
                                </td>
                                <td>
                                    <button type="button" onclick="set_paid_val(this, 'BR');" class="btn btn-success btn-sm"><?php echo $paid_status; ?></button>
                                    <input type="hidden" class="is_paid" name="br_is_paid[]" id="is_paid_<?php echo $i; ?>" value="<?php echo isset($Row->is_paid) ? $Row->is_paid : ''; ?>"/>
                                </td>
                                <td  style="padding: 6px 0 !important">
                                    <a class="btn-icon-only attachment-icon" href="javascript:;">
                                        <i class="fa fa-paperclip fa-2x" aria-hidden="true"></i>
                                        <input type="file"  multiple class="form-control" name="br_attachment[0][]" id="attachment_<?php echo $i; ?>"/>
                                    </a>
                                </td>
                                <td>
                                </td>
                            </tr>
                            <?php
                            if (isset($bill_item_breakdown_res['records']) && !empty($bill_item_breakdown_res['records']) && count($bill_item_breakdown_res['records']) > 0) {

                                $expenses_item_bal = 0;
                                $i = 1;
                                foreach ($bill_item_breakdown_res['records'] as $Row) {
                                    $expenses_item_bal += $Row->amount;
                                    $this->load->model('category_model');
                                    $cat_Res = $this->category_model->get_category_desc("breakdown_description", $Row->category_key);
                                    $br_cat_Res = $this->category_model->Get_breakdown_category($Row->description);
                                    ?>
                                    <tr id="itr_<?php echo $i; ?>">

                                        <td>
                                            <?php
                                            $store_key = field(set_value('name', NULL), (isset($Row->store_key)) ? $Row->store_key : '');
                                            $options = array();
                                            $options[''] = '-- Select --';
                                            foreach ($store['records'] as $row) {
                                                $options[$row->key] = $row->key;
                                            }
                                            echo form_dropdown(array('name' => 'br_store[]', 'options' => $options, 'class' => 'form-control store_cls', 'onchange' => 'set_store(this, \'BD\');breakdown_desc_prev_data(this);', 'selected' => $store_key));
                                            ?>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control store_physical_address" name="br_store_physical_address[]" id="store_physical_address_<?php echo $i; ?>" value="<?php echo isset($Row->store_physical_address) ? $Row->store_physical_address : ''; ?>"/>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control bill_date" name="br_bill_date[]" id="bill_date_<?php echo $i; ?>" value="<?php echo isset($Row->bill_date) && $Row->bill_date != '' ? DB2Disp($Row->bill_date) : ''; ?>"/>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control bill_no" name="br_bill_no[]" id="bill_no_<?php echo $i; ?>" value="<?php echo isset($Row->bill_no) ? $Row->bill_no : ''; ?>"/>
                                        </td>
                                        <td>
                                            <?php
                                            $category_selection = field(set_value('name', NULL), (isset($Row->category_key)) ? $Row->category_key : '');
                                            $options = array();
                                            $options[''] = '-- Select --';
                                            foreach ($br_category['records'] as $row) {
                                                $options[$row->category_key] = $row->category_name;
                                            }
                                            echo form_dropdown(array('name' => 'br_category[]', 'options' => $options, 'class' => 'form-control category_cls', 'onchange' => 'get_desc(this, \'BD\');breakdown_desc_prev_data(this);', 'selected' => $category_selection));
                                            ?>
                                        </td>

                                        <td>
                                            <?php
                                            $description = field(set_value('name', NULL), (isset($Row->description)) ? $Row->description : '');

                                            $options = array();
                                            $options[''] = '-- Select --';
                                            foreach ($cat_Res as $row) {
                                                $options[$row->id] = $row->description;
                                            }
                                            echo form_dropdown(array('name' => 'br_description[]', 'options' => $options, 'class' => 'form-control description_cls', 'selected' => $description, 'onchange' => 'get_breakdown_description(this);breakdown_desc_prev_data(this);'));
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $breakdown_description = field(set_value('name', NULL), (isset($Row->breakdown_description)) ? $Row->breakdown_description : '');
                                            $options = array();
                                            $options[''] = '-- Select Description First--';
                                            foreach ($br_cat_Res as $row) {
                                                $options[$row->description] = $row->description;
                                            }
                                            echo form_dropdown(array('name' => 'br_breakdown_description[]', 'options' => $options, 'class' => 'form-control br_description_cls', 'selected' => $breakdown_description, 'onchange' => 'breakdown_desc_prev_data(this);'));
                                            ?>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control integers qty" name="br_qty[]" id="qty_<?php echo $i; ?>" value="<?php echo isset($Row->qty) ? $Row->qty : ''; ?>" onkeyup="qty(this, 'BD');"/>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control rate" name="br_rate[]" id="rate_<?php echo $i; ?>" value="<?php echo isset($Row->rate) ? $Row->rate : ''; ?>" onkeyup="rate(this, 'BD');"/>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control amount" name="br_amount[]" id="amount_<?php echo $i; ?>" value="<?php echo isset($Row->amount) ? $Row->amount : ''; ?>" readonly=""/>
                                        </td>
                                        <td>
                                            <input type="text" readonly class="form-control status" name="br_status[]" id="status_<?php echo $i; ?>" value="<?php echo isset($Row->status) ? $Row->status : ''; ?>"/>
                                        </td>
                                        <td>
                                            <input type="text" readonly="" class="form-control last_paid_date" name="br_last_paid_date[]" id="last_paid_date_<?php echo $i; ?>" value="<?php echo isset($Row->last_paid_date) && $Row->last_paid_date != '' ? DB2Disp($Row->last_paid_date) : ''; ?>"/>
                                        </td>
                                        <td>
                                            <input type="text" readonly class="form-control last_paid_amount" name="last_paid_amount[]" id="last_paid_amount_<?php echo $i; ?>" value="<?php echo isset($Row->last_paid_amount) ? $Row->last_paid_amount : ''; ?>"/>
                                        </td>
                                        <td><?php $paid_status = isset($Row->is_paid) && $Row->is_paid == 1 ? "Unpaid" : 'Paid'; ?>
                                            <button type="button" onclick="set_paid_val(this, 'BR');" class="btn btn-success btn-sm"><?php echo $paid_status; ?></button>
                                            <input type="hidden" class="is_paid" name="br_is_paid[]" id="is_paid_<?php echo $i; ?>" value="<?php echo isset($Row->is_paid) ? $Row->is_paid : ''; ?>"/>
                                        </td>
                                        <td  style="padding: 6px 0 !important">
                                            <input type="hidden" class="is_attachment" name="br_hidden_attachment[<?php echo $i;?>][]" id="br_hidden_attachment_<?php echo $i; ?>" value="<?php echo isset($Row->attachment) ? $Row->attachment : ''; ?>"/>
                                            <a class="btn-icon-only attachment-icon" href="javascript:;">
                                                <i class="fa fa-paperclip fa-2x" aria-hidden="true"></i>
                                                <input type="file"  multiple name="br_attachment[<?php echo $i;?>][]" id="attachment_<?php echo $i; ?>"/>
                                            </a>
                                            <!-- Display Attachment -->
                                            <?php if(isset($Row->attachment) && $Row->attachment != ''): ?>
                                            <a onclick="openBillItemAttachment(<?php echo $Row->id; ?>)">View</a>
                                            <div class="modal fade" id="attachment_modal_<?php echo $Row->id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <div class="col-md-10">
                                                                <h3 class="modal-title"><b>Attachments</b></h3>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <?php
                                                                    if(isset($Row->attachment) && $Row->attachment != '')
                                                                    {
                                                                        $attachment = explode(",", $Row->attachment);
                                                                        foreach ($attachment as $_attachment) {
                                                                            $extension  = pathinfo($_attachment,PATHINFO_EXTENSION);
                                                                            ?>
                                                                            <a href='<?php echo base_url()."files_upload/bill_attachment/$Row->bill_id/$_attachment"; ?>' class="<?php echo strtolower($extension).'-icon' ; ?>" target="_blank" download><?php echo $_attachment; ?></a>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <a class="btn-icon-only font-red" name="delete_btn[]" onclick="delete_row(this);"><i class="fa fa-trash fa-2x"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                    $i++;
                                }
                            }
                            ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="9" align="right">Total</td>
                                <td id="br_item_total" align="right"><?php echo isset($expenses_item_bal) && $expenses_item_bal != '' ? sprintf("%.2f", $expenses_item_bal) : '0'; ?></td>
                                <td colspan="6"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <button type="button" class="btn btn-sm btn-success" name="btnAddLines" id="btnAddLines" onclick="add_br_item_lines(this);">Add Lines</button>
                <button type="button" class="btn btn-sm btn-danger" name="btnRemoveLines" id="btnRemoveLines" onclick="remove_item_lines(this);">Remove Lines</button>
            </div>
        </div>

                <div class="portlet green box">
            <div class="portlet-title">
                <div class="caption">
                    <i class="fa fa-cogs"></i>Bill Check Entry
                </div>
                <div class="tools">
                    <a href="javascript:;" class="expand" data-original-title="" title="">
                    </a>
                </div>
            </div>
            <div class="portlet-body ">
                <div id="check_error_div_item"  style="display: none;">
                    <div class="alert alert-danger">
                        <button class="close" data-close="alert"></button>
                        <strong>Error!  </strong>
                        <div class="item_error_msg">
                        </div>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover table-bordered" id="check_desc_tbl">
                        <thead>
                            <tr>
                                <th>Store Key</th>
                                <th>Payable</th>
                                <th>Check No</th>
                                <th>Memo</th>
                                <th>Amount</th>
                                <th>Check Date</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <?php
                                    $options = array();
                                    $options[''] = '-- Select --';
                                    foreach ($store['records'] as $row) {
                                        $options[$row->key] = $row->key;
                                    }
                                    echo form_dropdown(array('name' => 'bc_store_key[]', 'options' => $options, 'class' => 'form-control bc_store_cls', 'onchange' => 'get_check_no(this);'));
                                    ?>
                                </td>

                                <td>
                                    <input type="text" name="bc_payable[]" class="form-control bc_payable" />
                                </td>
                                <td>
                                    <input type="text" name="bc_check_no[]" class="form-control bc_check_no" />
                                </td>
                                <td>
                                    <input type="text" name="bc_memo[]" class="form-control bc_memo" />
                                </td>
                                <td>
                                    <input type="text" name="bc_amount[]" class="form-control bc_amount" />
                                </td>
                                <td>
                                    <input type="text" name="bc_check_date[]" class="form-control bc_datepicker" />
                                </td>
                                <td  style="padding: 6px 0 !important">
                                    <a class="btn-icon-only attachment-icon" href="javascript:;">
                                        <i class="fa fa-paperclip fa-2x" aria-hidden="true"></i>
                                        <input type="file" name="bc_attachment[0][]" class="form-control" multiple=""/>
                                    </a>
                                </td>
                                <td>
                                </td>
                            </tr>
                            <?php
                            if (isset($bill_item_check_res['records']) && !empty($bill_item_check_res['records']) && count($bill_item_check_res['records']) > 0) {

                                $i = 1;
                                foreach ($bill_item_check_res['records'] as $Row) {

                                    ?>
                                    <tr id="ctr_<?php echo $i; ?>">

                                        <td>
                                            <?php
                                            $store_key = field(set_value('name', NULL), (isset($Row->bc_store_key)) ? $Row->bc_store_key : '');
                                            $options = array();
                                            $options[''] = '-- Select --';
                                            foreach ($store['records'] as $row) {
                                                $options[$row->key] = $row->key;
                                            }
                                            echo form_dropdown(array('name' => 'bc_store_key[]', 'options' => $options, 'class' => 'form-control bc_store_cls', 'selected' => $store_key, 'onchange' => 'get_check_no(this);'));
                                            ?>
                                        </td>
                                        <td>
                                            <input type="text" name="bc_payable[]" class="form-control bc_payable" value="<?php echo (isset($Row->bc_payable)) ? $Row->bc_payable : ''; ?>"/>
                                        </td>
                                        <td>
                                            <input type="text" name="bc_check_no[]" class="form-control bc_check_no" value="<?php echo (isset($Row->bc_check_no)) ? $Row->bc_check_no : ''; ?>"/>
                                        </td>
                                        <td>
                                            <input type="text" name="bc_memo[]" class="form-control bc_memo" value="<?php echo (isset($Row->bc_memo)) ? $Row->bc_memo : ''; ?>"/>
                                        </td>
                                        <td>
                                            <input type="text" name="bc_amount[]" class="form-control bc_amount" value="<?php echo (isset($Row->bc_amount)) ? $Row->bc_amount : ''; ?>"/>
                                        </td>
                                        <td>
                                            <input type="text" name="bc_check_date[]" class="form-control bc_datepicker" value="<?php echo (isset($Row->bc_check_date)) && $Row->bc_check_date != '' ? DB2Disp($Row->bc_check_date) : ''; ?>"/>
                                        </td>
                                        <td style="padding: 6px 0 !important;">
                                            <input type="hidden" class="is_attachment" name="bc_hidden_attachment[<?php echo $i;?>][]" id="bc_hidden_attachment_<?php echo $i; ?>" value="<?php echo isset($Row->bc_attachment) ? $Row->bc_attachment : ''; ?>"/>
                                            <a class="btn-icon-only attachment-icon" href="javascript:;">
                                                <i class="fa fa-paperclip fa-2x" aria-hidden="true"></i>
                                                <input type="file"  multiple name="bc_attachment[<?php echo $i;?>][]" id="attachment_<?php echo $i; ?>"/>
                                            </a>
                                            <!-- Display Attachment -->
                                            <?php if(isset($Row->bc_attachment) && $Row->bc_attachment != ''): ?>
                                            <a onclick="openBillItemAttachment(<?php echo $Row->bc_id; ?>)">View</a>
                                            <div class="modal fade" id="attachment_modal_<?php echo $Row->bc_id; ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                                <div class="modal-dialog modal-lg" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <div class="col-md-10">
                                                                <h3 class="modal-title"><b>Attachments</b></h3>
                                                            </div>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    <?php
                                                                    if(isset($Row->bc_attachment) && $Row->bc_attachment != '')
                                                                    {
                                                                        $attachment = explode(",", $Row->bc_attachment);
                                                                        foreach ($attachment as $_attachment) {
                                                                            $extension  = pathinfo($_attachment,PATHINFO_EXTENSION);
                                                                            ?>
                                                                            <a href='<?php echo base_url()."files_upload/bill_attachment/$Row->bill_id/check/$_attachment"; ?>' class="<?php echo strtolower($extension)."-icon" ; ?>" target="_blank" download><?php echo $_attachment; ?></a>
                                                                            <?php
                                                                        }
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php endif; ?>

                                        </td>
                                        <td>
                                            <a class="btn-icon-only font-red" name="delete_btn[]" onclick="delete_row(this);"><i class="fa fa-trash fa-2x"></i></a>
                                        </td>
                                    </tr>
                                    <?php
                                    $i++;
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <button type="button" class="btn btn-sm btn-success" name="btnCheckRemoveLines" id="btnCheckRemoveLines" onclick="add_check_item_lines(this);">Add Lines</button>
                <button type="button" class="btn btn-sm btn-danger" name="btnCheckRemoveLines" id="btnCheckRemoveLines" onclick="remove_item_lines(this);">Remove Lines</button>
            </div>
                </div>
    </div>
</div>
<div class="form-group">
    <div class="col-xs-6">
    </div>
</div>
<div class="modal fade" id="add_attachment_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Add Attachment</h4>
            </div>
            <div class="modal-body">
            </div>
        </div>
    </div>
</div>
<?php echo form_close(); ?>

<!-- Royalty section start -->
<hr>
<div id="bill_royal_data"></div>
<script src="<?php echo base_url(); ?>assets/js/royalty.js" type="text/javascript"></script>
<script type="text/javascript">
let roy = new Royalty();
//load royal data
$.ajax({
    type: "POST",
    url: site_url + 'royal/royal',
    success: function (responsehtml) {
        $("#bill_royal_data").html(responsehtml);
    }
});
</script>
<!-- Royalty section end -->

<!-- Donut section start -->
<hr>
<div id="bill_donut_data"></div>
<script src="<?php echo base_url(); ?>assets/js/donut.js" type="text/javascript"></script>
<script type="text/javascript">
let donut = new Donut();
//load royal data
$.ajax({
    type: "POST",
    url: site_url + 'donut/donut',
    success: function (responsehtml) {
        $("#bill_donut_data").html(responsehtml);
    }
});
</script>
<!-- Donut section end -->

<script>
$(".attachment-icon i").click(function () {
    $(this).parent('.attachment-icon').find("input[type='file']").trigger('click');
});


            $(document).on('keydown', '.integer', function (e) {
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
            $(document).ready(function () {
                $(".lock_datepicker").datepicker({
                    'format': 'mm/yyyy'
                });
                $(".bill_date,.bc_datepicker").datepicker({
                    'format': 'mm/dd/yyyy'
                });
            });

            var Validator = $("#frm_hgeneral").validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-block help-block-error', // default input error message class
                highlight: function (element) { // hightlight error inputs
                    $(element)
                            .closest('.form-group').addClass('has-error'); // set error class to the control group
                },
                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                            .closest('.form-group').removeClass('has-error'); // set error class to the control group
                },
                errorPlacement: function (error, element) { // render error placement for each input type
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            });

            var date = new Date();
            var today = new Date(date.getFullYear(), date.getMonth(), date.getDate());

            $('#frmAddBillEntry').submit(function (event) {

                var product_error = 0;
                var br_product_error = 0;
                var bill_item = [];
                var br_bill_item = [];
                jQuery('#desc_tbl > tbody  > tr').each(function () {
                    var row = $(this);
                    var store = row.find('.store_cls').val();
                    var address = row.find('.store_physical_address').val();
                    var bill_date = row.find('.bill_date').val();
                    var bill_no = row.find('.bill_no').val();
                    var category = row.find('.category_cls').val();
                    var description = row.find('.description_cls').val();
                    var qty = row.find('.qty').val();
                    var rate = row.find('.rate').val();
                    var amount = row.find('.amount').val();

                    var tr_id = row.attr("id");
                    var tr_id_arr = tr_id.split("_");
                    var tr_index = tr_id_arr[1];
                    if (store != "" || address != "" || bill_date != "" || bill_no != "" || category != "" || description != '' || qty != "" || rate != '' || amount != '') {
                        bill_item.push({
                            'store': store,
                            'address': address,
                            'bill_date': bill_date,
                            'bill_no': bill_no,
                            'category': category,
                            'description': description,
                            'qty': qty,
                            'rate': rate,
                            'amount': amount,
                            'row_no': tr_index
                        });
                    }
                });
                if (bill_item.length > 0) {

                    $("#error_div_item").hide();
                    $("#error_div_item").find(".item_error_msg").html("");
                    for (var i = 0; i < bill_item.length; i++) {
                        var row_index = parseInt(bill_item[i]['row_no']) + 1;

                        if (bill_item[i]['store'] == "") {
                            $("#error_div_item").show();
                            $("#error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please select the store in row " + row_index + ".</p>");
                            jQuery("#error_div_item").animate({scrollTop: 0}, "fast");
                            product_error = 1;
                        }
                        if (bill_item[i]['address'] == "") {
                            $("#error_div_item").show();
                            $("#error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please enter the address in row " + row_index + ".</p>");
                            jQuery("#error_div_item").animate({scrollTop: 0}, "fast");
                            product_error = 1;
                        }
                        if (bill_item[i]['bill_date'] == "") {
                            $("#error_div_item").show();
                            $("#error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please enter the bill date in row ." + row_index + "</p>");
                            jQuery("#error_div_item").animate({scrollTop: 0}, "fast");
                            product_error = 1;
                        }
                        if (bill_item[i]['bill_no'] == "") {
                            $("#error_div_item").show();
                            $("#error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please enter the bill no in row. " + row_index + "</p>");
                            jQuery("#error_div_item").animate({scrollTop: 0}, "fast");
                            product_error = 1;
                        }
                        if (bill_item[i]['category'] == "") {
                            $("#error_div_item").show();
                            $("#error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please select the category in row. " + row_index + "</p>");
                            jQuery("#error_div_item").animate({scrollTop: 0}, "fast");
                            product_error = 1;
                        }
                        if (bill_item[i]['description'] == "") {
                            $("#error_div_item").show();
                            $("#error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please select the description in row. " + row_index + "</p>");
                            jQuery("#error_div_item").animate({scrollTop: 0}, "fast");
                            product_error = 1;
                        }
                        if (bill_item[i]['qty'] == "") {
                            $("#error_div_item").show();
                            $("#error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please enter the qty in row. " + row_index + "</p>");
                            jQuery("#error_div_item").animate({scrollTop: 0}, "fast");
                            product_error = 1;
                        }
                        if (bill_item[i]['rate'] == "") {
                            $("#error_div_item").show();
                            $("#error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please enter the rate in row. " + row_index + "</p>");
                            jQuery("#error_div_item").animate({scrollTop: 0}, "fast");
                            product_error = 1;
                        }
                        if (bill_item[i]['amount'] == "") {
                            $("#error_div_item").show();
                            $("#error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please enter the amount in row. " + row_index + "</p>");
                            jQuery("#error_div_item").animate({scrollTop: 0}, "fast");
                            product_error = 1;
                        }
                    }
                }
                jQuery('#br_desc_tbl > tbody  > tr').each(function () {
                    var row = $(this);
                    var store = row.find('.store_cls').val();
                    var address = row.find('.store_physical_address').val();
                    var bill_date = row.find('.bill_date').val();
                    var bill_no = row.find('.bill_no').val();
                    var category = row.find('.category_cls').val();
                    var description = row.find('.description_cls').val();
                    var br_description = row.find('.br_description_cls').val();
                    var qty = row.find('.qty').val();
                    var rate = row.find('.rate').val();
                    var amount = row.find('.amount').val();

                    var tr_id = row.attr("id");
                    var tr_id_arr = tr_id.split("_");
                    var tr_index = tr_id_arr[1];
                    if (store != "" || address != "" || bill_date != "" || bill_no != "" || category != "" || description != '' || qty != "" || rate != '' || amount != '' || br_description != '') {
                        br_bill_item.push({
                            'store': store,
                            'address': address,
                            'bill_date': bill_date,
                            'bill_no': bill_no,
                            'category': category,
                            'description': description,
                            'br_description': br_description,
                            'qty': qty,
                            'rate': rate,
                            'amount': amount,
                            'row_no': tr_index
                        });
                    }
                });
                if (br_bill_item.length > 0) {

                    $("#br_error_div_item").hide();
                    $("#br_error_div_item").find(".item_error_msg").html("");
                    for (var i = 0; i < br_bill_item.length; i++) {
                        var row_index = parseInt(br_bill_item[i]['row_no']) + 1;

                        if (br_bill_item[i]['store'] == "") {
                            $("#br_error_div_item").show();
                            $("#br_error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please select the store in row " + row_index + ".</p>");
                            jQuery("#br_error_div_item").animate({scrollTop: 0}, "fast");
                            br_product_error = 1;
                        }
                        if (br_bill_item[i]['address'] == "") {
                            $("#br_error_div_item").show();
                            $("#br_error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please enter the address in row " + row_index + ".</p>");
                            jQuery("#br_error_div_item").animate({scrollTop: 0}, "fast");
                            br_product_error = 1;
                        }
                        if (br_bill_item[i]['bill_date'] == "") {
                            $("#br_error_div_item").show();
                            $("#br_error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please enter the bill date in row ." + row_index + "</p>");
                            jQuery("#br_error_div_item").animate({scrollTop: 0}, "fast");
                            br_product_error = 1;
                        }
                        if (br_bill_item[i]['bill_no'] == "") {
                            $("#br_error_div_item").show();
                            $("#br_error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please enter the bill no in row. " + row_index + "</p>");
                            jQuery("#br_error_div_item").animate({scrollTop: 0}, "fast");
                            br_product_error = 1;
                        }
                        if (br_bill_item[i]['category'] == "") {
                            $("#br_error_div_item").show();
                            $("#br_error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please select the category in row. " + row_index + "</p>");
                            jQuery("#br_error_div_item").animate({scrollTop: 0}, "fast");
                            br_product_error = 1;
                        }
                        if (br_bill_item[i]['description'] == "") {
                            $("#br_error_div_item").show();
                            $("#br_error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please select the description in row. " + row_index + "</p>");
                            jQuery("#br_error_div_item").animate({scrollTop: 0}, "fast");
                            br_product_error = 1;
                        }
                        if (br_bill_item[i]['qty'] == "") {
                            $("#br_error_div_item").show();
                            $("#br_error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please enter the qty in row. " + row_index + "</p>");
                            jQuery("#br_error_div_item").animate({scrollTop: 0}, "fast");
                            br_product_error = 1;
                        }
                        if (bill_item[i]['rate'] == "") {
                            $("#br_error_div_item").show();
                            $("#br_error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please enter the rate in row. " + row_index + "</p>");
                            jQuery("#br_error_div_item").animate({scrollTop: 0}, "fast");
                            br_product_error = 1;
                        }
                        if (bill_item[i]['amount'] == "") {
                            $("#br_error_div_item").show();
                            $("#br_error_div_item").find(".item_error_msg").append("<p style=\"margin:0px;\">Something is not right! Please enter the amount in row. " + row_index + "</p>");
                            jQuery("#br_error_div_item").animate({scrollTop: 0}, "fast");
                            br_product_error = 1;
                        }
                    }
                }
                if (product_error == 0 && br_product_error == 0) {
                    $('#frmAddBillEntry').submit();
                } else {
                    return false;
                }

//        $.ajax({
//            type: "POST",
//            url: site_url + "vendors/bill_entry_save",
//            data: $('#frmAddBillEntry').serialize() + '&' + RecurringTxns_Data,
//            async: "false",
//            beforeSend: function () {
//                $("#loadingmessage").show();
//            },
//            success: function (responseJSON) {
//                var response = JSON.parse(responseJSON);
//                $("#loadingmessage").hide();
//                if ($.trim(response.status) == 'success') {
//                    location.reload();
//                    window.location.href = site_url + "expenses/bill";
//                }
//            }
//
//        });
            });
            $("#confirmModal .confirmYes").click(function () {
                var id = $(this).attr("data-id");
                if (id == "") {
                    var value = $("#confirmModal #action").val();
                    if (value == "clear") {
                        location.reload();
                    } else {
                        window.location.href = site_url + "expenses/";
                    }

                } else {
                    var id = $(this).attr("data-id");
                    $.ajax({
                        type: "POST",
                        url: site_url + 'expenses/delete_expenses/' + id,
                        data: $('#frmAddExpenseEntry').serialize(),
                        async: "false",
                        beforeSend: function () {
                            $("#loadingmessage").show();
                        },
                        success: function (responseJSON) {
                            var response = JSON.parse(responseJSON);
                            $("#loadingmessage").hide();
                            $("#confirmModal").modal('hide');
                            if ($.trim(response.status) == 'success') {
                                $('#adjust_entries_model').find('.journal_date').val($('#journal_date').val());
                                $('#adjust_entries_model').modal('show');
                            }
                        }
                    });
                }
            });
//    function frm_save() {
//        if ($("#btn_type").length > 0) {
//            $("#btn_type").val("save");
//        } else {
//            $("#frmAddBillEntry").append("<input type='hidden' name='btn_type' id='btn_type' value='save'>");
//        }
//        $('#frmAddBillEntry').submit();
//    }
//    function frm_save_new() {
//        if ($("#btn_type").length > 0) {
//            $("#btn_type").val("save_new");
//        } else {
//            $("#frmAddBillEntry").append("<input type='hidden' name='btn_type' id='btn_type' value='save_new'>");
//        }
//        $('#frmAddBillEntry').submit();
//    }
            function setDeleteConfirm(value, title, del_entity) {
                var delete_id = $(value).attr("data-id");
                $("#confirmModal").find(".modal-title").html(title + " Deletion");
                $("#confirmModal").find(".modal-body").children("p").html("Do you really want to delete this " + del_entity + "?");
                $("#confirmModal").find(".confirmYes").attr('data-id', delete_id);
            }
            function set_confiramtion(type) {
                $("#confirmModal").find(".confirmYes").attr('data-id', '');
                $("#confirmModal").find(".modal-body").append("<input type=hidden name='action' id='action' value='cancel'>");
                $("#confirmModal").find(".modal-body").children("p").html("Do you want to leave without saving?");
                $("#confirmModal").find(".modal-header").children("h4").html("Cancel Transaction");
                $("#confirmModal").modal("show");
            }

            function get_desc(element, type) {
                var value = $(element).val();
                is_week_desc = 0;

                if (value != '') {
                    $.ajax({
                        type: "POST",
                        url: site_url + 'category/get_category_desc',
                        data: {category: value, type: type},
                        beforeSend: function () {
                            $("#loadingmessage").show();
                        },
                        success: function (responseJSON) {
                            var response = JSON.parse(responseJSON);
                            $("#loadingmessage").hide();
                            $("#confirmModal").modal('hide');
                            var ledger_date = $("#ledger_bill_date").val();
                            if ($.trim(response.status) == 'success') {
                                option_html = '<option>--Select Description--</option>';
                                $(response.res).each(function (i) {
                                    if (response.res[i].type == 'week_description') {
                                        is_week_desc = 1;
                                        if(response.res[i].is_display_calender == '1'){
                                            var datepicker_html = '<input type="text" name="description[]" class="form-control description_cls desc_datepicker"/>';
                                               $(element).closest("tr").find(".td_description_cls").html(datepicker_html);
                                               $(".desc_datepicker").datepicker();
                                        }else {
                                        $.ajax({
                                            type: "POST",
                                            url: site_url + 'category/get_week_dates',
                                            data: {date: ledger_date, is_display_last_date : response.res[i].is_display_last_week_ending_date_as_last_date_of_month},
                                            beforeSend: function () {
                                                $("#loadingmessage").show();
                                            },
                                            success: function (responseJSON) {
                                                $("#loadingmessage").hide();
                                                var Result = JSON.parse(responseJSON);
                                                var option_html = "<select name='description[]' class='form-control description_cls' onchange='prev_data(this);'><option>--Select Description--</option>";
                                                if(Result.status) {
                                                    $(Result.dates).each(function (i) {
                                                        option_html += '<option value=' + Result.dates[i] + '>' + Result.dates[i] + '</option>';
                                                    });
                                                    $(element).closest("tr").find(".td_description_cls").html(option_html);
                                                    $(element).closest("tr").find("[name='bill_type[]']").val("week_description");
                                                }else {
                                                    alert("Please set the year setting of the selected year");
                                                    return false;
                                                }
                                            }
                                        });
                                    }

                                    } else {
                                        console.log("sss:" + is_week_desc);
                                        if (is_week_desc == 0) {
                                            option_html += '<option value=' + response.res[i].id + '>' + response.res[i].description + '</option>';
                                        }
                                    }
                                });

                                if (type == 'BD') {
                                    $(element).closest("tr").find("[name='br_description[]']").html(option_html);
                                } else {
                                    $(element).closest("tr").find("[name='description[]']").html(option_html);
                                }

                            }
                        }
                    });
                }
            }
            function get_breakdown_description(element) {
                var value = $(element).val();
                if (value != '') {
                    $.ajax({
                        type: "POST",
                        url: site_url + 'category/get_category_breakdown_desc',
                        data: {id: value},
                        beforeSend: function () {
                            $("#loadingmessage").show();
                        },
                        success: function (responseJSON) {
                            var response = JSON.parse(responseJSON);
                            $("#loadingmessage").hide();
                            $("#confirmModal").modal('hide');
                            if ($.trim(response.status) == 'success') {
                                option_html = '<option>--Select Breakdown Description--</option>';
                                $(response.res).each(function (i) {
                                    option_html += '<option value=' + response.res[i].description + '>' + response.res[i].description + '</option>';
                                });
                                $(element).closest("tr").find("[name='br_breakdown_description[]']").html(option_html);
                            }
                        }
                    });
                }
            }
            function set_store(element, type) {
                var value = $(element).val();
                if (value != '') {
                    $.ajax({
                        type: "POST",
                        url: site_url + 'store/get_address',
                        data: {id: value, type: type},
                        beforeSend: function () {
                            $("#loadingmessage").show();
                        },
                        success: function (responseJSON) {
                            var response = JSON.parse(responseJSON);
                            $("#loadingmessage").hide();
                            $("#confirmModal").modal('hide');
                            if ($.trim(response.status) == 'success') {
                                if (type == 'BD') {
                                    $(element).closest("tr").find("[name='br_store_physical_address[]']").val(response.address);
                                } else {
                                    $(element).closest("tr").find("[name='store_physical_address[]']").val(response.address);
                                }

                            }
                        }
                    });
                }
            }

            function qty(element, type) {

                if (type == 'BD') {
                    input = $("input[name='br_rate[]']");
                } else {
                    input = "input[name='rate[]']";
                }
                var rate = $(element).parents("tr").find(input).val();
                var qty = $(element).val();
                var tot_amount = parseFloat(rate * qty).toFixed(2);
                if (type == 'BD') {
                    amt_element = $("input[name='br_amount[]']");
                } else {
                    amt_element = $("input[name='amount[]']");
                }
                $(element).parents("tr").find(amt_element).val(tot_amount);
                tot_sum = 0;
                $(amt_element).each(function () {
                    var value = $(this).val();
                    if ($.trim(value) != '' && !isNaN($.trim(value))) {
                        tot_sum += parseFloat(value);
                    }
                });
                if (type == 'BD') {
                    $("#br_item_total").html(tot_sum.toFixed(2));
                } else {
                    $("#item_total").html(tot_sum.toFixed(2));
                }
            }
            function rate(element, type) {
                if (type == 'BD') {
                    qty_element = $("input[name='br_qty[]']");
                    amt_element = $("input[name='br_amount[]']");
                } else {
                    qty_element = $("input[name='qty[]']");
                    amt_element = $("input[name='amount[]']");
                }

                var qty = $(element).parents("tr").find(qty_element).val();
                var rate = $(element).val();
                var tot_amount = parseFloat(rate * qty).toFixed(2);
                $(element).parents("tr").find(amt_element).val(tot_amount);
                tot_sum = 0;
                $(amt_element).each(function () {
                    var value = $(this).val();
                    if ($.trim(value) != '' && !isNaN($.trim(value))) {
                        tot_sum += parseFloat(value);
                    }
                });
                if (type == 'BD') {
                    $("#br_item_total").html(tot_sum.toFixed(2));
                } else {
                    $("#item_total").html(tot_sum.toFixed(2));
                }
            }

            function add_item_lines() {
                var last_index =    $("#desc_tbl tbody tr:last").index() + 1;
                $("#desc_tbl tbody tr:first")
                        .clone()
                        .find("input, textarea,select").val("").end() // ***
                        .show()
                        .insertAfter("#desc_tbl tbody tr:last");
                $("#desc_tbl tbody tr:last").attr("id", "itr_"+last_index);
                $("#desc_tbl tbody tr:last").find(":file").attr("name", "attachment["+last_index+"][]");
                $(".bill_date").datepicker();
                var delete_html = '<a class="btn-icon-only font-red" name="delete_btn[]" onclick="delete_row(this);"><i class="fa fa-trash fa-2x"></i></a>';
                $("#desc_tbl tbody tr:last td:last").html(delete_html);
            }
            function add_br_item_lines() {
                var last_index = $("#br_desc_tbl tbody tr:last").index() + 1;
                $("#br_desc_tbl tbody tr:first")
                        .clone()
                        .find("input, textarea,select").val("").end() // ***
                        .show()
                        .insertAfter("#br_desc_tbl tbody tr:last");
                $(".bill_date").datepicker();
                $("#br_desc_tbl tbody tr:last").attr("id", "itr_"+last_index);
                $("#br_desc_tbl tbody tr:last").find(":file").attr("name", "br_attachment["+last_index+"][]");
                var delete_html = '<a class="btn-icon-only font-red" name="delete_btn[]" onclick="delete_row(this);"><i class="fa fa-trash fa-2x"></i></a>';
                $("#br_desc_tbl tbody tr:last td:last").html(delete_html);
            }
            function add_check_item_lines() {
                var last_index = $("#check_desc_tbl tbody tr:last").index() + 1;
                $("#check_desc_tbl tbody tr:first")
                        .clone()
                        .find("input, textarea,select").val("").end() // ***
                        .show()
                        .insertAfter("#check_desc_tbl tbody tr:last");
                $(".bc_datepicker").datepicker();
                $("#check_desc_tbl tbody tr:last").attr("id", "itr_"+last_index);
                $("#check_desc_tbl tbody tr:last").find(":file").attr("name", "bc_attachment["+last_index+"][]");
                var delete_html = '<a class="btn-icon-only font-red" name="delete_btn[]" onclick="delete_row(this);"><i class="fa fa-trash fa-2x"></i></a>';
                $("#check_desc_tbl tbody tr:last td:last").html(delete_html);
            }
            function delete_row(element) {
                $(element).parents("tr").remove();
            }

            //get prev last paid amt / date
            function prev_data(element) {
                var store = $(element).parents("tr").find("select[name='store[]']").val();
                var category = $(element).parents("tr").find("select[name='category[]']").val();
                var description = $(element).parents("tr").find("select[name='description[]']").val();
                if (store != '' && category != '' && description != '') {
                    $.ajax({
                        type: "POST",
                        url: site_url + 'bill/get_prev_data',
                        data: {store: store, category: category, description: description},
                        success: function (responsehtml) {
                            var response = JSON.parse(responsehtml);
                            var amt = response.amount;
                            var billDate = response.bill_date;
                            $(element).parents("tr").find("[name='last_paid_date[]']").val(billDate);
                            $(element).parents("tr").find("[name='last_paid_amount[]']").val(amt);
                        }
                    });
                }
            }
            function breakdown_desc_prev_data(element) {
                var store = $(element).parents("tr").find("select[name='br_store[]']").val();
                var category = $(element).parents("tr").find("select[name='br_category[]']").val();
                var description = $(element).parents("tr").find("select[name='br_description[]']").val();
                var br_breakdown_description = $(element).parents("tr").find("select[name='br_breakdown_description[]']").val();
                if (store != '' && category != '' && description != '') {
                    $.ajax({
                        type: "POST",
                        url: site_url + 'bill/get_br_prev_data',
                        data: {store: store, category: category, description: description, br_breakdown_description: br_breakdown_description},
                        success: function (responsehtml) {
                            var response = JSON.parse(responsehtml);
                            var amt = response.amount;
                            var billDate = response.bill_date;
                            $(element).parents("tr").find("[name='last_paid_date[]']").val(billDate);
                            $(element).parents("tr").find("[name='last_paid_amount[]']").val(amt);
                        }
                    });
                }
            }
            function set_paid_val(element, type) {
                var bill_id = $(element).parents("tr").find("[name='bill_id[]']").val();
                var value = $(element).html();
                if (value == 'Paid') {
                    var status = 'Unpaid';
                } else {
                    var status = 'Paid';
                }
                if (bill_id > 0) {

                    $.ajax({
                        type: "POST",
                        url: site_url + 'bill/update_status',
                        data: {bill_id: bill_id, status: status},
                        success: function (responsehtml) {


                        }
                    });
                } else {
                    if (type == 'D') {
                        $(element).parents("tr").find("[name='is_paid[]']").val(status);
                    } else {
                        $(element).parents("tr").find("[name='br_is_paid[]']").val(status);
                    }
                }
                $(element).html(status);
                $(element).parents("tr").find("[name='status[]']").val(value);
            }
             function display_attachment(element) {
                 var tr_index = $(element).parents("tr").index();
                   $("#add_attachment_modal").modal("show");
             }
             function get_check_no(element){
                 var value = $(element).val();
                 if(value != ''){
                      $.ajax({
                        type: "POST",
                        url: site_url + 'bill/get_checkno_data',
                        data: {store: value},
                        success: function (responsehtml) {
                            var response = JSON.parse(responsehtml);
                            if(response.status == 'success'){
                                $(element).parents("tr").find("input[name='bc_check_no[]']").val(response.check_no);
                            }
                        }
                    });
                 }
             }

//attachment code
function openBillItemAttachment(billItemId)
{
    $('#attachment_modal_'+billItemId).modal({
        backdrop: 'static',
        keyboard: true,
        show: true
    });
}
</script>