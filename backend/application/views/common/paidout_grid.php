
<?php
$attributes = array('class' => 'form-horizontal validate', 'id' => 'paidout_data');
echo form_open_multipart('common/addpaidout', $attributes);
?>
<input type="hidden" name="store_key" value="<?php echo $store_key ?>">
<input type="hidden" name="year" value="<?php echo $year ?>">
<input type="hidden" name="month" value="<?php echo $month ?>">
<div id="successmessage"></div>
<div class="col-md-12">
    <table class="table table-striped table-bordered" id="tbl_paidout">
        <thead>
        <th>Action</th>
        <th style="width:70%">Date</th>
        <th style="width:40%">Day</th>
        <?php
        if (sizeof($dynamic_column)) {
            foreach ($dynamic_column as $value) {
                ?>
                <th style="width:50%"><?php echo $value ?></th>
                <?php
            }
        }
        ?>
        <th>Total</th>
        <th>Paid Out</th>
        <!-- <th>Actions</th> -->
        </thead>
        <tbody>
            <?php
            if (sizeof($alldates) > 0):
                $i = 0;
                foreach ($alldates as $dates):
                    $dataarray = "";
                    $invoice_array = "";
                    $style = "display:none";
                    if (sizeof($paidoutdata) > 0):
                        $exists = "";
                        $exists = array_search($dates, array_column($paidoutdata, 'cdate'));
                        if ($exists !== false) {
                            $dataarray = $paidoutdata[$exists];
                        }
                    endif;
                    if (sizeof($invoice_uploaded) > 0):
                        if (isset($invoice_uploaded[$dates])) {
                            $invoice_array = $invoice_uploaded[$dates];
                        }
                    endif;
                    $is_lock_entry = isset($dataarray['is_lock']) ? $dataarray['is_lock'] : '0';
                    ?>


                    <tr id="row_<?php echo $i ?>" class="<?php echo ($is_lock_entry == 1) ? 'lock_tr_bg' : ''; ?>">
                        <td><i class="<?php echo $is_lock_entry == 1 ? "fa fa-unlock" : "fa fa-lock"; ?>" data-value="<?php echo $is_lock_entry; ?>" onclick="lock_paidout_entry(this);"></i><i class="fa fa-save" onclick="save_paidout_entry(this);"></i></td>
                        <td><input type='hidden' name='cdate[]' id='cdate_<?php echo $i ?>' value='<?php echo $dates ?>'><?php echo $dates ?></td>
                        <td><input type='hidden' name='day[]' value='<?php echo date('D', strtotime($dates)) ?>'><?php echo date('D', strtotime($dates)) ?></td>

                        <?php
                        foreach ($dynamic_column as $key => $value):
                            if ($invoice_array != "") {
                                if (in_array($key, $invoice_array)) {
                                    $style = "display:block";
                                } else {
                                    $style = "display:none";
                                }
                            } else {
                                $style = "display:none";
                            }
                            ?>

                            <td><input style="pointer-events: <?php echo $is_lock_entry == 1 ? "none" : "initial"; ?>" data-name="<?php echo $key; ?>" class="txt_expense integers" type="text" id='<?php echo $key . "_" . $i ?>' onfocusout='calculatetotal(this,<?php echo $i ?>, 1)'  name="<?php echo $key ?>[]" value="<?php
                                if ($dataarray != ""): if (isset($dataarray[$key]))
                                        echo $dataarray[$key];
                                    else
                                        echo "0.00";
                                else: echo "0.00";
                                endif;
                                ?>"> <a  href='javascript:void(0);' id='addattachment-<?php echo $key; ?>' data-id='addattachment_<?php echo $i ?>'  onclick='display_attachment(this, "<?php echo $i ?>")'><span class='fa fa-upload pull-right'></span></a><i class='fa fa-paperclip pull-right' id='alreadyupload-<?php echo $key . "-$i" ?>' onclick='showupload_attachment(this, "<?php echo $i ?>")'  style='<?php echo $style ?>'></i></td>

                        <?php endforeach; ?>


                        <td><input type='hidden' value='<?php
                            if (isset($dataarray['total']))
                                echo $dataarray['total'];
                            else
                                echo "0.00";
                            ?>' name='total[]' id='total_<?php echo $i ?>'><label id='lbltotal_<?php echo $i ?>'><?php
                                   if (isset($dataarray['total']))
                                       echo $dataarray['total'];
                                   else
                                       echo "0.00";
                                   ?></label></td>
                        <td><input type='hidden' value='<?php
                            if (array_key_exists($dates, $paidoutamount)) {
                                if ($paidoutamount[$dates] > 0)
                                    echo $paidoutamount[$dates];
                                else
                                    echo "0.00";
                            } else {
                                echo "0.00";
                            }
                            ?>' name='paidout[]' id='paidout_<?php echo $i ?>'><?php
                                   if (array_key_exists($dates, $paidoutamount)) {
                                       if ($paidoutamount[$dates] > 0)
                                           echo $paidoutamount[$dates];
                                       else
                                           echo "0.00";
                                   } else {
                                       echo "0.00";
                                   }
                                   ?></td>
                    </tr>
                    <?php
                    $i++;
                endforeach;
            else:
                ?>
            <p class="no-data">No more data</p>
        <?php endif; ?>
        </tbody>
        </thead>
    </table>
