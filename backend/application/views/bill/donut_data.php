<?php
if (isset($status) && $status == 'success'):
?>
<table class="table table-striped table-hover" >
    <thead>
        <tr>
            <th>
                #
            </th>
            <th>
                Store Key
            </th>
            <th>
                Store Physical #/Customer
            </th>
            <th>
                Bill Date/Invoice Date
            </th>
            <th>
                Bill Number/Invoice Number
            </th>
            <th>
                Description/Order Date Range
            </th>
            <th width="5px">
                Qty
            </th>
            <th>
                Rate/Total
            </th>
            <th>
                Amount/Total
            </th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $rowNumber = 0;
        if (isset($donut_data)) {
            foreach ($donut_data as $key => $_donut_data) {
                ?>
                <tr class="default-disabled">
                    <td>
                        <?php echo ++$rowNumber; ?>
                    </td>
                    <td>
                        <input type="text" name="donut[<?php echo $key; ?>][store_key]" class="store_key form-control" value="<?php echo $_donut_data['store_key'];?>">
                    </td>
                    <td>
                        <input type="text" name="donut[<?php echo $key; ?>][store_address]" class="store_address form-control" value="<?php echo $_donut_data['store_address'];?>">
                    </td>
                    <td>
                        <input type="text" name="donut[<?php echo $key; ?>][bill_date]" class="bill_date form-control" value="<?php echo $_donut_data['bill_date']; ?>">
                    </td>
                    <td>
                        <input type="text" name="donut[<?php echo $key; ?>][bill_number]" class="bill_number form-control" value="<?php echo $_donut_data['bill_number']; ?>">
                        <input type="hidden" name="donut[<?php echo $key; ?>][bill_cat]" class="bill_cat form-control" value="<?php echo $_donut_data['bill_cat']; ?>">
                        <input type="hidden" name="donut[<?php echo $key; ?>][bill_week_start_date]" class="bill_cat form-control" value="<?php echo $_donut_data['bill_week_start_date']; ?>">
                        <input type="hidden" name="donut[<?php echo $key; ?>][bill_week_end_date]" class="bill_cat form-control" value="<?php echo $_donut_data['bill_week_end_date']; ?>">
                    </td>
                    <td>
                        <input type="text" name="donut[<?php echo $key; ?>][bill_desc]" class="bill_desc form-control" value="<?php echo $_donut_data['bill_desc']; ?>">
                    </td>
                    <td width="5px">
                        <input type="text" name="donut[<?php echo $key; ?>][bill_qty]" class="bill_qty form-control" value="<?php echo $_donut_data['bill_qty']; ?>">
                    </td>
                    <td>
                        <input type="text" name="donut[<?php echo $key; ?>][bill_rate]" class="bill_rate form-control" value="<?php echo $_donut_data['bill_rate']; ?>">
                    </td>
                    <td>
                        <input type="text" name="donut[<?php echo $key; ?>][bill_amt]" class="bill_amt form-control" value="<?php echo $_donut_data['bill_amt']; ?>">
                    </td>
                    <td class="final_action">
                        <input type="hidden" name="donut[<?php echo $key; ?>][result]" class="result form-control" value="<?php echo $_donut_data['result']; ?>">
                        <?php if($_donut_data['result'] == 'success'): ?>
                            <i class="fa fa-check" style="font-size:36px;color:green;margin:10px"></i>
                        <?php else: ?>
                            <i class="fa fa-times" style="font-size:36px;color:red;margin:10px"></i>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>
<?php
else:
    ?>
    <div class="custom-alerts alert alert-danger fade in">
        <?php echo $msg; ?>
    </div>
    <?php
endif;
?>
