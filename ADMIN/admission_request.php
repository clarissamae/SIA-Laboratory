<?php
session_start();
require_once '../PHP/db.php'; // adjust path

// Only admins should access
if (!isset($_SESSION['admin'])) {
    header("Location: admin_login.php");
    exit();
}

$adminName = isset($_SESSION['admin']) ? $_SESSION['admin'] : 'Guest';

// Fetch all online applications
$query = "SELECT * FROM OnlineApplication ORDER BY created_at DESC";
$result = $conn->query($query);

if (!$result) {
    die("Error fetching data: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admission Requests - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    .status-accepted {background-color:#d1fae5; color:#059669;}
    .status-rejected {background-color:#fee2e2; color:#dc2626;}
    
    /* Filter Styles */
    .filter-section {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        margin-bottom: 20px;
    }
    
    /* Action buttons */
    .action-buttons {
        display: flex;
        gap: 5px;
    }
    .btn-sm {
        padding: 4px 8px;
        font-size: 12px;
    }
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
        <div class="nav-item"><a href="#" class="nav-link active"><i class="fas fa-user-graduate"></i> Admission</a></div>
        <div class="nav-item"><a href="enrollment_request.php" class="nav-link"><i class="fas fa-file-alt"></i> Enrollment</a></div>
        <div class="nav-item"><a href="courses.php" class="nav-link"><i class="fas fa-book"></i><span>Courses</span></a></div>
    </div>
    <div class="logout-section">
        <a href="logout.php" class="nav-link"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="page-header">
        <h1 class="page-title"><i class="fas fa-user-graduate"></i> Admission Requests</h1>
        <div class="date-display"><?= date('F j, Y') ?></div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="row">
            <div class="col-md-3">
                <label for="statusFilter" class="form-label">Filter by Status</label>
                <select class="form-select" id="statusFilter">
                    <option value="">All Statuses</option>
                    <option value="Pending">Pending</option>
                    <option value="Accepted">Accepted</option>
                    <option value="Rejected">Rejected</option>
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
        <table class="table table-hover" id="admissionTable">
            <thead>
                <tr>
                    <th>Application ID</th>
                    <th>Full Name</th>
                    <th>Student Type</th>
                    <th>Course</th>
                    <th>Year Level</th>
                    <th>Semester</th>
                    <th>Email</th>
                    <th>Mobile</th>
                    <th>Created At</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['application_id'] ?></td>
                        <td>
                            <?= htmlspecialchars($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname'] . ' ' . $row['suffix']) ?>
                        </td>
                        <td><?= htmlspecialchars($row['student_type']) ?></td>
                        <td><?= htmlspecialchars($row['course']) ?></td>
                        <td><?= htmlspecialchars($row['year_level']) ?></td>
                        <td><?= htmlspecialchars($row['semester']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td><?= htmlspecialchars($row['mobile_number']) ?></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <span class="status-badge status-<?= strtolower(htmlspecialchars($row['status'])) ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <form method="POST" action="update_status.php" class="status-form" style="display:inline-block;">
                                    <input type="hidden" name="application_id" value="<?= $row['application_id'] ?>">
                                    <input type="hidden" name="status" value="Accepted">
                                    <button type="button" class="btn btn-success btn-sm confirm-btn">Accept</button>
                                </form>

                                <form method="POST" action="update_status.php" class="status-form" style="display:inline-block;">
                                    <input type="hidden" name="application_id" value="<?= $row['application_id'] ?>">
                                    <input type="hidden" name="status" value="Rejected">
                                    <button type="button" class="btn btn-danger btn-sm confirm-btn">Reject</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
// Confirmation SweetAlert
document.querySelectorAll('.confirm-btn').forEach(button => {
    button.addEventListener('click', function() {
        const form = this.closest('form');
        const status = form.querySelector('input[name="status"]').value;

        Swal.fire({
            title: `Are you sure?`,
            text: `Do you really want to ${status.toLowerCase()} this application?`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: (status === 'Accepted') ? '#198754' : '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: `Yes, ${status.toLowerCase()} it!`
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});

// Show success alert after update
<?php if (isset($_GET['status_updated'])): ?>
    Swal.fire({
        icon: "success",
        title: "Status Updated",
        text: "The application has been updated successfully!"
    });
<?php endif; ?>

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const table = document.getElementById('admissionTable');
    const statusFilter = document.getElementById('statusFilter');
    const searchInput = document.getElementById('searchInput');
    const rowsPerPage = document.getElementById('rowsPerPage');
    const resetFilters = document.getElementById('resetFilters');
    
    let currentPage = 1;
    let rowsPerPageValue = parseInt(rowsPerPage.value);
    
    function filterTable() {
        const statusValue = statusFilter.value.toLowerCase();
        const searchValue = searchInput.value.toLowerCase();
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        
        let visibleRows = 0;
        
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            const statusCell = row.cells[9]; // Status column
            const statusText = statusCell.textContent.toLowerCase();
            
            // Check if row matches status filter
            const statusMatch = statusValue === '' || statusText.includes(statusValue);
            
            // Check if row matches search filter
            let searchMatch = false;
            if (searchValue === '') {
                searchMatch = true;
            } else {
                for (let j = 0; j < row.cells.length - 1; j++) { // Exclude action column
                    if (row.cells[j].textContent.toLowerCase().includes(searchValue)) {
                        searchMatch = true;
                        break;
                    }
                }
            }
            
            // Show or hide row based on filters
            if (statusMatch && searchMatch) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        }
        
        // Update pagination if needed
        updatePagination(visibleRows);
    }
    
    function updatePagination(visibleRows) {
        // Simple pagination implementation
        // You can enhance this with a more sophisticated pagination system
        const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
        let visibleCount = 0;
        
        for (let i = 0; i < rows.length; i++) {
            if (rows[i].style.display !== 'none') {
                visibleCount++;
                if (visibleCount <= (currentPage - 1) * rowsPerPageValue || 
                    visibleCount > currentPage * rowsPerPageValue) {
                    rows[i].style.display = 'none';
                } else {
                    rows[i].style.display = '';
                }
            }
        }
        
        // You could add pagination controls here
    }
    
    // Event listeners for filters
    statusFilter.addEventListener('change', filterTable);
    searchInput.addEventListener('input', filterTable);
    rowsPerPage.addEventListener('change', function() {
        rowsPerPageValue = parseInt(this.value);
        currentPage = 1;
        filterTable();
    });
    
    resetFilters.addEventListener('click', function() {
        statusFilter.value = '';
        searchInput.value = '';
        rowsPerPage.value = '10';
        rowsPerPageValue = 10;
        currentPage = 1;
        filterTable();
    });
    
    // Initial filter
    filterTable();
});
</script>

</body>
</html>