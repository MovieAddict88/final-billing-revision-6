<?php
session_start();
require_once 'config/dbconnection.php';
require_once 'includes/customer_header.php';
require_once 'includes/classes/admin-class.php';

$admins = new Admins($dbh);

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'employer') {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['customer'])) {
    header('Location: index.php');
    exit();
}

$customer_id = $_GET['customer'];
$customer = $admins->getCustomerInfo($customer_id);
$unpaid_bills = $admins->fetchAllIndividualBill($customer_id) ?: [];

?>

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <div class="card-header">
                    <h3>Apply Discount for <?php echo htmlspecialchars($customer->full_name); ?></h3>
                </div>
                <div class="card-body">
                    <?php if (isset($error_message)): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <form action="apply_discount.php" method="POST">
                        <input type="hidden" name="customer" value="<?php echo $customer_id; ?>">
                        <h4>Open Balances</h4>
                        <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Month</th>
                                    <th>Amount</th>
                                    <th>Balance Due</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($unpaid_bills as $bill): ?>
                                    <?php
                                        $due_amount = (is_numeric($bill->balance) && (float)$bill->balance > 0) ? (float)$bill->balance : (float)$bill->amount;
                                        if ($bill->status === 'Paid' || $due_amount <= 0) {
                                            continue;
                                        }
                                    ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="bills[]" value="<?php echo $bill->id; ?>" data-balance="<?php echo htmlspecialchars(number_format($due_amount, 2, '.', '')); ?>">
                                        </td>
                                        <td><?php echo htmlspecialchars($bill->r_month); ?></td>
                                        <td><?php echo htmlspecialchars(number_format((float)$bill->amount, 2)); ?></td>
                                        <td><?php echo htmlspecialchars(number_format($due_amount, 2)); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        </div>
                        <div class="form-group">
                            <label for="amount">Discount Amount</label>
                            <input type="number" name="amount" id="amount" class="form-control" step="0.01" min="0" placeholder="Enter discount amount">
                        </div>
                        <div class="form-group">
                            <label for="payment_date">Discount Date</label>
                            <input type="date" name="payment_date" id="payment_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="payment_time">Discount Time</label>
                            <input type="time" name="payment_time" id="payment_time" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="reference_number">Discount Reference Number</label>
                            <input type="text" name="reference_number" id="reference_number" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Apply Discount</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/customer_footer.php';
?>
<script>
    (function() {
        var checkboxes = document.querySelectorAll('input[type="checkbox"][name="bills[]"]:not([disabled])');
        var amountInput = document.getElementById('amount');

        function updateAmountFromSelection() {
            var total = 0;
            checkboxes.forEach(function(cb) {
                if (cb.checked) {
                    var bal = parseFloat(cb.getAttribute('data-balance')) || 0;
                    total += bal;
                }
            });
            if (total > 0) {
                amountInput.value = total.toFixed(2);
            }
        }

        checkboxes.forEach(function(cb) {
            cb.addEventListener('change', updateAmountFromSelection);
        });
    })();
</script>