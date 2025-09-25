<?php
session_start();
include '../PHP/db.php';

if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$adminName = isset($_SESSION['admin']) ? $_SESSION['admin'] : 'Guest';

// Handle approve/reject action
if (isset($_POST['action']) && isset($_POST['id'])) {
    $id = $_POST['id'];
    $status = $_POST['action'] === 'approve' ? 'approved' : 'rejected';
    $stmt = $conn->prepare("UPDATE registrations SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    echo json_encode(['success' => true]);
    exit();
}

// Fetch all enrollment requests
$query = "SELECT * FROM registrations ORDER BY created_at DESC";
$result = $conn->query($query);

// Function to convert year level number to text
function yearLevelText($year) {
    switch($year) {
        case 1: return '1st Year';
        case 2: return '2nd Year';
        case 3: return '3rd Year';
        case 4: return '4th Year';
        default: return $year . ' Year';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Enrollment Requests - Admin</title>
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
}
.sidebar .school-brand {text-align:center; padding:0 20px 20px;}
.sidebar .school-logo img {width:80px; height:80px; object-fit:contain;}
.sidebar .school-name {font-weight:bold; margin-top:10px;}
.sidebar .admin-info {padding:15px; margin:0 15px 20px; background: rgba(255,255,255,0.1); border-radius:8px;}
.sidebar .admin-name {font-weight:600; color: var(--accent-yellow);}
.sidebar .admin-role {font-size:13px; color: rgba(255,255,255,0.8);}
.nav-menu {flex:1; overflow-y:auto; padding:0 15px;}
.nav-item {margin-bottom:8px;}
.nav-link {color:white; padding:12px 15px; border-radius:8px; display:flex; align-items:center; text-decoration:none;}
.nav-link:hover, .nav-link.active {background-color: var(--secondary-blue); color: var(--accent-yellow);}
.nav-link i {width:20px; margin-right:12px;}
.logout-section {padding:15px; border-top:1px solid rgba(255,255,255,0.1);}

/* Main Content */
.main-content {margin-left:260px; padding:25px 30px;}
.page-header {display:flex; justify-content:space-between; align-items:center; margin-bottom:25px; border-bottom:1px solid #e5e7eb;}
.page-title {font-size:24px; font-weight:600; color:var(--primary-blue);}
.page-title i {margin-right:10px; color: var(--secondary-blue);}

/* Table */
.table-container {background:white; border-radius:10px; padding:20px; box-shadow:0 4px 6px rgba(0,0,0,0.05); overflow-x:auto;}
.status-badge {padding:5px 10px; border-radius:20px; font-size:12px; font-weight:600;}
.status-pending {background-color:#fef3c7; color:#d97706;}
.status-approved {background-color:#d1fae5; color:#059669;}
.status-rejected {background-color:#fee2e2; color:#dc2626;}
.view-btn {cursor:pointer; color: var(--secondary-blue);}
.modal-content {border-radius:10px;}
.modal-header {background-color: var(--primary-blue); color:white; border-radius:10px 10px 0 0;}
.modal-actions {margin-top:15px; display:flex; gap:10px;}
.approve {background-color:#059669; color:white; border:none; padding:8px 15px; border-radius:5px;}
.reject {background-color:#dc2626; color:white; border:none; padding:8px 15px; border-radius:5px;}

 /* Filter Styles */
        .filter-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }

/* Modal */
.modal {display:none; position:fixed; z-index:1000; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.4);}
.modal-content {background:white; margin:50px auto; padding:20px; border-radius:10px; max-width:800px;}

/* Close button */
.close {color:#aaa; float:right; font-size:28px; font-weight:bold; cursor:pointer;}
.close:hover {color:black;}
</style>
</head>
<body>

<div class="sidebar">
    <div class="school-brand">
        <div class="school-logo"><img src="../IMAGES/NCST-logo.png" alt="Logo"></div>
        <div class="school-name">NCST Admin Portal</div>
    </div>
    <div class="admin-info">
        <div class="admin-name"><?= htmlspecialchars($adminName) ?></div>
        <div class="admin-role">System Administrator</div>
    </div>
    <div class="nav-menu">
        <div class="nav-item"><a href="admin_dashboard.php" class="nav-link"><i class="fas fa-home"></i> Dashboard</a></div>
        <div class="nav-item"><a href="profile.php" class="nav-link"><i class="fas fa-user"></i> Profile</a></div>
        <div class="nav-item"><a href="admission_request.php" class="nav-link"><i class="fas fa-user-graduate"></i> Admission</a></div>
        <div class="nav-item"><a href="#" class="nav-link active"><i class="fas fa-file-alt"></i> Enrollment</a></div>
        <div class="nav-item"><a href="courses.php" class="nav-link"><i class="fas fa-book"></i><span>Courses</span></a></div>
    </div>
    <div class="logout-section">
        <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">


    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-file-alt"></i> Enrollment Requests</h1>
        <div class="date-display"><?= date('F j, Y') ?></div>
    </div>

    <!-- Filter Section -->
        <div class="filter-section">
            <div class="row">
                <div class="col-md-3">
                    <label for="statusFilter" class="form-label">Filter by Status</label>
                    <select class="form-select" id="statusFilter">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="approved">Approved</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="searchInput" class="form-label">Search</label>
                    <input type="text" class="form-control" id="searchInput" placeholder="Search by name, email, or course...">
                </div>
                <div class="col-md-2">
                    <label for="rowsPerPage" class="form-label">Rows per page</label>
                    <select class="form-select" id="rowsPerPage">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button class="btn btn-secondary w-100" id="resetFilters">Reset Filters</button>
                </div>
            </div>
        </div>

    <div class="table-container">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Full Name</th>
                    <th>Course</th>
                    <th>Year Level</th>
                    <th>Semester</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['student_id']) ?></td>
                            <td><?= htmlspecialchars($row['fullname']) ?></td>
                            <td><?= htmlspecialchars($row['course']) ?></td>
                            <td><?= htmlspecialchars(yearLevelText($row['year_level'])) ?></td>
                            <td><?= htmlspecialchars($row['semester']) ?></td>
                            <td>
                                <span class="status-badge status-<?= htmlspecialchars($row['status']) ?>">
                                    <?= ucfirst(htmlspecialchars($row['status'])) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info view-btn" data-student='<?= json_encode($row) ?>'>
                                    <i class="fas fa-eye"></i> View
                                </button>
                            </td>

                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center">No enrollment requests found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal -->
<div id="viewModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Enrollment Details</h3>
        <div id="student-info"></div>
        <table class="table table-bordered" id="subjects-table">
            <thead>
                <tr>
                    <th>Section</th>
                    <th>Subject</th>
                    <th>Day</th>
                    <th>Time</th>
                    <th>Units</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
        <p id="total-units"><strong>Total Units:</strong> 0</p>
        <div class="modal-actions">
            <button id="approve-btn" class="approve">Approve</button>
            <button id="reject-btn" class="reject">Reject</button>
        </div>
    </div>
</div>

<script>
const modal = document.getElementById('viewModal');
const studentInfoDiv = document.getElementById('student-info');
const subjectsTableBody = document.querySelector('#subjects-table tbody');
const totalUnitsP = document.getElementById('total-units');
const closeBtn = document.querySelector('.close');
let currentId = null;

document.querySelectorAll('.view-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        const student = JSON.parse(btn.getAttribute('data-student'));
        currentId = student.id;
        function yearLevelTextJS(year) {
            switch(parseInt(year)){
                case 1: return '1st Year';
                case 2: return '2nd Year';
                case 3: return '3rd Year';
                case 4: return '4th Year';
                default: return year + ' Year';
            }
        }
        studentInfoDiv.innerHTML = `
            <p><strong>Full Name:</strong> ${student.fullname}</p>
            <p><strong>Student ID:</strong> ${student.student_id}</p>
            <p><strong>Course:</strong> ${student.course}</p>
            <p><strong>Year Level:</strong> ${yearLevelTextJS(student.year_level)}</p>
            <p><strong>Semester:</strong> ${student.semester}</p>
            <p><strong>Status:</strong> ${student.status}</p>
        `;
        subjectsTableBody.innerHTML = '';
        const subjects = JSON.parse(student.subjects || '[]');
        let totalUnits = 0;
        subjects.forEach(subj => {
            totalUnits += parseFloat(subj.units || 0);
            subjectsTableBody.innerHTML += `
                <tr>
                    <td>${subj.section_name || ''}</td>
                    <td>${subj.subject_name || ''}</td>
                    <td>${subj.day || ''}</td>
                    <td>${subj.start_time || ''} - ${subj.end_time || ''}</td>
                    <td>${subj.units || 0}</td>
                </tr>
            `;
        });
        totalUnitsP.innerHTML = `<strong>Total Units:</strong> ${totalUnits}`;
        modal.style.display = 'block';
    });
});

closeBtn.onclick = () => modal.style.display = 'none';
window.onclick = e => { if(e.target === modal) modal.style.display = 'none'; };

function updateStatus(action) {
    if (!currentId) return;
    fetch('', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: `id=${currentId}&action=${action}`
    }).then(res => res.json())
      .then(data => { if(data.success) location.reload(); });
}

document.getElementById('approve-btn').addEventListener('click', () => updateStatus('approve'));
document.getElementById('reject-btn').addEventListener('click', () => updateStatus('reject'));
</script>

</body>
</html>
