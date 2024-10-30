<style>
    .site_title{
        color : #FFF;
        font-size: 38px;
    }
</style> 
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
<link href="<?php echo base_url(); ?>assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/login/components.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/pages/css/login.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/login/custom.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/login/login.min.css" rel="stylesheet" type="text/css" />
<link href="<?php echo base_url(); ?>assets/global/login/plugins.min.css" rel="stylesheet" type="text/css" />
<body class="login">
    <div class="logo">
    <a href="/" class="logo_a">
        <h3 class="site_title"><?php echo strtoupper(site_title); ?></h3>
    </a>
</div>
    <div class="content">
        <!-- BEGIN LOGIN FORM -->
        <form class="login-form validate-form" data-toggle="validator" role="form" action="<?php echo base_url();?>login/verify" method="post" novalidate>
            <h3 class="form-title font-green">Sign In</h3>
            <?php
             $error = $this->session->flashdata('error');
                if($error)
                {
            ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                <?php echo $this->session->flashdata('error'); ?>                    
            </div>
            <?php } ?>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Username</label>
                <input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username" required data-error="This field is required"/>
                <div class="help-block with-errors"></div>
            </div>
            <div class="form-group">
                <label class="control-label visible-ie8 visible-ie9">Password</label>
                <input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="userpassword" required data-error="This field is required"/>
                <div class="help-block with-errors"></div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary" >Submit</button>
            </div>
        </form>
        <!-- END LOGIN FORM -->
    </div>
</div>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/1000hz-bootstrap-validator/0.11.9/validator.min.js"></script>
 <script src="<?php echo base_url(); ?>assets/global/plugins/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/jquery.validate.js" type="text/javascript"></script>
<script src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/additional-methods.js" type="text/javascript"></script>

<script src="<?php echo base_url(); ?>assets/global/plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
