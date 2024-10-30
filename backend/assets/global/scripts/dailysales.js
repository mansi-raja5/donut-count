class Dailysales {
    constructor() {
        this.store_key   = "";
        this.month      = "";
        this.year       = "";
    }
    setPostData(store_key, month, year){
        this.store_key   = store_key;
        this.month      = month;
        this.year       = year;
    }
    getPostData() {
        return {
            store_key: this.store_key,
            month: this.month,
            year: this.year,
        };
    }
    getDailysalesGrid() {
        $("#loader").show();
        $.ajax({
            url: site_url + 'dailysales/getDailysalesGrid',
            data: this.getPostData(),
            method: 'POST',
            async:false,
            success: function(response) {
                $('#add_attachment_modal').html(response);
                $('#loader').css("display","none");
                $("#loader").hide();
                 return true;
            }
        });
       
    }
    calculatetotal(id,i){
        var total = 0;
         $("#row_"+i).find("input[type='number']").each(function() {
            if($(this).val()!="" && $(this).val()!=NaN && $(this).val()!=undefined){
                var count = parseFloat($(this).val());
                total= total + count;
            }
           
        });
        $("#lbltotal_"+i).text(total);
        $("#total_"+i).val(parseFloat(total));
    }
    setlock(lock,counter){
        if($(lock).val()=="lock"){
            var total = parseFloat($("#total_"+counter).val());
            var paid_out = parseFloat($("#paidout_"+counter).val());
            if(total == paid_out){
                $("#row_"+counter).find("input[type='number']").each(function() {
                    $(this).attr("readonly","true");
                   
                });
                $("#is_lock_"+counter).val("1");
                $("#lock_"+counter).val("Unlock");
                $("#errormsg").text("");
                $("#row_"+counter).css('background-color', '#fbfcfd !important');
                $("#row_"+counter+':hover').css('background-color', '#fbfcfd !important');
             }else{
                 $("#lock_"+counter).val("lock");
                 $("#is_lock_"+counter).val("0");
                $("#row_"+counter).css('background-color', '#cf352e !important');
                $("#row_"+counter+':hover').css('background-color', '#cf352e !important');
                $("#errormsg").text("Paid out and total does not match you canot lock this data");
               $('html, body').animate({
                    'scrollTop' : $("#errormsg").position().top
                });
             }
           
        }else{
            $("#row_"+counter).find("input[type='number']").each(function() {
                $(this).removeAttr("readonly");
               
            });
            $("#is_lock_"+counter).val(0);
           $("#row_"+counter).css('background-color', '#fbfcfd !important');
              $("#lock_"+counter).val("lock");
        }
    }
    submitdata(days){
        //check total
        var chk =0;
        var i=0;
        for(i=0;i<=days;i++){
            var total = 0;
             $("#row_"+i).find("input[type='number']").each(function() {
                if($(this).val()!="" && $(this).val()!=NaN && $(this).val()!=undefined){
                    var count = parseFloat($(this).val());
                    total= total + count;
                }
               
            });
             var paid_out_chk = parseFloat($("#paidout_"+i).val());
             if(parseFloat(total) < paid_out_chk){
                // console.log("total"+parseFloat(total));
                // console.log("paid_out_chk"+paid_out_chk);
                // console.log(i);
                 chk=i;
                 $("#row_"+i).css('background-color', '#cf352e !important');
                $("#row_"+i+':hover').css('background-color', '#cf352e !important');
               
             }else{
                  $("#row_"+i).css('background-color', '#fbfcfd !important');
                $("#row_"+i+':hover').css('background-color', '#fbfcfd !important');
             }
               
        }

        if(chk==0){
            $('#loader').show();
             $("#errormsg").text("");
            $("#submit_daily").submit();
            return true;
        }else{
            $("#errormsg").text("Paid out and total does not match");
           $('html, body').animate({
                'scrollTop' : $("#errormsg").position().top
            });
            return false;
        }
    }
    display_attachment(ct,counter){
        $("#loader").show();
        var dynamic_column = $(ct).attr("id");
        dynamic_column =dynamic_column.split('-').pop(); 
        var cdate = $("#cdate_"+counter).val();
        $.ajax({
            url: site_url + 'dailysales/getAttachmentmodal',
            data: {"dynamic_column":dynamic_column,"cdate":cdate,"counter":counter},
            method: 'POST',
            async:false,
            success: function(response) {
                $('#add_uploadattachment_modal').html(response);
                $("#loader").hide();
                 $("#addattachment").modal("show");
                 return true;
            }
        });
        return false;
    }
    uploadattachments(){
        $("#loader").show();
        var fd = new FormData();
        var file_data = $('#dynamicinvoicefile_counter')[0].files; // for multiple files
        // console.log(file_data[0]);
        fd.append("files", file_data[0]);
        fd.append("store_key",this.store_key);
        fd.append("month",this.month);
        fd.append("year",this.year);
        fd.append("dynamic_column",$("#hd_dynamic_column").val());
        fd.append("hd_cdate",$("#hd_cdate").val());
        var counter = $("#hd_counter").val();
        $.ajax({
            url: site_url + 'dailysales/setuploadAttachments',
            data: fd,
            method: 'POST',
            async:false,
            dataType: 'json',
              contentType: false,
              processData: false,
              cache: false,
            success: function(response) {
                if(response > 0){
                    $("#alreadyupload-"+$("#hd_dynamic_column").val()+"_"+counter).css("display","block");
                    $("#addattachment").modal("hide");
                    $("#loader").hide();
                    return true;
                }else{
                    $("#fileerrormsg").text("Please Upload a file and try again!!!")
                }
                
            }
        });
        return false;
    }
}