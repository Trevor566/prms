<?php
session_start();
if ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'receptionist') {
    header('Location: ../login.php'); exit();
}
$pageTitle = 'Billing';
require_once '../config/db.php';

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visit_id         = intval($_POST['visit_id']);
    $consultation_fee = floatval($_POST['consultation_fee']);
    $lab_fee          = floatval($_POST['lab_fee']);
    $pharmacy_fee     = floatval($_POST['pharmacy_fee']);
    $total            = $consultation_fee + $lab_fee + $pharmacy_fee;

    if (empty($visit_id)) {
        $error = 'Visit ID is required.';
    } else {
        $check = $conn->prepare("SELECT bill_id FROM billing WHERE visit_id = ?");
        $check->bind_param("i", $visit_id);
        $check->execute();
        if ($check->get_result()->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE billing SET consultation_fee=?, lab_fee=?, pharmacy_fee=?, total_amount=?, payment_status='paid', billed_at=NOW() WHERE visit_id=?");
            $stmt->bind_param("ddddi", $consultation_fee, $lab_fee, $pharmacy_fee, $total, $visit_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO billing (visit_id, consultation_fee, lab_fee, pharmacy_fee, total_amount, payment_status) VALUES (?, ?, ?, ?, ?, 'paid')");
            $stmt->bind_param("idddd", $visit_id, $consultation_fee, $lab_fee, $pharmacy_fee, $total);
        }

        if ($stmt->execute()) {
            $upd = $conn->prepare("UPDATE visits SET visit_status = 'discharged' WHERE visit_id = ?");
            $upd->bind_param("i", $visit_id);
            $upd->execute();
            $success = "Payment recorded. Total: KES " . number_format($total, 2) . ". Patient discharged.";
        } else {
            $error = 'Failed to save billing record.';
        }
    }
}

$bills = $conn->query("
    SELECT b.bill_id, b.visit_id, p.full_name, b.consultation_fee,
           b.lab_fee, b.pharmacy_fee, b.total_amount, b.payment_status, b.billed_at
    FROM billing b
    JOIN visits v ON b.visit_id = v.visit_id
    JOIN patients p ON v.patient_id = p.patient_id
    ORDER BY b.billed_at DESC
    LIMIT 20
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRMS – Billing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .navbar { background-color: #0d6efd; }
        .navbar-brand, .navbar-text { color: white !important; }
        .card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        .card-header { background-color: #0d6efd; color: white; font-weight: 500; }
        table thead { background-color: #e9ecef; }
        .main-content { padding: 30px 20px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">NWH – PRMS</span>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="navbar-text">
                <?= htmlspecialchars($_SESSION['full_name']) ?>
                <span class="badge bg-light text-primary ms-1">
                    <?= ucfirst($_SESSION['role']) ?>
                </span>
            </span>
            <a href="../logout.php" class="btn btn-sm btn-outline-light">Log Out</a>
        </div>
    </div>
</nav>
<div class="container-fluid main-content">

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="row g-4">

    <!-- Billing Form -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Record Payment</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Visit ID <span class="text-danger">*</span></label>
                        <input type="number" name="visit_id" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Consultation Fee (KES)</label>
                        <input type="number" step="0.01" name="consultation_fee" class="form-control" value="500">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Lab Fee (KES)</label>
                        <input type="number" step="0.01" name="lab_fee" class="form-control" value="0">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pharmacy Fee (KES)</label>
                        <input type="number" step="0.01" name="pharmacy_fee" class="form-control" value="0">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Confirm Payment & Discharge</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Billing Records -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Billing Records</div>
            <div class="card-body p-0">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Bill ID</th>
                            <th>Patient</th>
                            <th>Consult</th>
                            <th>Lab</th>
                            <th>Pharmacy</th>
                            <th>Total (KES)</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $bills->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['bill_id'] ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= number_format($row['consultation_fee'], 2) ?></td>
                            <td><?= number_format($row['lab_fee'], 2) ?></td>
                            <td><?= number_format($row['pharmacy_fee'], 2) ?></td>
                            <td><strong><?= number_format($row['total_amount'], 2) ?></strong></td>
                            <td><span class="badge bg-success"><?= ucfirst($row['payment_status']) ?></span></td>
                            <td><?= date('d M Y', strtotime($row['billed_at'])) ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
</div>
</body>
</html>