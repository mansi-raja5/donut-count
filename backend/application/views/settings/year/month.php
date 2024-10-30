<?php
$min_year = 2018;
$max_year = 2025;
$total_month_weeks = 0;
?>
<input type="hidden" name="weeks" id="weeks_hidden" value='<?php echo isset($weeks) ? json_encode($weeks) : ''; ?>'>
<div class="form-group">
    <?php echo form_label('Year Starting Date<span class="required" aria-required="true"> * </span>', 'year', array('class' => 'col-md-4 control-label')); ?>
    <div class="col-md-4">
    <?php
    $default_year_starting_date = isset($default_year_starting_date) ? date("m/d/Y", strtotime($default_year_starting_date)) : NULL;
    $year_starting_date = field(set_value('year_starting_date', $default_year_starting_date), (isset($year_starting_date)) ? date("m/d/Y", strtotime($year_starting_date)) : $default_year_starting_date);
    echo form_input(array('required' => 'required', 'id' => 'year_starting_date', 'name' => 'year_starting_date', 'class' => 'form-control', 'value' => $year_starting_date));
    ?>
    </div>
</div>
<?php $year_weeks = $year_weeks_post ? $year_weeks_post : ($year_weeks ?? null); ?>
<div class="form-group">
    <?php echo form_label('No. of Weeks in Year<span class="required" aria-required="true"> * </span>', 'weeknumber', array('class' => 'col-md-4 control-label')); ?>
    <div class="col-md-4">
        <label class="mt-radio">
            <input type="radio" name="yearweeks" value="52" required="required" onclick="getMonthsForTheYear(undefined, 52);" <?php echo (isset($year_weeks) && $year_weeks == 52) ? 'checked' : ''; ?>>52
            <span></span>
        </label>
        <label class="mt-radio">
            <input type="radio" name="yearweeks" value="53" required="required" onclick="getMonthsForTheYear(undefined, 53);" <?php echo (isset($year_weeks) && $year_weeks == 53) ? 'checked' : ''; ?>>53
            <span></span>
        </label>
    </div>
</div>
<?php if (($year_weeks ?? null) == 52 || ($year_weeks ?? null) == 53) : ?> 
<div class="form-group">
    <?php $label = $year_weeks == 52 ? 'Which week need to clone?' : 'Which week need to remove?'; ?>
    <?php echo form_label($label . '<span class="required" aria-required="true"> * </span>', 'week_position', array('class' => 'col-md-4 control-label')); ?>
    <div class="col-md-4">
        <label class="mt-radio">
            <input type="radio" name="week_position" value="1" required="required" <?php echo (isset($week_position) && $week_position == 1) ? 'checked' : ''; ?>>1
            <span></span>
        </label>
        <label class="mt-radio">
            <input type="radio" name="week_position" value="<?php echo $year_weeks; ?>" required="required" <?php echo (isset($week_position) && $week_position == $year_weeks) ? 'checked' : ''; ?>><?php echo $year_weeks; ?>
            <span></span>
        </label>
    </div>
</div>
<?php if (($year_weeks ?? null) == 52) : ?> 
<div class="form-group">
    <?php echo form_label('where you want to add clone week?<span class="required" aria-required="true"> * </span>', 'is_shifted', array('class' => 'col-md-4 control-label')); ?>
    <div class="col-md-4">
        <label class="mt-radio">
            <input type="radio" name="is_shifted" value="1" required="required" <?php echo (isset($is_shifted) && $is_shifted == 1) ? 'checked' : ''; ?>>First
            <span></span>
        </label>
        <label class="mt-radio">
            <input type="radio" name="is_shifted" value="0" required="required" <?php echo (isset($is_shifted) && $is_shifted == 0) ? 'checked' : ''; ?>>Last
            <span></span>
        </label>
    </div>
</div>
<?php endif; ?>
<?php endif; ?>

<?php
for ($monthCount=1; $monthCount <= 12 ; $monthCount++) {
    $total_month_weeks += isset($year[$monthCount]->month_weeks) ? $year[$monthCount]->month_weeks : 0;
?>
    <div class="form-group">
        <?php echo form_label('No. of Weeks in '.monthname($monthCount).' <span class="required" aria-required="true"> * </span>', 'weeknumber', array('class' => 'col-md-4 control-label')); ?>
        <?php
        $class = "";
        if(in_array($monthCount, $ledger_month)){
            $class = " locked ";
        }
        ?>
        <div class="col-md-4 <?php echo $class; ?>">
            <label class="mt-radio"><input type="radio" name="monthweeks[<?php echo $monthCount; ?>]" value="4" <?php echo (isset($year[$monthCount]) && $year[$monthCount]->month_weeks == 4) ? 'checked' : ''; ?> onclick="changeMonthWeek()">4<span></span></label>
            <label class="mt-radio"><input type="radio" name="monthweeks[<?php echo $monthCount; ?>]" value="5" <?php echo (isset($year[$monthCount]) && $year[$monthCount]->month_weeks == 5) ? 'checked' : ''; ?> onclick="changeMonthWeek()">5<span></span></label>&nbsp;&nbsp;&nbsp;&nbsp;
            <div id="weeklist_<?php echo $monthCount; ?>">
                <?php
                if(isset($weeks[$monthCount]))
                {
                    foreach ($weeks[$monthCount] as $_weeks) {
                        echo '<div style="margin:5px"><span class="label label-sm label-success">'.date('F d Y',strtotime($_weeks->start_of_week)).'</span>'.' - '.'<span class="label label-sm label-success">'.date('F d Y',strtotime($_weeks->end_of_week)).'</span></div>';
                    }
                }
                ?>
            </div>
        </div>
    </div>
<?php
}
?>
<div class="form-group" style="color: red">
    <?php echo form_label('Total Month Weeks:', 'total month weeks', array('class' => 'col-md-4 control-label')); ?>
    <div id="total_month_weeks">
    <?php echo form_label($total_month_weeks, 'total month weeks', array('class' => 'control-label')); ?>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#year_starting_date").datepicker({
            daysOfWeekDisabled: [1, 2, 3, 4, 5,6],
            format: 'mm/dd/yyyy',
            yearRange: "2020",
            autoclose: true,
        }).on('changeDate', function (e) {
        });
    });
