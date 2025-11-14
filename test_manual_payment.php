<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'config/dbconnection.php';
require_once 'includes/classes/admin-class.php';
require_once 'includes/classes/session.php';

$dbh = new Dbconnect();
$admins = new Admins($dbh);

// Mock session for employer
$_SESSION['user_id'] = 37; // Assuming employer with ID 37 exists
$_SESSION['user_role'] = 'employer';

// Test Data
$customer_id = 18;
$amount = 100;
$reference_number = 'TEST' . uniqid();
$payment_method = 'GCash';
$payment_date = date('Y-m-d');
$payment_time = date('H:i:s');

// Fetch unpaid bills for the customer
$unpaid_bills = $admins->fetchAllIndividualBill($customer_id, 'Unpaid');
if (empty($unpaid_bills)) {
    echo "No unpaid bills found for customer {$customer_id}. Cannot run test.\n";
    // Let's create a bill to test with
    $admins->billGenerate($customer_id, 'December 2025', 500);
    $unpaid_bills = $admins->fetchAllIndividualBill($customer_id, 'Unpaid');
    if(empty($unpaid_bills)) {
        echo "Failed to create a test bill. Exiting.\n";
        exit;
    }
}

$selected_bills = [$unpaid_bills[0]->id];
$r_months = [$unpaid_bills[0]->id => $unpaid_bills[0]->r_month];


echo "Running test: Submitting a manual payment...\n";

$result = $admins->processManualPayment(
    $customer_id,
    $_SESSION['user_id'],
    $amount,
    $reference_number,
    $selected_bills,
    $payment_method,
    null, // screenshot
    $payment_date,
    $payment_time,
    $r_months
);

if ($result) {
    echo "Test PASSED: processManualPayment completed successfully.\n";

    // Verify that a submission record was created
    $stmt = $dbh->prepare("SELECT * FROM payment_submissions WHERE reference_number = ?");
    $stmt->execute([$reference_number]);
    $submission = $stmt->fetch(PDO::FETCH_OBJ);

    if ($submission) {
        echo "Verification PASSED: Payment submission found in the database.\n";

        // Verify that a submission item was created
        $stmt = $dbh->prepare("SELECT * FROM payment_submission_items WHERE submission_id = ?");
        $stmt->execute([$submission->id]);
        $item = $stmt->fetch(PDO::FETCH_OBJ);

        if ($item) {
            echo "Verification PASSED: Payment submission item found in the database.\n";
        } else {
            echo "Verification FAILED: No payment submission item found for submission ID {$submission->id}.\n";
        }

    } else {
        echo "Verification FAILED: No payment submission found with reference number {$reference_number}.\n";
    }

} else {
    echo "Test FAILED: processManualPayment returned false.\n";
}

?>
