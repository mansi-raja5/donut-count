//oTable1.destroy();
var company_currency = $("#company_currency").val();
$('body').on('DOMNodeInserted', 'select.jselect2me', function () {
    $(this).select2();
});
$('body').click(function (evt) {
    if (evt.target.id == "sample_editable_2") {
        return false;
    } else if ($(evt.target).closest('#sample_editable_2').length) {
        return false;
    } else {
        var Row = $(".account_cls").parents("tr")[0];
        save_data(Row);
    }

});
function save_data(nEditing) {
    var id = $(nEditing).attr("id");
    if (id != "" && typeof id !== 'undefined') {
        var id_arr = id.split("_");
        var id_index = id_arr[1];

        var customer_guid = $("#sample_editable_2 #customer").val();
        var customer_name = $("#sample_editable_2 #customer").select2('data').text;
        var account_guid = $("#sample_editable_2 #account").val();
        var account_name = $("#sample_editable_2 #account").select2('data').text;
        var fund_paymentmethod_name = $("#sample_editable_2 #fund_paymentmethod").val();
        var description_ = $("#sample_editable_2 #fund_description").val();
        var fund_refno = $("#sample_editable_2 #fund_refno").val();
        var pro_amount = $("#sample_editable_2 #pro_amount_txt").val();

        var ref_name_guid = $("#sample_editable_2 #ref_name").val();
        // var ref_name = $("#sample_editable_2 #ref_name").select2('data').text;

        // if(customer_guid != "" || account_guid != "" || fund_paymentmethod_name != "" || pro_amount != ""){
        if ($("#sample_editable_2 #customer").val() != "" || $("#sample_editable_2 #account").val() != "" || $("#sample_editable_2 #ref_name").val() != "" || $("#sample_editable_2 #pro_amount_txt").val() != "") {
            var temp_total = 0;
            if ($("#sample_editable_2 #customer").val() == "") {
                $("#" + id).children('td').eq('2').find('.jselect2me > a').addClass('errorClass');
                temp_total++;
            }
            if ($("#sample_editable_2 #account").val() == "") {
                $("#" + id).children('td').eq('3').find('.jselect2me > a').addClass('errorClass');
                temp_total++;
            }
            if ($("#sample_editable_2 #fund_paymentmethod").val() == "") {
                $("#" + id).children('td').eq('5').find('.jselect2me > a').addClass('errorClass');
                temp_total++;
            }
            if ($("#sample_editable_2 #pro_amount_txt").val() == "") {
                $("#" + id).children('td').eq('6').find('input').addClass('errorClass');
                temp_total++;
            }
            if (temp_total > 0) {
                return false;
            }
        }
        if (customer_guid == "") {
            $("#" + id).children('td').eq('2').html("");
        } else {
            $("#" + id).children('td').eq('2').html(customer_name);
        }
        if (account_guid == "") {
            $("#" + id).children('td').eq('3').html("");
        } else {
            $("#" + id).children('td').eq('3').html(account_name);
        }
//        if (ref_name_guid == "") {
//            $("#" + id).children('td').eq('6').html("");
//        } else {
//            $("#" + id).children('td').eq('6').html(ref_name);
//        }
        $("#" + id).children('td').eq('4').html(description_);
        $("#" + id).children('td').eq('5').html(fund_paymentmethod_name);
        $("#" + id).children('td').eq('6').html(fund_refno);
        $("#" + id).children('td').eq('7').html(pro_amount);
        $("#" + id).children('td').eq('8').html('<a class="delete" href="javascript:;"><i class="fa fa-trash"></i></a>');
    }
}

function item_calc_total1(index) {
    total_amount_final();
//    var credit_sum = 0.00;
//    // var amount_val = parseFloat($("#itr_" + index).children('td').eq('7').children("input").val());
//    var amount_val = parseFloat($("#pro_amount_" + index).val());
//    var acc_total = parseFloat($("#total").html());
//    if (amount_val != '' && typeof amount_val !== 'undefined' && !isNaN(amount_val)) {
//        credit_sum = amount_val;
//    }
//    
//    $(".pro_amount_txt").each(function () {
//        var value = $(this).html();
//        if ($.trim(value) != '' && !isNaN($.trim(value))) {
//            credit_sum += parseFloat(value);
//        }
//    });
//
//    if (credit_sum) {
//        $("#item_total").html(credit_sum.toFixed(2));
//        var disp_total;
//        if (!isNaN(acc_total)) {
//            disp_total = parseInt(credit_sum) + parseInt(acc_total);
//        } else {
//            disp_total = credit_sum;
//        }
//        $("#balance_due, #disp_total").html(addCommas(disp_total.toFixed(2)));
//    } else {
//        if (typeof acc_total != 'undefined' && acc_total != '' && !isNaN($.trim(acc_total))) {
//            $("#disp_total").html(addCommas(acc_total.toFixed(2)));
//        }
//    }
}

