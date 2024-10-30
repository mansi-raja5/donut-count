<div class="col-md-12">
    <div class="form-group">
        <?php echo form_label('Select Month<span class="required" aria-required="true"> * </span>', 'month', array('class' => 'col-md-4 control-label')); ?>
        <div class="col-md-4">
            <?php
            $month_arr = array("January", "February", "March", "April", "May", "Jun", "July", "August", "September", "October", "November", "December");
            $options = array();
            $options['']    = '-- Month  --';
            $options['all'] = 'ALL MONTHS';
            foreach ($month_arr as $key => $value) {
                if(isset($valid_months[++$key]) && $valid_months[$key])
                    $options[$key] = $value;
            }
            ?>
            <select required = "required" id="month" name="month" class="form-control select2me">
                <?php foreach ($options as $monthNumber => $monthName) { ?>
                    <option value="<?php echo $monthNumber; ?>"><?php echo $monthName; ?></option>
                <?php } ?>
            </select>
        </div>
    </div>
</div>