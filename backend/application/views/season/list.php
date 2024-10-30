<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!-- BEGIN EXAMPLE TABLE PORTLET-->
<style>
tr.dtrg-group td::before {
   font-family: 'Glyphicons Halflings';
   content: "\e114";
   float: right;
   transition: all 0.5s;
}
tr.dtrg-group[aria-expanded="true"] td::before {
	-webkit-transform: rotate(180deg);
	-moz-transform: rotate(180deg);
	transform: rotate(180deg);
} 
</style>
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
                <a href="<?php echo base_url('season/add'); ?>" class="btn blue" id="new_project">Add Season Day</a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover"  id="tblListing">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Store</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Name</th>
                            <th>Status</th>
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
                    data: "store_key",
                    targets: 1
                }, {
                    data: "from_date",
                    targets: 2
                },{
                    data: "to_date",
                    targets: 3
                }, {
                    data: "name",
                    targets: 4,
                    visible: false
                },
                {
                    data: "status",
                    targets: 5
                },
                {
                    data: "action",
                    targets: 6,
                    orderable: false,
                    className: "dt-center",
                    width: 150
                }];
            cOptions.order = [
                [1, 'asc']
            ];
            cOptions.srNo = true;
            cOptions.mColumns = [1, 2,3,4,5];
            cOptions.rowGroup = { endRender: drawCallback, dataSrc: 'name' };
            cOptions.drawCallback = drawCallback;
            Custom.initListingTable(cOptions);
        }
    });

    function drawCallback (settings, json) {
        $('#tblListing tr').each(function() {
            var tr = jQuery(this);
            if (tr.hasClass('dtrg-group')) {
                tr.attr('aria-expanded', 'false');
            } else {
                tr.hide();
            }
        });
    }

    $(document).ready(function () {
        $('#tblListing tbody').on('click', 'tr.dtrg-group', function() {
            var trs = $(this).nextUntil('tr.dtrg-group');
            if ($(this).attr('aria-expanded') == 'false') {
                trs.show();
            } else {
                trs.hide();
            }
            $(this).attr('aria-expanded', $(this).attr('aria-expanded') != 'true');
        });
    });

    function setConfirmDetails(value) {
                            var delete_url = $(value).attr("data-url");
                            $("#ConfirmDeleteModal").find(".modal-body").children("p").show();
                            $("#ConfirmDeleteModal").find(".confirmYes").show();
                            $("#ConfirmDeleteModal").find(".modal-title").html("Season Day Deletion");
                            $("#ConfirmDeleteModal").find(".modal-body").children("p").html("Do you really want to delete this season data");
                            $("#ConfirmDeleteModal").find("#ConfirmDelete").attr('href', delete_url);
                        }
</script>