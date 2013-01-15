<?PHP
	// Dan Berkowitz, berkod2@rpi.edu, dansberkowitz@gmail.com, January 2013

	include_once './cas/CAS.php';
	
	phpCAS::client(CAS_VERSION_2_0,'cas-auth.rpi.edu',443,'/cas/');
	
	// SSL!
	phpCAS::setCasServerCACert("./cas/cas-auth.rpi.edu");
		
	//If not authenticated then do it
	if (!(phpCAS::isAuthenticated()))
	{
		phpCAS::forceAuthentication();
	}else{
		//We are authenticated, but we may not be in the users database		
		include('./core.php');
		$user = database_helper::db_return_row("SELECT * FROM `users` WHERE `username`='" . phpCAS::getUser() ."' LIMIT 0,1");
		
		if (sizeof($user) > 0)
		{	//user is in the system
			
		}else{
			//user is a RPI user but not in system
			database_helper::db_insert_query("INSERT INTO `users`(`username`, `privilege`) VALUES ('" . phpCAS::getUser() ."', 1);");
		}
	}
	header("location: ./index.php");
?>