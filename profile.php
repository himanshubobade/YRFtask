<?php
include('./classes/DB.php');
include('./classes/Login.php');

$username = "";
$verified = False;
$isFollowing = False;
if (isset($_GET['username'])) {
        if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'] == "admin") {
                #echo "we roll here";

                $username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
                $userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
                $verified = DB::query('SELECT verified FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['verified'];
                $followerid = Login::isLoggedIn();

                if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                        //echo 'Already following!';
                        $isFollowing = True;
                }


                if (isset($_POST['post'])) {
                        $postbody = $_POST['postbody'];
                        $loggedInUserId = Login::isLoggedIn();

                        if (strlen($postbody) > 160 || strlen($postbody) < 1) {
                                die('Incorrect length!');
                        }

                        if ($loggedInUserId == $userid) {

                                DB::query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0)', array(':postbody'=>$postbody, ':userid'=>$userid));

                        } else {
                                die('Incorrect user!');
                        }
                }

                if (isset($_GET['postid'])) {
                        if (!DB::query('SELECT user_id FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid))) {
                                DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$_GET['postid']));
                                DB::query('INSERT INTO post_likes VALUES (\'\', :postid, :userid)', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
                        } else {
                                DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$_GET['postid']));
                                DB::query('DELETE FROM post_likes WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
                        }
                }

                $dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid'=>$userid));
                $posts = "";
                foreach($dbposts as $p) {

                        if(!DB::query('SELECT post_id FROM post_allows WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$followerid))){
                                $posts .= htmlspecialchars($p['body'])."
                                <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                                        <input type='submit' name='like' value='Allow'>
                                </form>" .
                                "<hr /><br />
                                ";
                        }else{
                                $posts .= htmlspecialchars($p['body'])."
                                <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                                        <input type='submit' name='unlike' value='Unallow'>
                                </form>"."
                                <hr /><br />
                                ";
                        }
                        
                }


        } elseif (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))){
                #echo "duh its me";
                $username = DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
                $userid = DB::query('SELECT id FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['id'];
                $verified = DB::query('SELECT verified FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['verified'];
                $followerid = Login::isLoggedIn();

                if (isset($_POST['follow'])) {

                        if ($userid != $followerid) {

                                if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                                        if ($followerid == 4) {
                                                DB::query('UPDATE users SET verified=1 WHERE id=:userid', array(':userid'=>$userid));
                                        }
                                        DB::query('INSERT INTO followers VALUES (\'\', :userid, :followerid)', array(':userid'=>$userid, ':followerid'=>$followerid));
                                } else {
                                        echo 'Already following!';
                                }
                                $isFollowing = True;
                        }
                }
                if (isset($_POST['unfollow'])) {

                        if ($userid != $followerid) {

                                if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                                        if ($followerid == 4) {
                                                DB::query('UPDATE users SET verified=0 WHERE id=:userid', array(':userid'=>$userid));
                                        }
                                        DB::query('DELETE FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid));
                                }
                                $isFollowing = False;
                        }
                }
                if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid AND follower_id=:followerid', array(':userid'=>$userid, ':followerid'=>$followerid))) {
                        //echo 'Already following!';
                        $isFollowing = True;
                }


                if (isset($_POST['post'])) {
                        $postbody = $_POST['postbody'];
                        $loggedInUserId = Login::isLoggedIn();

                        if (strlen($postbody) > 160 || strlen($postbody) < 1) {
                                die('Incorrect length!');
                        }

                        if ($loggedInUserId == $userid) {

                                DB::query('INSERT INTO posts VALUES (\'\', :postbody, NOW(), :userid, 0)', array(':postbody'=>$postbody, ':userid'=>$userid));

                        } else {
                                die('Incorrect user!');
                        }
                }

                if (isset($_GET['postid'])) {
                        if (!DB::query('SELECT user_id FROM post_allows WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid))) {
                                DB::query('UPDATE posts SET likes=likes+1 WHERE id=:postid', array(':postid'=>$_GET['postid']));
                                DB::query('INSERT INTO post_allows VALUES (\'\', :postid, :userid)', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
                        } else {
                                DB::query('UPDATE posts SET likes=likes-1 WHERE id=:postid', array(':postid'=>$_GET['postid']));
                                DB::query('DELETE FROM post_allows WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$_GET['postid'], ':userid'=>$followerid));
                        }
                }

                $dbposts = DB::query('SELECT * FROM posts WHERE user_id=:userid ORDER BY id DESC', array(':userid'=>$userid));
                $posts = "";
                foreach($dbposts as $p) {

                        if(!DB::query('SELECT post_id FROM post_allows WHERE post_id=:postid AND user_id=:userid', array(':postid'=>$p['id'], ':userid'=>$followerid))){
                                if ($followerid == 4){
                                        $posts .= htmlspecialchars($p['body'])."
                                        <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                                                <input type='submit' name='like' value='Allow'>
                                        </form>"."
                                        <hr /><br />
                                        ";
                                }else{
                                        $posts .= htmlspecialchars($p['body'])."
                                        <hr /><br />
                                        ";
                                }
                        }else{
                                $posts .= htmlspecialchars($p['body'])."
                                <form action='profile.php?username=$username&postid=".$p['id']."' method='post'>
                                        <input type='submit' name='unlike' value='Unallow'>
                                </form>
                                
                                ";
                        }
                        
                }

        } else{
                die('User not found!');
        }
}

?>

<h1><?php if($username=='admin'){echo $username.'';}else{echo $username;} ?>'s Profile<?php if ($verified) { echo ' - Approved User'; } ?></h1>
<form action="profile.php?username=<?php echo $username; ?>" method="post">
        <?php
        #echo Login::isLoggedIn();
        if (Login::isLoggedIn() == 4){
                if ($userid != $followerid) {
                        if ($isFollowing) {
                                echo '<input type="submit" name="unfollow" value="Unapprove User">';
                        } else {
                                echo '<input type="submit" name="follow" value="Approve User">';
                        }
                }
        }
        ?>
</form>
<form action="profile.php?username=<?php echo $username; ?>" method="post">
        <textarea name="postbody" rows="8" cols="80"></textarea>
        <input type="submit" name="post" value="Post">
</form>

<div class="posts">
        <?php 
        #echo $username; 
        echo "<br>";
        echo "POSTS BY  -  ". $username;
        echo "<br/><br/>";
        echo $posts; 
        ?>
</div>
<?php
$username1 = $username;
?>
<div class=user>
<?php
if(Login::isLoggedIn() == 4){
        echo "<br>";
        echo "Approve new User accounts";
        echo "<br><br>";
        $users = DB::query('SELECT * FROM users ORDER BY id DESC', array(':username'=>$username));
        $username = "";
        $i = 0;
        $usernameNonApproved = DB::query('SELECT username FROM users WHERE users.verified = 0');
        #echo sizeof($usernameNonApproved);
        foreach($users as $user) {
                if($i<sizeof($usernameNonApproved)){
                        $username .= $usernameNonApproved[$i]['username']." <hr /><br />";
                        #echo $username;
                        $i++;
                }else{
                        echo $username;
                        break;
                }
                
        }
        
}       
?>
<a href="http://localhost/RisingYouthFoundationTask/feed.php">Feed</a>
<a href=<?php echo "http://localhost/RisingYouthFoundationTask/broadcast.php?username=" . $username1 ;?>>Broadcast</a>
<a href="http://localhost/RisingYouthFoundationTask/logout.php">Logout</a>
</div>
