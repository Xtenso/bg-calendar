IN CASE OF SWITCHING DOMAINS, there are a few things that need to be changed:
    - sitemap.php: $mainPages and $url
    - header.php: Google Tag script, after a new one is generated
    - .htaccess: NO NEED to change anything since the file will automatically detect any changes, and will still redirect to HTTPS even if the domain name is changed
    - robots.txt: Disallow page and sitemap address

IF CASE OF SWITCHING HOSTING PROVIDER:
    - dbConnection.php: $servername, $username, $password, $dbname