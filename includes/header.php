<?php
require 'config/config.php'; 
include("includes/classes/User.php");
include("includes/classes/Post.php");
include("includes/classes/Message.php");

 
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
            <a href="<?php echo $user['username'];?>">
                <?php echo $user['first_name']; ?>
            </a>
            <a href="messages.php"><i class="far fa-envelope fa-lg"></i></a>
            <a href="index.php"><i class="fas fa-home fa-lg"></i></a>
            <a href="#"><i class="far fa-bell fa-lg"></i></a>
            <a href="requests.php"><i class="fas fa-users fa-lg"></i></a>
            <a href="#"><i class="fas fa-cog fa-lg"></i></a>
            <a href="includes/handlers/logout.php"><i class="fas fa-sign-out-alt fa-lg"></i></a>
        </nav>
    </div> <!-- End top_bar -->

    <div class="wrapper">
 