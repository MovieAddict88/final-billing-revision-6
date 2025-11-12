<?php
require_once "includes/headx.php";
if (!isset($_SESSION['admin_session'])) {
    $commons->redirectTo(SITE_PATH . 'login.php');
}
require_once "includes/classes/admin-class.php";
$admins = new Admins($dbh);
$id = isset($_GET['customer']) ? $_GET['customer'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : 'pay';
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

    .account-info {
        margin-bottom: 20px;
    }

    .account-info p {
        margin: 5px 0;
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

    .footer-notes {
        margin-top: 40px;
        font-style: italic;
    }

    .highlight {
        background-color: yellow;
        padding: 10px;
        text-align: center;
        font-size: 18px;
    }

    .blue-bg {
        background-color: #007bff !important;
        color: white !important;
    }

    @media print {
        body {
            margin: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .no-print {
            display: none;
        }

        .statement-container {
            width: 100%;
            margin: auto;
            padding: 20px;
            text-align: center;
        }

        .header-container {
            position: relative !important;
            height: 120px !important;
            margin: 0 auto 20px auto;
            display: flex;
            align-items: center;
        }

        .header-container .logo {
            position: relative !important;
            top: auto !important;
            left: auto !important;
            margin-right: 15px;
        }

        .header-container .logo img {
            max-width: 100px !important;
        }

        .header-container .company-info {
            position: relative !important;
            top: auto !important;
            left: auto !important;
            font-size: 12px !important;
            line-height: 1.4 !important;
            white-space: nowrap;
        }

        .customer-details-grid {
            text-align: left;
        }

        .blue-bg {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            background-color: #007bff !important;
            color: white !important;
        }

        .highlight {
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
            background-color: yellow !important;
            color: black !important;
        }
    }
</style>
<!doctype html>
<html lang="en" class="no-js">

<head>
    <meta charset=" utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:300,400,700' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="component/css/bootstrap.css">
    <link rel="stylesheet" href="component/css/bootstrap-select.min.css">
    <link rel="stylesheet" href="component/css/style.css">
    <link rel="stylesheet" href="component/css/reset.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <script src="component/js/modernizr.js"></script>
    <title>Statement of Account | Cornerstone</title>
</head>

<body>
    <?php
    $info = $admins->getCustomerInfo($id);
    include "_statement.php";
    ?>
    <script src="component/js/jquery-2.1.4.js"></script>
    <script src="component/js/bootstrap-select.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#payment-form').on('submit', function(e) {
                e.preventDefault();

                $.ajax({
                    type: 'POST',
                    url: 'post_approve.php',
                    data: $(this).serialize(),
                    success: function(response) {
                        $('.statement-container').html(response);
                        $('#payment-form-container').hide();
                    }
                });
            });
        });
    </script>
</body>

</html>