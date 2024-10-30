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
    
        <div class="row">
            <div class="col-md-4 mb10 pull-right text-right">
                <a href="<?php echo base_url('bill/add'); ?>" class="btn blue" id="new_project">Add Bill</a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover"  id="tblListing">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Month</th>
                            <th>Year</th>
                            <th>Created On</th>
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
                    data: "month",
                    targets: 1
                }, {
                    data: "year",
                    targets: 2
                },{
                    data: "created_on",
                    targets: 3
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
            cOptions.mColumns = [1, 2,3,4];
            Custom.initListingTable(cOptions);
        }
    });
        function setConfirmDetails(value) {
                            var delete_url = $(value).attr("data-url");
                            $("#ConfirmDeleteModal").find(".modal-body").children("p").show();
                            $("#ConfirmDeleteModal").find(".confirmYes").show();
                            $("#ConfirmDeleteModal").find(".modal-title").html("Season Day Deletion");
                            $("#ConfirmDeleteModal").find(".modal-body").children("p").html("Do you really want to delete this bill data");
                            $("#ConfirmDeleteModal").find("#ConfirmDelete").attr('href', site_url+delete_url);
                        }
</script>