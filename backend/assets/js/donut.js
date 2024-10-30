class Donut {
    constructor(storeKey, type) {
        this.storeKey = storeKey;
        this.type = type;
    }
    getPostData() {
        return {
            storeKey: this.storeKey,
            type: this.type
        };
    }
    setPostData(storeKey, type) {
        return {
            storeKey: this.storeKey,
            type: this.type
        };
    }
    getDataFromOtherSite(r) {
        paste();
        async function paste() {
          const text = await navigator.clipboard.readText();
          document.getElementById('paste_text').innerHTML = text;
        }
        var pasteText = $("#paste_text").html();
        if(!pasteText)
        {
            alert("No data is copied");
            return false;
        }
        $("#loadingmessage").show();
        $.ajax({
            url: site_url + 'donut/getDataFromOtherSite',
            data: {
                websitedata: pasteText
            },
            method: 'POST',
            success: function(responseHtml) {
                $("#donut-data").html(responseHtml);
                $("#save_donut_btn").show();
                $("#loadingmessage").hide();
            }
        });
    }
    saveDonutData()
    {
        var curObj = this;
        $("#loadingmessage").show();
        $(".donut_button").hide();
        $.ajax({
            url: site_url + 'donut/saveDonutData',
            data: $("#donut-main-form").serialize(),
            method: 'POST',
            success: function(response) {
                $("#loadingmessage").hide();
                var Res = JSON.parse(response);
                window.location = site_url+Res.redirect;
            }
        });
    }
    triggerImportFile(r)
    {
        $(r).parent('a').find("input[type='file']").trigger('click');
    }
    importDonutPurchaseForWholeMonthWeekWise(r)
    {
        var curObj = this;
        $(".donut_button").hide();
        $("#loadingmessage").show();
        var file_data = $(r).prop('files')[0];
        var form_data = new FormData();
        form_data.append('file', file_data);
        $.ajax({
            url: site_url + 'donut/importDonutPurchaseForWholeMonthWeekWise',
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function(response) {
                $("#donut-data").html(response);
                $("#save_donut_btn").show();
                $("#loadingmessage").hide();
            }
        });
    }
    importDonutPurchaseForDays(r)
    {
        var curObj = this;
        $("#loadingmessage").show();
        $(".donut_button").hide();
        var file_data = $(r).prop('files')[0];
        var form_data = new FormData();
        form_data.append('file', file_data);
        $.ajax({
            url: site_url + 'donut/importDonutPurchaseForDays',
            data: form_data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            success: function(response) {
                $("#donut-data").html(response);
                $("#save_donut_day_data_btn").show();
                $("#loadingmessage").hide();
            }
        });
    }

    saveDonutLastWeekData()
    {
        var curObj = this;
        $("#loadingmessage").show();
        $(".donut_button").hide();
        $.ajax({
            url: site_url + 'donut/saveDonutLastWeekData',
            data: $("#donut-main-form").serialize(),
            method: 'POST',
            success: function(response) {
                $("#loadingmessage").hide();
                var Res = JSON.parse(response);
                window.location = site_url+Res.redirect;
            }
        });
    }
}