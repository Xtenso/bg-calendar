<!--header-->
<?php
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$pageTitle = "Почивни дни $year";
$additionalStyling = 'holidays';
$additionalStyling2 = 'calendar';
$currentURL = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$metaDescription = "Всички официални празници, почивни и неучебни дни в България през $year, събрани на едно място.";
include 'header.php';
?>

<!--Page title and description-->
<div class="pageDescription">
	<h1>Официални празници и почивни дни през <?php echo $year; ?></h1>
	<p>Всички официални празници, почивни и неучебни дни в България през <?php echo $year; ?>, събрани на едно място.</p>
</div>

<!--Days of the year graph-->
<?php
//number of weekdays
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
$totalWeekends = countWeekendsInYear($year);

// Number of official holidays which are not during the weekend and don't overlap with other holidays
$officialHolidaysOff = 0;
//School days off (not including weekends or official holidays)
$schoolHolidaysOff = 0;
// Construct the SQL query
$query = "SELECT `date`, `end_date`, stays_same, type FROM holidays 
			WHERE (`stays_same` = 'false' AND YEAR(date) = '$year') 
			OR (`stays_same` = 'true')
			OR (
				(`stays_same` = 'false')
				AND (
					(YEAR(date) < '$year' AND YEAR(end_date) = '$year')  -- Start date in previous year, end date in current year
					OR (YEAR(date) = '$year' AND YEAR(end_date) > '$year')  -- Start date in current year, end date in future year
				)
			)";
// Execute the query
$result = mysqli_query($conn, $query);
// Array to store each unique holiday date
$uniqueDates = [];
// Fetch each row
while ($row = mysqli_fetch_assoc($result)) {
	// Modify the date to use the year from $year and not the year from the db for events that stay the same
	if ($row['stays_same'] == 'true' && date('Y', strtotime($row['date'])) == date('Y', strtotime($row['end_date']))) {
		$startDate = strtotime(date("{$year}-m-d", strtotime($row['date'])));
		$endDate = strtotime(date("{$year}-m-d", strtotime($row['end_date'])));
	} elseif ($row['stays_same'] == 'true' && (date('Y', strtotime($row['date'])) + 1) == date('Y', strtotime($row['end_date']))) {
		$endYear = $year + 1;
		$startDate = strtotime(date("{$year}-m-d", strtotime($row['date'])));
		$endDate = strtotime(date("{$endYear}-m-d", strtotime($row['end_date'])));
	} elseif ($row['stays_same'] == 'true' && $row['end_date'] == null) {
		$startDate = strtotime(date("{$year}-m-d", strtotime($row['date'])));
		$endDate = strtotime($row['end_date']);
	} else {
		$startDate = strtotime($row['date']);
		$endDate = strtotime($row['end_date']);
	}
	if ($endDate == null || $endDate === '0000-00-00') {
		$endDate = $startDate;
	}
	// Iterate over each day from start date to end date
	$dateCycle = $startDate;
	while ($dateCycle <= $endDate) {
		// Check if the date is within the current year
		if (date('Y', $dateCycle) == $year) {
			$modifiedDate = date('Y-m-d', $dateCycle);
			// If the date is not already in our array, process it
			if (!in_array($modifiedDate, $uniqueDates)) {
				$dayOfWeek = date('w', $dateCycle);
				// Check if the day isn't a Saturday (6) or Sunday (0)
				if ($dayOfWeek != 0 && $dayOfWeek != 6) {
					if ($row['type'] == 'school') {
						$schoolHolidaysOff++;
					} elseif ($row['type'] == 'official') {
						$officialHolidaysOff++;
					}
				}
				if ($row['type'] == 'official') {
					$uniqueDates[] = $modifiedDate; //To make sure that the counter for school holidays doesn't take into account the official holidays
				}
			}
		}
		// Move to the next day
		$dateCycle = strtotime('+1 day', $dateCycle);
	}
}
unset($uniqueDates, $endDate, $startDate);

//Checking for a leap year
function isLeapYear($year) {
	return (($year % 4 == 0) && ($year % 100 != 0)) || ($year % 400 == 0);
}

//total number of days off
$totalDaysOff = $totalWeekends + $officialHolidaysOff;

//Working days
$totalDays = (isLeapYear($year)) ? 366 : 365;
$workingDays = $totalDays - $totalWeekends - $officialHolidaysOff;

