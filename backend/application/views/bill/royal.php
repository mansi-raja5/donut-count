<style type="text/css">
#royalty-main-form .table>tbody>tr>td  {
    padding: 2px;
}
#royalty-main-form .table>tbody>tr>td input  {
    border:1px solid;
    width: 130px;
}
</style>
<div class="portlet box green">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-comments">
            </i>
            Royalty Payment
        </div>
        <div class="tools">
            <a class="collapse" data-original-title="" href="javascript:;" title="">
            </a>
            <a class="config" data-original-title="" data-toggle="modal" href="#portlet-config" title="">
            </a>
            <a class="reload" data-original-title="" href="javascript:;" title="">
            </a>
            <a class="remove" data-original-title="" href="javascript:;" title="">
            </a>
        </div>
    </div>
    <div class="portlet-body">
        <form name="royalty-main-form" id="royalty-main-form">
            <div id="paste_text" style="display: none;"></div>
            <div class="col-md-6 mb10">
                <div class="form-group">
                    <label for="store" class="col-md-4 control-label">Weekending Date<span class="required" aria-required="true"> * </span></label>
                    <div class="col-md-4">
                        <?php
                        echo form_input(array('required' => 'required', 'name' => 'weekend_date', 'class' => 'week_ending_date form-control', 'value' => ''));
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-md-2 mb10 pull-right">
                <input type="button" class="btn purple btn-block" id = "save_royalty_btn" value="Pay Royalty"  style="display: none;" onclick="roy.saveRoyaltyData();">
            </div>
            <div class="table-scrollable" id="royalty-data"></div>
        </form>
    </div>
</div>
<script type="text/javascript">
let roy = new Royalty();
$(document).ready(function () {
    $("#royalty-main-form .week_ending_date").datepicker({
        daysOfWeekDisabled: [0, 1, 2, 3, 4, 5],
        format: 'mm/dd/yyyy'
    }).on('changeDate', function (e) {
        roy.getRoyaltyData();
    });
});
</script>