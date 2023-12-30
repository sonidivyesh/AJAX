<?php
require_once '../db.php';
$return['mesaage'] = '';

// Extract data from the POST request
$p_username = $_POST['p_username'];
$p_tmg = $_POST['p_tmg'];
$p_title = $_POST['p_title'];
$p_status = $_POST['p_status'];
$state = $_POST['state'];
$city = $_POST['city'];

$sql = "INSERT INTO post (
        p_username,
        p_tmg,
        p_title,
        p_status,
        state,
        city
    ) 
    VALUES 
    (
        '$p_username', 
        '$p_tmg', 
        '$p_title', 
        '$p_status', 
        '$state', 
        '$city')";

if (mysqli_query($link, $sql)) {
    $return['message'] = "Data Added Successfully";
    $return['error_flag'] = 0;
} else {
    $return['message'] = "Failed to add Data details.";
    $return['error_flag'] = 1;
    $return['error'] = $link->error;
}

echo json_encode($return);
