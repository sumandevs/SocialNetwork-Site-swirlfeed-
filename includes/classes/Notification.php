<?php
    class Notification {
        private $user_obj;
        private $con;

        public function __construct($con,$user){
            $this->con = $con;
            $this->user_obj = new User($con,$user);
        }

        public function getUnreadNumber () {
            $userLoggedIn = $this->user_obj->getUsername();
            $query = mysqli_query($this->con, "SELECT * FROM notifications WHERE viewed='no' AND user_to = '$userLoggedIn'");
            return $num_unread_mesaages = mysqli_num_rows($query);
        }
        
        public function getNotificationsDropdown($data, $limit) {
            $page = $data['page'];
            $userLoggedIn = $this->user_obj->getUsername();
            $return_string = "";
    
            if($page == 1)
                $start = 0;
            else {
                $start = ($page -1) * $limit;
            }
    
            $set_viewed_query = mysqli_query($this->con, "UPDATE notifications SET viewed = 'yes' WHERE user_to = '$userLoggedIn'");
    
            
    
            $query = mysqli_query($this->con, "SELECT * FROM notifications WHERE user_to = '$userLoggedIn' ORDER BY id DESC");
    
            //if statement
            if(mysqli_num_rows($query) == 0){
                echo "You have no notifications at this time!";
                return;
            }
    
            $num_of_iterations = 0; // Number of notifications checked
            $count = 1;   
    
            while($row = mysqli_fetch_array($query)){
        
                if($num_of_iterations++ < $start)
                    continue;
                if($count > $limit)
                    break;
                else {
                    $count++;
                }


                $user_from = $row['user_from'];
                $user_data_query = mysqli_query($this->con, "SELECT * FROM users WHERE username='$user_from'");
                $user_data = mysqli_fetch_array($user_data_query);


                //Timeframe
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DATETIME($row['datetime']);  //  time of notification
                $end_date = new DATETIME($date_time_now);  // time of when seeing the notification
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
                        $time_message = $interval->m . " month" . $days;
                    else
                        $time_message = $interval->m . " months" . $days;
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

    
                
                
                $opened = $row['opened'];
                $style = ($row['opened'] == 'no') ? "background-color: #DDEDFF;" : "";
    
                $return_string .= "<a href='". $row['link'] . "' style='text-decoration: none;'>
                                        <div class='notificationDisplay' style='" . $style . "'>
                                            <div class='notificationsProfilePic'>
                                                <img src='". $user_data['profile_pic'] ."' alt='Profile image'>
                                            </div>
                                            <div class='notificationContent'>
                                                <p class='notification_msg_body'>". $row['message'] ."</p>
                                                <p class='timestamp_smaller' id='grey'>". $time_message ."</p>
                                            </div>
                                        </div>
                                        <hr class='notification_hr_dropdown'>
                                   </a>";
                                    
            } // End while loop
            
            // if all messages are loaded
            if($count > $limit){
                $return_string .= "<input type='hidden' class='nextPageDropdownData' value='". ($page + 1) ."'><input type='hidden' class='noMoreDropdownData' value='false'>";
            }     
             else {
                $return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'><p style='text-align: center; font-size: .82rem; color: grey; font-style: italic; font-family: cursive;' class='mt-2'>No more notifications to show...</p>";
            }
            
    
            
            return $return_string;
        }



        public function insertNotification($post_id,$user_to,$type) {

            $userLoggedIn = $this->user_obj->getUsername();
            $userLoggedInName = $this->user_obj->getFirstAndLastName();

            $date_time = date("Y-m-d H:i:s");
            switch($type) {
                case 'comment':
                    $message = $userLoggedInName . " commented in your post";
                    break;
                case 'like':
                    $message = $userLoggedInName . " liked your post";
                    break;
                case 'profile_post':
                    $message = $userLoggedInName . " posted on your profile";
                    break;
                case 'comment_non_owner':
                    $message = $userLoggedInName . " commented on a post you commented on";
                    break;
                case 'profile_comment':
                    $message = $userLoggedInName . " commented on your profile post";
                    break;
            }

            $link = "post.php?id=" . $post_id; 

            //INsert to notifications table
            $insert_query = mysqli_query($this->con, "INSERT INTO notifications VALUES('','$user_to','$userLoggedIn','$message','$link','$date_time','no','no')");


        }


    }

?>