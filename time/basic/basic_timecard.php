<?PHP
    // Dan Berkowitz, berkod2@rpi.edu, dansberkowitz@gmail.com, January 2013
    /*
     * This was a experiment to make a basic version that did not need javascript. It got partialy done 
     * but other issues became more important.
     *
     *
     */
    include('./core.php');
	
    if(!phpCAS::isAuthenticated())
    {
        header("Location: ./index.php");
    }/**else{
	$user	= phpCAS::getUser();
	
	if ($privilege <= 0)
	{
	    header("Location: ./index.php");
	}
    }**/
    //PHP still does redirection
?>

<html>
    <head>
	<title class = "title">Time Tracker Time Card</title>
	<link rel="stylesheet" type="text/css" href="./style.css"/>
	<link href="http://www.rpi.edu/favicon.ico" type="image/ico" rel="icon">
    </head>
    <body>
	<!-- Most of the page is the same as the more advanced on -->
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
		    //PHP on the server end can still be trusted so having php draw out things is still allowed in basic
			$privilege = intval(database_helper::db_group_privilege(urlencode($_REQUEST['group']), time_auth::getUser()));
			//echo '<h3> ' . $privilege . time_auth::getUser() .  '</h3>';
			switch($privilege)
			    {
				case 0:
				    echo "<h3 style='text-align: center'> You are not a member of existing group " . urlencode($_REQUEST['group']) . ", please contact administrator of group to be added</h3>";  
				    break;
				case 2:
				    break;
				case 1:
				case 3:
				    //timescard stuff here
                                    timetracker::draw_week();
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