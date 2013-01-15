<?PHP
	// Dan Berkowitz, berkod2@rpi.edu, dansberkowitz@gmail.com, January 2013
	include_once './cas/CAS.php';
	
	phpCAS::client(CAS_VERSION_2_0,'cas-auth.rpi.edu',443,'/cas/');
	
	// SSL!
	phpCAS::setCasServerCACert("../cas/cas-auth.rpi.edu");
	
	if (phpCAS::isAuthenticated())
	{
		phpCAS::logout();
	}
	header( 'Location: ./index.php' );
?>