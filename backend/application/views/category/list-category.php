<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$min_year = 2015;
$max_year = 2025;
?>
<!-- BEGIN EXAMPLE TABLE PORTLET-->
<div class="portlet light bordered">
      <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
       <div class="portlet-body">
        <div class="row">
            <div class="col-md-12">
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
                                    <?php echo form_label('Category', 'category', array('class' => 'control-label')); ?>
<?php echo form_input(array('id' => 'category', 'name' => 'category', 'class' => 'form-control', 'placeholder' => 'category')); ?>
                                </div>
                            </div>
                         
                            <div class="col-md-3">
                                  <?php echo form_label('Descriptin', 'description', array('class' => 'control-label')); ?>
                               <?php echo form_input(array('id' => 'description', 'name' => 'description', 'class' => 'form-control', 'placeholder' => 'Description')); ?>
                            </div>

                        </div>
                        <div class="form-actions right">
                            <div class="col-md-12">
                                <?php echo form_button(array('id' => 'btnSearch', 'content' => 'Search', 'class' => 'btn blue')); ?>
<?php echo anchor('category', 'Cancel', array('class' => 'btn default')); ?>
                            </div>
                        </div>
<?php echo form_close(); ?>
                    </div>
                </div>


<div class="portlet box">
  
   
    
     <div class="portlet light bordered">
    <div class="portlet-body">
    
        <div class="row">
            <div class="col-md-4 mb10 pull-right text-right">
                <a href="<?php echo base_url('category/add'); ?>" class="btn blue" id="new_project">Add Category</a>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover"  id="tblListing">
                    <thead>
                        <tr>
                            <th>Sr No.</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Breakdown Description</th>
                            <th>Company</th>
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
</div>
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
                    data: "name",
                    targets: 1
                }, {
                    data: "description",
                    targets: 2
                }, {
                    data: "breakdown_description",
                    targets: 3,
                      orderable: false,
                }, {
                    data: "company",
                    targets: 4
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
            cOptions.mColumns = [1, 2, 3];
            Custom.initListingTable(cOptions);
        }
    });
        function setConfirmDetails(value) {
                            var delete_url = $(value).attr("data-url");
                            $("#ConfirmDeleteModal").find(".modal-body").children("p").show();
                            $("#ConfirmDeleteModal").find(".confirmYes").show();
                            $("#ConfirmDeleteModal").find(".modal-title").html("Category Deletion");
                            $("#ConfirmDeleteModal").find(".modal-body").children("p").html("Do you really want to delete this category");
                            $("#ConfirmDeleteModal").find("#ConfirmDelete").attr('href', delete_url);
                        }
</script>