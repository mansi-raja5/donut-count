class Royalty {
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
    getRoyaltyData() {
        var weekEndingDate = $(".week_ending_date").val();
        $("#loadingmessage").show();
        $.ajax({
            url: site_url + 'royal/getRoyaltyData',
            data: {
                week_ending_date: weekEndingDate,
            },
            method: 'POST',
            success: function(response) {
                var Res = JSON.parse(response);
                if (Res.status == 'success') {
                    $("#royalty-data").html(Res.royhtml);
                }
                else
                {
                    alert("No data found");
                }
                $(".default-disabled input").attr('disabled','disabled');
                $("#loadingmessage").hide();
            }
        });
    }
    getDataFromOtherSite(r) {
        var currentRow = $(r).closest("tr");
        var storeKey = currentRow.find(".store_key").val();
        var royalType = currentRow.find(".royal_type").val();
        if(!storeKey)
        {
            alert("Please select store");
            currentRow.find(".store_key").trigger("click");
            return false;
        }
        if(!royalType)
        {
            alert("Please select type");
            currentRow.find(".royal_type").focus();
            return false;
        }
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
            url: site_url + 'royal/getDataFromOtherSite',
            data: {
                websitedata: pasteText
            },
            method: 'POST',
            success: function(response) {
                var Res = JSON.parse(response);
                if (Res.status == 'success') {

                    if (Res.period_ending != $(".week_ending_date").val()) {
                        alert("Week ending date is not matched with data");
                        $("#loadingmessage").hide();
                        return false;
                    }

                    var store_key_td = currentRow.find(".store_key").closest("td");
                    var royal_type_td = currentRow.find(".royal_type").closest("td");
                    var net_sales_td = currentRow.find(".net_sales").closest("td");
                    var royalty_amt_td = currentRow.find(".royalty_amt").closest("td");
                    var adfund_amt_td = currentRow.find(".adfund_amt").closest("td");
                    var cust_count_td = currentRow.find(".cust_count").closest("td");
                    var sys_eft_amt_td = currentRow.find(".sys_eft_amt").closest("td");
                    currentRow.find('td').removeClass('has-success');
                    currentRow.find('td').removeClass('has-error');
                    var all_matched = 1;
                    if (Res.pc == currentRow.find(".store_key").val()) {
                        currentRow.find(".store_key").closest("td").addClass('has-success');
                    } else {
                        all_matched = 0;
                        currentRow.find(".store_key").closest("td").addClass('has-error');
                    }
                    if (Res.royalType == currentRow.find(".royal_type").val()) {
                        currentRow.find(".royal_type").closest("td").addClass('has-success');
                    } else {
                        all_matched = 0;
                        currentRow.find(".royal_type").closest("td").addClass('has-error');
                    }
                    if (Res.total_sales == currentRow.find(".net_sales").val()) {
                        currentRow.find(".net_sales").closest("td").addClass('has-success');
                    } else {
                        all_matched = 0;
                        currentRow.find(".net_sales").closest("td").addClass('has-error');
                    }
                    if (Res.royalty_amt == currentRow.find(".royalty_amt").val()) {
                        currentRow.find(".royalty_amt").closest("td").addClass('has-success');
                    } else {
                        all_matched = 0;
                        currentRow.find(".royalty_amt").closest("td").addClass('has-error');
                    }
                    if (Res.adfund_amt == currentRow.find(".adfund_amt").val()) {
                        currentRow.find(".adfund_amt").closest("td").addClass('has-success');
                    } else {
                        all_matched = 0;
                        currentRow.find(".adfund_amt").closest("td").addClass('has-error');
                    }
                    if (Res.customer_count == currentRow.find(".cust_count").val()) {
                        currentRow.find(".cust_count").closest("td").addClass('has-success');
                    } else {
                        all_matched = 0;
                        currentRow.find(".cust_count").closest("td").addClass('has-error');
                    }
                    if (Res.eft_transfer_amt == currentRow.find(".sys_eft_amt").val()) {
                        currentRow.find(".sys_eft_amt").closest("td").addClass('has-success');
                    } else {
                        all_matched = 0;
                        currentRow.find(".sys_eft_amt").closest("td").addClass('has-error');
                    }
                    currentRow.find(".store_key_help_block").html(Res.pc);
                    currentRow.find(".royal_type_help_block").html(Res.royalType);
                    currentRow.find(".net_sales_help_block").html(Res.total_sales);
                    currentRow.find(".royalty_amt_help_block").html(Res.royalty_amt);
                    currentRow.find(".adfund_amt_help_block").html(Res.adfund_amt);
                    currentRow.find(".cust_count_help_block").html(Res.customer_count);
                    currentRow.find(".sys_eft_amt_help_block").html(Res.eft_transfer_amt);
                    currentRow.find(".actual_eft_amt").val(Res.eft_transfer_amt);

                    if (all_matched == 1) {
                        $("#save_royalty_btn").show();
                        currentRow.find("input").removeAttr('disabled');

                        currentRow.find(".final_action").html('<i class="fa fa-check" style="font-size:36px;color:green;margin:10px"></i>');
                    } else {
                        currentRow.find(".final_action").html('<i class="fa fa-close" style="font-size:36px;color:red;margin:10px"></i>');
                    }
                }
                else
                {
                    alert("No data found");
                }
                $("#loadingmessage").hide();
            }
        });
    }
    saveRoyaltyData()
    {
        var curObj = this;
        $("#loadingmessage").show();
        $.ajax({
            url: site_url + 'royal/saveRoyaltyData',
            data: $("#royalty-main-form").serialize(),
            method: 'POST',
            success: function(response) {
                curObj.getRoyaltyData();
                $("#loadingmessage").hide();
            }
        });
    }
    saveRoyaltySettingData()
    {
        $("#loadingmessage").show();
        $.ajax({
            url: site_url + 'settings/royaltysetting',
            data: $("#royalty-setting-form").serialize(),
            method: 'POST',
            success: function(response) {
                $("#loadingmessage").hide();
            }
        });
    }
}