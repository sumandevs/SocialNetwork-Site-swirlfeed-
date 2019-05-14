<?php
include("includes/header.php");

?>

<div class="newsfeed_right_column column">
    <h4 class="friend_text_h4">Friend Requests:</h4>

    <?php
        $friend_request_query = mysqli_query($con, "SELECT * FROM friend_requests WHERE user_to = '$userLoggedIn'");
        if(mysqli_num_rows($friend_request_query) == 0){
            echo "You have no friend requests at this time!";
        } else {
            while($row = mysqli_fetch_array($friend_request_query)){
                $user_from = $row['user_from'];
                $user_from_obj = new User($con,$user_from);
                $user_from_friend_array = $user_from_obj->getFriendArray();

                echo  $user_from_obj->getFirstAndLastName() . " sent you a friend request";

                if(isset($_POST['accept_request' . $user_from])){
                    $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array = CONCAT(friend_array, '$user_from,') WHERE username = '$userLoggedIn'");
                    $add_friend_query = mysqli_query($con, "UPDATE users SET friend_array = CONCAT(friend_array, '$userLoggedIn,') WHERE username = '$user_from'");

                    $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to = '$userLoggedIn' AND user_from = '$user_from'");
                    echo "You are now friend with " . $user_from;
                    header("Location: requests.php");
                }

                if(isset($_POST['ignore_request' . $user_from])){
                    $delete_query = mysqli_query($con, "DELETE FROM friend_requests WHERE user_to = '$userLoggedIn' AND user_from = '$user_from'");
                    echo "You've deleted friend request of " . $user_from;
                    header("Location: requests.php");
                    
                }

    ?>            
                <form action="requests.php" method="POST" style="display: inline-block;">
                    <input type="submit" name="accept_request<?php echo $user_from;?>" id="accept_button" class="request_button" value="Accept">
                    <input type="submit" name="ignore_request<?php echo $user_from;?>" id="ignore_button" class="request_button" value="Ignore">
                </form>
                </br>
                <hr>

    <?php
            } //end while loop
        }
    ?>

    

</div>   
    
</div>   <!-- End wrapper -->
</body>
</html>