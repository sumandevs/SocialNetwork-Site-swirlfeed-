<?php
class Message {
    private $user_obj;
    private $con;

    public function __construct($con,$user){
        $this->con = $con;
        $this->user_obj = new User($con,$user);
    }
    
    public function getMostRecentUser(){
        $userLoggedIn= $this->user_obj->getUsername();

        $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to = '$userLoggedIn' OR user_from = '$userLoggedIn' ORDER BY id DESC LIMIT 1");
        if(mysqli_num_rows($query) == 0){
            return false;
        }
        
        $row = mysqli_fetch_array($query);
        $user_to = $row['user_to'];
        $user_from = $row['user_from'];

        if($user_to != $userLoggedIn)
            return $user_to;
        else {
            return $user_from;
        }
    }

    public function sendMessage($user_to, $body, $date) {
        $userLoggedIn = $this->user_obj->getUsername();
        if($body != ""){
            $query = mysqli_query($this->con, "INSERT INTO messages VALUES ('','$user_to','$userLoggedIn','$body','$date','no','no','no')");
        }
    }

    public function getMessages($otherUser){
        $userLoggedIn = $this->user_obj->getUsername();
        $data = "";
        $query = mysqli_query($this->con, "UPDATE messages SET opened = 'yes' WHERE user_to = '$userLoggedIn' AND user_from = '$otherUser'");

        $get_messages_query = mysqli_query($this->con, "SELECT * FROM messages WHERE  (user_to = '$userLoggedIn' AND user_from = '$otherUser') OR (user_to = '$otherUser' AND user_from = '$userLoggedIn') ORDER BY id ASC");

        while($row = mysqli_fetch_array($get_messages_query)){
            $user_to      = $row['user_to'];
            $user_from    = $row['user_from'];
            $body = $row['body'];

            $div_top = ($user_to == $userLoggedIn) ? "<div class='message bg-success text-white bt-light' id='green'>" : "<div class='message bg-primary text-white' id='blue'>"; // if else statement in one line => (Condition) ? result(if) : result(else);
            $data = $data . $div_top . $body . "</div><br><br><br><br>";

        } // End while loop

        return $data;
    }

    public function getLatestMessage($userLoggedIn,$user2){    // $user2 = person i talked to...
        $details_query = array();
        $query = mysqli_query($this->con, "SELECT body, user_to, date FROM messages WHERE (user_to = '$userLoggedIn' AND user_from = '$user2') OR (user_to ='$user2' AND user_from = '$userLoggedIn') ORDER BY id DESC LIMIT 1");
        $row = mysqli_fetch_array($query);
        $sent_by = ($row['user_to'] == $userLoggedIn) ? "They said:&nbsp;&nbsp;" : "You said:&nbsp;&nbsp;";
        $date_time = $row['date'];

        //Timeframe
        $date_time_now = date("Y-m-d H:i:s");
        $start_date = new DATETIME($date_time);  //  time of message
        $end_date = new DATETIME($date_time_now);  // time of when seeing the message
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
        
        array_push($details_query, $sent_by);
        array_push($details_query, $row['body']);
        array_push($details_query, $time_message);

        return $details_query;  // its an array like =>   [$sent_by, $row['body], $time_message];


    }

    public function getConvos(){
        $userLoggedIn = $this->user_obj->getUsername();
        $return_string = "";
        $convos = array();

        $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to = '$userLoggedIn' OR user_from = '$userLoggedIn' ORDER BY id DESC");

        while($row = mysqli_fetch_array($query)){
            $user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

            if(!in_array($user_to_push, $convos)){ 
                array_push($convos, $user_to_push);
            }
        
        }

        foreach($convos as $username){
            $user_found_obj = new User($this->con,$username);
            $latest_message_details = $this->getLatestMessage($userLoggedIn,$username);   //$latest_message_details = $details_query      -- which is an array

            $dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
            $split = str_split($latest_message_details[1], 12);
            $split = $split[0] . $dots;

            $return_string .= "<a href='messages.php?u=$username' style='text-decoration: none;'>
                                    <div class='user_found_messages'>
                                        <img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 50px; margin-right: 5px;' class='latest_convo_user_img'>
                                        <div class='user_content'>
                                        <span class='user_content_name'>" . $user_found_obj->getFirstAndLastName() ."</span>
                                        <span class='timestamp_smaller' id='grey'>" . $latest_message_details[2] . "</span>
                                        <p class='user_content_text mt-2' id='grey' style='margin: 0px;'>".$latest_message_details[0] . $split ."</p>
                                        </div>
                                    </div>   
                               </a><hr class='latest_convo_hr'>";
                                
        }
        
        return $return_string;

    }

