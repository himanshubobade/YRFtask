<?php
include('./classes/DB.php');
include('./classes/Login.php');

if (Login::isLoggedIn()) {
        echo 'Logged In';
        echo Login::isLoggedIn();
} else {
        echo 'Not logged in';
}

?>

<h1>Hello welcome to our task website</h1>
<a href="http://localhost/RisingYouthFoundationTask/login.php">Login</a>
<a href="http://localhost/RisingYouthFoundationTask/create-account.php">Create Account</a>