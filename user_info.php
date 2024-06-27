<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once "database.php";

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_stmt_init($conn);
if (mysqli_stmt_prepare($stmt, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $currentPassword = $_POST["current_password"];
    $newPassword = $_POST["new_password"];
    $confirmNewPassword = $_POST["confirm_new_password"];

    if (password_verify($currentPassword, $user['password'])) {
        if ($newPassword === $confirmNewPassword) {
            $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);
            $updateSql = "UPDATE users SET password = ? WHERE id = ?";
            if ($stmt = mysqli_stmt_prepare($conn, $updateSql)) {
                mysqli_stmt_bind_param($stmt, "si", $newPasswordHash, $user_id);
                mysqli_stmt_execute($stmt);
                echo "<div class='alert alert-success'>Password updated successfully.</div>";
            } else {
                echo "SQL Error: " . mysqli_error($conn);
            }
        } else {
            echo "<div class='alert alert-danger'>New password and confirmation do not match.</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Current password is incorrect.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Information</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div>
            <h1>User Information</h1>
            <p><strong>Welcome</strong> <?php echo htmlspecialchars($user['first_name'] . " " . $user['middle_name'] . " " . $user['last_name']); ?></p>
            <p><strong>Birthday:</strong> <?php echo htmlspecialchars($user['birthday']); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><strong>Contact:</strong> <?php echo htmlspecialchars($user['contact_number']); ?></p>
            <a href="logout.php">Log-out</a>
        </div>
        
        <div>
            <h2>Reset Password</h2>
            <form action="user_info.php" method="post">
                <div class="form-group">
                    <input type="password" class="form-control" name="current_password" placeholder="Enter Current Password" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="new_password" placeholder="Enter New Password" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control" name="confirm_new_password" placeholder="Re-Enter New Password" required>
                </div>
                <div class="form-btn">
                    <input type="submit" class="btn btn-primary" value="Reset Password">
                </div>
            </form>
        </div>
    </div>
</body>
</html>
