<style>
    .sanpshot_tbl{
        margin-top: 20px;
    }
    .sanpshot_tbl .title{
        background-color: yellow;
        font-weight: bold;
    }
    .sanpshot_tbl .tr_heading{
        font-weight: 800;
    }
    .sanpshot_tbl .tr_sub_heading{
        font-weight: 700;
    }
    .sanpshot_tbl td, .sanpshot_tbl th{
        font-size: 12px !important;
    }
    .red-color {
        background: red;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered sanpshot_tbl">
            <thead>
                <tr>
                    <td class="title" colspan="14">
                        Sales Comparison
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="3" class="tr_heading" align='center'>Dunkin Net Sales</td>
                    <td colspan="3" class="tr_heading" align='center'>Baskin Net Sales</td>
                    <td colspan="3" class="tr_heading" align='center'>Customer Count</td>
                    <td colspan="4" class="tr_heading" align='center'>Product Mix(weekly)</td>
                </tr>
                <tr>
                    <td class="tr_sub_heading">Store #</td>
                    <td class="tr_sub_heading"><?php echo $current_date; ?></td>
                    <td class="tr_sub_heading"><?php echo $previous_date; ?></td>
                    <td class="tr_sub_heading">%</td>
                    <td class="tr_sub_heading"><?php echo $current_date; ?></td>
                    <td class="tr_sub_heading"><?php echo $previous_date; ?></td>
                    <td class="tr_sub_heading">%</td>
                    <td class="tr_sub_heading"><?php echo $current_date; ?></td>
                    <td class="tr_sub_heading"><?php echo $previous_date; ?></td>
                    <td class="tr_sub_heading">%</td>
                    <td class="tr_sub_heading">Beverage</td>
                    <td class="tr_sub_heading">Donuts</td>
                    <td class="tr_sub_heading">Sandwich</td>
                    <td class="tr_sub_heading">Bagel & CC</td>
                </tr>
            </thead>
            <tbody>
                <?php
                        $dd_retail_net_sales_total              = 0;
                    $previous_dd_retail_net_sales_total     = 0;
                    $dd_retail_net_sales_per_total          = 0;

                    $br_retail_gross_sales_total            = 0;
                    $previous_br_retail_gross_sales_total   = 0;
                    $br_retail_gross_sales_per_total        = 0;

                    $trans_count_qty_total                  = 0;
                    $previous_trans_count_qty_total         = 0;
                    $trans_count_qty_per_total              = 0;

                    $d_beverage_subtotal_total              = 0;
                    $d_donuts_subtotal_total                = 0;
                    $d_sandwich_subtotal_total              = 0;
                    $d_bagel_cc_subtotal_total              = 0;

                    //For Loss prevention
                    $gross_sales_total = 0;
                    $gross_sales_percentage_total = 0;
                    $refunds_qty_total = 0;
                    $refunds_amt_total = 0;
                    $refunds_percentage_gross_total = 0;
                    $discounts_qty_total = 0;
                    $discounts_amt_total = 0;
                    $discounts_percentage_gross_total = 0;
                    $coupon_qty_total = 0;
                    $coupon_amt_total = 0;
                    $coupon_percentage_gross_total = 0;
                    $net_auto_disc_amt_total = 0;
                    $net_auto_disc_percentage_total = 0;

                       //For Loss prevention
                    $gift_qty_total = 0;
                    $gift_amt_total = 0;
                    $sales_qty_total = 0;
                    $sales_amt_total = 0;
                    $item_deletion_before_qty_total = 0;
                    $item_deletion_before_amt_total = 0;
                    $item_deletion_after_qty_total = 0;
                    $item_deletion_after_amt_total = 0;
                    $cancel_txns_qty_total = 0;
                    $cancel_txns_amt_total = 0;

                    //For Bakery Count
                    $donuts_order_total = 0;
                    $donuts_sold_total = 0;
                    $donuts_thrown_percentage_total = 0;
                    $munchinks_order_total = 0;
                    $munchinks_sold_total = 0;
                    $fancies_order_total = 0;
                    $fancies_sold_total = 0;

                    //For GSS-OSAT Reviews
                    $beverage_total = 0;
                    $food_total = 0;
                    $sos_total = 0;
                    $cleanliness_total = 0;
                    $friendly_total = 0;
                    $noofsurvey_total = 0;
                    $days45_total = 0;
                    $days_3month_total = 0;
                    $days_ytd_total = 0;

                    //For Expresso
                    $expresso_qty_total = 0;
                    $expresso_amt_total = 0;
                foreach ($stores as $_stores) {
                    $posData = isset($pos_data[$_stores->key]) ? json_decode($pos_data[$_stores->key]) : [];
                    $posPreviousData = isset($pos_previous_data[$_stores->key]) ? json_decode($pos_previous_data[$_stores->key]) : [];
                    ?>
                    <tr>
                        <th><?php echo $_stores->key; ?></th>

                        <!-- Dunkin Net Sales -->
                        <td>
                            <?php
                            $dd_retail_net_sales = $posData ? $posData->dd_retail_net_sales : 0;
                              $dd_retail_net_sales_total += $dd_retail_net_sales;
                            echo showInDollar($dd_retail_net_sales);
                            ?>
                        </td>
                        <td>
                            <?php
                            $previous_dd_retail_net_sales = $posPreviousData ? $posPreviousData->dd_retail_net_sales : 0;
                            $previous_dd_retail_net_sales_total += $previous_dd_retail_net_sales;
                            echo showInDollar($previous_dd_retail_net_sales);
                            ?>
                        </td>
                        <td><?php 
                        $dd_retail_net_sales_per =  ( $previous_dd_retail_net_sales ? (($dd_retail_net_sales - $previous_dd_retail_net_sales) / $previous_dd_retail_net_sales) : 0 );
                        $dd_retail_net_sales_per_total += $dd_retail_net_sales_per;
                        echo showInPercentage($dd_retail_net_sales_per);
                        ?></td>

                        <!-- Baskin Net Sales -->
                        <td>
                            <?php
                            $br_retail_gross_sales = $posData ? $posData->br_retail_gross_sales : 0;
                            $br_retail_gross_sales_total += $br_retail_gross_sales;
                            echo showInDollar($br_retail_gross_sales);
                            ?>
                        </td>
                        <td>
                            <?php
                            $previous_br_retail_gross_sales = $posPreviousData ? $posPreviousData->br_retail_gross_sales : 0;
                            $previous_br_retail_gross_sales_total += $previous_br_retail_gross_sales;
                            echo showInDollar($previous_br_retail_gross_sales);
                            ?>
                        </td>
                        <td><?php $br_retail_gross_sales_per = ($previous_br_retail_gross_sales ? (($br_retail_gross_sales - $previous_br_retail_gross_sales) / $previous_br_retail_gross_sales) : 0); 
                            $br_retail_gross_sales_per_total += $br_retail_gross_sales_per;
                            echo showInPercentage($br_retail_gross_sales_per);
                        ?></td>

                        <!-- Customer Count -->
                        <td>
                            <?php
                            $trans_count_qty = $posData ? $posData->trans_count_qty : 0;
                            $trans_count_qty_total += $trans_count_qty;
                            echo showInDollar($trans_count_qty);
                            ?>
                        </td>
                        <td>
                            <?php
                            $previous_trans_count_qty = $posPreviousData ? $posPreviousData->trans_count_qty : 0;
                            $previous_trans_count_qty_total += $previous_trans_count_qty;
                            echo showInDollar($previous_trans_count_qty);
                            ?>
                        </td>
                        <td><?php $trans_count_qty_per = ($previous_trans_count_qty ? (($trans_count_qty - $previous_trans_count_qty) / $previous_trans_count_qty) : 0); 
                            $trans_count_qty_per_total += $trans_count_qty_per;
                            echo showInPercentage($trans_count_qty_per);
                        ?></td>

                        <!-- Product Mix(weekly) -->
                        <td>
                            <?php
                            $d_beverage_subtotal = $posData ? $posData->d_beverage_subtotal : 0;
                            $beverage = ($dd_retail_net_sales ? ($d_beverage_subtotal/$dd_retail_net_sales) * 100 : 0) ;
                            $d_beverage_subtotal_total += $beverage;
                            echo showInPercentage($beverage);
                            ?>
                        </td>
                        <td>
                            <?php
                            $d_donuts_subtotal = $posData ? $posData->d_donuts_subtotal : 0;
                            $donuts = $dd_retail_net_sales ? ($d_donuts_subtotal / $dd_retail_net_sales) * 100 : 0;
                            $d_donuts_subtotal_total += $donuts;
                             echo showInPercentage($donuts);
                            ?>
                        </td>
                        <td>
                            <?php
                            $d_sandwich_subtotal = $posData ? $posData->d_donuts_subtotal : 0;
                            $sandwich = $d_sandwich_subtotal ? ($d_donuts_subtotal/$d_sandwich_subtotal) * 100 : 0;
                            $d_sandwich_subtotal_total += $sandwich;
                            echo showInPercentage($sandwich);
                            ?>
                        </td>
                        <td>
                            <?php
                            $d_bagel_cc_subtotal = $posData ? $posData->d_bagel_cc_subtotal : 0;
                            $bagel = $dd_retail_net_sales ? ($d_bagel_cc_subtotal/$dd_retail_net_sales) * 100 : 0;
                            $d_bagel_cc_subtotal_total += $bagel;
                            echo showInPercentage($bagel);
                            ?>
                        </td>
                    </tr>
                    <?php
                }
                ?>
                    <tr>
                        <th>Network</th>
                        <th><?php echo showInDollar($dd_retail_net_sales_total); ?></th>
                        <th><?php echo showInDollar($previous_dd_retail_net_sales_total); ?></th>
                        <th><?php
                      echo $dd_retail_net_sales_total > 0 ? showInPercentage(round(($dd_retail_net_sales_total - $previous_dd_retail_net_sales_total) / $dd_retail_net_sales_total) * 100) : showInPercentage(0) ;

                       ?></th>
                        <th><?php echo showInDollar($br_retail_gross_sales_total); ?></th>
                        <th><?php echo showInDollar($previous_br_retail_gross_sales_total); ?></th>
                        <th><?php
                         echo $br_retail_gross_sales_total > 0 ? showInPercentage(round(($br_retail_gross_sales_total - $previous_br_retail_gross_sales_total) / $br_retail_gross_sales_total) * 100) : showInPercentage(0);
                        ?></th>
                        <th><?php echo showInDollar($trans_count_qty_total); ?></th>
                        <th><?php echo showInDollar($previous_trans_count_qty_total); ?></th>
                        <th><?php
                        echo $trans_count_qty_total > 0 ? showInPercentage(round(($trans_count_qty_total - $previous_trans_count_qty_total) / $trans_count_qty_total) * 100) : showInPercentage(0);
                        ?></th>
                        <th><?php echo showInPercentage(round($d_beverage_subtotal_total /10)); ?></th>
                        <th><?php echo showInPercentage(round($d_donuts_subtotal_total / 10)); ?></th>
                        <th><?php echo showInPercentage(round($d_sandwich_subtotal_total / 10)); ?></th>
                        <th><?php echo showInPercentage(round($d_bagel_cc_subtotal_total / 10)); ?></th>
                    </tr>
            </tbody>
        </table>
        <table class="table table-bordered sanpshot_tbl">
            <thead>
                <tr>
                    <td class="title" colspan="14">
                       Loss Prevention
                    </td>
                </tr>
                <tr>
                    <td colspan="2">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td colspan="3" class="tr_heading" align='center'>Refunds</td>
                    <td colspan="3" class="tr_heading" align='center'>Discounts</td>
                    <td colspan="3" class="tr_heading" align='center'>Coupons</td>
                    <td colspan="2" class="tr_heading" align='center'>Net Auto Disc.</td>
                </tr>
                <tr>
                    <td class="tr_sub_heading">Store #</td>
                    <td class="tr_sub_heading">Gross Sales</td>
                    <td class="tr_sub_heading">%</td>
                    <td class="tr_sub_heading">Qty</td>
                    <td class="tr_sub_heading">Amt</td>
                    <td class="tr_sub_heading">% of Gross</td>
                    <td class="tr_sub_heading">Qty</td>
                    <td class="tr_sub_heading">Amt</td>
                    <td class="tr_sub_heading">% of Gross</td>
                    <td class="tr_sub_heading">Qty</td>
                    <td class="tr_sub_heading">Amt</td>
                    <td class="tr_sub_heading">% of Gross</td>
                    <td class="tr_sub_heading">Amt</td>
                    <td class="tr_sub_heading">% of Gross</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($stores as $_stores) {
                    $posData = isset($pos_data[$_stores->key]) ? json_decode($pos_data[$_stores->key]) : [];
                    $posPreviousData = isset($pos_previous_data[$_stores->key]) ? json_decode($pos_previous_data[$_stores->key]) : [];
                    $grossSales = $posData ? ($posData->br_retail_gross_sales + $posData->dd_retail_gross_sales) : 0;
                    ?>
                    <tr>
                        <th><?php echo $_stores->key; ?></th>
                        <td>
                            <?php echo showInDollar($grossSales);
                             $gross_sales_total += $grossSales;
                            ?>
                        </td>
                        <td><?php echo showInPercentage(100); ?></td>

                        <!-- Refunds -->
                        <td><?php $refunds_qty = $posData ? $posData->refunds_qty : 0; 
                          echo $refunds_qty;
                            $refunds_qty_total += $refunds_qty;
                        ?></td>
                        <td class="<?php echo ($posData && $posData->refunds_amount > $refund_amount_limit) ? 'red-color' : ''; ?>">
                            <?php
                            $refunds_amount = $posData ? $posData->refunds_amount : 0;
                            $refunds_amt_total += $refunds_amount;
                            echo showInDollar($refunds_amount);
                            ?>
                        </td>
                        <td><?php $refunds_percentage_gross = ($grossSales ? ($refunds_amount/$grossSales) : 0);
                            echo showInDollar($refunds_percentage_gross);
                            $refunds_percentage_gross_total += $refunds_percentage_gross;
                        ?></td>

                        <!-- Discounts -->
                        <td><?php $discounts_qty =  $posData ? $posData->discounts_qty : 0;
                            echo $discounts_qty;
                            $discounts_qty_total += $discounts_qty;
                            ?></td>
                        <td>
                            <?php
                            $discounts_amount = $posData ? $posData->discounts_amount : 0;
                            $discounts_amt_total += $discounts_amount;
                            echo showInDollar($discounts_amount);
                            ?>
                        </td>
                        <td><?php $discount_percentage = ($grossSales ? ($discounts_amount/$grossSales) : 0); 
                            echo showInPercentage($discount_percentage);
                            $discounts_percentage_gross_total += $discount_percentage;
                            ?></td>

                        <!-- Discounts -->
                        <td><?php $coupon_qty = $posData ? $posData->coupons_qty : 0; 
                            echo $coupon_qty;
                            $coupon_qty_total += $coupon_qty;
                            ?></td>
                        <td>
                            <?php
                            $coupons_amount = $posData ? $posData->coupons_amount : 0;
                            $coupon_amt_total += $coupons_amount;
                            echo showInDollar($coupons_amount);
                            ?>
                        </td>
                        <td><?php $coupon_percentage = showInPercentage($grossSales ? ($coupons_amount/$grossSales) : 0); 
                         echo $coupon_percentage;
                        ?></td>

                        <!-- Net Auto Disc. -->
                        <td>
                            <?php
                            $net_autodetect_disc_amount = $posData ? $posData->net_autodetect_disc_amount : 0;
                            echo showInDollar($net_autodetect_disc_amount);
                            $net_auto_disc_amt_total += $net_autodetect_disc_amount;
                            ?>
                        </td>
                        <td><?php echo showInPercentage($grossSales ? ($net_autodetect_disc_amount/$grossSales) : 0); ?></td>
                    </tr>
                    <?php
                }
                ?>
                      <tr>
                        <th>Network</th>
                        <th><?php echo showInDollar($gross_sales_total); ?></th>
                        <th><?php echo showInPercentage($gross_sales_percentage_total); ?></th>
                        <th><?php echo showInDollar($refunds_qty_total); ?></th>
                        <th><?php echo showInDollar($refunds_amt_total); ?></th>
                        <th><?php
                        $refunds_percentage = $gross_sales_total > 0 ? $refunds_amt_total / $gross_sales_total : 0;
                        echo showInPercentage(round($refunds_percentage * 100)); ?></th>
                        <th><?php echo showInDollar($discounts_qty_total); ?></th>
                        <th><?php echo showInDollar($discounts_amt_total); ?></th>
                        <th><?php
                        $discount_percentage = $gross_sales_total > 0 ? $discounts_amt_total /$gross_sales_total : 0;
//                        echo showInPercentage(($discount_percentage * 100));
                        echo $gross_sales_total > 0 ? showInPercentage(round($discounts_amt_total /$gross_sales_total * 100)) : showInPercentage(0);
                        ?></th>
                        <th><?php echo showInDollar($coupon_qty_total); ?></th>
                        <th><?php echo showInDollar($coupon_amt_total); ?></th>
                        <th><?php
                        $coupon_percentage = $gross_sales_total > 0 ? $refunds_amt_total / $gross_sales_total : 0;
                        echo showInPercentage(round($coupon_percentage  * 100));
                        ?></th>
                        <th><?php echo showInDollar($net_auto_disc_amt_total); ?></th>
                        <th><?php
                        $net_auto_disc_percentage = $gross_sales_total > 0 ? $net_auto_disc_amt_total / $gross_sales_total : 0;
                        echo showInPercentage(round($net_auto_disc_percentage * 100)); ?></th>
                    </tr>
            </tbody>
            
        </table>
        <table class="table table-bordered sanpshot_tbl">
            <thead>
                <tr>
                    <td class="title" colspan="14">
                       Loss Prevention
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="2" class="tr_heading">Gift Card Sales</td>
                    <td colspan="2" class="tr_heading" align='center'>No Sales</td>
                    <td colspan="2" class="tr_heading" align='center'>Item Deletions before Total</td>
                    <td class="tr_heading" align='center'>% of Gross</td>
                    <td colspan="2" class="tr_heading" align='center'>Item Deletions after Total</td>
                    <td class="tr_heading" align='center'>% of Gross</td>
                    <td colspan="2" class="tr_heading" align='center'>Canceled Transactions</td>
                    <td class="tr_heading" align='center'>% of Gross</td>
                </tr>
                <tr>
                    <td class="tr_sub_heading">Store #</td>
                    <td class="tr_sub_heading">Qty</td>
                    <td class="tr_sub_heading">Amt</td>
                    <td class="tr_sub_heading">Qty</td>
                    <td class="tr_sub_heading">Amt</td>
                    <td class="tr_sub_heading">Qty</td>
                    <td class="tr_sub_heading">Amt</td>
                    <td class="tr_sub_heading">&nbsp;</td>
                    <td class="tr_sub_heading">Qty</td>
                    <td class="tr_sub_heading">Amt</td>
                    <td class="tr_sub_heading">&nbsp;</td>
                    <td class="tr_sub_heading">Qty</td>
                    <td class="tr_sub_heading">Amt</td>
                    <td class="tr_sub_heading">&nbsp;</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($stores as $_stores) {
                    $posData = isset($pos_data[$_stores->key]) ? json_decode($pos_data[$_stores->key]) : [];
                    $posPreviousData = isset($pos_previous_data[$_stores->key]) ? json_decode($pos_previous_data[$_stores->key]) : [];
                    $grossSales = $posData ? ($posData->br_retail_gross_sales + $posData->dd_retail_gross_sales) : 0;
                    ?>
                    <tr>
                        <th><?php echo $_stores->key; ?></th>

                        <!-- Gift Card Sales -->
                        <td><?php $gift_cars_qty = ($posData ? $posData->gift_card_sales_qty : 0);
                          echo showInDollar($gift_cars_qty);
                            $gift_qty_total += $gift_cars_qty;
                        ?></td>
                        <td><?php $gift_cars_Amt = ($posData ? $posData->gift_card_sales_amount : 0);
                          echo showInDollar($gift_cars_Amt);
                            $gift_amt_total += $gift_cars_Amt;
                        ?></td>

                        <!-- No Sales -->
                        <td><?php $sales_qty = ($posData ? $posData->no_sale_transactions_qty : 0); 
                          echo showInDollar($sales_qty);
                            $sales_qty_total += $sales_qty;
                        ?></td>
                        <td><?php $sales_amt = ($posData ? $posData->no_sale_transactions_amount : 0); 
                          echo showInDollar($sales_amt);
                            $sales_amt_total += $sales_amt;
                        ?></td>

                        <!-- Item Deletions before Total -->
                        <td><?php $item_deletion_before_qty = ($posData ? $posData->item_deletions_before_total_qty : 0); 
                        echo showInDollar($item_deletion_before_qty);
                        $item_deletion_before_qty_total += $item_deletion_before_qty;
                        ?></td>
                        <td>
                            <?php
                            $item_deletions_before_total_amount = $posData ? $posData->item_deletions_before_total_amount : 0;
                             echo showInDollar($item_deletions_before_total_amount);
                                $item_deletion_before_amt_total += $item_deletions_before_total_amount; ?>
                        </td>
                        <td><?php echo showInPercentage($grossSales ? ($item_deletions_before_total_amount/$grossSales) : 0); ?></td>

                        <!-- Item Deletions after Total -->
                        <td><?php $item_deletion_after_qty = ($posData ? $posData->item_deletions_after_total_qty : 0);
                        echo showInDollar($item_deletion_after_qty);
                            $item_deletion_after_qty_total += $item_deletion_after_qty;
                        ?></td>
                        <td>
                            <?php
                            $item_deletions_after_total_qty_amount = $posData ? $posData->item_deletions_after_total_qty_amount : 0;
                            echo showInDollar($item_deletion_after_qty);
                            $item_deletion_after_qty_total += $item_deletion_after_qty;
                            ?>
                        </td>
                        <td><?php echo showInPercentage($grossSales ? ($item_deletions_after_total_qty_amount/$grossSales) : 0); 
                        
                        ?></td>

                        <!-- Canceled Transactions -->
                        <td><?php $cancel_txns_qty = ($posData ? $posData->cancelled_transactions_qty : 0); 
                            echo showInDollar($cancel_txns_qty);
                            $cancel_txns_qty_total += $cancel_txns_qty;
                            ?>
                        </td>
                        <td>
                            <?php
                            $cancelled_transactions_amount = $posData ? $posData->cancelled_transactions_amount : 0;
                            echo showInDollar($cancelled_transactions_amount);
                            $cancel_txns_amt_total += $cancelled_transactions_amount;
                            ?>
                        </td>
                        <td><?php echo showInPercentage($grossSales ? ($cancelled_transactions_amount/$grossSales) : 0); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
             <tr>
                               <th>Network</th>
                        <th><?php echo showInDollar($gift_qty_total); ?></th>
                        <th><?php echo showInPercentage($gift_amt_total); ?></th>
                        <th><?php echo showInDollar($sales_qty_total); ?></th>
                        <th><?php echo showInDollar($sales_amt_total); ?></th>
                        <th><?php echo showInDollar($item_deletion_before_qty_total); ?></th>
                        <th><?php echo showInDollar($item_deletion_before_amt_total); ?></th>
                        <th><?php
                        $item_deletion_before_percentage = $gross_sales_total > 0 ? $item_deletion_before_amt_total / $gross_sales_total : 0;
                        echo showInPercentage(round($item_deletion_before_percentage * 100)); ?></th>
                        <th><?php echo showInDollar($item_deletion_after_qty_total); ?></th>
                        <th><?php echo showInDollar($item_deletion_after_amt_total); ?></th>
                        <th><?php
                        $item_deletion_after_percentage = $gross_sales_total > 0 ? $item_deletion_after_amt_total /$gross_sales_total : 0;
                        echo showInPercentage(round($item_deletion_after_percentage * 100));
                        ?></th>

                        <th><?php echo showInDollar($cancel_txns_qty_total); ?></th>
                        <th><?php echo showInDollar($cancel_txns_amt_total); ?></th>
                         <th><?php
                        $cancel_txns_percentage = $gross_sales_total > 0 ? $cancel_txns_amt_total / $gross_sales_total : 0;
                        echo showInPercentage(round($cancel_txns_percentage  * 100));
                        ?></th>
                        </tr>
        </table>
        <table class="table table-bordered sanpshot_tbl">
            <thead>
                <tr>
                    <td class="title" colspan="14">
                      Bakery Count
                    </td>
                </tr>
                <tr>
                    <td>PC#</td>
                    <td>ORDER</td>
                    <td>SOLD</td>
                    <td>THROWN</td>
                    <td>THROWN %</td>
                    <td></td>
                     <td>PC#</td>
                    <td>ORDER</td>
                    <td>SOLD</td>
                    <td>THROWN</td>
                    <td>PC#</td>
                    <td>ORDER</td>
                    <td>SOLD</td>
                    <td>THROWN</td>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($stores as $_stores) {
                    ?>
                    <tr>
                        <th><?php echo $_stores->key; ?></th>

                        <!-- DONUTS -->
                        <td>
                            <?php
                            $totalDonut = isset($donut_count[$_stores->key]) ? $donut_count[$_stores->key]->donut_total : 0;
                            echo showInDollar($totalDonut);
                            $donuts_order_total += $totalDonut;
                            ?>
                        </td>
                        <td>
                            <?php
                            $soldDonut = isset($donut_count[$_stores->key]) ? $donut_count[$_stores->key]->donut_sale : 0;
                            echo showInDollar($soldDonut);
                            $donuts_sold_total += $soldDonut;
                            ?>
                        </td>
                        <td><?php echo showInDollar($totalDonut - $soldDonut); ?></td>
                        <td><?php $donuts_thrown_percentage = ($soldDonut ? ($totalDonut - $soldDonut)/$soldDonut : 0); 
                            echo showInPercentage($donuts_thrown_percentage);
                            $donuts_thrown_percentage_total += $donuts_thrown_percentage;
                            ?>
                        </td>
                        <td></td>

                        <!-- MUNCHKINS -->
                        <th><?php echo $_stores->key; ?></th>
                        <td>
                            <?php
                            $totalMunchkins = isset($donut_count[$_stores->key]) ? $donut_count[$_stores->key]->munckins_total : 0;
                            echo showInDollar($totalMunchkins);
                            $munchinks_order_total += $totalMunchkins;
                            ?>
                        </td>
                        <td>
                            <?php
                            $soldMunchkins = isset($donut_count[$_stores->key]) ? $donut_count[$_stores->key]->munckins_sale : 0;
                            echo showInDollar($soldMunchkins);
                            $munchinks_sold_total += $soldMunchkins;
                            ?>
                        </td>
                        <td><?php echo showInDollar($totalMunchkins - $soldMunchkins); ?></td>

                        <!-- FANCIES -->
                        <th><?php echo $_stores->key; ?></th>
                        <td>
                            <?php
                            $totalFancy = isset($donut_count[$_stores->key]) ? $donut_count[$_stores->key]->fancy_total : 0;
                            echo showInDollar($totalFancy);
                            $fancies_order_total += $totalFancy;
                            ?>
                        </td>
                        <td>
                            <?php
                            $soldFancy = isset($donut_count[$_stores->key]) ? $donut_count[$_stores->key]->fancy_sale : 0;
                            echo showInDollar($soldFancy);
                            $fancies_sold_total += $soldFancy;
                            ?>
                        </td>
                        <td><?php echo showInDollar($totalFancy - $soldFancy); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
              <tr>
                    <th>Network</th>
                    <th><?php echo showInDollar($donuts_order_total);?></th>
                    <th><?php echo showInDollar($donuts_sold_total);?></th>
                    <th><?php echo showInDollar($donuts_order_total - $donuts_sold_total);?></th>
                    <th><?php echo showInPercentage(($donuts_thrown_percentage_total) / count($stores));?></th>
                    <th>&nbsp;</th>
                      <th>Network</th>
                      <th><?php echo showInDollar($munchinks_order_total);?></th>
                      <th><?php echo showInDollar($munchinks_sold_total);?></th>
                      <th><?php echo showInDollar($munchinks_order_total - $munchinks_sold_total);?></th>
                       <th>Network</th>
                         <th><?php echo showInDollar($fancies_order_total);?></th>
                      <th><?php echo showInDollar($fancies_sold_total);?></th>
                      <th><?php echo showInDollar($fancies_order_total - $fancies_sold_total);?></th>

                </tr>
        </table>
    </div>
</div>
<script>
    /*$(document).ready(function () {
        $(".datepicker").datepicker({
            format : "dd-mm-yyyy"
        });
        $("#tblListing").dataTable();
    })*/
</script>