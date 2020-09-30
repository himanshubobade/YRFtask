<?php
include('classes/DB.php');

if (isset($_POST['login'])) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))) {

                if (password_verify($password, DB::query('SELECT password FROM users WHERE username=:username', array(':username'=>$username))[0]['password'])) {
                        if(DB::query('SELECT verified FROM users WHERE username=:username', array(':username'=>$username))[0]['verified']){
                        #if ($approved){
                                #echo 'Logged in!';
                                $cstrong = True;
                                $token = bin2hex(openssl_random_pseudo_bytes(64, $cstrong));
                                echo $token;
                                $user_id = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$username))[0]['id'];
                                DB::query('INSERT INTO login_tokens VALUES (\'\', :token, :user_id)', array(':token'=>sha1($token), ':user_id'=>$user_id));

                                setcookie("SNID", $token, time() + 60 * 60 * 24 * 7, '/', NULL, NULL, TRUE);
                                setcookie("SNID_", '1', time() + 60 * 60 * 24 * 3, '/', NULL, NULL, TRUE);

                                #$url = "http://localhost/RisingYouthFoundationTask/profile.php";
                                #$urlmain = ($url . '?username=' . $username);
                                #if ($username=="admin"){
                                        #header("Location:adminpanel.php");
                                #}else{
                                        header("Location:profile.php?username=".$username);
                                        exit();
                                #}
                        }else{
                                echo "Your account is yet to be approved by the admin";
                        }

                } else {
                        echo 'Incorrect Password!';
                }

        } else {
                echo 'User not registered!';
        }

}

?>
<h1>Login to your account</h1>
<form action="login.php" method="post">
<input type="text" name="username" value="" placeholder="Username ..."><p />
<input type="password" name="password" value="" placeholder="Password ..."><p />
<input type="submit" name="login" value="Login">
</form>
