//oTable1.destroy();
$('body').on('DOMNodeInserted', 'select.jselect2me', function () {
    $(this).select2();
});
$('body').on('DOMNodeInserted', 'input.bill_date_txt', function () {
    $(this).datepicker({
        todayBtn: 1,
        autoclose: true,
    });
});
//$("#bill_date_txt_1")
//.datepicker({
//    onSelect: function(dateText) {
//        console.log("Selected date: " + dateText + "; input's current value: " + this.value);
//    }
//}).on("change", function() {
//    console.log("Got change event from field");
//});
var TableDatatablesEditable1 = function () {
    var handleTable = function () {

        function restoreRow(oTable1, nRow) {
            var aData = oTable1.fnGetData(nRow);
            var jqTds = $('>td', nRow);

            for (var i = 0, iLen = jqTds.length; i < iLen; i++) {
                oTable1.fnUpdate(aData[i], nRow, i, false);
            }
            oTable1.fnDraw();
        }

        function editRow(oTable1, nRow, tr_index) {
            console.log("DSxss");
            var store_key = $("#store_key_" + tr_index).val();
            var store_physical_address = $("#store_physical_address_" + tr_index).val();
            var bill_date = $("#bill_date_" + tr_index).val();
            var bill_no = $("#bill_no_" + tr_index).val();
            var selected_option = $("#category_id_" + tr_index).val();
            var selected_option_text = $("#selected_category_" + tr_index).val();
            var description = $("#description_" + tr_index).val();
            var qty = $("#qty_" + tr_index).val();
            var rate = $("#rate_" + tr_index).val();
            var amount = $("#amount_" + tr_index).val();
            var status = $("#status_" + tr_index).val();
            var last_paid_date = $("#last_paid_date_" + tr_index).val();
            var last_paid_amount = $("#last_paid_amount_" + tr_index).val();
            var attachment = $("#attachment_" + tr_index).val();
            var is_paid = $("#is_paid_" + tr_index).val();
            var aData = oTable1.fnGetData(nRow);

            var jqTds = $('>td', nRow);
            var tr_id = jqTds.parents("tr").attr("id");
            var tree_dropdown = '';
            if ($("#category_selection").length) {
                tree_dropdown = $("#category_selection").html();

            }
            if ($("#store_selection").length) {
                store_dropdown = $("#store_selection").html();

            }

            description_dropdown = '<select class="form-control jselect2me category_description" name="category_description">';
            description_dropdown += '<option>--Please select category first--</option>';
            description_dropdown += '</select>';
            jqTds[2].innerHTML = store_dropdown;
            jqTds[3].innerHTML = '<input type="text" class="form-control input-small store_physical_address_txt" id="store_physical_address_txt" value="' + aData[3] + '" onkeyup="store_physical_address(this);" >';
            jqTds[4].innerHTML = '<input type="text" class="form-control input-small bill_date_txt" id="bill_date_txt_' + tr_index + '" value="' + aData[4] + '"  onkeyup="bill_date(this);" onblur="bill_date(this);">';
            jqTds[5].innerHTML = '<input type="text" class="form-control input-small bill_no_txt" id="bill_no_txt" value="' + aData[4] + '" onkeyup="bill_no(this);" >';
            jqTds[6].innerHTML = tree_dropdown;
//            jqTds[7].innerHTML = '<input type="text" class="form-control input-small pro_desc_txt" id="pro_desc_txt" value="' + aData[3] + '" onkeyup="pro_desc(this);" >';
            jqTds[7].innerHTML = description_dropdown;
            jqTds[8].innerHTML = '<input type="text" class="form-control input-small integers qty_txt ggg" id="qty_txt" value="' + aData[4] + '" onkeyup="qty(this);" onblur="qty_dis(this);" >';
            jqTds[9].innerHTML = '<input type="text" class="form-control input-small integers rate_txt" id="rate_txt" value="' + aData[5] + '" onkeyup="rate(this);" onblur="rate_dis(this);" >';
            jqTds[10].innerHTML = '<input type="text" class="form-control input-small integers pro_amount_txt" id="pro_amount_txt" value="' + aData[6] + '" onkeyup="item_amount(this);" onblur="item_amount_dis(this);">';
            jqTds[11].innerHTML = '<input type="text" class="form-control input-small status" id="status_txt" value="' + aData[6] + '" onkeyup="status(this);">';
            jqTds[12].innerHTML = '<input type="text" class="form-control input-small last_paid_date" readonly id="last_paid_date_txt" value="' + aData[6] + '" onkeyup="last_paid_date(this);">';
            jqTds[13].innerHTML = '<input type="text" class="form-control input-small last_paid_amount" readonly id="last_paid_amount_txt" value="' + aData[6] + '" onkeyup="last_paid_amount(this);">';
            jqTds[14].innerHTML = '<input type="file" class="input-small attachment" id="attachment_txt">';
//            jqTds[15].innerHTML = '<input type="text" class="form-control input-small is_paid" id="is_paid_txt" value="' + aData[6] + '" onkeyup="is_paid(this);">';
//            jqTds[15].innerHTML = '<input type="button" content="Paid" value="Paid" class="btn btn-success btn-sm is_paid" id="is_paid_txt" value="' + aData[6] + '" onclick="is_paid(this);">';
            jqTds[15].innerHTML = '<button class="btn btn-success btn-sm is_paid" id="is_paid_txt" value="' + aData[6] + '" onclick="is_paid(this);">Paid</button>';
            jqTds[16].innerHTML = '<a class="item_cancel" href="">Cancel</a>';
            console.log($(".datepicker-dropdown dropdown-menu").length);

//            var select_cnt = $("#" + tr_id).find("td > .select2-container").length;
//            if (select_cnt >= 2) {
//                $("#" + tr_id).find("td > .select2-container:first").remove();
//            }
//alert(store_physical_address);
            $("#" + tr_id).children('td').eq('2').children("select").select2('data', {id: selected_option, text: selected_option_text});
            $("#" + tr_id).children('td').eq('2').children("select").val(selected_option).trigger('change');
            $("#" + tr_id).children('td').eq('3').children("input").val(store_physical_address);
            $("#" + tr_id).children('td').eq('4').children("input").val(bill_date);
            $("#" + tr_id).children('td').eq('5').children("input").val(bill_no);
            $("#" + tr_id).children('td').eq('6').children("select").select2('data', {id: selected_option, text: selected_option_text});
            $("#" + tr_id).children('td').eq('6').children("select").val(selected_option).trigger('change');
            $("#" + tr_id).children('td').eq('7').children("input").val(description);
            $("#" + tr_id).children('td').eq('8').children("input").val(qty);
            $("#" + tr_id).children('td').eq('9').children("input").val(rate);
            $("#" + tr_id).children('td').eq('10').children("input").val(amount);
            $("#" + tr_id).children('td').eq('11').children("input").val(status);
            $("#" + tr_id).children('td').eq('12').children("input").val(last_paid_date);
            $("#" + tr_id).children('td').eq('13').children("input").val(last_paid_amount);
            $("#" + tr_id).children('td').eq('14').children("input").val(attachment);
            $("#" + tr_id).children('td').eq('15').children("input").val(is_paid);
        }

        function saveRow(oTable1, nRow, tr_index) {
           
            var jqInputs = $('input[type="text"]', nRow);
            var acc_name = $("#selected_category_" + tr_index).val();
            console.log(jqInputs);
            alert("DS");
            oTable1.fnUpdate(acc_name.value, nRow, 2, false);
            oTable1.fnUpdate(jqInputs[1].value, nRow, 3, false);
            oTable1.fnUpdate(jqInputs[2].value, nRow, 4, false);
            oTable1.fnUpdate(jqInputs[3].value, nRow, 5, false);
            oTable1.fnUpdate(jqInputs[4].value, nRow, 6, false);

//            oTable1.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 9, false);
//            oTable1.fnUpdate('<a class="delete" href="">Delete</a>', nRow, 10, false);
            oTable1.fnDraw();
//            item_calc_total1();
        }

        function cancelEditRow(oTable1, nRow) {
            var jqInputs = $('input', nRow);
            oTable1.fnUpdate(jqInputs[0].value, nRow, 0, false);
            oTable1.fnUpdate(jqInputs[1].value, nRow, 1, false);
            oTable1.fnUpdate(jqInputs[2].value, nRow, 2, false);
            oTable1.fnUpdate(jqInputs[3].value, nRow, 3, false);
            oTable1.fnUpdate(jqInputs[4].value, nRow, 4, false);
            oTable1.fnUpdate(jqInputs[5].value, nRow, 5, false);
            oTable1.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 6, false);
            oTable1.fnDraw();
        }

        var table = $('#sample_editable_2');

        oTable1 = table.dataTable({
            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
            // So when dropdowns used the scrollable div should be removed. 
            //"dom": "<'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",

            "lengthMenu": [
                [5, 15, 20, -1],
                [5, 15, 20, "All"] // change per page values here
            ],
            "lengthChange": false,
//             "searching": false,
            // Or you can use remote translation file
            //"language": {
            //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
            //},

            // set the initial value
            "pageLength": -1,
            "language": {
                "lengthMenu": " _MENU_ records"
            },
            "columnDefs": [{// set default column settings
                    'orderable': true,
                    'targets': [0]
                }, {
                    "searchable": true,
                    "targets": [0]
                }],
            "order": [
                [0, "asc"]
            ] // set first column as a default sort by asc
        });

        var tableWrapper = $("#sample_editable_2_wrapper");

        var nEditing = null;
        var nNew = false;

        $('#sample_editable_2_new').click(function (e) {
            e.preventDefault();

            if (nNew && nEditing) {
                if (confirm("Previose row not saved. Do you want to save it ?")) {
                    saveRow(oTable1, nEditing, ''); // save
                    $(nEditing).find("td:first").html("Untitled");
                    nEditing = null;
                    nNew = false;

                } else {
                    oTable1.fnDeleteRow(nEditing); // cancel
                    nEditing = null;
                    nNew = false;

                    return;
                }
            }

            var aiNew = oTable1.fnAddData(['', '', '', '', '', '']);
            var nRow = oTable1.fnGetNodes(aiNew[0]);
            editRow(oTable1, nRow, '');
            nEditing = nRow;
            nNew = true;
        });

        table.on('click', '.delete', function (e) {
            e.preventDefault();
            var row_index = $(this).parents("tr").index();


            var tot_row = $("#sample_editable_2 tbody > tr").length;
            if (tot_row > 1) {
                if (confirm("Are you sure to delete this row ?") == false) {
                    return;
                }
                var nRow = $(this).parents('tr')[0];
                oTable1.fnDeleteRow(nRow);
                $('#sample_editable_2 tbody tr').each(function (idx) {
                    $(this).children("td:eq(1)").html(idx + 1);
                });
                item_calc_total1();
                if (tot_row == 2) {
                    $("#sample_editable_2 tbody > tr > td").find(".delete").html("Clear");
                }
            } else {
                $("#category_id_" + row_index).val("");
                $("#selected_category_" + row_index).val("");
                $("#pro_description_" + row_index).val("");
                $("#qty_" + row_index).val("");
                $("#rate_" + row_index).val("");
                $("#pro_amount_" + row_index).val("");
                $("#sample_editable_2 tbody > tr > td:eq(2)").html("");
                $("#sample_editable_2 tbody > tr > td:eq(3)").html("");
                $("#sample_editable_2 tbody > tr > td:eq(4)").html("");
                $("#sample_editable_2 tbody > tr > td:eq(5)").html("");
                $("#sample_editable_2 tbody > tr > td:eq(6)").html("");
                $("#item_total").html("0.00");
                item_calc_total1();

            }
        });

        table.on('click', '.item_cancel', function (e) {
            var row_index = $(this).parents("tr").index();
            $("#category_id_" + row_index).val("");
            $("#selected_category_" + row_index).val("");
            $("#description_" + row_index).val("");
            $("#qty_" + row_index).val("");
            $("#rate_" + row_index).val("");
            $("#amount_" + row_index).val("");
            item_calc_totbalance(row_index);
            e.preventDefault();

            if (nNew) {
                oTable1.fnDeleteRow(nEditing);
                nEditing = null;
                nNew = false;
            } else {
                restoreRow(oTable1, nEditing);
                nEditing = null;
            }
        });

        table.on('click', '.txt_td,.save_td', function (e) {
            console.log("Ds");

            var event_target = e.target;
            if (event_target == "[object HTMLInputElement]" || event_target == "[object HTMLSelectElement]" || event_target == "[object HTMLOptionElement]") {
                return false;
            }

            var tr_id = $(this).closest('tr').attr('id');
            var id_arr = tr_id.split("_");
            var tr_index = id_arr[1];

            item_calc_total1(tr_index);
            e.preventDefault();
            nNew = false;
            /* Get the row as a parent of the link that was clicked on */
            var nRow = $(this).parents('tr')[0];

            if (nEditing !== null && nEditing != nRow) {
                $('#' + tr_id + ' > td').addClass("fill_tr");
                /* Currently editing - but not this row - restore the old before continuing to edit mode */

                restoreRow(oTable1, nEditing);
                editRow(oTable1, nRow, tr_index);
                item_save_data(nEditing);
                nEditing = nRow;
            } else if (nEditing == nRow && $(this).children("a").html() == "Save") {

                $('#qty_' + tr_index).val($('#' + tr_id).find(".qty_txt  > input").val());
                $('#rate_' + tr_index).val($('#' + tr_id).find(".rate_txt  > input").val());
                $('#amount_' + tr_index).val($('#' + tr_id).find(".amount_txt  > input").val());
                /* Editing this row and want to save it */
                saveRow(oTable1, nEditing, tr_index);
                $('#' + tr_id + ' > td').addClass("fill_tr");
                nEditing = null;
//                alert("Updated! Do not forget to do some ajax to sync with backend :)");
            } else {
                if (!$('.fill_tr > .product_cls').length) {
                    $('#' + tr_id + ' > td').addClass("fill_tr");
                    /* No edit in progress - let's start one */
                    editRow(oTable1, nRow, tr_index);
                    nEditing = nRow;
                }
            }
        });
    }

    return {
        //main function to initiate the module
        init: function () {
            handleTable();
        }

    };

}();

