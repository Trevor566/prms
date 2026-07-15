<?php
session_start();
if ($_SESSION['role'] !== 'nurse') {
    header('Location: ../login.php'); exit();
}
$pageTitle = 'Nurse Dashboard';
require_once '../includes/header.php';
require_once '../config/db.php';

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visit_id    = intval($_POST['visit_id']);
    $temperature = trim($_POST['temperature']);
    $pulse_rate  = intval($_POST['pulse_rate']);
    $bp          = trim($_POST['blood_pressure']);
    $weight      = trim($_POST['weight_kg']);

    if (empty($visit_id) || empty($temperature) || empty($bp)) {
        $error = 'Visit ID, temperature and blood pressure are required.';
    } else {
        $stmt = $conn->prepare("SELECT visit_id FROM visits WHERE visit_id = ? AND visit_status = 'active'");
        $stmt->bind_param("i", $visit_id);
        $stmt->execute();
        $check = $stmt->get_result();

        if ($check->num_rows === 0) {
            $error = 'No active visit found with that ID.';
        } else {
            $stmt2 = $conn->prepare("INSERT INTO vital_signs (visit_id, temperature, pulse_rate, blood_pressure, weight_kg, recorded_by) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt2->bind_param("idisdi", $visit_id, $temperature, $pulse_rate, $bp, $weight, $_SESSION['user_id']);
            if ($stmt2->execute()) {
                $success = "Vital signs recorded successfully for Visit ID $visit_id.";
            } else {
                $error = 'Failed to save vital signs. Please try again.';
            }
        }
    }
}

$recent = $conn->query("
    SELECT v.vitals_id, v.visit_id, p.full_name, v.temperature, v.pulse_rate,
           v.blood_pressure, v.weight_kg, v.recorded_at
    FROM vital_signs v
    JOIN visits vi ON v.visit_id = vi.visit_id
    JOIN patients p ON vi.patient_id = p.patient_id
    ORDER BY v.recorded_at DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRMS – Nurse Dashboard</title>
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
                <span class="badge bg-light text-primary ms-1">Nurse</span>
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

    <div class="col-md-5">
        <div class="card">
            <div class="card-header">Record Vital Signs</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Visit ID <span class="text-danger">*</span></label>
                        <input type="number" name="visit_id" class="form-control" placeholder="Enter visit ID from reception" required>
                        <div class="form-text">The receptionist will provide this after registering the patient.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Temperature (°C) <span class="text-danger">*</span></label>
                        <input type="number" step="0.1" name="temperature" class="form-control" placeholder="e.g. 36.5" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Pulse Rate (bpm)</label>
                        <input type="number" name="pulse_rate" class="form-control" placeholder="e.g. 72">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Blood Pressure <span class="text-danger">*</span></label>
                        <input type="text" name="blood_pressure" class="form-control" placeholder="e.g. 120/80" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Weight (kg)</label>
                        <input type="number" step="0.1" name="weight_kg" class="form-control" placeholder="e.g. 65.0">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Vital Signs</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card">
            <div class="card-header">Recently Recorded Vital Signs</div>
            <div class="card-body p-0">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th>Visit ID</th>
                            <th>Patient</th>
                            <th>Temp</th>
                            <th>Pulse</th>
                            <th>BP</th>
                            <th>Weight</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $recent->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['visit_id'] ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= $row['temperature'] ?>°C</td>
                            <td><?= $row['pulse_rate'] ?></td>
                            <td><?= $row['blood_pressure'] ?></td>
                            <td><?= $row['weight_kg'] ?>kg</td>
                            <td><?= date('d M H:i', strtotime($row['recorded_at'])) ?></td>
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