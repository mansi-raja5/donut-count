<?php
$min_year = 2018;
$max_year = 2025;
?>
<style type="text/css">
label.mt-radio {
    margin: 10px 0 0 0;
}
.locked
{
    pointer-events: none;
    opacity: 0.4;
    background: #FA8072;
}
</style>
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body">
        <?php $message = $this->session->flashdata('msg'); ?>
        <?php if (!empty($message)) : ?>
            <div class="alert alert-success alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><?php echo $message; ?></div>
        <?php endif; ?>
        <div class="row">
            <div class="col-md-12">
                <?php
                $attributes = array('class' => 'form-horizontal validate', 'id' => 'submit_form');
                echo form_open_multipart('settings/yearsetting', $attributes, array());
                ?>
                <div class="form-group">
                    <?php echo form_label('Select Year<span class="required" aria-required="true"> * </span>', 'year', array('class' => 'col-md-4 control-label')); ?>
                    <div class="col-md-4">
                        <?php
                        $options = array();
                        $options[''] = '-- Year --';
                        for ($yi = $min_year; $yi <= $max_year; $yi++) {
                            $options[$yi] = $yi;
                        }
                        echo form_dropdown(array('required' => 'required', 'id' => 'year', 'name' => 'year', 'options' => $options, 'class' => 'form-control select2me','onchange'=>'getMonthsForTheYear(this)'));
                        ?>
                    </div>
                </div>
                <div id="month_div"></div>
                <hr>
                <div class="form-actions">
                    <div class="row">
                        <div class="col-md-offset-4 col-md-8">
                            <button type="button" class="btn default">Cancel</button>
                            <button type="submit" class="btn blue">Submit</button>
                        </div>
                    </div>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        <?php if (isset($selected_year)) : ?>
            $('#year').val('<?php echo $selected_year; ?>').trigger('change');
        <?php endif; ?>
    });

    function getMonthsForTheYear(r, yearweek = undefined) {
        if (r == undefined) {
            r = $('#year')[0];
        }
        $("#loadingmessage").show();
        $.ajax({
            url: site_url + 'settings/getyearmonthsetting',
            data: { year: r.value, year_week: yearweek, week_position: ($('input[name="week_position"]') ? $('input[name="week_position"]').val() : ''), is_shifted: ($('input[name="is_shifted"]') ? $('input[name="is_shifted"]').val() : '') },
            method: 'POST',
            success: function(response) {
                $('#month_div').html(response);
                $("#loadingmessage").hide();
            }
        });
    }

    function changeMonthWeek()
    {
        var year_starting_date = $("#year_starting_date").val();
        if(year_starting_date == "")
        {
            alert("Please add year starting date");
            $("#year_starting_date").focus();
            return false;
        }
        $("#loadingmessage").show();
        var no_of_weeks_in_year = $("input[name='yearweeks']:checked").val();
        var monthweeks = {};
        for(var i = 1; i <= 12; i++){
            monthweeks[i] = $("input[name='monthweeks["+i+"]']:checked").val();
        }
        $.ajax({
            url: site_url + 'settings/getMonthStartDateEnddateYearly',
            data: {year_starting_date: year_starting_date,no_of_weeks_in_year:no_of_weeks_in_year,monthweeks:monthweeks},
            method: 'POST',
            success: function(response) {
                var result = $.parseJSON(response);
                $("#weeks_hidden").val(response);
                for(var i = 1; i <= 12; i++){
                    var weeklistHtml = "";
                    $.each(result[i],function(index,value){
                        // console.log(value.start_of_week);
                        startWeekDate   = $.datepicker.formatDate('MM dd yy', new Date(value.start_of_week));
                        startWeekDate   = value.start_of_week;
                        // console.log(startWeekDate);
                        endWeekDate     = $.datepicker.formatDate('MM dd yy', new Date(value.end_of_week));
                        endWeekDate     = value.end_of_week;
                        weeklistHtml += '<div style="margin:5px"><span class="label label-sm label-success">'+startWeekDate+'</span>'+' - '+'<span class="label label-sm label-success">'+endWeekDate+'</span></div>';
                    });
                    $("#weeklist_"+i).html(weeklistHtml);
                }
                $("#loadingmessage").hide();
                $("#total_month_weeks label").html(totalMonthsOfYear());
            }
        });
    }

    function totalMonthsOfYear()
    {
        var totalMonthsOfYear = 0;
        for(var i = 1; i <= 12; i++){
            var monthweeks = $("input[name='monthweeks["+i+"]']:checked").val();
            totalMonthsOfYear = parseInt(totalMonthsOfYear) + parseInt(monthweeks);
        }
        return totalMonthsOfYear;
    }

    $('#submit_form').submit(function() {
        var totalWeeksOfYear = parseInt($("input[name='yearweeks']:checked").val());
        var yearWeeks = 0;
        for(var i = 1; i <= 12; i++){
            yearWeeks += parseInt($("input[name='monthweeks["+i+"]']:checked").val());
        }
        if (totalWeeksOfYear !== yearWeeks) {
            alert("Total Month Weeks does not match with Total year weeks");
            return false;
        }
        return true;
    });
</script>