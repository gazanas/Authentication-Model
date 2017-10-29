<?php


/**
 *
 */
require_once("Form_Model.php");
require_once("Password_Model.php");

use Form_Model as Form;
use Password_Model as Password;
use SQL_Model as SQL;

class Update_Model
{
    /**
     *
     */
    public function __construct($url = null)
    {
        $this->url = $url;
        $form_model = new Form;
        if(isset($_POST["update"]) && $_POST["update"] === "Update")
            if(isset($_POST["token"]) && $form_model->check_token($_POST["token"]))
                if(isset($_POST["username"], $_POST["old-password"], $_POST["new-password"]) && !empty($_POST["username"]) && !empty($_POST["old-password"]) && !empty($_POST["new-password"]))
                {
                    $this->sanitize_validate(trim($_POST["username"]), trim($_POST["old-password"]), trim($_POST["new-password"]));
                } else {
                    print("Please complete all the fields.");
                }
        $form_model->create_update_form(filter_var($this->url = $url, FILTER_SANITIZE_STRING));
    }

    /**
     * @param void $username
     * @param void $password
     */
    private function sanitize_validate($username, $old_password, $new_password)
    {
        $username = filter_var($username, FILTER_SANITIZE_STRING);
        $old_password = filter_var($old_password, FILTER_SANITIZE_STRING);
        $new_password = filter_var($new_password, FILTER_SANITIZE_STRING);
        try{
            $this->update($username, $old_password, $new_password);
        } catch(Exception $e)
        {
            print($e->getMessage());
        }
    }
    /**
     * @param void $username
     */
    private function check_existing_user($username, $old_password, $new_password)
    {
        $sql = new SQL;
        if(empty($sql->getID($username)))
        {
            throw new Exception("User doesn't exist.");
            return false;
        } else {
           return true;
        }
    }

    public function update($username, $old_password, $new_password)
    {
        try {
            self::check_existing_user($username, $old_password, $new_password);
            Password::update_password($username, $old_password, $new_password);
        } catch(Exception $e)
        {
            print($e->getMessage());
        }
    }
}

?>