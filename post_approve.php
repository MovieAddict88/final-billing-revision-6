<?php
require_once "includes/headx.php";
if (!isset($_SESSION['admin_session'])) {
    $commons->redirectTo(SITE_PATH . 'login.php');
}
require_once "includes/classes/admin-class.php";
$admins = new Admins($dbh);

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Sanitize and retrieve POST data
    $customer_id = isset($_POST['customer']) ? filter_var($_POST['customer'], FILTER_SANITIZE_NUMBER_INT) : null;
    $bill_ids_raw = isset($_POST['bills']) ? $_POST['bills'] : '';
    $bill_ids = !empty($bill_ids_raw) ? explode(',', $bill_ids_raw) : [];
    $months = isset($_POST['months']) && is_array($_POST['months']) ? $_POST['months'] : [];
    $total_due = isset($_POST['total']) ? filter_var($_POST['total'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : 0;
    $discount_name = isset($_POST['discount_name']) ? $_POST['discount_name'] : '';
    $discount_amount = isset($_POST['discount']) ? (float)$_POST['discount'] : 0;

    $info = $admins->getCustomerInfo($customer_id);
    $package_id = $info->package_id;
    $packageInfo = $admins->getPackageInfo($package_id);

    $final_amount_due = $total_due - $discount_amount;

    // Check if it's an AJAX request
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
        // It's an AJAX request, so process the payment
        $bill_months = implode(', ', $months);
        $bill_ids_string = implode(',', $bill_ids);
        if ($admins->billPay($customer_id, $bill_ids_string, $bill_months, $discount_amount, $final_amount_due)) {
            // After successful payment, regenerate the statement of account view
            $info = $admins->getCustomerInfo($customer_id);
            $id = $customer_id; // Set $id for _statement.php
            $action = 'pay'; // Set $action for _statement.php
            include "_statement.php";
        } else {
            // Handle payment failure
            echo "Payment failed. Please try again.";
        }
        exit; // Terminate script execution for AJAX request
    }

    // Original non-AJAX response
?>
    <div class="statement-container">
        <div class="header-container">
            <div class="logo">
                <img src="component/orig_cs.png" alt="Company Logo" style="max-width: 100px;">
            </div>
            <div class="company-info">
                <strong>CORNERSTONE INNOVATE TECH SOL</strong><br>
                #11 Cassa Apartment Mambog IV, Bacoor, Cavite<br>
                Brix Bryan S. Villas-Prop.<br>
                NON-VAT Reg. TIN: 434-028-840-000
            </div>
        </div>
        <div class="statement-title"><span>STATEMENT OF ACCOUNT</span></div>
        <p style="text-align: right;">Date: <?= htmlspecialchars(date("F d, Y")) ?></p>
        <div class="customer-details-grid">
            <strong>Name:</strong>
            <span><?= htmlspecialchars($info->full_name) ?></span>

            <strong>Address:</strong>
            <span><?= htmlspecialchars($info->address) ?></span>

            <strong>CP #:</strong>
            <span><?= htmlspecialchars($info->contact) ?></span>
        </div>

        <table class="account-summary">
            <tbody>
                <tr>
                    <td class="blue-bg"><b>Plan</b></td>
                    <td><?= htmlspecialchars($packageInfo->name) ?></td>
                </tr>
                <tr>
                    <td class="blue-bg"><strong>Amount</strong></td>
                    <td>Php<?= htmlspecialchars(number_format($total_due, 2)) ?></td>
                </tr>
                <?php if ($discount_amount > 0) : ?>
                    <tr>
                        <td class="blue-bg"><strong><?= htmlspecialchars($discount_name) ?></strong></td>
                        <td>- Php<?= htmlspecialchars(number_format($discount_amount, 2)) ?></td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td class="blue-bg"><strong>Total Amount Due</strong></td>
                    <td>Php<?= htmlspecialchars(number_format($final_amount_due, 2)) ?></td>
                </tr>
            </tbody>
        </table>
        <div class="total-due">
            <span>TOTAL AMOUNT DUE</span>
            <span class="leader"></span>
            <span>Php<?= htmlspecialchars(number_format($final_amount_due, 2)) ?></span>
        </div>
    </div>
<?php
} else {
    echo "Invalid request.";
}
?>
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 40px;
        color: #000;
    }

    .statement-container {
        max-width: 800px;
        margin: auto;
        padding: 20px;
    }

    .header-container {
        text-align: center;
        height: 120px;
        /* Adjust as needed */
        margin-bottom: 20px;
    }

    .header-container .logo {
        display: inline-block;
        vertical-align: middle;
    }

    .header-container .company-info {
        display: inline-block;
        vertical-align: middle;
        text-align: left;
        margin-left: 15px;
        font-size: 12px;
        line-height: 1.4;
    }

    .header-container .company-info strong {
        font-size: 24px;
        font-weight: bold;
    }

    .statement-title {
        text-align: center;
        font-size: 24px;
        font-weight: bold;
        margin: 40px 0;
    }

    .statement-title span {
        border-bottom: 2px solid #000;
        padding-bottom: 5px;
    }

    .customer-details-grid {
        display: grid;
        grid-template-columns: 100px 1fr;
        gap: 5px 10px;
        margin-bottom: 20px;
    }

    .customer-details-grid strong {
        font-weight: bold;
    }

    .account-summary {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .account-summary th,
    .account-summary td {
        border: 1px solid #000;
        padding: 8px;
        text-align: left;
    }

    .account-summary td:nth-child(2) {
        text-align: right;
    }

    .total-due {
        display: flex;
        justify-content: space-between;
        font-size: 18px;
        font-weight: bold;
        margin-top: 20px;
    }

    .total-due .leader {
        flex-grow: 1;
        border-bottom: 2px dotted #000;
        margin: 0 10px;
    }
    .blue-bg {
        background-color: #007bff !important;
        color: white !important;
    }
</style>
