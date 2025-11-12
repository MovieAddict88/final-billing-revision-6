
<?php
// This file contains the HTML for the statement of account.
// It is included in both pay.php and post_approve.php to ensure
// the same styling is applied in both places.
?>
<div class="statement-container">
    <?php
    if (isset($info) && is_object($info)) {
        $package_id = $info->package_id;
        $packageInfo = $admins->getPackageInfo($package_id);
        $advance_payment = $admins->getAdvancePaymentBalance($id);
    ?>
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

        <div class="no-print" style="text-align: right; margin-bottom: 20px;">
            <button class="btn btn-primary" onclick="window.print();">
                <i class="fa fa-print"></i> Print
            </button>
        </div>

        <p style="text-align: right;">Date: <?= htmlspecialchars(date("F d, Y")) ?></p>

        <?php
        $has_unpaid = false;
        $bills = $admins->fetchAllIndividualBill($id);
        if (isset($bills) && sizeof($bills) > 0) {
            foreach ($bills as $bill) {
                if ($bill->status == 'Unpaid') {
                    $has_unpaid = true;
                    break;
                }
            }
        }
        ?>
        <div class="customer-details-grid">
            <strong>Subject:</strong>
            <?php if ($action != 'bill' && $has_unpaid) : ?>
                <span style="text-decoration: underline; font-weight: bold;">NOTICE FOR DISCONNECTION</span>
            <?php else : ?>
                <span>PAYMENT TRANSACTION</span>
            <?php endif; ?>

            <strong>Name:</strong>
            <span><?= htmlspecialchars($info->full_name) ?></span>

            <strong>Address:</strong>
            <span><?= htmlspecialchars($info->address) ?></span>

            <strong>CP #:</strong>
            <span><?= htmlspecialchars($info->contact) ?></span>
        </div>

        <?php if ($action == 'bill') : ?>
            <table class="account-summary">
                <thead>
                    <tr>
                        <th class="blue-bg">Billing Period(<?= htmlspecialchars($packageInfo->name) ?>)</th>
                        <th class="blue-bg">Amount</th>
                        <th class="blue-bg">Balance</th>
                        <th class="blue-bg">Over</th>
                        <th class="blue-bg">Payment Paid Months</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $total_balance = 0;
                    $paymentHistory = $admins->fetchAllIndividualBillHistory($id);
                    $paymentLedger = $admins->fetchPaymentHistoryByCustomer($id);

                    $paymentsByBill = [];
                    foreach ($paymentLedger as $ledger) {
                        $paymentsByBill[$ledger->payment_id][] = $ledger;
                    }

                    if (!empty($paymentHistory)) {
                        foreach ($paymentHistory as $bill) {
                            $total_balance += $bill->balance;
                            $billing_period_display = $bill->r_month;

                            echo "<tr>";
                            echo "<td>" . htmlspecialchars($billing_period_display) . "</td>";
                            echo "<td>" . htmlspecialchars(number_format($bill->amount, 2)) . "</td>";
                            echo "<td>" . ($bill->balance > 0 ? htmlspecialchars(number_format($bill->balance, 2)) : '') . "</td>";
                            echo "<td></td>"; // Over column - logic to be added if necessary

                            echo "<td>";
                            if (isset($paymentsByBill[$bill->id])) {
                                foreach ($paymentsByBill[$bill->id] as $payment) {
                                    echo "#" . htmlspecialchars($payment->reference_number) . "<br>";
                                    echo "Paid ( " . htmlspecialchars(date('F d, Y / h:i A', strtotime($payment->paid_at))) . " ) " . htmlspecialchars($payment->payment_method) . "<br>";
                                }
                            } else {
                                echo "NO PAYMENT";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    }
                    ?>
                    <tr>
                        <td colspan="2" style="text-align: right; font-weight: bold;">BALANCE</td>
                        <td style="font-weight: bold;"><?= htmlspecialchars(number_format($total_balance, 2)) ?></td>
                        <td style="font-weight: bold;"><?= htmlspecialchars(number_format($advance_payment, 2)) ?></td>
                        <td style="font-weight: bold;"><?= htmlspecialchars(number_format($total_balance - $advance_payment, 2)) ?> - (TOTAL BALANCE)</td>
                    </tr>
                </tbody>
            </table>
            <div class="account-info">
                <p class="highlight"><strong>Account No. : <?= htmlspecialchars($info->account_number) ?></strong></p>
            </div>
        <?php else : ?>
            <table class="account-summary">
                <tbody>
                    <?php
                    $total_due = 0.00;
                    $bill_ids = [];
                    $monthArray = [];
                    $packageName = isset($packageInfo->name) ? $packageInfo->name : 'N/A';
                    $packageFee = isset($packageInfo->fee) ? $packageInfo->fee : 0;
                    $billing_period_display = "N/A";
                    $amount_display = $packageFee;

                    echo '<tr><td class="blue-bg"><b>Plan</b></td><td>' . htmlspecialchars($packageName) . '</td></tr>';

                    if (isset($bills) && sizeof($bills) > 0) {
                        $unpaid_bills = [];
                        foreach ($bills as $bill) {
                            if ($bill->status !== 'Paid') {
                                $due_amount = (is_numeric($bill->balance) && (float)$bill->balance > 0)
                                    ? (float)$bill->balance
                                    : (float)$bill->amount;
                                $total_due += $due_amount;
                                $unpaid_bills[] = $bill;
                            }
                        }

                        if (count($unpaid_bills) > 0) {
                            if (count($unpaid_bills) == 1) {
                                $bill = $unpaid_bills[0];
                                $billing_period_display = $bill->r_month;
                                $amount_display = $bill->amount;
                            } else {
                                $billing_period_display = "Multiple Unpaid Bills";
                                $amount_display = $total_due;
                            }
                        } else {
                            // If no unpaid bills, show the latest paid bill's period
                            $paid_bills = array_filter($bills, function($bill) {
                                return $bill->status === 'Paid';
                            });

                            if (!empty($paid_bills)) {
                                usort($paid_bills, function($a, $b) {
                                    return strtotime($b->g_date) - strtotime($a->g_date);
                                });
                                $latest_bill = $paid_bills[0];
                                $billing_period_display = $latest_bill->r_month;
                                $amount_display = 0.00;
                            }
                        }
                    } else {
                        // For new customers with no bills, assume current month's fee
                        $total_due = $packageFee;
                        if (!empty($info->start_date) && !empty($info->end_date)) {
                            $start_date = new DateTime($info->start_date);
                            $end_date = new DateTime($info->end_date);
                            $billing_period_display = $start_date->format('F d') . ' - ' . $end_date->format('F d, Y');
                        } else {
                            $billing_period_display = date("F d") . " - " . date("F d, Y", strtotime('+1 month'));
                        }
                    }
                    ?>
                    <tr>
                        <td class="blue-bg"><strong>Billing Period</strong></td>
                        <td><?= htmlspecialchars($billing_period_display) ?></td>
                    </tr>
                    <tr>
                        <td class="blue-bg"><strong>Amount</strong></td>
                        <td>Php<?= htmlspecialchars(number_format($amount_display, 2)) ?></td>
                    </tr>
                    <tr>
                        <td class="blue-bg"><strong>Total Amount Due</strong></td>
                        <td>Php<?= htmlspecialchars(number_format($total_due, 2)) ?></td>
                    </tr>
                </tbody>
            </table>

            <div class="total-due">
                <span>TOTAL AMOUNT DUE</span>
                <span class="leader"></span>
                <span>Php<?= htmlspecialchars(number_format($total_due, 2)) ?></span>
            </div>

            <div class="account-info">
                <p class="highlight"><strong>Account No. : <?= htmlspecialchars($info->account_number) ?></strong></p>
            </div>

            <?php if ($action != 'bill' && (isset($total_due) && $total_due > 0)) : ?>
                <div class="row no-print" id="payment-form-container">
                    <form class="form-inline" id="payment-form" action="post_approve.php" method="POST">
                        <input type="hidden" name="customer" value="<?= (isset($info->id) ? htmlspecialchars($info->id) : '') ?>">
                        <input type="hidden" name="bills" value="<?= htmlspecialchars(implode(',', array_map(function ($bill) {
                                                                    return $bill->id;
                                                                }, $unpaid_bills))) ?>">
                        <div class="form-group">
                            <label for="months"></label>
                            <select class="selectpicker" name="months[]" id="months" multiple required title="Select months">
                                <?php
                                if (!empty($unpaid_bills)) {
                                    foreach ($unpaid_bills as $bill) {
                                        echo '<option value="' . htmlspecialchars($bill->r_month) . '" selected>' . htmlspecialchars($bill->r_month) . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="sr-only" for="discount_name">Discount Name</label>
                            <input type="text" class="form-control" name="discount_name" id="discount_name" placeholder="Discount Name">
                        </div>
                        <div class="form-group">
                            <label class="sr-only" for="discount">Discount Amount</label>
                            <input type="number" class="form-control" name="discount" id="discount" placeholder="Discount Amount">
                        </div>
                        <div class="form-group">
                            <label class="sr-only" for="total">Amount</label>
                            <input type="number" class="form-control" name="total" id="total" placeholder="Amount" required="" value="<?= htmlspecialchars($total_due) ?>">
                        </div>
                        <button type="submit" class="btn btn-primary">Paid</button>
                    </form>
                </div>
            <?php endif; ?>
        <?php endif; ?>


        <div class="footer-notes">
            <p><em>We appreciate your prompt payment and value you as a customer.</em></p>
        </div>
        <hr>
        <div class="contact-info-container">
            <div class="contact-title">
                <strong>Contact us:</strong>
            </div>
            <table>
                <tr>
                    <td><strong>FB Page</strong></td>
                    <td>:</td>
                    <td>CORNER STONE INNOVATE TECH SOL</td>
                </tr>
                <tr>
                    <td><strong>Customer Service</strong></td>
                    <td>:</td>
                    <td>0951-6551142</td>
                </tr>
                <tr>
                    <td><strong>Billing Department</strong></td>
                    <td>:</td>
                    <td>0985-3429675</td>
                </tr>
            </table>
        </div>
    <?php } ?>
</div>
