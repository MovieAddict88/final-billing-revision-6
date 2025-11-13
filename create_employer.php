<?php
require_once 'config/dbconnection.php';
require_once 'includes/classes/session.php';
require_once 'includes/classes/admin-class.php';

$dbh = new Dbconnect();
$admins = new Admins($dbh);

$username = 'testemployer';
$password = 'password';
$email = 'testemployer@example.com';
$fullname = 'Test Employer';
$address = 'Test Address';
$contact = '1234567890';
$role = 'employer';

if ($admins->adminExists($username)) {
    echo "User already exists.\n";
} else {
    if ($admins->addNewAdmin($username, $password, $email, $fullname, $address, $contact, $role)) {
        echo "User created successfully.\n";
    } else {
        echo "Failed to create user.\n";
    }
}
