<?php
$min_year = 2000;
$max_year = 2024;
?>
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption"><i class="fa fa-filter"></i> Advance Filter</div>
    </div>
    <div class="portlet-body">
        <?php
        $attributes = array('class' => 'horizontal-form validate', 'id' => 'advance_search_form');
        echo form_open('', $attributes, array());
        ?>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3">
                    <div class="form-group">
                        <label class="control-label">Select Year:</label>
                        <?php
                        $options = array();
                        $years = field(set_value('years', NULL), $advanceYear);
                        for ($i = $min_year; $i < $max_year; $i++) {
                            $options[$i] = $i;
                        }

                        echo form_dropdown(array('multiple' => 'multiple', 'required' => 'required', 'id' => 'years', 'id' => 'advance_years', 'name' => 'advance_years', 'options' => $options, 'class' => 'form-control multi_select2', 'selected' => $years));
                        ?>

                    </div>
                </div>
                <div class="col-md-9">
                    <div class="form-group">
                        <label class="control-label">Select Date Granularity:</label>
                        <br/>
                        <label class="mt-radio mt-radio-outline">
                            <input type="radio" name="advance_status" id="status_1" value="day" class="radio" onchange="show_option(this);" <?php echo ($advance_status_selected == 'day') ? 'checked' : ''; ?> />Day<span></span></label>
                        <label class="mt-radio mt-radio-outline"   data-placement="top">
                            <input type="radio" name="advance_status" id="status_2" value="special_day" class="radio" onchange="show_option(this);" <?php echo ($advance_status_selected == 'special_day') ? 'checked' : ''; ?>>Special Day<span></span></label>
                        <label class="mt-radio mt-radio-outline"   data-placement="top">
                            <input type="radio" name="advance_status" id="status_3" value="week" class="radio" onchange="show_option(this);" <?php echo ($advance_status_selected == 'week') ? 'checked' : ''; ?>>Week<span></span></label>
                        <label class="mt-radio mt-radio-outline"   data-placement="top">
                            <input type="radio" name="advance_status" id="status_5" value="year" class="radio" onchange="show_option(this);" <?php echo ($advance_status_selected == 'year') ? 'checked' : ''; ?>>Year<span></span></label>
                        <label class="mt-radio mt-radio-outline"   data-placement="top">
                            <input type="radio" name="advance_status" id="status_6" value="custom_date" class="radio" onchange="show_option(this);" <?php echo ($advance_status_selected == 'custom_date') ? 'checked' : ''; ?>>Custom Date Range<span></span></label>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3 day_div option_div <?php echo ($advance_date_selected != '') ? '' : 'display-hide' ?>">
                    <div class="form-group">
                        <label class="control-label">Select Day:</label>
                        <?php
                        echo form_input(array('id' => 'advance_date', 'name' => 'advance_date', 'class' => 'form-control day_datepicker', 'value' => $advance_date_selected));
                        ?>
                    </div>
                </div>
                <div class="col-md-3 special_day_div option_div <?php echo ($special_day_selected != '') ? '' : 'display-hide' ?>">
                    <div class="form-group">
                        <label class="control-label">Special Day</label>
                        <?php
                        $options = array();
                        $options[''] = '-- Select Special Day  --';
                        $special_day = field(set_value('special_day', NULL), $special_day_selected);
                        if (isset($special_day_list) && !empty($special_day_list)) {
                            foreach ($special_day_list as $row) {
                                $options[$row->special_ids] = ucfirst($row->name);
                            }
                        }
                        echo form_dropdown(array('id' => 'special_day', 'name' => 'advance_special_day', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $special_day));
                        ?>
                    </div>
                </div>
                <div class="col-md-3 week_div option_div <?php echo ($advance_week_date_selected != '') ? '' : 'display-hide' ?>">
                    <div class="form-group">
                        <label class="control-label">Select Week Ending Date:</label>
                        <?php
                        $week_date = field(set_value('date', NULL), $advance_week_date_selected);
                        echo form_input(array('id' => 'week_date', 'name' => 'advance_week_date', 'class' => 'form-control week_datepicker', 'value' => $week_date));
                        ?>
                    </div>
                </div>
                <div class="custom_date_div option_div <?php echo ($advance_from_date_selected != '' && $advance_to_date_selected != '') ? '' : 'display-hide' ?>">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">From Date:</label>
                            <?php
                            echo form_input(array('id' => 'advance_from_date', 'name' => 'advance_from_date', 'class' => 'form-control', 'value' => $advance_from_date_selected));
                            ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label class="control-label">To Date:</label>
                            <?php
                            echo form_input(array('id' => 'advance_to_date', 'name' => 'advance_to_date', 'class' => 'form-control', 'value' => $advance_to_date_selected));
                            ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <input type="button" class="btn blue" value="Show Data" onclick="daily.showMainTabbing($('.btn-pref .btn-group .btn-1.active'));"/>
                        <input type="button" class="btn red" value="Reset" onclick="reset_advance_search();"/>
                    </div>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>