<input type="hidden" name="store_key" value="<?php echo $store_key ?>">
<input type="hidden" name="year" value="<?php echo $year ?>">
<input type="hidden" name="month" value="<?php echo $month ?>">
<?php
//echo "<pre>";
//print_R($monthlydata);
//echo "</pre>";
$display_charity_column = 0;
foreach ($alldates as $dates):
    foreach ($dynamic_column as $key => $value):
        if (isset($monthlydata[$dates][$key])):
            if (($key == "tracked_fee_exempt_net_sales" && $monthlydata[$dates]['tracked_fee_exempt_net_sales'] > 0) || ($key == "charity_net_sales" && $monthlydata[$dates]['charity_net_sales'] > 0) || ($key == "paid_ins" && $monthlydata[$dates]['paid_ins'] > 0) || ($key == "gift_certificate_sales" && $monthlydata[$dates]['gift_certificate_sales'] > 0)):
                $display_charity_column = 1;
                break;

            endif;

        endif;
    endforeach;
    if ($display_charity_column == 1) {
        break;
    }
endforeach;
?>
<span id="errormsg" style="color:red"></span>
<div id="successmessage"></div>
<div class="row">
    <div class="col-md-12">
        <div class="btn_group pull-right">
            <button type="button" id="add_monthly" class="btn btn-sm blue pull-right">Save</button>
            <?php if ($display_charity_column == 1) { ?>
                <a class="btn blue toggle-vis pull-right btn-sm" data-column="0" >Show Charity</a> 
            <?php } ?>
        </div>
    </div>
    <div class="col-md-12">
        <table class="table table-striped table-bordered" id="tbl_monthlyrecap">
            <thead>
            <th>Action</th>
            <th>Date</th>
            <th>Day</th>
            <?php
            if (sizeof($dynamic_column)) {
                foreach ($dynamic_column as $key_txt => $value) {
                    if ($display_charity_column == 0 && ($key_txt == 'tracked_fee_exempt_net_sales' || $key_txt == 'charity_net_sales' || $key_txt == 'paid_ins' || $key_txt == 'gift_certificate_sales')):
                        continue;
                    endif;
                    ?>
                    <td style="width:50%"><?php echo $value; ?></td>
                    <?php
                }
            }
            ?>
            </thead>
            <tbody>
                <?php
                if (sizeof($alldates) > 0):
                    $i = 0;
                    foreach ($alldates as $dates):
                         $is_lock_entry = isset($monthlydata[$dates]['is_lock']) ? $monthlydata[$dates]['is_lock'] : '0';
                        ?>
                      <tr id="row_<?php echo $i ?>" class="<?php echo ($is_lock_entry == 1) ? 'lock_tr_bg' : ''; ?>">
                    <input type="hidden" name="is_actual_bal_updated" id="is_actual_bal_updated" value="0"/> 
                    <input type='hidden' name='cdate[]' id='cdate_<?php echo $i ?>' value='<?php echo $dates ?>'>
                    <input type="hidden" name="current_row" id="current_row" value="0"/> 
                         <td><i class="<?php echo $is_lock_entry == 1 ? "fa fa-unlock" : "fa fa-lock"; ?>" data-value="<?php echo $is_lock_entry; ?>" onclick="lock_paidout_entry(this);"></i><i class="fa fa-save" onclick="save_paidout_entry(this);"></i></td>
                    <td ><?php echo $dates ?></td>
                    <td><?php echo date('D', strtotime($dates)) ?></td>
                    <?php
                    foreach ($dynamic_column as $key => $value):
                        if ($display_charity_column == 0 && ($key == 'tracked_fee_exempt_net_sales' || $key == 'charity_net_sales' || $key == 'paid_ins' || $key == 'gift_certificate_sales')):
                            continue;
                        endif;
                        ?>

                        <td style="text-align: center;">
                            <?php
                            if ($monthlydata != ""):
                                if (isset($monthlydata[$dates][$key])):
                                    if ($key == "actual_bank_deposit") {
                                        $pointer_style =  $is_lock_entry == 1 ? "none" : "initial";
                                        echo "<input style='pointer-events:$pointer_style' type='text' onkeydown='is_updated(this);' onblur='set_bank_deposite(this);' class='integers' name='actual_deposit[]' id='$dates' value='" . $monthlydata[$dates][$key] . "'><i id='save_txns_" . $i . "' data-id=" . $monthlydata[$dates]['id'] . " onclick='save_deposit(this);' class='fa fa-save doposit_cls'></i>";
                                    } else {

                                        echo "$" . $monthlydata[$dates][$key];
                                        if (($key == 'tracked_fee_exempt_net_sales' || $key == 'charity_net_sales' || $key == 'paid_ins' || $key == 'gift_certificate_sales')):
                                            $checked = $monthlydata[$dates]["is_".$key] == 1 ? "checked" : "";
                                            ?>
                                            <input style="pointer-events: <?php echo $is_lock_entry == 1 ? "none" : "initial"; ?>" type="checkbox" class="pull-right" name="chk_<?php echo $dates . "_" . $key; ?>" onchange="add_value(this);" data-value="<?php echo $monthlydata[$dates][$key]; ?>" data-name="<?php echo $value; ?>" data-key="<?php echo $key; ?>" id="<?php echo $monthlydata[$dates]['id']; ?>" <?php echo $checked;?>/>
                                            <?php
                                        endif;
                                    }


//                             


                                else:
                                    echo "-";
                                endif;
                            else:
                                echo "-";
                            endif;
                            ?>
                        </td>

                        <?php
//                            $tr_cnt++;
                    endforeach;
                    ?>
                    </tr>
                    <?php
                    $i++;
                endforeach;
            else:
                ?>
                <p class="no-data">No more data</p>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<div id="add_uploadattachment_modal"></div>
