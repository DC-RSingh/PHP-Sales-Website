<?php
/*
    Raje Singh
    WEBD3201 
    December 16, 2020
*/ 

/**
 *  Function to redirect to another URL
 *  @param string $url The URL to redirect to
 */
function redirect(string $url)
{
    header("Location:".$url);
    ob_flush();
    exit;
}

/**
 * Dumps variables in html pre tags (Test function)
 * @param mixed $arg The variable to be dumped
 */
function dump($arg) 
{
    echo "<pre>";
    print_r($arg);
    echo "</pre>";
}

/* $_SESSION Related Functions */

/**
 * Sets a message variable in the $_SESSION array
 * @param string $msg The message to be set
 */
function setMessage(string $msg)
{
    $_SESSION['message'] = $msg;
}

/**
 * Gets the message variable in the $_SESSION array
 * @return string The value set in the message variable
 */
function getMessage()
{
    return $_SESSION['message'];
}

/**
 * Returns whether a message variable in $_SESSION is set or not
 * @return bool TRUE if the message is set, FALSE if it is not
 */
function isMessage()
{
    return isset($_SESSION['message'])?true:false;
}

/**
 * Removes the message variable in the $_SESSION array
 */
function removeMessage()
{
    unset($_SESSION['message']);
}

/**
 * Returns the value of the message variable in $_SESSION array. Unsets the message after it's value is copied.
 * @return string The message stored in the $_SESSION array
 */
function flashMessage()
{
    $message = "";
    if(isMessage())
    {
        $message = getMessage(); 
        removeMessage();
    }
    return $message;
}

/**
 *  Displays the message using a div with bootstrap alert css
 */
function displayMessage()
{
    echo $message = isMessage() ? '<div class="alert alert-primary" role="alert">'.flashMessage()."</div>" : ""; 
}

/**
 * @return bool Whether a user is logged in or not
 */
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}


/* End of $_SESSION Related Functions */

/**
 * Appends to a file called DATE_log.txt in the format: [(the current date returned by the date function)] $entry"
 * @param string $entry The log entry to be recorded
 * 
 */
function log_activity(string $entry)
{
    $log_handle = fopen('./DATE_log.txt', 'ab');
    fwrite($log_handle, "[".date('Y-m-d h:i:s')."] $entry\n");
    fclose($log_handle);
}

/** 
 * Check whether an array is associative.
 * @param array $array 
 * @return bool **FALSE** for an only integer or mixed key array, **TRUE** for an array with only associative keys
 * 
 * @disclaimer 
 * THIS FUNCTION WAS TAKEN FROM THE FOLLOWING stackoverflow POST AND USER AND WAS MODIFIED BY ME TO RETURN TRUE FOR "TRUE" ASSOCIATIVE ARRAYS
 * 
 * Credit: 
 * 
 * Answer by user Captain kurO (User Profile: https://stackoverflow.com/users/356912/captain-kuro)
 * 
 * @source https://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential/4254008#4254008
 */
function is_assoc($array)
{
    if (!is_array($array) || $array == array()) return false;
    return count(array_filter(array_keys($array), 'is_string')) == count($array);
}

/**
 * Returns the number of associative arrays within an array or object
 * @param mixed $array
 * 
 * @return int The number of associative arrays in the array/object.
 */
function num_assoc($array)
{
    $count = 0;
    foreach($array as &$ele)
    {
        if(is_assoc($ele)) $count++;
    }
    unset($ele);

    return $count;
}

/**
 * Compares the keys of the given array to an array with the desired keys
 * 
 * 
 * @param array $array The array to compare
 *
 * @param array $desired_keys The keys to be compared against
 * 
 * @param bool $strict [optional] 
 * 
 * If true, will only succeed if the arrays are identical.
 * 
 * This means that both the orders AND the contents must be the same.
 * 
 * @return bool true on all keys found in array, false otherwise
 */
