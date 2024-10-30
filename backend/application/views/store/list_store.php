<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body">
         <?php echo $this->session->flashdata('msg'); ?>
        <div class="row">
            <div class="col-md-4 mb10 pull-right text-right">
                <a href="<?php echo base_url('store/add'); ?>" class="btn blue" id="new_project">Add Store</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover"  id="tbl_store">
                    <thead>
                        <tr>
                            <th>Store Key</th>
                            <th>Store Name</th>
                            <th>Address</th>
                            <th>Tax Id</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(sizeof($stores['records']) > 0): 
                            foreach($stores['records'] as $row):
                        ?>
                            <tr>
                                <td><?php echo $row->key ?></td>
                                <td><?php echo $row->name  ?></td>
                                <td><?php echo $row->location  ?></td>
                                <td><?php echo $row->tax_id  ?></td>
                                <td><?php echo $row->status == 'A' ? 'Active' : 'InActive'; ?></td>
                                <td><a href="<?php $url="store/delete/$row->store_id";echo base_url($url) ?>" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Delete</a>&nbsp;<a href="<?php $url= "store/edit/$row->store_id" ;$baseurl=base_url($url); echo $baseurl; ?>" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a></td>
                            </tr>
                        <?php endforeach;
                            else: ?>
                            <tr>
                                <td colspan=3 style="color:red">No more Records</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $("#tbl_store").dataTable({});
    });
</script>