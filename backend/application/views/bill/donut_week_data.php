<?php
if (isset($status) && $status == 'success'):
?>
<div class="col-md-12 mt10">
    <div class="form-group">
        <label for="store" class="col-md-2 control-label">Start Date</label>
        <div class="col-md-4">
            <input type="text" name="start_date" class="store_key form-control" value="<?php echo $start_date;?>">
        </div>
        <label for="store" class="col-md-2 control-label">End Date</label>
        <div class="col-md-4">
            <input type="text" name="end_date" class="end_date form-control" value="<?php echo $end_date;?>">
        </div>
    </div>
</div>
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
                        <input type="text" name="donut[<?php echo $key; ?>][bill_amt]" class="bill_amt form-control" value="<?php echo $_donut_data['bill_amt']; ?>">
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
