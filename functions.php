<?php
//! HOLIDAYS PAGE - Days of the year line graph
//* Number of weekdays
function countWeekendsInYear($year) {
    $weekendCount = 0;
    for ($month = 1; $month <= 12; $month++) {
        // Get the number of days in the month
        $daysInMonth = date('t', strtotime("$year-$month-01"));
        for ($day = 1; $day <= $daysInMonth; $day++) {
            $dayOfWeek = date('w', strtotime("$year-$month-$day"));

            // If it's a Saturday (6) or Sunday (0), increase the counter
            if ($dayOfWeek == 0 || $dayOfWeek == 6) {
                $weekendCount++;
            }
        }
    }
    return $weekendCount;
}
//*Checking for a leap year
function isLeapYear($year) {
    return (($year % 4 == 0) && ($year % 100 != 0)) || ($year % 400 == 0);
}
