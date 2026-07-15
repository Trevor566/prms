<?php
session_start();
if ($_SESSION['role'] !== 'doctor') {
    header('Location: ../login.php'); exit();
}
$pageTitle = 'Doctor Dashboard';
require_once '../config/db.php';

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {

    if ($_POST['action'] === 'consult') {
        $visit_id  = intval($_POST['visit_id']);
        $diagnosis = trim($_POST['diagnosis']);
        $notes     = trim($_POST['notes']);

        if (empty($visit_id) || empty($diagnosis)) {
            $error = 'Visit ID and diagnosis are required.';
        } else {
            $stmt = $conn->prepare("SELECT visit_id FROM visits WHERE visit_id = ? AND visit_status = 'active'");
            $stmt->bind_param("i", $visit_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows === 0) {
                $error = 'No active visit found with that ID.';
            } else {
                $stmt2 = $conn->prepare("INSERT INTO consultations (visit_id, doctor_id, diagnosis, notes) VALUES (?, ?, ?, ?)");
                $stmt2->bind_param("iiss", $visit_id, $_SESSION['user_id'], $diagnosis, $notes);
                if ($stmt2->execute()) {
                    $consultation_id = $conn->insert_id;
                    $success = "Consultation saved. Consultation ID: $consultation_id";
                } else {
                    $error = 'Failed to save consultation.';
                }
            }
        }
    }

    if ($_POST['action'] === 'lab') {
        $consultation_id = intval($_POST['consultation_id']);
        $test_name       = trim($_POST['test_name']);

        if (empty($consultation_id) || empty($test_name)) {
            $error = 'Consultation ID and test name are required.';
        } else {
            $stmt = $conn->prepare("INSERT INTO lab_requests (consultation_id, test_name) VALUES (?, ?)");
            $stmt->bind_param("is", $consultation_id, $test_name);
            if ($stmt->execute()) {
                $success = "Lab request submitted for: $test_name";
            } else {
                $error = 'Failed to submit lab request.';
            }
        }
    }

    if ($_POST['action'] === 'prescribe') {
        $consultation_id = intval($_POST['consultation_id']);
        $drug_name       = trim($_POST['drug_name']);
        $dosage          = trim($_POST['dosage']);
        $duration        = intval($_POST['duration_days']);

        if (empty($consultation_id) || empty($drug_name)) {
            $error = 'Consultation ID and drug name are required.';
        } else {
            $stmt = $conn->prepare("INSERT INTO prescriptions (consultation_id, drug_name, dosage, duration_days) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("issi", $consultation_id, $drug_name, $dosage, $duration);
            if ($stmt->execute()) {
                $success = "Prescription added: $drug_name";
            } else {
                $error = 'Failed to save prescription.';
            }
        }
    }
}

$recent_consults = $conn->query("
    SELECT c.consultation_id, c.visit_id, p.full_name, c.diagnosis, c.consultation_date
    FROM consultations c
    JOIN visits v ON c.visit_id = v.visit_id
    JOIN patients p ON v.patient_id = p.patient_id
    ORDER BY c.consultation_date DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRMS – Doctor Dashboard</title>
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
                <span class="badge bg-light text-primary ms-1">Doctor</span>
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

    <!-- Step 1: Record Consultation -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Step 1 — Record Consultation</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="consult">
                    <div class="mb-3">
                        <label class="form-label">Visit ID <span class="text-danger">*</span></label>
                        <input type="number" name="visit_id" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Diagnosis <span class="text-danger">*</span></label>
                        <textarea name="diagnosis" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Additional Notes</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Consultation</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Step 2: Request Lab Test -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Step 2 — Request Lab Test</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="lab">
                    <div class="mb-3">
                        <label class="form-label">Consultation ID <span class="text-danger">*</span></label>
                        <input type="number" name="consultation_id" class="form-control" required>
                        <div class="form-text">From the success message in Step 1.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Test Name <span class="text-danger">*</span></label>
                        <input type="text" name="test_name" class="form-control" placeholder="e.g. Full Blood Count">
                    </div>
                    <button type="submit" class="btn btn-warning w-100">Submit Lab Request</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Step 3: Prescribe Medication -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Step 3 — Prescribe Medication</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="prescribe">
                    <div class="mb-3">
                        <label class="form-label">Consultation ID <span class="text-danger">*</span></label>
                        <input type="number" name="consultation_id" class="form-control" required>
                        <div class="form-text">From the success message in Step 1.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Drug Name <span class="text-danger">*</span></label>
                        <input type="text" name="drug_name" class="form-control" placeholder="e.g. Amoxicillin">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Dosage</label>
                        <input type="text" name="dosage" class="form-control" placeholder="e.g. 500mg twice daily">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Duration (days)</label>
                        <input type="number" name="duration_days" class="form-control" placeholder="e.g. 7">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Add Prescription</button>
                </form>
            </div>
        </div>
    </div>

</div>

<!-- Recent Consultations -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">Recent Consultations</div>
            <div class="card-body p-0">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Consultation ID</th>
                            <th>Visit ID</th>
                            <th>Patient</th>
                            <th>Diagnosis</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $recent_consults->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['consultation_id'] ?></td>
                            <td><?= $row['visit_id'] ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= htmlspecialchars($row['diagnosis']) ?></td>
                            <td><?= date('d M Y H:i', strtotime($row['consultation_date'])) ?></td>
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