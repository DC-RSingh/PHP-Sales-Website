<?php
    /*
        Raje Singh
        WEBD3201 
        December 16, 2020
    */
    $author = "Raje Singh";
	$title = "Reset Password";
	$file = "reset.php";
	$description = "This is the Password Reset page of the Lab 4 assignment for WEBD3201";
	$date = "December 16, 2020";
	$banner = "Reset";
    include "./includes/header.php";
    
     // If the user is signed in, redirect them to dashboard.php
    if (isLoggedIn())
    {
        redirect("./dashboard.php");
    }


    echo "\n<div class=\"container\">\n";   // Container Div

    echo "<h2 class=\"h2 text-center\">REQUEST A PASSWORD RESET</h2>\n";
    echo "<hr/>\n";

    if($_SERVER['REQUEST_METHOD'] == "POST")
    {
        $email = $_POST["email_address"];
        $message = "";

        // Validate Form Input
        if (filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $user_info = user_select($email);

            // Check if the email was found in the database
            if((bool)$user_info)
            {
                // Get the Associative Array of the user information
                $user_info = pg_fetch_assoc($user_info);

                // Reset Link: A proper one would expire after a certain period of time, maybe using a temporary password reset table in a database
				// Password is not plain-text, the hashed (and salted) password is hashed with the md5 digest algorithm
                // Alternatively, could have changed the password and sent the new one to the user
                $reset_link=WEBSITE_URL."change_password.php?key=".md5($email)."&reset=".md5($user_info['password']);

                log_activity("$email requested a password reset.");

                // Subject of the Email
                $subject = "Password Reset Request";

                // Get the formatted Date from the date function
                $now = date("l, F jS, Y \a\\t g:i:sa");

                // Create the template dir if it does not exist, though it would not work if it didn't exist
                if(create_dir(TEMPLATE_DIR))
                {
                    // Get the contents of the password reset template from the template dir
                    $body_file = file_get_contents(TEMPLATE_DIR."/password_reset_email_template.html");

                    // An Array Holding the Email Data to pass to the template
                    $email_data = array(WEBSITE_URL, $user_info['firstname'], $now, $reset_link, $reset_link, $reset_link);

                    $body = vsprintf($body_file, $email_data);

                    $headers[] = 'MIME-Version: 1.0';
                    $headers[] = 'Content-type: text/html; charset=iso-8559-1';
                    $headers[] = 'To: '.$user_info['firstname'].'<'.$email.'>';
                    $headers[] = 'From: WEBD3201 Raje Singh'.'<'.WEBSITE_EMAIL.'>';

                    // If we were on a mail server, we would send the mail here
                    // Since the mail function is disabled, we skip the mailing part, else statement contains code that would
					// be in the true part of the statement if mail was enabled
                    
                    if(@mail($email, $subject, $body, implode("\r\n",$headers)))
                    {
						log_activity("Password Reset email for $email was successfully sent, refer to $log_path for email");
                    }
                    else
                    {
                        $log_path = "./email_log/".$user_info['firstname']."_password_reset_request.html";
                        increment_file_name($log_path);
                        log_activity("$email tried to request a password reset, but it failed to send. Refer to $log_path for the attempted email.");
                        // An html file is created in the email_log directory which contains the contents of the email
                        if(create_dir("./email_log")) file_put_contents($log_path, $body);
                    }
                }

            }

			// Fake news
            $message = "Please check your e-mail for a link to reset your password.";
        }
        else
        {
            $message = "Email must be a valid email e.g. example@domain.com<br/>";
        }

        if(!empty($message)) setMessage($message);
        //redirect($_SERVER['PHP_SELF']);
    }

    // Display Banner Message
    displayMessage();

    display_form(array(
        array(
            "type" => "email",
            "name" => "email_address",
            "value" => "",
            "label" => "Email Address",
            "pattern" => HTML_EMAIL_REGEX, 
            "title" => "Enter a valid email: e.g. example@domain.com",
            "required" => "",
            "autofocus" => ""
        )
    ),
    "form-signin",
    "Request Reset");

    echo "</div>\n";    // End of Container Div

?>

<?php 
    include "./includes/footer.php";
?>