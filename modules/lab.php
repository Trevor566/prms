<?php
session_start();
if ($_SESSION['role'] !== 'lab_technician') {
    header('Location: ../login.php'); exit();
}
$pageTitle = 'Laboratory Dashboard';
require_once '../config/db.php';

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lab_request_id = intval($_POST['lab_request_id']);
    $results        = trim($_POST['results']);

    if (empty($lab_request_id) || empty($results)) {
        $error = 'Lab request ID and results are required.';
    } else {
        $stmt = $conn->prepare("UPDATE lab_requests SET results = ?, status = 'completed', completed_at = NOW() WHERE lab_request_id = ?");
        $stmt->bind_param("si", $results, $lab_request_id);
        if ($stmt->execute() && $stmt->affected_rows > 0) {
            $success = "Results saved for Lab Request ID $lab_request_id.";
        } else {
            $error = 'Lab request ID not found or already completed.';
        }
    }
}

$pending = $conn->query("
    SELECT lr.lab_request_id, lr.test_name, lr.status, lr.requested_at,
           p.full_name, c.consultation_id
    FROM lab_requests lr
    JOIN consultations c ON lr.consultation_id = c.consultation_id
    JOIN visits v ON c.visit_id = v.visit_id
    JOIN patients p ON v.patient_id = p.patient_id
    ORDER BY lr.requested_at DESC
    LIMIT 20
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRMS – Laboratory Dashboard</title>
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
                <span class="badge bg-light text-primary ms-1">Lab Technician</span>
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

    <!-- Enter Results Form -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Enter Test Results</div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Lab Request ID <span class="text-danger">*</span></label>
                        <input type="number" name="lab_request_id" class="form-control" required>
                        <div class="form-text">From the pending requests table on the right.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Results <span class="text-danger">*</span></label>
                        <textarea name="results" class="form-control" rows="4" placeholder="Enter test results here..." required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Results</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Pending and Completed Requests -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">Lab Requests</div>
            <div class="card-body p-0">
                <table class="table table-bordered table-sm mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Test</th>
                            <th>Status</th>
                            <th>Requested</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $pending->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['lab_request_id'] ?></td>
                            <td><?= htmlspecialchars($row['full_name']) ?></td>
                            <td><?= htmlspecialchars($row['test_name']) ?></td>
                            <td>
                                <?php if ($row['status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark">Pending</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Completed</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d M H:i', strtotime($row['requested_at'])) ?></td>
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