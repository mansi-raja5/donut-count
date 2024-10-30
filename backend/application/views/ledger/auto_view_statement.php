<?php
$isImpoundSection       = 1;
$isCommentSection       = 0;
$isImpoundExtraSection  = 0;
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
$totalFinalCredits      = 0;
$totalFinalDebits       = 0;
$totalFinalFood         = 0;
$totalExtraCredit       = 0;

$ledgerBalance          = 0;
$ledgerCarryForward     = 0;
$checksTotal            = 0;
$totalCreditReceiveFrom = 0;

$extraDebitKey  = -1;
$donutKey       = -1;
$dcpKey         = -1;
$netKey         = -1;
$grosskey       = -1;
$deanKey        = -1;
$royKey         = -1;
$extraCreditKey = -1;
$checkRecordKey = -1;

?>
<link href="<?php echo base_url() ?>/assets/css/ledger.css" rel="stylesheet">
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-picture"></i>Auto - <b><?php echo $title; ?></b>
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
            <a href="javascript:;" class="fullscreen" data-original-title="" title=""></a>
        </div>
    </div>
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-12" id="auto_ledger">
                <?php
                $attributes = array('class' => 'form-horizontal validate', 'id' => 'auto_ledger_frm');
                echo form_open_multipart('statement/createLedgerFromAutoView', $attributes, array());
                ?>
                <form name="auto_ledger_frm" id="auto_ledger_frm" action="<?php echo base_url()?>">
                    <?php
                    if(isset($error))
                    {
                        ?>
                        <tr><td colspan="6"><?php echo $error; ?></td><tr>
                        <?php
                    }
                    else
                    {
                    ?>
                        <table class="table table-bordered table-hover auto_ledger_view_table">
                            <thead>
                                <tr>
                                    <th><?php echo ++$i; ?></th>
                                    <th colspan="6">
                                        <b>AUTO LEDGER STATEMENT</b>
                                        <div class="pull-right">
                                            <?php if($ledger_id): ?>
                                                <a class="btn btn-danger btn-sm" href="javascript:void(0);" data-toggle="modal" data-id="1" onclick="ledger.setConfirmDetails(this)" data-target="#ConfirmDeleteModal" data-url="<?php echo base_url()?>reconcile/delete/?lid=<?php echo $ledger_id; ?>&bid=0">Delete</a>
                                                <a class="btn purple btn-sm" href="<?php echo base_url()?>statement/view/<?php echo $ledger_id; ?>" target="_blank">View Ledger</a>
                                            <?php endif; ?>
                                            <button type="submit" class="btn btn-success">Create Ledger</button>
                                            <!-- <button type="button" class="btn btn-success" onclick="ledger.createLedger()">Create Ledger</button> -->
                                            <a onclick="window.print();" title="Print PDF" class="btn btn-default btn-circle btn-sm"><i class="fa fa-file-pdf-o"></i> PDF </a>
                                        </div>
                                    </th>
                                </tr>
                                <tr>
                                    <th><?php echo ++$i; ?></th>
                                    <th><b>STORE #</b></th>
                                    <th><b><?php echo $store_key; ?></b></th>
                                    <th><b>Month</b></th>
                                    <th><b><?php echo monthname($month); ?></b></th>
                                    <th><b id="ledger_balance_top"></b></th>
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
                                $key = -1;
                                while ($i < 34) {
                                    ++$key;
                                ?>
                                    <tr>
                                        <th><?php echo ++$i; ?></th>
                                        <td>
                                            <input type="text" name="ledger[credits][<?php echo $key;?>][key]" value='<?php echo isset($credits[$key]) && $credits[$key]->cdate != '' ? date('d F Y', strtotime($credits[$key]->cdate)) : '';?>' readonly>
                                        </td>
                                        <td>
                                            <?php
                                            $amount = isset($credits[$key]) && $credits[$key]->actual_bank_deposit != '' ? ($credits[$key]->actual_bank_deposit) : '';
                                            $totalGeneralCredits += (float)$amount;
                                            ?>
                                            <input type="text" name="ledger[credits][<?php echo $key;?>][amt]" value='<?php echo $amount; ?>' >
                                        </td>
                                        <td>
                                            <?php
                                            $debitKey = isset($debits[$key]) && $debits[$key]['debit'] != '' ? $debits[$key]['debit'] : '';
                                            ?>
                                            <input type="text" name="ledger[debits][<?php echo $key;?>][key]" value='<?php echo $debitKey; ?>' >
                                        </td>
                                        <td>
                                            <?php
                                            $amount = isset($debits[$key]) && $debits[$key]['amt'] != '' ? ($debits[$key]['amt']) : '';
                                            $totalGeneralDebits += (float)$amount;
                                            ?>
                                            <input type="text" name="ledger[debits][<?php echo $key;?>][amt]" value='<?php echo $amount; ?>' readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="ledger[impound][<?php echo $key;?>][amt]" value='<?php echo isset($impounds[$key]) ? ($impounds[$key]->amount) : ''; ?>' readonly>
                                        </td>
                                        <td>
                                            <input type="text" name="ledger[impound][<?php echo $key;?>][key]" value='<?php echo isset($impounds[$key]) ? date('d F Y', strtotime($impounds[$key]->end_date)) : ''; ?>' readonly>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                                <tr>
                                    <th><?php echo ++$i; ?></th>
                                    <th>Total:</th>
                                    <th>
                                        <?php
                                        echo $totalGeneralCredits;
                                        $totalFinalCredits += $totalGeneralCredits;
                                        ?>
                                    </th>
                                    <td></td>
                                    <td></td>
                                    <th colspan="2">DONUT PURCHASES FROM CML</th>
                                </tr>
                                <?php
                                //Donut section starts
                                while ($i < 41) {
                                ?>
                                    <tr>
                                        <th><?php echo ++$i; ?></th>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>
                                            <?php
                                            $donutWeekDate = isset($donut[++$donutKey]->end_date) ?  date('d F', strtotime($donut[$donutKey]->end_date)) : '';
                                            ?>
                                            <input type="text" name="ledger[donut][<?php echo $donutKey;?>][key]" value='<?php echo $donutWeekDate; ?>' >
                                        </td>
                                        <td>
                                            <?php
                                            $amount = isset($donut[$donutKey]->amount) ? $donut[$donutKey]->amount : '';
                                            $totalDonut += (float) $amount;
                                            ?>
                                            <input type="text" name="ledger[donut][<?php echo $donutKey;?>][amt]" value='<?php echo $amount; ?>' readonly>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                                <tr>
                                    <th><?php echo ++$i; ?></th>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <th>Total:</th>
                                    <th><?php echo $totalDonut; ?></th>
                                </tr>
                                <!-- DCP Title -->
                                <tr>
                                    <th><?php echo ++$i; ?></th>
                                    <td></td>
                                    <td></td>
                                    <th>Total:</th>
                                    <th>
                                        <?php
                                        echo $totalGeneralDebits;
                                        $totalFinalDebits += $totalGeneralDebits;
                                        ?>
                                    </th>
                                    <th>DCP EFTS</th>
                                    <th>DOLLAR AMT.</th>
                                </tr>

                                <!-- PAYROLL NET Title -->
                                <tr>
                                    <th><?php echo ++$i; ?></th>
                                    <td></td>
                                    <td></td>
                                    <th>PAYROLL NET</th>
                                    <th>DOLLAR AMT.</th>
                                    <td>
                                        <?php
                                        $dcpWeekDate = isset($dcp_data[++$dcpKey]->end_date) ?  date('d F', strtotime($dcp_data[$dcpKey]->end_date)) : '';
                                        ?>
                                        <input type="text" name="ledger[dcp][<?php echo $dcpKey;?>][key]" value='<?php echo $dcpWeekDate; ?>' >
                                    </td>
                                    <td>
                                        <?php
                                        $dcamount = isset($dcp_data[$dcpKey]->amount) ? $dcp_data[$dcpKey]->amount : '';
                                        $totalDcp += (float) $dcamount;
                                        ?>
                                        <input type="text" name="ledger[dcp][<?php echo $dcpKey;?>][amt]" value='<?php echo $dcamount; ?>' readonly>
                                    </td>
                                </tr>
                                <!-- PAYROLL NET DATA -->
                                <?php
                                while ($i < 49) {
                                    ?>
                                    <tr>
                                        <th><?php echo ++$i; ?></th>
                                        <td></td>
                                        <td></td>
                                        <?php
                                        if(isset($payroll_net[++$netKey]))
                                        {
                                            ?>
                                            <td>
                                                <?php $payrollKey = date('d F', strtotime($payroll_net[$netKey]->end_date)) ?>
                                                <input type="text" name="ledger[payroll_net][<?php echo $netKey;?>][key]" value='<?php echo $payrollKey; ?>' readonly>
                                            </td>
                                            <td>
                                                <?php
                                                $pnamount = $payroll_net[$netKey]->amount;
                                                $totalPayrollNet += (float)$pnamount;
                                                ?>
                                                <input type="text" name="ledger[payroll_net][<?php echo $netKey;?>][amt]" value='<?php echo $pnamount; ?>' readonly>
                                            </td>
                                            <?php
                                        }
                                        else
                                        {
                                            ?>
                                            <td></td>
                                            <td></td>
                                            <?php
                                        }
                                        ?>
                                        <td>
                                            <?php
                                            $dcpWeekDate = isset($dcp_data[++$dcpKey]->end_date) ?  date('d F', strtotime($dcp_data[$dcpKey]->end_date)) : '';
                                            ?>
                                            <input type="text" name="ledger[dcp][<?php echo $dcpKey;?>][key]" value='<?php echo $dcpWeekDate; ?>' >
                                        </td>
                                        <td>
                                            <?php
                                            $dcamount = isset($dcp_data[$dcpKey]->amount) ? $dcp_data[$dcpKey]->amount : '';
                                            $totalDcp += (float) $dcamount;
                                            ?>
                                            <input type="text" name="ledger[dcp][<?php echo $dcpKey;?>][amt]" value='<?php echo $dcamount; ?>' readonly>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                $grossCount = 0;
                                ?>
                                <!-- PAYROLL TOTAL -->
                                <tr>
                                    <th><?php echo ++$i; ?></th>
                                    <td></td>
                                    <td></td>
                                    <th>Total:</th>
                                    <th><?php echo $totalPayrollNet; ?></th>
                                    <td>
                                        <?php
                                        $dcpWeekDate = isset($dcp_data[++$dcpKey]->end_date) ?  date('d F', strtotime($dcp_data[$dcpKey]->end_date)) : '';
                                        ?>
                                        <input type="text" name="ledger[dcp][<?php echo $dcpKey;?>][key]" value='<?php echo $dcpWeekDate; ?>' >
                                    </td>
                                    <td>
                                        <?php
                                        $dcamount = isset($dcp_data[$dcpKey]->amount) ? $dcp_data[$dcpKey]->amount : '';
                                        $totalDcp += (float) $dcamount;
                                        ?>
                                        <input type="text" name="ledger[dcp][<?php echo $dcpKey;?>][amt]" value='<?php echo $dcamount; ?>' readonly>
                                    </td>
                                </tr>
                                <!-- PAYROLL GROSS Title -->
                                <tr>
                                    <th><?php echo ++$i; ?></th>
                                    <td></td>
                                    <td></td>
                                    <th>PAYROLL GROSS</th>
                                    <th>DOLLAR AMT.</th>
                                    <td>
                                        <?php
                                        $dcpWeekDate = isset($dcp_data[++$dcpKey]->end_date) ?  date('d F', strtotime($dcp_data[$dcpKey]->end_date)) : '';
                                        ?>
                                        <input type="text" name="ledger[dcp][<?php echo $dcpKey;?>][key]" value='<?php echo $dcpWeekDate; ?>' >
                                    </td>
                                    <td>
                                        <?php
                                        $dcamount = isset($dcp_data[$dcpKey]->amount) ? $dcp_data[$dcpKey]->amount : '';
                                        $totalDcp += (float) $dcamount;
                                        ?>
                                        <input type="text" name="ledger[dcp][<?php echo $dcpKey;?>][amt]" value='<?php echo $dcamount; ?>' readonly>
                                    </td>
                                </tr>
                                <!-- PAYROLL GROSS DATA -->
                                <?php
                                while ($i < 56) {
                                ?>
                                    <tr>
                                        <th><?php echo ++$i; ?></th>
                                        <td></td>
                                        <td></td>
                                        <?php
                                        if (isset($payroll_gross[++$grosskey]))
                                        {
                                        ?>
                                            <td>
                                                <?php $payroll_gross_key = date('d F', strtotime($payroll_gross[$grosskey]->end_date)) ?>
                                                <input type="text" name="ledger[payroll_gross][<?php echo $grosskey;?>][key]" value='<?php echo $payroll_gross_key; ?>' readonly>
                                            </td>
                                            <td>
                                                <?php
                                                $pgamount = $payroll_gross[$grosskey]->amount;
                                                $totalPayrollGross += (float) $pgamount;
                                                ?>
                                                <input type="text" name="ledger[payroll_gross][<?php echo $grosskey;?>][amt]" value='<?php echo $pgamount; ?>' readonly>
                                            </td>
                                            <?php
                                            $grossCount++;
                                        } else {
                                            ?>
                                            <td></td>
                                            <td></td>
                                        <?php
                                        }
                                        ?>
                                        <td>
                                            <?php
                                            $dcpWeekDate = isset($dcp_data[++$dcpKey]->end_date) ?  date('d F', strtotime($dcp_data[$dcpKey]->end_date)) : '';
                                            ?>
                                            <input type="text" name="ledger[dcp][<?php echo $dcpKey;?>][key]" value='<?php echo $dcpWeekDate; ?>' >
                                        </td>
                                        <td>
                                            <?php
                                            $dcamount = isset($dcp_data[$dcpKey]->amount) ? $dcp_data[$dcpKey]->amount : '';
                                            $totalDcp += (float) $dcamount;
                                            ?>
                                            <input type="text" name="ledger[dcp][<?php echo $dcpKey;?>][amt]" value='<?php echo $dcamount; ?>' readonly>
                                        </td>
                                    </tr>
                                <?php
                                }
                                ?>
                                <!-- PAYROLL GROSS TOTAL -->
                                <tr>
                                    <th><?php echo ++$i; ?></th>
                                    <td></td>
                                    <td></td>
                                    <th>Total:</th>
                                    <th><?php echo $totalPayrollGross; ?></th>
                                    <td>
                                        <?php
                                        $dcpWeekDate = isset($dcp_data[++$dcpKey]->end_date) ?  date('d F', strtotime($dcp_data[$dcpKey]->end_date)) : '';
                                        ?>
                                        <input type="text" name="ledger[dcp][<?php echo $dcpKey;?>][key]" value='<?php echo $dcpWeekDate; ?>' >
                                    </td>
                                    <td>
                                        <?php
                                        $dcamount = isset($dcp_data[$dcpKey]->amount) ? $dcp_data[$dcpKey]->amount : '';
                                        $totalDcp += (float) $dcamount;
                                        ?>
                                        <input type="text" name="ledger[dcp][<?php echo $dcpKey;?>][amt]" value='<?php echo $dcamount; ?>' readonly>
                                    </td>
                                </tr>
                                <!-- ROY TITLE -->
                                <tr>
                                    <th><?php echo ++$i; ?></th>
                                    <td></td>
                                    <td></td>
                                    <th>ROY. & ADV. (First BR; Second Dunkin)</th>
                                    <th>DOLLAR AMT.</th>
                                    <td>
                                        <?php
                                        $dcpWeekDate = isset($dcp_data[++$dcpKey]->end_date) ?  date('d F', strtotime($dcp_data[$dcpKey]->end_date)) : '';
                                        ?>
                                        <input type="text" name="ledger[dcp][<?php echo $dcpKey;?>][key]" value='<?php echo $dcpWeekDate; ?>' >
                                    </td>
                                    <td>
                                        <?php
                                        $dcamount = isset($dcp_data[$dcpKey]->amount) ? $dcp_data[$dcpKey]->amount : '';
                                        $totalDcp += (float) $dcamount;
                                        ?>
                                        <input type="text" name="ledger[dcp][<?php echo $dcpKey;?>][amt]" value='<?php echo $dcamount; ?>' readonly>
                                    </td>
                                </tr>
                                <?php
                                while($i < 68)
                                {
                                    ?>
                                    <tr>
                                        <th><?php echo ++$i; ?></th>
                                        <?php
                                        if($i == 68) {
                                        ?>
                                            <th>Total:</th>
                                            <th>
                                                <?php
                                                echo $totalExtraCredit;
                                                $totalFinalCredits += (float) $totalExtraCredit;
                                                ?>
                                            </th>
                                        <?php
                                        } else {
                                        ?>
                                            <td>
                                                <?php
                                                $extraCredit = isset($extra_credit[++$extraCreditKey]->credit) ?  $extra_credit[$extraCreditKey]->credit : '';
                                                ?>
                                                <input type="text" name="ledger[extra_credit][<?php echo $extraCreditKey;?>][key]" value='<?php echo $extraCredit; ?>' >
                                            </td>
                                            <td>
                                                <?php
                                                $extraCreditAmount = isset($extra_credit[$extraCreditKey]->amount) ? $extra_credit[$extraCreditKey]->amount : '';
                                                $totalExtraCredit += (float) $extraCreditAmount;
                                                ?>
                                                <input type="text" name="ledger[extra_credit][<?php echo $extraCreditKey;?>][amt]" value='<?php echo $extraCreditAmount; ?>' readonly>
                                            </td>
                                        <?php
                                        }
                                        ?>
                                        <td>
                                            <?php
                                            $royWeekDate = isset($roy[++$royKey]->end_date) ?  date('d F', strtotime($roy[$royKey]->end_date)) : '';
                                            ?>
                                            <input type="text" name="ledger[roy][<?php echo $royKey;?>][key]" value='<?php echo $royWeekDate; ?>' >
                                        </td>
                                        <td>
                                            <?php
                                            $royamount = isset($roy[$royKey]->amount) ? $roy[$royKey]->amount : '';
                                            $totalRoy += (float) $royamount;
                                            ?>
                                            <input type="text" name="ledger[roy][<?php echo $royKey;?>][amt]" value='<?php echo $royamount; ?>' readonly>
                                        </td>
                                        <?php
                                        if ($i < 62) {
                                        ?>
                                            <td>
                                                <?php
                                                $dcpWeekDate = isset($dcp_data[++$dcpKey]->end_date) ?  date('d F', strtotime($dcp_data[$dcpKey]->end_date)) : '';
                                                ?>
                                                <input type="text" name="ledger[dcp][<?php echo $dcpKey;?>][key]" value='<?php echo $dcpWeekDate; ?>' >
                                            </td>
                                            <td>
                                                <?php
                                                $dcamount = isset($dcp_data[$dcpKey]->amount) ? $dcp_data[$dcpKey]->amount : '';
                                                $totalDcp += (float) $dcamount;
                                                ?>
                                                <input type="text" name="ledger[dcp][<?php echo $dcpKey;?>][amt]" value='<?php echo $dcamount; ?>' readonly>
                                            </td>
                                        <?php
                                        } elseif ($i == 62) {
                                        ?>
                                            <th>Total:</th>
                                            <th><?php echo $totalDcp; ?></th>
                                        <?php
                                        } elseif ($i == 63) {
                                        ?>
                                            <th>DEAN FOODS</th>
                                            <th>DOLLAR AMT.</th>
                                            <?php
                                        } else {
                                        ?>
                                            <td>
                                                <?php
                                                $deanWeekDate = isset($dean[++$deanKey]->end_date) ?  date('d F', strtotime($dean[$deanKey]->end_date)) : '';
                                                ?>
                                                <input type="text" name="ledger[dean][<?php echo $deanKey;?>][key]" value='<?php echo $deanWeekDate; ?>' >
                                            </td>
                                            <td>
                                                <?php
                                                $deanamount = isset($dean[$deanKey]->amount) ? $dean[$deanKey]->amount : '';
                                                $totalDean += (float) $deanamount;
                                                ?>
                                                <input type="text" name="ledger[dean][<?php echo $deanKey;?>][amt]" value='<?php echo $deanamount; ?>' readonly>
                                            </td>
                                        <?php
                                        }
                                        ?>
                                    </tr>
                                    <?php
                                }
                                ?>
                                <tr>
                                    <th><?php echo ++$i; ?></th>
                                    <th>Credit Card Credits</th>
                                    <th>
                                        <?php
                                        $popoverHtml = "<table border=1>";
                                        $popoverHtml .= "<tr><th>No</th><th>Breakdown Description</th><th>Amount</th></tr>";
                                        $count = 0;
                                        foreach ($all_breakdown_extra_credit as $_all_breakdown_extra_credit) {
                                            $popoverHtml .= '<tr>';
                                            $popoverHtml .= '<td>' . ++$count . '</td>';
                                            $popoverHtml .= '<td>' . $_all_breakdown_extra_credit->breakdown_description . '</td><td>' . $_all_breakdown_extra_credit->amount . '</td>';
                                            $popoverHtml .= '</tr>';
                                        }
                                        $popoverHtml .= "</table>";

                                        $creditCardAmount = isset($breakdown_extra_credit[0]->amount) ? $breakdown_extra_credit[0]->amount : '';
                                        $totalFinalCredits += (float) $creditCardAmount;
                                        ?>
                                        <a href="javascript:void(0)" data-toggle="popover" data-placement="bottom" data-content="<?php echo $popoverHtml; ?>" class="open-tooltip">
                                        <input type="text" name="ledger[credit_card][0][amt]" value='<?php echo $creditCardAmount; ?>' readonly>
                                        </a>
                                    </th>
                                    <th>TOTAL:</th>
                                    <th><?php echo $totalRoy; ?></th>
                                    <th>TOTAL:</th>
                                    <th><?php echo $totalDean; ?></th>
                                </tr>
                                <tr>
                                    <th><?php echo ++$i; ?></th>
                                    <th>TOTAL CREDITS</th>
                                    <th><?php echo $totalFinalCredits; ?></th>
                                    <th>TOTAL DEBITS</th>
                                    <th><?php echo $totalFinalDebits; ?></th>
                                    <th>TOTAL FOOD</th>
                                    <th><?php echo $totalFinalFood; ?></th>
                                </tr>
                                <?php
                                $ledgerBalance = $totalFinalCredits - $totalFinalDebits - $totalFinalFood;
                                ?>
                                <input type="hidden" name="ledger[ledger_balance]" value="<?php echo $ledgerBalance; ?>">
                                <input type="hidden" name="ledger[store_key]" value="<?php echo $store_key; ?>">
                                <input type="hidden" name="ledger[month]" value="<?php echo $month; ?>">
                                <input type="hidden" name="ledger[year]" value="<?php echo $year; ?>">
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
                                    <th><b><?php echo monthname($month) ?></b></th>
                                    <th>STORE#</th>
                                    <th><b><?php echo $store_key ?></b></th>
                                    <th>Balance C/F</th>
                                    <th>
                                        <?php
                                        $ledgerCarryForward = 0;
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
                            <table class="table table-bordered mb0 checkbook_record_table">
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
                                                <td><input type="text" name="ledger[checkbook_record][<?php echo ++$checkRecordKey; ?>][bc_payable]" value='<?php echo isset($_checkbook_record->bc_payable) ? $_checkbook_record->bc_payable : '' ?>' readonly></td>
                                                <td><input type="text" name="ledger[checkbook_record][<?php echo $checkRecordKey; ?>][bc_check_no]" value='<?php echo isset($_checkbook_record->bc_check_no) ? $_checkbook_record->bc_check_no : '' ?>' readonly></td>
                                                <td><input type="text" name="ledger[checkbook_record][<?php echo $checkRecordKey; ?>][bc_memo]" value='<?php echo isset($_checkbook_record->bc_memo) ? $_checkbook_record->bc_memo : '' ?>' readonly></td>
                                                <td><input type="text" name="ledger[checkbook_record][<?php echo $checkRecordKey; ?>][bc_amount]" value='<?php echo isset($_checkbook_record->bc_amount) ? $_checkbook_record->bc_amount : '' ?>' readonly></td>
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
                                    <tr>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <?php
                                    $receivedCount = 0;
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
                    <?php
                    }
                    ?>
                </form>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/js/ledger.js" type="text/javascript"></script>
<script type="text/javascript">
var storeKey = '<?php echo $store_key ?>';
var month = '<?php echo $month ?>';
var year = '<?php echo $year ?>';
let ledger = new Ledger(storeKey, month, year);
</script>