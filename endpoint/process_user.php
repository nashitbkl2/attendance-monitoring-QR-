<?php
include('../conn/conn.php');

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action == 'delete' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $stmt = $conn->prepare("DELETE FROM tbl_users WHERE id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: ./manage_users.php");
        exit();
    } elseif ($action == 'edit' && $_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = $_POST['id'];
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (!empty($password)) {
            $stmt = $conn->prepare("UPDATE tbl_users SET username = :username, password = :password WHERE id = :id");
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $password);
        } else {
            $stmt = $conn->prepare("UPDATE tbl_users SET username = :username WHERE id = :id");
            $stmt->bindParam(':username', $username);
        }
        
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        header("Location: ./manage_users.php");
        exit();
    }
}
?>
