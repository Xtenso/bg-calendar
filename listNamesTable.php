<?php
//! This page creates a table "names" in the database, if it doesn't exist or if it's last modified date is before the last modified date of the table "name_days". This makes sure that the table "names" is always up to date with the latest names from the table "name_days".

// Check if the table "names" exists
$query = "SHOW TABLES LIKE 'names'";
$result = $conn->query($query);

// Check if the query was successful
if ($result === false) {
    die("Error checking table existence: " . $conn->error);
}

// Fetch the result
$tableExists = $result->num_rows > 0;

// Free the result set
$result->free();

// If the table doesn't exist or its last modified date is before the last modified date of the table "name_days", create a new table "names"
if (!$tableExists) {
    createNamesTable($conn);
} else {
    // Get the last modified time of the table "names"
    $query = "SELECT UPDATE_TIME
              FROM information_schema.TABLES
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'names'";
    $result = $conn->query($query);
    if ($result === false) {
        die("Error getting last modified time of 'names' table: " . $conn->error);
    }
    $row = $result->fetch_assoc();
    $lastModifiedTimeNames = $row['UPDATE_TIME'];

    // Get the last modified time of the table "name_days"
    $query = "SELECT UPDATE_TIME
              FROM information_schema.TABLES
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'name_days'";
    $result = $conn->query($query);
    if ($result === false) {
        die("Error getting last modified time of 'name_days' table: " . $conn->error);
    }
    $row = $result->fetch_assoc();
    $lastModifiedTimeNameDays = $row['UPDATE_TIME'];

    // Check if the last modified time of the table "names" is before the last modified time of the table "name_days"
    if (strtotime($lastModifiedTimeNames) < strtotime($lastModifiedTimeNameDays)) {
        // Drop the existing "names" table
        $query = "DROP TABLE IF EXISTS names";
        if ($conn->query($query) === false) {
            die("Error dropping existing 'names' table: " . $conn->error);
        }

        // Recreate the "names" table
        createNamesTable($conn);
    }
}

function createNamesTable($conn)
{
    // Initialize an empty set to store unique names
    $uniqueNames = [];

    // Get unique names from the "list_names" column of the "name_days" table
    $query = "SELECT list_names FROM name_days";
    $result = $conn->query($query);
    if ($result === false) {
        die("Error getting names from 'name_days' table: " . $conn->error);
    }

    // Fetch names and store unique names in the set
    while ($row = $result->fetch_assoc()) {
        $listNames = explode(", ", $row['list_names']);
        foreach ($listNames as $name) {
            $uniqueNames[$name] = true;
        }
    }

    // Create the "names" table
    $query = "CREATE TABLE names (
              id INT AUTO_INCREMENT PRIMARY KEY,
              name VARCHAR(255) NOT NULL
              )";
    if ($conn->query($query) === false) {
        die("Error creating 'names' table: " . $conn->error);
    }

    // Insert unique names into the "names" table
    $query = "INSERT INTO names (name) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $name);

    foreach ($uniqueNames as $name => $_) {
        $stmt->execute();
    }

    // Close the statement
    $stmt->close();
}
