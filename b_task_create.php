<?php 

session_start();
if(!isset($_SESSION["b_person_id"])){ // if "user" not set,
	session_destroy();
	header('Location: login.php');     // go to login page
	exit;
}

require 'database.php';
require 'functions.php';

if ( !empty($_POST)) { // if not first time through

	// initialize user input validation variables
	$dateError = null;
	$timeError = null;
	
	$descriptionError = null;
	
	// initialize $_POST variables
	$date = $_POST['task_date'];
	$time = $_POST['task_time'];

	// initialize $_FILES variables
	$filename = $_FILES['userfile']['name'];
	$tmpName  = $_FILES['userfile']['tmp_name'];
	$filesize = $_FILES['userfile']['size'];
	$filetype = $_FILES['userfile']['type'];
	$filecontent = file_get_contents($tmpName);
	
	$description = $_POST['task_description'];		
	
	// validate user input
	$valid = true;
	if (empty($date)) {
		$dateError = 'Please enter Date';
		$valid = false;
	}
	if (empty($time)) {
		$timeError = 'Please enter Time';
		$valid = false;
	} 		
		
	if (empty($description)) {
		$descriptionError = 'Please enter Description';
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
		$sql = "INSERT INTO b_task (task_date,task_time,task_description,filename,filesize,filetype,filecontent) values(?,?,?, ?, ?, ?, ?)";
		$q = $pdo->prepare($sql);
		$q->execute(array($date,$time,$description,$filename,$filesize,$filetype,$filecontent));
		Database::disconnect();
		header("Location: b_tasks.php");
	}
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link   href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<style>
		input{
			height:50px !important;
		}

	</style>
</head>

<body>
    <div class="container">
		<div class="span10 offset1">
		
			<div class="row">
				<h3><b>Add New Task</b></h3>
			</div>
	
			<form class="form-horizontal" action="b_task_create.php" method="post" enctype="multipart/form-data">
			
				<div class="control-group <?php echo !empty($dateError)?'error':'';?>">
					<label class="control-label"><b>Date</b></label>
					<div class="controls">
						<input name="task_date" type="date"  placeholder="Date" value="<?php echo !empty($date)?htmlspecialchars($date):'';?>">
						<?php if (!empty($dateError)): ?>
							<span class="help-inline"><?php echo $dateError;?></span>
						<?php endif; ?>
					</div>
				</div>
			  
				<div class="control-group <?php echo !empty($timeError)?'error':'';?>">
					<label class="control-label"><b>Time</b></label>
					<div class="controls">
						<input name="task_time" type="time" placeholder="Time" value="<?php echo !empty($time)?htmlspecialchars($time):'';?>">
						<?php if (!empty($timeError)): ?>
							<span class="help-inline"><?php echo $timeError;?></span>
						<?php endif;?>
					</div>
				</div>
				
				
				
				<div class="control-group <?php echo !empty($descriptionError)?'error':'';?>">
					<label class="control-label"><b>Description</b></label>
					<div class="controls">
						<input name="task_description" type="text" placeholder="Description" value="<?php echo !empty($description)?htmlspecialchars($description):'';?>">
						<?php if (!empty($descriptionError)): ?>
							<span class="help-inline"><?php echo $descriptionError;?></span>
						<?php endif;?>
					</div>
				</div>

				<div class="control-group <?php echo !empty($pictureError)?'error':'';?>">
					<label class="control-label">Picture</label>
					<div class="controls">
						<input type="hidden" name="MAX_FILE_SIZE" value="16000000">
						<input name="userfile" type="file" id="userfile">
					</div>
				</div>
				
				<div class="form-actions">
					<button type="submit" class="w3-button w3-pale-green w3-round-xlarge">Create</button>
					<a class="w3-button w3-sand w3-round-xlarge" href="b_tasks.php">Back</a>
				</div>
				
			</form>
			
		</div> <!-- div: class="container" -->
				
    </div> <!-- div: class="container" -->
	
</body>
</html>