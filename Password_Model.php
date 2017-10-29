<?php

require_once("Storage_Model.php");

use Storage_Model as Storage;
use SQL_Model as SQL;
/**
 *
 */
class Password_Model
{
    /**
     *
     */

    /**
     * @var void
     */
    private $time_to_live;


    /**
     * @param void $username
     */
    public static function check_time_to_live($username, $update)
    {
        date_default_timezone_set("Europe/Athens");
        $storage = new Storage;
        $sql = new SQL;
        $time_to_live = $storage->get_variables();
        $start = date_create($sql->getUpdated($username));
        $end = date_create(date("Y-m-d H:i:s", time()));
        $diff = date_diff($start, $end);
        if(intval($diff->format("%a")) >= $time_to_live["time to live"])
        {
            throw new Exception("Your password is expired, you must <a href='$update'>change it</a>.");
            return false;
        } else {
            return true;
        }
    }

    public static function increment_tries($username)
    {
        $storage = new Storage;
        $tries = $storage->get_variables();
        if($tries["tries to timeout"] != 0)
        {
            $sql = new SQL;
            $sql->incrementTries($username);
        }
    }

    public static function tries_timeout($username)
    {
        date_default_timezone_set("Europe/Athens");
        $storage = new Storage;
        $tries = $storage->get_variables();
        if($tries["tries to timeout"] == 0)
        {
            return true;
        } else {
            $sql = new SQL;
            $start = date_create($sql->getLockedTime($username));
            $end = date_create(date("Y-m-d H:i:s", time()));
            $diff = date_diff($start, $end);
            if($sql->getLockedTime($username) != "0000-00-00 00:00:00")
            {
                if(intval($diff->format("%i")) >= $tries["time to unlock"])
                {
                    $sql->initializeTries($username);
                    $sql->initializeLockedTime($username);
                }
            }
            $consecutive_tries = $sql->getTries($username);
            if($consecutive_tries > $tries["tries to timeout"])
            {
                if($sql->getLockedTime($username) == "0000-00-00 00:00:00")
                    $sql->setLockedTime($username);
                throw new Exception("The account is locked out. It will be unlocked after ".$tries["time to unlock"]." minutes.");
                return false;
            } else {
                return true;
            }
        }
    }

    public static function initialize_tries($username)
    {
        $sql = new SQL;
        $sql->initializeTries($username);
    }

    /**
     * @param void $username
     * @param void $old_password
     * @param void $new_password
     */
    public static function update_password($username, $old_password, $new_password)
    {
        $storage = new Storage;
        $data = $storage->get_variables();
        $sql = new SQL;
        $password_strength = $storage->check_length($new_password) && $storage->check_numerics($new_password) && $storage->check_alphanumerics($new_password) && $storage->check_uppercase($new_password) && $storage->check_lowercase($new_password) && $storage->check_special($new_password) && $storage->check_recurring($new_password);
        if(password_verify($old_password, $sql->getPassword($username)))
        {
            if($password_strength)
            {
                $options = [
                    "cost" => $data["hashing iterations"],
                ];
                $sql->updatePassword($username, password_hash($new_password, PASSWORD_BCRYPT, $options));
                print("Password Updated.");
            }
        } else {
            throw new Exception("Expired password is wrong.");
        }    
    }


}
