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
        /* Header Background Color: #284390 */
        /* Header Text Color: #f8cb18 */

        .cd-main-header {
            background: #284390;
            border-bottom: 3px solid #f8cb18;
        }

        .cd-main-header .cd-logo {
            color: #f8cb18;
            font-weight: 900;
            font-size: 18px;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-logo {
            width: 40px;
            height: 40px;
            object-fit: cover;
            display: block;
            border-radius: 50%;
            border: 2px solid #f8cb18;
        }

        /* Ensure all text within the logo link has the correct color and weight */
        .cd-main-header .cd-logo,
        .cd-main-header .cd-logo * {
            color: #f8cb18;
            font-weight: bold;
        }
    </style>
</head>
<body>
<header class="cd-main-header">
    <a href="customer_dashboard.php" class="cd-logo">
        <img src="component/orig_cs.png" alt="avatar" class="header-logo">
        CS-BILLING | Customer Portal
    </a>
</header>