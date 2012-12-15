<?PHP

class time_auth {
	public static function login_redirect()
	{
		if(!time_auth::is_authenticated())
		{
		    header("Location: ./index.php");
		}/**else{
		    $user	= phpCAS::getUser();
		    
		    if ($privilege <= 0)
		    {
			header("Location: ./index.php");
		    }
		}**/
	}
	
	private function dev_mode()
	{
		return true;
	}
	
	public static function is_authenticated()
	{
		if (time_auth::dev_mode())
		{
			//dev mode is enabled z
			if(isset($_COOKIE['timeDev']))
			{
				return true;
			}else{
				return false;
			}
		}else{
			include_once('./cas/CAS.php');
	
			phpCAS::client(CAS_VERSION_2_0,'cas-auth.rpi.edu',443,'/cas/');
			    
			// SSL!
			phpCAS::setCasServerCACert("./cas-auth.rpi.edu");
			
			return phpCAS::isAuthenticated();
		}
	}
	
	public static function getUser()
	{
		if (time_auth::dev_mode())
		{
			//dev mode is enabled z
			if(isset($_COOKIE['timeDev']))
			{
				return $_COOKIE['timeDev'];
			}else{
				return "";
			}
		}else{
			include_once('./cas/CAS.php');
	
			phpCAS::client(CAS_VERSION_2_0,'cas-auth.rpi.edu',443,'/cas/');
			    
			// SSL!
			phpCAS::setCasServerCACert("./cas-auth.rpi.edu");
			
			return phpCAS::getUser();
		}
	}
}

class database_helper {

	public static function db_connect()
	{
		if (isset($connected) == false)
		{
			mysql_connect("localhost", "timetracker", "DdCyzpALrxndc6BY") or die("Could Not Connect To MYSQL");
			mysql_select_db("timetracker") or die ("Could Not Connect to DATABASE");
		}
	}
	
	public static function db_disconnect()
	{
		if(isset($connected))
		{
		    mysql_close($connected);
		    $connected = null;
		}
	}
	
	//same as the array just gets first item only
	public static function db_return_row($passed_query)
	{
		database_helper::db_connect();
		$return_array = array();
		//echo $query;
		$main_result = mysql_query($passed_query);
		if (mysql_num_rows($main_result) == 0)
		{
			return $return_array;
		}
		if ($main_result)
		{
			$main_row = mysql_fetch_array($main_result);
			array_push($return_array, $main_row);
			return $return_array;
		}else{
			echo "Error with " . $passed_query;
			return $return_array;
		}
	}
	
	public static function db_return_array($passed_query)
        {
            database_helper::db_connect();
            $return_array = array();
            //echo $query;
            $main_result = mysql_query($passed_query);
            if (mysql_num_rows($main_result) == 0)
            {
                return $return_array;
            }
            if ($main_result)
            {
                while($main_row = mysql_fetch_array($main_result))
                {
                    array_push($return_array, $main_row);
                }
                return $return_array;
            }else{
                echo "Error with " . $passed_query;
                return $return_array;
            }
        }
        
        //Another wrapper but for a insert comamnd
        public static function db_insert_query($passed_query)
        {
            database_helper::db_connect();
            $result = mysql_query($passed_query);
            if ($result)
            {
                return mysql_insert_id();
            }else{
                echo "Error on inset " . $passed_query;
                return false;
            }
        }
	
	
	public static function db_get_groups($username)
	{
		return database_helper::db_return_array("SELECT groups.name FROM groups INNER JOIN (users INNER JOIN groupusers ON users.id = groupusers.userid) ON groups.id = groupusers.groupid WHERE (((users.username)='" . $username . "') AND (groupusers.privilege)>0);");
	}
	public static function db_scan_for_name($passed_name)
	{
		//echo $passed_name;
		$groupArray = database_helper::db_return_array("SELECT COUNT(*) FROM `groups` WHERE `name`='" . $passed_name . "';");
		//print_r($groupArray);
		if (intval($groupArray[0][0]) > 0)
		{
			return true;
		}else{
			return false;
		}
	}
	
