
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body ">

        <?php
        $attributes = array('class' => 'form-horizontal validate', 'id' => 'submit_form');
        echo form_open_multipart('settings/save', $attributes, array());
        ?>
        <?php if ($general_settings != "") : ?>
            <input type="hidden" name="id" value="<?php echo $general_settings[0]['id'] ?>">
        <?php endif; ?>
        <div class="col-md-12">
            <div class="form-group">
                <?php
                echo form_label('Setting Value<span class="required" aria-required="true"> * </span>', 'text', array('class' => 'col-md-4 control-label'));
                ?>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="key_name" value="<?php if ($general_settings != "") : echo $general_settings[0]['key_name'];
                endif; ?>" required/>
                </div>
            </div>
        </div>
        <?php
        if (trim($general_settings[0]['key_name']) == 'check_number_starting') {

            $key_value = $general_settings[0]['key_value'];
            if($key_value != ''){
                $Arr = json_decode($key_value);
            }
            if (isset($store_list['records']) && !empty($store_list['records'])) {
                $key = 0;
                foreach ($store_list['records'] as $row) {
                    ?>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php
                                echo form_label('Store Key<span class="required" aria-required="true"> * </span>', 'text', array('class' => 'col-md-4 control-label'));
                                ?>
                                <div class="col-md-4">
                                    <input type="text" name="store_key[]" readonly="" class="form-control" value="<?php echo $row->key; ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <?php
                                echo form_label('Setting Value<span class="required" aria-required="true"> * </span>', 'text', array('class' => 'col-md-4 control-label'));
                                ?>
                                <div class="col-md-4">
                                    <input type="text" name="key_value[]" class="form-control" value="<?php echo isset($Arr[$key]->key_value) ? $Arr[$key]->key_value : 1001; ?>" required>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                
                $key++;
                }
            }
            ?>
            <?php
        } else {
            ?>


            <div class="col-md-12">
                <div class="form-group">
                    <?php
                    echo form_label('Setting Value<span class="required" aria-required="true"> * </span>', 'text', array('class' => 'col-md-4 control-label'));
                    ?>
                    <div class="col-md-4">
                        <input type="text" name="key_value" class="form-control" value="<?php if ($general_settings != "") : echo $general_settings[0]['key_value'];
                endif; ?>" required>
                    </div>
                </div>
            </div>
<?php } ?>
        <div class="form-group">
            <div class="col-xs-2 btn_group">
                <input type="submit" value="Save" id="submit" class="btn blue">
            </div>
        </div>
<?php echo form_close(); ?>
    </div>
</div>
