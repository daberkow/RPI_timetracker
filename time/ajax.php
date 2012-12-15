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
                        //stopped here
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
                        //stopped here
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
		if (isset($_REQUEST['day']) && isset($_REQUEST['hour']) && isset($_REQUEST['quarter']) && isset($_REQUEST['punch']) && isset($_REQUEST['mode']))
		{
		    $startMin = 0;
		    $endMin = 0;
		    switch($_REQUEST['mode'])
		    {
			case "half":
			    if (intval($_REQUEST['quarter']) == 2)
			    {
				//30-59
				$startMin = 30;
				$endMin = 59;
			    }else{
				$startMin = 0;
				$endMin = 29;
			    }
			    break;
			default:
			    break;
		    }
		    database_helper::db_connect();
		    //if the db isnt connected, escape strign does not work!
		    $startTime = mysql_real_escape_string($_REQUEST['day']) . ' ' . mysql_real_escape_string($_REQUEST['hour']) . ':' . $startMin . ':00';
		    $endTime   = mysql_real_escape_string($_REQUEST['day']) . ' ' . mysql_real_escape_string($_REQUEST['hour']) . ':' . $endMin . ':00';
		    $RESULT = database_helper::db_return_row("SELECT EXISTS(SELECT * FROM `timedata` WHERE `startTime`='" . $startTime . "' AND `endTime`='" . $endTime . "' AND `user`=(SELECT `id` FROM `users` WHERE `username`='" . time_auth::getUser() . "')) AS RESULT");
		    
		    echo $startTime . " " . $endTime;
		    if (intval($RESULT[0][0]) == 1 )
		    {
			//already has a row in table
			database_helper::db_insert_query("UPDATE `timedata` SET `status`='" . mysql_real_escape_string($_REQUEST['punch']) . "' WHERE `startTime`='" . $startTime . "' AND `endTime`='" . $endTime . "' AND `user`=(SELECT `id` FROM `users` WHERE `username`='" . time_auth::getUser() . "')");
			echo "updated";
		    }else{
			//INSERT INTO `timetracker`.`timedata` (`id`, `user`, `startTime`, `endTime`, `status`) VALUES (NULL, '2', '2012-12-14 00:00:00', '2012-12-14 00:30:00', '1');
			database_helper::db_insert_query("INSERT INTO `timedata`(`user`, `startTime`, `endTime`, `status`) VALUE((SELECT `id` FROM `users` WHERE `username`='" . time_auth::getUser() . "'), '" . $startTime . "','" . $endTime . "','" . mysql_real_escape_string($_REQUEST['punch']) . "')");
			//echo "INSERT INTO `timedata`(`user`, `startTime`, `endTime`, `status`) VALUE((SELECT `id` FROM `users` WHERE `username`='" . time_auth::getUser() . "'), '" . $startTime . "','" . $endTime . "','" . mysql_real_escape_string($_REQUEST['punch']) . "')";
			echo "inserted";
		    }
		    
		}else{
		    echo "Invalid Post";
		}
		break;
	    case "getPunches":
		if (isset($_REQUEST['start_day']))
		{
		    $result = array();
		    //stopeped here this needs to bring punches to page
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