</div>
<div class="form-group">
    <div class="col-xs-2 btn_group">
        <input type="submit" value="Save" id="add_paidout" class="btn btn-sm blue">
    </div>
</div>

</form>
<div id="add_uploadattachment_modal"></div>
<div id="show_uploadattachment_modal"></div>
<script>
    $(document).ready(function () {
        var table = $('#tbl_paidout').DataTable({
            scrollY: '70vh',
            scrollX: '100vw',
            scrollCollapse: true,
            fixedColumns: {
                leftColumns: 2,
            },
            paging: false,
            columnDefs: [
                {width: '20%', targets: 0}
            ]
        });
    });
    $("#paidout_data").submit(function () {
        //check paidout
        days =<?php echo sizeof($alldates) ?>;
        var chk = 0;
        var i = 0;
        for (i = 0; i <= days; i++) {
            var total = 0;
            $("#row_" + i).find(".txt_expense").each(function () {
                if ($(this).val() != "" && $(this).val() != NaN && $(this).val() != undefined) {
                    var count = parseFloat($(this).val());
                    total = total + count;
                }

            });
            var paid_out_chk = parseFloat($("#paidout_" + i).val());
            if (paid_out_chk > 0) {
//                 if(parseFloat(total) < paid_out_chk || parseFloat(total) > paid_out_chk){
                if (parseFloat(total) == parseFloat(paid_out_chk)) {
                    $("#row_" + i).css('background-color', '#fbfcfd !important');
                    $("#row_" + i + ':hover').css('background-color', '#fbfcfd !important');
                } else {
                    chk = i;
                    $("#row_" + i).css('background-color', '#cf352e !important');
                    $("#row_" + i + ':hover').css('background-color', '#cf352e !important');
                }
            }
        }
        if (chk == 0) {
            $('#loader').show();
            $("#err-msg").text("");
            // $("#paidout_data").submit();
            $.ajax({
                url: site_url + 'common/addpaidout',
                data: $("#paidout_data").serialize(),
                method: 'POST',
                async: false,
                success: function (response) {
                    type = "paid-out-recap"
                    method = "getPaidoutGrid";
                    store_id = $("#store_id").val();
                    year = $("#year").val();
                    month = "";
                    //get month of current active li
                    $(".bootstrapWizard.form-wizard li").each(function () {
                        if ($(this).hasClass("active")) {
                            month = $(this).attr("id");
                            month = month.split('_').pop();
                        }

                    });
                    callajax(store_id, year, month, type, method);
                    $('#successmessage').css("display", "block");
                    $('html, body').animate({
                        'scrollTop': $("#successmessage").position().top
                    });
                    $('#successmessage').fadeIn().html('<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Paid out data added successfully!!!</div>');
                    setTimeout(function () {
                        $('#successmessage').fadeOut("slow");
                    }, 5000);
                    return true;
                }
            });
            return false;
        } else {
            $("#err-msg").text("Paid out and total does not match");
            $('html, body').animate({
                'scrollTop': $("#err-msg").position().top
            });
            return false;
        }
    });
    function display_attachment(ct, counter) {
        $("#loader").show();
        var dynamic_column = $(ct).attr("id");
        dynamic_column = dynamic_column.split('-').pop();
        var cdate = $("#cdate_" + counter).val();
        $.ajax({
            url: site_url + 'common/getAttachmentmodal',
            data: {"dynamic_column": dynamic_column, "cdate": cdate, "counter": counter, "store_key": $("#store_id").val()},
            method: 'POST',
            async: false,
            success: function (response) {
                $('#add_uploadattachment_modal').html(response);
                $("#loader").hide();
                $("#addattachment-modal").modal("show");
                return true;
            }
        });
        return false;
    }
    function showupload_attachment(ct, counter) {
        $("#loader").show();
        var dynamic_column = $(ct).attr("id");
        dynamic_column = dynamic_column.split('-')[1];
        var cdate = $("#cdate_" + counter).val();
        $.ajax({
            url: site_url + 'common/getUploadAttachmentmodal',
            data: {"dynamic_column": dynamic_column, "cdate": cdate, "counter": counter, "store_key": $("#store_id").val()},
            method: 'POST',
            async: false,
            success: function (response) {
                $('#show_uploadattachment_modal').html(response);
                $("#loader").hide();
                $("#showattachment-modal").modal("show");
                return true;
            }
        });
        return false;
    }
    function deleteAttachment(r) {

        var retVal = confirm("Are you sure want to delete this invoice ?");
        if (retVal == true) {
            $("#loader").show();
            var deleteid = jQuery(r).attr('deleteid');
            var counter = $("#hd_show_counter").val();
            var dynamic_column = $("#hd_show_dynamic_column").val();
            $.ajax({
                url: site_url + 'common/deleteAttachment',
                data: {id: deleteid},
                method: 'POST',
                success: function (response) {
                    jQuery(r).closest('tr').remove();
                    if ($("#show-attachement-table tr").length == 1) {
                        $("#no-records").css("display", "block");
                        $("#alreadyupload-" + dynamic_column + "-" + counter).css("display", "none")
                    }
                    $("#loader").hide();
                }
            });

            return true;
        } else {
            return false;
        }
    }
    $('#confirm-delete').on('show.bs.modal', function (e) {
        $(this).find('.btn-ok').attr('href', $(e.relatedTarget).data('href'));

        $('.debug-url').html('Delete URL: <strong>' + $(this).find('.btn-ok').attr('href') + '</strong>');
    });
    function uploadattachments() {
        $("#loader").show();
        var fd = new FormData();
        var file_data = $('#dynamicinvoicefile_counter')[0].files; // for multiple files
        fd.append("files", file_data[0]);
        fd.append("store_key", $("#store_id").val());
        month = "";
        //get month of current active li
        $(".bootstrapWizard.form-wizard li").each(function () {
            if ($(this).hasClass("active")) {
                month = $(this).attr("id");
                month = month.split('_').pop();
            }

        });
        fd.append("month", month);
        fd.append("year", $("#year").val());
        fd.append("dynamic_column", $("#hd_dynamic_column").val());
        fd.append("hd_cdate", $("#hd_cdate").val());
        var counter = $("#hd_counter").val();
        $.ajax({
            url: site_url + 'common/setuploadAttachments',
            data: fd,
            method: 'POST',
            async: false,
            dataType: 'json',
            contentType: false,
            processData: false,
            cache: false,
            success: function (response) {
                if (response > 0) {
                    $("#alreadyupload-" + $("#hd_dynamic_column").val() + "-" + counter).css("display", "block");
                    $("#addattachment-modal").modal("hide");
                    $("#loader").hide();
                    return true;
                } else {
                    $("#fileerrormsg").text("Please Upload a file and try again!!!")
                }

            }
        });
        return false;
    }
    function save_paidout_entry(element) {
        var tr_Arr = {};
        var store_key = $("input[name='store_key']").val();
        var month = $("input[name='month']").val();
        var year = $("input[name='year']").val();
        var cdate = $(element).parents("tr").find("input[name='cdate[]']").val();
        var total = $(element).parents("tr").find("input[name='total[]']").val();
        var paidout = $(element).parents("tr").find("input[name='paidout[]']").val();
        var total = 0;
        $(element).parents("tr").find("input[type='text']").each(function () {
            var key = $(this).data("name");
            var value = $(this).val() != '' ? parseFloat($(this).val()) : "";
            if (value != "" && !isNaN(value) && typeof value != undefined) {
                var count = value;
                total = total + count;
            }
//               debugger;
            tr_Arr[key] = value;
        });
        tr_Arr['store_key'] = store_key;
        tr_Arr['month'] = month;
        tr_Arr['year'] = year;
        tr_Arr['cdate'] = cdate;
        tr_Arr['single_row'] = 1;
        tr_Arr['total'] = total;
        if (paidout > 0) {
//                 if(parseFloat(total) < paid_out_chk || parseFloat(total) > paid_out_chk){
            if (parseFloat(total) == parseFloat(paidout)) {
//                debugger;
                $(this).parents("tr").css('background-color', '#fbfcfd !important');
                $(this).parents("tr").css('background-color', '#fbfcfd !important');

//                return false;
//                var jsonString = JSON.stringify(tr_Arr);
                console.log(tr_Arr);
//                console.log(jsonString);
//                console.log($(tr_Arr).serialize());

                $.ajax({
                    method: 'POST',
                    url: site_url + 'common/addpaidout',
                    data: tr_Arr,
                    async: false,
                    success: function (data) {
                        alert("Succeded");
                    }

                });

            } else {
                $(this).parents("tr").css('background-color', '#cf352e !important');
                $(this).parents("tr").css('background-color', '#cf352e !important');
                $("#err-msg").text("Paid out and total does not match");
                $('html, body').animate({
                    'scrollTop': $("#err-msg").position().top
                });
                return false;
            }
        }

    }
    function lock_paidout_entry(element) {
        var store_key = $("input[name='store_key']").val();
        var month = $("input[name='month']").val();
        var year = $("input[name='year']").val();
        var cdate = $(element).parents("tr").find("input[name='cdate[]']").val();
        var is_lock = $(element).data("value");
        var total = $(element).parents("tr").find("input[name='total[]']").val();
        var paidout = $(element).parents("tr").find("input[name='paidout[]']").val();
        if (parseFloat(total) == parseFloat(paidout)) {
            $.ajax({
                method: 'POST',
                url: site_url + 'common/LockUnlockEntry',
                data: {store_key: store_key, month: month, year: year, cdate: cdate, is_lock: is_lock},
                async: false,
                beforeSend: function () {
                    $("#loadingmessage").show();
                },
                success: function (data) {
                    $("#loadingmessage").hide();
                    if (is_lock == 1) {
                        $(element).parents("tr").removeClass("lock_tr_bg");
                    } else {
                        $(element).parents("tr").addClass("lock_tr_bg");
                    }
                    $(element).parents("tr").find("input[type='text']").each(function () {
                        if (is_lock == 1) {
                            $(this).css("pointer-events", "initial");
                        } else {
                            $(this).css("pointer-events", "none");
                        }
                    });
                    (is_lock == 1) ? $(element).data("value", 0) : $(element).data("value", 1);
                    $(element).toggleClass('fa-lock fa-unlock');
                }

            });
        } else {
            $(this).parents("tr").css('background-color', '#cf352e !important');
            $(this).parents("tr").css('background-color', '#cf352e !important');
            $("#err-msg").text("Paid out and total does not match");
            $('html, body').animate({
                'scrollTop': $("#err-msg").position().top
            });
            return false;
        }
    }
</script>