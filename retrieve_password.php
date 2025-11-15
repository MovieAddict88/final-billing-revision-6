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
    <link rel="stylesheet" href="component/css/bootstrap.css">
    <link rel="stylesheet" href="component/css/style.css">
    <link rel="stylesheet" href="assets/css/custom.css">
    <title>CS-BILLING | Retrieve Password</title>
</head>
<body>

	<div class="container" style="margin-top: 50px;">
		<div class="row">
			<div class="col-md-12">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4>Retrieve Password</h4>
					</div>
					<div class="panel-body">
						<div class="table-responsive" style="overflow-x: auto; -webkit-overflow-scrolling: touch;">
							<table class="table table-striped table-bordered" id="grid-basic" style="min-width: 1200px; width: 100%;">
								<thead class="thead-inverse">
								  <tr class="info">
									<th style="min-width: 150px; background-color: #284390; color: white; white-space: nowrap;">Name</th>
									<th style="min-width: 200px; background-color: #284390; color: white; white-space: nowrap;">Email</th>
									<th style="min-width: 150px; background-color: #284390; color: white; white-space: nowrap;">Username</th>
									<th style="min-width: 120px; background-color: #284390; color: white; white-space: nowrap;">Contact</th>
									<th style="min-width: 100px; background-color: #284390; color: white; white-space: nowrap;">Actions</th>
								  </tr>
								</thead>
							  <tbody>
							  </tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php include 'includes/footer.php'; ?>

	<script type="text/javascript">
    function viewData(page = 1, q = '', limit = 10) {
        $.ajax({
            method: "GET",
            url:"user_approve.php?view=retrieve_password",
            data: { page: page, q: q, limit: limit },
            success: function(data){
                $('tbody').html(data);
            }
        });
    }
    window.onload = function(){ viewData(); };

    function retrievePassword(user_id){
	var admin_username = '';
	<?php if (!isset($_SESSION['admin_session'])) { ?>
		// admin_username = prompt("Please enter the username of an admin to authorize this action:");
		// if (admin_username == null || admin_username == "") {
		// 	return; // Cancelled
		// }
	<?php } ?>

	var retrieve_code = prompt("Please enter the 6-digit retrieve code:");
	if(retrieve_code != null){
		$.ajax({
			method: "POST",
			url: "user_approve.php?p=retrieve_password",
			data: {
				user_id: user_id,
				retrieve_code: retrieve_code,
				admin_username: admin_username
			},
			success: function(data){
				alert(data);
			}
		});
	}
    }
	</script>
</body>
</html>
