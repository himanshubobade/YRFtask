<?php
include('./classes/DB.php');
include('./classes/Login.php');
$showFeed = False;

if (Login::isLoggedIn()) {
        #echo 'Logged In';
        #echo Login::isLoggedIn();
        $showFeed = TRUE;
        $feedposts = DB::query('SELECT posts.body, users.username,posted_at FROM posts, post_allows, users WHERE posts.id = post_allows.post_id AND posts.user_id = users.id ORDER BY posts.posted_at DESC');

        foreach($feedposts as $post){
            echo $post['username']. "     -   ".$post['posted_at']. "<br />". $post['body']."<br />". "<hr />";
        }
} else {
        echo 'Not logged in';
}



#print_r ( $feedposts) ;
?>
