<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
    <h4 class="modal-title"><?php echo $title; ?></h4>
</div>
<div class="modal-body">
    <div class="alert alert-danger display-hide confirmError">
        <button class="close" data-close="alert"></button>
        <strong>Error!</strong> <span class="error-msg"></span>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Description</th>
                        <th>Invoice</th>
                        <th>Document 1</th>
                        <th>Document 2</th>
                        <th>Document 3</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
//                    echo "<pre>";
//                    print_r($attachment_data);
//                    exit;
                    if (isset($attachment_data) && !empty($attachment_data)) {
                        foreach ($attachment_data as $aRow) {
                            $types_Arr = explode(",", $aRow->types);
                            $file_names_Arr = explode(",", $aRow->uploaded_file_names);

                            $type_invoice_index = array_search("invoice", $types_Arr);
                            $invoice_name = isset($file_names_Arr[$type_invoice_index]) ? $file_names_Arr[$type_invoice_index] : '';
                            unset($file_names_Arr[$type_invoice_index]);
                            $final_arr = array_values($file_names_Arr);
                            ?>
                            <tr>
                                <td><?php echo $aRow->description; ?></td>
                                <td><?php echo isset($invoice_name) ? "<span>" . $invoice_name . "</span><button class='btn btn-success btn-sm pull-right' name='copy_genreate_key' type='button' onclick=copy_generate_key(this);>Copy</button>" : "N/A"; ?></td>
                                <td><?php echo isset($final_arr[0]) ? $final_arr[0] . "</span><button class='btn btn-success btn-sm pull-right' name='copy_genreate_key' type='button' onclick=copy_generate_key(this);>Copy</button>" : "N/A"; ?></td>
                                <td><?php echo isset($final_arr[1]) ? $final_arr[1] . "</span><button class='btn btn-success btn-sm pull-right' name='copy_genreate_key' type='button' onclick=copy_generate_key(this);>Copy</button>" : "N/A"; ?></td>
                                <td><?php echo isset($final_arr[2]) ? $final_arr[2] . "</span><button class='btn btn-success btn-sm pull-right' name='copy_genreate_key' type='button' onclick=copy_generate_key(this);>Copy</button>" : "N/A"; ?></td>
                            </tr>
                            <?php
                        }
                    } else {
                        ?>
                        <tr><td colspan="4"><center><b>No Records Found</b></center></td></tr>
                    <?php
                }
                ?>
                </tbody>
            </table>

            <?php
            ?>
        </div>
        <?php
//        echo "<pre>";
//        print_r($attachment_data);
//        echo "</pre>";
        ?>
    </div>
</div>
<div class="modal-footer" style="display: <?php (isset($is_sales_tax) && $is_sales_tax == 1) ? "block" : "none"; ?>">
    <button class="btn default" data-dismiss="modal" aria-hidden="true">Cancel</button>
    <button class="btn blue confirmYes" aria-hidden="true" data-id="0" type="button">Save</button>
</div>
<script>
    function copy_generate_key(element) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val($(element).prev("span").text()).select();
        document.execCommand("copy");
        $temp.remove();
    }
</script>
<!--$(element).prev("span")-->
