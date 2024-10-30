<div class="portlet box purple">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-picture"></i><b><?php echo $title; ?> (<?php echo $bank_reconciled_count . "/" . count($statement_data['records']); ?>)</b>
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
                    <th width="1%"><b>No</b></th>
                    <th width="5%"><b>Date</b></th>
                    <th width="5%"><b>Transaction</b></th>
                    <th width="5%"><b>Check</b></th>
                    <th width="40%"><b>Description</b></th>
                    <th width="10%"><b>Amount</b></th>
                    <?php if (!$is_locked[0]->islocked): ?>
                    <th width="1%"><b>Void</b></th>
                    <?php endif; ?>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th width="1%"><b>No</b></th>
                    <th width="5%"><b>Date</b></th>
                    <th width="5%"><b>Transaction</b></th>
                    <th width="5%"><b>Check</b></th>
                    <th width="40%"><b>Description</b></th>
                    <th width="10%"><b>Amount</b></th>
                    <?php if (!$is_locked[0]->islocked): ?>
                    <th width="1%"><b>Void</b></th>
                    <?php endif; ?>
                </tr>
            </tfoot>
            <tbody>
                <?php
                if (isset($statement_data['records']) && !empty($statement_data['records'])) {
                    $key = 0;
                    foreach ($statement_data['records'] as $row) {

                        if($is_previous == 1 && $row->is_reconcile == 1)
                            $reconciledClass = "blue-reconciled-class";
                        elseif($is_previous == 1)
                            $reconciledClass = "";
                        else
                            $reconciledClass = getLedgerCompareReconciledClass($row);

                        $borderClass = '';
                        if($row->with_point_diff == 1)
                        {
                            $borderClass = 'with-point-diff';
                        }
                        ?>
                        <tr class="<?php echo $reconciledClass." ".$borderClass; ?>" id="bank-<?php echo $row->id ?>">
                            <td><?php echo ++$key; ?></td>
                            <td><?php echo date('m/d/Y', strtotime($row->date)); ?></td>
                            <td><?php echo $row->transaction; ?></td>
                            <td><?php echo $row->check_num; ?></td>
                            <td><?php echo $row->description; ?></td>
                            <?php
                            if ($row->is_reconcile && $row->is_void == 0) {
                                ?>
                                <td>
                                    <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="Show Respective Reconciled Entries" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $row->ledger_statement_id ?>" type="<?php echo ($row->check_num && strtolower($row->description) == "check") ? 'check' : 'bank'; ?>"  reconciliation-type = "<?php echo $row->reconcile_type ?>">
                                    <?php echo isset($row->transaction_type) && $row->transaction_type == 'credit' ? "<span class='text-success'>" . $row->amount . "</span>" : "<span class='text-danger'>" . $row->amount . "</span>"; ?>
                                    </a>
                                    <?php if (!$is_locked[0]->islocked): ?>
                                        <a title ='Unreconciled Entry' href='javascript:void(0);' onclick="show_unreconcile_modal(<?php echo "[" . $row->ledger_statement_id . "]"; ?>,<?php echo "[" . $row->id . "]"; ?>, '<?php echo $row->reconcile_type; ?>');">U</a>
                                    <?php endif; ?>
                                </td>
                                <?php
                                } else {
                                ?>
                                <td>
                                <?php
                                echo isset($row->transaction_type) && $row->transaction_type == 'credit' ? "<span class='text-success'>" . $row->amount . "</span>" : "<span class='text-danger'>" . $row->amount . "</span>";
                                ?>
                                </td>
                                <?php
                            }
                            ?>
                            <?php
                            if (!$is_locked[0]->islocked)
                            {
                                $disabled = '';
                                if ($row->is_reconcile == 1 && $row->is_void == 0) {
                                    $disabled = 'disabled';
                                }
                                ?>
                                <td><input type="checkbox" <?php echo $disabled; ?> name="void_ledger_statement" value="<?php echo $row->id ?>" class="ledger-checkbox" onclick="void_bank_entry(this);" <?php echo $row->is_void == 1 ? "checked" : ""; ?>></td>
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
function getLedgerCompareReconciledClass($bankData)
{
    global $previous_reconciled_ledger;
    global $next_reconciled_ledger;

    global $previous_reconciled_checkbook_numbers;
    global $next_reconciled_checkbook_numbers;

    $previous_reconciled_ledger_ids_ary = explode(",", $previous_reconciled_ledger[0]->reconciledids);
    $next_reconciled_ledger_ids_ary     = explode(",", $next_reconciled_ledger[0]->reconciledids);

    $previous_reconciled_checked_ary    = explode(",", $previous_reconciled_checkbook_numbers[0]->reconciled_checks);
    $next_reconciled_checked_ary        = explode(",", $next_reconciled_checkbook_numbers[0]->reconciled_checks);

    $reconciledClass = "";
    if ($bankData->is_void == 1) {
        $reconciledClass = 'gray-reconciled-class';
    } else if ($bankData->is_reconcile && (in_array($bankData->ledger_statement_id, $previous_reconciled_ledger_ids_ary) || in_array($bankData->check_num, $previous_reconciled_checked_ary) )) {
        $reconciledClass = 'blue-reconciled-class';
    } else if ($bankData->is_reconcile && (in_array($bankData->ledger_statement_id, $next_reconciled_ledger_ids_ary) || in_array($bankData->check_num, $next_reconciled_checked_ary) )) {
        $reconciledClass = 'orange-reconciled-class';
    } elseif ($bankData->is_reconcile) {
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
        $(this).html('<input type="text"  style="width:100%;" class="current-bank-entries-input" placeholder="Search ' + title + '" />');
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
        $('.current-bank-entries-input', this.footer()).on('keyup change clear', function () {
            if (that.search() !== this.value) {
                that
                        .search(this.value)
                        .draw();
            }
        });
    });
});
</script>