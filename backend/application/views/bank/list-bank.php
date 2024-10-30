<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$min_year = 2015;
$max_year = 2025;
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
        <?php
        $attributes = array('name' => 'frmSearch', 'id' => 'frmSearch', 'method' => 'get');
        echo form_open('', $attributes);
        ?>
        <div class="row">
            <div class="col-md-4 mb10 pull-right text-right">
                <a href="<?php echo base_url('bank/import'); ?>" class="btn blue" id="new_project">Add Bank Statement</a>
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-md-3 mb10">
                <?php
                $month_arr = array("January", "February", "March", "April", "May", "Jun", "July", "August", "September", "October", "November", "December");
                $options = array();
                $options[''] = '-- Month  --';
                $cid = field(set_value('month', NULL), $this->input->get('month'));
                foreach ($month_arr as $key => $value) {
                    $options[$key+1] = $value;
                }

                echo form_dropdown(array('id' => 'month', 'name' => 'month', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $cid));
                ?>
            </div>
              <div class="col-md-3 mb10">
                <?php
                $options = array();
                $options[''] = '-- Year  --';
                $year = field(set_value('year', NULL), $this->input->get('year'));
                for($i = $min_year; $i <= $max_year; $i++){
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
                <table class="table table-striped table-bordered table-hover"  id="tblListing">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>store</th>
                            <th>Month</th>
                            <th>Year</th>
                            <th>Is Locked</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
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
                    data: "is_locked",
                    targets: 4,
                    orderable: false,
                },
                {
                    data: "action",
                    targets: 5,
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
</script>