<script>
    $(document).ready(function () {
        var table = $('#tbl_monthlyrecap').DataTable({
            scrollY: '70vh',
            scrollX: '100vw',
            scrollCollapse: true,
            fixedColumns: {
                leftColumns: 2,
            },
            paging: false,
            columnDefs: [
                {width: '20%', targets: 0}
            ],
        });
        $('.toggle-vis').on('click', function (e) {
            e.preventDefault();

            // Get the column API object
            var column1 = table.column(19);
            var column2 = table.column(20);
            var column3 = table.column(21);
            var column4 = table.column(22);

            // Toggle the visibility
            if (!column1.visible()) {
                $(this).html("Hide Charity");
            } else {
                $(this).html("Show Charity");
            }
            column1.visible(!column1.visible());
            column2.visible(!column2.visible());
            column3.visible(!column3.visible());
            column4.visible(!column4.visible());
        });
    });
    $("#add_monthly").click(function () {
        var b_destinations = document.querySelectorAll("[name^=actual_deposit]");
        var b_destinationsArr = {};

        b_destinations.forEach(function (element) {
            if (element.value != "")
                b_destinationsArr[element.id] = element.value;
        });

        var data = JSON.stringify(b_destinationsArr);
        $.ajax({
            url: site_url + 'common/addactualdeposit',
            data: {data: data, store_key: $("#store_id").val(), single_row: 0},
            method: 'POST',
            dataType: 'JSON',
            async: false,
            success: function (response) {
                type = "paid-out-recap"
                method = "getMonthlyrecapGrid";
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
                $('#successmessage').fadeIn().html('<div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>Actual deposit added successfully!!!</div>');
                setTimeout(function () {
                    $('#successmessage').fadeOut("slow");
                }, 5000);
                return true;
            }
        });
    });
    function set_bank_deposite(element) {
        var value = $.trim($(element).val());
        if (value > 0 && value != '') {
            var gross_sales = $(element).parents("tr").find("td").eq(7).html().replace('$', '');
            var all_cards_total = $(element).parents("tr").find("td").eq(8).html().replace('$', '');
            var paidout = $(element).parents("tr").find("td").eq(12).html().replace('$', '');

            calc_actual_over_shot = (parseFloat(all_cards_total) + parseFloat(paidout) + parseFloat(value)) - parseFloat(gross_sales)
//            console.log(all_cards_total);
//            console.log(gross_sales);
            console.log(paidout);
            console.log(calc_actual_over_shot);
            $(element).parents("tr").find("td").eq(12).html(calc_actual_over_shot.toFixed(2));

        } else {
            $(element).parents("tr").find("td").eq(12).html("$0");
        }
    }
    function save_deposit(element) {
        value = $(element).prev("input[name='actual_deposit[]']").val();
        var actual_over_Shot = $(element).parents("tr").find("td").eq(12).html().replace('$', '');
        id = $(element).data("id");
        $.ajax({
            url: site_url + 'common/addactualdeposit',
            data: {value: value, id: id, actual_over_Shot: actual_over_Shot, single_row: 1},
            method: 'POST',
            dataType: 'JSON',
            async: false,
            success: function (response) {
                $(element).css("color", "green");
                $("#is_actual_bal_updated").val("0");
                $(element).parents("tr").removeClass("edit_txns");
            }
        });
    }
    function add_value(element) {
        id = $(element).attr("id");
        value = $(element).data("value");
        name = $(element).data("name");
        key = $(element).data("key");
        var actual_over_Shot = $.trim($(element).parents("tr").find("td").eq(12).html().replace('$', ''));


        var is_checked = $(element).is(":checked");
        if (is_checked) {
            msg = 'Are you sure you want to add this ' + name + "?";
            var final_value = parseFloat(actual_over_Shot) + parseFloat(value);
            var is_checked_val = 1;
        } else {
            msg = 'Are you sure you want to remove this ' + name + "?";
            var final_value = parseFloat(actual_over_Shot) - parseFloat(value);
            var is_checked_val = 0;
        }
        $(element).parents("tr").find("td").eq(12).html("$"+parseFloat(final_value).toFixed(2));
        var r = confirm(msg);
        if (r == true) {
            $.ajax({
                url: site_url + 'common/addcharityData',
                data: {key: key, final_value: final_value, id: id, is_checked: is_checked_val},
                method: 'POST',
                dataType: 'JSON',
                async: false,
                success: function (response) {
                    
                }
            });
        } else {
            return false;
        }
    }
    function records_changes() {
        var c_element = $("#current_row").val();
        $("#save_txns_" + c_element).trigger("click");
         $("#ConfirmSaveModal").modal("hide");
    }
    function is_updated(element) {
        $("#is_actual_bal_updated").val("1");
        $(element).parents("tr").addClass("edit_txns");
        $(element).next(".doposit_cls").css("color", "red");
    }
    $(document).click(function (e) {
        if ($("#is_actual_bal_updated").val() == 1 && e.target != '[object HTMLTableCellElement]' && e.target != '[object HTMLInputElement]' && e.target != '[object HTMLButtonElement]') {
            $("#current_row").val($("tr.edit_txns").index());
            $('#ConfirmSaveModal').modal({backdrop: 'static', keyboard: false});
            $("#ConfirmSaveModal").modal("show");
//                           
            is_allow_next = 0;
            e.preventDefault();
            return false;
        }
    });
     function lock_paidout_entry(element) {
        var store_key = $("input[name='store_key']").val();
        var month = $("input[name='month']").val();
        var year = $("input[name='year']").val();
        var cdate = $(element).parents("tr").find("input[name='cdate[]']").val();
        var is_lock = $(element).data("value");
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
       
    }
</script>