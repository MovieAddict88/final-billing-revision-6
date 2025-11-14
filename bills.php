<?php
	// Start from getting the hader which contains some settings we need
	require_once 'includes/header.php';
	require_once 'config/dbconnection.php';
	$dbh = new Dbconnect();
	require_once "includes/classes/admin-class.php";
	$admins	= new Admins($dbh);
	// Redirect visitor to the login page if he is trying to access
	// this page without being logged in
	if (!isset($_SESSION['admin_session']) )
	{
		$commons->redirectTo(SITE_PATH.'login.php');
	}
?>
			<!-- <button disabled="disabled" class="btn btn-success">Generate Now</button> -->
	<div class="dashboard">
		<div class="col-md-12 col-sm-12">
		<div class="col-md-6">
			<!-- <h4>Customer Billing: Month of October</h4> -->
			<a href='paid_bills.php' class="btn btn-primary">Paid Bills</a> 
		</div>
		<div class="col-md-6">
			<form class="form-inline pull-right">
			  <div class="form-group">
			    <label class="sr-only" for="search">Search for</label>
			    <div class="input-group">
			      <div class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></div>
			      <input type="text" class="form-control" id="search" placeholder="Type a name">
			      <div class="input-group-addon"></div>
			    </div>
			  </div>
			</form>
			<a href='bill_generation.php' class="btn btn-success pull-right">Generate Now</a> 
			
		</div>
		<br><br>
		
		</div>
		<div class="col-md-12 col-sm-12" id="bill_table">
			<?php
			  $submissions = $admins->fetchPendingSubmissions();
			  if (isset($submissions) && sizeof($submissions) > 0){ ?>
				<div class="table-responsive">
				<table class="table table-striped table-bordered">
				<thead class="thead-inverse">
				  <tr class="info">
				    <th style="background-color: #284390; color: white;">ID </th>
				    <th style="background-color: #284390; color: white;">Name</th>
				    <th style="background-color: #284390; color: white;">Submitted on</th>
				    <th style="background-color: #284390; color: white;">Amount</th>
				    <th style="background-color: #284390; color: white;">Method</th>
				    <th style="background-color: #284390; color: white;">Reference #</th>
				    <th style="background-color: #284390; color: white;">Status</th>
				    <th style="background-color: #284390; color: white;">Action</th>
				  </tr>
				</thead>
			  <tbody>
				<?php
				foreach ($submissions as $submission) {
			  	 	?>
			  <tr>
				<td scope="row"><?=$submission->id?></td>
				<td><?=$submission->customer_name?></td>
				<td><?=$submission->created_at?></td>
				<td>₱<?=number_format($submission->total_amount, 2)?></td>
				<td><?=$submission->payment_method?></td>
				<td><?=$submission->reference_number?></td>
				<td>
					<span class="label label-info">Pending</span>
				</td>
				<td>
					<button type="button" onClick="view_payment(<?=$submission->id?>)" class="btn btn-warning">View</button>
				</td>
			  </tr>
			  <?php
			  	}
				}else{
					?>
						<h1>Congratulations ! No pending payments to review !</h1>
					<?php
				}
			?>
			  </tbody>
				</table>
				</div>
			<div>
			</div>
			
		</div>
	</div>

	<?php include 'includes/footer.php'; ?>
	<script type="text/javascript">
		function pay(id) {
		let left = (screen.width/2)-(600/2);
  	let top = (screen.height/2)-(800/2);
		let params = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=600,height=800,left=${left},top=${top}`;
		open('pay.php?customer='+id, 'Hello World !', params)
		}
		function bill(id) {
		let left = (screen.width/2)-(600/2);
  	let top = (screen.height/2)-(800/2);
		let params = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=600,height=800,left=${left},top=${top}`;
		open('pay.php?customer='+id+'&action=bill', 'Invoice', params)
		}
		function view_payment(id) {
		let left = (screen.width/2)-(600/2);
		let top = (screen.height/2)-(800/2);
		let params = `scrollbars=no,resizable=no,status=no,location=no,toolbar=no,menubar=no,width=600,height=800,left=${left},top=${top}`;
		open('view_payment.php?id='+id, 'Payment Details', params)
		}
	</script>
