<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$month_arr = array(" ", "January", "February", "March", "April", "May", "Jun", "July", "August", "September", "October", "November", "December");
$splitsDataAry = [];
foreach ($splits_data as $_splits_data) {
    $splitsDataAry[$_splits_data->statement_id][] = $_splits_data->description;
}
$ledger_month = isset($ledger->month) ? $ledger->month : '';
$ledger_year = isset($ledger->year) ? $ledger->year : '';
$ledger_storekey = isset($ledger->store_key) ? $ledger->store_key : '';
// echo "<pre>";
// print_r($ledger);
// exit;

$isImpoundSection       = 1;
$isCommentSection       = 0;
$isImpoundExtraSection  = 0;
$impoundExtraCount      = count($impound_extra);
$impoundExtraDisplayed  = -1;
$splitCount             = 0;
$i                      = 0;
$totalGeneralCredits    = 0;
$totalGeneralDebits     = 0;
$totalDcp               = 0;
$totalPayrollNet        = 0;
$totalPayrollGross      = 0;
$totalDonut             = 0;
$totalRoy               = 0;
$totalDean              = 0;
$totalCreditExtraEntriesFromImportAmtTotal = 0;
$totalFinalCredits      = 0;
$totalFinalDebits       = 0;
$totalFinalFood         = 0;

$ledgerBalance          = 0;
$ledgerCarryForward     = 0;
$checksTotal            = 0;
$totalCreditReceiveFrom = 0;

$extraCreditKey = -1;
$extraDebitKey  = -1;
$donutKey       = -1;
$dcpKey         = -1;
$netKey         = -1;
$grosskey       = -1;
$deanKey        = -1;
$royKey        = -1;
$netCount       = 0;
// previous reconciled ids ary
$previous_reconciled_ledger_ids_ary = explode(",", $previous_reconciled_ledger[0]->reconciledids);
$totalCreditExtraEntriesFromImport = count($credit_extra_entries_from_import);


?>
<style>
    #copy_info_modal .modal-dialog {
        width: 90%;
        height: 100%;
        margin: 0 20;
        padding: 0;
    }
    .download-ledger{
        float:right;
    }
    .bottomline{
        border-bottom: 1px solid #D3D3D3;
    }
    .mp0{
        margin: 0;
        padding: 0;
    }

    .mt20{
        margin-top: 20px;
    }

    .mb0{
        margin-bottom: 0;
    }


    @media print {
      body * {
        visibility: hidden;
      }
      .ledger_view_table, .ledger_view_table * {
        visibility: visible;
      }
      .ledger_view_table {
        position: absolute;
        left: 0;
        top: 0;
      }
      .ledger_view_table .add_attachment,
      .ledger_view_table .uncomment-modal,
      .ledger_view_table .comment-modal {
        display: none;
      }
      a[href]:after {
        content: none !important;
      }
    }
</style>

<!-- comment sucess message -->
<?php
$error = $this->session->flashdata('error');
if ($error) {
    ?>
    <div class="alert alert-danger alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
        <?php echo $this->session->flashdata('error'); ?>
    </div>
<?php } ?>
<?php
$success = $this->session->flashdata('success');
if ($success) {
    ?>
    <div class="alert alert-success alert-dismissable">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
    <?php echo $this->session->flashdata('success'); ?>
    </div>
    <?php } ?>

<!-- BEGIN EXAMPLE TABLE PORTLET-->

