/**
 Custom module for you to write your own javascript functions
 **/
$.fn.serializeObject = function (obj) {
    //var obj = {};

    $.each(this.serializeArray(), function (i, o) {
        var n = o.name, v = o.value;

        obj[n] = obj[n] === undefined ? v
                : $.isArray(obj[n]) ? obj[n].concat(v)
                : [obj[n], v];
    });

    return obj;
};
$(document).ready(function () {
    $('form.validate').each(function () {
        var error1 = $('.alert-danger', $(this));
        $(this).validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            focusInvalid: false, // do not focus the last invalid input
            ignore: "", // validate all fields including form hidden input
            highlight: function (element) { // hightlight error inputs
                $(element)
                        .closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                        .closest('.form-group').removeClass('has-error'); // set error class to the control group
            },
            success: function (label) {
                label
                        .closest('.form-group').removeClass('has-error'); // set success class to the control group
            },
            invalidHandler: function (event, validator) { //display error alert on form submit
                error1.show();
                App.scrollTo(error1, -200);
            },
            errorPlacement: function (error, element) { // render error placement for each input type
                if (element.parent(".input-group").size() > 0) {
                    error.insertAfter(element.parent(".input-group"));
                } else if (element.attr("data-error-container")) {
                    error.appendTo(element.attr("data-error-container"));
                } else if (element.parents('.radio-list').size() > 0) {
                    error.appendTo(element.parents('.radio-list').attr("data-error-container"));
                } else if (element.parents('.radio-inline').size() > 0) {
                    error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
                } else if (element.parents('.checkbox-list').size() > 0) {
                    error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
                } else if (element.parents('.checkbox-inline').size() > 0) {
                    error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
                } else {
                    error.insertAfter(element); // for other inputs, just perform default behavior
                }
            }
        });
    });
    $('#confirmModal').on('show.bs.modal', function (e) {
        $(this).find('.confirmYes').data('id', $(e.relatedTarget).data('id'));
    });
    $('#confirmModal').on('hidden.bs.modal', function () {
        $(this).find('.confirmYes').data('id', '0');
        $(this).find(".confirmError").addClass('display-hide');
        $(this).find(".confirmError .error-msg").text('');
    });
    $("#frmSearch .form-control:not(.date-range)").change(function () {
        $("#btnSearch").trigger("click");
    });
    $('#btnSearch').click(function () {
        var oTable = $('#tblListing').DataTable();
        oTable.ajax.reload();
    });
    $("#confirmModal .confirmYes").click(function () {
//        $.ajax({
//            url     : App.getCurrentClass()+'/delete/'+$(this).data('id'),
//            type    : "POST",
//            success : function(responseText) {
//                var response = JSON.parse(responseText);
//                if(response.status === "success") {
//                    $('#confirmModal').modal('hide');
//                    $('#tblListing').DataTable().row('.selected').remove().draw( false );
//                } else {
//                    $("#confirmModal .confirmError .error-msg").text(response.errormsg);
//                    $("#confirmModal .confirmError").removeClass("display-hide");
//                }
//            } 
//        });
    });
    $("#customModal").on('shown.bs.modal', function () {
        $("#customModal .date-picker").datepicker({
            rtl: App.isRTL(),
            orientation: "left",
            autoclose: true
        });
    });
    $(document).scroll(function () {
        $('#customModal .date-picker').datepicker('place'); //#modal is the id of the modal
    });

});
function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}
function isDecimal(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57) && charCode !== 46) {
        return false;
    }
    return true;
}
var Custom = function () {

    // private functions & variables

    var initListingTable = function (cOptions) {
        var table = $('#tblListing');

        /* Table tools samples: https://www.datatables.net/release-datatables/extras/TableTools/ */

        /* Set tabletools buttons and button container */

        var oTable = table.dataTable({
            serverSide: true,
            processing: true,
            // Internationalisation. For more info refer to http://datatables.net/manual/i18n
            language: {
                aria: {
                    sortAscending: ": activate to sort column ascending",
                    sortDescending: ": activate to sort column descending"
                },
                emptyTable: "No data available",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "No entries found",
                infoFiltered: "",
                lengthMenu: "Show _MENU_ entries",
                search: "Search:",
                zeroRecords: "No matching records found"
            },
            // Or you can use remote translation file
            //language: {
            //   url: '//cdn.datatables.net/plug-ins/3cfcc339e89/i18n/Portuguese.json'
            //},
            searching: false,
            order: cOptions.order,
            rowGroup: cOptions.rowGroup,
            drawCallback: cOptions.drawCallback,
            lengthMenu: [
                [5, 25, 50, 100, -1],
                [5, 25, 50, 100, "All"] // change per page values here
            ],
            // set the initial value
            pageLength: 25,
            dom: "<'row' <'col-md-12'B>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
            fnRowCallback: function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                var index = oSettings._iDisplayStart + iDisplayIndex + 1;
//                $('td:eq(0)',nRow).html(index);
                return nRow;
            },
            ajax: {
                type: "POST",
                url: cOptions.url,
                data: function (obj) {
                    $("#frmSearch").serializeObject(obj);
                },
                dataSrc: function (json) {
                    if ($("#tot_balance").length) {
                        $("#tot_balance").html(json.tot_balance);
                    }
                    if ($("#tot_remain_balance").length) {
                        $("#tot_remain_balance").html(json.tot_remain_balance);
                    }
                    return json.data;
                }
            },
            columnDefs: cOptions.columnDefs,
            // Uncomment below line("dom" parameter) to fix the dropdown overflow issue in the datatable cells. The default datatable layout
            // setup uses scrollable div(table-scrollable) with overflow:auto to enable vertical scroll(see: assets/global/plugins/datatables/plugins/bootstrap/dataTables.bootstrap.js). 
            // So when dropdowns used the scrollable div should be removed. 
            //dom: "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r>t<'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>",

            buttons: [
            ]
        });
        $('#store_id').change(function () {
            oTable.fnFilter($(this).val());
        });
        $('#month').change(function () {
            oTable.fnFilter($(this).val());
        });
        $('#year').change(function () {
            
            alert("DFd");
            oTable.fnFilter($(this).val());
        });
        var tableWrapper = $('#tblListing_wrapper'); // datatable creates the table wrapper by adding with id {your_table_jd}_wrapper

        tableWrapper.find('.dataTables_length select').select2({minimumResultsForSearch: Infinity}); // initialize select2 dropdown
    };

    var handleDatePickers = function () {
        if ($().datepicker) {
            $('.date-picker').datepicker({
                rtl: App.isRTL(),
                autoclose: true
            });
            //$('body').removeClass("modal-open"); // fix bug when inline picker is used in modal
        }

        /* Workaround to restrict daterange past date select: http://stackoverflow.com/questions/11933173/how-to-restrict-the-selectable-date-ranges-in-bootstrap-datepicker */
    };

    var handleTimePickers = function () {
        if (jQuery().timepicker) {
            $('.timepicker-default').timepicker({
                autoclose: true,
                showSeconds: true,
                minuteStep: 1
            });

            $('.timepicker-no-seconds').timepicker({
                autoclose: true,
                minuteStep: 5
            });

            $('.timepicker-24').timepicker({
                autoclose: true,
                minuteStep: 5,
                showSeconds: false,
                showMeridian: false
            });

            // handle input group button click
            $('.timepicker').parent('.input-group').on('click', '.input-group-btn', function (e) {
                e.preventDefault();
                $(this).parent('.input-group').find('.timepicker').timepicker('showWidget');
            });
        }
    };

    var initCurrenciesListTable = function (cOptionsCurrencies) {
        var table = $('#tblCurrencies');
        var oTable = table.dataTable({
            serverSide: true,
            processing: true,
            language: {
                aria: {
                    sortAscending: ": activate to sort column ascending",
                    sortDescending: ": activate to sort column descending"
                },
                emptyTable: "No data available",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "No entries found",
                //infoFiltered  : "(filtered from _MAX_ total entries)",
                infoFiltered: "",
                lengthMenu: "_MENU_",
                search: "Search:",
                zeroRecords: "No matching records found"
            },
            searching: false,
            order: cOptionsCurrencies.order,
            lengthMenu: [
                [10, 20, 30, -1],
                [10, 20, 30, "All"] // change per page values here
            ],
            // set the initial value
            pageLength: 10,
            dom: "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
            /*fnRowCallback: function(nRow, aData, iDisplayIndex){
             var oSettings = oTable.fnSettings();
             var index = oSettings._iDisplayStart + iDisplayIndex + 1;
             $('td:eq(0)',nRow).html(index);
             return nRow;
             },*/
            ajax: {
                type: "POST",
                url: "/settings/currencies/",
                data: function (obj) {
                    $("#frmSearch").serializeObject(obj);
                },
                dataSrc: function (json) {
                    //alert(json);
                    return json.data;
                }
            },
            columnDefs: cOptionsCurrencies.columnDefs


        });
        var tableWrapper = $('#tblCurrencies_wrapper'); // datatable creates the table wrapper by adding with id {your_table_jd}_wrapper

        tableWrapper.find('.dataTables_length select').select2({minimumResultsForSearch: Infinity}); // initialize select2 dropdown
    };
    
    var initCopyBooksListTable = function (cOptionsCopyBooks) {
        var table = $('#tblCopyBooks');
        var oTable = table.dataTable({
            serverSide: true,
            processing: true,
            language: {
                aria: {
                    sortAscending: ": activate to sort column ascending",
                    sortDescending: ": activate to sort column descending"
                },
                emptyTable: "No data available",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "No entries found",
                //infoFiltered  : "(filtered from _MAX_ total entries)",
                infoFiltered: "",
                lengthMenu: "_MENU_",
                search: "Search:",
                zeroRecords: "No matching records found"
            },
            searching: false,
            order: cOptionsCopyBooks.order,
             "paging":   false,
             "info":     false,
//            lengthMenu: [
//                [10, 20, 30, -1],
//                [10, 20, 30, "All"] // change per page values here
//            ],
            // set the initial value
            // pageLength: 10,
            dom: "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
            /*fnRowCallback: function(nRow, aData, iDisplayIndex){
             var oSettings = oTable.fnSettings();
             var index = oSettings._iDisplayStart + iDisplayIndex + 1;
             $('td:eq(0)',nRow).html(index);
             return nRow;
             },*/
            ajax: {
                type: "POST",
                url: "/settings/copy_books/",
                data: function (obj) {
                    $("#frmSearch").serializeObject(obj);
                },
                dataSrc: function (json) {
                    //alert(json);
                    return json.data;
                }
            },
            columnDefs: cOptionsCopyBooks.columnDefs


        });
        var tableWrapper = $('#tblCurrencies_wrapper'); // datatable creates the table wrapper by adding with id {your_table_jd}_wrapper

        tableWrapper.find('.dataTables_length select').select2({minimumResultsForSearch: Infinity}); // initialize select2 dropdown
    };
    
    var initApiKeysListTable = function (cOptionsKeys) {
        var table = $('#tblApiKeys');
        var oTable = table.dataTable({
            serverSide: true,
            processing: true,
            language: {
                aria: {
                    sortAscending: ": activate to sort column ascending",
                    sortDescending: ": activate to sort column descending"
                },
                emptyTable: "No API Keys in System",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "",
                //infoFiltered  : "(filtered from _MAX_ total entries)",
                infoFiltered: "",
                lengthMenu: "_MENU_",
                search: "Search:",
                zeroRecords: "No matching records found"
            },
            searching: false,
            lengthChange: false,
            order: cOptionsKeys.order,
//            lengthMenu: [
//                [ -1],
//                ["All"] // change per page values here
//            ],
            // set the initial value
            dom: "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
            /*fnRowCallback: function(nRow, aData, iDisplayIndex){
             var oSettings = oTable.fnSettings();
             var index = oSettings._iDisplayStart + iDisplayIndex + 1;
             $('td:eq(0)',nRow).html(index);
             return nRow;
             },*/
            ajax: {
                type: "POST",
                url:  cOptionsKeys.url,
                data: function (obj) {
                    $("#frmSearch").serializeObject(obj);
                },
                dataSrc: function (json) {
                    $("#total_records").val(json.recordsTotal);
                    return json.data;
                    
                }
            },
            columnDefs: cOptionsKeys.columnDefs


        });
        var tableWrapper = $('#tblCurrencies_wrapper'); // datatable creates the table wrapper by adding with id {your_table_jd}_wrapper

        tableWrapper.find('.dataTables_length select').select2({minimumResultsForSearch: Infinity}); // initialize select2 dropdown
    };
    
     var initIssuanceListingTable = function (cOptionsIssuance) {
        var table = $('#tblIssuanceListing');
        var oTable = table.dataTable({
            serverSide: true,
            processing: true,
            language: {
                aria: {
                    sortAscending: ": activate to sort column ascending",
                    sortDescending: ": activate to sort column descending"
                },
                emptyTable: "No 1099's to issue",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                infoEmpty: "",
                //infoFiltered  : "(filtered from _MAX_ total entries)",
                infoFiltered: "",
                lengthMenu: "_MENU_",
                search: "Search:",
                zeroRecords: "No matching records found"
            },
            searching: false,
            lengthChange: false,
            order: cOptionsIssuance.order,
//            lengthMenu: [
//                [ -1],
//                ["All"] // change per page values here
//            ],
            // set the initial value
            dom: "<'row' <'col-md-12'T>><'row'<'col-md-6 col-sm-12'l><'col-md-6 col-sm-12'f>r><'table-scrollable't><'row'<'col-md-5 col-sm-12'i><'col-md-7 col-sm-12'p>>", // horizobtal scrollable datatable
            /*fnRowCallback: function(nRow, aData, iDisplayIndex){
             var oSettings = oTable.fnSettings();
             var index = oSettings._iDisplayStart + iDisplayIndex + 1;
             $('td:eq(0)',nRow).html(index);
             return nRow;
             },*/
            ajax: {
                type: "POST",
                url:  cOptionsIssuance.url,
                data: function (obj) {
                    $("#frmSearch").serializeObject(obj);
                },
                dataSrc: function (json) {
                    $("#total_records").val(json.recordsTotal);
                    $(".selected_year").html(json.selected_year);
                    $("#export_excel_href").attr("href", site_url+"reports/export_issuance_report/"+json.selected_year);
                    return json.data;
                    
                }
            },
            columnDefs: cOptionsIssuance.columnDefs


        });
        $("#issuance_year").click(function () {
            oTable.fnFilter($("#year").val());
        });
        var tableWrapper = $('#tblCurrencies_wrapper'); // datatable creates the table wrapper by adding with id {your_table_jd}_wrapper

        tableWrapper.find('.dataTables_length select').select2({minimumResultsForSearch: Infinity}); // initialize select2 dropdown
    };

    // public functions
    return {
        //main function
        init: function () {
            //initialize here something.
            handleDatePickers();
            handleTimePickers();
        },
        initListingTable: function (cOptions) {
            initListingTable(cOptions);
        },
        initCurrenciesListTable: function (cOptionsCurrencies) {
            initCurrenciesListTable(cOptionsCurrencies);
        },
        initCopyBooksListTable: function (cOptionsCopyBooks) {
            initCopyBooksListTable(cOptionsCopyBooks);
        },
        initApiKeysListTable: function (cOptionsKeys) {
            initApiKeysListTable(cOptionsKeys);
        },
        initIssuanceListingTable: function (cOptionsIssuance) {
            initIssuanceListingTable(cOptionsIssuance);
        },
        handleDatePickers: function () {
            handleDatePickers();
        },
        handleTimePickers: function () {
            handleTimePickers();
        }

    };

}();
window.setTimeout(function () {
    $(".alert-message").fadeTo(1500, 0).slideUp(500, function () {
        $(this).remove();
    });
}, 3000);
$(document).on('keydown', '.integers', function (e) {
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
            (e.keyCode == 65 && e.ctrlKey === true) ||
            (e.keyCode == 67 && e.ctrlKey === true) ||
            (e.keyCode == 88 && e.ctrlKey === true) ||
            (e.keyCode >= 35 && e.keyCode <= 39)) {
        return;
    }
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});
//$(".modal").on('hidden.bs.modal', function () {
//    $(".jselect2me, .select2me, .aselect2me").select2("close");
//});