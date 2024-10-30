<?php echo isset($advance_search_html) ? $advance_search_html : ''; ?>

        <div class="row">
            <div class="col-xs-12">
            <div class="board">
                <?php if($advance_status_selected == ''): ?>
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
                                if(in_array($_stores->key, $advanceStore))
                                {
                                    ?>
                                    <div class="item" style="width: 100%;">
                                        <li class="<?php echo ++$count == 1 ? 'active' : '';     ?>" style="width: 100%; padding: 0px 10px;">
                                            <a <?php echo (($_stores->status ?? null) == 'I') ? 'style="width: 100%; padding: 0px 20px; background-color: bisque"' : 'style="width: 100%; padding: 0px 20px;"'; ?> data-toggle="tab" href="#<?php echo trim($_stores->key); ?>" title="welcome">
                                                <span class="round-tabs one" style="width: 100%"><?php echo $_stores->key; ?></span>
                                            </a>
                                        </li>
                                    </div>
                                    <?php
                                }
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
                                <table class="table table-condensed sales-comparison-table" id="sales_comparision_report">
                                    <caption>
                                        <?php echo ($_stores->key == 'Accumulate' ? "Accumulate" : ucfirst($store_list[$_stores->key]) . " #{$_stores->key}") . " Sales Comparison " . implode(' - ', $advanceYear); ?>
                                        <a class="export-btn" onclick="week.exportXls();">Export XLSX</a>
                                    </caption>
                                    <thead>
                                        <tr>
                                            <th class="no-sort" data-orderable="false" style="width: 60px; margin: auto">Period</th>
                                            <th class="no-sort" data-orderable="false" style="width: 30px"></th>
                                            <th class="no-sort" data-orderable="false" style="width: 50px"><?php echo $_stores->key; ?></th>
                                            <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                            ?>
                                                <th style="background: #<?php echo $_advanceKey; ?>">DD <?php echo substr($_advanceYear, -2); ?></th>
                                            <?php } ?>
                                            <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <th>DD % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                            <?php endfor; ?>
                                            <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                            ?>
                                                <th style="background: #<?php echo $_advanceKey; ?>">BR <?php echo substr($_advanceYear, -2); ?></th>
                                            <?php } ?>
                                            <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <th>BR % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                            <?php endfor; ?>
                                            <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {

                                            ?>
                                                <th style="background: #<?php echo $_advanceKey; ?>">Total <?php echo substr($_advanceYear, -2); ?></th>
                                            <?php } ?>
                                            <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <th>Total % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                            <?php endfor; ?>
                                            <?php
                                            foreach ($advanceYear as $_advanceKey => $_advanceYear) {

                                            ?>
                                                <th style="background: #<?php echo $_advanceKey; ?>">Cust <?php echo substr($_advanceYear, -2); ?></th>
                                            <?php } ?>
                                            <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <th>Cust % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                            <?php endfor; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        sort($weeks);
                                        $periodNo = 0;
                                        $weekNo = 0;
                                        foreach ($weeks as $_weeks) {
                                            $week_number = $_weeks['week_number'];
                                            $month = date("M", strtotime($_weeks['start_of_week']));
                                            ?>
                                            <?php $isModified = ($added_week[$_advanceYear][$week_number] ?? false) == true; ?>
                                            <tr <?php echo $isModified ? 'style="background-color: bisque;"' : ''; ?>>
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
                                                <td class="details-control no-sort" data-month = "<?php echo $month; ?>" data-store-key ="<?php echo $_stores->key; ?>" data-start-date = "<?php echo $_weeks['start_of_week']; ?>" data-end-date = "<?php echo $_weeks['end_of_week']; ?>"></td>
                                                <td>
                                                    <span data-toggle="tooltip" title="<?php echo date('d M',strtotime($_weeks['start_of_week'])); ?> - <?php echo date('d-M',strtotime($_weeks['end_of_week'])); ?>">Week <?php echo trim($weekNo); ?> <?php echo showInDateFormat($_weeks['end_of_week']); ?></span>
                                                </td>

                                                <!-- dd_retail_net_sales -->
                                                <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                                <td style="background: #<?php echo $_advanceKey; ?>">
                                                    <?php $dd = $pos_data[$_stores->key][$_advanceYear][$week_number]['dd_retail_net_sales'] ?? 0; ?>
                                                    <?php echo showInDollar($dd); ?>
                                                </td>
                                                <?php endforeach; ?>                                                
                                                <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <td>
                                                    <?php $dd1 = $pos_data[$_stores->key][$yearlist[$i]][$week_number]['dd_retail_net_sales'] ?? 0; ?>
                                                    <?php $dd2 = $pos_data[$_stores->key][$yearlist[$i + 1]][$week_number]['dd_retail_net_sales'] ?? 0; ?>
                                                    <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                        <i class="tool-tip__icon"><?php echo $dd1 != 0 ? showInPercentage(($dd2 - $dd1) / $dd1 * 100) : ''; ?></i>
                                                        <p class="tool-tip__info">
                                                            <span>(<span style="color: royalblue;">DD <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">DD <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">DD <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                            <span>(<span style="color: royalblue;"><?= $dd2 ?></span> - <span style="color: tomato;"><?= $dd1 ?></span>) / <span style="color: tomato;"><?= $dd1 ?></span></span>
                                                        </p>
                                                    </div>
                                                </td>         
                                                <?php endfor; ?>

                                                <!-- br_retail_net_sales -->
                                                <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                                <td style="background: #<?php echo $_advanceKey; ?>">
                                                    <?php $br = $pos_data[$_stores->key][$_advanceYear][$week_number]['br_retail_net_sales'] ?? 0; ?>
                                                    <?php echo showInDollar($br); ?>
                                                </td>
                                                <?php endforeach; ?>                                                
                                                <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <td>
                                                    <?php $br1 = $pos_data[$_stores->key][$yearlist[$i]][$week_number]['br_retail_net_sales'] ?? 0; ?>
                                                    <?php $br2 = $pos_data[$_stores->key][$yearlist[$i + 1]][$week_number]['br_retail_net_sales'] ?? 0; ?>
                                                    <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                        <i class="tool-tip__icon"><?php echo $br1 != 0 ? showInPercentage(($br2 - $br1) / $br1 * 100) : ''; ?></i>
                                                        <p class="tool-tip__info">
                                                            <span>(<span style="color: royalblue;">BR <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">BR <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">BR <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                            <span>(<span style="color: royalblue;"><?= $br2 ?></span> - <span style="color: tomato;"><?= $br1 ?></span>) / <span style="color: tomato;"><?= $br1 ?></span></span>
                                                        </p>
                                                    </div>
                                                </td>         
                                                <?php endfor; ?>
                                                
                                                <!-- total -->
                                                <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                                <td style="background: #<?php echo $_advanceKey; ?>">
                                                    <?php $total = ($pos_data[$_stores->key][$_advanceYear][$week_number]['dd_retail_net_sales'] ?? 0) + ($pos_data[$_stores->key][$_advanceYear][$week_number]['br_retail_net_sales'] ?? 0); ?>
                                                    <?php echo showInDollar($total); ?>
                                                </td>
                                                <?php endforeach; ?>                                                
                                                <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <td>
                                                    <?php $total1 = ($pos_data[$_stores->key][$yearlist[$i]][$week_number]['dd_retail_net_sales'] ?? 0) + ($pos_data[$_stores->key][$yearlist[$i]][$week_number]['br_retail_net_sales'] ?? 0); ?>
                                                    <?php $total2 = ($pos_data[$_stores->key][$yearlist[$i + 1]][$week_number]['dd_retail_net_sales'] ?? 0) + ($pos_data[$_stores->key][$yearlist[$i + 1]][$week_number]['br_retail_net_sales'] ?? 0); ?>
                                                    <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                        <i class="tool-tip__icon"><?php echo $total1 != 0 ? showInPercentage(($total2 - $total1) / $total1 * 100) : ''; ?></i>
                                                        <p class="tool-tip__info">
                                                            <span>(<span style="color: royalblue;">Total <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">Total <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">Total <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                            <span>(<span style="color: royalblue;"><?= $total2 ?></span> - <span style="color: tomato;"><?= $total1 ?></span>) / <span style="color: tomato;"><?= $total1 ?></span></span>
                                                        </p>
                                                    </div>
                                                </td>         
                                                <?php endfor; ?>
                                                
                                                <!-- trans_count_qty -->
                                                <?php foreach ($advanceYear as $_advanceKey => $_advanceYear) : ?>
                                                <td style="background: #<?php echo $_advanceKey; ?>">
                                                    <?php $cust = $pos_data[$_stores->key][$_advanceYear][$week_number]['trans_count_qty'] ?? 0; ?>
                                                    <?php echo showInDollar($cust); ?>
                                                </td>
                                                <?php endforeach; ?>                                                
                                                <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                                <td>
                                                    <?php $cust1 = $pos_data[$_stores->key][$yearlist[$i]][$week_number]['trans_count_qty'] ?? 0; ?>
                                                    <?php $cust2 = $pos_data[$_stores->key][$yearlist[$i + 1]][$week_number]['trans_count_qty'] ?? 0; ?>
                                                    <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                        <i class="tool-tip__icon"><?php echo $cust1 != 0 ? showInPercentage(($cust2 - $cust1) / $cust1 * 100) : ''; ?></i>
                                                        <p class="tool-tip__info">
                                                            <span>(<span style="color: royalblue;">Cust <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">Cust <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">Cust <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                            <span>(<span style="color: royalblue;"><?= $cust2 ?></span> - <span style="color: tomato;"><?= $cust1 ?></span>) / <span style="color: tomato;"><?= $cust1 ?></span></span>
                                                        </p>
                                                    </div>
                                                </td>         
                                                <?php endfor; ?>
                                            </tr>
                                            <?php
                                        }
                                        ?>
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
                <?php else: ?>
                    <?php
                    $count = 0;
                    $yearlist = array_values($advanceYear);
                    ?>
                    <div class="tab-pane fade <?php echo ++$count == 1 ? 'in active' : ''; ?>" >
                        <table class="table table-condensed sales-comparison-table" id="sales_comparision_report">
                            <caption><?php echo "Sales Comparison " . implode(' - ', $advanceYear); ?></caption>
                            <thead>
                                <tr>
                                    <th class="no-sort" data-orderable="false"></th>
                                    <th>Store Key</th>
                                    <th>Time period</th>
                                    <?php
                                    foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                    ?>
                                        <th style="background: #<?php echo $_advanceKey; ?>">
                                            DD <?php echo is_numeric($_advanceYear) ? substr($_advanceYear, -2) : ''; ?>
                                            <br>
                                            <?php echo $column_data[$_advanceYear] ?? null; ?>
                                        </th>
                                    <?php } ?>
                                    <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                        <th>DD % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                    <?php endfor; ?>
                                    <?php
                                    foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                    ?>
                                        <th style="background: #<?php echo $_advanceKey; ?>">
                                            BR <?php echo is_numeric($_advanceYear) ? substr($_advanceYear, -2) : ''; ?>
                                            <br>
                                            <?php echo $column_data[$_advanceYear] ?? null; ?>
                                        </th>
                                    <?php } ?>
                                    <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                        <th>BR % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                    <?php endfor; ?>
                                    <?php
                                    foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                    ?>
                                        <th style="background: #<?php echo $_advanceKey; ?>">
                                            Total <?php echo is_numeric($_advanceYear) ? substr($_advanceYear, -2) : ''; ?>
                                            <br>
                                            <?php echo $column_data[$_advanceYear] ?? null; ?>
                                        </th>
                                    <?php } ?>
                                    <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                        <th>Total % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                    <?php endfor; ?>
                                    <?php
                                    foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                    ?>
                                        <th style="background: #<?php echo $_advanceKey; ?>">
                                            Cust <?php echo is_numeric($_advanceYear) ? substr($_advanceYear, -2) : ''; ?>
                                            <br>
                                            <?php echo $column_data[$_advanceYear] ?? null; ?>
                                        </th>
                                    <?php } ?>
                                    <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                        <th>Cust % +/-<br><?php echo substr($yearlist[$i], -2); ?>~<?php echo substr($yearlist[$i + 1], -2); ?></th>
                                    <?php endfor; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_dd_previous      = 0;
                                $total_dd_next          = 0;
                                $total_br_previous      = 0;
                                $total_br_next          = 0;
                                $total_total_previous   = 0;
                                $total_total_next       = 0;
                                $total_qty_previous     = 0;
                                $total_qty_next         = 0;
                                foreach ($advanceStore as $_advanceStore) {
                                    foreach ($row_data as $rdk => $_row_data) {
                                    ?>
                                    <tr>
                                        <th class="no-sort" data-orderable="false"></th>
                                        <td ><?php echo (isset($storeKey) && $storeKey == $_advanceStore) ? '' : $storeKey = $_advanceStore; ?></td>
                                        <td>
                                            <?php echo $row_data_label[$rdk]; ?>
                                        </td>

                                        <!-- dd_retail_net_sales -->
                                        <?php
                                        foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                        ?>
                                        <td style="background: #<?php echo $_advanceKey; ?>">
                                            <?php
                                            $dd_next = isset($pos_data[$_advanceStore][$_advanceYear][$_row_data]['dd_retail_net_sales']) ? $pos_data[$_advanceStore][$_advanceYear][$_row_data]['dd_retail_net_sales'] : 0;
                                            echo showInDollar($dd_next);
                                            $total_dd_next += $dd_next;
                                            ?>
                                        </td>
                                        <?php } ?>
                                        <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                        <td>
                                            <?php $dd1 = $pos_data[$_advanceStore][$yearlist[$i]][$_row_data]['dd_retail_net_sales'] ?? 0; ?>
                                            <?php $dd2 = $pos_data[$_advanceStore][$yearlist[$i + 1]][$_row_data]['dd_retail_net_sales'] ?? 0; ?>
                                            <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                <i class="tool-tip__icon"><?php echo $dd1 != 0 ? showInPercentage(($dd2 - $dd1) / $dd1 * 100) : ''; ?></i>
                                                <p class="tool-tip__info">
                                                    <span>(<span style="color: royalblue;">DD <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">DD <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">DD <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                    <span>(<span style="color: royalblue;"><?= $dd2 ?></span> - <span style="color: tomato;"><?= $dd1 ?></span>) / <span style="color: tomato;"><?= $dd1 ?></span></span>
                                                </p>
                                            </div>
                                        </td>         
                                        <?php endfor; ?>


                                        <!-- br_retail_net_sales -->
                                        <?php
                                        foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                        ?>
                                        <td style="background: #<?php echo $_advanceKey; ?>">
                                            <?php
                                            $br_next = isset($pos_data[$_advanceStore][$_advanceYear][$_row_data]['br_retail_net_sales']) ? $pos_data[$_advanceStore][$_advanceYear][$_row_data]['br_retail_net_sales'] : 0;
                                            echo showInDollar($br_next);
                                            $total_br_next += $br_next;
                                            ?>
                                        </td>
                                        <?php } ?>

                                        <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                        <td>
                                            <?php $br1 = $pos_data[$_advanceStore][$yearlist[$i]][$_row_data]['br_retail_net_sales'] ?? 0; ?>
                                            <?php $br2 = $pos_data[$_advanceStore][$yearlist[$i + 1]][$_row_data]['br_retail_net_sales'] ?? 0; ?>
                                            <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                <i class="tool-tip__icon"><?php echo $br1 != 0 ? showInPercentage(($br2 - $br1) / $br1 * 100) : ''; ?></i>
                                                <p class="tool-tip__info">
                                                    <span>(<span style="color: royalblue;">BR <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">BR <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">BR <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                    <span>(<span style="color: royalblue;"><?= $br2 ?></span> - <span style="color: tomato;"><?= $br1 ?></span>) / <span style="color: tomato;"><?= $br1 ?></span></span>
                                                </p>
                                            </div>
                                        </td>         
                                        <?php endfor; ?>

                                        <!-- total -->
                                        <?php
                                        foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                        ?>
                                        <td style="background: #<?php echo $_advanceKey; ?>">
                                            <?php
                                            $total_next = $br_next + $dd_next;
                                            echo showInDollar($total_next);
                                            $total_total_next += $total_next;
                                            ?>
                                        </td>
                                        <?php } ?>
                                        <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                        <td>
                                            <?php $total1 = ($pos_data[$_advanceStore][$yearlist[$i]][$_row_data]['dd_retail_net_sales'] ?? 0) + ($pos_data[$_advanceStore][$yearlist[$i]][$_row_data]['br_retail_net_sales'] ?? 0); ?>
                                            <?php $total2 = ($pos_data[$_advanceStore][$yearlist[$i + 1]][$_row_data]['dd_retail_net_sales'] ?? 0) + ($pos_data[$_advanceStore][$yearlist[$i + 1]][$_row_data]['br_retail_net_sales'] ?? 0); ?>
                                            <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                <i class="tool-tip__icon"><?php echo $total1 != 0 ? showInPercentage(($total2 - $total1) / $total1 * 100) : ''; ?></i>
                                                <p class="tool-tip__info">
                                                    <span>(<span style="color: royalblue;">Total <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">Total <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">Total <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                    <span>(<span style="color: royalblue;"><?= $total2 ?></span> - <span style="color: tomato;"><?= $total1 ?></span>) / <span style="color: tomato;"><?= $total1 ?></span></span>
                                                </p>
                                            </div>
                                        </td>         
                                        <?php endfor; ?>

                                        <!-- trans_count_qty -->
                                        <?php
                                        foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                        ?>
                                        <td style="background: #<?php echo $_advanceKey; ?>">
                                            <?php
                                            $qty_next = isset($pos_data[$_advanceStore][$_advanceYear][$_row_data]['trans_count_qty']) ? $pos_data[$_advanceStore][$_advanceYear][$_row_data]['trans_count_qty'] : 0;
                                            echo $qty_next;
                                            $total_qty_next += $qty_next;
                                            ?>
                                        </td>
                                        <?php } ?>
                                        <?php for ($i = 0; $i < sizeof($yearlist) - 1; $i++) : ?>
                                        <td>
                                            <?php $cust1 = $pos_data[$_advanceStore][$yearlist[$i]][$_row_data]['trans_count_qty'] ?? 0; ?>
                                            <?php $cust2 = $pos_data[$_advanceStore][$yearlist[$i + 1]][$_row_data]['trans_count_qty'] ?? 0; ?>
                                            <div class="tool-tip" data-colno ="<?= sizeof($yearlist) ?>">
                                                <i class="tool-tip__icon"><?php echo $cust1 != 0 ? showInPercentage(($cust2 - $cust1) / $cust1 * 100) : ''; ?></i>
                                                <p class="tool-tip__info">
                                                    <span>(<span style="color: royalblue;">Cust <?= substr($yearlist[$i + 1], -2) ?></span> - <span style="color: tomato;">Cust <?= substr($yearlist[$i], -2) ?></span>) / <span style="color: tomato;">Cust <?= substr($yearlist[$i], -2) ?></span></span></br>
                                                    <span>(<span style="color: royalblue;"><?= $cust2 ?></span> - <span style="color: tomato;"><?= $cust1 ?></span>) / <span style="color: tomato;"><?= $cust1 ?></span></span>
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
        $('[data-toggle="tooltip"]').tooltip();
    });

    var detailRows = [];
    $('#sales_comparision_report tbody').on('click', 'tr td.details-control', function () {
        var tr = $(this).closest('tr');
        if (tr.hasClass("details")) {
            tr.removeClass('details');
            tr.next(".detail-tr").remove();
        } else {
            tr.addClass('details');
            start_date = $(this).data("start-date");
            end_date = $(this).data("end-date");
            store_key = $(this).data("store-key");
            month = $(this).data("month");
            var advance_search = objectifyForm($('#advance_search_form').serializeArray());
            var postData = {
                start_date: start_date,
                end_date: end_date,
                store_key: store_key,
                month: month,
            }
            $.extend(postData, advance_search);
            $.ajax({
                'async': false,
                'type': "POST",
                'global': false,
                'dataType': 'html',
//                'dataType': 'html',
                url: site_url + 'weekly/sales_comp_weekly_data',
                data: postData,
                'success': function (response) {
                    console.log(response);
                    $(response).insertAfter(tr);
                }
            });
        }
    });
    function  objectifyForm(formArray) {
        var returnArray = {};

        for (var i = 0; i < formArray.length; i++) {
            if (returnArray[formArray[i]['name']]) {
                returnArray[formArray[i]['name']].push(formArray[i]['value']);
            } else {
                returnArray[formArray[i]['name']] = [formArray[i]['value']];
            }
        }
        return returnArray;
    }

    // DataTable
    var sales_comparision_datatable = $('.sales-comparison-table').DataTable({
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

    $('.sales-comparison-table').largetable({
        enableMaximize: true
    });

    $(".sales-comparison-table td .tool-tip").hover(function() {
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