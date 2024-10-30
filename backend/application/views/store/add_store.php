
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
        echo form_open_multipart('store/'.$action, $attributes, array());
        ?>
        <input type="hidden" name="store_id" value="<?php echo isset($stores->store_id) ? $stores->store_id : 0; ?>">
        <div class="col-md-12">
            <div class="form-group">
               <?php
                echo form_label('Certipay Control<span class="required" aria-required="true"> * </span>','text', array('class' => 'col-md-4 control-label'));
                 $certipay_control = set_value('certipay_control', (isset($stores->certipay_control)) ? $stores->certipay_control : NULL);
                ?>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="certipay_control" value="<?php echo  $certipay_control; ?>"/>
                </div>
                <span class="help-block help-block-error"><?php echo form_error('certipay_control'); ?></span>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
               <?php
                echo form_label('Business Name<span class="required" aria-required="true"> * </span>','text', array('class' => 'col-md-4 control-label'));
                 $business_name = set_value('certipay_control', (isset($stores->business_name)) ? $stores->business_name : NULL);
                ?>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="business_name" value="<?php echo $business_name; ?>" required/>
                </div>
                <span class="help-block help-block-error"><?php echo form_error('business_name'); ?></span>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
               <?php
                echo form_label('Store Key<span class="required" aria-required="true"> * </span>','text', array('class' => 'col-md-4 control-label'));
                 $key = set_value('key', (isset($stores->key)) ? $stores->key : NULL);
                ?>
                <div class="col-md-4">
                    <input type="text" class="form-control" name="key" value="<?php echo $key; ?>" required/>
                </div>
                 <span class="help-block help-block-error"><?php echo form_error('key'); ?></span>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php
                echo form_label('Store Name<span class="required" aria-required="true"> * </span>','text', array('class' => 'col-md-4 control-label'));
                $name = set_value('key', (isset($stores->name)) ? $stores->name : NULL);
                ?>
                <div class="col-md-4">
                    <input type="text" name="name" class="form-control" value="<?php echo $name; ?>" required>
                </div>
                  <span class="help-block help-block-error"><?php echo form_error('name'); ?></span>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php
                echo form_label('Address<span class="required" aria-required="true"> * </span>','text', array('class' => 'col-md-4 control-label'));
                $location = set_value('key', (isset($stores->location)) ? $stores->location : NULL);
                ?>
                <div class="col-md-4">
                    <input type="text" name="location" class="form-control" value="<?php  echo $location; ?>" required>
                </div>
                <span class="help-block help-block-error"><?php echo form_error('location'); ?></span>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php
                echo form_label('Tax ID<span class="required" aria-required="true"> * </span>','text', array('class' => 'col-md-4 control-label'));
                $tax_id = set_value('tax_id', (isset($stores->tax_id)) ? $stores->tax_id : NULL);
                ?>
                <div class="col-md-4">
                    <input type="text" name="tax_id" class="form-control" value="<?php echo $tax_id;  ?>" required>
                </div>
                <span class="help-block help-block-error"><?php echo form_error('tax_id'); ?></span>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php
                echo form_label('Status<span class="required" aria-required="true"> * </span>','text', array('class' => 'col-md-4 control-label'));
                $status = set_value('status', (isset($stores->status)) ? $stores->status : 'A');
                ?>
                <div class="col-md-4">
                    <select name="status" class="form-control">
                        <option value='A' <?php echo ($status=='A') ?  'selected' : '';?>>Active</option>
                        <option value='I' <?php echo ($status=='I') ?  'selected' : ''; ?>>InActive</option>
                    </select>
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
