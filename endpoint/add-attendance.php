<?php
include('../conn/conn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $qrCode = $_POST['qr_code'];

    try {
        // Find the student ID based on the QR code
        $stmt = $conn->prepare("SELECT tbl_student_id FROM tbl_student WHERE generated_code = :qr_code");
        $stmt->bindParam(':qr_code', $qrCode);
        $stmt->execute();
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($student) {
            $studentId = $student['tbl_student_id'];

            // Check if the student has an open attendance record (i.e., no time_out) for today
            $stmt = $conn->prepare("SELECT * FROM tbl_attendance WHERE tbl_student_id = :student_id AND DATE(time_in) = CURDATE() AND time_out IS NULL");
            $stmt->bindParam(':student_id', $studentId);
            $stmt->execute();
            $attendance = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($attendance) {
                // If the student has an open attendance record, update the time_out
                $stmt = $conn->prepare("UPDATE tbl_attendance SET time_out = NOW() WHERE tbl_attendance_id = :attendance_id");
                $stmt->bindParam(':attendance_id', $attendance['tbl_attendance_id']);
                $stmt->execute();
            } else {
                // If no open attendance record, insert a new record with time_in
                $stmt = $conn->prepare("INSERT INTO tbl_attendance (tbl_student_id, time_in) VALUES (:student_id, NOW())");
                $stmt->bindParam(':student_id', $studentId);
                $stmt->execute();
            }

            // Redirect back to the index page with a success message
            header('Location: ../index.php?status=success');
            exit();
        } else {
            // Redirect back with an error message if the student is not found
            header('Location: ../index.php?status=student_not_found');
            exit();
        }
    } catch (PDOException $e) {
        // Log error and redirect back with an error message
        error_log("Database error: " . $e->getMessage());
        header('Location: ../index.php?status=error');
        exit();
    }
} else {
    // Redirect if the request method is not POST
    header('Location: ../index.php');
    exit();
}
?>
