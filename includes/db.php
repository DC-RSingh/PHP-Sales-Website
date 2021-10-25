<?php
 /*
    Raje Singh
    WEBD3201 
    November 18, 2020
*/

/** 
 *
 * Connects to my PostgreSQL server
 * @return resource|false 
 */
function db_connect() 
{ 
    return pg_connect("host=".DB_HOST." port=".DB_PORT." dbname=".DATABASE." user=".DB_ADMIN." password=".DB_PASSWORD);  
}


// Establishing connection to database
$conn = db_connect();

/******* PREPARED STATEMENTS ********/

/* USERS TABLE */

// Select a user by email
$user_select_stmt = pg_prepare($conn, "user_select", "SELECT * FROM users WHERE EmailAddress = $1"); 

// Update a specific user's login time
$user_update_login_time_stmt = pg_prepare($conn, "user_update_login_time", "UPDATE users SET LastAccess=$1 WHERE EmailAddress = $2");

// Update a specific user's password
$user_update_password_stmt = pg_prepare($conn, "user_update_password", "UPDATE users SET Password=crypt($1, gen_salt('bf')) WHERE Id=$2");

// Insert a user record into the database
$user_insert_stmt = pg_prepare($conn, "user_insert", 
    "INSERT INTO users(EmailAddress, Password, FirstName, LastName, LastAccess, EnrolDate, PhoneExtension, Enable, Type) 
VALUES ($1, crypt($2, gen_salt('bf')), $3, $4, $5, $5, $6, true, $7)");

// Select users by type
$user_by_type_select_stmt = pg_prepare($conn, "user_by_type_select", "SELECT * FROM users WHERE Type LIKE $1");

// Select Users by type with offset
$user_by_type_offset_stmt = pg_prepare($conn, "user_by_type_offset", "SELECT * FROM users WHERE TYPE LIKE $1 ORDER BY ID LIMIT $2 OFFSET $3");

// Update a specific user's Type
$user_update_type_stmt = pg_prepare($conn, "user_update_type", "UPDATE users SET Type=$1 WHERE Id=$2");

// Select all users
$user_select_all_stmt = pg_prepare($conn, "user_select_all", "SELECT * FROM users");

/* END OF USERS TABLE */

/* CLIENTS TABLE */

// Select a client by email
$client_select_stmt = pg_prepare($conn, "client_select", "SELECT * FROM clients WHERE EmailAddress = $1");

// Insert a client record into the database
$client_insert_stmt = pg_prepare($conn, "client_insert", "INSERT INTO clients(EmailAddress, FirstName, LastName, PhoneAreaCode, PhoneNumber, LogoPath, SalespersonId) 
VALUES($1, $2, $3, $4, $5, $6, $7)");

// Select a client by id
$client_by_id_stmt = pg_prepare($conn, "client_by_id", "SELECT * FROM clients WHERE ID = $1");

// Select all clients
$client_select_all_stmt = pg_prepare($conn, "client_select_all", "SELECT * FROM clients ORDER BY ID ASC LIMIT $1 OFFSET $2");

// Client Select by Salesperson
$client_select_by_salesperson_stmt = pg_prepare($conn, "client_by_salesperson", "SELECT * FROM clients WHERE SalespersonID = $1 ORDER BY ID LIMIT $2 OFFSET $3");

// Total Amount of clients 
$client_count_stmt = pg_prepare($conn, "client_count", "SELECT COUNT(*) FROM clients");

// Total Amount of Clients for a Salesperson
$client_count_salesperson_stmt = pg_prepare($conn, "client_count_salesperson", "SELECT COUNT(*) FROM clients where SalesPersonID = $1");

/* END OF CLIENTS TABLE */

/* CALLS TABLE */

// Insert a call record into the database
$call_insert_stmt = pg_prepare($conn, "call_insert", "INSERT INTO calls(ClientId, CallStartTime, CallEndTime) 
VALUES($1, $2, $3)");

// Select all calls
$call_select_all_stmt = pg_prepare($conn, "call_select_all", "SELECT * FROM calls ORDER BY ID LIMIT $1 OFFSET $2");

// Select calls by client ID(s)
$call_by_clients_stmt = pg_prepare($conn, "call_by_clients", "SELECT * FROM calls WHERE ClientId = ANY($1::int[]) ORDER BY ID LIMIT $2 OFFSET $3");

// Total Amount of calls
$call_count_stmt = pg_prepare($conn, "call_count", "SELECT COUNT(*) FROM calls");

/* END OF CALLS TABLE */

/******* END OF PREPARED STATEMENTS ********/

/**
 * 
 * Retrieves user information from the database
 * @param string $user_email The email of the user to search for
 * @return resource|false Query result resource on success, false on failure
 * 
 */
function user_select($user_email)
{
    return pg_execute("user_select", array($user_email));
}

/**
 * 
 * Retrieves user information from the database based on their user type; displays disabled users by default.
 * 
 * @param mixed $user_type The type of user to output.
 * 
 * @param bool $include_disabled [optional]
 * 
 * Whether or not to include disabled users in the query.
 * 
 * @param int $limit [optional] 
 * 
 * The amount of records to retrieve.
 * 
 * @param int $offset [optional]
 * 
 * The record at which retrieval should start.
 * 
 * @return resource|false Query result resource on success, **FALSE** on failure.
 */
function user_by_type_select($user_type, bool $include_disabled = true, int $limit = 0, int $offset = 0)
{
    if ($include_disabled) $user_type .= "%";
    
    return $limit == 0 && $offset == 0 ? pg_execute("user_by_type_select", array($user_type)) : pg_execute("user_by_type_offset", array($user_type, $limit, $offset));
}

/**
 * Updates a user's password.
 * 
 * @param string $plain_password The plain text of the new password.
 * @param string $user_id The ID of the user.
 * @return resource|false Query result resource on success, **FALSE** on failure.
 * 
 */
function user_update_password(string $plain_password, string $user_id)
{
    return pg_execute("user_update_password", array($plain_password, $user_id));
}

/**
 * Retrieve all information from the users table
 * 
 * @return resource|false Query result resource on success, **FALSE** on failure.
 */
function user_select_all()
{
    return pg_execute("user_select_all", array());
}

/**
 * Retrieves client information from the database.
 * @param string $client_email The email of the user to search for.
 * @return resource|false Query result resource on success, **FALSE** on failure.
 * 
*/
function client_select($client_email)
{
    return pg_execute("client_select", array($client_email));
}

/**
 * Retrieves client information from the database.
 * @param string $client_id The id of the user to search for.
 * @return resource|false Query result resource on success, **FALSE** on failure.
 * 
*/
function client_by_id_select($client_id)
{
    return pg_execute("client_by_id", array($client_id));
}

/**
 * Retrieves all client information from the database.
 *  
 * @param int $limit The amount of records to retrieve.
 * @param int $offset The record at which retrieval should start.
 * @return resource|false Query result resource on success, **FALSE** on failure.
 * 
 */
function client_select_all(int $limit, int $offset)
{
    return pg_execute("client_select_all", array($limit, $offset));
}

/**
 * Retrieves all client information from the database for a specific salesperson
 * 
 * @param int $salesperson_id The ID of the salesperson 
 * @param int $limit The amount of records to retrieve
 * @param int $offset The record at which retrieval should start
 * @return resource|false Query result resource on success, **FALSE** on failure
 * 
 */
function client_by_salesperson(int $salesperson_id, int $limit, int $offset)
{
    return pg_execute("client_by_salesperson", array($salesperson_id, $limit, $offset));
}

/**
 * Returns the number of rows in the clients table
 * 
 * @return string The number of rows in the clients table
 */
function client_count()
{
    return pg_fetch_result(pg_execute("client_count", array()), 0);
}

/**
 *  Returns the number of clients for a specific salesperson.
 * 
 * @param int $salesperson_id The id of the salesperson
 * 
 * @return string The number of rows in the clients table
 */
function client_count_salesperson(int $salesperson_id)
{
    return pg_fetch_result(pg_execute("client_count_salesperson", array($salesperson_id)), 0);
}

/**
 * Retrieves all call information from the database.
 * @param int $limit The amount of records to retrieve
 * @param int $offset The record at which retrieval should start
 * @return resource|false Query result resource on success, **FALSE** on failure
 */
function call_select_all(int $limit, int $offset)
{
    return pg_execute("call_select_all", array($limit, $offset));
}

/**
 * @param string $clients A string containing an array to represent the series of clients e.g. _"{10000, 10001}"_
 * @param int $limit The amount of records to retrieve
 * @param int $offset The record at which retrieval should start
 * @return resource|false Query result resource on success, **FALSE** on failure
 */
function call_by_clients(string $clients, int $limit, int $offset)
{
    return pg_execute("call_by_clients", array($clients, $limit, $offset));
}

/**
 * Returns the number of rows in the calls table
 * 
 * @return string The number of rows in the calls table
 */
function call_count()
{
    return pg_fetch_result(pg_execute("call_count",array()), 0);
}

/**
 * 
 * Authenticates a user's information.
 * @param string $user_id The id of the user to authenticate.
 * @param string $password The plain text password to authenticate.
 * @return array|false An associative array containing user information if authentication is successful or FALSE on failure.
 */
function user_authenticate($user_id, $password)
{
    $result = user_select($user_id);
    $user = array();
    $auth = false;

    if(pg_num_rows($result) > 0)
    {
        $user = pg_fetch_assoc($result, 0);
        $auth = password_verify($password, $user["password"]);

        if($auth)
        {
            pg_execute("user_update_login_time", array(date('y-m-d h:i:s'), $user_id));
        }
    }

    return $auth ? $user : false;
}

/**
 * Update a user's type.
 * 
 * @param mixed $user_id The id of the user intended to be updated.
 * 
 * @param string $user_type The type you wish to update the user to.
 * 
 * Type must be either 1 or 2 characters, or the type will not be updated.
 * 
 * Available Types are defined in the constants.php file.
 * 
 * The type cannot start with the **DISABLED** flag defined in constants.php.
 * 
 * If a type contains 2 characters and the second character is not a **DISABLED** flag, the type will not be updated.
 * 
 * @return bool True if the user was updated successfully, false otherwise
 */
function user_update_type($user_id, string $user_type)
{
    // Check the length of the string
    if (strlen($user_type) > 2) return false;

    // Check if the first character is set to the disabled flag
    if (substr($user_type, 0, 1) === DISABLED) return false;


    // Check if a second character is present and, if it is, check if it is set to the disabled flag
    if (strlen($user_type === 2) && mb_substr($user_type, 1, 1, 'utf-8') !== DISABLED) return false;

    return (bool)pg_execute("user_update_type", array($user_type, $user_id));
}

/**
 * Inserts into a specified table in the PostgreSQL database the data in an associative array.
 * 
 * @param string $table The table in the database you want to insert data into.
 * 
 * @param array $insert_data An associative array containing keys relevant to the table fields.
 * 
 * The associative array must be indexed with these keys in the exact same order or the record will not be added and an exception message will be placed into the result variable.
 * 
 * _**KEYS FOR THE TABLES**_
 *  
 * **users**: 
 * "email", "password" , "first_name", "last_name", "date", "ext", "type" | **Note**: _"date"_ Format : 'y-m-d h:i:s'
 * 
 * **clients**:
 * "email", "first_name", "last_name", "area_code", "phone", "logo_path", "salesperson_id"
 * 
 * **calls**:
 * "client\_id", "start\_time", "end\_time"
 * 
 * @param string $insert_result [optional]
 * 
 *  Modifies a string variable to contain the result of the insert. 
 * 
 * It may contain any of the following messages: 
 * 
 * _"The record was successfully added!"_ on successful insertion to the database.
 * 
 * _"Record was not added: $exception"_ on encountering an exception trying to add the record.
 * 
 * _"No tables matching $table was found in the database"_ on no matching table.
 * 
 * _"Client/User with that email already exists!"_ on a record that already exists in the database.
 * 
 *  A **PostgreSQL** error message returned from **pg_last_error** if the data added passed all tests but the execution
 *  was unsuccessful.
 * 
 * @return bool **TRUE** on success, **FALSE** otherwise
 * 
 */
function db_insert(string $table, array $insert_data, string &$insert_result = "")   
{
    // I could make this into separate functions, or keep it and use separate functions inside of it
    try 
    {
        if(!is_assoc($insert_data)) throw new Exception("Non-associative array passed where associative array was expected for insert_data.");
        switch(strtolower($table))
        {
            // In the case the user wants to add to the users table
            case "users":

                // Check if associative array is indexed correctly
                if(!has_correct_keys($insert_data, array("email", "password" , "first_name", "last_name", "date", "ext", "type"), true)) 
                {
                    throw new Exception("Keys did not match required fields for users.");
                }

                // Check if user already exists
                $user_result = user_select($insert_data["email"]);
                if(pg_num_rows($user_result) > 0)
                {
                    $insert_result = "User with that email already exists!"; 
                }
                else 
                {
                    // Add user to database
                    $success = (bool)@pg_execute("user_insert", $insert_data);
                } 

                break;
            // In the case the user wants to add to the clients table
            case "clients":

                // Check if associative array is indexed correctly
                if(!has_correct_keys($insert_data, array("email", "first_name", "last_name", "area_code", "phone", "logo_path", "salesperson_id"), true)) 
                {
                    throw new Exception("Keys did not match required fields for clients.");
                }

                // Check if client already exists
                $client_result = client_select($insert_data["email"]);
                if(pg_num_rows($client_result) > 0)
                {
                    $insert_result = "Client with that email already exists!"; 
                }
                else 
                {
                    // Add client to database
                    $success = (bool)@pg_execute("client_insert", $insert_data);
                } 

                break;
            // In the case the user wants to add to the calls table
            case "calls":

                // Check if associative array is indexed correctly
                if(!has_correct_keys($insert_data, array("client_id", "start_time", "end_time"), true)) 
                {
                    throw new Exception("Keys did not match required fields for calls.");
                }

                // Add call to database
                $success = (bool)@pg_execute("call_insert", $insert_data);

                break;
            default:
                $insert_result = "No tables matching " . $table . " was found in the database";
        }
    }
    catch (Exception $ex)
    {
        $insert_result = "Record was not added: " . $ex->getMessage();
    }

    // If the success variable is set
    if (isset($success))
    {
        
        if (!$success)
        {
            // If success is not set to true, set the result string to the PostgreSQL error message
            $insert_result = pg_last_error();
        }
        else
        {
            // Set the result string to a sucess message
            $insert_result = "The record was added successfully!";
        }
    }

    return isset($success) ? $success : false;
}
?>