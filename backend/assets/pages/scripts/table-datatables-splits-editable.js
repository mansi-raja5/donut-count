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

            var total_row = $("#sample_editable_1 tbody tr").length - 1;
            var selected_splits_guid = $("#splits_guid_" + tr_index).val();
            var selected_option = $("#parent_guid_" + tr_index).val();
            var selected_option_text = $("#selected_accname_" + tr_index).val();
            var check_no = $("#num_check_" + tr_index).val();
            var debit = $("#debit_" + tr_index).val();
            var credit = $("#credit_" + tr_index).val();
            var desc = $("#description_" + tr_index).val();
            var aData = oTable.fnGetData(nRow);
            var jqTds = $('>td', nRow);
            var tr_id = jqTds.parents("tr").attr("id");
            var tree_dropdown = '';
            if ($("#account_selection").length) {
                tree_dropdown = $("#account_selection").html();
            }
            jqTds[1].innerHTML = tree_dropdown;
            jqTds[2].innerHTML = check_no;
            jqTds[3].innerHTML = '<input type="text" class="form-control input-small integers debit_txt" value="' + aData[4] + '" onkeyup="debit(this);" onblur="debit_dis(this);">';
            jqTds[4].innerHTML = '<input type="text" class="form-control input-small integers credit_txt" value="' + aData[5] + '" onkeyup="credit(this);" onblur="credit_dis(this);">';
            jqTds[5].innerHTML = '<input type="text" class="form-control desc_txt" value="' + aData[6] + '" onkeyup="desc(this);">';
//            if (total_row == tr_index) {
            jqTds[6].innerHTML = '<button type="button" id="btn_addlines" class="btn btn-sm btn-success addlines" onclick="add_lines(this);" data-id="' + selected_splits_guid + '">Save</button>';
//            }

//            jqTds[5].innerHTML = '<a class="cancel" href="">Cancel</a>';

            var select_cnt = $("#" + tr_id).find("td > .select2-container").length;
            if (select_cnt >= 2) {
                $("#" + tr_id).find("td > .select2-container:first").remove();
            }
//            console.log(selected_option);
//            console.log(selected_option_text);
//            console.log(tr_id);
            $("#" + tr_id).children('td').eq('1').children("select").select2('data', {id: selected_option, text: selected_option_text});
            $("#" + tr_id).children('td').eq('1').children("select").val(selected_option).trigger('change');
            $("#" + tr_id).children('td').eq('2').children("input").val(check_no);
            $("#" + tr_id).children('td').eq('3').children("input").val(debit);
            $("#" + tr_id).children('td').eq('4').children("input").val(credit);
            $("#" + tr_id).children('td').eq('5').children("input").val(desc);
        }

        function saveRow(oTable, nRow, tr_index) {
            var jqInputs = $('input[type="text"]', nRow);
            var acc_name = $("#selected_accname_" + tr_index).val();
            var num_check_ = $("#num_check_" + tr_index).val();
            oTable.fnUpdate(acc_name.value, nRow, 1, false);
            oTable.fnUpdate(num_check_.value, nRow, 2, false);
            oTable.fnUpdate(jqInputs[1].value, nRow, 3, false);
            oTable.fnUpdate(jqInputs[2].value, nRow, 4, false);
            oTable.fnUpdate(jqInputs[3].value, nRow, 5, false);

            oTable.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 6, false);
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
            calc_totbalance(row_index);
            oTable.fnDeleteRow(nRow);
            $('#sample_editable_1 tbody tr').each(function (idx) {
                $(this).children("td:eq(1)").html(idx + 1);
            });

            calc_total1();