//jQuery(document).ready(function () {
TableDatatablesEditable1.init();
//});
function item_save_data(nEditing) {
    $('#sample_editable_2 tbody tr').each(function (idx) {
        $(this).children("td:eq(1)").html(idx + 1);
    });
    var id = $(nEditing).attr("id");
    if (id) {
        var id_arr = id.split("_");
        var id_index = id_arr[1];
        var store_physical_address = $("#store_physical_address_" + id_index).val();
        var store_physical_address = $("#bill_no_" + id_index).val();
        var store_physical_address = $("#store_physical_address_" + id_index).val();
        var store_physical_address = $("#store_physical_address_" + id_index).val();
        var pro_name = $("#selected_category_" + id_index).val();
        var desc = $("#description_" + id_index).val();
        var desc = $("#description_" + id_index).val();
        var desc = $("#description_" + id_index).val();
        var qty = $("#qty_" + id_index).val();
        var rate = $("#rate_" + id_index).val();
        var amount = $("#amount_" + id_index).val();
//        if ($("#sample_editable_2 #pro_desc_txt").val() != "" || $("#sample_editable_2 #qty_txt").val() != "" || $("#sample_editable_2 #rate_txt").val() != "" || $("#sample_editable_2 #pro_amount_txt").val() != "") {
//            var temp_total = 0;
//            if ($("#sample_editable_2 #pro_desc_txt").val() == "") {
//                $("#" + id).children('td').eq('2').find('.jselect2me > a').addClass('errorClass');
//                temp_total++;
//            }
//            if ($("#sample_editable_2 #qty_txt").val() == "") {
//                $("#" + id).children('td').eq('4').find('input').addClass('errorClass');
//                temp_total++;
//            }
//            if ($("#sample_editable_2 #rate_txt").val() == "") {
//                $("#" + id).children('td').eq('5').find('input').addClass('errorClass');
//                temp_total++;
//            }
//            if ($("#sample_editable_2 #pro_amount_txt").val() == "") {
//                $("#" + id).children('td').eq('6').find('input').addClass('errorClass');
//                temp_total++;
//            }
//            if (temp_total > 0) {
//                return false;
//            }
//
//        }
        $("#" + id).children('td').eq('2').html(pro_name);
        $("#" + id).children('td').eq('3').html(store_physical_address);
        $("#" + id).children('td').eq('3').html(desc);
        $("#" + id).children('td').eq('4').html(qty);
        $("#" + id).children('td').eq('5').html(rate);
        $("#" + id).children('td').eq('6').html(amount);
        item_calc_total1(id_index);
    }

}
function qty_dis(element) {
    var index = $(element).closest("tr").attr("id");
    var id_arr = index.split("_");
    var id_index = id_arr[1];
    var qty_val = $(element).val();
    var rate_val = $("#" + index).children('td').eq('5').children("input").val();
    if (qty_val != '') {
        var val1 = parseFloat(qty_val).toFixed(2);
        $("#qty_" + id_index).val(val1);
        if (rate_val != '') {
            $(element).val(val1);
            var tot_amount = parseFloat(val1 * rate_val).toFixed(2);
            $("#" + index).children('td').eq('6').children("input").val(tot_amount);
            $("#amount_" + id_index).val(tot_amount);
        }
    }
//    calc_total(index, 'qty');
}
function rate_dis(element) {
    var index = $(element).closest("tr").attr("id");
    var id_arr = index.split("_");
    var id_index = id_arr[1];
    var rate_val = $(element).val();
    if (rate_val != '' && !isNaN(rate_val)) {
        var val1 = parseFloat(rate_val).toFixed(2);
        $("#rate_" + id_index).val(val1);
        var qty_val = $("#" + index).children('td').eq('4').children("input").val();
        if (qty_val != '') {
            $(element).val(val1);
            var tot_amount = parseFloat(val1 * qty_val).toFixed(2);
            $("#" + index).children('td').eq('6').children("input").val(tot_amount);
            $("#amount_" + id_index).val(tot_amount);
        }
    }
//    calc_total(index, 'rate');
}
function item_amount_dis(element) {
    var index = $(element).closest("tr").attr("id");
    var id_arr = index.split("_");
    var id_index = id_arr[1];
    var amount_val = $(element).val();
    if (amount_val != '' && !isNaN(amount_val)) {
        var val1 = parseFloat(amount_val).toFixed(2);
        $(element).val(val1);
        $("amount_" + id_index).val(val1);
        var qty_val = $("#" + index).children('td').eq('4').children("input").val();
        var rate_val = $("#" + index).children('td').eq('5').children("input").val();
        var tot_amount = parseFloat(qty_val * rate_val).toFixed(2);
        if (val1 != tot_amount && qty_val != '') {
            var rate = parseFloat(val1 / qty_val).toFixed(2);
            $("#" + index).children('td').eq('5').children("input").val(rate);
            $("#rate_" + id_index).val(rate);
        }
    } else {
        $("#amount_" + id_index).val("");
    }
//    calc_total(index, 'rate');
}

