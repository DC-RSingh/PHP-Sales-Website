<?php
   /*
        Raje Singh
        WEBD3201 
        November 18, 2020
    */
    $author = "Raje Singh";
	$title = "Clients";
	$file = "clients.php";
	$description = "This is the Clients page of the Lab 2 assignment for WEBD3201";
	$date = "October 20, 2020";
	$banner = "Clients";
    include "./includes/header.php";

    // If the user is not signed in, redirect to sign-in page
    if(!isLoggedIn())
    {
        setMessage("You must be logged in to access that page!");
        redirect("./sign-in.php");
    }
    // If the user is not an admin or salesperson, redirect to dashboard
    else if(!($_SESSION['user_type'] == SALESPERSON || $_SESSION['user_type'] == ADMIN))
    {
        setMessage("You must be an administrator or salesperson to access that page!");
        redirect("./dashboard.php");
    }


    echo "<div class=\"container\">";   // Div to center the elements

    /******* TABLE GENERATION ********/

    echo "<h2 class=\"h2\">CLIENT INFORMATION</h2>\n";
    echo "<hr/>\n";

    echo "<div class=\"table-responsive\">\n"; // Table Container Div

    // Array of table headings
    $table_array = array(
        "emailaddress" => "Email",
        "firstname" => "First Name",
        "lastname" => "Last Name",
        "phoneareacode" => "Extension",
        "phonenumber" => "Phone Number",
        "logopath" => "Logo"
    );

    // Total records from select query
    $total_row_count = $_SESSION["user_type"] == ADMIN ? client_count() : client_count_salesperson($_SESSION["user_id"]);

    // Get the value of page
    $page = validate_page($total_row_count, RECORDS_PER_PAGE);

    // Offset for query
    $offset = ($page - 1) * RECORDS_PER_PAGE;
    
    // If the user is an admin, add the Salesperson column
    if ($_SESSION["user_type"] == ADMIN) $table_array["salespersonid"] = "Salesperson";

	// If 
	if ($offset >= 0) 
	{
		// The query with the limit and offset
		$table_query = $_SESSION["user_type"] == ADMIN ? client_select_all(RECORDS_PER_PAGE, $offset) 
			: client_by_salesperson($_SESSION["user_id"],RECORDS_PER_PAGE, $offset);

		
		// Generate Table
		@display_table(
			$table_array,
			$table_query,
			$total_row_count,
			$page
		);
	}
	else 
	{
		echo "<h3><strong>You have no clients!</strong></h3>\n<p><em>Why don't you go ahead and add one?</em></p>\n";
	}
    echo "</div>\n";  // End of Table Container

    /******* FORM GENERATION AND LOGIC ********/

    echo "<h2 class=\"h2\">ADD NEW CLIENT</h2>";
    echo "<hr/>";

    // display banner 
    displayMessage();

    // Set form variables
    $email = isset($_POST["email_address"]) ? $_POST["email_address"] : "";
    $first_name = isset($_POST["first_name"]) ? $_POST["first_name"] : "";
    $last_name = isset($_POST["last_name"]) ? $_POST["last_name"] : "";
    $area_code = isset($_POST["area_code"]) ? $_POST["area_code"] : "";
    $phone_number = isset($_POST["phone_number"]) ? $_POST["phone_number"] : "";

    // Array with the form inputs
    $form_inputs = array(
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
            "type" => "text",
            "name" => "area_code",
            "value" => "$area_code",
            "label" => "Area Code",
            "pattern" => "[0-9]{3}",
            "title" => "Enter a 3 digit number.",
            "required" => ""
        ),
        array(
            "type" => "text",
            "name" => "phone_number",
            "value" => "$phone_number",
            "label" => "Phone Number",
            "pattern" => "[0-9]{7}",
            "title" => "Enter a 7 digit number.",
            "required" => ""
        ),
        array(
            "type" => "file",
            "name" => "userfile",
            "value" => "",
            "label" => "Logo File (Optional)",
            "accept" => "image/png, image/jpeg"
        )
    );

    // If the user is an admin, add the salesperson select to the form_inputs array
    if($_SESSION['user_type'] == ADMIN)
    {
        // Retrieve all of the salesperson users
        $result = user_by_type_select(SALESPERSON, false);
        $users = array();
        $num_rows = $result ? pg_num_rows($result) : -1;

        if($num_rows > 0)
        {
            $salespeople = array();
            $salespeople_ids = array();
            for($row = 0; $row < $num_rows; $row++)
            {
                $users = pg_fetch_assoc($result, $row);

                // Generate an associative array with first and last name as the keys and ids as the values
                $key = $users["firstname"] . " " . $users["lastname"];
                $value = $users["id"];
                
                $salespeople[$key] = $value;
            }
            
            // Add the select with keys of the salespeople array being the options
            array_push($form_inputs, array(
                "type" => "select",
                "name" => "salesperson_select",
                "value" => array_keys($salespeople),
                "label" => "Salesperson",
                "required" => ""
            ));
        }
    }

    // Form validation
    if ($_SERVER['REQUEST_METHOD'] == "POST")
    {
        $target_filename = basename($_FILES["userfile"]["name"]);
        $target_path = UPLOAD_DIR . $target_filename;

        // Tokenizing form input
        $token = md5($_POST["first_name"] . $_POST["last_name"] . $_POST["email_address"] . $_POST["area_code"] . $_POST["phone_number"] . $target_path);

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
            $area_code = $_POST["area_code"];
            $phone_number = $_POST["phone_number"];
            $logo_path = "";

            $uploadOk = true;

            // Logo Path File Upload Handling
            if (!empty($target_filename))
            {
                // The following code is refactored from the following w3schools tutorial on File Uploading
                // Source: https://www.w3schools.com/php/php_file_upload.asp

                // String containing File Type
                $imageFileType = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));

                // Check if File is actually an image
                $actual_image_check = @getimagesize($_FILES["userfile"]["tmp_name"]);
                if ($actual_image_check !== false)
                {
                    $uploadOk = true;
                }
                else
                {
                    $uploadOk = false;
                    $message .= "The file you are trying to upload is not an image. ";
                }  

                // Check file size
                if ($_FILES["userfile"]["size"] > MAX_FILE_SIZE)
                {
                    $uploadOk = false;
                    $message .= "The file is too large: Must be less than 500 KB. ";
                }

                // Allow Image File Formats
                if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif")
                {
                    $uploadOk = false;
                    $message .= "Only JPG, JPEG, PNG & GIF files are allowed. ";
                }

                // Try to upload the file if everything is okay
                if ($uploadOk)
                {
                    // Increment file name if the file already exists
                    @increment_file_name($target_path);

                    // Create the upload dir if it doesn't exist, then proceed with moving the file
                    if (create_dir("./".UPLOAD_DIR))
                    {
                        // Move the temporary file to the uploads folder
                        if(@move_uploaded_file($_FILES["userfile"]["tmp_name"], $target_path))
                        {
                            $logo_path = $target_path;
                        }
                        else
                        {
                            $message .= "There was an error uploading your file. ";
                            $uploadOk = false;
                        }
                    }
                }
                    
            }

            // END OF FILE UPLOAD HANDLING
            
            // array with the data to insert
            $data = array(
                "email" => $email,
                "first_name" => $first_name,
                "last_name" => $last_name,
                "area_code" => $area_code,
                "phone" => $phone_number,
                "logo_path" => $logo_path,
                "salesperson_id" => $_SESSION['user_id']
            );

            // Check if the salespeople array (which is created when an admin is logged in) is set
            if (isset($salespeople))
            {
                $selected_salesperson = $_POST["salesperson_select"];

                // Set the salesperson_id to the id of the person who the admin chose
                $data["salesperson_id"] = $salespeople[$selected_salesperson];
            }

            // Sentinel Value
            $flag = true;

            // Validate Form Input
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
            if (strlen($area_code) == 0 || strlen($area_code) > 3)
            {
                $flag = false;
                $message .= "Extension must be between 1 and 3 digits";
                $area_code = "";
            }
            if (!preg_match("/[0-9]{7}/", $phone_number))
            {
                $flag = false;
                $message .= "Phone Number must be a 7 digit number.<br/>";
                $phone_number = "";
            }

            // Execute the insert query and store the result if the file is valid
            $query_result = $uploadOk && $flag ? db_insert("clients", $data ,$message) : false;
            
            // Log activity to log file and unset form token
            if($query_result)
            {
                log_activity($_SESSION["user_email"]." added a new client");
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

    // Generate form using form_inputs array
    display_form($form_inputs);

    echo "</div>\n";  // end of form and table wrapper div

    include "./includes/footer.php";
?>