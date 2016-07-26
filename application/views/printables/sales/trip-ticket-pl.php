<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css')?>" />
	<style type="text/css">
		small{
			display:block;
			color: #000!important;
		}
		.page-header{
			margin-top: 0;
		}
		.form-group{
			margin: 3px 0px;
		}
		.form-group label{
			margin-bottom:0px;
			text-transform: uppercase;
		}
		.form-group .form-control-static{
		    padding-top: 0px; 
    		padding-bottom: 0px; 
    		display: table-cell;
    		vertical-align: middle;
		    word-wrap: break-word;
    		word-break: break-all;
		}
		.form-group p.form-control-static.empty {
		  border:1px solid black;
		  width:250px;
		  padding: 0px 3px;
		  height: 30px;
		}

		.form-group p.form-control-static.empty.signatory {
		  height: 80px;
		  border:0;
		  width: 100%;
		  vertical-align: bottom;
		  text-decoration: overline;
		}


		.form.details{
			border-radius:10px;
			border:1px solid black;
			padding: 0px 10px;
			overflow: hidden;
		}

		.form.details .row:nth-child(2){
			border-top: 1px solid black;
		}

		table{
			border-bottom:0!important;
			border-left:0!important;
		}

		tfoot td:nth-child(1){
			border:0!important;
		}

		tfoot td:nth-child(2){
			font-weight: bold;
			text-align: right;
		}
		tfoot td:nth-child(3){
			text-align: right;
		}

		tbody td:nth-child(1),thead th:nth-child(1){
			text-align: center;
		}

		tbody td:nth-child(2),tbody td:nth-child(3),tbody td:nth-child(4),
		thead th:nth-child(2),thead th:nth-child(3),thead th:nth-child(4){
			text-align: right;
		}
	</style>
</head>
<body style="height:5in;margin:0;padding:0">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12">
				<h4 class="text-center" style="margin-top:0px">
					Arditezza Poultry Integration Corporation
					<small style=";margin-top:2px;margin-bottom:2px;">Ultima Residences Tower 3, Unit 1018, Osmena Blvrd., Cebu City</small>
					<small>Tel/Fax Nos.: (032) 253­4570 to 71 / 414­3312 / 512­3067</small>
				</h4>
				<h5 class="text-center" style="margin:2px 0 10px 0;font-weight: bold;font-size: 120%;">TRUCKING PACKING LIST No. <?= put_value($data, 'id', 0)?></h5>
				<form class="form details">
					<div class="row">
						<div class="col-xs-5">
							<div class="form-group">
								<label>Customer:</label>
								<p class="form-control-static"><?= put_value($data, 'customer', 'Customer Name')?></p>
							</div>
						</div>
						<div class="col-xs-7"  style="border-left:1px dotted black;">
							<div class="row">
								<div class="col-xs-8">
									<div class="form-group">
										<label><?= put_value($data, 'trip_point', 'Arrival/Departure')?>:</label>
										<p class="form-control-static"><?= put_value($data, 'trip_point_location', 'Arrival/Departure Location')?></p>
									</div>
								</div>
								<div class="col-xs-4">
									<div class="form-group">
										<label>Date:</label>
										<p class="form-control-static"><?= date_create(put_value($data, 'date', date('Y-m-d')))->format('M d, Y')?></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-4">
									<div class="form-group">
										<label>Trip TICKET #:</label>
										<p class="form-control-static"><?= put_value($data, 'trip_ticket_id', 0)?></p>
									</div>
								</div>
								<div class="col-xs-4">
									<div class="form-group">
										<label>Trip Type:</label>
										<p class="form-control-static"><?= put_value($data, 'trip_type', 'DC/H/CV') ?></p>
									</div>
								</div>
								<div class="col-xs-4">
									<div class="form-group">
										<label>Trip Date:</label>
										<p class="form-control-static"><?= date_create(put_value($data, 'trip_ticket_date', date('Y-m-d')))->format('M d, Y')?></p>
									</div>
								</div>
							</div>
							
						</div>
					</div>
				</form>
				<table class="table table-bordered table-condensed" style="margin-top:10px;table-layout:fixed">
					<thead>
						<tr class="active">
							<th><?= put_value($data, 'service_point', 'Arrival/Departure')?></th>
							<th>RATE</th>
							<th>HEADS</th>
							<th>AMOUNT</th>
						</tr>
					</thead>
					<tbody>

						<?php 
							$details = put_value($data, 'less', []);
							$totalAmount = 0;
							$lessAdjustments = put_value($data, 'adjustments', 0);
							$otherCharges = put_value($data, 'other_charges', 0);
						?>
						<?php foreach($details['id'] AS $key => $id):?>
							<tr>
								<?php $amount = $details['rate'][$key] * $details['pcs'][$key]; ?>
								<td><?= $details['location_desc'][$key] ?></td>
								<td><?= number_format($details['rate'][$key], 2)?></td>
								<td><?= number_format($details['pcs'][$key], 2)?></td>
								<td><?= number_format($amount, 2)?></td>
								<?php $totalAmount += $amount; ?>
							</tr>
						<?php endforeach;?>
					</tbody>
					<tfoot>
						<tr>
							<td colspan="2"></td>
							<td>TOTAL AMOUNT</td>
							<td><?= number_format($totalAmount, 2)?></td>
						</tr>
						<tr>
							<td colspan="2"></td>
							<td>LESS ADJUSTMENTS</td>
							<td><?= number_format($lessAdjustments, 2)?></td>
						</tr>
						<tr>
							<td colspan="2"></td>
							<td>OTHER CHARGES</td>
							<td><?= number_format($otherCharges, 2)?></td>
						</tr>
						<tr>
							<td colspan="2"></td>
							<td>NET AMOUNT DUE</td>
							<td><?= number_format($totalAmount - $lessAdjustments + $otherCharges, 2)?></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</div>
</body>
</html>