<?php
session_start();
include('../conn/conn.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['clear_attendance'])) {
    $stmt = $conn->prepare("TRUNCATE TABLE tbl_attendance"); // Truncate the table to delete all records
    if ($stmt->execute()) {
        header("Location: ../index.php"); // Redirect to the masterlist page after clearing
        exit();
    } else {
        echo "Error clearing attendance.";
    }
}
?>
