<?php

include('config.php');
include('dblogin.php');	

// continue session
session_start();
$error = array();
$action ='';
$id ='';
$getAction = isset($_GET['action']) ? $_GET['action'] : '';
$getId = isset($_GET['id']) ? $_GET['id'] : '';
$userid = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : '';
$shoutid = '';
// check for a user_id
// if(!$_SESSION['user_id'])
if(!$userid)
{
	// redirect user to homepage if they are not signed in
	header("Location: index.php");	
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
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script src="assets/js/vendor/jquery.ui.widget.js"></script>
		<script src="assets/js/jquery.iframe-transport.js"></script>
		<script src="assets/js/jquery.fileupload.js"></script>
		<script>
			$(function() {
				$('#fileupload').fileupload({
					dataType : 'json',
					add : function(e, data) {
						data.context = $('<button/>').text('Upload').appendTo(document.body).click(function() {
							data.context = $('<p/>').text('Uploading...').replaceAll($(this));
							data.submit();
						});
					},
					done : function(e, data) {
						data.context.text('Upload finished.');
					}
				});
			}); 
</script>					
	</head>
	<body>
		
		<!-- top navigation -->
		<?php include('topnavigation.php'); ?>
		
		<!-- content -->	
		<div class="container" style="margin-top: 65px">

			<h2>Welcome <?php echo "{$_SESSION['username']}, {$userid}"; ?>!</h2>
			
	<?php
		// check for shout removal
		if($getAction == 'remove')
			{
				$statement = $dbConnect->prepare('SELECT user_id FROM shouts2 WHERE shout_id = :id LIMIT 1');
				$statement->bindParam(':id', $getAction, PDO::PARAM_STR);
				$result = $statement->fetch(PDO::FETCH_ASSOC);
				$statement->execute();
  		if(!$statement->execute()) {
				echo "sql failed";
			}					
				// check ownership
				if($result['user_id'] == $userid)
				{
					// delete shout
					$statement2 = $dbConnect->prepare('DELETE FROM shouts2 WHERE shout_id = :getId LIMIT 1');
					$statement2->bindParam(':getId', $getAction, PDO::PARAM_INT);
					$result = $statement2->fetch(PDO::FETCH_ASSOC);					
					$statement2->execute();
  					if(!$statement2->execute()) {
						echo "sql failed";
					}		
									
					// display confirmation
					echo "<div class=\"alert alert-success\">Your shout has been removed</div>";
				}
			}
			

				$postedShout = isset($_POST['shout']) ? $_POST['shout'] : '';
				if(isset($_POST['submit'])){	
				// check for a shout
				if(empty($postedShout))
				{
					$error[] = 'A shout is required';
				}
				echo "Errors!";
				var_dump($error);
				// if there are no errors, insert shout into the database.
				// otherwise, display errors.
				if(sizeof($error) == 0)
				{
				// insert shout
				$statement3 = $dbConnect -> prepare('INSERT INTO shouts2 (user_id, shout, shout_date) VALUES (:userid, :shout, NOW())');
				$statement3 -> execute(array(':userid' => $userid, ':shout' => $postedShout));
				$shoutid = $dbConnect -> lastInsertId();					
					// display confirmation
					echo "<div class=\"text-success\">Your shout has been added</div>";
					
				} else {
					
					// display error message
					foreach($error as $value)
					{
						echo "<div class=\"text-error\">{$value}</div>";
					}
					
				}
			}

			
			?>
			
			<!-- shoutbox form -->
			<form method="post" action="activity.php" style="margin-bottom: 25px">
				<div class="form-group">
					<textarea name="shout" placeholder="What do you want to say?" class="form-control" rows="5"></textarea>
				</div>
				<input name="submit" type="submit" value="Shout" class="btn btn-primary" />
			</form>
			<input id="fileupload" type="file" name="files[]" data-url="server/php/" multiple />

			<?php			
					
			// select all shouts from the database
			$shoutDate['shout_date'] = DateTime::createFromFormat('m/d/Y', '08/14/2015');		
			$statement4 = $dbConnect -> prepare('SELECT shout_id, user_id, shout, shout_date FROM shouts2 ORDER BY :shoutdate DESC');
			$statement4 -> bindValue(':shoutdate', $shoutDate['shout_date']->format('Y-m-d'), PDO::PARAM_LOB);
			$statement4 -> execute();
			
			$row = $statement4 -> fetchAll(PDO::FETCH_ASSOC);
			var_dump($row[0]);
			if(!empty($row)) 
			{

 				$statement5 = $dbConnect->prepare('SELECT user_id, username FROM users WHERE user_id = :userid');
 				$statement6 = $dbConnect->prepare('SELECT shout_id, user_id, shout, shout_date FROM shouts2 WHERE user_id = :userid ORDER BY :shoutdate DESC');

 				$statement5->bindValue(':userid', $userid, PDO::PARAM_INT);
 				$statement6->bindValue(':userid', $userid, PDO::PARAM_INT);
				$statement6 -> bindValue(':shoutdate', $shoutDate['shout_date']->format('Y-m-d'), PDO::PARAM_LOB);

 				$statement5->execute();
 				$statement6->execute();
				if(!$statement6->execute()){
					echo "sql search failed";

				}
				$row2 = $statement5 -> fetchAll(PDO::FETCH_ASSOC);
				$row3 = $statement6->fetchAll(PDO::FETCH_ASSOC);
//  				var_dump($row2);				
				// display shout (two columns - left column display the image; right column displays the text)
				echo "<div class=\"well\">";
				echo "<div class=\"row\">";
				echo "<div class=\"col-md-1\">";
// check for a profile image
				if(file_exists('photos/' . $userid . '.jpg'))
				{
					// assign time to prevent image caching
					$timestamp = time();
					
					//If the user has a profile image on file, display the user's profile image
					echo "<img src=\"photos/{$userid}.jpg?time={$timestamp}\" class=\"img-rounded profileimage\" />";
 					
				} else {
 				
					// If the user does not have a profile image on file, display a default profile image
					echo "<img src=\"photos/noimage.png\" class=\"img-rounded profileimage\" />";
 					
				}
				
				echo "</div>";
				echo "<div class=\"col-md-11\">";
				$count = count($row3);
				for($i = 0; $i < $count; $i++){
					echo $row3[$i][2];
					echo $row3[$i][2];
				}

				// check ownership
				if($userid == $_SESSION['user_id'])
				{	
					echo "<a href=\"activity.php?action=remove&id={$shoutid}\" class=\"pull-right btn btn-danger\"><i class=\"fa fa-times\"></i></i></a>";
				}
				// display name and shout
				echo "<p><strong>{$row2[0]['username']} writes:</strong></p>";
				echo "<p>$postedShout</p>";
				if($postedShout){
				echo "<span style=\"color: #666\">{$row3[0]['shout_date']}<span>";
				}
// 				echo $row['username'];
// 				echo $row['shoutdate'];
// 				var_dump($row2);
				echo "</div>";
				echo "</div>";
				echo "</div>";
			}
		?>

			
		</div>
	
	</body>
</html>