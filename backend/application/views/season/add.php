<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body ">

        <?php
         $season_id = field(set_value('id', NULL), (isset($season->id)) ? $season->id : NULL, NULL);
        $attributes = array('class' => 'form-horizontal validate', 'id' => 'submit_form');
        echo form_open_multipart('season/save', $attributes, array('id' => $season_id));
        ?>
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Select Store<span class="required" aria-required="true"> * </span>', 'store', array('class' => 'col-md-4 control-label')); ?>
                <div class="col-md-4">
                    <?php
                    $options = array();
                    $options[''] = '-- Select Store  --';
                    $options['all'] = 'All';
                    $store_key = field(set_value('store_key', NULL), (isset($season->store_key)) ? $season->store_key : NULL);
                    if (isset($store_list['records']) && !empty($store_list['records'])) {
                        foreach ($store_list['records'] as $row) {
                            $options[$row->key] = $row->name." (".$row->key.")";
                        }
                    }
                    echo form_dropdown(array('required' => 'required', 'id' => 'store_key', 'name' => 'store_key', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $store_key));
                    ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('From Date<span class="required" aria-required="true"> * </span>', 'from_date', array('class' => 'col-md-4 control-label')); ?>
                <div class="col-md-4">
                     <?php
                    $date = field(set_value('date', NULL), (isset($season->from_date)) ? date("m/d/Y", strtotime($season->from_date)) : NULL);
                    echo form_input(array('id' => 'from_date', 'name' => 'from_date', 'class' => 'form-control from_datepicker', 'value' => $date, 'required' => 'required'));
                    ?>
                </div>
            </div>
         </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('To Date<span class="required" aria-required="true"> * </span>', 'to_date', array('class' => 'col-md-4 control-label')); ?>
                <div class="col-md-4">
                     <?php
                    $date = field(set_value('to_date', NULL), (isset($season->to_date)) ? date("m/d/Y", strtotime($season->to_date)) : NULL);
                    echo form_input(array('id' => 'to_date', 'name' => 'to_date', 'class' => 'form-control to_datepicker', 'value' => $date, 'required' => 'required'));
                    ?>
                </div>
            </div>
         </div>

        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Name<span class="required" aria-required="true"> * </span>', 'name', array('class' => 'col-md-4 control-label')); ?>
                <div class="col-md-4">
                    <?php
                    $item_name = field(set_value('name', NULL), (isset($season->name)) ? $season->name : 'Winter');
                    ?>
                     <label class="mt-radio mt-radio-outline">
                            <input type="radio" name="name" id="status_W" value="Winter" class="radio" <?php echo $item_name == 'Winter' ? "checked" : ""; ?> >Winter
                            <span></span>
                        </label>
                        <label class="mt-radio mt-radio-outline"   data-placement="top">
                            <input type="radio" name="name" id="status_SP" value="Spring" class="radio" <?php echo $item_name == 'Spring' ? "checked" : ""; ?>>Spring
                            <span></span>
                        </label>
                        <label class="mt-radio mt-radio-outline"   data-placement="top">
                            <input type="radio" name="name" id="status_S" value="Summer" class="radio" <?php echo $item_name == 'Summer' ? "checked" : ""; ?>>Summer
                            <span></span>
                        </label>
                        <label class="mt-radio mt-radio-outline"   data-placement="top">
                            <input type="radio" name="name" id="status_F" value="Fall" class="radio" <?php echo $item_name == 'Fall' ? "checked" : ""; ?>>Fall
                            <span></span>
                        </label>
                </div>
            </div>
        </div>
        
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Status<span class="required" aria-required="true"> * </span>', 'status', array('class' => 'col-md-4 control-label')); 
                 $status = field(set_value('status', NULL), (isset($season->status)) ? $season->status : 'A');
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
    $(document).ready(function(){
        $("#from_date").datepicker({
            format: 'mm/dd/yyyy',
            autoclose: true,
//            startDate: '+d',
        }).on('changeDate', function (selected) {
            var startDate = new Date(selected.date.valueOf());
            $('#to_date').datepicker('setStartDate', startDate);
        }).on('clearDate', function (selected) {
            $('#to_date').datepicker('setStartDate', null);
        });

        $("#to_date").datepicker({
            format: 'mm/dd/yyyy',
            autoclose: true,
        }).on('changeDate', function (selected) {
            var endDate = new Date(selected.date.valueOf());
            $('#from_date').datepicker('setEndDate', endDate);
        }).on('clearDate', function (selected) {
            $('#from_date').datepicker('setEndDate', null);
        });
    });
     function validateDecimalVal(evt) {
        var val = parseFloat($(evt.target).val());
        if(val != '' && !isNaN(val)) {
            $(evt.target).val(val.toFixed(2));
        } else {
            $(evt.target).val('');
        }
            
    }
    
    </script>