<?php
session_start();
include '../PHP/db.php'; // database connection

// Make sure student is logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: student_portal_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Get student info for filtering sections
$stmt = $conn->prepare("SELECT course, year_level, semester FROM students WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();

$course = $student['course'];
$year_level = $student['year_level'];
$semester = $student['semester'];

// Fetch sections for this student
$stmt2 = $conn->prepare("SELECT * FROM sections WHERE course=? AND year_level=? AND semester=?");
$stmt2->bind_param("sss", $course, $year_level, $semester);
$stmt2->execute();
$sections_result = $stmt2->get_result();
$sections = $sections_result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Select Section</title>
    <link rel="stylesheet" href="../CSS/select_section.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>

<div class="container">
    <h2>Available Sections</h2>
    <button id="toggle-multi" class="btn-toggle">Select Multiple Subjects</button>

    <form action="registration_form.php" method="POST" id="multi-enroll-form">
        <?php if (count($sections) > 0): ?>
            <?php foreach ($sections as $section): ?>
                <?php
                // Decode subjects JSON safely
                $subjects = json_decode($section['subjects'], true);
                if (!is_array($subjects)) $subjects = [];
                ?>
                <div class="section-card">
                    <h3><?= htmlspecialchars($section['section_name']) ?> - 
                        <?= htmlspecialchars($section['course']) ?> 
                        <?= htmlspecialchars($section['year_level']) ?> 
                        (<?= htmlspecialchars($section['semester']) ?>)
                    </h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Subject</th>
                                <th>Day</th>
                                <th>Time</th>
                                <th>Select</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (count($subjects) > 0): ?>
                                <?php foreach ($subjects as $subj): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($subj['subject']) ?></td>
                                        <td><?= htmlspecialchars($subj['day']) ?></td>
                                        <td><?= htmlspecialchars($subj['start']) ?> - <?= htmlspecialchars($subj['end']) ?></td>
                                        <td>
                                            <input type="checkbox" name="subjects[]" 
                                                value="<?= htmlspecialchars($section['section_name'] . '|' . $subj['subject'] . '|' . $subj['day'] . '|' . $subj['start'] . '|' . $subj['end'] . '|' . ($subj['units'] ?? 3)) ?>" 
                                                class="multi-checkbox" style="display:none;">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">⚠ No subjects available in this section</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                    <button type="submit" formaction="registration_form.php" class="btn-enroll" data-section-name="<?= htmlspecialchars($section['section_name']) ?>">Enroll Section</button>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No sections available for your course/year/semester.</p>
        <?php endif; ?>

        <div style="text-align: right; margin-bottom: 10px;">
            <a href="student_dashboard.php" style="padding: 10px 20px; background-color: #0056b3; color: #fff; border: none; border-radius: 5px; cursor: pointer; text-decoration: none;">
                Back
            </a>
        </div>
        <button type="submit" id="global-enroll-btn" class="btn-global-enroll" style="display:none;">Enroll Selected Subjects</button>
    </form>
</div>

<script>
const toggleBtn = document.getElementById('toggle-multi');
const checkboxes = document.querySelectorAll('.multi-checkbox');
const enrollBtns = document.querySelectorAll('.btn-enroll');
const globalEnrollBtn = document.getElementById('global-enroll-btn');

toggleBtn.addEventListener('click', function() {
    if (checkboxes.length === 0) {
        Swal.fire('No subjects', 'There are no subjects to select.', 'info');
        return;
    }
    const isActive = checkboxes[0].style.display === 'table-cell';
    checkboxes.forEach(cb => cb.style.display = isActive ? 'none' : 'table-cell');
    enrollBtns.forEach(btn => btn.style.display = isActive ? 'inline-block' : 'none');
    globalEnrollBtn.style.display = isActive ? 'none' : 'block';
});

enrollBtns.forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const sectionName = this.dataset.sectionName;
        Swal.fire({
            title: 'Confirm Enrollment?',
            text: `You are about to enroll in ${sectionName} section.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, enroll',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Submit form with only this section's subjects
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'registration_form.php';
                document.body.appendChild(form);
                document.querySelectorAll(`.multi-checkbox`).forEach(cb => {
                    if (cb.value.startsWith(sectionName + '|')) {
                        const input = document.createElement('input');
                        input.type = 'hidden';
                        input.name = 'subjects[]';
                        input.value = cb.value;
                        form.appendChild(input);
                    }
                });
                form.submit();
            }
        });
    });
});

globalEnrollBtn.addEventListener('click', function(e) {
    e.preventDefault();
    const selected = Array.from(document.querySelectorAll('.multi-checkbox:checked'));
    if(selected.length === 0){
        Swal.fire('No subjects selected', 'Please select at least one subject.', 'error');
        return;
    }
    Swal.fire({
        title: 'Confirm Enrollment?',
        text: "You are about to enroll in selected subjects.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, enroll',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if(result.isConfirmed){
            document.getElementById('multi-enroll-form').submit();
        }
    });
});
</script>

</body>
</html>