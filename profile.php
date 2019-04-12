<?php 
include("includes/header.php");

// session_destroy();
if(isset($_GET['profile_username'])){
    $username = $_GET['profile_username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username = '$username'");
    $user_array = mysqli_fetch_array($user_details_query);

    $num_friends = (substr_count($user_array['friend_array'],",")) - 1;    // (num of commas - 1) => num of friends 
}

if (isset($_POST['remove_friend'])){
    $user = new User($con, $userLoggedIn);
    $user->removeFriend($username);
}

if (isset($_POST['add_friend'])){
    $user = new User($con, $userLoggedIn);
    $user->sendRequest($username);
}

if (isset($_POST['respond_request'])){
    header("Location: requests.php");
}

?>
    <!-- Left SECTION  profile-section -->
    <div class="profile_left_column column">
        <div class="image">
            <img class="profile_img" src="<?php echo $user_array['profile_pic']?>" alt="profile picture" title="<?php echo $username;?>">
        </div>
        <div class="profile_info">
            <?php
            $user_obj = new User($con,$username);
            $full_name = $user_obj->getFirstAndLastName();
            ?>
            <h3><?php echo $full_name; ?></h3>
            <h5><i class="fas fa-sticky-note text-secondary mr-2"></i> Posts: <span><?php echo $user_array['num_posts']; ?></span></h5>
            <h5><i class="fas fa-thumbs-up text-secondary mr-2"></i> Likes: <span><?php echo $user_array['num_likes']; ?></span></h5>
            <h5><i class="fas fa-user text-secondary mr-2"></i> Friends: <span><?php echo $num_friends; ?></span></h5>
            <!-- Mutual Friends showing -->
            <?php
                $user_mutual_friend_obj = new User($con,$userLoggedIn);
                $mutualFriends = $user_mutual_friend_obj->getMutualFriends($username);
                if ($userLoggedIn != $username){
            ?>
            <h5><i class="fas fa-user-friends text-secondary mr-2"></i>Mutual Friends: <span><?php echo $mutualFriends; ?></span></h5>
            <?php } ?>
        </div>

        <div class="friend_button">
            <form action="<?php echo $username; ?>" method="POST">
                <?php 
                    $profile_user_obj = new User($con, $username);
                    if($profile_user_obj->isClosed()) {
                        header("Location: user_closed.php");
                    }

                    if($username !== $userLoggedIn){  
                    // nijer profile e jeno add,remove,frend request sent ey sb button na dekhay tai ey condintion ta deyoa..simple..

                        $logged_in_user_obj = new User($con, $userLoggedIn);
                        if($logged_in_user_obj->isFriend($username)){
                            echo "<input type='submit' class='profile-btn-text danger' name='remove_friend' value='Remove Friend'>";
                        }else if ($logged_in_user_obj->didRecieveRequest($username)) {
                            echo "<input type='submit' class='profile-btn-text warning' name='respond_request' value='Respond to Request'>";
                        }else if ($logged_in_user_obj->didSendRequest($username)){
                            echo "<input type='submit' class='profile-btn-text default' name='' value='Request Sent'>";
                        } else {
                            echo "<input type='submit' class='profile-btn-text success' name='add_friend' value='Add Friend'>";
                        }
                    }
                
                ?>
            </form>
                <!-- Button that triggers modal post form -->
                <input type="submit" class="profile-btn-text btn-primary mt-2" data-toggle="modal" data-target="#post_form" value="Post Something">
        </div>  
    </div>

    <!-- RIGHT SECTION  newsfeed-section -->
    <div class="newsfeed_right_column column">
        <div class="posts_area"></div> 
        <!-- .posts_area => this class is used for ajax call.We are selecting this div through ajax and showing all posts within this div -->
       
        <img id="loading" src="assets/images/icons/loading.gif" alt="loading.gif"> <!-- Gif file -->

    </div> <!-- End newsfeed column -->


    <!-- Bootstrap modal popup form -->
    <!-- Modal -->
    <div class="modal fade" id="post_form" tabindex="-1" role="dialog" aria-labelledby="postModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Post something</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <p>This will appear on user's profile page and also their newsfeed for your friends to see</p>
                    <form action="" class="profile_post" method="POST">
                        <div class="form-group">
                            <textarea class="form-control" name="post_body"></textarea>
                            <input type="hidden" name="user_from" value="<?php echo $userLoggedIn; ?>">
                            <input type="hidden" name="user_to" value="<?php echo $username; ?>">
                        </div>
                    </form>
                </div> <!-- End modal-body -->

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" name="post_button" id="submit_profile_post">Post</button>
                </div>
            </div>
        </div>
    </div>   <!-- End modal -->

    <!-- Loading profile posts AJAX part -->
    <script>
        $(function(){
        
            var userLoggedIn = '<?php echo $userLoggedIn; ?>';
            var profileUsername = '<?php echo $username ?>';
            var inProgress = false;
            loadPosts(); //Load first posts
        
            $(window).scroll(function() {
                var bottomElement = $(".status_post").last();
                var noMorePosts = $('.posts_area').find('.noMorePosts').val();
        
                // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
                if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
                    loadPosts();
                }
            });
        
            function loadPosts() {
                if(inProgress) { //If it is already in the process of loading some posts, just return
                    return;
                }
                
                inProgress = true;
                $('#loading').show();
                var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'
        
                $.ajax({
        
                    url: "includes/handlers/ajax_load_profile_posts.php",
                    type: "POST",
                    data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
                    cache:false,
                    success: function(response) {
        
                        $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage
                        $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage
                        $('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage
                        $('#loading').hide();
                        $(".posts_area").append(response);
                        inProgress = false;
        
                    }
        
                });
            }
        
            //Check if the element is in view
            function isElementInView (el) {
                    if(el == null) {
                        return;
                    }
                var rect = el.getBoundingClientRect();
                return (
                    rect.top >= 0 &&
                    rect.left >= 0 &&
                    rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
                    rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
                );
            }
        });
    </script>






 </div> <!-- End Wrapper -->
</body>
</html>