	//-3 is no group, -2 is no user, -1 is user not in group, or privilege
	public static function db_group_privilege($passed_name, $passed_username)
	{
		$groupexists = database_helper::db_return_array("SELECT COUNT(`id`) AS 'COUNT' FROM `groups` WHERE `name`='" . $passed_name . "'");
		if (intval($groupexists[0][0]) == 0)
		{
			return -3;
		}
		$userexists = database_helper::db_return_array("SELECT COUNT(`id`) AS 'COUNT' FROM `users` WHERE `username`='" . $passed_username . "'");
		if (intval($userexists[0][0]) == 0)
		{
			return -2;
		}
		$userInfo = database_helper::db_return_array("SELECT privilege FROM `groupusers` WHERE `userid`=(SELECT id FROM `users` WHERE `username`='" . $passed_username . "' LIMIT 0,1) AND `groupid`=(SELECT id FROM `groups` WHERE `name`='" . $passed_name . "' LIMIT 0,1);");
		if (sizeof($userInfo) == 0)
		{
			return -1;
		}
		return $userInfo[0][0];
	}
	
	public static function db_user_privilege($passed_username)
	{
		$return_array = database_helper::db_return_array("SELECT `privilege` FROM `users` WHERE `username`='" . $passed_username . "' LIMIT 0,1;");
		if (count($return_array) > 0)
		{
			return $return_array[0][0];
		}
		else{
			return -1;
		}
	}

	public static function db_convert_returnarray_usernames($passed_array)
	{//this could be optomized for memory
		$returnedNames = array();
		for($i = 0; $i < sizeof($passed_array); $i++)
		{
			$temp_array = database_helper::db_return_row("SELECT username FROM `users` WHERE `id`=" . $passed_array[$i][0] . ";");
			array_push($returnedNames, $temp_array[0][0]);
		}
		return $returnedNames;
	}
	
	public static function db_add_user_system($passedUsername, $passedPrivilege, $passedCalledPriv)
	{//This can only be called by passing the privilege of the user calling it
		/*
		 * -1 Disabled User tries command
		 * -2 User tries to use higher than held privilegs
		 * 
		 */
		switch ($passedCalledPriv)
		{
			case 0:
				return -1;
				break;
			case 1:
				if ($passedPrivilege == 1)
				{
					//make a standard user
					$insertID = database_helper::db_insert_query("INSERT INTO `users`(`username`,`privilege`) VALUES ('" . $passedUsername . "',1);");
					return $insertID;
				}else{
					return -2;
				}
				break;
			case 2:
				//user can add super users
				
				//Hey Dan Why is this switch here if that is just doing the same thing
				
				//Well its here if we need to do different things for different users down the line, and no equal injecting the permission area
				switch($passedPrivilege)
				{
					case 0:
						$insertID = database_helper::db_insert_query("INSERT INTO `users`(`username`,`privilege`) VALUES ('" . $passedUsername . "',0);");
						return $insertID;
						break;
					case 1:
						$insertID = database_helper::db_insert_query("INSERT INTO `users`(`username`,`privilege`) VALUES ('" . $passedUsername . "',1);");
						return $insertID;
						break;
					case 2:
						$insertID = database_helper::db_insert_query("INSERT INTO `users`(`username`,`privilege`) VALUES ('" . $passedUsername . "',2);");
						return $insertID;
						break;
				}
				break;
			default:
				return -1;
				break;
		}
	}
	
