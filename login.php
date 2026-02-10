<?php
require_once __DIR__ . '/config/database.php';

// If already logged in, redirect
if (isset($_SESSION['admin_id'])) {
    header('Location: pages/dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($conn, $_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password harus diisi!';
    } else {
        $query = "SELECT * FROM admin WHERE username = '$username'";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) > 0) {
            $admin = mysqli_fetch_assoc($result);
            if (password_verify($password, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_nama'] = $admin['nama_lengkap'];
                header('Location: pages/dashboard.php');
                exit;
            } else {
                $error = 'Password salah!';
            }
        } else {
            $error = 'Username tidak ditemukan!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sistem Absensi Face Recognition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-container {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            backdrop-filter: blur(10px);
        }
        .login-card .logo-area {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-card .logo-area .icon-circle {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        .login-card .logo-area .icon-circle i {
            font-size: 35px;
            color: white;
        }
        .login-card h4 {
            color: #2d3436;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .login-card .subtitle {
            color: #636e72;
            font-size: 14px;
        }
        .form-floating {
            margin-bottom: 15px;
        }
        .form-floating .form-control {
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 12px 15px;
            height: 55px;
            font-size: 14px;
            transition: all 0.3s;
        }
        .form-floating .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s;
            letter-spacing: 0.5px;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            color: white;
        }
        .alert {
            border-radius: 12px;
            font-size: 14px;
        }
        .floating-shapes {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: -1;
        }
        .floating-shapes span {
            position: absolute;
            display: block;
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.05);
            animation: float 25s linear infinite;
            border-radius: 50%;
        }
        @keyframes float {
            0% { transform: translateY(100vh) rotate(0deg); opacity: 1; }
            100% { transform: translateY(-10vh) rotate(720deg); opacity: 0; }
        }
        .floating-shapes span:nth-child(1) { left: 25%; width: 80px; height: 80px; animation-delay: 0s; }
        .floating-shapes span:nth-child(2) { left: 10%; width: 20px; height: 20px; animation-delay: 2s; animation-duration: 12s; }
        .floating-shapes span:nth-child(3) { left: 70%; width: 20px; height: 20px; animation-delay: 4s; }
        .floating-shapes span:nth-child(4) { left: 40%; width: 60px; height: 60px; animation-delay: 0s; animation-duration: 18s; }
        .floating-shapes span:nth-child(5) { left: 65%; width: 20px; height: 20px; animation-delay: 0s; }
        .floating-shapes span:nth-child(6) { left: 75%; width: 110px; height: 110px; animation-delay: 3s; }
        .floating-shapes span:nth-child(7) { left: 35%; width: 150px; height: 150px; animation-delay: 7s; }
        .floating-shapes span:nth-child(8) { left: 50%; width: 25px; height: 25px; animation-delay: 15s; animation-duration: 45s; }
    </style>
</head>
<body>
    <div class="floating-shapes">
        <span></span><span></span><span></span><span></span>
        <span></span><span></span><span></span><span></span>
    </div>

    <div class="login-container">
        <div class="login-card">
            <div class="logo-area">
                <div class="icon-circle">
                    <i class="fas fa-face-smile-beam"></i>
                </div>
                <h4>Absensi Face Recognition</h4>
                <p class="subtitle">Silakan login untuk melanjutkan</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i><?= $error ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-floating">
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    <label for="username"><i class="fas fa-user me-2"></i>Username</label>
                </div>
                <div class="form-floating">
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                    <label for="password"><i class="fas fa-lock me-2"></i>Password</label>
                </div>
                <button type="submit" class="btn btn-login mt-3">
                    <i class="fas fa-sign-in-alt me-2"></i>Login
                </button>
            </form>

            <div class="text-center mt-4">
                <small class="text-muted">© 2026 Sistem Absensi Siswa</small>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
