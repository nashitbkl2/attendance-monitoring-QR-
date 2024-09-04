<?php
session_start();
include('./conn/conn.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ./endpoint/login.php");
    exit();
}

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: ./endpoint/login.php");
    exit();
}

$username = $_SESSION['username'];

$stmt = $conn->prepare("SELECT * FROM tbl_attendance LEFT JOIN tbl_student ON tbl_student.tbl_student_id = tbl_attendance.tbl_student_id");
$stmt->execute();
$present_students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Attendance System</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@500&display=swap');

* {
    margin: 0;
    padding: 0;
    font-family: 'Poppins', sans-serif;
}

body {
    background: linear-gradient(to bottom, rgba(255,255,255,0.15) 0%, rgba(0,0,0,0.15) 100%), radial-gradient(at top center, rgba(255,255,255,0.40) 0%, rgba(0,0,0,0.40) 120%) #989898;
    background-blend-mode: multiply,multiply;
    background-attachment: fixed;
    background-repeat: no-repeat;
    background-size: cover;
}

.main {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top:-300px;
    height: 1000.5vh;
}

.attendance-container {
    height: 90%;
    width: 90%;
    border-radius: 20px;
    padding: 40px;
    background-color: rgba(255, 255, 255, 0.8);
}

.attendance-container > div {
    box-shadow: rgba(0, 0, 0, 0.24) 0px 3px 8px;
    border-radius: 10px;
    padding: 30px;
}

.attendance-container > div:last-child {
    width: 64%;
    margin-left: auto;
    
}
.anjuman_logo{
    width: 100px;
    height: 100px;
}
    </style>
</head>
<body>
    <!-- Title Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <img class="anjuman_logo" src="https://falconinstitutions.org/wp-content/uploads/2020/02/anjuman-logo.png" alt="">
        <a class="navbar-brand ml-4" href="#">Anjuman Institute of Management and Computer Applications</a>
    </nav>

    <!-- Main Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="./index.php">Home <span class="sr-only"></span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./endpoint/manage_users.php">Manage Users</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="./masterlist.php">List of Students</a>
                </li>
                <li class="nav-item ">
                    <a class="nav-link" href="./attendance_search.html">search attendance</a>
                </li>
                

                
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item mr-3">
                    <span class="navbar-text text-white mr-2">Welcome, <?php echo $username; ?></span>
                    <a class="nav-link" href="?logout">Logout</a>
                </li>
            </ul>
            
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main">
        <div class="attendance-container row">
            <div class="qr-container col-4">
                <div class="scanner-con">
                    <h5 class="text-center">Scan your QR Code here for your attendance</h5>
                    <video id="interactive" class="viewport" width="100%"></video>
                </div>

                <div class="qr-detected-container" style="display: none;">
                    <form id="attendance-form" action="./endpoint/add-attendance.php" method="POST">
                        <h4 class="text-center">Student QR Detected!</h4>
                        <input type="hidden" id="detected-qr-code" name="qr_code">
                    </form>
                </div>
            </div>
        
            <!-- Attendance List Section -->
            <div class="attendance-list col-8">
                <h4>List of Present Students</h4>
                <div class="table-container table-responsive">
                    <table class="table text-center table-sm" id="attendanceTable">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">Name</th>
                                <th scope="col">UUCMS</th>
                                <th scope="col">Time In</th>
                                <th scope="col">Time Out</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($present_students as $student): ?>
                                <tr>
                                    <td><?php echo $student['tbl_attendance_id']; ?></td>
                                    <td><?php echo $student['student_name']; ?></td>
                                    <td><?php echo $student['course_section']; ?></td>
                                    <td><?php echo $student['time_in']; ?></td>
                                    <td><?php echo $student['time_out']; ?></td>
                                    <td>
                                        <div class="action-button">
                                            <button class="btn btn-danger" onclick="deleteAttendance(<?php echo $student['tbl_attendance_id']; ?>)">X</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <form action="./endpoint/clear-attendance.php" method="POST">
    <button type="submit" name="clear_attendance" class="btn btn-danger mt-3">Clear Attendance List</button>
</form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>

    <!-- Instascan JS -->
    <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>

    <script>
        let scanner;

function startScanner() {
    scanner = new Instascan.Scanner({ video: document.getElementById('interactive') });

    scanner.addListener('scan', function (content) {
        $("#detected-qr-code").val(content);
        console.log(content);
        document.querySelector(".qr-detected-container").style.display = '';
        document.querySelector(".scanner-con").style.display = 'none';

        
        speak(`Attendance updated `);

        document.getElementById('attendance-form').submit();
    });

    Instascan.Camera.getCameras()
        .then(function (cameras) {
            if (cameras.length > 0) {
                scanner.start(cameras[0]);
            } else {
                console.error('No cameras found.');
                alert('No cameras found.');
            }
        })
        .catch(function (err) {
            console.error('Camera access error:', err);
            alert('Camera access error: ' + err);
        });
}

function speak(message) {
    const utterance = new SpeechSynthesisUtterance(message);
    
    const setVoice = () => {
        const voices = window.speechSynthesis.getVoices();
        const femaleVoice = voices.find(voice => voice.name.includes('Google UK English Male') || voice.name.includes('Microsoft Zira') || voice.name.includes('Google US English Female'));

        if (femaleVoice) {
            utterance.voice = femaleVoice;
        }
        window.speechSynthesis.speak(utterance);
    };

    if (window.speechSynthesis.getVoices().length === 0) {
        window.speechSynthesis.addEventListener('voiceschanged', setVoice);
    } else {
        setVoice();
    }
}

// Ensure voices are loaded
window.speechSynthesis.getVoices();


document.addEventListener('DOMContentLoaded', startScanner);

function deleteAttendance(id) {
    if (confirm("Do you want to remove this attendance?")) {
        window.location = "./endpoint/delete-attendance.php?attendance=" + id;
        speak(`Attendance deleted `);
    }
}

    </script>
</body>
</html>
