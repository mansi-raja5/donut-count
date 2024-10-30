<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body ">

        <?php
         $special_day_id = field(set_value('id', NULL), (isset($special_day->id)) ? $special_day->id : NULL, NULL);
        $attributes = array('class' => 'form-horizontal validate', 'id' => 'submit_form');
        echo form_open_multipart('special_day/save', $attributes, array('id' => $special_day_id));
        ?>
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Select Store<span class="required" aria-required="true"> * </span>', 'store', array('class' => 'col-md-4 control-label')); ?>
                <div class="col-md-4">
                    <?php
                    $options = array();
                    $options[''] = '-- Select Store  --';
                      $options['all'] = 'All';
                    $store_key = field(set_value('store_key', NULL), (isset($special_day->store_key)) ? $special_day->store_key : NULL);
                    if (isset($store_list['records']) && !empty($store_list['records'])) {
                        foreach ($store_list['records'] as $row) {
                            $options[$row->key] = $row->name." (".$row->key.")";
                        }
                    }
                    echo form_dropdown(array('required' => 'required', 'id' => 'store_key', 'name' => 'store_key', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $store_key));
                    ?>
                    <span class="help-block help-block-error"><?php echo form_error('store_key'); ?> </span>
                </div>
            </div>
        </div>
        
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Date<span class="required" aria-required="true"> * </span>', 'date', array('class' => 'col-md-4 control-label')); ?>
                <div class="col-md-4">
                     <?php
                    $date = field(set_value('date', NULL), (isset($special_day->date)) ? date("m/d/Y", strtotime($special_day->date)) : NULL);
                    echo form_input(array('id' => 'date', 'name' => 'date', 'class' => 'form-control datepicker', 'value' => $date, 'required' => 'required'));
                    ?><span class="help-block help-block-error"><?php   echo form_error('date'); ?> </span>
                </div>
            </div>
         </div>

        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Name<span class="required" aria-required="true"> * </span>', 'name', array('class' => 'col-md-4 control-label')); ?>
                <div class="col-md-4">
                    <?php
                    $item_name = field(set_value('name', NULL), (isset($special_day->name)) ? $special_day->name : NULL);
                    echo form_input(array('id' => 'name', 'name' => 'name', 'class' => 'form-control', 'value' => $item_name, 'required' => 'required'));
                      ?>
                    <span class="help-block help-block-error"><?php echo form_error('name'); ?> </span>
                </div>
            </div>
        </div>
        
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Status<span class="required" aria-required="true"> * </span>', 'status', array('class' => 'col-md-4 control-label')); 
                 $status = field(set_value('status', NULL), (isset($special_day->status)) ? $special_day->status : 'A');
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
       $("#date").datepicker();
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