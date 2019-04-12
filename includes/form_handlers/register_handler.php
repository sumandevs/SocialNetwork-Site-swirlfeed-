<?php
//Declaring variables to prevent errors
$fname = ""; //first name
$lname = ""; //last name
$em = ""; //email
$em2 = ""; //email 2
$password = ""; //password
$password2 = ""; //password 2
$date = ""; //signed up date
$error_array = array(); //holds error message

if(isset($_POST['register_button'])){

    //Registration form values
    //First Name
    $fname = strip_tags($_POST['reg_fname']); //Remove html tags from fname
    $fname = str_replace(' ','',$fname); //remove empty spaces from fname
    $fname = ucfirst(strtolower($fname)); // ucfirst => only first letter is Capital and rest all letters are in lowercase 
    $_SESSION['reg_fname'] = $fname; //stores first name into session

    //Last Name
    $lname = strip_tags($_POST['reg_lname']); //Remove html tags from lname
    $lname = str_replace(' ','',$lname); //remove empty spaces from lname
    $lname = ucfirst(strtolower($lname)); // ucfirst => only first letter is Capital and rest all letters are in lowercase 
    $_SESSION['reg_lname'] = $lname; //stores last name into session

    //Email
    $em = strip_tags($_POST['reg_email']); //Remove html tags from email
    $em = str_replace(' ','',$em); //remove empty spaces from email
    // $em = ucfirst(strtolower($em)); // ucfirst => only first letter is Capital and rest all letters are in lowercase
    $_SESSION['reg_email'] = $em; //stores email into session 

    //Email 2
    $em2 = strip_tags($_POST['reg_email2']); //Remove html tags from email2
    $em2 = str_replace(' ','',$em2); //remove empty spaces from email2
    // $em2 = ucfirst(strtolower($em2)); // ucfirst => only first letter is Capital and rest all letters are in lowercase 
    $_SESSION['reg_email2'] = $em2; //stores email 2 into session

    //Password
    $password = strip_tags($_POST['reg_password']); //Remove html tags from password 
    //Password 2
    $password2 = strip_tags($_POST['reg_password2']); //Remove html tags from password 2
    //Date
    $date = date("Y-m-d"); //Current date
    
    //EMAIL
    if($em == $em2){
        //check email is in valid format
        if(filter_var($em, FILTER_VALIDATE_EMAIL)){
            $em = filter_var($em, FILTER_VALIDATE_EMAIL);
            //check if email already exists
            $e_check = mysqli_query($con, "SELECT email FROM users WHERE email = '$em'");
            $num_rows = mysqli_num_rows($e_check);
            if($num_rows > 0){
                array_push($error_array, "Email already in use.Please give a different email<br>");
            }else {}
        }else {
            array_push($error_array, "Invalid email format<br>");
        }
    }else {
        array_push($error_array, "Emails dont match<br>");
    }
    // First name
    if(strlen($fname) > 25 || strlen($fname) < 2){
        array_push($error_array, "Your first name must be between 2 and 25 charecters<br>");
    }
    // Last name
    if(strlen($lname) > 25 || strlen($lname) < 2){
        array_push($error_array, "Your last name must be between 2 and 25 charecters<br>");
    }
    //Password
    if($password != $password2){
        array_push($error_array, "Your passwords do not match<br>");
    } else {
        if(preg_match('/[^A-Za-z0-9]/', $password)){
            array_push($error_array, "Your password can only contain english charecters and numbers<br>");
        }
    }
    if(strlen($password) > 30 || strlen($password) < 5){
        array_push($error_array, "Your password must be between 5 and 30 charecters<br>");
    }

    if(empty($error_array)){
        $password = md5($password); // encrypting password before sending to database
        $username = strtolower($fname."_".$lname); //creating unique username
        //Check if username already exists in database or not
        $check_username_query = mysqli_query($con,"SELECT username FROM users WHERE username = '$username'");
        
        $i = 0;
        //if username exists then add number to username
        while(mysqli_num_rows($check_username_query) != 0){
            $i++; // add 1 to i
            $username = $username . "_" . $i;
            $check_username_query = mysqli_query($con, "SELECT username FROM users WHERE username = '$username'");
        }

        //Profile pciture assignment
        $rand = rand(1,4); //creating random number between 1 and 4
        if($rand == 1){
            $profile_pic = "assets/images/profile_pics/defaults/head_deep_blue.png";
        }
        else if($rand == 2){
            $profile_pic = "assets/images/profile_pics/defaults/head_sun_flower.png";
        }
        else if($rand == 3){
            $profile_pic = "assets/images/profile_pics/defaults/head_alizarin.png";
        }
        else if($rand == 4){
            $profile_pic = "assets/images/profile_pics/defaults/head_emerald.png";
        }

        $query = mysqli_query($con,"INSERT INTO users VALUES ('','$fname','$lname','$username','$em','$password','$date','$profile_pic','0','0','no',',')");
        array_push($error_array, "<span style='color: #14c800'>You're all set! Go ahead and login!</span>");

        //clear session variables
        $_SESSION['reg_fname'] = "";
        $_SESSION['reg_lname'] = "";
        $_SESSION['reg_email'] = "";
        $_SESSION['reg_email2'] = "";

    }
}
?>