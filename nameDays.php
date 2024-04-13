<!--header-->
<?php
if (isset($_GET['year'])) {
	$year = $_GET['year'];
} else {
	$year = date('Y'); //It sets the year to the current one
}
$pageTitle = "Именни дни";
$additionalStyling = 'nameDays';
$additionalStyling2 = 'calendar';
$currentURL = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$metaDescription = "Чудите се кога важ познат празнува имен ден? Елате и открийте всички именни дни в България, събрани на едно място, подредени както по дати така и по имена.";
include 'header.php';
include 'listNamesTable.php';
?>

<div class="pageDescription">
	<h1>Именни дни</h1>
	<p>На тази страница ще откриете пълен списък на именните дни в България, както и информация за това кога се празнуват. А ако се чудите дали дадено име се празнува в повече от един ден, просто го напишете в търсачката и ще ви покажем всички дати, на които се отбелязва!</p>
</div>

<?php
/*Fetching the current name day, it also works if there are multiple name days in a single day*/
$currentDate = date('Y-m-d');
$query = "SELECT name, date, list_names, stays_same 
            FROM name_days 
            WHERE (date = '$currentDate' AND stays_same = 'false') 
            OR (DATE_FORMAT(date, '%m-%d') = DATE_FORMAT('$currentDate', '%m-%d') AND stays_same = 'true')";
$result = $conn->query($query);
// Initialize empty arrays to store names and list of names
$names = [];
$listOfNames = [];

// Fetch all records that match the query
while ($row = $result->fetch_assoc()) {
	// Append name to names array
	$names[] = $row['name'];
	// Append list of names to listOfNames array
	$listOfNames[] = $row['list_names'];
}
// Convert arrays to comma-separated strings
$namesString = implode(', ', $names);
$listOfNamesString = implode(', ', $listOfNames);

/*Fetching the next three name days:*/
$query = "SELECT name, date, list_names, stays_same
FROM name_days
WHERE (DATE_FORMAT(date, '%m-%d') > DATE_FORMAT(CURDATE(), '%m-%d') AND stays_same = 'true')
OR (date > '$currentDate' AND stays_same = 'false')
/*if stays_same = 'true',we only consider the month and day for sorting, else the entire date*/
ORDER BY CASE WHEN stays_same = 'true' THEN DATE_FORMAT(date, '%m-%d') ELSE date END
LIMIT 3";
$result = $conn->query($query);
$nextNameDays = $result->fetch_all(MYSQLI_ASSOC);
?>

<!-- Current Name Day -->
<div id="nameDayToday">
	<section>
		<h2>Днес:&nbsp;</h2>
		<!--The data-... is a custom attribute used to store data, in this case the date, which is then passed on to the js function and extracted using element.getAttribute('data-date');-->
		<h2 class="date-to-format" data-date="<?= date('Y-m-d') ?>">Днес: <?= date('Y-m-d') ?></h2>
	</section>
	<section>
		<?php if ($namesString !== "") : ?>
			<h2 id="nameDayTodayH2"><?= $namesString ?></h2>
			<p id="nameDayTodayP"><?= $listOfNamesString ?></p>
		<?php else : ?>
			<h2>Днес няма именни дни</h2>
		<?php endif; ?>
	</section>
</div>

<!-- Next Three Name Days -->
<h2>Предстоящи именни дни</h2>
<div id="nextThreeNameDays">
	<?php foreach ($nextNameDays as $day) : ?>
		<section>
			<h2><span class="date-to-format" data-date="<?= $day['date'] ?>"><?= $day['date'] ?></span></h2>
			<h3><?= $day['name'] ?></h3>
			<p><?= $day['list_names'] ?></p>
		</section>
	<?php endforeach; ?>
</div>

<!-- Search by name -->
<h2>Търсене по име</h2>
<?php
//query for $allNames
$query = "SELECT name FROM names";
$result = $conn->query($query);
$allNames = [];
while ($row = $result->fetch_assoc()) {
	$allNames[] = $row['name'];
}
?>
<div id="nameDaySearchContainer">
	<input type="text" id="nameSearch" placeholder="Търсете по име">
	<div id="searchResults"></div>
</div>

<!--Calendar-->
<h2>Календар</h2>
<!-- Name day selected from calendar -->
<div id="scrollToHere">
	<!--The scroll to here div is used to guide the js where to scroll to, before the lower div is visible-->
	<div id="nameDayClickedContainer"></div>
</div>
<!--Main Calendar container-->
<div class="calendars-container">
	<?php
	$months = [
		'Януари', 'Февруари', 'Март', 'Април', 'Май', 'Юни',
		'Юли', 'Август', 'Септември', 'Октомври', 'Ноември', 'Декември'
	];
	$days = ['П', 'В', 'С', 'Ч', 'П', 'С', 'Н'];

	// Query to fetch the rest of the data for the name days (both queries can be combined into one)
	$query = "SELECT name, DATE_FORMAT(date, '%c-%e') as date, list_names, stays_same
          FROM name_days
          WHERE stays_same = 'true'
          OR (stays_same = 'false' AND date > CURDATE())";
	$result = $conn->query($query);
	$allNameDayData = [];
	while ($row = $result->fetch_assoc()) {
		$date = $row['date'];
		// If the date already exists as a key, append the current row to the array
		if (isset($allNameDayData[$date])) {
			$allNameDayData[$date][] = $row;
		} else {
			// Otherwise, create a new array with the current row as the first element
			$allNameDayData[$date] = [$row];
		}
	}

	// Query to fetch name days
	$nameDays = [];
	$query = "SELECT name, DATE_FORMAT(date, '%c-%e') as date, list_names, stays_same
	FROM name_days
	WHERE stays_same = 'true'
	OR (stays_same = 'false' AND date > CURDATE())";
	$result = $conn->query($query);
	while ($row = $result->fetch_assoc()) {
		$nameDays[$row['date']] = $row['name'];
	}

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
			$style = ($index >= count($days) - 2) ? ' style="color: var(--yellow);"' : '';  // check if it's one of the last two days
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

			if (isset($nameDays[$currentDateKey])) {
				// Set background color to yellow for name days
				$backgroundColor = 'var(--very-light-gray)';
				$pointer = 'cursor: pointer;';
				// Set the title attribute to display the name of the name day
				$titleAttributes = ' title="' . htmlspecialchars($nameDays[$currentDateKey]) . '"';
			} else {
				$backgroundColor = '';
				$pointer = '';
			}

			// Check if the current day is a weekend day (Saturday or Sunday)
			if ($currentDayOfWeek == 6 || $currentDayOfWeek == 7) {
				$dayColor = 'color: var(--yellow); font-weight: 900'; // Apply color for weekend days
			} else {
				$dayColor = ''; // Default style for weekdays
			}

			// Check if the current day is today
			if ($currentDate == date('Y-m-d')) {
				$borderColor = 'var(--yellow)'; // Apply border for the current day
				$dayColor = 'color: var(--yellow); font-weight: 900';
			} else {
				$borderColor = ''; // No border for other days
			}

			$styleAttributes = ' style="background-color: ' . $backgroundColor . '; border-color: ' . $borderColor . '; ' . $dayColor . '; ' . $pointer . '"';

			// Print the date with the generated attributes
			echo '<div class="' . $class . '"' . $styleAttributes . $titleAttributes . ' onclick="loadData(this, \'' . $currentDateKey . '\')">' . $d . '</div>';
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


<!--JS script-->
<script>
	const allNames = <?= json_encode($allNames) ?>;
	var allNameDayData = <?php echo json_encode($allNameDayData); ?>;
</script>

<!--footer-->
<?php
include 'footer.php';
?>