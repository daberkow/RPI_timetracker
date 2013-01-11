<?PHP
    include('./core.php');
    
    if(time_auth::is_authenticated())
    {
        switch($_REQUEST['type'])
        {
            case "addgroupperm":
                if(isset($_REQUEST['group']) && isset($_REQUEST['username']) && isset($_REQUEST['priv']))
                {
                    $permission = database_helper::db_group_privilege(urlencode($_REQUEST['group']), time_auth::getUser());
                    if ($permission >= 2)
                    {
			$returned_result = database_helper::db_add_user_group(urlencode($_REQUEST['group']), urlencode($_REQUEST['username']), urlencode($_REQUEST['priv']), time_auth::getUser());
			echo $returned_result;
                    }else{
                        echo "error: not permitted to add user";
                    }
                }else 
		{
		    echo "Required parts not sent";
		}
                
                break;
	    case "removeUser"://type: "removeUser", group: Group, username: passedAccount, priv: passedPriv},
		if(isset($_REQUEST['group']) && isset($_REQUEST['username']) && isset($_REQUEST['priv']))
                {
                    $permission = database_helper::db_group_privilege(urlencode($_REQUEST['group']), time_auth::getUser());
                    if ($permission >= 2)
                    {
			$returned_result = database_helper::db_remove_priv(urlencode($_REQUEST['group']), urlencode($_REQUEST['username']), urlencode($_REQUEST['priv']), time_auth::getUser());
			echo $returned_result;
                    }else{
                        echo "error: not permitted to add user";
                    }
                }else 
		{
		    echo "Required parts not sent";
		}
		break;
	    case "pageUpdate":
		if (isset($_POST['newPage']) && isset($_REQUEST['group']))
		{
		    $permission = database_helper::db_group_privilege(urlencode($_REQUEST['group']), time_auth::getUser());
                    
		    if($permission >= 2)
		    {
			$getGrouPage = database_helper::db_return_row("SELECT `page` FROM `groups` WHERE `name`='" . urlencode($_REQUEST['group']) . "'");
			if (sizeof($getGrouPage) <= 0)
			{
			    //group doesnt have page
			    //bug this shouldnt happen, group creation error
			}else{
			    $returned = database_helper::db_insert_query("UPDATE `pages` SET `data`='" . urlencode($_POST['newPage']) . "' WHERE `id`='" . $getGrouPage[0][0] . "'");
			    if ($returned == 0)
			    {
				header("Location: ./group_settings.php?group=" . urlencode($_REQUEST['group']));
			    }
			}
		    }else{
			echo "not enough permissions";
			//feature, this just sits here if a invalud user tries to update
		    }
		}else{
		    echo "no given data";
		}
		break;
	    case "newGroup":
		$permission = database_helper::db_user_privilege(time_auth::getUser());
		if (intval($permission) >= 1)
		{
		    if(isset($_REQUEST['newGroupName']))
		    {
			$checkGroupName = database_helper::db_return_array("SELECT count(*) FROM `groups` WHERE `name`='" . urlencode($_REQUEST['newGroupName']) . "'");
			if ( intval($checkGroupName[0][0]) > 0)
			{
			    //group already exists
			    header("Location: ./group.php?group=" . urlencode($_REQUEST['newGroupName']));
			}else{
			    //Stopped here need to make a new group
			}
		    }else{
			echo "no group given";
		    }
		}
		break;
	    
	    //day: the_day, hour: passedHour, quarter: passedQuarter, punch: passedUsedTime, mode: "half"
	    case "punchClock":
		if (isset($_REQUEST['day']) && isset($_REQUEST['end_time']) && isset($_REQUEST['start_time']) && isset($_REQUEST['punch']))
		{
		    //echo "Day: " . $_REQUEST['day'] . ", start: " . $_REQUEST['start_time'] . ", end: " . $_REQUEST['end_time'] . " punch: " . $_REQUEST['punch'];
		    
		    database_helper::db_connect();
		    
		    //if the db isnt connected, escape strign does not work!
		    $query = "SELECT * FROM  `timedata` WHERE EXTRACT(DAY FROM `startTime`)=" . date('d', strtotime($_REQUEST['day'])) . " AND EXTRACT(MONTH FROM `startTime`)=" . date('m', strtotime($_REQUEST['day'])) . " AND EXTRACT(YEAR FROM `startTime`)=" . date('Y', strtotime($_REQUEST['day'])) . ";";
		    //echo $query;
		    $RESULT = database_helper::db_return_array($query);
		    $insert = false;
		    foreach($RESULT as $row)
		    {
			//we have times on that day lets see if they overlap the time we are trying to do
			$front = strtotime($row['startTime']);
			$tail = strtotime($row['stopTime']);
			$seeker = strtotime($_REQUEST['day'] . " " . $_REQUEST['start_time']);
			$rear_seeker = strtotime($_REQUEST['day'] . " " . $_REQUEST['end_time']);
			//we are assuming that everyone is using this interface in v0.1
			if ($front == $seeker)
			{
			    if (intval($_REQUEST['punch']) != intval($row['status']))
			    {
				//Do update
				$result = database_helper::db_insert_query("UPDATE  `timetracker`.`timedata` SET `status` = " . mysql_real_escape_string($_REQUEST['punch']) . ",`submitted` = now() WHERE  `timedata`.`id`=" . $row['id'] . ";");
				if ($result == '0')
				    echo "Saved";
				else
				    echo "Error";
				$insert = true;
			    }else{
				//trying to punch again?
				$insert = true;
			    }
			}
		    }
		    
		    //now we have the overlapping areas and need to adjust accoringly
		    if ($insert == false)
		    {
			$result = database_helper::db_insert_query("INSERT INTO `timetracker`.`timedata` (`id`, `user`, `startTime`, `stopTime`, `submitted`, `status`) VALUES (NULL, (SELECT `id` FROM `users` WHERE `username`='" . time_auth::getUser() . "'), '" . $_REQUEST['day'] . " " . $_REQUEST['start_time'] . "', '" . $_REQUEST['day'] . " " . $_REQUEST['end_time'] . "', now(),'1');");
			if ($result != false)
			    echo "Saved";
			else
			    echo "Error";
		    }
		    
		}else{
		    echo "Invalid Post";
		}
		break;
	    case "getPunches":
		if (isset($_REQUEST['start_day']))
		{
		    database_helper::db_connect();
		    //echo "SELECT * FROM `timedata` WHERE `startTime`>=FROM_UNIXTIME(" . mysql_real_escape_string($_REQUEST['start_day']) . ") AND `stopTime`<= FROM_UNIXTIME((" . mysql_real_escape_string($_REQUEST['start_day']) . " + (60*60*24*14))) AND `username`='" . time_auth::getUser() . "');";
		    $result = database_helper::db_return_array("SELECT * FROM `timedata` WHERE `startTime`>=FROM_UNIXTIME(" . mysql_real_escape_string($_REQUEST['start_day']) . ") AND `stopTime`<= FROM_UNIXTIME((" . mysql_real_escape_string($_REQUEST['start_day']) . " + (60*60*24*14))) AND `user`=(SELECT `id` FROM `users` WHERE `username`='" . time_auth::getUser() . "') AND `status`=1;");
		    echo json_encode($result);
		}else{
		    echo "Invalid Post";
		}
		break;
	    case "saveTemplate":
		if (isset($_REQUEST['dataString']) && isset($_REQUEST['temName']))
		{
		    database_helper::db_connect();
		    $query = "SELECT * FROM `templateid` WHERE `name`='" . mysql_real_escape_string($_REQUEST['temName']) . "' AND `owner`=(SELECT `id` FROM `users` WHERE `username`='" . time_auth::getUser() . "') AND `status`=1;";
		    $result = database_helper::db_return_array($query);
		    if (sizeof($result) >= 1)
		    {
			//A tempalte named that already exists
			if (sizeof($result) == 1)
			{
			    $query = "UPDATE  `timetracker`.`templates` SET  `data` = '" . mysql_real_escape_string($_REQUEST['dataString']) . "' WHERE  `templates`.`id` =" . $result[0]['id'] . ";";
			    $result = database_helper::db_insert_query($query);
			    if ($result != false)
			    {
				echo "Saved " . $result[0]['id'];
			    }else{
				echo "Error";
			    }
			}else{
			    //something weird is going on
			    echo "Error";
			}
		    }else{
			//this is a new name
			$query = "INSERT INTO `timetracker`.`templates` (`id`, `data`, `name`, `owner`, `status`) VALUES (NULL, '" . mysql_real_escape_string($_REQUEST['dataString']) . "', '" . mysql_real_escape_string($_REQUEST['temName']) . "', (SELECT `id` FROM `users` WHERE `username`='" . time_auth::getUser() . "'), '1');";
			$result = database_helper::db_insert_query($query);
			if ($result != false)
			{
			    echo "Saved " . $result;
			}else{
			    echo "Error";
			}
		    }
		}else{
		    echo "Invalid Post";
		}
		break;
	    case "getTemplate":
		if (isset($_REQUEST['template']))
		{
		    database_helper::db_connect();
		    $template = database_helper::db_return_array("SELECT `data` FROM `templates` WHERE `id`=" . mysql_escape_string($_REQUEST['template']) . " AND `owner`=(SELECT `id` FROM `users` WHERE `username`='" . time_auth::getUser() . "') AND `status`=1;");
		    echo json_encode($template);
		}else{
		    echo "Invalid Post";
		}
		break;
            default:
                echo "No Type given";
                break;
        }
    }else{
	echo "error: not authenticted user";
    }
?>