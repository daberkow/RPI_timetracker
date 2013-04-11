<?php

class LDAP {
    public static function LDAPUIDSEARCH($passedname)
    {
        $LDAPCON = ldap_connect("ldap.rpi.edu");  //Have to be internal to VCC or VCC firewall will block
        $LDAPBIND = ldap_bind($LDAPCON);
        $ResultArray = Array();
        $filterArray = array("givenname", "sn");
        $LDAPSEARCH = ldap_search($LDAPCON, "dc=rpi, dc=edu", "(uid=" . $passedname . ")", $filterArray, 0 , 10);
        $LDAPRESULTS = ldap_get_entries($LDAPCON, $LDAPSEARCH);
        //print_r($LDAPRESULTS);
        for ($i = 0; $i < $LDAPRESULTS["count"]; $i++)
        {
            $tempRow = Array();
            array_push($tempRow, $LDAPRESULTS[$i]["givenname"][0]);
            array_push($tempRow, $LDAPRESULTS[$i]["sn"][0]);
            array_push($ResultArray, $tempRow);
        }
        ldap_close($LDAPCON);
        return $ResultArray;
    }
}

    if (isset($_REQUEST['fname']) && isset($_REQUEST['lname']))
    {
        if ($LDAPCON)//Valid Connection
        {
                //Connection
            $LDAPCON = ldap_connect("ldap.rpi.edu");  //Have to be internal to VCC or VCC firewall will block
            $LDAPBIND = ldap_bind($LDAPCON);
        
            $ResultArray = Array();
            $filterArray = array("uid");
            $LDAPSEARCH = ldap_search($LDAPCON, "dc=rpi, dc=edu", "(&(givenName=*" . $_REQUEST['fname'] . "*)(sn=*" . $_REQUEST['lname'] . "*))", $filterArray, 0, 10);
            $LDAPRESULTS = ldap_get_entries($LDAPCON, $LDAPSEARCH);
            //print_r($LDAPRESULTS);
            for ($i = 0; $i < $LDAPRESULTS["count"]; $i++)
            {
                $tempRow = Array();
                array_push($tempRow, $LDAPRESULTS[$i]["uid"][0]);
                array_push($ResultArray, $tempRow);
            }
            ldap_close($LDAPCON);
            echo json_encode($ResultArray);
        }else{
            echo "Error Connecting";
        }
    }else{
        if(isset($_REQUEST['uid']))
        {
            echo json_encode(LDAP::LDAPUIDSEARCH($_REQUEST['uid']));
        }else{
            echo "Either pass 'lname' and 'fname' for last and first name, or uid";
        }
    }


    
?>
