class Daily {
    constructor(storeKey, date, maintab) {
        this.storeKey   = storeKey;
        this.maintab    = '#labor';
        this.date    = date;
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
        var postData = {
            store_key: this.storeKey,
            maintab: "#donut_count",
            daily_date : this.date
        }
        var advance_search = this.objectifyForm($('#advance_search_form').serializeArray());
        $.extend(postData, advance_search);
        return postData;
    }
    showMainTabbing(r) {
        //$("#loadingmessage").show();
        let loadurl = jQuery(r).attr('href');
        if(loadurl == '' || typeof  loadurl == 'undefined'){
             loadurl = $(".btn-group .btn-1.active").attr("href");
        }
        this.maintab = loadurl;
        $("#maintab").val(loadurl);
        $.ajax({
            url: 'http://ledger.local/daily/showMainTabbing',
            data: this.getPostData(),
            method: 'POST',
            success: function(response) {
                jQuery(".main-layout-tabs .tab-pane").html('');
                jQuery(loadurl).html(response);
                $(".item li a[href='#"+this.storeKey+"']").trigger("click");
                $(".item li a[href='#"+this.store_key+"']").parent().addClass("active");
                $("#loadingmessage").hide();

                $(".day_datepicker").datepicker({
                    format: 'mm/dd/yyyy'
                });

                $(".week_datepicker").datepicker({
                    daysOfWeekDisabled: [0, 1, 2, 3, 4, 5],
                    format: 'mm/dd/yyyy'
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
            }
        });
        return false;
    }
}

$('#check_book_modal').on('hidden.bs.modal', function() {
    location.reload();
})
var carousel = $('.owl_1').owlCarousel({
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
$('body').on("click", ".item li", function(){
    $('.item li').not($(this)).removeClass('active');
});
function show_option(element) {
    var value = $(element).val();
    $("div.option_div").hide();
    $('div.' + value + "_div").show();
    $('#special_day').prop('selectedIndex', 0);
    $('#advance_season').prop('selectedIndex', 0);
    $('#advance_date').val('');
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