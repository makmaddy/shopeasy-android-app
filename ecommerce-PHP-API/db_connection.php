<?php

define('HOST', 'localhost');
define('DB1', 'bustebp2_shopeasy');
define('USER', 'bustebp2_blueapp');
define('PASS', 'test@1234@');

$OTPauthKey = " I have remove the key, please use your sms gateway key here";

$conn = new mysqli(HOST, USER, PASS, DB1);

if($conn-> connection_error){
	trigger_error('Database connection has failed '. $conn->connect_error, E_USER_ERROR);

}

// database name - bustebp2_shopeasy
// username  blueapp
//password - blueapp
//test@1234@

?>

