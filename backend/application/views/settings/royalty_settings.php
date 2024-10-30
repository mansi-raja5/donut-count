<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body">
        <form name="royalty-main-form" id="royalty-setting-form">
            <div class="col-md-2 mb10 pull-right">
                <input type="button" class="btn purple btn-block" id = "save_royalty_btn" value="Save Royalty Setting" onclick="roy.saveRoyaltySettingData();">
            </div>
            <div class="row">
                <table class="table table-striped table-hover" >
                    <thead>
                        <tr>
                            <th>
                                #
                            </th>
                            <th>
                                Store Number
                            </th>
                            <th>
                                Royalty Type
                            </th>
                            <th>
                                Royalty Percentage R%
                            </th>
                            <th>
                                Adfund Percentage T%
                            </th>
                            <th>
                                Customer count
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $rowNumber = 0;
                        if (isset($royal_data)) {
                            foreach ($royal_data as $_storekey => $_royal_data) {
                                foreach ($_royal_data as $_royalType => $_royal_type_data) {
                                ?>
                                <tr class="default-disabled">
                                    <td>
                                        <?php echo ++$rowNumber; ?>
                                    </td>
                                    <td>
                                        <label><?php echo $_storekey; ?></label>
                                        <input type="hidden" name="royal[<?php echo $_storekey;?>][<?php echo $_royalType;?>][store_key]" class="store_key form-control" value="<?php echo $_storekey; ?>">
                                    </td>
                                    <td>
                                        <label><?php echo $_royalType;?></label>
                                        <input type="hidden" name="royal[<?php echo $_storekey;?>][<?php echo $_royalType;?>][royal_type]" class="royal_type form-control" value="<?php echo $_royalType;?>">
                                    </td>
                                    <td>
                                        <input type="text" name="royal[<?php echo $_storekey;?>][<?php echo $_royalType;?>][royalty_per]" class="royalty_per form-control" value="<?php echo $_royal_type_data['royalty_percentage'];?>">
                                    </td>
                                    <td>
                                        <input type="text" name="royal[<?php echo $_storekey;?>][<?php echo $_royalType;?>][adfund_per]" class="adfund_per form-control" value="<?php echo $_royal_type_data['adfund_percentage'];?>">
                                    </td>
                                    <td>
                                        <input type="text" name="royal[<?php echo $_storekey;?>][<?php echo $_royalType;?>][cust_count]" class="cust_count form-control" value="<?php echo $_royal_type_data['customer_count_for_br'];?>">
                                    </td>
                                </tr>
                                <?php
                                }
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
<script src="<?php echo base_url(); ?>assets/js/royalty.js" type="text/javascript"></script>
<script type="text/javascript">
let roy = new Royalty();
</script>