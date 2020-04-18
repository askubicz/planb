<?php

session_start();
if(!isset($_SESSION["b_person_id"])){ // if "user" not set,
	session_destroy();
	header('Location: login.php');     // go to login page
	exit;
}
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

<body >
    <div class="container">
		<div class="row">
			<h3><b>Person</b></h3>
		</div>
		<div class="row">
			
			<p>
				
				<?php if($_SESSION['b_person_title']=='Administrator' || $_SESSION['b_person_title']=='Person')
					echo '<a href="b_task_create.php" class="w3-button w3-pale-green w3-round-xlarge">Add Task</a>';
					echo '&nbsp; <a href="b_per_create.php" class="w3-button w3-pale-green w3-round-xlarge"> Add Person</a>&nbsp;';
				?>
				
				<!-- if($_SESSION['b_person_title']=='Administrator')
					echo '<a href="b_persons.php"><b>Persons</b></a> &nbsp;'; -->
				
				<a href="b_tasks.php"><b>Tasks</b></a> &nbsp;

				<?php if($_SESSION['b_person_title']=='Administrator' || $_SESSION['b_person_title']=='Person')
					echo '<a href="b_assignments.php"><b>Assignments</b></a>&nbsp;';
				?>
				<a href="b_assignments.php?id=<?php echo $sessionid; ?>"><b>MyTasks</b></a>&nbsp;
				<a href="logout.php" class="w3-button w3-khaki w3-round-xlarge">Logout</a> 
			</p>

			<table class="table table-striped table-bordered" style="background-color: lightgrey !important">
				<thead>
					<tr>
						<th>Name</th>
						<th>Email</th>
						<th>Mobile</th>
						<th>Action</th>
					</tr>
				</thead>

				<tbody>
					<?php 
						include 'database.php';
						$pdo = Database::connect();
						$sql = 'SELECT `b_person`.*, COUNT(`b_assign`.assign_person_id) AS countAssigns FROM `b_person` LEFT OUTER JOIN `b_assign` ON (`b_person`.id=`b_assign`.assign_person_id) GROUP BY `b_person`.id ORDER BY `b_person`.lname ASC, `b_person`.fname ASC';
					
						foreach ($pdo->query($sql) as $row) {
							echo '<tr>';
							// if ($row['countAssigns'] == 0)
							// 	echo '<td>'. trim($row['lname']) . ', ' . trim($row['fname']) . ' (' . substr($row['title'], 0, 1) . ') '.' - UNASSIGNED</td>';
							// else
							echo '<td>'. trim($row['lname']) . ', ' . trim($row['fname']) . ' (' . substr($row['title'], 0, 1) . ') - '.$row['countAssigns']. ' tasks</td>';
							echo '<td>'. $row['email'] . '</td>';
							echo '<td>'. $row['mobile'] . '</td>';
							echo '<td width=300>';
							# always allow read
							echo '<a class="w3-button w3-khaki w3-round-xlarge" href="b_per_read.php?id='.$row['id'].'">Details</a>&nbsp;';
							# person can update own record
							if ($_SESSION['b_person_title']=='Administrator'
								|| $_SESSION['b_person_id']==$row['id'])
								echo '<a class="w3-button w3-pale-green w3-round-xlarge" href="b_per_update.php?id='.$row['id'].'">Update</a>&nbsp;';
							# only admins can delete
							
							if ($_SESSION['b_person_title']=='Administrator' || $_SESSION['b_person_id']==$row['id']) // && $row['countAssigns']==0
								echo '<a class="w3-button w3-pale-red w3-round-xlarge" href="b_per_delete.php?id='.$row['id'].'">Delete</a>&nbsp;&nbsp;';
							
							

							echo '</td>';
							echo '</tr>';
						}
						Database::disconnect();
					?>
				</tbody>
			</table>
			
    	</div>
    </div> <!-- /container -->
  </body>
</html>