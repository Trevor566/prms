<?php
session_start();
if ($_SESSION['role'] !== 'receptionist') {
    header('Location: ../login.php'); exit();
}
$pageTitle = 'Receptionist Dashboard';
require_once '../includes/header.php';
require_once '../config/db.php';

$success = '';
$error   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'register') {
    $full_name   = trim($_POST['full_name']);
    $dob         = trim($_POST['date_of_birth']);
    $gender      = trim($_POST['gender']);
    $address     = trim($_POST['address']);
    $phone       = trim($_POST['phone_number']);

    if (empty($full_name) || empty($dob) || empty($gender)) {
        $error = 'Full name, date of birth and gender are required.';
    } else {
        $stmt = $conn->prepare("INSERT INTO patients (full_name, date_of_birth, gender, address, phone_number) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $full_name, $dob, $gender, $address, $phone);
        if ($stmt->execute()) {
            $new_patient_id = $conn->insert_id;
            $stmt2 = $conn->prepare("INSERT INTO visits (patient_id, receptionist_id) VALUES (?, ?)");
            $stmt2->bind_param("ii", $new_patient_id, $_SESSION['user_id']);
            $stmt2->execute();
            $success = "Patient registered successfully. Patient ID: $new_patient_id";
        } else {
            $error = 'Registration failed. Please try again.';
        }
    }
}

$search_results = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'search') {
    $keyword = '%' . trim($_POST['keyword']) . '%';
    $stmt = $conn->prepare("SELECT patient_id, full_name, date_of_birth, gender, phone_number FROM patients WHERE full_name LIKE ? OR patient_id LIKE ?");
    $stmt->bind_param("ss", $keyword, $keyword);
    $stmt->execute();
    $search_results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<div class="mb-4">
    <a href="billing.php" class="btn btn-outline-primary">Go to Billing</a>
</div>

<?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<div class="row g-4">

    <!-- Register New Patient -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Register New Patient</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="register">
                    <div class="mb-3">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                        <input type="date" name="date_of_birth" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gender <span class="text-danger">*</span></label>
                        <select name="gender" class="form-select" required>
                            <option value="">-- Select --</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="text" name="phone_number" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Register Patient</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Search Existing Patient -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Search Existing Patient</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action" value="search">
                    <div class="mb-3">
                        <label class="form-label">Search by Name or Patient ID</label>
                        <input type="text" name="keyword" class="form-control" placeholder="e.g. John or 3" required>
                    </div>
                    <button type="submit" class="btn btn-secondary w-100">Search</button>
                </form>

                <?php if (!empty($search_results)): ?>
                    <table class="table table-bordered table-sm mt-3">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>DOB</th>
                                <th>Gender</th>
                                <th>Phone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($search_results as $row): ?>
                            <tr>
                                <td><?= $row['patient_id'] ?></td>
                                <td><?= htmlspecialchars($row['full_name']) ?></td>
                                <td><?= $row['date_of_birth'] ?></td>
                                <td><?= $row['gender'] ?></td>
                                <td><?= htmlspecialchars($row['phone_number']) ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'search'): ?>
                    <p class="text-muted mt-3">No patients found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

</div>
</div></body></html>