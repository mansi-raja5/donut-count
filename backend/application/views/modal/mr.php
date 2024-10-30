<?php
$documentTypeArray = [];
foreach ($document_data as $_document_data) {
    $documentTypeArray[$_document_data->key_name] = $_document_data->label;
}
?>
<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <div class="col-md-4">
                <h3 class="modal-title"><b>Manual Reconcilation</b></h3>
            </div>
            <div class="col-md-2">
                <label id="manual_ledger_balance"><h4>Ledger Balance: $<span>0.00</span></h4></label>
            </div>
            <div class="col-md-2">
                <label id="manual_bank_balance"><h4>Bank Balance: $<span>0.00</span></h4></label>
            </div>
            <div class="col-md-3">
                <button type="button" href="javascript::void(0)" onClick="recon.resetManualReconcileSummary(this)" class="btn btn-danger mr10 pull-right text-right ">Reset</button>
                <button type="button" href="javascript::void(0)" onClick="recon.lockEntries(this)" class="btn blue manual_reconcile_lock mr10 pull-right text-right btn-disabled">Lock</button>
                <button type="button" href="<?php echo base_url('reconcile/manual?ledger_id=' . $ledger_id . "&bank_id=" . $bank_id); ?>" onClick="$('#submit_manual_form').submit()" class="btn blue manual_reconcile_submit mr10 pull-right text-right btn-disabled">Manual Reconcile</button>
            </div>
            <div class="col-md-1">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
        <div class="modal-body">
            <?php
            $attributes = array('class' => 'form-horizontal validate', 'id' => 'submit_manual_form');
            echo form_open_multipart('reconcile/manual', $attributes);
            ?>
            <div class="row">
                <div class="top_section">
                    <div class="col-md-12 mt20 top_section_manual_data">
                    </div>
                </div>
            </div>
            <input type="hidden" name="txt_manual_ledger_balance" id="txt_manual_ledger_balance" value="">
            <input type="hidden" name="txt_manual_bank_balance" id="txt_manual_bank_balance" value="">
            <input type="hidden" name="ledger_id" value="<?php echo $ledger_id; ?>">
            <input type="hidden" name="bank_id"  value="<?php echo $bank_id; ?>">
            <?php echo form_close(); ?>
            <div class="row manual_reconcile_cover">
                <div class="col-md-6 mt20">
                    <table class="table table-bordered table-hover" id="unreconciled_manual_ledger">
                        <thead>
                            <tr>
                                <th colspan="7">
                                    <b>Unreconciled Entries of Ledger (<span id="manual_ledger_checked_count"> 0</span> / <?php echo count($unreconciled_ledger_statement_data); ?>)</b>
                                    <div class="pull-right">
                                        <input type="radio" name="primary_selection" class="primary_selection" value="ledger_wise" checked="checked"> Ledger wise
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th></th>
                                <th><b>Date</b></th>
                                <th><b>Transaction</b></th>
                                <th><b>Description</b></th>
                                <th><b>Amount</b></th>
                                <th><b>Section</b></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td width="5%"></td>
                                <th width="10%"><b>Date</b></th>
                                <th width="5%"><b>Transaction</b></th>
                                <th width="45%"><b>Description</b></th>
                                <th width="10%"><b>Amount</b></th>
                                <th width="15%"><b>Section</b></th>
                                <td width="10%"></td>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php
                            $s_cnt = 0;
                            $desc_arr = array();
                            if (isset($unreconciled_ledger_statement_data) && !empty($unreconciled_ledger_statement_data)) {
                                $key = 0;
                                foreach ($unreconciled_ledger_statement_data as $row) {
                                    $key++;
                                    ?>
                                    <tr class="unreconcile-ledger-<?php echo $row->id; ?>">
                                        <td>
                                            <input type="checkbox" name="ledger_statement_id[]" value="<?php echo $row->id; ?>" class="ledger-checkbox <?php echo ($row->description == "Credit Card Credits") ? 'credit_card_total_class' : ''; ?>" amt = "<?php echo isset($row->transaction_type) && $row->transaction_type == 'credit' ? $row->credit_amt : (($row->debit_amt) * (-1)); ?>" />
                                        </td>
                                        <td>
                                            <div class="manual-label">
                                            <?php echo ($row->credit_date) ? date('m/d/Y', strtotime($row->credit_date)) : ''; ?>
                                            </div>
                                            <input class="display-none manual-input manual_datepicker" type="text" value="<?php echo ($row->credit_date) ? date('m/d/Y', strtotime($row->credit_date)) : ''; ?>" name="manual-ledger-credit-date">
                                        </td>
                                        <td>
                                            <div class="manual-label">
                                            <?php echo $row->transaction_type; ?>
                                            </div>
                                            <select class="display-none manual-input manual-ledger-transaction-type" name="manual-ledger-transaction-type">
                                                <option value="credit" <?php echo ($row->transaction_type == "credit") ? 'selected' : ''; ?> >Credit</option>
                                                <option value="debit" <?php echo ($row->transaction_type == "debit") ? 'selected' : ''; ?> >Debit</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="manual-label">
                                            <?php echo $row->description; ?>
                                            </div>
                                            <input class="display-none manual-ledger-description manual-input <?php echo (isset($row->document_type) && $row->document_type != "general_section") ? "non-editable" : ""; ?>" type="text" value="<?php echo $row->description; ?>" name="manual-ledger-description">
                                        </td>
                                        <td>
                                            <div class="manual-label">
                                            <?php echo isset($row->transaction_type) && $row->transaction_type == 'credit' ? "<span class='text-success'>" . $row->credit_amt . "</span>" : "<span class='text-danger'>" . $row->debit_amt . "</span>"; ?>
                                            </div>
                                            <input class="display-none manual-input manual-ledger-amount" type="number" step="1" value="<?php echo isset($row->transaction_type) && $row->transaction_type == 'credit' ? $row->credit_amt : $row->debit_amt; ?>" name="manual-ledger-amount">
                                        </td>
                                        <td>
                                            <div class="manual-label">
                                                <?php echo isset($documentTypeArray[$row->document_type]) ? $documentTypeArray[$row->document_type] : $row->document_type; ?>
                                            </div>
                                            <select class="display-none manual-input manual-ledger-document-type" name="manual-ledger-document-type" ledgerid="<?php echo isset($ledger->id) ? $ledger->id : ''; ?>">
                                                <option value="-1">Select Section</option>
                                                <?php foreach ($document_data as $_document_data): ?>
                                                    <option value="<?php echo $_document_data->key_name; ?>" <?php echo ($row->document_type == $_document_data->key_name ) ? 'selected' : ''; ?> ><?php echo $_document_data->label; ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" title="Edit Ledger Entry" onclick="recon.edit_cell_manual(this);" class="manual-edit"><i class="fa-icons fa fa-pencil fa-2x"></i></a>
                                            <?php if($row->is_manual == 1): ?>
                                            <a href="javascript:void(0)" title="Delete Ledger Entry" type="ledger" ledger-statement-id='<?php echo $row->id; ?>' onclick="recon.delete_cell_manual(this);" class="manual-delete"><i class="fa-icons fa fa-trash fa-2x fa-danger"></i></a>
                                            <?php endif; ?>
                                            <a href="javascript:void(0)" title="Save Ledger Entry" class='manual-save display-none' type='button' ledger-statement-id='<?php echo $row->id; ?>' onclick='recon.extra_manual_entry(this);'><span class="fa-icons fa fa-save fa-2x"></span></a>
                                            <a href="javascript:void(0)" class="remove-edit pull-right display-none" onclick='recon.remove_edit_from_row(this);'><span class="fa-icons fa fa-times-circle fa-2x"></span></a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <button type="button" class="btn blue pull-right ml10 mb10" add-type="ledger" onclick="recon.addManualRow(this)">Add Ledger Entries</button>
                </div>
                <div class="col-md-6 mt20">
                    <table class="table table-bordered table-hover" id="unreconciled_manual_bank">
                        <thead>
                            <tr>
                                <th colspan="7">
                                    <b>Unreconciled Entries of Bank (<span id="manual_bank_checked_count"> 0</span> / <?php echo count($unreconciled_bank_statement_data); ?>)</b>
                                    <div class="pull-right">
                                        <input type="radio" name="primary_selection" class="primary_selection" value="bank_wise"> Bank wise
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th></th>
                                <th><b>Date</b></th>
                                <th><b>Transaction</b></th>
                                <th><b>Check Num</b></th>
                                <th><b>Description</b></th>
                                <th><b>Amount</b></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <td width="5%"></td>
                                <th width="10%"><b>Date</b></th>
                                <th width="5%"><b>Transaction</b></th>
                                <th width="5%"><b>Check No.</b></th>
                                <th width="45%"><b>Description</b></th>
                                <th width="10%"><b>Amount</b></th>
                                <td width="10%"></td>
                            </tr>
                        </tfoot>
                        <tbody>
                            <?php
                            $s_cnt = 0;
                            $desc_arr = array();
                            if (isset($unreconciled_bank_statement_data) && !empty($unreconciled_bank_statement_data)) {
                                $key = 0;
                                $creditCardMatchAry = ['DEPOSIT BOFA MERCH', 'FDCLGIFT DD', 'SETTLEMENT AMERICAN EXPRESS'];
                                foreach ($unreconciled_bank_statement_data as $row) {
                                    $key++;
                                    $credit_card_total_class = '';
                                    foreach ($creditCardMatchAry as $_creditCardMatch) {
                                        if (strpos($row->description, $_creditCardMatch) !== false) {
                                            $credit_card_total_class = 'credit_card_total_bank_class';
                                        }
                                    }
                                    ?>
                                    <tr class="unreconcile-bank-<?php echo $row->id; ?>">
                                        <td>
                                            <input type="checkbox" name="bank_statement_id[]" value="<?php echo $row->id; ?>" class="bank-checkbox <?php echo $credit_card_total_class; ?>" amt = "<?php echo isset($row->transaction_type) && $row->transaction_type == 'credit' ? $row->amount : (($row->amount) * (-1)); ?>"/>
                                        </td>
                                        <td>
                                            <div class="manual-label">
                                            <?php echo date('m/d/Y', strtotime($row->date)); ?>
                                            </div>
                                            <input class="display-none manual-input manual_datepicker" type="text" value="<?php echo ($row->date) ? date('m/d/Y', strtotime($row->date)) : ''; ?>" name="manual-bank-credit-date">
                                        </td>
                                        <td>
                                            <div class="manual-label">
                                            <?php echo $row->transaction; ?>
                                            </div>
                                            <select class="display-none manual-input <?php echo (isset($row->id) && $row->id != 0) ? 'non-editable' : ''; ?>" name="manual-bank-transaction-type">
                                                <option value="check" <?php echo (strtolower($row->transaction) == "check") ? 'selected' : ''; ?> >Check</option>
                                                <option value="credit" <?php echo (strtolower($row->transaction) == "credit") ? 'selected' : ''; ?> >Credit</option>
                                                <option value="debit" <?php echo (strtolower($row->transaction) == "debit") ? 'selected' : ''; ?> >Debit</option>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="manual-label">
                                            <?php echo $row->check_num; ?>
                                            </div>
                                            <input class="display-none manual-input" type="text" value="<?php echo $row->check_num; ?>" name="manual-bank-check_num">

                                        </td>
                                        <td>
                                            <div class="manual-label">
                                            <?php echo $row->description; ?>
                                            </div>
                                            <input class="display-none manual-input" type="text" value="<?php echo $row->description; ?>" name="manual-bank-description">
                                        </td>
                                        <td>
                                            <div class="manual-label">
                                            <?php echo isset($row->transaction_type) && $row->transaction_type == 'credit' ? "<span class='text-success'>" . $row->amount . "</span>" : "<span class='text-danger'>" . $row->amount . "</span>"; ?>
                                            </div>
                                            <input class="display-none manual-input manual-bank-amount" type="number" value="<?php echo $row->amount; ?>" name="manual-bank-amount" step="1">
                                        </td>
                                        <td>
                                            <a href="javascript:void(0)" title="Edit Bank Entry" onclick="recon.edit_cell_manual(this);" class="manual-edit"><i class="fa-icons fa fa-pencil fa-2x"></i></a>
                                            <?php if($row->is_manual == 1): ?>
                                            <a href="javascript:void(0)" title="Delete Bank Entry"  bank-statement-id="<?php echo $row->id; ?>" type="bank" onclick="recon.delete_cell_manual(this);" class="manual-delete"><i class="fa-icons fa-danger fa fa-trash fa-2x"></i></a>
                                            <?php endif; ?>
                                            <a href="javascript:void(0)" title="Save Bank Entry" class='manual-save display-none' type='button' bank-statement-id='<?php echo $row->id; ?>' onclick='recon.extra_manual_entry_bank(this);'><span class="fa-icons fa fa-save fa-2x"></span></a>
                                            <a href="javascript:void(0)" class="remove-edit pull-right display-none" onclick='recon.remove_edit_from_row(this);'><span class="fa-icons fa fa-times-circle fa-2x"></span></a>
                                        </td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                    <button type="button" class="btn blue pull-right ml10 mb10" add-type="bank" onclick="recon.addManualRow(this)">Add Bank Entries</button>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<table id="ledger_blank_tr_div" class="display-none">
    <tr>
        <td></td>
        <td>
            <input class="manual-input manual_datepicker" type="text" value="" name="manual-ledger-credit-date" />
        </td>
        <td>
            <select class="manual-input manual-ledger-transaction-type" name="manual-ledger-transaction-type">
                <option value="credit">Credit</option>
                <option value="debit">Debit</option>
            </select>
        </td>
        <td>
            <input class="manual-input manual-ledger-description" type="text" name="manual-ledger-description" />
        </td>
        <td>
            <input class="manual-input manual-ledger-amount" type="number" step="1" name="manual-ledger-amount" />
        </td>
        <td>
            <select class="manual-input manual-ledger-document-type" name="manual-ledger-document-type" ledgerid="<?php echo isset($ledger->id) ? $ledger->id : ''; ?>">
                <option value="-1">Select Section</option>
                    <?php foreach ($document_data as $_document_data): ?>
                    <option value="<?php echo $_document_data->key_name; ?>"><?php echo $_document_data->label; ?></option>
                    <?php endforeach; ?>
            </select>
        </td>
        <td>
            <a href="javascript:void(0)" title="Save Ledger Entry" class='manual-save' type='button' ledger-statement-id='0' onclick='recon.extra_manual_entry(this);'><span class="fa-icons fa fa-save fa-2x"></span></a>
            <a href="javascript:void(0)" class="delete-row pull-right"><span class="fa-icons fa fa-times-circle fa-2x"></span></a>
        </td>
    </tr>
</table>

<table id="bank_blank_tr_div" class="display-none">
    <tr>
        <td></td>
        <td>
            <input class="manual-input manual_datepicker" type="text" value="" name="manual-bank-credit-date">
        </td>
        <td>
            <select class="manual-input" name="manual-bank-transaction-type" onchange='recon.bankEntryType(this);'>
                <option value="-1">--Select Type--</option>
                <option value="check">Check</option>
                <option value="credit">Credit</option>
                <option value="debit">Debit</option>
            </select>
        </td>
        <td>
            <input class="manual-input" type="text" value="" name="manual-bank-check_num" onchange='recon.bankEntryType(this);'>

        </td>
        <td>
            <input class="manual-input" type="text" value="" name="manual-bank-description">
        </td>
        <td>
            <input class="manual-input manual-bank-amount" type="number" step="1" value="" name="manual-bank-amount">
        </td>
        <td>
            <a href="javascript:void(0)" title="Save Bank Entry" class='manual-save' type='button' bank-statement-id='0' onclick='recon.extra_manual_entry_bank(this);'><span class="fa-icons fa fa-save fa-2x"></span></a>
            <a href="javascript:void(0)" class="delete-row pull-right"><span class="fa-icons fa fa-times-circle fa-2x"></span></a>
        </td>
    </tr>
</table>

<script type="text/javascript">
    $(document).ready(function () {
        // Setup - add a text input to each footer cell
        $('#unreconciled_manual_bank tfoot th').each(function () {
            var title = $(this).text();
            $(this).html('<input type="text" style="width:100%;" class="manual-search-bank-input" placeholder="Search ' + title + '" />');
        });

        // DataTable
        var bankDataTable = $('#unreconciled_manual_bank').DataTable({
            "paging": false,
            "info": false,
            "stripeClasses": []
        });

        // Apply the search
        bankDataTable.columns().every(function () {
            var that = this;
            $('.manual-search-bank-input', this.footer()).on('keyup change clear', function () {
                if (that.search() !== this.value) {
                    that
                            .search(this.value)
                            .draw();
                }
            });
        });
    });

    $(document).ready(function () {
        // Setup - add a text input to each footer cell
        $('#unreconciled_manual_ledger tfoot th').each(function () {
            var title = $(this).text();
            $(this).html('<input type="text" style="width:100%;" class="manual-search-ledger-input" placeholder="Search ' + title + '" />');
        });

        // DataTable
        var ledgerDataTable = $('#unreconciled_manual_ledger').DataTable({
            "paging": false,
            "info": false,
            "stripeClasses": []
        });

        // Apply the search
        ledgerDataTable.columns().every(function () {
            var that = this;
            $('.manual-search-ledger-input', this.footer()).on('keyup change clear', function () {
                if (that.search() !== this.value) {
                    that
                            .search(this.value)
                            .draw();
                }
            });
        });
    });
</script>