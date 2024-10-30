<style>
#loader {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100%;
  z-index: 10000;
  background: rgba(0,0,0,0.75) url(../loader.gif) no-repeat center center;
}
.append-conditional{
    background-color: #777;
    color: white;
    cursor: pointer;
    padding: 8px;
    width: 2%;
    border: none;
    text-align: center;
    outline: none;
    font-size: 15px;
}
button.append-conditional:after {
    content: '\002B';
    color: white;
    font-weight: bold;
}
span.icon{
    font-size: 15px;
    display: flex;
    align-items: center;
    padding-left: 2px;
    padding-right: 2px;
    color: #666666;
    transition: all 0.4s;
}
span.icon input{
    width:50%;
}
</style>
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
        <div id="loader" style="display: none"></div>
        <div class="row">
            <div class="col-md-12 table-responsive">
                <?php
                $attributes = array('class' => 'form-horizontal validate', 'id' => 'submit_form');
                echo form_open_multipart('settings/conditional_save', $attributes, array());
                ?>
                <span id="error-msg" style="color:red"></span>
                <span id="success-msg"></span>
                <input type="hidden" id="counter" name="counter" value="0"/>
                <table class="table table-striped table-bordered table-hover "  id="tbl_conditional_settings">
                    <thead>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <?php if(isset($store_list)):
                                foreach($store_list['records'] as $row):
                            ?>
                                <th><?php echo $row->key; ?>
                                </th>
                        <?php endforeach; endif;?>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th></th>
                            <?php if(isset($store_list)):
                                foreach($store_list['records'] as $row):
                            ?>
                                <th> <?php
                                echo form_label('Select Year<span class="required" aria-required="true"> * </span>', 'year', array('class' => 'col-md-4 control-label'));
                                $options = array();
                                $options[''] = '-- Year  --';
                                for ($yi = $min_year; $yi <= $max_year; $yi++) {
                                    $options[$yi] = $yi;
                                }
                                $id= 'year_'.$row->key;
                                echo form_dropdown(array('id' => $id, 'name' => 'year', 'options' => $options, 'class' => 'select2me'));
                                ?>
                                </th>
                        <?php endforeach; endif;?>
                    </thead>
                    <tbody>
                        <tr id="tr_0">
                            <td>
                                <select name="pos_key[]" id="poskey_0" required>
                                    <option value="">Select Key</option>
                                <?php if(isset($pos_key)):
                                    foreach($pos_key as $key=>$value):
                                ?>
                                    <option value="<?php echo $key ?>"><?php echo $value ?></option>
                                <?php endforeach; endif; ?>
                                </select>
                            </td>
                            <td>
                                <select name="value_type[]" id="valuetype_0" required>
                                    <option value="">Select Value</option>
                                    <option value="Percentage">Percentage</option>
                                    <option value="amount">Amount</option>
                                </select>
                            </td>
                            <td>
                                <select name="expression_type[]" id="expressiontype_0" required>
                                    <option value="">Select Expression </option>
                                    <option value="<"><</option>
                                    <option value="<="><=</option>
                                    <option value=">">></option>
                                    <option value=">=">>=</option>
                                </select>
                            </td>
                            <?php if(isset($store_list)):
                                foreach($store_list['records'] as $row):
                            ?>
                                <td><input type="number" name="amount[<?php echo $row->key ?>]" id="amount_<?php echo $row->key ?>_0" class="form-control">&nbsp;<input type="color" class="form-control" id="color_<?php echo $row->key ?>_0" name="color[<?php echo $row->key ?>]" value="#FFFFFF">
                                </td>
                        <?php endforeach; endif;?>
                        </tr>
                        
                    </tbody>
                    <thead>
                        <tr>
                            <td colspan="<?php echo count($store_list['records']) ?>"><button type="button" class="append-conditional"/></td>
                        </tr>
                    </thead>
                </table>
                <div class="form-group">
                    <div class="col-xs-2 btn_group">
                        <input type="button" value="Save" id="add_conditional" class="btn btn-sm blue">
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    var conditional_data = [];
    var selected_year = [];
    var selected_store = [];
    var counter = 0;
    $(document).ready(function(){
        $("select[id^=year]").val("2020");
        $("select[id^=year]").trigger("change");
    });
    //show alredy inserted data
    $(".select2me").on('change', function() {
        var conditional_data = [];
        counter++;
        id = $(this).attr("id");
        store_key =id.split('_').pop();
        year = $(this).val();
        conditional_data['year'] = year;
        conditional_data['store_key'] = store_key;
        conditional_data['counter'] = counter;
        callajax(conditional_data,"getconditional");
    });
    $(".append-conditional").on("click",function(){
        counter++;
        $("#counter").val(counter);
        html = "";
        html+='<tr id="tr_'+counter+'">'+
                '<td>'+
                    '<select name="pos_key[]" id="poskey_'+counter+'" required>'+
                        '<option value="">Select Key</option>';
                    <?php if(isset($pos_key)):
                        foreach($pos_key as $key=>$value):
                    ?>
                        html+='<option value="<?php echo $key ?>"><?php echo $value ?></option>';
                    <?php endforeach; endif; ?>
                     html+='</select>'+
                '</td>'+
                '<td>'+
                    '<select name="value_type[]" id="valuetype_'+counter+'" required>'+
                        '<option value="">Select Value</option>'+
                        '<option value="Percentage">Percentage</option>'+
                        '<option value="amount">Amount</option>'+
                    '</select>'+
                '</td>'+
                '<td>'+
                    '<select name="expression_type[]" id="expressiontype_'+counter+'" required>'+
                        '<option value="">Select Expression </option>'+
                        '<option value="<"><</option>'+
                        '<option value="<="><=</option>'+
                        '<option value=">">></option>'+
                        '<option value=">=">>=</option>'+
                    '</select>'+
                '</td>';
                <?php if(isset($store_list)):
                    foreach($store_list['records'] as $row):
                ?>
                    html+='<td><input type="number" name="amount[<?php echo $row->key ?>]" id="amount_<?php echo $row->key ?>_'+counter+'" class="form-control" >&nbsp;<input type="color" class="form-control" id="color_<?php echo $row->key ?>_'+counter+'" name="color[<?php echo $row->key ?>]" value="#FFFFFF" >'+
                    '</td>';
            <?php endforeach; endif;?>
            html+='</tr>';
            $('#tbl_conditional_settings tbody tr:last').after(html);
    });
    $("#add_conditional").on("click",function(){
         selected_year = [];
         selected_store = [];
        counter = $("#counter").val();
        error_count=0;
        //check any of year is selected
        <?php if(isset($store_list)):
            foreach($store_list['records'] as $row):
        ?>
            var year = $("#year_<?php echo $row->key ?>").val();
            if(year!=""){
                selected_store.push("<?php echo $row->key ?>");
                selected_year.push(year);
            }
        <?php endforeach; endif;?>
        for(i=0;i<=counter;i++){
            var pos_key = $("#poskey_"+i).val();
            var expressiontype = $("#expressiontype_"+i).val();
            var valuetype = $("#valuetype_"+i).val();
            $("#loader").css("display","block");
            array_length = selected_store.length;
            loop_counter  =0;
            $.each( selected_store, function( key, value ) {
                conditional_data = [];
               var amount = $("#amount_"+value+"_"+counter).val();
               var color = $("#color_"+value+"_"+counter).val();
               var year  = $("#year_"+value).val();
                if(pos_key=="" || expressiontype=="" || value==""){
                 error_count++;
                }
                if(amount!="" && color!="#FFFFFF" && year!="" && pos_key!="" && expressiontype!="" && value!=""){
                    conditional_data['pos_key'] = pos_key;
                    conditional_data['value_type'] = valuetype;
                    conditional_data['expression_type'] = expressiontype;
                    conditional_data['store_key'] = value;
                    conditional_data['year'] = year;
                    conditional_data['amount'] = amount;
                    conditional_data['color'] = color;
                    callajax(conditional_data,"conditional_save");
                }
                loop_counter++;
                if(array_length==loop_counter){
                    $("#loader").css("display","none");
                }
            });
        }
        if(error_count == 0){
            $("#loader").css("display","none");
            $("#error-msg").text("");
            $("#success-msg").html('<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Data Added Successfully</div></p></div>');
            $('html, body').animate({
                'scrollTop' : $("#success-msg").position().top
            });
        }else{
            $("#loader").css("display","none");
            $("#error-msg").text("Please Select Proper details");
             $('html, body').animate({
                'scrollTop' : $("#error-msg").position().top
            });
        }
        
        return false;
    });
    function callajax(conditionaldata,type){
        $.ajax({
                url: site_url + 'settings/'+type,
                method: 'POST',
                data: {counter:conditionaldata['counter'],year:conditionaldata['year'],store_key:conditionaldata['store_key'],pos_key:conditionaldata['pos_key'],expression_type:conditionaldata['expression_type'],value_type:conditionaldata['value_type'],amount:conditionaldata['amount'],color:conditionaldata['color']},
                async:false,
                success: function(response) {
                    if(type=="getconditional"){
                        if(response!=""){
                            $("#counter").val(counter);
                            $('#tbl_conditional_settings tbody tr:first').before(response);
                        }else{
                            counter--;
                        }
                    }
                    result = response;
                    return true;
                }
        });
    }
</script>