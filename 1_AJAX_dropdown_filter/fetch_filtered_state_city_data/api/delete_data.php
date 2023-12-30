<?php
require_once '../db.php';

// Check if the request method is DELETE
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Assuming the ID is passed in the request body as JSON
    $data = json_decode(file_get_contents("php://input"), true);

    // Use prepared statement to prevent SQL injection
    $id = mysqli_real_escape_string($link, $data['id']);
    $sql = "DELETE FROM post WHERE p_no ='$id' ";

    if (mysqli_query($link, $sql)) {
        // Return a success response as JSON
        echo json_encode(['status' => 'success', 'message' => 'Data deleted successfully']);
    } else {
        // Return an error response as JSON
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete Data']);
    }

    $link->close();
} else {
    // Return an error response if the request method is not DELETE
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
