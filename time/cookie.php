<?PHP
    if (isset($_REQUEST['action']))
    {
        switch($_REQUEST['action'])
        {
            case 'make':
                setcookie('timeDev', $_REQUEST['username']);
                echo '<h4>Cookie Writen</h4>';
                break;
            case 'delete':
                setcookie('timeDev', '', 0);
                echo '<h4>Cookie Deleted</h4>';
                break;
        }
    }

?>

<html>
    <head>
        <title>Dev Mode Cookie Maker</title>
    </head>
    <body>
        <h3>Dev Cookie maker</h3>
        <form action='./cookie.php'>
            <input type='hidden' name='action' value='make'/>
            Username:<input name='username' type='text'/>
            <input type='submit'>
        </form>
        <h4>delete cookie</h4>
        <form action='./cookie.php'>
            <input type='hidden' name='action' value='delete'/>
            <input type='submit'>
        </form>
    </body>
</html>