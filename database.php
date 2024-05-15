<?php
    $hostName = "XXXX";
    $dbUser = "XXXX";
    $dbPassword = "XXXXX";
    $dbName = "XXXXX";
    $conn = mysqli_connect($hostName, $dbUser, $dbPassword, $dbName);

    if (!$conn) {
        exit("Trouble connecting to the database.". mysqli_connect_error());
    }
    else {
        // We were able to connect to the database 
    }
?>