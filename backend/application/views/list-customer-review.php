<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$min_year = 2015;
$max_year = 2025;
?>
<style>
    .duration_title{
        font-size: 14px;
        font-weight: bold;
        text-transform: uppercase;
    }
</style>
<!-- BEGIN EXAMPLE TABLE PORTLET-->
<div class="portlet box blue">
    <div class="portlet-title">
        <div class="caption">
            <i class="fa fa-globe"></i>
            <span class="caption-subject bold uppercase"><?php echo $title; ?></span>
        </div>
    </div>
    <div class="portlet-body">
        <?php
        $attributes = array('ndame' => 'frmSearch', 'id' => 'frmSearch', 'method' => 'post');
        echo form_open(base_url('review/view'), $attributes);
        ?>
        <div class="row">
            <div class="col-md-4 mb10 pull-right text-right">
                <a href="<?php echo base_url('common'); ?>" class="btn blue" id="new_project">Add Customer Review</a>
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-md-3 mb10">
                <?php
                $options = array();
                $options[''] = '-- Select Store  --';
                $store_key = field(set_value('store_key', NULL), $this->input->post('store_key'));
                if (isset($store_list['records']) && !empty($store_list['records'])) {
                    foreach ($store_list['records'] as $row) {
                        $options[$row->key] = $row->name . " (" . $row->key . ")";
                    }
                }
                echo form_dropdown(array('id' => 'store_key', 'name' => 'store_key', 'options' => $options, 'class' => 'form-control select2me', 'selected' => $store_key));
                ?>
            </div>
            <div class="col-md-3 mb10">
                <?php
                $curr_end_date = (date('D') != 'Sat') ? date('d-m-Y', strtotime('next Saturday')) : date('d-m-Y');
                $end_date = field(set_value('end_date', NULL), ($this->input->post('end_date') != '') ? date("d-m-Y", strtotime($this->input->post('end_date'))) : $curr_end_date);
                ?>
                <?php
                echo form_input(array('required' => 'required', 'id' => 'end_date', 'name' => 'end_date', 'class' => 'form-control datepicker', 'value' => $end_date));
                ?>
            </div>
            <div class="col-md-3">
                <button type="submit" id="btnSearch" name="btnSubmit" class="btn btn-success">Submit</button>
            </div>
        </div>
        <?php echo form_close(); ?>


        <div class="row">
            <div class="col-md-12">
                <table class="table table-striped table-bordered table-hover"  id="tblListing1">
                    <thead>
                        <tr>
                            <th>Store</th>
                            <th>Duration</th>
                            <th colspan="2">Cleanliness</th>
                            <th colspan="2">Crew Manager</th>
                            <th colspan="2">Overall Satisfaction</th>
                            <th colspan="2">Speed of Service</th>
                            <th colspan="2">Taste of Beverage</th>
                            <th colspan="2">Taste of Food</th>
                            <?php
//                            if (isset($key_list) && !empty($key_list)) {
//                                foreach ($key_list as $kRow) {
                            ?>
                                    <!--<th colspan="2"><?php echo $kRow->type; ?></th>-->
                            <?php
//                                }
//                            }
                            ?>
                        </tr>
                        <tr>
                            <th></th>
                            <th></th>
                            <th>n</th>
                            <th>5</th>
                            <th>n</th>
                            <th>5</th>
                            <th>n</th>
                            <th>5</th>
                            <th>n</th>
                            <th>5</th>
                            <th>n</th>
                            <th>5</th>
                            <th>n</th>
                            <th>5</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($review_Arr) && !empty($review_Arr)) {

                            foreach ($review_Arr as $key => $val) {
                                $store_key = $key;
                                ?>
                          <tr><td colspan="14" style="font-size: 16px;font-weight: bold;">&nbsp;</td></tr>
                        <?php
                                if (isset($val) && is_array($val) && !empty($val)) {
                                    foreach ($val as $key => $vRow) {
                                       
                                        $duration_type = str_replace("_", " ", $key)
                                        ?>
<!--                                        <tr>
                                            <td colspan="14" class="duration_title"><?php echo $duration_type; ?></td>
                                        </tr>-->
                                        <tr>
                                            <td><b><?php echo $store_key; ?></b></td>
                                            <td><b><?php echo $duration_type; ?></b></td>
                                            <td><?php echo isset($vRow['cleanliness_n']) ? $vRow['cleanliness_n'] : "N/A"; ?></td>
                                            <td><?php echo isset($vRow['cleanliness_5']) ? $vRow['cleanliness_5'] : "N/A"; ?></td>
                                            <td><?php echo isset($vRow['crew_manager_n']) ? $vRow['crew_manager_n'] : "N/A"; ?></td>
                                            <td><?php echo isset($vRow['crew_manager_5']) ? $vRow['crew_manager_5'] : "N/A"; ?></td>
                                            <td><?php echo isset($vRow['overall_satisfaction_n']) ? $vRow['overall_satisfaction_n'] : "N/A"; ?></td>
                                            <td><?php echo isset($vRow['overall_satisfaction_5']) ? $vRow['overall_satisfaction_5'] : "N/A"; ?></td>
                                            <td><?php echo isset($vRow['speed_of_service_n']) ? $vRow['speed_of_service_n'] : "N/A"; ?></td>
                                            <td><?php echo isset($vRow['speed_of_service_5']) ? $vRow['speed_of_service_5'] : "N/A"; ?></td>
                                            <td><?php echo isset($vRow['taste_of_beverage_n']) ? $vRow['taste_of_beverage_n'] : "N/A"; ?></td>
                                            <td><?php echo isset($vRow['taste_of_beverage_5']) ? $vRow['taste_of_beverage_5'] : "N/A"; ?></td>
                                            <td><?php echo isset($vRow['taste_of_food_n']) ? $vRow['taste_of_food_n'] : "N/A"; ?></td>
                                            <td><?php echo isset($vRow['taste_of_food_5']) ? $vRow['taste_of_food_5'] : "N/A"; ?></td>
                                        </tr>
                                        <?php
                                    }
                                }else{
                                    ?>
                                        <tr><td colspan="14" align="center">No records found.</td></tr>
                                            <?php
                                }
                                ?>

                                <?php
                               
                            }
                            ?>

                            <?php
                        }else{
                            ?>
                                          <tr><td colspan="14" align="center">No records found.</td></tr>
                                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>  
    </div>
</div>
<!--delete project confirm Modal-->
<div class="modal fade" id="project_archived_confirm_Modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-label="Close" data-dismiss="modal" class="close" type="button"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title">Change Status of Project</h4>
            </div>
            <div class="modal-body">
                <p><b>WARNING:</b> Are you sure you want to move this project in Archived state?</p>
            </div>
            <div class="modal-footer">

                <input data-dismiss="modal" class="btn btn-danger pull-right ml10" type="button" id="record_changes" value="Cancel">
                <input class="btn btn-success pull-right" type="button" id="record_changes" value="Confirm" data-id="" data-status="" onclick="confirm_status(this);">
            </div>
        </div>
    </div>
</div>
<script>

    $(document).ready(function () {
        $("#end_date").datepicker({
            daysOfWeekDisabled: [0, 1, 2, 3, 4, 5],
            format: 'dd-mm-yyyy'
        });
    });
</script>