	public static function db_add_user_group($passedGroup, $passedUsername, $passedPrivilege, $passedCommandingUser)
	{
		$resultint = database_helper::db_group_privilege($passedGroup, $passedUsername);
		switch($resultint)
		{
			case -3:
				//group doesnt exist
				//This should not be called
				return -3;
				break;
			case -2:
				//user doesnt exist
				//Add user to user database
				$privilege = database_helper::db_user_privilege($passedCommandingUser);
				database_helper::db_add_user_system($passedUsername, 1, $privilege);
				$addedResult = database_helper::db_insert_query("INSERT INTO `groupusers`(`userid`,`groupid`, `privilege`) VALUES ((SELECT `id` FROM `users` WHERE `username`='" . $passedUsername . "'),(SELECT `id` FROM `groups` WHERE `name`='" . $passedGroup . "'), " . $passedPrivilege . ");");
				return $addedResult;
				break;
			case -1:
				//user exists but not in group
				$addedResult = database_helper::db_insert_query("INSERT INTO `groupusers`(`userid`,`groupid`, `privilege`) VALUES ((SELECT `id` FROM `users` WHERE `username`='" . $passedUsername . "'),(SELECT `id` FROM `groups` WHERE `name`='" . $passedGroup . "'), " . $passedPrivilege . ");");
				return $addedResult;
				break;
			case 0:
				//expired user
				if ($passedPrivilege > 0)
				{//moving them back to active
					$addedResult = database_helper::db_insert_query("UPDATE `groupusers` SET `privilege`=" . $passedPrivilege . " WHERE `userid`=(SELECT `id` FROM `users` WHERE `username`='" . $passedUsername . "') AND `groupid`=(SELECT `id` FROM `groups` WHERE `name`='" . $passedGroup . "')");
					return $addedResult;
				}
				break;
			case 1:
				//the user has standard users rights and is either being given new right or disabled
				switch($passedPrivilege)
				{
					case 0:
						$addedResult = database_helper::db_insert_query("UPDATE `groupusers` SET `privilege`=0 WHERE `userid`=(SELECT `id` FROM `users` WHERE `username`='" . $passedUsername . "') AND `groupid`=(SELECT `id` FROM `groups` WHERE `name`='" . $passedGroup . "')");
						return $addedResult;
						break;//not reachable
					case 1:
						//do nothing, nothing has changed
						break;
					case 2:
						//we are assuming that we are adding this privilage with the other we already have
						$addedResult = database_helper::db_insert_query("UPDATE `groupusers` SET `privilege`=3 WHERE `userid`=(SELECT `id` FROM `users` WHERE `username`='" . $passedUsername . "') AND `groupid`=(SELECT `id` FROM `groups` WHERE `name`='" . $passedGroup . "')");
						return $addedResult;
						break;
					case 3:
						$addedResult = database_helper::db_insert_query("UPDATE `groupusers` SET `privilege`=3 WHERE `userid`=(SELECT `id` FROM `users` WHERE `username`='" . $passedUsername . "') AND `groupid`=(SELECT `id` FROM `groups` WHERE `name`='" . $passedGroup . "')");
						return $addedResult;
						break;
				}
				break;
			case 2:
				//we are currently owner and that is changing
				switch($passedPrivilege)
				{
					case 0:
						//disabling account
						$addedResult = database_helper::db_insert_query("UPDATE `groupusers` SET `privilege`=0 WHERE `userid`=(SELECT `id` FROM `users` WHERE `username`='" . $passedUsername . "') AND `groupid`=(SELECT `id` FROM `groups` WHERE `name`='" . $passedGroup . "')");
						return $addedResult;
						break;//not reachable
					case 1:
						//user is a admin and getting normal rights
						$addedResult = database_helper::db_insert_query("UPDATE `groupusers` SET `privilege`=3 WHERE `userid`=(SELECT `id` FROM `users` WHERE `username`='" . $passedUsername . "') AND `groupid`=(SELECT `id` FROM `groups` WHERE `name`='" . $passedGroup . "')");
						return $addedResult;
						break;
					case 2:
						//do nothing, already set
						break;
					case 3:
						//user already has privilege
						break;
				}
				break;
			case 3:
				switch($passedPrivilege)
				{
					case 0:
						//disabling account
						$addedResult = database_helper::db_insert_query("UPDATE `groupusers` SET `privilege`=0 WHERE `userid`=(SELECT `id` FROM `users` WHERE `username`='" . $passedUsername . "') AND `groupid`=(SELECT `id` FROM `groups` WHERE `name`='" . $passedGroup . "')");
						return $addedResult;
						break;//not reachable
					case 1:
						//user has right
						break;
					case 2:
						//user has right
						break;
					case 3:
						//user already has privilege
						break;
				}
				break;
		}
		return -1;
	}
	public static function db_remove_priv($passedGroup, $passedUsername, $passedPrivilege, $passedCommandingUser)
	{
		$resultint = database_helper::db_group_privilege($passedGroup, $passedUsername);
		
		switch($resultint)
		{//switch depending what privilege the user currently has
			case -3:
				return -3;
				break;
			case -2:
				return -2;
				break;
			case -1:
				return -1;
				break;
			case 0:
				return 0;
				break;
			case 1:
				if ($passedPrivilege == 1)
				{
					$addedResult = database_helper::db_insert_query("UPDATE `groupusers` SET `privilege`=0 WHERE `userid`=(SELECT `id` FROM `users` WHERE `username`='" . $passedUsername . "') AND `groupid`=(SELECT `id` FROM `groups` WHERE `name`='" . $passedGroup . "')");
					return 0;
					break;//not reachable
				}
				break;
			case 2:
				switch($passedPrivilege)
				{
					//switching to the new privilege to be removed
					case 1:
						return 2;
						break;
					case 2:
						$addedResult = database_helper::db_insert_query("UPDATE `groupusers` SET `privilege`=0 WHERE `userid`=(SELECT `id` FROM `users` WHERE `username`='" . $passedUsername . "') AND `groupid`=(SELECT `id` FROM `groups` WHERE `name`='" . $passedGroup . "')");
						return 0;
						break;
				}
				break;
			case 3:
				switch($passedPrivilege)
				{
					//switching to the new privilege to be removed
					case 1:
						$addedResult = database_helper::db_insert_query("UPDATE `groupusers` SET `privilege`=2 WHERE `userid`=(SELECT `id` FROM `users` WHERE `username`='" . $passedUsername . "') AND `groupid`=(SELECT `id` FROM `groups` WHERE `name`='" . $passedGroup . "')");
						return 2;
						break;
					case 2:
						$addedResult = database_helper::db_insert_query("UPDATE `groupusers` SET `privilege`=1 WHERE `userid`=(SELECT `id` FROM `users` WHERE `username`='" . $passedUsername . "') AND `groupid`=(SELECT `id` FROM `groups` WHERE `name`='" . $passedGroup . "')");
						return 1;
						break;
				}
				break;
		}
	}
}

