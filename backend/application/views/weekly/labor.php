<?php echo isset($advance_search_html) ? $advance_search_html : ''; 
?>
<div class="row">
    <div class="board">
        <div class="col-xs-12">
          <?php if($advance_status_selected == ''):
              ?>
        <?php
            $stdObj = new stdClass;
            $stdObj->key = 'Accumulate';
            $advanceStore[] = 'Accumulate';
            array_splice($stores, 0, 0, array('Accumulate' => $stdObj));
        ?>
        <div class="board-inner">
            <ul class="nav nav-tabs" id="myTab">
                <div class="owl_1 owl-carousel owl-theme">
                    <?php
                    $count = 0;
                    foreach ($stores as $_stores) {
                        ?>
                        <div class="item" style="width: 100%;">
                            <li class="<?php echo ++$count == 1 ? 'active' : ''; ?>" style="width: 100%; padding: 0px 10px;">
                                <a <?php echo (($_stores->status ?? null) == 'I') ? 'style="width: 100%; padding: 0px 20px; background-color: bisque"' : 'style="width: 100%; padding: 0px 20px;"'; ?> data-toggle="tab" href="#<?php echo trim($_stores->key); ?>" title="welcome">
                                    <span class="round-tabs one" style="width: 100%"><?php echo $_stores->key; ?></span>
                                </a>
                            </li>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </ul>
        </div>
        <div class="tab-content">
            
            <?php
           
            $count = 0;
            $yearlist = array_values($advanceYear);
             foreach ($stores as $_stores) {
                        if(in_array($_stores->key, $advanceStore))
                        {
                            ?>
                            <div class="tab-pane fade <?php echo ++$count == 1 ? 'in active' : ''; ?>" id="<?php echo trim($_stores->key); ?>">
                                <table class="table table-condensed labor-table" id="labor_report">
                                    <caption>
                                        <?php echo ($_stores->key == 'Accumulate' ? "Accumulate" : ucfirst($store_list[$_stores->key]) . " #{$_stores->key}") . " Labor Percentage " . implode(' - ', $advanceYear); ?>
                                        <a class="export-btn" onclick="week.exportXls();">Export XLSX</a>
                                    </caption>
                                    <thead>
                                        <tr>
                                            <th class="no-sort" data-orderable="false" style="width: 60px; margin: auto">Period</th>
                                            <th class="no-sort" data-orderable="false" style="width: 50px"><?php echo $_stores->key; ?></th>
                                            <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                            ?>
                                                <th style="background: #<?php echo $_advanceKey; ?>">Gross Pay <?php echo substr($_advanceYear, -2); ?></th>
                                            <?php } ?>
                                            <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <th>Gross Pay % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                            <?php endfor; ?>
                                            <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                            ?>
                                                <th style="background: #<?php echo $_advanceKey; ?>">Bonus <?php echo substr($_advanceYear, -2); ?></th>
                                            <?php } ?>
                                            <?php /* for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <th>Bonus % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                            <?php endfor; */ ?>
                                            <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {

                                            ?>
                                                <th style="background: #<?php echo $_advanceKey; ?>">Covid <?php echo substr($_advanceYear, -2); ?></th>
                                            <?php } ?>
                                            <?php /* for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <th>Covid % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                            <?php endfor; */ ?>
                                            <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {

                                            ?>
                                                <th style="background: #<?php echo $_advanceKey; ?>">Tax <?php echo substr($_advanceYear, -2); ?></th>
                                            <?php } ?>
                                            <?php /* for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <th>Tax % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                            <?php endfor; */ ?>
                                                 <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {

                                            ?>
                                                <th style="background: #<?php echo $_advanceKey; ?>">Total Pay <?php echo substr($_advanceYear, -2); ?></th>
                                            <?php } ?>
                                            <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <th>Total Pay % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                            <?php endfor; ?>
                                                 <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {

                                            ?>
                                                <th style="background: #<?php echo $_advanceKey; ?>">Net Sales <?php echo substr($_advanceYear, -2); ?></th>
                                            <?php } ?>
                                            <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <th>Net Sales % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                            <?php endfor; ?>
                                                 <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {

                                            ?>
                                                <th style="background: #<?php echo $_advanceKey; ?>">Labor % <?php echo substr($_advanceYear, -2); ?></th>
                                            <?php } ?>
                                            <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <th>Labor % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                            <?php endfor; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        sort($weeks);
                                        $periodNo = 0;
                                        $weekNo = 0;
                                        foreach ($weeks as $_weeks) {
                                              $week_number = $_weeks['week_number'] < 10 ? "0".$_weeks['week_number'] : $_weeks['week_number'];
                                            $month = date("M", strtotime($_weeks['start_of_week']));
                                            ?>
                                            <tr>
                                                <?php
                                                    if ($weekNo == 4 && $periodNo != 13) {
                                                        $weekNo = 1;
                                                    } else {
                                                        $weekNo++;
                                                    }
                                                    if ($weekNo == 1) {
                                                        $periodNo++;
                                                    }
                                                ?>
                                                <td><?php echo $weekNo == 1 ? "P{$periodNo}" : ''; ?></td>
                                                <td>
                                                    <span data-toggle="tooltip" title="<?php echo date('d M',strtotime($_weeks['start_of_week'])); ?> - <?php echo date('d-M',strtotime($_weeks['end_of_week'])); ?>">Week <?php echo trim($weekNo); ?> <?php echo showInDateFormat($_weeks['end_of_week']); ?></span>
                                                </td>

                                                <!-- gross_pay -->
                                                <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                                <td style="background: #<?php echo $_advanceKey; ?>">
                                                    <?php $g_pay = $labordara_data[$_stores->key][$_advanceYear][$week_number]['gross_pay'] ?? 0; ?>
                                                    <?php echo showInDollar($g_pay); ?>
                                                </td>
                                                <?php endforeach; ?>                                                
                                                <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <td>
                                                    <?php $g_pay1 = $labordara_data[$_stores->key][$yearlist[$i]][$week_number]['gross_pay'] ?? 0; ?>
                                                    <?php $g_pay2 = $labordara_data[$_stores->key][$yearlist[$i + 1]][$week_number]['gross_pay'] ?? 0; ?>
                                                    <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                        <i class="tool-tip__icon"><?php echo $g_pay1 != 0 ? showInPercentage(($g_pay2 - $g_pay1) / $g_pay1 * 100) : ''; ?></i>
                                                        <p class="tool-tip__info">
                                                            <span>(<span style="color: royalblue;">Gross Pay <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">Gross Pay <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">Gross Pay <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                            <span>(<span style="color: royalblue;"><?= $g_pay2 ?></span> - <span style="color: tomato;"><?= $g_pay1 ?></span>) / <span style="color: tomato;"><?= $g_pay1 ?></span></span>
                                                        </p>
                                                    </div>
                                                </td>         
                                                <?php endfor; ?>

                                                <!-- bonus -->
                                                <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                                <td style="background: #<?php echo $_advanceKey; ?>">
                                                    <?php $bonus = $labordara_data[$_stores->key][$_advanceYear][$week_number]['bonus'] ?? 0; ?>
                                                    <?php echo showInDollar($bonus); ?>
                                                </td>
                                                <?php endforeach; ?>                                                
                                                <?php /* for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <td>
                                                    <?php $bonus1 = $labordara_data[$_stores->key][$yearlist[$i]][$week_number]['bonus'] ?? 0; ?>
                                                    <?php $bonus2 = $labordara_data[$_stores->key][$yearlist[$i + 1]][$week_number]['bonus'] ?? 0; ?>
                                                    <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                        <i class="tool-tip__icon"><?php echo $bonus1 != 0 ? showInPercentage(($bonus2 - $bonus1) / $bonus1 * 100) : ''; ?></i>
                                                        <p class="tool-tip__info">
                                                            <span>(<span style="color: royalblue;">Bonus <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">Bonus <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">Bonus <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                            <span>(<span style="color: royalblue;"><?= $bonus2 ?></span> - <span style="color: tomato;"><?= $bonus1 ?></span>) / <span style="color: tomato;"><?= $bonus1 ?></span></span>
                                                        </p>
                                                    </div>
                                                </td>         
                                                <?php endfor; */ ?>

                                                <!-- covid -->
                                                <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                                <td style="background: #<?php echo $_advanceKey; ?>">
                                                    <?php $covid = $labordara_data[$_stores->key][$_advanceYear][$week_number]['covid'] ?? 0; ?>
                                                    <?php echo showInDollar($covid); ?>
                                                </td>
                                                <?php endforeach; ?>                                                
                                                <?php /* for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <td>
                                                    <?php $covid1 = $labordara_data[$_stores->key][$yearlist[$i]][$week_number]['covid'] ?? 0; ?>
                                                    <?php $covid2 = $labordara_data[$_stores->key][$yearlist[$i + 1]][$week_number]['covid'] ?? 0; ?>
                                                    <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                        <i class="tool-tip__icon"><?php echo $covid1 != 0 ? showInPercentage(($covid2 - $covid1) / $covid1 * 100) : ''; ?></i>
                                                        <p class="tool-tip__info">
                                                            <span>(<span style="color: royalblue;">Covid <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">Covid <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">Covid <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                            <span>(<span style="color: royalblue;"><?= $covid2 ?></span> - <span style="color: tomato;"><?= $covid1 ?></span>) / <span style="color: tomato;"><?= $covid1 ?></span></span>
                                                        </p>
                                                    </div>
                                                </td>         
                                                <?php endfor; */ ?>

                                                <!-- tax_amount -->
                                                <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                                <td style="background: #<?php echo $_advanceKey; ?>">
                                                    <?php $tax_amount = $labordara_data[$_stores->key][$_advanceYear][$week_number]['tax_amount'] ?? 0; ?>
                                                    <?php echo showInDollar($tax_amount); ?>
                                                </td>
                                                <?php endforeach; ?>                                                
                                                <?php /* for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <td>
                                                    <?php $tax_amount1 = $labordara_data[$_stores->key][$yearlist[$i]][$week_number]['tax_amount'] ?? 0; ?>
                                                    <?php $tax_amount2 = $labordara_data[$_stores->key][$yearlist[$i + 1]][$week_number]['tax_amount'] ?? 0; ?>
                                                    <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                        <i class="tool-tip__icon"><?php echo $tax_amount1 != 0 ? showInPercentage(($tax_amount2 - $tax_amount1) / $tax_amount1 * 100) : ''; ?></i>
                                                        <p class="tool-tip__info">
                                                            <span>(<span style="color: royalblue;">Tax <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">Tax <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">Tax <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                            <span>(<span style="color: royalblue;"><?= $tax_amount2 ?></span> - <span style="color: tomato;"><?= $tax_amount1 ?></span>) / <span style="color: tomato;"><?= $tax_amount1 ?></span></span>
                                                        </p>
                                                    </div>
                                                </td>         
                                                <?php endfor; */ ?>

                                                <!-- total_pay -->
                                                <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                                <td style="background: #<?php echo $_advanceKey; ?>">
                                                    <?php $total_pay = $labordara_data[$_stores->key][$_advanceYear][$week_number]['total_pay'] ?? 0; ?>
                                                    <?php echo showInDollar($total_pay); ?>
                                                </td>
                                                <?php endforeach; ?>                                                
                                                <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <td>
                                                    <?php $total_pay1 = $labordara_data[$_stores->key][$yearlist[$i]][$week_number]['total_pay'] ?? 0; ?>
                                                    <?php $total_pay2 = $labordara_data[$_stores->key][$yearlist[$i + 1]][$week_number]['total_pay'] ?? 0; ?>
                                                    <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                        <i class="tool-tip__icon"><?php echo $total_pay1 != 0 ? showInPercentage(($total_pay2 - $total_pay1) / $total_pay1 * 100) : ''; ?></i>
                                                        <p class="tool-tip__info">
                                                            <span>(<span style="color: royalblue;">Total Pay <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">Total Pay <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">Total Pay <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                            <span>(<span style="color: royalblue;"><?= $total_pay2 ?></span> - <span style="color: tomato;"><?= $total_pay1 ?></span>) / <span style="color: tomato;"><?= $total_pay1 ?></span></span>
                                                        </p>
                                                    </div>
                                                </td>         
                                                <?php endfor; ?>

                                                <!-- net_sales -->
                                                <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                                <td style="background: #<?php echo $_advanceKey; ?>">
                                                    <?php $net_sales = $labordara_data[$_stores->key][$_advanceYear][$week_number]['net_sales'] ?? 0; ?>
                                                    <?php echo showInDollar($net_sales); ?>
                                                </td>
                                                <?php endforeach; ?>                                                
                                                <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <td>
                                                    <?php $net_sales1 = $labordara_data[$_stores->key][$yearlist[$i]][$week_number]['net_sales'] ?? 0; ?>
                                                    <?php $net_sales2 = $labordara_data[$_stores->key][$yearlist[$i + 1]][$week_number]['net_sales'] ?? 0; ?>
                                                    <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                        <i class="tool-tip__icon"><?php echo $net_sales1 != 0 ? showInPercentage(($net_sales2 - $net_sales1) / $net_sales1 * 100) : ''; ?></i>
                                                        <p class="tool-tip__info">
                                                            <span>(<span style="color: royalblue;">Net Sales <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">Net Sales <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">Net Sales <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                            <span>(<span style="color: royalblue;"><?= $net_sales2 ?></span> - <span style="color: tomato;"><?= $net_sales1 ?></span>) / <span style="color: tomato;"><?= $net_sales1 ?></span></span>
                                                        </p>
                                                    </div>
                                                </td>         
                                                <?php endfor; ?>

                                                <!-- labor_percentage -->
                                                <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                                <td style="background: #<?php echo $_advanceKey; ?>">
                                                    <?php $labor_percentage = $labordara_data[$_stores->key][$_advanceYear][$week_number]['labor_percentage'] ?? 0; ?>
                                                    <?php echo showInDollar($labor_percentage); ?>
                                                </td>
                                                <?php endforeach; ?>                                                
                                                <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <td>
                                                    <?php $labor_percentage1 = $labordara_data[$_stores->key][$yearlist[$i]][$week_number]['labor_percentage'] ?? 0; ?>
                                                    <?php $labor_percentage2 = $labordara_data[$_stores->key][$yearlist[$i + 1]][$week_number]['labor_percentage'] ?? 0; ?>
                                                    <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                        <i class="tool-tip__icon"><?php echo $labor_percentage1 != 0 ? showInPercentage(($labor_percentage2 - $labor_percentage1) / $labor_percentage1 * 100) : ''; ?></i>
                                                        <p class="tool-tip__info">
                                                            <span>(<span style="color: royalblue;">Labor % <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">Labor % <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">Labor % <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                            <span>(<span style="color: royalblue;"><?= $labor_percentage2 ?></span> - <span style="color: tomato;"><?= $labor_percentage1 ?></span>) / <span style="color: tomato;"><?= $labor_percentage1 ?></span></span>
                                                        </p>
                                                    </div>
                                                </td>         
                                                <?php endfor; ?>
                                            </tr>
                                                <?php
                                        }?>

                                    </tbody>
                                </table>
                            </div>
                                    <?php
                        }
             }
           
            ?>
            <div class="clearfix">
            </div>
        </div>
           <?php else:
               ?>
                    <?php
                    $count = 0;
                    $yearlist = array_values($advanceYear);
                    ?>
                    <div class="tab-pane fade <?php echo ++$count == 1 ? 'in active' : ''; ?>" >
                        <table class="table table-condensed labor-table" id="labor_report">
                            <caption><?php echo "Labor Comparison " . implode(' - ', $advanceYear); ?></caption>
                            <thead>
                                <tr>
                                    <th class="no-sort" data-orderable="false"></th>
                                    <th>Store Key</th>
                                    <th>Time period</th>
                                      <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                            ?>
                                                 <th style="background: #<?php echo $_advanceKey; ?>">
                                            Gross Pay <?php echo substr($_advanceYear, -2); ?>
                                            <br>
                                            <?php
                                            echo isset($display_week_dates[$_advanceYear]) ? date('m/d/Y',strtotime($display_week_dates[$_advanceYear])) : '';
                                            ?>
                                        </th>
                                            <?php } ?>
                                        <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                            <th>Gross Pay % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                        <?php endfor; ?>
                                            <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                            ?>
                                                  <th style="background: #<?php echo $_advanceKey; ?>">
                                            Bonus <?php echo substr($_advanceYear, -2); ?>
                                            <br>
                                            <?php
                                            echo isset($display_week_dates[$_advanceYear]) ? date('m/d/Y',strtotime($display_week_dates[$_advanceYear])) : '';
                                            ?>
                                        </th>
                                            <?php } ?>
                                            <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {

                                            ?>
                                          <th style="background: #<?php echo $_advanceKey; ?>">
                                            Covid <?php echo substr($_advanceYear, -2); ?>
                                            <br>
                                            <?php
                                            echo isset($display_week_dates[$_advanceYear]) ? date('m/d/Y',strtotime($display_week_dates[$_advanceYear])) : '';
                                            ?>
                                        </th>
                                            <?php } ?>
                                            <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {

                                            ?>
                                                <th style="background: #<?php echo $_advanceKey; ?>">
                                            Tax <?php echo substr($_advanceYear, -2); ?>
                                            <br>
                                            <?php
                                            echo isset($display_week_dates[$_advanceYear]) ? date('m/d/Y',strtotime($display_week_dates[$_advanceYear])) : '';
                                            ?>
                                        </th>
                                            <?php } ?>
                                                 <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {

                                            ?>
                                              <th style="background: #<?php echo $_advanceKey; ?>">
                                            Total Pay <?php echo substr($_advanceYear, -2); ?>
                                            <br>
                                            <?php
                                            echo isset($display_week_dates[$_advanceYear]) ? date('m/d/Y',strtotime($display_week_dates[$_advanceYear])) : '';
                                            ?>
                                        </th>
                                            <?php } ?>
                                        <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                            <th>Total Pay % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                        <?php endfor; ?>
                                                 <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {

                                            ?>
                                                <th style="background: #<?php echo $_advanceKey; ?>">
                                            Net Sales <?php echo substr($_advanceYear, -2); ?>
                                            <br>
                                            <?php
                                            echo isset($display_week_dates[$_advanceYear]) ? date('m/d/Y',strtotime($display_week_dates[$_advanceYear])) : '';
                                            ?>
                                        </th>
                                            <?php } ?>
                                        <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                            <th>Net Sales % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                        <?php endfor; ?>
                                                 <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {

                                            ?>
                                                <th style="background: #<?php echo $_advanceKey; ?>">
                                            Labor % <?php echo substr($_advanceYear, -2); ?>
                                            <br>
                                            <?php
                                            echo isset($display_week_dates[$_advanceYear]) ? date('m/d/Y',strtotime($display_week_dates[$_advanceYear])) : '';
                                            ?>
                                        </th>
                                            <?php } ?>
                                        <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                            <th>Labor % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                        <?php endfor; ?>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                            
                                foreach ($advanceStore as $_advanceStore) {
                                    foreach ($display_dates as $_display_dates) {
                                    ?>
                                    <tr>
                                        <th class="no-sort" data-orderable="false"></th>
                                        <td ><?php echo (isset($storeKey) && $storeKey == $_advanceStore) ? '' : $storeKey = $_advanceStore; ?></td>
                                        <td>
                                            <?php echo $_display_dates; ?>
                                        </td>

                                        <!-- gross_pay -->
                                        <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                        <td style="background: #<?php echo $_advanceKey; ?>">
                                            <?php $g_pay = $labordara_data[$_advanceStore][$_advanceYear][$_display_dates]['gross_pay'] ?? 0; ?>
                                            <?php echo showInDollar($g_pay); ?>
                                        </td>
                                        <?php endforeach; ?>                                                
                                        <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                        <td>
                                            <?php $g_pay1 = $labordara_data[$_advanceStore][$yearlist[$i]][$_display_dates]['gross_pay'] ?? 0; ?>
                                            <?php $g_pay2 = $labordara_data[$_advanceStore][$yearlist[$i + 1]][$_display_dates]['gross_pay'] ?? 0; ?>
                                            <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                <i class="tool-tip__icon"><?php echo $g_pay1 != 0 ? showInPercentage(($g_pay2 - $g_pay1) / $g_pay1 * 100) : ''; ?></i>
                                                <p class="tool-tip__info">
                                                    <span>(<span style="color: royalblue;">Gross Pay <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">Gross Pay <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">Gross Pay <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                    <span>(<span style="color: royalblue;"><?= $g_pay2 ?></span> - <span style="color: tomato;"><?= $g_pay1 ?></span>) / <span style="color: tomato;"><?= $g_pay1 ?></span></span>
                                                </p>
                                            </div>
                                        </td>         
                                        <?php endfor; ?>

                                        <!-- bonus -->
                                        <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                        <td style="background: #<?php echo $_advanceKey; ?>">
                                            <?php $bonus = $labordara_data[$_advanceStore][$_advanceYear][$_display_dates]['bonus'] ?? 0; ?>
                                            <?php echo showInDollar($bonus); ?>
                                        </td>
                                        <?php endforeach; ?>                                                

                                        <!-- covid -->
                                        <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                        <td style="background: #<?php echo $_advanceKey; ?>">
                                            <?php $covid = $labordara_data[$_advanceStore][$_advanceYear][$_display_dates]['covid'] ?? 0; ?>
                                            <?php echo showInDollar($covid); ?>
                                        </td>
                                        <?php endforeach; ?>                                                

                                        <!-- tax_amount -->
                                        <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                        <td style="background: #<?php echo $_advanceKey; ?>">
                                            <?php $tax_amount = $labordara_data[$_advanceStore][$_advanceYear][$_display_dates]['tax_amount'] ?? 0; ?>
                                            <?php echo showInDollar($tax_amount); ?>
                                        </td>
                                        <?php endforeach; ?>                                                

                                        <!-- total_pay -->
                                        <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                        <td style="background: #<?php echo $_advanceKey; ?>">
                                            <?php $total_pay = $labordara_data[$_advanceStore][$_advanceYear][$_display_dates]['total_pay'] ?? 0; ?>
                                            <?php echo showInDollar($total_pay); ?>
                                        </td>
                                        <?php endforeach; ?>                                                
                                        <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                        <td>
                                            <?php $total_pay1 = $labordara_data[$_advanceStore][$yearlist[$i]][$_display_dates]['total_pay'] ?? 0; ?>
                                            <?php $total_pay2 = $labordara_data[$_advanceStore][$yearlist[$i + 1]][$_display_dates]['total_pay'] ?? 0; ?>
                                            <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                <i class="tool-tip__icon"><?php echo $total_pay1 != 0 ? showInPercentage(($total_pay2 - $total_pay1) / $total_pay1 * 100) : ''; ?></i>
                                                <p class="tool-tip__info">
                                                    <span>(<span style="color: royalblue;">Total Pay <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">Total Pay <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">Total Pay <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                    <span>(<span style="color: royalblue;"><?= $total_pay2 ?></span> - <span style="color: tomato;"><?= $total_pay1 ?></span>) / <span style="color: tomato;"><?= $total_pay1 ?></span></span>
                                                </p>
                                            </div>
                                        </td>         
                                        <?php endfor; ?>

                                        <!-- net_sales -->
                                        <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                        <td style="background: #<?php echo $_advanceKey; ?>">
                                            <?php $net_sales = $labordara_data[$_advanceStore][$_advanceYear][$_display_dates]['net_sales'] ?? 0; ?>
                                            <?php echo showInDollar($net_sales); ?>
                                        </td>
                                        <?php endforeach; ?>                                                
                                        <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                        <td>
                                            <?php $net_sales1 = $labordara_data[$_advanceStore][$yearlist[$i]][$_display_dates]['net_sales'] ?? 0; ?>
                                            <?php $net_sales2 = $labordara_data[$_advanceStore][$yearlist[$i + 1]][$_display_dates]['net_sales'] ?? 0; ?>
                                            <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                <i class="tool-tip__icon"><?php echo $net_sales1 != 0 ? showInPercentage(($net_sales2 - $net_sales1) / $net_sales1 * 100) : ''; ?></i>
                                                <p class="tool-tip__info">
                                                    <span>(<span style="color: royalblue;">Net Sales <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">Net Sales <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">Net Sales <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                    <span>(<span style="color: royalblue;"><?= $net_sales2 ?></span> - <span style="color: tomato;"><?= $net_sales1 ?></span>) / <span style="color: tomato;"><?= $net_sales1 ?></span></span>
                                                </p>
                                            </div>
                                        </td>         
                                        <?php endfor; ?>

                                        <!-- labor_percentage -->
                                        <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                        <td style="background: #<?php echo $_advanceKey; ?>">
                                            <?php $labor_percentage = $labordara_data[$_advanceStore][$_advanceYear][$_display_dates]['labor_percentage'] ?? 0; ?>
                                            <?php echo showInDollar($labor_percentage); ?>
                                        </td>
                                        <?php endforeach; ?>                                                
                                        <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                        <td>
                                            <?php $labor_percentage1 = $labordara_data[$_advanceStore][$yearlist[$i]][$_display_dates]['labor_percentage'] ?? 0; ?>
                                            <?php $labor_percentage2 = $labordara_data[$_advanceStore][$yearlist[$i + 1]][$_display_dates]['labor_percentage'] ?? 0; ?>
                                            <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                <i class="tool-tip__icon"><?php echo $labor_percentage1 != 0 ? showInPercentage(($labor_percentage2 - $labor_percentage1) / $labor_percentage1 * 100) : ''; ?></i>
                                                <p class="tool-tip__info">
                                                    <span>(<span style="color: royalblue;">Labor % <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">Labor % <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">Labor % <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                    <span>(<span style="color: royalblue;"><?= $labor_percentage2 ?></span> - <span style="color: tomato;"><?= $labor_percentage1 ?></span>) / <span style="color: tomato;"><?= $labor_percentage1 ?></span></span>
                                                </p>
                                            </div>
                                        </td>         
                                        <?php endfor; ?>
                                    </tr>
                                    <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="clearfix">
                    </div>
                <?php endif; ?>
    </div>
</div>
</div>
<script>
    function format(d) {

        return '<div class="slider">' +
                '<table class="table table-striped table-bordered" style="padding-left:50px;">' +
                '<tbody>' +
                '<td>1</td>' +
                '<td>2</td>' +
                '<td>3</td>' +
                '<td>4</td>' +
                '<td>5</td>' +
                '<td>6</td>' +
                '<td>7</td>' +
                '<td>8</td>' +
                '<td>9</td>' +
                '</tbody>' +
                '</table>' +
                '</div>';
    }
    var dt = $('#tblListing').DataTable({
        "order": [],
        "columnDefs": [{
                "targets": 'no-sort',
                "orderable": false,
            }]
    });
    dt.on('draw', function () {
        $.each(detailRows, function (i, id) {
            $('#' + id + ' td.details-control').trigger('click');
        });
    });
    var detailRows = [];

    $('#tblListing tbody').on('click', 'tr td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = dt.row(tr);
        var idx = $.inArray(tr.attr('id'), detailRows);
        if (row.child.isShown()) {
            tr.removeClass('details');
            row.child.hide();
            // Remove from the 'open' array
            detailRows.splice(idx, 1);
        } else {
            tr.addClass('details');
            var child_html = '<div class="slider">' +
                    '<table class="table table-striped table-bordered" style="padding-left:50px;">' +
                    '<tbody>' +
                    '<td>1</td>' +
                    '<td>2</td>' +
                    '<td>3</td>' +
                    '<td>4</td>' +
                    '<td>5</td>' +
                    '<td>6</td>' +
                    '<td>7</td>' +
                    '<td>8</td>' +
                    '<td>9</td>' +
                    '<td>10</td>' +
                    '<td>11</td>' +
                    '<td>12</td>' +
                    '<td>13</td>' +
                    '<td>14</td>' +
                    '<td>15</td>' +
                    '</tbody>' +
                    '</table>' +
                    '</div>';
//                row.child(child_html.show());
            row.child(child_html).show();
            // Add to the 'open' array
            if (idx === -1) {
                detailRows.push(tr.attr('id'));
            }
        }
    });
 $(document).ready(function () {
        $('#advance_store_key').multiselect({
            buttonWidth: '359px',
            enableClickableOptGroups: true,
            enableCollapsibleOptGroups: true,
            enableFiltering: true,
            includeSelectAllOption: true
        });
        $('#advance_years').select2({
            maximumSelectionLength: 3
        });
        $(".portlet-body.form").hide();
    });

    // DataTable
    var labor_report_datatable = $('.labor-table').DataTable({
        aaSorting: [],
        "paging": false,
        "sorting": false,
        "info": false,
        "stripeClasses": [],
        "autoWidth": false,
        aoColumnDefs: [
            { "aTargets": [ 0 ], "bSortable": false },
        ]
    });
    
    $('.labor-table').largetable({
        enableMaximize: true
    });

    $(".labor-table td .tool-tip").hover(function() {
        var td = $(this).parent();
        for (i = 1; i < parseInt($(this).data('colno')); i++) {
            td = td.prev();
        }        
        td.addClass('background-royalblue');
        td.prev().addClass('background-tomato');
    }, function() {
        var td = $(this).parent();
        for (i = 1; i < parseInt($(this).data('colno')); i++) {
            td = td.prev();
        }
        td.removeClass('background-royalblue');
        td.prev().removeClass('background-tomato');
    });
</script>