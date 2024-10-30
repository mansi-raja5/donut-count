class Reconcile {
    constructor(ledgerId, bankId, storeKey, month, year) {
        this.ledgerId   = ledgerId;
        this.bankId     = bankId;
        this.storeKey   = storeKey;
        this.month      = month;
        this.year       = year;
        this.nonEditableDescAry = ['donut_purchases', 'impound', 'payroll_net', 'payroll_gross', 'roy_adv', 'dean_foods', 'dcp_efts'];
        this.nonEditableDebitAry = ['impound', 'payroll_net', 'payroll_gross', 'roy_adv', 'donut_purchases'];
        this.ledger_section_description = [];
        this.ledger_section_description['donut_purchases'] = 'Donut';
        this.ledger_section_description['impound'] = 'Impound';
        this.ledger_section_description['payroll_net'] = 'Payroll Net';
        this.ledger_section_description['payroll_gross'] = 'Payroll Gross';
        this.ledger_section_description['roy_adv'] = 'Roy Adv';
        this.ledger_section_description['dean_foods'] = 'Dean Foods';
        this.ledger_section_description['dcp_efts'] = 'Dcp Efts';
        this.manualMappigGlobalCount = 0;
    }
    getPostData() {
        return {
            ledger_id: this.ledgerId,
            bank_id: this.bankId
        };
    }
    getManualReconcilationData() {
        $("#loadingmessage").show();
        $.ajax({
            url: site_url + 'reconcile/getManualReconcilationData',
            data: this.getPostData(),
            method: 'POST',
            success: function(response) {
                $("#manual_bank_balance span").html("");
                $('#manual_reconcilation_modal').html("");
                $('#manual_reconcilation_modal').html(response);
                $('#manual_reconcilation_modal').modal({
                    backdrop: 'static',
                    keyboard: true,
                    show: true
                });
                $("#loadingmessage").hide();
            }
        });
    }
    getLedgerData()
    {
        $("#loadingmessage").show();
        $.ajax({
            url: site_url + 'reconcile/getLedgerData',
            data: this.getPostData(),
            method: 'POST',
            success: function(response) {
                $('#ledger_data_div').html(response);
                $("#loadingmessage").hide();
            }
        });
    }
    getPreviousLedgerData()
    {
        $("#loadingmessage").show();
        $.ajax({
            url: site_url + 'reconcile/getPreviousLedgerData',
            data: this.getPostData(),
            method: 'POST',
            success: function(response) {
                $('#previous_ledger_data_div').html(response);
                $("#loadingmessage").hide();
            }
        });
    }

    getCheckData() //this is for recocilation process page
    {
        $("#loadingmessage").show();
        $.ajax({
            url: site_url + 'reconcile/getCheckData',
            data: this.getPostData(),
            method: 'POST',
            success: function(response) {
                $('#checkbook_data_div').html(response);
                $("#loadingmessage").hide();
            }
        });
    }
    getPreviousCheckData()
    {
        $("#loadingmessage").show();
        $.ajax({
            url: site_url + 'reconcile/getPreviousCheckData',
            data: this.getPostData(),
            method: 'POST',
            success: function(response) {
                $('#previous_check_data_div').html(response);
                $("#loadingmessage").hide();
            }
        });
    }
    getBankData() //this is for recocilation process page
    {
        $("#loadingmessage").show();
        $.ajax({
            url: site_url + 'reconcile/getBankData',
            data: this.getPostData(),
            method: 'POST',
            success: function(response) {
                $('#bank_data_div').html(response);
                $("#loadingmessage").hide();
            }
        });
    }
    getPreviousBankData() //this is for recocilation process page
    {
        $("#loadingmessage").show();
        $.ajax({
            url: site_url + 'reconcile/getPreviousBankData',
            data: this.getPostData(),
            method: 'POST',
            success: function(response) {
                $('#previous_bank_data_div').html(response);
                $("#loadingmessage").hide();
            }
        });
    }
    getCreditReceivedFromData() //this is for recocilation process page
    {
        $("#loadingmessage").show();
        $.ajax({
            url: site_url + 'reconcile/getCreditReceivedFromData',
            data: this.getPostData(),
            method: 'POST',
            success: function(response) {
                $('#credit_received_from_data_div').html(response);
                $("#loadingmessage").hide();
            }
        });
    }
    addManualRow(r){
	    var addType = $(r).attr('add-type');
	    if (addType == 'ledger') {
	        var trHtml = $("#ledger_blank_tr_div").html();
	        $("#unreconciled_manual_ledger").append(trHtml);
	    }
	    if (addType == 'bank') {
	        var trHtml = $("#bank_blank_tr_div").html();
	        $("#unreconciled_manual_bank").append(trHtml);
	    }
        $(".manual_datepicker").datepicker({
            format: "mm/dd/yyyy",
            startDate: new Date(this.year + "-" + this.month + "-01"),
            endDate: new Date(this.year + "-" + this.month + "-31")
        });
    }
    edit_cell_manual(r) {
        $(".manual_reconcile_lock").addClass('btn-disabled');
        $(r).closest("tr").find(".manual-label").hide();
        $(r).closest("tr").find(".manual-edit").hide();
        $(r).closest("tr").find(".manual-delete").hide();
        $(r).closest("tr").find(".manual-input").show();
        $(r).closest("tr").find(".manual-save").show();
        $(r).closest("tr").find(".remove-edit").show();

        $(".manual_datepicker").datepicker({
            format: "mm/dd/yyyy",
            startDate: new Date(this.year + "-" + this.month + "-01"),
            endDate: new Date(this.year + "-" + this.month + "-31")
        });

        var selectedDocumentType = $(r).closest("tr").find(".manual-ledger-document-type").val();
        this.make_field_editable(selectedDocumentType, r);
        this.resetManualReconcileSummary();
    }

    make_field_editable(selectedDocumentType, currentObj)
    {
        var description = jQuery(currentObj).closest('tr').find(".manual-ledger-description").val();
        //check description field
        if (-1 != jQuery.inArray(selectedDocumentType, this.nonEditableDescAry))
        {
            jQuery(currentObj).closest('tr').find(".manual-ledger-description").val(this.ledger_section_description[selectedDocumentType]);
            jQuery(currentObj).closest('tr').find(".manual-ledger-description").addClass('non-editable');
        }
        else if (selectedDocumentType == 'general_section'
            && jQuery(currentObj).closest('tr').find(".manual-ledger-transaction-type").val() == 'credit'
            && description == 'DEPOSIT')
        {
            jQuery(currentObj).closest('tr').find(".manual-ledger-description").val('DEPOSIT');
            jQuery(currentObj).closest('tr').find(".manual-ledger-description").addClass('non-editable');
        }
        else if (selectedDocumentType == 'general_section' && description == 'Credit Card Credits')
        {
            jQuery(currentObj).closest('tr').find(".manual-ledger-description").addClass('non-editable');
        }
        else
        {
            jQuery(currentObj).closest('tr').find(".manual-ledger-description").removeClass('non-editable');
        }

        //check credit/debit field
        if (-1 != jQuery.inArray(selectedDocumentType, this.nonEditableDebitAry))
        {
            jQuery(currentObj).closest('tr').find(".manual-ledger-transaction-type").val('debit');
            jQuery(currentObj).closest('tr').find(".manual-ledger-transaction-type").addClass('non-editable');
        } else
        {
            jQuery(currentObj).closest('tr').find(".manual-ledger-transaction-type").removeClass('non-editable');
        }
    }

	resetManualReconcileSummary()
    {
        $("#manual_bank_balance span").html(0);
        $("#txt_manual_bank_balance").val(0);
        $("#manual_ledger_balance span").html(0);
        $("#txt_manual_ledger_balance").val(0);
        $('#unreconciled_manual_bank_wrapper .bank-checkbox:checked').removeAttr('checked');
        $('#unreconciled_manual_ledger_wrapper .ledger-checkbox:checked').removeAttr('checked');
        $('.ledger-checkbox').closest('tr').removeClass("highlight_row");
        $('.bank-checkbox').closest('tr').removeClass("highlight_row");
        $('.ledger-checkbox').closest('tr').find(".manual-edit").removeClass("non-editable");
        $('.bank-checkbox').closest('tr').find(".manual-edit").removeClass("non-editable");
    }

    extra_manual_entry(element) {
        var inputDataLedger = $(element).closest("tr").find('input').serialize();
        var selectDataLedger = $(element).closest("tr").find('select').serialize();

        var date = $(element).closest("tr").find('input[name="manual-ledger-credit-date"]');
        var desc = $(element).closest("tr").find('input[name="manual-ledger-description"]')
        var amount = $(element).closest("tr").find('input[name="manual-ledger-amount"]');
        var selectedDocumentType  = $(element).closest("tr").find('select[name="manual-ledger-document-type"]');

        var currentObj = this;
        if ((desc).val() == ''
            || (date).val() == ''
            || (selectedDocumentType).val() == -1
            || (amount).val() == '') {
            alert("Please fill all the fields before add the entry");
            if((date.val()) == ''){
                $(date).addClass("error_border");
            }
            if ((desc.val()) == '') {
                $(desc).addClass("error_border");
            }
            if ((amount.val()) == '') {
                $(amount).addClass("error_border");
            }
            if ((selectedDocumentType.val()) == -1) {
                $(selectedDocumentType).addClass("error_border");
            }
            return false;
        } else {
            var ledgerStatementId       = $(element).attr('ledger-statement-id');
            var selectedDocumentType    = selectedDocumentType.val();
            $("#loadingmessage").show();
            $.ajax({
                url: site_url + 'reconcile/extra_manual_entry',
                type: "POST",
                data: inputDataLedger + "&" + selectDataLedger + "&ledger_statement_id=" + ledgerStatementId + "&ledger_id=" + this.ledgerId + "&store_key=" + this.storeKey,
                success: function (responseText) {
                    $("#loadingmessage").hide();
                    var Res = JSON.parse(responseText);
                    if (Res.status == 'success') {
                        $(element).closest("tr").find(".manual-label").show();
                        $(element).closest("tr").find(".manual-edit").show();
                        $(element).closest("tr").find(".manual-input").hide();
                        $(element).closest("tr").find(".manual-save").hide();
                        currentObj.enableLockBtn();
                        currentObj.getManualReconcilationData();
                    }
                }
            });
        }
    }

    remove_edit_from_row(r) {
        $(r).closest("tr").find(".manual-label").show();
        $(r).closest("tr").find(".manual-edit").show();
        $(r).closest("tr").find(".manual-delete").show();
        $(r).closest("tr").find(".manual-input").hide();
        $(r).closest("tr").find(".manual-save").hide();
        $(r).closest("tr").find(".remove-edit").hide();
        this.resetManualReconcileSummary();
        this.getManualReconcilationData();
    }

    lockEntries(r)
    {
        ++this.manualMappigGlobalCount;
        $('#unreconciled_manual_bank tfoot input').val('').change();
        $('#unreconciled_manual_ledger tfoot input').val('').change();

        var trHtml = "<table class='table table-bordered'>";
        trHtml += "<tr>";
        trHtml += "<td style='width:50%'>";
        trHtml += "<table class='table table-bordered'>";

        var ledgerTr        = "";
        var ledger_amount   = 0;
        var ledger_tr_arr   = [];
        jQuery('.manual_reconcile_cover #unreconciled_manual_ledger tr:visible .ledger-checkbox').each(function () {
            if ($(this).prop("checked"))
            {
                var allClassNames = $(this).closest("tr").attr("class");
                ledger_tr_arr.push(allClassNames.replace(' ', '.'));
                ledger_amount += parseFloat($(this).closest("tr").find(".manual-ledger-amount").val());

            }
        });

        var bankTr      = "";
        var bank_amount = 0;
        var bank_tr_arr = [];
        jQuery('.manual_reconcile_cover #unreconciled_manual_bank tr:visible .bank-checkbox').each(function () {
            if ($(this).prop("checked"))
            {
                var allClassNames = $(this).closest("tr").attr("class");
                bank_tr_arr.push(allClassNames.replace(' ', '.'));
                bank_amount += parseFloat($(this).closest("tr").children("td:eq(5)").find(".manual-input").val());
            }
        });
        ledger_amount = jQuery('#manual_ledger_balance span').html();
        bank_amount = jQuery('#manual_bank_balance span').html();

        if (bank_amount == ledger_amount) {
            for (var i = 0; i < ledger_tr_arr.length; i++) {
                $("." + ledger_tr_arr[i]).hide();
                var inputCheckboxElement = $("." + ledger_tr_arr[i]).find("td:first").find("input:checkbox");
                inputCheckboxElement.attr("checked", "checked");
                var trClass = $("." + ledger_tr_arr[i]).closest("tr").attr('class');
                var checkboxName = 'ledger_statement_id[' + this.manualMappigGlobalCount + '][]';
                $("." + ledger_tr_arr[i]).find('.ledger-checkbox').attr('name', checkboxName);
                ledgerTr += "<tr class='" + trClass + "'>" + $("." + ledger_tr_arr[i]).closest("tr").html() + "</tr>";
            }

            trHtml += ledgerTr;
            trHtml += "</table>";
            trHtml += "</td>";
            trHtml += "<td style='width:50%'>";
            trHtml += "<table class='table table-bordered'>";
            for (var i = 0; i < bank_tr_arr.length; i++) {
                $("." + bank_tr_arr[i]).hide();
                var inputCheckboxElement = $("." + bank_tr_arr[i]).find("td:first").find("input:checkbox");
                inputCheckboxElement.attr("checked", "checked");
                var trClass = $("." + bank_tr_arr[i]).closest("tr").attr('class');
                var checkboxName = 'bank_statement_id[' + this.manualMappigGlobalCount + '][]';
                $("." + bank_tr_arr[i]).find('.bank-checkbox').attr('name', checkboxName);
                bankTr += "<tr class='" + trClass + "'>" + $("." + bank_tr_arr[i]).closest("tr").html() + "</tr>";
//                bank_tr_arr.push($("."+bank_tr_arr[i]).closest("tr").attr("class"));
            }
            trHtml += bankTr;
            trHtml += "</table>";
            trHtml += "</td>";
            trHtml += "<td  style='display:block;'>";
            trHtml += "<button type='button' onclick='recon.unlockEntries(this);'><i class='fa fa-times'></i></button>";
            trHtml += "</td>";
            trHtml += "</tr>";
            trHtml += "</table>";
            $(".top_section .top_section_manual_data").append(trHtml);
            $(".manual_reconcile_submit").removeClass('btn-disabled');
        } else {
            alert("For manual reconciliation, Ledger amount and Bank amount should be same");
            return false;
        }

        $(".manual_reconcile_lock").addClass('btn-disabled');
        this.resetManualReconcileSummary();
    }

    unlockEntries(element)
    {
        var mainTrElement = $(element).closest("tr");
        $(mainTrElement).find("tr").each(function () {
            var trClass = $(this).attr('class');
            $("." + trClass.replace(' ', '.')).show();
        });
        mainTrElement.remove();
    }

    enableLockBtn()
    {
        if ($("#txt_manual_bank_balance").val() == $("#txt_manual_ledger_balance").val())
        {
            $(".manual_reconcile_lock").removeClass('btn-disabled');
        } else
        {
            $(".manual_reconcile_lock").addClass('btn-disabled');
        }
    }

    extra_manual_entry_bank(element) {
        var inputDataBank   = $(element).closest("tr").find('input').serialize();
        var selectDataBank  = $(element).closest("tr").find('select').serialize();

        var date      = $(element).closest("tr").find('input[name="manual-bank-credit-date"]');
        var desc      = $(element).closest("tr").find('input[name="manual-bank-description"]');
        var amount    = $(element).closest("tr").find('input[name="manual-bank-amount"]');
        var checkNum  = $(element).closest("tr").find('input[name="manual-bank-check_num"]');
        var checkType = $(element).closest("tr").find('select[name="manual-bank-transaction-type"]');

        var currentObj = this;
        if ((date).val() == '' || (desc).val() == '' || (amount).val() == '' || (checkType.val() == 'check' && (checkNum.val()) == '')) {
            alert("Please fill all the fields before add the entry");
            if ((date.val()) == '') {
                $(date).addClass("error_border");
            }
            if ((desc.val()) == '') {
                $(desc).addClass("error_border");
            }
            if ((amount.val()) == '') {
                $(amount).addClass("error_border");
            }
            if (checkType.val() == 'check' && (checkNum.val()) == '') {
                $(checkNum).addClass("error_border");
            }
            return false;
        } else {
            var bankStatementId = $(element).attr('bank-statement-id');
            $("#loadingmessage").show();
            $.ajax({
                url: site_url + 'reconcile/extra_manual_entry_bank',
                type: "POST",
                data: inputDataBank + "&" + selectDataBank + "&bank_statement_id=" + bankStatementId + "&bank_id=" + bankId,
                success: function (responseText) {
                    $("#loadingmessage").hide();
                    var Res = JSON.parse(responseText);
                    if (Res.status == 'success') {
                        $(element).closest("tr").find(".manual-label").show();
                        $(element).closest("tr").find(".manual-edit").show();
                        $(element).closest("tr").find(".manual-input").hide();
                        $(element).closest("tr").find(".manual-save").hide();
                        currentObj.enableLockBtn();
                        currentObj.getManualReconcilationData();
                    }
                }
            });
        }
    }

    delete_cell_manual(element) {
        var t_type = $(element).attr('type');
        var currentObj = this;
        if (t_type == 'ledger') {
            var delete_id = $(element).attr('ledger-statement-id');
        } else {
            var delete_id = $(element).attr('bank-statement-id');
        }
        if (confirm('Are you sure you want to delete this entry')) {
            $("#loadingmessage").show();
            $.ajax({
                url: site_url + 'statement/delete_entry',
                type: "POST",
                data: {
                    'id': delete_id,
                    'type': t_type,
                },
                success: function (responseText) {
                    $("#loadingmessage").hide();
                    var Res = JSON.parse(responseText);
                    if (Res.status == 'success') {
                        currentObj.getManualReconcilationData();
                    } else {
                        alert("Something went wrong");
                    }
                }
            });
        } else {
            return false;
        }
    }

    remove_entries_from_adjustment(r)
    {
        var mid = jQuery(r).closest('tr').attr("id");
        jQuery("#chk-"+mid).trigger("click")
    }

    getCheckBookData() //this is for modal to add/edit checks
    {
        $("#loadingmessage").show();
        $.ajax({
            url: site_url + 'reconcile/getCheckBookData',
            data: this.getPostData(),
            method: 'POST',
            success: function(response) {
                $('#check_book_modal').html(response);
                $('#check_book_modal').modal({
                    backdrop: 'static',
                    keyboard: true,
                    show: true
                });
                $("#loadingmessage").hide();
            }
        });
    }

    addEditChecks(r)
    {
        $("#loadingmessage").show();
        var checkId = jQuery(r).attr('checkid');
        this.pData = this.getPostData();
        var formData = {
            checkid: checkId
        };
        jQuery.extend(this.pData, formData);
        $.ajax({
            url: site_url + 'reconcile/addEditChecks',
            data: this.pData,
            method: 'POST',
            success: function(response) {
                $('#add_edit_check_book_modal').html(response);
                $('#add_edit_check_book_modal').modal({
                    backdrop: 'static',
                    keyboard: true,
                    show: true
                });
                $("#loadingmessage").hide();
            }
        });
    }

    submitCheckData()
    {
        if(!jQuery("#frm_add_checks").valid())
            return false;

        $("#loadingmessage").show();
        $.ajax({
            url: site_url + 'reconcile/submitCheck',
            data: jQuery("#frm_add_checks").serialize(),
            method: 'POST',
            success: function(response) {
                $("#add_edit_check_book_modal").modal('hide');
                $('#check_book_modal').html(response);
                $('#check_book_modal').modal({
                    backdrop: 'static',
                    keyboard: true,
                    show: true
                });
                $("#loadingmessage").hide();
            }
        });
    }

    deleteChecks(r)
    {
        $("#loadingmessage").show();
        var checkId = jQuery(r).attr('checkid');
        $.ajax({
            url: site_url + 'reconcile/deleteChecks',
            data: {checkid: checkId},
            method: 'POST',
            success: function(response) {
                jQuery(r).closest('tr').remove();
                $("#loadingmessage").hide();
            }
        });
    }

    voidChecks(r)
    {
        $("#loadingmessage").show();
        var checkId = jQuery(r).attr('checkid');
        var amount = jQuery(r).attr('amount');
        var memo = jQuery(r).attr('memo');
        this.pData = this.getPostData();
        
        var formData = {
            checkid: checkId,
            amount: amount,
            memo: memo
        };
        var formData = {
            checkid: checkId,
            amount: amount,
            memo: memo
        };
        
        jQuery.extend(this.pData, formData);
        $.ajax({
            url: site_url + 'reconcile/voidChecks',
            data: this.pData,
            method: 'POST',
            success: function(response) {
                $('#check_book_modal').html(response);
                $('#check_book_modal').modal({
                    backdrop: 'static',
                    keyboard: true,
                    show: true
                });
                $("#loadingmessage").hide();
            }
        });
    }

    unReconcileCheck(r)
    {
        $("#confirm_unreconciled").data("checkid", jQuery(r).attr('checkid'));
        $("#confirm_unreconciled").data("bank_statement_id", jQuery(r).attr('bankstatementid'));
        $("#unreconciled_Modal").modal("show");
    }

    bankEntryType(r)
    {
        var checkNum = $(r).closest("tr").find('input[name="manual-bank-check_num"]').val();
        var checkType = $(r).closest("tr").find('select[name="manual-bank-transaction-type"]').val();
        if(checkType == 'check' && checkNum != 0 && checkNum != ''){
            $(r).closest("tr").find('input[name="manual-bank-description"]').val('Check');
            $(r).closest("tr").find('input[name="manual-bank-description"]').addClass('non-editable');
        }
        else
        {
            $(r).closest("tr").find('input[name="manual-bank-description"]').removeClass('non-editable');
        }
    }

    showLedgerView(r)
    {
        if($(r).prop("checked") == true) {
            localStorage.setItem("ledger_view", 1);
            $("#loadingmessage").show();
            $('.ledger_div').hide();
            $.ajax({
                url: site_url + 'reconcile/showLedgerView',
                data: this.getPostData(),
                method: 'POST',
                success: function(response) {
                    $('.ledger_view_div').html(response);
                    $('.ledger_view_div').show();
                    $("#loadingmessage").hide();
                }
            });
        } else {
            localStorage.setItem("ledger_view", 0);
            $('.ledger_div').show();
            $('.ledger_view_div').html('');
            $('.ledger_view_div').hide();
        }
    }

    refreshStatus()
    {
        $.ajax({
            url: site_url + 'reconcile/refreshStatus',
            data: this.getPostData(),
            method: 'POST',
            success: function(response) {
                location.reload();
            }
        });
    }
}

