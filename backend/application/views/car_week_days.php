<?php

?>
<div class="col-md-12">
    <div class="form-group">
        <div class="col-md-4">
            <label>&nbsp;
        </label>
        </div>
        <div class="col-md-2">
            &nbsp;
        </div>
        <div class="col-md-2">
            <label class="control-label text-center text-bold">
                Total no. of cars
            </label>
        </div>
        <div class="col-md-4">
            <label class="control-label text-center text-bold">
                Average time spend on each Car
            </label>
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
        <input type="hidden" name="day[]" value="monday"/>
        <?php echo form_label('Monday<span class="required" aria-required="true"> * </span>', 'sunday', array('class' => 'col-md-4 control-label')); ?>
        <div class="col-md-2">
            <?php
            echo form_input(array('name' => 'date[]', 'class' => 'form-control', 'readonly' => 'readonly', 'value' => isset($week_days['day1']) ? DB2Disp($week_days['day1']) : "N/A"));
            ?>
        </div>
        <div class="col-md-2">
            <?php
            $day1_no_of_cars = field(set_value('no_of_cars[]', NULL), (isset($day1_no_of_cars)) ? $day1_no_of_cars : NULL);
            echo form_input(array('name' => 'no_of_cars[]', 'class' => 'form-control integers', 'value' => $day1_no_of_cars));
            ?>
        </div>
        <div class="col-md-2">
            <?php
            $day1_avg_time = field(set_value('avg_time[]', NULL), (isset($day1_avg_time)) ? $day1_avg_time : NULL);
            echo form_input(array('name' => 'avg_time[]', 'class' => 'form-control integer', 'value' => $day1_avg_time));
            ?>
        </div> 

    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
        <input type="hidden" name="day[]" value="tuesday"/>
        <?php echo form_label('Tuesday<span class="required" aria-required="true"> * </span>', 'monday', array('class' => 'col-md-4 control-label')); ?>
       <div class="col-md-2">
            <?php
            echo form_input(array('name' => 'date[]', 'class' => 'form-control', 'readonly' => 'readonly', 'value' => isset($week_days['day2']) ? DB2Disp($week_days['day2']) : "N/A"));
            ?>
        </div>
        <div class="col-md-2">
            <?php
            $day2_no_of_cars = field(set_value('no_of_cars[]', NULL), (isset($day2_no_of_cars)) ? $day2_no_of_cars : NULL);
            echo form_input(array('name' => 'no_of_cars[]', 'class' => 'form-control integers', 'value' => $day2_no_of_cars));
            ?>
        </div>
        <div class="col-md-2">
            <?php
             $day2_avg_time = field(set_value('avg_time[]', NULL), (isset($day2_avg_time)) ? $day2_avg_time : NULL);
            echo form_input(array('name' => 'avg_time[]', 'class' => 'form-control integer', 'value' => $day2_avg_time));
            ?>
        </div> 
    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
        <input type="hidden" name="day[]" value="wednesday"/>
        <?php echo form_label('Wednesday<span class="required" aria-required="true"> * </span>', 'wednesday', array('class' => 'col-md-4 control-label')); ?>
        <div class="col-md-2">
            <?php
            echo form_input(array('name' => 'date[]', 'class' => 'form-control', 'readonly' => 'readonly', 'value' => isset($week_days['day3']) ? DB2Disp($week_days['day3']) : "N/A"));
            ?>
        </div>
        <div class="col-md-2">
            <?php
             $day3_no_of_cars = field(set_value('no_of_cars[]', NULL), (isset($day3_no_of_cars)) ? $day3_no_of_cars : NULL);
            echo form_input(array('name' => 'no_of_cars[]', 'class' => 'form-control integers', 'value' => $day3_no_of_cars));
            ?>
        </div>
        <div class="col-md-2">
            <?php
            $day3_avg_time = field(set_value('avg_time[]', NULL), (isset($day3_avg_time)) ? $day3_avg_time : NULL);
            echo form_input(array('name' => 'avg_time[]', 'class' => 'form-control integer', 'value' => $day3_avg_time));
            ?>
        </div> 
        </div>
    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
        <input type="hidden" name="day[]" value="thursday"/>
        <?php echo form_label('Thursday<span class="required" aria-required="true"> * </span>', 'thursday', array('class' => 'col-md-4 control-label')); ?>
      <div class="col-md-2">
            <?php
            echo form_input(array('name' => 'date[]', 'class' => 'form-control', 'readonly' => 'readonly', 'value' => isset($week_days['day4']) ? DB2Disp($week_days['day4']) : "N/A"));
            ?>
        </div>
        <div class="col-md-2">
            <?php
             $day4_no_of_cars = field(set_value('no_of_cars[]', NULL), (isset($day4_no_of_cars)) ? $day4_no_of_cars : NULL);
            echo form_input(array('name' => 'no_of_cars[]', 'class' => 'form-control integers', 'value' => $day4_no_of_cars));
            ?>
        </div>
        <div class="col-md-2">
            <?php
            $day4_avg_time = field(set_value('avg_time[]', NULL), (isset($day4_avg_time)) ? $day4_avg_time : NULL);
            echo form_input(array('name' => 'avg_time[]', 'class' => 'form-control integer', 'value' => $day4_avg_time));
            ?>
        </div> 
       
    </div>
</div>
<div class="col-md-12">
    <div class="form-group">
        <input type="hidden" name="day[]" value="friday"/>
        <?php echo form_label('Friday<span class="required" aria-required="true"> * </span>', 'friday', array('class' => 'col-md-4 control-label')); ?>
        <div class="col-md-2">
            <?php
            echo form_input(array('name' => 'date[]', 'class' => 'form-control', 'readonly' => 'readonly', 'value' => isset($week_days['day5']) ? DB2Disp($week_days['day5']) : "N/A"));
            ?>
        </div>
        <div class="col-md-2">
            <?php
             $day5_no_of_cars = field(set_value('no_of_cars[]', NULL), (isset($day5_no_of_cars)) ? $day5_no_of_cars : NULL);
            echo form_input(array('name' => 'no_of_cars[]', 'class' => 'form-control integers', 'value' => $day5_no_of_cars));
            ?>
        </div>
        <div class="col-md-2">
            <?php
            $day5_avg_time = field(set_value('avg_time[]', NULL), (isset($day5_avg_time)) ? $day5_avg_time : NULL);
            echo form_input(array('name' => 'avg_time[]', 'class' => 'form-control integer', 'value' => $day5_avg_time));
            ?>
        </div> 
    </div>
</div>