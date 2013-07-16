<?PHP
    
    include("./core.php");
    if(!phpCAS::isAuthenticated())
    {
        header("Location: ./index.php");
    }else{
	$user	= phpCAS::getUser();
    }
    $privilege = intval(database_helper::db_user_privilege($user));
    if ($privilege >= 2)
    {
	$ownersID = database_helper::db_return_array("SELECT `id` FROM `users` WHERE `privilege`>=2");
	$ownerUsernames = database_helper::db_convert_returnarray_usernames($ownersID);
    }
?>

<html>
    <head>
        <title class = "title">Time Tracker Group Settings</title>
        <link rel="stylesheet" type="text/css" href="./style.css"/>
        <link href="http://www.rpi.edu/favicon.ico" type="image/ico" rel="icon">
        <script src="./static/jquery.js"></script> <!--Only used for easy ajax requests-->
	<script>
	    lastHit = 0;
	</script>
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
		    
                    echo "<h2 style='text-align: center'><a style='color: black; text-decoration: none;' href='./index.php'> Home Page Settings </a></h2>";
                    
                        //status 1, check privilage
		    switch($privilege)
		    {
			case 2:
			    echo "<table id='owners' style='width: 70%; min-width: 600px; margin: auto; text-align: center; border-width: 1px; border-style: solid;'>";
			    //Owners
			    echo "<tr><td style='min-width: 100px;'>Owner(s):</td><td style='min-width: 500px;'></td></tr>";
			    echo "<tr><td>Add Owner:</td><td><button onclick=\"findUser('ownerAdd', 'ownerPossible')\">Lookup</button><input id='ownerAdd' type='text' onkeydown=\"check_enter(event, 'ownerAdd', 2)\"/><button onclick=\"addtoOwners('ownerAdd', 2);\">Add</button><div id='ownerPossible'></div></td></tr>";
			    
			    foreach($ownerUsernames as $username)
			    {
				echo "<tr class='" . $username[0] . "2'><td></td><td>" . $username[1] . " " . $username[2] . " >  " . $username[0] . "<td><span id='remove' class='removeButton' onclick=\"removeAccount('" . $username[0] . "', 2)\">Remove</span></td></td></tr>";
			    }
			    echo "<tr><td>System Wide Name Search</td><td><button onclick='fetchNames();'>Search!</button>(Find first and last names for every user missing them)</td></tr>";
			    echo "</table>";
			    
			    echo "</table>";
			    
			    //This is where import and export goes
			    /*echo "<table style='width: 70%; min-width: 600px; margin: auto; text-align: center; border-width: 1px; border-style: solid;'>";
			    echo "<tr><td style='min-width: 100px;'>Import/Export System Data</td><td style='min-width: 500px;'></td></tr>";
			    echo "<tr><td>Check Database status</td><td id='dbCheck'><button onclick='scanDB()'>Scan</button></td></tr>";
			    echo "<tr><td></td><td>Note: If there are errors, the exporter will ignore that data while creating a backup</td></tr>";
			    echo "<tr><td></td><td><form action='export.php?import=true' method='post' enctype='multipart/form-data'><label for='file'>Filename:</label><input type='file' name='file' id='file'><br><input type='submit' name='submit' value='Submit'></form></td></tr>";
			    //export?skip=true exports data
			    echo "</table>";*/
			    
			    //not authed
			    echo "<table style='width: 70%; min-width: 600px; margin: auto; text-align: center; border-width: 1px; border-style: solid;'>\n";
			    $groupInfo = database_helper::db_return_row("SELECT `data` FROM `pages` WHERE `page`='home'");
			    echo "<tr><td style='width: 15%;'>Home Page (not logged in)(HTML):</td><td style='min-width: 500px; '><form name='input' action='./ajax.php' method='post'><input type='hidden' name='type' value='pageUpdate'><input type='hidden' name='group' value='home'><textarea name='newPage' style='width: 90%; min-height:300px;'>" . urldecode($groupInfo[0][0]) . "</textarea></tr>";
			    echo "<tr><td></td><td><button>Save Changes</button></td></form></tr></table>";
			    
			    //authed
			    echo "<table style='width: 70%; min-width: 600px; margin: auto; text-align: center; border-width: 1px; border-style: solid;'>\n";
			    $groupInfo = database_helper::db_return_row("SELECT `data` FROM `pages` WHERE `page`='homeAuth'");
			    echo "<tr><td style='width: 15%;'>Home Page (logged in, no default group)(HTML):</td><td style='min-width: 500px; '><form name='input' action='./ajax.php' method='post'><input type='hidden' name='type' value='pageUpdate'><input type='hidden' name='group' value='homeAuth'><textarea name='newPage' style='width: 90%; min-height:300px;'>" . urldecode($groupInfo[0][0]) . "</textarea></tr>";
			    echo "<tr><td></td><td><button>Save Changes</button></td></form></tr></table>";
			    break;
			default:
			    echo "<h3 style='text-align: center'> You do not have privilege for this page </h3>";
			    break;
                    }
                ?>
            </div>
            
	    <script>
		function scanDB() {
		    order = $.ajax({
			type: 'get',
			url: './export.php',
			success: function(data) {
			    $('#dbCheck').html("<a href='./export.php?error=true'>" + data + "</a>");
			},
			error: function(data) {
			    //error calling names
			}, 
		    });
		}
		
		function findUser( passedSearchBox, passedFillBox){//ownerAdd
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
			data: {type: "addgroupperm", group: "home", username: $('#' + passedBox).val(), priv: passedpiv},
			success: function(data) {
			    //console.log(data);
			    //Owners.push($('#ownerAdd').val());
			    
			    $("#" + accountType + "s").append("<tr class='" + $('#' + passedBox).val() + "2'><td></td><td>" + $('#' + passedBox).val() + "<td><span id='remove' class='removeButton' onclick=\"removeAccount('" + $('#' + passedBox).val() + "', 2)\">Remove</span></td></td></tr>");
			    //$("#" + accountType + "s").append("<p>" + $('#' + passedBox).val() + "</p>");
			    $('#' + accountType + 'Add').val("");
			    $('#' + accountType + 'Possible').html("");
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
				data: {type: "removeUser", group: "home", username: passedAccount, priv: passedPriv},
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
						    $("." + passedAccount + "2").remove();
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
				data: {type: "removeUser", group: "home", username: passedAccount, priv: passedPriv},
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
						    $("." + passedAccount + "2").remove();
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
		
		function fetchNames() {
		    order = $.ajax({
			type: 'POST',
			url: './ajax.php',
			data: {type: "nameLookup"},
			success: function(data) {
			    alert('Name Search Complete');
			},
			error: function(data) {
			    //console.log(data);
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