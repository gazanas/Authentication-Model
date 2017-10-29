<?php
session_start();

require_once("Session_Model.php");

use Session_Model as Session;

/**
 *
 */
class Form_Model
{
    /**
     *
     */


    public function __construct()
    {
        Session::session_timeout();
    }

    /**
     *
     */
    public function generate_token()
    {
        $_SESSION["token"] = bin2hex(openssl_random_pseudo_bytes(16));
        return $_SESSION["token"];
    }

    /**
     * @param void $token
     */
    public function check_token($token)
    {
        if(strcmp($token, $_SESSION["token"]) == 0)
        {    
            unset($_SESSION["token"]);
            return true;
        }
        return false;
    }

    /**
     *
     */
    public function create_register_form()
    {
        $this->generate_token();
        echo <<<HTML
            <form action="" method="POST">
                <table>
                    <tr>
                        <th>Username</th>
                        <th><input type="text" name="username"></th>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <th><input type="email" name="email"></th>
                    </tr>
                    <tr>
                        <th>Password</th>
                        <th><input type="password" name="password"></th>
                        <th><input type="hidden" name="token" value="{$_SESSION['token']}"></th>
                    </tr>
                    <tr>
                        <th></th>
                        <th><input type="submit" name="register" value="Register"></th>
                    </tr>
                </table> 
            </form> 
HTML;
    }

    public function create_login_form($url)
    {
        $this->generate_token();
        if(isset($_SESSION["username"]))
        {
            if(isset($url) && !empty($url))
            {
                header("Location: ".$url);
            } else {
                print($_SESSION["username"]."  <a href='?logout=true'>Logout</a>");
            }
        }
        echo <<<HTML
            <form action="" method="POST">
                <table>
                    <tr>
                        <th>Username</th>
                        <th><input type="text" name="username"></th>
                    </tr>
                    <tr>
                        <th>Password</th>
                        <th><input type="password" name="password"></th>
                        <th><input type="hidden" name="token" value="{$_SESSION['token']}"></th>
                    </tr>
                    <tr>
                        <th></th>
                        <th><input type="submit" name="login" value="Login"></th>
                    </tr>
                </table> 
            </form> 
HTML;
    }

    public function create_update_form($url)
    {
        $this->generate_token();
        if(isset($url) && !empty($url))
        {
            header("header: ".$url);
        }
        echo <<<HTML
            <form action="" method="POST">
                <table>
                    <tr>
                        <th>Username</th>
                        <th><input type="text" name="username"></th>
                    </tr>
                    <tr>
                        <th>Old Password</th>
                        <th><input type="password" name="old-password"></th>
                    </tr>
                    <tr>
                        <th>New Password</th>
                        <th><input type="password" name="new-password"></th>
                        <th><input type="hidden" name="token" value="{$_SESSION['token']}"></th>
                    </tr>
                    <tr>
                        <th></th>
                        <th><input type="submit" name="update" value="Update"></th>
                    </tr>
                </table> 
            </form> 
HTML;
    }

}

?>