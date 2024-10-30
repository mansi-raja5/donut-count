class Weekly {
    store_list = {};
    constructor(maintab) {
        this.maintab = maintab;
    }
    setStoreList(store_list) {
        this.store_list = store_list;
        this.store_list.inactive.forEach(key => {
            $('a[href="#' + key + '"]').css('background-color', 'bisque');
        });
    }
    objectifyForm(formArray) {
        var returnArray = {};
        for (var i = 0; i < formArray.length; i++) {
            if (returnArray[formArray[i]['name']]) {
                returnArray[formArray[i]['name']].push(formArray[i]['value']);
            } else {
                returnArray[formArray[i]['name']] = [formArray[i]['value']];
            }
        }
        return returnArray;
    }
    getPostData() {
        this.date = $("#weekend_date").val();
        var postData = {
            maintab: this.maintab,
        }
        var advance_search = this.objectifyForm($('#advance_search_form').serializeArray());
        $.extend(postData, advance_search);
        return postData;
    }
    exportXls() {
        $("#loadingmessage").show();
        this.maintab = $(".btn-group .btn-1.active").attr("href");
        var data = $.extend(this.getPostData(), { is_export: true });;
        $.ajax({
            url: site_url + 'weekly/showMainTabbing',
            data: data,
            method: 'POST',
            success: function (data) {
                data = JSON.parse(data);
                if (data.file_content) {
                    var downloadLink = document.createElement("a");
                    downloadLink.href = data.file_content;
                    downloadLink.download = data.file_name;
                    document.body.appendChild(downloadLink);
                    downloadLink.click();
                    document.body.removeChild(downloadLink);
                }
                $("#loadingmessage").hide();
            }
        });
    }
    showMainTabbing(r) {
        $("#loadingmessage").show();
        var is_valid = true;

        var loadurl = jQuery(r).attr('href');
        if (loadurl == '' || typeof loadurl == 'undefined') {
            loadurl = $(".btn-group .btn-1.active").attr("href");
        }
        this.maintab = loadurl;
        $("#maintab").val(loadurl);
        var value = $("input[name='advance_status']:checked").val();
        if ($("#advance_search_form").length) {
            is_valid = $("#advance_search_form").valid();
        }
        if (is_valid) {
            let maintab = this.maintab;
            $.ajax({
                url: site_url + 'weekly/showMainTabbing',
                data: this.getPostData(),
                method: 'POST',
                success: function (response) {
                    jQuery(".main-layout-tabs .tab-pane").html('');
                    jQuery(loadurl).html(response);
                    var value = $("input[name='advance_status']:checked").val();
                    $("div.option_div").hide();
                    $('div.' + value + "_div").show();
                    var carousel = $('.owl_1').owlCarousel({
                        autoWidth: true,
                        loop: false,
                        margin: 2,
                        responsiveClass: true,
                        autoplayHoverPause: true,
                        autoplay: false,
                        slideSpeed: 400,
                        paginationSpeed: 400,
                        autoplayTimeout: 3000,
                        responsive: {
                            0: {
                                items: 5,
                                nav: true,
                                loop: false
                            },
                            600: {
                                items: 4,
                                nav: true,
                                loop: false
                            },
                            1000: {
                                items: 10,
                                nav: true,
                                loop: false
                            }
                        }
                    });

                    $("#weekend_date").datepicker({
                        format: 'mm/dd/yyyy',
                        autoclose: true,
                    });

                    $("#advance_from_date").datepicker({
                        format: 'mm/dd/yyyy',
                        autoclose: true,
                    }).on('changeDate', function (selected) {
                        var startDate = new Date(selected.date.valueOf());
                        $('#advance_to_date').datepicker('setStartDate', startDate);
                    }).on('clearDate', function (selected) {
                        $('#advance_to_date').datepicker('setStartDate', null);
                    });

                    $("#advance_to_date").datepicker({
                        format: 'mm/dd/yyyy',
                        autoclose: true,
                    }).on('changeDate', function (selected) {
                        var endDate = new Date(selected.date.valueOf());
                        $('#advance_from_date').datepicker('setEndDate', endDate);
                    }).on('clearDate', function (selected) {
                        $('#advance_from_date').datepicker('setEndDate', null);
                    });

                    if (maintab == '#labor') {
                        $("#advance_from_date").datepicker('setDaysOfWeekDisabled', [1, 2, 3, 4, 5, 6]);
                        $("#advance_to_date").datepicker('setDaysOfWeekDisabled', [0, 1, 2, 3, 4, 5]);
                    }

                    $(".week_datepicker").datepicker({
                        daysOfWeekDisabled: [0, 1, 2, 3, 4, 5],
                        format: 'mm/dd/yyyy'
                    });
                    $("#advance_date").datepicker({
                        format: 'mm/dd/yyyy'
                    });
                    $("#loadingmessage").hide();
                }
            });
        } else {
            $("#loadingmessage").hide();
            return false;
        }

    }
}

