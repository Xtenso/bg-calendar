<?php
$currentDate = date('Y-m-d');
$year = date('Y');
//If month is set in url
if (isset($_GET['month'])) {
	$currentMonth = $_GET['month'];
} else {
	$currentMonth = date('n');
}

$pageTitle = "Български Календар - Празници, Именни дни и Важни събития";
$additionalStyling = 'homePage';
$additionalStyling2 = 'calendar';
$currentURL = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
$metaDescription = "Всички официални празници, именни дни, както и най-важните събития от България, събрани на едно място.";
include 'header.php';
?>

<?php
//!All queries needed for this page
//* Database query for todays name days
$query = "SELECT name, date, list_names, stays_same 
            FROM name_days 
            WHERE (date = '$currentDate' AND stays_same = 'false') 
            OR (DATE_FORMAT(date, '%m-%d') = DATE_FORMAT('$currentDate', '%m-%d') AND stays_same = 'true')";
$result = $conn->query($query);
// Fetch all rows into $allNameDays
$todaysNameDays = $result->fetch_all(MYSQLI_ASSOC);

//* Database query for todays holidays
$query = "SELECT name, date, type, stays_same, end_date, description
          FROM holidays
          WHERE (type = 'official' AND
                 ('$currentDate' BETWEEN date AND COALESCE(end_date, date)) AND
                 stays_same = 'false')
            OR (type = 'official' AND
                (DATE_FORMAT('$currentDate', '%m-%d') BETWEEN DATE_FORMAT(date, '%m-%d') AND 
                 COALESCE(DATE_FORMAT(end_date, '%m-%d'), DATE_FORMAT(date, '%m-%d'))) AND
                 stays_same = 'true')";
$result = $conn->query($query);
// Fetch all rows into $allNameDays
$todaysHolidays = $result->fetch_all(MYSQLI_ASSOC);

//* Database query for todays school holidays
$query = "SELECT name, date, type, stays_same, end_date, description
          FROM holidays
          WHERE (type = 'school' AND
                 ('$currentDate' BETWEEN date AND COALESCE(end_date, date)) AND
                 stays_same = 'false')
            OR (type = 'school' AND
                (DATE_FORMAT('$currentDate', '%m-%d') BETWEEN DATE_FORMAT(date, '%m-%d') AND 
                 COALESCE(DATE_FORMAT(end_date, '%m-%d'), DATE_FORMAT(date, '%m-%d'))) AND
                 stays_same = 'true')";
$result = $conn->query($query);
// Fetch all rows into $allNameDays
$todaysSchoolHolidays = $result->fetch_all(MYSQLI_ASSOC);

//*Database query for upcoming holidays and name days
$endDateUpcHol = date('Y-m-d', strtotime('+360 days', strtotime($currentDate)));
$query = "(SELECT name, date, 'holiday' AS source, type, stays_same, end_date
            FROM holidays
            WHERE ((date BETWEEN '$currentDate' AND '$endDateUpcHol') AND stays_same = 'false') 
            OR (DATE_FORMAT(date, '%m-%d') > DATE_FORMAT('$currentDate', '%m-%d') AND stays_same = 'true'))
          UNION
          (SELECT name, date, 'name_day' AS source, list_names, stays_same, NULL AS end_date
            FROM name_days
            WHERE ((date BETWEEN '$currentDate' AND '$endDateUpcHol') AND stays_same = 'false') 
            OR (DATE_FORMAT(date, '%m-%d') > DATE_FORMAT('$currentDate', '%m-%d') AND stays_same = 'true'))
			ORDER BY DATE_FORMAT(date, '%m-%d')
			LIMIT 10";
