<?php
session_start();
require_once 'config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT user_id, full_name, password_hash, role FROM users WHERE username = ? AND is_active = 1");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id']   = $user['user_id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];

            switch ($user['role']) {
                case 'receptionist':  header('Location: modules/receptionist.php'); break;
                case 'nurse':         header('Location: modules/nurse.php');         break;
                case 'doctor':        header('Location: modules/doctor.php');         break;
                case 'lab_technician':header('Location: modules/lab.php');            break;
                case 'pharmacist':    header('Location: modules/pharmacy.php');       break;
                case 'admin':         header('Location: modules/admin.php');          break;
            }
            exit();
        } else {
            $error = 'Incorrect password. Please try again.';
        }
    } else {
        $error = 'Username not found. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRMS – Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .login-card {
            max-width: 420px;
            margin: 100px auto;
        }
        .card-header {
            background-color: #0d6efd;
            color: white;
            text-align: center;
            padding: 20px;
        }
        .card-header h4 {
            margin: 0;
            font-size: 18px;
        }
        .card-header p {
            margin: 4px 0 0;
            font-size: 13px;
            opacity: 0.85;
        }
    </style>
</head>
<body>
<div class="login-card">
    <div class="card shadow-sm">
        <div class="card-header">
            <h4>Nairobi Women's Hospital</h4>
            <p>Patient Record Management System</p>
        </div>
        <div class="card-body p-4">
            <?php if ($error): ?>
                <div class="alert alert-danger py-2"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Log In</button>
                </div>
            </form>
        </div>
        <div class="card-footer text-center text-muted" style="font-size:12px;">
            &copy; <?= date('Y') ?> Nairobi Women's Hospital
        </div>
    </div>
</div>
</body>
</html>