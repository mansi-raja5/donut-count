class Ledger {
    constructor(storeKey, month, year) {
        this.storeKey   = storeKey;
        this.month      = month;
        this.year       = year;
    }
    getPostData() {
        return {
            storeKey: this.storeKey,
            month: this.month
        };
    }
    createLedger()
    {
        $("#loadingmessage").show();
        var auto_ledger = jQuery("#auto_ledger_frm").serialize();
        var auto_ledger_data = jQuery("#auto_ledger_frm").serializeArray();
        auto_ledger_data = this.jQFormSerializeArrToJson(auto_ledger_data);
        jQuery.extend(this.getPostData(), auto_ledger_data);
        $.ajax({
            url: site_url + 'statement/createLedgerFromAutoView',
            data: auto_ledger,
            method: 'POST',
            success: function(response) {
                $("#loadingmessage").hide();
            }
        });
    }
    jQFormSerializeArrToJson(formSerializeArr) {
        var jsonObj = {};
        jQuery.map(formSerializeArr, function(n, i) {
            jsonObj[n.name] = n.value;
        });
        return jsonObj;
    }
    setConfirmDetails(value) {
        var delete_url = $(value).attr("data-url");
        $("#ConfirmDeleteModal").find(".modal-body").children("p").show();
        $("#ConfirmDeleteModal").find(".confirmYes").show();
        $("#ConfirmDeleteModal").find(".modal-title").html("Ledger Deletion");
        $("#ConfirmDeleteModal").find(".modal-body").children("p").html("Do you really want to delete this ledger statement? :O <br>Thats not good...it would be unfair with existing ledger. <br>Do you want to see that ledger :) or still want to delete? :( ");
        $("#ConfirmDeleteModal").find("#ConfirmDelete").attr('href', delete_url);
    }
}

// show toolip
$(document).on("click", ".open-tooltip", function () {
    if ($('.popover').hasClass('in')) {
        $('.popover').popover('hide');
    }
    $('.popover').popover('hide');
    $(this).attr('title', '<a class="close-popover pull-right"><i class="fa fa-times" aria-hidden="true"></i></a>');
    $(this).attr('html', true);
    $(this).popover({html: true}).popover('show');
});

$(document).on("click", ".close-popover", function () {
    $(this).closest('div.popover').popover('hide');
});