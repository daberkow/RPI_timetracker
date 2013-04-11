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
	<link rel="stylesheet" type="text/css" media="print" href="./print.css" />
	<link href="http://www.rpi.edu/favicon.ico" type="image/ico" rel="icon"/>
	<style>
	    .colored{
		background:#CCCCCC;
	    }
	    
	    .cell {
		border-width: 0px;
		border-left-width:1px;
		border-bottom-width:1px;
		border-style:solid;
	    }
	</style>
	<script src="./static/jquery.js"></script> <!--Only used for easy ajax requests-->
	<script src="./report.js"></script>
    </head>
    <body onload='loader()'>
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
						echo "<h3 style='text-align: center'> You are a member, but not a administrator, so you can not see reports of group " . urlencode($_REQUEST['group']) . "</h3>";  
						break;
					case 2:
					case 3:
						$groupInfo = database_helper::db_return_row("SELECT * FROM `groups` WHERE `name`='" . urlencode($_REQUEST['group']) . "';");
						$start_time = timetracker::get_First_day(time());
						echo "<h2 style='text-align:center;'>" . $groupInfo[0][1] . " </h2>";
						echo "<script> start_time = '" . $start_time . "'; savedData = new Array(); groupName = '" . urlencode($_REQUEST['group']) . "'</script>";
						echo "<table style='margin:auto; width: 90%; border-width:1px; border-style:solid;'>
							<tr>
								<td style='width: 10%;'><button onclick='lastweek()'><-- Last week</button></td>
								<td style='text-align: center;' id='weekdescription'></td>
								<td style='width: 10%; text-align: right;'><button onclick='nextweek()'>Next week --></button></td>
							</tr>
							</table>
							<div id='tableArea' style='margin:auto; width: 90%; border-width:1px; border-style:solid;'>
							</div>
							<div style='width: 200px; margin: auto;'><button id='locker' style='width: 200px;' onclick='lock()'>Lock Time Cards</button></div>";
							
						break;
				}
		    ?>
	    </div>
	    
	    <!-- NEW SECTION! -->
	    <hr class='splitter'>
	    <div id="footer">
		    <?PHP include("./footer.php"); ?>
	    </div>
	</div>
    </body>
</html>