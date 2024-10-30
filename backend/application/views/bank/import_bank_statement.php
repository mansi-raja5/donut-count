<?php
$min_year = 2018;
$max_year = 2025;
?>
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body">
        <?php
        $attributes = array('class' => 'form-horizontal validate', 'id' => 'submit_form');
        echo form_open_multipart('bank/upload_bank_statement_file', $attributes);
        ?>
        <input type="hidden" id="import_csvfile_error" name="import_csvfile_error" value="0"/>
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
                            $options[$row->key] = $row->name." (".$row->key.")";
                        }
                    }
                    echo form_dropdown(array('required' => 'required', 'id' => 'store_id', 'name' => 'store_id', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $store_id));
                    ?>
                </div>
            </div>
        </div>
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
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Select Month<span class="required" aria-required="true"> * </span>', 'month', array('class' => 'col-md-4 control-label')); ?>
                <div class="col-md-4">
                    <?php
                    $month_arr = array("January", "February", "March", "April", "May", "Jun", "July", "August", "September", "October", "November", "December");
                    $options = array();
                    $options[''] = '-- Month  --';
                    $cid = field(set_value('month', NULL), $this->input->get('month'));
                    foreach ($month_arr as $key => $value) {
                        $options[$key + 1] = $value;
                    }

                    echo form_dropdown(array('required' => 'required', 'id' => 'month', 'name' => 'month', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $cid));
                    ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Select a CSV file to upload<span class="required" aria-required="true"> * </span>', 'choose_file', array('class' => 'col-md-4 control-label')); ?>
                <div class="col-md-6">
                    <input type="file" name="import_file" id="import_file" required="" extension="csv"/>
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-6 col-xs-offset-4">
                    <a href="<?php echo base_url() . "files_upload/import_sample_file/Bank_Statement.csv" ?>" download>Download Sample File</a>
                </div>
            </div>
        </div>
      
        <div class="form-group">
            <div class="col-xs-2 btn_group">
                <input type="submit" value="Save" id="submit" class="btn blue">
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>