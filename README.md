RPI_timetracker
===============

Time Tracking

Parts that arent finished:
    Email reminders
    Direct send to payrole
    Delete Template
    Key setup to share info
    Names
    Print out color cells
    
Back Burner:
    Basic Version

Version 1.9 Table Update
    CREATE TABLE IF NOT EXISTS `email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `group` int(11) NOT NULL,
  `setting` tinyint(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

version 0.2.0 table update
ALTER TABLE  `users` ADD  `fname` VARCHAR( 35 ) NOT NULL AFTER  `id` ,
ADD  `lname` VARCHAR( 35 ) NOT NULL AFTER  `fname`

Patch for pre 1.9.4 hour error
PHP
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