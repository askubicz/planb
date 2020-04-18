<?php

error_reporting(E_ALL); ini_set('display_errors', 1);
// Start or resume session, and create: $_SESSION[] array
session_start(); 

require 'database.php';

if (isset($_POST["submit"])) { // if $_POST filled then process the form

	// initialize $_POST variables
	$username = $_POST['username']; // username is email address
	$password = $_POST['password'];
	$passwordhash = MD5($password);
	// echo $password . " " . $passwordhash; exit();
	// robot 87b7cb79481f317bde90c116cf36084b
		
	// verify the username/password
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM b_person WHERE email = ? AND password = ? LIMIT 1";
	$q = $pdo->prepare($sql);
	$q->execute(array($username,$passwordhash));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	
	if($data) { // if successful login set session variables
		echo "success!";
		$_SESSION['b_person_id'] = $data['id'];
		$sessionid = $data['id'];
		$_SESSION['b_person_title'] = $data['title'];
		Database::disconnect();
		header("Location: b_assignments.php?id=$sessionid ");
		// javascript below is necessary for system to work on github
		echo "<script type='text/javascript'> document.location = 'b_assignments.php'; </script>";
		exit();
	}
	else { // otherwise go to login error page
		Database::disconnect();
		header("Location: login_error.html");
	}
}
// if $_POST NOT filled then display login form, below.

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
	<script src="js/bootstrap.min.js"></script>
	
	
    <link   href="css/bootstrap.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css"> 
	
    
	  
	
</head>

<body >
    <div class="container">

		<div class="span10 offset1">
			
			<!--
			<div class="row">
				<br />
				<p style="color: red;">System temporarily unavailable.</p>
			</div>
			-->

			<div class="row">
				<h3><b>Project Manager Login</b></h3>
			</div>

			<form class="form-horizontal" action="login.php" method="post">
								  
				<div class="control-group">
					<label class="control-label"><b>Username (Email)</b></label>
					<div class="controls">
						<input name="username" type="text"  placeholder="me@email.com" required> 
						
					</div>	
				</div> 
				
				<div class="control-group">
					<label class="control-label"><b>Password</b></label>
					<div class="controls">
						<input  name="password"  type="password" placeholder="********" required> 
					</div>
				</div> 

				<div class="form-actions">
					<button type="submit" name="submit" class="w3-button w3-light-green w3-round-xlarge">Sign in</button>
					&nbsp; &nbsp;
					<a class="w3-button w3-light-blue w3-round-xlarge" href="b_per_create2.php">Join (New User)</a>
				</div>
				
				
				<p><strong>Regarding passwords</strong>: Please create a new unique password for this site. <strong><em>
				<span style="color: red;">Please do not use your regular SVSU password.</span><em></strong> </p>
				
				<br />

				<footer>
					<small>&copy; Copyright 2020, Adriana Kubicz
					</small>
				</footer>
				
			</form>


		</div> <!-- end div: class="span10 offset1" -->
				
    </div> <!-- end div: class="container" -->

  </body>
  
</html>
	