<?php
$min_year = 2018;
$max_year = 2025;
$weekend_data = isset($cars_entry->weekend_data) ? $cars_entry->weekend_data : '';
?>
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body ">

        <?php
        $car_id = field(set_value('id', NULL), (isset($cars_entry->id)) ? $cars_entry->id : NULL, NULL);
        $attributes = array('class' => 'form-horizontal validate', 'id' => 'submit_form');
        echo form_open_multipart('cars_entry/save', $attributes, array('id' => $car_id));
        ?>
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Select Store<span class="required" aria-required="true"> * </span>', 'store', array('class' => 'col-md-4 control-label')); ?>
                <div class="col-md-4">
                    <?php
                    $options = array();
                    $options[''] = '-- Select Store  --';
                    $store_key = field(set_value('store_key', NULL), (isset($cars_entry->store_key)) ? $cars_entry->store_key : NULL);
                    if (isset($store_list['records']) && !empty($store_list['records'])) {
                        foreach ($store_list['records'] as $row) {
                            $options[$row->key] = $row->name . " (" . $row->key . ")";
                        }
                    }
                    echo form_dropdown(array('required' => 'required', 'id' => 'store_key', 'name' => 'store_key', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $store_key));
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php
                echo form_label('Select Date<span class="required" aria-required="true"> * </span>', 'date', array('class' => 'col-md-4 control-label'));
                $weekend_date = field(set_value('weekend_date', NULL), (isset($cars_entry->weekend_date)) ? date("d-m-Y", strtotime($cars_entry->weekend_date)) : NULL);
                ?>
                <div class="col-md-4">
                    <?php
                    echo form_input(array('required' => 'required', 'id' => 'weekend_date', 'name' => 'weekend_date', 'class' => 'form-control datepicker', 'value' => $weekend_date));
                    ?>
                </div>
                <div class="col-md-3">
                    <!--<input type="button" value="Show Days" id="show_days" class="btn blue" onclick="show_days();" disabled="">-->
                </div>
            </div>
        </div>
        <div id="view_dates">

        </div>

        <div class="form-group">
            <div class="col-xs-2 btn_group">
                <input type="submit" value="Save" id="submit" class="btn blue">
            </div>
        </div>
<?php echo form_close(); ?>
    </div>
</div>
<script>
    var car_id = '<?php echo $car_id; ?>';
    var weekend_data = '<?php echo $weekend_data; ?>';

    $(document).ready(function () {
        selected_date = $("#weekend_date").val();
        store_key = $("#store_key").val();
        if (car_id > 0) {
            $.ajax({
                url: site_url + 'cars_entry/get_dates',
                data: {"selected_date": selected_date, "store_key": store_key, "weekend_data" : weekend_data},
                method: 'POST',
                success: function (response) {
                    $('#view_dates').html(response);
                }
            });
        }
        $("#weekend_date").datepicker({
            daysOfWeekDisabled: [0, 1, 2, 3, 4, 5],
            format: 'dd-mm-yyyy'

        }).on('changeDate', function (e) {
             selected_date = $("#weekend_date").val();
            if (selected_date != '') {
                $.ajax({
                    url: site_url + 'cars_entry/get_dates',
                    data: {"selected_date": selected_date, "store_key": store_key},
                    method: 'POST',
                    success: function (response) {
                        $('#view_dates').html(response);
                    }
                });
            }else{
                 alert("Please select the date");
            }
        });
    })
</script>