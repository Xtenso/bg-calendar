<?php
//Database Connection
include 'dbConnection.php';

// Get form data
$originalPageUrl = urldecode($_POST['url'] ?? 'holidays.php'); /* decode page url before sending it to db */
$reportName = $_POST['reportName'] ?? '';
$reportType = $_POST['reportType'] ?? '';
$reportDescription = $_POST['reportDescription'] ?? '';

// Extract the page name from the URL
$originalPage = basename($originalPageUrl);

// Create the table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS reported_problems (
        id INT AUTO_INCREMENT PRIMARY KEY,
        report_name VARCHAR(255) NOT NULL,
        report_type VARCHAR(255) NOT NULL,
        report_description TEXT NOT NULL,
        originating_page VARCHAR(255) NOT NULL,
        submission_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
if ($conn->query($sql) === FALSE) {
    die("Error creating table: " . $conn->error);
}

// Insert form data into the table
$sql = "INSERT INTO reported_problems (report_name, report_type, report_description, originating_page) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $reportName, $reportType, $reportDescription, $originalPageUrl);

if ($stmt->execute() === FALSE) {
    die("Error inserting data: " . $conn->error);
}

// Close statement and database connection
$stmt->close();
$conn->close();

// Redirect back to the form page
if (strpos($originalPage, '?') !== false) {
    // If the original page URL already contains parameters
    echo '<script>window.location.href = "' . $originalPage . '&success=true";</script>';
} else {
    // If the original page URL does not contain parameters
    echo '<script>window.location.href = "' . $originalPage . '?success=true";</script>';
}
exit;
