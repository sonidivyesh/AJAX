<?php
require_once '../db.php';
$return['message'] = "";
$array = [];

$sql = mysqli_query($link, "SELECT * FROM post ORDER BY p_no DESC");
$num_rows = mysqli_num_rows($sql);

if ($num_rows > 0) {
    while ($result = mysqli_fetch_assoc($sql)) {
        array_push($array, $result);
    }
    $return['message'] = "Data Found Successfully";
    $return['error_flag'] = 0;
    $return['data'] = $array;
} else {
    $return['message'] = "Data not Found";
    $return['error_flag'] = 0;
}
echo json_encode($return);
