<?php
include 'includes/header.php';
require_once "includes/classes/admin-class.php";

$admins = new Admins($dbh);

// Check user role from session
$user_role = $_SESSION['user_role'] ?? 'admin';

if ($user_role == 'employer') {
    // Employer Dashboard
    $employer_id = $_SESSION['user_id'];
    
    // Pagination setup
    $customers_per_page = 10;
    $current_page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $offset = ($current_page - 1) * $customers_per_page;
    
    // Fetch paginated customers
    $customers = $admins->fetchCustomersByEmployerPage($employer_id, $offset, $customers_per_page);
    $total_customers = $admins->countCustomersByEmployer($employer_id);
    $total_pages = ceil($total_customers / $customers_per_page);
    
    $products = $admins->fetchProductsByEmployer($employer_id);
?>
<h3 style="margin-top: 20px;">ACCOUNT MANAGER</h3>
<style>
    .table-custom thead {
        background-color: #008080;
        color: white;
    }
    .table-custom .btn-primary {
        background-color: #007bff;
        border-color: #007bff;
    }

    .action-buttons {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 5px;
        min-width: 120px;
    }

    .action-btn {
        min-height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        padding: 6px 4px;
        white-space: nowrap;
        border: none;
        border-radius: 4px;
        transition: all 0.2s ease;
        text-decoration: none;
        text-align: center;
    }

    .action-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        text-decoration: none;
        color: inherit;
    }

    /* Specific button colors */
    .btn-warning { background-color: #ffc107; border-color: #ffc107; color: #212529; }
    .btn-info { background-color: #17a2b8; border-color: #17a2b8; color: white; }
    .btn-success { background-color: #28a745; border-color: #28a745; color: white; }
    .btn-primary { background-color: #007bff; border-color: #007bff; color: white; }
    .btn-danger { 
        background-color: #dc3545; 
        border-color: #dc3545; 
        color: white; 
        grid-column: span 2;
        width: 100%;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .progress-container {
        width: 100%;
        padding: 10px;
    }
    .progress-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
    }
    .progress-label {
        width: 80px;
        font-size: 16px;
    }
    .progress-bar-background {
        flex-grow: 1;
        background-color: #f0f0f0;
        height: 20px;
        border-radius: 5px;
        margin: 0 10px;
    }
    .progress-bar-fill {
        height: 100%;
        border-radius: 5px;
        text-align: right;
        color: white;
        font-weight: bold;
        padding-right: 5px;
    }
    .progress-value {
        width: 40px;
        font-size: 16px;
        text-align: right;
    }

    /* Enhanced table styling for professional look */
    .table-container {
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        overflow: hidden;
    }
    
    .professional-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
        min-width: 1200px; /* Force horizontal scroll on smaller screens */
    }
    
    .professional-table thead th {
        background: #284390;
        color: white;
        font-weight: 600;
        padding: 12px 15px;
        text-align: left;
        border: none;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .professional-table tbody tr {
        border-bottom: 1px solid #e8e8e8;
        transition: background-color 0.2s ease;
    }
    
    .professional-table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .professional-table tbody tr:nth-child(even) {
        background-color: #fafafa;
    }
    
    .professional-table tbody tr:nth-child(even):hover {
        background-color: #f1f3f4;
    }
    
    .professional-table td {
        padding: 12px 15px;
        vertical-align: middle;
        border: none;
        word-wrap: break-word;
        max-width: 200px;
    }
    
    .professional-table .text-nowrap {
        white-space: nowrap;
    }
    
    .professional-table .login-code {
        font-family: 'Courier New', monospace;
        font-size: 12px;
        word-break: break-all;
        max-width: 150px;
    }
    
    .professional-table .amount {
        text-align: right;
        font-family: 'Courier New', monospace;
        font-weight: 600;
    }
    
    .professional-table .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
    }
    
    .status-paid { background: #d4edda; color: #155724; }
    .status-unpaid { background: #f8d7da; color: #721c24; }
    .status-balance { background: #fff3cd; color: #856404; }
    .status-pending { background: #cce5ff; color: #004085; }
    
    .scrollable-table-wrapper {
        overflow-x: auto;
        overflow-y: visible;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        margin-bottom: 20px;
        -webkit-overflow-scrolling: touch;
    }
    
    .scrollable-table-wrapper::-webkit-scrollbar {
        height: 8px;
    }
    
    .scrollable-table-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }
    
    .scrollable-table-wrapper::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }
    
    .scrollable-table-wrapper::-webkit-scrollbar-thumb:hover {
        background: #a8a8a8;
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        .professional-table {
            font-size: 12px;
            min-width: 1000px;
        }
        
        .professional-table th,
        .professional-table td {
            padding: 8px 10px;
        }
        
        .action-btn {
            font-size: 11px;
            padding: 5px 3px;
            min-height: 30px;
        }
        
        .professional-table .login-code {
            font-size: 10px;
            max-width: 120px;
        }
        
        .action-buttons {
            grid-template-columns: 1fr;
            gap: 4px;
        }
        
        .btn-danger {
            grid-column: span 1;
        }
    }
    
    @media (min-width: 1200px) {
        .professional-table {
            min-width: auto;
            width: 100%;
        }
        
        .scrollable-table-wrapper {
            overflow-x: visible;
        }
    }
    
    /* Pagination Styling */
    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .pagination-container nav {
        flex: 1;
        display: flex;
        justify-content: center;
    }
    
    .pagination-info {
        min-width: 200px;
        text-align: right;
    }
    
    .pagination {
        margin: 0;
    }
    
    .pagination .page-link {
        color: #2c3e50;
        border-color: #dee2e6;
        padding: 6px 12px;
        transition: all 0.2s;
    }
    
    .pagination .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #2c3e50;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #2c3e50;
        border-color: #2c3e50;
        color: white;
        font-weight: 600;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
    }
    
    @media (max-width: 768px) {
        .pagination-container {
            flex-direction: column;
            text-align: center;
        }
        
        .pagination-info {
            text-align: center;
            width: 100%;
        }
        
        .pagination .page-link {
            padding: 4px 8px;
            font-size: 12px;
        }
    }

    /* Chart legend styles for admin dashboard */
    .chart-legend {
        width: 100%;
    }
    
    .legend-items-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
        width: 100%;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        padding: 6px 0;
    }
    
    .legend-color {
        width: 20px;
        height: 10px;
        margin-right: 8px;
        border-radius: 3px;
        flex-shrink: 0;
    }
    
    .legend-text {
        flex: 1;
        min-width: 0;
        word-wrap: break-word;
    }

    /* === Tablet / Small Laptop (768px - 1024px) === */
    @media (min-width: 768px) {
        .legend-items-container {
            grid-template-columns: repeat(2, 1fr);
            max-height: 400px;
            overflow-y: auto;
        }
    }

    /* === Standard Laptop / Desktop (1024px and above) === */
    @media (min-width: 1024px) {
        .legend-items-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* === Large Desktop / Smart TV (1400px and above) === */
    @media (min-width: 1400px) {
        .legend-items-container {
            grid-template-columns: repeat(4, 1fr);
        }
        .chart-legend {
            font-size: 1.1em;
        }
    }
</style>

<div class="row employer-stack-laptop">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Your Assigned Customers</h4>
            </div>
            <div class="panel-body">
                <div class="scrollable-table-wrapper">
                    <table class="professional-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Contact</th>
                                <th>Login Code</th>
                                <th>Due Date</th>
                                <th class="text-nowrap">Paid (₱)</th>
                                <th class="text-nowrap">Balance (₱)</th>
                                <th class="text-nowrap">Over (₱)</th>
                                <th>Status</th>
                                <th>Actions</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($customers): ?>
                                <?php foreach ($customers as $customer): ?>
                                    <tr>
                                        <td class="text-nowrap"><?php echo htmlspecialchars($customer->full_name); ?></td>
                                        <td><?php echo htmlspecialchars($customer->address); ?></td>
                                        <td class="text-nowrap"><?php echo htmlspecialchars($customer->contact); ?></td>
                                        <td class="login-code"><?php echo htmlspecialchars($customer->login_code); ?></td>
                                        <td class="text-nowrap"
                                            <?php
                                            $dueDate = $customer->due_date;
                                            $status = $customer->status;
                                            $style = '';
                                            if ($status == 'Paid') {
                                                $style = 'style="background-color: #D4EFDF;"'; // Light Green
                                            } else if ($dueDate) {
                                                $today = new DateTime();
                                                $dueDateObj = new DateTime($dueDate);
                                                $today->setTime(0,0,0);
                                                $dueDateObj->setTime(0,0,0);

                                                if ($dueDateObj < $today) {
                                                    $style = 'style="background-color: #8B0000; color: white;"'; // Dark Red for overdue
                                                } else {
                                                    $interval = $today->diff($dueDateObj);
                                                    $days = $interval->days;

                                                    if ($days <= 3) {
                                                        $style = 'style="background-color: #FADBD8;"'; // Light Red
                                                    } elseif ($days <= 7) {
                                                        $style = 'style="background-color: #FDEBD0;"'; // Light Orange
                                                    } else {
                                                        $style = 'style="background-color: #D6EAF8;"'; // Light Blue
                                                    }
                                                }
                                            }
                                            echo $style;
                                            ?>
                                        ><?php echo htmlspecialchars($customer->due_date); ?></td>
                                        <td class="amount"><?php echo htmlspecialchars(number_format($customer->total_paid, 2)); ?></td>
                                        <td class="amount"><?php echo htmlspecialchars(number_format($customer->total_balance, 2)); ?></td>
                                        <td class="amount"><?php echo htmlspecialchars(number_format($customer->advance_payment, 2)); ?></td>
                                        <td>
                                            <?php
                                            $status_class = 'status-' . strtolower($customer->status);
                                            echo '<span class="status-badge ' . $status_class . '">' . htmlspecialchars($customer->status) . '</span>';
                                            ?>
                                        </td>
                                        <td>
    <div class="action-buttons">
        <a href="manual_payment.php?customer=<?php echo $customer->id; ?>" class="btn btn-warning btn-sm action-btn">Pay</a>
        <a href="discount.php?customer=<?php echo $customer->id; ?>" class="btn btn-info btn-sm action-btn">Discount</a>
        <button type="button" class="btn btn-success btn-sm action-btn" data-toggle="modal" data-target="#edit-<?php echo $customer->id; ?>">Edit</button>
        <button type="button" class="btn btn-primary btn-sm action-btn" onclick='openRemarkModal(<?php echo $customer->id; ?>, <?php echo json_encode($customer->remarks); ?>)'>Remark</button>
        <a href="pay.php?customer=<?php echo $customer->id; ?>&action=bill" class="btn btn-primary btn-sm action-btn">Invoice</a>
        <a href="pay.php?customer=<?php echo $customer->id; ?>" class="btn btn-info btn-sm action-btn">Bill</a>
        <a href="disconnect_customer.php?customer_id=<?php echo $customer->id; ?>" class="btn btn-danger btn-sm action-btn">DISCONNECT</a>
    </div>
    
											<div class="fade modal" id="edit-<?php echo $customer->id; ?>">
												<div class="modal-dialog" role="document">
													<div class="modal-content">
														<div class="modal-header">
															<button type="button" class="close" data-dismiss="modal">×</button>
															<h4>Edit Details</h4>
														</div>
														<form method="POST">
															<div class="modal-body">
																<input type="hidden" id="<?php echo $customer->id; ?>" value="<?php echo $customer->id; ?>">

																<div class="form-group has-success">
																	<label for="name">Full Name</label>
																	<input type="text" class="form-control" id="fnm-<?php echo $customer->id; ?>"  value="<?php echo $customer->full_name; ?>" required>
																</div>
																<div class="form-group">
																	<label for="nid">NID</label>
																	<input type="text" class="form-control" id="nid-<?php echo $customer->id; ?>"  value="<?php echo $customer->nid; ?>" required>
																</div>
																<div class="form-group">
																	<label for="account_number">Account Number</label>
																	<input type="text" class="form-control" id="acn-<?php echo $customer->id; ?>"  value="<?php echo $customer->account_number; ?>" required>
																</div>
																<div class="form-group">
																	<label for="address">Address</label>
																	<input type="text" class="form-control" id="ad-<?php echo $customer->id; ?>"  value="<?php echo $customer->address; ?>" required>
																</div>
																<div class="form-group">
																<label for="package">Select Package</label>
																	<select class="form-control form-control-sm" name="package" id="pk-<?php echo $customer->id; ?>">
																	<?php
																		$packageInfo = $admins->getPackageInfo($customer->package_id);
																		$package_name = $packageInfo ? $packageInfo->name : 'N/A';
																	?>
																	<option value='<?php echo $customer->package_id; ?>'><?php echo $package_name; ?></option>
																	<?php
																		$packages = $admins->getPackages();
																		if (isset($packages) && sizeof($packages) > 0){
																			foreach ($packages as $package) { ?>
																			<option value='<?php echo $package->id; ?>'><?php echo $package->name; ?></option>
																	<?php }} ?>
																	</select>
																</div>
																<div class="form-group">
																	<label for="ip">IP Address</label>
																	<input type="text" class="form-control" id="ip-<?php echo $customer->id; ?>"  value="<?php echo $customer->ip_address; ?>" required>
																</div>
																<div class="form-group">
																	<label for="contact">Contact</label>
																	<input type="text" class="form-control" id="con-<?php echo $customer->id; ?>"   value="<?php echo $customer->contact; ?>" required>
																</div>
																<div class="form-group">
																	<label for="start_date">Start Date</label>
																	<input type="date" class="form-control" id="start_date-<?php echo $customer->id; ?>"   value="<?php echo $customer->start_date; ?>" required onchange="updateEndDate(<?php echo $customer->id; ?>)">
																</div>
																<div class="form-group">
																	<label for="end_date">End Date</label>
																	<input type="date" class="form-control" id="end_date-<?php echo $customer->id; ?>"   value="<?php echo $customer->end_date; ?>">
																</div>
																<div class="form-group">
																	<label for="due_date">Due Date</label>
																	<input type="date" class="form-control" id="due_date-<?php echo $customer->id; ?>"   value="<?php echo $customer->due_date; ?>" required>
																</div>
																<div class="form-group">
																	<label for="conlocation">Connection Location</label>
																	<input type="text" class="form-control" id="conn_loc-<?php echo $customer->id; ?>"   value="<?php echo $customer->conn_location; ?>" required>
																</div>
																<div class="form-group">
																	<label for="type">Connection Type</label>
																	<input type="text" class="form-control" id="ct-<?php echo $customer->id; ?>"   value="<?php echo $customer->conn_type; ?>" required>
																</div>
																<div class="form-group">
																	<label for="email">Email</label>
																	<input type="text" class="form-control" id="em-<?php echo $customer->id; ?>"   value="<?php echo $customer->email; ?>" required>
																</div>
															</div>
															<div class="modal-footer">
																<button type="button"  onclick="updateData(<?php echo $customer->id; ?>)" class="btn btn-primary">Update</button>
																<a href="#" class="btn btn-warning" data-dismiss="modal">Cancel</a>
															</div>
														</form>
													</div>
												</div>
											</div>
                                        </td>
										<td><?php echo htmlspecialchars($customer->remarks ?? ''); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fa fa-users fa-2x mb-2"></i><br>
                                            No customers found for this location.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if ($total_pages > 1): ?>
                <div class="pagination-container">
                    <nav aria-label="Customer pagination">
                        <ul class="pagination pagination-sm">
                            <?php if ($current_page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=1" aria-label="First">
                                        <span aria-hidden="true">&laquo;&laquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $current_page - 1; ?>" aria-label="Previous">
                                        <span aria-hidden="true">&laquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <?php
                            // Show page numbers with ellipsis for large page counts
                            $page_range = 2; // Show 2 pages on each side of current page
                            $start_page = max(1, $current_page - $page_range);
                            $end_page = min($total_pages, $current_page + $page_range);
                            
                            // Show first page and ellipsis if needed
                            if ($start_page > 1) {
                                echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                                if ($start_page > 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }
                            
                            // Show page numbers
                            for ($i = $start_page; $i <= $end_page; $i++):
                            ?>
                                <li class="page-item <?php echo ($i == $current_page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php
                            endfor;
                            
                            // Show last page and ellipsis if needed
                            if ($end_page < $total_pages) {
                                if ($end_page < $total_pages - 1) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '">' . $total_pages . '</a></li>';
                            }
                            ?>
                            
                            <?php if ($current_page < $total_pages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $current_page + 1; ?>" aria-label="Next">
                                        <span aria-hidden="true">&raquo;</span>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $total_pages; ?>" aria-label="Last">
                                        <span aria-hidden="true">&raquo;&raquo;</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                    <div class="pagination-info">
                        <span class="text-muted">
                            Showing <?php echo (($current_page - 1) * $customers_per_page + 1); ?> 
                            to <?php echo min($current_page * $customers_per_page, $total_customers); ?> 
                            of <?php echo $total_customers; ?> customers
                        </span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="row employer-stack-laptop">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Products Availed by Your Customers</h4>
            </div>
            <div class="panel-body">
                <div class="scrollable-table-wrapper">
                    <table class="professional-table">
                        <thead>
                            <tr>
                                <th>Package Name</th>
                                <th class="text-nowrap">Fee (₱)</th>
                                <th>Number of Customers</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($products): ?>
                                <?php foreach ($products as $product): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($product->name); ?></td>
                                        <td class="amount"><?php echo htmlspecialchars(number_format($product->fee, 2)); ?></td>
                                        <td class="text-center">
                                            <span class="badge bg-primary" style="font-size: 14px; padding: 6px 12px;">
                                                <?php echo htmlspecialchars($product->customer_count); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fa fa-box fa-2x mb-2"></i><br>
                                            No products found for this location.
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">Overview - Subscribers Count</h4>
            </div>
            <div class="panel-body" id="customer-status-progress-bars">
                <!-- Progress bars will be generated here by JavaScript -->
            </div>
        </div>
    </div>
</div>
<?php
} else {
    // Admin Dashboard (unchanged)
?>
<!-- Admin dashboard code remains the same -->
<style>
    .chart-legend {
        width: 100%;
    }
    
    .legend-items-container {
        display: grid;
        grid-template-columns: 1fr;
        gap: 8px;
        width: 100%;
    }
    
    .legend-item {
        display: flex;
        align-items: center;
        padding: 6px 0;
    }
    
    .legend-color {
        width: 20px;
        height: 10px;
        margin-right: 8px;
        border-radius: 3px;
        flex-shrink: 0;
    }
    
    .legend-text {
        flex: 1;
        min-width: 0;
        word-wrap: break-word;
    }

    /* === Tablet / Small Laptop (768px - 1024px) === */
    @media (min-width: 768px) {
        .legend-items-container {
            grid-template-columns: repeat(2, 1fr);
            max-height: 400px;
            overflow-y: auto;
        }
    }

    /* === Standard Laptop / Desktop (1024px and above) === */
    @media (min-width: 1024px) {
        .legend-items-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    /* === Large Desktop / Smart TV (1400px and above) === */
    @media (min-width: 1400px) {
        .legend-items-container {
            grid-template-columns: repeat(4, 1fr);
        }
        .chart-legend {
            font-size: 1.1em;
        }
    }
</style>

<div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-heading">
        Balance &amp; Cash Collection Chart:
        </div>
        <div class="panel-body">
            <canvas id="myChart">
					
				</canvas>
        </div>
    </div>
</div>
<div class="col-md-6">
    <div class="panel panel-default">
        <div class="panel-heading">
        Monthly Bill Collection : 2016
        </div>
        <div class="panel-body">
            <canvas id="chart2">
					
				</canvas>
        </div>
    </div>
</div>
<?php
} // End of role check
include 'includes/footer.php';
?>

<script type="text/javascript">
<?php if ($user_role == 'employer'): ?>
function updateEndDate(id) {
    var startDate = $('#start_date-' + id).val();
    if (startDate) {
        var date = new Date(startDate);
        date.setMonth(date.getMonth() + 1);
        var year = date.getFullYear();
        var month = ('0' + (date.getMonth() + 1)).slice(-2);
        var day = ('0' + date.getDate()).slice(-2);
        $('#end_date-' + id).val(year + '-' + month + '-' + day);
    }
}

function updateData(str){
    var id = str;
    var full_name = $('#fnm-'+str).val();
    var nid = $('#nid-'+str).val();
    var account_number = $('#acn-'+str).val();
    var address = $('#ad-'+str).val();
    var package = $('#pk-'+str).val();
    var conn_location = $('#conn_loc-'+str).val();
    var email = $('#em-'+str).val();
    var ip_address = $('#ip-'+str).val();
    var conn_type = $('#ct-'+str).val();
    var contact = $('#con-'+str).val();
    var start_date = $('#start_date-'+str).val();
    var due_date = $('#due_date-'+str).val();
    var end_date = $('#end_date-'+str).val();

    $.ajax({
        method:"POST",
        url: "customers_approve.php?p=edit",
        data: "full_name="+full_name+"&nid="+nid+"&account_number="+account_number+"&address="+address+"&conn_location="+conn_location+"&email="+email+"&package="+package+"&ip_address="+ip_address+"&conn_type="+conn_type+"&contact="+contact+"&employer=<?php echo $employer_id; ?>&id="+id+"&start_date="+start_date+"&due_date="+due_date+"&end_date="+end_date,
        success: function (data){
            location.reload();
        }
    });
}
<?php endif; ?>
    $(document).ready(function() {
        <?php if ($user_role == 'employer'): ?>
        $.ajax({
            url: "customer_status_chart_data.php",
            method: "GET",
            dataType: 'JSON',
            success: function(data) {
                var container = $('#customer-status-progress-bars');
                if (!container.length) return;

                var statusConfig = {
                    'Paid': { label: 'Paid', color: '#28a745', order: 1 },
                    'Balance': { label: 'Balance', color: '#fd7e14', order: 2 },
                    'Unpaid': { label: 'Unpaid', color: '#8B0000', order: 3 },
                    'Rejected': { label: 'Reject', color: '#dc3545', order: 4 },
                    'Prospects': { label: 'Prospect', color: '#6c757d', order: 5 },
                    'Pending': { label: 'Pending', color: '#ffc107', order: 6 }
                };

                var total = data.reduce((acc, item) => acc + parseInt(item.count), 0);

                var displayData = data.map(item => {
                    var config = statusConfig[item.status] || {
                        label: item.status,
                        color: '#333',
                        order: 99
                    };
                    return {
                        ...item,
                        ...config,
                        count: parseInt(item.count)
                    };
                });

                displayData.sort((a, b) => a.order - b.order);

                var progressHtml = '<div class="progress-container">';
                displayData.forEach(function(item) {
                    var percentage = (total > 0) ? (item.count / total) * 100 : 0;
                    progressHtml += `
                        <div class="progress-item">
                            <div class="progress-label">${item.label}</div>
                            <div class="progress-bar-background">
                                <div class="progress-bar-fill" style="width: ${percentage}%; background-color: ${item.color};"></div>
                            </div>
                            <div class="progress-value">${item.count}</div>
                        </div>
                    `;
                });
                progressHtml += '</div>';

                container.html(progressHtml);
            },
            error: function(data) {
                console.log(data);
            }
        });
        <?php else: ?>
        // Admin dashboard JavaScript remains the same
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Sat', 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri'],
                datasets: [{
                    label: 'Cash Collection',
                    data: [12, 19, 3, 17, 6, 3, 20],
                    backgroundColor: "rgba(153,255,51,0.6)"
                }, {
                    label: 'Balance',
                    data: [2, 29, 5, 5, 2, 3, 10],
                    backgroundColor: "rgba(245,0,0,0.6)"
                }]
            }
        });
        var ctx = document.getElementById('chart2').getContext('2d');
        var myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Monthly Bill Collection',
                    data: [50000, 60000, 30000, 45000, 48000, 38000, 80000, 50000, 0],
                    backgroundColor: "rgba(0,255,51,0.6)"
                }]
            }
        });


        $.ajax({
            url: "chart.php",
            method: "GET",
            dataType: 'JSON',
            success: function(data) {
                console.log(data);
                var raw = [];
                var qty = [];

                for (var i in data) {
                    raw.push(data[i].name);
                    qty.push(data[i].quantity);
                }
                console.log(raw);
                console.log(qty);
                var chartdata = {
                    labels: raw,
                    datasets: [{
                        label: 'Product Stock',
                        backgroundColor: 'rgba(0,200,225,.5)',
                        borderColor: 'rgba(200, 200, 200, 0.75)',
                        hoverBackgroundColor: 'rgba(200, 200, 200, 1)',
                        hoverBorderColor: 'rgba(200, 200, 200, 1)',
                        data: qty
                    }]
                };

                var ctx = $('#myChart2');

                var barGraph = new Chart(ctx, {
                    type: 'bar',
                    data: chartdata
                });
            },
            error: function(data) {
                console.log(data);
            }
        });
        <?php endif; ?>
    });
	function openRemarkModal(customer_id, remarks) {
		$('#customer_id_remark').val(customer_id);
		$('#remark').val(remarks);
		$('#add_remark_modal').modal('show');
	}
</script>
<div id="add_remark_modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title">Add/Edit Remark</h4>
			</div>
			<div class="modal-body">
				<form method="post" id="remark_form" action="remarks.php">
					<label>Remark</label>
					<textarea name="remark" id="remark" class="form-control"></textarea>
					<input type="hidden" name="customer_id" id="customer_id_remark">
					<input type="hidden" name="return_page" value="index.php">
					<br>
					<input type="submit" name="add_remark" id="add_remark_btn" class="btn btn-primary" value="Add Remark">
				</form>
			</div>
		</div>
	</div>
</div>