// var table = $('#sample_editable_1');
//table.destroy();
//$("#sample_editable_1").dataTable().fnDestroy();

//var table = $('#bundle_editable_1');
var TableDatatablesEditable2 = function () {

    var handleTable = function () {


        function restoreRow(oTable2, nRow) {
            var aData = oTable2.fnGetData(nRow);
            var jqTds = $('>td', nRow);

            for (var i = 0, iLen = jqTds.length; i < iLen; i++) {
                oTable2.fnUpdate(aData[i], nRow, i, false);
            }

            oTable2.fnDraw();
        }

        function editRow(oTable2, nRow, tr_index) {
            var selected_option = $("#bundle_product_guid_" + tr_index).val();
            var selected_option_text = $("#bundle_selected_product_" + tr_index).val();
            var qty = $("#bundle_qty_" + tr_index).val();
            var aData = oTable2.fnGetData(nRow);
            var jqTds = $('>td', nRow);
            var tr_id = jqTds.parents("tr").attr("id");
            var tree_dropdown = '';
            if ($("#bundle_product_selection").length) {
                tree_dropdown = $("#bundle_product_selection").html();

            }
            jqTds[2].innerHTML = tree_dropdown;
            jqTds[3].innerHTML = '<input type="text" class="form-control input-small integer bundle_qty_txt input_bundle_qty" value="' + aData[3] + '" onkeyup="bundle_qty(this);" onblur="bundle_qty_dis(this);">';
            jqTds[4].innerHTML = '<a class="cancel" href="">Cancel</a>';
            var select_cnt = $("#" + tr_id).find("td > .select2-container").length;
            if (select_cnt >= 2) {
                $("#" + tr_id).find("td > .select2-container:first").remove();
            }
  
            $("#" + tr_id).children('td').eq('2').children("select").select2('data', {id: selected_option, text: selected_option_text});
            $("#" + tr_id).children('td').eq('2').children("select").val(selected_option_text).trigger('change');
            $("#" + tr_id).children('td').eq('3').children("input").val(qty);
        }

        function saveRow(oTable2, nRow, tr_index) {
            var jqInputs = $('input[type="text"]', nRow);
            var acc_name = $("#bundle_selected_product_" + tr_index).val();
            
            oTable2.fnUpdate(acc_name.value, nRow, 2, false);
            oTable2.fnUpdate(jqInputs[1].value, nRow, 3, false);
            oTable2.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 4, false);
            oTable2.fnUpdate('<a class="delete" href="">Delete</a>', nRow, 5, false);
            oTable2.fnDraw();
            calc_total1();
        }

        function cancelEditRow(oTable2, nRow) {
            var jqInputs = $('input', nRow);
            oTable2.fnUpdate(jqInputs[0].value, nRow, 0, false);
            oTable2.fnUpdate(jqInputs[1].value, nRow, 1, false);
            oTable2.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 2, false);
            oTable2.fnDraw();
        }

        var table = $('#bundle_editable_1');

        oTable2 = table.dataTable({
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

//        var tableWrapper = $("#bundle_editable_1_wrapper");

        var nEditing = null;
        var nNew = false;

//        $('#bundle_editable_1_new').click(function (e) {
//            e.preventDefault();
//
//            if (nNew && nEditing) {
//                if (confirm("Previose row not saved. Do you want to save it ?")) {
//                    saveRow(oTable2, nEditing, ''); // save
//                    $(nEditing).find("td:first").html("Untitled");
//                    nEditing = null;
//                    nNew = false;
//
//                } else {
//                    oTable2.fnDeleteRow(nEditing); // cancel
//                    nEditing = null;
//                    nNew = false;
//
//                    return;
//                }
//            }
//
//            var aiNew = oTable2.fnAddData(['', '', '', '', '', '']);
//            var nRow = oTable2.fnGetNodes(aiNew[0]);
//            editRow(oTable2, nRow, '');
//            nEditing = nRow;
//            nNew = true;
//        });

        table.on('click', '.delete', function (e) {
            e.preventDefault();
            var row_index = $(this).parents("tr").index();
            if (confirm("Are you sure to delete this row ?") == false) {

                return;
            }

            var nRow = $(this).parents('tr')[0];
//            calc_totbalance(row_index);
            oTable2.fnDeleteRow(nRow);
            $('#bundle_editable_1 tbody tr').each(function (idx) {
                $(this).children("td:eq(1)").html(idx + 1);
            });

            calc_total1();
//            alert("Deleted! Do not forget to do some ajax to sync with backend :)");
        });

        table.on('click', '.cancel', function (e) {
            var row_index = $(this).parents("tr").index();
            $("#bundle_product_guid_" + row_index).val("");
            $("#bundle_selected_product_" + row_index).val("");
            $("#bundle_qty_" + row_index).val("");
            e.preventDefault();

            if (nNew) {
                oTable2.fnDeleteRow(nEditing);
                nEditing = null;
                nNew = false;
            } else {
                restoreRow(oTable2, nEditing);
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

            e.preventDefault();
            nNew = false;
            /* Get the row as a parent of the link that was clicked on */
            var nRow = $(this).parents('tr')[0];

            if (nEditing !== null && nEditing != nRow) {
                $('#' + tr_id + ' > td').addClass("fill_tr");
                /* Currently editing - but not this row - restore the old before continuing to edit mode */

                restoreRow(oTable2, nEditing);
                editRow(oTable2, nRow, tr_index);
                bundle_save_data(nEditing);
                nEditing = nRow;
            } else if (nEditing == nRow && $(this).children("a").html() == "Save") {

                $('#bundle_qty_' + tr_index).val($('#' + tr_id).find(".bundle_qty_txt  > input").val());
                /* Editing this row and want to save it */
                saveRow(oTable2, nEditing, tr_index);
                $('#' + tr_id + ' > td').addClass("fill_tr");
                nEditing = null;
//                alert("Updated! Do not forget to do some ajax to sync with backend :)");
            } else {
                if(!$('.fill_tr > .product_cls').length) {
                    $('#' + tr_id + ' > td').addClass("fill_tr");
                    /* No edit in progress - let's start one */
                    editRow(oTable2, nRow, tr_index);
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
    TableDatatablesEditable2.init();
});
function bundle_save_data(nEditing) {

    $('#bundle_editable_1 tbody tr').each(function (idx) {

        $(this).children("td:eq(1)").html(idx + 1);
    });
    var id = $(nEditing).attr("id");
    if (id) {
        var id_arr = id.split("_");
        var id_index = id_arr[1];
        var pro_name = $("#bundle_selected_product_" + id_index).val();
        var qty = $("#bundle_qty_" + id_index).val();
        $("#" + id).children('td').eq('2').html(pro_name);
        $("#" + id).children('td').eq('3').html(qty);
    }

}
$('#add_product #frm_bundle').click(function (evt) {
    if (evt.target.id == "bundle_editable_1")
        return false;
//       //For descendants of menu_content being clicked, remove this check if you do not want to put constraint on descendants.
    if ($(evt.target).closest('#bundle_editable_1').length)
        return false;
        var Row = $(".input_bundle_qty").parents("tr")[0];
    bundle_save_data(Row);

});
function bundle_qty(element) {
    var index = $(element).closest("tr").attr("id");
    var id_arr = index.split("_");
    var id_index = id_arr[1];
    var qty = $(element).val();
    if (qty != '') {
        if($("#bundle_product_guid_" + id_index).val() != ""){
            $("#bundle_qty_" + id_index).val(qty);
        }
        
    }
}
function bundle_qty_dis(element) {
    var index = $(element).closest("tr").attr("id");
    var id_arr = index.split("_");
    var id_index = id_arr[1];
    var qty_val = $(element).val();
    if (qty_val != '') {
        var val1 = parseFloat(qty_val).toFixed(2);
        if($("#bundle_product_guid_" + id_index).val() != ""){
            $("#bundle_qty_" + id_index).val(val1);
        }
        

    }
//    calc_total(index, 'qty');
}
function bundle_add_lines() {
    var cnt = 1;
    for (var i = 0; i < cnt; i++) {
        var new_index = $('#bundle_editable_1 tbody tr:last').index() + 1;
        var new_index1 = $('#bundle_editable_1 tbody tr:last').index() + 2;
        var table = $("#bundle_editable_1").DataTable();
        
        var append_HTML = '<input type="hidden" name="bundle_product_guid[]" id="bundle_product_guid_'+new_index+'" value=""/>';
        append_HTML +=  '<input type="hidden" name="bundle_selected_product[]" id="bundle_selected_product_'+new_index+'"/>';
        append_HTML +=  '<input type="hidden" name="bundle_qty[]" id="bundle_qty_'+new_index+'" value="1.00"/>';
                       
        //td html  
        append_HTML += '<td class="drag_td"><center><i class="fa fa-th"></i></center></td>';
        append_HTML += '<td>' + new_index1 + '</td>';
        append_HTML += '<td class="txt_td"></td>';
        append_HTML += '<td class="bundle_qty_txt txt_td"></td>';
        append_HTML += '<td><a class="delete" href="javascript:;">Delete </a></td>';
        table.row.add($('<tr id ="btr_' + new_index + '">'+append_HTML+'</tr>')[0]).draw();
    }
}