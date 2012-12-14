<div id="Stats">					
    <?PHP
    
    	if (time_auth::is_authenticated())
	{
		$authed = true;
		$user	= time_auth::getUser();
	}else{
		$authed = false;
	}
        
        if ($authed)
        {//Authenacted users get logout
            echo "<a href='./logout.php' class='labels'>Logout " . $user . "</a>";
        }else
        {
            echo "<a href='./login.php' class='labels'>Login</a>";
        }
    ?>	
</div>
<div id="version">v<?PHP echo timetracker::get_version(); ?><a href="https://github.com/daberkow/RPI_timetracker">Source</a></a></div> <!-- YAY -->
<div id="options">
    <?PHP
        if ($authed)
        {
            echo "Groups:<select id='groupSelector' style='width: 150px;' onchange='gotoGroup()'><option value='-'>---------</option>";
            $getGroups = database_helper::db_get_groups(time_auth::getUser());
            foreach ($getGroups as $group)
            {
                if ($group == $_REQUEST['group'])
                {
                    echo "<option SELECTED value='" . $group[0] . "'>" . $group[0] . "</option>";
                }else{
                    echo "<option onclick='window.location = \"./group.php?group=" . $_REQUEST['group'] . "\";' value='" . $group[0] . "'>" . $group[0] . "</option>";
                }
            }
            echo "</select>";
            echo "<form name='input' action='./ajax.php' method='post'>New Group:<input type='hidden' name='type' value='newGroup'><input name='newGroupName' style='display: inline; width: 100px;' type='text'><input type='submit'></form>";
        }
    ?>
</div>