function has_correct_keys(array $array, array $desired_keys, bool $strict = false)
{
    $count = 0;
    $keys = array_keys($array);
    foreach($keys as &$ele)
    {
        if(in_array($ele,$desired_keys)) $count++;
    }
    unset($ele);

    return $strict ? $keys == $desired_keys : count($desired_keys) == $count;
}

/** 
 * Check whether a file name already exists and increments it if it does.
 * 
 * @param string $file_path Reference to the file path of the file to increment.
 * 
 * @disclaimer 
 * THIS FUNCTION WAS TAKEN FROM THE FOLLOWING stackoverflow POST AND USER. 
 * 
 * Credit: 
 * 
 * Answer by user Jade Steffen (User Profile: https://stackoverflow.com/users/4146923/jade-steffen)
 * 
 * @source https://stackoverflow.com/questions/15633242/how-to-increment-filename-in-php-to-prevent-duplicates/53191908
 */
function increment_file_name(string &$file_path)
{

   if (file_exists($file_path))
    {
        $path = pathinfo($file_path, PATHINFO_DIRNAME);
        $filename = pathinfo($file_path, PATHINFO_FILENAME);
        $ext = pathinfo($file_path, PATHINFO_EXTENSION);

        $dup_count = 1;

        while(file_exists($iter_filepath = $path . "/" . $filename ."-". $dup_count .".". $ext))
        {
            $dup_count++;
        }

        $file_path = $iter_filepath;
    }

}

/**
 * Generates forms based on the values passed in an array of associative arrays.
 * 
 *  Accepts optional keys _"id"_ and/or the names of input attributes (e.g. _"max"_, "_min"_, _"readonly"_).
 * 
 *  Optional Keys "div-class", "input-class" and/or "label-class" may be used to modify the class of the form-container divs, input and labels tags respectively.
 * 
 *  @param array $arr An array containing associative arrays of form element data with the following keys: _"type"_, _"name"_, _"value"_, _"label"_.
 * 
 *  Input restrictions and other attributes are supported through optional keys. They may be passed as key-value pairs using the name of the attribute as the key/index.
 * 
 *  _"id"_ is an optional key, however, if **checkbox** and **radio** input types are to be used then _"id"_ should be present for them to work correctly.
 * 
 *  The **image**, **list** attributes and **datalist** elements are not supported.
 * 
 *  _"name"_ is used for both the name and id of the input tag, unless the _"id"_ is set, in which case _"id"_ will be used as the id instead.
 * 
 *  If the _"type"_ value in an array is _"select"_ then an array with the options for the select form control is expected for the _"value"_ associated value.
 * 
 *  If the _"value"_ is set to an empty string, a placeholder will be displayed, otherwise the value passed in _"value"_ will be displayed.
 * 
 *  The _"label"_ is what will be displayed as the label for the form control, as well as the text in the placeholder.
 * 
 *  The _"submit"_ input tag has value set to "Submit", unless a value is passed for $submit_value.
 * 
 *  This function displays **Exception** messages if the array passed does not match requirements, and will not generate a form.
 * 
 *  @param string $form_class [optional]
 * 
 *  The classes intended for the form, if any.
 * 
 *  @param string $submit_value [optional]
 * 
 *  The value for the submit string.
 * 
 *  @param string $submit_class [optional]
 * 
 *  The classes intended for the submit button, if any. Defaults to _btn, btn-lg, btn-primary, and btn-block_.
 * 
 */
