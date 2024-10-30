<style type="text/css">
#donut-main-form .table>tbody>tr>td  {
    padding: 2px;
}
#donut-main-form .table>tbody>tr>td input  {
    border:1px solid;
}
</style>
<div class="portlet box green">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-comments">
            </i>
            Donut Purchase
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
        <form name="donut-main-form" id="donut-main-form">
            <div id="paste_text" style="display: none;"></div>
            <div class="col-md-12 mb10">
                <div class="form-group">
                    <button type="button" class="btn btn-danger btn-sm mt-ladda-btn ladda-button btn-circle" data-style="expand-right" data-size="s" onclick="donut.getDataFromOtherSite(this)">
                        <span class="ladda-label">Grab Donut Purchase data from site</span>
                        <span class="ladda-spinner"></span>
                    </button>
                    <a class="btn btn-danger btn-sm mt-ladda-btn ladda-button btn-circle" href="javascript:;">
                        <span onclick="donut.triggerImportFile(this)">Import Donut Purchase Weekly Data</span>
                        <input type="file" name="donut_purchase_weekly_file" style="display: none;" extension="csv,xls,xlsx" onChange="donut.importDonutPurchaseForWholeMonthWeekWise(this)"/>
                    </a>
                    <a class="btn btn-danger btn-sm mt-ladda-btn ladda-button btn-circle" href="javascript:;">
                        <span onclick="donut.triggerImportFile(this)">Import Donut Purchase last Week Data</span>
                        <input type="file" name="donut_purchase_weekly_file" style="display: none;" extension="csv,xls,xlsx" onChange="donut.importDonutPurchaseForDays(this)"/>
                    </a>
                </div>
            </div>
            <div class="col-md-2 mb10 pull-right">
                <input type="button" class="btn purple btn-block donut_button" id = "save_donut_btn" value="Save Donut Purchase"  style="display: none;" onclick="donut.saveDonutData();">
                <input type="button" class="btn purple btn-block donut_button" id = "save_donut_day_data_btn" value="Save Last Week Data"  style="display: none;" onclick="donut.saveDonutLastWeekData();">
            </div>
            <div class="table-scrollable" id="donut-data"></div>
        </form>
    </div>
</div>