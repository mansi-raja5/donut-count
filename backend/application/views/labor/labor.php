<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body ">
        <div class="row">
            <div class="col-md-4 mb10">
                <span style="color:red;font-size:14px;text-align:right">Current Tax Percent is <?php echo $tax_percent ?><span>
            </div>
            <div class="col-md-4 mb10 pull-right text-right">
                <a href="<?php echo base_url('labor/add'); ?>" class="btn blue" >Add Labour Summary</a>
            </div>
        </div>
        <table class="table table-striped table-bordered table-hover" id="tbl_listing">
            <thead>
                <th>Store</th>
                <th>Week Ending Date</th>
                <th>Gross Pay</th>
                <th>Bonus</th>
                <th>Covid</th>
                <th>Tax Percentage</th>
                <th>Tax</th>
                <th>Total Pay</th>
                <th>Net Sales</th>
                <th>Labor %</th>
                <th>Action</th>
            </thead>
            <tbody>
                <?php if(sizeof($labor) > 0) :
                        foreach($labor as $row):
                            ?>
                                <tr>
                                    <td><?php echo $row['store_key'] ?></td>
                                    <td><?php echo date("m-d-Y",strtotime($row['week_ending_date'])) ?></td>
                                    <td><?php echo "$".$row['gross_pay'] ?></td>
                                    <td><?php echo "$".$row['bonus'] ?></td>
                                    <td><?php echo "$".$row['covid'] ?></td>
                                    <td><?php echo $row['tax_percentage']."%"; ?></td>
                                    <td><?php echo "$".$row['tax_amount'] ?></td>
                                    <td><?php echo "$".$row['total_pay'] ?></td>
                                    <td><?php echo "$".$row['net_sales'] ?></td>
                                    <td><?php echo $row['labor_percentage']."%"; ?></td>
                                    <?php $url= 'labor/delete/'.$row['id'] ?>
                                    <td><a href="<?php  echo base_url($url) ?>" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i> Delete</a><a href="<?php  $url= 'labor/edit/'.$row['id']; echo base_url($url) ?>" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i> Edit</a></td>
                                </tr>
                            <?php
                        endforeach;
                    else:
                         ?>
                    <tr><td colspan=11 color="red">No more Records</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
    $(document).ready(function () {
        $("#tbl_listing").dataTable({
        });
    });
</script>