function display_form(array $arr, string $form_class = "", string $submit_value = "Submit", string $submit_class = "btn btn-lg btn-primary btn-block")
{
    $required_keys = array("type", "name", "value", "label");
    $supported_types = array("select", "button", "radio", "checkbox", "color", "date", "datetime-local", "email", "file", "month", 
    "number", "password", "range", "reset", "search", "tel", "text", "time", "url", "week", "hidden");
    $supported_attributes = array("checked", "disabled", "max", "maxlength", "min", "pattern", "readonly", "required", "size", "step", "multiple", 
        "title", "accesskey", "contenteditable", "dir", "hidden", "lang", "spellcheck", "style", "tabindex", "accept", "autofocus");
    $boolean_attributes = array("disabled", "required", "readonly", "multiple", "checked", "hidden", "autofocus");
    $placeholder_attributes = array("email", "number", "password", "search", "tel", "text", "url");
    $value_attribute_not_supported = array("file", "image");
    
    // Custom Exception handling: Tried to make it foolproof but I'm the only fool using it so it's just wasted effort
    try 
    {
        // Check if array argument is empty
        if ($arr == array()) throw new Exception("Empty array passed where array of associative arrays was expected.");
        // Check if array contains associative arrays
        if(!(num_assoc($arr) == count($arr))) throw new Exception("Non-associative array passed where associative array was expected.");

        // Array argument element checks
        foreach($arr as &$element)
        {
            // Check if elements are arrays
            if(!is_array($element)) throw new Exception("Non-array element where an array of associative arrays was expected.");

            // Check if elements of the elements have the required keys
            if(!has_correct_keys($element, $required_keys)) throw new Exception("Bad keys passed: expected \"type\", \"name\", \"value\", \"label\" to be present.");

            //  Check if an invalid type was passed
            if(!in_array($element["type"], $supported_types)) throw new Exception("Invalid type passed.");

            // Check if the "value" key associated value is an array when the "type" is "select"
            if($element["type"] == "select")
            { 
                if(!is_array($element["value"])) throw new Exception("Non-array value passed where array was expected for \"value\".");
                if($element["value"] == array()) throw new Exception("Empty array passed where values were expected for \"value\".");
            }

        }
        unset($element);
    }
    catch(Exception $e)
    {
        echo "Caught exception: ", $e->getMessage(), "\n";
        return;
    }

    // End of Exception Handling

    // REQUIRED FUNCTIONALITY STARTS HERE

    $method = "post";
    $action = '"'.htmlspecialchars($_SERVER["PHP_SELF"]).'"';
    
    // Echo Form opening tag
    echo "\n<form method=$method action=$action enctype=\"multipart/form-data\"";

    // If a value for form_class exists, use it
    echo !empty($form_class) ? " class=\"$form_class\">\n" : ">\n";

    // Loop through each array (the form inputs) in the array (array of form inputs)
    foreach($arr as &$form_input)
    {
        // If a "div-class" key-value pair is passed, use that for the class of the div containing the form inputs.
        $div_class = isset($form_input["div-class"]) ? $form_input["div-class"] : "form-group";

        echo "<div class=\"$div_class\">\n";

        // If an "id" key-value pair is passed, use that instead of name for id
        $id = isset($form_input["id"]) ? $form_input["id"] : $form_input["name"];

        // If an "input-class" key-value pair is passed, use that for the class of the input
        $input_class = isset($form_input["input-class"]) ? $form_input["input-class"] : "form-control";

        $restrictions = " ";

        foreach(array_keys($form_input) as &$attribute)
        {
            // Check if the attribute is supported
            if(in_array($attribute, $supported_attributes))
            {
                // Check if the attribute is a boolean attribute
                if(in_array($attribute, $boolean_attributes))
                {
                    $restrictions .= $attribute . " ";
                }
                else
                {
                    $restrictions .= $attribute . "=\"" . $form_input[$attribute] . "\" ";
                }
            }
        }
        unset($attribute);

        // Echo Label if the Input Type is not Hidden
        if ($form_input["type"] != "hidden") 
        {
            echo "<label for=\"" . $id . "\"";

            // Echo Label Class if the label-class is set
            echo isset($form_input["label-class"]) ? " class=\"" . $form_input["label-class"] . "\">" : " >";

            echo $form_input["label"] . "</label>\n";
        }


        // Output for select control, else output for a normal input control
        if($form_input["type"] == "select")
        {
            
            echo "<select class=\"$input_class\"" 
            . " id=\"" . $id . "\""
            . " name=\"" . $form_input["name"] . "\"" 
            . $restrictions
            . ">\n";

            echo "<option value=\"\" disabled selected>" . "Select " . $form_input["label"] . "</option>\n";

            // Add the options from the value array
            foreach($form_input["value"] as &$select_option)
            {
                echo "<option value=\"" . $select_option . "\">";
                echo $select_option;
                echo "</option>\n";
            }
            unset($select_option);
            echo "</select>\n";
        }
        else
        {
            // Echo input tag in html
            echo "<input type=\"" . $form_input["type"] . "\"" 
            . " class=\"$input_class\"" 
            . " id=\"" . $id . "\""
            . " name=\"" . $form_input["name"] . "\"";

            // If Input type is an attribute that can have a placeholder
            echo in_array($form_input["type"], $placeholder_attributes) ? " placeholder=\"Enter ". $form_input["label"] . "\"" : "";
            
            echo in_array($form_input["type"], $value_attribute_not_supported) ? "" : " value=\"" . $form_input["value"] . "\"";
            echo $restrictions . ">\n";
        }
        echo "</div>\n";    // end of form-group div
    }
    unset($form_input); 
    
    // Echo submit button
    echo "<input class=\"$submit_class\" type=\"submit\" value=\"$submit_value\">\n";
    echo "</form>\n";   // end of form
}