$result = $conn->query($query);
// Fetch all rows into $allNameDays
$upcomingHolidays = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="pageDescription">
	<h1>Днес - <script>
			document.write(toBulgarianDateFormat('<?= $currentDate ?>'));
		</script>
	</h1>
	<p>На тази страница ще откриете информация за днешния ден, включително именни дни, почивни дни и други важни събития.</p>
	<div id="todayFlex">
		<!--Todays name days-->
		<div id="todaysNameDays">
			<section>
				<a href="nameDays.php">
					<h2>Именни дни</h2>
				</a>
			</section>
			<section>
				<?php if (isset($todaysNameDays) && !empty($todaysNameDays)) : ?>
					<?php foreach ($todaysNameDays as $nameDay) : ?>
						<div>
							<h2><?= $nameDay['name'] ?></h2>
							<p><?= $nameDay['list_names'] ?></p>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<h2>Днес няма именни дни!</h2>
				<?php endif; ?>
			</section>
		</div>
		<!--Todays holidays-->
		<div id="todaysHolidays">
			<section>
				<a href="holidays.php">
					<h2>Празници</h2>
				</a>
			</section>
			<section>
				<?php if (isset($todaysHolidays) && !empty($todaysHolidays)) : ?>
					<?php foreach ($todaysHolidays as $holiday) : ?>
						<div>
							<h2><?= $holiday['name'] ?></h2>
							<p><?= $holiday['description'] ?></p>
							<?php if (isset($holiday['end_date'])) { ?>
								<p>
									<script>
										document.write(toBulgarianDateFormat('<?= $holiday['date'] ?>'));
									</script> - <script>
										document.write(toBulgarianDateFormat('<?= $holiday['end_date'] ?>'));
									</script>
								</p>
							<?php } ?>
						</div>
					<?php endforeach; ?>
				<?php else : ?>
					<h2>Днес не е официален празник!</h2>
				<?php endif; ?>
			</section>
		</div>
	</div>
	<!--Todays school holidays-->
	<div id="todaysSchoolHolidays">
		<section>
			<a href="holidays.php">
				<h2>Ученически ваканции и неучебни дни</h2>
			</a>
		</section>
		<section>
			<?php if (isset($todaysSchoolHolidays) && !empty($todaysSchoolHolidays)) : ?>
				<?php foreach ($todaysSchoolHolidays as $holiday) : ?>
					<div>
						<h2><?= $holiday['name'] ?></h2>
						<p><?= $holiday['description'] ?></p>
						<?php if (isset($holiday['end_date'])) { ?>
							<p>
								<script>
									document.write(toBulgarianDateFormat('<?= $holiday['date'] ?>'));
								</script> - <script>
									document.write(toBulgarianDateFormat('<?= $holiday['end_date'] ?>'));
								</script>
							</p>
						<?php } ?>
					</div>
				<?php endforeach; ?>
			<?php else : ?>
				<h2>Няма предстоящи ваканции</h2>
			<?php endif; ?>
		</section>
	</div>

	<div id="flexContainerTodayPage">
		<div id="flexContainerTodayPageCol1">
			<h2>Предстоящи празници</h2>
			<?php
			if (empty($upcomingHolidays)) {
				echo '<p>Грешка при зареждането на информация! Ако проблема не се оправи при презареждане на страницата, моля докладвайте го!</p>';
			} else {
				foreach ($upcomingHolidays as $holiday) {
					if ($holiday['source'] === 'holiday') {
						$linkTo = 'holidays.php';
						if ($holiday['type'] === 'school') {
							$source = 'Неучебен ден';
						} else {
							$source = 'Официален празник';
						}
					} else {
						$linkTo = 'nameDays.php';
						$source = 'Имен ден';
					}
					if (isset($holiday['end_date'])) {
						echo '<a href="' . $linkTo . '"><p class="upcomingHolidayTodayPage"><span class="upcomingHolidayTodayPageOverflow"><script>document.write(toBulgarianDateFormat("' . $holiday['date'] . '"));</script>/<script>document.write(toBulgarianDateFormat("' . $holiday['end_date'] . '"));</script> - ' . $holiday['name'] . '</span> <span style="float: right; font-weight: bold;">' . $source . '</span></p></a>';
					} else {
						echo '<a href="' . $linkTo . '"><p class="upcomingHolidayTodayPage"><span class="upcomingHolidayTodayPageOverflow"><script>document.write(toBulgarianDateFormat("' . $holiday['date'] . '"));</script> - ' . $holiday['name'] . '</span> <span style="float: right; font-weight: bold;">' . $source . '</span></p></a>';
					}
				}
			}
			?>
		</div>

		<!--Month calendar container-->
		<input type="hidden" id="currentMonth" value="<?= $currentMonth ?>">
		<div id="flexContainerTodayPageCol2">
			<div class="calendars-container">
				<?php
				$months = [
					'Януари', 'Февруари', 'Март', 'Април', 'Май', 'Юни',
					'Юли', 'Август', 'Септември', 'Октомври', 'Ноември', 'Декември'
				];
				$days = ['П', 'В', 'С', 'Ч', 'П', 'С', 'Н'];

				// Include the holidays array and sql query
				include 'holidaysArray.php';

				// Database name days query
				$nameDays = [];
				$query = "SELECT name, DATE_FORMAT(date, '%c-%e') as formatted_date, list_names, stays_same FROM name_days
              WHERE MONTH(date) = $currentMonth AND (stays_same = 'true' OR YEAR(date) = $year)";
				$result = mysqli_query($conn, $query);
				while ($row = mysqli_fetch_assoc($result)) {
					$nameDays[$row['formatted_date']] = ['names' => explode(', ', $row['list_names'])];
				}

				// Determine the number of days in the current month
				$daysInMonth = cal_days_in_month(CAL_GREGORIAN, $currentMonth, $year);

				// Find out on which day of the week the month starts
				$startDayOfWeek = date('w', mktime(0, 0, 0, $currentMonth, 1, $year));
				if ($startDayOfWeek == 0) {
					$startDayOfWeek = 7;
				}

				echo '<div class="month">
            <div id="homePageMonthHeader" class="monthHeader">
				<button id="prev-month">&lt;</button>
				<a href="index.php">
					<svg xmlns="http://www.w3.org/2000/svg" fill="white" height="36" viewBox="0 -960 960 960" width="36">
						<path d="M360-300q-42 0-71-29t-29-71q0-42 29-71t71-29q42 0 71 29t29 71q0 42-29 71t-71 29ZM200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Z"/>
					</svg>
				</a>
                <h2>' . $months[$currentMonth - 1] . '</h2>
				<a href="holidays.php">
					<svg xmlns="http://www.w3.org/2000/svg" fill="white" height="36" viewBox="0 -960 960 960" width="36">
						<path d="M200-80q-33 0-56.5-23.5T120-160v-560q0-33 23.5-56.5T200-800h40v-80h80v80h320v-80h80v80h40q33 0 56.5 23.5T840-720v560q0 33-23.5 56.5T760-80H200Zm0-80h560v-400H200v400Zm0-480h560v-80H200v80Zm0 0v-80 80Zm280 240q-17 0-28.5-11.5T440-440q0-17 11.5-28.5T480-480q17 0 28.5 11.5T520-440q0 17-11.5 28.5T480-400Zm-160 0q-17 0-28.5-11.5T280-440q0-17 11.5-28.5T320-480q17 0 28.5 11.5T360-440q0 17-11.5 28.5T320-400Zm320 0q-17 0-28.5-11.5T600-440q0-17 11.5-28.5T640-480q17 0 28.5 11.5T680-440q0 17-11.5 28.5T640-400ZM480-240q-17 0-28.5-11.5T440-280q0-17 11.5-28.5T480-320q17 0 28.5 11.5T520-280q0 17-11.5 28.5T480-240Zm-160 0q-17 0-28.5-11.5T280-280q0-17 11.5-28.5T320-320q17 0 28.5 11.5T360-280q0 17-11.5 28.5T320-240Zm320 0q-17 0-28.5-11.5T600-280q0-17 11.5-28.5T640-320q17 0 28.5 11.5T680-280q0 17-11.5 28.5T640-240Z"/>
					</svg>
				</a>
				<button id="next-month">&gt;</button>
            </div>
            <div class="days">';
				foreach ($days as $index => $day) {
					$style = ($index >= count($days) - 2) ? ' style="color: #e25825;"' : '';  // check if it's one of the last two days
					echo '<div class="day"' . $style . '>' . $day . '</div>';
				}
				echo '</div>
            <div class="dates">';

				// Output the "past" days from the previous month to fill the week
				$prevMonthLastDay = date('t', strtotime("$year-" . ($currentMonth - 1) . "-01"));
				$prevMonthStartDay = $startDayOfWeek == 1 ? 7 : $startDayOfWeek - 1;

				// Only show days from the previous month if the current month doesn't start on a Monday
				if ($prevMonthStartDay < 7) {
					for ($d = $prevMonthLastDay - $prevMonthStartDay + 1; $d <= $prevMonthLastDay; $d++) {
						echo '<div class="past date">' . $d . '</div>';
					}
				}

				// Output the days of the current month with fixed positions
				for ($d = 1; $d <= $daysInMonth; $d++) {
					$currentDateKey = "$currentMonth-$d";
					$styleAttributes = '';
					$titleAttributes = '';
					$class = 'date';  // default class
					$currentDate = date('Y-m-d', mktime(0, 0, 0, $currentMonth, $d, $year));
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

					// Initialize link variables
					$linkStart = '';
					$linkEnd = '';

					// Check if the current date has a holiday
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
						// Set the link to holidays.php
						$linkStart = '<a href="holidays.php?date=' . $currentDate . '">';
						$linkEnd = '</a>';
					} elseif (isset($nameDays[$currentDateKey])) {
						// Set background color for name days
						$backgroundColor = 'green';
						$dayColor = 'color: var(--white); font-weight: 900';
						// Set the title for the name day
						$titleAttributes = ' title="' . htmlspecialchars(implode(', ', $nameDays[$currentDateKey]['names'])) . '"';
						// Set the link to nameDays.php
						$linkStart = '<a href="nameDays.php?date=' . $currentDate . '">';
						$linkEnd = '</a>';
					} else {
						$backgroundColor = '';
					}

					$styleAttributes = ' style="background-color: ' . $backgroundColor . '; border-color: ' . $borderColor . '; ' . $dayColor . ';"';

					// Print the date with the generated attributes and make it clickable if needed
					echo $linkStart . '<div class="' . $class . '"' . $styleAttributes . $titleAttributes . '>' . $d . '</div>' . $linkEnd;
				}

				// Output the "next" days from the next month to fill the remaining cells, if necessary
				if (($daysInMonth + $startDayOfWeek - 1) % 7 != 0) {
					$nextMonthDays = 7 - (($startDayOfWeek + $daysInMonth - 1) % 7);
					for ($d = 1; $d <= $nextMonthDays; $d++) {
						echo '<div class="past date">' . $d . '</div>';
					}
				}

				echo '</div>
        </div>';
				?>
			</div>
		</div>
	</div>

	<!--This div will only unless there are special events upcoming like: elections-->
	<?php
	$currentDate = date('Y-m-d');
	$endDate = date('Y-m-d', strtotime('+21 days', strtotime($currentDate)));
	$query = "SELECT name, description, date, end_date, image, more_info, color, stays_same FROM events
          WHERE (date BETWEEN '$currentDate' AND '$endDate')
		  OR (end_date BETWEEN '$currentDate' AND '$endDate')
          OR (stays_same = true AND DATE_FORMAT(date, '%m-%d') BETWEEN DATE_FORMAT('$currentDate', '%m-%d') AND DATE_FORMAT('$endDate', '%m-%d'))";
	$result = mysqli_query($conn, $query);
	if (mysqli_num_rows($result) > 0) {
		echo '<h2>Важни събития и празници</h2>';
		while ($row = mysqli_fetch_assoc($result)) {
			echo '<div id="specialEvent">';
			// Display appropriate color if set
			if (isset($row['color'])) {
				echo '<section style="background: ' . $row['color'] . '; color: white;">';
			} else {
				echo '<section>';
			}
			echo '<h2><script>document.write(toBulgarianDateFormat("' . $row['date'] . '"));</script></h2>';
			if (!empty($row['end_date'])) {
				echo '<h2>&nbsp;-&nbsp;</h2>';
				echo '<h2><script>document.write(toBulgarianDateFormat("' . $row['end_date'] . '"));</script></h2>';
			}
			echo '</section>';
			// Display name, description, and read more button
			echo '<section>';
			echo '<h2>' . $row['name'];
			if (!empty($row['more_info'])) {
				echo ' <a href="' . $row['more_info'] . '" target="_blank">Прочети повече ></a>';
			}
			echo '</h2>';
			echo '<p class="eventDescriptionTodayPageOverflow">' . $row['description'] . '</p>';
			echo '</section>';
			// Display image if available
			if (!empty($row['image'])) {
				echo '<img src="img/' . $row['image'] . '" alt="Event Image">';
			}
			echo '</div>';
		}
	} /*FOR DEBUGGING PURPOSES: else {
		echo 'No upcoming events found.';
		var_dump($result);
		echo $endDate;
		echo $currentDate;
	}*/
	?>

	<div>
		<h2>Интересни факти и проучвания</h2>
		<p>Отговорите са напълно анонимни! Като отговорите можете да видите какво мислят другите по този въпрос.</p>
	</div>
</div>
<?php
include 'footer.php';
?>