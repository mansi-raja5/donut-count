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
                <a href="<?php echo base_url('settings/add/exclude'); ?>" class="btn blue" id="new_project">Add Settings</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover"  id="tbl_settings">
                    <thead>
                        <tr>
                            <th>Store Key</th>
                            <th>Title</th>
                            <th>From Date</th>
                            <th>To Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(sizeof($settings) > 0): 
                            foreach($settings as $row):
                        ?>
                            <tr>
                                <td><?php echo $row['store_key'] ?></td>
                                <td><?php echo $row['key_label'] ?></td>
                                <td><?php echo date("d-m-Y ",strtotime($row['from_date'])) ?></td>
                                <td><?php if($row['to_date']!="0000-00-00") echo date("d-m-Y ",strtotime($row['to_date'])) ?></td>
                                <td>
                                    <a href="<?php $url= "settings/edit/exclude/".$row["id"] ;$baseurl=base_url($url); echo $baseurl; ?>" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a>
                                </td>
                            </tr>
                        <?php endforeach;
                            else: ?>
                            <tr>
                                <td colspan=5 style="color:red"><center>No more Records</center></td>
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