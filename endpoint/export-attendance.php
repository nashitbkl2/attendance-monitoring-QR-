<?php
require '../vendor/autoload.php';
include('../conn/conn.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

// Fetch attendance data before clearing
$stmt = $conn->prepare("SELECT tbl_attendance_id, student_name, course_section, time_in, time_out FROM tbl_attendance LEFT JOIN tbl_student ON tbl_student.tbl_student_id = tbl_attendance.tbl_student_id");
$stmt->execute();
$result = $stmt->fetchAll();

// Clear the attendance data
$deleteStmt = $conn->prepare("DELETE FROM tbl_attendance");
$deleteStmt->execute();

// Create the spreadsheet
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set header values
$headers = ['ID', 'Name', 'UUCMS', 'Time In', 'Time Out'];
$sheet->fromArray($headers, NULL, 'A1');

// Apply style to headers
$headerStyle = [
    'font' => [
        'bold' => true,
    ],
    'alignment' => [
        'horizontal' => Alignment::HORIZONTAL_CENTER,
    ],
    'borders' => [
        'bottom' => [
            'borderStyle' => Border::BORDER_THIN,
        ],
    ],
    'fill' => [
        'fillType' => Fill::FILL_SOLID,
        'startColor' => [
            'argb' => 'FFCCCCCC',
        ],
    ],
];
$sheet->getStyle('A1:E1')->applyFromArray($headerStyle);

// Write data to spreadsheet
$rowIndex = 2; // Start from the second row
foreach ($result as $row) {
    $timeIn = date('Y-m-d H:i:s', strtotime($row['time_in']));
    $timeOut = !empty($row['time_out']) ? date('Y-m-d H:i:s', strtotime($row['time_out'])) : '';
    $sheet->fromArray([$row['tbl_attendance_id'], $row['student_name'], $row['course_section'], $timeIn, $timeOut], NULL, 'A' . $rowIndex);
    $rowIndex++;
}

// Auto-size columns
foreach (range('A', 'E') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Write to file and send as download
$writer = new Xlsx($spreadsheet);

// Redirect output to a client’s web browser (Xlsx)
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="attendance.xlsx"');
header('Cache-Control: max-age=0');
$writer->save('php://output');
exit;
?>