?>
<div id="barContainer">
	<p>
		<?php
		echo $year;
		if ($totalDays == 366) {
			echo " е";
		} else {
			echo " не е";
		}
		?>
		високосна година и има <?php echo $totalDays; ?> дни
	</p>
	<div id="bar">
		<span style="width: calc(<?php echo ($workingDays / $totalDays) * 100; ?>% + 10px); background-color: white;"></span>
		<span style="width: calc(<?php echo ($totalWeekends / $totalDays) * 100; ?>% + 10px); background-color: green"></span>
		<span style="width: calc(<?php echo ($officialHolidaysOff / $totalDays) * 100; ?>% + 10px); background-color: #B30000"></span>
	</div>
	<div id="barDescription">
		<span>Работни дни: <?php echo $workingDays; ?></span>
		<span style="color: #52A447;">Уикенди: <?php echo $totalWeekends; ?></span>
		<span style="color: #B30000">Почивни дни: <?php echo $officialHolidaysOff; ?></span>
		<span style="color: #E6AD00">Неучебни дни: <?php echo $schoolHolidaysOff; ?><img id="warningSign" src="img/warning.png" alt="Warning sign" title="Не включва официални празници, уикенди, както и ваканции от следващата учебна година.">
		</span>
	</div>
</div>

<!--Filters-->
<div id="filters">
	<!--Year-->
	<label for="year">Година:</label>
	<div class="custom-select">
		<select name="year" id="year" onchange="changeYear(this)">
			<?php
			$currentYear = date('Y');
			for ($i = 0; $i < 5; $i++) :
			?>
				<option value="<?= $currentYear + $i ?>" <?= (isset($year) && $year == $currentYear + $i) ? 'selected' : '' ?>>
					<?= $currentYear + $i ?>
				</option>
			<?php endfor; ?>
		</select>
	</div>
	<!--Holiday type-->
	<label class="custom-checkbox"><input type="checkbox" id="officialCheckbox" checked onchange="filterHolidays()"><span class="checkMark" id="officialCheckboxSpan">Официални празници и почивни дни</span></label>
	<label class="custom-checkbox"><input type="checkbox" id="schoolCheckbox" checked onchange="filterHolidays()"><span class="checkMark" id="schoolCheckboxSpan">Неучебни дни</span></label>
</div>

