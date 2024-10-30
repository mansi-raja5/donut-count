var company_currency = $("#company_currency").val();
$('body').on('DOMNodeInserted', 'select.jselect2me', function () {
    $(this).select2();
});
var TableDatatablesEditable = function () {

    var handleTable = function () {

        function restoreRow(oTable, nRow) {
            var aData = oTable.fnGetData(nRow);
            var jqTds = $('>td', nRow);

            for (var i = 0, iLen = jqTds.length; i < iLen; i++) {
                oTable.fnUpdate(aData[i], nRow, i, false);
            }

            oTable.fnDraw();
        }

        function editRow(oTable, nRow, tr_index) {
            var selected_option = $("#product_guids_" + tr_index).val();
            var selected_option_text = $("#selected_products_" + tr_index).val();
            var description = $("#description_" + tr_index).val();
            var qty = $("#qty_" + tr_index).val();
            var rate = $("#rate_" + tr_index).val();
            var amount = $("#amount_" + tr_index).val();
            var selected_tax_option = $("#tax_" + tr_index).val();
            var selected_tax_text = $("#tax_txt_" + tr_index).val();
            var aData = oTable.fnGetData(nRow);


            var jqTds = $('>td', nRow);
            var tr_id = jqTds.parents("tr").attr("id");
            var tree_dropdown = '';

            if ($("#product_selection").length) {
                tree_dropdown = $("#product_selection").html();
            }

            jqTds[2].innerHTML = tree_dropdown;
            jqTds[3].innerHTML = '<input type="text" class="form-control input-small desc_txt" value="' + aData[3] + '" onkeyup="desc(this);" >';
            jqTds[4].innerHTML = '<input type="text" class="form-control input-small integers qty_txt" value="' + aData[4] + '" onkeyup="qty(this);" onblur="qty_dis(this);">';
            jqTds[5].innerHTML = '<input type="text" class="form-control input-small integers rate_txt" value="' + aData[5] + '" onkeyup="rate(this);" onblur="rate_dis(this);">';
            jqTds[6].innerHTML = '<input type="text" class="form-control input-small integers amount_txt" value="' + aData[6] + '" onkeyup="amount(this);" onblur="amount_dis(this);">';
            jqTds[7].innerHTML = '<select class="form-control jselect2me"  onchange="set_tax_val(this);"><option value="0">Non-Taxable Sales</option><option value="1">Taxable Sales</option></select>';
            jqTds[8].innerHTML = '<a class="cancel" href="">Cancel</a>';

            $.each($("#" + tr_id + " > td.txt_td"), function (i, val) {
                var select_cnt = $(this).find(".select2-container").length;
                if (select_cnt >= 2) {
                    $(this).find(".select2-container:first").remove();
                }
            });

//            var select_cnt = $("#" + tr_id).find("td > .select2-container").length;
//            if (select_cnt >= 2) {
//                $("#" + tr_id).find("td > .select2-container:first").remove();
//            }
            $("#" + tr_id).children('td').eq('2').children("select").select2('data', {id: selected_option, text: selected_option_text});
            //if (qty == "" && rate == "" && amount == "") {
            $("#" + tr_id).children('td').eq('2').children("select").val(selected_option).trigger('change');
            //}
            $("#" + tr_id).children('td').eq('3').children("input").val(description);
            $("#" + tr_id).children('td').eq('4').children("input").val(qty);
            $("#" + tr_id).children('td').eq('5').children("input").val(rate);
            $("#" + tr_id).children('td').eq('6').children("input").val(amount);
            $("#" + tr_id).children('td').eq('7').children("select").select2('data', {id: selected_tax_option, text: selected_tax_text});
            $("#" + tr_id).children('td').eq('7').children("select").val(selected_tax_option).trigger('change');
        }

        function saveRow(oTable, nRow, tr_index) {
            var jqInputs = $('input[type="text"]', nRow);
            var acc_name = $("#selected_products_" + tr_index).val();
            var sales_tax_text = $("#tax_text_" + tr_index).val();
            oTable.fnUpdate(acc_name.value, nRow, 2, false);
            oTable.fnUpdate(jqInputs[1].value, nRow, 3, false);
            oTable.fnUpdate(jqInputs[2].value, nRow, 4, false);
            oTable.fnUpdate(jqInputs[3].value, nRow, 5, false);
            oTable.fnUpdate(jqInputs[4].value, nRow, 6, false);
            oTable.fnUpdate(sales_tax_text.value, nRow, 7, false);

            oTable.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 9, false);
            oTable.fnUpdate('<a class="delete" href="">Delete</a>', nRow, 10, false);
            oTable.fnDraw();
            calc_total1();
        }

        function cancelEditRow(oTable, nRow) {
            var jqInputs = $('input', nRow);
            oTable.fnUpdate(jqInputs[0].value, nRow, 0, false);
            oTable.fnUpdate(jqInputs[1].value, nRow, 1, false);
            oTable.fnUpdate(jqInputs[2].value, nRow, 2, false);
            oTable.fnUpdate(jqInputs[3].value, nRow, 3, false);
            oTable.fnUpdate(jqInputs[4].value, nRow, 4, false);
            oTable.fnUpdate(jqInputs[5].value, nRow, 5, false);
            oTable.fnUpdate(jqInputs[6].value, nRow, 6, false);
            oTable.fnUpdate(jqInputs[7].value, nRow, 7, false);
            oTable.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 8, false);
            oTable.fnDraw();
        }

        var table = $('#sample_editable_1');

        oTable = table.dataTable({
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

        var tableWrapper = $("#sample_editable_1_wrapper");

        var nEditing = null;
        var nNew = false;

        $('#sample_editable_1_new').click(function (e) {
            e.preventDefault();

            if (nNew && nEditing) {
                if (confirm("Previose row not saved. Do you want to save it ?")) {
                    saveRow(oTable, nEditing, ''); // save
                    $(nEditing).find("td:first").html("Untitled");
                    nEditing = null;
                    nNew = false;

                } else {
                    oTable.fnDeleteRow(nEditing); // cancel
                    nEditing = null;
                    nNew = false;

                    return;
                }
            }

            var aiNew = oTable.fnAddData(['', '', '', '', '', '']);
            var nRow = oTable.fnGetNodes(aiNew[0]);
            editRow(oTable, nRow, '');
            nEditing = nRow;
            nNew = true;
        });

        table.on('click', '.delete', function (e) {
            e.preventDefault();
            var row_index = $(this).parents("tr").index();
            if (confirm("Are you sure to delete this row ?") == false) {

                return;
            }

            var nRow = $(this).parents('tr')[0];
//            calc_totbalance(row_index);
            oTable.fnDeleteRow(nRow);
            $('#sample_editable_1 tbody tr').each(function (idx) {
                $(this).children("td:eq(1)").html(idx + 1);
            });
            calc_total1();
//            alert("Deleted! Do not forget to do some ajax to sync with backend :)");
        });

        table.on('click', '.cancel', function (e) {

            console.log("s5");
            var row_index = $(this).parents("tr").index();
            $("#product_guids_" + row_index).val("");
            $("#selected_products_" + row_index).val("");
            $("#tax_" + row_index).val("");
            $("#tax_text_" + row_index).val("");
            $("#description_" + row_index).val("");
            $("#qty_" + row_index).val("");
            $("#rate_" + row_index).val("");
            $("#amount_" + row_index).val("");
            calc_totbalance(row_index);
            e.preventDefault();

            if (nNew) {
                oTable.fnDeleteRow(nEditing);
                nEditing = null;
                nNew = false;
            } else {
                restoreRow(oTable, nEditing);
                nEditing = null;
            }
        });

//        table.on('click', '.edit', function (e) {
//        table.on('click', '.txt_td:not(.fill_tr),.save_td', function (e) {
        table.on('click', '.txt_td,.save_td', function (e) {
            console.log("come_3_1");
            var event_target = e.target;
            if (event_target == "[object HTMLInputElement]" || event_target == "[object HTMLSelectElement]" || event_target == "[object HTMLOptionElement]") {
                return false;
            }


//        table.on('click', '.txt_td,.save_td', function (e) {

            var tr_id = $(this).closest('tr').attr('id');
            var id_arr = tr_id.split("_");
            var tr_index = id_arr[1];
            calc_total1(tr_index);
            e.preventDefault();
            nNew = false;
            /* Get the row as a parent of the link that was clicked on */
            var nRow = $(this).parents('tr')[0];

            if (nEditing !== null && nEditing != nRow) {
                console.log("Come here2");
                $('#' + tr_id + ' > td').addClass("fill_tr");
                /* Currently editing - but not this row - restore the old before continuing to edit mode */

                restoreRow(oTable, nEditing);
                editRow(oTable, nRow, tr_index);
                save_data(nEditing);
                nEditing = nRow;
            } else if (nEditing == nRow && $(this).children("a").html() == "Save") {
                console.log("Come here1");
                $('#qty_' + tr_index).val($('#' + tr_id).find(".qty_txt  > input").val());
                $('#rate_' + tr_index).val($('#' + tr_id).find(".rate_txt  > input").val());
                $('#amount_' + tr_index).val($('#' + tr_id).find(".amount_txt  > input").val());
                /* Editing this row and want to save it */
                saveRow(oTable, nEditing, tr_index);
                $('#' + tr_id + ' > td').addClass("fill_tr");
                nEditing = null;
//                alert("Updated! Do not forget to do some ajax to sync with backend :)");
            } else {
                if (!$('.fill_tr > .product_cls').length) {
                    console.log("Come here");
                    $('#' + tr_id + ' > td').addClass("fill_tr");
                    /* No edit in progress - let's start one */
                    editRow(oTable, nRow, tr_index);
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
TableDatatablesEditable.init();
//});
function save_data(nEditing) {
    $('#sample_editable_1 tbody tr').each(function (idx) {
        $(this).children("td:eq(1)").html(idx + 1);
    });
    var id = $(nEditing).attr("id");
    if (id) {
        var id_arr = id.split("_");
        var id_index = id_arr[1];
        var pro_name = $("#selected_products_" + id_index).val();
        var sales_tax = $("#tax_text_" + id_index).val();
        var desc = $("#description_" + id_index).val();
        var qty = $("#qty_" + id_index).val();
        var rate = $("#rate_" + id_index).val();
        var amount = $("#amount_" + id_index).val();
        $("#" + id).children('td').eq('2').html(pro_name);
        $("#" + id).children('td').eq('3').html(desc);
        $("#" + id).children('td').eq('4').html(qty);
        $("#" + id).children('td').eq('5').html(rate);
        $("#" + id).children('td').eq('6').html(amount);

        if (pro_name == "" && desc == "" && qty == "" && rate == "" && amount == "") {
            sales_tax = "";
        }
        $("#" + id).children('td').eq('7').html(sales_tax);
        calc_total1(id_index);
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
        $("#old_qty_" + id_index).val(val1);
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
            if (!isNaN(tot_amount)) {
                $("#amount_" + id_index).val(tot_amount);
                $("#" + index).children('td').eq('6').children("input").val(tot_amount);
            }

        }
    }
//    calc_total(index, 'rate');
}
function amount_dis(element) {
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
            if (!isNaN(rate)) {
                $("#rate_" + id_index).val(rate);
            }
        }
    }
//    calc_total(index, 'rate');
}
$('body').click(function (evt) {
    var value = $("#event_type").val();
    if (value == "invoice") {
        if (evt.target.id == "sample_editable_1")
            return false;
//       //For descendants of menu_content being clicked, remove this check if you do not want to put constraint on descendants.
        if ($(evt.target).closest('#sample_editable_1').length)
            return false;

        var Row = $(".desc_txt").parents("tr")[0];
        save_data(Row);
    }

});
function add_lines(element) {
    var cnt = 8;

    for (var i = 0; i < cnt; i++) {
        var last_index = $('#sample_editable_1 tbody tr:last').index();
        var new_index = $('#sample_editable_1 tbody tr:last').index() + 1;
        var new_index1 = $('#sample_editable_1 tbody tr:last').index() + 2;
        var table = $("#sample_editable_1").DataTable();

        var append_html = '<input type="hidden" name="product_guid[]" id="product_guids_' + new_index + '" value=""/>';
        append_html += '<input type="hidden" name="selected_product[]" id="selected_products_' + new_index + '"/>';
        append_html += '<input type="hidden" name="description[]" id="description_' + new_index + '"/>';
        append_html += '<input type="hidden" name="old_qty[]" id="old_qty_' + new_index + '"/>';
        append_html += '<input type="hidden" name="qty[]" id="qty_' + new_index + '"/>';
        append_html += '<input type="hidden" name="rate[]" id="rate_' + new_index + '"/>';
        append_html += '<input type="hidden" name="amount[]" id="amount_' + new_index + '"/>';
        append_html += '<input type="hidden" name="i_taxable[]" id="tax_' + new_index + '" value="0"/>';
        append_html += '<input type="hidden" name="i_taxable_text[]" id="tax_text_' + new_index + '" value="Non-Taxable Sales"/>';

        //td html
        append_html += '<td class="drag_td"><center><i class="fa fa-th"></i></center></td>';
        append_html += '<td>' + new_index1 + '</td>';
        append_html += '<td class="txt_td"></td>';
        append_html += '<td class="txt_td"></td>';
        append_html += '<td class="qty_txt txt_td"></td>';
        append_html += '<td class="rate_txt txt_td"></td>';
        append_html += '<td class="amount_txt center txt_td"></td>';
        append_html += '<td class="txt_td"></td>';
        append_html += '<td><a class="delete" href="javascript:;">Delete </a></td>';
        table.row.add($('<tr id ="tr_' + new_index + '">' + append_html + '</tr>')[0]).draw();
    }

}
//function remove_lines(element) {
//    var cnt = $("#no_rows").val();
//    var total_tr = $("#sample_editable_1 tbody > tr").length;
//    if (total_tr > 8) {
//        for (i = 0; i < cnt; i++) {
//            $('#sample_editable_1 tbody > tr:last').remove();
//        }
//    } else {
//        location.reload();
//    }
//}
function remove_lines(element) {
    var total_tr = $("#sample_editable_1 tbody > tr").length;
    if (total_tr >= 0) {
        sum_amount = 0;
        for (var i = 0; i <= total_tr; i++) {
            var product_guids = $("#product_guids_" + i).val();
            var pro_description = $("#pro_description_" + i).val();
            var qty = $("#qty_" + i).val();
            var rate = $("#rate_" + i).val();
            var pro_amount = $("#pro_amount_" + i).val();
            var sales_tax = $("#tax_text_" + i).val();
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
            if (typeof sales_tax == "undefined") {
                sales_tax = "";
            }
            var table = $("#sample_editable_1").DataTable();
            if (product_guids == "" && pro_description == "" && qty == "" && rate == "" && pro_amount == "") {
                sum_amount += amount;
                if (i == 0) {
                    $("#tr_0 td").not(".drag_td").html("");
                    $("#tr_0 input:hidden").html("");
                } else {
                    table.row('#tr_' + i).remove().draw(false);
                }
            }
        }
        var total_bal = parseFloat($("#total").html().replace(/,/g, ''));
        var final_total = parseFloat(total_bal - sum_amount);
        if (!isNaN(final_total)) {
            var tax_amt = $("#tax_amount").val();
            if (tax_amt > 0) {
                final_total = parseFloat(tax_amt) + parseFloat(final_total);
            }
            $("#total").html(company_currency + final_total.toFixed(2));
        }
    } else {
        location.reload();
    }
}
function set_tax_val(element) {
    var value = $(element).val();
    var element_select_text = $(element).find('option:selected').text();
    var id = $(element).parents("tr").attr("id");
    var id_arr = id.split("_");
    var element_index = id_arr[1];
    $("#tax_" + element_index).val(value);
    $("#tax_text_" + element_index).val(element_select_text);
    calc_tax();
}
function calc_tax() {
    var values = [];
    $('input[name="amount[]"]').each(function () {
        var amount_id = $(this).attr("id");
        var amount_id_arr = amount_id.split("_");
        var amount_id_index = amount_id_arr[1];
        var is_tax_apply = $("#tax_" + amount_id_index).val();
        if (is_tax_apply == 1) {
            values.push($(this).val());
        }

    });
    if (values.length > 0) {
    }
    var total = 0;
    for (var i = 0; i < values.length; i++) {
        total += values[i] << 0;
    }
    tax_rate = $("#tax_rate").val();
    if (tax_rate > 0) {
        var total_tax_amount = (total * tax_rate) / 100;
        $(".tax_amount").html(parseFloat(total_tax_amount).toFixed(2));
        $("#tax_amount").val(total_tax_amount);
        final_total = parseFloat(total_tax_amount) + total;

        $("#total").html(company_currency + final_total.toFixed(2));
        var total_amt = parseFloat($("#total_amount").val());
        var balance_due_amt = parseFloat($("#total_balance_due").val());
        var total_diff = total - total_amt;
        var total_due = balance_due_amt + total_diff + parseFloat(total_tax_amount);
        $("#balance_due, #disp_total").html(company_currency + total_due.toFixed(2));

    }

}