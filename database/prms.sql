-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 15, 2026 at 11:11 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `prms`
--

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

CREATE TABLE `billing` (
  `bill_id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `consultation_fee` decimal(10,2) DEFAULT 0.00,
  `lab_fee` decimal(10,2) DEFAULT 0.00,
  `pharmacy_fee` decimal(10,2) DEFAULT 0.00,
  `total_amount` decimal(10,2) DEFAULT 0.00,
  `payment_status` enum('unpaid','paid') DEFAULT 'unpaid',
  `billed_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`bill_id`, `visit_id`, `consultation_fee`, `lab_fee`, `pharmacy_fee`, `total_amount`, `payment_status`, `billed_at`) VALUES
(1, 1, 500.00, 200.00, 500.00, 1200.00, 'paid', '2026-07-12 20:56:23'),
(2, 2, 2000.00, 500.00, 3000.00, 5500.00, 'paid', '2026-07-15 11:59:12'),
(3, 2, 500.00, 300.00, 450.00, 1250.00, 'paid', '2026-07-15 12:04:45'),
(4, 3, 500.00, 600.00, 380.00, 1480.00, 'paid', '2026-07-15 12:04:45'),
(5, 4, 500.00, 400.00, 520.00, 1420.00, 'paid', '2026-07-15 12:04:45'),
(6, 5, 500.00, 800.00, 600.00, 1900.00, 'paid', '2026-07-15 12:04:45'),
(7, 6, 500.00, 700.00, 400.00, 1600.00, 'paid', '2026-07-15 12:04:45');

-- --------------------------------------------------------

--
-- Table structure for table `consultations`
--