function item_calc_totbalance() {
    var tot_sum = 0;
    $(".pro_amount_txt").each(function () {
        var value = $(this).html();
        if ($.trim(value) != '' && !isNaN($.trim(value))) {
            tot_sum += parseFloat(value);
        }
    });
    if (tot_sum) {
        $("#item_total").html(tot_sum.toFixed(2));
    } else {
        $("#item_total").html("0");
    }
}

function fund_desc(element) {
    var index = $(element).closest("tr").attr("id");

    var id_arr = index.split("_");
    var id_index = id_arr[1];
    var desc_val = $(element).val();
    $("#fund_description_" + id_index).val(desc_val);
}

function fund_ref_no(element) {
    var index = $(element).closest("tr").attr("id");

    var id_arr = index.split("_");
    var id_index = id_arr[1];
    var desc_val = $(element).val();
    $("#fund_refno_" + id_index).val(desc_val);
}

jQuery(document).on('change', '#sample_editable_2 #account', function () {
    set_account(this, 2);
});

function set_account(element, type) {
    var index = $(element).attr("id");
    var id = $(element).parents("tr").attr("id");
    var id_arr = id.split("_");
    var element_index = id_arr[1];

    if ($(element).val() != "") {
        if ($(element).val() == 'CA') {
            if (type == '1') {
                var types = $("#type_accounts").val();
                if (typeof index == "undefined") {
                    var index = $(element).closest("tr").index();
                    $("#new_account").attr("href", site_url + "/accounts/add_by_type?types=" + types + "&page=expense_accounts&index=" + index);
                } else {
                    $("#new_account").attr("href", site_url + "/accounts/add_by_type?types=" + types + "&page=expense&index=" + index);
                }
            } else {
                var index = $(element).closest("tr").index();
                $("#new_account").attr("href", site_url + "/accounts/add?page=deposit_slip&index=" + index);
            }
            $("#new_account").trigger("click");
        } else {
            if (type == '1' && typeof index !== "undefined") {
                $("#customModal").modal("hide");
//            ajax call for get selected account
                $.ajax({
                    type: 'POST',
                    url: site_url + 'expenses/get_account_balance',
                    data: {
                        action: 'get_balance',
                        guid: $(element).val()
                    },
                    beforeSend: function (xhr) {
                        $("#loadingmessage").show();
                    },
                    success: function (responseJSON) {
                        $("#loadingmessage").hide();
                        var response = JSON.parse(responseJSON);
                        $("#balance").html(response.tot_bal);
                        $("#acc_balance").val(response.tot_bal);
                    }
                });
            }
            if (type == '2') {
                var index = $(element).attr("id");
                var id = $(element).parents("tr").attr("id");
                var id_arr = id.split("_");
                var element_index = id_arr[1];

                var element_select_text = $(element).find('option:selected').text();
                var element_select_option = $(element).val();
                if (element_select_option != '' && element_select_option != null && element_select_option != 'undefined') {
                    $("#account_guid_" + element_index).val(element_select_option);
                } else {
                    $("#account_guid_" + element_index).val("");
                }
                if (element_select_text != '' && element_select_text != '-- Select --') {
                    $("#account_name_" + element_index).val(element_select_text);
                } else {
                    $("#account_name_" + element_index).val("");
                }
            }
        }
    } else {
        $("#account_name_" + element_index).val("");
        $("#account_guid_" + element_index).val("");
    }
}
function set_paymentmothod(element) {
    var id = $(element).parents("tr").attr("id");
    var id_arr = id.split("_");
    var element_index = id_arr[1];

    var element_select_text = $(element).find('option:selected').text();
    var element_select_option = $(element).val();
    if (element_select_option != '' && element_select_option != null && element_select_option != 'undefined') {
        $("#fund_paymentmethod_id_" + element_index).val(element_select_option);
    } else {
        $("#fund_paymentmethod_id_" + element_index).val("");
    }
    if (element_select_text != '' && element_select_text != '-- Select --') {
        $("#fund_paymentmethod_name_" + element_index).val(element_select_text);
    } else {
        $("#fund_paymentmethod_name_" + element_index).val("");
    }
}
function addCommas(nStr) {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + x2;
    }
