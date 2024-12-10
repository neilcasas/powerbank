
<?php
function create_connection() {
    // Database credentials
    $user = 'root';
    $db_password = 'admin'; // Change as needed
    $server = 'localhost:3310'; // Change if necessary
    $database = 'powerbank';

    return new mysqli($server, $user, $db_password, $database);
}

?>