CREATE TABLE `consultations` (
  `consultation_id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `diagnosis` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `consultation_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `consultations`
--

INSERT INTO `consultations` (`consultation_id`, `visit_id`, `doctor_id`, `diagnosis`, `notes`, `consultation_date`) VALUES
(1, 1, 4, 'Malaria', 'fever', '2026-07-12 20:38:16'),
(2, 2, 4, 'Asthma', 'Early stage', '2026-07-15 11:55:39'),
(3, 2, 4, 'Upper Respiratory Tract Infection', 'Patient presents with sore throat, mild fever and nasal congestion for 3 days. No chest pain.', '2026-07-15 12:04:45'),
(4, 3, 4, 'Malaria', 'Patient presents with high fever, chills and body aches. Rapid diagnostic test positive for Plasmodium falciparum.', '2026-07-15 12:04:45'),
(5, 4, 4, 'Urinary Tract Infection', 'Patient reports burning sensation during urination for 2 days. Urine appears cloudy.', '2026-07-15 12:04:45'),
(6, 5, 4, 'Hypertensive Crisis', 'Patient presents with severe headache and dizziness. BP critically elevated. Immediate management required.', '2026-07-15 12:04:45'),
(7, 6, 4, 'Type 2 Diabetes — Routine Review', 'Patient on long-term management. Blood sugar levels reviewed. Lifestyle counselling provided.', '2026-07-15 12:04:45');

-- --------------------------------------------------------

--
-- Table structure for table `lab_requests`
--

CREATE TABLE `lab_requests` (
  `lab_request_id` int(11) NOT NULL,
  `consultation_id` int(11) NOT NULL,
  `test_name` varchar(100) NOT NULL,
  `results` text DEFAULT NULL,
  `status` enum('pending','completed') DEFAULT 'pending',
  `requested_at` datetime DEFAULT current_timestamp(),
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lab_requests`
--

INSERT INTO `lab_requests` (`lab_request_id`, `consultation_id`, `test_name`, `results`, `status`, `requested_at`, `completed_at`) VALUES
(1, 1, 'Full blood count', 'Malaria Positive', 'completed', '2026-07-12 20:38:39', '2026-07-12 20:42:37'),
(2, 2, 'Respiratory', 'Patient has asthma', 'completed', '2026-07-15 11:56:29', '2026-07-15 11:58:24'),
(3, 2, 'Throat Swab Culture', 'No significant bacterial growth detected.', 'completed', '2026-07-15 12:04:45', '2026-07-15 12:04:45'),
(4, 3, 'Malaria RDT', 'Positive — Plasmodium falciparum detected.', 'completed', '2026-07-15 12:04:45', '2026-07-15 12:04:45'),
(5, 3, 'Full Blood Count', 'WBC elevated at 11.2. Haemoglobin 10.8 g/dL.', 'completed', '2026-07-15 12:04:45', '2026-07-15 12:04:45'),
(6, 4, 'Urinalysis', 'Pus cells +++. Nitrites positive. Culture pending.', 'completed', '2026-07-15 12:04:45', '2026-07-15 12:04:45'),
(7, 5, 'Lipid Profile', 'Total cholesterol 6.2 mmol/L. LDL elevated.', 'completed', '2026-07-15 12:04:45', '2026-07-15 12:04:45'),
(8, 5, 'Renal Function Test', 'Creatinine 98 umol/L. eGFR 72. Within normal limits.', 'completed', '2026-07-15 12:04:45', '2026-07-15 12:04:45'),
(9, 6, 'Fasting Blood Sugar', 'FBS 9.4 mmol/L — above target range.', 'completed', '2026-07-15 12:04:45', '2026-07-15 12:04:45'),
(10, 6, 'HbA1c', 'HbA1c 8.1% — above target. Medication review recommended.', 'completed', '2026-07-15 12:04:45', '2026-07-15 12:04:45');

-- --------------------------------------------------------

--
-- Table structure for table `patients`
--

CREATE TABLE `patients` (
  `patient_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `date_of_birth` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `registration_date` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patients`
--

INSERT INTO `patients` (`patient_id`, `full_name`, `date_of_birth`, `gender`, `address`, `phone_number`, `registration_date`) VALUES
(1, 'Peter Ouma', '2026-07-12', 'Male', '12345678', '0112345678', '2026-07-12 16:38:23'),
(2, 'Jennifer Njoroge', '2026-07-06', 'Female', '14243765', '0726745274', '2026-07-15 11:51:06'),
(3, 'Grace Wanjiku Kamau', '1990-03-15', 'Female', 'Westlands, Nairobi', '0712345678', '2026-07-15 12:04:45'),
(4, 'James Otieno Odhiambo', '1985-07-22', 'Male', 'Kibera, Nairobi', '0723456789', '2026-07-15 12:04:45'),
(5, 'Fatuma Aisha Mohamed', '1995-11-08', 'Female', 'Eastleigh, Nairobi', '0734567890', '2026-07-15 12:04:45'),
(6, 'David Kipchoge Rotich', '2000-01-30', 'Male', 'Kasarani, Nairobi', '0745678901', '2026-07-15 12:04:45'),
(7, 'Mary Njeri Githae', '1978-09-12', 'Female', 'Githurai, Nairobi', '0756789012', '2026-07-15 12:04:45');

-- --------------------------------------------------------

--
-- Table structure for table `prescriptions`
--

CREATE TABLE `prescriptions` (
  `prescription_id` int(11) NOT NULL,
  `consultation_id` int(11) NOT NULL,
  `drug_name` varchar(100) NOT NULL,
  `dosage` varchar(100) DEFAULT NULL,
  `duration_days` int(11) DEFAULT NULL,
  `dispensed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prescriptions`
--

INSERT INTO `prescriptions` (`prescription_id`, `consultation_id`, `drug_name`, `dosage`, `duration_days`, `dispensed`) VALUES
(1, 1, 'Amoxocilin', '500mg twice a day', 7, 1),
(2, 2, 'Inhaled Corticosteroids (ICS)', '500mg', 7, 1),
(3, 2, 'Paracetamol', '500mg every 8 hours', 5, 1),
(4, 2, 'Amoxicillin', '500mg every 8 hours', 7, 1),
(5, 2, 'Loratadine', '10mg once daily', 5, 1),
(6, 3, 'Artemether/Lumefantrine', '4 tablets twice daily', 3, 1),
(7, 3, 'Paracetamol', '1g every 6 hours', 3, 1),
(8, 4, 'Ciprofloxacin', '500mg twice daily', 7, 1),
(9, 4, 'Metronidazole', '400mg three times daily', 5, 1),
(10, 5, 'Amlodipine', '10mg once daily', 30, 1),
(11, 5, 'Hydrochlorothiazide', '25mg once daily', 30, 1),
(12, 6, 'Metformin', '500mg twice daily', 30, 1),
(13, 6, 'Glibenclamide', '5mg once daily', 30, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('receptionist','nurse','doctor','lab_technician','pharmacist','admin') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `full_name`, `username`, `password_hash`, `role`, `is_active`, `created_at`) VALUES
(1, 'Admin User', 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, '2026-07-12 15:54:48'),
(2, 'Mary Receptionist', 'receptionist', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'receptionist', 1, '2026-07-12 15:54:48'),
(3, 'Jane Nurse', 'nurse', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'nurse', 1, '2026-07-12 15:54:48'),
(4, 'Dr. James Doctor', 'doctor', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'doctor', 1, '2026-07-12 15:54:48'),
(5, 'Lab Tech', 'labtech', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'lab_technician', 1, '2026-07-12 15:54:48'),
(6, 'Peter Pharmacist', 'pharmacist', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'pharmacist', 1, '2026-07-12 15:54:48');

-- --------------------------------------------------------

--
-- Table structure for table `visits`
--

CREATE TABLE `visits` (
  `visit_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `receptionist_id` int(11) NOT NULL,
  `visit_date` datetime DEFAULT current_timestamp(),
  `visit_status` enum('active','discharged') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `visits`
--

INSERT INTO `visits` (`visit_id`, `patient_id`, `receptionist_id`, `visit_date`, `visit_status`) VALUES
(1, 1, 2, '2026-07-12 16:38:23', 'discharged'),
(2, 2, 2, '2026-07-15 11:51:07', 'discharged'),
(3, 2, 2, '2026-07-15 12:04:45', 'discharged'),
(4, 3, 2, '2026-07-15 12:04:45', 'discharged'),
(5, 4, 2, '2026-07-15 12:04:45', 'discharged'),
(6, 5, 2, '2026-07-15 12:04:45', 'discharged'),
(7, 6, 2, '2026-07-15 12:04:45', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `vital_signs`
--

CREATE TABLE `vital_signs` (
  `vitals_id` int(11) NOT NULL,
  `visit_id` int(11) NOT NULL,
  `temperature` decimal(4,1) DEFAULT NULL,
  `pulse_rate` int(11) DEFAULT NULL,
  `blood_pressure` varchar(20) DEFAULT NULL,
  `weight_kg` decimal(5,2) DEFAULT NULL,
  `recorded_by` int(11) NOT NULL,
  `recorded_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vital_signs`
--

INSERT INTO `vital_signs` (`vitals_id`, `visit_id`, `temperature`, `pulse_rate`, `blood_pressure`, `weight_kg`, `recorded_by`, `recorded_at`) VALUES
(1, 1, 37.0, 72, '120/80', 65.00, 3, '2026-07-12 16:48:04'),
(2, 2, 36.0, 73, '121/82', 65.00, 3, '2026-07-15 11:52:53'),
(3, 2, 37.2, 78, '118/76', 62.50, 3, '2026-07-15 12:04:45'),
(4, 3, 38.5, 92, '130/85', 75.00, 3, '2026-07-15 12:04:45'),
(5, 4, 36.8, 68, '110/70', 55.00, 3, '2026-07-15 12:04:45'),
(6, 5, 39.1, 105, '140/90', 88.00, 3, '2026-07-15 12:04:45'),
(7, 6, 37.0, 72, '120/80', 58.50, 3, '2026-07-15 12:04:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `billing`
--
ALTER TABLE `billing`
  ADD PRIMARY KEY (`bill_id`),
  ADD KEY `visit_id` (`visit_id`);

--
-- Indexes for table `consultations`
--
ALTER TABLE `consultations`
  ADD PRIMARY KEY (`consultation_id`),
  ADD KEY `visit_id` (`visit_id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indexes for table `lab_requests`
--
ALTER TABLE `lab_requests`
  ADD PRIMARY KEY (`lab_request_id`),
  ADD KEY `consultation_id` (`consultation_id`);

--
-- Indexes for table `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`patient_id`);

--
-- Indexes for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD PRIMARY KEY (`prescription_id`),
  ADD KEY `consultation_id` (`consultation_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `visits`
--
ALTER TABLE `visits`
  ADD PRIMARY KEY (`visit_id`),
  ADD KEY `patient_id` (`patient_id`),
  ADD KEY `receptionist_id` (`receptionist_id`);

--
-- Indexes for table `vital_signs`
--
ALTER TABLE `vital_signs`
  ADD PRIMARY KEY (`vitals_id`),
  ADD KEY `visit_id` (`visit_id`),
  ADD KEY `recorded_by` (`recorded_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `billing`
--
ALTER TABLE `billing`
  MODIFY `bill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `consultations`
--
ALTER TABLE `consultations`
  MODIFY `consultation_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `lab_requests`
--
ALTER TABLE `lab_requests`
  MODIFY `lab_request_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `patients`
--
ALTER TABLE `patients`
  MODIFY `patient_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `prescriptions`
--
ALTER TABLE `prescriptions`
  MODIFY `prescription_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `visits`
--
ALTER TABLE `visits`
  MODIFY `visit_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vital_signs`
--
ALTER TABLE `vital_signs`
  MODIFY `vitals_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`visit_id`);

--
-- Constraints for table `consultations`
--
ALTER TABLE `consultations`
  ADD CONSTRAINT `consultations_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`visit_id`),
  ADD CONSTRAINT `consultations_ibfk_2` FOREIGN KEY (`doctor_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `lab_requests`
--
ALTER TABLE `lab_requests`
  ADD CONSTRAINT `lab_requests_ibfk_1` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`consultation_id`);

--
-- Constraints for table `prescriptions`
--
ALTER TABLE `prescriptions`
  ADD CONSTRAINT `prescriptions_ibfk_1` FOREIGN KEY (`consultation_id`) REFERENCES `consultations` (`consultation_id`);

--
-- Constraints for table `visits`
--
ALTER TABLE `visits`
  ADD CONSTRAINT `visits_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`patient_id`),
  ADD CONSTRAINT `visits_ibfk_2` FOREIGN KEY (`receptionist_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `vital_signs`
--
ALTER TABLE `vital_signs`
  ADD CONSTRAINT `vital_signs_ibfk_1` FOREIGN KEY (`visit_id`) REFERENCES `visits` (`visit_id`),
  ADD CONSTRAINT `vital_signs_ibfk_2` FOREIGN KEY (`recorded_by`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
