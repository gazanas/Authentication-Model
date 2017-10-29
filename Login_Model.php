<?php


/**
 *
 */
require_once("Form_Model.php");
require_once("Session_Model.php");
require_once("Password_Model.php");

use Session_Model as Session;
use Form_Model as Form;
use Password_Model as Password;
use SQL_Model as SQL;

class Login_Model
{

    private $url;
    private $update;
    /**
     *
     */
    public function __construct($update, $url = NULL)
    {
        $this->url = $url;
        $this->update = $update;
        $form_model = new Form;
        if(isset($_POST["login"]) && $_POST["login"] === "Login")
            if(isset($_POST["token"]) && $form_model->check_token($_POST["token"]))
                if(isset($_POST["username"], $_POST["password"]) && !empty($_POST["username"]) && !empty($_POST["password"]))
                {
                    $this->sanitize_validate(trim($_POST["username"]), trim($_POST["password"]), filter_var($this->update, FILTER_SANITIZE_STRING));
                } else {
                    print("Please complete all the fields.");
                }
        if(isset($_GET["logout"]) && $_GET["logout"] === "true")
        {
            Session::logout();
        }
        $form_model->create_login_form(filter_var($this->url, FILTER_SANITIZE_STRING));
    }

    /**
     * @param void $username
     * @param void $password
     */
    private function sanitize_validate($username, $password, $update)
    {
        $username = filter_var($username, FILTER_SANITIZE_STRING);
        $password = filter_var($password, FILTER_SANITIZE_STRING);
        try {
            $this->check_existing_user($username, $password, $update);
        } catch(Exception $e)
        {
            print($e->getMessage());
        }
    }

    /**
     * @param void $username
     */
    private function check_existing_user($username, $password, $update)
    {
        $sql = new SQL;
        if(empty($sql->getID($username)))
        {
            throw new Exception("User doesn't exist.");
        } else {
            $hash = $sql->getPassword($username);
            $this->login($username, $password, $hash, $update);
        }
    }

    public function login($username, $password, $hash, $update)
    {
        try {
            if(Password::tries_timeout($username))
            {
                if(Password::check_time_to_live($username, $update))
                {
                    if(password_verify($password, $hash))
                    {
                        $_SESSION["username"] = $username;
                        Password::initialize_tries($username);
                    } else {
                        Password::increment_tries($username);
                        throw new Exception("Wrong password for ".$username);
                    }
                }
            }
        } catch(Exception $e)
        {
            print($e->getMessage());
        }
    }
}
?>