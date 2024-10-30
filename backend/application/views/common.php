<?php
$min_year = 2018;
$max_year = 2025;
?>
<style type="text/css">
    /*Start Wizard*/
    input{
        display:inline-block;
        width:65%;
    }
    .bootstrapWizard {
        display: block;
        list-style: none;
        padding: 0;
        position: relative;
        width: 100%
    }

    .bootstrapWizard a:hover,.bootstrapWizard a:active,.bootstrapWizard a:focus {
        text-decoration: none
    }

    .bootstrapWizard li {
        display: block;
        float: left;
        width: 8%;
        text-align: center;
        padding-left: 0
    }

    .bootstrapWizard li:before {
        border-top: 3px solid #55606E;
        content: "";
        display: block;
        font-size: 0;
        overflow: hidden;
        position: relative;
        top: 11px;
        right: 1px;
        width: 100%;
        z-index: 1
    }

    .bootstrapWizard li:first-child:before {
        left: 50%;
        max-width: 50%
    }

    .bootstrapWizard li:last-child:before {
        max-width: 50%;
        width: 50%
    }

    .bootstrapWizard li.complete .step {
        background: #0aa66e;
        padding: 1px 6px;
        border: 3px solid #55606E
    }

    .bootstrapWizard li .step i {
        font-size: 10px;
        font-weight: 400;
        position: relative;
        top: -1.5px
    }

    .bootstrapWizard li .step {
        background: #B2B5B9;
        color: #fff;
        display: inline;
        font-size: 15px;
        font-weight: 700;
        line-height: 12px;
        padding: 7px 13px;
        border: 3px solid transparent;
        border-radius: 50% !important;
        line-height: normal;
        position: relative;
        text-align: center;
        z-index: 2;
        transition: all .1s linear 0s
    }

    .bootstrapWizard li.active .step,.bootstrapWizard li.active.complete .step {
        background: #0091d9;
        color: #fff;
        font-weight: 700;
        padding: 7px 13px;
        font-size: 15px;
        border-radius: 50%;
        border: 3px solid #55606E
    }

    .bootstrapWizard li.complete .title,.bootstrapWizard li.active .title {
        color: #2B3D53
    }

    .bootstrapWizard li .title {
        color: #bfbfbf;
        display: block;
        font-size: 13px;
        line-height: 15px;
        max-width: 100%;
        position: relative;
        table-layout: fixed;
        text-align: center;
        top: 20px;
        word-wrap: break-word;
        z-index: 104
    }

    .wizard-actions {
        display: block;
        list-style: none;
        padding: 0;
        position: relative;
        width: 100%
    }

    .wizard-actions li {
        display: inline
    }

    .tab-content.transparent {
        background-color: transparent
    }

    /*End Wizard*/

    .project-tab #tabs{
        background: #007b5e;
        color: #eee;
    }
    .project-tab #tabs h6.section-title{
        color: #eee;
    }
    .project-tab #tabs .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
        color: #0062cc;
        background-color: transparent;
        border-color: transparent transparent #f3f3f3;
        border-bottom: 3px solid !important;
        font-size: 22px;
        font-weight: bold;
        padding: 10px 18px 1px 18px;
    }
    .project-tab .nav-link {
        border: 1px solid transparent;
        border-top-left-radius: .25rem;
        border-top-right-radius: .25rem;
        color: #0062cc;
        font-size: 22px;
        font-weight: 600;
        padding: 10px 18px 1px 18px;
    }
    .no-data{
        color:red;
    }
    .project-tab .nav-link:hover {
        border: none;
    }
    .project-tab thead{
        background: #f3f3f3;
        color: #333;
    }
    .project-tab a{
        text-decoration: none;
        color: #333;
        font-weight: 600;
    }
    #loader {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        z-index: 10000;
        background: rgba(0,0,0,0.75) url(./loader.gif) no-repeat center center;
    }
    table thead th {
        word-break: break-word;
        vertical-align: top;
    }
    th, td { white-space: nowrap;word-break: break-word; text-align:center; }
    div.dataTables_wrapper {
        margin: 0 auto;
    }
    .tab-pane .alert{
        padding: 10px;
        padding-right: 35px;
    }
    table.dataTable {
        margin-top: 0px !important;
        margin-bottom: 0px !important;
    }
    div.DTFC_LeftWrapper table.dataTable.no-footer {
        border-bottom: 1px solid #fff;
    }

    #PopupConfirmation  #mismach_records .table{
        overflow-x: scroll !important;
        display: block !important;
    }

    .mt-element-list .list-default.mt-list-container ul>.mt-list-item>.list-item-content{
        padding: 0 0 0 60px !important;
    }

    .mt-element-list .list-default.mt-list-head .list-title{
        margin: 0 !important;
    }
