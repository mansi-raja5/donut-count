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
                <a href="<?php echo base_url('settings/add/labor'); ?>" class="btn blue" id="new_project">Add Settings</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover"  id="tbl_settings">
                    <thead>
                        <tr>
                            <th>Store</th>
                            <th>Calculation By</th>
                            <th>Year</th>
                            <th>Month</th>
                            <th>Weekly Date</th>
                            <th>Amount</th>
                            <th>Created date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(sizeof($general_settings) > 0): 
                            foreach($general_settings as $row):
                        ?>
                            <tr>
                                <td><?php echo $row['store_key'] ?></td>
                                <td><?php echo ucfirst($row['calculation_type']) ?></td>
                                <td><?php if($row['calculation_type']=="weekly")  echo date("Y", strtotime($row['weekly_date'])); else echo $row['year'] ?></td>
                                <td><?php if($row['calculation_type']=="weekly")  echo date("F", strtotime($row['weekly_date'])); else echo $row['month'] ?></td>
                                <td><?php if($row['weekly_date']!="0000-00-00") echo date("d-M-Y", strtotime($row['weekly_date'])); ?></td>
                                <td><?php echo $row['amount'] ?></td>
                                <td><?php echo date("d-M-Y", strtotime($row['created_on']))  ?></td>
                                <td><a href="<?php $url="settings/deletelabor/".$row['id'];echo base_url($url) ?>" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Delete</a>&nbsp;<a href="<?php $url= "settings/edit/labor/".$row["id"] ;$baseurl=base_url($url); echo $baseurl; ?>" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a></td>
                            </tr>
                        <?php endforeach;
                            else: ?>
                            <tr>
                                <td colspan=8 style="color:red">No more Records</td>
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