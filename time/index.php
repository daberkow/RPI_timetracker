<?PHP
	// Dan Berkowitz, berkod2@rpi.edu, dansberkowitz@gmail.com, November 2012
	
	include('./core.php');

	if(time_auth::is_authenticated())
	{
		$groupID = database_helper::db_return_row("SELECT `defaultgroup` FROM `users` WHERE `username`='" . time_auth::getUser() . "' ;");
		if (intval($groupID[0][0]) > 0)
		{
			$group = database_helper::db_return_row("SELECT `name` FROM `groups` WHERE `id`='" . $groupID[0][0] . "';");
			header("Location: ./group.php?group=" . $group[0][0]);
		}else{
			$user = time_auth::getUser();
		}
	}
	
?>

<html>
	<head>
		<title class = "title">Time Tracker</title>
		<link rel="stylesheet" type="text/css" href="./style.css"/>
		<link href="http://www.rpi.edu/favicon.ico" type="image/ico" rel="icon">
		<script src="./jquery-1.6.2.min.js"></script> <!--Only used for easy ajax requests-->
		<script src="./core.js"></script>
	</head>
	<body>
		<!-- DIVS! -->
		<div id="main">
			<div id="title">
				<div class="logo"></div>
				<a href="./index.php"><div id="logo">Time Tracker</div></a>
				<div id="result"></div>
			</div>
			<div class="red_bar"></div>
			<div class="gray_bar"></div>
			<div id="working_area">
				<?PHP
					$page = database_helper::db_return_row("SELECT * FROM `pages` WHERE `page`='home'");
					
					echo $page[0]['data'];
				?>
			</div>
			
			<!-- NEW SECTION! -->
			<hr>
			<div id="footer">
				<?PHP include("./footer.php"); ?>
			</div>
		</div>
	</body>
</html>