function item_amount(element) {
    var index = $(element).closest("tr").attr("id");
    var id_arr = index.split("_");
    var id_index = id_arr[1];
    var amount = $(element).val();
    var tot_sum = 0;
    var qty = $("#" + index).children('td').eq('4').children("input").val();

    if (amount != '') {
        tot_sum = parseFloat(amount);
        var acc_total = parseFloat($("#total").html());
        $(".pro_amount_txt").each(function () {
            var value = $(this).html();
            if ($.trim(value) != '' && !isNaN($.trim(value))) {
                tot_sum += parseFloat(value);
            }
        });

        if (qty != '') {
            var rate = parseFloat(amount / qty).toFixed(2);
            $("#rate_" + id_index).val(rate);
        }
        if (!isNaN(acc_total)) {
            bal_tot_sum = parseFloat(tot_sum) + parseFloat(acc_total);
        } else {
            bal_tot_sum = tot_sum;
        }
        $("#pro_amount_" + id_index).val(parseFloat(amount).toFixed(2));
        $("#item_total").html(tot_sum.toFixed(2));
        $("#disp_total").html(addCommas(bal_tot_sum.toFixed(2)));
    }
}
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
            var jqTds = $('>td', nRow);
            var tr_id = jqTds.parents("tr").attr("id");
            var tree_dropdown = '';
            var customer_guid = $("#customer_guid_" + tr_index).val();
            var account_guid = $("#account_guid_" + tr_index).val();
            var fund_paymentmethod_id = $("#fund_paymentmethod_id_" + tr_index).val();
            var ref_name_guid = $("#ref_name_guid_" + tr_index).val();
            var description_ = $("#fund_description_" + tr_index).val();
            var fund_refno = $("#fund_refno_" + tr_index).val();
            var pro_amount = $("#pro_amount_" + tr_index).val();
            if ($("#account_selection").length) {
                tree_dropdown = $("#account_selection").html();
            }
            var aData = oTable1.fnGetData(nRow);
            var rec_dropdown = '';
            if ($("#rec_selection").length) {
                rec_dropdown = $("#rec_selection").html();
            }
