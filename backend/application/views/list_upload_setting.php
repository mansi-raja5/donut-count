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
                <a href="<?php echo base_url('statement/upload_setting'); ?>" class="btn blue" id="new_project">Add Upload Setting</a>
            </div>
        </div>
        <br/>
       

        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover"  id="tblListing">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Invoice Text</th>
                            <th>Document Name1</th>
                            <th>Document Name2</th>
                            <th>Document Name3</th>
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
<!--delete project confirm Modal-->
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
                    data: "selected_type",
                    targets: 1
                }, {
                    data: "description",
                    targets: 2
                },
                {
                    data: "invoice_text",
                    targets: 3
                },
                {
                    data: "document_name_1",
                    targets: 4,
//                    orderable: false,
                },
                {
                    data: "document_name_2",
                    targets: 5,
//                    orderable: false,
                },
                {
                    data: "document_name_3",
                    targets: 6,
//                    orderable: false,
                } , {
                    data: "created_on",
                    targets: 7,
//                    orderable: false,
                },
                {
                    data: "action",
                    targets: 8,
                    orderable: false,
                    className: "dt-center",
                    width: 150
                }];
            cOptions.order = [
                [2, 'asc']
            ];
            cOptions.srNo = true;
            cOptions.mColumns = [ 1,2, 3];
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