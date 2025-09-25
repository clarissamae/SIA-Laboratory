<?php
session_start();
include '../PHP/db.php';

if (!isset($_SESSION['student_id'])) {
    header("Location: student_login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch latest registration safely
$registration = null;
$stmt = $conn->prepare("SELECT * FROM registrations WHERE student_id=? ORDER BY created_at DESC LIMIT 1");
if ($stmt) {
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $registration = $result ? $result->fetch_assoc() : null;
    $stmt->close();
}
// Decode subjects JSON into array
$subjects = [];
if ($registration && !empty($registration['subjects'])) {
    $decoded = json_decode($registration['subjects'], true);
    if (is_array($decoded)) {
        $subjects = $decoded;
    }
}

function yearLevelText($year) {
    switch($year) {
        case 1: return '1st Year';
        case 2: return '2nd Year';
        case 3: return '3rd Year';
        case 4: return '4th Year';
        default: return $year . ' Year';
    }
}

// Decode subjects
$subjects = [];
if ($registration && isset($registration['subjects'])) {
    $decoded = json_decode($registration['subjects'], true);
    $subjects = is_array($decoded) ? $decoded : [];
}

// Calculate total units and tuition
$totalUnits = 0;
$cashTotal = 0;
foreach ($subjects as &$subj) {
    $subjName = $subj['subject_name'] ?? '';
    if ($subjName !== '') {
        $subjStmt = $conn->prepare("SELECT units, price_per_unit FROM subjects WHERE subject_name=? LIMIT 1");
        if ($subjStmt) {
            $subjStmt->bind_param("s", $subjName);
            $subjStmt->execute();
            $subjResult = $subjStmt->get_result();
            $subjectData = $subjResult ? $subjResult->fetch_assoc() : null;
            $subjStmt->close();

            $subjUnits = $subjectData['units'] ?? ($subj['units'] ?? 0);
            $pricePerUnit = $subjectData['price_per_unit'] ?? 0;

            $subj['units'] = $subjUnits;
            $subj['price_per_unit'] = $pricePerUnit;

            $totalUnits += $subjUnits;
            $cashTotal += $subjUnits * $pricePerUnit;
        }
    }
}

// Installment fee
$installmentCharge = 1037;
$installmentTotal = $cashTotal + $installmentCharge;

// Fetch latest payment
$payment = null;
if ($registration && isset($registration['id'])) {
    $paymentStmt = $conn->prepare("SELECT * FROM student_payments WHERE registration_id=? ORDER BY payment_date DESC LIMIT 1");
    if ($paymentStmt) {
        $paymentStmt->bind_param("i", $registration['id']);
        $paymentStmt->execute();
        $paymentResult = $paymentStmt->get_result();
        $payment = $paymentResult ? $paymentResult->fetch_assoc() : null;
        $paymentStmt->close();
    }

    // Sum of all payments
    $sumStmt = $conn->prepare("SELECT SUM(amount) as total_paid FROM student_payments WHERE registration_id=?");
    $sumStmt->bind_param("i", $registration['id']);
    $sumStmt->execute();
    $sumResult = $sumStmt->get_result();
    $paymentsData = $sumResult ? $sumResult->fetch_assoc() : null;
    $paymentsTotal = $paymentsData['total_paid'] ?? 0;
    $sumStmt->close();

    $balance = $cashTotal - $paymentsTotal;
    if ($balance < 0) $balance = 0;
} else {
    $paymentsTotal = 0;
    $balance = $cashTotal;
}

// Check if there are any successful payments
$hasSuccessfulPayments = false;
if ($registration && isset($registration['id'])) {
    $paymentCheckStmt = $conn->prepare("SELECT COUNT(*) as payment_count FROM student_payments WHERE registration_id = ?");
    $paymentCheckStmt->bind_param("i", $registration['id']);
    $paymentCheckStmt->execute();
    $paymentCheckResult = $paymentCheckStmt->get_result();
    $paymentCountData = $paymentCheckResult->fetch_assoc();
    $hasSuccessfulPayments = ($paymentCountData['payment_count'] > 0);
    $paymentCheckStmt->close();
}

// Payment button state
$payButtonClass = 'btn-success';
$payButtonDisabled = '';
$payButtonTitle = '💳 Pay Now';

if(!$registration || $registration['status'] !== 'approved'){
    $payButtonClass = 'btn-secondary';
    $payButtonDisabled = 'disabled';
    $payButtonTitle = 'Payment is disabled until registration is approved';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Registration</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background: #f1f5f9; font-family: 'Segoe UI', sans-serif; color: #1e293b; }
.container { max-width: 900px; margin: 40px auto 60px auto; padding: 20px; }
.notification { padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; position: relative; font-weight: 500; }
.notification.pending {background-color:#fef3c7; color:#92400e; border-left:5px solid #f59e0b;}
.notification.approved {background-color:#dbeafe; color:#1e40af; border-left:5px solid #3b82f6;}
.notification .close-btn { position:absolute; top:6px; right:12px; cursor:pointer; font-weight:bold; font-size:18px; }
.table-container {background:white; padding:25px 20px 20px 20px; border-radius:10px; box-shadow:0 4px 6px rgba(0,0,0,0.05);}
.form-header {text-align: center; margin-bottom: 20px;}
.form-header img {width: 80px; margin-bottom: 5px;}
.form-header h2 {color: #1d4ed8; font-weight: 600; margin-bottom: 0;}
.form-header p {color: #fbbf24; font-weight: 500; margin:0;}
.student-info p {margin-bottom: 6px; font-size: 15px;}
.student-info strong {width: 140px; display:inline-block; color:#1d4ed8;}
.table-bordered th {background-color: #fff; color: #000; font-weight: 600;}
.table-bordered td {text-align:center;}
.total-units {text-align:right; font-weight:600; margin-top:8px;}
.payment-details p {margin: 6px 0; font-size: 15px;}
.payment-details hr {border-top: 1px solid #3b82f6; margin: 8px 0;}
.btn-action {color: white; border: none; padding: 10px 20px; border-radius: 5px; font-size: 15px; min-width: 180px; text-align: center; cursor: pointer; margin-top: 10px;}
.btn-back { background-color: #fbbf24; } .btn-back:hover { background-color: #f59e0b; }
.btn-primary { background-color: #1d4ed8; } .btn-primary:hover { background-color: #2563eb; }
.btn-success { background-color: #16a34a; } .btn-success:hover { background-color: #22c55e; }
.btn-warning { background-color: #f59e0b; } .btn-warning:hover { background-color: #d97706; }
.btn-secondary { background-color: gray; }
</style>
</head>
<body>

<div class="container">

<?php if($registration): ?>
    <?php if($registration['status'] === 'pending'): ?>
        <div class="notification pending" id="notif">
            Your registration is <strong>pending</strong>. Please wait for approval.
            <span class="close-btn" onclick="document.getElementById('notif').style.display='none'">&times;</span>
        </div>
    <?php elseif($registration['status'] === 'approved'): ?>
        <div class="notification approved" id="notif">
            Your registration has been <strong>approved</strong>.
            <span class="close-btn" onclick="document.getElementById('notif').style.display='none'">&times;</span>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <div class="form-header">
            <img src="../IMAGES/NCST-logo.png" alt="NCST Logo">
            <h2>NCST Student Registration Form</h2>
            <p>National College of Science and Technology</p>
        </div>

        <div class="student-info">
            <p><strong>Full Name:</strong> <?= htmlspecialchars($registration['fullname']) ?></p>
            <p><strong>Student ID:</strong> <?= htmlspecialchars($registration['student_id']) ?></p>
            <p><strong>Course:</strong> <?= htmlspecialchars($registration['course']) ?></p>
            <p><strong>Year Level:</strong> <?= htmlspecialchars(yearLevelText($registration['year_level'])) ?></p>
            <p><strong>Semester:</strong> <?= htmlspecialchars($registration['semester']) ?></p>
        </div>

        <table class="table table-bordered">
            <thead>
                <tr><th>Subject</th><th>Day</th><th>Time</th><th>Units</th></tr>
            </thead>
            <tbody>
                <?php foreach ($subjects as $subj): ?>
                <tr>
                    <td><?= htmlspecialchars($subj['subject_name'] ?? '') ?></td>
                    <td><?= htmlspecialchars($subj['day'] ?? '') ?></td>
                    <td><?= htmlspecialchars($subj['start_time'] ?? '') ?> - <?= htmlspecialchars($subj['end_time'] ?? '') ?></td>
                    <td><?= htmlspecialchars($subj['units'] ?? 0) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if (count($subjects) > 0): ?>
    <?php foreach ($subjects as $subj): ?>
        <tr>
            <td><?= htmlspecialchars($subj['section_name']) ?></td>
            <td><?= htmlspecialchars($subj['subject_name']) ?></td>
            <td><?= htmlspecialchars($subj['units']) ?></td>
            <td><?= htmlspecialchars($subj['day']) ?></td>
            <td><?= htmlspecialchars($subj['start_time'] . ' - ' . $subj['end_time']) ?></td>
        </tr>
    <?php endforeach; ?>
<?php else: ?>
    <tr>
        <td colspan="5">⚠ No subjects enrolled</td>
    </tr>
<?php endif; ?>

            </tbody>
        </table>
        <p class="total-units"><strong>Total Units:</strong> <?= $totalUnits ?></p>
        <?php
// --- Account summary: paste this where you want the totals shown ---
// constants
$lab_fee = 1500;
$misc_fee = 1000;

// tuition from subjects (you already computed $cashTotal earlier)
$tuition_fee = floatval($cashTotal) + floatval($lab_fee) + floatval($misc_fee);

// ensure we have student_id and registration id available
$student_id = $_SESSION['student_id'] ?? ($registration['student_id'] ?? null);
$registration_id = $registration['id'] ?? null;

// Prepare a robust SUM query that checks by student_id OR registration_id
$total_paid = 0.00;
if ($student_id || $registration_id) {
    $sql = "SELECT COALESCE(SUM(amount),0) AS total_paid 
            FROM payments 
            WHERE (student_id = ?)";
    if ($registration_id) {
        $sql .= " OR registration_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $student_id, $registration_id);
    } else {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $student_id);
    }

    if ($stmt) {
        $stmt->execute();
        $res = $stmt->get_result();
        $rowp = $res->fetch_assoc();
        $total_paid = isset($rowp['total_paid']) ? floatval($rowp['total_paid']) : 0.00;
        $stmt->close();
    } else {
        error_log("Payments SUM prepare failed: " . $conn->error);
    }
}

// compute balance
$balance = $tuition_fee - $total_paid;
if ($balance < 0) $balance = 0.00;
?>

<div class="payment-details mt-4 p-3 border rounded">
    <h5>Account Summary</h5>
    <p><strong>Total Tuition Fee:</strong> ₱<?= number_format($tuition_fee, 2) ?></p>
    <p><strong>Total Paid:</strong> ₱<?= number_format($total_paid, 2) ?></p>
    <p><strong>Current Balance:</strong> ₱<?= number_format($balance, 2) ?></p>

</div>


              <!-- Payment History & Button -->
              <?php
        // constants
        $lab_fee = 1500;
        $misc_fee = 1000;

        // compute tuition (use existing $cashTotal)
        $tuition_fee = floatval($cashTotal) + floatval($lab_fee) + floatval($misc_fee);

        $totalPaid = 0.00;
        $allPayments = [];

        if ($registration && isset($registration['id'])) {
            $regId = (int)$registration['id'];
            $sid = $registration['student_id'] ?? $student_id;

            // fetch all payments for this registration or student
            $paymentsSql = "SELECT * FROM payments WHERE registration_id = ? OR student_id = ? ORDER BY payment_date ASC";
            $paymentsStmt = $conn->prepare($paymentsSql);
            if ($paymentsStmt) {
                $paymentsStmt->bind_param("is", $regId, $sid);
                $paymentsStmt->execute();
                $paymentsResult = $paymentsStmt->get_result();
                $allPayments = $paymentsResult ? $paymentsResult->fetch_all(MYSQLI_ASSOC) : [];
                $paymentsStmt->close();
            }

            // sum total paid
            $sumSql = "SELECT COALESCE(SUM(amount),0) AS total_paid FROM payments WHERE registration_id = ? OR student_id = ?";
            $sumStmt = $conn->prepare($sumSql);
            if ($sumStmt) {
                $sumStmt->bind_param("is", $regId, $sid);
                $sumStmt->execute();
                $sumResult = $sumStmt->get_result();
                $sumRow = $sumResult ? $sumResult->fetch_assoc() : null;
                $totalPaid = isset($sumRow['total_paid']) ? floatval($sumRow['total_paid']) : 0.00;
                $sumStmt->close();
            }
        }

        // compute remaining balance
        $balance = $tuition_fee - $totalPaid;
        if ($balance < 0) $balance = 0.00;

        // whether there are any payments
        $hasSuccessfulPayments = count($allPayments) > 0;
        ?>

        <div class="payment-details mt-4 p-3 border rounded">
            <h5>Payment History</h5>

            <?php if($hasSuccessfulPayments): ?>
            <table class="table table-bordered">
                <thead><tr><th>Date</th><th>Method</th><th>Amount Paid</th><th>Transaction ID</th></tr></thead>
                <tbody>
                <?php foreach($allPayments as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars(date('F j, Y g:i A', strtotime($p['payment_date'] ?? $p['payment_date']))) ?></td>
                        <td><?= htmlspecialchars(ucfirst($p['method'])) ?></td>
                        <td>₱<?= number_format(floatval($p['amount']),2) ?></td>
                        <td><?= htmlspecialchars($p['transaction_id'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
                <?php 
$total_units = count($subjects) ? array_sum(array_column($subjects, 'units')) : 0;
?>
<tr>
    <td colspan="2"><strong>Total Units:</strong></td>
    <td><?= $total_units ?></td>
    <td colspan="2"></td>
</tr>

            </table>
            <?php else: ?>
            <p>No payments yet. Payment history will appear here after your first successful payment.</p>
            <?php endif; ?>

            <hr>
            <p><strong>Total Tuition Fee:</strong> ₱<?= number_format($tuition_fee,2) ?></p>
            <p><strong>Total Paid:</strong> ₱<?= number_format($totalPaid,2) ?></p>
            <p><strong>Remaining Balance:</strong> ₱<?= number_format($balance,2) ?></p>

            <!-- Buttons -->
            <div class="d-flex justify-content-between mt-4">
            <button class="btn-back btn-action" onclick="window.location.href='student_dashboard.php'">⬅ Back</button>

                <button class="<?= $payButtonClass ?> btn-action" <?= $payButtonDisabled ?> data-bs-toggle="modal" data-bs-target="#paymentModal"><?= $payButtonTitle ?></button>
                <button class="btn-primary btn-action" onclick="window.print()">🖨️ Print Registration</button>
            </div>
        </div>


    <!-- Payment Modal -->
    <?php if($balance > 0): ?>
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="paymentModalLabel">Make a Payment</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form id="paymentForm" action="create_payment.php" method="POST">
              <input type="hidden" name="method" id="methodField">
              <input type="hidden" name="name" value="<?= htmlspecialchars($registration['fullname']) ?>">
              <input type="hidden" name="email" value="<?= htmlspecialchars($registration['student_id']) ?>@ncst.edu">
              <input type="hidden" name="phone" value="09123456789">

              <div class="form-group mt-3">
                <label for="amountField"><strong>Enter Amount to Pay:</strong></label>
                <input type="number" class="form-control" name="amount" id="amountField" min="1" max="<?= $balance ?>" required>
              </div>

              <div class="d-flex justify-content-around mt-3">
                <button type="button" class="btn btn-primary" id="installmentBtn">Installment</button>
                <button type="button" class="btn btn-warning" id="cashBtn">Full Payment</button>
              </div>

              <div id="paymentDetails" class="mt-4 p-3 border rounded bg-light" style="display:none;">
                <h6><strong>Payment Breakdown</strong></h6>
                <p><strong>Total Amount:</strong> ₱<span id="totalAmount">0.00</span></p>
                <p id="installmentFeeLine" style="display:none;"><strong>Installment Fee:</strong> ₱<?= number_format($installmentCharge,2) ?></p>
                <p id="perTermLine" style="display:none;"><strong>Per Term:</strong> ₱<span id="termAmount">0.00</span></p>
              </div>

              <div class="text-center mt-3">
                <button type="submit" class="btn-success btn-action">💳 Pay Now</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

<?php else: ?>
    <div class="alert alert-info">You have not submitted any registration yet.</div>
    <button class="btn-back" onclick="window.history.back()">⬅Back</button>
<?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
var cashTotal = <?= $cashTotal ?>;
var installmentCharge = <?= $installmentCharge ?>;
var balance = <?= $balance ?>;
var installmentTotal = cashTotal + installmentCharge;

var paymentModal = document.getElementById("paymentModal");

if (paymentModal) {
    paymentModal.addEventListener("show.bs.modal", function () {
      document.getElementById("amountField").value = "";
      document.getElementById("amountField").min = "1";
      document.getElementById("amountField").max = balance;
      document.getElementById("methodField").value = "";
      document.getElementById("paymentDetails").style.display = "none";
      document.getElementById("totalAmount").innerText = "0.00";
      document.getElementById("installmentFeeLine").style.display = "none";
      document.getElementById("perTermLine").style.display = "none";
    });

    document.getElementById("installmentBtn").addEventListener("click", function() {
      var installmentAmount = Math.min(installmentCharge, balance);
      document.getElementById("amountField").value = installmentAmount.toFixed(2);
      document.getElementById("methodField").value = "installment";

      document.getElementById("paymentDetails").style.display = "block";
      document.getElementById("totalAmount").innerText = (installmentAmount + cashTotal - balance).toFixed(2);
      document.getElementById("installmentFeeLine").style.display = "block";
      document.getElementById("perTermLine").style.display = "block";

      var perTerm = ((installmentTotal - installmentCharge)/4).toFixed(2);
      document.getElementById("termAmount").innerText = perTerm;
    });

    document.getElementById("cashBtn").addEventListener("click", function() {
  document.getElementById("amountField").value = Math.min(cashTotal, balance).toFixed(2);
  document.getElementById("methodField").value = "full";  // <-- change here

  document.getElementById("paymentDetails").style.display = "block";
  document.getElementById("totalAmount").innerText = Math.min(cashTotal, balance).toFixed(2);

  document.getElementById("installmentFeeLine").style.display = "none";
  document.getElementById("perTermLine").style.display = "none";
});


    document.getElementById("paymentForm").addEventListener("submit", function(e) {
      e.preventDefault();
      var amount = parseFloat(document.getElementById("amountField").value) || 0;
      var method = document.getElementById("methodField").value;

      if (!method) {
        Swal.fire({icon:'warning', title:'Select Payment Method', text:'Please choose Installment or Full Payment first.'});
        return;
      }

      if (amount > balance) {
        Swal.fire({icon:'error', title:'Exceeds Balance', text:'You cannot pay more than the remaining balance ₱'+balance.toFixed(2)});
        return;
      }

      if (method === "cash" && amount < cashTotal) {
        Swal.fire({icon:'error', title:'Insufficient Amount', text:'Full payment requires at least ₱'+cashTotal.toFixed(2)});
        return;
      }

      if (method === "installment" && amount < installmentCharge) {
        Swal.fire({icon:'error', title:'Insufficient Amount', text:'Installment payment must be at least ₱'+installmentCharge.toFixed(2)});
        return;
      }

      Swal.fire({
        icon:'success',
        title:'Payment Validated',
        text:'Redirecting to PayMongo...',
        showConfirmButton:false,
        timer:2000
      }).then(() => e.target.submit());
    });
}
</script>
</body>
</html>