<!--Main Calendar container-->
<div class="calendars-container">
	<?php
	$months = [
		'Януари',
		'Февруари',
		'Март',
		'Април',
		'Май',
		'Юни',
		'Юли',
		'Август',
		'Септември',
		'Октомври',
		'Ноември',
		'Декември'
	];
	$days = ['П', 'В', 'С', 'Ч', 'П', 'С', 'Н'];

	//the year is at the top of the page for integration in other elements of the website

	// Include the holidays array and sql query
	include 'holidaysArray.php';

	for ($m = 0; $m < 12; $m++) {
		// Determine the number of days in the month
		$daysInMonth = date('t', strtotime("$year-" . ($m + 1) . "-01"));

		// Find out on which day of the week the month starts
		$startDayOfWeek = date('w', strtotime("$year-" . ($m + 1) . "-01"));
		if ($startDayOfWeek == 0) {
			$startDayOfWeek = 7;
		}

		// Determine the day of the week the current month ends
		$endDayOfWeek = date('w', strtotime("$year-" . ($m + 1) . "-$daysInMonth"));

		echo '<div class="month">
		<div class="monthHeader">
		<h2>' . $months[$m] . '</h2>
		</div>
		<div class="days">';

		foreach ($days as $index => $day) {
			$style = ($index >= count($days) - 2) ? ' style="color: #e25825;"' : '';  // check if it's one of the last two days
			echo '<div class="day"' . $style . '>' . $day . '</div>';
		}

		echo '</div>
<div class="dates">';

		// Output the "past" days from the previous month
		for ($d = date('t', strtotime("$year-" . $m . "-01")) - $startDayOfWeek + 2; $d <= date('t', strtotime("$year-" . $m . "-01")); $d++) {
			echo '<div class="past date">' . $d . '</div>';
		}

		// Output the days of the current month with the appropriate colors
		for ($d = 1; $d <= $daysInMonth; $d++) {
			$currentDateKey = ($m + 1) . '-' . $d;
			$styleAttributes = '';
			$titleAttributes = '';
			$class = 'date';  // default class
			$currentDate = date('Y-m-d', strtotime("$year-" . ($m + 1) . "-$d"));
			$currentDayOfWeek = date('N', strtotime($currentDate));

			// Check if the current day is a weekend day (Saturday or Sunday)
			if ($currentDayOfWeek == 6 || $currentDayOfWeek == 7) {
				$dayColor = 'color: var(--orange); font-weight: 900'; // Apply color for weekend days
			} else {
				$dayColor = ''; // Default style for weekdays
			}

			// Check if the current day is today
			if ($currentDate == date('Y-m-d')) {
				$borderColor = 'var(--orange)'; // Apply border for the current day
				$dayColor = 'color: var(--orange); font-weight: 900';
			} else {
				$borderColor = ''; // No border for other days
			}

			if (isset($holidays[$currentDateKey])) {
				$holiday = $holidays[$currentDateKey];
				// Set background color and class based on the holiday type
				if ($holiday['type'] === 'school') {
					$backgroundColor = '#E6AD00';
					$class .= ' school';
				} else {
					$backgroundColor = '#B30000';
					$class .= ' official';
				}
				$dayColor = 'color: var(--white); font-weight: 900';
				// Set the title for the holiday
				$titleAttributes = ' title="' . htmlspecialchars($holiday['name']) . '"';
			} else {
				$backgroundColor = '';
			}

			$styleAttributes = ' style="background-color: ' . $backgroundColor . '; border-color: ' . $borderColor . '; ' . $dayColor . ';"';

			// Print the date with the generated attributes
			echo '<div class="' . $class . '"' . $styleAttributes . $titleAttributes . '>' . $d . '</div>';
		}

		// Output the "next" days from the next month
		// Calculate total cells occupied after adding days from the current month
		$totalCells = $daysInMonth + $startDayOfWeek - 1;

		// Calculate how many cells to fill for 6 weeks (42 days)
		if ($totalCells <= 35) {
			$cellsToFill = 35 - $totalCells;
		} else {
			$cellsToFill = 42 - $totalCells;
		}

		// If the month's days already fill up 6 weeks, no additional days from the next month are needed
		if ($cellsToFill > 0) {
			for ($d = 1; $d <= $cellsToFill; $d++) {
				echo '<div class="past date">' . $d . '</div>';
			}
		}

		echo '</div>
</div>';
	}

	?>
</div>

<!--Custom accordions-->
<div id="accordionContainer">
	<div id="holidayAccordion" class="accordion-item">
		<button class="accordion-button" onclick="toggleAccordion('firstAccordion')">
			Почивни дни
		</button>
		<div id="firstAccordion" class="accordion-content">
			<div id="holidayAccordion" class="accordion-body">
				<ul>
					<?php
					// Iterate over the holidays array and print official holidays matching the specified year
					foreach ($holidaysAccordion as $date => $holiday) {
						// Check if the holiday type is "official"
						if ($holiday['type'] === 'official') {
							// Handles the case when the holiday spans multiple days
							echo '<li><script>document.write(toBulgarianDateFormat("' .  $date . '"));';
							if ($holiday['end_date'] != null) {
								echo ' document.write(" / " + toBulgarianDateFormat("' . $holiday['end_date'] . '"));';
							}
							echo '</script> - ' . $holiday['name'] . '</li>';
						}
					}
					?>
				</ul>
			</div>
		</div>
	</div>
	<div id="schoolAccordion" class="accordion-item">
		<button class="accordion-button" onclick="toggleAccordion('secondAccordion')">
			Неучебни дни и Ваканции
		</button>
		<div id="secondAccordion" class="accordion-content">
			<div id="schoolAccordion" class="accordion-body">
				<ul>
					<?php
					// Iterate over the holidays array and print official holidays matching the specified year
					foreach ($holidaysAccordion as $date => $holiday) {
						// Check if the holiday type is "official"
						if ($holiday['type'] === 'school') {
							// Handles the case when the holiday spans multiple days
							echo '<li><script>document.write(toBulgarianDateFormat("' .  $date . '"));';
							if ($holiday['end_date'] != null) {
								echo ' document.write(" / " + toBulgarianDateFormat("' . $holiday['end_date'] . '"));';
							}
							echo '</script> - ' . $holiday['name'] . '</li>';
						}
					}
					?>
				</ul>
			</div>
		</div>
	</div>
</div>

<!--footer-->
<?php
include 'footer.php';
?>