<div class="col-md-12">
    <table class="table table-striped table-bordered table-hover" id="tbl_donutcount">
        <thead>
            <tr>
                <th style="width:10%">Date</th>
                <th style="width:5%">Day</th>
                <th colspan=2>Donuts</th>
                <th colspan=2>Fancy</th>
                <th colspan=2>Munkins</th>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td>Order</td>
                <td>Sale</td>
                <td>Order</td>
                <td>Sale</td>
                <td>Order</td>
                <td>Sale</td>
            </tr>
        </thead>
        <tbody>
            <?php
            if(sizeof($alldates) > 0):
                $i=0;
                foreach($alldates as $dates):
                ?>
                <tr id="row_<?php echo $i ?>">
                    <td><?php echo date('m/d/yy',strtotime($dates)) ?></td>
                    <td><?php echo date('D',strtotime($dates)) ?></td>
                    <?php foreach($dynamic_column as $value): ?>
                        <td style="text-align: center;">
                            <?php
                            if($donutdata != ""):
                                if(isset($donutdata[$dates][$value]))
                                    echo "$".$donutdata[$dates][$value];
                                else
                                    echo "-";
                            else:
                                echo "-";
                            endif;
                            ?>
                        </td>
                    <?php endforeach; ?>
                </tr>
            <?php
                $i++;
                endforeach;
            else:
            ?>
                <p class="no-data">No more data</p>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<script>
    $("#tbl_donutcount").dataTable({
        scrollY: '70vh',
        scrollX: '100vw',
        scrollCollapse: true,
        fixedColumns: {
            leftColumns: 2,
        },
        paging: false,
    });
</script>