//            var ref_name_dropdown = '';
//            if ($("#rec_selection").length) {
//                ref_name_dropdown = $("#ref_name_selection").html();
//            }
            
            var fund_paymentmethod = '';
            if ($("#rec_selection").length) {
                fund_paymentmethod = $("#fund_paymentmethod").html();
            }

            jqTds[2].innerHTML = rec_dropdown;
            jqTds[3].innerHTML = tree_dropdown;
            jqTds[4].innerHTML = '<input type="text" name="fund_description[]" id="fund_description" class="form-control input-small fund_description" value="" onkeyup="return fund_desc(this);" >';
            jqTds[5].innerHTML = fund_paymentmethod;
            // jqTds[6].innerHTML = ref_name_dropdown;
            jqTds[6].innerHTML = '<input type="text" name="fund_refno[]"  id="fund_refno" class="form-control input-small fund_refno" value="" onkeyup="return fund_ref_no(this);">';
            jqTds[7].innerHTML = '<input type="text" class="form-control input-small float pro_amount_txt" id="pro_amount_txt" value="" onkeyup="item_amount(this);" onblur="item_amount_dis(this);">';
            jqTds[8].innerHTML = '<a class="item_cancel" href="">Cancel</a>';

            jQuery('#' + tr_id).children('td').eq('2').find('select').val(customer_guid).trigger('change');
            jQuery('#' + tr_id).children('td').eq('3').find('select').val(account_guid).trigger('change');
            jQuery('#' + tr_id).children('td').eq('5').find('select').val(fund_paymentmethod_id).trigger('change');
            $("#" + tr_id).children('td').eq('4').children("input").val(description_);
            // jQuery('#' + tr_id).children('td').eq('6').find('select').val(ref_name_guid).trigger('change');
            // $("#" + tr_id).children('td').eq('2').children("select").val(selected_option_text).trigger('change');
            $("#" + tr_id).children('td').eq('6').children("input").val(fund_refno);
            $("#" + tr_id).children('td').eq('7').children("input").val(pro_amount);

            var select_cnt = $("#" + tr_id).children('td').eq('2').find(".select2-container").length;
            if (select_cnt >= 2) {
                $("#" + tr_id).children('td').eq('2').find(".select2-container:first").remove();
            }

            var select_cnt = $("#" + tr_id).children('td').eq('3').find(".select2-container").length;

            if (select_cnt >= 2) {
                $("#" + tr_id).children('td').eq('3').find(".select2-container:first").remove();
            }

        }

        function saveRow(oTable1, nRow, tr_index) {
            var jqInputs = $('input[type="text"]', nRow);
            var acc_name = $("#selected_products_" + tr_index).val();
            oTable1.fnUpdate(acc_name.value, nRow, 2, false);
            oTable1.fnUpdate(jqInputs[1].value, nRow, 3, false);
            oTable1.fnUpdate(jqInputs[2].value, nRow, 4, false);
            oTable1.fnUpdate(jqInputs[3].value, nRow, 5, false);
            oTable1.fnUpdate(jqInputs[4].value, nRow, 6, false);
            // oTable1.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 9, false);
//            oTable1.fnUpdate('<a class="delete" href="">Delete</a>', nRow, 10, false);
            oTable1.fnDraw();
            item_calc_total1();
        }

        function cancelEditRow(oTable1, nRow) {
            var jqInputs = $('input', nRow);
            oTable1.fnUpdate(jqInputs[0].value, nRow, 0, false);
            oTable1.fnUpdate(jqInputs[1].value, nRow, 1, false);
            oTable1.fnUpdate(jqInputs[2].value, nRow, 2, false);
            oTable1.fnUpdate(jqInputs[3].value, nRow, 3, false);
            oTable1.fnUpdate(jqInputs[4].value, nRow, 4, false);
            oTable1.fnUpdate(jqInputs[5].value, nRow, 5, false);
            // oTable1.fnUpdate('<a class="edit" href="">Edit</a>', nRow, 6, false);
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
                [1, "asc"]
            ] // set first column as a default sort by asc
        });
        
        function update_srno() {
            $(".ui-sortable > tr").each(function () {
                var sr_no = $(this).index() + 1;
                $(this).children('td').eq('1').html(sr_no);
            });
        }
        jQuery('#sample_editable_2  tbody').sortable();
        $("#sample_editable_2 tbody").sortable({
            placeholder: "ui-state-highlight",
            update: function (event, ui) {
                update_srno();
            }
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
            if (confirm("Are you sure to delete this row ?") == false) {
                return;
            }
            var row_index = $(this).parents("tr").index();
            var tot_row = $("#sample_editable_2 tbody > tr").length;
            if (tot_row > 1) {
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
                $("#customer_guid_" + row_index).val("");
                $("#customer_name_" + row_index).val("");
                $("#account_guid_" + row_index).val("");
                $("#account_name_" + row_index).val("");
                $("#fund_paymentmethod_id_" + row_index).val("");
                $("#fund_paymentmethod_name_" + row_index).val("");
                $("#fund_description_" + row_index).val("");
                $("#fund_refno_" + row_index).val("");
                $("#pro_amount_" + row_index).val("");
                
                $("#sample_editable_2 tbody > tr > td:eq(2)").html("");
                $("#sample_editable_2 tbody > tr > td:eq(3)").html("");
                $("#sample_editable_2 tbody > tr > td:eq(4)").html("");
                $("#sample_editable_2 tbody > tr > td:eq(5)").html("");
                $("#sample_editable_2 tbody > tr > td:eq(6)").html("");
                $("#sample_editable_2 tbody > tr > td:eq(7)").html("");
                $("#item_total").html("0");
            }
            var total_amount = 0;
            $('input[name^="pro_amount"]').each(function () {
                if ($(this).val() != "") {
                    total_amount = total_amount + parseFloat($(this).val());
                }

            });
            jQuery('#total_payment').text(company_currency + total_amount.toFixed(2));
            total_amount_final();
        });

        table.on('click', '.item_cancel', function (e) {
            var row_index = $(this).parents("tr").index();
//            // $("#customer_guid_" + row_index).val("");
//            $("#selected_products_" + row_index).val("");
//            $("#pro_description_" + row_index).val("");
//            $("#qty_" + row_index).val("");
//            $("#rate_" + row_index).val("");
//            $("#pro_amount_" + row_index).val("");
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
                $('#pro_amount_' + tr_index).val($('#' + tr_id).find(".pro_amount_txt  > input").val());
                /* Editing this row and want to save it */
                saveRow(oTable1, nEditing, tr_index);
                $('#' + tr_id + ' > td').addClass("fill_tr");
                nEditing = null;
            } else {
                if (!$('.fill_tr > .account_cls_payment').length) {
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

jQuery(document).ready(function () {
    TableDatatablesEditable1.init();
});
function item_save_data(nEditing) {
    $('#sample_editable_2 tbody tr').each(function (idx) {
        $(this).children("td:eq(1)").html(idx + 1);
    });
    var id = $(nEditing).attr("id");
    if (id) {
        var id_arr = id.split("_");
        var id_index = id_arr[1];
        var customer_guid = $("#customer_guid_" + id_index).val();
        var customer_name = $("#customer_name_" + id_index).val();

        var account_guid = $("#account_guid_" + id_index).val();
        var account_name = $("#account_name_" + id_index).val();

        var fund_paymentmethod_id = $("#fund_paymentmethod_id_" + id_index).val();
        var fund_paymentmethod_name = $("#fund_paymentmethod_name_" + id_index).val();

        var ref_name_guid = $("#ref_name_guid_" + id_index).val();
        var ref_name = $("#ref_name_" + id_index).val();

        var desc = $("#fund_description_" + id_index).val();
        var fund_refno = $("#fund_refno_" + id_index).val();
        var amount = $("#pro_amount_" + id_index).val();

        $("#" + id).children('td').eq('2').html(customer_name);
        $("#" + id).children('td').eq('3').html(account_name);
        $("#" + id).children('td').eq('4').html(desc);
        // $("#" + id).children('td').eq('4').html(qty);
        $("#" + id).children('td').eq('5').html(fund_paymentmethod_name);
        // $("#" + id).children('td').eq('6').html(ref_name);
        $("#" + id).children('td').eq('6').html(fund_refno);
        $("#" + id).children('td').eq('7').html(amount);
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
            $("#pro_amount_" + id_index).val(tot_amount);
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
            $("#pro_amount_" + id_index).val(tot_amount);
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
    }
    var total_amount = 0;
    $('input[name^="pro_amount"]').each(function () {
        if ($(this).val() != "") {
            total_amount = total_amount + parseFloat($(this).val());
        }

    });
    jQuery('#total_payment').text(company_currency + total_amount.toFixed(2));
    total_amount_final();

//    calc_total(index, 'rate');
}
function add_item_lines(element) {
    var cnt = 2;

    for (var i = 0; i < cnt; i++) {
        var last_index = $('#sample_editable_2 tbody tr:last').index();
        var new_index = $('#sample_editable_2 tbody tr:last').index() + 1;
        var new_index1 = $('#sample_editable_2 tbody tr:last').index() + 2;
        var table = $("#sample_editable_2").DataTable();

        var append_html = '<input type="hidden" class="customer_guid" name="customer_guid[]" id="customer_guid_' + new_index + '" value=""/>';
        append_html += '<input type="hidden" class="customer_name" name="customer_name[]" id="customer_name_' + new_index + '"/>';
        append_html += '<input type="hidden" class="account_guid" name="account_guid[]" id="account_guid_' + new_index + '"/>';
        append_html += '<input type="hidden" class="account_name" name="account_name[]" id="account_name_' + new_index + '"/>';
        append_html += '<input type="hidden" class="fund_paymentmethod_id" name="fund_paymentmethod_id[]" id="fund_paymentmethod_id_' + new_index + '"/>';
        append_html += '<input type="hidden" class="fund_paymentmethod_name" name="fund_paymentmethod_name[]" id="fund_paymentmethod_name_' + new_index + '"/>';
        append_html += '<input type="hidden" class="ref_name_guid" name="ref_name_guid[]" id="ref_name_guid_' + new_index + '" value=""/>';
        // append_html += '<input type="hidden" class="ref_name" name="ref_name[]" id="ref_name_' + new_index + '"/>';
        append_html += '<input type="hidden" class="fund_description" name="fund_description[]" id="fund_description_' + new_index + '"/>';
        append_html += '<input type="hidden" class="fund_refno" name="fund_refno[]" id="fund_refno_' + new_index + '"/>';
        append_html += '<input type="hidden" class="pro_amount" name="pro_amount[]" id="pro_amount_' + new_index + '"/>';

        //td html
        append_html += '<td class="drag_td"><center><i class="fa fa-th"></i></center></td>';
        append_html += '<td>' + new_index1 + '</td>';
        append_html += '<td class="txt_td"></td>';
        append_html += '<td class="txt_td"></td>';
        append_html += '<td class="txt_td"></td>';
        append_html += '<td class="txt_td"></td>';
        append_html += '<td class="txt_td"></td>';
        //append_html += '<td class="txt_td"></td>';
        append_html += '<td class="pro_amount_txt center txt_td"></td>';
        append_html += '<td><a class="delete" href="javascript:;">\n' +
                '                    <i class="fa fa-trash"></i>\n' +
                '                </a></td>';
        table.row.add($('<tr id ="itr_' + new_index + '">' + append_html + '</tr>')[0]).draw();
    }
}
function remove_lines(element) {
    var cnt = 2;
    var total_tr = $("#sample_editable_2 tbody > tr").length;
    if (total_tr > 1) {
        for (i = 0; i < cnt; i++) {
            $('#sample_editable_2 tbody > tr:last').remove();
        }
    } else {
        location.reload();
    }
}