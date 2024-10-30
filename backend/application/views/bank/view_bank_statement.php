<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$month_arr = array(" ", "January", "February", "March", "April", "May", "Jun", "July", "August", "September", "October", "November", "December");
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
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover" id="tblListing">
                    <thead>
                        <tr>
                            <th colspan="6"><b>Bank STATEMENT</b></th>
                        </tr>
                        <tr>
                            <th><b>Sr No.</b></th>
                            <th><b>Date</b></th>
                            <th><b>Transaction</b></th>
                            <th><b>Check Num</b></th>
                            <th><b>Description</b></th>
                            <th><b>Amount</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $s_cnt = 0;
                        $desc_arr = array();
                        // echo "<pre>";
                        // print_r($ledger_data);exit;
                        if (isset($statement_data['records']) && !empty($statement_data['records'])) {
                            $key = 0;
                            foreach ($statement_data['records'] as $row) {
                                $key++;
                                ?>
                                <tr>
                                    <td><?php echo $key; ?></td>
                                    <td><?php echo date('m/d/Y', strtotime($row->date)); ?></td>
                                    <td><?php echo $row->transaction; ?></td>
                                    <td><?php echo $row->check_num; ?></td>
                                    <td><?php echo $row->description; ?></td>
                                    <td><?php echo isset($row->transaction_type) && $row->transaction_type == 'credit' ? "<span class='label label-success'>".$row->amount."</span>" : "<span class='label label-danger'>".$row->amount."</span>"; ?></td>

                                </tr>
                                <?php
                            }
                        }
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
    $(document).ready(function(){
       $("#tblListing").dataTable(); 
    });
</script>