<?php
	// Get the application settings and parameters
	require_once "includes/headx.php";
?>
<!doctype html>
<html lang="en" class="no-js">
<head>
    <meta charset=" utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="component/css/bootstrap.css"> <!-- CSS bootstrap -->
    <link rel="stylesheet" href="component/css/style.css"> <!-- Resource style -->
    <link rel="stylesheet" href="assets/css/custom.css"> <!-- Shared responsive tweaks -->
    <script src="component/js/modernizr.js"></script> <!-- Modernizr -->
    <title>CS-BILLING | Customer Portal</title>
    <style>
        .cd-main-header {
            background-color: #284390 !important;
            border-bottom: 3px solid #f8cb18 !important;
        }
        .cd-logo {
            color: #f8cb18 !important;
            font-weight: bold !important;
            display: flex !important;
            align-items: center !important;
            gap: 10px !important;
            text-decoration: none !important;
        }
        .header-logo {
            width: 40px !important;
            height: 40px !important;
            object-fit: contain !important;
            display: block !important;
        }
    </style>
</head>
<body>
<header class="cd-main-header">
    <a href="customer_dashboard.php" class="cd-logo">
        <img src="component/orig_cs.png" alt="avatar" style="border: 2px solid #f8cb18; object-fit: cover;" class="header-logo" style="width: 40px; height: 40px; object-fit: contain;">
        CS-BILLING | Customer Portal
    </a>
</header>