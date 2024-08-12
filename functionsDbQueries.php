<?php
//! Here can be found all reusable db queries
include 'objects.php';

//? Method to fetch all holidays for a specific year and return them as an object array
function getHolidaysByYear($conn, $year) {
    $query = "SELECT `name`, `description`, `date`, `end_date`, `type`, `stays_same` 
              FROM holidays 
              WHERE (`stays_same` = 'false' AND YEAR(date) = '$year') 
              OR (`stays_same` = 'true')
              OR (
                  (`stays_same` = 'false')
                  AND (
                      (YEAR(date) < '$year' AND YEAR(end_date) = '$year') 
                      OR (YEAR(date) = '$year' AND YEAR(end_date) > '$year') 
                  )
              )";
    $result = mysqli_query($conn, $query);
    $holidays = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $holiday = new Holiday(
            $row['name'],
            $row['description'],
            $row['date'],
            $row['end_date'],
            $row['type'],
            $row['stays_same']
        );
        $holidays[] = $holiday;
    }
    return $holidays;
}

//? Method to fetch all name days for a specific year
function getNameDaysByYear($conn, $year) {
    $query = "SELECT `name`, `date`, `list_names`, `stays_same` 
                  FROM name_days 
                  WHERE (`stays_same` = 'false' AND YEAR(date) = '$year') 
                  OR (`stays_same` = 'true')";
    $result = mysqli_query($conn, $query);
    $nameDays = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $nameDay = new NameDay(
            $row['name'],
            $row['date'],
            explode(', ', $row['list_names']), //turns the names from a string into an array of names
            $row['stays_same']
        );
        $nameDays[] = $nameDay;
    }
    return $nameDays;
}