$('#check_book_modal').on('hidden.bs.modal', function () {
    location.reload();
})

$(document).on("click", ".close-popover", function () {
    $(this).closest('div.popover').popover('hide');
});


// show toolip
$(document).on("click", ".reconciled-info", function () {
    var reconciledIds = $(this).attr('reconciled-ids');
    var bankType = $(this).attr('type');
    var reconciliationType = $(this).attr('reconciliation-type');
    var reconciledElement = this;
    $.ajax({
        url: site_url + 'reconcile/get_reconciled_info',
        type: "POST",
        data: {"reconciled_ids": reconciledIds, "bank_type": bankType, "reconciliation_type": reconciliationType},
        success: function (responseText) {
            var Res = JSON.parse(responseText);
            $("#loadingmessage").hide();
            if ($('.popover').hasClass('in')) {
                $('.popover').popover('hide');
            }
            $('.popover').popover('hide');
            $(reconciledElement).attr('data-content', Res.reconcileHtml);
            $(reconciledElement).attr('title', '<a class="close-popover pull-right"><i class="fa fa-times" aria-hidden="true"></i></a>');
            $(reconciledElement).attr('html', true);
            $(reconciledElement).popover({html: true}).popover('show');

            var reconciledIdsAry = reconciledIds.split(',');
        }
    });
});