$('#check_book_modal').on('hidden.bs.modal', function () {
    location.reload();
})

$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip();
});


$('body').on("click", ".item li", function () {
    $('.item li').not($(this)).removeClass('active');

});

$('body').on('change', 'checkbox[name="all"], checkbox[name="active"], checkbox[name="inactive"]', function() {
    el = $(this);
    if ($.inArray('all', el.val()) !== -1) {
        el.val($.merge(week.store_list.active, week.store_list.inactive)).multiselect();
    } else if ($.inArray('active', el.val()) !== -1) {
        el.val(week.store_list.active).multiselect();
    } else if ($.inArray('inactive', el.val()) !== -1)  {
        el.val(week.store_list.inactive).multiselect();
    }
});

$('body').on('change', 'input[name="filter_view"]', function() {
    if ($(this).val() == 'standard') {
        $('.weekending_div').show();
        $('.selection_div').hide();
        $("div.option_div").hide();
        $('input[name="advance_status"]').prop('checked', false);
        resetComparsionField();
        $("#weekend_date").attr("required", true);
        $("#advance_date").attr("required", false);
        $("#advance_special_day").attr("required", false);
        $("#advance_week_date").attr("required", false);
        $("#advance_season").attr("required", false);
        $("#advance_from_date").attr("required", false);
        $("#advance_to_date").attr("required", false);
    } else {
        $('.weekending_div').hide();
        $('.selection_div').show();
        var subChild = $('input[name="advance_status"]:checked');
        if (subChild.length == 1) {
            show_option(subChild[0]);
        }
        $("#weekend_date").attr("required", false);
    }
});

function show_option(element) {
    var value = $(element).val();
    $("div.option_div").hide();
    $('div.' + value + "_div").show();
    resetComparsionField();
    $("#advance_date").attr("required", false);
    $("#advance_special_day").attr("required", false);
    $("#advance_week_date").attr("required", false);
    $("#advance_season").attr("required", false);
    $("#advance_from_date").attr("required", false);
    $("#advance_to_date").attr("required", false);
    if (value == 'day') {
        $("#advance_date").attr("required", true);
    } else if (value == 'special_day') {
        $("#advance_special_day").attr("required", true);
    } else if (value == 'week') {
        $("#advance_week_date").attr("required", true);
    } else if (value == 'season') {
        $("#advance_season").attr("required", true);
    } else if (value == 'custom_date') {
        $("#advance_from_date").attr("required", true);
        $("#advance_to_date").attr("required", true);
    }
}

function resetComparsionField() { 
    $('#special_day').prop('selectedIndex', 0);
    $('#advance_season').prop('selectedIndex', 0);
    $('#advance_date').val('');
    $('#advance_week_date').val('');
    $('#advance_from_date').val('');
    $('#advance_to_date').val('');
}

function reset_advance_search() {
    $('#advance_store_key').multiselect('deselectAll', false).multiselect('refresh');
    $('#advance_years, #weekend_date').val(null).trigger('change');
    $('input[name="advance_status"]').prop('checked', false);
    $('.day_div,.special_day_div,.week_div,.season_div,.custom_date_div').hide();
}