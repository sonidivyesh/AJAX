<?php
require_once '../db.php';
$return['message'] = "";
$array = [];

if (isset($_POST['request'])) {
    $request = $_POST['request'];

    $sql = mysqli_query($link, "SELECT * FROM post WHERE p_title = '$request' ORDER BY p_no DESC");
    $num_rows = mysqli_num_rows($sql);

    if ($num_rows > 0) {
        while ($result = mysqli_fetch_assoc($sql)) {
            array_push($array, $result);
        }
        $return['message'] = "Data found Successfully";
        $return['error_flag'] = 0;
        $return['data'] = $array;
    } else {
        $return['message'] = "Data not found";
        $return['error_flag'] = 0;
    }
} else {
    $return['message'] = "Error found while fetching data";
    $return['error_flag'] = 1;
    $return['error'] = $link->error;
}
echo json_encode($return);
