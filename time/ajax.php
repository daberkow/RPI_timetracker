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
            default:
                echo "No Type given";
                break;
        }
    }else{
	echo "error: not authenticted user";
    }
?>