<?php
include('includes/header.php');

$message_obj = new Message($con,$userLoggedIn);

if(isset($_GET['u']))
    $user_to = $_GET['u'];
else {
    $user_to = $message_obj->getMostRecentUser();
    if($user_to == false){
        $user_to = "new";
    }
}

if($user_to != "new") {
    $user_to_obj = new User($con, $user_to);
}

if(isset($_POST['post_message'])){

    if(isset($_POST['message_body'])){
        $body = mysqli_real_escape_string($con, $_POST['message_body']);
        $date = date("Y-m-d H:i:s");
        $message_obj->sendMessage($user_to, $body, $date);
        header("Location: messages.php");
    }
}

?>

<div class="user_details column">
        <a href="<?php echo $user['username'];?>"> <img src="<?php echo $user['profile_pic'];?>" alt="user profile picture"> </a>
        
        <div class="user_details_left_right">
            <a href="<?php echo $user['username'];?>">
                <?php
                    echo $user['first_name'] ." ".$user['last_name'];
                ?>
            </a>
            <br>
            <?php echo "Posts: ".$user['num_posts']."<br>";
                echo "Likes: ".$user['num_likes'];
            ?>
        </div>
</div> <!-- User details column -->



<div class="newsfeed column" style='min-height: 200px;'>
<?php
  if($user_to != "new"){
    echo "<h4>You and <a href='$user_to'>". $user_to_obj->getFirstAndLastName() ."</a></h4><hr><br>";
    echo "<div class='loaded_messages' id='scroll_message'>";
    echo $message_obj->getMessages($user_to);
    echo "</div>";
  }else {
    echo "<h4>New Messages</h4><hr>";
  }

?> 

    <div class="messages_post">
        <form action="" class="" method="POST">
            <?php
                if($user_to == "new"){
                    echo "<div class='mt-2' style='width: 500px;'><p>Select the friend you would like to message : </p>"; 
            ?>
                    <p class='display-5'>To :</p> <span><input type='text' class='form-control d-inline' onkeyup='getUsers(this.value, "<?php echo $userLoggedIn;?>")' name='q' placeholder='Name' autocomplete='off' id='search_text_input'></span>
            <?php
                    echo "</div><div class='results'></div>";
                }else {
                    echo "<textarea name='message_body' class='form-control' id='message_textarea' placeholder='Write your message ...'></textarea>";
                    echo "<input type='submit' value='Send' name='post_message' id='message_submit' class='btn btn-dark btn-lg rounded'>";
                }
            ?>
        
        </form>
    </div> <!--End message_post -->

    <script>
        var div = document.getElementById("scroll_message");
        if(div != null){
            div.scrollTop = div.scrollHeight;
        }
    </script>

</div>

<div class="user_details column">
        <h5>Recent Conversations</h5>
        <hr>

        <div class="loaded_conversations">
            <?php echo $message_obj->getConvos(); ?>
        </div>
        <br>

        <a href="messages.php?u=new">New Message</a>
</div>