/**
 * Returns a valid page number for pagination.
 * @return int The validated page number returned by $_GET['page']
 */
function validate_page(int $row_count, int $amount_per_page)
{
    // Get the page variable from url
    $page = isset($_GET["page"]) ? $_GET["page"] : 1;

    // Total Pages
    $total_pages = ceil($row_count / $amount_per_page);

    // Check if page is numeric 
    if (!is_numeric($page))
    {
        $page = 1;
    }
    // Check if Page is an integer
    else if(!is_int($page))
    {
        $page = floor($page);
    }

    // Check if page is less than zero
    if($page <= 0)
    {
        $page = 1;
    }
    else if($page > $total_pages)
    {
        $page = $total_pages;
    }

    return $page;
}

/**
 * Generates page navigation list.
 * 
 * @param int $current_page The current page.
 * @param int $row_count The row count of the table.
 * @param int $amount_per_page The amount of records to show for each page.
 */
function paginate(int $current_page, int $row_count, int $amount_per_page)
{
    // Pagination

    echo "<nav aria-label=\"Page Navigation\">\n";
    
    echo "<ul class=\"pagination\">\n";

    // Check if previous page would be valid
    $can_prev = ($current_page - 1) > 0;
    echo $can_prev ? "<li class=\"page-item\">" : "<li class=\"page-item disabled\">";
    echo "<a class=\"page-link\" ";
    echo "href=\"";
    echo $can_prev ?  htmlspecialchars($_SERVER["PHP_SELF"])
    . "?page=" 
    . ($current_page - 1)
    . "\"" 
    : "#\"" . " tabindex=\"-1\"";
    echo ">&laquo; Previous</a></li>\n";

    // The total number of pages to be generated
    $total_pages = ceil($row_count / $amount_per_page);
    
    // Create Page List items and Links
    for($page = 1; $page <= $total_pages; $page++)
    {
        
        if ($page == $current_page)
        {
            echo "<li class=\"page-item active\">";
        }
        else
        {
            echo "<li class=\"page-item\">";
        }
        
        echo "<a class=\"page-link\" href=\""
        . htmlspecialchars($_SERVER["PHP_SELF"])
        . "?page="
        . $page
        . "\">";

        echo $page;

        echo "</a>";
        
        echo "</li>\n";
    }

    // Check if next page would be valid
    $can_next = ($current_page + 1) <= $total_pages;
    echo $can_next ? "<li class=\"page-item\">" : "<li class=\"page-item disabled\">";
    echo "<a class=\"page-link\" "; 
    echo "href=\"";
    echo $can_next ? htmlspecialchars($_SERVER["PHP_SELF"])
    . "?page=" 
    . ($current_page + 1)
    . "\"" 
    : "#\"" . " tabindex=\"-1\"";
    echo ">Next &raquo;</a></li>\n";

    echo "</ul>\n";

    echo "</nav>\n";
}


