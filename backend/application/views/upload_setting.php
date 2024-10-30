
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body ">

        <?php
        $attributes = array('class' => 'form-horizontal validate', 'id' => 'submit_form');
        $id = field(set_value('id', NULL), (isset($upload_data->id)) ? $upload_data->id : NULL, NULL);
        echo form_open_multipart('statement/save_upload_setting', $attributes, array('id' => $id));
        $selected_type = "Ledger";
        if(isset($upload_data->selected_type)){
            $selected_type = $upload_data->selected_type;
        }
        ?>
        <div class="col-md-12">
            <div class="form-group">

                <?php echo form_label('Select Type<span class="required" aria-required="true"> * </span>', 'store', array('class' => 'col-md-4 control-label')); ?>
               <div class="col-md-4">
                <input class="" type="radio" name="selected_type"  value="Ledger" <?php if($selected_type=="Ledger") echo "checked";?>>
                 <?php echo form_label('Ledger', 'store', array('class' => 'control-label')); ?>
               <!-- </div>

               <div class="col-md-4">  -->
                <input class="" type="radio" name="selected_type"  value="Dailysales" <?php if($selected_type=="Dailysales") echo "checked";?>>
                <?php echo form_label('Dailysales', 'store', array('class' => 'control-label')); ?>
               </div>

            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Select Description<span class="required" aria-required="true"> * </span>', 'store', array('class' => 'col-md-4 control-label')); ?>
                <div class="col-md-4">
                    <?php
                    $options = array();
                    $options[''] = '-- Select Description  --';
                    $desc_data_txt = field(set_value('desc_data', NULL), (isset($upload_data->description)) ? $upload_data->description : NULL);
                    if (isset($desc_data) && !empty($desc_data)) {
                        foreach ($desc_data as $row) {
                            $options[$row->description] = $row->description;
                        }
                    }
                    echo form_dropdown(array('required' => 'required', 'id' => 'desc_data', 'name' => 'desc_data', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $desc_data_txt));
                    ?>
                </div>

            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Invoice Text<span class="required" aria-required="true"> * </span>', 'invoice_name', array('class' => 'col-md-4 control-label')); ?>
                <div class="col-md-4">
                    <?php
                    $invoice_text = field(set_value('invoice_name', NULL), (isset($upload_data->invoice_name)) ? $upload_data->invoice_name : NULL);
                    echo form_input(array('id' => 'invoice_name', 'name' => 'invoice_name', 'class' => 'form-control', 'value' => $invoice_text, 'onblur' => 'display_uploaded_name(this);'));
                    ?>
                </div>
                 <div class="col-md-3">
                  <span class="final_uploaded_name"><?php echo $invoice_text != '' ? "(<b>Ex.</b> ".$invoice_text."_mm_yyyy_StoreNumber)" : ''; ?></span>
                </div>
                <!--                <label class="pull-left">testttttt</label>-->
            </div>
        </div>
        <div id="document_section">
            <div class="col-md-12">
                <div class="form-group">
                    <?php echo form_label('Document Name 1<span class="required" aria-required="true"> * </span>', 'document_name_1', array('class' => 'col-md-4 control-label')); ?>
                    <div class="col-md-4">
                        <?php
                        $document_name_1 = field(set_value('document_name_1', NULL), (isset($upload_data->document_name_1)) ? $upload_data->document_name_1 : NULL);
                        echo form_input(array('id' => 'document_name_1', 'name' => 'document_name_1', 'class' => 'form-control', 'value' => $document_name_1, 'onblur' => 'display_uploaded_name(this);'));
                        ?>
                    </div>
                      <div class="col-md-3">
                    <span class="final_uploaded_name"><?php echo $document_name_1 != '' ? "(<b>Ex.</b> ".$document_name_1."_mm_yyyy_StoreNumber)" : ''; ?></span>
                </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <?php echo form_label('Document Name 2<span class="required" aria-required="true"> * </span>', 'document_name_2', array('class' => 'col-md-4 control-label')); ?>
                    <div class="col-md-4">
                        <?php
                        $document_name_2 = field(set_value('document_name_2', NULL), (isset($upload_data->document_name_2)) ? $upload_data->document_name_2 : NULL);
                        echo form_input(array('id' => 'document_name_2', 'name' => 'document_name_2', 'class' => 'form-control', 'value' => $document_name_2, 'onblur' => 'display_uploaded_name(this);'));
                        ?>
                    </div>
                      <div class="col-md-3">
                   <span class="final_uploaded_name"><?php echo $document_name_2 != '' ? "(<b>Ex.</b> ".$document_name_2."_mm_yyyy_StoreNumber)" : ''; ?></span>
                </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <?php echo form_label('Document Name 3<span class="required" aria-required="true"> * </span>', 'document_name_3', array('class' => 'col-md-4 control-label')); ?>
                    <div class="col-md-4">
                        <?php
                        $document_name_3 = field(set_value('document_name_3', NULL), (isset($upload_data->document_name_3)) ? $upload_data->document_name_3 : NULL);
                        echo form_input(array('id' => 'document_name_3', 'name' => 'document_name_3', 'class' => 'form-control', 'value' => $document_name_3));
                        ?>
                    </div>
                       <div class="col-md-3">
                    <span class="final_uploaded_name"><?php echo $document_name_3 != '' ? "(<b>Ex.</b> ".$document_name_3."_mm_yyyy_StoreNumber)" : ''; ?></span>
                </div>
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
    <?php if(isset($selected_type) && $selected_type!=""){ ?>
        var already_selected ="<?php echo $selected_type ?>";
    <?php }else{ ?>
    var already_selected = "";
<?php } ?>
$(document).ready(function () {
    action = "<?php echo $action ?>";
    if(action=="edit"){
        selected_type = "<?php echo $selected_type ?>";
        already_selected = selected_type;
        description = "<?php  if(isset($upload_data->description)) echo $upload_data->description; else echo ''; ?>";
        if(selected_type=="Ledger"){
            $("#desc_data").empty();
            $.ajax({
                url: "<?php echo base_url('dailysales/getdescription')?>",
                method:'POST',
                data : {type:this.value},
                success: function(result){

                    var jsontoarr = JSON.parse(result);
                    for( jsontoarr_each in jsontoarr) {
                        $('#desc_data').append('<option value="'+jsontoarr[jsontoarr_each].description+'">'+jsontoarr[jsontoarr_each].description+'</option>');
                    }
                }
            });
            $("#desc_data").trigger("change");
             $('#desc_data').val(description);
             display_uploaded_name($("#invoice_name"));
            $("#document_section").css("display","block");
        }
        if(selected_type=="Dailysales"){
             $.ajax({
                url: "<?php echo base_url('dailysales/getColumns')?>",
                method:'POST',
                data : {type:this.value},
                success: function(result){

                    $("#desc_data").empty();
                    var jsontoarr = JSON.parse(result);
                    for( jsontoarr_each in jsontoarr) {
                        $('#desc_data').append('<option value="'+jsontoarr[jsontoarr_each].key+'">'+jsontoarr[jsontoarr_each].value+'</option>');
                    }
                    $("#desc_data").trigger("change");
                    $("#document_section").css("display","none");
                    $('#desc_data').val(description);
                    display_uploaded_name($("#invoice_name"));
                }
            });
        }
    }
});
$('#invoice_name').keypress(function() {
    if(event.which == 32) {
        return false;
    }
});
$('#document_name_1').keypress(function() {
    if(event.which == 32) {
        return false;
    }
});
$('#document_name_2').keypress(function() {
    if(event.which == 32) {
        return false;
    }
});
$('#document_name_3').keypress(function() {
    if(event.which == 32) {
        return false;
    }
});
$('input[type=radio][name=selected_type]').change(function() {
    already_selected = this.value;
    if(this.value=="Ledger"){
        $("#desc_data").empty();
        $.ajax({
            url: "<?php echo base_url('dailysales/getdescription')?>",
            method:'POST',
            data : {type:this.value},
            success: function(result){

                var jsontoarr = JSON.parse(result);
                for( jsontoarr_each in jsontoarr) {
                    $('#desc_data').append('<option value="'+jsontoarr[jsontoarr_each].description+'">'+jsontoarr[jsontoarr_each].description+'</option>');
                }

            }
        });
        $("#desc_data").trigger("change");
         display_uploaded_name($("#invoice_name"));
        $("#document_section").css("display","block");
    }
    if(this.value=="Dailysales"){
         $.ajax({
            url: "<?php echo base_url('dailysales/getColumns')?>",
            method:'POST',
            data : {type:this.value},
            success: function(result){

                $("#desc_data").empty();
                var jsontoarr = JSON.parse(result);
                for( jsontoarr_each in jsontoarr) {
                    $('#desc_data').append('<option value="'+jsontoarr[jsontoarr_each].key+'">'+jsontoarr[jsontoarr_each].value+'</option>');
                }
                $("#desc_data").trigger("change");
                $("#document_section").css("display","none");
                 display_uploaded_name($("#invoice_name"));
            }
        });
    }
});
    function display_uploaded_name(element) {
        var element_id = $(element).attr("id");
        var value = $(element).val();
        if(value != ''){
            if(already_selected=="Dailysales"){
                var uploaded_name_txt = value +"_dd_mm_yyyy_StoreNumber";
            }
            else
                var uploaded_name_txt = value +"_mm_yyyy_StoreNumber";
            $("#"+element_id).closest(".form-group").find(".final_uploaded_name").html("(<b>Ex. </b>"+uploaded_name_txt+")");
        }
    }
</script>