</style>

    <div class="row">
        <div class="col-xs-12">
             <?php echo $this->session->flashdata('msg'); ?>
        </div>
    </div>
   
    <div class="portlet box green-meadow">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase">Import Data</span>
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand" data-original-title="" title=""> </a>
            <a href="" class="fullscreen"> </a>
        </div>
    </div>
     
    <div class="portlet-body portlet-collapsed">
        <?php
        $attributes = array('class' => 'form-horizontal validate', 'id' => 'submit_form_file');
        echo form_open_multipart('dailysales/import', $attributes);
        ?>
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Select File Type<span class="required" aria-required="true"> * </span>', 'store', array('class' => 'col-md-4 control-label')); ?>
                <div class="col-md-4">
                    <select class="form-control" name="select_type" id="select_type" required>
                        <option value="">-- Select Type</option>
                        <option value="dailysales">Daily Sales</option>
                        <option value="masterpos">Master Pos</option>
                        <option value="donutcount">Donut Count</option>
                        <option value="payroll">Master Payroll</option>
                        <option value="review">Customer Review</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Select a file upload<span class="required" aria-required="true"> * </span>', 'choose_file', array('class' => 'col-md-4 control-label')); ?>
                <div class="col-md-6">
                    <input type="file" name="import_file" id="import_file" required="" extension="csv,xls,xlsx,xlsm"/>
                </div>
            </div>
        </div>
        <div class="form-group">
            <div class="col-md-4"></div>
            <div class="col-md-2 ">
                <input type="submit" value="Save" id="submit" class="btn blue pull-right">
            </div>
            <div class="col-md-4"></div>
        </div>

        <?php echo form_close(); ?>
    </div>