//scrolling JS
$(document).on("click", ".reconciled-info-class", function () {
    var scrollId = $(this).attr('scroll-to-id');
    $(".reconciled-class").removeClass('highlight');
    $(".blue-reconciled-class").removeClass('highlight');
    $(".adjustment-class").removeClass('highlight');
    $("#" + scrollId).addClass('highlight');
    $('html, body').animate({
        scrollTop: $("#" + scrollId).offset().top - 300
    }, 2000);
});
$(document).on("click", "#unreconciled_manual_bank_wrapper .bank-checkbox", function () {
    $("#manual_bank_checked_count").html($('.bank-checkbox:checked').length);
    if (!$("input[name='primary_selection']:checked").val())
    {
        alert("Please select if you want to reconcile ledger wise or bank wise");
        return false;
    }

    if ($("input[name='primary_selection']:checked").val() == 'bank_wise')
    {
        $('.bank-checkbox:checked').removeAttr('checked');
        $('.bank-checkbox').closest('tr').removeClass("highlight_row");
        $('.bank-checkbox').closest('tr').find(".manual-edit").removeClass("non-editable");
        $(this).closest('tr').find(".manual-edit").addClass("non-editable");
        $(this).prop("checked", true);
        $(this).closest('tr').addClass("highlight_row");
        $("#manual_bank_balance span").html(0);
        amt = parseFloat($(this).attr('amt'));
    } else {
        var amt = $("#manual_bank_balance span").html();
        if (this.checked)
        {
            $(this).closest('tr').addClass("highlight_row");
            $(this).closest('tr').find(".manual-edit").addClass("non-editable");
            amt = parseFloat(amt) + parseFloat($(this).attr('amt'));
        } else
        {
            $(this).closest('tr').find(".manual-edit").removeClass("non-editable");
            $(this).closest('tr').removeClass("highlight_row");
            amt = parseFloat(amt) - parseFloat($(this).attr('amt'));
        }
    }
    $("#manual_bank_balance span").html(amt.toFixed(2));
    $("#txt_manual_bank_balance").val(amt.toFixed(2));
    recon.enableLockBtn();
});

