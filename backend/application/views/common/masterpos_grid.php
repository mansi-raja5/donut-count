<input type="hidden" name="store_key" value="<?php echo $store_key ?>">
<input type="hidden" name="year" value="<?php echo $year ?>">
<input type="hidden" name="month" value="<?php echo $month ?>">
<div class="col-md-12">
    <table class="table table-striped table-bordered" id="tbl_masterpos">
        <thead>
            <th>Action</th>
            <th style="width:70%">Date</th>
            <?php
                if(sizeof($dynamic_column)) {
                    foreach($dynamic_column as $value){
            ?>
                        <th style="width:50%"><?php echo $value ?></th>
            <?php   }
                }
            ?>
            </thead>
            <tbody>
                <?php
                if(sizeof($alldates) > 0):
                    foreach($alldates as $dates):
                        $jsondata = [];
                        $exists = "";
                        $posId = $isLock = 0;
                        if(count($masterpos) > 0):
                            $exists = array_search($dates, array_column($masterpos, 'cdate'));
                            if($exists !== false){
                                $jsondata = json_decode($masterpos[$exists]['data']);
                                $posId = $masterpos[$exists]['id'];
                                $isLock = $masterpos[$exists]['is_lock'];
                            }
                        endif;                            
                ?>
                <tr class="<?php echo ($isLock == 1) ? 'lock_tr_bg' : ''; ?>">
                    <td>
                        <input type='hidden' name='pos_id[]' value='<?php echo $posId ?>'>
                        <input type='hidden' name='cdate[]' value='<?php echo $dates ?>'>
                        <i class="<?php echo $isLock == 1 ? "fa fa-lock" : "fa fa-unlock"; ?>" data-value="<?php echo $isLock; ?>" onclick="lock_paidout_entry(this);"></i>
                    </td>
                    <td><?php echo showInDateFormat($dates); ?></td>
                    <?php foreach($dynamic_column as $key=>$value): ?>
                        <td style="text-align: center">
                            <?php
                            $cellData = "-";
                            if($jsondata != "" && isset($jsondata->$key)):
                                $cellData = $jsondata->$key;
                                if (strpos($key, 'qty') !== false) :
                                    echo $cellData;
                                else :
                                    echo showInDollar($cellData);
                                endif;
                            else :
                                 echo ($cellData);
                            endif;
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <?php
                    endforeach;
                else:
                ?>
                    <p class="no-data">No Records Found</p>
                <?php endif; ?>

                <!-- Show monthly and weekly data -->
                <?php 
                $masterposWeeklyMonthly = array_merge($masterpos_monthly,$masterpos_weekly);
                foreach($masterposWeeklyMonthly + $masterpos_monthly as $_masterpos):
                ?>
                <tr class="<?php echo ($_masterpos['is_lock'] == 1) ? 'lock_tr_bg' : ''; ?>">
                    <td>
                        <input type='hidden' name='pos_id[]' value='<?php echo $_masterpos['id'] ?>'>
                        <i class="<?php echo $_masterpos['is_lock'] == 1 ? "fa fa-lock" : "fa fa-unlock"; ?>" data-value="<?php echo $_masterpos['is_lock']; ?>" onclick="lock_paidout_entry(this);" data-type="<?php echo $_masterpos['type']; ?>"></i>
                    </td>
                    <td><?php echo showInDateFormat($_masterpos['start_date'])."-".showInDateFormat($_masterpos['end_date']) ?></td>
                    <?php foreach($dynamic_column as $key=>$value): ?>
                        <td style="text-align: center">
                            <?php
                            $cellData = "-";
                            $jsondata = json_decode($_masterpos['data']);
                            if($jsondata != "" && isset($jsondata->$key)):
                                $cellData = $jsondata->$key;
                                if (strpos($key, 'qty') !== false) :
                                    echo $cellData;
                                else :
                                    echo showInDollar($cellData);
                                endif;
                            else :
                                 echo ($cellData);
                            endif;
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
                <?php endforeach; ?>
            </tbody>
    </table>
</div>
<script>
    $("#tbl_masterpos").dataTable({
        scrollY: '70vh',
        scrollX: '100vw',
        scrollCollapse: true,
        fixedColumns: {
            leftColumns: 2,
        },
        paging: false,
        columnDefs: [
            { width: '20%', targets: 0}
        ],
    });

    function lock_paidout_entry(element) {
        var store_key = $("input[name='store_key']").val();
        var month = $("input[name='month']").val();
        var year = $("input[name='year']").val();
        var cdate = $(element).parents("tr").find("input[name='cdate[]']").val();
        var pos_id = $(element).parents("tr").find("input[name='pos_id[]']").val();
        var is_lock = $(element).data("value");
        var type = $(element).data("type") != undefined ? $(element).data("type") : "master_pos_daily";
            $.ajax({
                method: 'POST',
                url: site_url + 'common/LockUnlockEntry',
                data: {store_key: store_key, month: month, year: year, cdate: cdate,id:pos_id, is_lock: is_lock, type:type},
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