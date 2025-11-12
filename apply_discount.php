<?php
session_start();
require_once 'config/dbconnection.php';
require_once 'includes/classes/admin-class.php';

$admins = new Admins($dbh);

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'employer') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customer_id = $_POST['customer'];
    $employer_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $reference_number = $_POST['reference_number'];
    $selected_bills = isset($_POST['bills']) ? $_POST['bills'] : [];
    $payment_date = $_POST['payment_date'];
    $payment_time = $_POST['payment_time'];

    if (!empty($selected_bills)) {
        if ($admins->processDiscount($customer_id, $employer_id, $amount, $reference_number, $selected_bills, $payment_date, $payment_time)) {
            echo "<script>alert('Discount applied successfully.'); window.location.href = 'customers.php';</script>";
            exit();
        } else {
            echo "<script>alert('Failed to apply discount. Please try again.'); window.location.href = 'customers.php';</script>";
        }
    } else {
        echo "<script>alert('Please select at least one bill to apply the discount to.'); window.location.href = 'discount.php?customer=$customer_id';</script>";
    }
}