$(document).on("click", "#unreconciled_manual_ledger_wrapper .ledger-checkbox", function () {

    $("#manual_ledger_checked_count").html($('.ledger-checkbox:checked').length);
    //validation
    if (!$("input[name='primary_selection']:checked").val())
    {
        alert("Please select if you want to reconcile ledger wise or bank wise");
        return false;
    }

    //check - uncheck
    if ($("input[name='primary_selection']:checked").val() == 'ledger_wise')
    {
        $('.manual_reconcile_cover .ledger-checkbox:checked').removeAttr('checked');
        $('.ledger-checkbox').closest('tr').removeClass("highlight_row");
        $('.ledger-checkbox').closest('tr').find(".manual-edit").removeClass("non-editable");
        $(this).prop("checked", true);
        $(this).closest('tr').find(".manual-edit").addClass("non-editable");
        $(this).closest('tr').addClass("highlight_row");
        $("#manual_ledger_balance span").html(0);
        amt = parseFloat($(this).attr('amt'));
    } else
    {
        var amt = $("#manual_ledger_balance span").html();
        if (this.checked)
        {
            $(this).closest('tr').find(".manual-edit").addClass("non-editable");
            $(this).closest('tr').addClass("highlight_row");
            amt = parseFloat(amt) + parseFloat($(this).attr('amt'));
        } else
        {
            $(this).closest('tr').find(".manual-edit").removeClass("non-editable");
            $(this).closest('tr').removeClass("highlight_row");
            amt = parseFloat(amt) - parseFloat($(this).attr('amt'));
        }
    }

    $("#manual_ledger_balance span").html(amt.toFixed(2));
    $("#txt_manual_ledger_balance").val(amt.toFixed(2));


    if (jQuery(this).hasClass('credit_card_total_class'))
    {
        console.log("come1");
        $('.manual_reconcile_cover .bank-checkbox:checked').removeAttr('checked');
        $("#manual_bank_balance span").html(0);
        jQuery(".credit_card_total_bank_class").trigger('click');
    }
    recon.enableLockBtn();
});

