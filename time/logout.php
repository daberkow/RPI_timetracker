<?PHP
	// Dan Berkowitz, berkod2@rpi.edu, dansberkowitz@gmail.com, January 2013
	include('./core.php');
	
	if (phpCAS::isAuthenticated())
	{
		phpCAS::logout();
	}
	header( 'Location: ./index.php' );
?>