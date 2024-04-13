IN CASE OF SWITCHING DOMAINS, there are a few things that need to be changed:
    - sitemap.php: $mainPages and $url
    - header.php: Google Tag script, after a new one is generated
    - .htaccess: NO NEED to change anything since the file will automatically detect any changes, and will still redirect to HTTPS even if the domain name is changed

IF CASE OF SWITCHING HOSTING PROVIDER:
    - dbConnection.php: $servername, $username, $password, $dbname

-------------------------------------------------
TO-DO LIST:
    1.Add information about all relevant holiday dates from the holidays table into the evens table
    2.Responsive design for devices with smaller screens