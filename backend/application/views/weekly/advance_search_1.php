<?php
$min_year = 2000;
$max_year = 2024;
?>
<div class="portlet box blue-hoki">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>Advance Search
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
        </div>
    </div>
    <div class="portlet-body form">
        <form class="horizontal-form" method="post" id="advance_search_form">
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Select Store:</label>
                                <?php
                                $options = array();
                                $store_key = field(set_value('key', NULL), $advanceStore);
                                if (isset($stores) && !empty($stores)) {
                                    foreach ($stores as $row) {
                                        $options[$row->key] = $row->name . " (" . $row->key . ")";
                                    }
                                }

                                echo form_dropdown(array('multiple' => 'multiple', 'required' => 'required', 'id' => 'store_key', 'name' => 'advance_store_key', 'options' => $options, 'class' => 'form-control multi_select2', 'selected' => $store_key));
                                ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Select Year:</label>
                                <?php
                                $options = array();
                                $years = field(set_value('years', NULL), $advanceYear);
                                for ($i = $min_year; $i < $max_year; $i++) {
                                    $options[$i] = $i;
                                }

                                echo form_dropdown(array('multiple' => 'multiple', 'required' => 'required', 'id' => 'years', 'name' => 'advance_years', 'options' => $options, 'class' => 'form-control multi_select2', 'selected' => $years));
                                ?>

                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="form-group">
                                <label class="control-label">Select Date Granularity:</label>
                                <br/>
                                <label class="mt-radio mt-radio-outline">
                                    <input type="radio" name="advance_status" id="status_1" value="day" class="radio" onchange="show_option(this);" <?php echo ($advance_status_selected == 'day') ? 'checked' : ''; ?> />Day
                                    <span></span>
                                </label>
                                <label class="mt-radio mt-radio-outline"   data-placement="top">
                                    <input type="radio" name="advance_status" id="status_2" value="special_day" class="radio" onchange="show_option(this);" <?php echo ($advance_status_selected == 'special_day') ? 'checked' : ''; ?>>Special Day
                                    <span></span>
                                </label>
                                <label class="mt-radio mt-radio-outline"   data-placement="top">
                                    <input type="radio" name="advance_status" id="status_3" value="week" class="radio" onchange="show_option(this);" <?php echo ($advance_status_selected == 'week') ? 'checked' : ''; ?>>Week
                                    <span></span>
                                </label>
                                <label class="mt-radio mt-radio-outline"   data-placement="top">
                                    <input type="radio" name="advance_status" id="status_4" value="season" class="radio" onchange="show_option(this);" <?php echo ($advance_status_selected == 'season') ? 'checked' : ''; ?>>Season
                                    <span></span>
                                </label>
                                <label class="mt-radio mt-radio-outline"   data-placement="top">
                                    <input type="radio" name="advance_status" id="status_5" value="year" class="radio" onchange="show_option(this);" <?php echo ($advance_status_selected == 'year') ? 'checked' : ''; ?>>Year
                                    <span></span>
                                </label>
                                <label class="mt-radio mt-radio-outline"   data-placement="top">
                                    <input type="radio" name="advance_status" id="status_6" value="custom_date" class="radio" onchange="show_option(this);" <?php echo ($advance_status_selected == 'custom_date') ? 'checked' : ''; ?>>Custom Date Range
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <?php print_r($special_day_selected); ?>
                        <div class="col-md-3 day_div option_div <?php echo ($advance_date_selected != '') ? '' : 'display-hide' ?>">
                            <div class="form-group">
                                <label class="control-label">Select Day:</label>
                                <?php
                                $date = $advance_date_selected;
                                echo form_input(array('id' => 'day', 'name' => 'day', 'class' => 'form-control datepicker', 'value' => $date));
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
                                echo form_dropdown(array('id' => 'special_day', 'name' => 'special_day', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $special_day));
                                ?>
                            </div>
                        </div>
                        <div class="col-md-3 week_div option_div display-hide">
                            <div class="form-group">
                                <label class="control-label">Select Day:</label>
                                <?php
                                $week_date = field(set_value('week', NULL));
                                echo form_input(array('id' => 'week', 'name' => 'week', 'class' => 'form-control week_datepicker', 'value' => $week_date));
                                ?>
                            </div>
                        </div>
                        <div class="col-md-3 season_div option_div display-hide">
                            <div class="form-group">
                                <label class="control-label">Season</label>
                                <?php
                                $options = array();
                                $options[''] = '-- Select Season  --';
                                $season = field(set_value('season', NULL));
                                if (isset($season_list) && !empty($season_list)) {
                                    foreach ($season_list as $row) {
                                        $options[$row->id] = ucfirst($row->name);
                                    }
                                }
                                echo form_dropdown(array('id' => 'season', 'name' => 'season', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $season));
                                ?>
                            </div>
                        </div>
                        <div class="custom_date_div option_div display-hide">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">From Date:</label>
                                    <?php
                                    $from_date = field(set_value('from_date', NULL));
                                    echo form_input(array('id' => 'from_date', 'name' => 'from_date', 'class' => 'form-control', 'value' => $from_date));
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">To Date:</label>
                                    <?php
                                    $to_date = field(set_value('to_date', NULL));
                                    echo form_input(array('id' => 'to_date', 'name' => 'to_date', 'class' => 'form-control', 'value' => $to_date));
                                    ?>
                                </div>
                            </div>
                        </div>


                        <div class="col-md-2">
                            <div class="form-group">
                                <label class="control-label"></label>
                                <input type="button" class="form-control btn blue" value="Update Date" onclick="week.showMainTabbing($('.btn-pref .btn-group .btn-1.active'));" style="margin-top: 5px;"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>