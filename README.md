# Authentication-Model
Easily create functional register, login and password update forms

that allows you to set the safety parametres you need.

# Getting it to work
This authentication model uses mysqli prepared statements to

avoid SQL injection attacks. 

MySQLi is activated by uncommenting the line:

extension=php_mysqli.dll; for windows or

extension=php_mysqli.so for linux.

After you should create the appropriate table for users by executing in mysql:

CREATE TABLE users(id INT PRIMARY KEY AUTO_INCREMENT, user_login VARCHAR(20) UNIQUE NOT NULL, email VARCHAR(100) UNIQUE NOT NULL, password VARCHAR(100) NOT NULL, last_update TIMESTAMP DEFAULT CURRENT_TIMESTAMP, failed_tries INT NOT NULL DEFAULT 0, locked_time TIMESTAMP DEFAULT 0);

The last step is to give MySQL server credentials for the php connection.

In the file SQL_Model.php complete the necessary credentials at the settings table.

# Usage

Now usage is as simple as instantiating a class.

Login Form

new Login_Model($update_page);

Mandatory parameter url of the update page e.g. new Login_Model("http://example.com/update/");

Optional parameter url to redirect after successfull login e.g. new Login_Model("http://example.com/update/", "http://example.com/success/");

Register Form

new Register_Model;

Optional parameter url to redirect after successfull register e.g. new Register_Model("http://example.com/success/");

Update Form

new Update_Model;

Optional parameter url to redirect after successfull updating e.g. new Update_Model("http://example.com/success/");
