<?php $action = isset($actionValue) ? $actionValue : 'add'; ?>
<div class="col-md-12" id="grosspay_div">
    <div class="form-group">
        <?php echo form_label('Gross Pay ($)<span class="required" aria-required="true"> * </span>', 'store', array('class' => 'col-md-4 control-label')); ?>
        <div class="col-md-4">
            <input type="number" class="form-control" id="grosspay" value="<?php echo round($labordata['grosspay'], 2) ?>" name ="gross_pay" readonly="" />
        </div>
    </div>
</div>

<div class="col-md-12">
    <div class="form-group">
        <?php echo form_label('Bonus ($)<span class="required" aria-required="true"> * </span>', 'store', array('class' => 'col-md-4 control-label')); ?>
        <div class="col-md-4">
            <input type="number" name="bonus" id="bonus" value="<?php if ($labordata['bonus'] == "")
            echo $bonus;
        else
            echo $labordata['bonus'];
        ?>" class="form-control" />
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
<?php echo form_label('Covid ($)<span class="required" aria-required="true"> * </span>', 'store', array('class' => 'col-md-4 control-label')); ?>
        <div class="col-md-4">
            <input type="number" name="covid" id="covid" value="<?php echo round($labordata['covid'], 2) ?>" class="form-control" />
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
<?php echo form_label('Tax Percentage (%)<span class="required" aria-required="true"> * </span>', 'store', array('class' => 'col-md-4 control-label')); ?>
        <div class="col-md-4">
            <input type="number" name="tax_percentage" id="tax_percentage" value="<?php echo round($labordata['tax_percent'], 2) ?>" class="form-control" readonly/>
        </div>
        <span style="color:red;font-size:14px;text-align:right">Current Tax Percent is <?php echo $admin_percentage; ?><span>
                </div>
                </div>
                <div class="col-md-12" id="tax_div">
                    <div class="form-group">
<?php echo form_label('Tax ($)<span class="required" aria-required="true"> * </span>', 'store', array('class' => 'col-md-4 control-label')); ?>
                        <div class="col-md-4">
                            <input type="number" name="tax_amount" id="taxamount" value="<?php echo round($labordata['tax_amount'], 2) ?>" class="form-control" readonly/>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" id="total_pay_div">
                    <div class="form-group">
<?php echo form_label('Total Pay ($)<span class="required" aria-required="true"> * </span>', 'store', array('class' => 'col-md-4 control-label')); ?>
                        <div class="col-md-4">
                            <input type="number" name="total_pay" id="totalpay" value="<?php echo round($labordata['total_pay'], 2) ?>" class="form-control" readonly/>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" id="netSales_div">
                    <div class="form-group">
<?php echo form_label('Net Sales ($)<span class="required" aria-required="true"> * </span>', 'store', array('class' => 'col-md-4 control-label')); ?>
                        <div class="col-md-4">
                            <input type="number" name="net_sales" id="netsales" value="<?php echo round($labordata['net_sales'], 2) ?>" class="form-control" readonly/>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" id="laborPercentage_div">
                    <div class="form-group">
<?php echo form_label('Labor Percentage (%)<span class="required" aria-required="true"> * </span>', 'store', array('class' => 'col-md-4 control-label')); ?>
                        <div class="col-md-4">
                            <input type="number" name="labor_percentage" id="laborpercentage" value="<?php echo round($labordata['labor_percentage'], 2) ?>" class="form-control" readonly />
                        </div>
                    </div>
                </div>

               
                <script>
                    
                    $("#bonus, #covid").on("change", function () {
                        calculateData($("#tax_percentage").val());
                    });
                    $(document).on("click", "#recalculate", function () {
                        calculateData(<?php echo $admin_percentage ?>);
                    });
                    $(document).on("click", "#cancel", function () {
                        window.location.href = site_url + 'labor';
                    });
                    function calculateData(taxpercent) {
                        bonus = $("#bonus").val();
                        covid = $("#covid").val();
                        grosspay = $("#grosspay").val();
                        totalpay = parseFloat(bonus) + parseFloat(covid) + parseFloat(grosspay);
                        tax = (totalpay * parseFloat(taxpercent)) / 100;
                        totalpay = parseFloat(bonus) + parseFloat(covid) + parseFloat(grosspay) + parseFloat(tax);
                        $("#tax_percentage").val(taxpercent);
                        $("#taxamount").val(tax.toFixed(2));
                        $("#totalpay").val(totalpay.toFixed(2));
                        netsales = parseFloat($("#netsales").val());
                        laborpercentage = ((totalpay / netsales) * 100).toFixed(2);
                        $("#laborpercentage").val(laborpercentage)
                    }
                   
                </script>