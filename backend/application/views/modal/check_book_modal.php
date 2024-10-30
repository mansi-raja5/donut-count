<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <div class="col-md-10">
                <h3 class="modal-title"><b>Check Records</b></h3>
            </div>
            <div class="col-md-2">
                <a href="javascript:void(0);" class="btn blue  pull-right ml10 mb10" onclick="recon.addEditChecks(this)">Add Checks</a>
            </div>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Check Payable To</th>
                                <th>Check#</th>
                                <th>Memo</th>
                                <th>Amount</th>
                                <!-- <th>Credit Received From</th>
                                <th>Amount</th> -->
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (isset($checkbook_record)) {
                            $i = 0;
                            foreach ($checkbook_record as $_checkbook_record) {
                                ?>
                                <tr>
                                    <th><?php echo ++$i; ?></th>
                                    <td><?php echo isset($_checkbook_record->payble_to) ? $_checkbook_record->payble_to : '' ?></td>
                                    <td><?php echo isset($_checkbook_record->check_number) ? $_checkbook_record->check_number : '' ?></td>
                                    <td><?php echo isset($_checkbook_record->memo) ? $_checkbook_record->memo : '' ?></td>
                                    <td id="ledger-check-<?php echo $_checkbook_record->id ?>">
                                    <?php echo $_checkbook_record->amount1 != '' ? ($_checkbook_record->amount1) : ''; ?>
                                    </td>
                                    <!-- <td><?php //echo isset($_checkbook_record->credit_received_from) ? $_checkbook_record->credit_received_from : '' ?></td>
                                    <td><?php //echo isset($_checkbook_record->amount2) ? $_checkbook_record->amount2 : '' ?></td> -->
                                    <td>
                                        <a href="javascript:void(0)"  checkid="<?php echo $_checkbook_record->id; ?>" title="Edit Check" onclick="recon.addEditChecks(this);"><i class="fa-icons fa fa-pencil fa-2x"></i></a>
                                        <a href="javascript:void(0)" checkid="<?php echo $_checkbook_record->id; ?>" title="Delete Check" onclick="recon.deleteChecks(this);"><i class="fa-icons fa fa-trash fa-2x fa-danger"></i></a>
                                        <a href="javascript:void(0)" memo="<?php echo isset($_checkbook_record->memo) ? $_checkbook_record->memo : '' ?>" amount= "<?php echo $_checkbook_record->amount1 != '' ? ($_checkbook_record->amount1) : ''; ?>" checkid="<?php echo $_checkbook_record->id; ?>" title="Void Check" onclick="recon.voidChecks(this)"><i class="fa-icons fa fa-tag fa-2x"></i></a>
                                    </td>
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
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>