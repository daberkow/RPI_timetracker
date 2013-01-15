<?PHP
	// Dan Berkowitz, berkod2@rpi.edu, dansberkowitz@gmail.com, January 2013
	include('./core.php');

			
	if(phpCAS::isAuthenticated())
	{
		$groupID = database_helper::db_return_row("SELECT `defaultgroup` FROM `users` WHERE `username`='" . phpCAS::getUser() . "' ;");
		if (intval($groupID[0][0]) > 0)
		{
			$group = database_helper::db_return_row("SELECT `name` FROM `groups` WHERE `id`='" . $groupID[0][0] . "';");
			header("Location: ./group.php?group=" . $group[0][0]);
		}else{
			$groups = database_helper::db_return_row("SELECT COUNT(`groupid`) AS RESULT FROM `groupusers` WHERE `userid`=(SELECT `id` FROM `users` WHERE `username`='" . phpCAS::getUser() . "') AND `privilege`>=1;");
			if (intval($groups[0][0]) == 1)
			{
				$theGroup = database_helper::db_return_row("SELECT `name` FROM `groups` WHERE `id`=(SELECT `groupid` AS RESULT FROM `groupusers` WHERE `userid`=(SELECT `id` FROM `users` WHERE `username`='" . phpCAS::getUser() . "'));");
				header("Location: ./group.php?group=" . $theGroup[0][0]);
			}
			$user = phpCAS::getUser();
		}
	}
	
?>

<html>
	<head>
		<title class = "title">Time Tracker</title>
		<link rel="stylesheet" type="text/css" href="./style.css"/>
		<link href="http://www.rpi.edu/favicon.ico" type="image/ico" rel="icon">
		<script src="./static/jquery.js"></script> <!--Only used for easy ajax requests-->
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
					if(phpCAS::isAuthenticated())
					{
						$page = database_helper::db_return_row("SELECT * FROM `pages` WHERE `page`='homeAuth'");
						echo urldecode($page[0]['data']);
					}else{
						$page = database_helper::db_return_row("SELECT * FROM `pages` WHERE `page`='home'");
						echo urldecode($page[0]['data']);
					}
					
					
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