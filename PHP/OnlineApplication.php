<?php
// DB connection
session_start();
$host = "localhost";
$user = "root"; // change if different
$pass = "";     // change if you set a password
$db   = "EnrollmentSystem";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname     = $_POST['firstname'];
    $lastname      = $_POST['lastname'];
    $student_type  = $_POST['student_type'];
    $course        = $_POST['course'];
    $year_level    = $_POST['year_level'];
    $semester      = $_POST['semester'];
    $mobile_number = $_POST['mobile_number'];
    $email         = $_POST['email'];

    // Place = complete address combined
    $place = $_POST['address'] . ', ' . $_POST['barangay'] . ', ' . $_POST['city'] . ', ' . $_POST['province'] . ', ' . $_POST['region'] . ', ' . $_POST['zip'];

    $stmt = $conn->prepare("INSERT INTO OnlineApplication 
        (firstname, lastname, student_type, course, year_level, semester, mobile_number, email, place) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $firstname, $lastname, $student_type, $course, $year_level, $semester, $mobile_number, $email, $place);

    if ($stmt->execute()) {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                  Swal.fire({
                    title: 'Success!',
                    text: 'Your application has been submitted successfully.',
                    icon: 'success',
                    confirmButtonColor: '#003366'
                  }).then(() => {
                    window.location.href = 'index.php';
                  });
                });
              </script>";
    } else {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                  Swal.fire({
                    title: 'Error!',
                    text: 'Something went wrong. Please try again.',
                    icon: 'error',
                    confirmButtonColor: '#d33'
                  });
                });
              </script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>NCST Online Application</title>
  <link rel="stylesheet" href="../CSS/OnlineApplication.css">
  <!-- Google Places API -->
  <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>
</head>
<body>

