<?php
$documentTypeArray = [];
foreach ($document_data as $_document_data) {
    $documentTypeArray[$_document_data->key_name] = $_document_data->label;
}
?>
<div class="portlet box green-haze">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-picture"></i><b><?php echo $title; ?> (<?php echo $ledger_reconciled_count . "/" . count($ledger_data['records']); ?>)</b>
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand" data-original-title="" title=""> </a>
            <a href="" class="fullscreen"> </a>
        </div>
    </div>
    <div class="portlet-body">
        <table class="table table-condensed table-hover" id="<?php echo $table_id; ?>">
            <thead>
                <tr>
                    <th width="3%"><b>No</b></th>
                    <th width="20%"><b>Section</b></th>
                    <th width="15%"><b>Date</b></th>
                    <th width="7%"><b>Trans.</b></th>
                    <th width="40%"><b>Description</b></th>
                    <th width="15%"><b>Amount</b></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th width="3%"><b>No</b></th>
                    <th width="20%"><b>Section</b></th>
                    <th width="15%"><b>Date</b></th>
                    <th width="7%"><b>Trans.</b></th>
                    <th width="40%"><b>Description</b></th>
                    <th width="15%"><b>Amount</b></th>
                </tr>
            </tfoot>
            <tbody>
                <?php
                if (isset($ledger_data['records']) && !empty($ledger_data['records'])) {
                    $key = 0;
                    foreach ($ledger_data['records'] as $row) {

                        if($is_previous == 1 && $row->is_reconcile == 1)
                            $reconciledClass = "blue-reconciled-class";
                        elseif($is_previous == 1)
                            $reconciledClass = "";
                        else
                            $reconciledClass = getBankCompareReconciledClass($row);

                        $borderClass = '';
                        if($row->with_point_diff == 1)
                        {
                            $borderClass = 'with-point-diff';
                        }
                        ?>
                        <tr class="<?php echo $reconciledClass." ".$borderClass; ?>" id="ledger-<?php echo $row->id ?>">
                            <td><?php echo ++$key; ?></td>
                            <td><?php echo $documentTypeArray[$row->document_type]; ?></td>
                            <td><?php echo date('m/d/Y', strtotime($row->credit_date)); ?></td>
                            <td><?php echo $row->transaction_type; ?></td>
                            <td><?php echo $row->description; ?></td>
                            <?php
                            if ($row->is_reconcile) {
                            ?>
                                <td>
                                    <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="Show Respective Reconciled Entries" data-content="" class="reconciled-info" reconciled-ids = "<?php echo $row->bank_statement_id ?>" type="ledger"  reconciliation-type = "<?php echo $row->reconcile_type ?>">
                                    <?php echo isset($row->transaction_type) && $row->transaction_type == 'credit' ? "<span class='text-success'>" . $row->credit_amt . "</span>" : "<span class='text-danger'>" . $row->debit_amt . "</span>"; ?>
                                    </a>
                                    <?php
                                    $showUnreconcile = true;
                                    if($row->reconcile_type == 'adjustment' && $row->is_adjustment_entry != 1)
                                        $showUnreconcile = false;
                                    if (!$is_locked[0]->islocked && ($showUnreconcile)): ?>
                                        <a title ='Unreconciled Entry' href='javascript:void(0);' onclick="show_unreconcile_modal(<?php echo "[" . $row->id . "]"; ?>,<?php echo "[" . $row->bank_statement_id . "]"; ?>,'<?php echo $row->reconcile_type; ?>');">U</a>
                                    <?php endif; ?>
                                </td>
                            <?php
                            } else {
                            ?>
                                <td>
                                    <?php
                                    echo isset($row->transaction_type) && $row->transaction_type == 'credit' ? "<span class='text-success'>" . $row->credit_amt . "</span>" : "<span class='text-danger'>" . $row->debit_amt . "</span>";
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
    global $next_adjustment_ledger;

    $previous_reconciled_bank_ids_ary   = explode(",", $previous_reconciled_bank[0]->reconciledids);
    $next_reconciled_bank_ids_ary       = explode(",", $next_reconciled_bank[0]->reconciledids);
    $next_adjustment_ledger_ids_ary     = explode(",", $next_adjustment_ledger[0]->reconciledids);

    $reconciledClass = "";
    if ($ledgerData->is_reconcile == 1 && $ledgerData->credit_amt == 0 && $ledgerData->debit_amt == 0) {
        $reconciledClass = 'gray-reconciled-class';
    } elseif ($ledgerData->is_reconcile && in_array($ledgerData->bank_statement_id, $previous_reconciled_bank_ids_ary)){
        $reconciledClass = 'blue-reconciled-class';
    } elseif ($ledgerData->is_reconcile && in_array($ledgerData->bank_statement_id, $next_reconciled_bank_ids_ary) && $ledgerData->reconcile_type == 'auto'){
        $reconciledClass = 'orange-reconciled-class';
    } elseif ($ledgerData->is_reconcile && in_array($ledgerData->bank_statement_id, $next_adjustment_ledger_ids_ary) && $ledgerData->reconcile_type == 'adjustment'){
        $reconciledClass = 'orange-reconciled-class';
    } elseif ($ledgerData->is_reconcile && $ledgerData->reconcile_type == 'adjustment' && $ledgerData->is_manual == 1 ){
        $reconciledClass = 'adjustment-class';
    } elseif ($ledgerData->is_reconcile){
        $reconciledClass = 'reconciled-class';
    }
    return $reconciledClass;
}
?>
<script type="text/javascript">
$(document).ready(function () {
//    $('.collapse').trigger('click');
    // Setup - add a text input to each footer cell
    $('#<?php echo $table_id; ?> tfoot th').each(function () {
        var title = $(this).text();
        $(this).html('<input type="text"  style="width:100%;" class="current-ledger-entries-input" placeholder="Search ' + title + '" />');
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
        $('.current-ledger-entries-input', this.footer()).on('keyup change clear', function () {
            if (that.search() !== this.value) {
                that
                        .search(this.value)
                        .draw();
            }
        });
    });
});

/*$(document).on("click", "#<?php //echo $table_id; ?> thead tr", function () {
    $('#<?php //echo $table_id; ?> tfoot').hide(1000);
    $('#<?php //echo $table_id; ?> tbody').hide(1000);
});*/
</script>