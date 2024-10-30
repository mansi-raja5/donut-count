<?php $this->load->view('templates/header',array('c_view' => $_ci_view)); ?>
<!-- BEGIN PAGE CONTENT-->
<div class="row">
        <div class="col-md-12">
            <?php echo $body; ?>
        </div>
</div>
<!-- END PAGE CONTENT-->
<?php $this->load->view('templates/footer',array('c_view' => $_ci_view)); ?>