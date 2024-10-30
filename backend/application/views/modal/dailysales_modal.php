<div class="modal fade" id="addattachment-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Add Attachment</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="portlet light portlet-fit portlet-form bordered">
                            <div class="portlet-body">
                                    <div class="form-body">
                                        <div id="msg"></div>
                                        <input type="hidden" name="hd_dynamic_column" id="hd_dynamic_column" value='<?php echo $dynamic_column ?>' />
                                        <input type="hidden" name="hd_cdate" id="hd_cdate" value="<?php echo $cdate ?>"/>
                                        <input type="hidden" name="hd_counter" id="hd_counter" value="<?php echo $counter ?>"/>
                                        <div class="form-group">
                                           <label class="control-label col-md-3">Invoice</label>
                                            <div class="col-md-6">
                                                <input type="file" name="dynamic_invoice_file" id="dynamicinvoicefile_counter" class="form-control" required/>
                                            </div><span id="fileerrormsg" style="color:red"></span>
                                        </div>
                                        <br><br>
                                        <?php if(sizeof($uploaded_attachments) > 0) : ?>
                                             <span id="lbl_invoice">(Ex. <?php echo $dynamic_column ?>_<?php echo sizeof($uploaded_attachments)+1 ?>_dd_mm_yyyy_StoreNumber)</span>
                                        <?php else: ?>
                                         <span id="lbl_invoice">(Ex. <?php echo $dynamic_column ?>_dd_mm_yyyy_StoreNumber)</span>
                                     <?php endif; ?>
                                     <br><br/>
                                       <div class="form-group">
                                            <div class="col-xs-3"></div>
                                            <div class="col-xs-3 btn_group">
                                                <input type="button" name="btn_upload" id="uploadcounter" value="Save" class="btn btn-primary" onclick="uploadattachments()"/>
                                            </div>
                                            <div class="col-xs-3"></div>
                                        </div>
                                    </div>
                                   <br>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>