/**
 * Generates tables based on the headings passed in an associative array and the values from a PostgreSQL query result resource.
 * 
 * @param array $headings An associative array with the headings of the table.
 * 
 * The keys **MUST** match the names of the fields in the database.
 * 
 * The values will be used as the table headings.
 * 
 * @param resource $query_result PostgreSQL query result resource, returned by pg_query, pg_query_params or pg_execute (among others).
 * @param int $row_count The total number of records that should be displayed (for pagination purposes).
 * @param int $current_page The current page for pagination purposes, returned by $_GET['page'].
 * @param array $extra_fields [optional] 
 * 
 * An array with the extra values that the table should have (for any extra headings that are not from the query result resource).
 * 
 * The key should be equal to the key that was provided in the headings array for that field.
 * 
 * If an array is passed as a value, then it will be assumed to be a form of radio buttons and the display_form() function will be called.
 * 
 * If an array is a value, then it should contain strings indicating the value attribute for that particular button.
 * 
 * A key-value pair with key as **"determined_by"** may be used if there is a field in the query that determines the "checked" status of a radio button.
 * 
 * The value of the **"determined_by"** element, if present, must be an associative array with keys:
 *     
 *      _"field"_: The field in the database which determines the current button to be checked.
 * 
 *      _"value"_: The value this field should have.
 * 
 *      _"button"_: The value of the button which should be checked.
 * 
 * If the specified value of the field is present, then the specified button will be checked.
 * 
 * @param int $records_per_page [optional]
 * 
 * The allowed amount of records per page for paginaton purposes, is defaulted to the RECORDS_PER_PAGE  constant.
 * 
 */
