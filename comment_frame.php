<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title></title>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css"></link>   <!-- Custom css -->
</head>
<body>

<?php
    require 'config/config.php'; 
    include("includes/classes/Post.php");
    include("includes/classes/User.php");
    
    if(isset($_SESSION['username'])){
        $userLoggedIn = $_SESSION['username'];
        $user_details_query = mysqli_query($con,"SELECT * FROM users WHERE username = '$userLoggedIn'");
        $user = mysqli_fetch_array($user_details_query);
    } else {
        header("Location: register.php");
    }
?>

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

        $user_query = mysqli_query($con, "SELECT added_by, user_to FROM posts WHERE id = '$post_id'"):
        $row = mysqli_fetch_array($user_query);

        $posted_to = $row['added_by'];  // Post owner..

        if(isset($_POST['postComment' . $post_id])){
            $post_body = $_POST['post_body'];
            $post_body = mysqli_real_escape_string($con,$post_body); // Comment body
            $date_time_now = date("Y-m-d H:i:s");
            // $userLoggedIn  =>  comment author
            
            $insert_post = mysqli_query($con,"INSERT INTO comments VALUE ('','$post_body','$userLoggedIn','$posted_to','$date_time_now','no','$post_id')"):
            
            echo "<p>Comment Posted!</p>";
        }

    ?>

    <form action="comment_frame.php?post_id=<?php echo $post_id; ?>" id="comment_form" name="postComment<?php echo $post_id; ?>" method="POST">
        <textarea name="post_body" id="" cols="30" rows="10" placeholder="Your comment"></textarea>
        <input type="submit" name="postComment<?php echo $post_id; ?>" value="Post">
    </form>

    <!-- Load comments -->
    
</body>
</html>