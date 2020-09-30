<?php
include('./classes/DB.php');
include('./classes/Login.php');
?>
<?php
if (Login::isLoggedIn()) {
    echo 'Logged In';
    $username = "";
    $verified = False;
    $isFollowing = False;
    $followerid = Login::isLoggedIn();
    #$username = DB::query('SELECT username from users where id = 4');
    if (isset($_GET['username'])) {
        if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username']){
                $username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
                $userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
                $verified = DB::query('SELECT verified FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['verified'];
                $followerid = Login::isLoggedIn();

                if (isset($_POST['message'])) {
                        $messagebody = $_POST['messagebody'];
                        $loggedInUserId = Login::isLoggedIn();
                
                        if (strlen($messagebody) > 160 || strlen($messagebody) < 1) {
                                die('Incorrect length!');
                        }
                
                        if ($loggedInUserId == 4) {
                                DB::query('INSERT INTO messages VALUES (\'\', :messagebody, NOW(), :userid)', array(':messagebody'=>$messagebody, ':userid'=>$userid));
                
                        } else {
                                die('Incorrect user!');
                        }
                }

                } else {
                echo 'Not logged in';
                }
        }
        $dbposts = DB::query('SELECT * FROM messages WHERE user_id=:userid ORDER BY id DESC', array(':userid'=>$userid));
                $messages = "";
                foreach($dbposts as $p) {
                                $messages .= htmlspecialchars($p['messagebody']).
                                "<hr /><br />
                                ";
                        
                }
}
?>
<form action="broadcast.php?username=<?php echo $username; ?>" method="post">
        <h1>ADMIN MESSAGES</h1>
        <textarea name="messagebody" rows="8" cols="80"></textarea>
        <input type="submit" name="message" value="SEND">
</form>

<?php 
echo $messages;
?>