class timetracker {
	public function get_version()
	{
		return "0.1";
	}
	
	public function get_group_page($groupID)
	{
		
	}
	
	public function get_First_day($date)
	{
		$nowDate = $date;
		$startDate = strtotime("January 3, 2002");
		$endDate = $startDate;
		while ($nowDate > $endDate)
		{
		    $endDate += (60 * 60 * 24 * 14); // push two weeks ahead
		    if ($endDate <= $nowDate)
		    {
			$startDate = $endDate;
		    }
		}
		return $startDate;
	}
	
	public function draw_week()
	{
		If (isset($_REQUEST['date']))
		{
			$start_time = timetracker::get_First_day(strtotime($_REQUEST['date']));
		}else{
			$start_time = timetracker::get_First_day(time());
		}
		
		echo "<script></script>";
		echo "<div style='margin: auto;'>";
		echo "<form action='./ajax.php' method='post'><input type='hidden' name='type' value='timecardUpdate'>";
		echo "<input type='hidden' name='date' value='" . $start_time ."'>";
		for($i = 1; $i < 15; $i++)
		{
			echo "<div id='day" . $i . "' style='text-align: center; width: 14%; min-width: 150px; height: 100px; border-width: 1px; border-color: black; border-style: solid; display: inline-block;'>
				<h4 style='margin-bottom: 0px;'>" . date('m/d/Y', ($start_time + (60*60*24*($i - 1)))) . "</h4>
				<h5 style='margin-bottom: 0px; margin-top: 0px;'>" . date("l", ($start_time + (60*60*24*($i - 1)))) . "</h5>
				Hours: <input type='text' name='day' style='width: 50%; min-width: 60px;'></input>
				</div>";
		}
		echo "<input style='margin-left: 90%;' type='submit'/>";
		echo "</div></form>";
	}
	public function draw_week_js()
	{
		If (isset($_REQUEST['date']))
		{
			$start_time = timetracker::get_First_day(strtotime($_REQUEST['date']));
		}else{
			$start_time = timetracker::get_First_day(time());
		}
		
		echo "<script> start_time = '" . $start_time . "'; savedData = new Array(); </script>";
		echo "<div style='margin: auto;'>";
		echo "<div style='width: 100%; height: 40px; margin: auto;'><span style='width: 40px; border-width: 1px; border-style:solid; padding-right:10px;' onclick='lastweek();'><--</span>Saved Templates: <select style='width: 100px;'></select><button style='margin-left:25%;'>Save Time Card</button><span id='pageStatus' style='display: block-inline; height: 100%; width: 3%;'>Synced</span><span style='margin-left: 12%;'>Save As: <input type='text'/><input type='submit'></span><span style='width: 40px; border-width: 1px; border-style:solid; padding-right:10px;' onclick='nextweek();'>--></span></div>";
		echo "<input type='hidden' name='date' value='" . $start_time ."'>";
		for($i = 1; $i < 15; $i++)
		{
			echo "<div id='day" . $i . "' style='text-align: center; width: 14%; min-width: 150px; height: 650px; border-width: 1px; border-color: black; border-style: solid; display: inline-block;'>
				<h4 class='day" . $i . "Name'style='margin-bottom: 0px;'>" . date('m/d/Y', ($start_time + (60*60*24*($i - 1)))) . "</h4>
				<h5 style='margin-bottom: 0px; margin-top: 0px;'>" . date("l", ($start_time + (60*60*24*($i - 1)))) . "</h5>
				Hours: <input type='text' id='dayTotal" . $i . "' name='day' style='width: 50%; min-width: 60px;' disabled='disabled'></input>";
			for ($k = 0; $k < 24; $k++)
			{
				if ($k < 12)
				{
					switch ($k)
					{
						case 0:
							echo "<div>";
							echo "<span onclick=\"clockPunch('" . $i . "','" . $k . "','0');\" style='border-width: 1px; border-style: solid; border-right-width: 0px; font-size: 70%; width: 60px; height: 20px; display: inline-block;' id='hour_" . $i . "_" . $k . "_0'>12:00AM</span>";
							echo "<span onclick=\"clockPunch('" . $i . "','" . $k . "','2');\" style='border-width: 1px; border-style: solid; font-size: 70%; width: 60px; height: 20px; display: inline-block;' id='hour_" . $i . "_" . $k . "_2'>12:30AM</span>";
							echo "</div>";
							break;
						default:
							echo "<div>";
							echo "<span onclick=\"clockPunch('" . $i . "','" . $k . "','0');\" style='border-width: 1px; border-style: solid; border-right-width: 0px; font-size: 70%; width: 60px; height: 20px; display: inline-block;' id='hour_" . $i . "_" . $k . "_0'>$k:00AM</span>";
							echo "<span onclick=\"clockPunch('" . $i . "','" . $k . "','2');\" style='border-width: 1px; border-style: solid; font-size: 70%; width: 60px; height: 20px; display: inline-block;' id='hour_" . $i . "_" . $k . "_2'>$k:30AM</span>";
							echo "</div>";
							break;
					}
				}else{
					switch ($k)
					{
						case 12:
							echo "<div>";
							echo "<span onclick=\"clockPunch('" . $i . "','" . $k . "','0');\" style='border-width: 1px; border-style: solid; border-right-width: 0px; font-size: 70%; width: 60px; height: 20px; display: inline-block;' id='hour_" . $i . "_" . $k . "_1'>12:00PM</span>";
							echo "<span onclick=\"clockPunch('" . $i . "','" . $k . "','2');\" style='border-width: 1px; border-style: solid; font-size: 70%; width: 60px; height: 20px; display: inline-block;' id='hour_" . $i . "_" . $k . "_2'>12:30PM</span>";
							echo "</div>";
							break;
						default:
							echo "<div>";
							echo "<span onclick=\"clockPunch('" . $i . "','" . $k . "','0');\" style='border-width: 1px; border-style: solid; border-right-width: 0px; font-size: 70%; width: 60px; height: 20px; display: inline-block;' id='hour_" . $i . "_" . $k . "_1'>" . ($k - 12) . ":00PM</span>";
							echo "<span onclick=\"clockPunch('" . $i . "','" . $k . "','2');\" style='border-width: 1px; border-style: solid; font-size: 70%; width: 60px; height: 20px; display: inline-block;' id='hour_" . $i . "_" . $k . "_2'>" . ($k - 12) . ":30PM</span>";
							echo "</div>";
							break;
					}
					
				}
				
				
			}
			
			echo "</div>";
		}
		echo "</div></form>";
	}
}

?>