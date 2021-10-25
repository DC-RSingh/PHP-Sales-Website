<?php
   /*
        Raje Singh
        WEBD3201 
        December 15, 2020
    */
    $author = "Raje Singh";
	$title = "Sales People";
	$file = "salespeople.php";
	$description = "This is the Salespeople page of the Lab 2 assignment for WEBD3201";
	$date = "October 20, 2020";
	$banner = "Sales People";
    include "./includes/header.php";
    
    // If the user is not signed in, redirect to sign-in page
    if(!isLoggedIn())
    {
        setMessage("You must be logged in to access that page!");
        redirect("./sign-in.php");
    }
    // If the user is not an admin, redirect to dashboard
    else if($_SESSION['user_type'] != ADMIN)
    {
        setMessage("You must be an administrator to access that page!");
        redirect("./dashboard.php");
    }

    // Using container to center the form
    echo "\n<div class=\"container\">\n";

    /******* TABLE GENERATION ********/

    echo "<h2 class=\"h2\">SALESPEOPLE INFORMATION</h2>\n";
    echo "<hr/>\n";

    echo "<div class=\"table-responsive\">\n"; // Table Container Div

    // Array of table headings
    $table_array = array(
        "id" => "ID",
        "emailaddress" => "Email",
        "firstname" => "First Name",
        "lastname" => "Last Name",
        "lastaccess" => "Last Access",
        "phoneextension" => "Extension",
        "active" => "Is Active? (Blank means Active)"
    );

    // Total records from select query
    $total_row_count = pg_num_rows(user_by_type_select(SALESPERSON, true));

    // Get the value of page
    $page = validate_page($total_row_count, RECORDS_PER_PAGE);

    // Offset for query
    $offset = ($page - 1) * RECORDS_PER_PAGE;

    // The query with the limit and offset
    $table_query = user_by_type_select(SALESPERSON,true,RECORDS_PER_PAGE, $offset);

    // display banner 
    displayMessage();

    // Generate Table
    @display_table(
        $table_array,
        $table_query,
        $total_row_count,
        $page,
        array(
            "active" => array(
                "Active",
                "Inactive",
                "determined_by" => array(
                    "field" => "type",
                    "value" => "d",
                    "button" => "Inactive"
                )
            )
        )
    );

    echo "</div>\n";  // End of Table Container

    /* UPDATE SALESPERSON FORM LOGIC */

    // Update Salesperson Status (Since there are multiple forms, only one can be submitted without using JavaScript)
    // The Browser Only Posts One Form, that is the latest form submitted; it will abort all requests but the last.
    if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['active']))
    {
        $active_message = "";
        foreach($_POST['active'] as $record_num => $active)
        {

            $user_id = $_POST['id'][$record_num];

            if ($active === "Active")
            {
                if(user_update_type($user_id, SALESPERSON))
                {
                    log_activity($_SESSION['user_email']." enabled user ".$user_id);
                    $active_message .= "Successfully Enabled User $user_id";
                }
                else
                {
                    $active_message .= "User $user_id active status was not updated.";
                }
            }
            else if($active === "Inactive")
            {
                if(user_update_type($user_id, SALESPERSON.DISABLED))
                {
                    log_activity($_SESSION['user_email']." disabled user ".$user_id);
                    $active_message .="Successfully Disabled User $user_id";
                } 
                else
                {
                    $active_message .= "User $user_id active status was not updated.";
                }
            }
        }

        // If the Message is not Empty, Set it
        if (!empty($active_message)) setMessage($active_message);

        redirect(htmlspecialchars($_SERVER['PHP_SELF']));
    }

    /* END OF UPDATE SALESPERSON FORM LOGIC */

   

    /******* FORM GENERATION AND LOGIC ********/


    echo "<h2 class=\"h2\">ADD SALESPERSON</h2>";
    echo "<hr/>";
    
    // display banner 
    displayMessage();

    // Set Form Variables
    $email = "";
    $first_name = "";
    $last_name = "";
    $password = "";
    $extension = "";

    // Form validation
    if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST["email_address"]))
    {
        // Tokenizing form input
        $token = md5($_POST["email_address"] . $_POST["first_name"] . $_POST["last_name"] . $_POST["input_password"] . $_POST["extension"]);

        // Comparison token
        $session_token = isset($_SESSION["form_token"]) ? $_SESSION["form_token"] : "";

        // Check if user is sending the same data (refreshing the page)
        if ($token != $session_token)
        {
            $_SESSION["form_token"] = $token;

            $message = "";        
            $email = $_POST["email_address"];
            $first_name = $_POST["first_name"];
            $last_name = $_POST["last_name"];
            $password = $_POST["input_password"];
            $extension = $_POST["extension"];
            $date = date('y-m-d h:i:s');

            // Sentinel value
            $flag = true;

            // Validate Input
            if (!filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $flag = false;
                $message .= "Email must be a valid email e.g. example@domain.com<br/>";
                $email = "";
            }
            if(!preg_match(PHP_NAME_REGEX, $first_name))
            {
                $flag = false;
                $message .= "First Name must only contain alpha-numeric characters!<br/>";
                $first_name = "";
            }
            if(!preg_match(PHP_NAME_REGEX, $last_name))
            {
                $flag = false;
                $message .= "Last Name must only contain alpha-numeric characters!<br/>";
                $last_name = "";
            }
            if (strlen($extension) == 0 || strlen($extension) > 4)
            {
                $flag = false;
                $message .= "Extension must be between 1 and 4 digits";
                $extension = "";
            }

            // Store the result of the db_insert
            $query_result = $flag ? db_insert("users", 
            array(
                "email" => $email,
                "password" => $password,
                "first_name" => $first_name,
                "last_name" => $last_name,
                "date" => $date,
                "ext" => $extension,
                "type" => SALESPERSON
            ), 
            $message) : false;

            if($query_result)
            {
                // Log activity to log file and unset form token
                log_activity($_SESSION["user_email"]." added a new salesperson");
                unset($_SESSION["form_token"]);

                // redirect to this page to clear $_POST data
                setMessage($message);
                redirect(htmlspecialchars($_SERVER["PHP_SELF"]));
            }
            else
            {
                // Set error message
                setMessage($message);
            }
        }
        else
        {
            // Inform user form data was not processed
            setMessage("Duplicate entry... did not process data.");
        }
    }


    // Display banner message
    displayMessage();

    // Using display_form to create a form
    display_form(
        array(
            array(
                "type" => "text",
                "name" => "first_name",
                "value" => "$first_name",
                "label" => "First Name",
                "required" => "",
                "pattern" => HTML_NAME_REGEX,
                "title" => "Enter a valid name (only alpha-numeric characters)"
            ),
            array(
                "type" => "text",
                "name" => "last_name",
                "value" => "$last_name",
                "label" => "Last Name",
                "required" => "",
                "pattern" => HTML_NAME_REGEX,
                "title" => "Enter a valid name (only alpha-numeric characters)"
            ),
            array(
                "type" => "email",
                "name" => "email_address",
                "value" => "$email",
                "label" => "Email",
                "pattern" => HTML_EMAIL_REGEX, 
                "title" => "Enter a valid email: e.g. example@domain.com",
                "required" => ""
            ),
            array(
                "type" => "password",
                "name" => "input_password",
                "value" => "",
                "label" => "Password",
                "required" => ""
            ),
            array(
                "type" => "text",
                "name" => "extension",
                "value" => "$extension",
                "label" => "Extension",
                "pattern" => "[0-9]{1,4}",
                "title" => "Enter a number between 1 and 4 digits.",
                "required" => ""
            )
        )
    );

    echo "</div>\n";    // end of container div

    include "./includes/footer.php";
?>