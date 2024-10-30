<?php
$min_year = 2018;
$max_year = 2025;
?>
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body">
        <?php
        $category_id = field(set_value('id', NULL), (isset($category->id)) ? $category->id : NULL, NULL);
        $attributes = array('class' => 'form-horizontal validate', 'id' => 'submit_form');
        echo form_open_multipart('category/save', $attributes, array('id' => $category_id));
        ?>
        <input type="hidden" name="category_name" id="category_name" value="<?php echo isset($category) ? $category->category_name : ''; ?>"/>
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Category<span class="required" aria-required="true"> * </span>', 'name', array('class' => 'col-md-4 control-label')); ?>
                <div class="col-md-4">
                    <?php
                    $item_name = field(set_value('name', NULL), (isset($category->category_key)) ? $category->category_key : NULL);
//                    echo form_input(array('id' => 'name', 'name' => 'name', 'class' => 'form-control', 'value' => $item_name, 'required' => 'required'));
                    ?>
                    <?php
                    $options = array();
                    $options[''] = '-- Select --';
                    $options['debit'] = 'debit';
                    $options['credit'] = 'credit';
                    if (isset($document['records']) && !empty($document['records'])) {
                        foreach ($document['records'] as $vRow) {
                            if ($vRow->key_name == 'general_section') {
                                continue;
                            }
                            $options[$vRow->key_name] = $vRow->label;
                        }
                    }
                    echo form_dropdown(array('required' => 'required', 'id' => 'name', 'name' => 'name', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $item_name, 'onchange' => 'set_category_name(this);'));
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php
                $type = field(set_value('name', NULL), (isset($category->type)) ? $category->type : 'description');
                echo form_label('Type<span class="required" aria-required="true"> * </span>', 'name', array('class' => 'col-md-4 control-label'));
                ?>
                <div class="col-md-4">
                    <label class="mt-radio mt-radio-outline">
                        <input onchange="get_type(this);" type="radio" name="type" id="description" value="description" class="radio" <?php echo $type == 'description' ? "checked" : ""; ?> >Description
                        <span></span>
                    </label>
                    <label class="mt-radio mt-radio-outline"   data-placement="top">
                        <input onchange="get_type(this);" type="radio" name="type" id="breakdown_description" value="breakdown_description" class="radio" <?php echo $type == 'breakdown_description' ? "checked" : ""; ?>>Breakdown Description
                        <span></span>
                    </label>
                    <label class="mt-radio mt-radio-outline"   data-placement="top">
                        <input onchange="get_type(this);" type="radio" name="type" id="week_description" value="week_description" class="radio" <?php echo $type == 'week_description' ? "checked" : ""; ?>>Week Description
                        <span></span>
                    </label>
                </div>
                <div class="col-md-3 week_display_option <?php echo $type != 'week_description' ? 'display-hide' : '' ?> " >
                    <label><input type="radio" value="1" name="week_date_display_option" id="week_date_display_option" <?php echo isset($category->week_date_display_option) && $category->week_date_display_option == 1 ? "checked" : ""; ?> onchange="display_ending_date_option(this);"/>
                        Week date display option</label>
                </div>
                <div class="col-md-3 end_week_date_display_option <?php echo $type != 'week_description' ? 'display-hide' : '' ?> " >
                    <label><input type="checkbox" value="1" name="is_display_last_week_ending_date_as_last_date_of_month" id="is_display_last_week_ending_date_as_last_date_of_month" <?php echo isset($category->is_display_last_week_ending_date_as_last_date_of_month) && $category->is_display_last_week_ending_date_as_last_date_of_month == 1 ? "checked" : ""; ?>/>
                        Is Display Last Week Ending Date as Last Date of Month
                    </label>
                </div>
                <div class="col-md-offset-8 col-md-3 calender_display_option <?php echo $type != 'week_description' && $item_name != 'DCP EFTS' ? 'display-hide' : '' ?> " >
                    <label><input type="radio" value="2" name="week_date_display_option" id="is_display_calender" <?php echo isset($category->is_display_calender) && $category->is_display_calender == 1 ? "checked" : ""; ?>/>
                        Is Display Calender</label>
                </div>
            </div>

        </div>

        <div class="col-md-12 dec_cover <?php echo $type == 'week_description' ? 'display-hide' : '' ?> ">
            <div class="col-md-7">
                <div class="form-group">
                    <?php echo form_label('Description<span class="required" aria-required="true"> * </span>', 'description', array('class' => 'col-md-7 control-label')); ?>
                    <div class="col-md-5">
                        <?php
                        $description = field(set_value('description', NULL), (isset($category->description)) ? $category->description : NULL);
                        ?>
                        <textarea class="form-control" rows="4" id="description" name="description" required><?php echo $description; ?></textarea>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <?php echo form_label('Vendor<span class="required" aria-required="true"> * </span>', 'vendor', array('class' => 'col-md-4 control-label')); ?>
                <div class="col-md-8">
                    <?php
                    $options = array();
                    $vendor_id = field(set_value('vendor', NULL), (isset($category->vendor_id)) ? $category->vendor_id : NULL);
                    $options[''] = '-Select-';
                    if (isset($vendor['records']) && !empty($vendor['records'])) {
                        foreach ($vendor['records'] as $vRow) {
                            $options[$vRow->id] = $vRow->company;
                        }
                        ?>
                        <?php
                    }
                    echo form_dropdown(array('required' => 'required', 'id' => 'vendor_id', 'name' => 'vendor_id', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $vendor_id));
                    ?>

                </div>
            </div>

        </div>

        <?php
        $css_cls = '';
        if ($type != 'breakdown_description') {
            $css_cls = 'display-none';
        }
        if ($category_id > 0) {
            foreach ($breakdown_category as $bRow) {
                ?>
                <div class="col-md-12 breakdown_dec_cover">
                    <div class="col-md-7">
                        <div class="form-group">
                            <?php echo form_label('Breakdown Description<span class="required" aria-required="true"> * </span>', 'breakdown_description', array('class' => 'col-md-7 control-label')); ?>
                            <div class="col-md-5">
                                <?php
                                $description = field(set_value('breakdown_description', NULL), (isset($bRow->description)) ? $bRow->description : NULL);
                                ?>
                                <textarea class="form-control" rows="4" id="description" name="breakdown_description[]" required><?php echo $description; ?></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <?php echo form_label('Vendor<span class="required" aria-required="true"> * </span>', 'vendor', array('class' => 'col-md-4 control-label')); ?>
                        <div class="col-md-8">
                            <?php
                            $options = array();
                            $vendor_id = field(set_value('vendor', NULL), (isset($bRow->vendor_id)) ? $bRow->vendor_id : NULL);
                            $options[''] = '-Select-';
                            if (isset($vendor['records']) && !empty($vendor['records'])) {
                                foreach ($vendor['records'] as $vRow) {
                                    $options[$vRow->id] = $vRow->company;
                                }
                                ?>
                                <?php
                            }
                            echo form_dropdown(array('required' => 'required', 'name' => 'breakdown_vendor_id[]', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $vendor_id));
                            ?>

                        </div>

                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-success btn-sm" type="button" onclick="add_more(this);"><i class="fa fa-plus"></i></button>

                    </div>
                </div>
                <?php
            }
        } else {
            ?>

            <div class="col-md-12 breakdown_dec_cover display-none">
                <div class="col-md-7">
                    <div class="form-group">
                        <?php echo form_label('Breakdown Description<span class="required" aria-required="true"> * </span>', 'breakdown_description', array('class' => 'col-md-7 control-label')); ?>
                        <div class="col-md-5">
                            <?php
                            $description = field(set_value('breakdown_description', NULL), (isset($category->description)) ? $category->description : NULL);
                            ?>
                            <textarea class="form-control" rows="4" id="description" name="breakdown_description[]" required><?php echo $description; ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <?php echo form_label('Vendor<span class="required" aria-required="true"> * </span>', 'vendor', array('class' => 'col-md-4 control-label')); ?>
                    <div class="col-md-8">
                        <?php
                        $options = array();
                        $vendor_id = field(set_value('vendor', NULL), (isset($category->breakdown_vendor_id)) ? $category->breakdown_vendor_id : NULL);
                        $options[''] = '-Select-';
                        if (isset($vendor['records']) && !empty($vendor['records'])) {
                            foreach ($vendor['records'] as $vRow) {
                                $options[$vRow->id] = $vRow->company;
                            }
                            ?>
                            <?php
                        }
                        echo form_dropdown(array('required' => 'required', 'name' => 'breakdown_vendor_id[]', 'options' => $options, 'class' => 'form-control', 'selected' => $vendor_id));
                        ?>

                    </div>

                </div>
                <div class="col-md-1">
                    <button class="btn btn-success btn-sm" type="button" onclick="add_more(this);"><i class="fa fa-plus"></i></button>

                </div>
            </div>
        <?php } ?>
        <div class="col-md-12">
            <div class="form-group">
                <?php
                echo form_label('Status<span class="required" aria-required="true"> * </span>', 'status', array('class' => 'col-md-4 control-label'));
                $status = field(set_value('status', NULL), (isset($category->status)) ? $category->status : 'A');
                ?>
                <div class="col-md-4">
                    <label class="mt-radio mt-radio-outline">
                        <input type="radio" name="status" id="status_A" value="A" class="radio" <?php echo $status == 'A' ? "checked" : ""; ?> >Active
                        <span></span>
                    </label>
                    <label class="mt-radio mt-radio-outline"   data-placement="top">
                        <input type="radio" name="status" id="status_I" value="I" class="radio" <?php echo $status == 'I' ? "checked" : ""; ?>>Inactive
                        <span></span>
                    </label>
                </div>
            </div>
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
    function validateDecimalVal(evt) {
        var val = parseFloat($(evt.target).val());
        if (val != '' && !isNaN(val)) {
            $(evt.target).val(val.toFixed(2));
        } else {
            $(evt.target).val('');
        }

    }
    function get_type(element) {
        var type = $(element).val();
        if (type == 'description') {
            $(".breakdown_dec_cover").hide();
            $(".dec_cover").show();
            $(".week_display_option").hide();
            $(".calender_display_option").hide();
        } else if (type == 'breakdown_description') {
            $(".breakdown_dec_cover").show();
            $(".dec_cover").show();
            $(".week_display_option").hide();
            $(".calender_display_option").hide();
        } else if (type == 'week_description') {
            var category = $("#category_name").val();
            var name = $("#name").val();
            if (category == 'DCP EFTS') {
                $(".calender_display_option").show();
            }else{
                $(".calender_display_option").hide();
            }
            $(".breakdown_dec_cover").hide();
            $(".week_display_option").show();
            $(".dec_cover").hide();
        }
    }
    function add_more(element) {
        $(".breakdown_dec_cover:first")
                .clone()
                .find("input, textarea").val("").end() // ***
                .show()
                .insertAfter(".breakdown_dec_cover:last");

    }
    function set_category_name(element) {
        var value = $(element).find("option:selected").text();
        $("#category_name").val(value);
    }
    function display_ending_date_option(element){
        var checked = $(element).is(":checked");
        if(checked){
            $(".end_week_date_display_option").show();
        }else{
            $(".end_week_date_display_option").hide();
        }
    }

</script>