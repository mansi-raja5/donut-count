<link href="<?php echo base_url() ?>/assets/css/owl.theme.default.min.css" rel="stylesheet">
<link href="<?php echo base_url() ?>/assets/css/weekly.css" rel="stylesheet">
<link href="<?php echo base_url() ?>assets/css/owl.carousel.min.css" rel="stylesheet">
<script src="<?php echo base_url() ?>assets/js/owl.carousel.min.js"></script>
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption font-dark">
            <i class="icon-settings font-dark"></i>
            <span class="caption-subject bold uppercase">Daily View</span>
        </div>
    </div>
    <div class="portlet-body">
        <div class="row">
            <div class="col-md-12">
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption"><i class="fa fa-filter"></i> Filter</div>
                    </div>
                    <div class="portlet-body">
                        <?php
                        $attributes = array('name' => 'frmSearch', 'id' => 'frmSearch', 'method' => 'post');
                        echo form_open(base_url('daily'), $attributes);
                        ?>
                        <input type="hidden" name="maintab" id="maintab" value="snapshot"/>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Store</label>
                                    <?php
                                    $options = array();
                                    $options[''] = '-- Select Store  --';
                                    $store_key = field(set_value('store_key', NULL));
                                    if (isset($store_list) && !empty($store_list)) {
                                        foreach ($store_list as $row) {
                                            $options[$row->key] = $row->name . " (" . $row->key . ")";
                                        }
                                    }
                                    echo form_dropdown(array('id' => 'store_key', 'name' => 'store_key', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $store_key, 'onchange' => 'get_data(this);'));
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label">Select Date</label>
                                    <?php
                                    $daily_date = field(set_value('daily_date', NULL), (isset($daily_date)) ? date("d-m-Y", strtotime($daily_date)) : NULL);
                                    echo form_input(array('required' => 'required', 'id' => 'daily_date', 'name' => 'daily_date', 'class' => 'form-control', 'value' => $daily_date));
                                    ?>
                                </div>
                            </div>
                        </div>
                        <?php echo form_close(); ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="btn-pref btn-group btn-group-justified btn-group-lg" role="group">
                            <a class="btn-1" id="snapshot_main_id"  data-toggle="tab" href="#snapshot">
                                <span class="txt">
                                    Snapshot
                                </span>
                                <span class="round">
                                    <i class="fa fa-camera">
                                    </i>
                                </span>
                            </a>
                            <a class="btn-1" data-toggle="tab" href="#donut_count">
                                <span class="txt">
                                    Donut Count
                                </span>
                                <span class="round">
                                    <i class="fa fa-dot-circle-o">
                                    </i>
                                </span>
                            </a>
                            <div class="btn-1" data-toggle="tab" href="#paid_out_recap">
                                <span class="txt">
                                    Paid Out Recap
                                </span>
                                <span class="round">
                                    <i class="fa fa-line-chart">
                                    </i>
                                </span>
                            </div>
                            <div class="btn-1" data-toggle="tab" href="#card_recap">
                                <span class="txt">
                                    Card Recap
                                </span>
                                <span class="round">
                                    <i class="fa fa-line-chart">
                                    </i>
                                </span>
                            </div>
                            <div class="btn-1" data-toggle="tab" href="#monthly_recap">
                                <span class="txt">
                                    Monthly Recap
                                </span>
                                <span class="round">
                                    <i class="fa fa-line-chart">
                                    </i>
                                </span>
                            </div>
                        </div>
                        <div class="well main-layout-tabs">
                            <div class="tab-content">
                                <div class="tab-pane fade in" id="snapshot">
                                </div>
                                <div class="tab-pane fade in" id="donut_count">
                                </div>
                                <div class="tab-pane fade in" id="paid_out_recap">
                                </div>
                                <div class="tab-pane fade in" id="card_recap">
                                </div>
                                <div class="tab-pane fade in" id="monthly_recap">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/js/daily.js" type="text/javascript"></script>
<script type="text/javascript">
    let daily = new Daily('<?php echo $default_store_key; ?>', '<?php echo $default_date; ?>', "#snapshot");
    var snapshot_main_id = $('#snapshot_main_id');
    snapshot_main_id.addClass('active');
    daily.showMainTabbing(snapshot_main_id);
    $(document).ready(function () {
        $('#snapshot_main_id').trigger('click');


        $(".btn-pref .btn-1").click(function () {
            $(".btn-pref .btn-1").removeClass("active").addClass("btn-default");
            $(this).removeClass("btn-default").addClass("active");
        });

        $("#daily_date").datepicker({
            format: 'dd-mm-yyyy'
        }).on('changeDate', function (e) {
            var daily_date  = $(this).val();
            var store_key   = $("#store_key").val();
            var main_tab    = $(".btn-group .btn-1.active").attr("href");
            let week1       = new Daily(store_key, daily_date, main_tab);
            week1.showMainTabbing(this);
        });

        $('[data-toggle="tab"]').on('shown.bs.tab', function (e) {
            var daily_date  = $("#daily_date").val();
            var store_key   = $("#store_key").val();
            var main_tab    = ($(this).attr("href"));
            let week2       = new Daily(store_key, daily_date, main_tab);
            week2.showMainTabbing(this);
        });
    });

    function get_data(element) {
        var store_key = $(element).val();
        var daily_date = $("#daily_date").val();
        var main_tab = $(".btn-group .btn-1.active").attr("href");
        $(".item li a[href='#"+store_key+"']").trigger("click");
        $(".item li a[href='#"+store_key+"']").parent().addClass("active");
    }
</script>