$(document).on("click", ".primary_selection", function () {
    $('.bank-checkbox:checked').removeAttr('checked');
    $('.manual_reconcile_cover .ledger-checkbox:checked').removeAttr('checked');
    $("#manual_ledger_balance span").html(0.00);
    $("#manual_bank_balance span").html(0.00);
});

$(document).on("change", 'select[name="manual-ledger-transaction-type"]', function () {
    var selectedDocumentType = jQuery(this).closest('tr').find(".manual-ledger-document-type").val();
    recon.make_field_editable(selectedDocumentType, this);
});

$('#manual_reconcilation_modal').on('hidden.bs.modal', function () {
    location.reload();
})

$(document).on("change", ".manual-input", function () {
    $(this).prev('.manual-label').text(this.value);
});

$(document).ready(function () {
    $(document).on('click', '#unreconciled_manual_ledger tr', function (event) {
        if (event.target.type !== 'checkbox') {
            $(':checkbox', this).trigger('click');
        }
    });

    $('#unreconciled_manual_ledger tr').on('click', 'td:last-child', function (e) {
        return false;
    });

    $('#unreconciled_manual_ledger tr').on('click', '.manual-input', function (e) {
        return false;
    });

    $(document).on('click', '#unreconciled_manual_bank tr', function (event) {
        if (event.target.type !== 'checkbox') {
            $(':checkbox', this).trigger('click');
        }
    });

    $('#unreconciled_manual_bank tr').on('click', 'td:last-child', function (e) {
        return false;
    });

    $('#unreconciled_manual_bank tr').on('click', '.manual-input', function (e) {
        return false;
    });
});


$(document).on("change", 'select[name="manual-ledger-document-type"]', function () {
    var selectedDocumentType = this.value;
    var ledger_id = $(this).attr("ledgerid");
    var transaction_type = jQuery(this).closest('tr').find('.manual-ledger-transaction-type').val();
    var currentObj = this;
    $("#loadingmessage").show();
    $.ajax({
        url: site_url + 'reconcile/check_document_type',
        type: "POST",
        data: {"selected_document_type": selectedDocumentType, "ledger_id": ledger_id, "transaction_type": transaction_type},
        success: function (responseText) {
            $("#loadingmessage").hide();
            var Res = JSON.parse(responseText);
            if (selectedDocumentType)
            {
                recon.make_field_editable(selectedDocumentType, currentObj);
            }

            if (!Res.status) {
                alert("No more entries allowed for " + Res.value);
                jQuery(currentObj).closest('tr').find(".manual-save").addClass('non-editable');
                return false;
            } else {
                jQuery(currentObj).closest('tr').find(".manual-save").removeClass('non-editable');
            }
        }
    });
});

$(document).on("click", ".delete-row", function () {
    $(this).parents("tr").remove();
});