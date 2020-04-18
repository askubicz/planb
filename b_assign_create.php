<?php 

session_start();
if(!isset($_SESSION["b_person_id"])){ // if "user" not set,
	session_destroy();
	header('Location: login.php');     // go to login page
	exit;
}
$personid = $_SESSION["b_person_id"];
$taskid = $_GET['task_id'];

require 'database.php';
require 'functions.php';

if ( !empty($_POST)) {

	// initialize user input validation variables
	$personError = null;
	$taskError = null;
	
	// initialize $_POST variables
	$person = $_POST['person'];    // same as HTML name= attribute in put box
	$task = $_POST['task'];

	// initialize $_FILES variables
	$filename = $_FILES['userfile']['name'];
	$tmpName  = $_FILES['userfile']['tmp_name'];
	$filesize = $_FILES['userfile']['size'];
	$filetype = $_FILES['userfile']['type'];
	$filecontent = file_get_contents($tmpName);
	
	// validate user input
	$valid = true;
	if (empty($person)) {
		$personError = 'Please choose a person';
		$valid = false;
	}
	if (empty($task)) {
		$taskError = 'Please choose a task';
		$valid = false;
	} 

	// restrict file types for upload
	$types = array('image/jpeg','image/gif','image/png');
	if($filesize > 0) {
		if(in_array($_FILES['userfile']['type'], $types)) {
		}
		else {
			$filename = null;
			$filetype = null;
			$filesize = null;
			$filecontent = null;
			$pictureError = 'improper file type';
			$valid=false;
			
		}
	}
		
	// insert data
	if ($valid) {
		$pdo = Database::connect();
		$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$sql = "INSERT INTO b_assign 
			(assign_person_id,assign_task_id,filename,filesize,filetype,filecontent) 
			values(?, ?, ?, ?, ?, ?)";
		$q = $pdo->prepare($sql);
		$q->execute(array($person,$task,$filename,$filesize,$filetype,$filecontent));
		Database::disconnect();
		header("Location: b_assignments.php");
	}
}
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
				<h3><b>Assign a Person to a Task</b></h3>
			</div>
	
			<form class="form-horizontal" action="b_assign_create.php" method="post" enctype="multipart/form-data">
		
				<div class="control-group">
					<label class="control-label"><b>Person</b></label>
					<div class="controls">
						<?php
							$pdo = Database::connect();
							$sql = 'SELECT * FROM b_person ORDER BY lname ASC, fname ASC';
							echo "<select class='form-control' name='person' id='person_id'>";
							if($taskid) // if $_GET exists restrict person options to logged in user
								foreach ($pdo->query($sql) as $row) {
									if($personid==$row['id'])
										echo "<option value='" . $row['id'] . " '> " . $row['lname'] . ', ' .$row['fname'] . "</option>";
								}
							else
								foreach ($pdo->query($sql) as $row) {
									echo "<option value='" . $row['id'] . " '> " . $row['lname'] . ', ' .$row['fname'] . "</option>";
								}
							echo "</select>";
							Database::disconnect();
						?>
					</div>	<!-- end div: class="controls" -->
				</div> <!-- end div class="control-group" -->
			  
				<div class="control-group">
					<label class="control-label"><b>Task</b></label>
					<div class="controls">
						<?php
							$pdo = Database::connect();
							$sql = 'SELECT * FROM b_task ORDER BY task_date ASC, task_time ASC';
							echo "<select class='form-control' name='task' id='task_id'>";
							if($tasktid) // if $_GET exists restrict event options to selected event (from $_GET)
								foreach ($pdo->query($sql) as $row) {
									if($taskid==$row['id'])
									echo "<option value='" . $row['id'] . " '> " . Functions::dayMonthDate($row['task_date']) . " (" . Functions::timeAmPm($row['task_time']) . ") - " .
									trim($row['task_description']) . 
									"</option>";
								}
							else
								foreach ($pdo->query($sql) as $row) {
									echo "<option value='" . $row['id'] . " '> " . Functions::dayMonthDate($row['task_date']) . " (" . Functions::timeAmPm($row['task_time']) . ") - " .
									trim($row['task_description']) . " (" . 
									
									"</option>";
								}
								
							echo "</select>";
							Database::disconnect();
						?>
					</div>	<!-- end div: class="controls" -->
				</div> <!-- end div class="control-group" -->

				<div class="control-group <?php echo !empty($pictureError)?'error':'';?>">
					<label class="control-label">Picture</label>
					<div class="controls">
						<input type="hidden" name="MAX_FILE_SIZE" value="16000000">
						<input name="userfile" type="file" id="userfile">
					</div>
				</div>

				<div class="form-actions">
					<button type="submit" class="w3-button w3-pale-green w3-round-xlarge">Confirm</button>
						<a class="w3-button w3-sand w3-round-xlarge" href="b_tasks.php">Back</a>
				</div>
				
			</form>
			
		</div> <!-- end div: class="span10 offset1" -->
    </div> <!-- end div: class="container" -->

  </body>
</html>