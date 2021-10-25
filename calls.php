<?php
   /*
        Raje Singh
        WEBD3201 
        November 18, 2020
    */
    $author = "Raje Singh";
	$title = "Calls";
	$file = "calls.php";
	$description = "This is the Calls page of the Lab 2 assignment for WEBD3201";
	$date = "October 20, 2020";
	$banner = "Calls";
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

    // Using container to center the form
    echo "\n<div class=\"container\">\n";

    /******* TABLE GENERATION ********/

    echo "<h2 class=\"h2\">CALL INFORMATION</h2>\n";
    echo "<hr/>\n";

    echo "<div class=\"table-responsive\">\n"; // Table Container Div

    // Array of table headings
    $table_array = array(
        "id" => "ID",
        "clientid" => "Client ID",
        "callstarttime" => "Start Time",
        "callendtime" => "End Time"
    );

    // Total records from select query
    $total_row_count = call_count();

    // Get the value of page
    $page = validate_page($total_row_count, RECORDS_PER_PAGE);

    // Offset for query
    $offset = ($page - 1) * RECORDS_PER_PAGE;

    // The query with the limit and offset
    $table_query = call_select_all(RECORDS_PER_PAGE, $offset);

    // If the user is a Salesperson, only show them their clients
    if ($_SESSION["user_type"] == SALESPERSON)
    {
        $client_query = client_by_salesperson($_SESSION["user_id"], PHP_INT_MAX, 0);
		$num_clients =  pg_num_rows($client_query);
        // Start the client id string with an opening brace
        $id_string = "{";

        for($row = 0; $row < $num_clients; $row++)
        {

            // Get associative array of the row in result
            $user_clients = pg_fetch_assoc($client_query, $row);

            // If it is the last row, end the string with a closing brace
            if ($row == ($num_clients - 1))
            {
                $id_string .= $user_clients["id"] . "}";
            }
            else
            {
                $id_string .= $user_clients["id"] . ",";
            }
        }
		
        // The query with the limit and offset
        $table_query = $num_clients > 0 ? call_by_clients($id_string, RECORDS_PER_PAGE, $offset) : 0;

        // Total records from select query
        $total_row_count = $num_clients > 0 ? pg_num_rows(call_by_clients($id_string, PHP_INT_MAX, 0)) : 0;
    }

    // Generate Table
	if ($total_row_count > 0) 
	{
		@display_table(
			$table_array,
			$table_query,
			$total_row_count,
			$page
		);
	}
	else 
	{
		echo "<h3><strong>You have no calls on record!</strong></h3>\n<p><em>Why don't you go ahead and add one?</em></p>\n";
	}

    echo "</div>\n";  // End of Table Container

    /******* FORM GENERATION AND LOGIC ********/
    
    echo "<h2 class=\"h2\">ADD CALL</h2>";
    echo "<hr/>";

    // Set Form Variables
    $client_id = isset($_POST["client_id"]) ? $_POST["client_id"] : "";
    $start_date = isset($_POST["start_date"]) ? $_POST["start_date"] : "";
    $start_time = isset($_POST["start_time"]) ? $_POST["start_time"] : "";
    $end_date = isset($_POST["end_date"]) ? $_POST["end_date"] : "";
    $end_time =isset($_POST["end_time"]) ? $_POST["end_time"] : "";

    // Form validation
    if ($_SERVER['REQUEST_METHOD'] == "POST")
    {
        // Tokenizing form input
        $token = md5($_POST["client_id"] . $_POST["start_date"] . $_POST["start_time"] . $_POST["end_date"] . $_POST["end_time"]);

        // Comparison token
        $session_token = isset($_SESSION["form_token"]) ? $_SESSION["form_token"] : "";

        // Check if user is sending the same data (refreshing the page)
        if ($token != $session_token)
        {
            $_SESSION["form_token"] = $token;

            $message = "";        
            $client_id = $_POST["client_id"];
            $start_date = $_POST["start_date"];
            $start_time = $_POST["start_time"];
            $end_date = $_POST["end_date"];
            $end_time = $_POST["end_time"];

            // Store the result of the db_insert
            $start_datetime = $start_date . " " . $start_time;
            $end_datetime = $end_date . " " . $end_time;

            // Check if the client id exists
            $result = client_by_id_select($client_id);
            $client = array();
            $num_rows = $result ? pg_num_rows($result) : -1;

            // If the start time is less than end time, display error message and empty values
            if (strtotime($start_datetime) > strtotime($end_datetime))
            {
                setMessage("Start date must be before end date!");
                $start_date = "";
                $start_time = "";
            }
            // If Client ID is invalid
            else if(!is_numeric($client_id))
            {
                setMessage("Client ID format invalid");
                $client_id = "";
            }
            // If the client id does not exist in the database, display error message and empty value
            else if (!($num_rows > 0))
            {
                setMessage("No client with id \"" . $client_id . "\" exists.");
                $client_id = "";
            }
            else
            {

                $query_result = db_insert("calls", 
                array(
                    "client_id" => $client_id,
                    "start_time" => $start_datetime,
                    "end_time" => $end_datetime,
                ), 
                $message);

                if($query_result)
                {
                    // Log activity to log file and unset form token
                    log_activity($_SESSION["user_email"]." added a new call");
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
                "type" => "number",
                "name" => "client_id",
                "value" => "$client_id",
                "label" => "Client ID",
                "required" => ""
            ),
            array(
                "type" => "date",
                "name" => "start_date",
                "value" => "$start_date",
                "label" => "Call Start Date (yyyy-mm-dd)",
                "required" => ""
            ),
            array(
                "type" => "time",
                "name" => "start_time",
                "value" => "$start_time",
                "label" => "Call Start Time (hh:mm)",
                "required" => ""
            ),
            array(
                "type" => "date",
                "name" => "end_date",
                "value" => "$end_date",
                "label" => "Call End Date (yyyy-mm-dd)",
                "required" => ""
            ),
            array(
                "type" => "time",
                "name" => "end_time",
                "value" => "$end_time",
                "label" => "Call End Time (hh:mm)",
                "required" => ""
            )
        )
    );

    echo "</div>";  // end of container for form

    include "./includes/footer.php";
?>