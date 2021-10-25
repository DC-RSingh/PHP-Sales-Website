<?php
    /*
        Raje Singh
        WEBD3201 
        December 15, 2020
    */
    $author = "Raje Singh";
	$title = "Sign In";
	$file = "sign-in.php";
	$description = "This is the Sign In page of the Lab 1 assignment for WEBD3201";
	$date = "September 25, 2020";
	$banner = "Sign In";
    include "./includes/header.php";
    
    // If the user is signed in, redirect them to dashboard.php
    if (isLoggedIn())
    {
        redirect("./dashboard.php");
    }
?>

<?php 
// Echoes the message only if it is set
displayMessage();
?>

<?php
// Check if form method is post
if ($_SERVER['REQUEST_METHOD'] == "POST")
{
    // Check if form inputs are set
    if(isset($_POST['inputEmail']) && isset($_POST['inputPassword']))
    {
        // Check if form inputs are empty
        if(!empty($_POST['inputEmail']) && !empty($_POST['inputPassword']))
        {
            // Authenticate the login attempt
            $user_info = user_authenticate(trim($_POST['inputEmail']), trim($_POST['inputPassword'])); 

            // Check if the User's Account is Enabled
            $user_enabled = isset($user_info["type"]) ? (bool)!strpos($user_info["type"], DISABLED) : false;
            if($user_info && $user_enabled)
            {
                // MAYBE TODO: Could Replace with a For Loop and have session variables match database fields
                $_SESSION['user_id'] = $user_info['id'];    // added id
                $_SESSION['user_email'] = $user_info['emailaddress'];
                $_SESSION['password'] = $user_info['password'];            
                $_SESSION['user_first_name'] = $user_info['firstname'];
                $_SESSION['user_last_name'] = $user_info['lastname'];
                $_SESSION['user_enrol_date'] = $user_info['enrol_date'];
                $_SESSION['user_enabled'] = $user_info['enable'];
                $_SESSION['user_type'] = $user_info['type'];
                $_SESSION['user_phone_ext'] = $user_info['phoneextension'];
                $_SESSION['last_login'] = $user_info['lastaccess'];
                setMessage("You have successfully logged in! Last login time: ".$_SESSION['last_login']);
                log_activity("Successful login for User ".$_SESSION['user_email']);
                redirect("./dashboard.php");
            }
            else
            {
                // If the user was authenticated, then their account is disabled
                $user_info ? setMessage("Sorry, this account was disabled by an administrator.") : setMessage("Login failed! Email and password combination not found!");

                log_activity("Attempted login for User ".$_POST['inputEmail']);
                redirect("./sign-in.php");
            }
        }
    }
}

?>

<form class="form-signin" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <h1 class="h3 mb-3 font-weight-normal">SIGN IN</h1>
    <label for="inputEmail" class="sr-only">Email address</label>
    <input type="email" id="inputEmail" name="inputEmail" class="form-control" placeholder="Email address" required autofocus>
    <label for="inputPassword" class="sr-only">Password</label>
    <input type="password" id="inputPassword" name="inputPassword" class="form-control" placeholder="Password" required>
    <button class="btn btn-lg btn-primary btn-block" type="submit">Sign in</button><br/>
    <div class=""><a href="./reset.php" style="color: blue;">Forgot your Password?</a></div>
</form>

<?php
include "./includes/footer.php";
?>