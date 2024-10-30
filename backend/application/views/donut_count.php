
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
     
    <div class="portlet-body">
        <?php
        $attributes = array('class' => 'form-horizontal validate', 'id' => 'submit_form_file');
        echo form_open_multipart('donutcount/import', $attributes);
        ?>
        <div class="col-md-12">
            <div class="form-group">
                <?php echo form_label('Select File Type<span class="required" aria-required="true"> * </span>', 'store', array('class' => 'col-md-4 control-label')); ?>
                <div class="col-md-4">
                    <select class="form-control" name="select_type" id="select_type" required>
                        <option value="donutcount">Donut Count</option>
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