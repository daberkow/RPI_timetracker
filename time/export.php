<?PHP
    include("./core.php");
    
    database_helper::db_connect();
    function check_item($item, $Table, $name)
    {
        foreach($Table as $row)
        {
            if ($item == $row['id'])
            {
                return true;
            }
        }
        //echo $item . " Not in ". $name;
        return false;
    }

    function add_users($Lines, $i)
    {
        $UserTable = database_helper::db_return_array("SELECT * FROM `users`");
        $UsersDB = array();
        foreach($UserTable as $row)
        {
            $UsersDB[$row['username']] = $row['privilege'];
        }
        $UserTable = null;
        $k = 0;
        for($k = $i; $k < sizeof($Lines); $k++)
        {
            if($Lines[$k][0] == '[')
                return $k;
            
            //10,Michael James,Oatman,oatman,1,0
            $splity = explode(',',$Lines[$k]);
            if (sizeof($splity) == 6)
            {
                if (isset($UsersDB[$splity[3]]))
                {
                    //user does exist check privileges
                    $privilege = database_helper::db_user_privilege($splity[3]);
                    if (intval($privilege) < intval($splity[4]))
                    {
                        db_add_user_group("home", $splity[3], $splity[4], phpCAS::getUser());
                    }
                }else{
                    //no user ad them
                    $privilege = database_helper::db_user_privilege(phpCAS::getUser());
		    database_helper::db_add_user_system($splity[3], $splity[4], $privilege);
                }
            }
        }
        return $k;
    }
    
    function add_Group($Lines, $i)
    {
        $Grouptable = database_helper::db_return_array("SELECT * FROM  `groups`");
        $GroupDB = array();
        foreach($Grouptable as $row)
        {
            $GroupDB[$row['name']] = $row['page'];
        }
        $Grouptable = null;
        $k = 0;
        for($k = $i; $k < sizeof($Lines); $k++)
        {
            if($Lines[$k][0] == '[')
                return $k;
            
            //[Groups]
            $splity = explode(',',$Lines[$k]);
            if (sizeof($splity) == 3)
            {
                if (!isset($GroupDB[$splity[1]]))
                {
                    //no group
                    $permission = database_helper::db_user_privilege(phpCAS::getUser());
                    if (intval($permission) >= 1)
                    {
                        $page = get_page($Lines, $splity[2]);
                        $result = database_helper::db_insert_query("INSERT INTO  `timetracker`.`pages` (`id` ,`page`,`data`) VALUES (NULL ,  'group',  \"" . $page . "\");");
                        $group = database_helper::db_insert_query("INSERT INTO  `timetracker`.`groups` (`id` ,`name`,`page`) VALUES (NULL ,  '" . $splity[1] . "',  '" . $result . "');");
                    }
                }
            }
        }
        return $k;
    }
    
    function get_page($Datafile, $pageID)
    {
        $validArea = false;
        for($j = 0; $j < sizeof($Datafile); $j++)
        {
            if ($validArea)
            {//actively looking for page
                if ($Datafile[$j][0] == '[')
                    return "";
                //found no pages before page area ended
                
                $newsplit = explode(",", $Datafile[$j]);
                if (intval($newsplit[0]) == intval($pageID))
                    return $newsplit[2];
            }else{
                //yet to hit the page area
                if ($Datafile[$j] == "[Pages]")
                    $validArea = true;
            }
        }
    }
    
    function find_referenced_user($Datafile, $lineRef)
    {
        $validArea = false;
        for($j = 0; $j < sizeof($Datafile); $j++)
        {
            if ($validArea)
            {//actively looking for page
                if ($Datafile[$j][0] == '[')
                    return "";
                //found no pages before page area ended
                
                $newsplit = explode(",", $Datafile[$j]);
                if (intval($newsplit[0]) == intval($lineRef))
                    return $newsplit[3];
            }else{
                //yet to hit the page area
                if ($Datafile[$j] == "[Users]")
                    $validArea = true;
            }
        }
    }
    
    function find_referenced_group($Datafile, $lineRef)
    {
        $validArea = false;
        for($j = 0; $j < sizeof($Datafile); $j++)
        {
            if ($validArea)
            {//actively looking for page
                if ($Datafile[$j][0] == '[')
                    return "";
                //found no pages before page area ended
                
                $newsplit = explode(",", $Datafile[$j]);
                if (intval($newsplit[0]) == intval($lineRef))
                    return $newsplit[1];
            }else{
                //yet to hit the page area
                if ($Datafile[$j] == "[Groups]")
                    $validArea = true;
            }
        }
    }
    
    function add_Templates($Lines, $start)
    {
        for($j = $start; $j < sizeof($Lines); $j++)
        {
            if($Lines[$j][0] == '[')
                return $j;
            
            //2,5_12_0,4_1_2,4_2_2,4_4_0,4_5_0,3_14_2,3_13_0,4_4_2,11_0_2,11_1_2,3_1_2,,Dan,2,1
            $splity = explode(',',$Lines[$j]);
            $concat = "";
            for($z = 1; $z < (sizeof($splity) - 4); $z++)
            {
                $concat = $concat . $splity[$z];
            }
            $query = "INSERT INTO `timetracker`.`templates` (`id`, `data`, `name`, `owner`, `status`) VALUES (NULL, '" . $concat . "', '" . $splity[sizeof($splity) - 3] . "', (SELECT `id` FROM `users` WHERE `username`='" . find_referenced_user($Lines, $splity[sizeof($splity) - 2]) . "'), '" . $splity[sizeof($splity) - 1] . "');";
            database_helper::db_insert_query($query);
        }
    }
    
    function add_timedata($Lines, $spot)
    {
        for($j = $spot; $j < sizeof($Lines); $j++)
        {
            if($Lines[$j][0] == '[')
                return $j;
            
            //1,2,2013-01-06 00:00:00,2013-01-06 00:29:00,2013-01-15 04:10:03,0,1
            //id,user,startTime,stoptime,subtmitted,group,status
            $splity = explode(',',$Lines[$j]);
            
            $query = "INSERT INTO `timetracker`.`timedata` (`id`, `user`, `startTime`, `stopTime`, `submitted`, `status`, `group`) VALUES (NULL, (SELECT `id` FROM `users` WHERE `username`='" . find_referenced_user($Lines, $splity[1]) . "'), '" . $splity[2] . "', '" . $splity[3] . "', " . $splity[4] . "," . $splity[6] . ",(SELECT `id` FROM `groups` WHERE `name`='" . find_referenced_group($Lines, $splity[5]) . "'));";
	    $result = database_helper::db_insert_query($query);
        }
    }
    
    function add_groupusers($Lines, $spot)
    {
        for($j = $spot; $j < sizeof($Lines); $j++)
        {
            if($Lines[$j][0] == '[')
                return $j;
            
            //2,2,1,3
            //id,user,groupid, priv
            $splity = explode(',',$Lines[$j]);
        
            $resultint = database_helper::db_group_privilege(find_referenced_group($Lines, $splity[2]), find_referenced_user($Lines, $splity[1]));
            $passedUsername = find_referenced_user($Lines, $splity[1]);
            $passedGroup = find_referenced_group($Lines, $splity[2]);
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
                            database_helper::db_add_user_system($passedUsername, 1, 2);
                            $addedResult = database_helper::db_insert_query("INSERT INTO `groupusers`(`userid`,`groupid`, `privilege`) VALUES ((SELECT `id` FROM `users` WHERE `username`='" . $passedUsername . "'),(SELECT `id` FROM `groups` WHERE `name`='" . $passedGroup . "'), " . $splity[3] . ");");
                            return $addedResult;
                            break;
                    case -1:
                            //user exists but not in group
                            $addedResult = database_helper::db_insert_query("INSERT INTO `groupusers`(`userid`,`groupid`, `privilege`) VALUES ((SELECT `id` FROM `users` WHERE `username`='" . $passedUsername . "'),(SELECT `id` FROM `groups` WHERE `name`='" . $passedGroup . "'), " . $splity[3] . ");");
                            return $addedResult;
                            break;
                    case 0:
                            //expired user
                            if (intval($splity[3]) > 0)
                            {//moving them back to active
                                    $addedResult = database_helper::db_insert_query("UPDATE `groupusers` SET `privilege`=" . $splity[3] . " WHERE `userid`=(SELECT `id` FROM `users` WHERE `username`='" . $passedUsername . "') AND `groupid`=(SELECT `id` FROM `groups` WHERE `name`='" . $passedGroup . "')");
                                    return $addedResult;
                            }
                            break;
                    case 1:
                            //the user has standard users rights and is either being given new right or disabled
                            switch(intval($splity[3]))
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
                            switch(intval($splity[3]))
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
                            switch(intval($splity[3]))
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
        }
    }
    
    function check_database()
    {
        $Error = array();
        
        $UserTable = database_helper::db_return_array("SELECT * FROM  `users`");
        $Grouptable = database_helper::db_return_array("SELECT * FROM  `groups`");
        $PagesTable = database_helper::db_return_array("SELECt * FROM `pages`");
        foreach($Grouptable as $Row)
        {
            if ($Row['page'] == '0')
            {
                array_push($Error, array("Group", $Row['id']));
            }else{
                if (!check_item($Row['page'], $PagesTable, "Pages"))
                {
                    array_push($Error, array("Page", $Row['id']));
                }
            }
        }
        $Groupusers = database_helper::db_return_array("SELECT * FROM  `groupusers`");
        foreach($Groupusers as $Row)
        {
            $errorlvl = 0;
            if (!check_item($Row['userid'], $UserTable, "Users"))
            {
                array_push($Error, array("GroupUsers_User", $Row['id']));
                $errorlvl++;
            }
             if (!check_item($Row['groupid'], $Grouptable, "Groups"))
            {
                array_push($Error, array("GroupUsers_Group", $Row['id']));
                $errorlvl++;
            }
            if (!(intval($Row['privilege']) >= 0 && intval($Row['privilege']) <= 3))
            {
                array_push($Error, array("GroupUsers_Privilege", $Row['id']));
                $errorlvl++;
            }
            if ($errorlvl > 0)
            {
                $Row['id'] = "kill";
            }
        }
        $Templates = database_helper::db_return_array("SELECT * FROM  `templates`");
        foreach($Templates as $Row)
        {
            $errorlvl = 0;
            foreach (explode(",", $Row['data']) as $datasection)
            {
                
                if ($datasection != "")
                {
                    if (sizeof( explode("_", $datasection)) != 3)
                    {
                        array_push($Error, array("Templates", $Row['id']));
                        $errorlvl++;
                    }
                }
            }
            if (!check_item($Row['owner'], $UserTable, "Users"))
            {
                array_push($Error, array("Templates_User", $Row['id']));
                $errorlvl++;
            }
            if ($errorlvl > 0)
            {
                //echo $datasection;
                $Row['id'] = "kill";
            }
        }
        $timedata = database_helper::db_return_array("SELECT * FROM `timedata`");
        foreach($timedata as $Row)
        {
            $errorlvl = 0;
            if (!check_item($Row['user'], $UserTable, "Users"))
            {
                array_push($Error, array("Timedata_User", $Row['id']));
                $errorlvl++;
            }
            if (!check_item($Row['group'], $Grouptable, "Group"))
            {
                array_push($Error, array("Timedata_Group", $Row['id']));
                $errorlvl++;
            }
            if ($errorlvl > 0)
            {
                //echo $datasection;
                $Row['id'] = "kill";
            }
        }
        
        $Final = array();
        array_push($Final, $Error);
        array_push($Final, $UserTable);
        array_push($Final, $PagesTable);
        array_push($Final, $Templates);
        array_push($Final, $Grouptable);
        array_push($Final, $Groupusers);
        array_push($Final, $timedata);
        return $Final;
    }
