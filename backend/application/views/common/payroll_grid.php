<div class="col-md-12">
    <table class="table table-striped table-bordered table-hover" id="tbl_payroll">
        <thead>
            <th></th>
            <th>Start Date</th>
            <th>End Date</th>
            <?php
                if(sizeof($dynamic_column)) {
                    foreach($dynamic_column as $value) {
            ?>                                  
                        <th><?php echo $value; ?></th>
            <?php   }
                }
            ?>
            </thead>
            <tbody>
                <?php if(sizeof($start_date) > 0):
                    $i = -1;
                    foreach($start_date as $dates):
                        $end = date("d-m-Y",strtotime($end_date[++$i]));
                        $isLock = isset($payroll[$end]['is_lock']) ? $payroll[$end]['is_lock'] : 0;
                        $payrollId = isset($payroll[$end]['id']) ? $payroll[$end]['id'] : 0;
                    ?>
                    <tr>
                        <td>
                            <i class="<?php echo $isLock == 1 ? "fa fa-lock" : "fa fa-unlock"; ?>"
                                 data-value="<?php echo $isLock; ?>"
                                 data-payrollid="<?php echo $payrollId; ?>"
                                 onclick="lock_paidout_entry(this);">
                            </i>
                        </td>                    
                        <td><?php echo showInDateFormat($dates) ?></td>
                        <td><?php echo showInDateFormat($end_date[$i]) ?></td>
                        <?php
                            if(sizeof($dynamic_column)) {
                                foreach($dynamic_column as $key=>$value) {
                                ?>
                                    <td>
                                        <?php
                                        if(isset($payroll[$end][$key]))
                                        {
                                            echo "$".$payroll[$end][$key]; 
                                        }
                                        else
                                        {
                                            echo "-";
                                        }
                                        ?>
                                    </td>
                                <?php   
                                }
                            }
                        ?>
                    </tr>
                    <?php
                    endforeach; 
                ?>
            <?php endif; ?>
            </tbody>
        </thead>
    </table>
</div>
<script>
    $("#tbl_payroll").dataTable({
        scrollY: '70vh',
        scrollX: '100vw',
        scrollCollapse: true,
        fixedColumns: {
            leftColumns: 2,
        },
        paging: false,
        "aaSorting": [],
        "order": []
    });
    function lock_paidout_entry(element) {
        var payrollId =$(element).data("payrollid");
        var is_lock = $(element).data("value");        
        $.ajax({
            method: 'POST',
            url: site_url + 'common/LockUnlockEntry',
            data: {id:payrollId, is_lock: is_lock, type:"master_payroll"},
            async: false,
            beforeSend: function () {
                $("#loadingmessage").show();
            },
            success: function (data) {
                $("#loadingmessage").hide();
                if (is_lock == 1) {
                    $(element).parents("tr").removeClass("lock_tr_bg");
                } else {
                    $(element).parents("tr").addClass("lock_tr_bg");
                }
                $(element).parents("tr").find("input[type='text']").each(function () {
                    if (is_lock == 1) {
                        $(this).css("pointer-events", "initial");
                    } else {
                        $(this).css("pointer-events", "none");
                    }
                });
                (is_lock == 1) ? $(element).data("value", 0) : $(element).data("value", 1);
                $(element).toggleClass('fa-lock fa-unlock');
            }
        });
    }
</script>