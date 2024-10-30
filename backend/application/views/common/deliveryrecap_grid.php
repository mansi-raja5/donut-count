<div class="col-md-12">
    <div class="table-responsive">
    <table class="table table-striped table-bordered" id="tbl_deliveryrecap">
        <thead>
            <tr>
            <th style="width:70%">Date</th>
            <th>Day</th>
            <th colspan=2>GRUB-HUB</th>
            <th>GRUB-HUB NET</th>
            <th colspan=2>UBER-EATS</th>
            <th>UBER-EATS NET</th>
            <th>DELIVERY NET RECAP</th>
            <th colspan=2>EXTERNAL/KIOSK VISA</th>
            <th colspan=2>EXTERNAL/KIOSK MASTER CARD</th>
            <th colspan=2>EXTERNAL/KIOSK AMERICAN EXPRESS</th>
            <th colspan=2>EXTERNAL/KIOSK DISCOVER</th>
            <th>EXTERNAL/KIOSK ORDER</th>
            <th>EXTERNAL/KIOSK GIFT CARDS</th>
    </tr>
            <tr>
                <td></td>
                <td></td>
                 <?php
                if(sizeof($dynamic_column)) {
                    foreach($dynamic_column as $value){
                ?>
                            <td style="width:50%"><?php echo $value ?></td>
                <?php   }
                    }
                ?>
            </tr>
        </thead>
        <tbody>

        <?php
        if(sizeof($alldates) > 0):
            $i=0;
            foreach($alldates as $dates):
                   $is_lock_entry = isset($deliveryrecapdata[$dates]['is_lock']) ? $deliveryrecapdata[$dates]['is_lock'] : '0';
        ?>
                <tr id="row_<?php echo $i ?>" class="<?php echo ($is_lock_entry == 1) ? 'lock_tr_bg' : ''; ?>">
                    <td><?php echo $dates ?></td>
                     <td><?php echo date('D',strtotime($dates)) ?></td>
                    <?php foreach($dynamic_column as $key=>$value): ?>

                    <td><?php if($deliveryrecapdata!=""): if(isset($deliveryrecapdata[$dates][$key]))
                     if (strpos($key, 'transaction') !== false)  echo $deliveryrecapdata[$dates][$key]; else echo "$".$deliveryrecapdata[$dates][$key]; 
                    else echo "-"; else: echo "-";   endif; ?>
                    </td>

                    <?php endforeach; ?>
                </tr>
        <?php
            $i++;
            endforeach;
        else:
        ?>
            <p class="no-data">No more data</p>
        <?php
        endif;
        ?>
        </tbody>
    </table>
    </div>
</div>
<div id="add_uploadattachment_modal"></div>
<script>
    $("#tbl_deliveryrecap").dataTable({
        scrollY: '70vh',
        scrollX: '100vw',
        scrollCollapse: true,
        fixedColumns: {
            leftColumns: 2,
        },
        paging: false,
    });
</script>