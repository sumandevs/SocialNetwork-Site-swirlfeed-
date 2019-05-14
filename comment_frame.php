<?php
    require 'config/config.php'; 
    include("includes/classes/Post.php");
    include("includes/classes/User.php");
    include("includes/classes/Notification.php");
    
    if(isset($_SESSION['username'])){
        $userLoggedIn = $_SESSION['username'];
        $user_details_query = mysqli_query($con,"SELECT * FROM users WHERE username = '$userLoggedIn'");
        $user = mysqli_fetch_array($user_details_query);
    } else {
        header("Location: register.php");
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css"></link>   <!-- Custom css -->
    <style>
        body {
        background-color: #f3feff;
        font-family: 'Lato', sans-serif;
        box-sizing: border-box;
        }
    </style>
</head>
<body>

    <script>
        function toggle(){
            var element = document.getElementById("comment_section");
            if(element.style.display == "block"){
                element.style.display == "none";
            } else {
                element.style.display == "block";
            }
        }
    </script>

    <?php
        if(isset($_GET['post_id'])){
            $post_id = $_GET['post_id'];
        }

        $user_query = mysqli_query($con, "SELECT added_by, user_to FROM posts WHERE id = '$post_id'");
        $row = mysqli_fetch_array($user_query);

        $posted_to = $row['added_by'];  // Post owner..
        $user_to = $row['user_to'];   // post e jake tag kora hyeche...


        if(isset($_POST['postComment' . $post_id])){
            $post_body = $_POST['post_body'];
            $post_body = mysqli_real_escape_string($con,$post_body); // Comment body
            $date_time_now = date("Y-m-d H:i:s");
            // $userLoggedIn  =>  comment author
            
            $insert_post = mysqli_query($con,"INSERT INTO comments VALUE ('','$post_body','$userLoggedIn','$posted_to','$date_time_now','no','$post_id')");

            //Insert notification
            if($posted_to != $userLoggedIn) {
                $notification = new Notification($con,$userLoggedIn);
                $notification->insertNotification($post_id,$posted_to,'comment');
            } 
            
            if ($user_to != "none" && $user_to != $userLoggedIn){
                $notification = new Notification($con,$userLoggedIn);
                $notification->insertNotification($post_id,$user_to,'profile_comment');   // jake tag korechi tar kacheo notificatino jabe...
            }

            $get_commenters = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id'");
            $notified_users = array();

            while($row = mysqli_fetch_array($get_commenters)) {

                if ($row['posted_by'] != $posted_to && $row['posted_by'] != $user_to && $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notified_users)) {

                    $notification = new Notification($con,$userLoggedIn);
                    $notification->insertNotification($post_id,$row['posted_by'],'comment_non_owner');

                    array_push($notified_users,$row['posted_by']);
                }

            }
            
            echo "<p>Comment Posted!</p>";
        }

    ?>
    
    <form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
        <textarea name="post_body" id="textArea" cols="30" rows="5" placeholder="Your comment"></textarea>
        <input type="submit" id="commentButton" name="postComment<?php echo $post_id; ?>" value="Post">
    </form>

    <!-- Load comments -->
    <?php
    
        $get_comments = mysqli_query($con, "SELECT * FROM comments WHERE post_id = '$post_id' ORDER BY id ASC");
        $count = mysqli_num_rows($get_comments);

        if($count != 0){
            while($comment = mysqli_fetch_array($get_comments)){
                $comment_body = $comment['post_body'];
                $posted_to    = $comment['posted_to'];
                $posted_by    = $comment['posted_by'];
                $date_added   = $comment['date_added'];
                $removed      = $comment['removed'];

                //Timeframe
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DATETIME($date_added);  //  real time of posting a comment
                $end_date = new DATETIME($date_time_now);  // current time
                $interval = $start_date->diff($end_date);  // Difference between two time
                if($interval->y >= 1){
                    if($interval == 1)
                        $time_message = $interval-y . " year ago";  // 1 year ago
                    else 
                        $time_message = $interval-y . " years ago";  // 1+ year ago
                }
                else if($interval->m >= 1){
                    if($interval->d == 0)
                        $days = " ago";
                    else if ($interval->d == 1){
                        $days = $interval->d ." day ago";
                    }
                    else {
                        $days = $interval->d ." days ago";
                    }

                    if($interval->m == 1)
                        $time_message = $interval->m . " month " . $days;
                    else
                        $time_message = $interval->m . " months " . $days;
                }
                else if ($interval->d >=1){
                    if($interval->d == 1)
                        $time_message = " Yesterday";
                    else 
                        $time_message = $interval->d . " days ago";
                }
                else if($interval->h >= 1){
                    if($interval->h == 1)
                        $time_message = $interval->h . " hour ago";
                    else
                        $time_message = $interval->h . " hours ago";
                }
                else if($interval->i >= 1){
                    if($interval->i == 1)
                        $time_message = $interval->i . " minute ago";
                    else
                        $time_message = $interval->i . " minutes ago";
                }
                else {
                    if($interval->s < 30)
                        $time_message = " just now";
                    else
                        $time_message = $interval->s . " seconds ago";
                }
                
                $user_obj = new User($con,$posted_by);
                $profile_pic =  $user_obj->getProfilePic();
            ?>

            

            <div class="comment_section">
            <a href="<?php echo $posted_by; ?>" target="_parent">
                <img src="<?php echo $profile_pic ?>" title="<?php echo $posted_by; ?>" alt="profile picture" style="float:left; height:30px;">
            </a>
            <a href="<?php echo $posted_by; ?>" target="_parent" style="color: #3498db; text-decoration: none; margin-bottom: 5px;">
                <b style="margin-bottom: 10px;"><?php echo $user_obj->getFirstAndLastName(); ?></b>
            </a>&nbsp;&nbsp;&nbsp;&nbsp; <?php echo "<span>" . $time_message . "</span>"; ?>
            <p class="comment_body"><?php echo $comment_body; ?></p>
            <hr class="comment_hr">
            </div>
    <?php

            }  // End while loop

        } else {
            ?>

            <div class="comment_section">
                <em><h3 class="noComments">No comments yet.Be the first one to make a comment on this post.</h3></em>
            </div>
            
    <?php
        }
    ?>

    

</body>
</html>