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
                <a href="<?php echo base_url('settings/add/general'); ?>" class="btn blue" id="new_project">Add Settings</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover"  id="tbl_settings">
                    <thead>
                        <tr>
                            <th>Setting Key</th>
                            <th>Setting Value</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(sizeof($general_settings) > 0): 
                            foreach($general_settings as $row):
                        ?>
                            <tr>
                                <td><?php echo $row['key_name'] ?></td>
                                <?php if($row['key_name'] == 'check_number_starting') {
                                ?>
                                <td><a href="<?php echo base_url("settings/edit/general/".$row['id']);?>"><i class="fa fa-eye"></i></a></td>
                                <?php } else{ ?>
                                <td><?php echo $row['key_value'] ?></td>
                                <?php } ?>
                                <td><a href="<?php $url="settings/delete/general/".$row['id'];echo base_url($url) ?>" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Delete</a>&nbsp;<a href="<?php $url= "settings/edit/general/".$row["id"] ;$baseurl=base_url($url); echo $baseurl; ?>" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a></td>
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
        $("#tbl_settings").dataTable({});
    });
</script>