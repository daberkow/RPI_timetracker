<?PHP
    // Dan Berkowitz, berkod2@rpi.edu, dansberkowitz@gmail.com, November 2012
    
    include('./core.php');
	
    if(!time_auth::is_authenticated())
    {
        header("Location: ./index.php");
    }/**else{
	$user	= phpCAS::getUser();
	
	if ($privilege <= 0)
	{
	    header("Location: ./index.php");
	}
    }**/
?>

<html>
    <head>
	<title class = "title">Time Tracker Group</title>
	<link rel="stylesheet" type="text/css" href="./style.css"/>
	<link href="http://www.rpi.edu/favicon.ico" type="image/ico" rel="icon">
	<script src="./jquery-1.6.2.min.js"></script> <!--Only used for easy ajax requests-->
	<script src="./core.js"></script>
    </head>
    <body>
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
			$privilege = intval(database_helper::db_group_privilege(urlencode($_REQUEST['group']), time_auth::getUser()));
			switch($privilege)
			    {
				case 0:
				    echo "<h3 style='text-align: center'> You are not a member of existing group " . urlencode($_REQUEST['group']) . ", please contact administrator of group to be added</h3>";  
				    break;
				case 2:
				    break;
				case 1:
				case 3:
				    $groupInfo = database_helper::db_return_row("SELECT * FROM `groups` WHERE `name`='" . urlencode($_REQUEST['group']) . "';");
			
				    if($privilege >= 2)
				    {
					    echo "<h2 style='text-align:center;'>" . $groupInfo[0][1] . " <a href='./group_settings.php?group=" . $groupInfo[0][1] . "'>Edit Group</a></h2>";
				    }else{
					    echo "<h2 style='text-align:center;'>" . $groupInfo[0][1] . " </h2>";
				    }
				    
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