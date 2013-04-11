<?PHP
    include('./core.php');
    $pull = database_helper::db_return_array("SELECT * FROM `timedata` WHERE `status`=1");
    for ($i = 0; $i < sizeof($pull); $i++)
    {
        if (!strstr($pull[$i]['stopTime'], "00:00:00"))
        {
            //echo "fine";
        }else{
            $fix = "";
            if (substr($pull[$i]['startTime'], 14, 2) == "00")
            {
                $fix = "29";
            }else{
                $fix = "59";
            }
            echo "fix " . $pull[$i]['stopTime'] . " " . substr_replace($pull[$i]['startTime'], $fix,14, 2) . "|";
            database_helper::db_insert_query("UPDATE  `timetracker`.`timedata` SET  `stopTime`='" . substr_replace($pull[$i]['startTime'], $fix,14, 2) . "' WHERE  `timedata`.`id`='" . $pull[$i]['id'] . "';");
        }
        echo " ";
    }
    //print_r($pull[0]);
?>