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
            var selected_option = $("#parent_guid_" + tr_index).val();
            var selected_option_text = $("#selected_accname_" + tr_index).val();
            var desc = $("#acc_description_" + tr_index).val();
            var amount = $("#amount_" + tr_index).val();
            var aData = oTable.fnGetData(nRow);
            var jqTds = $('>td', nRow);
            var tr_id = jqTds.parents("tr").attr("id");
            var tree_dropdown = '';
            var customer_dropdown = '';
            if ($("#account_selection").length) {
                tree_dropdown = $("#account_selection").html();
            }
            jqTds[2].innerHTML = tree_dropdown;
            jqTds[3].innerHTML = '<input type="text" class="form-control input-small desc_txt" value="' + aData[3] + '" onkeyup="acc_desc(this);">';
            jqTds[4].innerHTML = '<input type="text" id ="amount_txt" class="form-control input-small integers amount_txt" value="' + aData[4] + '" onkeyup="acc_amount(this);" onblur="amount_dis(this);">';


            jqTds[5].innerHTML = '<a class="acc_cancel" href="">Cancel</a>';
            $.each($("#" + tr_id + " > td.txt_td"), function (i, val) {
                var select_cnt = $(this).find(".select2-container").length;
                if (select_cnt >= 2) {
                    $(this).find(".select2-container:first").remove();
                }
            });
            $("#" + tr_id).children('td').eq('2').children("select").select2('data', {id: selected_option, text: selected_option_text});
            $("#" + tr_id).children('td').eq('2').children("select").val(selected_option).trigger('change');
            $("#" + tr_id).children('td').eq('3').children("input").val(desc);
            $("#" + tr_id).children('td').eq('4').children("input").val(amount);
        }

        function saveRow(oTable, nRow, tr_index) {
            var jqInputs = $('input[type="text"]', nRow);
            var acc_name = $("#selected_accname_" + tr_index).val();
            oTable.fnUpdate(acc_name.value, nRow, 2, false);
            oTable.fnUpdate(jqInputs[1].value, nRow, 3, false);
            oTable.fnUpdate(jqInputs[2].value, nRow, 4, false);
//            oTable.fnUpdate(jqInputs[4].value, nRow, 6, false);

            oTable.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 5, false);
            oTable.fnUpdate('<a class="delete" href="">Delete</a>', nRow, 6, false);
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
            oTable.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 6, false);
            oTable.fnDraw();
        }

        var table = $('#expense_editable_1');

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

        var tableWrapper = $("#expense_editable_1_wrapper");

        var nEditing = null;
        var nNew = false;

        $('#expense_editable_1_new').click(function (e) {
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

            var tot_row = $("#expense_editable_1 tbody > tr").length;
            if (tot_row > 1) {
                if (confirm("Are you sure to delete this row ?") == false) {

                    return;
                }
                var nRow = $(this).parents('tr')[0];
                calc_totbalance(row_index);
                oTable.fnDeleteRow(nRow);
                $('#expense_editable_1 tbody tr').each(function (idx) {
                    $(this).children("td:eq(1)").html(idx + 1);
                });

                calc_total1();

                if (tot_row == 2) {
                    $("#expense_editable_1 tbody > tr > td").find(".delete").html("Clear");
                }
            } else {
                $("#parent_guid_" + row_index).val("");
                $("#selected_accname_" + row_index).val("");
                $("#acc_description_" + row_index).val("");
                $("#amount_" + row_index).val("");
                $("#expense_editable_1 tbody > tr > td:eq(2)").html("");
                $("#expense_editable_1 tbody > tr > td:eq(3)").html("");
                $("#expense_editable_1 tbody > tr > td:eq(4)").html("");
                // $("#total").html("0.00");
                calc_totbalance(row_index);
                calc_total1();
                e.preventDefault();
            }
        });

        table.on('click', '.acc_cancel', function (e) {
            var nRow = $(this).parents('tr')[0];
            var row_index = $(this).parents("tr").index();
            $("#parent_guid_" + row_index).val("");
            $("#selected_accname_" + row_index).val("");
            $("#acc_description_" + row_index).val("");
            $("#amount_" + row_index).val("");
            calc_totbalance(row_index);
            e.preventDefault();
//             var nRow = $(this).parents('tr')[0];
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
                $('#' + tr_id + ' > td').addClass("fill_tr");
                /* Currently editing - but not this row - restore the old before continuing to edit mode */

                restoreRow(oTable, nEditing);
                editRow(oTable, nRow, tr_index);
                save_data(nEditing);
                nEditing = nRow;
            } else if (nEditing == nRow && $(this).children("a").html() == "Save") {
                $('#amount_' + tr_index).val($('#' + tr_id).find(".amount_txt  > input").val());
                /* Editing this row and want to save it */
                saveRow(oTable, nEditing, tr_index);
                $('#' + tr_id + ' > td').addClass("fill_tr");
                nEditing = null;
//                alert("Updated! Do not forget to do some ajax to sync with backend :)");
            } else {
                if (!$('.fill_tr > .account_cls').length) {
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
function add_lines(element) {
    var cnt = $("#no_rows").val();
    for (i = 0; i < cnt; i++) {
        var last_index = $('#expense_editable_1 tbody tr:last').index();
        var new_index = last_index + 1;
        var new_index1 = last_index + 2;
        var table = $("#expense_editable_1").DataTable();


        var append_HTML = '<input type="hidden" class="parent_guid" name="parent_guid[]" id="parent_guid_' + new_index + '" value=""/>';
        append_HTML += '<input type="hidden" class="selected_accname" name="selected_accname[]" id="selected_accname_' + new_index + '"/>';
        append_HTML += '<input type="hidden" name="splits_guid[]" id="splits_guid' + new_index + '" value=""/>';
        append_HTML += '<input type="hidden" class="acc_description" name="acc_description[]" id="acc_description_' + new_index + '"/>';
        append_HTML += '<input type="hidden" class="acc_amount" name="acc_amount[]" id="amount_' + new_index + '"/>';

        //td html
        append_HTML += '<td class="drag_td"><center><i class="fa fa-th"></i></center></td>';
        append_HTML += '<td>' + new_index1 + '</td>';
        append_HTML += ' <td class="txt_td"></td>';
        append_HTML += ' <td class="txt_td"></td>';
        append_HTML += '<td class="amount_txt center txt_td"></td>';
        append_HTML += '<td><a class="delete" href="javascript:;">Delete</a></td>';

        table.row.add($('<tr id ="tr_' + new_index + '">' + append_HTML + '</tr>')[0]).draw();
        $("#expense_editable_1 tbody").sortable({
            items: "tr"
        });
    }

}
function remove_lines(element) {
    var total_tr = $('#expense_editable_1 tbody tr:last').index();
    if (total_tr >= 0) {
        sum_amount = 0;
        for (var i = 0; i <= total_tr; i++) {
            var parent_guid = $("#parent_guid_" + i).val();
            var description = $("#acc_description_" + i).val();
            var amount = $("#amount_" + i).val();
            if (typeof parent_guid == "undefined") {
                parent_guid = "";
            }
            if (typeof description == "undefined") {
                description = "";
            }
            if (typeof amount == "undefined") {
                amount = "";
            }
            var table = $("#expense_editable_1").DataTable();
            if (parent_guid == "" && description == "" && amount == "") {
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
            $("#total").html(final_total.toFixed(2));
        }
    } else {
        location.reload();
    }
}
function save_data(nEditing) {
    if (nEditing != 'undefined') {
        $('#expense_editable_1 tbody tr').each(function (idx) {

            $(this).children("td:eq(1)").html(idx + 1);
        });
        var id = $(nEditing).attr("id");
        if (id) {
            var id_arr = id.split("_");
            var id_index = id_arr[1];
            // var acc_name = $("#selected_accname_" + id_index).val();
            var acc_name = $("#expense_editable_1 #selected_accname_" + id_index).val();
            var desc = $("#acc_description_" + id_index).val();
            var amount = $("#amount_" + id_index).val();

//            if ($("#expense_editable_1 #account").val() != "" || $("#expense_editable_1 #amount_txt").val() != "") {
//                var temp_total = 0;
//                if ($("#expense_editable_1 #account").val() == "") {
//                    $("#" + id).children('td').eq('2').find('.jselect2me > a').addClass('errorClass');
//                    temp_total++;
//                }
//                if ($("#expense_editable_1 #amount_txt").val() == "") {
//                    $("#" + id).children('td').eq('4').find('input').addClass('errorClass');
//                    temp_total++;
//                }
//                if (temp_total > 0) {
//                    return false;
//                }
//
//            }

            if (acc_name != "" || amount != "") {
                $("#" + id).children('td').eq('2').html(acc_name);
                $("#" + id).children('td').eq('3').html(desc);

                $("#" + id).children('td').eq('4').html(amount);

                //set description
                var description = $("#description").val();
                var is_update_desc = $("#is_update_desc").val();

                if (desc != '') {
                    if (is_update_desc == 0) {
                        if (description != '') {
                            desc1 = '';
                            for (var k = 0; k <= id_index; k++) {
                                desc1 += $.trim($.trim($("#tr_" + k).children('td').eq('3').html()) + " , ");
                            }
                            desc1 = desc1.replace(/,\s*$/, "");
                            $("#description").val(desc1);
                        } else {
                            $("#description").val(desc);
                        }
                    }
                }


                var temp_total = 0;
                if (acc_name == "") {
                    $("#" + id).children('td').eq('2').find('.jselect2me > a').addClass('errorClass');
                    temp_total++;
                }
                if (amount == "") {
                    $("#" + id).children('td').eq('4').find('input').addClass('errorClass');
                    temp_total++;
                }
                if (temp_total > 0) {
                    return false;
                }

            }


            calc_total1(id_index);
        }
    }
}
$('body').click(function (evt) {
    var value = $("#event_type").val();
    if (value == "expenses" || value == "bills" || value == "checks" || value == "vendor_credits") {
        if (evt.target.id == "expense_editable_1") {
            return false;
        } else if ($(evt.target).closest('#expense_editable_1').length) {
            return false;
        } else {
            var Row = $(".desc_txt").parents("tr")[0];
            save_data(Row);
        }
//       //For descendants of menu_content being clicked, remove this check if you do not want to put constraint on descendants.
        if (evt.target.id == "sample_editable_2") {
            return false;
        }
        else if ($(evt.target).closest('#sample_editable_2').length)
        {
            return false;
        } else {
            var Row = $(".pro_desc_txt").parents("tr")[0];
            item_save_data(Row);
        }
    }
});
function amount_dis(element) {
    console.log("come-2");
    var index = $(element).closest("tr").attr("id");
    var id_arr = index.split("_");
    var id_index = id_arr[1];
    var amount_val = $(element).val();
    if (amount_val != '' && !isNaN(amount_val)) {
        var val1 = parseFloat(amount_val).toFixed(2);
        $(element).val(val1);
        $("#amount_" + id_index).val(val1);
        var qty_val = $("#" + index).children('td').eq('4').children("input").val();
        var rate_val = $("#" + index).children('td').eq('5').children("input").val();
        var tot_amount = parseFloat(qty_val * rate_val).toFixed(2);
        if (val1 != tot_amount && qty_val != '') {
            var rate = parseFloat(val1 / qty_val).toFixed(2);
            $("#" + index).children('td').eq('5').children("input").val(rate);
//            $("#rate_" + id_index).val(rate);
        }
    } else {
        $("#amount_" + id_index).val("");
    }
//    calc_total(index, 'rate');
}