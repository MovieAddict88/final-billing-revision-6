<?php
	// Get the application settings and parameters
	require_once "includes/headx.php";
	if (!isset($_SESSION['admin_session']) )
	{
		$commons->redirectTo(SITE_PATH.'login.php');
	}
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset=" utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="component/css/bootstrap.css"> <!-- CSS bootstrap -->
    <link rel="stylesheet" href="component/css/jquery.bootgrid.css"> <!-- Bootgrid stylesheet -->
    <link rel="stylesheet" href="component/css/style.css"> <!-- Resource style -->
    <link rel="stylesheet" href="component/css/reset.css"> <!-- Resource style -->
    <link rel="stylesheet" href="assets/css/custom.css"> <!-- Custom CSS -->
    <script src="component/js/modernizr.js"></script> <!-- Modernizr -->
    
    <!-- Dynamic Header Styles -->
    <style>
        /* Header Background Color: #284390 */
        /* Header Text Color: #f8cb18 */
        
        .cd-main-header {
            background: #284390 !important;
            background-color: #284390 !important;
            border-bottom: 3px solid #f8cb18 !important;
        }
        
        .cd-main-header .cd-logo {
            color: #f8cb18 !important;
            font-weight: 900 !important;
            font-size: 18px !important;
            text-decoration: none !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
        }
        
        .header-logo {
            width: 40px !important;
            height: 40px !important;
            object-fit: contain !important;
            display: block !important;
        }
        
        .cd-top-nav a,
        .cd-nav a {
            color: #f8cb18 !important;
            font-weight: 700 !important;
            text-decoration: none !important;
        }
        
        .cd-top-nav a:hover,
        .cd-nav a:hover {
            color: #ffffff !important;
            text-decoration: none !important;
        }
        
        /* Hamburger menu lines */
        .cd-nav-trigger span,
        .cd-nav-trigger span::before,
        .cd-nav-trigger span::after {
            background: #f8cb18 !important;
            background-color: #f8cb18 !important;
        }
        
        /* User info in header */
        .cd-top-nav .account a {
            color: #f8cb18 !important;
            font-weight: 700 !important;
        }
        
        /* Ensure no other colors override our styles */
        .cd-main-header * {
            color: #f8cb18 !important;
            font-weight: bold !important;
        }
    </style>
    
    <title>CS-BILLING | <?php echo ($_SESSION['user_role'] == 'employer') ? 'Account Manager' : ucfirst($_SESSION['user_role'] ?? 'Admin'); ?></title>
</head>
<body>
    <header class="cd-main-header" style="background: #284390;">
        <a href="#0" class="cd-logo" style="color: #f8cb18; font-weight: 900; font-size: 18px; text-decoration: none; display: flex; align-items: center; gap: 10px;">
            <img src="component/orig_cs.png" alt="avatar" style="border: 2px solid #f8cb18; object-fit: cover; border-radius: 50%; width: 40px; height: 40px;" class="header-logo">
            CS-BILLING | <?php echo ($_SESSION['user_role'] == 'employer') ? 'Account Manager' : ucfirst($_SESSION['user_role'] ?? 'Admin'); ?>
        </a>
        
        <!-- Global search suggestions container -->
        <datalist id="global-suggestions"></datalist>

        <a href="#0" class="cd-nav-trigger"><span></span></a>

        <nav class="cd-nav">
            <ul class="cd-top-nav">

            </ul>
        </nav>
    </header> <!-- .cd-main-header -->

	<main class="cd-main-content">
		<?php if ($_SESSION['user_role'] == 'admin'): ?>
		<nav class="cd-side-nav">
			<ul>
				<li class="overview">
					<a href="index.php">Dashboard</a>
				</li>
				<li class="has-children overview active">
					<a href="#0">Collection / Balance<!-- <span class="count">3</span> --></a>
					
					<ul>
						<li><a href="daily_data.php">Collect / Balance</a></li>						
						<li><a href="bills.php">Monthly Billing</a></li>
						<li><a href="balance.php">Balance Summary</a></li>
					</ul>
				</li>

				<li class="has-children overview active">
					<a href="#0">Products</a>
					<ul>
						<li><a href="production.php">Stock Entry</a></li>
						<li><a href="production_stat.php">Products Stock</a></li>						
					</ul>
				</li>
			</ul>

			<ul>
				<li class="cd-label">Administration</li>
				<li class="bookmarks">
					<a href="products.php">Products</a>
				</li>
				<li class="users">
					<a href="customers.php">Customers</a>
				</li>
				<li class="users">
					<a href="disconnected_clients.php">Disconnected Clients</a>
				</li>

				<li class="users">
					<a href="user.php">Users</a>
				</li>
				<li class="users">
					<a href="retrieve_password.php">Retrieve Password</a>
				</li>
				<li class="monitoring">
					<a href="employee_monitoring.php">Account Manager</a>
				</li>
				<li><a href="logout.php">Logout</a></li>
			</ul>
			<!-- <ul>
				<li class="cd-label">Action</li>
				<li class="action-btn"><a href="#0">INSERT DATA</a></li>
			</ul> -->
		</nav>
		<?php else: ?>
		<nav class="cd-side-nav">
			<ul>
				<li class="overview">
					<a href="index.php">Dashboard</a>
				</li>
				<li class="users">
					<a href="disconnected_clients.php">Disconnected Clients</a>
				</li>
				<li><a href="logout.php">Logout</a></li>
			</ul>
		</nav>
		<?php endif; ?>

		<div class="content-wrapper">
		<div class="container-fluid">