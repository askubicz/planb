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

if ( !empty($_POST)) { // if user clicks "yes" (sure to delete), delete record

	$id = $_POST['id'];
	
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	
	$sql = "DELETE FROM images WHERE new_id =? ";
	$q = $pdo->prepare($sql);
	$q->execute(array($id));
	
	$sql = "DELETE FROM b_person  WHERE id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($id));
	Database::disconnect();
	header("Location: b_persons.php");
	
} 
else { // otherwise, pre-populate fields to show data to be deleted
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM b_person where id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($id));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	Database::disconnect();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link   href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
	
</head>

<body>
    <div class="container">
		<div class="row">
			<h3>Delete Person</h3>
		</div>
		
		<form class="form-horizontal" action="b_per_delete.php" method="post">
			<input type="hidden" name="id" value="<?php echo $id;?>"/>
			<p class="alert alert-error">Are you sure you want to delete ?</p>
			<div class="form-actions">
				<button type="submit" class="btn btn-danger">Yes</button>
				<a class="btn" href="b_persons.php">No</a>
			</div>
		</form>
		
		<!-- Display same information as in file: fr_per_read.php -->
		
		<div class="form-horizontal" >
				
			<div class="control-group col-md-6">
			
				<div class="control-group"> <!-- col-md-6 -->
					<label class="control-label"><b>First Name</b></label>
					<div class="controls ">
						<label class="checkbox">
							<?php echo $data['fname'];?> 
						</label>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label"><b>Last Name</b></label>
					<div class="controls ">
						<label class="checkbox">
							<?php echo $data['lname'];?> 
						</label>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label"><b>Email</b></label>
					<div class="controls">
						<label class="checkbox">
							<?php echo $data['email'];?>
						</label>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label"><b>Mobile</b></label>
					<div class="controls">
						<label class="checkbox">
							<?php echo $data['mobile'];?>
						</label>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"><b>Mailing Address</b></label>
					<div class="controls">
						<label class="checkbox">
							<?php echo $data['address'];?>
						</label>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"><b>City</b></label>
					<div class="controls">
						<label class="checkbox">
							<?php echo $data['city'];?>
						</label>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"><b>State</b></label>
					<div class="controls">
						<label class="checkbox">
							<?php echo $data['state'];?>
						</label>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label"><b>Zip Code</b></label>
					<div class="controls">
						<label class="checkbox">
							<?php echo $data['zip'];?>
						</label>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label"><b>Title</b></label>
					<div class="controls">
						<label class="checkbox">
							<?php echo $data['title'];?>
						</label>
					</div>
				</div>
				
				<!-- password omitted on Read/View -->

				<div class='control-group col-md-6'>
					<div class="controls ">
					<?php 
					if ($data['filesize'] > 0) 
						echo '<img  height=5%; width=15%; src="data:image/jpeg;base64,' . 
							base64_encode( $data['filecontent'] ) . '" />'; 
					else 
						echo 'No photo on file.';
					?><!-- converts to base 64 due to the need to read the binary files code and display img -->
					</div>
				</div>
				
			</div>

				<div class="row">
					<h4>Tasks for which this Person has been assigned</h4>
				</div>
				
				<?php
					$pdo = Database::connect();
					$sql = "SELECT * FROM b_assign, b_tasks WHERE assign_task_id = b_task.id AND assign_per_id = " . $id . " ORDER BY task_date ASC, task_time ASC";
					foreach ($pdo->query($sql) as $row) {
						echo Functions::dayMonthDate($row['task_date']) . ': ' . Functions::timeAmPm($row['task_time']) . $row['task_description'] . '<br />';
					}
				?>
				
		</div>  <!-- end div: class="form-horizontal" -->

    </div> <!-- end div: class="container" -->
	
</body>
</html>