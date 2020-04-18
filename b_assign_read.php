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

# get assignment details
$sql = "SELECT * FROM b_assign where id = ?";
$q = $pdo->prepare($sql);
$q->execute(array($id));
$data = $q->fetch(PDO::FETCH_ASSOC);

# get volunteer details
$sql = "SELECT * FROM b_person where id = ?";
$q = $pdo->prepare($sql);
$q->execute(array($data['assign_person_id']));
$perdata = $q->fetch(PDO::FETCH_ASSOC);

# get event details
$sql = "SELECT * FROM b_task where id = ?";
$q = $pdo->prepare($sql);
$q->execute(array($data['assign_task_id']));
$eventdata = $q->fetch(PDO::FETCH_ASSOC);

Database::disconnect();
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
				<h3>Task Details</h3>
			</div>
			
			<div class="form-horizontal" >
			
				<div class="control-group">
					<label class="control-label">Person</label>
					<div class="controls">
						<label class="checkbox">
							<?php echo htmlspecialchars($perdata['lname'] . ', ' . $perdata['fname']);?>
						</label>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">Task</label>
					<div class="controls">
						<label class="checkbox">
							<?php echo htmlspecialchars(trim($eventdata['task_description']));?>
						</label>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">Date, Time</label>
					<div class="controls">
						<label class="checkbox">
							<?php echo Functions::dayMonthDate($eventdata['task_date']) . ", " . Functions::timeAmPm($eventdata['task_time']);?>
						</label>
					</div>
				</div>

				<div class="control-group">
					<div class="controls ">
						<?php 
							if ($data['filesize'] > 0) 
								echo '<img  height=5%; width=15%; src="data:image/jpeg;base64,' . 
									base64_encode($data['filecontent'] ) . '" />'; 
							else 
								echo 'No photo on file.';
						?> <!-- converts to base 64 due to the need to read the binary files code and display img -->
					</div>
				</div>
				
				<div class="form-actions">
					<a class="w3-button w3-khaki w3-round-xlarge" href="b_assignments.php">Back</a>
				</div>
			
			</div> <!-- end div: class="form-horizontal" -->
			
		</div> <!-- end div: class="span10 offset1" -->
				
    </div> <!-- end div: class="container" -->
	
</body>
</html>