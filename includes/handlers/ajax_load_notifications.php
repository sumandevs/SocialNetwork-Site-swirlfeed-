<?php
include("../../config/config.php");
include("../classes/Message.php");
include("../classes/User.php");
include("../classes/Post.php");
include("../classes/Notification.php");


$limit = 4; // Number of messages to load
$notification = new Notification($con, $_REQUEST['userLoggedIn']);
echo $notification->getNotificationsDropdown($_REQUEST, $limit);


?>