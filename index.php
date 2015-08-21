<?php
include('config.php');
include('dblogin.php');
session_start();

$error = ['email' => '', 'userpass' => '', 'user' => ''];
$email = '';

// if the submit button has been pressed
// if ('POST' === $_SERVER['REQUEST_METHOD']) {
	if(isset($_POST['submit'])) {
	$email 		= isset($_POST['email']) ? $_POST['email'] : '';
	$userpass 	= isset($_POST['userpass']) ? $_POST['userpass'] : '';
	$pot 		= isset($_POST['ssn']) ? $_POST['ssn'] : '';

	if (!empty($pot)) {
		$error['hp'] = "Form submission is invalid.";
	}	
	// check for a email
// 	if(empty($_POST['email']))
	if(empty($email))

	{
		$error['email'] = 'Required field';
	} 
	
	// check for a password
// 	if(empty($_POST['userpass']))
	if(empty($userpass))
	{
		$error['userpass'] = 'Required field';
	} 
	
	// check signin credentials
	if(!empty($email) && !empty($userpass))
	{
		$check = $dbConnect -> prepare('SELECT user_id FROM users WHERE email = :email AND userpass = :userpass');
		$check -> bindValue(':email', $email, PDO::PARAM_STR);
		$check -> bindValue(':userpass', $userpass, PDO::PARAM_STR);		
		$result = $check -> fetch(PDO::FETCH_ASSOC);
		$check -> execute();
		echo "array dump";
		print_r($result);

		if(!$result['user_id'])
		{
			$error['user'] = 'Invalid username and/or password';
		}
	}
// 	var_dump($result);
// 	var_dump($error);
	
	// if there are no errors
// 	if(sizeof($error) == 0)
	if(sizeof($error) < 2)

	{
		// append user variables to session
		$_SESSION['user_id'] = $row['user_id'];
		$_SESSION['username'] = $row['username'];
		
		// redirect user to profile page
		header("Location: activity.php");
		exit();

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
		<?php include('topnavigation.php'); ?>
		
		<!-- content -->	
		<div class="container" style="margin-top: 65px">
		
			<h2>Sign in</h2>
			
			<?php
				// check for a user error
				if($error['user'])
				{
					echo "<div class=\"text-danger\">{$error['user']}</div>";
				}
			?>
						
			<form method="post" action="index.php">
				<div class="form-group">
					<label>Email</label><br />
					<input name="email" type="text" value="<?php echo htmlentities($email, ENT_QUOTES); ?>" class="form-control" />
					<span class="text-danger"><?php echo $error['email']; ?></span>
				</div>
				<div class="form-group">
					<label for="password">Password</label><br />
					<input id="password" name="userpass" type="password" class="form-control" value="<?php echo $error['userpass']; ?>" />
					<span class="text-danger"><?php echo $error['userpass']; ?></span>
				</div>
			<p class="hp">
                <input type="text" name="ssn" id="ssn" value="">
            </p>				
				<div class="form-group">
					<input name="submit" type="submit" value="Sign in" class="btn btn-primary" />
				</div>
			</form>
			<p>Don't have an account with TRACES? <a href="signup.php">Click here to start following.</a></p>			
		</div>
	</body>
</html>