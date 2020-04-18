<?php

session_start();
if(!isset($_SESSION["b_person_id"])){ // if "user" not set,
	session_destroy();
	header('Location: login.php');     // go to login page
	exit;
}
$sessionid = $_SESSION['b_person_id'];
include 'functions.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <link   href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
    <script src="js/bootstrap.min.js"></script>
	
</head>

<body >
    <div class="container">
		<div class="row">
			<h3><b>Tasks</b></h3>
		</div>
		
		<div class="row">
			
			<p>
				<?php //if($_SESSION['b_person_title']=='Administrator')
					echo '<a href="b_task_create.php" class="w3-button w3-pale-green w3-round-xlarge">Add Task</a>';
				?>
				<?php //if($_SESSION['b_person_title']=='Person' || $_SESSION['b_person_title']=='Administrator')
					 
				echo '&nbsp; <a href="b_per_create.php" class="w3-button w3-pale-green w3-round-xlarge"> Add Person</a>&nbsp;';
				?>
				
				
				<!-- <a href="b_tasks.php"><b>Tasks</b></a> &nbsp; -->
				
				<?php //if($_SESSION['b_person_title']=='Administrator')
					echo '<a href="b_persons.php"><b>Users</b></a> &nbsp;';
				?>
				
				<?php //if($_SESSION['b_person_title']=='Administrator' || $_SESSION['b_person_title']=='Person')
					echo '<a href="b_assignments.php"><b>Assignments</b></a>&nbsp;';
				?>
				<a href="b_assignments.php?id=<?php echo $sessionid; ?>"><b>MyTasks</b></a>&nbsp;
				<a href="logout.php" class="w3-button w3-khaki w3-round-xlarge">Logout</a> 
			</p>
			
			<table class="table table-striped table-bordered" style="background-color: lightgrey !important">
				<thead>
					<tr>
						<th>Date</th>
						<th>Time</th>
						<th>Description</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
						include 'database.php';
						$pdo = Database::connect();
						$sql = 'SELECT `b_task`.*, SUM(case when assign_person_id ='. $_SESSION['b_person_id'] .' then 1 else 0 end) AS sumAssigns, COUNT(`b_assign`.assign_task_id) AS countAssigns FROM `b_task` LEFT OUTER JOIN `b_assign` ON (`b_task`.id=`b_assign`.assign_task_id) GROUP BY `b_task`.id ORDER BY `b_task`.task_date ASC, `b_task`.task_time ASC';
						foreach ($pdo->query($sql) as $row) {
							echo '<tr>';
							echo '<td>'. Functions::dayMonthDate($row['task_date']) . '</td>';
							echo '<td>'. Functions::timeAmPm($row['task_time']) . '</td>';
							
							if ($row['countAssigns']==0)
								echo '<td>'. $row['task_description'] . ' - UNSTAFFED </td>';
							else
								echo '<td>'. $row['task_description'] . ' (' . $row['countAssigns']. ' person)' . '</td>';
							//echo '<td width=250>';
							echo '<td>';
							echo '<a class="w3-button w3-khaki w3-round-xlarge" href="b_task_read.php?id='.$row['id'].'">Details</a> &nbsp;';
							//if ($_SESSION['b_person_title']=='Person' )
								//echo '<a class="w3-button w3-pale-blue w3-round-xlarge" href="b_assign_create.php?id='.$row['id'].'">Take Ownership</a> &nbsp;';
							//if ($_SESSION['b_person_title']=='Administrator' )
								echo '<a class="w3-button w3-pale-green w3-round-xlarge" href="b_task_update.php?id='.$row['id'].'">Update</a>&nbsp;';
							if ($_SESSION['b_person_title']=='Administrator' 
								&& $row['countAssigns']==0)
								echo '<a class="w3-button w3-pale-red w3-round-xlarge" href="b_task_delete.php?id='.$row['id'].'">Delete</a>';
							if($row['sumAssigns']==1) 
								echo " &nbsp;&nbsp;Me";
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