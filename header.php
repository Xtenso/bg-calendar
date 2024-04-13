<!DOCTYPE html>
<html lang="bg">

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="robots" content="index, follow"> <!--Allows search engines to index this and all related pages-->
	<meta name="description" content="<?php echo !empty($metaDescription) ? $metaDescription : 'Всички официални празници, именни дни, както и най-важните събития от България, събрани на едно място.'; ?>">
	<meta name="keywords" content="календар, български календар, именни дни, почивни дни, официални празници, важни събития, събития, празници, дни">
	<link rel="stylesheet" type="text/css" href="styles/style.css">
	<link rel="stylesheet" type="text/css" href="styles/responsive.css">
	<?php if (isset($currentURL)) : ?>
		<link rel="canonical" href="<?= $currentURL ?>">
	<?php endif; ?>
	<?php if (isset($additionalStyling)) : ?>
		<link rel="stylesheet" type="text/css" href="styles/<?php echo $additionalStyling; ?>.css">
	<?php endif; ?>
	<?php if (isset($additionalStyling2)) : ?>
		<link rel="stylesheet" type="text/css" href="styles/<?php echo $additionalStyling2; ?>.css">
	<?php endif; ?>
	<!--Website icon-->
	<link rel="icon" href="img/warning.png" type="image/x-icon">
	<link rel="shortcut icon" href="img/warning.png" type="image/x-icon"> <!--For old browsers-->
	<link rel="apple-touch-icon" href="img/warning.png"> <!--For Apple devices; NEEDS TO BE PNG-->
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@500&display=swap" rel="stylesheet">
	<title><?php echo !empty($pageTitle) ? $pageTitle : 'Български Календар - Празници, Именни дни и Важни събития'; ?></title>
</head>

<!--Java Script-->
<script src="scriptBeginning.js"></script>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-N4ZZTVW816"></script>
<script>
	window.dataLayer = window.dataLayer || [];

	function gtag() {
		dataLayer.push(arguments);
	}
	gtag('js', new Date());

	gtag('config', 'G-N4ZZTVW816');
</script>

<?php
// Database connection
include 'dbConnection.php';
?>

<body>
	<div id="confirmationMessage">
		<h3>Благодарим Ви за докладването на проблема!</h3>
	</div>
	<?php
	// Check if a success parameter is present in the URL
	if (isset($_GET['success']) && $_GET['success'] == 'true') {
		echo "<script>document.getElementById('confirmationMessage').style.display = 'flex';</script>";
	}
	?>
	<div id="mainBody">
		<header>
			<div id="logoDiv">
				<h2 id="logo" onclick="window.location.href = 'index.php';">Български Календар</h2>
			</div>
			<nav>
				<button class="hamburger-menu">&#9776;</button>
				<ul>
					<li><a href="index.php">Днес</a></li>
					<li><a href="holidays.php">Почивни Дни</a></li>
					<li><a href="nameDays.php">Именни Дни</a></li>
				</ul>
			</nav>
		</header>
		<!--Remove when website is ready; the CSS is in styles with id-->
		<div id="warningMessage">
			<h3>Този сайт е в процес на разработка!</h3>
		</div>
		<div class="mainContainer">