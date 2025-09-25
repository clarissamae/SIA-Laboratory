<?php
session_start();

// Get posted data from previous form
$firstname     = $_POST['firstname'] ?? '';
$middlename    = $_POST['middlename'] ?? '';
$lastname      = $_POST['lastname'] ?? '';
$suffix        = $_POST['suffix'] ?? '';
$student_type  = $_POST['student_type'] ?? '';
$course        = $_POST['course'] ?? '';
$year_level    = $_POST['year_level'] ?? '';
$semester      = $_POST['semester'] ?? '';
$gender        = $_POST['gender'] ?? '';
$civil_status  = $_POST['civil_status'] ?? '';
$nationality   = $_POST['nationality'] ?? '';
$dob           = $_POST['dob'] ?? '';
$pob           = $_POST['pob'] ?? '';
$mobile        = $_POST['mobile'] ?? '';
$email         = $_POST['email'] ?? '';
$religion      = $_POST['religion'] ?? '';

$address   = $_POST['address'] ?? '';
$barangay  = $_POST['barangay'] ?? '';
$city      = $_POST['city'] ?? '';
$province  = $_POST['province'] ?? '';
$region    = $_POST['region'] ?? '';
$zipcode   = $_POST['zipcode'] ?? '';

$primary_school   = $_POST['primary_school'] ?? '';
$primary_grad     = $_POST['primary_grad'] ?? '';
$secondary_school = $_POST['secondary_school'] ?? '';
$secondary_grad   = $_POST['secondary_grad'] ?? '';
$strand           = $_POST['strand'] ?? '';
$achievement      = $_POST['achievement'] ?? '';

$father_lname      = $_POST['father_lname'] ?? '';
$father_fname      = $_POST['father_fname'] ?? '';
$father_mname      = $_POST['father_mname'] ?? '';
$father_address    = $_POST['father_address'] ?? '';
$father_mobile     = $_POST['father_mobile'] ?? '';
$father_occupation = $_POST['father_occupation'] ?? '';

$mother_lname      = $_POST['mother_lname'] ?? '';
$mother_fname      = $_POST['mother_fname'] ?? '';
$mother_mname      = $_POST['mother_mname'] ?? '';
$mother_address    = $_POST['mother_address'] ?? '';
$mother_mobile     = $_POST['mother_mobile'] ?? '';
$mother_occupation = $_POST['mother_occupation'] ?? '';

$guardian_lname        = $_POST['guardian_lname'] ?? '';
$guardian_fname        = $_POST['guardian_fname'] ?? '';
$guardian_mname        = $_POST['guardian_mname'] ?? '';
$guardian_address      = $_POST['guardian_address'] ?? '';
$guardian_mobile       = $_POST['guardian_mobile'] ?? '';
$guardian_occupation   = $_POST['guardian_occupation'] ?? '';
$guardian_relationship = $_POST['guardian_relationship'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Confirm Application</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f4f6f9;
            font-family: Arial, sans-serif;
        }
        .confirm-box {
            max-width: 950px;
            margin: 40px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 35px;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .logo img {
            max-height: 100px;
        }
        .school-name {
            font-size: 1.4rem;
            font-weight: 700;
            margin-top: 10px;
            color: #0d6efd;
        }
        .sub-text {
            font-size: 1rem;
            color: #555;
            margin-bottom: 30px;
        }
        .section-title {
            font-size: 1.15rem;
            font-weight: 600;
            color: #0d6efd;
            border-bottom: 2px solid #dee2e6;
            margin-top: 20px;
            margin-bottom: 12px;
            padding-bottom: 5px;
        }
        .info p {
            margin-bottom: 6px;
        }
        .btn-submit {
            font-size: 1.1rem;
            padding: 10px 25px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="confirm-box">
        <div class="logo">
            <img src="../IMAGES/NCST-logo.png" alt="NCST Logo">
            <div class="school-name">National College of Science and Technology</div>
            <p class="sub-text">Please confirm your application details before final submission.</p>
        </div>

        <div class="info">
            <div class="section-title">Personal Information</div>
            <p><strong>Full Name:</strong> <?php echo htmlspecialchars("$firstname $middlename $lastname $suffix"); ?></p>
            <p><strong>Student Type:</strong> <?php echo htmlspecialchars($student_type); ?></p>
            <p><strong>Course:</strong> <?php echo htmlspecialchars($course); ?> | <strong>Year:</strong> <?php echo htmlspecialchars($year_level); ?> | <strong>Semester:</strong> <?php echo htmlspecialchars($semester); ?></p>
            <p><strong>Gender:</strong> <?php echo htmlspecialchars($gender); ?> | <strong>Status:</strong> <?php echo htmlspecialchars($civil_status); ?></p>
            <p><strong>Nationality:</strong> <?php echo htmlspecialchars($nationality); ?></p>
            <p><strong>Date of Birth:</strong> <?php echo htmlspecialchars($dob); ?> | <strong>Place of Birth:</strong> <?php echo htmlspecialchars($pob); ?></p>
            <p><strong>Mobile:</strong> <?php echo htmlspecialchars($mobile); ?> | <strong>Email:</strong> <?php echo htmlspecialchars($email); ?></p>
            <p><strong>Religion:</strong> <?php echo htmlspecialchars($religion); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars("$address, $barangay, $city, $province, $region, $zipcode"); ?></p>

            <div class="section-title">Educational Background</div>
            <p><strong>Primary:</strong> <?php echo htmlspecialchars($primary_school); ?> (<?php echo htmlspecialchars($primary_grad); ?>)</p>
            <p><strong>Secondary:</strong> <?php echo htmlspecialchars($secondary_school); ?> (<?php echo htmlspecialchars($secondary_grad); ?>)</p>
            <p><strong>Strand:</strong> <?php echo htmlspecialchars($strand); ?> | <strong>Achievement:</strong> <?php echo htmlspecialchars($achievement); ?></p>

            <div class="section-title">Father's Information</div>
            <p><?php echo htmlspecialchars("$father_fname $father_mname $father_lname"); ?> - <?php echo htmlspecialchars($father_occupation); ?> (<?php echo htmlspecialchars($father_mobile); ?>)</p>

            <div class="section-title">Mother's Information</div>
            <p><?php echo htmlspecialchars("$mother_fname $mother_mname $mother_lname"); ?> - <?php echo htmlspecialchars($mother_occupation); ?> (<?php echo htmlspecialchars($mother_mobile); ?>)</p>

            <div class="section-title">Guardian Information</div>
            <p><?php echo htmlspecialchars("$guardian_fname $guardian_mname $guardian_lname"); ?> (<?php echo htmlspecialchars($guardian_relationship); ?>) - <?php echo htmlspecialchars($guardian_occupation); ?> (<?php echo htmlspecialchars($guardian_mobile); ?>)</p>
        </div>

        <form action="SubmitApplication.php" method="POST" class="text-center mt-4">
            <?php
            // Output hidden fields for ALL data
            foreach ($_POST as $key => $value) {
                echo '<input type="hidden" name="'.htmlspecialchars($key).'" value="'.htmlspecialchars($value).'">';
            }
            ?>
            <button type="submit" class="btn btn-primary btn-submit">Confirm and Submit</button>
        </form>
    </div>
</body>
</html>
