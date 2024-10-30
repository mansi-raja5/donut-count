<?php $currency_symbol = $this->session->userdata('currency'); ?>
<style>
    .amcharts-chart-div > a {
        display: none !important;
    }
    .chart-div .title {
        font-size: 15px; 
        margin-bottom: 5px !important;
        font-weight: 700;
        color: #666;
    }

    .chart-div .current-val {
        font-size: 30px;
        font-weight: 700;
        color: #114f6b !important;
        margin-bottom: 0px !important;
    }

    .chart-div .diff-val > span {
        font-size:15px; 
        font-weight: 700;
        /*        color: green;*/
    }

    .chart-div .diff-val > span.red {
        color: #e43a45;
    }

    .chart-div .income-val {
        font-size: 30px;
        font-weight: 700;
        color: green !important;
        margin-bottom: 0px !important;
    }

    .chart-div .net-income-val {
        font-size: 30px;
        font-weight: 700;
        color: #f2784b !important;
        margin-bottom: 0px !important;
    }

    .chart-div .expense-val {
        font-size: 30px;
        font-weight: 700;
        color: #e43a45 !important;
        margin-bottom: 0px !important;
    }

    .portlet > .portlet-title > .tools > a.handle {
        display: inline-block;
        top: -3px;
        position: relative;
        font-size: 13px;
        font-family: FontAwesome;
        color: #ACACAC;
    }

    .portlet > .portlet-title > .tools > a.handle:before {
        content: "\F047";
    }
</style>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<div class="portlet light bordered">
    <div class="portlet-title">
        <div class="caption font-dark">
            <i class="icon-settings font-dark"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body">
        <div class="row">
            <?php
            if (isset($stores['records']) && !empty($stores['records'])) {
                foreach ($stores['records'] as $row) {
                    ?>
                    <div class="col-xl-3 col-lg-3 col-sm-3 col-xs-12" style="margin-bottom: 10px;">
                        <a href="" style="color:#fff; text-decoration: none;">
                            <div class="dashboard-stat red">
                                <div class="visual">
                                    <i class="fa fa-bar-chart-o"></i>
                                </div>
                                <div class="details" style="margin-top:10%;">
                                    <div class="desc">
                                        <?php echo $row->name . " = " . $row->key; ?>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>

                <?php
                }
            }
            ?>

        </div>
    </div>
</div>
