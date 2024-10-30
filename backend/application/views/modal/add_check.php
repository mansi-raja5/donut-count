<?php
$edit_id = isset($check[0]) ? $check[0]->id : '';
?>
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <div class="col-md-10">
                <h3 class="modal-title"><b>Add/Edit Check</b></h3>
            </div>
        </div>
        <div class="modal-body">
            <div class="row">
                 <div class="col-md-12">
                    <?php
                    $allow_to_add = 1;
                    $attributes = array('class' => 'validate', 'id' => 'frm_add_checks');
                    echo form_open_multipart('reconcile/addcheck', $attributes);
                    $display_style = 'none';
                    if($edit_id == '' && $check_no == ''){
                        $allow_to_add = 0;
                        $display_style = 'block';
                    }
                    ?>
                     <div class="alert alert-danger" id="check_no_error" style="display:<?php echo $display_style?>">
                         <strong>Error!</strong> <span class="error-msg">Please set check no using <a href="<?php echo base_url('settings/edit/general/5'); ?>">Admin Setting</a></span>
                     </div>
                        <input type="hidden" name="ledger_id" class="form-control" value="<?php echo $ledger_id; ?>">
                        <input type="hidden" name="id" class="form-control" value="<?php echo isset($check[0]) ? $check[0]->id : '';?>">

                        <div class="form-group">
                            <label for="Check Payable To">Check Payable To</label>
                            <input type="text" name="payble_to" class="form-control" placeholder="Check Payable To" value="<?php echo isset($check[0]) ? $check[0]->payble_to : '';?>" required>
                        </div>
                        <div class="form-group">
                            <label for="Check">Check</label>
                            <input type="text" name="check_number" class="form-control" placeholder="Check" readonly="" value="<?php echo $edit_id != '' ? (isset($check[0]) ? $check[0]->check_number : '') : ($check_no); ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="Memo">Memo</label>
                            <textarea class="form-control" name="memo" placeholder="Memo"><?php echo isset($check[0]) ? $check[0]->memo :''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label for="Check">Amount</label>
                            <input type="number" step="1" name="amount1" class="form-control" placeholder="Amount" value="<?php echo isset($check[0]) ? $check[0]->amount1 : ''; ?>">
                        </div>

                        <!-- <div class="form-group">
                            <label for="Check">Credit Received From</label>
                            <input type="text" name="credit_received_from" class="form-control" placeholder="Credit Received From" value="<?php //echo isset($check[0]) ? $check[0]->credit_received_from : ''; ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="Check">Amount</label>
                            <input type="number" step="1" name="amount2" class="form-control" placeholder="Amount" value="<?php //echo isset($check[0]) ? $check[0]->amount2 : ''; ?>" required>
                        </div> -->
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-primary" onclick="recon.submitCheckData()">Submit</button>
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>