    public function getConvosDropdown($data, $limit) {
        $page = $data['page'];
        $userLoggedIn = $this->user_obj->getUsername();
        $return_string = "";
        $convos = array();

        if($page == 1)
            $start = 0;
        else {
            $start = ($page -1) * $limit;
        }

        $set_viewed_query = mysqli_query($this->con, "UPDATE messages SET viewed = 'yes' WHERE user_to = '$userLoggedIn'");

        

        $query = mysqli_query($this->con, "SELECT user_to, user_from FROM messages WHERE user_to = '$userLoggedIn' OR user_from = '$userLoggedIn' ORDER BY id DESC");

        while($row = mysqli_fetch_array($query)){
            $user_to_push = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

            if(!in_array($user_to_push, $convos)){ 
                array_push($convos, $user_to_push);
            }
        
        }

        $num_of_iterations = 0; // Number of messages checked
        $count = 1;

        foreach($convos as $username){
    
            if($num_of_iterations++ < $start)
                continue;
            if($count > $limit)
                break;
            else {
                $count++;
            }

            $is_unread_query = mysqli_query($this->con, "SELECT opened FROM messages WHERE user_to = '$userLoggedIn' AND user_from = '$username' ORDER BY id DESC");
            // ey query tar mane holo amk jodi keu sms send kore ebong sei sms ta latest hoy(DESC) thle seta k select koro
            $row = mysqli_fetch_array($is_unread_query);
            $style = ($row['opened'] == 'no') ? "background-color: #DDEDFF;" : "";

            $user_found_obj = new User($this->con,$username);
            $latest_message_details = $this->getLatestMessage($userLoggedIn,$username);   //$latest_message_details = $details_query      -- which is an array

            $dots = (strlen($latest_message_details[1]) >= 12) ? "..." : "";
            $split = str_split($latest_message_details[1], 12);
            $split = $split[0] . $dots;

            $return_string .= "<a href='messages.php?u=$username' style='text-decoration: none;'>
                                    <div class='user_found_messages_dropdown' style='". $style ."'>
                                        <img src='" . $user_found_obj->getProfilePic() . "' style='border-radius: 50px; margin-right: 5px;' class='latest_convo_user_img_dropdown'>
                                        <div class='user_content_dropdown'>
                                        <span class='user_content_name_dropdown'>" . $user_found_obj->getFirstAndLastName() ."</span>
                                        <span class='timestamp_smaller_dropdown' id='grey'>" . $latest_message_details[2] . "</span>
                                        <p class='user_content_text_dropdown mt-2' id='grey' style='margin: 0px;'>".$latest_message_details[0] . $split ."</p>
                                        </div>
                                        </div>   
                                        <hr class='latest_convo_hr_dropdown'>
                                        </a>";
                                
        } // End foreach
        
        // if all messages are loaded
        if($count > $limit){
            $return_string .= "<input type='hidden' class='nextPageDropdownData' value='". ($page + 1) ."'><input type='hidden' class='noMoreDropdownData' value='false'>";
        }     
         else {
            $return_string .= "<input type='hidden' class='noMoreDropdownData' value='true'><p style='text-align: center; font-size: .82rem; color: grey; font-style: italic; font-family: cursive;' class='mt-2'>No more messages to show...</p>";
         }
        

        
        return $return_string;
    }

    public function getUnreadNumber () {
        $userLoggedIn = $this->user_obj->getUsername();
        $query = mysqli_query($this->con, "SELECT * FROM messages WHERE viewed='no' AND user_to = '$userLoggedIn'");
        return $num_unread_mesaages = mysqli_num_rows($query);
    }
    
}



?>