
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
        $year = "";
        $month = "";
        $weekly_date = "";
        $calculation_type = "";
        if($general_settings!=""):
                if($general_settings[0]['store_key']!="")
                {
                    $store_id = $general_settings[0]['store_key'];
                    $year = $general_settings[0]['year'];
                    $month = $general_settings[0]['month'];
                    $weekly_date = $general_settings[0]['weekly_date'];
                    $calculation_type = $general_settings[0]['calculation_type'];
                }
        endif;
        ?>
        <?php
        $attributes = array('class' => 'form-horizontal validate', 'id' => 'submit_form');
        echo form_open_multipart('settings/savelabor', $attributes, array());
        ?>
        <?php if($general_settings!="") : ?>
            <input type="hidden" name="id" value="<?php echo $general_settings[0]['id'] ?>">
        <?php endif; ?>
        <div class="col-md-12">
            <div class="form-group">
                <?php
                echo form_label('Select Store<span class="required" aria-required="true"> * </span>', 'select', array('class' => 'col-md-4 control-label'));
                ?>
                <div class="col-md-4">
                    <?php
                    $options = array();
                    $options[''] = '-- Select Store  --';
                    if (isset($store_list['records']) && !empty($store_list['records'])) {
                        foreach ($store_list['records'] as $row) {
                            $options[$row->key] = $row->name." (".$row->key.")";
                        }
                    }
                    echo form_dropdown(array('required' => 'required', 'id' => 'store_key', 'name' => 'store_key[]', 'options' => $options, 'class' => 'form-control select2me','multiple'=>"multiple","values"));
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php
                echo form_label('Select Calculation Type<span class="required" aria-required="true"> * </span>', 'select', array('class' => 'col-md-4 control-label'));
                ?>
                <div class="col-md-4">
                    <select name="calculation_type" id="calculation_type" class="form-control" required>
                        <option value="">-- Select -- </option>
                        <option value="yearly">Yearly</option>
                        <option value="monthly">Monthly</option>
                        <option value="weekly">Weekly</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-12" id="sectiondate" style="display:none">
            <div class="form-group">
                <?php
                echo form_label('Select Date<span class="required" aria-required="true"> * </span>', 'date', array('class' => 'col-md-4 control-label'));
                ?>
                <div class="col-md-4">
                    <?php
                    echo form_input(array('required' => 'required', 'id' => 'year','autocomplete'=>'off', 'name' => 'year', 'class' => 'form-control datepicker',"style"=>"display:none","value"=>$year));
                    ?>
                     <?php
                    echo form_input(array('required' => 'required', 'id' => 'month','autocomplete'=>'off', 'name' => 'month', 'class' => 'form-control datepicker',"style"=>"display:none","value"=>$month));
                    ?>
                     <?php
                    echo form_input(array('required' => 'required', 'id' => 'weekly_date','autocomplete'=>'off', 'name' => 'weekly_date', 'class' => 'form-control datepicker',"style"=>"display:none","value"=>$weekly_date));
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
               <?php
                echo form_label('Amount<span class="required" aria-required="true"> * </span>','text', array('class' => 'col-md-4 control-label'));
                ?>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="amount" value="<?php if($general_settings!="") : echo $general_settings[0]['amount']; endif; ?>" required/>
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
$(document).ready(function () {
    $('#weekly_date').datepicker( {
         daysOfWeekDisabled: [0, 1, 2, 3, 4, 5],
        format: 'yyyy-mm-dd',
        });
        $('#year').datepicker( {
            format: "yyyy",
            viewMode: "years", 
            minViewMode: "years",
            autoclose:true,
            onClose: function(dateText, inst) { 
                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
            }
        });
       $('#month').datepicker( {
             format: "MM",
            viewMode: "months", 
            minViewMode: "months",
            autoclose:true,
            onClose: function(dateText, inst) { 
                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
            }
        }); 
     actionValue = "<?php echo $action ?>";
    if(actionValue == "edit"){
        values = "<?php echo $store_id ?>";
        $.each(values.split(","), function(i,e){
            $("#store_key option[value='" + e + "']").prop("selected", true);
        });
        $("#calculation_type").val("<?php echo $calculation_type ?>");
        $("#calculation_type").attr("disabled","disabled");
        $("#sectiondate").css("display","block");
        value = "<?php echo $calculation_type ?>";
        if(value=="yearly"){
            $(".ui-datepicker-calendar").css("display","none");
             $("#year").css("display","block");
             $("#year").trigger('changeDate');
             $("#month").css("display","none");
             $("#weekly_date").css("display","none");
        }
        if(value=="monthly"){
            $(".ui-datepicker-calendar").css("display","none");
             $("#month").css("display","block");
              $("#month").trigger('changeDate');
             $("#year").css("display","none");
             $("#weekly_date").css("display","none");

        }
        if(value=="weekly"){
            $(".ui-datepicker-calendar").css("display","block");
             $("#weekly_date").trigger('changeDate');
             $("#weekly_date").css("display","block");
             $("#year").css("display","none");
             $("#month").css("display","none");

        }
    }
    
});
$("#calculation_type").on("change",function(){
    value = $(this).val();
    $("#sectiondate").css("display","block");
    if(value=="yearly"){
        $(".ui-datepicker-calendar").css("display","none");
         $("#year").css("display","block");
         $("#month").css("display","none");
         $("#weekly_date").css("display","none");
    }
    if(value=="monthly"){
        $(".ui-datepicker-calendar").css("display","none");
         $("#month").css("display","block");
         $("#year").css("display","none");
         $("#weekly_date").css("display","none");

    }
    if(value=="weekly"){
        $(".ui-datepicker-calendar").css("display","block");
         $("#weekly_date").css("display","block");
         $("#year").css("display","none");
         $("#month").css("display","none");

    }
});
</script>
