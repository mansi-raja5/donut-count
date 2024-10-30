class Weekly {
    constructor(storeKey, date, maintab) {
        this.storeKey = storeKey;
        this.maintab = '#labor';
        this.date = date;
    }
    objectifyForm(formArray) {
        var returnArray = {};

        for (var i = 0; i < formArray.length; i++){
            if ( returnArray[formArray[i]['name']]) {
                returnArray[formArray[i]['name']].push(formArray[i]['value']);
            } else {
                returnArray[formArray[i]['name']] = [formArray[i]['value']];
            }
        }
        return returnArray;
    }
    getPostData() {
        var postData = {
            store_key      : this.storeKey,
            maintab        : this.maintab,
            weekend_date   : this.date,
        }
        var advance_search = this.objectifyForm($('#advance_search_form').serializeArray());
        $.extend(postData,advance_search);
        return postData;
    }
    showMainTabbing(r) {
        $("#loadingmessage").show();
        var loadurl = jQuery(r).attr('href');
        if (loadurl == '' || typeof loadurl == 'undefined') {
            loadurl = $(".btn-group .btn-1.active").attr("href");
        }
        this.maintab = loadurl;
        $("#maintab").val(loadurl);
        $.ajax({
            url: site_url + 'weekly/showMainTabbing',
            data: this.getPostData(),
            method: 'POST',
            success: function (response) {
                jQuery(".main-layout-tabs .tab-pane").html('');
                jQuery(loadurl).html(response);
                $(".item li a[href='#" + this.storeKey + "']").trigger("click");
                $(".item li a[href='#" + store_key + "']").parent().addClass("active");
                $("#from_date").datepicker({
        format: 'mm/dd/yyyy',
        autoclose: true,
    }).on('changeDate', function (selected) {
        var startDate = new Date(selected.date.valueOf());
        $('#to_date').datepicker('setStartDate', startDate);
    }).on('clearDate', function (selected) {
        $('#to_date').datepicker('setStartDate', null);
    });

    $("#to_date").datepicker({
        format: 'mm/dd/yyyy',
        autoclose: true,
    }).on('changeDate', function (selected) {
        var endDate = new Date(selected.date.valueOf());
        $('#from_date').datepicker('setEndDate', endDate);
    }).on('clearDate', function (selected) {
        $('#from_date').datepicker('setEndDate', null);
    });
    $(".week_datepicker").datepicker({
        daysOfWeekDisabled: [0, 1, 2, 3, 4, 5],
        format: 'mm-dd-yyyy'
    });
    $(".datepicker").datepicker({
        format: 'mm-dd-yyyy'
    });
                $("#loadingmessage").hide();
            }
        });
        return false;
    }
}

$('#check_book_modal').on('hidden.bs.modal', function () {
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
$('body').on("click", ".item li", function () {
    $('.item li').not($(this)).removeClass('active');

});
function show_option(element) {
    var value = $(element).val();
    console.log(value);
    console.log($("#"+value).length);
    $("div.option_div").hide();
    $('div.' + value + "_div").show();
    $('#special_day').prop('selectedIndex',0);
    $('#advance_season').prop('selectedIndex',0);
    $('#advance_date').val('');
//    $("#"+value).attr("required", true);
//    if(value == 'date'){
//        $("#")
//    }else if(value == 'special_day'){
//        
//    }else if(value == 'week'){
//        
//    }else if(value == 'season'){
//        
//    }else if(value == 'year'){
//        
//    }else if(value == 'custom_date'){
//        
//    }
}