<!DOCTYPE html>
<html>
<head>
	<title></title>
	<link rel="stylesheet" href="<?= base_url('assets/css/bootstrap.min.css')?>" />
	<style type="text/css">
		html,body{
			font-family: 'Tahoma';
			color: #000!important;
		}
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
    		text-overflow: ellipsis;
    		white-space: nowrap;

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
		.form.details > .row:nth-child(2),
		.form.details > 	.row:nth-child(3){
			border-top: 1px solid black;
		}
	</style>
</head>
<?php
	
	$trip_types = [
		'1' => [
			'description' => 'Chick Van',
			'departure_place' => 'Hatchery',
			'arrival_place' => 'Farm',
			'departure_signatory' => 'Hatchery rep',
			'arrival_signatory' => 'Farm rep',
			'unit' => 'heads'
		],
		'2' => [
			'description' => 'Harvester',
			'departure_place' => 'Farm',
			'arrival_place' => 'Dressing plant',
			'departure_signatory' => 'Farm rep',
			'arrival_signatory' => 'Dressing plant rep',
			'unit' => 'heads'
		],
		'3' => [
			'description' => 'Dressed Chicken',
			'departure_place' => 'Dressing plant',
			'arrival_place' => 'Customer',
			'departure_signatory' => 'Dressing plant rep',
			'arrival_signatory' => 'Customer',
			'unit' => 'pieces'
		],
	];
	$trip = $trip_types[put_value($data, 'trip_type', '1')];
	
?>
<body style="height:5in;margin:0;padding:0;">
	<div class="container-fluid">
		<div class="row">
			<div class="col-xs-12">
				<h4 class="text-center" style="margin-top:0px">
					Arditezza Poultry Integration Corporation
					<small style=";margin-top:2px;margin-bottom:2px;">Ultima Residences Tower 3, Unit 1018, Osmena Blvrd., Cebu City</small>
					<small>Tel/Fax Nos.: (032) 253­4570 to 71 / 414­3312 / 512­3067</small>
				</h4>
				<h5 class="text-center" style="margin:2px 0 10px 0;font-weight: bold;font-size: 120%;">TRIP TICKET No. <?= put_value($data, 'id', 0)?></h5>
				<form class="form details">
					<div class="row">
						<div class="col-xs-6">
							<div class="form-group">
								<label>Customer:</label>
								<p class="form-control-static"><?= put_value($data, 'customer', '')?></p>
							</div>
						</div>
						<div class="col-xs-3">
							<div class="form-group">
								<label>Trip Type:</label>
								<p class="form-control-static"><?= $trip['description'] ?></p>
							</div>
						</div>
						<div class="col-xs-3">
							<div class="form-group">
								<label>Trip Date:</label>
								<p class="form-control-static"><?= date_create(put_value($data, 'date', date('Y-m-d')))->format('M d, Y')?></p>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-xs-4">
							<div class="form-group">
								<label>Truck:</label>
								<p class="form-control-static"><?= put_value($data, 'truck_name', '')?></p>
							</div>
						</div>
						<div class="col-xs-4">
							<div class="form-group">
								<label>Driver:</label>
								<p class="form-control-static"><?= put_value($data, 'truck_driver', '')?></p>
							</div>
						</div>
						<div class="col-xs-4">
							<div class="form-group">
								<label>Helper:</label>
								<p class="form-control-static"><?= put_value($data, 'truck_assistant', '')?></p>
							</div>
						</div>
					</div>
					<div class="row">

						<div class="col-xs-6" >
							<div class="row">
								<div class="col-xs-12">
									<div class="form-group">
										<label>Beginning kilometer reading:</label>
										<p class="form-control-static empty"></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div class="form-group">
										<label>Departure time:</label>
										<p class="form-control-static empty"><?= $trip['departure_place'] ?> @ </p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div class="form-group">
										<label>No. OF <?= $trip['unit'] ?>:</label>
										<p class="form-control-static empty"></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div class="form-group text-center">
										<p class="form-control-static empty signatory"><?= $trip['departure_signatory'] ?> signature over printed name</p>
									</div>
								</div>
							</div>
							
						</div>
						<div class="col-xs-6" style="border-left:1px dotted black">
							<div class="row">
								<div class="col-xs-12">
									<div class="form-group">
										<label>Ending kilometer reading:</label>
										<p class="form-control-static empty"></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div class="form-group">
										<label>Arrival time:</label>
										<p class="form-control-static empty"><?= $trip['arrival_place'] ?> @ </p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12">
									<div class="form-group">
										<label>Destination:</label>
										<p class="form-control-static empty"><?= put_value($data, 'destination', 'DESTINATION')?></p>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-xs-12" >
									<div class="form-group text-center">
										<p class="form-control-static empty signatory"><?= $trip['arrival_signatory'] ?> signature over printed name</p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</body>
</html>