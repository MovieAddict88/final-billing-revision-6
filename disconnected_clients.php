<?php
require_once 'includes/header.php';

if (!isset($_SESSION['admin_session'])) {
    $commons->redirectTo(SITE_PATH . 'login.php');
}

require_once 'config/dbconnection.php';
$dbh = new Dbconnect();
require_once "includes/classes/admin-class.php";
$admins = new Admins($dbh);

$user_role = $_SESSION['user_role'];
$is_admin = ($user_role == 'admin');
?>

<div class="dashboard" style="margin-top: 50px;">
    <div class="col-md-12 col-sm-12" id="employee_table">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4>Disconnected Clients</h4>
                <div class="pull-right">
                    <a href="customers.php" style="margin-top: 20px;" class="btn btn-primary">Back to Active Customers</a>
                </div>
            </div>
            <div class="panel-body">
                <?php if (isset($_SESSION['success'])) { ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['success'] ?>
                    </div>
                <?php 
                    unset($_SESSION['success']);
                } ?>
                <?php if (isset($_SESSION['errors'])) { ?>
                    <div class="alert alert-danger">
                        <?php foreach ($_SESSION['errors'] as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach ?>
                    </div>
                <?php 
                    unset($_SESSION['errors']);
                } ?>
                
                <?php if ($is_admin) : ?>
                    <div class="col-md-6">
                        <form class="form-inline pull-right">
                            <div class="form-group">
                                <label class="sr-only" for="search">Search for</label>
                                <div class="input-group">
                                    <div class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></div>
                                    <input type="text" class="form-control" id="search" placeholder="Type a name" list="global-suggestions" autocomplete="off">
                                    <div class="input-group-addon"></div>
                                </div>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
            </div>
            <div class="table-responsive" style="overflow-x: auto; margin-top: 50px;  -webkit-overflow-scrolling: touch;">
                <table class="table table-striped table-bordered" id="grid-basic" style="min-width: 1200px; width: 100%;">
                    <thead class="thead-inverse">
                        <tr class="info">
                            <th style="min-width: 50px; white-space: nowrap; background-color: #284390; color: white;">Original ID</th>
                            <th style="min-width: 150px; background-color: #284390; color: white;">Name</th>
                            <th style="min-width: 150px; background-color: #284390; color: white;">Employer</th>
                            <th style="min-width: 120px; background-color: #284390; color: white;">NID</th>
                            <th style="min-width: 200px; background-color: #284390; color: white;">ADDRESS</th>
                            <th style="min-width: 120px; background-color: #284390; color: white;">Package</th>
                            <th style="min-width: 120px; background-color: #284390; color: white;">IP</th>
                            <th style="min-width: 200px; background-color: #284390; color: white;">Email</th>
                            <th style="min-width: 120px; background-color: #284390; color: white;">Contact</th>
                            <th style="min-width: 100px; background-color: #284390; color: white;">Type</th>
                            <th style="min-width: 100px; background-color: #284390; color: white;">Status</th>
                            <th style="min-width: 120px; background-color: #284390; color: white;">Login Code</th>
                            <th style="min-width: 120px; background-color: #284390; color: white;">Due Date</th>
                            <th style="min-width: 200px; background-color: #284390; color: white;">Remarks</th>
                            <th style="min-width: 150px; white-space: nowrap; background-color: #284390; color: white;">Disconnected At</th>
                            <th style="min-width: 120px; background-color: #284390; color: white;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Data will be loaded here via AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script type="text/javascript">
    function viewData(page, q, limit) {
        page = typeof page !== 'undefined' ? page : 1;
        q = typeof q !== 'undefined' ? q : '';
        limit = typeof limit !== 'undefined' ? limit : 10;
        $.ajax({
            method: "GET",
            url: "disconnected_clients_approve.php",
            data: {
                page: page,
                q: q,
                limit: limit
            },
            success: function(data) {
                $('tbody').html(data);
            }
        });
    }

    window.onload = function() {
        viewData(1, '', 10);
    };
</script>

<script type="text/javascript">
    $(function() {
        var grid = $('#grid-basic');
        var typingTimer;
        var lastQ = '';

        $('#search').on('input', function() {
            var q = $(this).val();
            clearTimeout(typingTimer);
            typingTimer = setTimeout(function() {
                lastQ = q;
                viewData(1, q);
            }, 250);
        });

        $('#grid-basic').on('click', '.page-prev, .page-next', function() {
            var row = $('.pagination-row');
            var page = parseInt(row.data('page')) || 1;
            var total = parseInt(row.data('total')) || 0;
            var limit = parseInt(row.data('limit')) || 10;
            var q = row.data('query') || '';
            if ($(this).hasClass('page-prev') && page > 1) page--;
            else if ($(this).hasClass('page-next')) page++;
            viewData(page, q, limit);
        });

        $('#grid-basic').on('click', '.page-num', function() {
            var page = parseInt($(this).text());
            var row = $('.pagination-row');
            var limit = parseInt(row.data('limit')) || 10;
            var q = row.data('query') || '';
            viewData(page, q, limit);
        });
    });
</script>