<!DOCTYPE html>
<!-- 
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.3.5
Version: 4.1.0
Author: KeenThemes
Website: http://www.keenthemes.com/
Contact: support@keenthemes.com
Follow: www.twitter.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en" class="no-js">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8"/>
        <title><?php echo $title; ?></title>
        <?php $this->load->view('templates/styles'); ?>
        <?php $this->load->view('templates/page_level_styles'); ?>
        <script src="<?php echo base_url(); ?>assets/global/plugins/jquery.min.js" type="text/javascript"></script>
        <script type="text/javascript">
            $(document).ready(function () {
                App.setCurrentClass('<?php echo $this->router->fetch_class(); ?>');
                App.setCurrentMethod('<?php echo $this->router->fetch_method(); ?>');
                App.setSiteURL('<?php echo site_url(); ?>');
            });
        </script>
        <script src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/jquery.validate.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>assets/global/plugins/jquery-validation/js/additional-methods.js" type="text/javascript"></script>
        <script src="<?php echo base_url(); ?>assets/pages/scripts/jquery.treetable.js" type="text/javascript"></script>
        <script type="text/javascript">
            var site_url = '<?php echo base_url(); ?>';
        </script>
        <style type="text/css">
            /*.page-content-wrapper .page-content
            {
                height: 100vh !important;
            }*/
        </style>
    </head>
    <!-- END HEAD -->
    <!-- BEGIN BODY -->
    <body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
        <div class="loadingmessage" id="loadingmessage"></div>
        <?php $this->load->view('templates/top_bar'); ?>
        <div class="clearfix"> </div>
        <!-- BEGIN CONTAINER -->
        <div class="page-container">
            <?php $this->load->view('templates/menu'); ?>
            <!-- BEGIN CONTENT -->
            <div class="page-content-wrapper">
                <div class="page-content">
                    <div class="modal fade bs-modal-lg add-model" id="customModal" tabindex="-1" role="basic" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                    <h4 class="modal-title">Modal title</h4>
                                </div>
                                <div class="modal-body">
