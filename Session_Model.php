<?php


/**
 *
 */
class Session_Model
{
    /**
     *
     */
    public function __construct()
    {
    }

    public static function session_timeout()
    {
    	if(!isset($_SESSION["timeout"]))
        {
            session_regenerate_id(true);
            $_SESSION["timeout"] = time();
        }

        //Regenerate session id every 15 minutes
        if($_SESSION["timeout"] < time() - 900)
        {
            session_regenerate_id(true);
            unset($_SESSION["timeout"]);
            $_SESSION["timeout"] = time();
        }
    }


    public static function logout()
    {
    	session_unset();
    	session_destroy();
    	header("Location: ".$_SERVER["HTTP_REFERER"]);
    }

}
