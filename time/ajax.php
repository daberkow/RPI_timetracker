<?PHP
    include('./core.php');
    
    if(phpCAS::isAuthenticated())
    {
        switch($_REQUEST['type'])
        {
            case "addgroupperm":
                if(isset($_REQUEST['group']) && isset($_REQUEST['username']) && isset($_REQUEST['priv']))
                {
		    if ($_REQUEST['group'] != "home" && $_REQUEST['group'] != "homeAuth")
		    {
			$permission = database_helper::db_group_privilege(urlencode($_REQUEST['group']), phpCAS::getUser());
			if ($permission >= 2)
			{
			    $returned_result = database_helper::db_add_user_group(urlencode($_REQUEST['group']), urlencode($_REQUEST['username']), urlencode($_REQUEST['priv']), phpCAS::getUser());
			    echo $returned_result;
			}else{
			    echo "error: not permitted to add user";
			}
		    }else{
			$privilege = intval(database_helper::db_user_privilege(phpCAS::getUser()));
			if (2 == $privilege)
			{
			    $returned_result = database_helper::db_add_user_group(urlencode($_REQUEST['group']), urlencode($_REQUEST['username']), urlencode($_REQUEST['priv']), phpCAS::getUser());
			    echo $returned_result;
			}else{
			    echo "not enough permissions!";
			}
		    }
                }else 
		{
		    echo "Required parts not sent";
		}
                
                break;
	    case "removeUser"://type: "removeUser", group: Group, username: passedAccount, priv: passedPriv},
		if(isset($_REQUEST['group']) && isset($_REQUEST['username']) && isset($_REQUEST['priv']))
                {
		    if ($_REQUEST['group'] != "home" && $_REQUEST['group'] != "homeAuth")
		    {
			$permission = database_helper::db_group_privilege(urlencode($_REQUEST['group']), phpCAS::getUser());
			if ($permission >= 2)
			{
			    $returned_result = database_helper::db_remove_priv(urlencode($_REQUEST['group']), urlencode($_REQUEST['username']), urlencode($_REQUEST['priv']), phpCAS::getUser());
			    echo $returned_result;
			}else{
			    echo "error: not permitted to add user";
			}
		    }else{
			$privilege = intval(database_helper::db_user_privilege(phpCAS::getUser()));
			if (2 == $privilege)
			{
			    $returned_result = database_helper::db_remove_priv(urlencode($_REQUEST['group']), urlencode($_REQUEST['username']), urlencode($_REQUEST['priv']), phpCAS::getUser());
			    echo $returned_result;
			}else{
			    echo "not enough permissions!";
			}
		    }
                }else 
		{
		    echo "Required parts not sent";
		}
		break;
	    case "pageUpdate":
		if (isset($_POST['newPage']) && isset($_REQUEST['group']))
		{
		    if ($_REQUEST['group'] != "home" && $_REQUEST['group'] != "homeAuth")
		    {
			$permission = database_helper::db_group_privilege(urlencode($_REQUEST['group']), phpCAS::getUser());
			
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
			    echo "not enough permissions!";
			    //feature, this just sits here if a invalud user tries to update
			}
		    }else{
			//special cases for pages
			$privilege = intval(database_helper::db_user_privilege(phpCAS::getUser()));
			if (2 == $privilege)
			{
			    switch ($_REQUEST['group'])
			    {
				case "home":
				    $returned = database_helper::db_insert_query("UPDATE `pages` SET `data`='" . urlencode($_POST['newPage']) . "' WHERE `page`='home'");
				    if ($returned == 0)
				    {
					header("Location: ./index.php");
				    }
				    break;
				case "homeAuth":
				    $returned = database_helper::db_insert_query("UPDATE `pages` SET `data`='" . urlencode($_POST['newPage']) . "' WHERE `page`='homeAuth'");
				    if ($returned == 0)
				    {
					header("Location: ./index.php");
				    }
				    break;
			    }
			}else{
			    echo "not enough permissions";
			}
		    }
		}else{
		    echo "no given data";
		}
		break;
	    case "newGroup":
		$permission = database_helper::db_user_privilege(phpCAS::getUser());
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
		if (isset($_REQUEST['day']) && isset($_REQUEST['end_time']) && isset($_REQUEST['start_time']) && isset($_REQUEST['punch']) && isset($_REQUEST['group']))
		{
		    //echo "Day: " . $_REQUEST['day'] . ", start: " . $_REQUEST['start_time'] . ", end: " . $_REQUEST['end_time'] . " punch: " . $_REQUEST['punch'];
		    
		    database_helper::db_connect();
		    
		    $isOpposite = 0;
		    if (intval($_REQUEST['punch']) == 0)
		    {
			$isOpposite = 1;
		    }
		    
		    if (isset($_REQUEST['override']))
		    {
			$privilege = intval(database_helper::db_group_privilege(urlencode($_REQUEST['group']), phpCAS::getUser()));
			if($privilege >= 2)
			{
			    //if the db isnt connected, escape strign does not work!
			    $query = "SELECT * FROM  `timedata` WHERE EXTRACT(DAY FROM `startTime`)=" . date('d', strtotime($_REQUEST['day'])) . " AND EXTRACT(MONTH FROM `startTime`)=" . date('m', strtotime($_REQUEST['day'])) . " AND EXTRACT(YEAR FROM `startTime`)=" . date('Y', strtotime($_REQUEST['day'])) . " AND `user`='" . mysql_real_escape_string($_REQUEST['override']) . "' AND `group`=(SELECT `id` FROM `groups` WHERE `name`='" . mysql_real_escape_string($_REQUEST['group']) . "') AND `status`='" . $isOpposite . "';";
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
				$result = database_helper::db_insert_query("INSERT INTO `timetracker`.`timedata` (`id`, `user`, `startTime`, `stopTime`, `submitted`, `status`, `group`) VALUES (NULL, '" . mysql_real_escape_string($_REQUEST['override']) . "', '" . $_REQUEST['day'] . " " . $_REQUEST['start_time'] . "', '" . $_REQUEST['day'] . " " . $_REQUEST['end_time'] . "', now(),'1',(SELECT `id` FROM `groups` WHERE `name`='" . mysql_real_escape_string($_REQUEST['group']) . "'));");
				if ($result != false){
				    echo "Saved";
				    //echo "INSERT INTO `timetracker`.`timedata` (`id`, `user`, `startTime`, `stopTime`, `submitted`, `status`, `group`) VALUES (NULL, '" . mysql_real_escape_string($_REQUEST['override']) . "', '" . $_REQUEST['day'] . " " . $_REQUEST['start_time'] . "', '" . $_REQUEST['day'] . " " . $_REQUEST['end_time'] . "', now(),'1','" . mysql_escape_string($_REQUEST['group']) . "');";
				}else
				    echo "Error";
			    }
			}else{
			    //security problem
			}
		    }else{
			//if the db isnt connected, escape strign does not work!
			$query = "SELECT * FROM  `timedata` WHERE EXTRACT(DAY FROM `startTime`)=" . date('d', strtotime($_REQUEST['day'])) . " AND EXTRACT(MONTH FROM `startTime`)=" . date('m', strtotime($_REQUEST['day'])) . " AND EXTRACT(YEAR FROM `startTime`)=" . date('Y', strtotime($_REQUEST['day'])) . " AND `user`=(SELECT `id` from `users` WHERE `username`='" . phpCAS::getUser() . "') AND `group`=(SELECT `id` FROM `groups` WHERE `name`='" . mysql_real_escape_string($_REQUEST['group']) . "') AND `status`='" . $isOpposite . "';";
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
			    $result = database_helper::db_insert_query("INSERT INTO `timetracker`.`timedata` (`id`, `user`, `startTime`, `stopTime`, `submitted`, `status`, `group`) VALUES (NULL, (SELECT `id` FROM `users` WHERE `username`='" . phpCAS::getUser() . "'), '" . $_REQUEST['day'] . " " . $_REQUEST['start_time'] . "', '" . $_REQUEST['day'] . " " . $_REQUEST['end_time'] . "', now(),'1',(SELECT `id` FROM `groups` WHERE `name`='" . mysql_real_escape_string($_REQUEST['group']) . "'));");
			    if ($result != false)
				echo "Saved";
			    else
				echo "Error";
			}
		    }
		    
		}else{
		    echo "Invalid Post";
		}
		break;
	    case "getPunches":
		if (isset($_REQUEST['start_day']) && isset($_REQUEST['group']))
		{
		    database_helper::db_connect();
		    if (isset($_REQUEST['override']))
		    {
			$privilege = intval(database_helper::db_group_privilege(urlencode($_REQUEST['group']), phpCAS::getUser()));
			if($privilege >= 2)
			{
			    $result = database_helper::db_return_array("SELECT * FROM `timedata` WHERE `startTime`>=FROM_UNIXTIME(" . mysql_real_escape_string($_REQUEST['start_day']) . ") AND `stopTime`<= FROM_UNIXTIME((" . mysql_real_escape_string($_REQUEST['start_day']) . " + (60*60*24*14))) AND `user`='" . mysql_real_escape_string($_REQUEST['override']) . "' AND `group`=(SELECT `id` FROM `groups` WHERE `name`='" . mysql_real_escape_string($_REQUEST['group']) . "') AND `status`=1;");
			    echo json_encode($result);
			}else{
			    //security report
			}
		    }else{
			//echo "SELECT * FROM `timedata` WHERE `startTime`>=FROM_UNIXTIME(" . mysql_real_escape_string($_REQUEST['start_day']) . ") AND `stopTime`<= FROM_UNIXTIME((" . mysql_real_escape_string($_REQUEST['start_day']) . " + (60*60*24*14))) AND `user`=(SELECT `id` FROM `users` WHERE `username`='" . phpCAS::getUser() . "') AND `group`=(SELECT `id` FROM `groups` WHERE `name`='" . mysql_real_escape_string($_REQUEST['group']) . "') AND `status`=1;";
			$result = database_helper::db_return_array("SELECT * FROM `timedata` WHERE `startTime`>=FROM_UNIXTIME(" . mysql_real_escape_string($_REQUEST['start_day']) . ") AND `stopTime`<= FROM_UNIXTIME((" . mysql_real_escape_string($_REQUEST['start_day']) . " + (60*60*24*14))) AND `user`=(SELECT `id` FROM `users` WHERE `username`='" . phpCAS::getUser() . "') AND `group`=(SELECT `id` FROM `groups` WHERE `name`='" . mysql_real_escape_string($_REQUEST['group']) . "') AND `status`=1;");
			echo json_encode($result);
		    }
		}else{
		    echo "Invalid Post";
		}
		break;
	    case "saveTemplate":
		if (isset($_REQUEST['dataString']) && isset($_REQUEST['temName']))
		{
		    database_helper::db_connect();
		    $query = "SELECT * FROM `templateid` WHERE `name`='" . mysql_real_escape_string($_REQUEST['temName']) . "' AND `owner`=(SELECT `id` FROM `users` WHERE `username`='" . phpCAS::getUser() . "') AND `status`=1;";
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
			$query = "INSERT INTO `timetracker`.`templates` (`id`, `data`, `name`, `owner`, `status`) VALUES (NULL, '" . mysql_real_escape_string($_REQUEST['dataString']) . "', '" . mysql_real_escape_string($_REQUEST['temName']) . "', (SELECT `id` FROM `users` WHERE `username`='" . phpCAS::getUser() . "'), '1');";
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
		    $template = database_helper::db_return_row("SELECT `data` FROM `templates` WHERE `id`=" . mysql_real_escape_string($_REQUEST['template']) . " AND `owner`=(SELECT `id` FROM `users` WHERE `username`='" . phpCAS::getUser() . "') AND `status`=1;");
		    echo json_encode($template);
		}else{
		    echo "Invalid Post";
		}
		break;
	    case "DBMacro":
		if (isset($_REQUEST['macro_code']) && isset($_REQUEST['group']))
		{
		    //1 is load template to start_date, 2 is wipe board for week with start_date
		    switch (intval($_REQUEST['macro_code']))
		    {
			case 1:
			    if (isset($_REQUEST['start_date']) && isset($_REQUEST['template']))
			    {
				$theDate = explode("-",$_REQUEST['start_date']);
				database_helper::db_connect();
				$template = database_helper::db_return_row("SELECT `data` FROM `templates` WHERE `id`=" . mysql_real_escape_string($_REQUEST['template']) . " AND `owner`=(SELECT `id` FROM `users` WHERE `username`='" . phpCAS::getUser() . "') AND `status`=1;");
				$data = (string) $template[0][0];
				$data = explode(",", $data);
				$error = false;
				for($i = 0; $i < sizeof($data); $i++)
				{
				    $dateString = explode("_", $data[$i]);
				    //0 is day, 1 is hour, 2 is 0 or 2 for 0 or 30;
				    $mins = 0;
				    if (intval($dateString[2]) == 2)
				    {
					$mins = 30;
				    }
				    //hour, min, second, month, day, year
				    $punchTime = mktime($dateString[1], $mins, 0, $theDate[1], $theDate[2]+(intval($dateString[0])-1), $theDate[0]);
				    $start  = date('Y-m-d H:i',$punchTime);
				    $end    = date('Y-m-d H:i',$punchTime+(60*29));
				    //echo "<DIV>Start:" . $start . " End:" . $end . " </DIV>\n";
				    
				    $query = "INSERT INTO `timetracker`.`timedata` (`id`, `user`, `startTime`, `stopTime`, `submitted`, `status`, `group`) VALUES (NULL, (SELECT `id` FROM `users` WHERE `username`='" . phpCAS::getUser() . "'), '" . $start . "', '" . $end . "', now(),'1',(SELECT `id` FROM `groups` WHERE `name`='" . mysql_real_escape_string($_REQUEST['group']) . "'));";
				    
				    //echo $query;
				    $result = database_helper::db_insert_query($query);
				    if ($result[0] == 'E')
				    {
					$error = true;
				    }
				}
				if ($error == false)
				{
				    echo "Saved";
				}else{
				    echo "Error";
				}
			    }else{
				echo "Invalid Post";
			    }
			    break;
			case 2:
			    if (isset($_REQUEST['start_date']))
			    {
				$theDate = explode("-",$_REQUEST['start_date']);
				$newTime = mktime(0, 0, 0, $theDate[1], $theDate[2], $theDate[0]);
				$query = "UPDATE `timetracker`.`timedata` SET `status`=0 AND `submitted`=NOW() WHERE `startTime`>=FROM_UNIXTIME(" . $newTime . ") AND `stopTime`<= FROM_UNIXTIME((" . $newTime . " + (60*60*24*14))) AND `user`=(SELECT `id` FROM `users` WHERE `username`='" . phpCAS::getUser() . "') AND `group`=(SELECT `id` FROM `groups` WHERE `name`='" . mysql_real_escape_string($_REQUEST['group']) . "') AND `status`=1";
				$result = database_helper::db_insert_query($query);
				if ($result[0] == 'E')
				{
				    echo "Error";
				}else{
				    echo "Saved";
				}
			    }else{
				echo "Invalid Post";
			    }
			    break;
		    }
		}else{
		    echo "Invalid Post";
		}
		break;
	    case "printReport":
		if (isset($_REQUEST['start_date']) && isset($_REQUEST['group']))
		{
		    $permission = database_helper::db_group_privilege(urlencode($_REQUEST['group']), phpCAS::getUser());
		    $theDate = explode("-",$_REQUEST['start_date']);
		    $newTime = mktime(0, 0, 0, $theDate[1], $theDate[2], $theDate[0]);
		    $totalhours = 0.0;
		    if ($permission >= 2)
		    {
			$result = database_helper::db_return_array("SELECT * FROM `timedata` WHERE `startTime`>=FROM_UNIXTIME(" . $newTime . ") AND `stopTime`<= FROM_UNIXTIME((" . $newTime . " + (60*60*24*14))) AND `group`=(SELECT `id` FROM `groups` WHERE `name`='" . mysql_real_escape_string($_REQUEST['group']) . "') AND `status`=1;");
			//echo "SELECT * FROM `timedata` WHERE `startTime`>=FROM_UNIXTIME(" . $newTime . ") AND `stopTime`<= FROM_UNIXTIME((" . $newTime . " + (60*60*24*14))) AND `group`=(SELECT `id` FROM `groups` WHERE `name`='" . mysql_escape_string($_REQUEST['group']) . "') AND `status`=1";
			$users = database_helper::db_return_array("Select `users`.`id`, `users`.`username` FROM `users` LEFT JOIN `groupusers` on `groupusers`.`userid`=`users`.`id` WHERE `groupusers`.`privilege`=1 or `groupusers`.`privilege`=3");
			$Final_Array = array();
			$dayArray = array(); // hey that rhymes
			foreach($result as $row)
			{
			    //( [0] => 6 [id] => 6 [1] => 2 [user] => 2 [2] => 2013-01-06 03:30:00 [startTime] => 2013-01-06 03:30:00 [3] => 2013-01-06 03:59:00 [stopTime] => 2013-01-06 03:59:00 [4] => 2013-01-15 00:58:07 [submitted] => 2013-01-15 00:58:07 [5] => 1 [status] => 1 )
			    //this requires start day and end day be the same day
			    $temp_split = explode(" ",$row['startTime']);
			    $date = $temp_split[0];		//still one string
			    $newDate = explode("-", $date);	//0 year 1 day 2 month
			    $Starttime = $temp_split[1];
			    $Starttime = explode(":", $Starttime);	//0 hour 1 minute 2 second
			    $temp_split = explode(" ",$row['stopTime']);
			    $Endtime = $temp_split[1];
			    $Endtime = explode(":", $Endtime);
			    $Starttime = mktime($Starttime[0],$Starttime[1], $Starttime[2]);
			    $Endtime = mktime($Endtime[0],$Endtime[1]+1, $Endtime[2]);//make up for count by 0
			    $totalTime = $Endtime - $Starttime;
			    if (isset($Final_Array[$row['user']][$date]))
			    {
				$Final_Array[$row['user']][$date] += ($totalTime/60);
			    }else{
				$Final_Array[$row['user']][$date] = ($totalTime/60);
			    }
			    
			    $Final_Array[$row['user']]['read'] = false;
			    //$Final_Array[$row['user']]['name'] = $row['user'];
			}
			$flip = true;
			echo "<table style='border-style: solid; border-width:1px;'><tr style='border-style: solid; border-width:1px;'><td style='width: 20px;'></td>";
			for ($k = 0; $k < 14; $k++)
			{
			    $referenceDate = date('Y-m-d', mktime(0, 0, 0, $theDate[1], $theDate[2]+$k, $theDate[0]));
			    echo "<td style='border-width: 0px; border-left-width:1px; border-bottom-width:1px; border-style:solid;'>" . $referenceDate . "</td>";
			}
			echo "<td style='border-width: 0px; border-left-width:1px; border-bottom-width:1px; border-style:solid;'>Total</td>";
			echo "</tr>";
			
			foreach($users as $singleUser)
			{
			    if ($flip)
			    {
				echo "<tr class='colored' style='background:#CCCCCC;'>";
			    }else{
				echo "<tr>";
			    }
			    $flip = !$flip;
			    echo "<td>" . $singleUser['username'] . "</td>";
			    //itterate through days
			    $total = 0.0;
			    for ($k = 0; $k < 14; $k++)
			    {
				$referenceDate = date('Y-m-d', mktime(0, 0, 0, $theDate[1], $theDate[2]+$k, $theDate[0]));
				if (isset($Final_Array[$singleUser['id']][$referenceDate]))
				{
				    $Final_Array[$singleUser['id']]['read']=true;
				    echo "<td style='border-width: 0px; border-left-width:1px; border-bottom-width:1px; border-style:solid;'>" . ($Final_Array[$singleUser['id']][$referenceDate]/60) . "</td>";
				    $total += ($Final_Array[$singleUser['id']][$referenceDate]/60);
				    if (isset($dayArray[$referenceDate])){
					    $dayArray[$referenceDate] += (($Final_Array[$singleUser['id']][$referenceDate])/60);
				    }else{
					    $dayArray[$referenceDate] = (($Final_Array[$singleUser['id']][$referenceDate])/60);
				    }
				}else{
				    echo "<td style='border-width: 0px; border-left-width:1px; border-bottom-width:1px; border-style:solid;'>0</td>";
				}
			    }
			    
			    echo "<td style='border-width: 0px; border-left-width:1px; border-bottom-width:1px; border-style:solid;'>" . $total . "</td>";
			    $totalhours += $total;
			    echo "</tr>";
			}
			/* This is was started if we want to show hours for removed users
			foreach($Final_Array as $datapoint)
			{
			    if ($datapoint['read'] == false)
			    {
				
			    }
			}*/
			/*
			foreach($Final_Array as $user => $point)
			{
			    if ($flip)
			    {
				echo "<tr style='background:#CCCCCC;'>";
			    }else{
				echo "<tr>";
			    }
			    $flip = !$flip;
			    $myusername = "";
			    foreach($users as $userRow)
			    {
				if ($userRow['id'] == $user)
				{
				    $myusername = $userRow['username'];
				}
			    }
			    echo "<td>$myusername</td>";
			    
			    //itterate through days
			    $total = 0.0;
			    for ($k = 0; $k < 14; $k++)
			    {
				$referenceDate = date('Y-m-d', mktime(0, 0, 0, $theDate[1], $theDate[2]+$k, $theDate[0]));
				if (isset($point[$referenceDate]))
				{
				    echo "<td style='border-width: 0px; border-left-width:1px; border-bottom-width:1px; border-style:solid;'>" . ($point[$referenceDate]/60) . "</td>";
				    $total += ($point[$referenceDate]/60);
				    $dayArray[$referenceDate] += ($point[$referenceDate]/60);
				}else{
				    echo "<td style='border-width: 0px; border-left-width:1px; border-bottom-width:1px; border-style:solid;'>0</td>";
				}
			    }
			    echo "<td style='border-width: 0px; border-left-width:1px; border-bottom-width:1px; border-style:solid;'>" . $total . "</td>";
			    $totalhours += $total;
			    echo "</tr>";
			}*/
			if ($flip)
			{
			    echo "<tr class='colored' style='background:#CCCCCC;'>";
			}else{
			    echo "<tr>";
			}
			$flip = !$flip;
			echo "<td>Total:</td>";
			for ($k = 0; $k < 14; $k++)
			{
			    $referenceDate = date('Y-m-d', mktime(0, 0, 0, $theDate[1], $theDate[2]+$k, $theDate[0]));
			    if (isset($dayArray[$referenceDate]))
			    {
				echo "<td style='border-width: 0px; border-left-width:1px; border-bottom-width:1px; border-style:solid; border-top-width: 2px;'>" . $dayArray[$referenceDate] . "</td>";
			    }else{
				echo "<td style='border-width: 0px; border-left-width:1px; border-bottom-width:1px; border-style:solid; border-top-width: 2px;'>0</td>";
			    }
			}
			echo "<td style='border-width: 0px; border-left-width:1px; border-bottom-width:1px; border-style:solid; border-top-width: 2px;'>" . $totalhours . "</td>";
			echo "</tr></table>";
			//print_r($result);
		    }else{
			echo "Error not enough permissions";
		    }
		}else{
		    echo "Error invalid post";
		}
		break;
	    case "LockCards":
		if (isset($_REQUEST['start_date']) && isset($_REQUEST['group']) && isset($_REQUEST['end_date']))  //start_date: sqlDate, group: groupName,
		{
		    $permission = database_helper::db_group_privilege(urlencode($_REQUEST['group']), phpCAS::getUser());
		    if ($permission >= 2)
		    {
			$start = mysql_real_escape_string($_REQUEST['start_date']) . " 0:0:0";
			$end = mysql_real_escape_string($_REQUEST['end_date']) . " 23:59:59";
			
			$result = database_helper::db_insert_query("INSERT INTO `timedata`(`user`,`startTime`,`stopTime`,`group`,`submitted`,`status`) VALUES('0', '" . $start . "', '" . $end . "',(SELECT `id` FROM `groups` WHERE `name`='" . mysql_real_escape_string($_REQUEST['group']) . "'),  NOW(), 2)");
			if ($result != false)
			{
			    echo $result;
			}else{
			    echo "Error Inserting";
			}
		    }else{
			echo "Error Not enough permission";
		    }
		}else{
		    echo "Error Invalid post";
		}
		break;
	    case "unLockCards":
		if (isset($_REQUEST['start_date']) && isset($_REQUEST['group']) && isset($_REQUEST['end_date']))  //start_date: sqlDate, group: groupName, length: 14
		{
		    $permission = database_helper::db_group_privilege(urlencode($_REQUEST['group']), phpCAS::getUser());
		    if ($permission >= 2)
		    {
			$start = mysql_real_escape_string($_REQUEST['start_date']) . " 0:0:0";
			$end = mysql_real_escape_string($_REQUEST['end_date']) . " 23:59:59";
			//echo "UPDATE `timedata` SET `status`=3, `submitted`=NOW() WHERE `group`=(SELECT `id` FROM `groups` WHERE `name`='" . mysql_real_escape_string($_REQUEST['group']) . "') AND `startTime`='" . $start . "' AND `stopTime`='" . $end . "' AND `status`=2";
			$result = database_helper::db_insert_query("UPDATE `timedata` SET `status`=3, `submitted`=NOW() WHERE `group`=(SELECT `id` FROM `groups` WHERE `name`='" . mysql_real_escape_string($_REQUEST['group']) . "') AND `startTime`='" . $start . "' AND `stopTime`='" . $end . "' AND `status`=2");
			//echo $result;
			if ($result == '0')
			{
			    echo $result;
			}else{
			    echo "Error Inserting";
			}
		    }else{
			echo "Error Not enough permission";
		    }
		}else{
		    echo "Error Invalid post";
		}
		break;
	    case "check_locked":
		if (isset($_REQUEST['start_date']) && isset($_REQUEST['group']))  //start_date: sqlDate, group: groupName
		{
		    $start_date = mysql_real_escape_string($_REQUEST['start_date']);
		    $result = database_helper::db_return_array("SELECT COUNT(*) AS RESULT FROM `timedata` WHERE `group`=(SELECT `id` FROM `groups` WHERE `name`='" . mysql_real_escape_string($_REQUEST['group']) . "') AND `startTime`='" . mysql_real_escape_string($start_date) . "' AND `status`=2");
		    if (intval($result[0][0]) > 0)
		    {
			echo "locked";
		    }else{
			echo "unlocked";
		    }
		}else{
		    echo "Error Invalid post";
		}
		break;
            default:
                echo "Error No Type given";
                break;
        }
    }else{
	echo "error: not authenticted user";
    }
?>