?>
<?PHP
    if(!isset($_REQUEST['skip']))
    {
        if (isset($_REQUEST['import']))
        {
            $Data = file_get_contents($_FILES['file']['tmp_name']);
            $Lines = explode("\n", $Data);
            
            //Done    : Groups, pages, users, tmepaltes
            //Not Done: timedata, group users1
            $GroupsL = 0;
            $UsersL = 0;
            $TemplatesL = 0;
            $TimeDataL = 0;
            $GroupusersL = 0;
            for ($i = 0; $i < sizeof($Lines); $i++)
            {
                switch($Lines[$i])
                {
                    case "[Users]":
                        $UsersL = $i;
                        break;
                    case "[Groups]"://does pages also
                        $GroupsL = $i;
                        break;
                    case "[Templates]":
                        $TemplatesL = $i;
                        break;
                    case "[TimeData]":
                        $TimeDataL = $i;
                        break;
                    case "[GroupUsers]":
                        $GroupusersL = $i;
                        break;
                    
                }
            }
            
            add_users($Lines, $UsersL);
            add_Group($Lines, $GroupsL);
            add_Templates($Lines, $TemplatesL);
            add_timedata($Lines, $TimeDataL);
            add_groupusers($Lines, $GroupusersL);
            //print_r($Lines);
        }else{
            if (isset($_REQUEST['error']))
            {
                $Check = check_database();
                //echo "Total Errors " . sizeof($Check[0]);
                print_r($Check[0]);
            }else{
                $Check = check_database();
                echo "Total Errors " . sizeof($Check[0]);
                //echo "<h3>Begining Export</h3>";
                //echo "<a href='./export.php?skip=true'>Download</a>";
            }
        }
    }else{
        //actually export here
        header("Content-type: application/txt; ");
        header("Content-Disposition: attachment; filename=\"Timetracker_export.time\"");      
        $Check = check_database();
        /* [$Error,$UserTable,$PagesTable,$Templates,$Grouptable,$Groupusers,$timedata]*/
        
        //start writing to file, if id is kill dont export, do users, groups, pages, groupusers, templates, timedata
        echo ("[Users]\n");
        foreach($Check[1] as $User)
        {
            if($User['id'] != "kill")
            {
                echo $User['id'] . "," . $User['fname'] . "," . $User['lname'] . "," . $User['username'] . "," . $User['privilege'] . "," . $User['defaultgroup'] . "\n";
            }
        }
        echo "[Pages]\n";
        foreach($Check[2] as $Page)
        {
            if($Page['id'] != "kill")
            {
                echo $Page['id'] . "," . $Page['page'] . "," . $Page['data'] . "\n";
            }
        }
        echo "[Templates]\n";
        foreach($Check[3] as $temp)
        {
            if($temp['id'] != "kill")
            {
                echo $temp['id'] . "," . $temp['data'] . "," . $temp['name'] . "," . $temp['owner'] . "," . $temp['status'] . "\n";
            }
        }
        echo "[Groups]\n";
        foreach($Check[4] as $Group)
        {
            if($Group['id'] != "kill")
            {
                echo $Group['id'] . "," . $Group['name'] . "," . $Group['page'] . "\n";
            }
        }
        echo "[GroupUsers]\n";
        foreach($Check[5] as $GroupU)
        {
            if($GroupU['id'] != "kill")
            {
                echo $GroupU['id'] . "," . $GroupU['userid'] . "," . $GroupU['groupid'] . "," . $GroupU['privilege'] . "\n";
            }
        }
        echo "[TimeData]\n";
        foreach($Check[6] as $Time)
        {
            if($Time['id'] != "kill")
            {
                echo $Time['id'] . "," . $Time['user'] . "," . $Time['startTime'] . "," . $Time['stopTime'] . "," . $Time['submitted'] . "," . $Time['group'] . "," . $Time['status'] . "\n";
            }
        }
    }
?>