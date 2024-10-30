<div class="modal fade" id="showattachment-modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Uploaded Attachment</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <input type="hidden" name="hd_show_dynamic_column" id="hd_show_dynamic_column" value='<?php echo $dynamic_column ?>' />
                    <input type="hidden" name="hd_show_cdate" id="hd_show_cdate" value="<?php echo $cdate ?>"/>
                    <input type="hidden" name="hd_show_counter" id="hd_show_counter" value="<?php echo $counter ?>"/>
                     <table class="table table-bordered" id="show-attachement-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Uploaded Attachments</th>
                                <th width="15%">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        if (isset($uploaded_attachments)) {
                            $i = 0;
                            foreach ($uploaded_attachments as $row) {
                                ?>
                                <tr>
                                    <td><?php echo ++$i; ?></td>
                                    <td><?php echo isset($row->uploaded_file_name) ? $row->uploaded_file_name : '' ?></td>
                                    <td>
                                        <a href="javascript:void(0)" deleteid="<?php echo $row->id; ?>" title="Delete" onclick="deleteAttachment(this);" ><i class="fa-icons fa fa-trash fa-2x fa-danger"></i></a>
                                    </td>
                                </tr>
                                <?php
                            }
                        }else{
                        ?>
                        <tr><td colspan=3 style="color:red">No more records</td></tr>
                    <?php } ?>
                            <span id="no-records" style="display:none;color:red">No more Records</tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>