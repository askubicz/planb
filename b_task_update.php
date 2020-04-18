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

if ( !empty($_POST)) { // if $_POST filled then process the form

	# initialize/validate (same as file: fr_evenb_create.php)

	// initialize user input validation variables
	$dateError = null;
	$timeError = null;
	$descriptionError = null;
	
	// initialize $_POST variables
	$date = $_POST['task_date'];
	$time = $_POST['task_time'];
	$description = $_POST['task_description'];	

	// initialize $_FILES variables
	$filename = $_FILES['userfile']['name'];
	$tmpname  = $_FILES['userfile']['tmp_name'];
	$filesize = $_FILES['userfile']['size'];
	$filetype = $_FILES['userfile']['type'];
	
	if($_FILES['userfile']['error'] == 0){
		$filecontent = file_get_contents($tmpName);
		
	} else {
	$filecontent = null;
	}
	
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
	
	if ($valid) { // if valid user input update the database
		
		if (filesize > 0) {
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE b_task  set task_date = ?, task_time = ?,  task_description = ?, filename = ?,filesize = ?,filetype = ?,filecontent = ? WHERE id = ?";
			$q = $pdo->prepare($sql);
			$q->execute(array($date,$time,$description,$filename,$filesize,$filetype,$filecontent,$id));
			Database::disconnect();
			header("Location: b_tasks.php");
		}
		else {
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE b_task  set task_date = ?, task_time = ?,  task_description = ? WHERE id = ?";
			$q = $pdo->prepare($sql);
			$q->execute(array($date,$time,$description,$id));
			Database::disconnect();
			header("Location: b_tasks.php");
		}
	}
} else { // if $_POST NOT filled then pre-populate the form
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM b_task where id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($id));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	$date = $data['task_date'];
	$time = $data['task_time'];

	$description = $data['task_description'];
	Database::disconnect();
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
				<h3><b>Update Task Details</b></h3>
			</div>
	
			<form class="form-horizontal" action="b_task_update.php?id=<?php echo htmlspecialchars($id)?>" method="post" enctype="multipart/form-data">
			
				<div class="control-group <?php echo htmlspecialchars(!empty($dateError))?'error':'';?>">
					<label class="control-label"><b>Date</b></label>
					<div class="controls">
						<input name="task_date" type="date"  placeholder="Date" value="<?php echo htmlspecialchars(!empty($date))?htmlspecialchars($date):'';?>">
						<?php if (!empty($dateError)): ?>
							<span class="help-inline"><?php echo htmlspecialchars($dateError);?></span>
						<?php endif; ?>
					</div>
				</div>
			  
				<div class="control-group <?php echo htmlspecialchars(!empty($timeError))?'error':'';?>">
					<label class="control-label"><b>Time</b></label>
					<div class="controls">
						<input name="task_time" type="time" placeholder="Time" value="<?php echo htmlspecialchars(!empty($time))?htmlspecialchars($time):'';?>">
						<?php if (!empty($timeError)): ?>
							<span class="help-inline"><?php echo htmlspecialchars($timeError);?></span>
						<?php endif;?>
					</div>
				</div>
			
				<div class="control-group <?php echo htmlspecialchars(!empty($descriptionError))?'error':'';?>">
					<label class="control-label"><b>Description</b></label>
					<div class="controls">
						<input name="task_description" type="text" placeholder="Description" value="<?php echo htmlspecialchars(!empty($description))?htmlspecialchars($description):'';?>">
						<?php if (!empty($descriptionError)): ?>
							<span class="help-inline"><?php echo htmlspecialchars($descriptionError);?></span>
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

				<!-- Display photo, if any --> 

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

				<div class="form-actions">
					<button type="submit" class="w3-button w3-pale-green w3-round-xlarge">Update</button>
					<a class="w3-button w3-sand w3-round-xlarge" href="b_tasks.php">Back</a>
				</div>
				
			</form>
			
		</div><!-- end div: class="span10 offset1" -->
				
    </div> <!-- end div: class="container" -->
</body>
</html>