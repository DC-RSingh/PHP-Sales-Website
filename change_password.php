<?php
   /*
        Raje Singh
        WEBD3201 
        December 16, 2020
    */
    $author = "Raje Singh";
	$title = "Change Your Password";
	$file = "change_password.php";
	$description = "This is the Change Password page of the Lab 3 assignment for WEBD3201";
	$date = "November 13, 2020";
	$banner = "Change Password";
    include "./includes/header.php";

    // Check if a password reset link directed the user here
    if(isset($_GET['key']) && isset($_GET['reset']))
    {
        $reset_email_hash = $_GET['key'];
        $reset_password_hash = $_GET['reset'];

        $all_user_info = user_select_all();
        
        if((bool)$all_user_info)
        {
            $user_count = pg_num_rows($all_user_info);

            for($i = 0; $i < $user_count; $i++)
            {
                $record = pg_fetch_assoc($all_user_info, $i);

                // Check if the md5 hash matches, and if it does set the reset variables
                if(md5($record['emailaddress']) == $reset_email_hash && md5($record['password']) == $reset_password_hash)
                {
                    $_SESSION['reset_user_id'] = $record['id'];
                    $_SESSION['reset_user_email'] = $record['emailaddress'];
                    $_SESSION['reset_flag'] = true;
                    break;
                }
            }
        }
    }

    
    // If the user is not signed in redirect to sign-in page
    if(!isset($_SESSION['reset_flag']))
    {
        if(!isLoggedIn())
        {
            setMessage("You must be logged in to access that page!");
            redirect("./sign-in.php");
        }
    }

    // Password Verification Logic

    if ($_SERVER['REQUEST_METHOD'] == "POST")
    {
        $password = $_POST["password"];
        $confirm_password = $_POST["confirm"];

        $user_email = $_SESSION['reset_user_email'] ? $_SESSION['reset_user_email']: $_SESSION['user_email'];
        $user_id = $_SESSION['reset_user_id'] ? $_SESSION['reset_user_id'] : $_SESSION['user_id'];

        if (strlen($password) >= 3)
        {
            if ($confirm_password == $password)
            {
                // Update the user's password
                user_update_password($password, $user_id);

                log_activity($user_email." changed their password");
                setMessage("Your password has been updated!");

                // Redirect user that is resetting password to sign in page
                if(isset($_SESSION['reset_flag'])){
                    unset($_SESSION['reset_flag']);
                    redirect("./sign-in.php"); 
                }
                else
                {
                    redirect("./dashboard.php");
                }
            }
            else
            {
                setMessage("Passwords do not match!");
            }
            
        }
        else
        {
            setMessage("Please enter a password with more than 3 characters.");
        }
        
    }

    echo "\n\t\t\t<div class=\"container\">\n";   // Div to center the form
    echo "\t\t\t<h2 class=\"h2 text-center\">CHANGE YOUR PASSWORD</h2>";
    echo "<hr/>";

    // Display banner message
    displayMessage();

    display_form(
        array(
            array(
                "type" => "password",
                "name" => "password",
                "value" => "",
                "label" => "New Password",
                "pattern" => ".{3,}",
                "title" => "Enter at least 3 characters"
            ),
            array(
                "type" => "password",
                "name" =>  "confirm",
                "value" => "",
                "label" => "Confirm Password"
            )
            ),
            "form-signin",
            "Change Password"
    );

    echo "</div>"; // end of container div

    include "./includes/footer.php";
?>