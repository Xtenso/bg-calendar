<!--Database Connection-->
<?php
/*// Database connection for hosting server with Biz.nf
$servername = "fdb1031.biz.nf";  // server name
$username   = "4440163_bgcalendar";   // database username
$password   = "bestcal4life";   // database password
$dbname     = "4440163_bgcalendar";   // database name*/

/*// Database connection for hosting server with Infinity Free
$servername = "sql310.infinityfree.com";  // server name
$username   = "if0_35981857";   // database username
$password   = "bestcal4life";   // database password
$dbname     = "if0_35981857_bgcalendar";   // database name*/

$servername = "localhost";  // server name
$username   = "root";   // database username
$password   = "";   // database password
$dbname     = "calendar";   // database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
mysqli_set_charset($conn, "utf8");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>