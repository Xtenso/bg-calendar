<?php
$pageTitle = "Sitemap";
include 'header.php';

echo "<h1>Sitemap</h1>";
echo "<h3>To view the xml version of the website click <a href='sitemap.xml'>here</a>.</h3>";

// Fetch all names from the database
$query = "SELECT name FROM names";
$result = $conn->query($query);
$allNames = [];
while ($row = $result->fetch_assoc()) {
    $allNames[] = $row['name'];
}

// Array containing the main pages of your website
$main_pages = array(
    "https://bgcalendar.kesug.com/index.php",
    "https://bgcalendar.kesug.com/holidays.php",
    "https://bgcalendar.kesug.com/nameDays.php",
    "https://bgcalendar.kesug.com/FAQ.php",
);

// Function to generate sitemap
function generate_sitemap($main_pages, $allNames) {
    // Open sitemap file for writing
    $sitemap_file = fopen("sitemap.xml", "w");
    // Write XML header
    fwrite($sitemap_file, '<?xml version="1.0" encoding="UTF-8"?>' . "\n");
    fwrite($sitemap_file, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n");

    // Loop through the main pages and write them to the sitemap
    foreach ($main_pages as $page) {
        $lastmod = date("c"); // Use current time as last modification time
        fwrite($sitemap_file, "\t<url>\n");
        fwrite($sitemap_file, "\t\t<loc>$page</loc>\n");
        fwrite($sitemap_file, "\t\t<lastmod>$lastmod</lastmod>\n");
        fwrite($sitemap_file, "\t</url>\n");
    }

    // Loop through all names and generate URLs
    foreach ($allNames as $name) {
        $url = "https://bgcalendar.kesug.com/nameDayDetails.php?name=" . urlencode($name);
        $lastmod = date("c"); // Use current time as last modification time
        fwrite($sitemap_file, "\t<url>\n");
        fwrite($sitemap_file, "\t\t<loc>$url</loc>\n");
        fwrite($sitemap_file, "\t\t<lastmod>$lastmod</lastmod>\n");
        fwrite($sitemap_file, "\t</url>\n");
    }

    // Close the urlset tag
    fwrite($sitemap_file, '</urlset>');
    // Close the sitemap file
    fclose($sitemap_file);
}

// Generate sitemap
generate_sitemap($main_pages, $allNames);
?>

<?php
// Parse sitemap.xml and display as HTML
$sitemap = simplexml_load_file("sitemap.xml");

echo "<ul>";
foreach ($sitemap->url as $url) {
    echo "<li><a href='$url->loc'>$url->loc</a></li>";
}
echo "</ul>";
?>

<?php
include 'footer.php';
?>