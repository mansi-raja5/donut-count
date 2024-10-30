
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body">
        <?php
        $attributes = array('class' => 'form-horizontal validate', 'id' => 'labor_submit_form');
        echo form_open_multipart('labor/adddata', $attributes);
        ?>
        <?php echo $this->session->flashdata('msg'); ?>
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Select Store<span class="required" aria-required="true"> * </span>', 'store', array('class' => 'col-md-4 control-label')); ?>

                <input type="hidden" name="id" value="<?php echo isset($labordata['id']) ? $labordata['id'] : 0; ?>">
                <div class="col-md-4">
                    <?php
                    $store_id = "";
                    $weekend_date = "";
                    if (isset($labordata)):
                        if (sizeof($labordata) > 0):
                            if ($labordata['store_key'] != "") {
                                $store_id = $labordata['store_key'];
                                $weekend_date = date("d-m-Y", strtotime($labordata['week_ending_date']));
                            }
                        endif;
                    endif;
                    $options = array();
                    $options[''] = '-- Select Store  --';
                    $options['all'] = 'All';
                    // $store_id = field(set_value('store_id', NULL), $this->input->get('store_id'));

                    if (isset($store_list['records']) && !empty($store_list['records'])) {
                        foreach ($store_list['records'] as $row) {
                            $options[$row->key] = $row->name . " (" . $row->key . ")";
                        }
                    }
                    echo form_dropdown(array('required' => 'required', 'id' => 'store_key', 'name' => 'store_key', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $store_id));
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php
                echo form_label('Select Date<span class="required" aria-required="true"> * </span>', 'date', array('class' => 'col-md-4 control-label'));
                ?>
                <div class="col-md-4">
                    <?php
                    echo form_input(array('required' => 'required', 'id' => 'week_ending_date', 'autocomplete' => 'off', 'name' => 'week_ending_date', 'class' => 'form-control datepicker', 'value' => $weekend_date));
                    ?>
                </div>
            </div>
        </div>
        <div id="view_labor">

        </div>

        <div class="form-group">
            <div class="col-xs-2 btn_group">

            </div>
            <div class="col-md-12 display-hide" id="btn_cover">
                <div class="form-group">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4 btn_group">
                        <input type="button" value="Save" id="submit" class="btn blue" onclick="frm_submit();">
                        <input type="submit" class="btn blue display-hide" id="btnSubmit">
                        <input type="button" value="Cancel" id="cancel" class="btn btn-danger">
                        <?php if ($action == "edit"): if ($labordata['tax_percent'] != $admin_percentage): ?>
                                <input type="button" value="Calculate" id="recalculate" class="btn btn-warning">
                            <?php
                            endif;
                        endif;
                        ?>
                    </div>
                </div>
            </div>
        </div>

<?php echo form_close(); ?>
    </div>
</div>
<script>
    $(document).ready(function () {
        actionValue = "<?php echo $action ?>";
        if (actionValue == "edit") {
            $("#week_ending_date").trigger('changeDate');
            $("#week_ending_date").attr('readonly', 'true');

            selected_date = $("#week_ending_date").val();
            getCalculation(actionValue);
        } else {
            $("#week_ending_date").datepicker({
                daysOfWeekDisabled: [0, 1, 2, 3, 4, 5],
                format: 'mm-dd-yyyy',
                autoclose: true
            }).on('changeDate', function (e) {
                getCalculation(actionValue);
            });
        }

        $(document).on('change', '#store_key', function () {
            getCalculation(actionValue);
        });

        function getCalculation(actionValue) {
            if ($('#store_key').val() == "")
            {
                $('#store_key').trigger("click");
                return false;
            }
            selected_date = $("#week_ending_date").val();
            var pieces = selected_date.split("-");
            selected_date = pieces[1] + "-" + pieces[0] + "-" + pieces[2];
            if (selected_date != '') {
                $.ajax({
                    url: site_url + 'labor/getCalculation',
                    data: {action: actionValue, "selected_date": selected_date, "store_key": $("#store_key").val()},
                    method: 'POST',
                    success: function (response) {
                        $('#view_labor').html(response);
                        $("#btn_cover").show();
                        var value = $("#store_key").val();
                        if (value == 'all') {
                            $("#grosspay_div").hide();
                            $("#tax_div").hide();
                            $("#total_pay_div").hide();
                            $("#netSales_div").hide();
                            $("#laborPercentage_div").hide();
                        } else {
                            $("#grosspay_div").show();
                            $("#tax_div").show();
                            $("#total_pay_div").show();
                            $("#netSales_div").show();
                            $("#laborPercentage_div").show();
                        }
                    }
                });
            } else {
//             alert("Please select the date");
            }
        }

    });
    function frm_submit() {
        var actionValue = '<?php echo $action; ?>';
        if (actionValue == 'edit') {
            var Res = confirm("Are you sure you want to update the record");
            if (Res == true) {
               $("#labor_submit_form").submit();
            } else {
                window.location.href = site_url + 'labor';
            }
        } else {
            var store_key = $("#store_key").val();
            var week_ending_date = $("#week_ending_date").val();
            if (store_key != '' && week_ending_date != '') {
                $.ajax({
                    url: site_url + 'labor/checkExist',
                    data: {"selected_date": week_ending_date, "store_key": store_key},
                    method: 'POST',
                    beforeSend: function () {
                        $("#loadingmessage").show();
                    },
                    success: function (response) {
                        $("#loadingmessage").hide();
                        var Res = JSON.parse(response);
                        if (Res.status == "false") {
                            var Res1 = confirm("Labor summary for this week and store already exist Are you sure to update this record?");
                            if (Res1 == true) {
                                $("#btnSubmit").trigger("click");
                            } else {
                                return false;
                            }
                        } else {
                            $("#labor_submit_form").submit();
                        }
                    }
                });
            }
        }
    }
</script>