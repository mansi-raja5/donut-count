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
                echo form_open_multipart('settings/store_save', $attributes, array());
                ?>
                <span id="error-msg" style="color:red"></span>
                <table class="table table-striped table-bordered table-hover "  id="tbl_store_settings">
                    <thead>
                        <tr>
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
                                echo form_dropdown(array('required' => 'required', 'id' => $id, 'name' => 'year', 'options' => $options, 'class' => 'select2me'));
                                ?>
                                </th>
                        <?php endforeach; endif;?>
                        <tr>
                         <tr>
                            <th></th>
                            <th></th>
                            <?php if(isset($store_list)):
                                foreach($store_list['records'] as $row):
                            ?>
                                <th><input type="checkbox" name="check_all" id="<?php echo $row->key ?>"/>
                                </th>
                        <?php endforeach; endif;?>
                        <tr>
                    </thead>
                    <tbody>
                        <?php if(isset($pos_key)):
                            $i = 1;
                            foreach($pos_key as $key=>$value):
                        ?>
                        <tr>
                            <th><?php echo $i++; ?></th>
                            <td><?php echo $value ?></td>
                            <?php if(isset($store_list)):
                                foreach($store_list['records'] as $row):
                            ?>
                                <td><input type="checkbox" name="check_store[]" class="<?php echo $row->key ?>" id="<?php echo $row->key.'-'.$key ?>" value="" />
                                </td>
                            <?php endforeach; endif;?>
                        </tr>
                    <?php endforeach; endif; ?>
                    </tbody>
                </table>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    var storedata = [];
    var result = "";
    $(document).ready(function(){
        $("select[id^=year]").val("2020");
        $("select[id^=year]").trigger("change");
    });
    $("input[name='check_all']").click(function(){
        $("#loader").css("display","block");
            // $("#loader").show(); 
            // $('#loader').trigger('click');
         counter =0;
        total_counter = "<?php echo count($pos_key) ?>";
        if(counter==0){
            $("#loader").css("display","block"); 
            // $("#loader").show(); 
        }
        store_key = $(this).attr("id");
        var attr = $(this).attr('checked');
        if (typeof attr !== typeof undefined && attr !== false) {
            $(this).removeAttr("checked");
        }else{
           $(this).attr("checked","checked");
        }
        year = $("#year_"+store_key).val();
        if(year!=""){
             <?php if(isset($pos_key)):
                    foreach($pos_key as $key=>$value):
                ?>
             checked ="";
              if ($(this).attr("checked") == "checked") {
                checked =1;
                $("#"+store_key+'-<?php echo $key ?>').attr("checked","checked");
                storedata['year'] = year;
                storedata['store_key'] = store_key;
                storedata['pos_key'] = '<?php echo $key ?>';
                storedata['checked'] = checked;
                callajax(storedata,"savestore");
              } else {
                 $("#"+store_key+'-<?php echo $key ?>').removeAttr("checked");
                    checked =0;
                    storedata['year'] = year;
                    storedata['store_key'] = store_key;
                    storedata['pos_key'] = '<?php echo $key ?>';
                    storedata['checked'] = checked;
                    callajax(storedata,"savestore");
              }
              counter++;
            <?php  endforeach; ?>
            if(counter==total_counter){
                 $("#loader").css("display","none");
            }else{
                 $("#loader").css("display","block");
            }
            <?php  endif;?>
            $('.'+store_key+"_"+year).prop('checked',checked);
            $("#error-msg").text("");
        }else{
            // $("#loader").css("display","none");
            $("#error-msg").text("Please select year of this store "+store_key);
            $(this).attr('checked',false);
            $('html, body').animate({
                'scrollTop' : $("#error-msg").position().top
            });
            return false;
        }
        
    });

    $("input[name^=check_store]").click(function(){
         $("#loader").css("display","block");
            var id = $(this).attr("id");
            pos_key = id.split('-')[1];
            store_key =id.split('-')[0];
            year = parseInt($("#year_"+store_key).val());
            var attr = $(this).attr('checked');
            var checked ="";
            // For some browsers, `attr` is undefined; for others,
            // `attr` is false.  Check for both.
            if (typeof attr !== typeof undefined && attr !== false) {
                checked =0 ;
                $(this).removeAttr("checked");
            }else{
                checked = 1;
               $(this).prop("checked","true");
            }
        if($("#year_"+store_key).val()!=""){
            //uncheck checkall
            $("#"+store_key).attr("checked",false);
            storedata['year'] = year;
            storedata['store_key'] = store_key;
            storedata['pos_key'] = pos_key;
            storedata['checked'] = checked;
           
            callajax(storedata,"savestore");
            $("#loader").css("display","none");
        }else{
            $("#error-msg").text("Please select year of this store "+store_key);
            $(this).attr('checked',false);
            $('html, body').animate({
                'scrollTop' : $("#error-msg").position().top
            });
        }
        
        return true;
    });
    $(".select2me").on('change', function() {
         $("#loader").css("display","block");

        id = $(this).attr("id");
        store_key =id.split('_').pop();
        year = $(this).val();
        storedata['year'] = year;
        storedata['store_key'] = store_key;
       
        callajax(storedata,"getstore");
        if(result!=1){
            var arr = $.parseJSON(result); //convert to javascript array
            counter = 0;
            var total_counter = <?php echo sizeof($pos_key) ?>;
            $.each(arr,function(key,value){
                if(value=="0"){
                    $("#"+store_key+'-'+key).removeAttr('class');
                    $("#"+store_key+'-'+key).attr('class',store_key+'_'+year);
                    $("#"+store_key+'-'+key).prop('checked',false);
                }
                if(value=="1"){
                    $("#"+store_key+'-'+key).removeAttr('class');
                    $("#"+store_key+'-'+key).attr('class',store_key+'_'+year);
                    $("#"+store_key+'-'+key).prop('checked',true);
                }
                counter++;
            });
            console.log(counter)
            console.log(total_counter)
            if(counter==total_counter){
                $("#loader").css("display","none");
                $("#loader").hide();
            }
           
        }else{
               <?php if(isset($pos_key)):
                    foreach($pos_key as $key=>$value):
                ?>
                $("#"+store_key+'-<?php echo $key ?>').removeAttr('class');
                $("#"+store_key+'-<?php echo $key ?>').attr('class',store_key+'_'+year);
                $("."+store_key+'_'+year).prop('checked',false);
                <?php endforeach; endif;?>
                $("#loader").css("display","none");
                $("#loader").hide();
        }
        $("#"+store_key).prop('checked',false);
        
        return true;
    });
    function callajax(storedata,type){
        $.ajax({
                url: site_url + 'settings/'+type,
                method: 'POST',
                data: {year:storedata['year'],store_key:storedata['store_key'],pos_key:storedata['pos_key'],checked:storedata['checked']},
                async:false,
                success: function(response) {
                    result = response;
                    
                    // $("#error-msg").text("Settings saved successfully");
                    // $('html, body').animate({
                    //     'scrollTop' : $("#error-msg").position().top
                    // });
                    return true;
                }
        });
    }
</script>