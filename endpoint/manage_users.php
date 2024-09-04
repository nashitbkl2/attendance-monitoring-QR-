<?php
session_start();
include('../conn/conn.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: ./login.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM tbl_users");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - QR Code Attendance System</title>
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../style.css">
</head>
<body>
    <div class="container">
        <h2 class="text-center">Manage Users</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td>
                            <!-- <button class="btn btn-primary edit-btn" data-toggle="modal" data-target="#editUserModal<?php echo $user['id']; ?>" data-id="<?php echo $user['id']; ?>">Edit</button> -->
                            <a href="./process_user.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-danger">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="../index.php" class="btn btn-primary">Back to Home</a>
        <p class="text-center">Want to create an account? <a href="register.php">Register here</a></p>
    </div>

    <!-- Edit User Modals
    <?php foreach ($users as $user): ?>
        <div class="modal fade" id="editUserModal<?php echo $user['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel<?php echo $user['id']; ?>" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editUserModalLabel<?php echo $user['id']; ?>">Edit User</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="./process_user.php?action=edit" method="POST">
                        <div class="modal-body">
                            <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username<?php echo $user['id']; ?>" name="username" value="<?php echo $user['username']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password<?php echo $user['id']; ?>" name="password">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?> -->

    <script src="../bootstrap/jquery/jquery.slim.min.js"></script>
    <script src="../bootstrap/popper/popper.min.js"></script>
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.edit-btn').click(function() {
                var userId = $(this).data('id');
                $('#editUserModal' + userId).modal('show');
            });
        });
    </script>
</body>
</html>
