<div class="modal fade" id="ConfirmSaveModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <input type="hidden" name="current_row" id="current_row"/>
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Save the changed transaction?</h4>
            </div>
            <div class="modal-body">
                <p>The current actual deposit has been changed. Would you like the records the changes before moving to a new record or return to the changed actual bank deposit?</p>
            </div>
            <div class="modal-footer">

                <button class="btn btn-success pull-left" type="button" id="record_changes" onclick="records_changes();">Record Changes</button>
                <button data-dismiss="modal" class="btn btn-danger pull-left" type="button">Cancel</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>
<div class="modal fade" id="ConfirmDeleteModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Delete Confirmation</h4>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this record?</p>
            </div>
            <div class="modal-footer">
                <button data-dismiss="modal" class="btn btn-default pull-left" type="button">Close</button>
                <a class="btn btn-danger" href="" id="ConfirmDelete" >Confirm</a>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

<!-- END CONTENT BODY -->
</div>
<!-- END CONTENT -->
</div>
<!-- END CONTAINER -->
<!-- BEGIN FOOTER -->
<div class="page-footer">
    <div class="page-footer-inner">
        2020 &copy; Ledger Automation
    </div>
    <div class="scroll-to-top">
        <i class="icon-arrow-up"></i>
    </div>
</div>
<!-- END FOOTER -->
<!--[if lt IE 9]>
<script src="../assets/global/plugins/respond.min.js"></script>
<script src="../assets/global/plugins/excanvas.min.js"></script>
<script src="../assets/global/plugins/ie8.fix.min.js"></script>
<![endif]-->
<!-- BEGIN CORE PLUGINS -->
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/js.cookie.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-inputmask/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/scripts/form-input-mask.js" type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<?php $this->load->view('templates/page_level_scripts'); ?>
<script src="<?php echo base_url(); ?>assets/pages/scripts/custom.js" type="text/javascript"></script>

<link href="<?php echo base_url('assets/layouts/layout/css/hamburger/css/layout.min.css') ?>" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url('assets/layouts/layout/css/hamburger/css/custom.min.css') ?>" rel="stylesheet" type="text/css" />
<script src="<?php echo base_url('assets/layouts/layout/css/hamburger/scripts/layout.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/layouts/layout/css/hamburger/scripts/demo.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/layouts/layout/css/hamburger/scripts/quick-sidebar.min.js') ?>" type="text/javascript"></script>
<script src="<?php echo base_url('assets/layouts/layout/css/hamburger/scripts/quick-nav.min.js') ?>" type="text/javascript"></script>
</body>
<!-- END BODY -->
</html>
