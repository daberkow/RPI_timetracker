<?PHP
    // Dan Berkowitz, berkod2@rpi.edu, dansberkowitz@gmail.com, January 2013

    include('./core.php');
	
    if(!phpCAS::isAuthenticated())
    {
        header("Location: ./index.php");
    }
?>
<!DOCTYPE html>
<html>
    <head>
	<title class = "title">Time Tracker Group</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
	<link rel="stylesheet" type="text/css" href="./style.css"/>
	<link href="http://www.rpi.edu/favicon.ico" type="image/ico" rel="icon">
	<script src="./static/jquery.js"></script> <!--Only used for easy ajax requests-->
    </head>
    <body>
	<div id="main">
	    <div id="title">
		<a href="./index.php"><div class="logo"></div><div id="logo">Time Tracker</div></a>
		<div id="result"></div>
	    </div>
	    <div class="red_bar"></div>
	    <div class="gray_bar"></div>
	    <div id="working_area">
		    <?PHP
			$privilege = intval(database_helper::db_group_privilege(urlencode($_REQUEST['group']), phpCAS::getUser()));
			switch($privilege)
			    {
				case 0:
				    echo "<h3 style='text-align: center'> You are not a member of existing group " . urlencode($_REQUEST['group']) . ", please contact administrator of group to be added</h3>";  
				    break;
				case 1:
				    $groupInfo = database_helper::db_return_row("SELECT * FROM `groups` WHERE `name`='" . urlencode($_REQUEST['group']) . "';");
				    echo "<h2 style='text-align:center;'>" . $groupInfo[0][1] . " </h2>";
				    echo "<h3 style='text-align:center;'><a href='./timecard.php?group=" . $groupInfo[0][1] . "'>Timecard</a></h3>";
				    $page = database_helper::db_return_row("SELECT * FROM `pages` WHERE `id`='" . $groupInfo[0][2] . "';");
				    echo urldecode($page[0]['data']);
				    break;
				case 2:
				case 3:
				    $groupInfo = database_helper::db_return_row("SELECT * FROM `groups` WHERE `name`='" . urlencode($_REQUEST['group']) . "';");
				    echo "<h2 style='text-align:center;'>" . $groupInfo[0][1] . "-<a href='./group_settings.php?group=" . $groupInfo[0][1] . "'>Edit Group</a>-<a href='./report.php?group=" . $groupInfo[0][1] . "'>Report</a></h2>";
				    echo "<h3 style='text-align:center;'><a href='./timecard.php?group=" . $groupInfo[0][1] . "'>Timecard</a></h3>";
				    $page = database_helper::db_return_row("SELECT * FROM `pages` WHERE `id`='" . $groupInfo[0][2] . "';");
				    echo urldecode($page[0]['data']);
				    break;
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