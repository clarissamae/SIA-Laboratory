<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../PHP/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

// Get counts dynamically from OnlineApplication table only
$pendingAdmission = $conn->query("SELECT COUNT(*) AS cnt FROM OnlineApplication WHERE status = 'pending'");
$admissionCount = $pendingAdmission->fetch_assoc()['cnt'] ?? 0;

// Count all applications (for demonstration)
$totalApplications = $conn->query("SELECT COUNT(*) AS cnt FROM OnlineApplication");
$totalCount = $totalApplications->fetch_assoc()['cnt'] ?? 0;

// Count approved applications
$approvedApplications = $conn->query("SELECT COUNT(*) AS cnt FROM OnlineApplication WHERE status = 'approved'");
$approvedCount = $approvedApplications->fetch_assoc()['cnt'] ?? 0;

// Count rejected applications
$rejectedApplications = $conn->query("SELECT COUNT(*) AS cnt FROM OnlineApplication WHERE status = 'rejected'");
$rejectedCount = $rejectedApplications->fetch_assoc()['cnt'] ?? 0;

$academicYear = date("Y");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NCST Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #1E3A8A;
            --secondary-blue: #2563EB;
            --accent-yellow: #FFD60A;
            --light-gray: #f8fafc;
            --dark-text: #1f2937;
            --medium-text: #4b5563;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--light-gray);
            color: var(--dark-text);
            margin: 0;
            padding: 0;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 260px;
            background-color: var(--primary-blue);
            color: white;
            display: flex;
            flex-direction: column;
            padding-top: 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
            z-index: 1000;
        }
        
        .school-brand {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 20px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 15px;
        }
        
        .school-logo {
            width: 80px;
            height: 80px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .school-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }
        
        .school-name {
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            color: white;
            margin-top: 10px;
        }
        
        .admin-info {
            padding: 15px 20px;
            margin: 10px 0;
            background-color: rgba(255,255,255,0.1);
            border-radius: 8px;
            margin: 0 15px 20px;
        }
        
        .admin-name {
            font-weight: 600;
            color: var(--accent-yellow);
            margin-bottom: 5px;
            font-size: 16px;
        }
        
        .admin-role {
            font-size: 13px;
            color: rgba(255,255,255,0.8);
        }
        
        .nav-menu {
            flex: 1;
            overflow-y: auto;
            padding: 0 15px;
        }
        
        .nav-item {
            margin-bottom: 8px;
        }
        
        .nav-link {
            color: white;
            padding: 12px 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            transition: all 0.3s;
            text-decoration: none;
        }
        
        .nav-link:hover, .nav-link.active {
            background-color: var(--secondary-blue);
            color: var(--accent-yellow);
        }
        
        .nav-link i {
            width: 20px;
            margin-right: 12px;
            font-size: 16px;
        }
        
        .logout-section {
            padding: 15px;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        /* Main Content Styles */
        .main-content {
            margin-left: 260px;
            padding: 25px 30px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--primary-blue);
            display: flex;
            align-items: center;
        }
        
        .page-title i {
            margin-right: 10px;
            color: var(--secondary-blue);
        }
        
        .welcome-text {
            color: var(--medium-text);
            font-size: 16px;
            margin-bottom: 25px;
        }
        
        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            border-left: 4px solid var(--secondary-blue);
            text-align: center;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0,0,0,0.1);
        }
        
        .stat-icon {
            width: 50px;
            height: 50px;
            background-color: rgba(37, 99, 235, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            color: var(--secondary-blue);
            font-size: 20px;
        }
        
        .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-blue);
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            color: var(--medium-text);
        }
        
        .stat-action {
            margin-top: 15px;
        }
        
        .btn-sm {
            padding: 5px 12px;
            font-size: 13px;
        }
        
        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }
            
            .school-brand, .admin-info, .nav-link span {
                display: none;
            }
            
            .nav-link {
                justify-content: center;
                padding: 15px;
            }
            
            .nav-link i {
                margin-right: 0;
                font-size: 18px;
            }
            
            .main-content {
                margin-left: 70px;
                padding: 20px;
            }
        }
        
        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .main-content {
                padding: 15px;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="school-brand">
            <div class="school-logo">
                <img src="../IMAGES/NCST-logo.png" alt="NCST Logo">
            </div>
            <div class="school-name">NCST Admin Portal</div>
        </div>
        
        <div class="admin-info">
            <div class="admin-name"><?php echo isset($_SESSION['admin']) ? htmlspecialchars($_SESSION['admin']) : 'Administrator'; ?></div>
            <div class="admin-role">System Administrator</div>
        </div>
        
        <div class="nav-menu">
            <div class="nav-item">
                <a href="admin_dashboard.php" class="nav-link active">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="profile.php" class="nav-link">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="admission_request.php" class="nav-link">
                    <i class="fas fa-user-graduate"></i>
                    <span>Admission</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="enrollment_request.php" class="nav-link">
                    <i class="fas fa-file-alt"></i>
                    <span>Enrollment</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-book"></i>
                    <span>Courses</span>
                </a>
            </div>
        </div>
        
        <div class="logout-section">
            <a href="../ADMIN/student_portal_login.php" class="nav-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Logout</span>
            </a>
        </div>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="page-header">
            <h1 class="page-title"><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
            <div class="date-display"><?php echo date('F j, Y'); ?></div>
        </div>
        
        <p class="welcome-text">Welcome back, <b><?php echo isset($_SESSION['admin']) ? htmlspecialchars($_SESSION['admin']) : 'Administrator'; ?></b>. Here's an overview of the system.</p>
        
        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user"></i>
                </div>
                <div class="stat-value">1</div>
                <div class="stat-label">Your Profile</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div class="stat-value"><?php echo $admissionCount; ?></div>
                <div class="stat-label">Pending Admissions</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-value"><?php echo $approvedCount; ?></div>
                <div class="stat-label">Approved Applications</div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-value"><?php echo $rejectedCount; ?></div>
                <div class="stat-label">Rejected Applications</div>
            </div>
        </div>
        
        <!-- Quick Stats Section -->
        <div class="table-container" style="background: white; border-radius: 10px; padding: 20px; box-shadow: 0 4px 6px rgba(0,0,0,0.05);">
            <h4 class="mb-4"><i class="fas fa-chart-pie"></i> Application Overview</h4>
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                        <span>Total Applications:</span>
                        <strong><?php echo $totalCount; ?></strong>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
                        <span>Pending Review:</span>
                        <strong><?php echo $admissionCount; ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Simple JavaScript for interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Update date display
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            const today = new Date();
            document.querySelector('.date-display').textContent = today.toLocaleDateString('en-US', options);
            
            // Add active class to clicked nav items - ONLY FOR DASHBOARD INTERNAL LINKS
            const navLinks = document.querySelectorAll('.nav-link');
            navLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    // Allow actual navigation for all links except # links
                    if (this.getAttribute('href') === '#' || this.getAttribute('href').startsWith('javascript:')) {
                        e.preventDefault();
                        
                        // Remove active class from all links
                        navLinks.forEach(l => l.classList.remove('active'));
                        
                        // Add active class to clicked link
                        this.classList.add('active');
                        
                        // Update page title based on selection
                        const pageTitle = document.querySelector('.page-title');
                        const pageName = this.querySelector('span').textContent;
                        const iconClass = this.querySelector('i').className;
                        pageTitle.innerHTML = `<i class="${iconClass}"></i> ${pageName}`;
                        
                        // Update welcome text
                        document.querySelector('.welcome-text').innerHTML = 
                            `You are viewing the <b>${pageName}</b> section.`;
                    }
                    // Let all other links (with actual URLs) navigate normally
                });
            });
        });
    </script>
</body>
</html>