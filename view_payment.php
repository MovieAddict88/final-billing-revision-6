<?php
session_start();
require_once 'includes/header.php';
require_once 'includes/classes/admin-class.php';

$admins = new Admins($dbh);

if (!isset($_SESSION['admin_session'])) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['id'])) {
    header('Location: bills.php');
    exit();
}

$submission_id = $_GET['id'];
$submission = $admins->getPaymentSubmissionById($submission_id);
$customer = $admins->getCustomerInfo($submission->customer_id);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['approve'])) {
        $paid_at = $submission->payment_date ?: null;
        if ($admins->approvePayment($submission_id, $paid_at)) {
            echo "<script>window.opener.location.reload(); window.close();</script>";
            exit();
        } else {
            $error_message = "Failed to approve payment.";
        }
    } elseif (isset($_POST['reject'])) {
        if ($admins->rejectPayment($submission_id)) {
            echo "<script>window.opener.location.reload(); window.close();</script>";
            exit();
        } else {
            $error_message = "Failed to reject payment.";
        }
    }
}
?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h3>Payment Submission Details</h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <h4>Customer Information</h4>
                    <p><strong>Name:</strong> <?php echo $customer->full_name; ?></p>
                    <p><strong>Email:</strong> <?php echo $customer->email; ?></p>
                    <p><strong>Contact:</strong> <?php echo $customer->contact; ?></p>
                    <hr>
                    <h4>Submission Information</h4>
                    <p><strong>Total Submitted Amount:</strong> <?php echo number_format((float)$submission->total_amount, 2); ?></p>
                    <p><strong>Payment Method:</strong> <?php echo $submission->payment_method; ?></p>
                    <?php if ($submission->payment_method === 'Manual' && !empty($submission->employer_id)):
                        $employer_name = $admins->getEmployerNameById($submission->employer_id);
                        if ($employer_name): ?>
                            <p><strong>Paid by Employer:</strong> <?php echo htmlspecialchars($employer_name); ?></p>
                        <?php endif;
                    endif; ?>
                    <p><strong>Reference Number:</strong> <?php echo $submission->reference_number; ?></p>
                    <?php if (!empty($submission->payment_date)): ?>
                        <p><strong>Date:</strong> <?php echo htmlspecialchars(date('Y-m-d h:i:s A', strtotime($submission->payment_date))); ?></p>
                    <?php endif; ?>
                    <?php if ($submission->screenshot && file_exists($submission->screenshot)): ?>
                        <p><strong>Screenshot:</strong></p>
                        <img src="<?php echo $submission->screenshot; ?>" alt="Transaction Screenshot" class="img-fluid">
                    <?php endif; ?>
                    <hr>
                    <h4>Bills in this Submission</h4>
                    <?php
                        $items_request = $dbh->prepare("SELECT i.*, p.r_month, p.amount as bill_amount, p.balance FROM payment_submission_items i JOIN payments p ON i.payment_id = p.id WHERE i.submission_id = ?");
                        $items_request->execute([$submission_id]);
                        $items = $items_request->fetchAll();
                    ?>
                    <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th style="background-color: #284390; color: white;">Month</th>
                                <th style="background-color: #284390; color: white;">Bill Amount</th>
                                <th style="background-color: #284390; color: white;">Current Balance</th>
                                <th style="background-color: #284390; color: white;">Amount to be Paid</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($items): foreach ($items as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item->r_month); ?></td>
                                <td><?php echo number_format((float)$item->bill_amount, 2); ?></td>
                                <td><?php echo number_format((float)$item->balance, 2); ?></td>
                                <td><?php echo number_format((float)$item->amount_paid, 2); ?></td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="4" class="text-center">No bills found in this submission.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                    </div>
                    <hr>
                    <?php if ($submission->status == 'pending'): ?>
                    <form action="" method="POST">
                        <button type="submit" name="approve" class="btn btn-success">Approve</button>
                        <button type="submit" name="reject" class="btn btn-danger">Reject</button>
                    </form>
                    <?php else: ?>
                        <div class="alert alert-info">This submission has already been <?php echo $submission->status; ?>.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>