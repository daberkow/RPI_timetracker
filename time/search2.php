<?PHP

    if(isset($_REQUEST['q']))
    {
        $url = "http://rpidirectory.appspot.com/api?q=" . urlencode($_REQUEST['q']);
        $ch = curl_init();
        $timeout = 3;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER,array('Connection: close\r\n'));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        
        echo $data;
    }

?>