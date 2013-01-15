<div id="Stats">					
    <?PHP
    
    	if (phpCAS::isAuthenticated())
	{
		$authed = true;
		$user	= phpCAS::getUser();;
		$privl = intval(database_helper::db_user_privilege($user));
	}else{
		$authed = false;
	}
        
        if ($authed)
        {//Authenacted users get logout
            echo "<a href='./logout.php' class='labels'>Logout " . $user . "</a>";
	    if ( 2 <= $privl)
	    {
		echo "<p><a href='./index_settings.php'>System Settings</a></p>";
	    }
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
            echo "Groups:<select id='groupSelector' style='width: 150px;' onchange='gotoGroup()'>";
	    echo "<option value='-'>---------</option>";
            $getGroups = database_helper::db_get_groups(phpCAS::getUser());
            foreach ($getGroups as $group)
            {
		echo $group[0] . " " . $_REQUEST['group'];
                if ($group[0] == $_REQUEST['group'])
                {
                    echo "<option SELECTED value='" . $group[0] . "'>" . $group[0] . "</option>";
                }else{
                    echo "<option onclick='window.location = \"./group.php?group=" . $group[0] . "\";' value='" . $group[0] . "'>" . $group[0] . "</option>";
                }
            }
            echo "</select>";
	    if ( 2 <= $privl)
	    {
		echo "<form name='input' action='./ajax.php' method='post'>New Group:<input type='hidden' name='type' value='newGroup'><input name='newGroupName' style='display: inline; width: 100px;' type='text'><input type='submit'></form>";
	    }
	}
    ?>
</div>
<script>
    function gotoGroup()
    {
	if($('#groupSelector').val() == "index.php")
	{
	    window.location = './index.php';
	}else{
	    window.location = './group.php?group=' + $('#groupSelector').val();
	}
    }
</script>





