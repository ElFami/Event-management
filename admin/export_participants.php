<?php
session_start();

if ($_SESSION['role'] != 'admin') {
    header('Location: ../login.php');
    exit();
}

require '../config.php';
require '../vendor/autoload.php'; // Adjust the path as needed for your project structure

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Fetch all users and their registered events
$sql_users = "
    SELECT u.id AS user_id, u.name AS user_name, u.email AS user_email, e.name AS event_name
    FROM users u
    LEFT JOIN registrations r ON u.id = r.user_id
    LEFT JOIN events e ON r.event_id = e.id
    ORDER BY u.name, e.name";

$users_result = $conn->query($sql_users);

// Check for query errors
if ($users_result === false) {
    die("Error executing query: " . $conn->error);
}

// Check if any users are found
if ($users_result->num_rows === 0) {
    die("No users found.");
}

// Create a new Spreadsheet object
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Set the header for the Excel file
$sheet->setCellValue('A1', 'User ID');
$sheet->setCellValue('B1', 'Name');
$sheet->setCellValue('C1', 'Email');
$sheet->setCellValue('D1', 'Event Name');

// Populate the spreadsheet with user data
$row = 2; // Start at row 2 since row 1 is for headers
while ($user = $users_result->fetch_assoc()) {
    $sheet->setCellValue('A' . $row, $user['user_id']);
    $sheet->setCellValue('B' . $row, $user['user_name']);
    $sheet->setCellValue('C' . $row, $user['user_email']);
    $sheet->setCellValue('D' . $row, $user['event_name']);
    $row++;
}

// Set headers to download the file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="participant.xlsx"');
header('Cache-Control: max-age=0');

// Write the file to the output
$writer = new Xlsx($spreadsheet);
ob_end_clean(); // Clear the output buffer
$writer->save('php://output');
exit();
?>