<div class="form-wrapper">
  <div class="header">
    <img src="../IMAGES/NCST-logo.png" alt="NCST Logo" class="logo">
    <h1>National College of Science & Technology</h1>
    <h2>Online Admission Application</h2>
  </div>

  <form action="ConfirmApplication.php" method="POST" class="application-form">
    <!-- Student Type -->
    <fieldset>
      <legend>Student Type</legend>
      <label for="student_type">Type of Student <span style="color:red">*</span></label>
      <select name="student_type" id="student_type" required>
        <option value="">-- Select Student Type --</option>
        <option <?= (($_POST['student_type'] ?? '') == 'Freshman') ? 'selected' : '' ?>>Freshman</option>
        <option <?= (($_POST['student_type'] ?? '') == 'Transferee') ? 'selected' : '' ?>>Transferee</option>
        <option <?= (($_POST['student_type'] ?? '') == 'Second Courser') ? 'selected' : '' ?>>Second Courser</option>
      </select>
    </fieldset>

    <!-- Academic Information -->
    <fieldset>
      <legend>Academic Information</legend>
      <label>Desired Course <span style="color:red">*</span></label>
      <select name="course" required>
        <option <?= (($_POST['course'] ?? '') == 'BS in Information Technology') ? 'selected' : '' ?>>BS in Information Technology</option>
        <option <?= (($_POST['course'] ?? '') == 'BS in Computer Engineering') ? 'selected' : '' ?>>BS in Computer Engineering</option>
        <option <?= (($_POST['course'] ?? '') == 'BS in Business Administration') ? 'selected' : '' ?>>BS in Business Administration</option>
        <option <?= (($_POST['course'] ?? '') == 'BS in Education') ? 'selected' : '' ?>>BS in Education</option>
      </select>

      <div class="grid-2">
        <div>
          <label>Year Level <span style="color:red">*</span></label>
          <select name="year_level" required>
            <option value="">-- Select --</option>
            <option <?= (($_POST['year_level'] ?? '') == '1st Year') ? 'selected' : '' ?>>1st Year</option>
            <option <?= (($_POST['year_level'] ?? '') == '2nd Year') ? 'selected' : '' ?>>2nd Year</option>
            <option <?= (($_POST['year_level'] ?? '') == '3rd Year') ? 'selected' : '' ?>>3rd Year</option>
            <option <?= (($_POST['year_level'] ?? '') == '4th Year') ? 'selected' : '' ?>>4th Year</option>
          </select>
        </div>
        <div>
          <label>Semester <span style="color:red">*</span></label>
          <select name="semester" required>
            <option value="">-- Select --</option>
            <option <?= (($_POST['semester'] ?? '') == '1st Semester') ? 'selected' : '' ?>>1st Semester</option>
            <option <?= (($_POST['semester'] ?? '') == '2nd Semester') ? 'selected' : '' ?>>2nd Semester</option>
            <option <?= (($_POST['semester'] ?? '') == 'Summer') ? 'selected' : '' ?>>Summer</option>
          </select>
        </div>
      </div>
    </fieldset>

    <!-- Personal Information -->
    <fieldset>
      <legend>Personal Information</legend>
      <div class="grid-3">
        <div>
          <label>Last Name <span style="color:red">*</span></label>
          <input type="text" name="lastname" value="<?= $_SESSION['lastname'] ?? '' ?>" required>
        </div>
        <div>
          <label>First Name <span style="color:red">*</span></label>
          <input type="text" name="firstname" value="<?= $_SESSION['firstname'] ?? '' ?>" required>
        </div>
        <div>
          <label>Middle Name</label>
          <input type="text" name="middlename">
        </div>
      </div>

      <label>Suffix (e.g., Jr.)</label>
      <input type="text" name="suffix">

      <div class="grid-2">
        <div>
          <label>Zip Code</label>
          <input type="text" name="zipcode" placeholder="e.g., 4100">
        </div>
        <div>
          <label>Mobile Number <span style="color:red">*</span></label>
          <input type="text" name="mobile" placeholder="e.g., 09123456789"  pattern="^09[0-9]{9}$" 
       maxlength="11"
       minlength="11" 
       oninput="this.value = this.value.replace(/[^0-9]/g, '');"
       required>
        </div>
      </div>

      <div class="grid-4">
        <div>
          <label>Region <span style="color:red">*</span></label>
          <select name="region" id="region" required>
            <option value="">-- Select Region --</option>
          </select>
        </div>
        <div>
          <label>Province <span style="color:red">*</span></label>
          <select name="province" id="province" required>
            <option value="">-- Select Province --</option>
          </select>
        </div>
        <div>
          <label>City/Municipality <span style="color:red">*</span></label>
          <select name="city" id="city" required>
            <option value="">-- Select City --</option>
          </select>
        </div>
        <div>
          <label>Barangay <span style="color:red">*</span></label>
          <select name="barangay" id="barangay" required>
            <option value="">-- Select Barangay --</option>
          </select>
        </div>
      </div>

      <div class="grid-3">
        <div>
          <label>Gender <span style="color:red">*</span></label>
          <select name="gender" required>
            <option value="">-- Select --</option>
            <option <?= (($_POST['gender'] ?? '') == 'Male') ? 'selected' : '' ?>>Male</option>
            <option <?= (($_POST['gender'] ?? '') == 'Female') ? 'selected' : '' ?>>Female</option>
            <option <?= (($_POST['gender'] ?? '') == 'Prefer not to say') ? 'selected' : '' ?>>Prefer not to say</option>
          </select>
        </div>
        <div>
          <label>Civil Status <span style="color:red">*</span></label>
          <select name="civil_status" required>
            <option value="">-- Select --</option>
            <option <?= (($_POST['civil_status'] ?? '') == 'Single') ? 'selected' : '' ?>>Single</option>
            <option <?= (($_POST['civil_status'] ?? '') == 'Married') ? 'selected' : '' ?>>Married</option>
            <option <?= (($_POST['civil_status'] ?? '') == 'Widowed') ? 'selected' : '' ?>>Widowed</option>
          </select>
        </div>
        <div>
          <label>Nationality <span style="color:red">*</span></label>
          <input type="text" name="nationality" required>
        </div>
      </div>

      <div class="grid-2">
        <div>
          <label>Date of Birth <span style="color:red">*</span></label>
          <input type="date" name="dob" required>
        </div>
        <div>
          <label>Place of Birth <span style="color:red">*</span></label>
          <input type="text" name="pob" required>
        </div>
      </div>

      <div class="grid-2">
        <div>
          <label>Email Address <span style="color:red">*</span></label>
          <input type="email" name="email" required>
        </div>
        <div>
          <label>Religion</label>
          <input type="text" name="religion">
        </div>
      </div>
    </fieldset>

    <!-- Educational Background -->
    <fieldset>
      <legend>Educational Background</legend>
      <div class="grid-2">
        <div>
          <label>Primary School <span style="color:red">*</span></label>
          <input type="text" name="primary_school" required>
        </div>
        <div>
          <label>Year Graduated</label>
          <input type="text" name="primary_grad" placeholder="e.g. 2016">
        </div>
      </div>

      <div class="grid-2">
        <div>
          <label>Secondary School <span style="color:red">*</span></label>
          <input type="text" name="secondary_school" required>
        </div>
        <div>
          <label>Year Graduated</label>
          <input type="text" name="secondary_grad" placeholder="e.g. 2022">
        </div>
      </div>

      <div class="grid-2">
        <div>
          <label>Strand Finished</label>
          <select name="strand">
            <option value="">-- Select Strand --</option>
            <option>STEM</option>
            <option>ABM</option>
            <option>HUMSS</option>
            <option>GAS</option>
            <option>TVL</option>
          </select>
        </div>
        <div>
          <label>Academic Achievement</label>
          <select name="achievement">
            <option value="">-- Select Achievement --</option>
            <option>With Honors</option>
            <option>With High Honors</option>
            <option>With Highest Honors</option>
          </select>
        </div>
      </div>
    </fieldset>

    <!-- Family Information -->
    <fieldset>
      <legend>Father's Information (Optional)</legend>
      <div class="grid-3">
        <input type="text" name="father_lname" placeholder="Last Name">
        <input type="text" name="father_fname" placeholder="First Name">
        <input type="text" name="father_mname" placeholder="Middle Name">
      </div>
      <input type="text" name="father_address" placeholder="Complete Address">
      <div class="grid-2">
        <input type="text" name="father_mobile" placeholder="Mobile No">
        <input type="text" name="father_occupation" placeholder="Occupation">
      </div>
    </fieldset>

    <fieldset>
      <legend>Mother's Information (Optional)</legend>
      <div class="grid-3">
        <input type="text" name="mother_lname" placeholder="Last Name">
        <input type="text" name="mother_fname" placeholder="First Name">
        <input type="text" name="mother_mname" placeholder="Middle Name">
      </div>
      <input type="text" name="mother_address" placeholder="Complete Address">
      <div class="grid-2">
        <input type="text" name="mother_mobile" placeholder="Mobile No">
        <input type="text" name="mother_occupation" placeholder="Occupation">
      </div>
    </fieldset>

    <fieldset>
      <legend>Guardian's Information <span style="color:red">*</span></legend>
      <div class="grid-3">
        <input type="text" name="guardian_lname" placeholder="Last Name" required>
        <input type="text" name="guardian_fname" placeholder="First Name" required>
        <input type="text" name="guardian_mname" placeholder="Middle Name">
      </div>
      <input type="text" name="guardian_address" placeholder="Complete Address" required>
      <div class="grid-3">
        <input type="text" name="guardian_mobile" placeholder="Mobile No"  pattern="^09[0-9]{9}$" 
       maxlength="11"
       minlength="11"
       oninput="this.value = this.value.replace(/[^0-9]/g, '');"
        required>
        <input type="text" name="guardian_occupation" placeholder="Occupation" required>
        <input type="text" name="guardian_relationship" placeholder="Relationship" required>
      </div>
    </fieldset>

    <!-- Buttons -->
    <div class="form-actions">
      <a href="index.php" class="btn-back">Back</a>
      <button type="submit" class="btn-submit">Submit Application</button>
    </div>
  </form>

