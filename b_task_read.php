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
$sql = "SELECT * FROM b_task where id = ?";
$q = $pdo->prepare($sql);
$q->execute(array($id));
$data = $q->fetch(PDO::FETCH_ASSOC);

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
				<h3><b>Task Details</b></h3>
			</div>
			
			<div class="form-horizontal" >
				<div class="control-group">
					
					<label class="control-label"><b>Date</b></label>
					<div class="controls">
						<label class="checkbox">
							<?php echo Functions::dayMonthDate($data['task_date']);?>
						</label>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label"><b>Time</b></label>
					<div class="controls">
						<label class="checkbox">
							<?php echo Functions::timeAmPm($data['task_time']);?>
						</label>
					</div>
				</div>
				
				
				
				<div class="control-group">
					<label class="control-label"><b>Description</b></label>
					<div class="controls">
						<label class="checkbox">
							<?php echo htmlspecialchars($data['task_description']);?>
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
				
				<div class="form-actions">
					<a class="w3-button w3-pale-blue w3-round-xlarge" href="b_assign_create.php?task_id=<?php echo htmlspecialchars($id); ?>">Take ownership for this task</a>
					<a class="w3-button w3-sand w3-round-xlarge" href="b_tasks.php">Back</a>
				</div>
				
				
				
			<div class="row">
				<h4>People Assigned to This Task</h4>
			</div>
			
			<?php
				$pdo = Database::connect();
				$sql = "SELECT * FROM b_assign, b_person WHERE assign_person_id = b_person.id AND assign_task_id = " . $data['id'] . ' ORDER BY lname ASC, fname ASC';
				

				$countrows = 0;
				if($_SESSION['b_person_title']=='Administrator') {
					foreach ($pdo->query($sql) as $row) {
						echo htmlspecialchars($row['lname'] . ', ' . $row['fname'] . ' - ' . $row['mobile'] . '<br />');
						
					$countrows++;
					}
				}
				else {
					foreach ($pdo->query($sql) as $row) {
						echo htmlspecialchars($row['lname'] . ', ' . $row['fname'] . ' - ' . '<br />');
					$countrows++;
					}
				}
				if ($countrows == 0) echo 'none.';
			?>
			
			</div> <!-- end div: class="form-horizontal" -->
			
		</div> <!-- end div: class="span10 offset1" -->
				
    </div> <!-- end div: class="container" -->
	
</body>
</html>