//function remove_item_lines(element) {
//    var total_tr = $("#sample_editable_2 tbody > tr").length;
//    if (total_tr > 0) {
//
//        for (i = 0; i < total_tr - 1; i++) {
//            $('#sample_editable_2 tbody > tr:last').remove();
//        }
//        $("#itr_0 td").not(".drag_td").html("");
//        $("#itr_0 input:hidden").html("");
//        $("#item_total").html("0");
//    } else {
//        location.reload();
//    }
//}
function remove_item_lines(element) {
    var total_tr = $("#sample_editable_2 tbody > tr").length;
    if (total_tr >= 0) {
        sum_amount = 0;
        for (var i = 0; i <= total_tr; i++) {
            var product_guids = $("#category_id_" + i).val();
            var pro_description = $("#description_" + i).val();
            var qty = $("#qty_" + i).val();
            var rate = $("#rate_" + i).val();
            var pro_amount = $("#amount_" + i).val();
            if (typeof product_guids == "undefined") {
                product_guids = "";
            }
            if (typeof pro_description == "undefined") {
                pro_description = "";
            }
            if (typeof qty == "undefined") {
                qty = "";
            }
            if (typeof rate == "undefined") {
                rate = "";
            }
            if (typeof pro_amount == "undefined") {
                pro_amount = "";
            }
            var table = $("#sample_editable_2").DataTable();
            if (product_guids == "" && pro_description == "" && qty == "" && rate == "" && pro_amount == "") {
                sum_amount += amount;
                if (i == 0) {
                    $("#itr_0 td").not(".drag_td").html("");
                    $("#itr_0 input:hidden").html("");
                } else {
                    table.row('#itr_' + i).remove().draw(false);
                }

            }
        }

        var total_bal = parseFloat($("#item_total").html().replace(/,/g, ''));
        var final_total = parseFloat(total_bal - sum_amount);
        if (!isNaN(final_total)) {
            $("#item_total").html(final_total.toFixed(2));
        }
    } else {
        location.reload();
    }

}
$('body').click(function (evt) {
    var value = $("#event_type").val();
//    if (value == "expenses" || value == "bills" || value == "checks" || value == "vendor_credits") {
    //For descendants of menu_content being clicked, remove this check if you do not want to put constraint on descendants.
    if (evt.target.id == "sample_editable_2") {
        return false;
    } else if ($(evt.target).closest('#sample_editable_2').length)
    {
        return false;
    } else {
        var Row = $(".pro_desc_txt").parents("tr")[0];
        item_save_data(Row);
    }
//    }
});
//$('#bill_date_txt_1').datepicker().on('changeDate', function (ev) {
//    $('#bill_date_txt_1').change();
//});
//$('#bill_date_txt_1').val('0000-00-00');
//$('#bill_date_txt_1').change(function () {
//    console.log($('#bill_date_txt_1').val());
//});


