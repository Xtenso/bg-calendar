<?php
//!This file contains the holidays[] array and database query for use in holidays.php and index.php to avoid duplicate code.
//database holidays query
$holidays = [];
$holidaysAccordion = [];
$query = "SELECT name, date, end_date, type, stays_same, description FROM holidays
            WHERE stays_same = 'true'
            OR (
            CASE 
                WHEN end_date IS NULL THEN (stays_same = 'false' AND YEAR(date) = $year)
                ELSE (stays_same = 'false' AND YEAR(date) <= $year AND $year <= YEAR(end_date))
            END
            )
            ORDER BY DATE_FORMAT(date, '%m%d')";
$result = mysqli_query($conn, $query);
while ($row = mysqli_fetch_assoc($result)) {
    //Populate $holidaysAccordion array for the accordion
    $formattedDateKey = date('F j', strtotime($row['date'])); // Format: Month Day
    $holidaysAccordion[$formattedDateKey] = [
        'name' => $row['name'],
        'date' => $row['date'],
        'end_date' => $row['end_date'],
        'type' => $row['type'],
        'stays_same' => $row['stays_same'],
        'description' => $row['description'],
    ];

    if (empty($row['end_date'])) {
        $formattedStartDate = date('n-j', strtotime($row['date']));
        $holidays[$formattedStartDate] = ['name' => $row['name'], 'type' => $row['type']];
    } else {
        if ($row['stays_same'] === 'true') {
            //! If a holiday is between years and repeats yearly
            if (date('Y', strtotime($row['date'])) != date('Y', strtotime($row['end_date']))) {
                //! For the part in the current year
                $startDate = $year . '-01-01'; // January 1st of the current year
                $endDate = $row['end_date']; // End date of the event
                $dateCycle = $startDate;
                // Increment date by one day until it reaches or exceeds the end date
                while (strtotime($dateCycle) <= strtotime($endDate)) {
                    // Add the holiday for the current date without the year
                    $holidays[date('n-j', strtotime($dateCycle))] = ['name' => $row['name'], 'type' => $row['type']];
                    // Increment dateCycle by one day
                    $dateCycle = date('Y-m-d', strtotime($dateCycle . ' +1 day'));
                }
                //! For the part in the previous year
                $startDate = $year - 1 . substr($row['date'], 4); // Use $year - 1 for the previous year
                $endDate = $year - 1 . '-12-31'; // December 31st of the previous year
                $dateCycle = $startDate;
                // Increment date by one day until it reaches or exceeds the end date
                while (strtotime($dateCycle) <= strtotime($endDate)) {
                    // Add the holiday for the current date without the year
                    $holidays[date('n-j', strtotime($dateCycle))] = ['name' => $row['name'], 'type' => $row['type']];
                    // Increment dateCycle by one day
                    $dateCycle = date('Y-m-d', strtotime($dateCycle . ' +1 day'));
                }
            } else {
                //!If the holiday is multiple days and repeats yearly, change the year with $year
                $startDate = $year . substr($row['date'], 4);
                $endDate = $row['end_year'] . substr($row['end_date'], 4);
            }
        } else /*($row['stays_same'] === 'false')*/ {
            if (date('Y', strtotime($row['date'])) != date('Y', strtotime($row['end_date']))) {
                // Event spans across multiple years
                if (date('Y', strtotime($row['date'])) == $year) {
                    // Event starts in the current $year
                    $startDate = $row['date'];
                    $endDate = date('Y-m-d', strtotime('12/31/' . $year)); // Last day of the current year
                } elseif (date('Y', strtotime($row['end_date'])) == $year) {
                    // Event ends in the current $year
                    $startDate = date('Y-m-d', strtotime('1/1/' . $year)); // First day of the current year
                    $endDate = $row['end_date'];
                } else {
                    echo 'Error: Event spans across multiple years. Error in db. Please contact support with error code 100.';
                }
            } else {
                $startDate = $row['date'];
                $endDate = $row['end_date'];
            }
        }
        //echo $startDate . ' ' . $endDate . $row['name'] . '<br>';
        // Include all dates from start_date to end_date for multi-day holidays
        $dateCycle = $startDate;
        // Increment date by one day until it reaches or exceeds the end date
        while (strtotime($dateCycle) <= strtotime($endDate)) {
            // Add the holiday for the current date without the year; check if the date is already in the array, and don't overwrite official holidays
            $dateKey = date('n-j', strtotime($dateCycle));
            if (array_key_exists($dateKey, $holidays)) {
                // Date key already exists
                if ($holidays[$dateKey]['type'] === 'school' && $row['type'] === 'official') {
                    // If the existing holiday type is 'school' and the new one is 'official', update the type to 'official'
                    $holidays[$dateKey]['type'] = 'official';
                }
                // If the existing holiday type is 'official' and the new one is 'school', do nothing (don't overwrite)
            } else {
                // Date key doesn't exist, add the new holiday
                $holidays[$dateKey] = ['name' => $row['name'], 'type' => $row['type']];
            }

            // Increment dateCycle by one day
            $dateCycle = date('Y-m-d', strtotime($dateCycle . ' +1 day'));
        }
    }
}
