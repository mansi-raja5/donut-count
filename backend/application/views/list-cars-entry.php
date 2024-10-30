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
        $attributes = array('name' => 'frmSearch', 'id' => 'frmSearch', 'method' => 'post');
        echo form_open(base_url('cars_entry'), $attributes);
        ?>
        <div class="row">
            <div class="col-md-4 mb10 pull-right text-right">
                <a href="<?php echo base_url('cars_entry/add'); ?>" class="btn blue" id="new_project">Add Cars Entry</a>
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-md-3 mb10">
                <?php
                echo form_input(array('id' => 'weekend_date', 'name' => 'weekend_date', 'class' => 'form-control datepicker', 'value' => isset($weekend_date) ? date("d-m-Y", strtotime($weekend_date)) : ""));
                ?>
            </div>
            <div class="col-md-3 mb10">
               <?php
                $options = array();
                $options[''] = '-- Select Store  --';
                $store_key = field(set_value('store_key', NULL), $this->input->get('store_key'));
                if (isset($store_list['records']) && !empty($store_list['records'])) {
                    foreach ($store_list['records'] as $row) {
                       $options[$row->key] = $row->name . " (" . $row->key . ")";
                    }
                }
                echo form_dropdown(array('id' => 'store_key', 'name' => 'store_key', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $store_key));
                ?>
            </div>
            <div class="col-md-3">
                <button type="button" id="btnSearch" name="btnSubmit" class="btn btn-success display-hide">Submit</button>
            </div>
        </div>
        <?php echo form_close(); ?>


        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover"  id="tblListing">
                    <thead>
                        <tr>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th>&nbsp;</th>
                            <th colspan="2">Monday</th>
                            <th colspan="2">Tuesday</th>
                            <th colspan="2">Wednesday</th>
                            <th colspan="2">Thursday</th>
                            <th colspan="2">Friday</th>
                            <th>Action</th>
                        </tr>
                        <tr>
                            <th>Sr No.</th>
                            <th>Store No.</th>
                            <th>Weekend Date</th>
                            <th>Total # of Cars</th>
                            <th>D-Part 2</th>
                            <th>Total # of Cars</th>
                            <th>D-Part 2</th>
                            <th>Total # of Cars</th>
                            <th>D-Part 2</th>
                            <th>Total # of Cars</th>
                            <th>D-Part 2</th>
                            <th>Total # of Cars</th>
                            <th>D-Part 2</th>
                            <th>Action.</th>

                        </tr>
                    </thead>
                    <tbody>
                        <?php
//                        echo "<pre>";
//                        print_r($result);
//                        echo "</pre>";
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
        $("#weekend_date").datepicker({
            daysOfWeekDisabled: [0, 1, 2, 3, 4, 5],
            format: 'dd-mm-yyyy'

        });
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
                    data: "date",
                    targets: 2
                },
                {
                    data: "day1_no_of_cars",
                    targets: 3,
                    "searching": true
                },
                {
                    data: "day1_avg_time",
                    targets: 4,
                    "searching": true
                },
                {
                    data: "day2_no_of_cars",
                    targets: 5,
                    "searching": true
                },
                {
                    data: "day2_avg_time",
                    targets: 6,
                    "searching": true
                },
                {
                    data: "day3_no_of_cars",
                    targets: 7,
                    "searching": true
                },
                {
                    data: "day3_avg_time",
                    targets: 8,
                    "searching": true
                },
                {
                    data: "day4_no_of_cars",
                    targets: 9,
                    "searching": true
                },
                {
                    data: "day4_avg_time",
                    targets: 10,
                    "searching": true
                },
                {
                    data: "day5_no_of_cars",
                    targets: 11,
                    "searching": true
                },
                {
                    data: "day5_avg_time",
                    targets: 12,
                    "searching": true
                },
                {
                    data: "action",
                    targets: 13,
                    orderable: false,
                    className: "dt-center",
                    width: 150
                }];
            cOptions.order = [
                [1, 'asc']
            ];
            cOptions.srNo = true;
            cOptions.mColumns = [1, 2, 3, 4, 5];
            Custom.initListingTable(cOptions);
        }
    });
    function setConfirmDetails(value) {
        var delete_url = $(value).attr("data-url");
        $("#ConfirmDeleteModal").find(".modal-body").children("p").show();
        $("#ConfirmDeleteModal").find(".confirmYes").show();
        $("#ConfirmDeleteModal").find(".modal-title").html("Item Deletion");
        $("#ConfirmDeleteModal").find(".modal-body").children("p").html("Do you really want to delete this item");
        $("#ConfirmDeleteModal").find("#ConfirmDelete").attr('href', delete_url);
    }
</script>