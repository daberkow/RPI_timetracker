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
            //stopped here
            print_r($Lines);
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