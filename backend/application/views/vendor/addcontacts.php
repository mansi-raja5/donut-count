<?php
$id = field(set_value('id', NULL), (isset($phonedata['id'])) ? $phonedata['id'] : '');
$slug = $this->input->get('slug');
?>
<style>
    .account_btn label {
        display: inline-block;
        width: 80%;
        white-space: nowrap;
        overflow: hidden !important;
        text-overflow: ellipsis;
        margin-bottom: -4px;
    }
    .account_btn{
        font-family: "Open Sans",sans-serif;
        font-size: 14px;
        width: 270px !important;
        text-align: left;
    }
    .account_dm .mt-checkbox{
        margin-bottom: 5px;
    }
    .account_dm {
        max-height: 280px;
        overflow: auto;
        min-width: 270px;
        padding: 15px;
    }
    .account_btn span.caret {
        margin-top: 7px;
    }
    .pointer-arrow {
        cursor: pointer;
    }
</style>
<div class="portlet light bordered">
    <div class="portlet-body">
        <!-- END PAGE TITLE-->
        <!-- END PAGE HEADER-->
        <div class="row">
            <div class="col-md-12 contact_wrapper">
                <div class="alert alert-success display-hide confirmSuccess">
                    <button class="close" data-close="alert"></button>
                    <strong>Success!</strong> <span class="success-msg"></span>
                </div>
                <div class="alert alert-danger display-hide confirmError">
                    <button class="close" data-close="alert"></button>
                    <strong>Error!</strong> <span class="error-msg"></span>
                </div>
                <div class="portlet box blue">
                    <div class="portlet-title">
                        <div class="caption">
                            <i class="fa fa-gift"></i>Entry of Vendor
                        </div>
                    </div>
                    <div class="portlet-body">

                        <div class="tabbable-custom">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a href="#general" data-toggle="tab" aria-expanded="true"> General </a>
                                </li>

                                <li class="">
                                    <a href="#address" id="anchor_address" data-toggle="tab" aria-expanded="true"> Address </a>
                                </li>
                                <li class="">
                                    <a href="#attachments" data-toggle="tab" aria-expanded="false" id="anchor_attachments"> Attachments </a>
                                </li>
                            </ul>

                             <input type="hidden" name="slug" id="slug" value="<?php echo isset($slug) ? $slug : ''; ?>"/>
                            <form action="<?php echo base_url() ?>vendors/save/" class="form-horizontal validate" method="post" id="frm_contacts">

                                <div class="tab-content">
                                    <input type="hidden" name="active_tab" id="active_tab"/>
                                    <div class="tab-pane active" id="general">
                                        <input type="hidden" name="entry_phonebook" value="general"/>
                                        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : 0; ?>" id="id"/>
                                        <div class="form-body">
                                            <div class="form-group <?php echo form_error('first_name') ? 'has-error' : '' ?>">
                                                <?php echo form_label('Username<span class="required" aria-required="true"> * </span>', 'first_name', array('class' => 'control-label col-md-3')); ?>
                                                <div class="col-md-4">
                                                    <?php
                                                    $username = field(set_value('username', NULL), (isset($phonedata['username'])) ? $phonedata['username'] : '');
                                                    echo form_input(array('required' => 'required', 'id' => 'username', 'name' => 'username', 'class' => 'form-control', 'placeholder' => 'Username'), $username);
                                                    echo form_error('username');
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group <?php echo form_error('password') ? 'has-error' : '' ?>">
                                                <?php echo form_label('Password<span class="required" aria-required="true"> * </span>', 'password', array('class' => 'control-label col-md-3')); ?>
                                                <div class="col-md-4">
                                                    <?php
                                                    $password = field(set_value('password', NULL), (isset($phonedata['password'])) ? $phonedata['password'] : '');
                                                    echo form_input(array('type' => 'password', 'required' => 'required', 'id' => 'password', 'name' => 'password', 'class' => 'form-control', 'placeholder' => 'Password'), $password);
                                                    echo form_error('password');
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group <?php echo form_error('emp_no') ? 'has-error' : '' ?>">
                                                <?php echo form_label('Employee No<span class="required" aria-required="true"> * </span>', 'emp_no', array('class' => 'control-label col-md-3')); ?>
                                                <div class="col-md-4">
                                                    <?php
                                                    $emp_no = field(set_value('emp_no', NULL), (isset($phonedata['emp_no'])) ? $phonedata['emp_no'] : '');
                                                    echo form_input(array('required' => 'required', 'id' => 'emp_no', 'name' => 'emp_no', 'class' => 'form-control', 'placeholder' => 'Employee No'), $emp_no);
                                                    echo form_error('emp_no');
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group <?php echo form_error('first_name') ? 'has-error' : '' ?>">
                                                <?php echo form_label('First Name<span class="required" aria-required="true"> * </span>', 'first_name', array('class' => 'control-label col-md-3')); ?>
                                                <div class="col-md-4">
                                                    <?php
                                                    $f_name = field(set_value('first_name', NULL), (isset($phonedata['name_f'])) ? $phonedata['name_f'] : '');
                                                    echo form_input(array('required' => 'required', 'id' => 'first_name', 'name' => 'first_name', 'class' => 'form-control', 'placeholder' => 'First Name'), $f_name);
                                                    echo form_error('first_name');
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?php echo form_label('Middle Name:', 'middle_name', array('class' => 'control-label col-md-3')); ?>
                                                <div class="col-md-4">
                                                    <?php
                                                    $m_name = field(set_value('middle_name', NULL), (isset($phonedata['name_m'])) ? $phonedata['name_m'] : '');
                                                    echo form_input(array('id' => 'middle_name', 'name' => 'middle_name', 'class' => 'form-control', 'placeholder' => 'Middle Name'), $m_name);
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group <?php echo form_error('last_name') ? 'has-error' : '' ?>">
                                                <?php echo form_label('Last Name<span class="required" aria-required="true"> * </span>', 'last_name', array('class' => 'control-label col-md-3')); ?>
                                                <div class="col-md-4">
                                                    <?php
                                                    $l_name = field(set_value('last_name', NULL), (isset($phonedata['name_l'])) ? $phonedata['name_l'] : '');
                                                    echo form_input(array('required' => 'required', 'id' => 'last_name', 'name' => 'last_name', 'class' => 'form-control', 'placeholder' => 'Last Name'), $l_name);
                                                    echo form_error('last_name');
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group <?php echo form_error('company') ? 'has-error' : '' ?>">
                                                <?php echo form_label('Company', 'company', array('class' => 'control-label col-md-3')); ?>
                                                <div class="col-md-4">
                                                    <?php
                                                    $company_name = field(set_value('company', NULL), (isset($phonedata['company'])) ? $phonedata['company'] : '');
                                                    echo form_input(array('id' => 'company', 'name' => 'company', 'class' => 'form-control', 'placeholder' => 'Company'), $company_name);
                                                    echo form_error('company');
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?php echo form_label('E-Mail:', 'email', array('class' => 'control-label col-md-3')); ?>
                                                <div class="col-md-4">
                                                    <?php
                                                    $email = field(set_value('email', NULL), (isset($phonedata['email'])) ? $phonedata['email'] : '');
                                                    echo form_input(array('type' => 'email', 'id' => 'email', 'name' => 'email', 'class' => 'form-control', 'placeholder' => 'Email'), $email);
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?php echo form_label('Website:', 'website', array('class' => 'control-label col-md-3')); ?>
                                                <div class="col-md-4">
                                                    <?php
                                                    $website = field(set_value('website', NULL), (isset($phonedata['website'])) ? $phonedata['website'] : '');
                                                    echo form_input(array('type' => 'url', 'id' => 'website', 'name' => 'website', 'class' => 'form-control', 'placeholder' => 'Website'), $website);
                                                    ?>
                                                </div>
                                            </div>
                                             <div class="form-group">
                                                <label class="control-label col-md-3" style="text-align: left;">Add an Additional Note or Comment about this Vendor:</label>
                                                <div class="col-md-6">
                                                    <textarea class="form-control" rows="4" id="notes_text" name="notes" required></textarea>
                                                </div>
                                            </div>
                                            <h3>Payment Information</h3>
                                            <div class="form-group">
                                                <?php echo form_label('Recurring Period:', 'recurring_period', array('class' => 'control-label col-md-3')); ?>
                                                <div class="col-md-4">
                                                    <select class="form-control" name="recurring_period" id="recurring_period">
                                                        <option value="">--Select Recurring Period--</option>
                                                      <?php
                                                      for($i = 1; $i <= 12; $i++){
                                                          ?>
                                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                                            <?php
                                                      }
                                                      ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?php echo form_label('Schedule Payment Date:', 'schedule_payment_date', array('class' => 'control-label col-md-3')); ?>
                                                 <div class="col-md-4">
                                                    <?php
                                                    $schedule_payment_date = field(set_value('schedule_payment_date', NULL), (isset($phonedata['schedule_payment_date'])) ? DB2Disp($phonedata['schedule_payment_date']) : '');
                                                    echo form_input(array('id' => 'schedule_payment_date', 'name' => 'schedule_payment_date', 'class' => 'form-control datepicker', 'placeholder' => 'Schedule Payment Date'), $schedule_payment_date);
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?php echo form_label('Bill Due Date:', 'bill_due_date', array('class' => 'control-label col-md-3')); ?>
                                                 <div class="col-md-4">
                                                    <?php
                                                    $bill_due_date = field(set_value('bill_due_date', NULL), (isset($phonedata['bill_due_date'])) ? DB2Disp($phonedata['bill_due_date']) : '');
                                                    echo form_input(array('id' => 'bill_due_date', 'name' => 'bill_due_date', 'class' => 'form-control datepicker', 'placeholder' => 'Bill Due Date'), $bill_due_date);
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?php echo form_label('Payment Method:', 'Payment Method', array('class' => 'control-label col-md-3')); ?>
                                                <div class="col-md-4">
                                                    <select class="form-control" name="payment_method" id="payment_method">
                                                        <option value="">--Select Payment Mode--</option>
                                                        <option value="cash">Cash</option>
                                                        <option value="auto">Auto</option>
                                                        <option value="net_banking">Net Banking</option>
                                                        <option value="card">Card</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?php echo form_label('Account no:', 'account_no', array('class' => 'control-label col-md-3')); ?>
                                                <div class="col-md-4">
                                                    <?php
                                                    $account_no = field(set_value('account_no', NULL), (isset($phonedata['account_no'])) ? $phonedata['account_no'] : '');
                                                    echo form_input(array('type' => 'text', 'id' => 'account_no', 'name' => 'account_no', 'class' => 'form-control', 'placeholder' => 'Account no'), $account_no);
                                                    ?>
                                                </div>
                                            </div>



                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-offset-3">
                                                    <input type="button" class="btn blue" name="save_general" value="Save" onclick="add_customer_detail();"/>

                                                    <?php echo anchor('vendors', 'Cancel', array('class' => 'btn btn-danger ml10')); ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php if ($id != '') { ?>
                                            <div class="text-right">
                                                <a class="btn yellow" onclick="deleteRecord('<?php echo $phonedata['id']; ?>');">DELETE</a>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <div class="tab-pane" id="address">
                                        <input type="hidden" name="entry_phonebook" value="address"/>
                                        <input type="hidden" name="id" value="<?php echo $id; ?>" id="address_id"/>
                                        <div class="form-body">
                                            
                                            <div class="form-group">
                                                <h4 class="control-label col-md-3 sbold">Physical Address</h4>
                                            </div>

                                            <div class="form-group">
                                                <?php echo form_label('Address 1:', 'address_1', array('class' => 'control-label col-md-3')); ?>
                                                <div class="col-md-4">
                                                    <?php
                                                    $phys_addr1 = field(set_value('address_1', NULL), (isset($phonedata['phys_addr1'])) ? $phonedata['phys_addr1'] : '');
                                                    echo form_input(array('id' => 'address_1', 'name' => 'address_1', 'class' => 'form-control', 'placeholder' => 'Address 1'), $phys_addr1);
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?php echo form_label('Address 2:', 'address_2', array('class' => 'control-label col-md-3')); ?>
                                                <div class="col-md-4">
                                                    <?php
                                                    $phys_addr2 = field(set_value('address_2', NULL), (isset($phonedata['phys_addr2'])) ? $phonedata['phys_addr2'] : '');
                                                    echo form_input(array('id' => 'address_2', 'name' => 'address_2', 'class' => 'form-control', 'placeholder' => 'Address 2'), $phys_addr2);
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?php echo form_label('City:', 'city', array('class' => 'control-label col-md-3')); ?>
                                                <div class="col-md-4">
                                                    <?php
                                                    $city = field(set_value('city', NULL), (isset($phonedata['phys_city'])) ? $phonedata['phys_city'] : '');
                                                    echo form_input(array('id' => 'city', 'name' => 'city', 'class' => 'form-control', 'placeholder' => 'City'), $city);
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label col-md-3">State:</label>
                                                <div class="col-md-4">
                                                    <select class="form-control" name="state" id="state">
                                                        <option value="1">India</option>
                                                        <option value="2">Kenya</option>
                                                        <option value="3">US</option>
                                                        <option value="4">UK</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <?php echo form_label('Zip:', 'zip', array('class' => 'control-label col-md-3')); ?>
                                                <div class="col-md-2">
                                                    <?php
                                                    $zip = field(set_value('zip', NULL), (isset($phonedata['phys_zip'])) ? $phonedata['phys_zip'] : '');
                                                    echo form_input(array('id' => 'zip', 'name' => 'zip', 'class' => 'form-control', 'placeholder' => 'Zip'), $zip);
                                                    ?>
                                                </div>
                                                <!--                                                <div class="col-md-2">
                                                <?php
                                                //$zip4 = field(set_value('zip4', NULL), (isset($phonedata['phys_zip4'])) ? $phonedata['phys_zip4'] : '');
                                                //echo form_input(array('id' => 'zip4', 'name' => 'zip4', 'class' => 'form-control', 'placeholder' => 'Zip'), $zip4);
                                                ?>
                                                                                                </div>-->
                                            </div>
                                   
                                        </div>
                                        <div class="form-actions">
                                            <div class="row">
                                                <div class="col-md-offset-3 col-md-9">
                                                    <input type="button" class="btn btn-success" name="save_address" value="Save" onclick="add_customer_detail();"/>
                                                    <?php echo anchor('vendors', 'Cancel', array('class' => 'btn btn-danger ml10')); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                          
                                    <div class="tab-pane" id="attachments">
                                        <input type="hidden" name="ss"  id="attachmentsss"/>
                                        <div class="tab-pane active" id="attachments" >

                                            <div id="my-dropzone" class="dropzone">
                                                <input type="hidden" name="id" value="<?php echo $id; ?>" id="attachments_id1"/>
                                                <div class="fallback">
                                                    <input name="file[]" type="file" id="file" multiple />
                                                </div>
                                            </div>
                                        </div>
                                        <input type="hidden" name="id" value="<?php echo $id; ?>" id="attachments_id"/>
                                        <button class="btn btn-success save_btn pull-right" name="btnSave" id="btnSave" style="margin: 15px auto;" type="button" onclick="add_customer_detail();">Save</button>
                                        <button type="button" onclick="delete_files();" class="btn btn-danger save_btn pull-right" name="btnCancel" id="btnCancel" style="margin: 15px 5px;">Clear</button>
                                        <br>
                                        <div class="clearfix"></div>
                                        <div class="portlet light bordered">
                                            <div class="portlet-title">
                                                <div class="caption">
                                                    <i class="icon-social-dribbble"></i>
                                                    <span class="caption-subject bold uppercase">Attachments List</span>
                                                </div>
                                            </div>
                                            <div class="portlet-body form">
                                                <input type="hidden" name="attchid" id="attchid">
                                                <input type="hidden" name="docsid" id="docsid">
                                                <div class="table-responsive">
                                                    <table class="table table-striped table-bordered table-hover">
                                                        <?php
                                                        if (!empty($phone_attachments['records'])) {
                                                            $i = 1;
                                                            $countfile = array();
                                                            $sum = array();
                                                            foreach ($phone_attachments['records'] as $attachfile) {
//                                                                $countfile[] = count($i);
                                                                  $docs_id = isset($attachfile['id']) ? $attachfile['id'] : "";
                                                                $sum[] = $attachfile['size'];
                                                                $get_filename_arr = explode("/", $attachfile['file_md5']);
                                                                $get_filename = end($get_filename_arr);
                                                                $ext = pathinfo($get_filename, PATHINFO_EXTENSION);
                                                                $fname = pathinfo($get_filename, PATHINFO_FILENAME);

//                                                                $Doc_Res = get_document_data($fname);
//                                                                $docs_id = isset($Doc_Res->id) ? $Doc_Res->id : "";
//                                                                $thumb_status = isset($Doc_Res->thumbnail_status) ? $Doc_Res->thumbnail_status : 0;
//                                                                $upload_file_name = isset($Doc_Res->current_fname) ? $Doc_Res->current_fname : 0;
//                                                                $file_ext = isset($Doc_Res->current_fext) ? $Doc_Res->current_fext : 0;
                                                                ?>
                                                                <tr>
                                                                    <td>
                                                                        <?php if ($attachfile['filename_ext'] == '.pdf') { ?>
                                                                            <i class="fa fa-file-pdf-o" aria-hidden="true"></i>
                                                                        <?php } else if ($attachfile['filename_ext'] == '.png' || $attachfile['filename_ext'] == '.jpeg' || $attachfile['filename_ext'] == '.jpg') { ?>
                                                                            <i class="fa fa-file-image-o" aria-hidden="true"></i>
                                                                        <?php } else if ($attachfile['filename_ext'] == '.doc' || $attachfile['filename_ext'] == '.docx') { ?>
                                                                            <i class="fa fa-file-text-o" aria-hidden="true"></i>
                                                                        <?php } else if ($attachfile['filename_ext'] == '.zip') { ?>
                                                                            <i class="fa fa-file-archive-o" aria-hidden="true"></i>
                                                                        <?php } ?>
                                                                    </td>
                                                                    <td>
                                                                      
                                                                            <a download="<?php echo $attachfile['filename'] ?>" href="<?php echo $attachfile['file_md5']; ?>" title="Download Attachment"><i class="fa fa-download" aria-hidden="true" ></i></a>
                                                                      
                                                                    </td>
                                                                    <td>
                                                                      
                                                                        <a title="View Attachment" target="_blank" href="<?php echo base_url().$attachfile['file_md5']; ?>"  ><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                                       
                                                                    </td>
                                                                    <td id="<?php echo $attachfile['id']; ?>"><a title="Delete Attachment" onclick="deleteAttachment('<?php echo $attachfile['id']; ?>', '<?php echo $attachfile['vendor_id']; ?>', '<?php echo $docs_id; ?>');"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                                                    </td>
                                                                    <td id="file_name_<?php echo $i; ?>"><a title="Rename Attachment" onclick="renameFileName('<?php echo $attachfile['id']; ?>', '<?php echo $attachfile['filename']; ?>',<?php echo $i; ?>, '<?php echo base_url(); ?>', '<?php echo $docs_id; ?>')" ><?php echo $attachfile['filename']; ?></a>
                                                                    </td>
                                                                    <td><?php echo "<span class=size>" . round($attachfile['size'] / 1024 / 1024, 3) . '</span>  MB'; ?></td>
                                                                </tr>
                                                                <?php
                                                                $i++;
                                                            } $convert = array_sum($sum);
                                                            ?>
                                                            <tr>
                                                                <td colspan="5"><?php echo "<span id=count_files>" . count($phone_attachments['records']) . "</span>   Files"; ?></td>
                                                                <td colspan="2"><?php echo "Total Size : <span id=total_size>" . round($convert / 1024 / 1024, 3) . '</span>  MB'; ?></td>
                                                            </tr>
                                                        <?php } else { ?>
                                                            <tr>
                                                                <td colspan="6" align="center">No Data Available</td>
                                                            </tr>
                                                        <?php } ?>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                   
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- END CONTENT BODY -->
</div>
<!-- END CONTENT -->

<!--add phone type-->
<div class="modal fade" id="addPhoneTypeModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Add Custom Phone Type</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light portlet-fit portlet-form bordered">
                            <div class="portlet-body">
                                <!-- BEGIN FORM-->
                                <form method="post" id="form_add_phone_type" class="form-horizontal">
                                    <div class="form-body">
                                        <div id="msg"></div>
                                        <input type="hidden" name="phone_number_id" id="phone_number_id" />
                                        <input type="hidden" name="field_name" id="field_name" />
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Phone Type
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-6">
                                                <input type="text" name="phone_type_name" id="phone_type_name" data-required="1" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-offset-3 col-md-9">
                                                <input type="button" class="btn green" id="phone_type_add" value="Add"/>
                                                <button type="button" class="btn grey-salsa btn-outline" data-dismiss="modal">Cancel</button>
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

<!--add Custom Display Name-->
<div class="modal fade" id="addCustomDisplayNameModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Add Custom Display Name</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light portlet-fit portlet-form bordered">
                            <div class="portlet-body">
                                <!-- BEGIN FORM-->
                                <form method="post" id="form_add_display_name" class="form-horizontal">
                                    <div class="form-body">
                                        <div id="message"></div>
                                        <input type="hidden" name="Phone_Book_Guid" id="Phone_Book_Guid" />
                                        <div class="form-group">
                                            <label class="control-label col-md-3">Custom Name
                                                <span class="required"> * </span>
                                            </label>
                                            <div class="col-md-6">
                                                <input type="text" name="custom_display_name" id="custom_display_name" data-required="1" class="form-control" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-offset-3 col-md-9">
                                                <input type="button" class="btn green" id="custom_display_name_add" value="Add"/>
                                                <button type="button" class="btn grey-salsa btn-outline" data-dismiss="modal">Cancel</button>
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
<!--delete phonebook note-->
<div class="modal fade" id="deleteNoteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo base_url(); ?>/vendors/deleteNote" method="post" id="frm_delete_notes">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Delete Confirmation</h4>
                </div>
                <input type="hidden" id="note_id" name="note_id"/>
                <input type="hidden" id="phonebook_id" name="phonebook_id"/>
                <div class="modal-body">
                    <div class="alert alert-danger display-hide confirmError">
                        <button class="close" data-close="alert"></button>
                        <strong>Error!</strong> <span class="error-msg"></span>
                    </div>
                    <p>Are you sure want to delete this note?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" onclick="cancelDeleteNote();">Cancel</button>
                    <button type="button" class="btn red" onclick="delete_note(this)">Delete</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!--edit phonebook note-->
<div class="modal fade" id="editNoteModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Edit Vendor Note</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light portlet-fit portlet-form bordered">
                            <div class="portlet-body">
                                <!-- BEGIN FORM-->
                                <form action="<?php echo base_url(); ?>/vendors/updateNote" method="post" class="form-horizontal" id="frm_update_notes">
                                    <input type="hidden" name="loc_note_id" id="loc_note_id" />
                                    <input type="hidden" name="loc_id" id="loc_id" />
                                    <div class="form-body">
                                        <div class="form-group">
                                            <label class="control-label col-md-4">Add an Additional Note or Comment about this Vendors:</label>
                                            <div class="col-md-8">
                                                <textarea name="edit_note" id="edit_note" class="form-control" rows="4" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-actions">
                                        <div class="row">
                                            <div class="col-md-offset-4 col-md-9">
                                                <button type="button" onclick="save_note(this);" class="btn green">Update</button>
                                                <button type="button" class="btn grey-salsa btn-outline" onclick="cancelEditNote();">Cancel</button>
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

<!--delete phonebook attachment file-->
<div class="modal fade" id="deleteattachmentsModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo base_url('vendors/deleteAttach'); ?>" method="post" id="frm_delete_attachment">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Delete Confirmation</h4>
                </div>
                <input type="hidden" id="attach_id" name="attach_id"/>
                <input type="hidden" id="cdocs_id" name="cdocs_id"/>
                <input type="hidden" id="Phb_id" name="Phb_id"/>
                <div class="modal-body">
                    <div class="alert alert-danger display-hide confirmError">
                        <button class="close" data-close="alert"></button>
                        <strong>Error!</strong> <span class="error-msg"></span>
                    </div>
                    <p>Are you sure want to delete this attachment?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" onclick="cancelDeleteAttachment();">Cancel</button>
                    <button type="button" class="btn red" onclick="Delete_Attach_File();">Delete</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!--delete phone book entry-->
<div class="modal fade" id="deletePhoneBookModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo base_url(); ?>vendors/delete" method="post">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Delete Confirmation</h4>
                </div>
                <input type="hidden" id="phone_book_id" name="phone_book_id"/>
                <div class="modal-body">
                    <div class="alert alert-danger display-hide confirmError">
                        <button class="close" data-close="alert"></button>
                        <strong>Error!</strong> <span class="error-msg"></span>
                    </div>
                    <p>Are you SURE you want to delete this phonebook entry?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" onclick="cancelDeleteRecord();">Cancel</button>
                    <button type="submit" class="btn red">Delete</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<div class="modal fade" id="showSystemMessageModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Delete Confirmation</h4>
            </div>
            <input type="hidden" id="phone_book_id" name="phone_book_id"/>
            <div class="modal-body">
                <div class="alert alert-danger display-hide confirmError">
                    <button class="close" data-close="alert"></button>
                    <strong>Error!</strong> <span class="error-msg"></span>
                </div>
                <p>System account - please go to 'System>User management' to delete this user.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Cancel</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!--delete phone Number-->
<div class="modal fade" id="deletePhoneNumberModel" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo base_url(); ?>vendors/deletePhoneNumber" method="post" id="frm_delete_number">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                    <h4 class="modal-title">Delete Confirmation</h4>
                </div>
                <input type="hidden" id="phone_no_id" name="phone_no_id"/>
                <input type="hidden" id="phonebook_key" name="phonebook_key"/>
                <div class="modal-body">
                    <div class="alert alert-danger display-hide confirmError">
                        <button class="close" data-close="alert"></button>
                        <strong>Error!</strong> <span class="error-msg"></span>
                    </div>
                    <p>Are you sure want to delete this phone Number?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn default" onclick="cancelDeletePhoneNumber();">Cancel</button>
                    <button type="button" class="btn red" onclick="delete_number(this);">Delete</button>
                </div>
            </form>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<script>
    var log_table;
    var no_of_chars = 50;
    $(document).ready(function () {
      if ($("#slug").val() == "attachments") {
            $("#anchor_attachments").trigger("click");
        }
        $(".datepicker").datepicker();
      Dropzone.autoDiscover = false;
        $("div#my-dropzone").dropzone({
            url: site_url + "vendors/dropzone"
        });
        
        var Validator = $("#general_form").validate({
            errorElement: 'span', //default input error message container
            errorClass: 'help-block help-block-error', // default input error message class
            highlight: function (element) { // hightlight error inputs
                $(element)
                        .closest('.form-group').addClass('has-error'); // set error class to the control group
            },
            unhighlight: function (element) { // revert the change done by hightlight
                $(element)
                        .closest('.form-group').removeClass('has-error'); // set error class to the control group
            },
            errorPlacement: function (error, element) { // render error placement for each input type
                error.insertAfter(element); // for other inputs, just perform default behavior
            }
        });
        $('.account_dm').on('click', function (event) {
            $(this).parent().toggleClass('open');
        });
        jQuery(document).on('click', '.account_dm input[type="checkbox"]', function () {
            getnames();
        });
    });
    
    //for attachments
    function deleteAttachment(id, phonebook_id, docs_id) {
        $('#attach_id').val(id);
        $('#Phb_id').val(phonebook_id);
        $('#cdocs_id').val(docs_id);
        $('#deleteattachmentsModal').modal('show');
    }

    function cancelDeleteAttachment() {
        $('#attach_id').val(-1);
        $('#Phb_id').val(-1);
        $('#deleteattachmentsModal').modal('hide');
    }
    function delete_files() {
        var file_length = $(".dz-filename").length;
        if (file_length > 0) {
            $.ajax({
                type: 'POST',
                url: site_url + "sales/delete_all_files",
                data: {
                    action: 'delete_files'
                },
                beforeSend: function () {
                    $("#loadingmessage").show();
                },
                success: function (data) {
                    $("#loadingmessage").hide();
                    location.reload();
                }
            });
        }
    }
    function Delete_Attach_File() {
        $.ajax({
            type: 'POST',
            url: site_url + "vendors/deleteAttach",
            data: $('#frm_delete_attachment').serialize(),
            async: "false",
            beforeSend: function () {
                $("#loadingmessage").show();
            },
            success: function (responseJSON) {
                $("#loadingmessage").hide();
                $('#deleteattachmentsModal').modal('hide');
                var response = JSON.parse(responseJSON);
                if (response.status == 'success') {
                    var tot_files = $("#count_files").html();
                    var tot_file_size = $("#total_size").html();
                    var calc_tot_files = tot_files - 1;
                    var deleted_files_size = $("#" + response.id).parent("tr").find(".size").html();
                    var calc_tot_files_size = tot_file_size - deleted_files_size;
                    $("#count_files").html(calc_tot_files);
                    $("#total_size").html(calc_tot_files_size);
                    $("#" + response.id).parent("tr").remove();
                }
            }
        });
    }
    function deleteRecord(id, is_system) {
        if (is_system == 1) {
            $('#showSystemMessageModel').modal('show');
        } else {
            $('#phone_book_id').val(id);
            $('#deletePhoneBookModel').modal('show');
        }
    }
    function cancelDeleteRecord() {
        $('#phone_book_id').val(-1);
        $('#deletePhoneBookModel').modal('hide');
    }
    
    function remove_file(element) {
        var file_name = $(element).next("input[type='hidden']").val();
        var file_size = $(element).parent("div").find("input[type='hidden'][name='size[]']").val();
        var id = $(element).data("id");
        var tid = $("#tid").val();
        $.ajax({
            type: 'POST',
            url: site_url + 'vendors/delete_attachment',
            data: {
                action: 'removefile',
                file_name: file_name,
                file_size: file_size,
                tid: tid,
                id: id
            },
            success: function (data) {
                $(element).parents(".dz-preview").remove();
            }
        });
    }
    function add_customer_detail() {
    var active_id = $(".tab-content > div.active").attr("id");
    var active_tab = $("#active_tab").val();
    if (active_tab == "#attachments") {
        if_condition = "input:hidden";
    } else if (active_tab == "#notes") {
        if_condition = "textarea:visible";
    } else {
        if_condition = "input:visible,select:visible";
    }
    alert(active_tab +"=="+if_condition+"=="+$("#frm_contacts").find(if_condition).length);
    if ($("#frm_contacts").find(if_condition).valid()) {
        if ($("#display_name").val() == "") {
            $(".confirmError .error-msg").text("Please First Fill up General Tab Form!");
            $(".confirmError").removeClass("display-hide");
            $('.contact_wrapper').scrollTop(0);
        } else {            
                $(".confirmError .error-msg").text("");
                $(".confirmError").addClass("display-hide");
                $.ajax({
                    type: 'POST',
                    url: site_url + "vendors/add_customer_detail",
                    data: $('#frm_contacts').serialize(),
                    async: "false",
                    beforeSend: function () {
                        $("#loadingmessage").show();
                    },
                    success: function (responseJSON) {
                        $("#loadingmessage").hide();
                        var response = JSON.parse(responseJSON);
                        if (response.status == "error") {
                            $(".confirmError .error-msg").text(response.message);
                            $(".confirmError").removeClass("display-hide");
                            $('.contact_wrapper').scrollTop(0);
                        } else if (response.status === 'success') {
                            $("#address_id").val(response.id);
                            $("#attachments_id").val(response.id);
                            $("#id").val(response.id);
                            $(".confirmSuccess .success-msg").text("Record successfully saved");
                            $(".confirmSuccess").removeClass("display-hide");
                            $('.contact_wrapper').scrollTop(0);
                            $("#add_fname").val($("#first_name").val());
                            $("#add_lname").val($("#last_name").val());
                            $("#add_company").val($("#company").val());
                            $("#cemail").val($("#email").val());
                            if ($("#tblNotesListing").length) {
                                var html = '<tr>';
                                html += '<td id=' + response.notes_id + '>';
                                html += $("#notes_text").val();
                                html += '</td>';
                                html += '<td>';
                                html += '<a onclick="editNote(\'' + (response.notes_id) + '\', \'' + (response.id) + '\', \'' + ($("#notes_text").val()) + '\')">Edit</a>';
                                html += ' | <a onclick="deleteNote(\'' + (response.notes_id) + '\', \'' + (response.id) + '\')">Delete</a>';
                                html += '</td>';
                                html += '</tr>';
                                $("#tblNotesListing > tbody").append(html);
                                $("#notes_text").val("");
                            } else {
                                var html = '<br/> <div class="portlet box blue-hoki">';
                                html += '<div class="portlet-title">';
                                html += '<div class="caption">';
                                html += '<i class="icon-settings"></i>Notes';
                                html += '</div>';
                                html += '</div>';
                                html += '<div class="portlet-body">';
                                html += '<table class="table table-striped table-bordered table-hover" id="tblNotesListing">';
                                html += '<thead>';
                                html += '<tr>';
                                html += '<th width="85%"> Note </th>';
                                html += '<th width="15%"> Action </th>';
                                html += '</tr>';
                                html += '</thead>';
                                html += '<tbody>';
                                html += '<tr>';
                                html += '<td id=' + response.notes_id + '>';
                                html += $("#notes_text").val();
                                html += '</td>';
                                html += '<td>';
                                html += '<a onclick="editNote(\'' + (response.notes_id) + '\', \'' + (response.id) + '\', \'' + ($("#notes_text").val()) + '\')">Edit</a>';
                                html += ' | <a onclick="deleteNote(\'' + (response.notes_id) + '\', \'' + (response.id) + '\')">Delete</a>';
                                html += '</td>';
                                html += '</tr>';
                                html += '</tbody>';
                                html += '</table>';
                                html += '</div>';
//                            $("#frm_notes").append(html);
                            }
//                    }
                            if (active_tab == "#attachments") {
//                                location.reload();
                                window.location.href = site_url + "vendors/view/" + response.id + "?slug=attachments";
                            }
//                      location.reload();
                        }
                    }
                });
            }

        
    } else {
        console.log("not valid");
    }
}
$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    var target = $(e.target).attr("href") // activated tab
    $("#active_tab").val(target);
    if (target == "#address" || target == "#haddress") {
        $("#add_fname").val($("#first_name").val());
        $("#add_lname").val($("#last_name").val());
        $("#add_company").val($("#company").val());
    }
    $("#custDetailModal .alert").addClass("display-hide");
    $(".contact_wrapper .alert").addClass("display-hide");
});


</script>