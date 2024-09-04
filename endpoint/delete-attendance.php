<?php
include ('../conn/conn.php');

// Get the ID of the attendance to delete
$attendanceID = $_GET['attendance'];

// Delete the attendance record
$stmt = $conn->prepare("DELETE FROM tbl_attendance WHERE tbl_attendance_id = ?");
$stmt->execute([$attendanceID]);

// Reset the IDs
$stmt = $conn->prepare("SET @count = 0");
$stmt->execute();

$stmt = $conn->prepare("UPDATE tbl_attendance SET tbl_attendance.tbl_attendance_id = @count:= @count + 1");
$stmt->execute();

$stmt = $conn->prepare("ALTER TABLE tbl_attendance AUTO_INCREMENT = 1");
$stmt->execute();

// Redirect to the attendance list page
header("Location: ../index.php");
exit;
?>
