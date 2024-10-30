
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body ">
        <?php
        $store_id = "";
        $from_date = "";
        $to_date = "";
        $key_label = "";
        $status = "";
        $infinite = "";
        if($general_settings!=""):
                if($general_settings[0]['store_key']!="")
                {
                    $store_id = $general_settings[0]['store_key'];
                    $status = $general_settings[0]['is_active'];
                    $infinite = $general_settings[0]['is_infinite'];
                    $key_label = $general_settings[0]['key_label'];
                    $from_date = date("d-m-Y",strtotime($general_settings[0]['from_date']));
                    if($general_settings[0]['to_date']!="0000-00-00"){
                         $to_date = date("d-m-Y",strtotime($general_settings[0]['to_date']));
                    }
                }
        endif;
        ?>
        <?php
        $attributes = array('class' => 'form-horizontal validate', 'id' => 'submit_form');
        echo form_open_multipart('settings/saveexclude', $attributes, array());
        ?>
        <?php if($general_settings!="") : ?>
            <input type="hidden" name="id" value="<?php echo $general_settings[0]['id'] ?>">
        <?php endif; ?>
        <div class="col-md-12">
            <div class="form-group">
                <?php
                echo form_label('Select From Date<span class="required" aria-required="true"> * </span>', 'date', array('class' => 'col-md-4 control-label'));
                ?>
                <div class="col-md-4">
                    <?php
                    echo form_input(array('required' => 'required', 'id' => 'from_date','autocomplete'=>'off', 'name' => 'from_date', 'class' => 'form-control datepicker', 'value' => $from_date));
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">

                <label for="store" class="col-md-4 control-label">Select Type<span class="required" aria-required="true"> * </span></label>               
                <div class="col-md-4">
                    <input class="" type="radio" name="is_infinite" value="1">
                    <label for="store" class="control-label">Infinite</label>
                    <input class="" type="radio" name="is_infinite" value="0">
                    <label for="store" class="control-label">Custom</label>               
                </div>

            </div>
        </div>
        <div class="col-md-12" id="sectionto" style="display:none">
            <div class="form-group">
                <?php
                echo form_label('Select To Date<span class="required" aria-required="true"> * </span>', 'date', array('class' => 'col-md-4 control-label'));
                ?>
                <div class="col-md-4">
                    <?php
                    echo form_input(array('required' => 'required', 'id' => 'to_date','autocomplete'=>'off', 'name' => 'to_date', 'class' => 'form-control datepicker', 'value' => $to_date));
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php
                echo form_label('Select Key<span class="required" aria-required="true"> * </span>', 'select', array('class' => 'col-md-4 control-label'));
                ?>
                <div class="col-md-4">
                    <?php
                    $options = array();
                    $options[''] = '-- Select Title  --';
                    if (sizeof($dynamic_column) > 0) {
                        foreach ($dynamic_column as $key=>$value) {
                            $options[$key] = $value;
                        }
                    }
                    echo form_dropdown(array('required' => 'required', 'id' => 'key_label', 'name' => 'key_label', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $key_label));
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php
                echo form_label('Select Store<span class="required" aria-required="true"> * </span>', 'select', array('class' => 'col-md-4 control-label'));
                ?>
                <div class="col-md-4">
                    <?php
                    $options = array();
                    $options[''] = '-- Select Store  --';
                    // $store_id = field(set_value('store_id', NULL), $this->input->get('store_id'));
                    if (isset($store_list['records']) && !empty($store_list['records'])) {
                        foreach ($store_list['records'] as $row) {
                            $options[$row->key] = $row->name." (".$row->key.")";
                        }
                    }
                    echo form_dropdown(array('required' => 'required', 'id' => 'store_key', 'name' => 'store_key', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $store_id));
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php
                echo form_label('Select Status<span class="required" aria-required="true"> * </span>', 'text', array('class' => 'col-md-4 control-label'));
                ?>
                <div class="col-md-4">
                    <select name="is_active" id="is_active" class="form-control">
                        <option value="1">Active</option>
                        <option value="0" >Inactive</option>
                    </select>
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
actionValue = "<?php echo $action ?>";
var infinite = "<?php echo $infinite ?>";
if(actionValue == "edit"){
    $("#is_active").val(<?php echo $status ?>);
    $("input[type=radio][name=is_infinite][value=" + infinite + "]").attr('checked', 'checked');
    $('input[type=radio][name=is_infinite]').trigger("change");
    if(infinite==0){
        $("#sectionto").css("display","block");
    }
} else {
     $("#is_active").val(1);
    $("input[type=radio][name=is_infinite][value=1]").attr('checked', 'checked');
    $('input[type=radio][name=is_infinite]').trigger("change");
    $("#sectionto").css("display","none");
    $("#to_date").val("");
}
$(document).ready(function () {
    $("#from_date").datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true
        }).on('changeDate', function (e) {
            // if($("#to_date").length > 0){
            //     $('#to_date').datepicker('option', 'minDate', $("#from_date").val());
            // }
    });
    $("#to_date").datepicker({
            format: 'dd-mm-yyyy',
            autoclose: true,
        }).on('changeDate', function (e) {

    });
});
$('input[type=radio][name=is_infinite]').change(function() {
    if(this.value==1){
        $("#sectionto").css("display","none");
        $("#to_date").val("");
    }else{
        $("#sectionto").css("display","block");
    }
});
</script>
