<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body">
         <?php echo $this->session->flashdata('msg'); ?>
        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover"  id="tbl_file_history">
                    <thead>
                        <tr>
                            <th>File Id</th>
                            <th>File Name</th>
                            <th>File Type</th>
                            <th>Success</th>
                            <th>Failure</th>
                            <th>Uploaded At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (sizeof($file_history['records']) > 0) : ?> 
                        <?php foreach ($file_history['records'] as $row) : ?>
                            <tr>
                                <td><?php echo $row->file_id ?></td>
                                <td><?php echo $row->file_name ?></td>
                                <td><?php echo $file_types[$row->file_type] ?? "" ?></td>
                                <td><?php echo $row->success ?></td>
                                <td><?php echo $row->failure ?></td>
                                <td><?php echo $row->upload_at ?></td>
                                <td>
                                    <a href="<?= base_url("common/history_download/$row->file_id") ?>" class="btn btn-sm btn-primary"><i class="fa fa-download"></i> POS Download</a>
                                    <a href="<?= base_url("common/history_download/$row->file_id/fail") ?>" class="btn btn-sm btn-danger"><i class="fa fa-download"></i> Faild POS Download</a>
                                    <a data-toggle="modal" data-id="<?= $row->file_id ?>" onclick="setConfirmDetails(this); return false;" data-target="#ConfirmDeleteModal" data-url="<?= base_url("common/history_revert/$row->file_id") ?>" class="btn btn-sm btn-danger"><i class="fa fa-undo"></i> Revert</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php else : ?>
                            <tr><td colspan="6" style="color: red; text-align: center;">No more Records</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(){
        $("#tbl_file_history").dataTable({});
    });
    function setConfirmDetails(value) {
        var revert_url = $(value).attr("data-url");
        $("#ConfirmDeleteModal").find(".modal-body").children("p").show();
        $("#ConfirmDeleteModal").find(".confirmYes").show();
        $("#ConfirmDeleteModal").find(".modal-title").html("Revert Confiramtion");
        $("#ConfirmDeleteModal").find(".modal-body").children("p").html("Do you really want to revert this file?");
        $("#ConfirmDeleteModal").find("#ConfirmDelete").attr('href', revert_url);
    }
</script>