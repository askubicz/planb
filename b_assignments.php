<?php 

session_start();
if(!isset($_SESSION["b_person_id"])){ // if "user" not set,
	session_destroy();
	header('Location: login.php');   // go to login page
	exit;
}
$id = $_GET['id']; // for MyAssignments
$sessionid = $_SESSION['b_person_id'];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">

    <link   href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	
</head>

<body>
    <div class="container">
		<div class="row">
			<h3><b><?php if($id) echo 'My ';?>Task Assignments</b></h3>
		</div>
		
		<div class="row">
			
			<p>
				<?php //if($_SESSION['b_person_title']=='Person' || $_SESSION['b_person_title']=='Administrator')
					echo '<a href="b_task_create.php" class="w3-button w3-pale-green w3-round-xlarge">Add Task</a>';
					echo '&nbsp; <a href="b_per_create.php" class="w3-button w3-pale-green w3-round-xlarge">Add Person</a>&nbsp;';
					echo '&nbsp;&nbsp;<a href="b_assign_create.php" class="w3-button w3-pale-green w3-round-xlarge">Assign Task</a>&nbsp;';
				?>
				
				<?php //if($_SESSION['b_person_title']=='Administrator')
					echo '<a href="b_persons.php"><b>Users</b></a> &nbsp;';
				?>

				<a href="b_tasks.php"><b>Tasks</b></a> &nbsp;
				<a href="b_assignments.php?id=<?php echo $sessionid; ?>"><b>MyTasks</b></a>&nbsp;
				
				<a href="logout.php" class="w3-button w3-khaki w3-round-xlarge">Logout</a> &nbsp;&nbsp;&nbsp;
			</p>
			
			<table class="table table-striped table-bordered" style="background-color: lightgrey !important">
				<thead>
					<tr>
						<th>Date</th>
						<th>Time</th>
						<th>Task</th>
						<th>Person</th>
						<th>Action</th>
					</tr>
				</thead>
				
				<tbody>
				<?php 
					include 'database.php';
					include 'functions.php';
					$pdo = Database::connect();
					
					if($id) 
						$sql = "SELECT * FROM b_assign 
						LEFT JOIN b_person ON b_person.id = b_assign.assign_person_id 
						LEFT JOIN b_task ON b_task.id = b_assign.assign_task_id
						WHERE b_person.id = $id 
						ORDER BY task_date ASC, task_time ASC, lname ASC, lname ASC;";
					else
						$sql = "SELECT * FROM b_assign 
						LEFT JOIN b_person ON b_person.id = b_assign.assign_person_id 
						LEFT JOIN b_task ON b_task.id = b_assign.assign_task_id
						ORDER BY task_date ASC, task_time ASC, lname ASC, lname ASC;";

					foreach ($pdo->query($sql) as $row) {
						//var_dump($row);
						echo '<tr>';
						echo '<td>'. Functions::dayMonthDate($row['task_date']) . '</td>';
						echo '<td>'. Functions::timeAmPm($row['task_time']) . '</td>';
						
						echo '<td>'. $row['task_description'] . '</td>';
						echo '<td>'. $row['lname'] . ', ' . $row['fname'] . '</td>';
						echo '<td width=350>';
						# use $row[0] because there are 3 fields called "id"
						echo '<a class="w3-button w3-khaki w3-round-xlarge" href="b_assign_read.php?id='.$row[2].'">Details</a>';

						if ($_SESSION['b_person_title']=='Administrator' || $_SESSION['b_person_title']=='Person' )
							echo '&nbsp;<a class="w3-button w3-pale-green w3-round-xlarge" href="b_assign_update.php?id='.$row[2].'">Update</a>';

						if ($_SESSION['b_person_title']=='Administrator' || $_SESSION['b_person_title']=='Person'|| $_SESSION['b_person_id']==$row['assign_person_id'])
							echo '&nbsp;<a class="w3-button w3-pale-red w3-round-xlarge" href="b_assign_delete.php?id='.$row[2].'">Delete</a>';
						if($_SESSION["b_person_id"] == $row['assign_person_id']) 		echo " &nbsp;&nbsp;Me";
						echo '</td>';
						echo '</tr>';
					}
					Database::disconnect();
				?>
				</tbody>
			</table>
    	</div>

    </div> <!-- end div: class="container" -->
	
</body>
</html>