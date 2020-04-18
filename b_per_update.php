<?php 

error_reporting(E_ALL); ini_set('display_errors', 1);

session_start();
if(!isset($_SESSION["b_person_id"])){ // if "user" not set,
	session_destroy();
	header('Location: login.php');     // go to login page
	exit;
}
	
require 'database.php';

$id = $_GET['id'];

if ( !empty($_POST)) { // if $_POST filled then process the form

	// initialize user input validation variables
	$fnameError = null;
	$lnameError = null;
	$emailError = null;
	$mobileError = null;
	$passwordError = null;
	$titleError = null;
	$pictureError = null; // not used
	$addressError = null;
	$cityError = null;
	$stateError = null;
	$zipError = null;
	
	// initialize $_POST variables
	$fname = $_POST['fname'];
	$lname = $_POST['lname'];
	$email = $_POST['email'];
	$mobile = $_POST['mobile'];
	$password = $_POST['password'];
	$passwordhash = MD5($password);
	$title =  $_POST['title'];
	$address = $_POST['address'];
	$city = $_POST['city'];
	$state = $_POST['state'];
	$zip = $_POST['zip'];
	
	// initialize $_FILES variables
	$fileName = $_FILES['userfile']['name'];
	$tmpName  = $_FILES['userfile']['tmp_name'];
	$fileSize = $_FILES['userfile']['size'];
	$fileType = $_FILES['userfile']['type'];
	
	if($_FILES['userfile']['error'] == 0){
		$content = file_get_contents($tmpName);
		
	} else {
	$content = null;
	}
	
	
	// validate user input
	$valid = true;
	if (empty($fname)) {
		$fnameError = 'Please enter First Name';
		$valid = false;
	}
	if (empty($lname)) {
		$lnameError = 'Please enter Last Name';
		$valid = false;
	}

	if (empty($email)) {
		$emailError = 'Please enter valid Email Address (REQUIRED)';
		$valid = false;
	} else if ( !filter_var($email,FILTER_VALIDATE_EMAIL) ) {
		$emailError = 'Please enter a valid Email Address';
		$valid = false;
	}

	// email must contain only lower case letters
	if (strcmp(strtolower($email),$email)!=0) {
		$emailError = 'email address can contain only lower case letters';
		$valid = false;
	}

	if (empty($mobile)) {
		$mobileError = 'Please enter Mobile Number (or "none")';
		$valid = false;
	}
	if(!preg_match("/^[0-9]{3}-[0-9]{3}-[0-9]{4}$/", $mobile)) {
		$mobileError = 'Please write Mobile Number in form 000-000-0000';
		$valid = false;
	}
	if (empty($password)) {
		$passwordError = 'Please enter valid Password';
		$valid = false;
	}
	if (empty($title)) {
		$titleError = 'Please enter valid Title';
		$valid = false;
	}
	// restrict file types for upload
	
	// insert data
	if ($valid) {
	
		if($fileSize > 0) { // if file was updated, update all fields
			$pdo = Database::connect();
			
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE b_person  set fname = ?, lname = ?, email = ?, mobile = ?, password = ?, title = ?, filename = ?,filesize = ?,filetype = ?,filecontent = ?, address = ?, city = ?, state = ?, zip = ? WHERE id = ?";
			$q = $pdo->prepare($sql);
			$q->execute(array($fname, $lname, $email, $mobile, $password, $title, $fileName,$fileSize,$fileType,$content,$address,$city,$state,$zip, $id));
			Database::disconnect();
			header("Location: b_persons.php");
		}
		else { // otherwise, update all fields EXCEPT file fields
			$pdo = Database::connect();
			$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$sql = "UPDATE b_person  set fname = ?, lname = ?, email = ?, mobile = ?, password = ?, title = ?, address = ?, city = ?, state = ?, zip = ? WHERE id = ?";
			$q = $pdo->prepare($sql);
			$q->execute(array($fname, $lname, $email, $mobile, $password, $title, $address, $city, $state, $zip, $id));
			Database::disconnect();
			header("Location: b_persons.php");
		}
	}
} else { // if $_POST NOT filled then pre-populate the form
	$pdo = Database::connect();
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$sql = "SELECT * FROM b_person where id = ?";
	$q = $pdo->prepare($sql);
	$q->execute(array($id));
	$data = $q->fetch(PDO::FETCH_ASSOC);
	$fname = $data['fname'];
	$lname = $data['lname'];
	$email = $data['email'];
	$mobile = $data['mobile'];
	$password = $data['password'];
	$title =  $data['title'];
	$address = $data['address'];
	$city = $data['city'];
	$state = $data['state'];
	$zip = $data['zip'];
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

		<div class="span10 offset1">
		
			<div class="row">
				<h3>Update Person Details</h3>
			</div>
	
			<form class="form-horizontal" action="b_per_update.php?id=<?php echo htmlspecialchars($id)?>" method="post" enctype="multipart/form-data">
			
				<!-- Form elements (same as file: fr_per_create.php) -->

				<div class="control-group <?php echo htmlspecialchars(!empty($fnameError))?'error':'';?>">
					<label class="control-label">First Name</label>
					<div class="controls">
						<input name="fname" type="text"  placeholder="First Name" value="<?php echo htmlspecialchars(!empty($fname))?htmlspecialchars($fname):'';?>">
						<?php if (!empty($fnameError)): ?>
							<span class="help-inline"><?php echo htmlspecialchars($fnameError);?></span>
						<?php endif; ?>
					</div>
				</div>
				
				<div class="control-group <?php echo htmlspecialchars(!empty($lnameError))?'error':'';?>">
					<label class="control-label">Last Name</label>
					<div class="controls">
						<input name="lname" type="text"  placeholder="Last Name" value="<?php echo htmlspecialchars(!empty($lname))?htmlspecialchars($lname):'';?>">
						<?php if (!empty($lnameError)): ?>
							<span class="help-inline"><?php echo htmlspecialchars($lnameError);?></span>
						<?php endif; ?>
					</div>
				</div>
				
				<div class="control-group <?php echo htmlspecialchars(!empty($emailError))?'error':'';?>">
					<label class="control-label">Email</label>
					<div class="controls">
						<input name="email" type="text" placeholder="Email Address" value="<?php echo htmlspecialchars(!empty($email))?htmlspecialchars($email):'';?>">
						<?php if (!empty($emailError)): ?>
							<span class="help-inline"><?php echo htmlspecialchars($emailError);?></span>
						<?php endif;?>
					</div>
				</div>
				
				<div class="control-group <?php echo htmlspecialchars(!empty($mobileError))?'error':'';?>">
					<label class="control-label">Mobile Number</label>
					<div class="controls">
						<input name="mobile" type="text"  placeholder="Mobile Phone Number" value="<?php echo htmlspecialchars(!empty($mobile))?htmlspecialchars($mobile):'';?>">
						<?php if (!empty($mobileError)): ?>
							<span class="help-inline"><?php echo htmlspecialchars($mobileError);?></span>
						<?php endif;?>
					</div>
				</div>
				
				<div class="control-group <?php echo htmlspecialchars(!empty($passwordError))?'error':'';?>">
					<label class="control-label">Password</label>
					<div class="controls">
						<input id="password" name="password" type="text"  placeholder="Password" value="<?php echo htmlspecialchars(!empty($password))?htmlspecialchars($password):'';?>">
						<?php if (!empty($passwordError)): ?>
							<span class="help-inline"><?php echo htmlspecialchars($passwordError);?></span>
						<?php endif;?>
					</div>
				</div>

				<div class="control-group <?php echo htmlspecialchars(!empty($addressError))?'error':'';?>">
					<label class="control-label">Mailing Address</label>
					<div class="controls">
						<input name="address" type="text"  placeholder="123 Street" value="<?php echo htmlspecialchars(!empty($address))?htmlspecialchars($address):'';?>">
						<?php if (!empty($addressError)): ?>
							<span class="help-inline"><?php echo htmlspecialchars($addressError);?></span>
						<?php endif; ?>
					</div>
				</div>

				<div class="control-group <?php echo htmlspecialchars(!empty($cityError))?'error':'';?>">
					<label class="control-label">City</label>
					<div class="controls">
						<input name="city" type="text"  placeholder="City" value="<?php echo htmlspecialchars(!empty($city))?htmlspecialchars($city):'';?>">
						<?php if (!empty($cityError)): ?>
							<span class="help-inline"><?php echo htmlspecialchars($cityError);?></span>
						<?php endif; ?>
					</div>
				</div>

				<div class="control-group <?php echo htmlspecialchars(!empty($stateError))?'error':'';?>">
					<label class="control-label">State</label>
					<div class="controls">
						<input name="state" type="text"  placeholder="ST" value="<?php echo htmlspecialchars(!empty($state))?htmlspecialchars($state):'';?>">
						<?php if (!empty($stateError)): ?>
							<span class="help-inline"><?php echo htmlspecialchars($stateError);?></span>
						<?php endif; ?>
					</div>
				</div>

				<div class="control-group <?php echo htmlspecialchars(!empty($zipError))?'error':'';?>">
					<label class="control-label">Zip Code</label>
					<div class="controls">
						<input name="zip" type="text"  placeholder="12345" value="<?php echo htmlspecialchars(!empty($zip))?htmlspecialchars($zip):'';?>">
						<?php if (!empty($zipError)): ?>
							<span class="help-inline"><?php echo htmlspecialchars($zipError);?></span>
						<?php endif; ?>
					</div>
				</div>
				
				<div class="control-group">
					<label class="control-label">Title</label>
					<div class="controls">
						<select class="form-control" name="title">
							<?php 
							# editor is a volunteer only allow volunteer option
							if (0==strcmp($_SESSION['b_person_title'],'Person')) echo '<option selected value="Person" >Person</option>';
							else if($title==Person) echo 
							'<option selected value="Person" >Person</option><option value="Administrator" >Administrator</option>';
							else echo
							'<option value="Person">Person</option>
							<option selected value="Administrator" >Administrator</option>';
							?>
						</select>
					</div>
				</div>
			  
				<div class="control-group <?php echo htmlspecialchars(!empty($pictureError))?'error':'';?>">
					<label class="control-label">Picture</label>
					<div class="controls">
						<input type="hidden" name="MAX_FILE_SIZE" value="16000000">
						<input name="userfile" type="file" id="userfile">
					</div>
				</div>
			  
				<div class="form-actions">
					<button type="submit" class="btn btn-success">Update</button>
					<a class="btn" href="b_persons.php">Back</a>
				</div>
				
			</form>
			
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
				
		</div><!-- end div: class="span10 offset1" -->
		
    </div> <!-- end div: class="container" -->
	
</body>
</html>