</div>
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase">Daily Sales</span>
        </div>
        <div class="tools">
            <a href="javascript:;" class="collapse" data-original-title="" title=""> </a>
            <a href="" class="fullscreen"> </a>
        </div>
    </div>
    <div class="portlet-body">
        <div id="loader"></div>
        <!------ Include the above in your HEAD tag ---------->
        <section id="tabs" class="project-tab">
            <div class="row">
                <div class="col-md-12">

                    <span id='err-msg' class="no-data"></span>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php echo form_label('Select Store<span class="required" aria-required="true"> * </span>', 'store', array('class' => 'col-md-4 control-label')); ?>
                            <div class="col-md-4">
                                <?php
                                $options = array();
                                $options[''] = '-- Select Store  --';
                                $store_id = field(set_value('store_id', NULL), $this->input->get('store_id'));
                                if (isset($store_list['records']) && !empty($store_list['records'])) {
                                    foreach ($store_list['records'] as $row) {
                                        $options[$row->key] = $row->name . " (" . $row->key . ")";
                                    }
                                }
                                echo form_dropdown(array('required' => 'required', 'id' => 'store_id', 'name' => 'store_key', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $store_id));
                                ?>
                            </div>
                        </div>
                    </div><br><br>
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php echo form_label('Select Year<span class="required" aria-required="true"> * </span>', 'year', array('class' => 'col-md-4 control-label')); ?>
                            <div class="col-md-4">
                                <?php
                                $options = array();
                                $options[''] = '-- Year  --';
                                for ($yi = $min_year; $yi <= $max_year; $yi++) {
                                    $options[$yi] = $yi;
                                }

                                echo form_dropdown(array('required' => 'required', 'id' => 'year', 'name' => 'year', 'options' => $options, 'class' => 'form-control select2me'));
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <article class="col-sm-12 col-md-12 col-lg-12 sortable-grid ui-sortable">
                            <div id="wid-id-0">
                                <!-- widget div-->
                                <div role="content">
                                    <!-- widget content -->
                                    <div class="widget-body">
                                        <div class="row">
                                            <form id="wizard-1" novalidate="novalidate">
                                                <div id="months-tabbing-common" class="col-sm-12">
                                                    <div class="form-bootstrapWizard">
                                                        <ul class="bootstrapWizard form-wizard">
                                                            <li class="active" data-target="#step1" id="month_1">
                                                                <a href="#tab1" data-toggle="tab" class="active"> <span class="step">1</span> <span class="title">January</span> </a>
                                                            </li>
                                                            <li data-target="#step2" class="" id="month_2">
                                                                <a href="#tab2" data-toggle="tab"> <span class="step">2</span> <span class="title">February</span> </a>
                                                            </li>
                                                            <li data-target="#step3" class="" id="month_3">
                                                                <a href="#tab3" data-toggle="tab"> <span class="step">3</span> <span class="title">March</span> </a>
                                                            </li>
                                                            <li data-target="#step4" id="month_4">
                                                                <a href="#tab4" data-toggle="tab"> <span class="step">4</span> <span class="title">April</span> </a>
                                                            </li>
                                                            <li data-target="#step5" id="month_5">
                                                                <a href="#tab4" data-toggle="tab"> <span class="step">5</span> <span class="title">May</span> </a>
                                                            </li>
                                                            <li data-target="#step6" id="month_6">
                                                                <a href="#tab4" data-toggle="tab"> <span class="step">6</span> <span class="title">June</span> </a>
                                                            </li>
                                                            <li data-target="#step7" id="month_7">
                                                                <a href="#tab4" data-toggle="tab"> <span class="step">7</span> <span class="title">July</span> </a>
                                                            </li>
                                                            <li data-target="#step8" id="month_8">
                                                                <a href="#tab4" data-toggle="tab"> <span class="step">8</span> <span class="title">August</span> </a>
                                                            </li>
                                                            <li data-target="#step9" id="month_9">
                                                                <a href="#tab4" data-toggle="tab"> <span class="step">9</span> <span class="title">September</span> </a>
                                                            </li>
                                                            <li data-target="#step10" id="month_10">
                                                                <a href="#tab4" data-toggle="tab"> <span class="step">10</span> <span class="title">October</span> </a>
                                                            </li>
                                                            <li data-target="#step11" id="month_11">
                                                                <a href="#tab4" data-toggle="tab"> <span class="step">11</span> <span class="title">November</span> </a>
                                                            </li>
                                                            <li data-target="#step12" id="month_12">
                                                                <a href="#tab4" data-toggle="tab"> <span class="step">12</span> <span class="title">December</span> </a>
                                                            </li>
                                                        </ul>
                                                        <div class="clearfix"></div>
                                                    </div>

                                                </div>
                                        </div>

                                    </div>
                                    <!-- end widget content -->

                                </div>
                                <!-- end widget div -->

                            </div>
                            <!-- end widget -->

                        </article>
                    </div>
                    <br><br>
                    <nav>
                        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
                            <a class="nav-item nav-link active" id="tab_master-pos" data-toggle="tab" href="#master-pos" role="tab" >Master Pos</a>
                            <a class="nav-item nav-link" id="tab_paid-out-recap" data-toggle="tab" href="#paid-out-recap" role="tab" >Paid Out Recap</a>
                            <a class="nav-item nav-link" id="tab_card-recap" data-toggle="tab" href="#card-recap" role="tab" >Cards Recap</a>
                            <a class="nav-item nav-link" id="tab_delivery-recap" data-toggle="tab" href="#delivery-recap" role="tab" >Delivery Recap</a>
                            <a class="nav-item nav-link" id="tab_monthly-recap" data-toggle="tab" href="#monthly-recap" role="tab" >Monthly Recap</a>
                            <a class="nav-item nav-link" id="tab_payroll" data-toggle="tab" href="#payroll" role="tab" >Master Payroll</a>
                            <a class="nav-item nav-link" id="tab_donutcount" data-toggle="tab" href="#donutcount" role="tab" >Donut Count</a>
                        </div>
                    </nav>
                    <div class="tab-content" id="nav-tabContent">

                        <div class="tab-pane fade" id="master-pos" role="tabpanel" aria-labelledby="master-pos-tab">
                            <div id="data_master-pos"><p class="no-data"><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>No More Selected data</div></p></div>
                        </div>
                        <div class="tab-pane fade" id="paid-out-recap" role="tabpanel" aria-labelledby="paid-out-recap-tab">
                            <div id="data_paid-out-recap"><p  class="no-data"><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>No More Selected data</div></p></div>
                        </div>

                        <div class="tab-pane fade" id="card-recap" role="tabpanel" aria-labelledby="card-recap-tab">
                            <div id="data_card-recap"><p class="no-data"><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>No More Selected data</div></p></div>
                        </div>
                        <div class="tab-pane fade" id="delivery-recap" role="tabpanel" aria-labelledby="delivery-recap-tab">
                            <div id="data_delivery-recap"><p class="no-data"><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>No More Selected data</div></p></div>
                        </div>
                        <div class="tab-pane fade" id="monthly-recap" role="tabpanel" aria-labelledby="monthly-recap-tab">
                            <div id="data_monthly-recap"><p class="no-data"><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>No More Selected data</div></p></div>
                        </div>
                        <div class="tab-pane fade" id="payroll" role="tabpanel" aria-labelledby="payroll">
                            <div id="data_payroll"><p class="no-data"><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>No More Selected data</div></p></div>
                        </div>
                        <div class="tab-pane fade" id="donutcount" role="tabpanel" aria-labelledby="donutcount">
                            <div id="data_donutcount"><p class="no-data"><div class="alert alert-danger alert-dismissable"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>No More Selected data</div></p></div>
                        </div>
                    </div>
                </div>
            </div>

        </section>

    </div>