<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-picture"></i><b><?php echo $title; ?></b>
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
            <a href="javascript:;" class="fullscreen" data-original-title="" title=""></a>
        </div>
    </div>
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-hover mb0 ledger_view_table">
                    <thead>
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <th colspan="6">
                                <b>LEDGER STATEMENT</b>
                                <div class="pull-right">
                                    <a href="<?php echo base_url('statement/download_ledger/' . $ledger_id); ?>" title="Export to Excel" class="btn btn-default btn-circle btn-sm"><i class="fa fa-file-excel-o"></i> Excel </a>
                                    <a onclick="window.print();" title="Print PDF" class="btn btn-default btn-circle btn-sm"><i class="fa fa-file-pdf-o"></i> PDF </a>
                                    <a href="<?php echo base_url('statement/copy_info/' . $ledger_id); ?>" class="btn blue" id="copy_info" data-toggle="modal" data-target="#copy_info_modal">Copy</a>
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <th><b>STORE #</b></th>
                            <th><b><?php echo isset($ledger->store_key) ? $ledger->store_key : '' ?></b></th>
                            <th><b>Month</b></th>
                            <th><b><?php echo isset($ledger->month) ? $month_arr[$ledger->month] : '' ?></b></th>
                            <th><b id="ledger_balance_top"><?php echo isset($ledger->ledger_balance) ? number_format($ledger->ledger_balance, 2) : 0 ?></b></th>
                            <th><b>Balance</b></th>
                        </tr>
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <th><b>CREDITS</b></th>
                            <th><b>DOLLAR AMT.</b></th>
                            <th><b>DEBIT</b></th>
                            <th><b>DOLLAR AMT.</b></th>
                            <th><b>IMPOUND AMT.</b></th>
                            <th><b>W/E Dates</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $key=-1;
                        while ($i < 34) {
                        ?>
                            <tr>
                                <th><?php echo ++$i; ?></th>
                                <td>
                                    <?php
                                    if(isset($ledger_data[++$key]))
                                    {
                                        echo anchor('statement/view_attachement_info/' . $ledger_data[$key]->id, $ledger_data[$key]->credits != '' ? date('d F Y', strtotime($ledger_data[$key]->credits)) : '', array("target" => '_blank'));
                                        ?>
                                        <a  href="javascript:void(0);" class="add_attachment" data-id="<?php echo $ledger_data[$key]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($ledger_data[$key]->credit_desc); ?>" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                        <?php
                                        if ($ledger_data[$key]->credit_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        }
                                    }
                                    else
                                        echo "";
                                    ?>
                                </td>

                                <?php
                                if (isset($ledger_data[$key])) {
                                    if ($ledger_data[$key]->credit_reconcile) {
                                        ?>
                                        <td class="reconciled-class" id="ledger-<?php echo $ledger_data[$key]->id ?>">
                                            <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $ledger_data[$key]->bank_statement_id ?>" reconciliation-type = "<?php echo $ledger_data[$key]->reconcile_type ?>">
                                                <?php
                                                $amount = $ledger_data[$key]->credits_amt != '' ? ($ledger_data[$key]->credits_amt) : '';
                                                echo $amount;
                                                ?>
                                            </a>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td id="ledger-<?php echo $ledger_data[$key]->id ?>">
                                            <?php
                                            $amount = $ledger_data[$key]->credits_amt != '' ? ($ledger_data[$key]->credits_amt) : '';
                                            echo $amount;
                                            ?>
                                        </td>
                                        <?php
                                    }
                                    $totalGeneralCredits += $amount;
                                }
                                else
                                {
                                    ?>
                                    <td></td>
                                    <?php
                                }
                                ?>
                                <td>
                                    <?php
                                    if (isset($ledger_data[$key]))
                                    {
                                        if ($ledger_data[$key]->debits != '') {
                                            echo anchor('statement/view_attachement_info/' . $ledger_data[$key]->db_debit_id, str_replace(",", "<br>", $ledger_data[$key]->debits), array("target" => '_blank'));
                                        }
                                        ?>
                                        <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $ledger_data[$key]->db_debit_id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($ledger_data[$key]->debits); ?>" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                        <?php
                                        if ($ledger_data[$key]->debit_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        }
                                        ?>
                                        <?php if (isset($splitsDataAry[$ledger_data[$key]->db_debit_id])) : ?>
                                            <a href="javascript:void(0)" class="uncomment-modal"  data-id="statementid_<?php echo $ledger_data[$key]->db_debit_id ?>" data-toggle="modal" data-target="#unassign-comment-modal">    <i class="fa  fa-comments pull-right" title="View Comments"></i></a>
                                        <?php endif; ?>
                                        <a href="javascript:void(0)" class="comment-modal" data-id="assignstatementid_<?php echo $ledger_data[$key]->db_debit_id ?>" data-toggle="modal" data-target="#comment-modal">    <i class="fa  fa-comments-o pull-right" title="Assign Comments"></i></a>
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                        <td></td>
                                        <?php
                                    }
                                    ?>
                                </td>
                                <?php
                                if (isset($ledger_data[$key]))
                                {
                                    if ($ledger_data[$key]->debit_reconcile) {
                                        ?>
                                        <td class="reconciled-class" id="ledger-<?php echo $ledger_data[$key]->db_debit_id ?>">
                                            <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $ledger_data[$key]->debit_bank_statement_id ?>" reconciliation-type = "<?php echo $ledger_data[$key]->debit_reconcile_type ?>">
                                                <?php echo $damount = $ledger_data[$key]->debit_amt != '' ? ($ledger_data[$key]->debit_amt) : ''; ?>
                                            </a>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td id="ledger-<?php echo $ledger_data[$key]->db_debit_id ?>">
                                            <?php echo $damount = $ledger_data[$key]->debit_amt != '' ? ($ledger_data[$key]->debit_amt) : ''; ?>
                                        </td>
                                        <?php
                                    }
                                    $totalGeneralDebits += (float)$damount;
                                }
                                else
                                {
                                    ?>
                                    <td></td>
                                    <?php
                                }
                                ?>
                                <?php
                                if (isset($ledger_data[$key]))
                                {
                                    if (isset($splitsDataAry[$ledger_data[$key]->db_debit_id]))
                                        $statementId = $ledger_data[$key]->db_debit_id;

                                    if ($isImpoundSection == 0 && $isImpoundExtraSection == 0 && $isCommentSection == 1 && ($statementId == $ledger_data[$key]->db_debit_id)) {
                                        $splitCount = 0;
                                    }

                                    if ($ledger_data[$key]->we_dates != '') {
                                        $isImpoundSection = 1;
                                        if ($ledger_data[$key]->document_reconcile) {
                                            ?>
                                            <td class="reconciled-class" id="ledger-<?php echo $ledger_data[$key]->db_impound_id ?>">
                                                <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $ledger_data[$key]->impound_bank_statement_id ?>" reconciliation-type = "<?php echo $ledger_data[$key]->impound_reconcile_type ?>">
                                                    <?php echo ($ledger_data[$key]->impound_amt != '') ? ($ledger_data[$key]->impound_amt) : ''; ?>
                                                </a>
                                            </td>
                                            <?php
                                        } else {
                                            ?>
                                            <td id="ledger-<?php echo $ledger_data[$key]->db_impound_id ?>">
                                                <?php echo ($ledger_data[$key]->impound_amt != '') ? ($ledger_data[$key]->impound_amt) : ''; ?>
                                            </td>
                                            <?php
                                        }
                                        ?>
                                        <td>
                                            <?php echo isset($ledger_data[$key]->we_dates) ? anchor('statement/view_attachement_info/' . $ledger_data[$key]->impound_id, date('d F', strtotime($ledger_data[$key]->we_dates)), array("target" => '_blank')) : ''; ?>
                                            <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $ledger_data[$key]->impound_id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($ledger_data[$key]->impound_desc); ?>" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                            <?php if ($ledger_data[$key]->impound_attachment > 0) {
                                                ?>
                                                <i class="fa fa-paperclip pull-right"></i>
                                                <?php
                                            }
                                            ?>
                                        </td>
                                        <?php
                                    } else if (isset($impound_extra[++$impoundExtraDisplayed]->debit_amt) && $impoundExtraDisplayed <= $impoundExtraCount) {
                                        $isImpoundExtraSection = 1;
                                        ?>
                                        <td><?php echo ($impound_extra[$impoundExtraDisplayed]->transaction_type == 'debit') ? $impound_extra[$impoundExtraDisplayed]->debit_amt : $impound_extra[$impoundExtraDisplayed]->credit_amt; ?></td>
                                        <td><?php echo isset($impound_extra[$impoundExtraDisplayed]->credit_date) ? date('d F', strtotime($impound_extra[$impoundExtraDisplayed]->credit_date)) : ''; ?></td>
                                        <?php
                                    } else if ($isCommentSection != 1 && $isImpoundExtraSection == 1) {
                                        $isCommentSection = 1;
                                        $isImpoundSection = 0;
                                        echo '<td colspan = 2>';
                                        echo "<b>Comments</b>";
                                        echo '</td>';
                                        $statementId = $ledger_data[$key]->id;
                                    } else if ($isImpoundSection == 0 && $isCommentSection == 1 && isset($splitsDataAry[$statementId]) && isset($splitsDataAry[$statementId][$splitCount]) && $splitsDataAry[$statementId][$splitCount] != '') {
                                        echo '<td  colspan = 2>';
                                        echo $splitsDataAry[$statementId][$splitCount];
                                        echo '</td>';
                                        $splitCount++;
                                    } else {
                                        echo '<td>';
                                        echo '</td>';
                                        ?>
                                        <td>
                                            <?php echo ($ledger_data[$key]->impound_amt != '') ? ($ledger_data[$key]->impound_amt) : ''; ?>
                                        </td>
                                        <?php
                                    }
                                }
                                else
                                {
                                    ?>
                                    <td></td>
                                    <?php
                                }
                                ?>
                            </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <td><b>Total:</b></td>
                            <td>
                                <?php
                                echo "<b>" . $totalGeneralCredits . "</b>";
                                $totalFinalCredits += $totalGeneralCredits;
                                ?>
                            </td>
                            <td><?php echo isset($debit_extra_entries[++$extraDebitKey]->description) ? $debit_extra_entries[$extraDebitKey]->description : ''; ?></td>
                            <?php
                            $damount = isset($debit_extra_entries[$extraDebitKey]->debit_amt) ? $debit_extra_entries[$extraDebitKey]->debit_amt : '';
                            if (isset($debit_extra_entries[$extraDebitKey]) && $debit_extra_entries[$extraDebitKey]->is_reconcile) {
                                ?>
                                <td class="reconciled-class" id="ledger-<?php echo $debit_extra_entries[$extraDebitKey]->id ?>">
                                    <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $debit_extra_entries[$extraDebitKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $debit_extra_entries[$extraDebitKey]->reconcile_type ?>">
                                        <?php echo $damount; ?>
                                    </a>
                                </td>
                                <?php
                            } else {
                                ?>
                                <td id="ledger-<?php echo isset($debit_extra_entries[$extraDebitKey]) ? $debit_extra_entries[$extraDebitKey]->id : '' ?>">
                                    <?php echo $damount; ?>
                                </td>
                                <?php
                            }
                            $totalGeneralDebits += (float) $damount;
                            ?>
                            <td colspan="2"><b>DONUT PURCHASES FROM CML</b></td>
                        </tr>
                        <?php
                        //Donut section starts
                        while ($i < 41) {
                        ?>
                            <tr>
                                <th><?php echo ++$i; ?></th>
                                <td>
                                    <?php
                                    if(isset($credit_extra_entries[++$extraCreditKey])){
                                        echo $credit_extra_entries[$extraCreditKey]->description;
                                        ?>
                                        <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $credit_extra_entries[$extraCreditKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($credit_extra_entries[$extraCreditKey]->description); ?>" onclick="display_attachment(this)">
                                            <span class="fa fa-upload pull-right"></span>
                                        </a>
                                        <?php if ($credit_extra_entries[$extraCreditKey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        } ?>
                                    <?php
                                    }
                                  
                                    else{
                                        echo "";
                                    }?>
                                </td>
                                <?php
                                if(isset($credit_extra_entries[$extraCreditKey]))
                                {
                                    $creditExtraAmt = isset($credit_extra_entries[$extraCreditKey]->credit_amt) ? $credit_extra_entries[$extraCreditKey]->credit_amt : '';
                                    if (isset($credit_extra_entries[$extraCreditKey]) && $credit_extra_entries[$extraCreditKey]->is_reconcile) {
                                        ?>
                                        <td class="reconciled-class" id="ledger-<?php echo $credit_extra_entries[$extraCreditKey]->id ?>">
                                            <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $credit_extra_entries[$extraCreditKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $credit_extra_entries[$extraCreditKey]->reconcile_type ?>">
                                                <?php echo $creditExtraAmt; ?>
                                            </a>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td id="ledger-<?php echo isset($credit_extra_entries[$extraCreditKey]) ? $credit_extra_entries[$extraCreditKey]->id : '' ?>">
                                            <?php echo $creditExtraAmt; ?>
                                        </td>
                                        <?php
                                    }
                                    $totalCreditExtraEntriesFromImportAmtTotal += (float) $creditExtraAmt;
                                }
                                else
                                {
                                    ?>
                                    <td></td>
                                    <?php
                                }
                                ?>
                                <td>
                                    <?php echo isset($debit_extra_entries[++$extraDebitKey]->description) ? $debit_extra_entries[$extraDebitKey]->description : ''; ?>
                                </td>
                                <?php
                                if(isset($debit_extra_entries[$extraDebitKey]))
                                {
                                    $debitExtraAmt = isset($debit_extra_entries[$extraDebitKey]->debit_amt) ? $debit_extra_entries[$extraDebitKey]->debit_amt : '';
                                    if (isset($debit_extra_entries[$extraDebitKey]) && $debit_extra_entries[$extraDebitKey]->is_reconcile) {
                                        ?>
                                        <td class="reconciled-class" id="ledger-<?php echo $debit_extra_entries[$extraDebitKey]->id ?>">
                                            <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $debit_extra_entries[$extraDebitKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $debit_extra_entries[$extraDebitKey]->reconcile_type ?>">
                                                <?php echo $debitExtraAmt; ?>
                                            </a>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td id="ledger-<?php echo isset($debit_extra_entries[$extraDebitKey]) ? $debit_extra_entries[$extraDebitKey]->id : '' ?>">
                                            <?php echo $debitExtraAmt; ?>
                                        </td>
                                        <?php
                                    }
                                    $totalGeneralDebits += (float) $debitExtraAmt;
                                }
                                else
                                {
                                    ?>
                                    <td></td>
                                    <?php
                                }
                                if(isset($donut_data[++$donutKey]))
                                {
                                    ?>
                                    <td>
                                        <?php echo isset($donut_data[$donutKey]->credit_date) ? anchor('statement/view_attachement_info/' . $donut_data[$donutKey]->id, date('d F', strtotime($donut_data[$donutKey]->credit_date)), array("target" => '_blank')) : ''; ?>
                                        <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $donut_data[$donutKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($donut_data[$donutKey]->description); ?>" onclick="display_attachment(this)">
                                            <span class="fa fa-upload pull-right"></span>
                                        </a>
                                        <?php if ($donut_data[$donutKey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <?php
                                    if ($donut_data[$donutKey]->is_reconcile) {
                                        ?>
                                        <td class="reconciled-class" id="ledger-<?php echo $donut_data[$donutKey]->id ?>">
                                            <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $donut_data[$donutKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $donut_data[$donutKey]->reconcile_type ?>">
                                                <?php echo $amount = $donut_data[$donutKey]->debit_amt; ?>
                                            </a>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td id="ledger-<?php echo $donut_data[$donutKey]->id ?>">
                                            <?php
                                            echo $amount = $donut_data[$donutKey]->debit_amt;
                                            ?>
                                        </td>
                                        <?php
                                    }
                                    $totalDonut += $amount;
                                }
                                else
                                {
                                    ?>
                                    <td></td>
                                    <?php
                                }
                                ?>
                            </tr>
                        <?php
                        }
                        ?>
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <td>
                                <?php
                                if(isset($credit_extra_entries[++$extraCreditKey])){
                                    echo $credit_extra_entries[$extraCreditKey]->description;
                                    ?>
                                    <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $credit_extra_entries[$extraCreditKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($credit_extra_entries[$extraCreditKey]->description); ?>" onclick="display_attachment(this)">
                                            <span class="fa fa-upload pull-right"></span>
                                        </a>
                                        <?php if ($credit_extra_entries[$extraCreditKey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        } ?>
                                <?php
                                }
                                else
                                    echo "";
                                ?>
                            </td>
                            <?php
                            //Donut total
                            if(isset($credit_extra_entries[$extraCreditKey]))
                            {
                                $creditExtraAmt = isset($credit_extra_entries[$extraCreditKey]->credit_amt) ? $credit_extra_entries[$extraCreditKey]->credit_amt : '';
                                if (isset($credit_extra_entries[$extraCreditKey]) && $credit_extra_entries[$extraCreditKey]->is_reconcile) {
                                    ?>
                                    <td class="reconciled-class" id="ledger-<?php echo $credit_extra_entries[$extraCreditKey]->id ?>">
                                        <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $credit_extra_entries[$extraCreditKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $credit_extra_entries[$extraCreditKey]->reconcile_type ?>">
                                            <?php echo $creditExtraAmt; ?>
                                        </a>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td id="ledger-<?php echo isset($credit_extra_entries[$extraCreditKey]) ? $credit_extra_entries[$extraCreditKey]->id : '' ?>">
                                        <?php echo $creditExtraAmt; ?>
                                    </td>
                                    <?php
                                }
                                $totalCreditExtraEntriesFromImportAmtTotal += (float) $creditExtraAmt;
                            }
                            else
                            {
                                ?>
                                <td></td>
                                <?php
                            }
                            ?>
                            <td>
                                <?php echo isset($debit_extra_entries[++$extraDebitKey]->description) ? $debit_extra_entries[$extraDebitKey]->description : ''; ?>
                            </td>
                            <?php
                            if (isset($debit_extra_entries[$extraDebitKey]))
                            {
                                $debitExtraAmt = isset($debit_extra_entries[$extraDebitKey]->debit_amt) ? $debit_extra_entries[$extraDebitKey]->debit_amt : '';
                                if (isset($debit_extra_entries[$extraDebitKey]) && $debit_extra_entries[$extraDebitKey]->is_reconcile) {
                                    ?>
                                    <td class="reconciled-class" id="ledger-<?php echo $debit_extra_entries[$extraDebitKey]->id ?>">
                                        <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $debit_extra_entries[$extraDebitKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $debit_extra_entries[$extraDebitKey]->reconcile_type ?>">
                                            <?php echo $debitExtraAmt; ?>
                                        </a>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td id="ledger-<?php echo isset($debit_extra_entries[$extraDebitKey]) ? $debit_extra_entries[$extraDebitKey]->id : '' ?>">
                                        <?php echo $debitExtraAmt; ?>
                                    </td>
                                    <?php
                                }
                                $totalGeneralDebits += (float) $debitExtraAmt;
                            }
                            else
                            {
                                ?>
                                <td></td>
                                <?php
                            }
                            ?>
                            <td><b>Total:</b></td>
                            <td>
                                <b>
                                    <?php
                                        echo $totalDonut;
                                        $totalFinalFood += $totalDonut;
                                    ?>
                                </b>
                            </td>
                        </tr>
                        <!-- DCP Title -->
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <td>
                                <?php
                                if(isset($credit_extra_entries[++$extraCreditKey])){
                                    echo $credit_extra_entries[$extraCreditKey]->description;
                                    ?>
                                    <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $credit_extra_entries[$extraCreditKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($credit_extra_entries[$extraCreditKey]->description); ?>" onclick="display_attachment(this)">
                                            <span class="fa fa-upload pull-right"></span>
                                        </a>
                                        <?php if ($credit_extra_entries[$extraCreditKey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        } ?>
                                    <?php
                                }
                                else
                                    echo "";
                                ?>
                            </td>
                            <?php
                            if(isset($credit_extra_entries[$extraCreditKey]))
                            {
                                $creditExtraAmt = isset($credit_extra_entries[$extraCreditKey]->credit_amt) ? $credit_extra_entries[$extraCreditKey]->credit_amt : '';
                                if (isset($credit_extra_entries[$extraCreditKey]) && $credit_extra_entries[$extraCreditKey]->is_reconcile) {
                                    ?>
                                    <td class="reconciled-class" id="ledger-<?php echo $credit_extra_entries[$extraCreditKey]->id ?>">
                                        <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $credit_extra_entries[$extraCreditKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $credit_extra_entries[$extraCreditKey]->reconcile_type ?>">
                                        <?php echo $creditExtraAmt; ?>
                                        </a>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td id="ledger-<?php echo isset($credit_extra_entries[$extraCreditKey]) ? $credit_extra_entries[$extraCreditKey]->id : '' ?>">
                                    <?php echo $creditExtraAmt; ?>
                                    </td>
                                    <?php
                                }
                                $totalCreditExtraEntriesFromImportAmtTotal += (float) $creditExtraAmt;
                            }
                            else
                            {
                                ?>
                                <td></td>
                                <?php
                            }
                            ?>
                            <td><b>Total:</b></td>
                            <td><b>
                                    <?php
                                    echo $totalGeneralDebits;
                                    $totalFinalDebits += $totalGeneralDebits;
                                    ?>
                                </b></td>
                            <td><b>DCP EFTS:</b></td>
                            <td><b>DOLLAR AMT.</b></td>
                        </tr>

                        <!-- PAYROLL NET Title -->
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <td>
                                <?php
                                if(isset($credit_extra_entries[++$extraCreditKey])){
                                    echo $credit_extra_entries[$extraCreditKey]->description;
                                    ?>
                                    <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $credit_extra_entries[$extraCreditKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($credit_extra_entries[$extraCreditKey]->description); ?>" onclick="display_attachment(this)">
                                            <span class="fa fa-upload pull-right"></span>
                                        </a>
                                        <?php if ($credit_extra_entries[$extraCreditKey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        } ?>
                                    <?php
                                }
                                else
                                    echo "";
                                ?>
                            </td>
                            <?php
                            if (isset($credit_extra_entries[$extraCreditKey]))
                            {
                                $creditExtraAmt = isset($credit_extra_entries[$extraCreditKey]->credit_amt) ? $credit_extra_entries[$extraCreditKey]->credit_amt : '';
                                if (isset($credit_extra_entries[$extraCreditKey]) && $credit_extra_entries[$extraCreditKey]->is_reconcile) {
                                    ?>
                                    <td class="reconciled-class" id="ledger-<?php echo $credit_extra_entries[$extraCreditKey]->id ?>">
                                        <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $credit_extra_entries[$extraCreditKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $credit_extra_entries[$extraCreditKey]->reconcile_type ?>">
                                        <?php echo $creditExtraAmt; ?>
                                        </a>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td id="ledger-<?php echo isset($credit_extra_entries[$extraCreditKey]) ? $credit_extra_entries[$extraCreditKey]->id : '' ?>">
                                    <?php echo $creditExtraAmt; ?>
                                    </td>
                                    <?php
                                }
                                $totalCreditExtraEntriesFromImportAmtTotal += (float) $creditExtraAmt;
                            }
                            else
                            {
                                ?>
                                <td></td>
                                <?php
                            }
                            ?>
                            <td><b>PAYROLL NET</b></td>
                            <td><b>DOLLAR AMT.</b></td>
                                <?php
                                if(isset($dcp_data[++$dcpKey]))
                                {
                                    ?>
                                    <td>
                                    <?php
                                    echo isset($dcp_data[$dcpKey]->credit_date) ? anchor('statement/view_attachement_info/' . $dcp_data[$dcpKey]->id, date('d F', strtotime($dcp_data[$dcpKey]->credit_date)), array("target" => '_blank')) : '';
                                    if (isset($dcp_data[$dcpKey]->credit_date)) {
                                        ?>
                                        <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $dcp_data[$dcpKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($dcp_data[$dcpKey]->description); ?>" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                        <?php if ($dcp_data[$dcpKey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </td>
                                    <?php
                                    if (isset($dcp_data[$dcpKey]) && $dcp_data[$dcpKey]->is_reconcile) {
                                        ?>
                                        <td class="reconciled-class" id="ledger-<?php echo $dcp_data[$dcpKey]->id ?>">
                                            <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $dcp_data[$dcpKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $dcp_data[$dcpKey]->reconcile_type ?>">
                                            <?php echo $dcamount = $dcp_data[$dcpKey]->debit_amt; ?>
                                            </a>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td id="ledger-<?php echo isset($dcp_data[$dcpKey]) ? $dcp_data[$dcpKey]->id : '' ?>">
                                        <?php echo $dcamount = isset($dcp_data[$dcpKey]) ? $dcp_data[$dcpKey]->debit_amt : ''; ?>
                                        </td>
                                        <?php
                                    }
                                    if (strpos($dcamount, "(") !== false)
                                        $totalDcp -= (float) trim($dcamount, "()");
                                    else
                                        $totalDcp += (float) trim($dcamount, "()");

                                }
                                else
                                {
                                    ?>
                                    <td></td>
                                    <td></td>
                                    <?php
                                }
                                ?>
                        </tr>
                        <!-- PAYROLL NET DATA -->
                        <?php
                        while ($i < 49) {
                            ?>
                            <tr>
                                <th><?php echo ++$i; ?></th>
                                <td>
                                    <?php
                                    if(isset($credit_extra_entries[++$extraCreditKey])){
                                        echo $credit_extra_entries[$extraCreditKey]->description;
                                        ?>
                                        <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $credit_extra_entries[$extraCreditKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($credit_extra_entries[$extraCreditKey]->description); ?>" onclick="display_attachment(this)">
                                            <span class="fa fa-upload pull-right"></span>
                                        </a>
                                        <?php if ($credit_extra_entries[$extraCreditKey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        } ?>
                                    <?php
                                    }
                                    else
                                        echo "";
                                    ?>
                                </td>
                                <?php
                                if (isset($credit_extra_entries[$extraCreditKey]))
                                {
                                    $creditExtraAmt = isset($credit_extra_entries[$extraCreditKey]->credit_amt) ? $credit_extra_entries[$extraCreditKey]->credit_amt : '';
                                    if (isset($credit_extra_entries[$extraCreditKey]) && $credit_extra_entries[$extraCreditKey]->is_reconcile) {
                                        ?>
                                        <td class="reconciled-class" id="ledger-<?php echo $credit_extra_entries[$extraCreditKey]->id ?>">
                                            <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $credit_extra_entries[$extraCreditKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $credit_extra_entries[$extraCreditKey]->reconcile_type ?>">
                                            <?php echo $creditExtraAmt; ?>
                                            </a>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td id="ledger-<?php echo isset($credit_extra_entries[$extraCreditKey]) ? $credit_extra_entries[$extraCreditKey]->id : '' ?>">
                                        <?php echo $creditExtraAmt; ?>
                                        </td>
                                        <?php
                                    }
                                    $totalCreditExtraEntriesFromImportAmtTotal += (float) $creditExtraAmt;
                                }
                                else
                                {
                                    ?>
                                        <td></td>
                                    <?php
                                }
                                if(isset($payroll_net_data[++$netKey]))
                                {
                                    ?>
                                    <td>
                                        <?php echo isset($payroll_net_data[$netKey]->credit_date) ? anchor('statement/view_attachement_info/' . $payroll_net_data[$netKey]->id, date('d F', strtotime($payroll_net_data[$netKey]->credit_date)), array("target" => '_blank')) : ''; ?>
                                        <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $payroll_net_data[$netKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($payroll_net_data[$netKey]->description); ?>" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                        <?php if ($payroll_net_data[$netKey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <?php
                                    if ($payroll_net_data[$netKey]->is_reconcile) {
                                        ?>
                                        <td class="reconciled-class" id="ledger-<?php echo $payroll_net_data[$netKey]->id ?>">
                                            <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $payroll_net_data[$netKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $payroll_net_data[$netKey]->reconcile_type ?>">
                                            <?php echo $pnamount = $payroll_net_data[$netKey]->debit_amt; ?>
                                            </a>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td id="ledger-<?php echo $payroll_net_data[$netKey]->id ?>">
                                        <?php echo $pnamount = $payroll_net_data[$netKey]->debit_amt; ?>
                                        </td>
                                        <?php
                                    }
                                    $totalPayrollNet += $pnamount;
                                    $netCount++;
                                }
                                else
                                {
                                    ?>
                                    <td></td>
                                    <td></td>
                                    <?php
                                }
                                if(isset($dcp_data[++$dcpKey]->credit_date)) {
                                ?>
                                <td>
                                    <?php
                                    echo anchor('statement/view_attachement_info/' . $dcp_data[$dcpKey]->id, date('d F', strtotime($dcp_data[$dcpKey]->credit_date)), array("target" => '_blank'));
                                    ?>
                                    <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $dcp_data[$dcpKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($dcp_data[$dcpKey]->description); ?>" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                    <?php if ($dcp_data[$dcpKey]->total_attachment > 0) {
                                        ?>
                                        <i class="fa fa-paperclip pull-right"></i>
                                        <?php
                                    }
                                ?>
                                </td>
                                <?php
                                if (isset($dcp_data[$dcpKey]) && $dcp_data[$dcpKey]->is_reconcile) {
                                    ?>
                                    <td class="reconciled-class" id="ledger-<?php echo $dcp_data[$dcpKey]->id ?>">
                                        <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $dcp_data[$dcpKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $dcp_data[$dcpKey]->reconcile_type ?>">
                                        <?php echo $dcamount = $dcp_data[$dcpKey]->debit_amt; ?>
                                        </a>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td id="ledger-<?php echo isset($dcp_data[$dcpKey]) ? $dcp_data[$dcpKey]->id : '' ?>">
                                    <?php echo $dcamount = isset($dcp_data[$dcpKey]) ? $dcp_data[$dcpKey]->debit_amt : ''; ?>
                                    </td>
                                    <?php
                                }
                                if (strpos($dcamount, "(") !== false)
                                    $totalDcp -= (float) trim($dcamount, "()");
                                else
                                    $totalDcp += (float) trim($dcamount, "()");
                                ?>
                                <?php
                                } else {
                                    ?>
                                    <td></td>
                                    <td></td>
                                    <?php
                                }
                                ?>
                            </tr>
                            <?php
                        }
                        $grossCount = 0;
                        ?>
                        <!-- PAYROLL TOTAL -->
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <td>
                                <?php
                                if(isset($credit_extra_entries[++$extraCreditKey])){
                                    echo $credit_extra_entries[$extraCreditKey]->description;
                                    ?>
                                    <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $credit_extra_entries[$extraCreditKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($credit_extra_entries[$extraCreditKey]->description); ?>" onclick="display_attachment(this)">
                                            <span class="fa fa-upload pull-right"></span>
                                        </a>
                                        <?php if ($credit_extra_entries[$extraCreditKey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        } ?>
                                <?php
                                }
                                else
                                    echo "";
                                ?>
                            </td>
                            <?php
                            if(isset($credit_extra_entries[$extraCreditKey]))
                            {
                                $creditExtraAmt = isset($credit_extra_entries[$extraCreditKey]->credit_amt) ? $credit_extra_entries[$extraCreditKey]->credit_amt : '';
                                if (isset($credit_extra_entries[$extraCreditKey]) && $credit_extra_entries[$extraCreditKey]->is_reconcile) {
                                    ?>
                                    <td class="reconciled-class" id="ledger-<?php echo $credit_extra_entries[$extraCreditKey]->id ?>">
                                        <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $credit_extra_entries[$extraCreditKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $credit_extra_entries[$extraCreditKey]->reconcile_type ?>">
                                        <?php echo $creditExtraAmt; ?>
                                        </a>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td id="ledger-<?php echo isset($credit_extra_entries[$extraCreditKey]) ? $credit_extra_entries[$extraCreditKey]->id : '' ?>">
                                    <?php echo $creditExtraAmt; ?>
                                    </td>
                                    <?php
                                }
                                $totalCreditExtraEntriesFromImportAmtTotal += (float) $creditExtraAmt;
                            }
                            else
                            {
                                ?>
                                <td></td>
                                <?php
                            }
                            ?>
                            <td><b>Total:</b></td>
                            <td><b><?php
                                    echo $totalPayrollNet;
                                    $totalFinalDebits += $totalPayrollNet;
                                    ?></b></td>
                            <?php
                            if(isset($dcp_data[++$dcpKey]))
                            {
                                ?>
                                <td>
                                <?php
                                echo isset($dcp_data[$dcpKey]->credit_date) ? anchor('statement/view_attachement_info/' . $dcp_data[$dcpKey]->id, date('d F', strtotime($dcp_data[$dcpKey]->credit_date)), array("target" => '_blank')) : '';
                                if (isset($dcp_data[$dcpKey]->credit_date)) {
                                    ?>
                                    <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $dcp_data[$dcpKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($dcp_data[$dcpKey]->description); ?>" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                    <?php if ($dcp_data[$dcpKey]->total_attachment > 0) {
                                        ?>
                                        <i class="fa fa-paperclip pull-right"></i>
                                        <?php
                                    }
                                }
                                ?>
                                </td>
                                <?php
                                if (isset($dcp_data[$dcpKey]) && $dcp_data[$dcpKey]->is_reconcile) {
                                    ?>
                                    <td class="reconciled-class" id="ledger-<?php echo $dcp_data[$dcpKey]->id ?>">
                                        <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $dcp_data[$dcpKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $dcp_data[$dcpKey]->reconcile_type ?>">
                                        <?php echo $dcamount = $dcp_data[$dcpKey]->debit_amt; ?>
                                        </a>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td id="ledger-<?php echo isset($dcp_data[$dcpKey]) ? $dcp_data[$dcpKey]->id : '' ?>">
                                    <?php echo $dcamount = isset($dcp_data[$dcpKey]) ? $dcp_data[$dcpKey]->debit_amt : ''; ?>
                                    </td>
                                    <?php
                                }
                                if (strpos($dcamount, "(") !== false)
                                    $totalDcp -= (float) trim($dcamount, "()");
                                else
                                    $totalDcp += (float) trim($dcamount, "()");
                            }
                            else
                            {
                                ?>
                                <td></td>
                                <td></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <!-- PAYROLL GROSS Title -->
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <td>
                                <?php
                                if(isset($credit_extra_entries[++$extraCreditKey])){
                                    echo $credit_extra_entries[$extraCreditKey]->description;
                                    ?>
                                    <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $credit_extra_entries[$extraCreditKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($credit_extra_entries[$extraCreditKey]->description); ?>" onclick="display_attachment(this)">
                                            <span class="fa fa-upload pull-right"></span>
                                        </a>
                                        <?php if ($credit_extra_entries[$extraCreditKey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        } ?>
                                    <?php
                                }
                                else
                                    echo "";
                                ?>
                            </td>
                            <?php
                            if (isset($credit_extra_entries[$extraCreditKey]))
                            {
                                $creditExtraAmt = isset($credit_extra_entries[$extraCreditKey]->credit_amt) ? $credit_extra_entries[$extraCreditKey]->credit_amt : '';
                                if (isset($credit_extra_entries[$extraCreditKey]) && $credit_extra_entries[$extraCreditKey]->is_reconcile) {
                                    ?>
                                    <td class="reconciled-class" id="ledger-<?php echo $credit_extra_entries[$extraCreditKey]->id ?>">
                                        <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $credit_extra_entries[$extraCreditKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $credit_extra_entries[$extraCreditKey]->reconcile_type ?>">
                                        <?php echo $creditExtraAmt; ?>
                                        </a>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td id="ledger-<?php echo isset($credit_extra_entries[$extraCreditKey]) ? $credit_extra_entries[$extraCreditKey]->id : '' ?>">
                                    <?php echo $creditExtraAmt; ?>
                                    </td>
                                    <?php
                                }
                                $totalCreditExtraEntriesFromImportAmtTotal += (float) $creditExtraAmt;
                            }
                            else
                            {
                                ?>
                                <td></td>
                                <?php
                            }
                            ?>
                            <td><b>PAYROLL GROSS</b></td>
                            <td><b>DOLLAR AMT.</b></td>
                            <?php
                            if(isset($dcp_data[++$dcpKey]))
                            {
                                ?>
                                <td>
                                <?php
                                echo isset($dcp_data[$dcpKey]->credit_date) ? anchor('statement/view_attachement_info/' . $dcp_data[$dcpKey]->id, date('d F', strtotime($dcp_data[$dcpKey]->credit_date)), array("target" => '_blank')) : '';
                                if (isset($dcp_data[$dcpKey]->credit_date)) {
                                    ?>
                                    <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $dcp_data[$dcpKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($dcp_data[$dcpKey]->description); ?>" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                    <?php if ($dcp_data[$dcpKey]->total_attachment > 0) {
                                        ?>
                                        <i class="fa fa-paperclip pull-right"></i>
                                        <?php
                                    }
                                }
                                ?>
                                </td>
                                <?php
                                if (isset($dcp_data[$dcpKey]) && $dcp_data[$dcpKey]->is_reconcile) {
                                    ?>
                                    <td class="reconciled-class" id="ledger-<?php echo $dcp_data[$dcpKey]->id ?>">
                                        <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $dcp_data[$dcpKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $dcp_data[$dcpKey]->reconcile_type ?>">
                                        <?php echo $dcamount = $dcp_data[$dcpKey]->debit_amt; ?>
                                        </a>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td id="ledger-<?php echo isset($dcp_data[$dcpKey]) ? $dcp_data[$dcpKey]->id : '' ?>">
                                    <?php echo $dcamount = isset($dcp_data[$dcpKey]) ? $dcp_data[$dcpKey]->debit_amt : ''; ?>
                                    </td>
                                    <?php
                                }
                                if (strpos($dcamount, "(") !== false)
                                    $totalDcp -= (float) trim($dcamount, "()");
                                else
                                    $totalDcp += (float) trim($dcamount, "()");

                            }
                            else
                            {
                                ?>
                                <td></td>
                                <td></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <!-- PAYROLL GROSS DATA -->
                        <?php
                        while ($i < 56) {
                        ?>
                            <tr>
                                <th><?php echo ++$i; ?></th>
                                <?php
                                if(isset($credit_extra_entries[++$extraCreditKey]))
                                {
                                ?>
                                    <td>
                                        <?php echo $credit_extra_entries[$extraCreditKey]->description; ?>
                                            <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $credit_extra_entries[$extraCreditKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($credit_extra_entries[$extraCreditKey]->description); ?>" onclick="display_attachment(this)">
                                            <span class="fa fa-upload pull-right"></span>
                                        </a>
                                        <?php if ($credit_extra_entries[$extraCreditKey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        } ?>
                                    </td>
                                    <?php
                                    $creditExtraAmt = isset($credit_extra_entries[$extraCreditKey]->credit_amt) ? $credit_extra_entries[$extraCreditKey]->credit_amt : '';
                                    if (isset($credit_extra_entries[$extraCreditKey]) && $credit_extra_entries[$extraCreditKey]->is_reconcile) {
                                        ?>
                                        <td class="reconciled-class" id="ledger-<?php echo $credit_extra_entries[$extraCreditKey]->id ?>">
                                            <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $credit_extra_entries[$extraCreditKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $credit_extra_entries[$extraCreditKey]->reconcile_type ?>">
                                            <?php echo $creditExtraAmt; ?>
                                            </a>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td id="ledger-<?php echo isset($credit_extra_entries[$extraCreditKey]) ? $credit_extra_entries[$extraCreditKey]->id : '' ?>">
                                        <?php echo $creditExtraAmt; ?>
                                        </td>
                                        <?php
                                    }
                                    $totalCreditExtraEntriesFromImportAmtTotal += (float) $creditExtraAmt;
                                }
                                else
                                {
                                ?>
                                    <td></td>
                                    <td></td>
                                <?php
                                }
                                ?>
                                <?php
                                if (isset($payroll_gross_data[++$grosskey]))
                                {
                                ?>
                                    <td>
                                        <?php echo isset($payroll_gross_data[$grosskey]->credit_date) ? anchor('statement/view_attachement_info/' . $payroll_gross_data[$grosskey]->id, date('d F', strtotime($payroll_gross_data[$grosskey]->credit_date)), array("target" => '_blank')) : ''; ?>
                                        <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $payroll_gross_data[$grosskey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($payroll_gross_data[$grosskey]->description); ?>" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                        <?php if ($payroll_gross_data[$grosskey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <?php
                                    if (isset($payroll_gross_data[$grosskey]) && $payroll_gross_data[$grosskey]->is_reconcile) {
                                        ?>
                                        <td class="reconciled-class" id="ledger-<?php echo $payroll_gross_data[$grosskey]->id ?>">
                                            <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $payroll_gross_data[$grosskey]->bank_statement_id ?>" reconciliation-type = "<?php echo $payroll_gross_data[$grosskey]->reconcile_type ?>">
                                            <?php echo $pgamount = $payroll_gross_data[$grosskey]->debit_amt; ?>
                                            </a>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td id="ledger-<?php echo $payroll_gross_data[$grosskey]->id ?>">
                                        <?php echo $pgamount = $payroll_gross_data[$grosskey]->debit_amt; ?>
                                        </td>
                                        <?php
                                    }
                                    ?>
                                    <?php
                                    $grossCount++;
                                    $totalPayrollGross += (float) $pgamount;
                                } else {
                                    ?>
                                    <td></td>
                                    <td></td>
                                <?php
                                }
                                if(isset($dcp_data[++$dcpKey]))
                                {
                                    ?>
                                    <td>
                                    <?php
                                    echo isset($dcp_data[$dcpKey]->credit_date) ? anchor('statement/view_attachement_info/' . $dcp_data[$dcpKey]->id, date('d F', strtotime($dcp_data[$dcpKey]->credit_date)), array("target" => '_blank')) : '';
                                    if (isset($dcp_data[$dcpKey]->credit_date)) {
                                        ?>
                                        <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $dcp_data[$dcpKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($dcp_data[$dcpKey]->description); ?>" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                        <?php if ($dcp_data[$dcpKey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        }
                                    }
                                    ?>
                                    </td>
                                    <?php
                                    if (isset($dcp_data[$dcpKey]) && $dcp_data[$dcpKey]->is_reconcile) {
                                        ?>
                                        <td class="reconciled-class" id="ledger-<?php echo $dcp_data[$dcpKey]->id ?>">
                                            <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $dcp_data[$dcpKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $dcp_data[$dcpKey]->reconcile_type ?>">
                                            <?php echo $dcamount = $dcp_data[$dcpKey]->debit_amt; ?>
                                            </a>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td id="ledger-<?php echo isset($dcp_data[$dcpKey]) ? $dcp_data[$dcpKey]->id : '' ?>">
                                        <?php echo $dcamount = isset($dcp_data[$dcpKey]) ? $dcp_data[$dcpKey]->debit_amt : ''; ?>
                                        </td>
                                        <?php
                                    }
                                    if (strpos($dcamount, "(") !== false)
                                        $totalDcp -= (float) trim($dcamount, "()");
                                    else
                                        $totalDcp += (float) trim($dcamount, "()");

                                }
                                else
                                {
                                    ?>
                                    <td></td>
                                    <td></td>
                                    <?php
                                }
                                ?>
                            </tr>
                        <?php
                        }
                        ?>
                        <!-- PAYROLL GROSS TOTAL -->
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <?php
                            if(isset($credit_extra_entries[++$extraCreditKey]))
                            {
                            ?>
                                <td>
                                    <?php echo $credit_extra_entries[$extraCreditKey]->description; ?>
                                        <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $credit_extra_entries[$extraCreditKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($credit_extra_entries[$extraCreditKey]->description); ?>" onclick="display_attachment(this)">
                                            <span class="fa fa-upload pull-right"></span>
                                        </a>
                                        <?php if ($credit_extra_entries[$extraCreditKey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        } ?>
                                </td>
                                <?php
                                $creditExtraAmt = isset($credit_extra_entries[$extraCreditKey]->credit_amt) ? $credit_extra_entries[$extraCreditKey]->credit_amt : '';
                                if (isset($credit_extra_entries[$extraCreditKey]) && $credit_extra_entries[$extraCreditKey]->is_reconcile) {
                                    ?>
                                    <td class="reconciled-class" id="ledger-<?php echo $credit_extra_entries[$extraCreditKey]->id ?>">
                                        <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $credit_extra_entries[$extraCreditKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $credit_extra_entries[$extraCreditKey]->reconcile_type ?>">
                                        <?php echo $creditExtraAmt; ?>
                                        </a>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td id="ledger-<?php echo isset($credit_extra_entries[$extraCreditKey]) ? $credit_extra_entries[$extraCreditKey]->id : '' ?>">
                                    <?php echo $creditExtraAmt; ?>
                                    </td>
                                    <?php
                                }
                                $totalCreditExtraEntriesFromImportAmtTotal += (float) $creditExtraAmt;
                            }
                            else
                            {
                            ?>
                                <td></td>
                                <td></td>
                            <?php
                            }
                            ?>
                            <td><b>Total:</b></td>
                            <td><b><?php echo $totalPayrollGross; ?></b></td>
                            <?php
                            if(isset($dcp_data[++$dcpKey]))
                            {
                                ?>
                                <td>
                                <?php
                                echo isset($dcp_data[$dcpKey]->credit_date) ? anchor('statement/view_attachement_info/' . $dcp_data[$dcpKey]->id, date('d F', strtotime($dcp_data[$dcpKey]->credit_date)), array("target" => '_blank')) : '';
                                if (isset($dcp_data[$dcpKey]->credit_date)) {
                                    ?>
                                    <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $dcp_data[$dcpKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($dcp_data[$dcpKey]->description); ?>" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                    <?php if ($dcp_data[$dcpKey]->total_attachment > 0) {
                                        ?>
                                        <i class="fa fa-paperclip pull-right"></i>
                                        <?php
                                    }
                                }
                                ?>
                                </td>
                                <?php
                                if (isset($dcp_data[$dcpKey]) && $dcp_data[$dcpKey]->is_reconcile) {
                                    ?>
                                    <td class="reconciled-class" id="ledger-<?php echo $dcp_data[$dcpKey]->id ?>">
                                        <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $dcp_data[$dcpKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $dcp_data[$dcpKey]->reconcile_type ?>">
                                        <?php echo $dcamount = $dcp_data[$dcpKey]->debit_amt; ?>
                                        </a>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td id="ledger-<?php echo isset($dcp_data[$dcpKey]) ? $dcp_data[$dcpKey]->id : '' ?>">
                                    <?php echo $dcamount = isset($dcp_data[$dcpKey]) ? $dcp_data[$dcpKey]->debit_amt : ''; ?>
                                    </td>
                                    <?php
                                }
                                if (strpos($dcamount, "(") !== false)
                                    $totalDcp -= (float) trim($dcamount, "()");
                                else
                                    $totalDcp += (float) trim($dcamount, "()");

                            }
                            else
                            {
                                ?>
                                <td></td>
                                <td></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <!-- ROY TITLE -->
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <?php
                            if(isset($credit_extra_entries[++$extraCreditKey]))
                            {
                            ?>
                                <td>
                                    <?php echo $credit_extra_entries[$extraCreditKey]->description; ?>
                                        <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $credit_extra_entries[$extraCreditKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($credit_extra_entries[$extraCreditKey]->description); ?>" onclick="display_attachment(this)">
                                            <span class="fa fa-upload pull-right"></span>
                                        </a>
                                        <?php if ($credit_extra_entries[$extraCreditKey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        } ?>
                                </td>
                                <?php
                                $creditExtraAmt = isset($credit_extra_entries[$extraCreditKey]->credit_amt) ? $credit_extra_entries[$extraCreditKey]->credit_amt : '';
                                if (isset($credit_extra_entries[$extraCreditKey]) && $credit_extra_entries[$extraCreditKey]->is_reconcile) {
                                    ?>
                                    <td class="reconciled-class" id="ledger-<?php echo $credit_extra_entries[$extraCreditKey]->id ?>">
                                        <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $credit_extra_entries[$extraCreditKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $credit_extra_entries[$extraCreditKey]->reconcile_type ?>">
                                        <?php echo $creditExtraAmt; ?>
                                        </a>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td id="ledger-<?php echo isset($credit_extra_entries[$extraCreditKey]) ? $credit_extra_entries[$extraCreditKey]->id : '' ?>">
                                    <?php echo $creditExtraAmt; ?>
                                    </td>
                                    <?php
                                }
                                $totalCreditExtraEntriesFromImportAmtTotal += (float) $creditExtraAmt;
                            }
                            else
                            {
                            ?>
                                <td></td>
                                <td></td>
                            <?php
                            }
                            ?>
                            <td><b>ROY. & ADV. (First BR; Second Dunkin)</b></td>
                            <td><b>DOLLAR AMT.</b></td>
                            <?php
                            if(isset($dcp_data[++$dcpKey]))
                            {
                                ?>
                                <td>
                                <?php
                                echo isset($dcp_data[$dcpKey]->credit_date) ? anchor('statement/view_attachement_info/' . $dcp_data[$dcpKey]->id, date('d F', strtotime($dcp_data[$dcpKey]->credit_date)), array("target" => '_blank')) : '';
                                if (isset($dcp_data[$dcpKey]->credit_date)) {
                                    ?>
                                    <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $dcp_data[$dcpKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($dcp_data[$dcpKey]->description); ?>" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                    <?php if ($dcp_data[$dcpKey]->total_attachment > 0) {
                                        ?>
                                        <i class="fa fa-paperclip pull-right"></i>
                                        <?php
                                    }
                                }
                                ?>
                                </td>
                                <?php
                                if (isset($dcp_data[$dcpKey]) && $dcp_data[$dcpKey]->is_reconcile) {
                                    ?>
                                    <td class="reconciled-class" id="ledger-<?php echo $dcp_data[$dcpKey]->id ?>">
                                        <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $dcp_data[$dcpKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $dcp_data[$dcpKey]->reconcile_type ?>">
                                        <?php echo $dcamount = $dcp_data[$dcpKey]->debit_amt; ?>
                                        </a>
                                    </td>
                                    <?php
                                } else {
                                    ?>
                                    <td id="ledger-<?php echo isset($dcp_data[$dcpKey]) ? $dcp_data[$dcpKey]->id : '' ?>">
                                    <?php echo $dcamount = isset($dcp_data[$dcpKey]) ? $dcp_data[$dcpKey]->debit_amt : ''; ?>
                                    </td>
                                    <?php
                                }
                                if (strpos($dcamount, "(") !== false)
                                    $totalDcp -= (float) trim($dcamount, "()");
                                else
                                    $totalDcp += (float) trim($dcamount, "()");

                            }
                            else
                            {
                                ?>
                                <td></td>
                                <td></td>
                                <?php
                            }
                            ?>
                        </tr>
                        <?php
                        while($i < 68)
                        {
                            ?>
                            <tr>
                                <th><?php echo ++$i; ?></th>
                                <td>
                                    <?php
                                    if ($i < (69 - (count($credit_extra_entries_from_import) + 1))) {
                                        if(isset($credit_extra_entries[++$extraCreditKey])){
                                            echo $credit_extra_entries[$extraCreditKey]->description;
                                            ?>
                                        <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $credit_extra_entries[$extraCreditKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($credit_extra_entries[$extraCreditKey]->description); ?>" onclick="display_attachment(this)">
                                            <span class="fa fa-upload pull-right"></span>
                                        </a>
                                        <?php if ($credit_extra_entries[$extraCreditKey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        } ?>
                                            <?php
                                        }
                                        else
                                            echo "";
                                    } elseif($i==68) {
                                    ?>
                                    <b>Total:</b>
                                    <?php
                                    }  else {
                                        echo isset($credit_extra_entries_from_import[--$totalCreditExtraEntriesFromImport]) ? $credit_extra_entries_from_import[$totalCreditExtraEntriesFromImport]->description : '';
                                    }
                                    ?>
                                </td>
                                <?php
                                if ($i < (69 - (count($credit_extra_entries_from_import) + 1))) {
                                    $creditExtraAmt = isset($credit_extra_entries[$extraCreditKey]->credit_amt) ? $credit_extra_entries[$extraCreditKey]->credit_amt : '';
                                    if (isset($credit_extra_entries[$extraCreditKey]) && $credit_extra_entries[$extraCreditKey]->is_reconcile) {
                                        ?>
                                        <td class="reconciled-class" id="ledger-<?php echo $credit_extra_entries[$extraCreditKey]->id ?>">
                                            <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $credit_extra_entries[$extraCreditKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $credit_extra_entries[$extraCreditKey]->reconcile_type ?>">
                                            <?php echo $creditExtraAmt; ?>
                                            </a>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td id="ledger-<?php echo isset($credit_extra_entries[$extraCreditKey]) ? $credit_extra_entries[$extraCreditKey]->id : '' ?>">
                                        <?php echo $creditExtraAmt; ?>
                                        </td>
                                        <?php
                                    }
                                    $totalCreditExtraEntriesFromImportAmtTotal += (float) $creditExtraAmt;
                                } else {
                                    if($i == 68) {
                                    ?>
                                    <td>
                                        <?php
                                        echo '<b>' . $totalCreditExtraEntriesFromImportAmtTotal . '</b>';
                                        $totalFinalCredits += $totalCreditExtraEntriesFromImportAmtTotal;
                                        ?>
                                    </td>
                                    <?php
                                    } elseif (isset($credit_extra_entries_from_import[$totalCreditExtraEntriesFromImport]) && $credit_extra_entries_from_import[$totalCreditExtraEntriesFromImport]->is_reconcile) {
                                        ?>
                                        <td class="reconciled-class" id="ledger-<?php echo $credit_extra_entries_from_import[$totalCreditExtraEntriesFromImport]->id ?>">
                                            <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $credit_extra_entries_from_import[$totalCreditExtraEntriesFromImport]->bank_statement_id ?>" reconciliation-type = "<?php echo $credit_extra_entries_from_import[$totalCreditExtraEntriesFromImport]->reconcile_type ?>">
                                                <?php
                                                echo $amount = isset($credit_extra_entries_from_import[$totalCreditExtraEntriesFromImport]) ? $credit_extra_entries_from_import[$totalCreditExtraEntriesFromImport]->credit_amt : '';
                                                ?>
                                            </a>
                                        </td>
                                        <?php
                                        $totalCreditExtraEntriesFromImportAmtTotal += (float) $amount;
                                    } else {
                                        ?>
                                        <td id="ledger-<?php echo isset($credit_extra_entries_from_import[$totalCreditExtraEntriesFromImport]) ? $credit_extra_entries_from_import[$totalCreditExtraEntriesFromImport]->id : '' ?>">
                                            <?php
                                            echo $amount = isset($credit_extra_entries_from_import[$totalCreditExtraEntriesFromImport]) ? $credit_extra_entries_from_import[$totalCreditExtraEntriesFromImport]->credit_amt : '';
                                            ?>
                                        </td>
                                        <?php
                                        $totalCreditExtraEntriesFromImportAmtTotal += (float) $amount;
                                    }
                                }
                                ?>
                                <?php
                                if(isset($roy_data[++$royKey]))
                                {
                                ?>
                                    <td>
                                        <?php echo isset($roy_data[$royKey]->credit_date) ? anchor('statement/view_attachement_info/' . $roy_data[$royKey]->id, date('d F', strtotime($roy_data[$royKey]->credit_date)), array("target" => '_blank')) : ''; ?>
                                        <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $roy_data[$royKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($roy_data[$royKey]->description); ?>" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                        <?php if ($roy_data[$royKey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        }
                                        ?>
                                    </td>
                                    <?php
                                    if ($roy_data[$royKey]->is_reconcile) {
                                        ?>
                                        <td class="reconciled-class" id="ledger-<?php echo $roy_data[$royKey]->id ?>">
                                            <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $roy_data[$royKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $roy_data[$royKey]->reconcile_type ?>">
                                            <?php echo $rmount = $roy_data[$royKey]->debit_amt; ?>
                                            </a>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td id="ledger-<?php echo $roy_data[$royKey]->id ?>">
                                        <?php echo $rmount = $roy_data[$royKey]->debit_amt; ?>
                                        </td>
                                        <?php
                                    }
                                    if (strpos($rmount, "(") !== false)
                                        $totalRoy -= (float) trim($rmount, "()");
                                    else
                                        $totalRoy += (float) trim($rmount, "()");
                                }
                                else
                                {
                                    ?>
                                    <td></td>
                                    <td></td>
                                    <?php
                                }
                                ?>
                                <?php
                                if ($i < 62) {
                                    if(isset($dcp_data[++$dcpKey]))
                                    {
                                        ?>
                                        <td>
                                        <?php
                                        echo isset($dcp_data[$dcpKey]->credit_date) ? anchor('statement/view_attachement_info/' . $dcp_data[$dcpKey]->id, date('d F', strtotime($dcp_data[$dcpKey]->credit_date)), array("target" => '_blank')) : '';
                                        if (isset($dcp_data[$dcpKey]->credit_date)) {
                                            ?>
                                            <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $dcp_data[$dcpKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($dcp_data[$dcpKey]->description); ?>" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                            <?php if ($dcp_data[$dcpKey]->total_attachment > 0) {
                                                ?>
                                                <i class="fa fa-paperclip pull-right"></i>
                                                <?php
                                            }
                                        }
                                        ?>
                                        </td>
                                        <?php
                                        if (isset($dcp_data[$dcpKey]) && $dcp_data[$dcpKey]->is_reconcile) {
                                            ?>
                                            <td class="reconciled-class" id="ledger-<?php echo $dcp_data[$dcpKey]->id ?>">
                                                <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $dcp_data[$dcpKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $dcp_data[$dcpKey]->reconcile_type ?>">
                                                <?php echo $dcamount = $dcp_data[$dcpKey]->debit_amt; ?>
                                                </a>
                                            </td>
                                            <?php
                                        } else {
                                            ?>
                                            <td id="ledger-<?php echo isset($dcp_data[$dcpKey]) ? $dcp_data[$dcpKey]->id : '' ?>">
                                            <?php echo $dcamount = isset($dcp_data[$dcpKey]) ? $dcp_data[$dcpKey]->debit_amt : ''; ?>
                                            </td>
                                            <?php
                                        }
                                        if (strpos($dcamount, "(") !== false)
                                            $totalDcp -= (float) trim($dcamount, "()");
                                        else
                                            $totalDcp += (float) trim($dcamount, "()");

                                    }
                                    else
                                    {
                                        ?>
                                        <td></td>
                                        <td></td>
                                        <?php
                                    }


                                } elseif ($i == 62) {
                                    ?>
                                    <td><b>Total:</b></td>
                                    <td><b><?php
                                            echo $totalDcp;
                                            $totalFinalFood += $totalDcp;
                                            ?></b></td>
                                    <?php
                                } elseif ($i == 63) {
                                    ?>
                                    <td><b>DEAN FOODS</b></td>
                                    <td><b>DOLLAR AMT.</b></td>
                                    <?php
                                } elseif ($i > 63) {
                                    ?>
                                    <td>
                                        <?php
                                        if(isset($dean_foods[++$deanKey]->credit_date))
                                        {
                                        ?>
                                        <?php echo anchor('statement/view_attachement_info/' . $dean_foods[$deanKey]->id, date('d F', strtotime($dean_foods[$deanKey]->credit_date)), array("target" => '_blank'));
                                        ?>
                                        <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $dean_foods[$deanKey]->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="<?php echo trim($dean_foods[$deanKey]->description); ?>" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                        <?php if ($dean_foods[$deanKey]->total_attachment > 0) {
                                            ?>
                                            <i class="fa fa-paperclip pull-right"></i>
                                            <?php
                                        }
                                        ?>
                                        <?php
                                        }
                                        ?>
                                    </td>
                                    <?php
                                    if (isset($dean_foods[$deanKey]) && $dean_foods[$deanKey]->is_reconcile) {
                                        ?>
                                        <td class="reconciled-class" id="ledger-<?php echo $dean_foods[$deanKey]->id ?>">
                                            <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $dean_foods[$deanKey]->bank_statement_id ?>" reconciliation-type = "<?php echo $dean_foods[$deanKey]->reconcile_type ?>">
                                            <?php echo $dnamount = $dean_foods[$deanKey]->debit_amt; ?>
                                            </a>
                                        </td>
                                        <?php
                                    } else {
                                        ?>
                                        <td id="ledger-<?php echo isset($dean_foods[$deanKey]) ? $dean_foods[$deanKey]->id : ''; ?>">
                                        <?php echo $dnamount = isset($dean_foods[$deanKey]) ? $dean_foods[$deanKey]->debit_amt : ''; ?>
                                        </td>
                                        <?php
                                    }
                                    if (strpos($dnamount, "(") !== false)
                                        $totalDean -= (float) trim($dnamount, "()");
                                    else
                                        $totalDean += (float) trim($dnamount, "()");
                                    ?>
                                    <?php
                                } else {
                                    ?>
                                    <td></td>
                                    <td></td>
                                    <?php
                                }
                                ?>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <td><b>Credit Card Credits</b></td>
                            <?php
                            if (isset($credit_card_credits[0]) && $credit_card_credits[0]->is_reconcile) {
                                ?>
                                <td class="reconciled-class" id="ledger-<?php echo $credit_card_credits[0]->id ?>">
                                    <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $credit_card_credits[0]->bank_statement_id ?>" reconciliation-type = "<?php echo $credit_card_credits[0]->reconcile_type ?>">
                                    <?php echo $cfamount = $credit_card_credits[0]->credit_amt != '' ? ($credit_card_credits[0]->credit_amt) : ''; ?>
                                    </a>
                                </td>
                                <?php
                            } else {
                                ?>
                                <td id="ledger-<?php echo isset($credit_card_credits[0]) ? $credit_card_credits[0]->id : 0; ?>">
                                <?php echo $cfamount = isset($credit_card_credits[0]) && $credit_card_credits[0]->credit_amt != '' ? ($credit_card_credits[0]->credit_amt) : ''; ?>
                                </td>
                                <?php
                            }
                            $totalFinalCredits += (float)$cfamount;
                            ?>
                            <td><b>TOTAL:</b></td>
                            <td>
                                <b>
                                    <?php
                                    echo $totalRoy;
                                    $totalFinalDebits += $totalRoy;
                                    ?></b></td>
                            <td><b>TOTAL:</b></td>
                            <td><b><?php
                            echo $totalDean;
                            $totalFinalFood += $totalDean;
                            ?></b></td>
                        </tr>
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <td><b>TOTAL CREDITS</b></td>
                            <td><b><?php echo $totalFinalCredits; ?></b></td>
                            <td><b>TOTAL DEBITS</b></td>
                            <td><b><?php echo $totalFinalDebits; ?></b></td>
                            <td><b>TOTAL FOOD</b></td>
                            <td><b><?php echo $totalFinalFood; ?></b></td>
                        </tr>
                        <?php
                        $ledgerBalance = $totalFinalCredits - $totalFinalDebits - $totalFinalFood;
                        ?>
                        <script type="text/javascript">
                            jQuery("#ledger_balance_top").html("<?php echo ($ledgerBalance < 0) ? '<span class=red-color>('.$ledgerBalance * (-1).')</span>' : $ledgerBalance; ?>");
                        </script>
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <th  rowspan="2" colspan="6" style="text-align:center; vertical-align:middle">CHECKBOOK RECORD</th>
                        </tr>
                        <tr>
                            <th><?php echo ++$i; ?></th>
                        </tr>
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <th>Month</th>
                            <th><b><?php echo isset($ledger->month) ? $month_arr[$ledger->month] : '' ?></b></th>
                            <th>STORE#</th>
                            <th><b><?php echo isset($ledger->store_key) ? $ledger->store_key : '' ?></b></th>
                            <th>Balance C/F</th>
                            <th>
                                <?php
                                $ledgerCarryForward = isset($carry_forward_from_previous_ledger[0]) ? $carry_forward_from_previous_ledger[0]->ending_balance : $ledger->balance_cf;
                                ?>
                                <b><?php echo ($ledgerCarryForward < 0) ? '<span class=red-color>'.$ledgerCarryForward.'</span>': $ledgerCarryForward; ?></b>
                            </th>
                        </tr>
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <div class="col-md-8 mp0">
                    <table class="table table-bordered mb0">
                        <tbody>
                            <tr>
                                <th><?php echo ++$i; ?></th>
                                <th>Check Payable To</th>
                                <th>Check#</th>
                                <th>Memo</th>
                                <th>Amount</th>
                            </tr>
                            <?php
                            if (isset($checkbook_record)) {
                               
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
                                            <td class="reconciled-class <?php echo $_checkbook_record->is_void ? 'gray-reconciled-class' : ''; ?>" id="ledger-check-<?php echo $_checkbook_record->id ?>">
                                                <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" title="" data-content="Some content inside the popover" class="reconciled-info" reconciled-ids = "<?php echo $_checkbook_record->bank_statement_id ?>" reconciliation-type = "<?php echo $_checkbook_record->reconcile_type ?>">
                                                <?php echo $_checkbook_record->amount1 != '' ? ($_checkbook_record->amount1) : ''; ?>
                                                </a>
                                                 <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $_checkbook_record->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-source="checkbook" data-desc="<?php echo trim($_checkbook_record->memo); ?>" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                            <?php if ($_checkbook_record->total_attachment > 0) {
                                                ?>
                                                <i class="fa fa-paperclip pull-right"></i>
                                                <?php
                                            } ?>
                                            </td>
                                                <?php
                                            } else {
                                                ?>
                                            <td id="ledger-check-<?php echo $_checkbook_record->id ?>">
                                            <?php
                                            echo $checkAmount = $_checkbook_record->amount1 != '' ? ($_checkbook_record->amount1) : '';
                                            $checksTotal += (float)$checkAmount;
                                            ?>
                                                 <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $_checkbook_record->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-source="checkbook" data-desc="<?php echo trim($_checkbook_record->memo); ?>" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                            <?php if ($_checkbook_record->total_attachment > 0) {
                                                ?>
                                                <i class="fa fa-paperclip pull-right"></i>
                                                <?php
                                            } ?>

                                            </td>
                                            <?php
                                        }
                                        ?>
                                    </tr>
                                    <?php
                                }
                            }

                            while($i < 107)
                            {
                                ?>
                                <tr>
                                    <th><?php echo ++$i; ?></th>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-4 mp0">
                    <table class="table table-bordered mb0">
                        <tbody>
                            <tr>
                                <th>Credit Received From</th>
                                <th>Amount</th>
                            </tr>
                            <?php
                            if (isset($ledger_credit_received_from)) {
                                foreach ($ledger_credit_received_from as $_ledger_credit_received_from) {
                                ?>
                            <tr>
                                <td>
                                    <?php
                                    echo $creditReceivedFromLabel = isset($_ledger_credit_received_from->label) ? $_ledger_credit_received_from->label : ''
                                    ?>
                                </td>
                                <td>
                                    <?php
                                    if($creditReceivedFromLabel == 'LEDGER BALANCE')
                                    {
                                        echo $creditReceivedFromAmount = $ledgerBalance;
                                    }
                                    else
                                    {
                                        echo $creditReceivedFromAmount = $_ledger_credit_received_from->amount != '' ? ($_ledger_credit_received_from->amount) : '';
                                    }
                                    $totalCreditReceiveFrom += (float)$creditReceivedFromAmount;
                                    ?>
                                    <a href="javascript:void(0);" class="add_attachment" data-id="<?php echo $_ledger_credit_received_from->id; ?>" data-ledger-id ="<?php echo $ledger_id; ?>" data-desc="" data-source="credit_received_from" onclick="display_attachment(this)"><span class="fa fa-upload pull-right"></span></a>
                                            <?php if ($_ledger_credit_received_from->total_attachment > 0) {
                                                ?>
                                                <i class="fa fa-paperclip pull-right"></i>
                                                <?php
                                            } ?>
                                </td>
                            </tr>
                                <?php
                                }
                            }
                            $receivedCount = count($ledger_credit_received_from);
                            while($receivedCount <= 30)
                            {
                                ?>
                                <tr>
                                    <th>&nbsp;<?php ++$receivedCount; ?></th>
                                    <td>&nbsp;</td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <table class="table table-bordered mb0">
                    <tbody>
                        <tr>
                            <th><?php echo ++$i; ?></th>
                            <td width="30%"></td>
                            <td width="12%"></td>
                            <td width="12%"></td>
                            <td width="11%"></td>
                            <td width="25%"><b>Ending Balance</b></td>
                            <td width="10%">
                                <?php
                                $endingBalance = (float)$ledgerCarryForward - $checksTotal + $totalCreditReceiveFrom; ?>
                                <b><?php echo ($endingBalance < 0) ? '<span class=red-color>'.$endingBalance.'</span>': $endingBalance; ?></b>
                            </td>
                        </tr>
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
</script>
<div class="modal fade" id="add_attachment_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Add Attachment</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light portlet-fit portlet-form bordered">
                            <div class="portlet-body">
                                <!-- BEGIN FORM-->
                                <form action="<?php echo base_url('statement/upload_attachment'); ?>"method="post" id="form_add_attachment" class="form-horizontal" enctype="multipart/form-data">
                                    <div class="form-body">
                                        <div id="msg"></div>
                                        <input type="hidden" name="call_from" value="<?php echo $call_from; ?>"/>
                                        <input type="hidden" name="source" id="source" value="ledger"/>
                                        <input type="hidden" name="ledger_id" id="ledger_id"/>
                                        <input type="hidden" name="ledger_statement_id" id="ledger_statement_id"/>
                                        <input type="hidden" name="description_txt" id="description_txt"/>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Invoice
                                            </label>
                                            <div class="col-md-6">
                                                <input type="file" name="invoice_file" id="invoice_file"
                                                       class="form-control" required />
                                                <label id="uploaded_invoice_name"></label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Other Documents
                                            </label>
                                            <div class="col-md-6">
                                                <input type="file" name="doc_file[]" id="doc_file1"
                                                       class="form-control"/>
                                                <label id="uploaded_document1_name"></label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">&nbsp;
                                            </label>
                                            <div class="col-md-6">
                                                <input type="file" name="doc_file[]" id="doc_file2"
                                                       class="form-control"/>
                                                <label id="uploaded_document2_name"></label>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="control-label col-md-3">&nbsp;
                                            </label>
                                            <div class="col-md-6">
                                                <input type="file" name="doc_file[]" id="doc_file3"
                                                       class="form-control"/>
                                                <label id="uploaded_document3_name"></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <input type="submit" name="btn_submit" id="btn_submit" class="btn btn-primary" />
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade bs-modal-lg add-model" id="copy_info_modal" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
            <div class="modal-body">

            </div>
        </div>
    </div>
</div>
<!-- Assign comments -->
<div class="modal fade" id="comment-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title pull-left"><b>Assign Comments</b></h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-3 pull-right">
                            <button type="button" class="add-comment btn btn-primary " id="comment1">Add Comments</button>
                        </div>
                    </div>
                </div>
                <form role="form" method="post" name="assign-comment-form" id="assign-comment-form" action="<?php echo base_url("statement/add_ledger_comment") ?>">
                    <input type="hidden" name="call_from" value="<?php echo $call_from; ?>"/>
                    <input type="hidden" name="hd_comments" id="hd_comments" value="">
                    <input type="hidden" name="assign_hd_ids" id="assign_hd_ids" value="">
                    <input type="hidden" name="comment_ledger_id" value="<?php echo $ledger_id ?>">
                    <input type="hidden" name="comment_statement_id" id="comment_statement_id" value="">
                    <div class="listunassign_comments"></div>
                    <br/>
                    <div class="form-group">
                        <div id="append-comments"></div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary submit-comments">Assign</button>
            </div>
        </div>

    </div>
</div>
<!-- Un assign comments -->
<div class="modal fade" id="unassign-comment-modal" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><b>View Comments</b></h4>
            </div>
            <div class="modal-body" >
                <div class="unassign-comments"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary unassign-submit">Reset Comments</button>
            </div>
        </div>

    </div>
</div>
<script>
    var ledger_month = '<?php echo $ledger_month; ?>';
    var ledger_year = '<?php echo $ledger_year; ?>';
    var ledger_storekey = '<?php echo $ledger_storekey; ?>';
    function display_attachment(element) {
        var id = $(element).data("id");
        var l_id = $(element).data("ledger-id");
        var description = $(element).data("desc");
        var source = $(element).data("source");
        if(typeof source == 'undefined' || source == ''){
            source = 'ledger';
        }
        $("#ledger_id").val(l_id);
        $("#ledger_statement_id").val(id);
        $("#description_txt").val(description);
        $.ajax({
            url: '<?php echo base_url("statement/get_upload_details") ?>',
            type: 'POST',
            data: {
                'statement_id': id,
                'description': description,
            },
            success: function (data) {
                var Res = JSON.parse(data);
                if(Res.is_invoice == 1){
                    $("#invoice_file").hide();
                    $("#uploaded_invoice_name").html(Res.invoice_name);
                }else{
                    if(typeof Res.custom_invoice_name != 'undefined'){
                         $("#invoice_file").show();
                        $("#uploaded_invoice_name").html("(<b>Ex.</b> "+Res.custom_invoice_name+"_"+ledger_month+"_"+ledger_year+"_"+ledger_storekey+")");
                    }
                }
                if(Res.document_1 == 1){
                    $("#doc_file1").hide();
                    $("#uploaded_document1_name").html(Res.document_name_1);
                } else {
                      if(typeof Res.custom_document_name_1 != 'undefined'){
                       $("#doc_file1").show();
                       $("#uploaded_document1_name").html("(<b>Ex.</b> "+Res.custom_document_name_1+"_"+ledger_month+"_"+ledger_year+"_"+ledger_storekey+")");
                   }
                }
                if(Res.document_2 == 1){
                    $("#doc_file2").hide();
                    $("#uploaded_document2_name").html(Res.document_name_2);
                }else {
                    if(typeof Res.custom_document_name_2 != 'undefined'){
                       $("#doc_file2").show();
                       $("#uploaded_document2_name").html("(<b>Ex.</b> "+Res.custom_document_name_2+"_"+ledger_month+"_"+ledger_year+"_"+ledger_storekey+")");
                   }
                }
                if(Res.document_3 == 1){
                    $("#doc_file3").hide();
                    $("#uploaded_document3_name").html(Res.document_name_3);
                }else{
                    if(typeof Res.custom_document_name_3 != 'undefined'){
                       $("#doc_file3").show();
                       $("#uploaded_document3_name").html("(<b>Ex.</b> "+Res.custom_document_name_3+"_"+ledger_month+"_"+ledger_year+"_"+ledger_storekey+")");
                   }
                }
            }
        });
        $("#source").val(source);
        $("#add_attachment_modal").modal("show");


    }
    function copy_generate_key(element) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).prev("span").text()).select();
        document.execCommand("copy");
        $temp.remove();
    }
    var count = 0;
    $(".add-comment").click(function () {
        html = "";
        html += '<div class="row" id="count_' + count + '">' +
                '<div class="col-md-1">' +
                '<div class="checkbox" >' +
                '<label style="margin-left:20px"><input type="checkbox" class="assigncomments" name="assigncomments[]" value="0" ></label>' +
                '</div>' +
                '</div>' +
                '<div class="col-md-6">' +
                '<input type="text" class="form-control" id="comment" name="comment[]" placeholder="Enter Comment" required>' +
                '</div>' +
                '<div class="col-md-4">' +
                '<input type="text" class="form-control" id="amount-comment" name="amount-comment[]" placeholder="Enter Amount" required data-rule-number="true">' +
                '</div>' +
                '<div class="col-md-1"><label style="margin-top:10px"><i class="fa fa-trash" onclick="deletecomment(' + count + ')"></i></label>' +
                '</div>' +
                '</div>';
        count++;
        $("#append-comments").append(html);
        return false;
    });
    $(".submit-comments").click(function () {

        arr_comment = [];
        $('.assigncomments').each(function () {
            if ($(this).is(':checked'))
                arr_comment.push("true");
            else {
                arr_comment.push("false");
            }
        });
        arr_ids = [];
        $('.assign_comments').each(function () {
            if ($(this).is(':checked')) {
                var id = $(this).attr('id');
                id = id.split('_').pop();
                arr_ids.push(id);
            }
        });
        $("#assign_hd_ids").val(arr_ids);
        $("#hd_comments").val(arr_comment);
        if ($("#assign-comment-form").validate())
            $("#assign-comment-form").submit();
    });
    $(document).on("click", ".comment-modal", function () {
        var id = $(this).data('id');
        id = id.split('_').pop();
        $("#comment-modal #comment_statement_id").val(id);
        $("#append-comments").html("");
        $(".listunassign_comments").html("");
        $.ajax({
            url: '<?php echo base_url("statement/get_unassign_comment") ?>',
            type: 'POST',
            data: {
                'ledger_id': <?php echo $ledger_id ?>
            },
            dataType: 'json',
            success: function (data) {

                var parsed = $.parseJSON(JSON.stringify(data));
                console.log(parsed);
                html = "";
                if (parsed.length > 0) {
                    $.each(parsed, function (i, jsondata) {

                        html += '<div class="row bottomline">' +
                                '<div class="col-md-1">' +
                                '<div class="checkbox" >' +
                                '<label style="margin-left:20px"><input type="checkbox" class="assign_comments" name="assigncomments[]" id="statement_' + jsondata.id + '" value=""> </label>' +
                                '</div>' +
                                '</div>' +
                                '<div class="col-md-6"><label style="margin-top:10px">' +
                                jsondata.description +
                                '</label></div>' +
                                '<div class="col-md-4"><label style="margin-top:10px">$' +
                                jsondata.amount +
                                '</label></div>' +
                                '</div>';

                    });

                }
                $(".listunassign_comments").append(html);
            },
            error: function (request, error)
            {
            }
        });
        document.getElementById("assign-comment-form").reset();
    });
    $(document).on("click", ".uncomment-modal", function () {
        var id = $(this).data('id');
        id = id.split('_').pop();
        $(".unassign-comments").html("");
        $.ajax({
            url: '<?php echo base_url("statement/get_assign_comment") ?>',
            type: 'POST',
            data: {
                'ledger_id': <?php echo $ledger_id ?>,
                'statement_id': id
            },
            dataType: 'json',
            success: function (data) {

                var parsed = $.parseJSON(JSON.stringify(data));
                console.log(parsed);
                html = "";
                if (parsed.length > 0) {

                    url = '<?php echo base_url("statement/assign_ledger_comment") ?>';
                    html += "<form id='unassign-form' action='" + url + "' method='post'>";
                    html += '<input type="hidden" name="hd_ids" id="hd_ids" value="">' +
                            '<input type="hidden" name="ledger_id" value="' +<?php echo $ledger_id ?> + '">' +
                            '<input type="hidden" name="edit_ids" id="edit_ids" value="">' +
                            '<input type="hidden" name="statement_id" id="comment_statement_id" value="' + id + '">';
                    $.each(parsed, function (i, jsondata) {

                        html += '<input type="hidden" name="edit_ids_value[]" value="' + jsondata.id + '"><div class="row bottomline">' +
                                '<div class="col-md-1">' +
                                '<div class="checkbox" >' +
                                '<label  style="margin-left:20px"><input type="checkbox" class="unassigncomments" name="assigncomments[]" id="statement_' + jsondata.id + '" value="" checked="true"></label>' +
                                '</div>' +
                                '</div>' +
                                '<div class="col-md-6"><label style="margin-top:10px" id="labelcomment_' + jsondata.id + '">' +
                                jsondata.description +
                                '</label><input type="text" style="margin-top:10px;margin-bottom:10px;display:none" name="comment[]" id="comment_' + jsondata.id + '" value="' +
                                jsondata.description + '"  class="form-control"></div>' +
                                '<div class="col-md-4"><label style="margin-top:10px" id="labelamount_' + jsondata.id + '">$' +
                                jsondata.amount +
                                '</label><input type="text"  name="amount[]" id="amount_' + jsondata.id + '" value="' +
                                jsondata.amount + '" style="margin-top:10px;margin-bottom:10px;display:none"class="form-control" /></div>' +
                                '<div class="col-md-1"><label style="margin-top:10px"><a href="javascrript:void(0)" class="editcomment" onclick="editcontent(' + jsondata.id + ')"  id="edit_' + jsondata.id + '"><i class="fa fa-edit" ></i></a></label>' +
                                '</div>' +
                                '</div>';

                    });
                    html += "</form>";

                } else {
                    html = "<p style='color:red;font-size:14px;text-align:center'>No More Comments</p>";
                }
                $(".unassign-comments").append(html);
            },
            error: function (request, error)
            {
            }
        });
    });
    $(".editcomment").click(function () {

    });
    $(".unassign-submit").click(function () {
        arr_comment = [];
        $('.unassigncomments').each(function () {
            if (!($(this).is(':checked'))) {
                var id = $(this).attr('id');
                id = id.split('_').pop();
                arr_comment.push(id);
            }
        });
        $("#hd_ids").val(arr_comment);
        $("#unassign-form").submit();
    });
    editcomment_id = [];
    function editcontent(id) {

        if ($("#amount_" + id).is(":hidden")) {
            editcomment_id.push(id);
            $("#amount_" + id).css("display", "block");
            $("#comment_" + id).css("display", "block");
            $("#labelamount_" + id).css("display", "none");
            $("#labelcomment_" + id).css("display", "none");

        } else {
            $("#labelamount_" + id).css("display", "block");
            $("#labelcomment_" + id).css("display", "block");
            $("#amount_" + id).css("display", "none");
            $("#comment_" + id).css("display", "none");
            editcomment_id = jQuery.grep(editcomment_id, function (value) {
                return value != id;
            });
        }
        $("#edit_ids").val(editcomment_id);
    }
    function deletecomment(id) {
        $("#count_" + id).remove();
    }
</script>