</div>

<script>
    // Google Places
    function initAutocomplete() {
    var input = document.getElementById('address');
    new google.maps.places.Autocomplete(input);
    }
    google.maps.event.addDomListener(window, 'load', initAutocomplete);


    // Load Regions
    document.addEventListener("DOMContentLoaded", function() {
    fetch("https://psgc.gitlab.io/api/regions/")
        .then(res => res.json())
        .then(data => {
        let regionSelect = document.getElementById("region");
        data.forEach(region => {
            let opt = document.createElement("option");
            opt.value = region.code;
            opt.textContent = region.name;
            regionSelect.appendChild(opt);
        });
        });
    });

    // Provinces
    document.getElementById("region").addEventListener("change", function() {
    let code = this.value;
    let province = document.getElementById("province");
    province.innerHTML = "<option value=''>-- Select Province --</option>";
    document.getElementById("city").innerHTML = "<option value=''>-- Select City --</option>";
    document.getElementById("barangay").innerHTML = "<option value=''>-- Select Barangay --</option>";

    if(code) {
        fetch(`https://psgc.gitlab.io/api/regions/${code}/provinces/`)
        .then(res => res.json())
        .then(data => {
            data.forEach(p => {
            let opt = document.createElement("option");
            opt.value = p.code;
            opt.textContent = p.name;
            province.appendChild(opt);
            });
        });
    }
    });

    // Cities
    document.getElementById("province").addEventListener("change", function() {
    let code = this.value;
    let city = document.getElementById("city");
    city.innerHTML = "<option value=''>-- Select City --</option>";
    document.getElementById("barangay").innerHTML = "<option value=''>-- Select Barangay --</option>";

    if(code) {
        fetch(`https://psgc.gitlab.io/api/provinces/${code}/cities-municipalities/`)
        .then(res => res.json())
        .then(data => {
            data.forEach(c => {
            let opt = document.createElement("option");
            opt.value = c.code;
            opt.textContent = c.name;
            city.appendChild(opt);
            });
        });
    }
    });

    // Barangays
    document.getElementById("city").addEventListener("change", function() {
    let code = this.value;
    let brgy = document.getElementById("barangay");
    brgy.innerHTML = "<option value=''>-- Select Barangay --</option>";

    if(code) {
        fetch(`https://psgc.gitlab.io/api/cities-municipalities/${code}/barangays/`)
        .then(res => res.json())
        .then(data => {
            data.forEach(b => {
            let opt = document.createElement("option");
            opt.value = b.code;
            opt.textContent = b.name;
            brgy.appendChild(opt);
            });
        });
    }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.querySelector(".application-form").addEventListener("submit", function(e) {
  e.preventDefault();

  Swal.fire({
    title: "Are you sure?",
    text: "Do you want to review your application before final submission?",
    icon: "question",
    showCancelButton: true,
    confirmButtonColor: "#003366",
    cancelButtonColor: "#6c757d",
    confirmButtonText: "Yes, review",
    cancelButtonText: "Cancel"
  }).then((result) => {
    if (result.isConfirmed) {
      this.submit(); // go to ConfirmApplication.php
    }
  });
});
</script>

</script>


</body>
</html>


