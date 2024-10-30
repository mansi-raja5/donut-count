<div class="col-md-12">
    <table class="table table-striped table-bordered" id="tbl_cardrecap">
        <thead>
            <th style="width:70%">Date</th>
            <th>Day</th>
            <th colspan=2>MASTERCARD</th>
            <th colspan=2>VISA</th>
            <th colspan=2>AMEX</th>
            <th colspan=2>DISCOVER</th>
            <th >CC RECAP</th>
            <th colspan=3>DUNKIN CARDS</th>
            <th>DD CARDS</th>
            <!--<th>DD PAPER</th>-->
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
                   $is_lock_entry = isset($cardrecapdata[$dates]['is_lock']) ? $cardrecapdata[$dates]['is_lock'] : '0';
        ?>
                <tr id="row_<?php echo $i ?>" class="<?php echo ($is_lock_entry == 1) ? 'lock_tr_bg' : ''; ?>">
                    <td><?php echo $dates ?></td>
                     <td><?php echo date('D',strtotime($dates)) ?></td>
                    <?php foreach($dynamic_column as $key=>$value): ?>

                    <td><?php 
                            if($cardrecapdata!=""): if(isset($cardrecapdata[$dates][$key])) 
                               if (strpos($key, 'transaction') !== false) echo $cardrecapdata[$dates][$key]; else echo "$".$cardrecapdata[$dates][$key]; 
                            else 
                                echo "-"; 
                    else: 
                        echo "-";   
                    endif; ?>
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
<div id="add_uploadattachment_modal"></div>
<script>
    $("#tbl_cardrecap").dataTable({
        scrollY: '70vh',
        scrollX: '100vw',
        scrollCollapse: true,
        fixedColumns: {
            leftColumns: 2,
        },
        paging: false,
    });
</script>