function display_table(array $headings, $query_result, int $row_count, int $current_page, array $extra_fields = array(), int $records_per_page = RECORDS_PER_PAGE)
{
    // Pagination
    paginate($current_page, $row_count, $records_per_page);

    // Table Opening Tag
    echo "\n<table class=\"table table-striped table-bordered\" >\n";

    // Table Headings
    echo "<thead>\n";
    echo "<tr>\n";

    // Generate Table Headings
    foreach($headings as &$column_head)
    {
        echo "<th>" 
        . $column_head
        . "</th>\n";
    }
    unset($column_head);

    echo "</tr>\n";
    echo "</thead>\n";

    // Table Body
    echo "<tbody>\n";

    // Number of Headings
    $num_headings = count($headings);

    // Keys for the headings
    $heading_keys = array_keys($headings);

    // The amount of records from the query
    $num_rows = pg_num_rows($query_result);

    // The Extra Keys
    $extra_keys = array_keys($extra_fields);

    // Generate the Table Rows
    for($i = 0; $i < $num_rows; $i++)
    {

        echo "<tr>\n";

        // Get the associative array of the record
        $row = pg_fetch_assoc($query_result, $i);

        // The fields of the record
        $row_keys = array_keys($row);   

        // Generate the Row Data
        for($j = 0; $j < $num_headings; $j++)
        {
            // Set Current Key
            $current_key = $heading_keys[$j];

            echo "<td>";
            // Check if the heading is in the Query Result Set
            if (in_array($current_key, $row_keys))
            {
                // if the data is a path in upload directory, echo as an image tag
                if (strpos($row[$current_key], UPLOAD_DIR) !== false)
                {
                    echo "<img src=\"" . $row[$current_key]. "\" alt=\"image_logo\" class=\"img-thumbnail\" width=\"100\" height=\"100\"/>";
                }
                else
                {
                    echo htmlspecialchars($row[$current_key]);
                }
            }
            // Else, Check if the heading is in the extra key set
            else if (in_array($current_key, $extra_keys))
            {
                // Check if the value is an array
                if(is_array($extra_fields[$current_key]))
                {
                    // The Array of Radio Button Values
                    $radio_array = $extra_fields[$current_key];

                    // The Array to Hold the Form Inputs
                    $form_inputs = array();

                    // Check if determined_by key is set
                    $determined_by = isset($radio_array["determined_by"]) ? $radio_array["determined_by"] : false;

                    // Determine Size of the Radio Button Array
                    $radio_array_size = $determined_by ? count($radio_array) - 1 : count($radio_array);

                    // Loop Through The Radio Inputs
                    for($k = 0; $k < $radio_array_size; $k++)
                    {
                        // The Current Radio Button Value
                        $radio_value = $radio_array[$k];

                        $radio_id = "$i-$radio_value";

                        $radio_name = $current_key."[$i]";

                        $current_input =  array(
                            "type" => "radio",
                            "name" => $radio_name,
                            "value" => $radio_value,
                            "label" => $radio_value,
                            "id" => $radio_id,
                            "div-class" => "form-check form-check-inline",
                            "input-class" => "form-check-input",
                            "label-class" => "form-check-label"
                        );

                        // Check if the Determined_By Array is associative and has the correct keys
                        if(is_assoc($determined_by) && has_correct_keys($determined_by, array("field", "value", "button")))
                        {
                            // Get the value of the specified field
                            $field_value = $row[$determined_by["field"]];

                            // Check if the value is present
                            if(strpos($field_value, $determined_by["value"]) && $determined_by["button"] == $radio_value)
                            {
                                $current_input["checked"] = "";
                            }
                        }

                        // Push Input to Form Input Array
                        array_push($form_inputs, $current_input);
                    }

                    // An Identifying Hidden Element
                    $hidden_id = isset($row["id"]) ? $row["id"] : $i;
                    array_push($form_inputs, array(
                        "type" => "hidden",
                        "name" => "id[$i]",
                        "value" => $hidden_id,
                        "label" => "",
                        "hidden" => ""
                    )
                    );

                    // Display the Form
                    display_form($form_inputs, "form-inline", "Update", "");
                }
                else
                {
                    echo htmlspecialchars($extra_fields[$current_key]);
                }
            }   // End of Else If

            echo "</td>\n"; // End of Table Field

        }   // End of Row Data Generation Loop

        echo "</tr>\n";

    }   // End of Table Record Generation
    
    echo "</tbody>\n";

    // Table Closing Tag
    echo "</table>\n";

    // Pagination
    paginate($current_page, $row_count, $records_per_page);
}

/**
 * Creates a directory if it does not already exist.
 * 
 * @param string $dir_path The path to the directory to be created.
 * 
 * @param int $mode The access mode for the directory.
 * 
 * @return bool True on successful creation of directory or if the directory already exists, false on failure to create the directory
 */
function create_dir(string $dir_path, int $mode = 0777)
{
    if(!is_dir($dir_path))
    {
        return mkdir($dir_path, $mode, true);
    }
    else
    {
        return true;
    }
}

// Maybe TODO: FUTURE FUNCTION IMPLEMENTATION, TO BE USED IN RESET
/**
 * Formats and returns a string based on a template and array parameters from that template using vsprintf.
 * 
 * @param mixed $file The string containing the file contents, usually returned from file_get_contents
 * 
 * @param array $file_data The parameters 
 * 
 * @return string The formatted string
 */
function string_from_template($file, array $file_data)
{
    throw new Exception("This function has not been implemented."); 
}
?>