<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body form">
        <?php
        $attributes = array('name' => 'frmSearch', 'id' => 'frmSearch');
        echo form_open("reports/ledger_report", $attributes);
        ?>
        <div class="form-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <?php
                        $from_date = field(set_value('from_date', NULL), $this->input->post('from_date'));
                        echo form_label('From Date', 'from_date', array('class' => 'control-label'));
                        ?>
                        <input type="text" class="form-control datepicker" readonly="" name="from_date" value="<?php echo $from_date; ?>"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?php echo form_label('To Date', 'to_date', array('class' => 'control-label')); ?>
                        <input type="text" class="form-control datepicker" readonly="" name="to_date"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?php echo form_label('Ledger Description', 'ledger_desc', array('class' => 'control-label')); ?>
                        <input type="text" class="form-control" name="ledger_desc"/>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?php echo form_label('Bank Description', 'bank_desc', array('class' => 'control-label')); ?>
                        <input type="text" class="form-control" name="bank_desc"/>
                    </div>
                </div>

            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <?php echo form_label('Status', 'is_reconcile', array('class' => 'control-label')); ?>
                        <?php
                        $options = array();
                        $options[''] = '-- All  --';
                        $options['C'] = 'Completed';
                        $options['P'] = 'Pending';
                        $is_reconcile = field(set_value('is_reconcile', NULL), $this->input->post('is_reconcile'));
                        echo form_dropdown(array('id' => 'is_reconcile', 'name' => 'is_reconcile', 'options' => $options, 'class' => 'form-control', 'selected' => $is_reconcile));
                        ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?php echo form_label('Reconcile Type', 'reconcile_type', array('class' => 'control-label')); ?>
                        <?php
                        $options = array();
                        $options[''] = '-- All  --';
                        $options['auto'] = 'Auto';
                        $options['manual'] = 'Manual';
                        $options['void'] = 'Void';
                        $options['adjustment'] = 'Adjustment';
                        $reconcile_type = field(set_value('reconcile_type', NULL), $this->input->post('reconcile_type'));
                        echo form_dropdown(array('id' => 'reconcile_type', 'name' => 'reconcile_type', 'options' => $options, 'class' => 'form-control', 'selected' => $reconcile_type));
                        ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?php
                        $attachment = field(set_value('attachment', NULL), $this->input->post('attachment'));
                        echo form_label('Attachments', 'attachment', array('class' => 'control-label'));
                        ?>
                        <div>
                            <label class="mt-radio mt-radio-outline">
                                <input type="radio" name="attachment" id="attachment_i" value="I" class="radio" <?php echo $attachment == 'I' ? "checked" : ""; ?> >Invoice
                                <span></span>
                            </label>
                            <label class="mt-radio mt-radio-outline"   data-placement="top">
                                <input type="radio" name="attachment" id="attachment_o" value="O" class="radio" <?php echo $attachment == 'O' ? "checked" : ""; ?>>Other Documents
                                <span></span>
                            </label>
                            <label class="mt-radio mt-radio-outline"   data-placement="top">
                                <input type="radio" name="attachment" id="attachment_all" value="all" class="radio" <?php echo $attachment == 'all' ? "checked" : ""; ?>>All
                                <span></span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row right">
                <div class="form-group">
                    <div class="col-xs-12 btn_group">
                        <input type="submit" value="Report Data" id="submit" class="btn blue">
                        <input type="submit" value="Download" id="download_submit" class="btn blue">
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php if (isset($reports_data) && !empty($reports_data)) { ?>
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <i class="fa fa-globe"></i>
                <span class="caption-subject bold uppercase">Ledger Report</span>
            </div>
        </div>

        <div class="portlet-body ">

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped" id="tblListing">
                        <thead>
                            <tr>
                                <th>Ledger Date</th>
                                <th>Description</th>
                                <th>$$$</th>
                                <th>Bank Date</th>
                                <th>Description</th>
                                <th>$$$</th>
                                <th>Reconcile Status</th>
                                <th>Reconcile Date</th>
                                <th>Reconcile Type</th>
                                <th>BS Reconciled With</th>
                                <th>Attachments</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $month_name = 'JAN';
                            foreach ($reports_data as $Row) {
                                $month_name = monthName($Row->month);
                                break;
                            }
                            foreach ($reports_data as $Row) {
                                ?>
                                <tr>
                                    <td><?php echo $Row->l_date != '' ? date('d/m/y', strtotime($Row->l_date)) : "N/A"; ?></td>
                                    <td><?php echo ($Row->ledger_desc); ?></td>
                                    <td><?php echo number_format($Row->ledger_amount, 2); ?></td>
                                    <td><?php echo date('d/m/y', strtotime($Row->b_date)); ?></td>
                                    <td><?php echo ($Row->bank_desc); ?></td>
                                    <td><?php echo number_format($Row->bank_amount, 2); ?></td>
                                    <td><?php echo ($Row->status); ?></td>
                                    <td><?php echo $Row->reconcile_date != '' ? date('d/m/y', strtotime($Row->reconcile_date)) : "N/A"; ?></td>
                                    <td><?php echo ($Row->reconcile_type); ?></td>
                                    <td><?php echo $month_name . " Ledger"; ?></td>
                                    <td><?php echo ($Row->total_attachment); ?></td>
                                </tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<script>
    $(document).ready(function () {
        $(".datepicker").datepicker({
            format: "mm-yyyy",
            viewMode: "months",
            minViewMode: "months"
        });
        $("#tblListing").dataTable();
    })
</script>