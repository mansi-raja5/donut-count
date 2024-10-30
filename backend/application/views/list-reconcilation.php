<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$min_year = 2015;
$max_year = 2025;
?>
<!-- BEGIN EXAMPLE TABLE PORTLET-->
<?php if ($err = $this->session->flashdata('display-message')): ?>
<div id="error_div" class="alert alert-danger">
    <button class="close" data-close="alert"></button>
    <strong>Error! </strong>
    <p style="display: inline;">
        <?php echo $this->session->flashdata('display-message'); ?>
    </p>
</div>
<?php endif; ?>

<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body">
        <?php
        $attributes = array('name' => 'frmSearch', 'id' => 'frmSearch', 'method' => 'get');
        echo form_open('', $attributes);
        ?>
        <div class="row">
            <div class="col-md-3 mb10">
                <?php
                $month_arr = array("January", "February", "March", "April", "May", "Jun", "July", "August", "September", "October", "November", "December");
                $options = array();
                $options[''] = '-- Month  --';
                $cid = field(set_value('month', NULL), $this->input->get('month'));
                foreach ($month_arr as $key => $value) {
                    $options[$key + 1] = $value;
                }

                echo form_dropdown(array('id' => 'month', 'name' => 'month', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $cid));
                ?>
            </div>
            <div class="col-md-3 mb10">
                <?php
                $options = array();
                $options[''] = '-- Year  --';
                $year = field(set_value('year', NULL), $this->input->get('year'));
                for ($i = $min_year; $i <= $max_year; $i++) {
                    $options[$i] = $i;
                }
                echo form_dropdown(array('id' => 'year', 'name' => 'year', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $year));
                ?>
            </div>
            <div class="col-md-3 mb10">
                <?php
                $options = array();
                $options[''] = '-- Select Store  --';
                $store_id = field(set_value('store_id', NULL), $this->input->get('store_id'));
                if (isset($store_list['records']) && !empty($store_list['records'])) {
                    foreach ($store_list['records'] as $row) {
                        $options[$row->key] = $row->name . " (" . $row->key . ")";
                    }
                }
                echo form_dropdown(array('id' => 'store_id', 'name' => 'store_id', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $store_id));
                ?>
            </div>
        </div>
        <?php echo form_close(); ?>

        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover"  id="reconcile_tblListing">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>store</th>
                            <th>Month</th>
                            <th>Year</th>
                            <th>Ledger <br>Statement</th>
                            <th>Bank <br>Statement</th>
                            <th>Auto <br>Ledger</th>
                            <th>Ledger Status</th>
                            <th>Bank Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($records) && !empty($records)) {
                            $i = 1;
                            $storeKeyDisplay = 0;
                            foreach ($records as $row) {
                                $types_arr  = array();
                                $store_key  = $row['store_key'];
                                $month      = monthName($row['monthnumber']);
                                $monthnumber= $row['monthnumber'];
                                $year       = $row['year'];
                                $is_locked  = $row['is_locked'];

                                $ledger_id  = $row['ledger_id'];
                                $bank_id    = $row['bank_id'];

                                $disp_status = $row['status'] ? getStatusLabel($row['status']) : '';
                                $bank_status = isset($row['bank_status']) && $row['bank_status'] ? getStatusLabel($row['bank_status']) : '';

                                if($storeKeyDisplay != $store_key)
                                {
                                    $max_year  = $year;
                                    $max_month = $monthnumber;
                                }
                                ?>
                                <tr>
                                    <td><?php echo $i; ?></td>
                                    <td><?php echo $store_key; ?></td>
                                    <td><?php echo $month; ?></td>
                                    <td><?php echo $year; ?></td>
                                    <td>
                                        <?php
                                        if($ledger_id != '' && $ledger_id > 0)
                                        {
                                            ?>
                                            <a target='_blank' href='<?php echo base_url("statement/view/{$ledger_id}"); ?>'>
                                                <?php
                                                echo (isset($row['isautoledger']) && $row['isautoledger']) ? "<span class='badge badge-success'>Auto</span>" : "<span class='badge badge-info'>Imported</span>";
                                                ?>
                                            </a>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <span class='badge badge-danger'>No</span>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($bank_id != '' && $bank_id > 0)
                                        {
                                            ?>
                                            <a target='_blank' href='<?php echo base_url("bank/view/{$bank_id}"); ?>'>
                                                <span class='badge badge-success'>Yes</label>
                                            </a>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <span class='badge badge-danger'>No</span>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php

                                        if(isset($row['posid']) && $row['posid'] && isset($row['payrollid']) && $row['payrollid'])
                                        {
                                            ?>
                                            <a target='_blank' href='<?php echo base_url("statement/auto/{$store_key}/{$monthnumber}/{$year}"); ?>'><span class='badge badge-success'>Yes</label></a>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <span class='badge badge-danger'>No</span>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $disp_status; ?></td>
                                    <td><?php echo $bank_status; ?></td>
                                    <td>
                                        <?php
                                        echo $bank_id > 0 && $ledger_id > 0 ? "<a href =" . base_url('reconcile/process?ledger_id=' . $ledger_id . "&bank_id=" . $bank_id) . " class='btn btn-success btn-sm'>Process</a>" : "";
                                        ?>
                                        <?php
                                        if ($bank_id > 0 && $ledger_id > 0) {
                                        ?>
                                        <button type='button' data-ledger-id = "<?php echo $ledger_id; ?>" data-bank-id = "<?php echo $bank_id; ?>" value="<?php echo $is_locked; ?>" class="btn <?php echo $is_locked == 1 ? "btn-primary" : "btn-warning"; ?> btn-sm" onclick='lock_entry(this);'>
                                            <?php echo $is_locked == 1 ? "Unlock" : "Lock" ?>
                                        </button>
                                        <?php } ?>

                                        <?php
                                        if ($monthnumber >= $max_month && $is_locked != 1 && ($bank_id > 0 || $ledger_id > 0)) {
                                        ?>
                                        <a class="btn btn-danger btn-sm" href="javascript:void(0);" data-toggle="modal" data-id="1" onclick="setConfirmDetails(this)" data-target="#ConfirmDeleteModal" data-url="reconcile/delete/?lid=<?php echo $ledger_id; ?>&bid=<?php echo $bank_id; ?>">Delete</a>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php
                                $i++;
                                $storeKeyDisplay = $store_key;
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="8"><center>There is no record found.</center></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<!--delete project confirm Modal-->
<div class="modal fade" id="project_archived_confirm_Modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Change Status of Project</h4>
            </div>
            <div class="modal-body">
                <p><b>WARNING:</b> Are you sure you want to move this project in Archived state?</p>
            </div>
            <div class="modal-footer">

                <input data-dismiss="modal" class="btn btn-danger pull-right ml10" type="button" id="record_changes" value="Cancel">
                <input class="btn btn-success pull-right" type="button" id="record_changes" value="Confirm" data-id="" data-status="" onclick="confirm_status(this);">
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#reconcile_tblListing").DataTable();
        if ($("#tblListing").length) {
            var cOptions = [];
            cOptions.columnDefs = [{
                    data: "srNo",
                    targets: 0,
                    orderable: false,
                    width: 50
                }, {
                    data: "store_key",
                    targets: 1
                },
                {
                    data: "month",
                    targets: 2
                },
                {
                    data: "year",
                    targets: 3,
                    orderable: false,
                },
                {
                    data: "action",
                    targets: 4,
                    orderable: false,
                    className: "dt-center",
                    width: 150
                }];
            cOptions.order = [
                [1, 'asc']
            ];
            cOptions.srNo = true;
            cOptions.mColumns = [1, 2, 3];
            Custom.initListingTable(cOptions);
        }
    });
    function setConfirmDetails(value) {
        var delete_url = $(value).attr("data-url");
        $("#ConfirmDeleteModal").find(".modal-body").children("p").show();
        $("#ConfirmDeleteModal").find(".confirmYes").show();
        $("#ConfirmDeleteModal").find(".modal-title").html("Bank Statement Deletion");
        $("#ConfirmDeleteModal").find(".modal-body").children("p").html("Do you really want to delete this bank statement");
        $("#ConfirmDeleteModal").find("#ConfirmDelete").attr('href', delete_url);
    }
    $("#store_id, #month, #year").change(function () {
        $("#frmSearch").submit();
    });
    function lock_entry(element) {
        var value = $(element).val();
        var ledger_id = $(element).data("ledger-id");
        var bank_id = $(element).data("bank-id");
        $.ajax({
            url: site_url + 'reconcile/lock_unlock_process',
            data: {"value": value, "ledger_id": ledger_id, "bank_id": bank_id},
            method: 'POST',
            success: function (response) {
                location.reload();
            }
        });
    }
      function setConfirmDetails(value) {
                            var delete_url = $(value).attr("data-url");
                            $("#ConfirmDeleteModal").find(".modal-body").children("p").show();
                            $("#ConfirmDeleteModal").find(".confirmYes").show();
                            $("#ConfirmDeleteModal").find(".modal-title").html("Bank Statement Deletion");
                            $("#ConfirmDeleteModal").find(".modal-body").children("p").html("Do you really want to delete this bank statement");
                            $("#ConfirmDeleteModal").find("#ConfirmDelete").attr('href', delete_url);
                        }
</script>