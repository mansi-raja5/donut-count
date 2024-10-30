<style>
    .logo_title{
        font-size: 30px;
        color: #fff;
    }
</style>
<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="<?php echo base_url(); ?>">
                <span class="logo_title">LEDGER</span>
            </a>
           <div class="menu-toggler sidebar-toggler">
                            <span></span>
                        </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
            <span></span>
        </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN PAGE TOP -->
        <div class="page-top">
            <!-- BEGIN TOP NAVIGATION MENU -->
            <div class="top-menu">
                <div class="nav navbar-nav pull-right">
                    <a class="scroll-to-bottom"><i class="icon-arrow-down"></i></a>
                </div>
                <ul class="nav navbar-nav pull-right">
                    <li>
                        <a href="<?php echo base_url(); ?>/login/logout">
                            <i class="icon-key"></i> Log Out </a>
                    </li>
                </ul>
            </div>
            <!-- END TOP NAVIGATION MENU -->
        </div>
        <!-- END PAGE TOP -->
    </div>
    <!-- END HEADER INNER -->
</div>
<!-- END HEADER -->

<div id="depreciationReminderModal" class="modal fade" data-keyboard="false" data-backdrop="static">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Reminder to Record Depreciation</h4>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning confirmError">
                    <button class="close" data-close="alert"></button>
                    <strong>Warning!</strong> <span class="error-msg">There are some assets entries which need to record depreciation. </span>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-danger" onclick="set_dep_reminder('Dismiss'); ">Dismiss</button>
                <button class="btn blue" onclick="set_dep_reminder('Done'); ">Depreciate now</button>
                <button class="btn btn-warning" onclick="set_dep_reminder('Sleep'); ">Sleep</button>
            </div>
        </div>
    </div>
</div>

<script>
    $('.scroll-to-bottom').on('click', function(event) {
        var target = $('.page-footer');
        if( target.length ) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top
            }, 1000);
        }
    });

    function set_dep_reminder(action) {
        $.ajax({
            url: site_url + 'assets/set_dep_notification',
            type: "POST",
            data: {
                action : action
            },
            beforeSend: function () {
                $("#loadingmessage").show();
            },
            success: function (data) {
                $("#loadingmessage").hide();
                
                var response = $.parseJSON(data);
                
                if(response.status) {
                    if(action == 'Done') {
                        window.location.href = site_url + 'assets/assets_depreciation_taken';
                    } else {
                        location.reload();
                    }
                }
                
            }
        });
        
    };
</script>
