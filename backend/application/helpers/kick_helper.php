<?php
function getDateInfo($date)
{
    $date = date('Y-m-d', strtotime($date));
    $date = new DateTime($date);
    $data['date'] = $date->format("Y-m-d");
    $data['year'] = $date->format("Y");
    $data['month'] = $date->format("m");
    $data['week_number_of_month'] = weekOfMonth($data['date']);
    $data['week_number_of_year'] = $date->format("W");
    $data['day_number_of_week'] = $date->format("w") + 1; //NEED TO CHANGE THIS LATAR
    $start_of_week = clone $date->setISODate($data['year'], $data['week_number_of_year'], 0);
    $data['start_of_week'] = $start_of_week->format('Y-m-d');
    $end_of_week = clone $date->setISODate($data['year'], $data['week_number_of_year'], 6);
    $data['end_of_week'] = $end_of_week->format('Y-m-d');
    return $data;
}

function weekOfMonth($date)
{
    $firstOfMonth = date("Y-m-01", strtotime($date));
    return intval(date("W", strtotime($date))) - intval(date("W", strtotime($firstOfMonth)));
}

function working_days($date)
{
    $date = date('Y-m-d', strtotime($date));
    $date = new DateTime($date);
    $data['date'] = $date->format("Y-m-d");
    $data['year'] = $date->format("Y");
    $data['week_number_of_year'] = $date->format("W");
    $data['day_number_of_week'] = $date->format("w") + 1; //NEED TO CHANGE THIS LATAR
    $start_day = clone $date->setISODate($data['year'], $data['week_number_of_year'], 0);
    $data['day0'] = $start_day->format('Y-m-d');
    $end_day = clone $date->setISODate($data['year'], $data['week_number_of_year'], 6);
    $data['day1'] = date('Y-m-d', strtotime('+1 day', strtotime($data['day0'])));
    $data['day2'] = date('Y-m-d', strtotime('+2 day', strtotime($data['day0'])));
    $data['day3'] = date('Y-m-d', strtotime('+3 day', strtotime($data['day0'])));
    $data['day4'] = date('Y-m-d', strtotime('+4 day', strtotime($data['day0'])));
    $data['day5'] = date('Y-m-d', strtotime('+5 day', strtotime($data['day0'])));
    $data['day6'] = $end_day->format('Y-m-d');
    return $data;
}
function week_days($date)
{
    $date = date('Y-m-d', strtotime($date));
    $data['day0'] = date('Y-m-d', strtotime($date));
    $data['day1'] = date('Y-m-d', strtotime('+1 day', strtotime($date)));
    $data['day2'] = date('Y-m-d', strtotime('+2 day', strtotime($date)));
    $data['day3'] = date('Y-m-d', strtotime('+3 day', strtotime($date)));
    $data['day4'] = date('Y-m-d', strtotime('+4 day', strtotime($date)));
    $data['day5'] = date('Y-m-d', strtotime('+5 day', strtotime($date)));
    $data['day6'] = date('Y-m-d', strtotime('+6 day', strtotime($date)));
    return $data;
}

function getPreviousYearSameDay($dayNumber, $weekNumber, $year)
{
    $date = new DateTime();
    $date->setISODate($year, $weekNumber, ($dayNumber - 1));
    return $date->format('Y-m-d');
}

function getWeekNumberOfMonth($date)
{
    return 0;
}

function showInPercentage($amt)
{
    return number_format((float) $amt, 2, '.', '') . '%';
}

function showInDollar($amt)
{
    return '$' . number_format((float) $amt, 2, '.', '');
}

