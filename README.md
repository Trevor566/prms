# Nairobi Women's Hospital — Patient Record Management System (PRMS)

A web-based Patient Record Management System developed as a KNEC Diploma in 
Information Technology final project at KCA University.

## Project Overview

The system digitises patient record management at Nairobi Women's Hospital, 
replacing a manual paper-based process with an integrated web application 
covering all departments from reception through to billing.

## Tech Stack

- **Backend:** PHP 8
- **Database:** MySQL 8
- **Frontend:** HTML, CSS, Bootstrap 5, JavaScript
- **Environment:** XAMPP (Apache + MySQL)

## System Modules

| Module | Role | Features |
|---|---|---|
| Reception | Receptionist | Register patients, search records, billing |
| Nursing | Nurse | Record vital signs |
| Consultation | Doctor | Diagnosis, lab requests, prescriptions |
| Laboratory | Lab Technician | View pending tests, enter results |
| Pharmacy | Pharmacist | View and dispense prescriptions |
| Admin | Administrator | System overview, revenue reports |

## Database Tables

- `users` — system users and roles
- `patients` — patient biodata
- `visits` — each hospital visit
- `vital_signs` — nurse recordings
- `consultations` — doctor diagnosis and notes
- `lab_requests` — laboratory test requests and results
- `prescriptions` — medication prescriptions
- `billing` — payment and discharge records

## Installation

### Requirements
- XAMPP (Apache + MySQL + PHP)
- Web browser (Chrome, Firefox or Edge)

### Setup Steps

1. Clone this repository into your XAMPP htdocs folder:
git clone https://github.com/YOUR-USERNAME/prms.git

   Place it in `C:\xampp\htdocs\prms`

2. Import the database:
   - Open phpMyAdmin at `http://localhost/phpmyadmin`
   - Create a new database called `prms`
   - Click Import and select `database/prms.sql`

3. Configure the database connection:
   - Copy `config/db.example.php` to `config/db.php`
   - Update the credentials if needed (default XAMPP uses root with no password)

4. Start XAMPP and open your browser at:
http://localhost/prms/login.php


## Default Login Credentials

| Username | Password | Role |
|---|---|---|
| admin | password | Administrator |
| receptionist | password | Receptionist |
| nurse | password | Nurse |
| doctor | password | Doctor |
| labtech | password | Lab Technician |
| pharmacist | password | Pharmacist |

> **Note:** Change these passwords before deploying to a live environment.

## Developer

**Trevor Mbua Maringa**  
Diploma in Information Technology — KCA University   
GitHub: [github.com/Trevor566](https://github.com/Trevor566)
