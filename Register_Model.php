<?php


/**
 *
 */

require_once("Storage_Model.php");
require_once("Form_Model.php");

use Storage_Model as Storage;
use Form_Model as Form;

class Register_Model
{
    /**
     *
     */
    protected $storage; 
    private $url;

    public function __construct($url = null)
    {
        $this->url = $url;
        $form_model = new Form;
        if(isset($_POST["register"]) && $_POST["register"] === "Register")
            if(isset($_POST["token"]) && $form_model->check_token($_POST["token"]))
                if(isset($_POST["username"], $_POST["email"], $_POST["password"]) && !empty($_POST["username"]) && !empty($_POST["email"]) && !empty($_POST["password"]))
                {
                    if(strlen(trim($_POST["username"])) > 5)
                    {
                        $this->sanitize_validate(trim($_POST["username"]), trim($_POST["password"]), trim($_POST["email"]));
                    } else {
                        print("Username must contain at least 6 characters");
                    }
                } else {
                    print("Please complete all the fields.");
                }

        $form_model->create_register_form(filter_var($this->url, FILTER_SANITIZE_STRING));
    }

    /**
     * @param void $username
     * @param void $password
     * @param void $email
     */
    private function sanitize_validate($username, $password, $email)
    {
        $username = filter_var($username, FILTER_SANITIZE_STRING);
        $password = filter_var($password, FILTER_SANITIZE_STRING);
        $email = filter_var($email, FILTER_SANITIZE_STRING, FILTER_VALIDATE_EMAIL);
        $this->register($username, $password, $email);
    }

    /**
     * @param void $username
     * @param void $password
     * @param void $email
     */
    public function register($username, $password, $email)
    {
        $storage = new Storage;
        $storage->store($username, $password, $email);
    }
}

?>
