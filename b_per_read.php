<?php 

session_start();
if(!isset($_SESSION["b_person_id"])){ // if "user" not set,
	session_destroy();
	header('Location: login.php');     // go to login page
	exit;
}

require 'database.php';
require 'functions.php';

$id = $_GET['id'];

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT * FROM b_person where id = ?";
$q = $pdo->prepare($sql);
$q->execute(array($id));
$data = $q->fetch(PDO::FETCH_ASSOC);

Database::disconnect();

//--------------new

// $sql = "SELECT images.* FROM images, b_person where b_person.id= images.new_id AND b_person.id=?";
// $q = $pdo->prepare($sql);
// $q->execute(array($data['id']));
//$perdata = $q->fetch(PDO::FETCH_ASSOC);
//----------------end

//Database::disconnect();

?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link   href="css/bootstrap.min.css" rel="stylesheet">
		<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
		<script src="js/bootstrap.min.js"></script>
	</head>

	<body>
		<div class="container">
			<div class="span10 offset1">
				<div class="row">
					<h3><b>User Details</b></h3>
				</div>
				
				<div class="form-horizontal" >

					<div class="control-group"> <!-- col-md-6 -->
						<label class="control-label"><b>First Name</b></label>
						<div class="controls ">
							<label class="checkbox">
								<?php echo htmlspecialchars($data['fname']);?> 
							</label>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label"><b>Last Name</b></label>
						<div class="controls ">
							<label class="checkbox">
								<?php echo htmlspecialchars($data['lname']);?> 
							</label>
						</div>
					</div>
					
					<div class="control-group">
						<label class="control-label"><b>Email</b></label>
						<div class="controls">
							<label class="checkbox">
								<?php echo htmlspecialchars($data['email']);?>
							</label>
						</div>
					</div>
						
					<div class="control-group">
						<label class="control-label"><b>Mobile</b></label>
						<div class="controls">
							<label class="checkbox">
								<?php echo htmlspecialchars($data['mobile']);?>
							</label>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label"><b>Mailing Address</b></label>
						<div class="controls">
							<label class="checkbox">
								<?php echo htmlspecialchars($data['address']);?>
							</label>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label"><b>City</b></label>
						<div class="controls">
							<label class="checkbox">
								<?php echo htmlspecialchars($data['city']);?>
							</label>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label"><b>State</b></label>
						<div class="controls">
							<label class="checkbox">
								<?php echo htmlspecialchars($data['state']);?>
							</label>
						</div>
					</div>

					<div class="control-group">
						<label class="control-label"><b>Zip Code</b></label>
						<div class="controls">
							<label class="checkbox">
								<?php echo htmlspecialchars($data['zip']);?>
							</label>
						</div>
					</div>
						
					<div class="control-group">
						<label class="control-label"><b>Title</b></label>
						<div class="controls">
							<label class="checkbox">
								<?php echo htmlspecialchars($data['title']);?>
							</label>
						</div>
					</div>

					<!-- Display photo, if any --> 
					<div class="control-group">
						<div class="controls ">
							<?php 
								if ($data['filesize'] > 0) 
									echo '<img  height=5%; width=15%; src="data:image/jpeg;base64,' . 
										base64_encode( $data['filecontent'] ) . '" />'; 
								else 
									echo 'No photo on file.';
							?> <!-- converts to base 64 due to the need to read the binary files code and display img -->
						</div>
					</div>
				
					<div class="control-group">
						<div class="controls ">
							<?php 
								while($perdata = $q->fetch(PDO::FETCH_ASSOC)){if ($perdata['filesize'] > 0) 
									echo '<img  height=5%; width=15%; src="data:image/jpeg;base64,' . 
										base64_encode( $perdata['filecontent'] ) . '"  /> &nbsp;'; 
								else 
									echo 'No photo on file.';
							}?> <!-- converts to base 64 due to the need to read the binary files code and display img -->
						</div>
					</div>
						
					<!-- password omitted on Read/View -->
					
					<div class="form-actions">
						<a class="w3-button w3-khaki w3-round-xlarge" href="b_persons.php">Back</a>
					</div>
					
					<div class="row">
						<h4><b>Assigned Tasks</b></h4>
					</div>
					
					<?php
						$pdo = Database::connect();
						$sql = "SELECT * FROM b_assign, b_task WHERE assign_task_id = b_task.id AND assign_person_id = " . $id . " ORDER BY task_date ASC, task_time ASC";
						$countrows = 0;
						foreach ($pdo->query($sql) as $row) {
							echo Functions::dayMonthDate($row['task_date']) . ': ' . Functions::timeAmPm($row['task_time']) .  ' - ' . $row['task_description'] . '<br />';
							$countrows++;
						}
						if ($countrows == 0) echo 'No current assignments.';
					?>
					
				</div>  <!-- end div: class="form-horizontal" -->

			</div> <!-- end div: class="span10 offset1" -->

		</div> <!-- end div: class="container" -->
		
	</body> 
</html>