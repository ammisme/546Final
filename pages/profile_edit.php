<?php session_start(); 
if((isset($_SESSION['login']))&&($_SESSION['login']==true))
{
//print_r($_SESSION);
}
else{
header("Location: http://localhost/546Final/pages/index.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<title>Profile Edit</title>

<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/themes/smoothness/jquery-ui.css" rel="stylesheet">
<link href="../css/bootstrap.min.css" rel="stylesheet">
<link href="../css/profile_site.css" rel="stylesheet">

</head>
<?PHP  include '../include/page_title.php';?>


	<div class="container">

		<form role="form" action="../include/profileEdit.php" method="post" enctype="multipart/form-data">
			<fieldset>
				<legend>Personal Information</legend>
				<div class="form-group">
					<div class="row">
						<img src="http://umsbc.com/wp-content/uploads/2013/03/img_logo_blue.jpg" class="img-thumbnail" width="200" height="200">
					</div>
					<div class="row">
						<div class="col-md-4">
							<label for="Image">Edit Photo</label> <input type="file"
								name="pic" accept="image/*"> 
                            <button type="submit" class="btn btn-primary" id="upload">Upload</button>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-4">
							<label for="Name">Name:</label> <input type="text"
								class="form-control" id="Name" name="Name" value="<?PHP echo $_SESSION['Name'] ?>" readonly>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-4">
							<label for="Email">Email:</label> <input type="email"
								class="form-control" id="Email" name="Email" value="<?PHP echo $_SESSION['Email'] ?>" readonly >
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-4">
							<label for="Location">Location:</label> <input type="text"
								class="form-control" id="Location" name="Location" value="<?PHP echo $_SESSION['Location'] ?>">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-4">
							<label for="BOD">Date of Birth:</label> <input type="date"
								class="form-control" id="BOD" name="BOD" value="<?PHP 
								echo $_SESSION['BOD'] ?>">
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="row">
						<div class="col-md-4">
							<label for="habits">Habits:</label> <input type="text"
								class="form-control" id="Habit" name="Habit" value="<?PHP 
								echo $_SESSION['Habit'] ?>">
						</div>
					</div>
				</div>
				 <button type="submit" class="btn btn-primary" name="edit_submit" id="edit_submit">Submit</button>
			</fieldset>
		</form>

	</div>
	<!-- /container -->

	<!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
	<script
		src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script
		src="http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>
	<script src="../js/bootstrap.min.js"></script>
		<script src="../js/index.js"></script>
	<script src="../js/jquery.validate.min.js"></script>
</body>
</html>