<?php
require 'config/config.php'; 
include("includes/classes/User.php");
include("includes/classes/Post.php");
include("includes/classes/Message.php");
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
    <title>Social Networking Site | A Php Project</title>

     <!-- Jquery cdn -->
    <script
        src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous">
    </script>
    <!-- Bootstrap js links -->
    <script type="text/javascript" src="assets/js/bootstrap.js"></script>
    <script type="text/javascript" src="assets/js/main.js"></script>  <!-- Custom js -->
    <script type="text/javascript" src="assets/js/jcrop_bits.js"></script>  <!-- jQuery plugin -->
    <script type="text/javascript" src="assets/js/jquery.Jcrop.js"></script>  <!-- jQuery plugin -->

    <!-- Font awesome -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css?family=Lato:400,400i,700,700i" rel="stylesheet">

    <!-- Bootstrap css links -->
    <link rel="stylesheet" type="text/css" href="assets/css/bootstrap.css"></link>
    <link rel="stylesheet" type="text/css" href="assets/css/style.css"></link> <!-- Custom Css -->
    <link rel="stylesheet" type="text/css" href="assets/css/jquery.Jcrop.css"></link>   <!-- CSS for croping image(Upload profile picture) -->
</head>
<body>

    <div class="top_bar">
        <div class="logo">
            <a href="index.php"><h2>Swirlfeed!</h2></a>
        </div>

        <nav>
            <?php
                //get number of NOT viewed messages..
                $messages = new Message($con,$userLoggedIn);
                $num_messages = $messages->getUnreadNumber();

                //get number of NOT viewed notifications..
                $notifications = new Notification($con,$userLoggedIn);
                $num_notifications = $notifications->getUnreadNumber();
            ?>
    

            <a href="<?php echo $user['username'];?>">
                <?php echo $user['first_name']; ?>
            </a>
            <a href="index.php"><i class="fas fa-home fa-lg"></i></a>
            <!-- message icon -->
            <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn;?>', 'message')" style="position: relative;">
                <i class="far fa-envelope fa-lg"></i>
                <?php
                if($num_messages > 0){
                    echo "<span class='notification_badge' id='unread_message'>" . $num_messages . "</span>";

                }
                ?>
            </a>
            <!-- notification icon -->
            <a href="javascript:void(0);" onclick="getDropdownData('<?php echo $userLoggedIn;?>', 'notification')" style="position: relative;">
                <i class="far fa-bell fa-lg"></i>
                <?php
                if($num_notifications > 0){
                    echo "<span class='notification_badge' id='unread_notifications'>" . $num_notifications . "</span>";

                }
                ?>
            </a>
            <a href="requests.php"><i class="fas fa-users fa-lg"></i></a>
            <a href="#"><i class="fas fa-cog fa-lg"></i></a>
            <a href="includes/handlers/logout.php"><i class="fas fa-sign-out-alt fa-lg"></i></a>
        </nav>

        <div class="dropdown_data_window" style="height: 0px; border: none;"></div>
        <input type="hidden" id="dropdown_data_type" value="">

    </div> <!-- End top_bar -->

    <script>
 
    $(function(){
    
            var userLoggedIn = '<?php echo $userLoggedIn; ?>';
            var dropdownInProgress = false;
    
            $(".dropdown_data_window").scroll(function() {
                var bottomElement = $(".dropdown_data_window a").last();
                var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();
    
                // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
                if (isElementInView(bottomElement[0]) && noMoreData == 'false') {
                    loadPosts();
                }
            });
    
            function loadPosts() {
                if(dropdownInProgress) { //If it is already in the process of loading some posts, just return
                    return;
                }
                
                dropdownInProgress = true;
    
                var page = $('.dropdown_data_window').find('.nextPageDropdownData').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'
    
                var pageName; //Holds name of page to send ajax request to
                var type = $('#dropdown_data_type').val();
    
                if(type == 'notification')
                    pageName = "ajax_load_notifications.php";
                else if(type == 'message')
                    pageName = "ajax_load_messages.php";
    
                $.ajax({
                    url: "includes/handlers/" + pageName,
                    type: "POST",
                    data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                    cache: false,
    
                    success: function(response) {
    
                        $('.dropdown_data_window').find('.nextPageDropdownData').remove(); //Removes current .nextpage 
                        $('.dropdown_data_window').find('.noMoreDropdownData').remove();
    
                        $(".dropdown_data_window").append(response);
    
                        dropdownInProgress = false;
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

    <div class="wrapper">
 