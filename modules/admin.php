<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header('Location: ../login.php'); exit();
}
$pageTitle = 'Admin Dashboard';
require_once '../config/db.php';

// Summary counts
$total_patients  = $conn->query("SELECT COUNT(*) as c FROM patients")->fetch_assoc()['c'];
$total_visits    = $conn->query("SELECT COUNT(*) as c FROM visits")->fetch_assoc()['c'];
$active_visits   = $conn->query("SELECT COUNT(*) as c FROM visits WHERE visit_status = 'active'")->fetch_assoc()['c'];
$discharged      = $conn->query("SELECT COUNT(*) as c FROM visits WHERE visit_status = 'discharged'")->fetch_assoc()['c'];
$total_revenue   = $conn->query("SELECT SUM(total_amount) as s FROM billing WHERE payment_status = 'paid'")->fetch_assoc()['s'];
$pending_labs    = $conn->query("SELECT COUNT(*) as c FROM lab_requests WHERE status = 'pending'")->fetch_assoc()['c'];
$pending_rx      = $conn->query("SELECT COUNT(*) as c FROM prescriptions WHERE dispensed = 0")->fetch_assoc()['c'];

// Recent patients
$recent_patients = $conn->query("
    SELECT patient_id, full_name, gender, phone_number, registration_date
    FROM patients
    ORDER BY registration_date DESC
    LIMIT 10
");

// Revenue by date
$revenue_report = $conn->query("
    SELECT DATE(billed_at) as bill_date, COUNT(*) as total_bills,
           SUM(total_amount) as daily_total
    FROM billing
    WHERE payment_status = 'paid'
    GROUP BY DATE(billed_at)
    ORDER BY bill_date DESC
    LIMIT 10
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PRMS – Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f2f5; }
        .navbar { background-color: #0d6efd; }
        .navbar-brand, .navbar-text { color: white !important; }
        .card { border: none; box-shadow: 0 1px 4px rgba(0,0,0,0.1); }
        .card-header { background-color: #0d6efd; color: white; font-weight: 500; }
        table thead { background-color: #e9ecef; }
        .main-content { padding: 30px 20px; }
        .stat-card { text-align: center; padding: 20px; }
        .stat-card .stat-number { font-size: 2rem; font-weight: 700; color: #0d6efd; }
        .stat-card .stat-label { font-size: 0.85rem; color: #6c757d; margin-top: 4px; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <span class="navbar-brand fw-bold">NWH – PRMS</span>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span class="navbar-text">
                <?= htmlspecialchars($_SESSION['full_name']) ?>
                <span class="badge bg-light text-primary ms-1">Admin</span>
            </span>
            <a href="../logout.php" class="btn btn-sm btn-outline-light">Log Out</a>
        </div>
    </div>
</nav>
<div class="container-fluid main-content">

    <h5 class="mb-4">System Overview</h5>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card stat-card">
                <div class="stat-number"><?= $total_patients ?></div>
                <div class="stat-label">Total Patients</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card">
                <div class="stat-number"><?= $total_visits ?></div>
                <div class="stat-label">Total Visits</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card">
                <div class="stat-number text-success"><?= $active_visits ?></div>
                <div class="stat-label">Active Visits</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card">
                <div class="stat-number"><?= $discharged ?></div>
                <div class="stat-label">Discharged</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card">
                <div class="stat-number text-warning"><?= $pending_labs ?></div>
                <div class="stat-label">Pending Labs</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card stat-card">
                <div class="stat-number text-danger"><?= $pending_rx ?></div>
                <div class="stat-label">Pending Rx</div>
            </div>
        </div>
    </div>

    <!-- Revenue Banner -->
    <div class="alert alert-primary mb-4">
        <strong>Total Revenue Collected:</strong>
        KES <?= number_format($total_revenue ?? 0, 2) ?>
    </div>

    <div class="row g-4">

        <!-- Recent Patients -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">Recently Registered Patients</div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th>Registered</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $recent_patients->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['patient_id'] ?></td>
                                <td><?= htmlspecialchars($row['full_name']) ?></td>
                                <td><?= $row['gender'] ?></td>
                                <td><?= htmlspecialchars($row['phone_number']) ?></td>
                                <td><?= date('d M Y', strtotime($row['registration_date'])) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Revenue Report -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header">Daily Revenue Report</div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Bills</th>
                                <th>Total (KES)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $revenue_report->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('d M Y', strtotime($row['bill_date'])) ?></td>
                                <td><?= $row['total_bills'] ?></td>
                                <td><strong><?= number_format($row['daily_total'], 2) ?></strong></td>
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