<?php
// Check if 'name' parameter is set in the URL, if not, redirect to nameDays.php
if (!isset($_GET['name'])) {
    header("Location: nameDays.php");
    exit; // Stop further execution
}
$metaDescription = "Вижте на кои дати " . $_GET['name'] . " празнува имен ден.";
$name = $_GET['name'];
$additionalStyling = 'nameDayDetails';
$pageTitle = "$name - Именни Дни";
$currentURL = "$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
include 'header.php';

/*Name day details query; made so that it only includes exact matches from list_names*/
$query = "SELECT name, date, list_names, stays_same
          FROM name_days
          WHERE (CONCAT(',', REPLACE(list_names, ', ', ','), ',') LIKE '%,$name,%' AND stays_same = 'true')
          OR (CONCAT(',', REPLACE(list_names, ', ', ','), ',') LIKE '%,$name,%' AND stays_same = 'false' AND DATE_FORMAT(date, '%Y-%m-%d') > DATE_FORMAT(CURDATE(), '%Y-%m-%d'))";
$result = $conn->query($query);
$nameDayDetails = $result->fetch_all(MYSQLI_ASSOC);

/*Query for similar name days*/
$query = "SELECT name
          FROM names
          WHERE name LIKE CONCAT('%', SUBSTRING('$name', 1, 4), '%') AND name != '$name'
          LIMIT 10";
$result = $conn->query($query);
$similarNameDays = $result->fetch_all(MYSQLI_ASSOC);
?>

<!-- Selected name day details -->
<h1><?= $name ?></h1>
<h3><?= $name ?> има <?= sizeof($nameDayDetails) ?> <?= sizeof($nameDayDetails) === 1 ? 'имен ден' : 'именни дни' ?>.</h3>
<div id="nameDayDetailsContainer">
    <div id="nameDayDetailsCol1">
        <?php foreach ($nameDayDetails as $day) : ?>
            <section>
                <h2>
                    <script>
                        document.write(toBulgarianDateFormat('<?= $day['date'] ?>'));
                    </script>
                </h2>
                <h3><?= $day['name'] ?></h3>
                <p><?= $day['list_names'] ?></p>
                <span><?= $day['stays_same'] == 'true' ? 'ПОСТОЯНЕН' : 'ПРОМЕНЛИВ' ?></span>
            </section>
        <?php endforeach; ?>
    </div>
    <div id="nameDayDetailsCol2">
        <div>
            <h2>Подобни имена</h2>
            <p>Открихме <?= sizeof($similarNameDays) ?> <?= sizeof($similarNameDays) === 1 ? 'име, свързано' : 'имена, свързани' ?> <?= $name[0] === 'С' ? 'със' : 'с' ?> <?= $name ?>.</p>
        </div>
        <ul id="similarNameDays">
            <?php foreach ($similarNameDays as $day) : ?>
                <li onclick="window.location.href = 'nameDayDetails.php?name=<?= urlencode($day['name']) ?>';"><?= $day['name'] ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>

<?php
include 'footer.php';
?>