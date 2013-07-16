<?PHP
    //Settings page for groups
    include("./core.php");
    if(!phpCAS::isAuthenticated())
    {
        header("Location: ./index.php");
    }else{
	$user	= phpCAS::getUser();
    }
    
    if(isset($_REQUEST['group']))
    {
            
        if(database_helper::db_scan_for_name(urlencode($_REQUEST['group'])))
        {
            //it exists check for privilage
            $privilege = intval(database_helper::db_group_privilege(urlencode($_REQUEST['group']), $user));
            if ($privilege >= 0)
            {
                $status = 1;
            }
        }else{
            //does not exist
            if (isset($_REQUEST['bypass']))
            {
                $groupID = database_helper::db_insert_query("INSERT INTO `groups`(name) VALUES ('" . urlencode($_REQUEST['group']) . "');");
                if ($groupID > 0)
                {
                    //group made
                    $pageID = database_helper::db_insert_query("INSERT INTO `pages`(page) VALUES ('group');");
                    database_helper::db_insert_query("INSERT INTO `groups`(`page`) VALUES ('" . $pageID . "') WHERE `name`='" . urlencode($_REQUEST['group']) . "';");
                    database_helper::db_insert_query("INSERT INTO `groupusers`(`userid`,`groupid`,`privilege`) VALUES ((SELECT id FROM `users` WHERE `username`='" . $user . "' LIMIT 0,1),'" . $groupID . "', '2');");
                    $privilege = 2;
                    $status = 1;
                }else{
                    echo "Group ERROR";
                    $status = 0;
                }
            }else{
                $status = 0; // doesnt exist can be created
            }
        }
	
	if ($privilege >= 2)
	{
	    $ownersID = database_helper::db_return_array("SELECT userid FROM `groupusers` WHERE `groupid`=(SELECT id FROM `groups` WHERE `name`='" . urlencode($_REQUEST['group']) . "' AND `privilege`>=2 LIMIT 0,1)");
	    $usersID = database_helper::db_return_array("SELECT userid FROM `groupusers` WHERE `groupid`=(SELECT id FROM `groups` WHERE `name`='" . urlencode($_REQUEST['group']) . "' AND `privilege`!=2 AND `privilege`!=0 LIMIT 0,1)");
	    $ownerUsernames = database_helper::db_convert_returnarray_usernames($ownersID);
	    $usersUsernames = database_helper::db_convert_returnarray_usernames($usersID);
	}
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <title class = "title">Time Tracker Group Settings</title>
	<meta http-equiv="X-UA-Compatible" content="IE=9; IE=8; IE=7; IE=EDGE" />
        <link rel="stylesheet" type="text/css" href="./style.css"/>
        <link href="http://www.rpi.edu/favicon.ico" type="image/ico" rel="icon">
        <script src="./static/jquery.js"></script> <!--Only used for easy ajax requests-->
	<script>
	    Group = "<?PHP echo urlencode($_REQUEST['group']); ?>";
	    lastHit = 0;
	</script>
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
                    echo "<h2 style='text-align: center'><a style='color: black; text-decoration: none;' href='./group.php?group=" . urlencode($_REQUEST['group']) . "'> Group: " . urlencode($_REQUEST['group']) . " </a></h2>";
                    if($status == 0)
                    {//no group found
                        echo "<h3 style='text-align: center'> Group Not Found, Please Create The Group... </h3>";   
                    }else{
                        //status 1, check privilage
                        switch($privilege)
                        {
                            case 0:
                                echo "<h3 style='text-align: center'> You are a member of the group, but not currently active </h3>";  
                                break;
                            case 1:
                                echo "<h3 style='text-align: center'> You are a member of the group, but not a administrator </h3>";  
                                break;
                            case 2:
			    case 3:
				echo "<table id='owners' style='width: 40%; min-width: 600px; margin: auto; text-align: center;'>";
                                //Owners
				echo "<tr><td style='min-width: 100px;'>Owner(s):</td><td style='min-width: 500px;'></td></tr>";
				echo "<tr><td>Add Owner:</td><td><button onclick=\"findUser('ownerAdd', 'ownerPossible')\">Lookup</button><input id='ownerAdd' type='text' onkeydown=\"check_enter(event, 'ownerAdd', 2)\"/><button onclick=\"addtoOwners('ownerAdd', 2);\">Add</button><div id='ownerPossible'></div></td></tr>";
                                
				foreach($ownerUsernames as $username)
				{
				    echo "<tr class='" . $username[0] . "2'><td></td><td>" . $username[1] . " " . $username[2] . " >  " . $username[0] . "<td><span id='remove' class='removeButton' onclick=\"removeAccount('" . $username[0] . "', 2)\">Remove</span></td></td></tr>";
				}
				echo "</table><hr style='width:60%; margin: auto;'>";
				echo "<table id='users' style='width: 40%; min-width: 600px; margin: auto; text-align: center;'>\n";
                                
				//Users
				echo "<tr><td style='min-width: 100px;'>User(s):</td><td style='min-width: 500px;'></tr>";
				echo "<tr><td>Add User:</td><td><button onclick=\"findUser('userAdd', 'userPossible')\">Lookup</button><input id='userAdd' type='text' onkeydown=\"check_enter(event, 'userAdd', 1)\"/><button onclick=\"addtoOwners('userAdd', 1);\">Add</button><div id='userPossible'></div></td></tr>";
                                
				foreach($usersUsernames as $username)
				{
				    echo "<tr class='" . $username[0] . "1'><td></td><td>" . $username[1] . " " . $username[2] . " >  " . $username[0] . "<td><span id='remove' class='removeButton' onclick=\"removeAccount('" . $username[0] . "', 1)\">Remove</span></td></td></tr>";
				}
				echo "</table><hr style='width:60%; margin: auto;'>";

				/*echo "<div style='width: 40%; min-width: 600px; margin:auto;'><span style='width: 240px; display: inline-block;'>Group Email Notifications: </span><span text-align: center;'><select id='emailNotAll'>";
				
				if (timetracker::groupEmailSetting(urlencode($_REQUEST['group']), 1))
				{
				    echo "<option selected>Enabled</option><option>Disabled</option></select>";
				}else{
				    echo "<option>Enabled</option><option selected>Disabled</option></select>";
				}
				
				echo "<button onclick='updateGroupEmail();'>Submit</button><span id='allEmailUpdate'></span></span>";
				echo "<div><span style='width: 240px;  display: inline-block;'>Allow users to disable notifications:</span><span ><select id='emailNotAllow'>";
				if (timetracker::groupEmailSetting(urlencode($_REQUEST['group']), 2))
				{
				    echo "<option selected>Enabled</option><option>Disabled</option></select>";
				}else{
				    echo "<option>Enabled</option><option selected>Disabled</option></select>";
				}
				echo "<button onclick='updateuserEmail()'>Submit</button><span id='allEmailAllowUpdate'></span></span></div>";*/
				echo "<tr><td>System Wide Name Search</td><td><button onclick='fetchNames();'>Search!</button>(Find first and last names for every user missing them)</td></tr>";
			    
				echo "</div><hr style='width:60%; margin: auto;'>";

				echo "<table style='width: 60%; min-width: 600px; margin: auto; text-align: center;'>\n";
				//Users
				$groupInfo = database_helper::db_return_row("SELECT `data` FROM `pages` WHERE `id`=(SELECT `page` FROM `groups` WHERE `name`='" . urlencode($_REQUEST['group']) . "')");
				echo "<tr><td style='width: 15%;'>Group Page(HTML):</td><td style='min-width: 500px; '><form name='input' action='./ajax.php' method='post'><input type='hidden' name='type' value='pageUpdate'><input type='hidden' name='group' value='" . urlencode($_REQUEST['group']) . "'><textarea name='newPage' style='width: 90%; min-height:300px;'>" . urldecode($groupInfo[0][0]) . "</textarea></tr>";
				echo "<tr><td></td><td><button>Save Changes</button></td></form></tr></table>";
				break;
                        }
                    }
                ?>
            </div>
            
	    <script>
		function findUser( passedSearchBox, passedFillBox)
		{//ownerAdd
		    var newHit = new Date().getTime();
		    if ((newHit - lastHit) < 500)
		    {
			order.abort();
		    }
		    lastHit = new Date().getTime();
		    $('#'+passedFillBox).html("<img src='./images/8-1.gif'/>");
		    order = $.ajax({
			type: 'get',
			url: './search.php',
			data: {q: $('#'+passedSearchBox).val()},
			success: function(data) {
				JSONData = JSON.parse(data);
				$('#'+passedFillBox).html("");
				selected = 0;
				for(i = 0; (i < JSONData["data"].length) && (i != 3); i++)
				{
				    $('#'+passedFillBox).append("<div class='Name" + i + "' onmouseover='changeColor(" + i + ")' onclick='selectName(" + i + ", \"" + passedSearchBox + "\")'>" + JSONData["data"][i].name + " (" + JSONData["data"][i].rcsid  + ")</div>");
				}
			       
			},
			error: function(data) {
			    //error calling names
			}, 
		    });
		}
	    
		function check_enter(e, passedBox, passedpriv)
		{
		    x = e.keyCode;
		    if (x == 13)
		    {
			addtoOwners(passedBox, passedpriv);
		    }
		}
		
		function changeColor(passedarrayPosition)
		{
		    if (JSONData["data"].length > 3)
		    {
			jDataLength = 2;
		    }else{
			jDataLength = JSONData["data"].length;
		    }
		    for(j = 0; j <= jDataLength; j++)
		    {
			if (passedarrayPosition == j)
			{
			    $(".Name" + j).css("background", "lightblue");
			    selected = j;
			}else{
			    $(".Name" + j).css("background", "white");
			    selected = j;
			}
		    }
		    
		}
		
		function selectName(passedvalue, passedsearchbox)//add prvilege to this from box
		{
		    switch (passedsearchbox)
		    {
			case "userAdd":
			    $('#userAdd').val(JSONData["data"][passedvalue].rcsid);
			    addtoOwners('userAdd', 1);
			    break;
			case "ownerAdd":
			    $('#ownerAdd').val(JSONData["data"][passedvalue].rcsid);
			    addtoOwners('ownerAdd', 2);
			    break;
		    }
		    
		}
		
		function addtoOwners(passedBox, passedpiv)
		{
		    switch (passedBox)
		    {
			case "userAdd":
			    accountType = "user";
			    break;
			case "ownerAdd":
			    accountType = "owner";
			    break;
		    }
		    order = $.ajax({
			type: 'POST',
			url: './ajax.php',
			data: {type: "addgroupperm", group: "<?PHP echo urlencode($_REQUEST['group']); ?>", username: $('#' + passedBox).val(), priv: passedpiv},
			success: function(data) {
			    //console.log(data);
			    //Owners.push($('#ownerAdd').val());
			    
			    $("#" + accountType + "s").append("<tr class='" + $('#' + passedBox).val() + "2'><td></td><td>Fetching  >  " + $('#' + passedBox).val() + "<td><span id='remove' class='removeButton' onclick=\"removeAccount('" + $('#' + passedBox).val() + "', 2)\">Remove</span></td></td></tr>");
			    //$("#" + accountType + "s").append("<p>" + $('#' + passedBox).val() + "</p>");
			    $('#' + accountType + 'Add').val("");
			    $('#' + accountType + 'Possible').html("");
			    fetchNames();
			},
			error: function(data) {
			    //console.log(data);
			}, 
		    });
		}
		
		function fetchNames() {
		    order = $.ajax({
			type: 'POST',
			url: './ajax.php',
			data: {type: "nameLookup"},
			success: function(data) {
			},
			error: function(data) {
			    //console.log(data);
			}, 
		    });
		}
		
		function removeAccount(passedAccount, passedPriv)
		{
		    if (passedAccount == "<?PHP echo $user; ?>")
		    {
			if (confirm("Are you sure you want to remove privilege from yourself?"))
			{
			    CodeRun = $.ajax({
				type: 'POST',
				url: './ajax.php',
				data: {type: "removeUser", group: "<?PHP echo urlencode($_REQUEST['group']); ?>", username: passedAccount, priv: passedPriv},
				success: function(data) {
				    switch (data)
				    {
					//this is switching the new privilege
					case "0":
					    $("." + passedAccount + passedPriv).remove();
					    break;
					case "1":
					    switch(passedPriv)
					    {
						case 1:
						    //this means it failed to do it, if my current privilege is 1 and I tried to remvoe it htere was a problem
						    break;
						case 2:
						    //user to be a three and is now a 1
						    $("." + passedAccount + "2").remove();
						    break;
					    }
					    break;
					case "2":
					    switch(passedPriv)
					    {
						case 1:
						    //was a 3, now is a 2 due to view removed
						    $("." + passedAccount + "1").remove();
						    break;
						case 2:
						    //shouldnt happen
						    break;
					    }
					    break;
					case "3":
					    //this shouldnt happen if we just removed privilege for anything
					    break;
				    }
				    //console.log(data);
				},
				error: function(data) {
				    //error calling names
				    //bug no error return
				}, 
			    });
			}
		    }else{
			CodeRun = $.ajax({
			    type: 'POST',
			    url: './ajax.php',
			    data: {type: "removeUser", group: "<?PHP echo urlencode($_REQUEST['group']); ?>", username: passedAccount, priv: passedPriv},
			    success: function(data) {
				switch (data)
				{
				    //this is switching the new privilege
				    case "0":
					$("." + passedAccount + passedPriv).remove();
					break;
				    case "1":
					switch(passedPriv)
					{
					    case 1:
						//this means it failed to do it, if my current privilege is 1 and I tried to remvoe it htere was a problem
						break;
					    case 2:
						//user to be a three and is now a 1
						$("." + passedAccount + "2").remove();
						break;
					}
					break;
				    case "2":
					switch(passedPriv)
					{
					    case 1:
						//was a 3, now is a 2 due to view removed
						$("." + passedAccount + "1").remove();
						break;
					    case 2:
						//shouldnt happen
						break;
					}
					break;
				    case "3":
					//this shouldnt happen if we just removed privilege for anything
					break;
				}
				//console.log(data);
			    },
			    error: function(data) {
				//error calling names
				//bug no error return
			    }, 
			});
		    }
		}
		
		function updateGroupEmail() {
		    $('#allEmailUpdate').html("Updating");
		    var settingToSet = 0;
		    if ($('#emailNotAll').val() == "Enabled") {
			settingToSet = 1;
		    }
		
		    order = $.ajax({
			type: 'POST',
			url: './ajax.php',
			data: {type: "updateEmailGroup", group: "<?PHP echo urlencode($_REQUEST['group']); ?>", setting: settingToSet, EmailSettingtype: 1},
			success: function(data) {
			    //console.log(data);
			    $('#allEmailUpdate').html("Updated!");
			},
			error: function(data) {
			    $('#allEmailUpdate').html("Not Updated!");
			}, 
		    });
		}
		function updateuserEmail() {
		    $('#allEmailAllowUpdate').html("Updating");
		    var settingToSet = 0;
		    if ($('#emailNotAllow').val() == "Enabled") {
			settingToSet = 1;
		    }
		
		    order = $.ajax({
			type: 'POST',
			url: './ajax.php',
			data: {type: "updateEmailGroup", group: "<?PHP echo urlencode($_REQUEST['group']); ?>", setting: settingToSet, EmailSettingtype: 2},
			success: function(data) {
			    //console.log(data);
			    $('#allEmailAllowUpdate').html("Updated!");
			},
			error: function(data) {
			    $('#allEmailAllowUpdate').html("Not Updated!");
			}, 
		    });
		}
	    </script>
	    
            <!-- NEW SECTION! -->
            <hr>
            <div id="footer">
                <?PHP include("./footer.php"); ?>
            </div>
        </div>
    </body>
</html>