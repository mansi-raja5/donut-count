<div class="portlet box green-haze">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-picture"></i><b><?php echo $title; ?> (<?php echo count($credit_received_record); ?>)</b>
        </div>
        <div class="tools">
            <a href="javascript:;" class="expand" data-original-title="" title=""> </a>
            <a href="" class="fullscreen"> </a>
            <!--<a href="javascript:;" class="reload" data-original-title="" title=""> </a>-->
        </div>
    </div>
    <div class="portlet-body">
        <table class="table table-condensed table-hover" id="<?php echo $table_id; ?>">
            <thead>
                <tr>
                    <th width="3%"><b>No</b></th>
                    <th width="50%"><b>Credit Received Form</b></th>
                    <th width="15%"><b>Amount</b></th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <th width="3%"><b>No</b></th>
                     <th width="50%"><b>Credit Received Form</b></th>
                    <th width="15%"><b>Amount</b></th>
                </tr>
            </tfoot>
            <tbody>
                <?php
                if (isset($credit_received_record) && !empty($credit_received_record)) {
                    $key = 0;
                    foreach ($credit_received_record as $row) {
                        ?>
                        <tr id="ledger-<?php echo $row->id ?>">
                            <td><?php echo ++$key; ?></td>
                            <td><?php echo $row->label; ?></td>
                            <td><?php echo $row->amount; ?></td>
                        </tr>
                        <?php
                    }
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<!-- END CONDENSED TABLE PORTLET-->
<script type="text/javascript">
$(document).ready(function () {
//    $('.collapse').trigger('click');
    // Setup - add a text input to each footer cell
    $('#<?php echo $table_id; ?> tfoot th').each(function () {
        var title = $(this).text();
        $(this).html('<input type="text"  style="width:100%;" class="current-credit-received-input" placeholder="Search ' + title + '" />');
    });

    // DataTable
    var adjustmnt_entries_datatable = $('#<?php echo $table_id; ?>').DataTable({
        "paging": false,
        "info": false,
        "stripeClasses": [],
        "autoWidth": false
    });

    // Apply the search
    adjustmnt_entries_datatable.columns().every(function () {
        var that = this;
        $('.current-credit-received-input', this.footer()).on('keyup change clear', function () {
            if (that.search() !== this.value) {
                that
                        .search(this.value)
                        .draw();
            }
        });
    });
//     $(".collapse").each(function(){
//       $(this).trigger("click"); 
//    });
});
</script>