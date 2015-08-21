<?php
include ('config.php');
include ('dblogin.php');
// continue session
session_start();

$error = ['username' => '', 'email' => '', 'userpass' => '', 'hp' => '', 'token' => ''];
$username = '';
$email = '';
$new_member = true;

// if the form has been submitted
if ('POST' === $_SERVER['REQUEST_METHOD']) {

	$username = isset($_POST['username']) ? $_POST['username'] : '';
	$email = isset($_POST['email']) ? $_POST['email'] : '';
	//running PHP that is too low for password_hash();
	$userpass = sha1($_POST['userpass']);
	$token = isset($_POST['token']) ? $_POST['token'] : '';

	// if ($token !== $_SESSION['token']) {
	if (!$token) {
		$error['token'] = "Form submission is invalid.";
	}
	if (!empty($pot)) {
		$error['hp'] = "Form submission is invalid.";
	}

	// check for a username
	if (empty($username)) {
		$error['username'] = 'Required field';
	}
	// check for an email
	if (empty($email)) {
		$error['email'] = 'Required field';
	} else {
		// check to see if email address is unique
		$check = $dbConnect -> prepare('SELECT user_id FROM users WHERE email = :email');
		$check -> bindParam(':email', $email, PDO::PARAM_INT);
		$result = $check -> fetch(PDO::FETCH_ASSOC);
		// var_dump($result);
		$check -> execute();
		if (!$check -> execute()) {
			echo "sql failed";
		}

		$result = $check -> fetch(PDO::FETCH_ASSOC);
		if ($result) {
			$error['email'] = 'An account exists with this email address';
		}
	}
	// check for a password
	if (empty($userpass)) {
		$error['userpass'] = 'Required field';
	}
	if ($error['email'] == '') {

		if (isset($_SESSION["token"]) && isset($token)) {
			// printf('what what');
			if ($token == $_SESSION["token"]) {
				// printf('nopenope');
				$new_member = false;

			}
		} else {
		}
		if ($new_member) {
			$_SESSION['token'] = $token;
			// $token = $_SESSION['token'];

			// insert user into the users table
			$check = $dbConnect -> prepare('INSERT INTO users (username, email, userpass, signupdate) VALUES (:username, :email, :userpass, NOW())');
			$check -> execute(array(':username' => $username, ':email' => $email, ':userpass' => $userpass));
			$userid = $dbConnect -> lastInsertId();
			// echo $userid;

			// append user_id to session array
			$_SESSION['user_id'] = $userid;
			$_SESSION['username'] = $username;
			$_SESSION['email'] = $email;
			// $_SESSION['error'] = $error;

			header("Location: activity.php");
			exit ;

		}
	}
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title>T - R - A - C - E - S</title>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
		<link rel="stylesheet" href="assets/css/bootstrap.min.css">
		<link href='http://fonts.googleapis.com/css?family=Oswald:400,300,700' rel='stylesheet' type='text/css'>
		<link rel="stylesheet" href="assets/css/screen.css">		
		<style type="text/css">
			.profileimage {
				border: 1px solid #ccc;
				width: 100%;
			}
		</style>
						
	</head>
	<body>
		
		<!-- top navigation -->
	<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="container">
		<div class="navbar-header">
			<ul class="nav navbar-nav">
				<li><a href="index.php">Sign In</a></li>
				<li><a href="signup.php">Sign Up</a></li>
			</ul>
		</div>
	</div>
</div>
		<!-- content -->	
		<div class="container" style="margin-top: 65px">
		
			<h2>Sign up</h2>

			<!-- signup form -->
			<form method="post" action="signup.php">
				
				<div class="form-group">
					<label>Username</label>
					<!-- <input name="username" type="text" value="<?php echo $_POST['username']; ?>" class="form-control" /> -->
					<input name="username" type="text" value="<?php echo htmlspecialchars($username, ENT_QUOTES); ?>" class="form-control" />
					<span class="text-danger"><?php echo $error['username']; ?></span>
				</div>
				
				<div class="form-group">
					<label>E-mail</label>
					<!-- <input name="email" type="text" value="<?php echo $email; ?>" class="form-control" /> -->
					<input name="email" type="text" value="<?php echo htmlentities($email, ENT_QUOTES); ?>" class="form-control" />
					<!-- <input name="email" type="text" value="<?php echo htmlspecialchars($email, ENT_QUOTES); ?>" class="form-control" /> -->					
					<span class="text-danger"><?php echo $error['email']; ?></span>
				</div>
				
				<div class="form-group">
					<label>Password</label>
					<input name="userpass" type="password" class="form-control" />
					<span class="text-danger"><?php echo $error['userpass']; ?></span>
				</div>
            
			<p class="hp">
                <input type="text" name="ssn" id="ssn" value="">
            </p>
            <!-- <input type="hidden" name="token" id="token" value="<?php echo md5(uniqid(rand(), true)); ?>"> -->
                <input type="hidden" name="token" id="token" value="<?php echo md5(uniqid(rand(), true)); ?>"


				<!-- submit button -->
				<div class="form-group">
					<input name="submit" type="submit" value="Sign up" class="btn btn-primary" />
				</div>
				
			</form>
			
			<!-- sign in link -->
			<p>Already have an account? <a href="index.php">Sign in</a>!</p>
			
		</div>
	
	</body>
</html>