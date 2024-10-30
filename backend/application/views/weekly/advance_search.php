<?php
$min_year = 2000;
$max_year = 2024;
?>
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-filter"></i>Filter
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand" id="filter-arrow" data-original-title="" title=""> </a>
        </div>
    </div>
    <div class="portlet-body form">
        <?php
        $attributes = array('class' => 'horizontal-form validate', 'id' => 'advance_search_form');
        echo form_open('', $attributes, array());
        ?>
            <div class="form-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="filter-btn btn-group pull-right" id="filter_view" data-toggle="buttons">
                            <?php $filter_view = ($filter_view ?? 'standard') == 'standard'; ?>
                            <label class="btn btn-default btn-on <?php echo $filter_view ? 'active' : ''; ?>">
                                <input type="radio" value="standard" name="filter_view" <?php echo $filter_view ? 'checked="checked"' : ''; ?>> Standard
                            </label>
                            <label class="btn btn-default btn-off <?php echo $filter_view ? '' : 'active'; ?>">
                                <input type="radio" value="comparison" name="filter_view" <?php echo $filter_view ? '' : 'checked="checked"'; ?>> Comparison
                            </label>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Select Store:</label><br>
                                <select class="form-control" id="advance_store_key" name="advance_store_key" multiple="multiple" required="required">
                                <?php $store_key = field(set_value('key', NULL), $advanceStore); ?>
                                <?php $optionGrp = array('A' => 'All (Excl. Inactive)', 'I' => 'All (Excl. Active)'); ?>
                                    <?php foreach ($optionGrp as $oKey => $_optionGrp) : ?>
                                        <optgroup label="<?php echo $_optionGrp; ?>">
                                            <?php foreach (($stores ?? []) as $row) : ?>
                                                <?php if ($oKey == $row->status) : ?>
                                                    <?php $selected = in_array($row->key, $store_key) ? 'selected' : ''; ?>
                                                    <option value="<?php echo $row->key; ?>" <?php echo $selected; ?>><?php echo "{$row->name} ({$row->key})" ; ?></option>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        </optgroup>
                                    <?php endforeach; ?>
                                </select>
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

                                echo form_dropdown(array('multiple' => 'multiple', 'required' => 'required', 'id' => 'years', 'id' => 'advance_years', 'name' => 'advance_years', 'options' => $options, 'class' => 'form-control multi_select2', 'selected' => $years));
                                ?>

                            </div>
                        </div>
                        <?php $filter_view = ($filter_view ?? 'standard') == 'standard'; ?>
                        <div class="col-md-3 weekending_div <?php echo $filter_view ? '' : 'display-hide'; ?>">
                            <div class="form-group">
                                <label class="control-label">Weekending Date</label>
                                <?php
                                $weekend_date = field(set_value('weekend_date[0]', NULL), (isset($weekend_date)) ? date("m/d/Y", strtotime($weekend_date)) : NULL);
                                echo form_input(array('required' => 'required', 'id' => 'weekend_date', 'name' => 'weekend_date', 'class' => 'form-control', 'value' => $weekend_date));
                                ?>
                            </div>
                        </div>
                        <div class="col-md-5 selection_div <?php echo $filter_view ? 'display-hide' : ''; ?>">
                            <div class="form-group">
                                <label class="control-label">Select Date Granularity:</label>
                                <label id="advance_status-error" class="error" for="advance_status"></label>
                                <br/>
                                <?php if (!in_array($mainActiveTab, array('labor'))) : ?>
                                <label class="mt-radio mt-radio-outline">
                                    <input type="radio" name="advance_status" id="status_1" value="day" class="radio" onchange="show_option(this);" <?php echo ($advance_status_selected == 'day') ? 'checked' : ''; ?> required />Day&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span></span>
                                </label>
                                <label class="mt-radio mt-radio-outline"   data-placement="top">
                                    <input type="radio" name="advance_status" id="status_2" value="special_day" class="radio" onchange="show_option(this);" <?php echo ($advance_status_selected == 'special_day') ? 'checked' : ''; ?>>Special Day&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span></span>
                                </label>
                                <?php endif; ?>
                                <label class="mt-radio mt-radio-outline"   data-placement="top">
                                    <input type="radio" name="advance_status" id="status_3" value="week" class="radio" onchange="show_option(this);" <?php echo ($advance_status_selected == 'week') ? 'checked' : ''; ?>>Week&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span></span>
                                </label>
                                <!-- need to check -->
                                <!-- <label class="mt-radio mt-radio-outline"   data-placement="top">
                                    <input type="radio" name="advance_status" id="status_4" value="season" class="radio" onchange="show_option(this);" <?php echo ($advance_status_selected == 'season') ? 'checked' : ''; ?>>Season&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span></span>
                                </label> -->
                                <!-- <label class="mt-radio mt-radio-outline"   data-placement="top">
                                    <input type="radio" name="advance_status" id="status_5" value="year" class="radio" onchange="show_option(this);" <?php echo ($advance_status_selected == 'year') ? 'checked' : ''; ?>>Year&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span></span>
                                </label> -->
                                <label class="mt-radio mt-radio-outline"   data-placement="top">
                                    <input type="radio" name="advance_status" id="status_6" value="custom_date" class="radio" onchange="show_option(this);" <?php echo ($advance_status_selected == 'custom_date') ? 'checked' : ''; ?>>Custom Date Range&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <span></span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-3 day_div option_div <?php echo ($advance_date_selected != '') ? '' : 'display-hide' ?>">
                            <div class="form-group">
                                <label class="control-label">Select Day:</label>
                                <?php
                                echo form_input(array('id' => 'advance_date', 'name' => 'advance_date', 'class' => 'form-control datepicker', 'value' => $advance_date_selected));
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
                        <div class="col-md-3 week_div option_div display-hide">
                            <div class="form-group">
                                <label class="control-label">Select Day:</label>
                                <?php
                                $week_date = field(set_value('date', NULL), $advance_week_date_selected);
                                echo form_input(array('id' => 'advance_week_date', 'name' => 'advance_week_date', 'class' => 'form-control week_datepicker', 'value' => $week_date));
                                ?>
                            </div>
                        </div>
                        <div class="col-md-3 season_div option_div <?php echo ($advance_season_selected != '') ? '' : 'display-hide' ?>">
                            <div class="form-group">
                                <label class="control-label">Season</label>
                                <?php
                                $options = array();
                                $options[''] = '-- Select Season  --';
                                if (isset($season_list) && !empty($season_list)) {
                                    foreach ($season_list as $row) {
                                        $options[$row->id] = ucfirst($row->name);
                                    }
                                }
                                echo form_dropdown(array('id' => 'advance_season', 'name' => 'advance_season', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $advance_season_selected));
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
                            <!--<div class="form-group">-->
                                <input type="button" class="btn blue" value="Show Data" onclick="week.showMainTabbing($('.btn-pref .btn-group .btn-1.active'));" style="margin-top: 5px;"/>
                                <input type="button" class="btn red" value="Reset" onclick="reset_advance_search();" style="margin-top: 5px;"/>
                            <!--</div>-->
                        </div>
                    </div>
                </div>
            </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script>
    <?php if (isset($store_json) && !empty($store_json)) : ?>
        week.setStoreList(JSON.parse('<?php echo json_encode($store_json); ?>'));
    <?php endif; ?>
</script>