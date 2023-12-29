<?php
$servername = "localhost";
$username = "root";
$password = "";
$database = "ajax_dropdown_filters";

// create a connection
$link = mysqli_connect($servername, $username, $password, $database);
if ($link->connect_error) {
    die("Connection failed: " . $link->connect_error);
}
