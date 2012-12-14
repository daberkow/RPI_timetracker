<?PHP

    if(isset($_REQUEST['q']))
    {
        $context = stream_context_create(array('http' => array('header'=>'Connection: close\r\n')));
        echo file_get_contents("http://rpidirectory.appspot.com/api?q=" . urlencode($_REQUEST['q']),false,$context);
    }

?>