</div>
<div class="modal fade" id="PopupConfirmationIfNoError">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Are you sure want to import?</h4>
            </div>
            <div class="modal-body">
                <div class="mt-element-list">
                <p>Are you sure want to import?</p>                  
                </div>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default pull-left" type="button">No</button>
                <button class="btn btn-danger" type="button" onclick="forceSubmitForm();">Yes</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="PopupConfirmation">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Are you sure want to import?</h4>
            </div>
            <div class="modal-body">
                <div class="mt-element-list">
                <p>There is something fishy with below records so that we can not import!
                <br>You may check those records and update sheet. Click on NO to Exit.
                <br>Click on YES if you want to ignore below records and import rest of the data.</p>
                <div class="mt-list-head list-default green-haze">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="list-head-title-container">
                                <h3 class="list-title uppercase sbold pull-left">Duplicate Records</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-list-container list-default">
                    <ul id=duplicate_records> No Records Found. </ul>
                </div>                
                <div class="mt-list-head list-default green-haze">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="list-head-title-container">
                                <h3 class="list-title uppercase sbold">Date Diff Records</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-list-container list-default">
                    <ul id=date_diff_records> No Records Found. </ul>
                </div>
                <div class="mt-list-head list-default green-haze">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="list-head-title-container">
                                <h3 class="list-title uppercase sbold"> Mismatched Locked Entries from DB</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-list-container list-default">
                    <ul id=duplicate_db_records> No Records Found. </ul>
                </div>                                 
                </div>                
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default pull-left" type="button">No</button>
                <button class="btn btn-danger" type="button" onclick="forceSubmitForm();">Yes</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="finalResultDisplay">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">File Summary</h4>
            </div>
            <div class="modal-body">
                <div class="mt-element-list">
                <p>Congratulation! Your file is processed please find the summary below:</p>
                <p id='total_record_msg'></p>
                <div class="mt-list-head list-default blue">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="list-head-title-container">
                                <h3 class="list-title uppercase sbold pull-left">Success</h3>
                                <span class="badge badge-white pull-right font-lg sbold" id="success-counter">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-list-container list-default">
                    <ul id='success_msg'></ul>
                </div>
                <div class="mt-list-head list-default red">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="list-head-title-container">
                                <h3 class="list-title uppercase sbold pull-left">Failed</h3>
                                <span class="badge badge-white pull-right font-lg sbold" id="fail-counter">0</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-list-container list-default">
                    <ul id='fail_msg'></ul>
                </div>                
                </div>                
            </div>
            <div class="modal-footer">
                <button class="btn btn-success" type="button" onclick="window.location.reload();">Ok</button>
            </div>
        </div>
    </div>
