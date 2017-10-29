<?php

require_once("SQL_Model.php");
use SQL_Model as SQL;
/**
 *
 */
class Storage_Model
{
    protected $model;
    protected $errors;

    public function __construct() 
    {
        //All defaults (no protection)
        $this->model = array(
            "minimum length" => 0, //Minimum Length of password
            "numerics count" => 0, //Minimum number of numerics in a password
            "alphanumerics count" => 0, //Minimum number of letters in a password
            "uppercase count" => 0, //Minimum number of uppercase letters in a password
            "lowercase count" => 0, //Minimum number of lowercase letters in a password
            "special count" => 0, //Minimum number of special characters in a password
            "common password count" => 0, //Number of most common passwords to check
            "recurring characters" => 0, //Maximum number of recurring characters or numbers. Default is 0 that means the user is allowed to repeat infinite same characters
            "time to live" => 3652, //Minimum days to force user to change his password. Default 10 years or 3652 days
            "tries to timeout" => 0, //After how many tries to login the account is locked. Default is 0 that means the user is allowed to try infinite times
            "time to unlock" => 0, //Minutes to unlock account after it's been locked out
            "hashing iterations" => 4 //Iterations of blowfish encryption algorithm. Least iterations allowed is 4
        );

        $this->errors = array(
            "length error" => "The length of the password must be at least ".$this->model["minimum length"].".",
            "numeric error" => "Password must contain at least ".$this->model["numerics count"]." numbers.",
            "alphanumeric error" => "Password must contain at least ".$this->model["alphanumerics count"]." characters.",
            "uppercase error" => "Password must contain at least ".$this->model["uppercase count"]." uppercase letters.",
            "lowercase error" => "Password must contain at least ".$this->model["lowercase count"]." uppercase letters.",
            "special error" => "Password must contain at least ".$this->model["special count"]." special characters.",
            "common error" => "Password is in the ".$this->model["common password count"]." most common passwords."
        );
    }


    public function get_variables()
    {
        return $this->model;
    }

    /**
     * @param void $password
     */
    public function check_length($password)
    {
        if(strlen($password) >= $this->model["minimum length"])
        {
            return true;
        } else {
            throw new Exception($this->errors["length error"]);
            return false;
        }
    }

    /**
     * @param void $password
     */
    public function check_numerics($password)
    {
        if(preg_match_all("/[0-9]/", $password) >= $this->model["numerics count"])
        {
            return true;
        } else {
            throw new Exception($this->errors["numeric error"]);
            return false;
        }
    }

    /**
     * @param void $password
     */
    public function check_alphanumerics($password)
    {
        if(preg_match_all("/[a-zA-Z]/", $password) >= $this->model["alphanumerics count"])
        {
            return true;
        } else {
            throw new Exception($this->errors["alphanumeric error"]);
            return false;
        }
    }

    public function check_uppercase($password)
    {
        if(preg_match_all("/(?=[A-Z])/", $password) >= $this->model["uppercase count"])
        {
            return true;
        } else {
            throw new Exception($this->errors["uppercase error"]);
            return false;
        }
    }


    public function check_lowercase($password)
    {
        if(preg_match_all("/(?=[a-z])/", $password) >= $this->model["lowercase count"])
        {
            return true;
        } else {
            throw new Exception($this->errors["lowercase error"]);
            return false;
        }
    }

    /**
     * @param void $password
     */
    public function check_special($password)
    {
        if(preg_match_all("/(?=[~!^(){}<>%@#&*+=_-])/", $password) >= $this->model["special count"])
        {
            return true;
        } else {
            throw new Exception($this->errors["special error"]);
            return false;
        }
    }

    /**
     * @param void $password
     */
    public function check_common($password)
    {
        $passwords = file("password.lst");
        foreach($passwords as $key=>$common)
        {
            if($key < 11)
                continue;
            if(strcmp($password, str_replace("\n", "", $common)) == 0)
            {
                throw new Exception($this->errors["common error"]);
                return false;
            }
        }
        return true;
    }

    /**
     * @param void $password
     */
    public function check_recurring($password)
    {
        if($this->model["recurring characters"] == 0)
        {
            return true;
        } else {
            if(preg_match_all("/(.)\\1{".$this->model["recurring characters"]."}/", $password))
            {
                return false;
            } else {
                return true;
            }
        }
    }

    private function check_existing_user($username)
    {
        $sql = new SQL;
        if(empty($sql->getID($username)))
        {
            return true; 
        } else {
            throw new Exception("User already exists.");
            return false;
        }
    }

    private function check_existing_mail($email)
    {
        $sql = new SQL;
        if(empty($sql->getID_by_mail($email)))
        {
            return true; 
        } else {
            throw new Exception("Email already in use.");
            return false;
        }
    }

    /**
    * @param void $password
    */
    private function encrypt($password)
    {
        $options = [
            "cost" => $this->model["hashing iterations"],
        ];
        return password_hash($password, PASSWORD_BCRYPT, $options);
    }

    /**
     * @param void $enc_password
     */
    public function store($username, $password, $email)
    {
        try
        {
            $password_strength = self::check_length($password) && self::check_numerics($password) && self::check_alphanumerics($password) && self::check_uppercase($password) && self::check_lowercase($password) && self::check_special($password) && self::check_recurring($password);
            if(self::check_existing_user($username))
            {
                if(self::check_existing_mail($email))
                {
                    if($password_strength)
                    {
                        $encrypted = self::encrypt($password);
                        $sql = new SQL;
                        $sql->save($username, $email, $encrypted);
                        print("Registered Successfully.");
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