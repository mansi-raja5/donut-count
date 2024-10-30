$('body').on('DOMNodeInserted', 'select.jselect2me', function () {
    $(this).select2();
});
var tguid = $('#tguid').val();
var TableDatatablesEditable = function () {
    var handleTable = function () {

        function restoreRow(oTable, nRow) {
            var aData = oTable.fnGetData(nRow);
            var jqTds = $('>td', nRow);

            for (var i = 0, iLen = jqTds.length; i < iLen; i++) {
                oTable.fnUpdate(aData[i], nRow, i, false);
            }
            if (tguid != '') {
                $("#" + aData['DT_RowId']).children('td').eq('2').css('display', 'table-cell');
            }
            oTable.fnDraw();
        }

        function editRow(oTable, nRow, tr_index) {
            console.log("come_1");
            var selected_option = $("#parent_guid_" + tr_index).val();
            var selected_option_text = $("#selected_accname_" + tr_index).val();
            var customer_option = $("#customer_guid_" + tr_index).val();
            var selected_customer_text = $("#selected_customer_" + tr_index).val();
            var project_option = $("#project_guid_" + tr_index).val();
            var selected_project_text = $("#selected_project_" + tr_index).val();
            var debit = $("#debit_" + tr_index).val();
            var credit = $("#credit_" + tr_index).val();
            var desc = $("#description_" + tr_index).val();
            var name = $("#name_" + tr_index).val();
            var state = $("#state_" + tr_index).val();
            console.log(oTable);
            console.log(nRow);
            var aData = oTable.fnGetData(nRow);
            console.log(aData);

            var jqTds = $('>td', nRow);
            var tr_id = jqTds.parents("tr").attr("id");
            var tree_dropdown = '';
            var customer_dropdown = '';
            var project_dropdown = '';

            if ($("#account_selection").length) {
                tree_dropdown = $("#account_selection").html();
                if (tguid != '') {
                    tree_dropdown += '<p style="margin: 0px !important;"><i class="fa fa-magic magical-icon"></i></p>';
                }
            }
            if ($("#customer_selection").length) {
                customer_dropdown = $("#customer_selection").html();
            }
            if ($("#project_selection").length) {
                project_dropdown = $("#project_selection").html();
            }
            var debit_str = aData[3];
            var final_debit_str = debit_str.replace('<span class="pull-right"><b>DR</b></span>', '');
            var credit_str = aData[4];
            var final_credit_str = credit_str.replace('<span class="pull-right"><b>CR</b></span>', '');
            jqTds[2].innerHTML = tree_dropdown;
            jqTds[3].innerHTML = '<input type="text" class="form-control input-small integers debit_txt" value="' + final_debit_str + '" onkeyup="debit(this);" onblur="debit_dis(this);">';
            jqTds[4].innerHTML = '<input type="text" class="form-control input-small integers credit_txt" value="' + final_credit_str + '" onkeyup="credit(this);" onblur="credit_dis(this);">';
            jqTds[5].innerHTML = "<select class='form-control jselect2me' id='status_change' name='reconcile_state' onchange='change_state(this);' ><option value=''>-Select-</option><option value=n>N</option><option value=c>C</option><option value=y>R</option></select>";
            jqTds[6].innerHTML = '<input type="text" class="form-control input-small desc_txt" value="' + aData[5] + '" onkeyup="desc(this);">';
            var customer_cls = $("#" + tr_id).children('td').eq('6').attr("class");

            if (!$("#" + tr_id).children('td').eq('6').is(".readonly")) {
                jqTds[7].innerHTML = customer_dropdown;
            }
            jqTds[8].innerHTML = project_dropdown;
            jqTds[9].innerHTML = '<a class="cancel" href="">Cancel</a>';
            $.each($("#" + tr_id + " > td.txt_td"), function (i, val) {
                var select_cnt = $(this).find(".select2-container").length;
                if (select_cnt >= 2) {
                    $(this).find(".select2-container:first").remove();
                }
            });

            $("#" + tr_id).children('td').eq('2').children("select").select2('data', {id: selected_option, text: selected_option_text});
            $("#" + tr_id).children('td').eq('2').children("select").val(selected_option).trigger('change');

            if (tguid != '') {
                $("#" + tr_id).children('td').eq('2').css('display', 'flex');
                $("#" + tr_id).children('td').eq('2').children(".magical-icon").attr('data-acc-guid', selected_option);
                $("#" + tr_id).children('td').eq('2').children(".magical-icon").attr('data-tr-id', tr_id);
            }

            $("#" + tr_id).children('td').eq('3').children("input").val(debit);
            $("#" + tr_id).children('td').eq('4').children("input").val(credit);
            $("#" + tr_id).children('td').eq('5').children("select").select2('data', {id: state, text: state.toUpperCase()});
            $("#" + tr_id).children('td').eq('5').children("select").val(state).trigger('change');
            $("#" + tr_id).children('td').eq('6').children("input").val(desc);

            if (customer_option != '') {
                $("#" + tr_id).children('td').eq('7').children("select").select2('data', {id: customer_option, text: selected_customer_text});
                $("#" + tr_id).children('td').eq('7').children("select").val(customer_option).trigger('change');
            }

            if (project_option != '') {
                $("#" + tr_id).children('td').eq('8').children("select").select2('data', {id: project_option, text: selected_project_text});
                $("#" + tr_id).children('td').eq('8').children("select").val(project_option).trigger('change');
            }

            $('.magical-icon').on('click', function () {
                var account_guid = $("#parent_guid_" + tr_index).val();
                var tguid = $('#tguid').val();
                var account_guids = [];

                $('input[name="parent_guid[]"]').each(function () {
                    if ($(this).val() != '' && account_guid != $(this).val()) {
                        account_guids.push($(this).val());
                    }
                });
                var split_data = {
                    'split_description': $("#" + tr_id).children('td').eq('6').children("input").val(),
                    'name': $("#customer_guid_" + tr_index).val(),
                    'project': $("#project_guid_" + tr_index).val(),
                    'debits': $("#" + tr_id).children('td').eq('3').children("input").val() * 100,
                    'credits': $("#" + tr_id).children('td').eq('4').children("input").val() * 100,
                };

                var journal_desc = $('#txt-memo').val();
                var post_date = $('#journal_date').val();

                $.ajax({
                    type: 'POST',
                    url: App.getSiteURL() + "/accounts/get_guess_acc",
                    beforeSend: function () {
                        // $("#loadingmessage").show();
                        $('.magical-icon').addClass('fa-spinner fa-spin');
                        $('.magical-icon').removeClass('fa-magic');
                    },
                    data: {
                        account_guid: account_guid,
                        tguid: tguid,
                        split_data: split_data,
                        journal_desc: journal_desc,
                        post_date: post_date,
                        account_guids: account_guids
                    },
                    success: function (responseJSON) {
                        var response = JSON.parse(responseJSON);

                        if (response.status) {
                            $("#" + tr_id).children('td').eq('2').children("select").find('[data-type=magical-acc]').remove();
                            $("#" + tr_id).children('td').eq('2').children("select").prepend('<option class="divider_option" disabled data-type="magical-acc"></option>');
                            $.each(response.data.reverse(), function (i, v) {
                                $("#" + tr_id).children('td').eq('2').children("select").prepend("<option value='" + v.guid + "' data-type='magical-acc'>" + v.hierarchy_path + "</option>");
                            });
                            $("#" + tr_id).children('td').eq('2').children("select").select2('data', {id: response.data[response.data.length - 1].guid, text: response.data[response.data.length - 1].hierarchy_path});
                            $("#" + tr_id).children('td').eq('2').children("select").val(response.data[response.data.length - 1].guid).trigger('change');
                        }

                        // $("#loadingmessage").hide();
                        $('.magical-icon').removeClass('fa-spinner fa-spin');
                        $('.magical-icon').addClass('fa-magic');
                    }
                });
            });
        }

        function saveRow(oTable, nRow, tr_index) {
            console.log("come_2");
            var jqInputs = $('input[type="text"]', nRow);
            var acc_name = $("#selected_accname_" + tr_index).val();
            var customer_name = $("#selected_customer_" + tr_index).val();
            var project_name = $("#selected_project_" + tr_index).val();
            var state = $("#state_" + tr_index).val();

            oTable.fnUpdate(acc_name.value, nRow, 2, false);
            oTable.fnUpdate(jqInputs[1].value, nRow, 3, false);
            oTable.fnUpdate(jqInputs[2].value, nRow, 4, false);
            oTable.fnUpdate(state, nRow, 5, false);
            oTable.fnUpdate(jqInputs[3].value, nRow, 6, false);
//            oTable.fnUpdate(jqInputs[4].value, nRow, 6, false);
            oTable.fnUpdate(customer_name.value, nRow, 7, false);
            oTable.fnUpdate(project_name.value, nRow, 8, false);

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
            oTable.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 6, false);
            oTable.fnDraw();
        }

        var table = $('#journal_entry_editable');

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
            "columnDefs": [{
                    'orderable': false,
                    'targets': [0]
                }],
            "order": [
                [1, "asc"]
            ] // set first column as a default sort by asc
        });

        var tableWrapper = $("#journal_entry_editable_wrapper");

        var nEditing = null;
        var nNew = false;

        $('#journal_entry_editable_new').click(function (e) {
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


            var delete_debit = $("#debit_" + row_index).val();
            var delete_credit = $("#credit_" + row_index).val();

            var debit_total_fix = parseFloat($("#debit_total").html().replace(/,/g, '')).toFixed(2);
            var credit_total_fix = parseFloat($("#credit_total").html().replace(/,/g, '')).toFixed(2);

//           before delete check equilaity of credit and debit
            if (delete_debit != '') {
                delete_debit = parseFloat(delete_debit).toFixed(2);
                check_debit_total = debit_total_fix - delete_debit;
            } else {
                check_debit_total = debit_total_fix;
            }
            if (delete_credit != '') {
                delete_credit = parseFloat(delete_credit).toFixed(2);
                check_credit_total = credit_total_fix - delete_credit;
            } else {
                check_credit_total = credit_total_fix;
            }

            if (check_debit_total != check_credit_total) {
                $("#error_div").show();
                $("#error_div").children("p").html("The debits and credits columns MUST be equal. Please fix your entries, and try again.");
                if ($('#journalEntryModal').is(":visible")) {
                    $('.modal-body').animate({scrollTop: 0}, 'slow');
                } else {
                    $('html, body').animate({scrollTop: $('#journal_entry_editable').position().top}, 'slow');
                }

            } else {
//                var count = 0;
//                var no_of_deleted_splits = $('.deleted_splits').length;
//                if(no_of_deleted_splits > 0)
//                    count = parseInt(count) + parseInt(no_of_deleted_splits);
//                
//                var splits_guid = $("#splits_guid_" + row_index).val();
//                var parent_guid = $("#parent_guid_" + row_index).val();
//                var selected_accname = $("#selected_accname_" + row_index).val();
//                var customer_guid = $("#customer_guid_" + row_index).val();
//                var selected_customer = $("#selected_customer_" + row_index).val();
//                var debit = $("#debit_" + row_index).val();
//                var credit = $("#credit_" + row_index).val();
//                var description = $("#description_" + row_index).val();
//                var reconcile_date = $("#reconcile_date_" + row_index).val();
//                var name = $("#name_" + row_index).val();
//                var state = $("#state_" + row_index).val();
//                var project_guid = $("#project_guid_" + row_index).val();
//                var selected_project = $("#selected_project_" + row_index).val();
//                
//                var html = '<input type="hidden" name="deleted_split['+count+'][splits_guid]" value="'+splits_guid+'" class="deleted_splits"/>'+
//                            '<input type="hidden" name="deleted_split['+count+'][parent_guid]" value="'+parent_guid+'"/>'+
//                            '<input type="hidden" name="deleted_split['+count+'][selected_accname]" value="'+selected_accname+'"/>'+
//                            '<input type="hidden" name="deleted_split['+count+'][customer_guid]" value="'+customer_guid+'"/>'+
//                            '<input type="hidden" name="deleted_split['+count+'][selected_customer]" value="'+selected_customer+'"/>'+
//                            '<input type="hidden" name="deleted_split['+count+'][debit]" value="'+debit+'"/>'+
//                            '<input type="hidden" name="deleted_split['+count+'][credit]" value="'+credit+'"/>'+
//                            '<input type="hidden" name="deleted_split['+count+'][description]" value="'+description+'"/>'+
//                            '<input type="hidden" name="deleted_split['+count+'][reconcile_date]" value="'+reconcile_date+'"/>'+
//                            '<input type="hidden" name="deleted_split['+count+'][name]" value="'+name+'"/>'+
//                            '<input type="hidden" name="deleted_split['+count+'][state]" value="'+state+'"/>'+
//                            '<input type="hidden" name="deleted_split['+count+'][project_guid]" value="'+project_guid+'"/>'+
//                            '<input type="hidden" name="deleted_split['+count+'][selected_project]" value="'+selected_project+'"/>';
//                    
//                $('#frmAddJournal').append(html);
                oTable.fnDeleteRow(nRow);
                $('#journal_entry_editable tbody tr').each(function (idx) {
                    $(this).children("td:eq(1)").html(idx + 1);
                });

                calc_total1();
            }
//            alert("Deleted! Do not forget to do some ajax to sync with backend :)");
        });


        table.on('click', '.cancel', function (e) {
            var row_index = $(this).parents("tr").index();
            $("#parent_guid_" + row_index).val("");
            $("#selected_accname_" + row_index).val("");
            $("#customer_guid_" + row_index).val("");
            $("#selected_customer_" + row_index).val("");
            $("#debit_" + row_index).val("");
            $("#credit_" + row_index).val("");
            $("#description_" + row_index).val("");
            $("#name_" + row_index).val("");
            $("#state_" + row_index).val("n");
            $("#project_guid_" + row_index).val("");
            $("#selected_project_" + row_index).val("");
            if (tguid != '') {
                $("#tr_" + row_index).children('td').eq('2').css('display', 'table-cell');
            }
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
//        $('body').on('click', '.txt_td,.save_td', function (e) {
            console.log("come_3");
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


//        table.on('click', '.txt_td,.save_td', function (e) {

            var tr_id = $(this).closest('tr').attr('id');
            var id_arr = tr_id.split("_");
            var tr_index = id_arr[1];

//            calc_total1(tr_index);
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
                if (!$('.fill_tr > .account_cls').length || !$('.fill_tr > .customer_cls').length || !$('.fill_tr > .project_cls').length) {
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
        var last_index = $('#journal_entry_editable tbody tr:last').index();
        var new_index = $('#journal_entry_editable tbody tr:last').index() + 1;
        var new_index1 = $('#journal_entry_editable tbody tr:last').index() + 2;
        var table = $("#journal_entry_editable").DataTable();

        var append_HTML = '<input type="hidden" name="journal_attachments[]" class="journal_attachments" />';
        append_HTML += '<input type="hidden" name="splits_guid[]" id="splits_guid' + new_index + '" value=""/>';
        append_HTML += '<input type="hidden" name="parent_guid[]" id="parent_guid_' + new_index + '" value=""/>';
        append_HTML += '<input type="hidden" name="selected_accname[]" id="selected_accname_' + new_index + '"/>';
        append_HTML += '<input type="hidden" name="customer_guid[]" id="customer_guid_' + new_index + '" value=""/>';
        append_HTML += '<input type="hidden" name="selected_customer[]" id="selected_customer_' + new_index + '"/>';
        append_HTML += '<input type="hidden" name="debit[]" id="debit_' + new_index + '"/>';
        append_HTML += '<input type="hidden" name="credit[]" id="credit_' + new_index + '"/>';
        append_HTML += '<input type="hidden" name="description[]" id="description_' + new_index + '"/>';
        append_HTML += '<input type="hidden" name="name[]" id="name_' + new_index + '"/>';
        append_HTML += '<input type="hidden" name="state[]" id="state_' + new_index + '" value="n"/>';
        append_HTML += '<input type="hidden" name="reconcile_date[]" id="reconcile_date_' + new_index + '" value=""/>';
        append_HTML += '<input type="hidden" name="project_guid[]" id="project_guid_' + new_index + '" value=""/>';
        append_HTML += '<input type="hidden" name="selected_project[]" id="selected_project_' + new_index + '" value=""/>';

        //td html
        append_HTML += '<td class="drag_td"><center><i class="fa fa-th"></i></center></td>';
        append_HTML += '<td>' + new_index1 + '</td>';
        append_HTML += ' <td class="txt_td"></td>';
        append_HTML += ' <td class="debit_txt txt_td"></td>';
        append_HTML += ' <td class="credit_txt txt_td"></td>';
        append_HTML += '<td class="center txt_td"></td>';
        append_HTML += '<td class="center txt_td"></td>';
        append_HTML += '<td class="center txt_td"></td>';
        append_HTML += '<td class="center txt_td"></td>';
        append_HTML += '<td><a class="delete" href="javascript:;">Delete </a></td>';

        table.row.add($('<tr id ="tr_' + new_index + '">' + append_HTML + '</tr>')[0]).draw();
        $("#journal_entry_editable tbody").sortable({
            items: "tr"
        });
    }

}
function remove_lines(element) {
    var cnt = $("#no_rows").val();
    var total_tr = $("#journal_entry_editable tbody > tr").length;
    if (total_tr > 8) {
        for (i = 0; i < cnt; i++) {
            $('#journal_entry_editable tbody > tr:last').remove();
        }
    } else {
        if (!$('#journalEntryModal').is(":visible")) {
            location.reload();
        }

    }
}
function save_data(nEditing) {
//    alert($("#journal_entry_editable").length);
    console.log("come_5_1");
    $('#journal_entry_editable tbody tr').each(function (idx) {
        $(this).children("td:eq(1)").html(idx + 1);
    });
    var id = $(nEditing).attr("id");
    var is_search_page = $("#is_search_page").val();
    if (id) {

        var id_arr = id.split("_");
        var id_index = id_arr[1];
        var acc_name = $("#selected_accname_" + id_index).val();
        var customer_name = $("#selected_customer_" + id_index).val();
        var debit = $("#debit_" + id_index).val();
        var credit = $("#credit_" + id_index).val();
        if (is_search_page == 'search') {
            if (debit != '') {
                debit = debit + "<span class='pull-right'><b>DR</b></span>";
            }
            if (credit != '') {
                credit = credit + "<span class='pull-right'><b>CR</b></span>";
            }
        }
        var desc = $("#description_" + id_index).val();
        var state = $("#state_" + id_index).val();
        var project_name = $("#selected_project_" + id_index).val();

        $("#" + id).children('td').eq('2').html(acc_name);
        $("#" + id).children('td').eq('3').html(debit);
        $("#" + id).children('td').eq('4').html(credit);
        if (state == 'y') {
            state = 'R';
        }
        $("#" + id).children('td').eq('5').html(state.toUpperCase());
        $("#" + id).children('td').eq('6').html(desc);
        $("#" + id).children('td').eq('7').html(customer_name);
        $("#" + id).children('td').eq('8').html(project_name);
        $("#" + id).children('td').eq('2').css('display', 'table-cell');
//        calc_total1(id_index);
    }

}
function credit_dis(element) {
    var index = $(element).closest("tr").attr("id");
    var id_arr = index.split("_");
    var id_index = id_arr[1];
    var credit_val = $("#" + index).children('td').eq('4').children("input").val();
    if (credit_val != '') {
        var debite_val = parseFloat($("#" + index).children('td').eq('3').children("input").val());
        $("#" + index).children('td').eq('3').children("input").val('');
        var val1 = parseFloat(credit_val).toFixed(2);
        $(element).val(val1);
        if (debite_val != '' && !isNaN(debite_val)) {
            $("#debit_total").html((parseFloat($("#debit_total").html()) - debite_val).toFixed(2));
            $("#" + index).children('td').eq('3').children("input").val('');
            $("#debit_" + id_index).val('');
        }
        $("#credit_" + id_index).val(val1);
    } else {
        $("#credit_" + id_index).val('');
    }
    calc_total(index, 'credit');
}
function debit_dis(element) {
    var index = $(element).closest("tr").attr("id");
    var id_arr = index.split("_");
    var id_index = id_arr[1];
    var debit_val = $("#" + index).children('td').eq('3').children("input").val();
    if (debit_val != '' && !isNaN(debit_val)) {
        var credit_val = parseFloat($("#" + index).children('td').eq('4').children("input").val());
        $("#" + index).children('td').eq('4').children("input").val('');
        var val1 = parseFloat(debit_val).toFixed(2);
        $(element).val(val1);
        if (credit_val != '' && !isNaN(credit_val)) {
            $("#credit_total").html((parseFloat($("#credit_total").html()) - credit_val).toFixed(2));
            $("#" + index).children('td').eq('4').children("input").val('');
            $("#credit_" + id_index).val('');
        }
        $("#debit_" + id_index).val(val1);
    } else {
        $("#debit_" + id_index).val('');
    }
    calc_total(index, 'debit')
}
$('body').click(function (evt) {
    var value = $("#event_type").val();
    if (value == "journal_entry") {
        console.log("come_5");
        if (evt.target.id == "journal_entry_editable")
            return false;
//       //For descendants of menu_content being clicked, remove this check if you do not want to put constraint on descendants.
        if ($(evt.target).closest('#journal_entry_editable').length)
            return false;

        var Row = $(".desc_txt").parents("tr")[0];
        save_data(Row);
    }
});
