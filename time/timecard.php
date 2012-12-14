<?PHP
    // Dan Berkowitz, berkod2@rpi.edu, dansberkowitz@gmail.com, November 2012
    //not converted to time_auth
    include('./core.php');

    include_once('./cas/CAS.php');
    
    phpCAS::client(CAS_VERSION_2_0,'cas-auth.rpi.edu',443,'/cas/');
    
    // SSL!
    phpCAS::setCasServerCACert("./cas-auth.rpi.edu");
	
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
?>

<html>
    <head>
        <noscript><meta http-equiv="refresh" content="1;url=basic_timecard.php?group=<?PHP echo urlencode($_REQUEST['group']); ?>"></noscript>
	<title class = "title">Time Tracker Time Card</title>
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
			$privilege = intval(database_helper::db_group_privilege(urlencode($_REQUEST['group']), phpCAS::getUser()));
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
                                    timetracker::draw_week_js();
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