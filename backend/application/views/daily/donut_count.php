<div class="row">
    <div class="col-md-12 board">
        <?php echo isset($advance_search_html) ? $advance_search_html : ''; ?>
        <?php if(!$special_day_selected): //Store Tabbing View ?>
            <div class="board-inner">
                <ul class="nav nav-tabs" id="myTab">
                    <div class="owl_1 owl-carousel owl-theme">
                        <?php
                        $count = 0;
                        foreach ($stores as $_stores) {
                            ?>
                            <div class="item">
                                <li class="<?php echo ++$count == 1 ? 'active' : ''; ?>">
                                    <a data-toggle="tab" href="#<?php echo trim($_stores->key);?>" title="welcome">
                                        <span class="round-tabs one"><?php echo $_stores->key; ?></span>
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
                foreach ($stores as $_stores) {
                    ?>
                    <div class="tab-pane fade <?php echo ++$count == 1 ? 'in active' : ''; ?>" id="<?php echo trim($_stores->key); ?>" style="overflow-x:auto;">
                        <table class="table table-striped table-bordered" id="tblListing">
                            <thead>
                                <tr>
                                    <th rowspan=3 ></th>
                                    <th rowspan=3 >Date</th>
                                    <th colspan=<?php echo count($advanceYear) * 2; ?>><center>Donuts</center></th>
                                    <th colspan=<?php echo count($advanceYear) * 2; ?>><center>Fancy</center></th>
                                    <th colspan=<?php echo count($advanceYear) * 2; ?>><center>Munkins</center></th>
                                </tr>
                                <tr>
                                    <th colspan=<?php echo count($advanceYear); ?>><center>Order</center></th>
                                    <th colspan=<?php echo count($advanceYear); ?>><center>Sale</center></th>
                                    <th colspan=<?php echo count($advanceYear); ?>><center>Order</center></th>
                                    <th colspan=<?php echo count($advanceYear); ?>><center>Sale</center></th>
                                    <th colspan=<?php echo count($advanceYear); ?>><center>Order</center></th>
                                    <th colspan=<?php echo count($advanceYear); ?>><center>Sale</center></th>
                                </tr>
                                <tr>
                                    <?php
                                    foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                    ?>
                                        <th style="background: #<?php echo $_advanceKey; ?>"><?php echo $_advanceYear; ?></th>
                                    <?php } ?>
                                    <?php
                                    foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                    ?>
                                        <th style="background: #<?php echo $_advanceKey; ?>"><?php echo $_advanceYear; ?></th>
                                    <?php } ?>
                                    <?php
                                    foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                    ?>
                                        <th style="background: #<?php echo $_advanceKey; ?>"><?php echo $_advanceYear; ?></th>
                                    <?php } ?>
                                    <?php
                                    foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                    ?>
                                        <th style="background: #<?php echo $_advanceKey; ?>"><?php echo $_advanceYear; ?></th>
                                    <?php } ?>
                                    <?php
                                    foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                    ?>
                                        <th style="background: #<?php echo $_advanceKey; ?>"><?php echo $_advanceYear; ?></th>
                                    <?php } ?>
                                    <?php
                                    foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                    ?>
                                        <th style="background: #<?php echo $_advanceKey; ?>"><?php echo $_advanceYear; ?></th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (isset($donutdata['yearWiseDates']) && !empty($donutdata['yearWiseDates'])) {
                                    // echo '<pre>';print_r($donutdata['yearWiseDates']);die;
                                    foreach ($donutdata['yearWiseDates'][$donutdata['baseDateYear']] as $weekNumber => $weekData) {
                                        foreach ($weekData as $dayNumber => $_date) {
                                            $total_Amount = 0;
                                            ?>
                                            <tr>
                                                <?php if($dayNumber == 0): ?>
                                                    <td rowspan="7"><?php echo "W".($weekNumber+1); ?></td>
                                                <?php endif; ?>
                                                <td><?php echo date('D', strtotime($_date)); ?><br><?php echo date('m/d/y',strtotime($_date)); ?></td>
                                                <?php
                                                if (sizeof($dynamic_column)) {
                                                    foreach ($dynamic_column as $key => $value) {
                                                        foreach ($advanceYear as $_advanceKey => $_advanceYear) {
                                                        ?>
                                                        <td style="background: #<?php echo $_advanceKey; ?>">
                                                            <?php
                                                            $storeKey    = (int)$_stores->key;
                                                            if(isset($donutdata['yearWiseDates'][$_advanceYear][$weekNumber][$dayNumber]))
                                                            {
                                                                $dayDate    =  $donutdata['yearWiseDates'][$_advanceYear][$weekNumber][$dayNumber];
                                                                $dateKey    = str_replace("-", "", date("d-m-Y", strtotime($dayDate)));
                                                                $year       = date("Y", strtotime($dayDate));
                                                                $amount     = isset($donutdata['data'][$storeKey][$year][$dateKey][$value]) ? $donutdata['data'][$storeKey][$year][$dateKey][$value] : 0;
                                                                echo number_format($amount, 2);
                                                                echo "<br>";
                                                                echo date('m/d/y',strtotime($dayDate));
                                                                $total_Amount += $amount;
                                                            }
                                                            ?>
                                                        </td>
                                                        <?php
                                                        }
                                                    }
                                                }
                                                ?>
                                            </tr>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                <?php }
                ?>
            </div>
        <?php else: // Special Day view?>
            <div class="board-inner">
                <table class="table table-striped table-bordered" id="tblListing">
                    <thead>
                        <tr>
                            <th rowspan=3 >Store</th>
                            <th rowspan=3 ></th>
                            <th rowspan=3 >Date</th>
                            <th colspan=2><center>Donuts</center></th>
                            <th colspan=2><center>Fancy</center></th>
                            <th colspan=2><center>Munkins</center></th>
                        </tr>
                        <tr>
                            <th><center>Order</center></th>
                            <th><center>Sale</center></th>
                            <th><center>Order</center></th>
                            <th><center>Sale</center></th>
                            <th><center>Order</center></th>
                            <th><center>Sale</center></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // echo '<pre>';print_r($donutdata['yearWiseDates']);die;
                        foreach ($stores as $_stores) {
                            foreach ($donutdata['yearWiseDates'] as $year => $dates) {
                                foreach ($dates as $_dates) {
                                ?>
                                <tr>
                                    <?php
                                    if((isset($storeKey) && (int)$_stores->key != $storeKey) || !isset($storeKey)):
                                    ?>
                                        <td rowspan=<?php echo count($donutdata['yearWiseDates'],COUNT_RECURSIVE) - count($donutdata['yearWiseDates']); ?>>
                                            <?php
                                            echo $storeKey = (int)$_stores->key;
                                            ?>
                                        </td>
                                    <?php endif; ?>
                                    <?php
                                    if((isset($specialDayYear) && trim($year) != $specialDayYear) || !isset($specialDayYear)):
                                    ?>
                                        <td rowspan=<?php echo count($donutdata['yearWiseDates'][$year]); ?>>
                                            <?php
                                            echo $specialDayYear = trim($year);
                                            ?>
                                        </td>
                                    <?php endif; ?>
                                    <td><?php echo date('m/d/Y',strtotime($_dates)); ?></td>
                                    <?php
                                    if (sizeof($dynamic_column)) {
                                        foreach ($dynamic_column as $key => $value) {
                                            ?>
                                            <td>
                                                <?php
                                                $dateKey = date('dmY',strtotime($_dates));
                                                // print_r($donutdata['data'][$storeKey][$year]);
                                                $amount  = isset($donutdata['data'][$storeKey][$year][$dateKey][$value]) ? $donutdata['data'][$storeKey][$year][$dateKey][$value] : 0;
                                                echo number_format($amount, 2);
                                                ?>
                                            </td>
                                            <?php
                                        }
                                    }
                                    ?>
                                </tr>
                                <?php
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
        <?php endif; ?>
    </div>
</div>