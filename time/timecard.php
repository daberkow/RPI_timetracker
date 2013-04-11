<?PHP
    // Dan Berkowitz, berkod2@rpi.edu, dansberkowitz@gmail.com, January 2013
    //not converted to time_auth
    include('./core.php');

    if(!phpCAS::isAuthenticated())
    {
	header("Location: ./index.php");
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <noscript><!--<meta http-equiv="refresh" content="1;url=./basic/basic_timecard.php?group=<?PHP echo urlencode($_REQUEST['group']); ?>">--></noscript>
	<title class = "title">Time Tracker Time Card</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
	<link rel="stylesheet" type="text/css" href="./style.css"/>
	<link href="http://www.rpi.edu/favicon.ico" type="image/ico" rel="icon">
	<style>
	    .myButton_right{
	    }
	    
	    .myButton_left{
		border-right-width: 0px;
	    }
	    
	    .mybutton{
		font-size: 10px;
		width: 50px;
		height: 90%;
		border-width: 1px;
		border-style: solid;
		display: inline-block;
	    }
	    .buttonLine{
		height: 20px;
	    }
	    .timecardDay{
		text-align: center;
		width: 14%;
		min-width: 150px;
		height: 380px;
		border-width: 1px;
		border-color: black;
		border-style: solid;
		display: inline-block;
	    }

	</style>
	<script src="./static/jquery.js"></script> <!--Only used for easy ajax requests-->
	<script type="application/x-javascript">
	    pagegroup = '<?PHP echo urlencode($_REQUEST['group']); ?>';
	    pageuser = '<?PHP echo phpCAS::getUser(); ?>';
	    pageoverride = false;
	    locked = -1;
	</script>
	<script src="./timecard.js"></script>
    </head>
    <body onload='loadPage(0);'>
	<div id="main">
	    <div id="title">
		<a href="./index.php"><div class="logo"></div><div id="logo">Time Tracker</div></a>
		<div id="result"></div>
	    </div>
	    <div class="red_bar"></div>
	    <div class="gray_bar"></div>
	    <div id="working_area"  style='min-width: 915px;'>
		<?PHP
		    $privilege = intval(database_helper::db_group_privilege(urlencode($_REQUEST['group']), phpCAS::getUser()));
		    switch($privilege)
		    {
			case 0:
			    echo "<h3 style='text-align: center'> You are not a member of existing group " . urlencode($_REQUEST['group']) . ", please contact administrator of group to be added</h3>";  
			    break;
			case 2:
			    echo "<h3 style='text-align: center'> You are a administrator of the group, but not a user</h3>";  
			case 1:
			case 3:
			    //timescard stuff here
			    If (isset($_REQUEST['date']))
			    {
				$start_time = timetracker::get_First_day(strtotime($_REQUEST['date']));
			    }else{
				$start_time = timetracker::get_First_day(time());
			    }
			    
			    echo "<script> start_time = '" . $start_time . "'; savedData = new Array(); </script>";
			    echo "<div style='margin: auto; min-width: 915px;'>";
			    echo "<div style='width: 100%; height: 35px; margin: auto; min-width: 915px;'><div style='width: 40%; min-width: 320px; display: inline-block;'><button style='width: 40px;' onclick='lastweek();'><--</button> Saved Templates: <select id='templates' style='width: 18%' onclick='loadTemplate()'><option value=0>------</option>";
			    $templates = database_helper::db_return_array("SELECT * FROM `templates` WHERE `owner`=(SELECT `id` FROM `users` WHERE `username`='" . phpCAS::getUser() . "') AND `status`=1");
			    foreach($templates as $template)
			    {
				echo "<option value=" . $template['id'] . ">" .  $template['name'] . "</option>";
			    }
			    echo "</select>";
			    if ($privilege >= 2)
			    {
				echo "Save as User: <select id='SaveAsUser' style='width: 18%;' onclick='selectUser()'><option value=0>------</option>";
				$Users = database_helper::db_return_array("SELECT users.id, users.username FROM `users` LEFT JOIN `groupusers` ON users.id=groupusers.userid WHERE groupusers.`groupid`=(SELECT `id` FROM `groups` WHERE `name`='" . urlencode($_REQUEST['group']) . "' ) AND (groupusers.`privilege`=1 OR groupusers.`privilege`=3)");
				foreach($Users as $user)
				{
				    echo "<option value=" . $user['id'] . ">" .  $user['username'] . "</option>";
				}
				echo "</select>";
			    }
			    echo "</div><div id='pageStatus' style='display: inline-block; min-width: 185px; width: 19%; text-align: center;'>Synced</div>
				    <div style='width: 40%; min-width: 380px; display: inline-block; text-align: right;'>Save Template: <input id='templateName' type='text'/><button onclick='saveTemplate()'>Save</button></span><button style='width: 40px;' onclick='nextweek();'>--></button></div></div>";
			    echo "<input type='hidden' name='date' value='" . $start_time ."'>";
			    echo "<DIV id='holder' style='margin: auto; width: 91%;'>";
			    for($i = 1; $i < 15; $i++)
			    {
				echo "<div class='timecardDay' id='day" . $i . "'>
					<h4 class='day" . $i . "Name' style='margin-bottom: 0px; margin-top: 5px;'>" . date('m/d/Y', ($start_time + (60*60*24*($i - 1)))) . "</h4>
					<h5 style='margin-bottom: 0px; margin-top: 0px;'>" . date("l", ($start_time + (60*60*24*($i - 1)))) . "</h5>
					<p style='font-size: 12px; display: inline;'>Hours: </p><input type='text' id='dayTotal" . $i . "' name='day' style='width: 30%; min-width: 60px; height: 14px; font-size: 12px;' disabled='disabled'></input>";
				for ($k = 8; $k < 23; $k++)
				{
				    if ($k < 12)
				    {
					switch ($k)
					{
					    case 0:
						echo "<div class='buttonLine'>";
						echo "<span class='myButton_left mybutton' onclick=\"clockPunch('" . $i . "','" . $k . "','00', '30');\" id='hour" . $i . "_" . $k . "_0'>12:00AM</span>";
						echo "<span class='myButton_right mybutton' onclick=\"clockPunch('" . $i . "','" . $k . "','30', '30');\" id='hour" . $i . "_" . $k . "_2'>12:30AM</span>";
						echo "</div>\n";
						break;
					    default:
						echo "<div class='buttonLine'>";
						echo "<span class='myButton_left mybutton' onclick=\"clockPunch('" . $i . "','" . $k . "','00', '30');\" id='hour" . $i . "_" . $k . "_0'>$k:00AM</span>";
						echo "<span class='myButton_right mybutton' onclick=\"clockPunch('" . $i . "','" . $k . "','30', '30');\" id='hour" . $i . "_" . $k . "_2'>$k:30AM</span>";
						echo "</div>\n";
						break;
					}
				    }else{
					switch ($k)
					{
					    case 12:
						echo "<div class='buttonLine'>";
						echo "<span class='myButton_left mybutton' onclick=\"clockPunch('" . $i . "','" . $k . "','00', '30');\" id='hour" . $i . "_" . $k . "_0'>12:00PM</span>";
						echo "<span class='myButton_right mybutton' onclick=\"clockPunch('" . $i . "','" . $k . "','30', '30');\" id='hour" . $i . "_" . $k . "_2'>12:30PM</span>";
						echo "</div>\n";
						break;
					    default:
						echo "<div class='buttonLine'>";
						echo "<span class='myButton_left mybutton' onclick=\"clockPunch('" . $i . "','" . $k . "','00', '30');\" id='hour" . $i . "_" . $k . "_0'>" . ($k - 12) . ":00PM</span>";
						echo "<span class='myButton_right mybutton' onclick=\"clockPunch('" . $i . "','" . $k . "','30', '30');\" id='hour" . $i . "_" . $k . "_2'>" . ($k - 12) . ":30PM</span>";
						echo "</div>\n";
						break;
					}
					    
				    }
				}
				echo "</div>";
			    }
			    echo "</DIV>";
			    echo "</div></form>";
			    if (timetracker::groupEmailSetting(urlencode($_REQUEST['group']), 2))
			    {
				echo "<div style='margin:auto; text-align:center;'>Email Notifications: <select id='emailNot'>";
				if (timetracker::userEmailSetting(urlencode($_REQUEST['group']), 1))
				{
				    echo "<option selected>Enabled</option><option>Disabled</option></select>";
				}else{
				    echo "<option>Enabled</option><option selected>Disabled</option></select>";
				}
				
				echo "<button onclick='updateuserEmail()'>Submit</button><span id='UpdatedEmail'></span></div>";
			    }
			    break;
		    }
		?>
	    </div>
	    
	    <!-- NEW SECTION! -->
	    <hr>
	    <div id="footer">
		    <?PHP include("./footer.php"); ?>
	    </div>
	    
	    <script>
		function updateuserEmail() {
		    $('#UpdatedEmail').html("Updating");
		    var settingToSet = 0;
		    if ($('#emailNot').val() == "Enabled") {
			settingToSet = 1;
		    }
		
		    order = $.ajax({
			type: 'POST',
			url: './ajax.php',
			data: {type: "updateUserEmail", group: "<?PHP echo urlencode($_REQUEST['group']); ?>", setting: settingToSet},
			success: function(data) {
			    //console.log(data);
			    $('#UpdatedEmail').html("Updated!");
			},
			error: function(data) {
			    $('#UpdatedEmail').html("Not Updated!");
			}, 
		    });
		}
	    </script>
	</div>
    </body>
</html>