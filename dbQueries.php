<?php
$currentDate = date('Y-m-d');
//! Database query for name days, with date as the key
$query = "SELECT name, date, list_names, stays_same 
            FROM name_days 
            WHERE stays_same = 'true'
            OR (YEAR(date) >= YEAR('$currentDate') AND stays_same = 'false') ";
$result = $conn->query($query);
// Fetch all rows into $allNameDays
$allNameDays = $result->fetch_all(MYSQLI_ASSOC);
$result->data_seek(0); // Reset the internal pointer of the result set to the beginning
// Array with a date key, for when we need to access the data about a specific date
$dateKeyNameDays = [];
while ($row = $result->fetch_assoc()) {
    //Remove the year from the date as we no longer need to check if it stays the same, since we did that we did that with the query
    $dateWithoutYear = date('m-d', strtotime($row['date']));
    $dateKeyNameDays[$dateWithoutYear][] = $row;
    //Array within an array because there can be multiple name days on the same date
}

//! Database query for holidays, with date as the key
$query = "SELECT name, date, type, stays_same 
            FROM holidays
            WHERE stays_same = 'true'
            OR (YEAR(date) >= YEAR('$currentDate') AND stays_same = 'false') ";
$result = $conn->query($query);
// Fetch all rows into $allNameDays
$allHolidays = $result->fetch_all(MYSQLI_ASSOC);
$result->data_seek(0);
// Array with a date key, for when we need to access the data about a specific date
$dateKeyHolidays = [];
while ($row = $result->fetch_assoc()) {
    $dateWithoutYear = date('m-d', strtotime($row['date']));
    $dateKeyHolidays[$dateWithoutYear][] = $row;
}

//! Available arrays:
//? $allNameDays - all name days, $dateKeyNameDays - name days with date as the key
//? $allHolidays - all holidays, $dateKeyHolidays - holidays with date as the key

//TODO: FOR FUTURE DEVELOPMENT AND INTEGRATION, combining all queries in a single file to minimise the number of contact with the database and improve performance

//! Example ways to access the date from the above arrays
if (isset($dateKeyNameDays['03-23'])) {
    foreach ($dateKeyNameDays['03-23'] as $nameDay) {
        echo "<p>Имени дни: " . $nameDay['name'] . "</p>";
        echo $nameDay['date'];
        echo $nameDay['stays_same'];
    }
    echo sizeof($dateKeyNameDays);
    echo sizeof($dateKeyNameDays['03-23']);
} else {
    echo "Not set.";
}
echo sizeof($allNameDays);

if (isset($allNameDays)) {
    foreach ($allNameDays as $nameDay) {
        if ($nameDay['name'] == 'Стефановден') {
            echo $nameDay['name'];
            echo $nameDay['date'];
        }
    }
} else {
    echo "No data available for name days.";
}

if (isset($allHolidays['02-04'])) {
    $today = $allHolidays['02-04'];
    echo $today['name'];
    echo $today['type'];
    echo sizeof($allHolidays);
} else {
    echo "No data available for holidays today.";
    if (empty($allHolidays)) {
        echo "No data available for holidays.";
    }
}
