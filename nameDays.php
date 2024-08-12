<!--header-->
<?php
$year = isset($_GET['year']) ? $_GET['year'] : date('Y'); // Set the year or default to current year
$pageTitle = "Именни дни";
$additionalStyling = 'nameDays';
$additionalStyling2 = 'calendar';
$currentURL = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$metaDescription = "Чудите се кога ваш познат празнува имен ден? Елате и открийте всички именни дни в България, събрани на едно място, подредени както по дати така и по имена.";
include 'header.php';
include 'listNamesTable.php';
?>

<div class="pageDescription">
	<h1>Именни дни</h1>
	<p>На тази страница ще откриете пълен списък на именните дни в България, както и информация за това кога се празнуват. А ако се чудите дали дадено име се празнува в повече от един ден, просто го напишете в търсачката и ще ви покажем всички дати, на които се отбелязва!</p>
</div>

<?php
$currentDate = date('Y-m-d');

// Fetch the current name day and next three name days in a single query
$query = "
    SELECT name, date, list_names, stays_same
    FROM name_days
    WHERE 
        (date = '$currentDate' AND stays_same = 'false')
        OR (DATE_FORMAT(date, '%m-%d') = DATE_FORMAT('$currentDate', '%m-%d') AND stays_same = 'true')
    UNION ALL
    SELECT name, date, list_names, stays_same
    FROM name_days
    WHERE 
        (DATE_FORMAT(date, '%m-%d') > DATE_FORMAT(CURDATE(), '%m-%d') AND stays_same = 'true')
        OR (date > '$currentDate' AND stays_same = 'false')
    ORDER BY CASE WHEN stays_same = 'true' THEN DATE_FORMAT(date, '%m-%d') ELSE date END
    LIMIT 3
";

$result = $conn->query($query);
$todayNameDays = [];
$nextNameDays = [];
$names = [];
$listOfNames = [];

// Distribute results into today and next name days
while ($row = $result->fetch_assoc()) {
	$date = $row['date'];
	if ($date == $currentDate) {
		$names[] = $row['name'];
		$listOfNames[] = $row['list_names'];
	} else {
		$nextNameDays[] = $row;
	}
}

$namesString = implode(', ', $names);
$listOfNamesString = implode(', ', $listOfNames);
?>

<!-- Current Name Day -->
<div id="nameDayToday">
	<section>
		<h2>Днес:&nbsp;</h2>
		<h2 class="date-to-format" data-date="<?= $currentDate ?>">Днес: <?= $currentDate ?></h2>
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
// Fetch all names for the search feature
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

<!-- Calendar -->
<h2>Календар</h2>
<div id="scrollToHere">
	<div id="nameDayClickedContainer"></div>
</div>
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

	// Fetch name days data
	$query = "SELECT name, DATE_FORMAT(date, '%c-%e') as date, list_names, stays_same
              FROM name_days
              WHERE stays_same = 'true'
              OR (stays_same = 'false' AND date > CURDATE())";
	$result = $conn->query($query);
	$nameDays = [];
	while ($row = $result->fetch_assoc()) {
		$nameDays[$row['date']][] = $row;
	}

	for ($m = 0; $m < 12; $m++) {
		$daysInMonth = date('t', strtotime("$year-" . ($m + 1) . "-01"));
		$startDayOfWeek = date('w', strtotime("$year-" . ($m + 1) . "-01")) ?: 7;

		echo '<div class="month">
            <div class="monthHeader">
            <h2>' . $months[$m] . '</h2>
            </div>
            <div class="days">';

		foreach ($days as $index => $day) {
			$style = ($index >= count($days) - 2) ? ' style="color: var(--yellow);"' : '';
			echo '<div class="day"' . $style . '>' . $day . '</div>';
		}

		echo '</div>
        <div class="dates">';

		for ($d = 1; $d < $startDayOfWeek; $d++) {
			echo '<div class="past date">' . ($daysInMonth - $startDayOfWeek + $d + 1) . '</div>';
		}

		for ($d = 1; $d <= $daysInMonth; $d++) {
			$currentDateKey = ($m + 1) . '-' . $d;
			$currentDate = date('Y-m-d', strtotime("$year-" . ($m + 1) . "-$d"));
			$currentDayOfWeek = date('N', strtotime($currentDate));

			$backgroundColor = '';
			$pointer = '';
			$dayColor = '';
			$borderColor = '';

			if (isset($nameDays[$currentDateKey])) {
				$backgroundColor = 'var(--very-light-gray)';
				$pointer = 'cursor: pointer;';
			}

			if ($currentDayOfWeek == 6 || $currentDayOfWeek == 7) {
				$dayColor = 'color: var(--yellow); font-weight: 900';
			}

			if ($currentDate == date('Y-m-d')) {
				$borderColor = 'var(--yellow)';
				$dayColor = 'color: var(--yellow); font-weight: 900';
			}

			$styleAttributes = ' style="background-color: ' . $backgroundColor . '; border-color: ' . $borderColor . '; ' . $dayColor . '; ' . $pointer . '"';
			$titleAttributes = isset($nameDays[$currentDateKey]) ? ' title="' . htmlspecialchars(implode(', ', array_column($nameDays[$currentDateKey], 'name'))) . '"' : '';

			echo '<div class="date"' . $styleAttributes . $titleAttributes . ' onclick="loadData(this, \'' . $currentDateKey . '\')">' . $d . '</div>';
		}

		$totalCells = $daysInMonth + $startDayOfWeek - 1;
		$cellsToFill = ($totalCells <= 35) ? 35 - $totalCells : 42 - $totalCells;

		for ($d = 1; $d <= $cellsToFill; $d++) {
			echo '<div class="past date">' . $d . '</div>';
		}

		echo '</div></div>';
	}
	?>
</div>

<!-- JS script -->
<script>
	const allNames = <?= json_encode($allNames) ?>;
	const allNameDayData = <?= json_encode($nameDays) ?>;
</script>

<!--footer-->
<?php
include 'footer.php';
?>