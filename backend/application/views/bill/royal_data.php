<table class="table table-striped table-hover" >
    <thead>
        <tr>
            <th>
                #
            </th>
            <th>
                Store Number
            </th>
            <th>
                Type
            </th>
            <th>
                Net Sale
            </th>
            <th>
                Royalty Amt R%
            </th>
            <th>
                Adfund Amt T%
            </th>
            <th>
                Customer count
            </th>
            <th>
                System Generated <br>EFT Amt
            </th>
            <th>
                Actual EFT amt
            </th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $rowNumber = 0;
        if (isset($royal_data)) {
            foreach ($royal_data as $_storekey => $_royal_data) {
                foreach ($_royal_data as $_royalType => $_royal_type_data) {
                ?>
                <tr class="default-disabled">
                    <td>
                        <?php echo ++$rowNumber; ?>
                    </td>
                    <td>
                        <input type="text" name="royal[<?php echo $_storekey;?>][<?php echo $_royalType;?>][store_key]" class="store_key form-control" value="<?php echo $_storekey; ?>">
                        <span class="store_key_help_block help-block"></span>
                    </td>
                    <td>
                        <input type="text" name="royal[<?php echo $_storekey;?>][<?php echo $_royalType;?>][royal_type]" class="royal_type form-control" value="<?php echo $_royalType;?>">
                        <span class="royal_type_help_block help-block"></span>
                    </td>
                    <td>
                        <input type="text" name="royal[<?php echo $_storekey;?>][<?php echo $_royalType;?>][net_sales]" class="net_sales form-control" value="<?php echo $_royal_type_data['net_sales']; ?>">
                        <span class="net_sales_help_block help-block"></span>
                    </td>
                    <td>
                        <input type="text" name="royal[<?php echo $_storekey;?>][<?php echo $_royalType;?>][royalty_amt]" class="royalty_amt form-control" value="<?php echo $_royal_type_data['royalty_amt']; ?>">
                        <span class="royalty_amt_help_block help-block"></span>
                    </td>
                    <td>
                        <input type="text" name="royal[<?php echo $_storekey;?>][<?php echo $_royalType;?>][adfund_amt]" class="adfund_amt form-control" value="<?php echo $_royal_type_data['adfund_amt']; ?>">
                        <span class="adfund_amt_help_block help-block"></span>
                    </td>
                    <td>
                        <input type="text" name="royal[<?php echo $_storekey;?>][<?php echo $_royalType;?>][cust_count]" class="cust_count form-control" value="<?php echo $_royal_type_data['cust_count']; ?>">
                        <span class="cust_count_help_block help-block"></span>
                    </td>
                    <td>
                        <input type="text" name="royal[<?php echo $_storekey;?>][<?php echo $_royalType;?>][sys_eft_amt]" class="sys_eft_amt form-control" value="<?php echo $_royal_type_data['sys_eft_amt']; ?>">
                        <span class="sys_eft_amt_help_block help-block"></span>
                    </td>
                    <td>
                        <input type="text" name="royal[<?php echo $_storekey;?>][<?php echo $_royalType;?>][actual_eft_amt]" class="actual_eft_amt form-control" value="<?php echo $_royal_type_data['actual_eft_amt']; ?>">
                    </td>
                    <?php if(!$_royal_type_data['actual_eft_amt']): ?>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm mt-ladda-btn ladda-button btn-circle" data-style="expand-right" data-size="s" onclick="roy.getDataFromOtherSite(this)">
                                <span class="ladda-label">Verify Amount</span>
                                <span class="ladda-spinner"></span>
                            </button>
                        </td>
                        <td class="final_action"></td>
                    <?php else: ?>
                        <td><span class="label label-sm label-success label-mini"> Royalty paid </span></td>
                        <td class="final_action"><i class="fa fa-check" style="font-size:36px;color:green;margin:10px"></i></td>
                    <?php endif; ?>

                </tr>
                <?php
                }
            }
        }
        ?>
    </tbody>
</table>