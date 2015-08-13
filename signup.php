<?php

// include configuration file
include('config.php');
include('dblogin.php');

// continue session
session_start();
// create an empty error array
$error = array();

// if the form has been submitted
if ('POST' === $_SERVER['REQUEST_METHOD'])
{

    if ($token !== $_SESSION['token'])
    {
        $errors['token'] = "Form submission is invalid.";
    }
    if (!empty($pot))
    {
        $errors['hp'] = "Form submission is invalid.";
    }

	// check for a username
	if(empty($_POST['username']))
	{
		$error['username'] = 'Required field';
	} 

	// check for an email
	if(empty($_POST['email']))
	{
		$error['email'] = 'Required field';
	} else if (!empty($_POST['email'])){
	
		// check to see if email address is unique
		$sql = "SELECT user_id FROM users WHERE email = '{$_POST['email']}'";
		$result = mysqli_query($db, $sql);
		var_dump($result);

		if(mysqli_num_rows($result) > 0)
		{
			$error['email'] = 'You already have an account';
		}
	}
	
	// check for a password
	if(empty($_POST['userpass']))
	{
		$error['userpass'] = 'Required field';
	} 
	
	// if there are no errors
	if(sizeof($error) == 0){

        $username = $_POST['username'];
        $email    = $_POST['email'];

		$_SESSION['trc'] = array();	

		// insert user into the users table
		$sql = "INSERT INTO users (
					user_id, 
					username, 
					email, 
					userpass,
					signupdate
				) VALUES (
					null,
					'{$username}',
					'{$email}',
					sha1('{$_POST['userpass']}'),
					NOW()
					)";

		$result = mysqli_query($db, $sql);
		
		// obtain user_id from table
		$user_id = mysqli_insert_id($db);
		

		// append user_id to session array
		$_SESSION['trc']['user_id'] = $user_id;

		$_SESSION['trc']['username'] = $username;
		$_SESSION['trc']['email']    = $email;
		$_SESSION['trc']['error']    = $_POST['error'];	

		header("Location: activity.php");
		exit;
				
	} 
}
    else if(isset($_SESSION['trc']) && is_array($_SESSION['trc']))
    {
        $username = $_SESSION['trc']['username'];
        $email    = $_SESSION['trc']['email'];
        $error   = $_SESSION['trc']['error'];
		
}

$_SESSION['token'] = md5(uniqid(rand(), true));


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
		<?php include('topnavigation.php'); ?>
		
		<!-- content -->	
		<div class="container" style="margin-top: 65px">
		
			<h2>Sign up</h2>

			<!-- signup form -->
			<form method="post" action="signup.php">
				
				<!-- first name -->
				<div class="form-group">
					<label>Username</label>
					<input name="username" type="text" value="<?php echo $_POST['username']; ?>" class="form-control" />
					<span class="text-danger"><?php echo $error['username']; ?></span>
				</div>
				<!-- e-mail -->
				<div class="form-group">
					<label>E-mail</label>
					<input name="email" type="text" value="<?php echo $_POST['email']; ?>" class="form-control" />
					<span class="text-danger"><?php echo $error['email']; ?></span>
				</div>
				
				<!-- password -->
				<div class="form-group">
					<label>Password</label>
					<input name="userpass" type="password" class="form-control" />
					<span class="text-danger"><?php echo $error['userpass']; ?></span>
				</div>
            
			<p class="hp">
                <input type="text" name="ssn" id="ssn" value="">
            </p>				
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