//            alert("Deleted! Do not forget to do some ajax to sync with backend :)");
        });
        table.on('click', '.cancel', function (e) {
            var row_index = $(this).parents("tr").index();
            $("#parent_guid_" + row_index).val("");
            $("#selected_accname_" + row_index).val("");
            $("#debit_" + row_index).val("");
            $("#credit_" + row_index).val("");
            $("#description_" + row_index).val("");
//            calc_totbalance(row_index);
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


        table.on('click', '.txt_td,.save_td', function (e) {
            $("input[name='debit[]']").each(function () {
                var value1 = $(this).val();
                var val1 = parseFloat(value1);
                var val2 = val1.toFixed(2);

                if (!isNaN(val2)) {
                    var myval = parseFloat(val2).toFixed(2);
                    $(this).val(myval);
                }
            });
            $("input[name='credit[]']").each(function () {
                var value1 = $(this).val();
                var val1 = parseFloat(value1);
                var val2 = val1.toFixed(2);

                if (!isNaN(val2)) {
                    var myval = parseFloat(val2).toFixed(2);
                    $(this).val(myval);
                }
            });
            var event_target = e.target;
            if (event_target == "[object HTMLInputElement]" || event_target == "[object HTMLSelectElement]" || event_target == "[object HTMLOptionElement]") {
                return false;
            }

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

                $('#debit_' + tr_index).val($('#' + tr_id).find(".debit_txt  > input").val());
                $('#credit_' + tr_index).val($('#' + tr_id).find(".credit_txt  > input").val());
                /* Editing this row and want to save it */
                saveRow(oTable, nEditing, tr_index);
                $('#' + tr_id + ' > td').addClass("fill_tr");
                nEditing = null;
//                alert("Updated! Do not forget to do some ajax to sync with backend :)");
            } else {
                if(!$('.fill_tr > .account_cls').length) {
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

jQuery(document).ready(function () {
    TableDatatablesEditable.init();
    add_lines(1);
});
function add_lines(element) {
    if (typeof $(element).attr("data-id") != 'undefined' && $(element).attr("data-id") != '') {
        $(element).remove();
        var Row = $(element).parents("tr")[0];
        save_data(Row);
    } else {
        var new_index = $('#sample_editable_1 tbody tr:last').index() + 1;
        var new_index1 = $('#sample_editable_1 tbody tr:last').index() + 2;
        var new_index2 = $('#sample_editable_1 tbody tr:last').index();
        var table = $("#sample_editable_1").DataTable();
        if (($("#credit_" + new_index2).val() != '' || $("#debit_" + new_index2).val() != '') && $("#parent_guid_" + new_index2).val() != '') {
            $("#tr_" + new_index2).find("div.account_cls, td:eq(1)").removeClass("split_error");
            $("#tr_" + new_index2).find("input.debit_txt, input.credit_txt, td:eq(2), td:eq(3)").removeClass("split_error");
            var append_HTML = '<input type="hidden" name="parent_guid[]" id="parent_guid_' + new_index + '" value=""/>';
            append_HTML += '<input type="hidden" name="splits_guid[]" id="splits_guid_' + new_index + '" value=""/>';
            append_HTML += '<input type="hidden" name="selected_accname[]" id="selected_accname_' + new_index + '"/>';
            append_HTML += '<input type="hidden" name="num_check[]" id="num_check_' + new_index + '"/>';
            append_HTML += '<input type="hidden" name="debit[]" id="debit_' + new_index + '"/>';
            append_HTML += '<input type="hidden" name="credit[]" id="credit_' + new_index + '"/>';
            append_HTML += '<input type="hidden" name="description[]" id="description_' + new_index + '"/>';

            //td html
            append_HTML += '<td>' + new_index1 + '</td>';
            append_HTML += ' <td class="txt_td"></td>';
            append_HTML += ' <td class="txt_td"></td>';
            append_HTML += ' <td class="debit_txt txt_td"></td>';
            append_HTML += ' <td class="credit_txt txt_td"></td>';
            append_HTML += '<td class="center txt_td"></td>';
            append_HTML += '<td class="center"></td>';

            $("#sample_editable_1 tbody").sortable({
                items: "tr"
            });
            table.row.add($('<tr id ="tr_' + new_index + '">' + append_HTML + '</tr>')[0]).draw();
            var Row = $(".debit_txt").parents("tr")[0];
            $("#tr_" + new_index2).find(".addlines").remove();
            save_data(Row);

        } else {
            if ($("#parent_guid_" + new_index2).val() == "") {
                $("#tr_" + new_index2).find("div.account_cls").addClass("split_error");
            } 
            if ($("#credit_" + new_index2).val() == '' || $("#debit_" + new_index2).val() == '') {
                $("#tr_" + new_index2).find("input.debit_txt").addClass("split_error");
                $("#tr_" + new_index2).find("input.credit_txt").addClass("split_error");
            }
        }
    }
}

function save_data(nEditing) {
    $('#sample_editable_1 tbody tr').each(function (idx) {
        $(this).children("td:eq(0)").html(idx + 1);
    });
    var id = $(nEditing).attr("id");
    if (id) {
        var id_arr = id.split("_");
        var id_index = id_arr[1];
        var acc_name = $("#selected_accname_" + id_index).val();
        var num_check = $("#num_check_" + id_index).val();
        var debit = $("#debit_" + id_index).val();
        var credit = $("#credit_" + id_index).val();
        var desc = $("#description_" + id_index).val();

        $("#" + id).children('td').eq('1').html(acc_name);
        $("#" + id).children('td').eq('2').html(num_check);
        $("#" + id).children('td').eq('3').html((debit != "") ? parseFloat(debit).toFixed(2) : "");
        $("#" + id).children('td').eq('4').html((credit != "") ? parseFloat(credit).toFixed(2) : "");

        $("#" + id).children('td').eq('5').html(desc);
        calc_total1(id_index);
    }

}
function credit_dis(element) {
    $(element).parent("td").removeClass("credit_td");
    var value = $(element).val();
    var fixed_value = parseFloat(value).toFixed(2);
    if (!isNaN(fixed_value)) {
        $(element).val(fixed_value);
    }
    var index = $(element).closest("tr").attr("id");
    var id_arr = index.split("_");
    var id_index = id_arr[1];
    if (fixed_value != '' && !isNaN(fixed_value)) {
        $("#" + index).children('td').eq('3').children("input").val('');
        $("#" + index).children('td').removeClass("debit_td");
        $("#debit_" + id_index).val('');
        var debit_cnt = $(".debit_td").length;
        credit_bal = 0;
        if (debit_cnt == 1) {
            $("td.credit_td").each(function () {
                var debit = $(this).html();
                credit_bal += parseFloat(debit);
            });
            var new_debit_val = (credit_bal + parseFloat(fixed_value)).toFixed(2);
            if (!isNaN(new_debit_val)) {
                $("td.debit_td").html(new_debit_val);
                var debit_id = $("td.debit_td").parents("tr").attr("id");
                var debit_id_arr = debit_id.split("_");
                var debit_id_index = debit_id_arr[1];
                $("#debit_" + debit_id_index).val(new_debit_val);
                $("#debit_total").html(new_debit_val);
            }
        }
    }
    $(element).parent("td").addClass("credit_td");
}
function debit_dis(element) {
    $(element).parent("td").removeClass("debit_td");
    var value = $(element).val();
    var fixed_value = parseFloat(value).toFixed(2);

    if (!isNaN(fixed_value)) {
        $(element).val(fixed_value);
    }
    var index = $(element).closest("tr").attr("id");
    var id_arr = index.split("_");
    var id_index = id_arr[1];
    if (fixed_value != '' && !isNaN(fixed_value)) {
        $("#" + index).children('td').eq('4').children("input").val('');
        $("#" + index).children('td').removeClass("credit_td");
        $("#credit_" + id_index).val('');
        var credit_cnt = $(".credit_td").length;
        debit_bal = 0;
        if (credit_cnt == 1) {
            $("td.debit_td").each(function () {
                var credit = $(this).html();
                debit_bal += parseFloat(credit);
            });
            var new_credit_val = (debit_bal + parseFloat(fixed_value)).toFixed(2);
            if (!isNaN(new_credit_val)) {
                $("td.credit_td").html(new_credit_val);
                var credit_id = $("td.credit_td").parents("tr").attr("id");
                var credit_id_arr = credit_id.split("_");
                var credit_id_index = credit_id_arr[1];
                $("#credit_" + credit_id_index).val(new_credit_val);
                $("#credit_total").html(new_credit_val);
            }
        }
    }
    $(element).parent("td").addClass("debit_td");
}
$('body').click(function (evt) {
    if (evt.target.id == "sample_editable_1")
        return false;
//       //For descendants of menu_content being clicked, remove this check if you do not want to put constraint on descendants.
    if ($(evt.target).closest('#sample_editable_1').length)
        return false;

    var Row = $(".desc_txt").parents("tr")[0];
    save_data(Row);
});