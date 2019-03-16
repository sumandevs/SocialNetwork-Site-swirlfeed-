<?php
require 'config/config.php';
require 'includes/form_handlers/register_handler.php';
require 'includes/form_handlers/login_handler.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome to SwirlFeed | Register here</title>
    <!-- Google fonts -->
    <link href="https://fonts.googleapis.com/css?family=Lato:400,400i,700,700i" rel="stylesheet">
    <!-- Custom css -->
    <link rel="stylesheet" type="text/css" href="assets/css/register_style.css"></link>
    <!-- Jquery cdn -->
    <script
        src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous">
    </script>
    <script type="text/javascript" src="assets/js/register.js"></script>
</head>
<body>

<?php  

if(isset($_POST['register_button'])) {
    echo '
    <script>

    $(document).ready(function() {
        $("#first").hide();
        $("#second").show();
    });

    </script>

    ';
}


?>
    
    <div class="wrapper">
        <div class="login-box">
            <div class="login-header">
                <h1>Swirlfeed!</h1>
                <h3>Login or Sign up below</h3>
            </div>

            <div id="first">
                <!-- LOGIN FORM -->
                <form action="register.php" method="POST">
                    <input type="email" name="log_email" placeholder="Your email.." value="<?php
                    if(isset($_SESSION['log_email'])){
                        echo $_SESSION['log_email'];
                    } 
                    ?>" required>
                    <br>
                    <?php if(in_array("Email or Password was incorrect!.Try to login again.<br>", $error_array)) 
                        echo "Email or Password was incorrect!.Try to login again.<br>";
                    ?>
                    <input type="password" name="log_password" placeholder="Your password.." required>
                    <br>
                    <input type="submit" name="login_button" value="Login">
                    <br>
                    <a href="#" id="signup" class="signup">Need an account? Register here</a>
    
                </form> <!-- End login form -->
                <br>
            </div><!-- End first -->
            
            <div id="second">
                <!-- REGISTER FORM -->
                <form action="register.php" method="POST">
                    <input type="text" name="reg_fname" placeholder="Enter your first name" value="<?php
                    if(isset($_SESSION['reg_fname'])){
                        echo $_SESSION['reg_fname'];
                    }
                    ?>" required>
                    <br>
                    <?php if(in_array("Your first name must be between 2 and 25 charecters<br>", $error_array)) 
                        echo "Your first name must be between 2 and 24 charecters<br>";
                    ?>
                    <input type="text" name="reg_lname" placeholder="Enter your last name" value="<?php
                    if(isset($_SESSION['reg_lname'])){
                        echo $_SESSION['reg_lname'];
                    }
                    ?>" required>
                    <br>
                    <?php if(in_array("Your last name must be between 2 and 25 charecters<br>", $error_array)) 
                        echo "Your last name must be between 2 and 24 charecters<br>";
                    ?>
                    <input type="email" name="reg_email" placeholder="Your email" value="<?php
                    if(isset($_SESSION['reg_email'])){
                        echo $_SESSION['reg_email'];
                    }
                    ?>" required>
                    <br>
                    <input type="email" name="reg_email2" placeholder="Confirm your email" value="<?php
                    if(isset($_SESSION['reg_email2'])){
                        echo $_SESSION['reg_email2'];
                    }
                    ?>" required>
                    <br>
                    <?php if(in_array("Email already in use.Please give a different email<br>", $error_array)) 
                            echo "Email already in use.Please give a different email<br>";
                        else if(in_array("Invalid email format<br>", $error_array)) 
                            echo "Invalid email format<br>";
                        else if(in_array("Emails dont match<br>", $error_array)) 
                            echo "Emails dont match<br>";
                    ?>
                    <input type="password" name="reg_password" placeholder="Your password" required>
                    <br>
                    <input type="password" name="reg_password2" placeholder="Confirm your password" required>
                    <br>
                    <?php if(in_array("Your passwords do not match<br>", $error_array)) 
                        echo "Your passwords do not match<br>";
                    ?>
                    <?php if(in_array("Your password can only contain english charecters or numbers<br>", $error_array)) 
                        echo "Your password can only contain english charecters or numbers<br>";
                    ?>
                    <?php if(in_array("Your password must be between 5 and 30 charecters<br>", $error_array)) 
                        echo "Your password must be between 5 and 30 charecters<br>";
                    ?>
                    <input type="submit" name="register_button" value="Register">
                    <br>
    
                    <?php if(in_array("<span style='color: #14c800'>You're all set! Go ahead and login!</span>", $error_array)) 
                        echo "<span style='color: #14c800'>You're all set! Go ahead and login!</span>";
                    ?>
                        
                    <a href="#" id="signin" class="signin">Already have an account? Sign in here!</a>
                </form>
            </div> <!-- End second -->
        </div>
    </div> <!-- wrapper -->

</body>
</html>