function getWeekStartingEndingDateFromMonth($monthStartDate, $type = "month", $is_previous = 0, $is_report = 0)
{
    $date = date('Y-m-d', strtotime($monthStartDate));
//    echo $date;
    //    exit;
    $date = new DateTime($date);
    $week_number_of_year = (int) $date->format("W");
    $month = $date->format("m");
    $year = $date->format("Y");
    $weekData = [];
    $limit = 53;
    if ($type == 'month') {
        $limit = 6;
    }
    if ($week_number_of_year >= 52 && $is_report == 0) {
        $week_number_of_year = 0;
    }
    for ($i = 0; $i < $limit; $i++) {
        $start_of_week = clone $date->setISODate($year, $week_number_of_year, 0);
        $end_of_week = clone $date->setISODate($year, $week_number_of_year, 6);

        if ($type == 'month' && ($month == $start_of_week->format('m') || $month == $end_of_week->format('m'))
            || ($type != 'month' && ($year == $start_of_week->format('Y') || $year == $end_of_week->format('Y')))) {
//            $weekData[$week_number_of_year]['week_number']   = $week_number_of_year < 10 ? "0".$week_number_of_year : $week_number_of_year;
            $weekData[$week_number_of_year]['week_number'] = $week_number_of_year;
            $weekData[$week_number_of_year]['start_of_week'] = $start_of_week->format('Y-m-d');
            $weekData[$week_number_of_year]['end_of_week'] = $end_of_week->format('Y-m-d');
        }
        if ($is_previous == 1) {
            $week_number_of_year--;
        } else {
            $week_number_of_year++;
        }

    }
    return $weekData;
}

function getyearlymonthstartenddate($year_starting_date, $no_of_weeks_in_year)
{
    $startdate = date('Y-m-d', strtotime($year_starting_date));
    $weekData = [];
    for ($i = 1; $i <= $no_of_weeks_in_year; $i++) {
        $enddate = date('Y-m-d', strtotime(date("Y-m-d", strtotime($startdate)) . " +6 day"));
        $weekData[$i]['start_of_week'] = $startdate;
        $weekData[$i]['end_of_week'] = $enddate;
        $startdate = date('Y-m-d', strtotime(date("Y-m-d", strtotime($enddate)) . " +1 day"));
    }
    return $weekData;
}

function showInDateFormat($date)
{
    return date('m/d/y', strtotime($date));
}

function showInDateFormatWithMonthName($date)
{
    return date('d M', strtotime($date));
}

function dateDiffInDays($date1, $date2)
{
    // Calculating the difference in timestamps
    $diff = strtotime($date2) - strtotime($date1);

    // 1 day = 24 hours
    // 24 * 60 * 60 = 86400 seconds
    return abs(round($diff / 86400));
}

function getAllWeekDatesByWeekEndingDate($weekEndingDate)
{
    $weekDates = [];
    for ($dateCount = 6; $dateCount > 0; $dateCount--) {
        $weekDates[] = date('Y-m-d', strtotime($weekEndingDate . ' -' . $dateCount . ' day'));
    }
    $weekDates[] = date('Y-m-d', strtotime($weekEndingDate));
    return $weekDates;
}

/**
 * Find the differences between 2 objects using Reflection.
 *
 * @param $o1
 * @param $o2
 * @return array Properties that have changed
 * @throws InvalidArgumentException
 */
function diff($o1, $o2)
{
    if (!is_object($o1) || !is_object($o2)) {
        throw new InvalidArgumentException("Parameters should be of object type!");
    }

    $diff = [];
    if (get_class($o1) == get_class($o2)) {
        $o1Properties = (new ReflectionObject($o1))->getProperties();
        $o2Reflected = new ReflectionObject($o2);

        foreach ($o1Properties as $o1Property) {
            $o2Property = $o2Reflected->getProperty($o1Property->getName());
            // Mark private properties as accessible only for reflected class
            $o1Property->setAccessible(true);
            $o2Property->setAccessible(true);
            if (($oldValue = $o1Property->getValue($o1)) != ($newValue = $o2Property->getValue($o2))) {
                $diff[$o1Property->getName()] = [
                    'old_value' => $oldValue,
                    'new_value' => $newValue,
                ];
            }
        }
    }

    return $diff;
}