//$(function(){
//   $('.bill_date_txt').datepicker({
//      onSelect: function (dateText, inst) {
//         alert('select!'+dateText);
//         $(this).val("sd");
//         $(this).change();
//      }
//   });
//});

//$('.bill_date_txt').val('0000-00-00');
//$('.bill_date_txt').on('dp.change', function(e){ 
//    var formatedValue = e.date.format(e.date._f);
//    console.log("Test++"+formatedValue);
//});
//function set_datepicker(element){
//     $(element).change();
//     var value = $(this).val();
//    var index = $(this).closest("tr").attr("id");
//    var id_arr = index.split("_");
//    var id_index = id_arr[1];
//    console.log("sss"+value);
// $("#bill_date_"+id_index).val(value);
//
//}
//$(".bill_date_txt").datepicker().on('changeDate', function (ev) {
//   
//    var value = $(this).val();
//    var index = $(this).closest("tr").attr("id");
//    var id_arr = index.split("_");
//    var id_index = id_arr[1];
//    console.log("dsd"+value);
//    $("#bill_date_"+id_index).val(value);
//});
function store_physical_address(element) {
    var value = $(element).val();
    var index = $(element).closest("tr").attr("id");
    var id_arr = index.split("_");
    var id_index = id_arr[1];
    $("#store_physical_address_" + id_index).val(value);
}

function bill_date(element){
  var value = $(element).val();
  var index = $(element).closest("tr").attr("id");
  var id_arr = index.split("_");
  var id_index = id_arr[1];
  $("#bill_date_" + id_index).val(value);
}
function bill_no(element){
  var value = $(element).val();
  var index = $(element).closest("tr").attr("id");
  var id_arr = index.split("_");
  var id_index = id_arr[1];
  $("#bill_no_" + id_index).val(value);
}
function is_paid(element){
    var value = $(e)
}