</div>
<script>
    function forceSubmitForm() {
        $('#submit_form_file').append($("<input>").attr("type", "hidden").attr("name", "is_forced").val(true));
        $("#submit_form_file").submit();
        $('#PopupConfirmation').modal('hide');
    }

    $("#submit_form_file").submit(function (event) {
        $("#loadingmessage").show();
        if ($("#submit_form_file").valid()) {
            var url = $("#select_type").val() + "/import/";
            if ($("#select_type").val() != 'masterpos') {
                $("#submit_form_file").attr("action", url)
                return true;
            }
            var form_data = new FormData($('#submit_form_file')[0]);
            jQuery.ajax({
                type: "POST",
                url: url,
                data: form_data,
                processData: false,
                contentType: false,
                success: function(res) {
                    res = JSON.parse(res);
                    if (res.type == 'error' && res.popup) {
                        // $('#PopupConfirmation').find('.modal-body p').html(res.message);

                        if(Object.keys(res.date_diff).length 
                            || Object.keys(res.duplicate_db).length 
                            || Object.keys(res.duplicate).length)
                        {                           
                            if(Object.keys(res.duplicate).length)
                            {
                                var duplicateRecords = "";
                                $.each(res.duplicate, function(rowNumber, rowData) {
                                    duplicateRecords += '<li class="mt-list-item">';
                                    duplicateRecords += '<div class="list-icon-container done">';
                                    duplicateRecords += '<i class="fa fa-clone" aria-hidden="true"></i>';
                                    duplicateRecords += '</div>';
                                    duplicateRecords += '<div class="list-item-content">';
                                    duplicateRecords += '<table class="table table-bordered table-hover">';
                                    duplicateRecords += '<thead">';
                                    duplicateRecords += '<tr class="uppercase">';
                                    duplicateRecords += "<th>Row</th>";
                                    duplicateRecords += "<th>Store Number</th>";
                                    duplicateRecords += "<th>Start Business Date</th>";
                                    duplicateRecords += "<th>End Business date</th>";
                                    $.each(rowData.result, function(columnName, columnData) {
                                        duplicateRecords += "<th>"+ columnName.replaceAll("_", " ") + "</th>";
                                    });
                                    duplicateRecords += '</tr>';
                                    duplicateRecords += '</thead">';                            
                                    duplicateRecords += '<tbody">';
                                    duplicateRecords += '<tr>';
                                    duplicateRecords += "<td>"+ rowNumber + "</td>";
                                    duplicateRecords += "<td>"+ rowData.store_key + "</td>";
                                    duplicateRecords += "<td>"+ rowData.start_date + "</td>";
                                    duplicateRecords += "<td>"+ rowData.end_date + "</td>";
                                    $.each(rowData.result, function(columnName, columnData) {
                                        duplicateRecords += "<td>"+ columnData.old_value + "</td>";
                                    });
                                    duplicateRecords += '</tr>';
                                    duplicateRecords += '<tr>';
                                    var duplicatedRowNumber = rowData.mismatched_row == undefined ? rowData.duplicated_row : rowData.mismatched_row;
                                    duplicateRecords += "<td>"+ duplicatedRowNumber  + "</td>";
                                    duplicateRecords += "<td>"+ rowData.store_key + "</td>";
                                    duplicateRecords += "<td>"+ rowData.start_date + "</td>";
                                    duplicateRecords += "<td>"+ rowData.end_date + "</td>";
                                    $.each(rowData.result, function(columnName, columnData) {
                                        duplicateRecords += "<td>"+ columnData.new_value + "</td>";
                                    });
                                    duplicateRecords += '</tr>';                                                        
                                    duplicateRecords += '</tbody">';
                                    duplicateRecords += '</table>';
                                    duplicateRecords += '</div>';
                                    duplicateRecords += '</li>';
                                });
                                //fill popup
                                $('#PopupConfirmation').find('.modal-body #duplicate_records').html(duplicateRecords);
                            }

                            if(Object.keys(res.date_diff).length)
                            {
                                var dateDiffRecords = "";
                                $.each(res.date_diff, function(rowNumber, rowData) {
                                    dateDiffRecords += '<li class="mt-list-item">';
                                    dateDiffRecords += '<div class="list-icon-container done">';
                                    dateDiffRecords += '<i class="fa fa-calendar" aria-hidden="true"></i>';
                                    dateDiffRecords += '</div>';
                                    dateDiffRecords += '<div class="list-item-content">';
                                    dateDiffRecords += '<h3 class="uppercase bold">Row ' + rowNumber + '</h3>';
                                    dateDiffRecords += "<p>This Row can not be imported due to "+rowData.result+" Days Differance.</p>";
                                    dateDiffRecords += '</div>';
                                    dateDiffRecords += '</li>';
                                });
                                
                                //fill popup
                                $('#PopupConfirmation').find('.modal-body #date_diff_records').html(dateDiffRecords);
                            }

                            if(Object.keys(res.duplicate_db).length)
                            {
                                var duplicateDBRecords = "";
                                $.each(res.duplicate_db, function(rowNumber, rowData) {
                                    duplicateDBRecords += '<li class="mt-list-item">';
                                    duplicateDBRecords += '<div class="list-icon-container done">';
                                    duplicateDBRecords += '<i class="fa fa-clone" aria-hidden="true"></i>';
                                    duplicateDBRecords += '</div>';
                                    duplicateDBRecords += '<div class="list-item-content">';
                                    duplicateDBRecords += '<table class="table table-bordered table-hover">';
                                    duplicateDBRecords += '<thead">';
                                    duplicateDBRecords += '<tr class="uppercase">';
                                    duplicateDBRecords += "<th>Row</th>";
                                    duplicateDBRecords += "<th>Store Number</th>";
                                    duplicateDBRecords += "<th>Start Business Date</th>";
                                    duplicateDBRecords += "<th>End Business date</th>";
                                    $.each(rowData.result, function(columnName, columnData) {
                                        duplicateDBRecords += "<th>"+ columnName.replaceAll("_", " ") + "</th>";
                                    });
                                    duplicateDBRecords += '</tr>';
                                    duplicateDBRecords += '</thead">';                            
                                    duplicateDBRecords += '<tbody">';
                                    duplicateDBRecords += '<tr>';
                                    duplicateDBRecords += "<td>System Data</td>";
                                    duplicateDBRecords += "<td>"+ rowData.store_key + "</td>";
                                    duplicateDBRecords += "<td>"+ rowData.start_date + "</td>";
                                    duplicateDBRecords += "<td>"+ rowData.end_date + "</td>";
                                    $.each(rowData.result, function(columnName, columnData) {
                                        duplicateDBRecords += "<td>"+ columnData.old_value + "</td>";
                                    });
                                    duplicateDBRecords += '</tr>';
                                    duplicateDBRecords += '<tr>';
                                    var duplicatedRowNumber = rowData.duplicated_row;
                                    duplicateDBRecords += "<td>"+ duplicatedRowNumber  + "</td>";
                                    duplicateDBRecords += "<td>"+ rowData.store_key + "</td>";
                                    duplicateDBRecords += "<td>"+ rowData.start_date + "</td>";
                                    duplicateDBRecords += "<td>"+ rowData.end_date + "</td>";
                                    $.each(rowData.result, function(columnName, columnData) {
                                        duplicateDBRecords += "<td>"+ columnData.new_value + "</td>";
                                    });
                                    duplicateDBRecords += '</tr>';                                                        
                                    duplicateDBRecords += '</tbody">';
                                    duplicateDBRecords += '</table>';
                                    duplicateDBRecords += '</div>';
                                    duplicateDBRecords += '</li>';
                                });

                                //fill popup
                                $('#PopupConfirmation').find('.modal-body #duplicate_db_records').html(duplicateDBRecords);
                            }

                            $('#PopupConfirmation').modal('show');
                        }
                        else
                        {
                            $('#PopupConfirmationIfNoError').modal('show');
                        }
                    } else {
                        if (res.type == 'success' && res.popup) {                            
                            msg = "<b>Total " + res.sheet_total_records + " records are found in the sheet.</b><br/>";
                            $('#finalResultDisplay').find('.modal-body #total_record_msg').html(msg);
                            $('#finalResultDisplay #success-counter').html(res.success_count);
                            $('#finalResultDisplay #fail-counter').html(res.failure_count);
                            
                            
                            if (res.success) {
                                msg = "<b>Following records are imported successfully!! :-) </b><br/>";
                                msg += res.success.posDailyTotalAddCount + " Row(s) added to Daily Master POS. <br/>";
                                msg += res.success.posDailyTotalUpdatedCount + " Row(s) updated to Daily Master POS. <br/>";
                                msg += res.success.cardRecapTotalcount + " Row(s) added to Card Recap. <br/>";
                                msg += res.success.monthlyRecapTotalcount + " Row(s) added to Montly Recap. <br/>";
                                msg += res.success.deliveryRecapTotalcount + " Row(s) added to Delivery Recap. <br/> <br/> <br/>";
                                msg += res.success.posWeeklyTotalAddCount + " Row(s) added to Weekly Master POS. <br/>";
                                msg += res.success.posWeeklyTotalUpdateCount + " Row(s) updated to Weekly Master POS. <br/>";
                                msg += res.success.posMonthlyTotalAddCount + " Row(s) added to Monthly Master POS. <br/>";
                                msg += res.success.posMonthlyTotalUpdateCount + " Row(s) added to Monthly Master POS. <br/>";
                                $('#finalResultDisplay').find('.modal-body #success_msg').html(msg);
                            }
                            if (res.failure) {
                                msg = "<b>Following records are not imported!! :-( </b><br/>";
                                mismatchCount  = res.failure.mismatch ? (Object.keys(res.failure.mismatch).length) : 0;
                                msg += res.failure.duplicate ? (Object.keys(res.failure.duplicate).length + mismatchCount) + " row(s) is/are duplicated in sheet. <br/>" : "";
                                msg += res.failure.date_diff ? Object.keys(res.failure.date_diff).length + " row(s) has/have invalid date difference. <br/>" : "";
                                msg += res.failure.duplicate_db ? Object.keys(res.failure.duplicate_db).length + " row(s) is/are locked in DB. <br/>" : "";
                                msg += res.failure.other ? Object.keys(res.failure.other).length + " row(s) is/are not valid. you can check failed records in below sheet. <br/><br/>" : "";
                                msg += "<a href=" + res.failed_sheet + " download>Failed Record Sheet</a>";
                                $('#finalResultDisplay').find('.modal-body #fail_msg').html(msg);
                            }
                            $('#finalResultDisplay').modal('show');
                        }
                        else {
                            window.location.reload();
                        }
                    }
                },
                complete: function(res) {
                    $("#loadingmessage").hide();
                }
            });
        } else {
            $("#loadingmessage").hide();
        }
        return false;
    });

    $(document).ready(function () {
        activaTab('master-pos');
    });

    function activaTab(tab) {
        $('.nav-tabs a[href="#' + tab + '"]').tab('show');
    }
    ;
    $(".nav .nav-link").on("click", function () {
        var is_actual_bal_updated = $("#is_actual_bal_updated").val();
        if(is_actual_bal_updated == 1){
              $("#current_row").val($("tr.edit_txns").index());
             $('#ConfirmSaveModal').modal({backdrop: 'static', keyboard: false});
            $("#ConfirmSaveModal").modal("show");
            return false;
        }
        $("#err-msg").text("");
        $(".nav").find(".active").removeClass("active");
        $(this).addClass("active");
        store_id = $("#store_id").val();
        year = $("#year").val();
        month = "";
        //get month of current active li
        $(".bootstrapWizard.form-wizard li").each(function () {
            if ($(this).hasClass("active")) {
                month = $(this).attr("id");
                month = month.split('_').pop();
            }
        });
        type = $(this).attr("id");
        type = type.split('_').pop();
        if (type == "master-pos") {
            method = "getMasterPosGrid";
        }
        if (type == "card-recap") {
            method = "getCardrecapGrid";
        }
        if (type == "monthly-recap") {
            method = "getMonthlyrecapGrid";
        }
        if (type == "paid-out-recap") {
            method = "getPaidoutGrid";
        }
        if (type == "payroll") {
            method = "getPayrollGrid";
        }
        if (type == "donutcount") {
            method = "getDonutcountGrid";
        }
        if (type == "delivery-recap") {
            method = "getDeliveryrecapGrid";
        }
        if (store_id != "" && year != "")
            callajax(store_id, year, month, type, method);
    });
    $("#year").on("change", function () {
        $("#err-msg").text("");
        store_id = $("#store_id").val();
        year = $("#year").val();
        month = "";
        //get month of current active li
        $(".bootstrapWizard.form-wizard li").each(function () {
            if ($(this).hasClass("active")) {
                month = $(this).attr("id");
                month = month.split('_').pop();
            }
        });
        type = $(".nav.nav-tabs a.active").attr("id");
        type = type.split('_').pop();
        method = "getMasterPosGrid";
        if (type == "master-pos") {
            method = "getMasterPosGrid";
        }
        if (type == "card-recap") {
            method = "getCardrecapGrid";
        }
        if (type == "monthly-recap") {
            method = "getMonthlyrecapGrid";
        }
        if (type == "paid-out-recap") {
            method = "getPaidoutGrid";
        }
        if (type == "payroll") {
            method = "getPayrollGrid";
        }
        if (type == "donutcount") {
            method = "getDonutcountGrid";
        }
        if (store_id == "") {
            $("#err-msg").text("Please Select Store");
            return false;
        } else if (store_id != "" && year != "") {
            callajax(store_id, year, month, type, method);
        }
    });
    $("#store_id").on("change", function () {
        $("#err-msg").text("");
        year = $("#year").val();
        store_id = $("#store_id").val();
        month = "";
        //get month of current active li
        $(".bootstrapWizard.form-wizard li").each(function () {
            if ($(this).hasClass("active")) {
                month = $(this).attr("id");
                month = month.split('_').pop();
            }
        });
        type = $(".nav.nav-tabs a.active").attr("id");
        type = type.split('_').pop();
        method = "getDonutcountGrid";
        if (type == "master-pos") {
            method = "getMasterPosGrid";
        }
        if (type == "card-recap") {
            method = "getCardrecapGrid";
        }
        if (type == "monthly-recap") {
            method = "getMonthlyrecapGrid";
        }
        if (type == "paid-out-recap") {
            method = "getPaidoutGrid";
        }
        if (type == "payroll") {
            method = "getPayrollGrid";
        }
        if (type == "donutcount") {
            method = "getDonutcountGrid";
        }
        if (store_id != "" && year != "") {
            callajax(store_id, year, month, type, method);
        }
    });
    $(".bootstrapWizard.form-wizard li").on("click", function () {
        $("#err-msg").text("");
        month = $(this).attr("id");
        month = month.split('_').pop();
        year = $("#year").val();
        store_id = $("#store_id").val();
        type = $(".nav.nav-tabs a.active").attr("id");
        type = type.split('_').pop();
        method = "getMasterPosGrid";
        if (type == "master-pos") {
            method = "getMasterPosGrid";
        }
        if (type == "card-recap") {
            method = "getCardrecapGrid";
        }
        if (type == "monthly-recap") {
            method = "getMonthlyrecapGrid";
        }
        if (type == "paid-out-recap") {
            method = "getPaidoutGrid";
        }
        if (type == "payroll") {
            method = "getPayrollGrid";
        }
        if (type == "donutcount") {
            method = "getDonutcountGrid";
        }
        if (store_id != "" && year != "") {
            callajax(store_id, year, month, type, method);
        } else {
            $("#err-msg").text("Please Select Store and year");
            return false;
        }

    });
    function callajax(store_id, year, month, type, method) {
        $("#loader").show();
        activaTab(type);
        $.ajax({
            url: site_url + 'common/' + method,
            data: {store_key: store_id, year: year, month: month},
            method: 'POST',
            async: false,
            success: function (response) {
                $('#data_' + type).html(response);
                $("#loader").hide();
                return true;
            }
        });

    }
    function calculatetotal(id, i, type) {
        var total = 0;
        if (type == 1) {
            $("#row_" + i).find(".txt_expense").each(function () {
                if ($(this).val() != "" && $(this).val() != NaN && $(this).val() != undefined) {
                    var count = parseFloat($(this).val());
                    total = total + count;
                }
            });
        } else {
            $("#row_" + i).find("input[type='number']").each(function () {
                if ($(this).val() != "" && $(this).val() != NaN && $(this).val() != undefined) {
                    var count = parseFloat($(this).val());
                    total = total + count;
                }
            });
        }

        $("#lbltotal_" + i).text(total.toFixed(2));
        $("#total_" + i).val(parseFloat(total.toFixed(2)));
    }
</script>