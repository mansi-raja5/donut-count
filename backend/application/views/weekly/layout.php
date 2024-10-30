<link href="<?php echo base_url() ?>assets/css/owl.theme.default.min.css" rel="stylesheet">
<link href="<?php echo base_url() ?>assets/css/weekly.css" rel="stylesheet">
<link href="<?php echo base_url() ?>assets/css/owl.carousel.min.css" rel="stylesheet">
<script src="<?php echo base_url() ?>assets/js/owl.carousel.min.js"></script>
<link href="<?php echo base_url() ?>assets/global/plugins/bootstrap-multiselect/css/bootstrap-multiselect.css" rel="stylesheet">
<script src="<?php echo base_url() ?>assets/global/plugins/bootstrap-multiselect/js/bootstrap-multiselect.js"></script>
<link href="<?php echo base_url() ?>assets/global/plugins/largetable/css/largetable.css" rel="stylesheet">
<script src="<?php echo base_url() ?>assets/global/plugins/largetable/js/largetable.js"></script>
<script src="<?php echo base_url() ?>assets/global/plugins/table2excel/js/xlsx.core.min.js"></script>
<script src="<?php echo base_url() ?>assets/global/plugins/table2excel/js/filesaver.min.js"></script>
<script src="<?php echo base_url() ?>assets/global/plugins/table2excel/js/table2excel.min.js"></script>
<?php
$default_store_key = isset($store_list[0]->key) ? $store_list[0]->key : '';
$default_date = (date('D') != 'Sat') ? date('Y-m-d', strtotime('next Saturday')) : date('Y-m-d');
?>
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption font-dark">
            <i class="icon-settings font-dark"></i>
            <span class="caption-subject bold uppercase">Weekly View</span>
        </div>
        <button type="button" class="btn btn-warning btn-sm pull-right" onclick="location.reload();">SHOW DEFAULT VIEW</button>
    </div>
    <div class="portlet-body-no">
        <div class="row">
            <div class="col-md-12">
                        <div class="btn-pref btn-group btn-group-justified btn-group-lg" role="group">
                            <div class="btn-1" data-toggle="tab" href="#labor">
                                <span class="txt">
                                    Labor
                                </span>
                                <span class="round">
                                    <i class="fa fa-user">
                                    </i>
                                </span>
                            </div>
                            <div class="btn-1 top-selections" data-toggle="tab" href="#sales_comparision">
                                <span class="txt">
                                    Sales Comparison
                                </span>
                                <span class="round">
                                    <i class="fa fa-bar-chart">
                                    </i>
                                </span>
                            </div>
                            <div class="btn-1" data-toggle="tab" href="#snapshot">
                                <span class="txt">
                                    Snapshot
                                </span>
                                <span class="round">
                                    <i class="fa fa-camera">
                                    </i>
                                </span>
                            </div>
                           
                        </div>
                        <div class="well main-layout-tabs">
                            <div class="tab-content">
                                <div class="tab-pane fade in active" id="labor">
                                </div>
                                <div class="tab-pane fade in" id="sales_comparision">
                                </div>
                                <div class="tab-pane fade in" id="snapshot">
                                </div>
                               
                            </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/js/weekly.js" type="text/javascript"></script>
<script type="text/javascript">
    let week = new Weekly($("#maintab").val());
    $(document).ready(function () {
        $(".btn-pref .btn-1").click(function () {
            $(".btn-pref .btn-1").removeClass("active").addClass("btn-default");
            $(this).removeClass("btn-default").addClass("active");
        });
          $("#weekend_date").datepicker({
            daysOfWeekDisabled: [0, 1, 2, 3, 4, 5],
            format: 'mm/dd/yyyy'

        }).on('changeDate', function (e) {
            var weekend_date = $(this).val();
            var store_key = $("#store_key").val();
            var main_tab = $(".btn-group .btn-1.active").attr("href");
            let week1 = new Weekly(main_tab);
            week1.showMainTabbing(this);
        });
//        $("#weekend_date").datepicker({
//            daysOfWeekDisabled: [0, 1, 2, 3, 4, 5],
//            format: 'mm/dd/yyyy'
//
//        }).on('changeDate', function (e) {
//            var weekend_date = $(this).val();
//            var store_key = $("#store_key").val();
//            var main_tab = $(".btn-group .btn-1.active").attr("href");
//            let week1 = new Weekly(main_tab);
//            week1.showMainTabbing(this);
//        });

        $('[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var weekend_date = $("#weekend_date").val();
            var store_key = $("#store_key").val();
            var main_tab = ($(this).attr("href"));
            let week2 = new Weekly(main_tab);
            week2.showMainTabbing(this);
        });

    });
    function get_data(element) {
        var store_key = $(element).val();
        var weekend_date = $("#weekend_date").val();
        var main_tab = $(".btn-group .btn-1.active").attr("href");
        $(".item li a[href='#"+store_key+"']").trigger("click");
        $(".item li a[href='#"+store_key+"']").parent().addClass("active");
    }
</script>