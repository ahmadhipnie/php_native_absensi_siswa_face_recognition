<?php
require_once __DIR__ . '/../config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: ../login.php');
    exit;
}

// Get school settings
$setting_query = mysqli_query($conn, "SELECT * FROM pengaturan LIMIT 1");
$pengaturan = mysqli_fetch_assoc($setting_query);

// Get current page
$current_page = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'Dashboard' ?> - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        :root {
            --sidebar-width: 260px;
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --sidebar-bg: #1a1a2e;
            --sidebar-hover: #16213e;
            --sidebar-active: #0f3460;
        }
        body {
            background-color: #f0f2f5;
            overflow-x: hidden;
        }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: var(--sidebar-bg);
            z-index: 1000;
            transition: all 0.3s ease;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand .brand-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-gradient);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }
        .sidebar-brand .brand-icon i {
            font-size: 24px;
            color: white;
        }
        .sidebar-brand h5 {
            color: white;
            font-size: 14px;
            font-weight: 600;
            margin: 0;
        }
        .sidebar-brand small {
            color: rgba(255,255,255,0.5);
            font-size: 11px;
        }
        .sidebar-menu {
            padding: 15px 0;
        }
        .sidebar-menu .menu-label {
            color: rgba(255,255,255,0.4);
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 10px 20px 5px;
        }
        .sidebar-menu .menu-item {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
            margin: 2px 10px;
            border-radius: 10px;
        }
        .sidebar-menu .menu-item:hover {
            background: var(--sidebar-hover);
            color: white;
        }
        .sidebar-menu .menu-item.active {
            background: var(--primary-gradient);
            color: white;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        .sidebar-menu .menu-item i {
            width: 22px;
            margin-right: 12px;
            font-size: 16px;
            text-align: center;
        }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
        }

        /* ===== TOP NAVBAR ===== */
        .top-navbar {
            background: white;
            padding: 15px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }
        .top-navbar .toggle-sidebar {
            background: none;
            border: none;
            font-size: 20px;
            color: #636e72;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        .top-navbar .toggle-sidebar:hover {
            background: #f0f0f0;
        }
        .top-navbar .page-title {
            font-weight: 600;
            color: #2d3436;
            font-size: 18px;
            margin: 0 0 0 10px;
        }
        .top-navbar .navbar-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .top-navbar .user-dropdown {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 10px;
            transition: all 0.3s;
        }
        .top-navbar .user-dropdown:hover {
            background: #f0f0f0;
        }
        .top-navbar .user-avatar {
            width: 38px;
            height: 38px;
            background: var(--primary-gradient);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }

        /* ===== CONTENT AREA ===== */
        .content-area {
            padding: 30px;
        }

        /* ===== STAT CARDS ===== */
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border: none;
            position: relative;
            overflow: hidden;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .stat-card .stat-icon {
            width: 55px;
            height: 55px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }
        .stat-card .stat-icon.bg-primary-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .stat-card .stat-icon.bg-success-gradient {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }
        .stat-card .stat-icon.bg-warning-gradient {
            background: linear-gradient(135deg, #F2994A 0%, #F2C94C 100%);
        }
        .stat-card .stat-icon.bg-danger-gradient {
            background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        }
        .stat-card .stat-icon.bg-info-gradient {
            background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%);
        }
        .stat-card h3 {
            font-weight: 700;
            color: #2d3436;
            margin: 15px 0 5px;
            font-size: 28px;
        }
        .stat-card p {
            color: #636e72;
            font-size: 13px;
            margin: 0;
        }

        /* ===== TABLE CARDS ===== */
        .table-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border: none;
            overflow: hidden;
        }
        .table-card .card-header {
            background: white;
            border-bottom: 1px solid #eee;
            padding: 20px 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .table-card .card-header h5 {
            font-weight: 600;
            margin: 0;
            color: #2d3436;
        }
        .table-card .card-body {
            padding: 20px 25px;
        }

        /* ===== BUTTONS ===== */
        .btn-primary-gradient {
            background: var(--primary-gradient);
            border: none;
            color: white;
            border-radius: 10px;
            padding: 8px 20px;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s;
        }
        .btn-primary-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        /* ===== BADGE STATUS ===== */
        .badge-hadir { background: #d4edda; color: #155724; }
        .badge-izin { background: #fff3cd; color: #856404; }
        .badge-sakit { background: #cce5ff; color: #004085; }
        .badge-alpha { background: #f8d7da; color: #721c24; }

        /* ===== RESPONSIVE ===== */
        .sidebar-collapsed .sidebar {
            margin-left: calc(-1 * var(--sidebar-width));
        }
        .sidebar-collapsed .main-content {
            margin-left: 0;
        }

        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            .sidebar.show {
                margin-left: 0;
            }
            .main-content {
                margin-left: 0;
            }
            .content-area {
                padding: 15px;
            }
        }

        /* ===== SCROLLBAR ===== */
        .sidebar::-webkit-scrollbar {
            width: 5px;
        }
        .sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
        }

        /* ===== WEBCAM / FACE RECOGNITION ===== */
        .webcam-container {
            background: #000;
            border-radius: 15px;
            overflow: hidden;
            position: relative;
            aspect-ratio: 4/3;
        }
        .webcam-container video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .webcam-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 200px;
            height: 200px;
            border: 3px solid rgba(102, 126, 234, 0.8);
            border-radius: 50%;
            box-shadow: 0 0 30px rgba(102, 126, 234, 0.3);
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="brand-icon">
                <i class="fas fa-face-smile-beam"></i>
            </div>
            <h5>Absensi Siswa</h5>
            <small>Face Recognition System</small>
        </div>
        <div class="sidebar-menu">
            <div class="menu-label">Menu Utama</div>
            <a href="dashboard.php" class="menu-item <?= $current_page === 'dashboard' ? 'active' : '' ?>">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="absensi.php" class="menu-item <?= $current_page === 'absensi' ? 'active' : '' ?>">
                <i class="fas fa-camera"></i> Absensi Face ID
            </a>
            <a href="absensi_manual.php" class="menu-item <?= $current_page === 'absensi_manual' ? 'active' : '' ?>">
                <i class="fas fa-clipboard-check"></i> Absensi Manual
            </a>

            <div class="menu-label">Data Master</div>
            <a href="siswa.php" class="menu-item <?= $current_page === 'siswa' ? 'active' : '' ?>">
                <i class="fas fa-user-graduate"></i> Data Siswa
            </a>
            <a href="kelas.php" class="menu-item <?= $current_page === 'kelas' ? 'active' : '' ?>">
                <i class="fas fa-school"></i> Data Kelas
            </a>
            <a href="mapel.php" class="menu-item <?= $current_page === 'mapel' ? 'active' : '' ?>">
                <i class="fas fa-book"></i> Mata Pelajaran
            </a>
            <a href="register_face.php" class="menu-item <?= $current_page === 'register_face' ? 'active' : '' ?>">
                <i class="fas fa-id-card"></i> Register Wajah
            </a>

            <div class="menu-label">Laporan</div>
            <a href="laporan.php" class="menu-item <?= $current_page === 'laporan' ? 'active' : '' ?>">
                <i class="fas fa-chart-bar"></i> Laporan Absensi
            </a>
            <a href="rekap.php" class="menu-item <?= $current_page === 'rekap' ? 'active' : '' ?>">
                <i class="fas fa-file-alt"></i> Rekap Absensi
            </a>

            <div class="menu-label">Pengaturan</div>
            <a href="pengaturan.php" class="menu-item <?= $current_page === 'pengaturan' ? 'active' : '' ?>">
                <i class="fas fa-cog"></i> Pengaturan
            </a>
            <!-- <a href="dokumentasi.php" class="menu-item <?= $current_page === 'dokumentasi' ? 'active' : '' ?>">
                <i class="fas fa-book"></i> Dokumentasi
            </a> -->
            <a href="logout.php" class="menu-item">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="toggle-sidebar" id="toggleSidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="page-title"><?= $page_title ?? 'Dashboard' ?></h5>
            </div>
            <div class="navbar-right">
                <div class="dropdown">
                    <div class="user-dropdown" data-bs-toggle="dropdown">
                        <div class="user-avatar">
                            <?= strtoupper(substr($_SESSION['admin_nama'] ?? 'A', 0, 1)) ?>
                        </div>
                        <div class="d-none d-md-block">
                            <div style="font-size:13px; font-weight:600; color:#2d3436;"><?= $_SESSION['admin_nama'] ?? 'Admin' ?></div>
                            <div style="font-size:11px; color:#636e72;">Administrator</div>
                        </div>
                        <i class="fas fa-chevron-down ms-2" style="font-size:12px; color:#636e72;"></i>
                    </div>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="pengaturan.php"><i class="fas fa-cog me-2"></i>Pengaturan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="content-area">
