<?php
session_start();
if ($_SESSION['role'] !== 'pharmacist') {
    header('Location: ../login.php'); exit();
}
$pageTitle = 'Pharmacy Dashboard';
require_once '../config/db.php';

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prescription_id = intval($_POST['prescription_id']);

    if (empty($prescription_id)) {
        $error = 'Prescription ID is required.';
    } else {
        $stmt = $conn->prepare("UPDATE prescriptions SET dispensed = 1 WHERE prescription_id = ? AND dispensed = 0");
        $stmt->bind_param("i", $prescription_id);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $success = "Prescription ID $prescription_id marked as dispensed.";
        } else {
            $error = 'Prescription ID not found or already dispensed.';
        }
    }
}

$prescriptions = $conn->query("
    SELECT pr.prescription_id, pr.drug_name, pr.dosage, pr.duration_days,
           pr.dispensed, p.full_name, c.consultation_id
    FROM prescriptions pr
    JOIN consultations c ON pr.consultation_id = c.consultation_id
    JOIN visits v ON c.visit_id = v.visit_id
    JOIN patients p ON v.patient_id = p.patient_id
    ORDER BY pr.prescription_id DESC
    LIMIT 20
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRMS – Pharmacy Dashboard</title>
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
                <span class="badge bg-light text-primary ms-1">Pharmacist</span>
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

    <!-- Dispense Form -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Dispense Medication</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Prescription ID <span class="text-danger">*</span></label>
                        <input type="number" name="prescription_id" class="form-control" required>
                        <div class="form-text">From the prescriptions table on the right.</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Mark as Dispensed</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Prescriptions Table -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Prescriptions</div>
            <div class="card-body p-0">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Drug</th>
                            <th>Dosage</th>
                            <th>Days</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $prescriptions->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['prescription_id'] ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= htmlspecialchars($row['drug_name']) ?></td>
                            <td><?= htmlspecialchars($row['dosage']) ?></td>
                            <td><?= $row['duration_days'] ?></td>
                            <td>
                                <?php if ($row['dispensed']): ?>
                                    <span class="badge bg-success">Dispensed</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php endif; ?>
                            </td>
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