<link href="<?php echo base_url(); ?>assets/css/reconcile.css" rel="stylesheet" type="text/css"/>
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
        <div class="btn-group pull-right">
            <a href="" class="dropdown-toggle color-icon" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="true"><i class="fa fa-paint-brush"></i></a>
            <ul class="dropdown-menu pull-right">
                <li>
                    <div class="highlight_row color-row">Highlight Row - MAnual Reconcilation popup</div>
                </li>
                <li>
                    <div class="reconciled-class color-row">Current reconciled</div>
                </li>
                <li>
                    <div class="blue-reconciled-class color-row">Previous reconciled</div>
                </li>
                <li>
                    <div class="adjustment-class color-row">Adjustment Entry</div>
                </li>
                <li>
                    <div class="orange-reconciled-class color-row">Next reconciled</div>
                </li>
                <li>
                    <div class="gray-reconciled-class color-row">zero amount</div>
                </li>
                <li>
                    <div class="highlight color-row">highlight</div>
                </li>
            </ul>
        </div>
        <a href="javascript:void(0)" class="pull-right note-icon notes_modal" title="Add Message"><i class="fa fa-edit"></i></a>
    </div>
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-12">
                <a class="btn btn-danger  pull-right ml10 mb10 <?php echo $is_locked[0]->islocked ? 'btn-disabled' : ''; ?>" href="javascript:void(0);" data-toggle="modal" data-ledger-id="<?php echo $ledger_id; ?>" data-bank-statement-id="<?php echo $bank_id; ?>" onclick="setConfirmReset(this, 'reset')" data-target="#ConfirmDeleteModal" data-url="<?php echo base_url("reconcile/reset/" . $ledger_id . "/" . $bank_id); ?>">Reset</a>
                <a class="btn blue  pull-right ml10 mb10 <?php echo $is_locked[0]->islocked ? 'btn-disabled' : ''; ?>" href="javascript:void(0);" data-toggle="modal" onclick="setConfirmReset(this, 'auto_reconcile')" data-target="#ConfirmDeleteModal" data-url="<?php echo base_url('reconcile/auto?ledger_id=' . $ledger_id . "&bank_id=" . $bank_id); ?>">Auto Reconcile</a>
                <a class="btn blue  pull-right ml10 mb10 <?php echo $is_locked[0]->islocked ? 'btn-disabled' : ''; ?>" onclick="recon.getManualReconcilationData()">Manual Reconcile</a>
                <a class="btn blue  pull-right ml10 mb10 <?php echo $is_locked[0]->islocked ? 'btn-disabled' : ''; ?>"  data-toggle="modal" data-target="#adjustmnt_entries">Adjustment</a>
                <a class="btn blue  pull-right ml10 mb10 <?php echo $is_locked[0]->islocked ? 'btn-disabled' : ''; ?>" onclick="recon.getCheckBookData()">Checks</a>
                <a class="btn blue  pull-right ml10 mb10" onclick="recon.refreshStatus()">Rrfresh Status</a>
                <div class="bootstrap-switch bootstrap-switch-wrapper bootstrap-switch-off bootstrap-switch-animate">
                    <div class="bootstrap-switch-container">
                        <input id="viewToggle" type="checkbox" class="make-switch" data-off-color="danger" data-on-color="warning" data-on-text="&nbsp;ExcelView&nbsp;" data-off-text="&nbsp;SimpleView&nbsp;" onchange="recon.showLedgerView(this)">
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th><b>Type</b></th>
                            <th><b>Status</b></th>
                            <th><b>Total</b></th>
                            <th><b>Credit</b></th>
                            <th><b>Debit</b></th>
                            <th><b>Ignored</b></th>
                            <th><b>Credit Amount</b></th>
                            <th><b>Debit Amount</b></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (isset($leder_info) && !empty($leder_info)) {
                            ?>
                            <tr>
                                <th>Ledger</th>
                                <td><?php echo ($leder_info[0]->status != '') ? getStatusLabel($leder_info[0]->status) : ''; ?></td>
                                <td><?php echo ($leder_info[0]->total_reconciled + $check_info[0]->total_check_debit) . ' / ' . ($leder_info[0]->total + $check_info[0]->total); ?></td>
                                <td><?php echo ($leder_info[0]->total_reconciled_credit) . ' / ' . ($leder_info[0]->total_credit); ?></td>
                                <td><?php echo ($leder_info[0]->total_reconciled_debit + $check_info[0]->total_check_debit) . ' / ' . ($leder_info[0]->total_debit + $check_info[0]->total); ?></td>
                                <td><?php echo ($ignored_ledger[0]->total); ?></td>
                                <td><?php echo ($leder_info[0]->total_reconciled_credit_amt) . ' / ' . ($leder_info[0]->total_credit_amt); ?></td>
                                <td><?php echo ($leder_info[0]->total_reconciled_debit_amt + $check_info[0]->total_check_reconciled_amt) . ' / ' . ($leder_info[0]->total_debit_amt + $check_info[0]->total_check_amt); ?></td>
                            </tr>
                            <tr>
                            <tr>
                                <th>Bank</th>
                                <td><?php echo ($bank_info[0]->status != '') ? getStatusLabel($bank_info[0]->status) : ''; ?></td>
                                <td><?php echo ($bank_info[0]->total_reconciled) . ' / ' . ($bank_info[0]->total); ?></td>
                                <td><?php echo ($bank_info[0]->total_reconciled_credit) . ' / ' . ($bank_info[0]->total_credit); ?></td>
                                <td><?php echo ($bank_info[0]->total_reconciled_debit) . ' / ' . ($bank_info[0]->total_debit); ?></td>
                                <td>-</td>
                                <td><?php echo ($bank_info[0]->total_reconciled_credit_amt) . ' / ' . ($bank_info[0]->total_credit_amt); ?></td>
                                <td><?php echo ($bank_info[0]->total_reconciled_debit_amt) . ' / ' . ($bank_info[0]->total_debit_amt); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mt20 ledger_view_div" style="display: none;">
            </div>
            <div class="col-md-6 mt20 ledger_div">
                <div id="ledger_data_div"></div>
                <div id="checkbook_data_div"></div>
                <div id="credit_received_from_data_div"></div>
                <div id="previous_ledger_data_div"></div>
                <div id="previous_check_data_div"></div>
            </div>
            <div class="col-md-6 mt20 bank_div">
                <div id="bank_data_div"></div>
                <div id="previous_bank_data_div"></div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Manual Reconcilation -->
<div class="modal fade" id="manual_reconcilation_modal" tabindex="-1" role="dialog" aria-hidden="true">
</div>

<!-- Modal for Check Book Records -->
<div class="modal fade" id="check_book_modal" tabindex="-1" role="dialog" aria-hidden="true">
</div>

<!-- Modal for Check Book add/edit -->
<div class="modal fade" id="add_edit_check_book_modal" tabindex="-1" role="dialog" aria-hidden="true">
</div>

<!-- Modal for Adjust Entries Reconcilation -->
<div class="modal fade" id="adjustmnt_entries" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-9">
                    <h3 class="modal-title"><b>Adjustment Entries</b></h3>
                </div>
                <div class="col-md-2">
                    Total Entries: <?php echo count($all_unreconciled_ledger); ?>
                </div>
                <div class="col-md-1">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 mt20">
                        <table class='table table-bordered' id="reversed_adjustment_tbl">
                            <tbody>
                            </tbody>
                        </table>
                        <?php
                        if (isset($all_unreconciled_ledger)) {
                            ?>
                            <table class='table table-bordered' id="adjustmnt_entries_table">
                                <thead>
                                    <tr>
                                        <th width="1%"></th>
                                        <th width="5%"><b>Year</b></th>
                                        <th width="5%"><b>Month</b></th>
                                        <th width="5%"><b>Date</b></th>
                                        <th width="5%"><b>Type</b></th>
                                        <th width="40%"><b>Description</b></th>
                                        <th width="1%"><b>Amount</b></th>
                                    </tr>
                                </thead>
                                <tfoot>
                                    <tr>
                                        <th width="1%"></th>
                                        <th width="5%"><b>Year</b></th>
                                        <th width="5%"><b>Month</b></th>
                                        <th width="5%"><b>Date</b></th>
                                        <th width="5%"><b>Type</b></th>
                                        <th width="40%"><b>Description</b></th>
                                        <th width="1%"><b>Amount</b></th>
                                    </tr>
                                </tfoot>
                                <?php
                                foreach ($all_unreconciled_ledger as $_all_unreconciled_ledger) {
                                    ?>
                                    <tr>
                                        <td>
                                            <input onchange="display_reverse_entry(this);" type="checkbox" name="ledger_adjustment[]" value="<?php echo $_all_unreconciled_ledger->id; ?>" class="ledger_adjustment" id="chk-adjust-<?php echo $_all_unreconciled_ledger->id; ?>"/>
                                        </td>
                                        <td><?php echo $_all_unreconciled_ledger->year; ?></td>
                                        <td><?php echo monthName($_all_unreconciled_ledger->month); ?></td>
                                        <td>
                                            <?php echo ($_all_unreconciled_ledger->credit_date) ? date('m/d/Y', strtotime($_all_unreconciled_ledger->credit_date)) : ''; ?>
                                        </td>
                                        <td class="adj_transaction_cls">
                                            <?php echo $_all_unreconciled_ledger->transaction_type; ?>
                                        </td>
                                        <td class="adj_description_cls">
                                            <?php echo $_all_unreconciled_ledger->description; ?>
                                        </td>
                                        <td class="adj_amount_cls">
                                            <?php echo isset($_all_unreconciled_ledger->transaction_type) && $_all_unreconciled_ledger->transaction_type == 'credit' ? "<span class='text-success'>" . $_all_unreconciled_ledger->credit_amt . "</span>" : "<span class='text-danger'>" . $_all_unreconciled_ledger->debit_amt . "</span>"; ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </table>
                            <?php
                        }
                        ?>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input data-dismiss="modal" class="btn btn-danger pull-right ml10" type="button" id="record_changes" value="Cancel">
                <input class="btn btn-success pull-right" type="button" id="btn_ledger_adjustment" value="Adjust" onclick="ledger_adjustment(this);">
            </div>
        </div>
    </div>
</div>

<!-- notes moda -->
<div class="shownotesmodal"></div>

<!--delete project confirm Modal-->
<div class="modal fade" id="unreconciled_Modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Unreconciled Entry</h4>
            </div>
            <div class="modal-body">
                <p><b>WARNING:</b> Are you sure you want to unreconciled this entry?</p>
            </div>
            <div class="modal-footer">
                <input data-dismiss="modal" class="btn btn-danger pull-right ml10" type="button" id="record_changes" value="Cancel">
                <input class="btn btn-success pull-right" type="button" id="confirm_unreconciled" value="Confirm" data-id=""  data-bank_statement_id = "" data-reconcilation_type = "" onclick="confirm_unreconciled(this);">
            </div>
        </div>
    </div>
</div>

<script src="<?php echo base_url(); ?>assets/js/reconcile.js" type="text/javascript"></script>
<script type="text/javascript">
                    var ledgerId = '<?php echo $ledger_id ?>';
                    var bankId = '<?php echo $bank_id ?>';
                    var storeKey = '<?php echo $ledger->store_key ?>';
                    var month = '<?php echo $ledger->month ?>';
                    var year = '<?php echo $ledger->year ?>';
                    let recon = new Reconcile(ledgerId, bankId, storeKey, month, year);
                    recon.getLedgerData();
                    recon.getCheckData();
                    recon.getBankData();
                    recon.getCreditReceivedFromData();
                    recon.getPreviousLedgerData();
                    recon.getPreviousCheckData();
                    recon.getPreviousBankData();
</script>
<script type="text/javascript">
    function show_unreconcile_modal(ledger_id, bank_statement_id, reconcilation_type) {
        $("#confirm_unreconciled").data("id", ledger_id);
        $("#confirm_unreconciled").data("bank_statement_id", bank_statement_id);
        $("#confirm_unreconciled").data("reconcilation_type", reconcilation_type);
        $("#unreconciled_Modal").modal("show");
    }

    function confirm_unreconciled(e) {
        $("#loadingmessage").show();
        var ledger_id = $(e).data("id");
        var checkid = $(e).data("checkid");
        var bank_statement_id = $(e).data("bank_statement_id");
        var reconcilation_type = $(e).data("reconcilation_type");
        $.ajax({
            url: site_url + 'reconcile/unreconciled_entry',
            type: "POST",
            data: {ledger_id: ledger_id, bank_statement_id: bank_statement_id, checkid: checkid, reconcilation_type: reconcilation_type},
            success: function (responseText) {
                var Res = JSON.parse(responseText);
                if (Res.status == 'success') {
                    $("#loadingmessage").hide();
                    $("#unreconciled_Modal").modal("hide");
                    location.reload();
                }
            }
        });
    }

    function ledger_adjustment(e) {
        var ledger_adjustment = [];
        var ledger_adjustment_desc = [];
        $("input[name='adjustment_ids[]']").each(function () {
            ledger_adjustment.push($(this).val());
            ledger_adjustment_desc.push($(this).parents("tr").children("td:eq(4)").children("input").val());
        });

        var ledger_id = '<?php echo isset($ledger->id) ? $ledger->id : ''; ?>';
        $("#loadingmessage").show();
        $.ajax({
            url: site_url + 'reconcile/ledger_adjustment',
            type: "POST",
            data: {ledger_adjustment: ledger_adjustment, ledger_adjustment_desc: ledger_adjustment_desc, ledger_id: ledger_id},
            success: function (responseText) {
                var Res = JSON.parse(responseText);
                if (Res.status == 'success') {
                    // $("#loadingmessage").hide();
                    location.reload();
                }
            }
        });
    }

    function display_reverse_entry(element) {
        if (element.checked)
        {
            var id = $(element).val();
            var d = new Date();
            var month = d.getMonth() + 1;
            var day = d.getDate();
            var output = (day < 10 ? '0' : '') + day + '/' + (month < 10 ? '0' : '') + month + '/' + d.getFullYear();
            var trans_type = $(element).parents("tr").children("td:eq(4)").html();

            if ($.trim(trans_type) == 'credit') {
                display_trans_type = 'debit';
                display_amount_cls = '<span class="text-danger">' + $(element).parents("tr").children("td:eq(6)").find("span").html() + '</span>';
            } else {
                display_trans_type = 'credit';
                display_amount_cls = '<span class="text-success">' + $(element).parents("tr").children("td:eq(6)").find("span").html() + '</span>';
            }

            var ledger_id = '<?php echo isset($ledger->id) ? $ledger->id : ''; ?>';
            $("#loadingmessage").show();
            $.ajax({
                url: site_url + 'reconcile/check_document_type',
                type: "POST",
                data: {"selected_document_type": "general_section", "ledger_id": ledger_id, "transaction_type": display_trans_type},
                success: function (responseText) {
                    $("#loadingmessage").hide();
                    var Res = JSON.parse(responseText);
                    if (!Res.status) {
                        $(element).prop("checked", false);
                        alert("No more entries allowed for " + Res.value);
                        return false;
                    } else {
                        var td_html1 = "<td>" + $(element).parents("tr").children("td:eq(1)").html() + "</td>";
                        var td_html2 = "<td>" + $(element).parents("tr").children("td:eq(2)").html() + "</td>";
                        var td_html3 = "<td>" + output + "</td>";
                        var td_html4 = "<td>" + display_trans_type + "</td>";
                        var td_html5 = "<td><input class='form-control description' value='" + $.trim($(element).parents("tr").children("td:eq(5)").html()) + "-ADJ'></td>";
                        var td_html6 = "<td>" + display_amount_cls + "</td>";
                        var td_html7 = "<td><a href='javascript:void(0)' onclick='recon.remove_entries_from_adjustment(this);'><span class='fa-icons fa fa-times-circle fa-2x'></span></a></td>";
                        final_html = "<tr id='adjust-" + id + "'><input type='hidden' name='adjustment_ids[]' value = '" + id + "'>" + td_html1 + td_html2 + td_html3 + td_html4 + td_html5 + td_html6 + td_html7 + "</tr>";
                        $("#reversed_adjustment_tbl tbody").append(final_html);
                    }
                }
            });
        } else {
            $("#adjust-" + element.value).remove();
        }
    }
    $(document).ready(function () {
        if (localStorage.getItem("ledger_view") == 1 || !localStorage.getItem("ledger_view"))
        {
            $("#viewToggle").prop('checked', true);
            $("#viewToggle").trigger("change");
        }
        // Setup - add a text input to each footer cell
        $('#adjustmnt_entries_table tfoot th').each(function () {
            var title = $(this).text();
            $(this).html('<input type="text"  style="width:100%;" class="adjustmnt-entries-input" placeholder="Search ' + title + '" />');
        });

        // DataTable
        var adjustmnt_entries_datatable = $('#adjustmnt_entries_table').DataTable({
            "paging": false,
            "info": false,
            "stripeClasses": [],
            "autoWidth": false
        });

        // Apply the search
        adjustmnt_entries_datatable.columns().every(function () {
            var that = this;
            $('.adjustmnt-entries-input', this.footer()).on('keyup change clear', function () {
                if (that.search() !== this.value) {
                    that
                            .search(this.value)
                            .draw();
                }
            });
        });
    });

    function void_bank_entry(element) {
        $("#loadingmessage").show();
        var void_val = 0;
        if ($(element).is(":checked")) {
            void_val = 1;
        }
        var value = $(element).val();
        if (value != '') {
            $.ajax({
                url: site_url + 'bank/void_entry',
                type: "POST",
                data: {bank_statement_id: value, void_val: void_val},
                success: function (responseText) {
                    $("#loadingmessage").hide();
                    var Res = JSON.parse(responseText);
                    if (Res.status == 'success') {
                        location.reload();
                    }
                }
            });
        }
    }

    function setConfirmReset(value, type) {
        if (type == 'auto_reconcile') {
            var reconcile_url = $(value).attr("data-url");
            $("#ConfirmDeleteModal").find(".modal-body").children("p").show();
            $("#ConfirmDeleteModal").find(".confirmYes").show();
            $("#ConfirmDeleteModal").find(".modal-title").html("Auto Reconcilation");
            $("#ConfirmDeleteModal").find(".modal-body").children("p").html("Do you really want to perform auto reconcile");
            $("#ConfirmDeleteModal").find("#ConfirmDelete").attr('href', reconcile_url);
        } else {
            var delete_url = $(value).attr("data-url");
            $("#ConfirmDeleteModal").find(".modal-body").children("p").show();
            $("#ConfirmDeleteModal").find(".confirmYes").show();
            $("#ConfirmDeleteModal").find(".modal-title").html("Bank Statement Deletion");
            $("#ConfirmDeleteModal").find(".modal-body").children("p").html("Do you really want to reset this process");
            $("#ConfirmDeleteModal").find("#ConfirmDelete").attr('href', delete_url);
        }

    }

    $(document).on("click", ".notes_modal", function () {
        var ledger_id = '<?php echo isset($ledger->id) ? $ledger->id : ''; ?>';
        $("#loadingmessage").show();


        $.ajax({
            url: '<?php echo base_url("reconcile/getnotes") ?>',
            type: 'POST',
            data: {
                'ledger_id': ledger_id,
            },
            dataType: 'json',
            success: function (data) {
                html = "";
                // alert(data);
                html += '<div class="modal fade" id="notes_modal" role="dialog">' +
                        '<div class="modal-dialog">' +
                        '<div class="modal-content">' +
                        '<div class="modal-header">' +
                        '<button type="button" class="close" data-dismiss="modal">&times;</button>' +
                        '<h4 class="modal-title"><b>Notes</b></h4>' +
                        '</div>' +
                        '<div class="modal-body" >' +
                        '<div class="success-msg"></div><div class="form-group"><div class="row"><div class="col-md-12"><textarea rows=5 cols=50 name="notes" id="notes" placeholder="Enter Notes" class="form-control">' + data + '</textarea></div></div></div>' +
                        '</div>' +
                        '<div class="modal-footer">' +
                        '<button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>' +
                        '<button type="button" class="btn btn-warning" onclick="clear_note()">Clear</button>' +
                        '<button type="button" class="btn btn-primary" onclick="updatesnote()">Update</button>' +
                        '</div>' +
                        '</div>' +
                        '</div>';

                $(".shownotesmodal").html(html);
                $("#notes_modal").modal("show");
                $("#loadingmessage").hide();
            },
            error: function (request, error)
            {
                $("#loadingmessage").hide();
            }
        });

    });

    function updatesnote() {
        var ledger_id = '<?php echo isset($ledger->id) ? $ledger->id : ''; ?>';
        $("#loadingmessage").show();
        $.ajax({
            url: '<?php echo base_url("reconcile/setnotes") ?>',
            type: 'POST',
            data: {
                'ledger_id': ledger_id,
                'notes': $("#notes").val(),
                'bank_id': <?php echo $bank_id ?>
            },
            dataType: 'json',
            success: function (data) {
                html = "";
                html += '<div class="alert alert-success alert-dismissable">' +
                        '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>' +
                        data + '</div>';
                $(".success-msg").html(html);
                $("#notes_modals").modal('hide');
                location.reload();
            },
            error: function (request, error)
            {
            }
        });
    }
    function clear_note() {
        $("#notes").val("");
    }

    $('#adjustmnt_entries tr').click(function (event) {
        if (event.target.type !== 'checkbox') {
            $(':checkbox', this).trigger('click');
        }
    });

</script>