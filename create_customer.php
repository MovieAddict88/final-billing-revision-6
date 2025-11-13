<?php
require_once 'config/dbconnection.php';
require_once 'includes/classes/admin-class.php';

$dbh = new Dbconnect();
$admins = new Admins($dbh);

$full_name = 'Test Customer';
$nid = '1234567890';
$account_number = '9876543210';
$address = 'Test Address';
$conn_location = 'Test Location';
$email = 'testcustomer@example.com';
$package = 1; // Assuming package with ID 1 exists
$ip_address = '127.0.0.1';
$conn_type = 'Test';
$contact = '1234567890';
$login_code = 'testlogincode';
$employer_id = 38;
$start_date = '2025-01-01';
$due_date = '2025-01-15';
$end_date = '2025-02-01';

if ($admins->addCustomer($full_name, $nid, $account_number, $address, $conn_location, $email, $package, $ip_address, $conn_type, $contact, $login_code, $employer_id, $start_date, $due_date, $end_date)) {
    echo "Customer created successfully.\n";
} else {
    echo "Failed to create customer.\n";
}
