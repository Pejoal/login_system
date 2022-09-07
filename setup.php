<?php

$servername = "localhost";
$uname = "id19400222_root";
$upass = "Pd9SSL3guSntsai-";
$dbname = "id19400222_login_system";

$conn = new mysqli($servername, $uname, $upass);
if ($conn->connect_error) {
  die("Connection Failed Bro." . $conn->connect_error);
}
echo "Connection Established <br>";

// $sql = "CREATE DATABASE $dbname";
// if ($conn->query($sql) === TRUE) {
//   echo "Database Created Successfully <br>";
// } else {
//   echo "Error Creating Database<br>" . $conn->error;
// }

// SET FOREIGN_KEY_CHECKS = 0; // FORCE TRUNCATE || DROP TABLE
// SET FOREIGN_KEY_CHECKS = 1;

$sql = "CREATE TABLE $dbname.`admins` (`uid` INT(11) NOT NULL AUTO_INCREMENT UNIQUE, `username` VARCHAR(255) NOT NULL UNIQUE, `password` VARCHAR(255) NOT NULL , `update_time` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`uid`)) ENGINE = InnoDB;";
// $sql = "CREATE TABLE $dbname.`users` (`uid` INT(5) NOT NULL AUTO_INCREMENT UNIQUE, `username` VARCHAR(255) NOT NULL UNIQUE, `password` VARCHAR(255) NOT NULL , `update_time` DATETIME on update CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , PRIMARY KEY (`uid`)) ENGINE = InnoDB;";
if ($conn->query($sql) === TRUE) {
  echo "Admins Table Created Successfully <br>";
} else {
  echo "Error Creating Users Table<br>" . $conn->error;
}

// LINK FOREIGN KEY with another table | works successfully 
// $sql = "CREATE TABLE $dbname.`user_data` (`uid` INT NOT NULL UNIQUE, `firstname` VARCHAR(255) DEFAULT 'unknown', `lastname` VARCHAR(255) DEFAULT 'unknown', `user_type` VARCHAR(255) DEFAULT 'unknown', FOREIGN KEY (uid) REFERENCES users(uid) ON UPDATE CASCADE ON DELETE CASCADE ) ENGINE = InnoDB;";
// if ($conn->query($sql) === TRUE) {
//   echo "Users Data Table Created Successfully <br>";
// } else {
//   echo "Error Creating Users Table<br>" . $conn->error;
// }

// CUSTOM CONFIG
// CREATE USER "id19400222_root"@"localhost" IDENTIFIED BY "Pd9SSL3guSntsai-";
// mysql -u id19400222_root -p
// SELECT USER,PASSWORD,HOST FROM MYSQL.USER
// GRANT ALL PRIVILEGES ON *.* TO "id19400222_root"@"localhost";
// FLUSH PRIVILEGES
// Adding All PRIVILEGES to all databases
// SHOW GRANTS FOR id19400222_root@localhost
//  id19400222_root
//  id19400222_login_system
//  Pd9SSL3guSntsai-
// $sql = "ALTER TABLE `login_system`.`users` ADD UNIQUE (username)";
// if ($conn->query($sql) === TRUE) {
//   echo "All Done Successfully <br>";
//   echo "<a href='index.php'>Home Page</a>";
// } else {
//   echo "Error Creating UNIQUE usernames<br>" . $conn->error;
// }



echo '
<script>
setTimeout(() => {
  location.href="index.php";
}, 2500);
</script>
';

echo "<h1><a href='index.php'>Home Page</a></h2>";
// close connection
$conn->close();
exit;