<!--                                    Widget settings form goes here-->
                                    <div class="loader_cover" style="height: 50px;">
                                        <div class="loadingmessage" id="loadingmessage" style="display: block"></div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn blue">Save changes</button>
                                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                    </div>
                    <!-- /.modal-dialog -->
                    <div id="confirmModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                    <h4 class="modal-title">Delete Confirmation</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-danger display-hide confirmError">
                                        <button class="close" data-close="alert"></button>
                                        <strong>Error!</strong> <span class="error-msg"></span>
                                    </div>
                                    <p>
                                        Are you sure want to delete this record ?
                                    </p>
                                </div>
                                <div class="modal-footer">
                                     <button class="btn blue confirmYes" aria-hidden="true" data-id="0">Yes</button>
                                    <button class="btn btn-danger" data-dismiss="modal" aria-hidden="true">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.modal -->
                    <!-- /delete modal-lg-dialog -->
                    <div id="deleteAccountModal" class="modal fade bs-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                    <h4 class="modal-title">Delete Confirmation</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-danger display-hide confirmError">
                                        <button class="close" data-close="alert"></button>
                                        <strong>Error!</strong> <span class="error-msg"></span>
                                    </div>
                                    <p>
                                        Are you sure want to delete this record ?
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn default" data-dismiss="modal" aria-hidden="true">Cancel</button>
                                    <button class="btn blue confirmYes" aria-hidden="true" data-id="0">Yes</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.modal -->
                    <div id="add_customer" class="modal fade bs-modal-lg" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <?php
                            $attributes = array('class' => 'horizontal-form validate', 'id' => 'frmAddCustomer');
                            echo form_open('sales/add_cutomer', $attributes);
                            ?>
                            <input type="hidden" id="index" name="index"/>
                            <input type="hidden" id="ctype" name="ctype"/>
                            <input type="hidden" id="source_page" name="source_page"/>
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                    <h4 class="modal-title">Add Customer</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="alert alert-danger display-hide confirmError">
                                        <button class="close" data-close="alert"></button>
                                        <strong>Error!</strong> <span class="error-msg"></span>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <?php
                                                echo form_label('Name<span class="required" aria-required="true"> * </span>', 'name', array('class' => 'control-label'));
                                                echo form_input(array('id' => 'name', 'name' => 'name', 'class' => 'form-control require', 'placeholder' => 'Name', 'onkeyup' => 'check_exist(this)'));
                                                ?>
                                                <div class="exist_err display-hide" style="color: red;"></div>
                                                <input type="hidden" name="check_exist_name" id="check_exist_name" value="0"/>
                                            </div>
                                            <button onclick="add_cust_detail();" class="btn btn-success" type="button">Add Detail</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button class="btn default" data-dismiss="modal" aria-hidden="true">Cancel</button>
                                    <button class="btn blue confirmYes" aria-hidden="true" data-id="0" type="button">Save</button>
                                </div>
                            </div>
                            <?php echo form_close(); ?>
                        </div>
                    </div>
                    <!--Product Model-->
                    <div class="modal fade bs-modal-lg add-model" id="add_product" tabindex="-1" role="basic" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <input type="hidden" id="index" name="index"/>
                            <input type="hidden" id="product_type" name="product_type"/>
                            <div class="modal-content">
                                <!--                                <div class="modal-header">-->
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <!--                                    <h4 class="modal-title">Product/Service information</h4>-->
                                <!--                                </div>-->
                                <div class="modal-body">
                                    <div class="loader_cover" style="height: 50px;">
                                        <div class="loadingmessage" id="loadingmessage" style="display: block"></div>
                                    </div>
                                </div>
                                <!--                                <div class="modal-footer">-->
                                <!--                                    <button type="button" class="btn blue">Save changes</button>-->
                                <!--                                    <button type="button" class="btn default" data-dismiss="modal">Close</button>-->
                                <!--                                </div>-->
                            </div>
                            <!-- /.modal-content -->
                        </div>
                    </div>
                    <!--Project Module-->
                    <div class="modal fade bs-modal-lg add-model" id="add_project" tabindex="-1" role="basic" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <input type="hidden" id="index" name="index"/>
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                    <h4 class="modal-title">Product/Service information</h4>
                                </div>
                                <div class="modal-body">
                                    Widget settings form goes here
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn blue">Save changes</button>
                                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                    </div>
                    <!--Add Project Cost Module-->
                    <div class="modal fade bs-modal-lg" id="add_project_cost" tabindex="-1" role="basic" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <input type="hidden" id="index" name="index"/>
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                    <h4 class="modal-title">Add Project Cost</h4>
                                </div>
                                <div class="modal-body">
                                    Widget settings form goes here
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn blue">Save changes</button>
                                    <button type="button" class="btn default" data-dismiss="modal">Close</button>
                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                    </div>

                    <!--add category type-->
                    <div class="modal fade" id="addCategoryModel" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                    <h4 class="modal-title">Add Custom Category</h4>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="portlet light portlet-fit portlet-form bordered">
                                                <div class="portlet-body">
                                                    <!-- BEGIN FORM-->
                                                    <form method="post" id="form_add_category" class="form-horizontal horizontal-form validate">
                                                        <div class="form-body">
                                                            <div id="cat_msg"></div>
                                                            <div class="form-group">
                                                                <label class="control-label col-md-3">Name
                                                                    <span class="required"> * </span>
                                                                </label>
                                                                <div class="col-md-6">
                                                                    <input type="text" name="pc_name" id="pc_name" data-required="1" class="form-control" required="required"/>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="form-actions">
                                                            <div class="row">
                                                                <div class="col-md-offset-3 col-md-9">
                                                                    <input type="button" class="btn btn-success" id="product_category_add" value="Add"/>
                                                                    <button type="button" class="btn btn-danger btn-outline" onclick="cancel_category_modal(this);">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <!-- END FORM-->
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer"></div>
                            </div>
                        </div>
                    </div>
                    
  <!--Default Account Model-->
                    <div class="modal fade bs-modal-lg add-model" id="default_acc_modal" tabindex="-1" role="basic" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <input type="hidden" id="index" name="index"/>
                            <input type="hidden" id="product_type" name="product_type"/>
                            <div class="modal-content">
                                <!--                                <div class="modal-header">-->
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                                <!--                                    <h4 class="modal-title">Product/Service information</h4>-->
                                <!--                                </div>-->
                                <div class="modal-body">
                                    <div class="loader_cover" style="height: 50px;">
                                        <div class="loadingmessage" id="loadingmessage" style="display: block"></div>
                                    </div>
                                </div>
                                <!--                                <div class="modal-footer">-->
                                <!--                                    <button type="button" class="btn blue">Save changes</button>-->
                                <!--                                    <button type="button" class="btn default" data-dismiss="modal">Close</button>-->
                                <!--                                </div>-->
                            </div>
                            <!-- /.modal-content -->
                        </div>
                    </div>                    
                    
                    <!-- END SAMPLE PORTLET CONFIGURATION MODAL FORM-->
                    <?php $this->load->view('templates/breadcrumb', isset($acc_res) ? $acc_res : array()); ?>
                    <!-- BEGIN PAGE TITLE-->
                    <?php //if ($this->uri->segment(1) != "dashboard") { ?>
                        <h1 class="page-title"> <?php echo $this->session->userdata('company_name'); ?>
                            <?php if ($this->uri->segment(1) == "sales" && $this->uri->segment(2) == "add") {
                                ?>
                             <div class="btn-group pull-right">
                                <a class="btn btn-success pull-right" href="javascript:;" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" aria-expanded="false">
                                   Default Accounts <i class="fa fa-gears"></i>
                                </a>
                                <ul class="dropdown-menu pull-right action-dropdown">
                                    <li>
                                        <a href="<?php echo base_url('settings/invoice_accounts_setup'); ?>" id="invoice_accounts_setup" data-toggle="modal"
                                           data-target="#default_acc_modal">Invoice Account</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo base_url('settings/product_accounts_setup'); ?>" id="product_accounts_setup" data-toggle="modal"
                                           data-target="#default_acc_modal">Product Account</a>
                                    </li>
                                    <li>
                                          <a href="<?php echo base_url('settings/tax_accounts_setup'); ?>" id="tax_accounts_setup" data-toggle="modal"
                                           data-target="#default_acc_modal">Tax Account</a>
                                    </li>
                                </ul>
                             </div>
                                <?php }
                            ?>
    <!--                            <small>statistics, charts, recent events and reports</small>-->
                        </h1>
                    <?php //} ?>

                    <!-- END PAGE TITLE-->
                    <?php
                    if ($this->session->flashdata('msg_class') == "success") {
                        ?>
                        <div class="alert alert-success alert-message mt20">
                            <?php echo $this->session->flashdata('msg'); ?>
                        </div>

                        <?php
                    } else if ($this->session->flashdata('msg_class') == "failure") {
                        ?>
                        <div class="alert alert-danger alert-message mt20">
                            <?php echo $this->session->flashdata('msg'); ?>
                        </div>
                        <?php
                    }
                    ?>
