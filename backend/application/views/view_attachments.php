<?php
$statement_id = isset($ledger_statement_data->id) ? $ledger_statement_data->id : '';
$description = isset($ledger_statement_data->description) ? $ledger_statement_data->description : '';
$transaction_type = isset($ledger_statement_data->transaction_type) ? $ledger_statement_data->transaction_type : '';
$l_date = isset($ledger_statement_data->credit_date) && $ledger_statement_data->credit_date != '' && $ledger_statement_data->credit_date != '0000-00-00 00:00:00' ? date('d F Y', strtotime($ledger_statement_data->credit_date)) : 'N/A';
//$description = isset($ledger_statement_data->credit_date) && $ledger_statement_data->credit_date != '' && $ledger_statement_data->credit_date != '0000-00-00 00:00:00' ? date('d F Y', strtotime($ledger_statement_data->credit_date)) : 'N/A';
//$description = isset($ledger_statement_data->description) ? $ledger_statement_data->description : '';
?>
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="7">Current attachments <?php echo $description; ?></th>
                </tr>
                <tr>

                    <th>Sr No.</th>
                    <th>Date</th>
                    <th>Type</th>
                    <th>Desc</th>
                    <th>Uploaded Name</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>

            </thead>
            <tbody>
                <?php
                if (isset($attachment_data['records']) && !empty($attachment_data['records'])) {
                    $i = 0;
                    foreach ($attachment_data['records'] as $aRow) {
                        $i++;
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $aRow->month . "/" . $aRow->year; ?></td>
                            <td><?php echo ucfirst($aRow->type); ?></td>
                            <td><?php echo $aRow->description; ?></td>
                            <td><?php
                                $uploaded_url = explode("/", $aRow->uploaded_url);
                                echo end($uploaded_url);
                                ?></td>
                            <td><?php
                                if ($aRow->type == 'invoice') {
                                    echo $aRow->transaction_type == 'credit' ? number_format($aRow->credit_amt, 2) : number_format($aRow->debit_amt, 2);
                                } else {
                                    echo "N/A";
                                }
                                ?>
                            </td>
                            <!--<td><?php echo $aRow->transaction_type == 'credit' ? number_format($aRow->credit_amt, 2) : number_format($aRow->debit_amt, 2); ?></td>-->
                            <td>
                                <a href="<?php echo base_url($aRow->uploaded_url); ?>" download><i class="fa fa-download"></i></a>
                                <a href="<?php echo base_url($aRow->uploaded_url); ?>" target="_blank"><i class="fa fa-eye"></i></a>
                                <a href="javascript:void(0);" data-toggle="modal" data-id="<?php echo $aRow->id; ?>" onclick="setConfirmDetails(this)" data-target="#ConfirmDeleteModal" data-url="<?php echo base_url("statement/delete_attachment/" . $aRow->id."/".$aRow->statement_id); ?>"><i class="fa fa-trash"></i></a>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr><th colspan="7"><center>No Records Found.</center></th></tr>
                <?php
            }
            ?>
            </tbody>
        </table>
          <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="9">Reconcile Details Of <?php echo $description; ?></th>
                </tr>
                <tr>

                    <th>Sr No.</th>
                    <th>Ledger Transaction Type</th>
                    <th>Ledger Date</th>
                    <th>Ledger Description</th>
                    <th>Bank Date</th>
                    <th>Bank Description</th>
                    <th>Uploaded Name</th>
                    <th>Amount</th>
                    <th>Action</th>
                </tr>

            </thead>
            
            <tbody>
                <?php 
//                echo "<pre>";
//                print_r($bank_statement_entries);
//                echo "</pre>";
                if(isset($bank_statement_entries) && !empty($bank_statement_entries)){
                $i = 0;
                            foreach ($bank_statement_entries as $bRow){
                                $i++;
                                ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo  $transaction_type; ?></td>
                    <td><?php echo  $l_date; ?></td>
                    <td><?php echo  $description; ?></td>
                    <td><?php echo  date('m/d/Y', strtotime($bRow->date)); ?></td>
                    <td><?php echo $bRow->description; ?></td>
                    <td><?php echo $bRow->uploaded_file_name; ?></td>
                    <td><?php echo number_format($bRow->amount, 2); ?></td>
                     <td>
                                <a href="<?php echo base_url($bRow->uploaded_url); ?>" download><i class="fa fa-download"></i></a>
                                <a href="<?php echo base_url($bRow->uploaded_url); ?>" target="_blank"><i class="fa fa-eye"></i></a>
                                <a href="javascript:void(0);" data-toggle="modal" data-id="<?php echo $bRow->attach_id; ?>" onclick="setConfirmDetails(this)" data-target="#ConfirmDeleteModal" data-url="<?php echo base_url("statement/delete_attachment/" . $bRow->attach_id."/".$statement_id); ?>"><i class="fa fa-trash"></i></a>
                            </td>
                </tr>
            
                                <?php
                            }
                }else{
                    ?>
                <tr><td colspan="9"><center><b>This ledger statement isn't reconciled yet.</b></center></td></tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
        <?php if($transaction_type == 'debit') { ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="5">Last 5 transactions details of <?php echo $description; ?></th>
                </tr>
                <tr>

                    <th>Sr No.</th>
                    <th>Date</th>
                    <th>Desc</th>
                    <th>Amount</th>
                    <th>View</th>
                </tr>

            </thead>
            <tbody>
                <?php
                if (isset($month_data) && !empty($month_data)) {
                    $i = 0;
                    foreach ($month_data as $aRow) {
                        $i++;
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $aRow->month . "/" . $aRow->year; ?></td>
                            <td><?php echo $aRow->description; ?></td>
                            <td><?php echo $aRow->transaction_type == 'credit' ? number_format($aRow->credit_amt, 2) : number_format($aRow->debit_amt, 2); ?></td>
                            <!--<td><a href="<?php echo base_url($aRow->uploaded_url); ?>" target="_blank"><i class="fa fa-eye"></i></a></td>-->
                            <td><center>
                        <?php if ($aRow->id != '') { ?>
                            <a href="<?php echo base_url('statement/download_attachment/' . $aRow->statement_id) ?>" target="_blank"><i class="fa fa-download"></i></a>
                        <?php
                        } else {
                            echo "N/A";
                        }
                        ?>
                    </center></td>
                    </tr>
                    <?php
                }
            } else {
                ?>
                <tr><th colspan="5"><center>No Records Found.</center></th></tr>
    <?php
}
?>
            </tbody>
        </table>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th colspan="5">Last 5 same month transaction details of <?php echo $description; ?></th>
                </tr>
                <tr>
                    <th>Sr No.</th>
                    <th>Date</th>
                    <th>Desc</th>
                    <th>Amount</th>
                    <th>View</th>
                </tr>

            </thead>
            <tbody>
                <?php
                if (isset($year_data) && !empty($year_data)) {
                    $i = 0;
                    foreach ($year_data as $aRow) {
                        $i++;
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $aRow->month . "/" . $aRow->year; ?></td>
                            <td><?php echo $aRow->description; ?></td>
                            <td><?php echo $aRow->transaction_type == 'credit' ? number_format($aRow->credit_amt, 2) : number_format($aRow->debit_amt, 2); ?></td>
                            <td><a href="<?php echo base_url($aRow->uploaded_url); ?>" target="_blank"><i class="fa fa-eye"></i></a></td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr><th colspan="5"><center>No Records Found.</center></th></tr>
    <?php
}
?>
            </tbody>
        </table>
        <?php } ?>
    </div>
</div>
<script>
    function setConfirmDetails(value) {
        var delete_url = $(value).attr("data-url");
        $("#ConfirmDeleteModal").find(".modal-body").children("p").show();
        $("#ConfirmDeleteModal").find(".confirmYes").show();
        $("#ConfirmDeleteModal").find(".modal-title").html("Bank Statement Deletion");
        $("#ConfirmDeleteModal").find(".modal-body").children("p").html("Do you really want to delete this attachment");
        $("#ConfirmDeleteModal").find("#ConfirmDelete").attr('href', delete_url);
    }
</script>