<div class="portlet box green-haze">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-picture"></i><b><?php echo $title; ?> (<?php echo $checkbook_reconcile_count . "/" . count($checkbook_record); ?>)</b>
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand" data-original-title="" title=""> </a>
            <a href="" class="fullscreen"> </a>
            <!--<a href="javascript:;" class="reload" data-original-title="" title=""> </a>-->
        </div>
    </div>
    <div class="portlet-body">
        <table class="table table-condensed table-hover" id="<?php echo $table_id; ?>">
            <thead>
                <tr>
                    <th width="3%"><b>No</b></th>
                    <th width="20%"><b>Check Payable To</b></th>
                    <th width="15%"><b>Check#</b></th>
                    <th width="7%"><b>Memo.</b></th>
                    <th width="40%"><b>Amount</b></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th width="3%"><b>No</b></th>
                    <th width="20%"><b>Check Payable To</b></th>
                    <th width="15%"><b>Check#</b></th>
                    <th width="7%"><b>Memo.</b></th>
                    <th width="40%"><b>Amount</b></th>
                </tr>
            </tfoot>
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
                            <?php
                            if ($_checkbook_record->is_reconcile) {
                                ?>
                                <td class="<?php echo $is_previous ? 'blue-reconciled-class' : getBankCompareReconciledClass($_checkbook_record); ?> <?php echo $_checkbook_record->is_void ? 'gray-reconciled-class' : ''; ?>" id="ledger-check-<?php echo $_checkbook_record->id ?>">
                                    <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $_checkbook_record->bank_statement_id ?>" reconciliation-type = "<?php echo $_checkbook_record->reconcile_type ?>">
                                    <?php echo $_checkbook_record->amount1 != '' ? ($_checkbook_record->amount1) : ''; ?>
                                    </a>
                                    <?php if (!$is_locked[0]->islocked): ?>
                                        <a title ='Unreconciled Entry' href='javascript:void(0);' checkid="<?php echo $_checkbook_record->id; ?>" bankstatementid="<?php echo $_checkbook_record->bank_statement_id; ?>" onclick="recon.unReconcileCheck(this);">U</a>
                                    <?php endif; ?>
                                </td>
                                    <?php
                                } else {
                                    ?>
                                <td id="ledger-check-<?php echo $_checkbook_record->id ?>">
                                    <?php
                                    echo $checkAmount = $_checkbook_record->amount1 != '' ? ($_checkbook_record->amount1) : '';
                                    ?>
                                </td>
                                <?php
                            }
                            ?>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<!-- END CONDENSED TABLE PORTLET-->
<?php
function getBankCompareReconciledClass($ledgerData){

    global $previous_reconciled_bank;
    global $next_reconciled_bank;

    $previous_reconciled_bank_ids_ary   = explode(",", $previous_reconciled_bank[0]->reconciledids);
    $next_reconciled_bank_ids_ary   = explode(",", $next_reconciled_bank[0]->reconciledids);

    $reconciledClass = "";
    if ($ledgerData->is_reconcile == 1
            && property_exists($ledgerData, 'credit_amt') && $ledgerData->credit_amt == 0
            && property_exists($ledgerData, 'debit_amt') && $ledgerData->debit_amt == 0
        ) {
        $reconciledClass = 'gray-reconciled-class';
    } elseif ($ledgerData->is_reconcile && in_array($ledgerData->bank_statement_id, $previous_reconciled_bank_ids_ary)){
        $reconciledClass = 'blue-reconciled-class';
    } elseif ($ledgerData->is_reconcile && in_array($ledgerData->bank_statement_id, $next_reconciled_bank_ids_ary)){
        $reconciledClass = 'orange-reconciled-class';
    } elseif ($ledgerData->is_reconcile){
        $reconciledClass = 'reconciled-class';
    }
    return $reconciledClass;
}
?>
<script type="text/javascript">
$(document).ready(function () {
    // Setup - add a text input to each footer cell
    $('#<?php echo $table_id; ?> tfoot th').each(function () {
        var title = $(this).text();
        $(this).html('<input type="text"  style="width:100%;" class="current-check-entries-input" placeholder="Search ' + title + '" />');
    });

    // DataTable
    var adjustmnt_entries_datatable = $('#<?php echo $table_id; ?>').DataTable({
        "paging": false,
        "info": false,
        "stripeClasses": [],
        "autoWidth": false
    });

    // Apply the search
    adjustmnt_entries_datatable.columns().every(function () {
        var that = this;
        $('.current-check-entries-input', this.footer()).on('keyup change clear', function () {
            if (that.search() !== this.value) {
                that
                        .search(this.value)
                        .draw();
            }
        });
    });
//     $(".collapse").each(function(){
//       $(this).trigger("click"); 
//    });
});
</script>